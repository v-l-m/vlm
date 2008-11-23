<?
header("Content-Type: text/plain\n\n");

if ($time != "")
   echo gmdate("Y/M/D H:i:s", $time);

echo "NOW = ".time();
?>
