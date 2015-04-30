<?php
#  Include initialize file to make directories and classes available
require_once '../core/init.php';

# use a short alias for validation class namespace
# or simply call namespace of the used classes
use App\User;
use App\Redirect;

$user = new User();
$user->logout();
Redirect::to('index.php');