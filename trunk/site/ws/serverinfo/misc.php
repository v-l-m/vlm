<?php
    include_once("config.php");
    include_once("wslib.php");


    function get_info_array() {
      $info = array();
      $info['lastupdate'] = lastUpdateTime();
      $info['time'] = time();
      $info['servername'] = SERVER_NAME;
      $info['warning'] = "Do not rely on this ws without asking to the dev mailing list";
      
      $versiontxt = "";
      $fd = fopen("../../version.txt", 'r', True);
      while ($line = fgetss($fd)) {
        if (strlen(trim($line)) > 0) $versiontxt .= $line;
      }
      $versionarray = split("\n", $versiontxt);
      $info['version'] = $versionarray[0];
      $info['branch'] = $versionarray[1];

      return $info;
    }

    $info_array = get_info_array();
    
    header('Content-type: application/json; charset=UTF-8');
    echo json_encode($info_array);
?>
