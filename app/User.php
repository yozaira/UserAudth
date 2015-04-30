<?php  namespace App;

/*
* User class
* Creates all the user actions, like
* registration, login and data update
*/

class User {

    /*
    * private static $_db
    * @var string stores an instance of db connection
    */
		private $_db;
		/*
		* private static $_user
		* @var string stores user data retrived from findUser method
		*/
		private	$_user;
		/*
    * private static $_errors
    * @var string stores errors
    */
		private	$_errors = array();
		/*
    * private static $_sessionName
    * @var string stores the name of user session
    */
		private	$_sessionName;
		/*
    * private static $_cookieName
    * @var string stores name of the cookie to remember the user
    */
		private	$_cookieName;
		/*
    * private static $_isLoggedIn
    * @var boolean will check if the user is loggedin
    */
		private	$_isLoggedIn;


		public function __construct($user = null) {
		  # get an instance of the DB
			$this->_db = DB::getInstance();

			# set session and cookie values
			$this->_sessionName = Config::get('session/session_name');
			$this->_cookieName  = Config::get('remember/cookie_name');
			// var_dump($_SESSION);
			// var_dump($_COOKIE);

      # check if the user is loggedin
			if(!$user) {

				if(Session::exists($this->_sessionName)) {
          # if there is a session active, it means the
          # user is loggedin
					$user = Session::get($this->_sessionName);
				  # this method allows to get user's data by
				  # using the param of the class
					if($this->findUser($user)) {
					   $this->_isLoggedIn = true;
					}
				} else {
					 # process to logout
					$this->_isLoggedIn = false;
				}
			} else {
					# this allows to get an instance of the current user
					# and to access his data without the parameter
			    # ex: $user = new User()
				  $this->findUser($user);
			}
			// var_dump($user);

      /*
			# test isLoggIng() method and check access to user data
			if ( $this->isLoggedIn() ) {
				echo '<hr/>';
				echo "User " .$this->getUserInfo()->id. " is loggedin";
				var_dump($this->getUserInfo() );
				echo '<hr/>';
			}
			*/

		}


    /*
    * Updates user data
    * @param array $fields
    * @param int $id a second optional param to ensure
    * the functionality doesnt break if there is no id
    * available for whatever reason.
    */
		public function updateUser($fields = array(), $id = null) {
			# if no id param is entered, use current user id
			if(!$id && $this->isLoggedIn()) {
				$id = $this->_user->id;
			}
			if(!$this->_db->update('users', $id, $fields)) {
				throw new Exception('There was a problen updating.');
			}
		}



    /*
    * Finds a specific user by his email or id
    * @param string $user email or id of the user
    * @return array of user data
    */
		public function findUser($user = null) {
			if($user) {
			  # user can be found using id or email
				$field = (is_numeric($user)) ? 'id' : 'email';
				$data = DB::getInstance()->get('users', array($field, '=', $user));
				# check if there is data returned
				// secho 'DATA' ; var_dump($data);
				// var_dump($data->resultCount() );
				if($data->getResults() )  {
				  # pass data to $user private property
			    // var_dump($data);
			    // var_dump($data->resultCount() );
			    // var_dump($data->getResults() );
			    // var_dump( $data->first() );
			    $this->_user = $data->first() ;
			    // echo 'USER DATA' ; var_dump($this->_user);
			    return true;
			    # Or
			    // foreach ($data->getResults() as $userData)	{
			    // $this->_user = $userData;
			    // return $this->_user;
			    // }
			  }
			  return false;
			}
		}


    /*
    * Checks if the user email and password are valid
    * @param string $email user email
    * @param string $password user password
    * @return boolean
    */
		public function isValidUser($email, $password) {
			if($this->findUser($email) ) {
			  # if email or the passowrd the user is entering is wrong, echo an error
			  if($this->_user->pass === Hash::make($password, $this->_user->salt)
			   	 && $this->_user->email === $email) {
				   return true;
			   }else {
				   $this->_addError("That password is incorrect. Be sure you're using the right password.");
				}
			}
			return false;
		}


    /*
    * Ouputs errors
    * @param string error the message error
    * @return string
    */
		private function _addError($error){
			$this->_errors[] = $error;
		}


    /*
		* Checks whether the user is already  logged
		* in by simply checking the existence of user_id
		* in $_SESSION.
		* @return boolean
		*/
		public function isLoggedIn()  {
			return $this->_isLoggedIn;
		}


    /*
		* simple loging method without remember fearture
		* @return boolean
		*/
		public function login($email = null, $password = null) {
			$user = $this->findUser($email);
			if($user) {
			  # make sure data objects are working
			  // echo 'user email: '.$this->_user->email.'<br/>';
				if($this->isValidUser($email, $password )) {
					// echo 'Yay, you are a valid user!<br/>';
					# create user session
					Session::put($this->_sessionName, $this->_user->id);
					var_dump($_SESSION);
					echo 'Session ID:' .Session::get(Config::get('session/session_name')).'<br/>';
				  return true;
				}
			} # if user
			else {
			  $this->_addError("Your email was not found.");
			  return false;
			}
		}



		/*
		* Logs user in fater checking the email and password are valid.
		* @param string $email user email
		* @param string $password user password
		* @param boolean $remember if 'remember me' option is checked
		* @return boolean
		*/
		public function loginRemember($email = null, $password = null, $remember = false) {
			# if pass and email are not defined but user data is found, this means
			# the user asked to be remembered
			if (!$email && !$password && $this->UserExists()) {
			    # log user in creating a session
			    Session::put($this->_sessionName, $this->_user->id);
			} else {
			   	$user = $this->findUser($email);
					if($user) {
						  # make sure data objects are working
						  // echo 'user email: '.$this->_user->email.'<br/>';
						  if($this->isValidUser($email, $password )) {
							  // echo 'Yay, you are a valid user!<br/>';
							  # create user session
							  Session::put($this->_sessionName, $this->_user->id);
							  // echo 'Session ID:' .Session::get(Config::get('session/session_name')).'<br/>';
							  # create remember functionality using cookies
							  # this functinality will run if user select "remember me" option on register.phh
							  if($remember) {
								  $hash = Hash::unique();
								  # check if the cookie hash is already stored in the db
								  $currentHash = $this->_db->get('users_cookie', array('user_id','=', $this->_user->id) );
                  # if no cookie hash is found, create it
								  if(!$currentHash->getResults()) {
					          // var_dump($currentHash->getResults());
									  $this->_db->insert('users_cookie', array(
										  'user_id'     => $this->_user->id,
										  'cookie_hash' => $hash
									  ));
								  }else {
									  # if hash exists, set the new hash to the one that is already in the db
									  $hash = $currentHash->first()->cookie_hash;  # var_dump($hash);
								  }
									Cookie::put($this->_cookieName, $hash, Config::get( 'remember/cookie_expiry') );
									# check the output for the cookie. If the cookie is expired,
									# this will throw and error of undefined index
									// var_dump($_COOKIE );
									// var_dump($_COOKIE[$this->_cookieName] );
									// echo $_COOKIE[$this->_cookieName] ;
							  }
							  # end if remember
							  return true;
						  }
						  # end if isValidUser
					} else {
					  	# if user
							$this->_addError("That account doesn't exist. Enter a different email address
								               or <a href='../public/register.php'>get a new account.</a>");
							return false;
					}
			} # end if !email or pw
		}


    /*
    * Checks if user property that stores
    * user data is not empty
    * @return boolean
    */
		public function userExists() {
			return (!empty($this->_user) )? true : false;
		}


    /*
    * Logs user out
    * @return void
    */
		public function logout() {
			# delete user cookies and sessions when loggedout
		  $this->_db->delete('users_cookie', array('user_id', '=', $this->_user->id) );
			Session::delete($this->_sessionName);
			Cookie::delete($this->_cookieName);
		}


    /*
		* Retrieves user data stored in user property
		* @return array
		*/
		public function getUserData() {
	    return $this->_user;
		}


    /*
		* Checks is the user has administration or user
		* attributes
		* @return boolean
		*/
		public function hasPermission($key) {
      # grab the data from the group table
			$group = DB::getInstance()->get('groups', array('id', '=', $this->_user->group));
			// print_r($group);
			// var_dump($group);
			if($group->getResults()) {
				// echo 'getResult: ' .var_dump($group->getResults());
				# user json decode to convert the json object into an array
				$permission = json_decode($group->first()->permission, true);
				// echo 'Has permission type: ' .var_dump($permission );
				if($permission[$key] == true) {
					return true;
				}
			}
			return false;
		}


    /*
		* Sends activation account email to user's email address
		* @param string $from
		* @param string $name
		* @param string $to_email
		* @param string $subject
		* @param string $message
		*/
		public function sendActivationEmail($from, $name, $to_email, $subject, $message)  {
			# SMTP needs accurate times, and the PHP time zone MUST be set
			# This should be done in your php.ini, but this is how to do it
		  # if you don't have access to that.
			date_default_timezone_set('Etc/UTC');
			# require phpMailer.
			# At the very least you will need class.phpmailer.php. If you're using SMTP,
			# you'll need class.smtp.php, and if you're using POP-before SMTP, you'll
			# need class.pop3.php. For all of these, we recommend you use the
			# autoloader too as otherwise you will either have to require all
			# classes manually or use some other autoloader.
			# https://github.com/PHPMailer/PHPMailer
			require '../vendor/phpmailer/phpmailer/class.phpmailer.php';

      # Create a new PHPMailer instance
			$mail = new \PHPMailer();

			# Enable SMTP debugging
			# 0 = off (for production use)
			# 1 = client messages
			# 2 = client and server messages
			$mail->SMTPDebug = 0;

			$mail->isSMTP();                      # Tell PHPMailer to use SMTP
			$mail->Debugoutput = 'html';          # Ask for HTML-friendly debug output
			$mail->Host = 'smtp.gmail.com';       # Set the hostname of the mail server
			// $mail->Host = "tls://smtp.yourwebsite.com";

			$mail->Port = 587;                    # Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
			$mail->SMTPSecure = 'tls';            # Set the encryption system to use - ssl (deprecated) or tls
			$mail->SMTPAuth = true;               # Whether to use SMTP authentication
			$mail->Username = "yozaira@gmail.com"; # Username to use for SMTP authentication - use full email address for gmail
			$mail->Password = "yozieAb1";          # Password to use for SMTP authentication
			$mail->setFrom($from, $name);          # Set who the message is to be sent from
			//$mail->addReplyTo($from, $name);     # Set an alternative reply-to address
			$mail->addAddress($to_email);          # Set who the message is to be sent to
			$mail->Subject = $subject;             # Set the subject line
			$mail->MsgHTML($message);

			# send the message, check for errors
			if(!$mail->Send()) {
				// echo  $mail->ErrorInfo;  # for debugging
				return false;
			}
		}


    /*
		* Updates user 'active' field form cero to 1 if the
		* code from the query string is the same that was stored
		* on users when user registered
		* @param string $code
		*/
		public function activateAccount($code) {
			// $code = 'qkjqjqjqj9e99';  # test wrong code
			$user = DB::getInstance()->get('users', array('activation_code', '=' , $code) );
      // var_dump($user);
			if ($user->getResults()) {
				 //echo $user->first()->activation_code.'<br/>';
				 # if email and code are wrong, and active status is 0, throw an error
				if ($user->first()->activation_code === $code  &&  $user->first()->active == 0 ) {
				    return DB::getInstance()->updateByField('users', 'active', '1', 'activation_code', $code );
				} else {
				    $this->_addError('The url is either invalid or you already have activated your account.');
				}
			}
			return false;
		}

} # end class