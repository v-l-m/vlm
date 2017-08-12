<?
  include_once("config.php");
  include_once("wslib.php");

  header("content-type: text/plain; charset=UTF-8");

  //FIXME : types are badly checked
  $ws = new WSBaseRace();
  $now = time();
  
  $ws->require_idr();
  $limit = intval($ws->check_cgi('limit', "LIMIT01", 99999));
  $races = new races($ws->idr);
  
  $res = $races->UpdateRaceRankings();

  $ws->answer['ranking']=$res;

  $ws->reply_with_success();

?>
