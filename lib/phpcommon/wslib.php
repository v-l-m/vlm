<?php

include_once("functions.php");
include_once('players.class.php');
include_once('base.class.php');
require_once('users.class.php');

class WSBase extends baseClass
{

  public $answer = Array();
  public $maxage = WS_DEFAULT_CACHE_DURATION;
  public $now = 0;
  public $request = null;
  

  function __construct() {
      parent::__construct();
      $this->now = time();
      session_start();
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

  function usage() 
  {
    return "Usage:
    Documentation is in progress and should be available at the following url :
    https://github.com/v-l-m/vlm/wiki/WebServices";
  }

  function warning($msg) 
  {
    if (!isset($this->answer['warnings'])) $this->answer['warnings'] = Array();
    $this->answer['warnings'][] = $msg;
  }

  function reply() 
  {
    $fmt = "json";

    $this->maxage = min($this->maxage, WS_MAX_MAXAGE); //Max Lifetime is the minimum between ws setup and MAX_MAXAGE
    $this->maxage = max($this->maxage, WS_MIN_MAXAGE); //Min lifetime is the maximum between ws setup and MIN_MAXAGE

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

  function reply_with_error($code, $error_string = null) 
  {
    $this->answer['success'] = False;
    $this->answer['error'] = get_error($code);
    $this->answer['usage'] = $this->usage();
    if (!is_null($error_string)) $this->answer['error']['custom_error_string'] = $error_string;
    $this->reply();
  }
  
  function reply_with_success() 
  {
    $this->answer['success'] = True;
    $this->reply();
  }

  function finish() {
      //Must be surcharged (?) by inherited classes
      $ws->reply_with_error("CORE01");
  }

  function check_cgi($var, $err_exists, $default=null) 
  {
    $foo = get_cgi_var($var, $default);
    if (is_null($foo))
    {
      $this->reply_with_error($err_exists);
    }
    return $foo;
  }

  function check_cgi_int($var, $err_exists, $err_gt_0, $default = null) 
  {
    $foo = $this->check_cgi($var, $err_exists, $default);
    if (!is_numeric($foo))
    {
      $this->reply_with_error($err_gt_0);
    }

    $foo = intval($foo);
    if (is_int($foo) && $foo > 0) {
      return $foo;
    } 
    else 
    {
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

  function saveJson($filename, $force = 'no') {
    $path = dirname($filename);
    // Création et mise en cache
    if ( ( ! file_exists($filename) ) ||  ($force == 'yes') ) {
        if (!is_dir($path)) mkdir($path, 0777, True);
        return file_put_contents ($filename , json_encode($this->answer));
    }
    return True;
  }

  function reply_with_error_if_not_exists($key, $code, $request = null) 
  {
    if (is_null($request)) $request = $this->request;
    if (is_array($request) && !isset($request[$key]))
    { 
        $this->reply_with_error($code);
    }
    elseif (!is_array($request) && ! isset ($request->$key))
    {
        $this->reply_with_error($code);
    }
    
  }

}

////////////////////
// END WSBase class
////////////////////

/////////////////////////////////////////
//
// WS Class for new player registration
//
/////////////////////////////////////////
class WSNewPlayer extends WSBase 
{
  public $NewPlayerInfo = null;

  function __construct() 
  {
    parent::__construct();
    $this->request = $_POST;
    //$this->reply_with_error_if_not_exists('parms', 'NEWPLAYER00');
    //$params = $this->request; 
    //print_r($params);
    //$this->NewPlayerInfo = $this->request;
    //$ws->answer['DBG']=print_r($player,true);
    //print_r ($this->NewPlayerInfo);
    
    if (trim($this->request["emailid"]) === "")
    {
      $this->reply_with_error('NEWPLAYER01');
    }
    if (trim($this->request["pseudo"]) === "")
    {
      $this->reply_with_error('NEWPLAYER02');
    }
    if (trim($this->request["password"]) === "")
    {
      $this->reply_with_error('NEWPLAYER03');
    }

    $this->emailid = trim($this->request["emailid"]);
    $this->password=trim($this->request["password"]);
    $this->pseudo=trim($this->request["pseudo"]);
  } 

}

/////////////////////////////////////////
//
// WS Class for new player registration
//
/////////////////////////////////////////
class WSNewPlayerActivation extends WSBase 
{
  public $NewPlayerInfo = null;

  function __construct() 
  {
    parent::__construct();
    $this->request = $_POST;
    
    if (trim($this->request["emailid"]) === "")
    {
      $this->reply_with_error('NEWPLAYER01',var_dump($this->request));
    }
    if (trim($this->request["seed"]) === "")
    {
      $this->reply_with_error('NEWPLAYER05');
    }

    $this->emailid = trim($this->request["emailid"]);
    $this->seed=trim($this->request["seed"]);
  } 

  function ActivateAccount()
  {
    $Player = new playersPending($this->emailid, $this->seed);
    if (!$Player->validate()) 
    {
      $this->reply_with_error('NEWPLAYER07',$Player->error_string);
    }
    if (!$Player->create()) 
    {
      $this->reply_with_error('NEWPLAYER06', $player->error_string);
    }
    $this->answer['emailid']=$this->emailid;
    $log=[];
    $log["operation"]="Validate player account ".$this->emailid;
    insertAdminChangeLog($log);
    
    // This should be refactored with boat creation WS
    // Somehow
    $Player = new Players(0,$this->emailid);
    //$this->answer['PlayerDump']=var_dump($Player);
    $BoatId="P".$Player->idplayers."B__000";
    $BoatName=$Player->playername;
    $this->answer['idu'] = createBoat($BoatId, generatePassword($BoatName),"no_mail_in_users", $BoatName);
    $this->answer['BoatName'] = $BoatName;
    logPlayerEvent($Player->idplayers,$this->answer['idu'],0,'Player created new boat '.$BoatName);

    //Manual creation of users, forcing use of MASTER server
    $users = new users($this->answer['idu'], FALSE);
    $users->initFromId($this->answer['idu'], True);
    
    if (!$users->setOwnerId($Player->idplayers))
    {
        $this->reply_with_error("CREBOAT03");
    }
    $this->reply_with_success();
  }

}

/////////////////////////////////////////
//
// END WS Class for new player registration
//
/////////////////////////////////////////


class WSTracks extends WSBase {
    public $users = null;
    public $races = null;
    
    function __construct() {
        parent::__construct();

        $this->users = getUserObject($this->check_cgi_int('idu', 'IDU01', 'IDU02'));
        if (is_null($this->users)) $this->reply_with_error('IDU03');
        $idr = $this->check_cgi_int('idr', 'IDR01', 'IDR02');
        if (!raceExists($idr)) $this->reply_with_error('IDR03'); //FIXME : select on races table made two times !
        $this->races = new races($idr);

    }

    function isBo($starttime, $endtime) {
        return ($this->races->bobegin < $endtime && $this->races->boend > $starttime && $this->races->bobegin < $this->now && $this->races->boend > $this->now );
    }

    function H($lt) {
        //Heure ronde immédiatement inférieure
        $lt['0'] = $lt['0'] - 60*$lt['minutes'] - $lt['seconds'];
        $lt['minutes'] = 0;
        $lt['seconds'] = 0;
        return($lt);
    }
    
    function M($lt) {
        $hh = $lt['hours'];
        if ($hh == 0) {
            $lt = getdate($lt["0"]-24*3600);
            $hh = 24;
        }        
        return $this->delay_modulo($hh);
    }
    
    function delay_modulo($hh) {
        if ($hh == 0 or $hh == 24) {
            $l = 24*3600;
        } else if ( $hh % 8 == 0 ) {
            $l = 8*3600;
        } else if ( $hh % 4 == 0 ) {
            $l = 4*3600;
        } else if ( $hh % 2 == 0 ) {
            $l = 2*3600;
        } else {
            $l = 3600;
        }
        return($l);
    }
    
    function trackurl($lt) {
        $hh = $lt['hours'];
        if ($hh == 0) {
            $lt = getdate($lt["0"]-24*3600);
            $hh = 24;
        }
        $u2 = intval($this->users->idusers/100);
        $u1 = $this->users->idusers - 100*$u2;

        $url = sprintf("%04d%02d/%02d/%02d/%d/%02d/%d.json", $lt['year'], $lt['mon'], $lt['mday'], $hh, $this->races->idraces, $u1, $u2);
        return ($url);
    }
    
}


class WSBaseAuthent extends WSBase {
    function __construct() {
        parent::__construct();
        login_if_not($this->usage()); // WHY SHOULD WE LOGIN ?
    }
}

class WSBasePlayer extends WSBaseAuthent {
    function __construct() {
        parent::__construct();
        //FIXME : is this useless now that only players may log in ?
        if (!isPlayerLoggedIn()) $this->reply_with_error('AUTH03',var_dump($_SESSION));
    }
}

class WSBaseRace extends WSBase {
    var $idr = null;
    function __construct() {
        parent::__construct();
    }
    
    function require_idr() {
        //compat with old apis
        $idrace = get_cgi_var('idrace', null);
        $idr = $this->check_cgi_int('idr', 'IDR01', 'IDR02', $idrace);
        if (!raceExists($idr)) $this->reply_with_error('IDR03');
        $this->idr = $idr;
    }
}

class WSBaseRaceGroup extends WSBase {
    var $idg = null;
    var $rgo = null;

    function __construct() {
        parent::__construct();
    }
    
    function require_idg() {
        $idg = $this->check_cgi('idg', 'IDG01');
        $rgo = new racesgroups($idg);
        if (is_null($rgo->grouptag)) $this->reply_with_error('IDG03');
        $this->rgo = $rgo;
        $this->idg = $idg;
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

class WSRealBoat extends WSBasePlayer {
    var $idreals = null;
    var $debug = true;

    function __construct() {
        parent::__construct();
        $this->check_idreals();
    }
    
    function check_idreals() {
        $this->idreals = $this->check_cgi_int('idreals', 'REALS01', 'REALS02');
    }
    
    function check_debug() {
        $dbg = get_cgi_var('debug', 0);
        if (!is_int($dbg)) $this->reply_with_error('DBG02');
        $dbg = intval($dbg);
        $this->debug = ($dbg > 0);
    }    

}


class WSSetup extends WSBaseAuthent 
{
  //should be an extends from WSBaseBoat(?) starting from v0.15
  public $input = null;
  
  function __construct() 
  {
    parent::__construct();
    
    //surface test
    $this->input = get_cgi_var('parms', null);
    if (is_null($this->input)) $this->reply_with_error('PARM01');
    $this->request = json_decode($this->input, true);
    if (is_null($this->request) || !is_array($this->request)) $this->reply_with_error("PARM02");

    //ask for debug
    if (isset($this->request['debug']) && $this->request['debug']) 
    {
      $this->answer['request'] = $this->request;
      $this->warning("Debug mode for testing purposes only");
    }
      
    if (isset($_GET["parms"])) $this->warning("http/GET is allowed for testing purposes only");
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

    function ChangePassword($Params)
    {
        //print_r($Params);
        $this->request = json_decode( $Params['parms']);
        
        //print_r($this->request);
        $OldPassword = $this->request->OldPwd;
        $NewPassword = $this->request->NewPwd;
        
        if (! $OldPassword || ! $NewPassword)
        {
            if (! $OldPassword)
            {
                $this->reply_with_error_if_not_exists('OldPwd', 'PWDCHANGE01');
            }
            else
            {
                $this->reply_with_error_if_not_exists('NewPwd', 'PWDCHANGE01');
            }
        }
        if ($OldPassword === $NewPassword)
        {
            $this->reply_with_error("PWDCHANGE02","No action required");
        }
        
        if (!$this->player->checkPassword($OldPassword)) 
        {
            $this->reply_with_error("PWDCHANGE03","InvalidOldPassword");
        }

        if ($this->player->modifyPassword($NewPassword))
        {
            $this->reply_with_success();
        }
        else
        {
            $this->reply_with_error("PWDCHANGE04","UpdateFailed");
        }
    }

}




class WSBaseBoatsetup extends WSSetup {
    public $fullusers = null;
    
    function __construct() {
        parent::__construct();
        //auth check - FIXME en lien avec le mode player/boat
        $this->reply_with_error_if_not_exists('idu', "AUTH01");
        

        if ($_SESSION['idu'] != $this->request['idu'])
        {
            $this->answer["extended"]='Got '.$this->request['idu']. " expected ".$_SESSION['idu'];
            $this->reply_with_error("AUTH02");            
        }
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
        // it's  a numeric value, we now bring it in [0, 360[
        $pip = fmod($pip, 360);
        if ($pip < 0) $pip += 360;
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

class WSBaseBoatCreate extends WSSetup
{
    function __construct() {
        parent::__construct();
        if (! isset($_POST['parms']))
        {
            $this->reply_with_error('PARM01');
            return;
        }

        $this->request = json_decode( $_POST['parms']);           
        $this->reply_with_error_if_not_exists('idp', "AUTH04");
        if ($_SESSION['idp'] != $this->request->idp) $this->reply_with_error("AUTH02");

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

    function CheckParams() {

        $this->reply_with_error_if_not_exists('BoatName', 'CREBOAT01');
        
        $BoatName = $this->request->BoatName;
        // Fix Me : Need Test
        if (getLoggedPlayerObject()->hasMaxBoats() ) 
        {
            $this->reply_with_error("CREBOAT02");
            return;
        }

        $boat_id_base = "P".$this->request->idp."B_";
        $BoatIndex = 0;
        $query = "Select username from users where username like '$boat_id_base%' order by 1 desc limit 1";
        $res = $this->queryRead($query);
        if ($res && mysqli_num_rows($res) !== 0) 
        {
            $row = mysqli_fetch_assoc($res);
            $BoatIndex = (int)(substr($row['username'],strlen($row['username'])-3,3))+1;
        }
        
        
        $BoatId=sprintf("%s_%03d", $boat_id_base, $BoatIndex);
        
        $this->answer['idu'] = createBoat($BoatId, generatePassword($BoatName),"no_mail_in_users", $BoatName);
        $this->answer['BoatName'] = $BoatName;
        logPlayerEvent($this->request->idp,$this->answer['idu'],0,'Player created new boat '.$BoatName);

        //Manual creation of users, forcing use of MASTER server
        $users = new users($this->answer['idu'], FALSE);
        $users->initFromId($this->answer['idu'], True);
        
        if (!$users->setOwnerId($this->request->idp))
        {
            $this->reply_with_error("CREBOAT03");
        }
        return;
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
        "AUTH04" => "idp is mandatory for safety reasons and should match your login",
        "AUTH05" => "Your request does not match the idp you are login in",
        
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
        "PLAYERLIST01" => 'q is required',
        "PLAYERLIST02" => 'q should be defined and have at least 2 chars',
        //boat/user
        "IDU01" => 'idu (iduser = idboat) is required',
        "IDU02" => 'idu should be int and > 0',
        "IDU03" => 'idu is not valid user',
        "IDU04" => 'idu is not a manageable boat for current player',
        //boat/reals
        "REALS01" => 'idreals is required',
        "REALS02" => 'idreals should be int and > 0',
        "REALS03" => 'idreals is not valid user',

        //idr
        "IDR01"  => 'idr is required',
        "IDR02"  => 'idr should be int and > 0',
        "IDR03"  => 'idr is not valid',

        //idg
        "IDG01"  => 'idg is required',
        "IDG02"  => 'idg : report this error ?',
        "IDG03"  => 'idg is not valid',
        
        //tracks
        "TRK01"  =>  'hh should be >0 and <= 24',
        
        //gribs
        "GRB01" => "step should be 5 or 15",
        "GRB02" => "north should be greater than south",
        "GRB03" => "requested grib is not available",
        
        //RTFM
        "RTFM01"  => 'RTFM : please input idr when the boat is not racing',
        "RTFM02"  => 'RTFM : starttime should be lesser than endtime and both < $now',
        "RTFM03"  => 'RTFM : BO covering the time period',

        //ENGAGE
        "ENG01"   => 'the boat is already engaged',
        "ENG02"   => 'the race is not available for engagement',
        "ENG03"   => 'engagement error',
        "ENG04"   => 'the boat is not currently engaged in a race',
        "ENG05"   => 'Trying to abandon incorrect race',
        "ENG06"   => 'Error abondonning race',

        //DEBUG
        "DBG01"   => 'debug is required',
        "DBG02"   => 'debug should be 0 or 1',

        //limit - mainly for ranking/results
        "LIMIT01" => "limit is required",
        "LIMIT02" => 'limit should be int and > 0 if specified',

        //wpid
        "WPID01" => "wp is required",
        "WPID02" => "wp should be int",

        // New player registration
        "NEWPLAYER00" => "Data must be posted in JSON parms struct",
        "NEWPLAYER01" => "emailid is required",
        "NEWPLAYER02" => "pseudo is required",
        "NEWPLAYER03" => "password is required",
        "NEWPLAYER04" => "failed to insert in DB",
        "NEWPLAYER05" => "seed is required",
        "NEWPLAYER06" => "Player account creation error",
        "NEWPLAYER07" => "Account validation error. Email or Seed are wrong.",
        

        // Password reset
        "PWDRESET01" => "invalid parameter set (email)",
        "PWDRESET02" => "invalid parameter set (seed)",
        "PWDRESET03" => "invalid parameter set (key)",
        
        // Player password change
        "PWDCHANGE01" => "invalid parameter set (OldPwd, NewPwd required)",
        "PWDCHANGE02" => "NewPwdRequired",
        "PWDCHANGE03" => "OldPasswordInvalid",

        // Boat creation
        "CREBOAT01" => "Unspecified Boat Name",
        "CREBOAT02" => "Max fleet size reached",
        "CREBOAT03" => "Failed to attach new boat to boat fleet",

        // Dummy end to allow , at the end of each line
        "__END__DO_NOT_ADD_BELOW__" => ""
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
    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
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
    } /*else {
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
    }*/
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
