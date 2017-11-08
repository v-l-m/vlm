<?php
  include_once("config.php");
  include_once("wslib.php");
  
  $ws = new WSBase();

  $ws->request = $_POST;
  $ws->reply_with_error_if_not_exists('parms', 'Incorrect request');
  $params = $ws->request['parms']; 
  $request = json_decode($params);

  //only run when form is submitted
  $secretKey = RECAPTCHA_SITE_KEY;
  $response = $request->key;     
  $remoteIp = $_SERVER['REMOTE_ADDR'];
  $emailid = $request->email;

  $reCaptchaValidationUrl = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$response&remoteip=$remoteIp");
  $result = json_decode($reCaptchaValidationUrl, TRUE);

  //get response along side with all results
  //print_r($result);

  if($result['success'] == 1) 
  {
    $player = new players(0, $emailid);
    $newpass = generatePassword($emailid);
    $player->modifyPassword($newpass);
    $player->mailInformation(getLocalizedString("Here is your new password"), getLocalizedString("Your password is now")." : $newpass\n".getLocalizedString("Please change it quickly").".");
    $ws->reply_with_success();
  }
  
  // If you arrive here there was a failure somewhere
  $ws->reply_with_error("PARM03", "Recaptcha failed");
  
?>