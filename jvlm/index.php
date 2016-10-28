<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">

<html>
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>VLM 2.0 alpha</title>
      <meta http-equiv="X-UA-Compatible" content="IE=8">
      <link rel="stylesheet" type="text/css" href="jvlm.css"/>
      <link rel="stylesheet" type="text/css" media="screen" href="https://cdn.conversejs.org/css/converse.min.css">
      <link rel="stylesheet/less" type="text/css" href="jvlm.less">
      <link href="external/bootstrap-colorpicker-master/css/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css">
      <!--[if IE]>
      <script src="excanvas.js"></script><![endif]-->
      <!--<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.12.2.min.js"> </script>
      <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
      -->
      <!--<script src="http://jsconsole.com/js/remote.js?584f0017-f757-49de-88db-b87c30802ee9"></script>-->
      <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
      <script src="jquery-ui.min.js"></script>
      <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
      <!--<script src="http://maps.google.com/maps/api/js?v=3&amp;key=AIzaSyDnbDR01f8MheuxCMxth7w30A2OHtSv73U"></script>-->
      
      <script src="external/jquery.csv.js"></script>
      <!--<script src="external/bootstrap-colorpicker-master/js/bootstrap-colorpicker.min.js"></script>
      -->
      <script src="external/bootstrap-colorpicker-master/js/bootstrap-colorpicker.js"></script>
      <script src="OpenLayers/OpenLayers.debug.js"></script>
      
      <!--<script src="https://cdn.conversejs.org/dist/converse.min.js"></script>-->

      <script src="config.js"></script>
      <script src="localize.js"></script>
      <script src="GUI.js"></script>
      <script src='ControlSwitch.js' type='text/javascript'></script>
      <script src='gribmap.js' type='text/javascript'></script>
      <script src='vlmboats.js' type='text/javascript'></script>
      <script src='geomath.js' type='text/javascript'></script>
      <script src='position.js' type='text/javascript'></script>
      <script src="user.js"  type='text/javascript'></script>
      <script src='polar.js' type='text/javascript'></script>
      <script src='xmpp.js' type='text/javascript'></script>
      
      
  </head>
  <body >

    <!-- OpenLayer Map Div -->
    <div class="container-fluid">
      <div class="row main-row">
        <div id="jVlmControl" class="col-xs-12"></div>
        </div>
      <div class="row main-row">
        <div id="jVlmMap" class="col-xs-12"></div>
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
            <li  class="BoatSelector">
              <select id="BoatSelector" >
              </select> 
            </li>
            <li class="nav hidden" RacingBtn="false">
              <div class="BtnGroup" >
                <div class="BtnRaceList" >
                  <a data-toggle="collapse" data-target="#RacesListForm"><img class="TDB-Icon" src="images/races-list.png" alt=""/></a>
                </div>
              </div>
            </li>
            <li class="nav hidden" RacingBtn="true">
              <div class="BtnGroup " >
                <div class="BtnTDBPanel" >
                  <a data-toggle="collapse" data-target="#TDB-Panel"><img class="TDB-Icon" src="images/TdB-Icon-1.png" alt=""/></a>
                </div>
                <div class="BtnCtrlPanel" >
                  <a data-toggle="collapse" data-target="#Boat-Panel"><img class="TDB-Icon" src="images/TdB-Icon-2.png" alt=""/></a><span class="PilotOrdersBadge pilot btnbadge badge">...</span>
                </div>
              </div>
            </li>
            <li class="nav hidden" RacingBtn="true">
              <div class="BtnGroup1" >
                <div class="BtnRankingPanel" >
                  <a data-toggle="collapse" data-target="#Ranking-Panel"><img id="RankingButton" class="TDB-Icon" src="images/ranking.png"/></a><span id="RankingBadge" class="ranking btnbadge badge">...</span>
                </div>
              </div>
            </li>
            <li class="nav">
              <div id="BtnSetting" class="BtnGroup1 button hidden">
                <a ><img class="TDB-Icon" src=images/setting.png></img></a>
              </div>
            </li>
            <li class="nav hidden" RacingBtn="true">
              <div class="BtnGroup1" >
                <div class="BtnRaceInstruction" >
                  <a ><img id="ICSButton" class="TDB-Icon" src=images/raceinstructions.png></img></a>
                </div>
              </div>
            </li>
            <li class="nav hidden" RacingBtn="true">
                <div class="RaceInfoDiv">
                  <div class="NavRaceName">
                    <Span id="RaceName">  </Span>
                  </div>
                  <div class="NavRaceClock">
                    <Span id="RaceChrono"> </Span>
                  </div>                                    
                </div>
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
              <div class="dropdown">
                <button id="SelectionLanguageDropDown" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                <span class="caret"></span></button>
                <ul class="dropdown-menu">
                  <li><img class="LngFlag" lang="en" src="images/lng-en.png" title="English Version" alt="English Version"></li>
                  <li><img class="LngFlag" lang="fr" src="images/lng-fr.png" title="Version Française" alt="Version Française"></li>
                  <li><img class="LngFlag" lang="it" src="images/lng-it.png" title="Italian Version" alt="Italian Version"></li>
                  <li><img class="LngFlag" lang="es" src="images/lng-es.png" title="Spanish Version" alt="Spanish Version"></li>
                  <li><img class="LngFlag" lang="de" src="images/lng-de.png" title="Deutsche Fassung" alt="Deutsche Fassung"></li>
                  <li><img class="LngFlag" lang="pt" src="images/lng-pt.png" title="Portugese Version" alt="Portugese Version"></li>
                
                </ul>
              </div>
            </li>
            <li>
              <span class="glyphicon glyphicon-log-in"><button id="logindlgButton" type="button" class="btn btn-default"  I18n="login">Login</button></span> 
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right" LoggedInNav="true" style="display:none">
            <li>
              <span class="glyphicon glyphicon-log-out"><button id="logOutButton" type="button" class="btn btn-default"  I18n="logout">Logout</button></span> 
            </li>
          </ul>
        </div>
      </div>
      <!-- Collapsable Boat Controler panel -->
      <div Id="Boat-Panel" class="collapse">
        <div class="Controler-Panel Container-fluid" style="padding-left:85px">
                <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
                <li class="BCPane BearingMode"><a href="#BearingMode" data-toggle="tab" >
                  <img class="PMActiveMode ActiveMode_Heading" src="images/PMActiveMode.png"></img>
                  <span I18n="autopilotengaged">Cap</span></a>
                </li>
                <li class="BCPane AngleMode"><a href="#AngleMode" data-toggle="tab">
                  <img class="PMActiveMode ActiveMode_Angle" src="images/PMActiveMode.png"></img>
                  <span I18n="constantengaged">Angle</span></a>
                </li>
                <li class="BCPane WP_PM_Mode OrthoMode "><a href="#OrthoMode" data-toggle="tab">
                  <img class="PMActiveMode ActiveMode_Ortho" src="images/PMActiveMode.png"></img>
                  <span I18n="orthodromic">Ortho</span></a>
                </li>
                <li class="BCPane WP_PM_Mode VMGMode"><a href="#VMGMode" data-toggle="tab">
                  <img class="PMActiveMode ActiveMode_VMG" src="images/PMActiveMode.png"></img>
                  <span>VMG</span></a>
                </li>
                <li class="BCPane WP_PM_Mode VBVMGMode"><a href="#VBVMGMode" data-toggle="tab">
                  <img class="PMActiveMode ActiveMode_VBVMG" src="images/PMActiveMode.png"></img>
                  <span>VBVMG</span></a>
                </li>
                      <li class="BCPane AutoPilot"><a href="#AutoPilotTab" data-toggle="tab"><img src="images/autopilot.png" style="width:21px;"></img><span I18N="pilototoengaged">AutoPilot</span>
                        <span class="PilotOrdersBadge pilottab btnbadge badge">...</span></a></li>
            </ul>
				<!-- Fin de la barre d'onglet-->

            <div id="my-tab-content" class="tab-content">
              <div class="BCPane tab-pane" id="BearingMode">
                <div class="BoatControllerRow row">
                    <div class="col-sm-2" > <span  i18n="heading">Cap à suivre</span>
                    </div>
                    <div class="col-sm-2">
                      <input class="input Boat_SimpleInput" id="PM_Heading">
                    </div>
                    <div class="col-sm-4">Entrer le cap à suivre en °
                    </div>
                </div>
                <div class="BoatControllerRow row">
                  <div class="col-xs-12">
                    <button class="button-black" id="BtnPM_Heading">
                      <span I18n="autopilot">Do Heading</span>
                    </button>
                  </div>
                </div>
              </div>
              <!-- Fin HDG debut TWA-->
              <div class="BCPane tab-pane" id="AngleMode">
                 <div class="BoatControllerRow row">
                    <div class="col-sm-2"> <span I18n="WindAngle"> Angle du vent</span>
                    </div>
                    <div class="col-sm-2">
                      <input class="input Boat_SimpleInput" id="PM_Angle">
                      </input>
                    </div>
                    <div class="col-sm-4">Entrer l'angle +/- par rapport au vent
                    </div>                </div>
                <div class="BoatControllerRow row">
                    <div class="col-sm-2">
                    <button class="button-black" id="BtnPM_Tack" I18n="tack">Virer / Empanner</button>
                    </div>
                    <div class="col-sm-2">
                    <button class="button-black" id="BtnPM_Angle" I18n="constant">Regler l'allure</button>
                  </div>
                </div>
              </div>
              <!-- Fin TWA debut Ortho-->
              <div class="BCPane tab-pane" id="OrthoMode">
                <div id="PM_WPMode_Div">
                  <div class="BoatControllerRow row">
                      <div class="col-sm-2">
                      <span I18n="mytargetpoint"> CurDest</span>
                      </div>
                      <div class="col-sm-2">
                        <img id="SetWPOnClick" src="images/clickwp_pos.png"></img>
                      </div>
                      <div class="col-sm-8">Cliquez sur la main puis sur la map pour positionner votre WP
                      </div>
                  </div>
                  <div class="BoatControllerRow row">
                      <div class="col-sm-2"> Latitude</div>
                      <div class="col-sm-2">
                      <input class="input Boat_SimpleInput" id="PM_Lat" size="100">
                      </input>
                      </div>
                      <div class="col-sm-4"> <span class="input Boat_SimpleInput" id="PM_CurWPLat">Latitude du WP</span> 
                    </div>
                   </div>
                  <div class="BoatControllerRow row">
                      <div class="col-sm-2" > Longitude</div>
                      <div class="col-sm-2">
                        <input class="input Boat_SimpleInput" id="PM_Lon">
                        </input>
                      </div>
                      <div class="col-sm-4">
                      <span class="input Boat_SimpleInput" id="PM_CurWPLon">Longitude du WP</span> 
                      </div>
                  </div>
                  <div class="BoatControllerRow row">
                    <div class="col-sm-2">
                        <div class="checkbox "> 
                          <label>
                            <input type="checkbox" id="PM_WithWPHeading"></input>
                            @WPH
                          </label>
                        </div>
                   </div>
                   <div class="col-sm-2">
                        <input class="input Boat_SimpleInput" id="PM_WPHeading"></input>
                   </div>
                   <div class="col-sm-2">
                        <span class="input Boat_SimpleInput" id="PM_CurWPheading">@WPH</span>
                   </div>
                </div>                  
                </div>
                <div class="BoatControllerRow row">
                  <div class="col-sm-4">
                    <button class="button-black" id="BtnPM_Ortho" I18n="orthodromic">Do Angle</button>
                  </div>
                </div>
              </div>
              <!-- Fin Ortho debut VMG-->
              <div class="BCPane tab-pane" id="VMGMode">
                <div class="BoatControllerRow row">
                  <div class="col-sm-4">
                    <button class="button-black" id="BtnPM_VMG" I18n="bestvmgengaged">VMG</button>
                  </div>
                </div> 
              </div>
              <!-- Fin VMG debut VBVMG-->
              <div class="BCPane tab-pane" id="VBVMGMode">
                <div class="BoatControllerRow row">
                  <div class="col-sm-4">
                    <button class="button-black" id="BtnPM_VBVMG" I18n="vbvmgengaged">VBVMG</button>
                  </div>
                </div>    
              </div>
              <!-- Fin VBVMG debut Pilot-->
              <div class="BCPane tab-pane" id="AutoPilotTab">
                <div class="BoatControllerRow row">
                  <div class="container-fluid">
                    <div class='row'>
                      <div class='PAHeader col-xs-2'>
                        <span I18N="Human Readable date">..HD</span>
                      </div>
                      <div class='PAHeader col-xs-1'>
                        <span >PIM</span>
                      </div>
                      <div class='PAHeader col-xs-1'>
                        <span >PIP</span>
                      </div>
                      <div class='PAHeader col-xs-1'>
                        <span >Status</span>
                      </div>
                      <div class='PAHeader col-xs-4'></div>
                    </div>
                    <div id='PIL1' class='row'>
                      <div class='PAHeader col-xs-2'>
                        <img src="/externals/jscalendar/img.gif" id="trigger_jscal_1" class="calendarbutton" title="Date selector" onmouseover="this.style.background='red';" onmouseout="this.style.background=''">
                        <span id='PIL1_DATE' >10 Oct 2016 22:06</span>
                      </div>
                      <div class='PAHeader col-xs-1'>
                        <input ></input>
                      </div>
                      <div class='PAHeader col-xs-1'>
                        <span >PIP</span>
                      </div>
                      <div class='PAHeader col-xs-1'>
                        <span >Status</span>
                      </div>
                      <div class='PAHeader col-xs-4'></div>
                    </div>
                    <div id='PIL2' class='row'>
                      <div class='PAHeader col-xs-2'>
                        <img src="/externals/jscalendar/img.gif" id="trigger_jscal_1" class="calendarbutton" title="Date selector" onmouseover="this.style.background='red';" onmouseout="this.style.background=''">
                        <span id='PIL2_DATE' >12 Oct 2016 22:06</span>
                      </div>
                      <div class='PAHeader col-xs-1'>
                        <input ></input>
                      </div>
                      <div class='PAHeader col-xs-1'>
                        <span >PIP</span>
                      </div>
                      <div class='PAHeader col-xs-1'>
                        <span >Status</span>
                      </div>
                      <div class='PAHeader col-xs-4'>
                      </div>
                    </div>
                </div>   
                  </div>
                </div>    
              </div>
            </div>
          </div>
         </div>
       <!-- Collapsable Boat Dashboard (view only display) -->
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
                <div class="WindAnglePanel">
                  <img id="BearingRing" src="images/compass-small-complete.png"></img>
                </div>
                <div class="WindAnglePanel">
                  <img id="DeckImage" src="images/deck-small.png"></img>
                </div>
                <div class="WindAnglePanel">
                  <img id="ImgWindAngle"></img>
                </div>
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
      <!-- COllapsable Race ranking -->
      <div id="Ranking-Panel" class="TDB-Panel collapse">
        <div class="container">
          
        </div>
      </div>
    <!-- COllapsable Pilototo -->
    <div id="Pilot-Panel" class="TDB-Panel collapse">
      <div class="container">
        
      </div>
    </div>
      </div>
    </nav>
    <!-- Modal login form -->
    <div id="LoginForm" class="modal fade" role="dialog">
      <div class="modal-dialog">
  
        <!-- Modal content-->
        <div id="LoginPanel" class="modal-content">
               
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 I18n="Identification" class="modal-title" style="text-align:center">Identification</h4>
            <!-- Language bar -->
            <div class="container-fluid">
                  <div class="col-xs-2"><img class=" LngFlag" lang="en" src="images/lng-en.png" title="English Version" alt="English Version"></div>
                  <div class="col-xs-2"><img class="LngFlag" lang="fr" src="images/lng-fr.png" title="Version Française" alt="Version Française"></div>
                  <div class="col-xs-2"><img class="LngFlag" lang="it" src="images/lng-it.png" title="Italian Version" alt="Italian Version"></div>
                  <div class="col-xs-2"><img class="LngFlag" lang="es" src="images/lng-es.png" title="Spanish Version" alt="Spanish Version"></div>
                  <div class="col-xs-2"><img class="LngFlag" lang="de" src="images/lng-de.png" title="Deutsche Fassung" alt="Deutsche Fassung"></div>
                  <div class="col-xs-2"><img class="LngFlag" lang="pt" src="images/lng-pt.png" title="Portugese Version" alt="Portugese Version"></div>
                
            </div>
          </div>
          <div class="modal-body">
            <div class="row container-fluid">
              <div class="col-xs-12">
                <div class="row">
                  <div class="col-xs-6" align="center">
                    <span I18n="email">Adresse mail :</span>
                  </div>
                  <div class="col-xs-6">
                    <input  class="UserName " size="15" maxlength="64" name="pseudo" />
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-6" align="center">
                    <span I18n="password">Mot de passe :</span>
                  </div>
                  <div class="col-xs-6">
                    <input class="UserPassword" size="15" maxlength="15" type="password" name="password"/> 
                  </div>
                </div>
              </div>    
             </div>          
            <div class="row container-fluid" style="padding-top:20px">
              <span I18n="PleaseCreateDlg">plsc</span>
              <button id="BtnCreateAccount" class="button" I18n="CreateAcctBtn">cre</button>
            </div> </div>
          <div class="modal-footer">
            <button id="LoginButton" I18n="login" type="button" class="button" data-dismiss="modal">login</button>
          </div>
        </div>
  
      </div>
    </div>
    <!-- Modal Settings form -->
    <div id="SettingsForm" class="modal fade" role="dialog">
      <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content" id="SettingsPanel">              
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 align="center" I18n="change" class="modal-title">Préférences</h4>
            </div>
            <div class="modal-body">
              <div class="row container-fluid">
              <div class="col-xs-12">
                 <div class="row">
                <fieldset class="fieldset row-fluid">
                    <div class="col-xs-6" align="center">
                  <span I18n="boatname">boatname</span>
                  </div>
                    <div class="col-xs-6" align="center">
                  <input class="input form-control " id="pref_boatname" value="fill it here"></input>    
                  </div>
                </fieldset>
                </div>
              </div>
              </div>
              <div class="row container-fluid">
              <div class="col-xs-12">
                  <div class="row">
                    <div class="col-xs-6" align="center">
                  <span I18n="choose_your_country" >Choisir son drapeau</span>
                  </div>
                <fieldset class="fieldset row-fluid">
                    <div class="col-xs-6" align="center">
                  <select id="FlagSelector" class="select form-control"></select>  
                  </div>
                </fieldset>
                </div>
                </div>
                </div>
             <div class="row container-fluid">
              <div class="col-xs-12">
                  <div class="row">
                     <fieldset class="fieldset row-fluid">
                        <div class="col-xs-6" align="center">
                          <span I18n="color" >Couleur du bateau</span>
                        </div>
                        <div class="col-xs-6" align="center">
                          <div id="cp11" class="input-group colorpicker-component"> 
                          <input type="text" value="" class="form-control input-" />					
                          <span class="input-group-addon"><i></i></span>
                          </div> 
                        </div> 
                     </fieldset>
                  </div>
              </div>
            </div>
            <div class="modal-footer">
              <div class="row container-fluid">
              <div class="col-xs-12">
                 <div class="row">
                  <div class="col-xs-6" align="center">
              <button id="SettingCancelButton" I18n="cancel" type="button" class="button-black" data-dismiss="modal">Annuler</button>          
                  </div>
                  <div class="col-xs-6" align="center">
              <button id="SettingValidateButton"  type="button" class="button-black" data-dismiss="modal">Valider</button>
                  </div>
                 </div>
                 </div>
            </div>
          </div>
        </div>
      </div>
          </div>
      </div>
    <!-- Modal Races List -->
    <div id="RacesListForm" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div id="RacesListPanel" class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" I18n="current_races" style="text-align:center">racelist</h4>
          </div>
          <div id="RaceListBody" class="modal-body" >
            <div id="RaceListPanel" class="panel group">
            </div>            
          </div>
          <div class="modal-footer">
            <button type="button" class="button-black" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </body>
  
</html>

