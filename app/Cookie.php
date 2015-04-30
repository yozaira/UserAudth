<?php namespace App;

 /*
 * Cookie class
 * This is a class to handle cookies created
 * when users choose the 'remember me' option
 * on the login process.
 */
class Cookie {

  /*
  * Verify if cookie name exist
  * @param string $name
  * @returns boolean
  */
  public static function exists($name) {
    return (isset($_COOKIE[$name]) ) ? true : false;
  }

  /*
  * Retrieves cookie name if found
  * @param string $name
  * @returns string
  */
  public static function get($name) {
    return (isset($_COOKIE[$name]) ) ? $_COOKIE[$name] : '';
  }


  /*
  * Creates a cookie
  * @param string $name
  * @param string $value
  * @param string $expiry
  * @returns boolean
  */
  public static function put($name, $value, $expiry) {
    if (setcookie($name, $value, time() + $expiry, '/') ) {
       return true;
    }
    return false;
  }

  /*
  * Deletes cookie
  * @param string $name
  * @returns void
  */
  public static function delete($name) {
    self::put($name, ' ', time() - 1);
  }

}