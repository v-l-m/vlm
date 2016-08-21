<?php
  include_once("config.php");
  include_once("wslib.php");


  function get_info_array() 
  {
    $info = array();
    
    $flagres = getFlagsListCursor();
    while ($row = mysql_fetch_array($flagres, MYSQL_ASSOC) ) 
    {
      $info['flagsurl'][$row['idflags']]='/flagimg.php?idflags='.$row['idflags'];
      $info['flags'][$row['idflags']]=$row['idflags'];
      //$select_list = $select_list . "<option value=\"". $row['idflags'] . "\"";
      //if ( $fullUsersObj->users->country == $row['idflags'] ) $select_list = $select_list . " selected=\"selected\" ";
      //FIXME: il serait plus exact d'utiliser l'attribut label de la balise opton pour fixer l'affichage... mais les vieux navigateurs n'aiment pas...
      //$select_list = $select_list . ">". $row['idflags'] ."</option>\n";
    }
    mysql_free_result($flagres);

    $info['success']=true;
    return $info;
  }

    $info_array = get_info_array();
    
    header('Content-type: application/json; charset=UTF-8');
    header("Cache-Control: max-age=0, must-revalidate");

    echo json_encode($info_array);
?>
