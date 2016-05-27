<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">

<html>
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>VLM 2.0 alpha</title>
      <meta http-equiv="X-UA-Compatible" content="IE=8">
      <link rel="stylesheet" type="text/css" href="jvlm.css"/>
      <!--[if IE]>
      <script src="excanvas.js"></script><![endif]-->
      <!--<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.12.2.min.js"> </script>
      <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
      -->
      <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
      <script src="jquery-ui.min.js"></script>
      <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
      <script src="http://maps.google.com/maps/api/js?v=3&amp;key=AIzaSyDnbDR01f8MheuxCMxth7w30A2OHtSv73U"></script>
      <!--<script src="/externals/OpenLayers/OpenLayers.js"></script>
      -->
      <script src="OpenLayers/OpenLayers.js"></script>
      <script src="config.js"></script>
      <script src="localize.js"></script>
      <script src="user.js"></script>
      <script src="GUI.js"></script>
      <script src='ControlSwitch.js' type='text/javascript'></script>
      <script src='gribmap.js' type='text/javascript'></script>
      <script src='vlmboats.js' type='text/javascript'></script>
      <script>
      
          function init() {

             //Pour tenter le rechargement des tiles quand le temps de calcul est > au timeout
              OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;

              var default_latitude = 45.5;
              var default_longitude = -30.0;
              var default_zoom = 4;

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
                      MapOptions);

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
              map.addLayers([ VLMBoatsLayer,vlm, wms, bingroad, bingaerial, binghybrid, gphy, ghyb, gsat, grib]);
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
                  lonlat.transform(MapOptions.displayProjection, MapOptions.projection);
                  map.setCenter(lonlat, default_zoom);
              }
          }
      </script>
  </head>
  <body onload="init();">
    
    <div class="container-fluid">
      <div class="row main-row">
        <div id="jVlmControl" class="col-sm-12"></div>
        <div id="jVlmMap" class="col-sm-12"></div>
      </div>
    </div>
    
    <nav class="navbar navbar-inverse">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span> 
            <span class="icon-bar"></span>             
          </button>
          <a class="navbar-brand" href="#"><img src="/images/logos/logovlmnew.png"/></a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
          <ul  class="nav navbar-nav"  LoggedInNav="true" style="display:none">
            <li  class="active" ><a id="PlayerId">Not Logged in </a></li>
            <li  >
              <select id="BoatSelector" >
              </select> 
            </li>
          </ul>
          <ul class="navbar-nav" >
            <li class="active">
              <div id="PbLoginProgress" class="progress" height="0.2em" position="absolute" style="display: block; margin-bottom: 0px;">
                <div class="progress-bar progress-bar-striped active" role="progressbar"
                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%" I18n="PbLogin">Processing Login...
                </div>
              </div>
              <div id="PbGetBoatProgress" class="progress" height="0.2em" position="absolute" style="display: block; margin-bottom: 0px;">
                <div class="progress-bar progress-bar-striped active" role="progressbar"
                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%" I18n="PbBoat">Loading Boat Information...
                </div>
              </div>
              <div id="PbGribLoginProgress" class="progress" height="0.2em" position="absolute" style="display: block; margin-bottom: 0px;">
                <div class="progress-bar progress-bar-striped active" role="progressbar"
                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%" I18n="PbGribs">loading gribs...
                </div>
              </div>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li>
              <span class="glyphicon glyphicon-log-in"></span> 
              <button id="logindlgButton" type="button" class="btn btn-default" I18n="login">Login</button>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    

    <!-- Modal -->
    <div id="LoginForm" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div id="LoginPanel" class="modal-content">
               
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 I18n="Identification" class="modal-title">Identification</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <!-- Language bar -->
              <div style="float:right; width:64 px">
                <ul id="langbox" class="nav navbar-nav" >
                  <li><img class="LngFlag" lang="en" src="images/lng-en.png" title="English Version" alt="English Version"></li>
                  <li><img class="LngFlag" lang="fr" src="images/lng-fr.png" title="Version Française" alt="Version Française"></li>
                  <li><img class="LngFlag" lang="it" src="images/lng-it.png" title="Italian Version" alt="Italian Version"></li>
                  <li><img class="LngFlag" lang="es" src="images/lng-es.png" title="Spanish Version" alt="Spanish Version"></li>
                  <li><img class="LngFlag" lang="de" src="images/lng-de.png" title="Deutsche Fassung" alt="Deutsche Fassung"></li>
                  <li><img class="LngFlag" lang="pt" src="images/lng-pt.png" title="Portugese Version" alt="Portugese Version"></li>
                </ul>
              </div>
              <div >
                <table>
                  <tr>
                    <td width="50%" I18n="email">Adresse de courriel : 
                    </td>
                    <td><input  class="UserName" size="15" maxlength="64" name="pseudo" />
                    </td>
                  </tr>
                  <tr>
                    <td I18n="password">Mot de passe : 
                    </td>
                    <td>
                      <input class="UserPassword" size="15" maxlength="15" type="password" name="password"/> 
                    </td>
                  </tr>            
                </table>
              </div>
              
             </div>
          </div>
          <div class="modal-footer">
            <button id="LoginButton" I18n="login" type="button" class="btn " data-dismiss="modal">login</button>
          </div>
        </div>

      </div>
    </div>
   
    <!--
    <div  visibility="hidden" >
      <h1  align="center"></h1>
      
        
      
    </div>
    <div class="UserMenu"  >
      <div class="PlayerName">
        
        <div style="float:left">
          
        </div>
        <div style="float:left" id="DropLogoutMenu">v
        </div>
        </div>
        <ul id="Menu">
          <li I18n="logout">Logout</li>
        </ul>
      
    </div>
    

      <div id="logovlm">
        <img src="/images/logos/logovlmnew.png"/>
      </div>
    </div>-->
  </body>
</html>

