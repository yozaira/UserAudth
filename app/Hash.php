<?php namespace App;

 /**
 * Hash class
 * Create a password hash from a password
 */
  class Hash {

   /*
   * Generates a string hash and salt from a password
   * @param string $string
   * @param int $salt
   */
    public static function make($string, $salt = '' ) {
      return hash('sha256', $string .$salt);
    }


   /*
   * Generates the salt value that will be used
   * for passwords
   * @param int $length
   */
    public static function salt($length) {
      # http://php.net/manual/en/function.mcrypt-create-iv.php
      return mcrypt_create_iv($length);
    }


    public static function unique(){
      return self::make(uniqid());
    }

  }