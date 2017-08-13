<?
  include_once("config.php");
  include_once("wslib.php");

  header("content-type: text/plain; charset=UTF-8");

  //FIXME : types are badly checked
  $ws = new WSBasePlayer();

  $player = getLoggedPlayerObject();
  $now = time();
  
  $query = "select distinct idraces from races";

  $res = $ws->queryRead($query);
  $ws->answer["RnkGenStatus"]=[];
  while ($row = mysql_fetch_assoc($res)) 
  {
    if ($row['idraces']!=='0')
    {
      $race = new fullRaces($row['idraces']);
      $line = "Gen rankings for race".$race->races->idraces;
      array_push($ws->answer["RnkGenStatus"],$line);
      $race->UpdateRankingPage($race->races->idraces,$_SERVER['DOCUMENT_ROOT']."/../../cache");
    }
  }

  $ws->reply_with_success();

?>
