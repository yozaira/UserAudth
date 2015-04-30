<?php namespace App;

 /**
 * Input class
 * Verifies if there is a POST or GET request
 * and retrives the name of each input field
 */
class Input {

 /*
 * Verifies if there is a POST or GET request
 * @param string $type
 * @return boolean
 */
  public static function exists($type= 'post') {
    switch($type) {
      case 'post':
      return (!empty($_POST)) ? true: false;
      break;
      case 'get':
      return (!empty($_GET)) ? true: false;
      break;
      default:
      return false;
      break;
    }
  }


  /*
  * Retrieves the name of the input field of the
  * POST or GET request
  * @param string $item
  * @return string name of input field
  */
  public static function get($item) {
    if(isset($_POST[$item]) ) {
      return trim($_POST[$item] );
    } else {
       if(isset($_GET[$item])) {
          return trim($_GET[$iten] );
       }
    }
    return '';
  }

}
