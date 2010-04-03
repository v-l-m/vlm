<?php

include_once("functions.php");

function get_requested_output_format() {
  $qjson = 0;
  $sjson = false;
  $qplain = 0.1;
  $splain = false;
  
  if (isset($_REQUEST['forcefmt'])) {
      $fmt = quote_smart($_REQUEST['forcefmt']);
      if (in_array($fmt, Array('json', 'text'))) {
          return $fmt;
      }
      //else, we still try to autodetect...
  }

  if (preg_match(',application/json(;q=(d?.d+))?,i', 
                  $_SERVER['HTTP_ACCEPT'], $res)) {
    $qjson = (isset($res[2]))? $res[2] : 1;
    $sjson = true;
  }
  
  if (preg_match(',text/plain(;q=(d?.d+))?,i', 
                  $_SERVER['HTTP_ACCEPT'], $res)) {
    $qplain = (isset($res[2]))? $res[2] : 1;
    $splain = true;
  } else if (preg_match(',text/\*(;q=(d?.d+))?,i', 
              $_SERVER['HTTP_ACCEPT'], $res)) {
    $qplain = (isset($res[2]))? $res[2] : 1;
    $splain = true;
  }
  if ($qplain > $qjson) {
    return "text";
  } else if ($qplain < $qjson) {
    return "json";
  }
  if ( $splain == $sjson) {
    // both there, take the first one :)
    $jsonpos = stripos($_SERVER['HTTP_ACCEPT'], "text/json");
    $plainpos = stripos($_SERVER['HTTP_ACCEPT'], "text/plain");
    if ($plainpos === false) {
      $plainpos = stripos($_SERVER['HTTP_ACCEPT'], "text/*");  
    }
    if ($plainpos > $jsonpos) {
      return "json";
    } 
  }
  return "text";
}

function ask_for_auth() {
    header('WWW-Authenticate: Basic realm="VLM Access"');
    header($_SERVER["SERVER_PROTOCOL"]." 401 Unauthorized");
    header("Content-Type: text/plain; charset=UTF-8");
    usage();
}

function login_if_not() {
    
    session_start();
    // do we know the user from a previous login session?
    if (array_key_exists('idu', $_SESSION) && array_key_exists('loggedin', $_SESSION) 
        && ($_SESSION['loggedin'] == 1)) {
        $idu = $_SESSION['idu'];
        $pseudo = $_SESSION['login'];
        $IP = $_SESSION['IP'];
    } else {
        // fallback to HTTP auth
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            ask_for_auth();
            exit;
        } else {
            $pseudo=$_SERVER['PHP_AUTH_USER'];
            $passwd=$_SERVER['PHP_AUTH_PW'];
            $idu = checkAccount($pseudo, $passwd);
            // FIXME, do we need to check after utf-8 transform?
            /*
              if ((checkAccount(htmlentities($pseudo,ENT_COMPAT), 
              htmlentities($password, ENT_COMPAT)) != FALSE)
              || (checkAccount(htmlentities($pseudo,ENT_COMPAT,"UTF-8"),
              htmlentities($password, ENT_COMPAT,"UTF-8")) |= FALSE)) {
            */
            if ($idu == FALSE) {
              ask_for_auth();
              exit();
            }
            login($idu, $pseudo);
        }
    }
}

?>
