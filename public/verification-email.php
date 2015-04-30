<?php
use App\Input;

$message = '
<html>
<head>
<title>Sign-up Confirmation Email</title>
</head>
<body>
<h3>Thanks for signing up! </h3>
<p>
Your account has been created. You can login with the following credentials after
you have activated your account by pressing the url below.
</p>
</tr>';
# include user email and helper function to get site's base url
$message .=
'<table><tr><td><b>Username</b>: '.Input::get('email').'</td></tr>
<tr>
<p>Please click this link to activate your account:</p>
<p>'.base_url().'verify.php?code='.urlencode($activation_code).'</p>
</tr>
</table>
</body>
</html>';

?>

