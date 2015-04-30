<?php
#  Include initialize file to make directories and classes available
require_once '../core/init.php';

# use a short alias for validation class namespace
# or simply call namespace of the used classes
use App\User;
use App\Redirect;
use App\Session;

$user = new User();
# The profile page checks whether the user is indeed logged in,
# if not it redirects him/her to the login page. Otherwise it
# displays the user profile information.
if(!$user->isLoggedIn()){
  Redirect::to( 'login-remember.php');
}
?>

<?php include_once 'includes/header.php'; ?>
<div class="wrapper">
  <div class="row">
   <div class="profile col-md-4 col-centered">
      <?php
      # display flash messages if available
      if(Session::exists('success') ) {
        echo '<div class="text-center alert alert-info">'.Session::flashMessage('success').'</div>' ;
      }
      if(Session::exists('home') ) {
        echo '<div class="text-center alert alert-info">'.Session::flashMessage('home').'</div>' ;
      }

      # The profile page checks whether the user is indeed logged in,
      # if not it redirects him/her to the login page. Otherwise it
      # displays the user profile information.
      if($user->isLoggedIn() ) { ?>
         <h1 class="text-center">Hello <?php echo sanitize( $user->getUserData()->name); ?></h1>
        <ul class="profile-list">
          <li>
            <span class="field">Name: </span>
            <span class="value"><?php echo sanitize( $user->getUserData()->name); ?></span>
            <div class="clear"> </div>
          </li>
          <li>
            <span class="field">Email: </span>
            <span class="value"><?php echo sanitize( $user->getUserData()->email ); ?></span>
            <div class="clear"> </div>
          </li>
          <li>
            <span class="field">Signup Date: </span>
            <span class="value"><?php echo sanitize( $user->getUserData()->joined ); ?></span>
            <div class="clear"> </div>
          </li>
        </ul>

        <div>
          <ul>
            <li> <a href="update.php">Update Profile</a></li>
            <li> <a href="changepassword.php">Change Password</a></li>
            <!-- When the user clicks Log out, the user is logged out and redirected to the login page again. -->
            <li> <a href="logout.php">Log out</a></li>
          </ul>
        </div>
        <?php
          # check if user is admin.
          # this check can be perform on a page by page basis.
          # Administrator has an id of 2 in table group of the db.
          # And Users has id of 1
        	if($user->hasPermission('admin') ) {
        		echo '<p>You are logged in as an administrator!</p>';
        	}
        	else {
        		echo '<p>If you are an admin, <a href="#">Log in</a> as
            administrator or <a href="#">Create an account</a>';
        	}

      } else {
         echo '<p> If you dont have an account, <a href="register.php">Register</a> </p>';
         echo '<p> Already have an account? <a href="login.php">Log in</a> </p>';
      }
      ?>
    </div><!--# end col -->
  </div><!--# end row -->
</div><!--# end wrapper -->
