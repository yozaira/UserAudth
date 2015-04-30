<?php
#  Include initialize file to make directories and classes available
require_once '../core/init.php';

# use a short alias for validation class namespace
# or simply call namespace of the used classes
use App\Validator;
use App\User;
use App\Redirect;
use App\Input;
use App\Token;
use App\Session;
use App\Hash;
use App\Form;

$user = new User();

# The profile page checks whether the user is indeed logged in,
# if not it redirects him/her to the login page. Otherwise it
# displays the user profile information.
if(!$user->isLoggedIn()) {
  Redirect::to('index.php');
}

if(Input::exists() ) {
  if (Token::check( input::get('token') ) ) {
     # echo these values out to confirm that the form token and session token are the same
     // var_dump($_SESSION);

     # define array for validation errors
     $validation_errors = array();

     # define validation rules
     $validation_rules = array(
      'password_current' => array(
        'fieldName'=> 'Current Password',
        'required' => true
      ),
      'new-password'=> array(
        'fieldName' => 'New Password',
        'required'  => true,
        'alpha_num' => true,
        'min_length'=> 6,
        'max_length'=> 20
      ),
      'reenter-new-password' => array(
        'fieldName'=> 'Re-enter New Password',
        'required' => true,
        'matches'  => 'new-password'
      )
    );

    # Create a new Validator instance
    $validator = new Validator($_POST, $validation_rules);
    # sanitize input fields
    $clean_data = $validator->get_fields();

    # if validation, process the data
    if ($validator->validate()){
      # make a hash from the corruent password, and
      # then  compared it with the pw stored in db
      if(Hash::make( input::get('password_current'), $user->getUserData()->salt ) !== $user->getUserData()->pass ) {
        $validation_errors[] = 'The password you entered is incorrect';
      } else {
        # if there is a match, create a new salt for the new pw
        $salt = Hash::salt(32);
        # updated password for the new password.
        $user->updateUser( array(
          'pass' => Hash::make(Input::get('new-password'), $salt ),
          'salt' => $salt
        ));
        # if success, redirect user
        // echo 'Your password has been changed';
        Session::flashMessage('home', 'Your password has been changed');
        Redirect::to('index.php');
      }

    } else {
        # output errors
        // var_dump($validator->get_errors() );
        foreach ( $validator->get_errors() as $input_errors) {
          foreach ($input_errors as $error_key => $value) {
          # var_dump($value);
          # pass errors to validatin array
          $validation_errors[] = $value ;
          }
        }
    }

  } else {
      # the else wont show if using  if(Input::exists() )
      echo 'CSRF attack!<br/>';
      var_dump(Token::check( input::get('token') ) ) ;     # boolean false
  }
}

?>
<?php include_once 'includes/header.php'; ?>

<div class="wrapper">
  <form id ="passForm" class="allForms" action="" method="post">
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

    <h2 class="allForms-heading">Change Password</h2>
    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>" />
    <!-- Order of method parameters: label, type, name, id, value, errors, placeholder -->
    <?php Form::create_form_input('Current Password', 'password', 'password_current', 'password_current', ' ', ' ', 'Enter your password' ) ;?>
    <br/>
    <?php Form::create_form_input('New Password', 'password', 'new-password', 'new-password', ' ', ' ', 'Enter your new password') ;?>
    <br/>
    <?php Form::create_form_input('Re-enter New Password', 'password', 'reenter-new-password', 'reenter-new-password', ' ', ' ', 'Re-enter your new password') ;?>
    <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" id="submit">Change Password</button>
    <br/>
  </form>
</div>
<?php include_once 'includes/footer.php'; ?>








