<?php namespace App;

/**
 * Session class
 * This is a class to handle sessions created
 * when users login.
 */
  class Session {
      /*
      * Verify if session name exist
      * @param string $name
      * @returns boolean
      */
      public static function exists($name) {
        return (isset($_SESSION[$name]) ) ? true : false;
      }


      /*
      * Creates a session
      * @param string $name
      * @param string $value
      * @param string $expiry
      * @returns boolean
      */
      public static function put($name, $value) {
        return $_SESSION[$name] = $value;
      }

      /*
      * Retrieves session name if found
      * @param string $name
      * @returns string
      */
      public static function get($name) {
        return (isset($_SESSION[$name]) ) ? $_SESSION[$name] : '';
      }


      /*
      * Deletes session
      * @param string $name
      * @returns void
      */
      public static function delete($name) {
        if(self::exists($name)) {
           unset($_SESSION[$name]);
        }
      }


      /*
      * Generates a short message for user  that
      * only lasts until the pages is refreshed
      * @param string $name
      * @param string $sting
      * @returns string message
      */
      public static function flashMessage($name, $string = null ) {
        if(self::exists($name)) {
           $session = self::get($name);
           # when the page refresh, the session message will be
           # deleted.
           self::delete($name);
           return $session;
        } else {
           self::put($name, $string);
        }
      }

  }

