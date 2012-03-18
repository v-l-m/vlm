<?php

include_once("functions.php");

class WSBase {

    public $answer = Array();
    public $input = null;
    public $request = null;

    function __construct() {
        // now start the real work
        login_if_not($this->usage());

        //surface test
        $this->input = get_cgi_var('parms', null);
        if (is_null($this->input)) $this->reply_with_error('PARM01');
        $this->request = json_decode($this->input, true);
        if (is_null($this->request) || !is_array($this->request)) $this->reply_with_error("PARM02");

        //ask for debug
        if (isset($this->request['debug']) && $this->request['debug']) {
            $this->answer['request'] = $this->request;
            $this->warning("Debug mode for testing purposes only");
        }
        
        if (isset($_GET["parms"])) $this->warning("http/GET is allowed for testing purposes only");

    }
    
    function usage() {
        return "Usage:
        Documentation is in progress and should be available at the following url :
        http://dev.virtual-loup-de-mer.org/vlm/wiki/webservices";
    }

    function warning($msg) {
        if (!isset($this->answer['warnings'])) $this->answer['warnings'] = Array();
        $this->answer['warnings'][] = $msg;
    }
    
    function reply() {
        $fmt = "json";

        switch ($fmt) {
            //retourne du json par défaut, mais peut être qu'on pourra supporter autre chose plus tard
            case "json":
            default:
                header('Content-type: application/json; charset=UTF-8');
                echo json_encode($this->answer);
        }
        exit();
    }

    function reply_with_error($code, $error_string = null) {
        $this->answer['success'] = False;
        $this->answer['error'] = get_error($code);
        $this->answer['usage'] = $this->usage();
        if (!is_null($error_string)) $this->answer['error']['custom_error_string'] = $error_string;
        $this->reply();
    }
    
    function reply_with_success() {
        $this->answer['success'] = True;
        $this->reply();
    }

    function reply_with_error_if_not_exists($key, $code, $request = null) {
        if (is_null($request)) $request = $this->request;
        if (!isset($request[$key])) $this->reply_with_error($code);
    }

    function finish() {
        //Must be surcharged (?) by inherited classes
        $ws->reply_with_error("CORE01");
    }
    
}

class WSBaseBoatsetup extends WSBase {
    public $fullusers = null;
    
    function __construct() {
        parent::__construct();
        //auth check
        $this->reply_with_error_if_not_exists('idu', "AUTH01");
        if ($_SESSION['idu'] != $this->request['idu']) $this->reply_with_error("AUTH02");

        //OK, on peut instancier l'utilisateur
        $this->fullusers = new fullUsers(getLoginId());
    }

    function finish() {
        if ($this->fullusers->users->error_status) {
            $this->reply_with_error("CORE01", $this->fullusers->users->error_string);
        } else {
            $this->reply_with_success();
        }
    }

    function check_prefs_list() {
        $this->reply_with_error_if_not_exists('prefs', 'PREFS01');
        $prefs = $this->request['prefs'];
        foreach($prefs as $k => $v) {
            if (!in_array($k, explode(',', USER_PREF_ALLOWED))) {
                $this->reply_with_error('PREFS02', "BAD KEY:$k");
            }
        }
        return $prefs;
    }

    function check_pilototo_tasktime() {
        $this->reply_with_error_if_not_exists('tasktime', 'PILOTOTO01');
        $tasktime = $this->request['tasktime'];
        if (!is_int($tasktime)) $this->reply_with_error('PILOTOTO02');
        return $tasktime;
    }

    function check_pilototo_taskid() {
        $this->reply_with_error_if_not_exists('taskid', 'PILOTOTO03');
        $taskid = $this->request['taskid'];
        if (!is_int($taskid)) $this->reply_with_error('PILOTOTO04');
        return $taskid;
    }    

    function check_pim() {
        $this->reply_with_error_if_not_exists('pim', 'PIM01');
        $pim = $this->request['pim'];
        if (!is_int($pim)) $this->reply_with_error('PIM02');
        return $pim;
    }

    function check_pip_with_float() {
        $this->reply_with_error_if_not_exists('pip', 'PIP01');
        $pip = $this->request['pip'];
        if (!is_numeric($pip)) $this->reply_with_error('PIP02');
        return $pip;
    }
    
    function check_pip_with_wp() {
        //existence et pip/wp en tant qu'array
        $this->reply_with_error_if_not_exists('pip', 'WP01');
        $pip = $this->request['pip'];
        if (!is_array($pip)) $this->reply_with_error('WP02');
        //existence des paramètres
        $this->reply_with_error_if_not_exists('targetlat', 'WP03', $pip);
        $this->reply_with_error_if_not_exists('targetlong', 'WP04', $pip);
        if (!isset($pip['targetandhdg'])) {
            $pip['targetandhdg'] = -1.;
        }
        if (is_numeric($pip['targetlat']) && is_numeric($pip['targetlong']) && (is_numeric($pip['targetandhdg']))) {
            return $pip;
        } else {
            $this->reply_with_error('WP05');
        }
    }

    function target_array2string($target) {
        if (isset($target['targetandhdg'])) {
            return sprintf("%f,%f@%f", $target['targetlat'], $target['targetlong'], $target['targetandhdg']);
        } else {
            return sprintf("%f,%f@%f", $target['targetlat'], $target['targetlong']);
        }
    }

}

function get_error($code) {

    $ws_error_types = Array(
        //Format
        "PARM01" => "parms should be specified",
        "PARM02" => "parms is not a valid array",
        "PARM03" => "specified (maybe unspecified) action is not valid",
        //Auth
        "AUTH01" => "idu is mandatory for safety reasons and should match your login",
        "AUTH02" => "Your request does not match the idu you are login in",
        //SQL
        "CORE01" => "Something went wrong when passing orders to the core. You should report this to the developpers ! (See the custom_error_string)",
        //pim
        "PIM01" => "pim is unspecified",
        "PIM02" => "pim should be int",
        "PIM03" => "pim should be in range 1..5",
        //pip
        "PIP01" => "pip is unspecified",
        "PIP02" => "pip should be numeric",
        //pilototo
        "PILOTOTO01" => "tasktime is unspecified",
        "PILOTOTO02" => "tasktime should be int (EPOC)",
        "PILOTOTO03" => "taskid is unspecified",
        "PILOTOTO04" => "taskid should be int",
        //wp (and also pip when pip = wp)
        "WP01" => "pip/wp is unspecified",
        "WP02" => "pip/wp should be an array",
        "WP03" => "targetlat is unspecified",
        "WP04" => "targetlong is unspecified",
        "WP05" => "wp parameters should be numerics",
        //prefs
        "PREFS01" => "prefs is unspecified",
        "PREFS02" => "key is not allowed"
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

function login_if_not($usage = "No usage given") {
    
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