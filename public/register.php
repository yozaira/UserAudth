<?php
#  Include initialize file to make directories and classes available
require_once '../core/init.php';

# use a short alias for validation class namespace
# or simply call namespace of the used classes
use App\DB;
use App\Validator;
use App\User;
use App\Redirect;
use App\Input;
use App\Token;
use App\Session;
use App\Hash;
use App\Form;

# if user is loggedin, redirect
$user = new User();
if($user->isLoggedIn()){
  Redirect::to('profile.php');
}

if(Input::exists()) {
  if (Token::check(input::get('token') )){
    # add '?username=joe' to address bar
    # if it return false, token is working
    # echo these values out to confirm that the
    # form token and session token are the same
    // var_dump($_POST);
    // var_dump($_SESSION);

    # initialize validation rules array
    $validation_rules = array(
      'name' => array(
        'fieldName'  => 'Name',
        'required'   => true,
        'alpha'      => true,
        'min_length' => 2,
        'max_length' => 30
      ),
      'email' => array(
        'fieldName' => 'Email',
        'required'  => true,
        'email'     => true,
        'unique'    => true
      ),
      'password' => array(
        'fieldName'  => 'Password',
        'required'   => true,
        'alpha_num'  => true,
        'min_length' => 6,
        'max_length' => 20
      ),
      'pw-confirm'  => array(
        'fieldName' => 'Confirm Password',
        'required'  => true,
        'matches'   => 'password'
      )
    );
    # Create a new Validator instance
    $validator = new Validator($_POST, $validation_rules);
    # sanitize input fields
    $clean_data = $validator->get_fields();

    # if validation passed create an instance of the
    # user and process the data to register user
    if ($validator->validate()){
        # create the salt for the pw and salt field in the db
        $salt = Hash::salt(32);
        # user activation code to be sent to user's email
        $activation_code = md5(uniqid(rand(), true));
        # call create method to insert data
        $register = DB::getInstance()->insert('users', array(
            'name'  => Input::get('name'),
            'email' => Input::get('email'),
            'pass'  => Hash::make(Input::get('password'), $salt), # class for pw encryption
            'salt'  => $salt,
            'activation_code' => $activation_code,
            'group' => 1,
            'joined'=> date('Y-n-d H:i:s')
        ));
        if($register) {
           # include file with the html email. Contains the mesage body variable.
           # Can be substitute for a shorter massage with no html or style.
           include 'verification-email.php';
           # this method sends the cativation email
           $user->sendActivationEmail('yozaira@gmail.com','SiteName', Input::get('email'), 'Sign-up Verification', $message);
           # if data insert succesfuly, redirect user
           Redirect::to('account-created.php');
        }
    } else {
        # output errors
        // var_dump($validator->get_errors() );
        foreach ( $validator->get_errors() as $input_errors) {
          foreach ($input_errors as $error_key => $value) {
           # var_dump($value);
          $validation_errors[] = $value ;
          }
        }
    }
  } else {
    # this else wont be executed if using 'if(Input::exists())'control
    echo 'CSRF attack<br/>';
    // var_dump(Token::check( input::get('token')));  # boolean false
  }
}

?>
<?php include_once 'includes/header.php'; ?>
<div class="wrapper">
  <form id ="registerForm" class="allForms" action="" method="post">
    <!-- PHP errors response -->
    <?php
    if(!empty($validation_errors)) {
       echo '<div class="alert alert-danger">';
       foreach ($validation_errors as $error ) { echo $error.'<br/>';}
       echo '</div>';
     }
    ?><!-- Flash Messages here-->
    <?php
    if(Session::exists('success') ) {
       echo '<div class="alert alert-info text-center"><h4>' .Session::flashMessage('success') .'<h4/></div>' ;
    }
    ?> <!-- # flash Messages -->
   <h2 class="allForms-heading">Register</h2>
    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>" />
    <!-- Order of method parameters: label, type, name, id, value, errors, placeholder -->
    <?php Form::create_form_input( 'Name', 'text', 'name', 'name', ' ',   'Enter your name' ) ;?><br/>
    <?php Form::create_form_input( 'Email', 'text', 'email', 'email', ' ', 'Enter your email' ) ;?><br/>
    <?php Form::create_form_input( 'Choose a Password', 'password', 'password',  'pw',  ' ',  ' ', 'Choose a Password' ) ;?>
    <?php Form::create_form_input( 'Reenter your Password', 'password', 'pw-confirm', 'pw-confirm',  ' ', ' ', 'Re-enter your Password' ) ;?><br/>
    <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" id="submit">Register</button>
  </form>
</div>
<?php include_once 'includes/footer.php'; ?>
