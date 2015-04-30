<?php
# Include initialize file to make directories and classes available
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

$user = new User();

# The profile page checks whether the user is indeed logged in,
# if not it redirects him/her to the login page. Otherwise it
# displays the user profile information.
if(!$user->isLoggedIn()) {
  Redirect::to('index.php');
}

# add ?username=joe to address bar
# if it return false, token is working

if(Input::exists() ) {

  if (Token::check( input::get('token') ) ){
    # echo these values out to confirm that the form token and session token are the same
    // echo input::get('token').'<br/>';
    // var_dump($_SESSION);

    $validation_rules = array(
      'name' => array(
        'fieldName'  => 'Name',
        'required'   => true,
        'alpha'      => true,
        'min_length' => 2,
        'max_length' => 30
      ),
      'email' => array(
        'fieldName'  => 'Email',
        'required'  => true,
        'email'     => true,
        'unique'    => true
      )
    );

    # Create a new Validator instance
    $validator = new Validator($_POST, $validation_rules);
    $clean_data = $validator->get_fields();

    # if validation passed create an instance of the user and  process the data
    if($validator->validate() ){
      try {
        $user->updateUser( array(
            'name' => Input::get('name'),
            'email'=> Input::get('email')
        ));
        Session::flashMessage('home', 'Your data has been updated');
        Redirect::to('index.php');
      }
      catch (Exception $e) {
        die($e->getMessage());
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

  } else {  # the else wont show if using  if(Input::exists() )
      echo 'CSRF attack<br/>';
      var_dump(Token::check( input::get('token') ) ) ;     # boolean false
  }
}
?>

<?php include_once 'includes/header.php'; ?>
<div class="wrapper">
  <form id ="updateForm" class="allForms" action="" method="post">
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

    <h2 class="allForms-heading">Update Profile</h2>
    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>" />
    <label>Name</label>
    <input type="text" name="name", class ="form-control" value="<?php echo $user->getUserData()->name; ?>" placeholder="Enter new name"/>
    <br/>
    <label>Email</label>
    <input type="email", name="email", class ="form-control" value="<?php echo $user->getUserData()->email; ?>"  placeholder="Enter new email"/>
    <br/>
    <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" id="submit">Update Profile</button>
  </form>
</div>
<?php include_once 'includes/footer.php'; ?>








