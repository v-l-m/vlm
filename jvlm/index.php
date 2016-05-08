<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<?php

    include_once("includes/header.inc");

?>
<html>
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <title>jVLM alpha (grib viewer only)</title>
      <meta http-equiv="X-UA-Compatible" content="IE=8">
      <link rel="stylesheet" type="text/css" href="jvlm.css"/>
      <!--[if IE]>
      <script src="excanvas.js"></script><![endif]-->
      <script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.12.2.min.js"> </script>
      <script src="http://maps.google.com/maps/api/js?v=3&amp;key=AIzaSyDnbDR01f8MheuxCMxth7w30A2OHtSv73U"></script>
      <script src="/externals/OpenLayers/OpenLayers.js"></script>
      <script src="config.js"></script>
      <script src="localize.js"></script>
      <script src="user.js"></script>
      <script src='ControlSwitch.js' type='text/javascript'></script>
      <script src='gribmap.js' type='text/javascript'></script>
      <script>
      
          function init() {

             //Pour tenter le rechargement des tiles quand le temps de calcul est > au timeout
              OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;

              var default_latitude = 45.5;
              var default_longitude = -30.0;
              var default_zoom = 4;

              var options = {
                  //Projection mercator sphérique (type google map ou osm)
                  projection: new OpenLayers.Projection("EPSG:900913"),
                  //projection pour l'affichage des coordonnées
                  displayProjection: new OpenLayers.Projection("EPSG:4326"),
                  //unité : le m
                  units: "m",
                  maxResolution: 156543.0339,
                  maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34,
                          20037508.34, 20037508.34),
                  restrictedExtent: new OpenLayers.Bounds(-40037508.34, -20037508.34,
                          40037508.34, 20037508.34)
              };

              var layeroption = {
                  //sphérique
                  sphericalMercator: true,
                  //FIXME: voir s'il y a des effets spécifiques à certains layers
                  transitionEffect: "resize",
                  //pour passer l'ante-meridien sans souci
                  wrapDateLine: true
              };

              //MAP

              map = new OpenLayers.Map(
                      "jVlmMap", //identifiant du div contenant la carte openlayer
                      options);

              //NB: see config.js file. Le layer VLM peut utiliser plusieurs sous-domaine pour paralélliser les téléchargements des tiles.
              var urlArray = tilesUrlArray;

              var vlm = new OpenLayers.Layer.XYZ(
                      "VLM Layer",
                      urlArray,
                      layeroption
              );

              //Les layers Bing
              //FIXME : roads... what for ;) ?
              var bingroad = new OpenLayers.Layer.Bing({
                  key: bingApiKey,
                  type: "Road",
                  sphericalMercator: true,
                  //FIXME: voir s'il y a des effets spécifiques au layer ?
                  transitionEffect: "resize",
                  //pour passer l'ante-meridien sans souci
                  wrapDateLine: true
              });
              var bingaerial = new OpenLayers.Layer.Bing({
                  key: bingApiKey,
                  type: "Aerial",
                  sphericalMercator: true,
                  //FIXME: voir s'il y a des effets spécifiques au layer ?
                  transitionEffect: "resize",
                  //pour passer l'ante-meridien sans souci
                  wrapDateLine: true

              });
              var binghybrid = new OpenLayers.Layer.Bing({
                  key: bingApiKey,
                  type: "AerialWithLabels",
                  name: "Bing Aerial With Labels",
                  sphericalMercator: true,
                  //FIXME: voir s'il y a des effets spécifiques au layer ?
                  transitionEffect: "resize",
                  //pour passer l'ante-meridien sans souci
                  wrapDateLine: true
              });

              //Layer Multimap, désactivé car fonctionnement erratique
              //var mm = new OpenLayers.Layer.MultiMap( "MultiMap", layeroption);

              //Le layer openlayer classique
              //FIXME: voir les types de layers
              var wms = new OpenLayers.Layer.WMS("OpenLayers WMS",
                      "http://vmap0.tiles.osgeo.org/wms/vmap0",
                      {layers: 'basic', sphericalMercator: true}
              );

              //Le calque de vent made in Vlm
              var grib = new Gribmap.Layer("Gribmap", layeroption);
              //grib.setOpacity(0.9); //FIXME: faut il garder une transparence du vent ?

              //Layer Google Physical
              var gphy = new OpenLayers.Layer.Google(
                      "Google Physical",
                      {
                          type: google.maps.MapTypeId.TERRAIN,
                          sphericalMercator: true,
                          transitionEffect: "resize",
                          wrapDateLine: true
                      }
              );

              //Layer Google Hybrid
              //FIXME: faut t il vraiment le conserver ?
              var ghyb = new OpenLayers.Layer.Google(
                      "Google Hybrid",
                      {
                          type: google.maps.MapTypeId.HYBRID,
                          numZoomLevels: 20,
                          sphericalMercator: true,
                          transitionEffect: "resize",
                          wrapDateLine: true
                      }
              );

              //Layer Google Satelit
              var gsat = new OpenLayers.Layer.Google(
                      "Google Satellite",
                      {
                          type: google.maps.MapTypeId.SATELLITE,
                          numZoomLevels: 22,
                          sphericalMercator: true,
                          transitionEffect: "resize",
                          wrapDateLine: true
                      }
              );

              //La minimap utilise le layer VLM
              var vlmoverview = vlm.clone();

              //Et on ajoute tous les layers à la map.
              map.addLayers([vlm, wms, bingroad, bingaerial, binghybrid, gphy, ghyb, gsat, grib]);
              //map.addLayers([vlm, grib]); //FOR DEBUG

              //Controle l'affichage des layers
              map.addControl(new OpenLayers.Control.LayerSwitcher());

              //Controle l'affichage de la position ET DU VENT de la souris
              map.addControl(new Gribmap.MousePosition({gribmap: grib}));

              //Affichage de l'échelle
              map.addControl(new OpenLayers.Control.ScaleLine());

              //Le Permalink
              //FIXME: éviter que le permalink soit masqué par la minimap ?
              map.addControl(new OpenLayers.Control.Permalink('permalink'));

              //FIXME: Pourquoi le graticule est il un control ?
              map.addControl(new OpenLayers.Control.Graticule());

              //Navigation clavier
              map.addControl(new OpenLayers.Control.KeyboardDefaults());

              //Le panel de vent
              map.addControl(new Gribmap.ControlWind());

              //Evite que le zoom molette surcharge le js du navigateur
              var nav = map.getControlsByClass("OpenLayers.Control.Navigation")[0];
              nav.handlers.wheel.cumulative = false;
              nav.handlers.wheel.interval = 100;

              //Minimap
              var ovmapOptions = {
                  maximized: true,
                  layers: [vlmoverview]
              }
              map.addControl(new OpenLayers.Control.OverviewMap(ovmapOptions));

              //Pour centrer quand on a pas de permalink dans l'url
              if (!map.getCenter()) {
                  // Don't do this if argparser already did something...
                  var lonlat = new OpenLayers.LonLat(default_longitude, default_latitude);
                  lonlat.transform(options.displayProjection, options.projection);
                  map.setCenter(lonlat, default_zoom);
              }
          }
      </script>
  </head>
  <body onload="init();">
    <div class="LoginPanel" visibility="hidden" >
      <h1 I18n="email">Identification</h1>
          <table>
            <tr>
              <td>Adresse de courriel : 
              </td>
              <td><input  class="UserName" size="15" maxlength="64" name="pseudo" />
              </td>
            </tr>
            <tr>
              <td>Mot de passe : 
              </td>
              <td>
                <input class="UserPassword" size="15" maxlength="15" type="password" name="password"> 
              </td>
            </tr>            
            <tr>
              <td/>
              <td>
                  <div class="LoginButton">
                  <p class="LoginButton">Valider</p>
                  </div>
              </td>
            </tr>
          </table>
          <BR><BR>
          <div id="langbox" >
            <a href="/index.php?&amp;lang=en"><img src="/images/site/en.png" title="English Version" alt="English Version"></a>
            <a href="/index.php?&amp;lang=fr"><img src="/images/site/fr.png" title="Version Française" alt="Version Française"></a>
            <a href="/index.php?&amp;lang=it"><img src="/images/site/it.png" title="Italian Version" alt="Italian Version"></a>
            <a href="/index.php?&amp;lang=es"><img src="/images/site/es.png" title="Spanish Version" alt="Spanish Version"></a>
            <a href="/index.php?&amp;lang=de"><img src="/images/site/de.png" title="Deutsche Fassung" alt="Deutsche Fassung"></a>
            <a href="/index.php?&amp;lang=pt"><img src="/images/site/pt.png" title="Portugese Version" alt="Portugese Version"></a>
        </div>
      
    </div>
    <div class="UserMenu" visibility="hidden" >
    <div class="PlayerName">
      <table>
        <tr>
          <td id="PlayerId">
          </td>
          <td>v
          </td>
        </tr>
      </table>
    </div>
    </div>
    <div id="jVlmControl"></div>
    <div id="jVlmMap">
      <div id="logovlm">
        <img src="/images/logos/logovlmnew.png"/>
      </div>
    </div>
  </body>
</html>

