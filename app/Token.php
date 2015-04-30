<?php namespace App;

 /**
 * Token class
 * Create a token value for each GET or POST
 * request on each form. This prevents CSRF attacksc
 */
class Token {

  /*
  * Creates a session with the name
  * token and a value created with md5()
  * and unique() functions.
  */
  public static function generate() {
    // $token  = md5(uniqid(rand(), true));
    // $token  = sha1(microtime());
    // $token  = md5(uniqid(rand(), true));
    $token  = md5(uniqid());
    return Session::put(Config::get('session/token_name'), $token );
    // return Session::put('csrf', $token  );  # for testing
  }

  /*
  * Checks if the token name and the token value are valid.
  * It does this by comparing the value of the GET or POST
  * request (token generated on each form) with the value
  * stored in the users SESSION variable.
  * @param string $token string token defined in the form
  * @return boolean
  */
  public static function check($token) {
    // $tokenName = 'csrf';   # for testing
    $tokenName = Config::get('session/token_name');
    # check if input token and session token are the same
    if(Session::exists($tokenName) && $token === Session::get($tokenName)) {
       return true;
    }
     # bad token. if input token and session token are not the same
    return false;
  }

}