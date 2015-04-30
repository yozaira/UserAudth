<?php
require_once '../core/init.php';
// var_dump($_SESSION);
include_once 'includes/header.php';
?>
<div class="wrapper">
    <div class="main col-md-4 col-centered">
    <h3>Welcome!</h3>
      <?php
      # if not logged in, give user the option to login or register
      echo '<p> If you dont have an account, <a href="register.php">Register</a> </p>';
      echo '<p> Already have an account? <a href="login.php">Log in</a> </p>';
      ?>
    </div><!--# end col -->
  </div><!--# end row -->
</div><!--# end wrapper -->