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

    if (defined('MOTEUR')) {
      $link = mysql_pconnect(DBMASTERSERVER, DBMASTERUSER, DBMASTERPASSWORD) or 
              die("Could not connect : " . mysql_error());
      $GLOBALS['masterdblink']=$link;
      $GLOBALS['mapdblink']   =$link; // unused in 'MOTEUR' mode
      $GLOBALS['slavedblink'] =$link;
      mysql_select_db(DBNAME, $link) or die("Could not select database");
    } else {
      $link = mysql_connect(DBMASTERSERVER, DBMASTERUSER, DBMASTERPASSWORD) or 
        die503("Could not connect : " . mysql_error());
      $GLOBALS['masterdblink']=$link;
      mysql_select_db(DBNAME, $link) or die503("Could not select database");
      $link = mysql_connect(DBMAPSERVER, DBMAPUSER, DBMAPPASSWORD) or 
        die503("Could not connect : " . mysql_error());
      $GLOBALS['mapdblink']=$link;
      mysql_select_db(DBNAME, $link) or die503("Could not select database");
      $link = mysql_connect(DBSLAVESERVER, DBSLAVEUSER, DBSLAVEPASSWORD) or 
        die503("Could not connect : " . mysql_error());
      $GLOBALS['slavedblink']=$link;
      mysql_select_db(DBNAME, $link) or die503("Could not select database");
    } 

?>
