<?php

include_once("functions.php");

function get_error($code) {

    $ws_error_types = Array(
        //Format
        "PARM01" => "parms should be specified",
        "PARM02" => "parms is not a valid array",
        "PARM03" => "specified (maybe unspecified) action is not valid",
        //Auth
        "AUTH01" => "idu is mandatory for safety reasons and should match your login",
        "AUTH02" => "Your request does not match the idu you are login in",
        //pim
        "PIM01" => "pim is unspecified",
        "PIM02" => "pim should be int",
        "PIM03" => "pim should be in range 1..5",
        //pip
        "PIP01" => "pip is unspecified",
        "PIP02" => "pip should be numeric",
        //wp (and also pip when pip = wp)
        "WP01" => "pip/wp is unspecified",
        "WP02" => "pip/wp should be an array",
        "WP03" => "wplat is unspecified",
        "WP04" => "wplon is unspecified",
        "WP05" => "wp parameters should be numerics",        
    );
    
    return Array("code" => $code, "msg" => $ws_error_types[$code]);
}

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

function ask_for_auth($usage) {
    unset($_SERVER['PHP_AUTH_USER']);
    unset($_SERVER['PHP_AUTH_PW']);
    header('WWW-Authenticate: Basic realm="VLM Access"');
    header($_SERVER["SERVER_PROTOCOL"]." 401 Unauthorized");
    header("Content-Type: text/plain; charset=UTF-8");
    echo $usage;
}

function login_if_not($usage = "") {
    
    session_start();
    // do we know the user from a previous login session?
    if (array_key_exists('idu', $_SESSION) && array_key_exists('loggedin', $_SESSION) 
        && ($_SESSION['loggedin'] == 1) ) {
        $idu = $_SESSION['idu'];
        $pseudo = $_SESSION['login'];
        $IP = $_SESSION['IP'];
    } else {
        // fallback to HTTP auth
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ) {
            ask_for_auth($usage);
            exit;
        } else {
            $pseudo = $_SERVER['PHP_AUTH_USER'];
            $passwd = $_SERVER['PHP_AUTH_PW'];
            $idu = checkAccount($pseudo, $passwd);
            // FIXME, do we need to check after utf-8 transform?
            /*
              if ((checkAccount(htmlentities($pseudo,ENT_COMPAT), 
              htmlentities($password, ENT_COMPAT)) != FALSE)
              || (checkAccount(htmlentities($pseudo,ENT_COMPAT,"UTF-8"),
              htmlentities($password, ENT_COMPAT,"UTF-8")) |= FALSE)) {
            */
            if ($idu === False) {
              ask_for_auth($usage);
              exit();
            }
            login($idu, $pseudo);
        }
    }
}

function logout_if_not($usage="Logout usage:\nUsername: test\nPassword: ko\nto force logout.") {
    
    logout();
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])
        && $_SERVER['PHP_AUTH_USER'] == "test" && $_SERVER['PHP_AUTH_PW'] == "ko") {
        return True;
        exit;
    } else {
        ask_for_auth($usage);
        exit;
    }
}

?>
