<?php

include_once("functions.php");
include_once('players.class.php');
include_once('base.class.php');
require_once('users.class.php');

class WSBase extends baseClass {

    public $answer = Array();
    public $maxage = 0;

    function __construct() {
        parent::baseClass();
        // now start the real work
        login_if_not($this->usage());
    }

    function queryRead($query) {
        $res = parent::queryRead($query);
        if ($res) { 
            return $res;
        } else {
            $this->reply_with_error("CORE01", $this->error_string);
        }
    }
    
    function queryWrite($query) {
        $res = parent::queryWrite($query);
        if ($res) { 
            return $res;
        } else {
            $this->reply_with_error("CORE01", $this->error_string);
        }
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
                if ($this->maxage > 0 && !isset($_GET['nocache'])) {
                    header("Cache-Control: max-age=".$this->maxage.", must-revalidate");
                } else {
                    header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
                }
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

    function finish() {
        //Must be surcharged (?) by inherited classes
        $ws->reply_with_error("CORE01");
    }

    function check_cgi($var, $err_exists, $default=null) {
        $foo = get_cgi_var($var, $default);
        if (is_null($foo)) $this->reply_with_error($err_exists);
        return $foo;
    }

    function check_cgi_int($var, $err_exists, $err_gt_0, $default = null) {
        $foo = $this->check_cgi($var, $err_exists, $default);
        $foo = intval($foo);
        if (is_int($foo) && $foo > 0) {
            return $foo;
        } else {
            $this->reply_with_error($err_gt_0);
        }
    }

    function check_cgi_intzero($var, $err_exists, $err_notint, $default = null) {
        $foo = $this->check_cgi($var, $err_exists, $default);
        $foo = intval($foo);
        if (is_int($foo)) {
            return $foo;
        } else {
            $this->reply_with_error($err_notint);
        }
    }


}

class WSBasePlayer extends WSBase {
    function __construct() {
        parent::__construct();
        //FIXME : is this useless now that only players may log in ?
        if (!isPlayerLoggedIn()) $this->reply_with_error('AUTH03');
    }
}

class WSBaseRace extends WSBase {
    var $idr = null;
    function __construct() {
        parent::__construct();
    }
    
    function require_idr() {
        $idr = $this->check_cgi_int('idr', 'IDR01', 'IDR02');
        if (!raceExists($idr)) $this->reply_with_error('IDR03');
        $this->idr = $idr;
    }
    
}

class WSBaseBoat extends WSBasePlayer {
    var $idu = null;
    var $debug = true;

    function __construct() {
        parent::__construct();
        $this->check_idu();
    }
    
    function check_idu() {
        $this->idu = $this->check_cgi_int('idu', 'IDU01', 'IDU02');
    }
    
    function check_debug() {
        $dbg = get_cgi_var('debug', 0);
        if (!is_int($dbg)) $this->reply_with_error('DBG02');
        $dbg = intval($dbg);
        $this->debug = ($dbg > 0);
    }

}

class WSSetup extends WSBase {
    //should be an extends from WSBaseBoat(?) starting from v0.15
    public $input = null;
    public $request = null;

    function __construct() {
        parent::__construct();
        
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
    
    function reply_with_error_if_not_exists($key, $code, $request = null) {
        if (is_null($request)) $request = $this->request;
        if (!isset($request[$key])) $this->reply_with_error($code);
    }

    
}

class WSBasePlayersetup extends WSSetup {

    public $player = null;

    function __construct() {
        //l'auth a été checké dans WSBase
        parent::__construct();

        //OK, on peut instancier le joueur
        $this->player = getLoggedPlayerObject();
    }

    function check_prefs_list() {
        $this->reply_with_error_if_not_exists('prefs', 'PREFS01');
        $prefs = $this->request['prefs'];
        foreach($prefs as $k => $v) {
            if (!in_array($k, explode(',', PLAYER_PREF_ALLOWED))) {
                $this->reply_with_error('PREFS02', "BAD KEY:$k");
            }
            if (is_array($v)) {
                if (!isset($v['pref_value'])) $this->reply_with_error("PREFS04", "With key=$k");
                if (isset($v['permissions']) && !is_int($v['permissions'])) $this->reply_with_error("PREFS05", "With key=$k");
            } else {
                $prefs[$k] = Array('pref_value' => $v);
            }
            if (strlen($v['pref_value'])>255) $this->reply_with_error("PREFS03", "With key=$k");

        }
        return $prefs;
    }

    function finish() {
        if ($this->player->error_status) {
            $this->reply_with_error("CORE01", $this->player->error_string);
        } else {
            $this->reply_with_success();
        }
    }

}




class WSBaseBoatsetup extends WSSetup {
    public $fullusers = null;
    
    function __construct() {
        parent::__construct();
        //auth check - FIXME en lien avec le mode player/boat
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

    function check_pilototo_list_on_success() {
        if (isset($this->request['list_on_success']) && !$this->fullusers->users->error_status) {
            if (!is_bool($this->request['list_on_success'])) {
                $this->reply_with_error('PILOTOTO05');
            }
            return $this->request['list_on_success'];
        }
        return False;
    }    

    function check_pilototo_tasktime() {
        $this->reply_with_error_if_not_exists('tasktime', 'PILOTOTO01');
        $tasktime = $this->request['tasktime'];
        if (!is_int($tasktime)) $this->reply_with_error('PILOTOTO02');
        if ($tasktime < time()) $this->reply_with_error('PILOTOTO06');
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
    
    function check_idr() {
        $this->reply_with_error_if_not_exists('idr', 'IDR01');
        $idr = $this->request['idr'];
        if (!is_int($idr) || !($idr > 0)) $this->reply_with_error('IDR02');
        return $idr;
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
        "AUTH03" => "Boat account authentification is deprecated, please use a player account.",
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
        "PILOTOTO05" => "list_on_success should be boolean",
        "PILOTOTO06" => "tasktime should be > now()",
        //wp (and also pip when pip = wp)
        "WP01" => "pip/wp is unspecified",
        "WP02" => "pip/wp should be an array",
        "WP03" => "targetlat is unspecified",
        "WP04" => "targetlong is unspecified",
        "WP05" => "wp parameters should be numerics",
        //prefs
        "PREFS01" => "prefs is unspecified",
        "PREFS02" => "key is not allowed",
        "PREFS03" => "prefs value should not excess 255 characters",
        "PREFS04" => "prefs 'pref_value' not given",
        "PREFS05" => "prefs 'permissions' should be int",
        "PREFS06" => "pref not valid",

        //player
        "PLAYER01" => 'idp (id player) is required',
        "PLAYER02" => 'idp does not exist',
        //boat/user
        "IDU01" => 'idu (iduser = idboat) is required',
        "IDU02" => 'idu should be int and > 0',
        "IDU03" => 'idu is not valid user',
        "IDU04" => 'idu is not a manageable boat for current player',
        //idr
        "IDR01"  => 'idr is required',
        "IDR02"  => 'idr should be int and > 0',
        "IDR03"  => 'idr is not valid',
        "RTFM01"  => 'RTFM : please input idr when the boat is not racing',
        "RTFM02"  => 'RTFM : starttime should be lesser than endtime',

        //ENGAGE
        "ENG01"   => 'the boat is already engaged',
        "ENG02"   => 'the race is not available for engagement',
        "ENG03"   => 'engagement error',

        //DEBUG
        "DBG01"   => 'debug is required',
        "DBG02"   => 'debug should be 0 or 1',

        //limit - mainly for ranking/results
        "LIMIT01" => "limit is required",
        "LIMIT02" => 'limit should be int and > 0 if specified',
        //wpid
        "WPID01" => "wp is required",
        "WPID02" => "wp should be int",
    );
    
    return Array("code" => $code, "msg" => $ws_error_types[$code]);
}

function get_requested_output_format($allowed_fmt = null) {
  if (is_null($allowed_fmt)) $allowed_fmt = Array('json', 'text');
  $qjson = 0;
  $sjson = false;
  $qplain = 0.1;
  $splain = false;
  
  if (isset($_REQUEST['forcefmt'])) {
      $fmt = quote_smart($_REQUEST['forcefmt']);
      if (in_array($fmt, $allowed_fmt)) {
          return $fmt;
      }
      //else, we still try to autodetect...
  }

  if (isset($_SERVER['HTTP_ACCEPT'])) { // because some clients like tcv don't send http_accept headers
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

function ask_for_auth($usage = "") {
    unset($_SERVER['PHP_AUTH_USER']);
    unset($_SERVER['PHP_AUTH_PW']);
    header('WWW-Authenticate: Basic realm="VLM Access"');
    header($_SERVER["SERVER_PROTOCOL"]." 401 Unauthorized");
    header("Content-Type: text/plain; charset=UTF-8");
    echo $usage;
}

function checkPlayerLogin($pseudo, $passwd) {
    //New player auth
    $player = new players(0, $pseudo);
    if (!$player->error_status && $player->checkPassword($passwd) ) {
        return $player;
    } else {
        return null;
    }
}

function login_if_not($usage = "No usage given") {
    
    session_start();
    // do we know the player from a previous login session?
    if (isPlayerLoggedIn() && isLoggedIn() ) {
        //OK, we are logged
        $idu = get_cgi_var('select_idu');
        if (!is_null($idu) && $idu != getLoginId() && in_array($idu, getLoggedPlayerObject()->getManageableBoatIdList())) {
            //select_idu is correct, change login
            $user = getUserObject($idu);
            login($user->idusers, $user->username); //Boat login, to change idu in session
        }
        return $_SESSION['idu'];
    } else {
        // fallback to HTTP auth
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ) {
            //not http logged
            ask_for_auth($usage);
            exit();
        } else {
            $pseudo = $_SERVER['PHP_AUTH_USER'];
            $passwd = $_SERVER['PHP_AUTH_PW'];

            
            if (!is_null($player = checkPlayerLogin($pseudo, $passwd)) ) {
                $idu = get_cgi_var('select_idu');
                if (is_null($idu) || !in_array($idu, $player->getManageableBoatIdList())) {
                     //select_idu is not correct, selecting default
                     $idu = $player->getDefaultBoat();
                }
                    
                if ($idu > 0) {
                    $user = getUserObject($idu);
                    loginPlayer($user->idusers, $user->username, $player->idplayers, $player->playername);
                } else {
                    loginPlayer(0, "noboat", $player->idplayers, $player->playername);
                }
                return $idu;
            } else {
                ask_for_auth($usage);
                exit();
            }
        }
    }
}

function logout_if_not($usage="Logout usage:\nUsername: test\nPassword: ko\nto force logout.") {
    /* This is required to bypass www-auth which does not standardise logout (the client has to "forget")
       The trick is to allow logout with non-existing credentials.
       Thus, the client software will propose these bad credentials next time, and asked for auth !
    */
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
