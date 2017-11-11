<?php
  include_once("config.php");
  include_once("wslib.php");
  
  $ws = new WSBasePlayersetup();
  $ws->ChangePassword($_POST);
?>