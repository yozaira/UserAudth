<?php

  function base_url() {
    return "http://" . $_SERVER['SERVER_NAME'] .dirname($_SERVER['REQUEST_URI'] ). '/' ;
  }

  // echo 'Base URI: ' . base_url() .'<br/>';


  function site_url() {
    if(isset($_SERVER['HTTPS'])){
      $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    } else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  }

  // echo 'Base URI: ' . site_url() ;


  # http://php.net/manual/en/function.parse-url.php

  /*
  echo dirname(__FILE__).DIRECTORY_SEPARATOR.'classes.'.strtolower('User').'.php';
  echo '<hr/>';

  # Home url
   echo $_SERVER['HTTP_HOST'] .'<br/>';

  # current url (like: /myfodler/myfile.php?action=blabla
  echo $_SERVER["REQUEST_URI"].'<br/>';

  # ONLY current FILE url (like: /myfodler/myfile.php)
   echo $_SERVER['PHP_SELF'] .'<br/>';

  # current working folder location (like: /myfolder/subfolder)
  echo dirname($_SERVER["REQUEST_URI"]) .'<br/>';

  # current working folder location with file (like: /myfolder/subfolder/index.php)
  echo $_SERVER["REQUEST_URI"] .'<br/>';

  # current working folder (with ftp HOME folder)
  echo dirname(__FILE__);

  # Directory Separator
  echo DIRECTORY_SEPARATOR .'<br/>';
  */
