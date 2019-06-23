<?php
    require_once('config-funcs.php');
    include_once('param.php');

    /*********db_connect****************/
    define_if_not("DBSLAVESERVER", DBMASTERSERVER); // define if not defined in param.php
    define_if_not("DBSLAVEUSER", DBMASTERUSER); // define if not defined in param.php
    define_if_not("DBSLAVEPASSWORD", DBMASTERPASSWORD); // define if not defined in param.php
    define_if_not("DBMAPSERVER", DBSLAVESERVER); // define if not defined in param.php
    define_if_not("DBMAPUSER", DBSLAVEUSER); // define if not defined in param.php
    define_if_not("DBMAPPASSWORD", DBSLAVEPASSWORD); // define if not defined in param.php

    if (defined('MOTEUR')) 
    {
      $link = mysqli_connect('p:'.DBMASTERSERVER, DBMASTERUSER, DBMASTERPASSWORD) or 
              die("Could not connect : " . mysqli_error());
      $GLOBALS['masterdblink']=$link;
      $GLOBALS['mapdblink']   =$link; // unused in 'MOTEUR' mode
      $GLOBALS['slavedblink'] =$link;
      mysqls_select_db($link, DBNAME) or die("Could not select database");
    } 
    else 
    {
      $link = mysqli_connect('p:'.DBMASTERSERVER, DBMASTERUSER, DBMASTERPASSWORD) or 
        die503("Could not connect : " . mysqli_error());
      $GLOBALS['masterdblink']=$link;
      mysqli_select_db($link, DBNAME) or die503("Could not select database");
      mysqli_set_charset($link,'utf8');
      
      $link = mysqli_connect('p:'.DBMAPSERVER, DBMAPUSER, DBMAPPASSWORD) or 
        die503("Could not connect : " . mysqli_error());
      $GLOBALS['mapdblink']=$link;
      mysqli_select_db($link, DBNAME) or die503("Could not select database");
      mysqli_set_charset($link,'utf8');
      
      $link = mysqli_connect('p:'.DBSLAVESERVER, DBSLAVEUSER, DBSLAVEPASSWORD) or 
        die503("Could not connect : " . mysqli_error());
      $GLOBALS['slavedblink']=$link;
      mysqli_select_db($link, DBNAME) or die503("Could not select database");
      // Force charset to UTF-8 to help json encoder with db results.
      mysqli_set_charset($link,'utf8');
    } 

?>
