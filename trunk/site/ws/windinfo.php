<?php
include_once("config.php");
include_once("vlmc.php");
  date_default_timezone_set('UTC');
header("content-type: text/plain; charset=UTF-8");

function get_output_format() {
  $qjson = 0;
  $sjson = false;
  $qplain = 0.1;
  $splain = false;
  
  if (isset($_REQUEST['forcefmt'])) {
      $fmt = $_REQUEST['forcefmt'];
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

function get_info_array($_lat, $_long, $_time) {

  $temp_vlmc_context = new vlmc_context();
  shm_lock_sem_construct_grib_context($temp_vlmc_context, 1);
  
  $wind_boat = new wind_info();

  VLM_get_wind_info_latlong_millideg_selective_TWSA_context(
  				     $temp_vlmc_context, $_lat, $_long,
					     $_time, $wind_boat);
  shm_unlock_sem_destroy_grib_context($temp_vlmc_context, 1);
  
  return array (
    'MLAT' => $_lat, 'MLON'=> $_long, 'TWS' => $wind_boat->speed, 'TWD' => fmod($wind_boat->angle+180., 360.), 'NOW' => $_time,
    );
}

function usage() {
  echo "usage : http://virtual-loup-de-mer.org/ws/windinfo.php\n";
  echo "l'acces utilise l'authentification HTTP";
  echo "\nlogin = votre nom d'utilisateur";
  echo "\npassword = votre mot de passe";
  echo "\n\nLe mode txt est conservé uniquement pour des raisons historiques ";
  echo "et pourra disparaitre dans les version ultérieures. Merci d'utiliser ";
  echo "le format json, via Accept: application/json ou via ?forcefmt=json";
  echo "\n\nVariables = \n
    #* TWD : Wind direction (float)
    #* TWS : Wind speed (float)
    #* NOW : Time
    ";
}

function ia_print($value, $key) {
  echo $key."=".$value."\n";
}

// now start the real work

function ask_for_auth() {
    header('WWW-Authenticate: Basic realm="VLM Access"');
    header($_SERVER["SERVER_PROTOCOL"]." 401 Unauthorized");
    header("Content-Type: text/plain; charset=UTF-8");
    usage();
}

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

$fmt = get_output_format();
$lat=htmlentities(quote_smart($_REQUEST['lat']))*1000;
$lon=htmlentities(quote_smart($_REQUEST['lon']))*1000;
$time=intval(htmlentities(quote_smart($_REQUEST['time'])));

$info_array = get_info_array($lat, $lon, $time);

switch ($fmt) {
case "json":
  header("Content-Type: text/plain; charset=UTF-8");
  echo json_encode($info_array);
  break;
case "text":
default:
  header("Content-Type: text/plain; charset=UTF-8");
  array_walk($info_array, 'ia_print');
}

?>

