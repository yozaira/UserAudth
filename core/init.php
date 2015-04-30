<?php
session_start();

$GLOBALS['config'] = array (
                    # db inforation
										'mysql' =>  array(
												'host'     => '',
												'username' => '',
												'password' => '',
												'db'       => ''
										),
										'remember' => array(
												'cookie_name'  => 'hash',
												'cookie_expiry'=> '604800'
										),
										'session' =>  array(
												'session_name'=> 'user_id',
												'token_name'  => 'token'
										) );

# require the classes
# vendor is outside public directory
require_once '../vendor/autoload.php';

# include helper functions
require_once 'functions/sanitize.php';
require_once 'functions/baseURI.php';

# call namespace of the used classes
use App\DB;
use App\Cookie;
use App\Config;
use App\Session;

if(Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))) {
	# if session is closed but the user had selected to be remembered,
	# this will
	# To test this delete the session on the browser
  // echo 'User asked to be remembered.<br/>';

  # get the cookie name in the config file, and check if the cookie hash is stored in the db
  $hash = Cookie::get(Config::get('remember/cookie_name'));
  // var_dump( $hash );
  $userHasCookie = DB::getInstance()->get('users_cookie', array('cookie_hash','=', $hash) );
  // var_dump( $currentHash  );

  # if the user has a cookie stored in the db, log user in
  if($userHasCookie->getResults()) {
	  # Remember: this will be output if session doesnt exist.
	  # If logged in, delete PHPSSESID to test this
    // echo 'Hash matches. Log in user with the ID ' . $userHasCookie->first()->user_id.'<br/>';

    # get an instance of the with user id to find user
    # (see constructor on User class)
	  $user = new User($userHasCookie->first()->user_id);
	  $user->loginRemember();
	  if($user->isLoggedIn()) {
	  	return true;
	  }
	}
}

?>