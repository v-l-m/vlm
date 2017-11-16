<?php
  include_once("config.php");
  include_once("wslib.php");
  
  $ws = new WSBase();

  if ($_SERVER['REQUEST_METHOD'] === 'POST') 
  {
    // …
    $ws->request = $_POST;
    $ws->reply_with_error_if_not_exists('parms', 'Incorrect request');
    $params = $ws->request['parms']; 
    $request = json_decode($params);

    $emailid = $request->email;
    $response = $request->key;     
  
    $Validating=false;
  }
  else
  {
    $ws->request = $_GET;
    $ws->reply_with_error_if_not_exists('email', 'PWDRESET01');
    $ws->reply_with_error_if_not_exists('seed', 'PWDRESET02');
    $ws->reply_with_error_if_not_exists('key', 'PWDRESET03');
    $Seed = get_cgi_var('seed');
    $emailid = get_cgi_var('email');
    $response = get_cgi_var('key'); 
    $Validating = true;
  }

  //only run when form is submitted
  $secretKey = RECAPTCHA_SITE_KEY;
  $remoteIp = $_SERVER['REMOTE_ADDR'];
  
  $reCaptchaValidationUrl = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$response&remoteip=$remoteIp");
  $result = json_decode($reCaptchaValidationUrl, TRUE);

  //get response along side with all results
  //print_r($result);

  if($Validating && $result['success'] == 1) 
  {
    $player = new players(0, $emailid);
    if ($player->password !== $Seed)
    {
      $ws->reply_with_error('PWDRESET02','invalid seed**' .$player->password.'**'.$Seed);
    }
    $newpass = generatePassword($emailid);
    $player->modifyPassword($newpass);
    $player->mailInformation(getLocalizedString("Here is your new password"), getLocalizedString("Your password is now")." : $newpass\n".getLocalizedString("Please change it quickly").".");
    $ws->answer['msg'] ="An email has been sent to ".$emailid.". Click on the link inside";
    $ws->reply_with_success();
  }
  else if ($result['success'] == 1)
  {
    $player = new players(0, $emailid);
    $player->requestPasswordReset(true);
    $ws->answer['msg'] ="An email has been sent to ".$emailid.". Click on the link inside";
    $ws->reply_with_success();
    
  }
  
  // If you arrive here there was a failure somewhere
  $ws->reply_with_error("PARM03", "Recaptcha failed");
  
?>