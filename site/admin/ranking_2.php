<?
  include_once("config.php");
  include_once("wslib.php");

  header("content-type: text/plain; charset=UTF-8");

  //FIXME : types are badly checked
  $ws = new WSBasePlayer();

  $player = getLoggedPlayerObject();
  $now = time();
  $skip = get_cgi_var('skip', 0);
  
  $query = "select distinct idraces from races order by idraces";

  $res = $ws->queryRead($query);
  $ws->answer["RnkGenStatus"]=[];
  $skipped = 0;
  $count = $skip + 1;
  while ($row = mysql_fetch_assoc($res)) 
  {
    if ($row['idraces']!=='0' && $skipped >= $skip)
    {
      $race = new fullRaces($row['idraces']);
      $line = "Gen rankings for race".$race->races->idraces;
      array_push($ws->answer["RnkGenStatus"],$line);
      $race->UpdateRankingPage($race->races->idraces,$_SERVER['DOCUMENT_ROOT']."/../../cache");
      echo $count.' processed race '.$row['idraces']."\n";
      $count++;
    }
    else
    {
      $skipped ++;
    }
  }

  $ws->reply_with_success();

?>
