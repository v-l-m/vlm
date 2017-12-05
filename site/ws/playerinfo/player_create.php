<?php
  include_once("config.php");
  include_once("wslib.php");

  $ws = new WSNewPlayer();

 
  $emailid = $ws->emailid;
  $password = $ws->password;
  $playername = $ws->pseudo;

  if (!ALLOW_ACCOUNT_CREATION) 
  {
    $ws->answer['request']=getLocalizedString("Account creation is disabled on this server, you should ask to the admins")."</h1>";         
  } 
  else
  { 
    $player = new playersPending();
    $player->email = $emailid;
    $player->playername = $playername;
    $player->password = $password;
    if (!$player->checkNonconformity($ws)) 
    {
      $player->setPassword($password);
      $player->setSeed();
      $player->insert();
      if ($player->error_status) 
      {
        //print_r($player);
        $ws->answer['request']->errorstring=$player->error_status." - ".$player->error_string;
        $ws->reply_with_error("NEWPLAYER04");
      }
      
      $player->mailValidationMessage(true);
      $ws->reply_with_success();
    } 
    else 
    {
      $ws->answer['request']->errorstring=$player->error_string;
      $ws->reply_with_error($ws->answer['request']->ErrorCode);
    }
  }
  
?>