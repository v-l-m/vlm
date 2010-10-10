<?php
    include_once("config.php");
    include_once("wslib.php");


    function get_info_array() {
      $info = array();
      $info['time'] = time();
      return $info;
    }

    $info_array = get_info_array();
    
    header('Content-type: application/json; charset=UTF-8');
    echo json_encode($info_array);
?>
