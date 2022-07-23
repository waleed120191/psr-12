<?php

/**
 * This is Settings File
 */

namespace App\Config;

use Medoo\Medoo;

/**
 * This is Settings Class
 */

class Settings
{
    public $apiBaseURL = 'https://trial.craig.mtcserver15.com/';
    public $apiKey = '';
   /**
    * Return Medoo database object
    * TODO: Seperate Initializing of Medoo from this class
    * @return  Object
    */
    public function db()
    {
        return new Medoo([
         'type' => 'mysql',
         'host' => 'localhost',
         'database' => 'mtc-trail',
         'username' => 'root',
         'password' => ''
        ]);
    }
}
