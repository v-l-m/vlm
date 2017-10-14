<?
  include_once("config.php");
  include_once("wslib.php");

  // Turn off output buffering
  ini_set('output_buffering', 'off');
  // Turn off PHP output compression
  ini_set('zlib.output_compression', false);
          
  //Flush (send) the output buffer and turn off output buffering
  //ob_end_flush();
  while (@ob_end_flush());
          
  // Implicitly flush the buffer(s)
  ini_set('implicit_flush', true);
  ob_implicit_flush(true);
  
  //prevent apache from buffering it for deflate/gzip
  header("content-type: text/plain; charset=UTF-8");
  header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
  
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
      $race->UpdateRankingPage($_SERVER['DOCUMENT_ROOT']."/../../cache");
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
