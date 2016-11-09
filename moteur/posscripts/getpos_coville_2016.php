	
<?php
/*
This script is an example of using curl in php to log into on one page and 
then get another page passing all cookies from the first page along with you.
If this script was a bit more advanced it might trick the server into 
thinking its netscape and even pass a fake referer, yo look like it surfed 
from a local page.
*/

$ch = curl_init();
curl_setopt($ch, CURLOPT_COOKIEJAR, "./cookieFileName");
curl_setopt($ch, CURLOPT_URL,"http://http://tour-du-monde.sodebo.com/");
//curl_setopt($ch, CURLOPT_POST, 1);
//curl_setopt($ch, CURLOPT_POSTFIELDS, "_gat=1; _ga=GA1.2.1761406744.1478444094");

//ob_start();      // prevent any output
$buf1 = curl_exec ($ch); // execute the curl command
//ob_end_clean();  // stop preventing output

curl_close ($ch);
unset($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_COOKIEFILE, "./cookieFileName");
curl_setopt($ch, CURLOPT_URL,"http://tracker-tdm-sodebo.addviso.org/tracking/4/");
curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=ph4tl9ni1hso0kqfhqgls5qc83; _ga=GA1.2.1761406744.1478444094");
curl_setopt($ch, CURLOPT_REFERER, "http://tracker-tdm-sodebo.addviso.org/fr/");
$headers = array( 
            "Host: tracker-tdm-sodebo.addviso.org",
            "Accept: application/json", 
            "Cache-Control: no-cache", 
            "Pragma: no-cache",
            "X-Requested-With: XMLHttpRequest",
            "X-Request: JSON"
             
        );




curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.59 Safari/537.36 OPR/41.0.2353.46"); 
        

$buf2 = curl_exec ($ch);

curl_close ($ch);

echo $buf2;
?> 