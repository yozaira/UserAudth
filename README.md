### Overfiew

This is a simple registration-login system website that uses PHP Object Oriented features, PHP Data Objects (PDO), and PSR-4 specification for autoloading classes.


### Requirements

* PHP 5.3.7 and above
* PHP Data Objects (PDO)
* MySQL database
* Composer and PSR-4
* PHPMailer class to send account activation email


### User actions

* Register
* Receive activation email
* Login
* Access user profile
* Change password
* Change email address
* Change name
* Reset password
* Logout


### Features

* Uses hash() with sha256, a secure hashing algorithm to hash password.
* Implements CSRF prevention using a Synchronizer Token to authenticate POST or GET requests.
* Uses PHP's [PDO](http://php.net/manual/en/book.pdo.php) database interface and prepared statements, an efficient system against SQL injection.

### Future improvements:

* Blocking attackers by IP for any defined time after any amount of failed actions on the portal.
* Adding error pages.
* Write a funcion to log the any hack attempt for our own reference.


### Directory structure and Files:

* app    - stores classes
* core   - stores initialization file with db and session configuration info.
* public - contains site's directories and files
* vendor - stores Composer packeges
* composer.json
* composer.lock
