<?php
include_once("config.php");
include_once("wslib.php");

$trigram_mapping = Array(
/*
    FIXME : Not sure if we should map all the field name to trigram ???
    "idraces" => "RAC",
    "boattype" => "POL",
    "racename" => "RAN",
*/  
    );
   

function get_output_format() {
  return get_requested_output_format();
  }

function get_info_array($idraces = 0) {
    $info = Array();
    $query = "SELECT idusers, username, boatname, country";

    if ( isAdminLogged() ) {
        $query .= ", ipaddr, email, class";
    }

    $query .= " FROM users";
    $query .= " WHERE engaged = ". $idraces.";";
    
    $result = wrapper_mysql_db_query_reader($query);

    while(  $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {    
        $info[$row['idusers']] = $row;
        }

    return $info;
}

function ia_print($value, $key) {
    echo implode(',', $value);
    echo "\n";
}

function usage() {
    $usage = "usage : http://virtual-loup-de-mer.org/ws/raceuserlist.php?idrace=X\n";
    $usage .= "\nX = numero de la course";
    return $usage;
}

// now start the real work

$idrace=htmlentities(quote_smart($_REQUEST['idrace']));
if (intval($idrace) == 0) {
    header("Content-type: text/plain; charset=UTF-8");
    echo usage();
    exit();
}

login_if_not(usage());

$fmt = get_output_format();
$info_array = get_info_array($idrace);
switch ($fmt) {
    case "json":    
        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($info_array);
        break;
    case "text":
    default:
        header("Content-Type: text/plain; charset=UTF-8");
        array_walk($info_array, 'ia_print');
}

?>
