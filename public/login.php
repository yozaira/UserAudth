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
use App\Form;

if(Input::exists() ) {
  # test if the data is being posted
  // echo Input::get('email').'<br/>';

  if(Token::check( input::get('token') ) ) {
    # echo these values out to confirm that the form token and session token are the same
    // var_dump($_POST);
    // var_dump($_SESSION);

    # initialize validation rules array
    $validation_rules = array(
      'email' => array(
        'fieldName'=> 'Email',
        'required' => true,
        'email'    => true
        //'verify' => true
      ),
      'password'    => array(
        'fieldName' => 'Password',
        'required'  => true
        //'max_length' => 20,
        //'min_length'  => 6
      )
    );

    # create a new Validator instance
    $validator  = new Validator($_POST, $validation_rules);
    # sanitize input fields
    $clean_data = $validator->get_fields();

    # if validation passed create an instance of the user and  process the data
    if ($validator->validate()){
      $user = new User();
      # pass this value to loging method
      $remenber = (Input::get('remember') === 'on') ? true : false ;
      $login = $user->loginRemember( Input::get('email'), Input::get('password'), $remenber );
      if($login) {
         # if logged in succesfuly, redirect user
         // echo 'You are logged in!'.'<br/>';
         Session::flashMessage('success', 'You logged in succesfully');
         // Redirect::to('index.php');
         Redirect::to('profile.php');
      } else {
         # output errors
         // var_dump($user->getErrors()) ;
         # If the pw or email are not valid, this method will display the errors of isValidUser() method.
         $login_errors = $user->getErrors() ;
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
      # the else wont show if using  if(Input::exists() )
      echo 'CSRF attack<br/>';
      var_dump(Token::check( input::get('token'))) ;  # boolean false
  }
}
?>

<?php include_once 'includes/header.php'; ?>

<div class="wrapper">
  <form id ="loginForm" class="allForms" action="" method="post">
    <!-- PHP errors response -->
    <div>
    <?php
    if(!empty($validation_errors)) {
       echo '<div class="alert alert-danger">';
       foreach ($validation_errors as $error ) { echo $error.'<br/>';}
       echo '</div>';
     }
     elseif(!empty($login_errors)) {
       echo '<div class="alert alert-danger">';
       foreach ($login_errors as $err ) { echo $err.'<br/>';}
       echo '</div>';
     }
    ?>
    <!-- Flash Messages here-->
    <?php
    if(Session::exists('success') ) {
      echo '<div class="alert alert-info text-center">' .Session::flashMessage('success') .'</div>' ;
    }
    ?>
    <!-- # flash Messages -->
   <h2 class="allForms-heading">Login</h2>
    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>" />
    <!-- Order of method parameters: label, type, name, id, value, errors, placeholder -->
    <?php Form::create_form_input('Email', 'text', 'email', 'email',  ' ', '', 'Enter your email'); ?><br/>
    <?php Form::create_form_input('Password', 'password',  'password', 'pw', ' ', ' ', 'Choose a Password' ); ?>
    <label for ="remember"><input type="checkbox" name="remember"/> Remember me</label><br/><br/>
    <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" id="submit">Login</button>
  </form>
</div>
<?php include_once 'includes/footer.php'; ?>