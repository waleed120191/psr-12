<?php

/**
 * This is Property File
 */

namespace App\Cli;

use App\Config\Settings;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use App\Helper\UtilHelper;

/**
 * This is Property Class
 */

class Property
{
    private $db; // Can be defined in seperate class like model
    private $apiBaseURL;
    private $apiKey;
    public function __construct()
    {
        $settings = new Settings();
        $this->db = $settings->db();
        $this->apiBaseURL = $settings->apiBaseURL;
        $this->apiKey = $settings->apiKey;
    }

    /**
    * This function will start the process of fetching data from api & store into database
    */
    public function process()
    {
        $pageNumber = 1;
        $pageSize = 100;
        $data = $this->fetch($pageNumber, $pageSize);

        do {
            $validData = $this->validateData($data);
            $this->store($validData);
            $pageNumber++;
            $data = $this->fetch($pageNumber, $pageSize);
        } while (!empty($data));
    }

   /**
    * This function will fetch data from api
    * @param int pageNumber
    * @param int pageSize
    * @return Array
    */
    public function fetch($pageNumber = 1, $pageSize = 10)
    {
        $response = $this->apiRequest('api/properties', $pageNumber, $pageSize);
        $responseDecoded = json_decode($response, true);

        $data = [];
        if (isset($responseDecoded['data']) and count($responseDecoded['data']) > 0) {
            $data = $responseDecoded['data'];
        }

        return $data;
    }

   /**
    * This function will make Request to API
    * TODO: Can be moved to helper class for common use by add some more params
    * @param string endpoint
    * @param int pageNumber
    * @param int pageSize
    * @return JSON|Boolean
    */
    public function apiRequest($endpoint = '', $pageNumber = 1, $pageSize = 10)
    {
        $client = new Client(['base_uri' => $this->apiBaseURL]);
        try {
            $response = $client->request('GET', $endpoint, [
            'query' => [
              'page[number]' => $pageNumber,
              'page[size]' => $pageSize,
              'api_key' => $this->apiKey
              ]
            ]);
        } catch (ClientException $e) {
            error_log(Psr7\Message::toString($e->getRequest()));
            error_log(Psr7\Message::toString($e->getResponse()));
            return false;
        }

        if ($response->getStatusCode() == 200) {
            $utilHelper = new UtilHelper();
            $body = (string)$response->getBody();
            if ($utilHelper->validateJSON($body)) {
                  return $body;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
    * This function will validate single request fetched data & create array accordingly to store
    * TODO: Prepare all data that need to be inserted into DB
    * @param Array data
    * @return Array
    */
    public function validateData($data = [])
    {
        $validData = [];
        if (!empty($data)) {
            // To avoid additional loop validation & array prepration in same loop
            foreach ($data as $d) {
                if ($this->validate($d)) {
                    $validData[] = [
                     'town' => $d['town'],
                     'num_of_bedrooms' => $d['num_bedrooms'],
                     'price' => $d['price'],
                     'type' => $d['type'],
                     'property_type' => json_encode($d['property_type']),
                    ];
                }
            }
        }
        return $validData;
    }

    /**
    * This function will validate data coming from api
    * TODO: Validate all data
    * TODO: Use library like https://packagist.org/packages/respect/validation
    * @param Array data
    * @return Boolean
    */
    public function validate($data = [])
    {
        if (isset($data['town']) && !is_string($data['town'])) {
            return false;
        }

        if (isset($data['num_bedrooms']) && !is_int((int)$data['num_bedrooms'])) {
            return false;
        }

        if (isset($data['price']) && !is_float((float)$data['price'])) {
            return false;
        }

        if (isset($data['type']) && !is_string($data['type'])) {
            return false;
        }

        return true;
    }

   /**
    * This function will store data into database
    * TODO: Talking to database needs to be done in seperate class
    */
    public function store($data = [])
    {
        $this->db->insert('property', $data);
    }
}
