<?php
    header('Content-Type: application/xhtml+xml; charset=UTF-8');
    header('Cache-Control: max-age=1');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title><?php echo $PAGETITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="style/blue_1/style.css" />
    <link rel="icon" type="image/png" href="images/site/favicon.png" />
  </head>
  <body>
    <p class=".horizontalmenu">Available status : <a href="race-engine-status.php">Race engine status</a>&nbsp;<a href="windtable-status.php">Windtable status</a>&nbsp;</p>
    <h1><?php echo $PAGETITLE; ?></h1>
