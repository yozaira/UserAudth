<?php namespace App;

/**
* core/init.php file contains the global config
* array with the db info.
*/
require_once '../core/init.php';
/**
* Config class access and returns db, sessions
* and cookies info contains in config
* global array
*/
class Config {

  /**
   * this method gets the db info stored in global config array.
   * Accepts a string and returns the string path of the  global
   * config array in init.php
   *
   * @param string $path the string patth of global config.
   * @
   */
  public static function get($path = null) {
    if($path) {
      // var_dump($path);
      # initialize $config variable with $GOBALS
      # multidimensional array value
      $config = $GLOBALS['config'] ;
      # divide path into individual string separated by "/"
      $path = explode('/' , $path);
      # print_r($path);
      foreach ($path as $pathString) {
        // var_dump($pathString);
        # if there global config contains the string path
        # pass this value to $config var and retun it.
        if(isset( $config[$pathString] )) {
          // var_dump($config[$pathString] );
          $config = $config[$pathString];
        }
      }
      return $config;
    }
  }
}
