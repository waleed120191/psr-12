<?php

/**
 * This is Helper File
 */

namespace App\Helper;

/**
 * This is Util Helper Class
 */

class UtilHelper
{
   /**
    * validate Json
    * @param string json
    * @return Boolean
    */
    public function validateJSON(string $json): bool
    {
        try {
            $test = json_decode($json, null, flags: JSON_THROW_ON_ERROR);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
