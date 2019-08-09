<?php

    include_once("config-mysql.php");

$datas = file_get_contents('./shyrdatas.json');
$datas = utf8_encode($datas); 
$results = json_decode($datas, True);
foreach ($results['boats'] as $k => $v) {
//	print_r($v);
$sql = sprintf("REPLACE INTO `users` SET `idusers` = %d, `boatname` = \"%s\", `username` = \"%s\";", (int)$v['idusers'], $v['boatname'], $v['username']);
print $sql."\n";
            mysql_query($sql) or die("IMPORT : Query failed : " . mysql_error()." ".$sql);
}
//print_r( array_keys($results));
?>
