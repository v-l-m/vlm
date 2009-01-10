<?php
$latitude=htmlentities($_GET['latitude']);
$longitude=htmlentities($_GET['longitude']);
$zoom=htmlentities($_GET['zoom']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
      <title>Google Maps JavaScript API Example</title>
        <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA24tKuhiIyoy5pk443yc_iRRUzbAYu-PZ6L1rCACM_1fwQ4pjzxRCD5_LjUdYQjZsKKjXPDp63ltq5g"
        type="text/javascript"></script>
          <script type="text/javascript">

      //<![CDATA[


    function load() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map"));
        GEvent.addListener(map, "moveend", function() {
            var center = map.getCenter();
            document.title = center.toString();
        });


        var point = new GLatLng(<?php printf("%f,%f", $latitude , $longitude); ?>);

        map.addControl(new GMapTypeControl(G_SATELLITE_MAP)); 
        /*map.setMapType(G_NORMAL_MAP); */
        map.addControl(new GSmallMapControl());
              map.setCenter(point, <?php echo  $zoom ?>);

        /* Poly lines ==> dessiner la trajectoire des bateaux */
        /*
        var polyline = new GPolyline(
                 [
                new GLatLng(37.4419, -122.1419),
          new GLatLng(37.4519, -122.1519),
          new GLatLng( 37.4619, -122.1819)
           ], "#FF0000", 10);
        map.addOverlay(polyline);
        */
             }

     }


   //]]>
      </script>
    </head>
     <body onload="load()" onunload="GUnload()">
         <div id="map" style="width: 800px; height: 600px"></div>
   </body>
 </html>
