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
      <script src='geomath.js' type='text/javascript'></script>
  </head>
  <body >
    
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
            <li class="nav" data-toggle="collapse" data-target="#TDB-Panel"><a ><img class="TDB-Icon" src=images/TdB-Icon-1.png></img></a>
            </li>
          </ul>
          <ul class="nav navbar-nav" >
            <li class="active">
              <div id="PbLoginProgress" class="progress" >
                <div class="progress-bar progress-bar-striped active" role="progressbar"
                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%" I18n="PbLogin">Processing Login...
                </div>
              </div>
              <div id="PbGetBoatProgress" class="progress" >
                <div class="progress-bar progress-bar-striped active" role="progressbar"
                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%" I18n="PbBoat">Loading Boat Information...
                </div>
              </div>
              <div id="PbGribLoginProgress" class="progress" >
                <div class="progress-bar progress-bar-striped active" role="progressbar"
                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%" I18n="PbGribs">loading gribs...
                </div>
              </div>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right" LoggedInNav="false" style="display:none">
            <li>
              <span class="glyphicon glyphicon-log-in"><button id="logindlgButton" type="button" class="btn btn-default"  I18n="login">Login</button></span> 
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right" LoggedInNav="true" style="display:none">
            <li>
              <span class="glyphicon glyphicon-log-out"><button id="loginOutButton" type="button" class="btn btn-default"  I18n="logout">Logout</button></span> 
            </li>
          </ul>
        </div>
      </div>
        <div id="TDB-Panel" class="TDB-Panel collapse">
          <div class="container">
            <div class="row">
              <div class="TDB-EmptyCol col-xs-3"> 
              </div>
              <div class="TDB-Panel col-xs-3">            
                <div  class="TDB-Panel" style="background-image: url('images/VLM100-Nav-Center.png');">
                  <div class="VLM100_Pos" id="BoatLon"></div>
                  <div class="VLM100_Pos" id="BoatLat"></div>
                  <div class="VLM100_PosSmall" id="StatSpeed">Speed</div>
                  <div class="VLM100_PosSmall" id="StatAvg">Avg</div>
                  <div class="VLM100_PosSmall" id="StatHeading">Heading</div>
                  <div class="VLM100_PosSmall" id="BoatSpeed"></div>
                  <div class="VLM100_PosSmall" id="BoatAvg"></div>
                  <div class="VLM100_PosSmall" id="BoatHeading"></div>
                  <div class="VLM100_PosSmall" id="StatDNM">DNM</div>
                  <div class="VLM100_PosSmall" id="StatLoch">Loch</div>
                  <div class="VLM100_PosSmall" id="StatOrtho">Ortho</div>
                  <div class="VLM100_PosSmall" id="StatLoxo">Loxo</div>
                  <div class="VLM100_PosSmall" id="StatVMG">VMG</div>
                  <div class="VLM100_PosSmall" id="BoatDNM"></div>
                  <div class="VLM100_PosSmall" id="BoatLoch"></div>
                  <div class="VLM100_PosSmall" id="BoatOrtho"></div>
                  <div class="VLM100_PosSmall" id="BoatLoxo"></div>
                  <div class="VLM100_PosSmall" id="BoatVMG"></div>
                </div>
              </div>
              <div class="TDB-Panel col-xs-3">
                <div  class="TDB-Panel" style="background-image: url('images/VLM100-Wind-Angle.png');">
                    <img id="ImgWindAngle">
                    </img>
                </div>
              </div>
              <div class="TDB-Panel col-xs-3">
                <div  class="TDB-Panel" style="background-image: url('images/VLM100-Windstation.png');">
                  <div class="VLM100_Label" id="StatWindSpeed">Wind Speed</div>
                  <div class="VLM100_Label" id="StatWindDirection">Wind Direction</div>
                  <div class="VLM100_Label" id="StatWindAngle">Wind Angle</div>
                  <div class="VLM100_Unit" id="StatSpeedUnit">kts</div>
                  <div class="VLM100_Unit" id="StatDirUnit">°</div>
                  <div class="VLM100_Unit" id="StatAngleUnit">°</div>
                  <div class="VLM100_Value" id="BoatWindSpeed" ></div>
                  <div class="VLM100_Value" id="BoatWindDirection" ></div>
                  <div class="VLM100_Value" id="BoatWindAngle"></div>
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--
          <div  id = "TDB-Panel2" class="collapse">
          <table class="table" >
            <tr>
              <td>
                <div  class="TDB-Panel2" style="background-image: url('images/TdB-Projet-1-Fond-écran-jaune.png');">
                </div>
              </td>
            </tr>
          </table>
        </div>
        -->
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
  </body>
</html>

