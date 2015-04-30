<?php
# Include initialize file to make directories and classes available
require_once '../core/init.php';
include_once 'includes/header.php';

# use a short alias for validation class namespace
# or simply call namespace of the used classes
use App\User;
use App\Session;
use App\Redirect;

# verify that the sent uri has a code var and sanitize it
# before activating account
if(isset($_GET['code'])){
  $activation_code = filter_var($_GET['code'], FILTER_SANITIZE_STRING);
  $user = new User();
  $activate = $user->activateAccount($activation_code);
  if ($activate) {
      Session::flashMessage('success', 'Your account has been activated! <p> You can now login.</p>');
      Redirect::to('login.php');
    } else {
        // var_dump($user->getErrors() );
        $errors = $user->getErrors() ;
    }
} else  {
    # No match -> invalid url or account has already been activated.
    $error_url = '<div class="alert alert-danger"> This page has been accessed in an error.
                  Please use the link that has been send to your email</div>';
}

?>
<div class="wrapper">
  <div class=" col-md-4 col-centered">
    <!-- output email verification erros -->
    <?php
    if (!empty($errors)) {
       echo '<div class="alert alert-danger text-center">';
       foreach ($errors as $error ) { echo $error.'<br/>';  }
       echo '</div>';
    }
    if (isset($error_url) ) {
       echo $error_url;
    }
    ?>
    </div>
  </div>
