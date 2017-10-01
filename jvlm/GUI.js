
//
//
// Some consts 
var RACE_TYPE_CLASSIC = 0;
var RACE_TYPE_RECORD = 1;
var RACE_TYPE_OMORMB = 2;

var FIELD_MAPPING_TEXT = 0;
var FIELD_MAPPING_VALUE = 1;
var FIELD_MAPPING_CHECK = 2;
var FIELD_MAPPING_IMG = 3;
var FIELD_MAPPING_CALLBACK = 4;

var MAX_PILOT_ORDERS = 5;

var BoatRacingStatus = ["RAC","CST","LOC","DNS"];
var BoatArrivedStatus = ["ARR"];
var BoatNotRacingStatus = ["DNF","HC","HTP"];

// Global (beurk) holding last position return by OL mousemove.
var GM_Pos=null;
var GribWindController = null;
var PilototoFt = null;
var RankingFt = null;

// On ready get started with vlm management
$(document).ready(
  function(){

    ///////////////////////////////////////////////////
    //
    //Debug only this should not stay when releasing
    //
    //$("#TestGrib").click(HandleGribTestClick)
    //$("#StartEstimator").click(HandleEstimatorStart)
    
    //
    // End Debug only
    //
    ///////////////////////////////////////////////////

    // Setup global ajax error handling
    //setup ajax error handling
    $.ajaxSetup({
        error: function (x, status, error) {
            if (x.status === 401) {
                //on access denied try reviving the session
                OnLoginRequest();
            }
            else {
                VLMAlertDanger("An error occurred: " + status + "nError: " + error);
            }
        }
    });
    // Start converse
    //InitXmpp();

    // Init maps
    OLInit();
    
    // Load translation strings
    InitLocale();
    
    // Init Menus()
    InitMenusAndButtons();

    // Start-Up Polars manager
    PolarsManager.Init();
    
    // Go To WP Ortho, VMG, VBVMG Modes
    $("#BtnPM_Ortho, #BtnPM_VMG, #BtnPM_VBVMG").click(
      function()
      {
        var WpH=-1;
        var PMMode=PM_ORTHO;
        var Lat = $("#PM_Lat")[0].value;
        var Lon = $("#PM_Lon")[0].value;
        
        WpH = parseInt($("#PM_WPHeading")[0].value,10);
        
        switch ($(this)[0].id)
        {
          case "BtnPM_Ortho":
            PMMode=PM_ORTHO;
            break;

          case "BtnPM_VMG":
            PMMode=PM_VMG;
            break;

          case "BtnPM_VBVMG":
            PMMode=PM_VBVMG;
            break;

        }
        SendVLMBoatOrder(PMMode, Lon,Lat,WpH);
      }
    )
    
    
    // Handle boat selector selection change
    //
    $(".BoatSelectorDropDownList").on("click",HandleBoatSelectionChange)
    
    $('#cp11').colorpicker();

    // Init Alerts
    InitAlerts();
                           
    // Start the page clocks
    setInterval(PageClock,1000);
    
    // Load flags list (keep at the end since it takes a lot of time)
    GetFlagsList();
  }  
);

function OLInit() {

   //Pour tenter le rechargement des tiles quand le temps de calcul est > au timeout
    OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;

    var default_latitude = 45.5;
    var default_longitude = -30.0;
    var default_zoom = 4;

    if (typeof VLM2Prefs !== "undefined")
    {
      default_zoom = VLM2Prefs.MapPrefs.MapZoomLevel;
    }

    var layeroption = {
        //sphérique
        sphericalMercator: true,
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

    
     
    //Le calque de vent made in Vlm
    var grib = new Gribmap.Layer("Gribmap", layeroption);
    //grib.setOpacity(0.9); //FIXME: faut il garder une transparence du vent ?

    //La minimap utilise le layer VLM
    var vlmoverview = vlm.clone();

    //Et on ajoute tous les layers à la map.
    //map.addLayers([ VLMBoatsLayer,vlm, wms, bingroad, bingaerial, binghybrid, gphy, ghyb, gsat, grib]);
    map.addLayers([ grib, VLMBoatsLayer,vlm]);
    //map.addLayers([vlm, grib]); //FOR DEBUG

    //Controle l'affichage des layers
    //map.addControl(new OpenLayers.Control.LayerSwitcher());

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

    GribWindController = new Gribmap.ControlWind();
    map.addControl(GribWindController);

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

    // Click handler
    var click = new OpenLayers.Control.Click();
    map.addControl(click);
    click.activate();
}


function InitMenusAndButtons()
{
  $(".logindlgButton").on ('click',
      function (e)
      {
        // Show Login form
        $("#LoginForm").modal('show');
      }
  );
  
  $(".logOutButton").on ('click',
        function (e)
        {
          // Logout user
          Logout();
        }
  );
  
  $( "#Menu" ).menu();
  $( "#Menu" ).hide();
  
  $( "input[type=submit],button" )
      .button()
      .click(function( event ) 
        {
          event.preventDefault();
        }
      );
  
  // Theme tabs
  $(".JVLMTabs").tabs();
  
  // Hide all progressbars
  HidePb("#PbLoginProgress");
  HidePb("#PbGetBoatProgress");
  HidePb("#PbGribLoginProgress");

  // Add handler to set the WPMode controller in the proper tab
  $(".BCPane.WP_PM_Mode").click(
    function()
    {
      // Beurk , direct access by indexes :(
      // Assumes second class element is the id of target
      var target="#"+$(this)[0].classList[2];
      MoveWPBoatControlerDiv(target)
    }
  )

  // Display setting dialog
  $(".BtnRaceList").click(
    function()
    {
      LoadRacesList();
      $("#RacesListForm").modal("show");
    }
  )

  // Handle clicking on ranking button, and ranking sub tabs
  $("#Ranking-Panel").on('shown.bs.collapse',
        function(e)
        {
          HandleRaceSortChange(e);
        }
  )
  // Handle clicking on ranking button, and ranking sub tabs
  $(document.body).on('click',"[RnkSort]",function(e){HandleRaceSortChange(e)});
  $("#Ranking-Panel").on('hide.bs.collapse',
        function(e)
        {
          ResetRankingWPList(e);
        }
  )



  // Init event handlers
  // Login button click event handler
  $("#LoginButton").click( 
      function()
      {
        OnLoginRequest();
      }
  );   
  //valide par touche retour
  $('#LoginPanel').keypress(
    function(e) 
    {
      if (e.which === '13') {
          OnLoginRequest();
          $('#LoginForm').modal('hide');
      }
    }
  );
  // Display setting dialog
  $("#BtnSetting").click(
    function()
    {
      LoadVLMPrefs();
      SetDDTheme(VLM2Prefs.CurTheme);
      $("#SettingsForm").modal("show");
    }
  )

  // Handle SettingsSave button
  $('#SettingValidateButton').click(SaveBoatAndUserPrefs)
  
  // Handle SettingsSave button
  $('#SettingCancelButton').click(
    function()
    {
      LoadVLMPrefs();
      SetDDTheme(VLM2Prefs.CurTheme);
      $("#SettingsForm").modal("show");
    }
  )

  // Handle SettingsSave button
  $('#SettingValidateButton').click(SaveBoatAndUserPrefs)
  // Handle SettingsSave button
  $('#SettingCancelButton').click(function()
  {
    SetDDTheme(VLM2Prefs.CurTheme);
  })
  

  // Do fixed heading button
  $("#BtnPM_Heading").click(
    function()
    {
      SendVLMBoatOrder(PM_HEADING,$("#PM_Heading")[0].value)
    }

  );

  // Do fixed angle button
  $("#BtnPM_Angle").click(
    function()
    {
      SendVLMBoatOrder(PM_ANGLE,$("#PM_Angle")[0].value)
    }

  );

  // Tack
  $("#BtnPM_Tack").click(
    function()
    {
      $("#PM_Angle")[0].value= - $("#PM_Angle")[0].value;
    }
  )

  $("#BtnCreateAccount").click(
    function()
    {
      VLMAlert("Function not implemented yet","alert-danger");
    }
  )

  // Handler for Set WP on click
  $("#SetWPOnClick").click(HandleStartSetWPOnClick);
  $("#SetWPOffClick").click(HandleCancelSetWPOnClick);
  HandleCancelSetWPOnClick();

  // Add handlers for autopilot buttons
  $('body').on('click','.PIL_EDIT',HandlePilotEditDelete);
  $('body').on('click','.PIL_DELETE',HandlePilotEditDelete);
  

  // Init Datetime picker for autopilot
  $('.form_datetime').datetimepicker({
      language: 'fr',
      defaultTime: 'current',
      weekStart: 1,
      todayBtn: 1,
      autoclose: 1,
      todayHighlight: 1,
      startView: 2,
      forceParse: 0,
      showMeridian: 0
  });
  $('.form_date').datetimepicker({
      language: 'fr',
      defaultTime: 'current',
      weekStart: 1,
      todayBtn: 1,
      autoclose: 1,
      todayHighlight: 1,
      startView: 2,
      minView: 2,
      forceParse: 0
  });
  $('.form_time').datetimepicker({
      language: 'fr',
      defaultTime: 'current',
      weekStart: 1,
      todayBtn: 1,
      autoclose: 1,
      todayHighlight: 1,
      startView: 1,
      minView: 0,
      maxView: 1,
      forceParse: 0
  });

  $("#AutoPilotAddButton").click(HandleOpenAutoPilotSetPoint);
  $("#AP_SetTargetWP").click(HandleClickToSetWP)
  
  // AP datetime pickers
  $("#AP_Date").datetimepicker();
  $("#AP_Time").datetimepicker();
  $("#AP_Date").on('changeDate', HandleDateChange);
  $("#AP_Time").on('changeDate', HandleDateChange);
  $("#APValidateButton").click(HandleSendAPUpdate)
  $(".APField").on('change',HandleAPFieldChange);
  $(".APMode").on('click',HandleAPModeDDClick)

  // Draggable info window
  $(".Draggable").draggable(
            {
              handle: ".modal-header,.modal-body"
            });

  $("#MapPrefsToggle").click(HandleShowMapPrefs);

  $(".chkprefstore").on('change',HandleMapPrefOptionChange);
  $(".MapOppShowLi").click(HandleMapOppModeChange)

  $(".DDTheme").click(HandleDDlineClick)

  // Handle Start Boat Estimator button
  $("#StartEstimator").on('click',HandleStartEstimator)
  $("#EstimatorStopButton").on('click',HandleStopEstimator)

  InitGribSlider();

  // Handle race discontinuation request
  $("#DiscontinueRaceButton").on('click',HandleDiscontinueRaceRequest)
      
  // Init Pilototo footable, and get pointer to object          
  PilototoFt= FooTable.init("#PilototoTable",{
    'name' : "PilototoTable",
    'on':
    {
      'ready.ft.table' : HandleReadyTable,
      'postdraw.ft.table':HandleTableDrawComplete
    }
  });

  RankingFt = FooTable.init ("#RankingTable",{
    'name' : "RankingTable",
    'on':
    {
      'ready.ft.table' : HandleReadyTable,
      'postdraw.ft.table':HandleTableDrawComplete
    }
  });
  PilototoFt.DrawPending = true;
  RankingFt.DrawPending = true;
  
  

  // Add handler to refresh content of eth pilototo table when showing tab content
  $("[PilRefresh]").on('click', HandleUpdatePilototoTable)
  
  CheckLogin();
}

function HandleUpdatePilototoTable(e)
{
  UpdatePilotInfo(_CurPlayer.CurBoat);
}

function InitGribSlider()
{
  let handle = $( "#GribSliderHandle" );
  $( "#GribSlider" ).slider({
    orientation: "vertical",
    min: 0,
    max: 72,
    value: 0,
    create: function() {
      handle.text( $( this ).slider( "value" ) );
    },
    slide: function( event, ui ) {
      HandleGribSlideMove(event,ui);
    }
  });
  
}

function HandleRaceSortChange(e)
{
  let Target = $(e.currentTarget).attr('rnksort');

  //$("[rnksort]").removeClass("active")
  switch(Target)
  {
    case 'WP':
      SortRanking(Target,$(e.currentTarget).attr('WPRnk'));
      break;
    case 'DNF':
    case 'HTP':
    case 'HC':
    case 'ABD':
    case 'RAC':
    case 'ARR':
      SortRanking(Target);
      break;

    default:
      console.log("Sort change request"+e);
  }
  
}

function HandleGribSlideMove(event, ui )
{
  let handle = $( "#GribSliderHandle" );
  handle.text( ui.value);
  let l=GribWindController.getGribmapLayer();
  let GribEpoch = new Date().getTime();
  l.setTimeSegment(GribEpoch/1000 + ui.value*3600);

  if (VLM2Prefs.MapPrefs.TrackEstForecast && _CurPlayer.CurBoat.Estimator)
  {
    let EstPos = _CurPlayer.CurBoat.GetClosestEstimatePoint(new Date(GribEpoch + ui.value*3600*1000))
    RefreshEstPosLabels(EstPos);
  }
}

function HandleDiscontinueRaceRequest()
{
  GetUserConfirmation(GetLocalizedString('unsubscribe'),true,HandleRaceDisContinueConfirmation)
}

function HandleRaceDisContinueConfirmation(State)
{
  if (State)
  {
    //construct base
    let BoatId = _CurPlayer.CurBoat.IdBoat;
    let RaceId = _CurPlayer.CurBoat.Engaged;
    DiconstinueRace(BoatId,RaceId);
    $("#ConfirmDialog").modal('hide');
    $("#RacesInfoForm").modal('hide');
  }
  else
  {
    VLMAlertDanger("Ouf!")
  }  
}

function HandleStopEstimator(e)
{
  var CurBoat = _CurPlayer.CurBoat;

  if (typeof CurBoat === "undefined" || ! CurBoat)
  {
    // Something's wrong, just ignore
    return;
  }

  CurBoat.Estimator.Stop();
}

function HandleStartEstimator(e)
{
  var CurBoat = _CurPlayer.CurBoat;

  if (typeof CurBoat === "undefined" || ! CurBoat)
  {
    // Something's wrong, just ignore
    return;
  }

  CurBoat.Estimator.Start(HandleEstimatorProgress);
}

var LastPct = -1
  
function HandleEstimatorProgress(Complete, Pct, Dte)
{
  if (Complete)
  {
    $("#StartEstimator").removeClass("hidden")
    $("#PbEstimatorProgressBar").addClass("hidden")
    //$("#PbEstimatorProgressText").addClass("hidden")
    $("#EstimatorStopButton").addClass("hidden")
    LastPct = -1
  }
  else if (Pct - LastPct > 0.15)
  {
    $("#EstimatorStopButton").removeClass("hidden")
    $("#StartEstimator").addClass("hidden")
    $("#PbEstimatorProgressBar").removeClass("hidden")
    $("#PbEstimatorProgressText").removeClass("hidden")
    $("#PbEstimatorProgressText").text(Pct)
    $("#PbEstimatorProgress").css("width",Pct+"%");
    $("#PbEstimatorProgress").attr("aria-valuenow",Pct);
    $("#PbEstimatorProgress").attr("aria-valuetext",Pct);
    LastPct = Pct
  }
}

function HandleFlagLineClick(e)
{
  var Flag = e.target.attributes['flag'].value;

  SelectCountryDDFlag(Flag);
  
}

function HandleCancelSetWPOnClick()
{
  SetWPPending = false;
  $("#SetWPOnClick").show();
  $("#SetWPOffClick").hide();
}

function HandleStartSetWPOnClick()
{
  SetWPPending = true;
  WPPendingTarget = "WP";
  $("#SetWPOnClick").hide();
  $("#SetWPOffClick").show();

}

function ClearBoatSelector()
{
  $(".BoatSelectorDropDownList").empty();
}

function AddBoatToSelector(boat, isfleet)
{
  BuildUserBoatList(boat,isfleet);                          
}

function BuildUserBoatList(boat,IsFleet)
{
  $(".BoatSelectorDropDownList").append(GetBoatDDLine(boat,IsFleet));
}

function GetBoatDDLine(Boat, IsFleet)
{
   var Line = '<li class="DDLine" BoatID="'+Boat.IdBoat +'">'
   Line = Line + GetBoatInfoLine(Boat,IsFleet) + '</li>';
   return Line;
}

function GetBoatInfoLine(Boat,IsFleet)
{
  var Line = "";
  var BoatStatus="racing"

  if (!Boat.Engaged)
  {
    BoatStatus="Docked"
  }

  if ((typeof Boat.VLMInfo !== "undefined") && Boat.VLMInfo["S&G"])
  {
    BoatStatus = "stranded"
  }
  
  if (!IsFleet)
  {
    Line = Line + '<span class="badge">BS'
  }

  Line=Line+'<img class="BoatStatusIcon" src="images/'+BoatStatus+'.png" />'
  if (!IsFleet)
  {
    Line = Line + '</span>'
  }
  
  Line=Line+'<span>-</span><span>'+Boat.BoatName+'</span>'
  return Line  
}

function ShowBgLoad()
{
  $("#BgLoadProgress").css("display","block");
}

function HideBgLoad()
{
  $("#BgLoadProgress").css("display","block");
}

function ShowPb(PBName)
{
  $(PBName).show();
  //LocalizeString();
}

function HidePb(PBName)
{
  $(PBName).hide();
}

function DisplayLoggedInMenus(LoggedIn)
{
  var LoggedInDisplay;
  var LoggedOutDisplay;
  if (LoggedIn)
  {
    LoggedInDisplay="block";
    LoggedOutDisplay="none";
  }
  else
  {
    LoggedInDisplay="none";
    LoggedOutDisplay="block";
  }
  $("[LoggedInNav='true']").css("display",LoggedInDisplay);
  $("[LoggedInNav='false']").css("display",LoggedOutDisplay);

  // Display apropos
  $('#Apropos').modal(LoggedIn?'hide':'show');
  
}

function   HandleRacingDockingButtons(IsRacing)
{
  if (IsRacing)
  {
    $('[RacingBtn="true"]').removeClass("hidden");
    $('[RacingBtn="false"]').addClass("hidden");
  }
  else
  {
    $('[RacingBtn="true"]').addClass("hidden");
    $('[RacingBtn="false"]').removeClass("hidden");
  }
}


function UpdateInMenuDockingBoatInfo(Boat)
{
  var IsRacing = (typeof Boat !== "undefined") && (typeof Boat.VLMInfo !== "undefined") && parseInt(Boat.VLMInfo.RAC,10);
  HandleRacingDockingButtons(IsRacing);
}

function SetTWASign(Boat)
{
  var twd = Boat.VLMInfo.TWD;
  var heading = Boat.VLMInfo.HDG;
  
  twa = twd - heading;
  if (twa < -180 ) 
  {
    twa +=360;
  }
  
  if (twa > 180 ) 
  {
    twa -=360
  }


  var winddir = (360 - twd )%360 + 90;
  var boatdir = (360 - heading )%360 + 90;

  if ( twa * Boat.VLMInfo.TWA > 0 ) 
  {
    Boat.VLMInfo.TWA = - Boat.VLMInfo.TWA;
  }
    
}


function UpdateInMenuRacingBoatInfo(Boat, TargetTab)
{
  var NorthSouth;
  var EastWest;

  if (!Boat || typeof Boat === "undefined")
  {
    return;
  }

  HandleRacingDockingButtons(true);
  // Put a sign to the TWA
  SetTWASign(Boat)

  // Fix HDG when boat is mooring
  if (Boat.VLMInfo.PIM === "2" && Boat.VLMInfo.PIP ==="0")
  {
    // Mooring 
    Boat.VLMInfo.HDG = Boat.VLMInfo.TWD;
    Boat.VLMInfo.BSP = 0;
  }
  
  
  // Update GUI for current player
  // Todo Get Rid of Coords Class
  var lon = new Coords(Boat.VLMInfo.LON,true);
  var lat = new Coords(Boat.VLMInfo.LAT);

  // Create field mapping array
  // 0 for text fields
  // 1 for input fields
  var BoatFieldMappings=[];
  BoatFieldMappings.push([FIELD_MAPPING_TEXT,"#BoatLon",lon.ToString() ]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatLat",lat.ToString() ]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatSpeed",Math.round(Boat.VLMInfo.BSP * 10)/10]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatHeading",Math.round(Boat.VLMInfo.HDG * 10)/10]);
  BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Heading",Math.round(Boat.VLMInfo.HDG * 10)/10]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatAvg",Math.round(Boat.VLMInfo.AVG * 10)/10 ]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatDNM",Math.round(Boat.VLMInfo.DNM * 10)/10 ]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatLoch",Math.round(Boat.VLMInfo.LOC * 10)/10 ]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatOrtho",Math.round(Boat.VLMInfo.ORT * 10)/10 ]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatLoxo",Math.round(Boat.VLMInfo.LOX * 10)/10 ]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatVMG",Math.round(Boat.VLMInfo.VMG * 10)/10 ]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatWindSpeed",Math.round(Boat.VLMInfo.TWS * 10)/10 ]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatWindDirection",Math.round(Boat.VLMInfo.TWD * 10)/10 ]);
  BoatFieldMappings.push([FIELD_MAPPING_CHECK,"#PM_WithWPHeading", Boat.VLMInfo['H@WP'] !== "-1.0"]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#RankingBadge", Boat.VLMInfo.RNK]);
  BoatFieldMappings.push([FIELD_MAPPING_VALUE,"#PM_WPHeading",Boat.VLMInfo['H@WP']]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatClass", Boat.VLMInfo.POL.substring(5)]);
  
  WP = new VLMPosition(Boat.VLMInfo.WPLON,Boat.VLMInfo.WPLAT);
  BoatFieldMappings.push([FIELD_MAPPING_VALUE,"#PM_Lat", WP.Lat.Value]);
  BoatFieldMappings.push([FIELD_MAPPING_VALUE,"#PM_Lon", WP.Lon.Value]);
  
  if ((WP.Lon.Value === 0) && (WP.Lat.Value === 0))
  {
    WP = Boat.GetNextWPPosition();
  }

  if (typeof WP !== "undefined" && WP)
  {
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#PM_CurWPLat", WP.Lat.ToString()]);
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#PM_CurWPLon", WP.Lon.ToString()]);
  }
  else
  {
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#PM_CurWPLat", "N/A"]);
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#PM_CurWPLon", "N/A"]);
  }
  
  if (parseInt(Boat.VLMInfo.PIM,10) === PM_ANGLE)
  {
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatWindAngle",Math.round(Math.abs(Boat.VLMInfo.PIP) * 10)/10 ]);
    BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Angle",Boat.VLMInfo.PIP ]);
  }
  else
  {
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatWindAngle",Math.round(Math.abs(Boat.VLMInfo.TWA) * 10)/10 ]);
    BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Angle",Math.round(Boat.VLMInfo.TWA * 10)/10 ]);
  }

  // Race Instruction
  if (typeof Boat.RaceInfo !== "undefined" && Boat.RaceInfo)
  {
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".RaceName",Boat.RaceInfo.racename]);
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatType",Boat.RaceInfo.boattype.substring(5)]);
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".VacFreq",parseInt(Boat.RaceInfo.vacfreq,10)]);
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#EndRace",parseInt(Boat.RaceInfo.firstpcttime,10)]);
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#RaceStartDate",new Date(parseInt(Boat.RaceInfo.deptime,10)*1000)]);
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#RaceLineClose",new Date(parseInt(Boat.RaceInfo.closetime,10)*1000)]);
    BoatFieldMappings.push([FIELD_MAPPING_IMG,"#RaceImageMap","/cache/racemaps/"+Boat.RaceInfo.idraces+".png"])
    BoatFieldMappings.push([FIELD_MAPPING_CALLBACK,"#RaceWayPoints",function(p){FillRaceWaypointList(p,Boat)}])
  }

  // Loop all mapped fields to their respective location
  for (index in BoatFieldMappings)
  {
    switch (BoatFieldMappings[index][0])
    {
      case FIELD_MAPPING_TEXT:
        $(BoatFieldMappings[index][1]).text(BoatFieldMappings[index][2]);
        break;

      case FIELD_MAPPING_VALUE:
        $(BoatFieldMappings[index][1]).val(BoatFieldMappings[index][2]);
        break;
      
      case FIELD_MAPPING_CHECK:
        $(BoatFieldMappings[index][1]).prop('checked',(BoatFieldMappings[index][2]));
        break;

      case FIELD_MAPPING_IMG:
        $(BoatFieldMappings[index][1]).attr('src',(BoatFieldMappings[index][2]));
        break;

      case FIELD_MAPPING_CALLBACK:
        BoatFieldMappings[index][2](BoatFieldMappings[index][1]);
        break;

      
    }
  }
 
  // Change color depênding on windangle
  var WindColor="lime"
  if (Boat.VLMInfo.TWA > 0)
  {
    WindColor="red"
  }
  $(".BoatWindAngle").css("color",WindColor);

  // Get WindAngleImage
  var wHeading=Math.round((Boat.VLMInfo.TWD+180) * 100)/100;
  var wSpeed=Math.round(Boat.VLMInfo.TWS * 100)/100;
  var BoatType=Boat.VLMInfo.POL;
  var BoatHeading=Math.round(Boat.VLMInfo.HDG*100)/100;
  var WindSpeed=Math.round(Boat.VLMInfo.TWS*100)/100;
  var OrthoToWP=Math.round(Boat.VLMInfo.ORT*100)/100;


  $("#ImgWindAngle").attr('src','windangle.php?wheading='+wHeading+'&boatheading='+ BoatHeading +'&wspeed='+WindSpeed+'&roadtoend='+OrthoToWP+'&boattype='+BoatType+"&jvlm="+Boat.VLMInfo.NOW);
  $("#ImgWindAngle").css("transform","rotate("+wHeading+"deg)");
  $("#DeckImage").css("transform","rotate("+BoatHeading+"deg)");


   // Set active PM mode display
   $(".PMActiveMode").css("display","none");
  $(".BCPane").removeClass("active");
   var TabID = ".ActiveMode_";
   var ActivePane ="";

   switch (Boat.VLMInfo.PIM)
   {
     case "1":
      TabID += 'Heading';
      ActivePane="BearingMode"
      break;
     case "2":
      TabID += 'Angle';
      ActivePane="AngleMode"
      break;
     case "3":
      TabID += 'Ortho';
      ActivePane="OrthoMode"
      break;
     case "4":
      TabID += 'VMG';
      ActivePane="VMGMode"
      break;
     case "5":
      TabID += 'VBVMG';
      ActivePane="VBVMGMode"
      break;

    default:
      VLMAlert("Unsupported VLM PIM Mode, expect the unexpected....","alert-info");
      
   }

   // Override PIM Tab if requested
   /*if (typeof TargetTab !== "undefined" && TargetTab=='AutoPilot')
   {
     TabID+='AutoPilotTab';
     ActivePane=TargetTab;
     UpdatePilotInfo(Boat);
   }*/

    $(TabID).css("display","inline");
    $("."+ActivePane).addClass("active");
    $("#"+ActivePane).addClass("active");

    UpdatePilotInfo(Boat);
    UpdatePolarImages(Boat);

} 

function FillRaceWaypointList(p,Boat)
{
  $(p).empty();
}

function UpdatePolarImages(Boat)
{
  var PolarName = Boat.VLMInfo.POL.substring(5);
  var Angle;
  var HTML=""
  for (Angle=0; Angle <= 45; Angle +=15)
  {
    HTML += '<li><img class="polaire" src="/scaledspeedchart.php?boattype=boat_'+PolarName+'&amp;minws='+Angle+'&amp;maxws='+(Angle+15)+'&amp;pas=2" alt="speedchart"></li>'
  }

  $("#PolarList").empty();
  $("#PolarList").append(HTML);
}

function UpdatePilotInfo(Boat)
{
  if ((typeof Boat === "undefined") || (!Boat) || PilototoFt.DrawPending)
  {
    return;
  }   

  //let PIL_TEMPLATE = $("#PIL_TEMPLATE");
  //PIL_TEMPLATE.hide()
  
  /*for (index=1; index <=5 ; index ++)
  {
    $("#PIL"+PilIndex).hide();
  }*/

  PilRows=[];
  if (Boat.VLMInfo.PIL.length >0)
  {
    for (index in Boat.VLMInfo.PIL)
    {
      if (Boat.VLMInfo.PIL[index])
      {
        /*var PilIndex = parseInt(index,10)+1;
        //var PrevIndex = PilIndex -1;
        var PilLine = $("#PIL"+PilIndex).first();
        if (!PilLine.length)
        {
          PilLine = PIL_TEMPLATE.clone();
          PilLine.attr('id',"PIL"+PilIndex);
          
          $("#PilototoBodyTable").append(PilLine);
          PilLine.removeClass("hidden").addClass("pilototocol");
          
        }

        //PilLine.insertAfter($("#PIL"+PrevIndex));
        $("#PIL"+PilIndex+" .PIL_EDIT").attr("PIL_ID",PilIndex);        
        $("#PIL"+PilIndex+" .PIL_DELETE").attr("TID",Boat.VLMInfo.PIL[index].TID);
          
        ShowAutoPilotLine(Boat,PilIndex);  
        */
        var PilLine = GetPilototoTableLigneObject(Boat,index);
        PilRows.push(PilLine);
      }

       
    } 
    
    
    if (Boat.VLMInfo.PIL.length < MAX_PILOT_ORDERS)
    {
      $("#AutoPilotAddButton").removeClass("hidden");
    }
    else
    {
      $("#AutoPilotAddButton").addClass("hidden");  
    }
  }

  PilototoFt.loadRows(PilRows,false)
  PilototoFt.DrawPending = true;
  
  UpdatePilotBadge(Boat);
}

function HandleReadyTable(e,ft)
{
  ft.DrawPending = false;
}

function HandleTableDrawComplete(e,ft)
{
  ft.DrawPending = false ;
  if (ft === RankingFt)
  {
    setTimeout( DeferedGotoPage,500);
  }
}

function DeferedGotoPage()
{
  if (RankingFt.TargetPage)
  {
    RankingFt.gotoPage(RankingFt.TargetPage);
    RankingFt.TargetPage = 0;
  }
}

function GetPilototoTableLigneObject(Boat,Index)
{
  let PilOrder=Boat.VLMInfo.PIL[Index];
  let OrderDate = new Date(PilOrder.TTS*1000)
  let PIMText = GetPilotModeName(PilOrder.PIM);

  // Force as number and rebase from 1
  Index = parseInt(Index,10)+1;

  // Adapt the template to current order
  $("#EditCellTemplate .PIL_EDIT").attr('pil_id',Index)
  $("#DeleteCellTemplate .PIL_DELETE").attr("TID",PilOrder.TID).attr('pil_id',Index)

  let Ret = {
      date:OrderDate,
      PIM:PIMText,
      PIP:PilOrder.PIP,
      Status:PilOrder.STS,
      Edit:$("#EditCellTemplate").first().html(),
      Delete:$("#DeleteCellTemplate").first().html()
  }
  

  return Ret;
}

function ShowAutoPilotLine(Boat,Index)
{
  var Id = "#PIL"+Index;
  var PilOrder=Boat.VLMInfo.PIL[Index-1];
  var OrderDate = new Date(PilOrder.TTS*1000)
  var PIMText = GetPilotModeName(PilOrder.PIM);

  if (typeof $(Id)[0]==="undefined")
  {
    let bpkt = 0;
  }

  $(Id)[0].attributes['TID']=PilOrder.TID
  SetSubItemValue(Id,"#PIL_DATE",OrderDate)
  SetSubItemValue(Id,"#PIL_PIM",PIMText)
  SetSubItemValue(Id,"#PIL_PIP",PilOrder.PIP)
  SetSubItemValue(Id,"#PIL_STATUS",PilOrder.STS)
  $(Id).show();
}

function GetPILIdParentElement(item)
{
  var done = false;
  var RetValue=item;
  do
  {
    if (typeof RetValue === "undefined")
    {
      return
    }
    if ('id' in RetValue.attributes)
    {
      var ItemId = RetValue.attributes['id'].value;
      if ((ItemId.length === 4) && (ItemId.substring(0,3) === "PIL") )
      {
        return RetValue;
      }
    }
    
    RetValue = RetValue.parentElement;
    
  } while (!done)
}

function HandlePilotEditDelete(e)
{
  var ClickedItem = $(this)[0]
  var ItemId = ClickedItem.attributes['class'].value;
  var Boat = _CurPlayer.CurBoat;

  var OrderIndex = parseInt( ClickedItem.attributes['pil_id'].value,10);

  if (ItemId === "PIL_EDIT")
  {
    HandleOpenAutoPilotSetPoint (e);
  }
  else if (ItemId === "PIL_DELETE")
  {
    DeletePilotOrder(Boat,ClickedItem.attributes['TID'].value);
  }

}

function GetPilotModeName(PIM)
{
  switch (parseInt(PIM ,10))
  {
    case 1:
      return GetLocalizedString('autopilotengaged')
      
    case 2:
      return GetLocalizedString('constantengaged')
      
    case 3:
      return GetLocalizedString('orthoengaged')
      
    case 4:
      return GetLocalizedString('bestvmgengaged')

    case 5:
      return GetLocalizedString('vbvmgengaged')

    default:
      return "PIM ???"+PIM+"???" 
  }
}

function SetSubItemValue(SourceElementName,TargetElementName,NewVaue)
{
  var El = $(SourceElementName).find(TargetElementName)
  if (El.length>0)
  {
    El.text(NewVaue)
  }
}


function UpdatePilotBadge(Boat)
{
  var index;
  var PendingOrdersCount = 0
    
  if ((typeof Boat === "undefined") || (!Boat))
  {
    return;
  }

  var Pilot = Boat.VLMInfo.PIL;

  if (Pilot.length)
  {
    for (index in Pilot)
    {
      if (Pilot[index].STS==="pending")
      {
        PendingOrdersCount++;
      }
    }
  }
  
  if (PendingOrdersCount >0)
  {
    $(".PilotOrdersBadge").show();
    $(".PilotOrdersBadge").text(PendingOrdersCount);
  }
  else
  {
    $(".PilotOrdersBadge").hide();
  }

  
}

function MoveWPBoatControlerDiv(target)
{
  var div = $(target).prepend($("#PM_WPMode_Div"));
}

function UpdatePrefsDialog(Boat)
{
  // Hide prefs setting button is not boat or no vlminfo yet...
  if (typeof Boat === "undefined")
  {
    $("#BtnSetting").addClass("hidden");
  }
  else
  {
    $("#BtnSetting").removeClass("hidden");
    $("#pref_boatname").val(Boat.BoatName);

    if (typeof Boat.VLMInfo !== 'undefined')
    {
      SelectCountryDDFlag(Boat.VLMInfo.CNT);
      var ColString = SafeHTMLColor( Boat.VLMInfo.COL);
      
      $("#pref_boatcolor").val(ColString);
      $("#cp11").colorpicker({color:ColString});
    }
  }

  

}

let RaceSorter = function RaceSortEvaluator (r1, r2)
{
  if (r1.CanJoin === r2.CanJoin)
  {
    if (r1.deptime > r2.deptime)
    {
      return -1;
    }
    else if (r1.deptime === r2.deptime)
    {
      if (r1.racename > r2.racename)
      {
        return 1;
      }
      else if (r1.racename === r2.racename)
      {
        return 0;
      }
      else
      {
        return -1;
      }
    }
    else
    {
      return 1;
    }
    
  }
  else if (r1.CanJoin)
  {
    return 1;
  }
  else
  {
    return -1;
  }
}

function LoadRacesList()
{
  let CurUser = _CurPlayer.CurBoat.IdBoat;
  $.get("/ws/raceinfo/list.php?iduser="+CurUser,
    function (result)
    {
      var racelist= result;

      // Clear previous elements
      $("#RaceListPanel").empty();
      let RaceArray = [];
      for (index in racelist)
      {
        if (racelist[index])
        {
          RaceArray.push(racelist[index]);
        }
      }
      RaceArray.sort(RaceSorter);
      for (index in RaceArray)
      {
        if (RaceArray[index])
        {
          AddRaceToList(RaceArray[index]);
        }
      }
    }
  )
}

function AddRaceToList(race)
{
  let base = $("#RaceListPanel").first();

  
  let d = new Date(0); // The there is the key, which sets the date to the epoch
  //d.setUTCSeconds(utcSeconds);
  let RaceJoinStateClass 
  
  if(race.CanJoin)
  {
    let Now = new Date();
    let RaceStart = new Date(race.deptime*1000);
    if (RaceStart <=Now)
    {
      RaceJoinStateClass = 'CanJoinRace'
    }
    else
    {
      RaceJoinStateClass = 'CanJoinRaceNotStarted'
    }
  }
  else
  {
    RaceJoinStateClass = 'NoJoinRace'
  }
  
  let code = '<div class="raceheaderline panel panel-default '+RaceJoinStateClass+'" )>' +
              '  <div data-toggle="collapse" href="#RaceDescription'+race.idraces+'" class="panel-body collapsed " data-parent="#RaceListPanel" aria-expanded="false">'+
              '    <div class="col-xs-4">'+
              '      <img class="racelistminimap" src="/cache/minimaps/'+race.idraces+'.png" ></img>'+
              '    </div>'+
              '    <div class="col-xs-4">'+
              '      <span ">'+ race.racename +
              '      </span>'+
              '    </div>'+
              '    <div class="'+(race.CanJoin?'':'hidden') +' col-xs-4">'+
              '      <button id="JoinRaceButton" type="button" class="btn-default btn-md" IdRace="'+ race.idraces +'"  >'+GetLocalizedString("subscribe")+
              '      </button>'+
              '    </div>'+
              '  <div id="RaceDescription'+race.idraces+'" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">'+
              '  <div class="col-xs-12"><img class="img-responsive" src="/cache/racemaps/'+race.idraces+'.png" width="530px"></div>'+
              '  <div class="col-xs-12"><p>' + GetLocalizedString('race') +' : '+ race.racename +'</p>'+
              '     <p>Départ : ' + new Date(race.deptime*1000) + '</p>'+
              '     <p>'+ GetLocalizedString('boattype') +' : ' + race.boattype.substring(5) +'</p>'+
              '     <p>'+ GetLocalizedString('crank') +' : '+ race.vacfreq + '\'</p>'+
              '     <p>'+ GetLocalizedString('closerace') + new Date(race.closetime*1000) + '</p>'+
              '   </div>'

  base.prepend(code);

  // Handler for the join race button
  $("#JoinRaceButton").click(
    function(e)
    {
      var RaceId  = e.currentTarget.attributes.idrace.value;

      EngageBoatInRace(RaceId,_CurPlayer.CurBoat.IdBoat);
      

    }
  )
}

function PageClock()
{

  if (typeof _CurPlayer !== "undefined" && typeof _CurPlayer.CurBoat !== "undefined")
  {

    // Display race clock if a racing boat is selected
    var CurBoat = _CurPlayer.CurBoat;

    if (typeof CurBoat !== "undefined" && typeof CurBoat.RaceInfo !== "undefined")
    {
      var ClockValue=GetRaceClock(CurBoat.RaceInfo, CurBoat.VLMInfo.UDT);
      var Chrono = $(".RaceChrono");
      if (ClockValue < 0 )
      {
        Chrono.removeClass("ChronoRaceStarted").addClass("ChronoRacePending");
      }
      else
      {
        Chrono.addClass("ChronoRaceStarted").removeClass("ChronoRacePending");
      }

      var LastBoatUpdate = new Date(CurBoat.VLMInfo.LUP*1000);
      var TotalVac = CurBoat.VLMInfo.VAC;
      var TimeToNextUpdate = TotalVac - ((new Date() - LastBoatUpdate)/1000)%TotalVac;
      var Delay = 1000;
      if (TimeToNextUpdate >= TotalVac-1  )
      {
        Delay=100;
      }
      $("#pbar_innerdivvac").css("width",+Math.round((TimeToNextUpdate%60)*100.0/60.0)+"px");
      $("#pbar_innerdivmin").css("width",Math.round((TimeToNextUpdate/TotalVac)*100.0)+"px");

      Chrono.text(GetFormattedChronoString(ClockValue));
    }
  } 
}

function GetRaceClock(RaceInfo,UserStartTimeString)
{
  var CurDate=new Date();
  var Epoch=new Date(RaceInfo.deptime*1000);

  if (!(RaceInfo.racetype & RACE_TYPE_RECORD))
  {
    // non Permanent race chrono counts from race start time
    return Math.floor((CurDate-Epoch)/1000);
  }
  else
  {
    var UDT = parseInt(UserStartTimeString,10);

    if (UDT === -1)
    {
      return 0;
    }
    else
    {
      var StartDate = new Date(UDT*1000);
      return Math.floor((CurDate-StartDate)/1000);
    }
  }
  
  
}

function DisplayCurrentDDSelectedBoat(Boat)
{
  $('.BoatDropDown:first-child').html(
  '<span BoatID='+ Boat.IdBoat +'>'+GetBoatInfoLine(Boat,Boat.IdBoat in _CurPlayer.Fleet)+'</span>'+
  '<span class="caret"></span>'
  )
}

function PadLeftZero(v)
{
  if (v < 100)
  {
    return ("00"+v).slice(-2);
  }
  else
  {
    return v;
  }
}

function GetFormattedChronoString(Value)
{
  if (Value < 0)
  {
    Value = -Value;
  }
  else if (Value === 0)
  {
    return "--:--:--";
  }

  var Sec = PadLeftZero(Value % 60);
  var Min = PadLeftZero(Math.floor(Value/60) % 60);
  var Hrs = PadLeftZero(Math.floor(Value/3600 ) % 24);
  var Days = PadLeftZero(Math.floor(Value / 3600 / 24 ));

  var Ret = Hrs.toString() + ":" + Min.toString() +":"+Sec.toString(); 
  if (Days > 0)
  {
    Ret = Days.toString()+" d "+Ret;
  }
  return Ret;
}

function RefreshCurrentBoat(SetCenterOnBoat,ForceRefresh,TargetTab)
{
  var BoatIDSpan = $('.BoatDropDown > span')
  
  if (typeof BoatIDSpan !== "undefined" && typeof BoatIDSpan[0] !== "undefined" && 'BoatId' in BoatIDSpan[0].attributes)
  {
    BoatId=BoatIDSpan[0].attributes['BoatID'].value;
    SetCurrentBoat(GetBoatFromIdu(BoatId), SetCenterOnBoat,ForceRefresh,TargetTab)
  }
  
}

function UpdateLngDropDown()
{
  // Init the language combo to current language
  var lng = GetCurrentLocale();

  $('#SelectionLanguageDropDown:first-child').html(
    '<img class=" LngFlag" lang="'+lng+'" src="images/lng-'+lng+'.png" alt="'+lng+'">'+
    '<span class="caret"></span>'
    )

}

var _CurAPOrder=null;

function HandleOpenAutoPilotSetPoint(e) 
{
  var Target = e.target;
  var TargetId;
  
  if ('id' in Target.attributes )
  {
    TargetId = Target.attributes["id"].nodeValue;
  }
  else if ('class' in Target.attributes)
  {
    TargetId = Target.attributes["class"].nodeValue;
  }
  else
  {
    VLMAlert("Something bad has happened reload this page....","alert-danger");
    return;
  }
  switch(TargetId)
    {
      case "AutoPilotAddButton":
        // Create a new autopilot order
        _CurAPOrder = new AutoPilotOrder();
        break;
      case "PIL_EDIT":
        // Load AP Order from vlminfo structure
        var OrderIndex =parseInt(Target.attributes["pil_id"].value,10);
        _CurAPOrder = new AutoPilotOrder (_CurPlayer.CurBoat,OrderIndex)

        $("#AutoPilotSettingForm").modal('show');
        break;
      default:
        VLMalert("Something bad has happened reload this page....","alert-danger");
        return;
               
    }

    RefreshAPDialogFields()

}

function RefreshAPDialogFields()
{
  // Update dialog content from APOrder object
  $("#AP_Date").datetimepicker('update',_CurAPOrder.Date);
  $("#AP_Time").datetimepicker('update',_CurAPOrder.Date);

  $('#AP_PIM:first-child').html(
  '<span>'+_CurAPOrder.GetPIMString()+'</span>'+
  '<span class="caret"></span>'
  )
  $("#AP_PIP").val(_CurAPOrder.PIP_Value);
  $("#AP_WPLat").val(_CurAPOrder.PIP_Coords.Lat.Value);
  $("#AP_WPLon").val(_CurAPOrder.PIP_Coords.Lon.Value);
  $("#AP_WPAt").val(_CurAPOrder.PIP_WPAngle);
  

  UpdatePIPFields(_CurAPOrder.PIM);
  
}
var _DateChanging=false
function HandleDateChange(ev)
{
  if (!_DateChanging)
  {
    _DateChanging=true;
    _CurAPOrder.Date = ev.date;
    $("#AP_Date").datetimepicker('update',_CurAPOrder.Date);
    $("#AP_Time").datetimepicker('update',_CurAPOrder.Date);
    _DateChanging=false;
  }
  
}

function HandleClickToSetWP()
{
  SetWPPending = true;
  WPPendingTarget = "AP";
  $("#AutoPilotSettingForm").modal("hide")
}

function HandleAPModeDDClick(e)
{
  var NewMode = e.target.attributes["PIM"].value;

  _CurAPOrder.PIM=parseInt(NewMode,10);
   $('#AP_PIM:first-child').html(
    '<span>'+_CurAPOrder.GetPIMString()+'</span>'+
    '<span class="caret"></span>'
    )

  UpdatePIPFields(_CurAPOrder.PIM);
    
}

function UpdatePIPFields(PIM)
{
  var IsPip = true
  switch (PIM)
  {
    case PM_HEADING:
    case PM_ANGLE:
      IsPip=true
      break;
    case PM_ORTHO:
    case PM_VMG:
    case PM_VBVMG:
      IsPip=false
      break;
  }

  if (IsPip)
  {
    $(".AP_PIPRow").removeClass("hidden");
    $(".AP_WPRow").addClass("hidden");
  }
  else
  {
    $(".AP_PIPRow").addClass("hidden");
    $(".AP_WPRow").removeClass("hidden");
  }
}

function SaveBoatAndUserPrefs(e)
{
  // Check boat prefs
  var NewVals={};
  var BoatUpdateRequired =false;
  var PlayerUpdateRequired = false;

  // Get Theme
  var NewTheme = $("#SelectionThemeDropDown").attr("SelTheme");

  if (typeof NewTheme  !== "undefined")
  {
    VLM2Prefs.CurTheme = NewTheme    
  }

  VLM2Prefs.Save();

  if (!ComparePrefString($("#pref_boatname")[0].value,_CurPlayer.CurBoat.BoatName))
  {
    NewVals["boatname"]=encodeURIComponent($("#pref_boatname")[0].value);
    BoatUpdateRequired = true;
  }
  
  if (!ComparePrefString($("#pref_boatcolor")[0].value,SafeHTMLColor(_CurPlayer.CurBoat.VLMInfo.COL)))
  {
    NewVals["color"]=$("#pref_boatcolor")[0].value.substring(1);
    BoatUpdateRequired = true;
  }

  var NewCountry= GetPrefSelFlag();
  if (!ComparePrefString(NewCountry,_CurPlayer.CurBoat.VLMInfo.CNT))
  {
    NewVals["country"]=encodeURIComponent(NewCountry);
    BoatUpdateRequired = true;
  }

  //NewVals["country"]=$("#FlagSelector")[0].value;
  //NewVals["color"]=$("#pref_boatcolor")[0].value;
  if (BoatUpdateRequired)
  {
    UpdateBoatPrefs(_CurPlayer.CurBoat,{prefs:NewVals})
  }
}

function GetPrefSelFlag()
{
  var Item =$('#CountryDropDown:first-child [flag]')[0];
  return Item.attributes["flag"].value;
    
}

function ComparePrefString(Obj1, Obj2)
{

  return Obj1.toString() === Obj2.toString()
}

function SelectCountryDDFlag(Country)
{
  $('#CountryDropDown:first-child').html('<div>'+GetCountryDropDownSelectorHTML(Country,false)+'<span class="caret"></span></div>');
    
}

function ResetCollapsiblePanels(e)
{
  $(".collapse").collapse("hide");

}

function HandleBoatSelectionChange(e)
{
  ResetCollapsiblePanels();

  let BoatId= $(e.target).closest('li').attr('BoatID');
  let Boat = GetBoatFromIdu(BoatId);

  if (typeof Boat === "undefined" || ! Boat)
  {
    VLMAlertDanger(GetLocalizedString('Error Reload'));
    return;
  }
  SetCurrentBoat(Boat,true,false); 
  DisplayCurrentDDSelectedBoat(Boat);
}

var LastMouseMoveCall = 0;

function HandleMapMouseMove(e)
{

  if (GM_Pos  && (typeof _CurPlayer!=="undefined") && (typeof _CurPlayer.CurBoat !== 'undefined') && (typeof _CurPlayer.CurBoat.VLMInfo !== "undefined"))
  {
    var Pos = new VLMPosition(GM_Pos.lon,GM_Pos.lat)
    var CurPos  = new VLMPosition(_CurPlayer.CurBoat.VLMInfo.LON,_CurPlayer.CurBoat.VLMInfo.LAT)
    var WPPos = _CurPlayer.CurBoat.GetNextWPPosition();
    var EstimatePos = null ;
    var Estimated = new Date()-LastMouseMoveCall > 300;
    
    if (VLM2Prefs.MapPrefs.EstTrackMouse && Estimated)
    {
      // Throttle estimate update to 3/sec
      EstimatePos=_CurPlayer.CurBoat.GetClosestEstimatePoint(Pos);
      LastMouseMoveCall = new Date();
    }


    $("#MI_Lat").text(Pos.Lat.ToString());
    $("#MI_Lon").text(Pos.Lon.ToString());
    $("#MI_LoxoDist").text(CurPos.GetLoxoDist(Pos,2) + " nM");
    $("#MI_OrthoDist").text(CurPos.GetOrthoDist(Pos,2) + " nM");
    $("#MI_Loxo").text(CurPos.GetLoxoCourse(Pos,2) + " °");
    $("#MI_Ortho").text(CurPos.GetOrthoCourse(Pos,2) + " °");
    
    if (typeof WPPos !== "undefined" && WPPos)
    {
      $("#MI_WPLoxoDist").text(WPPos.GetLoxoDist(Pos,2) + " nM");
      $("#MI_WPOrthoDist").text(WPPos.GetOrthoDist(Pos,2) + " nM");
      $("#MI_WPLoxo").text(WPPos.GetLoxoCourse(Pos,2) + " °");
      $("#MI_WPOrtho").text(WPPos.GetOrthoCourse(Pos,2) + " °");
    }
    else
    {
      $("#MI_WPLoxoDist").text("--- nM");
      $("#MI_WPOrthoDist").text( "--- nM");
      $("#MI_WPLoxo").text("--- °");
      $("#MI_WPOrtho").text( "--- °");
    }

    if (Estimated)
    { 
       RefreshEstPosLabels (EstimatePos);
    } 
    
  }  
}

function RefreshEstPosLabels(Pos)
{
  if (Pos && typeof Pos.Date !== "undefined")
  {
    $("#MI_EstDate").text(Pos.Date); 
  }
  else
  {
    $("#MI_EstDate").text("");
  }
}

function GetWPrankingLI(WPInfo)
{
  return '<li id="RnkWP'+ WPInfo.wporder +'" RnkSort="WP" WPRnk="'+ WPInfo.wporder +'"><a href="#DivRnkRAC" RnkSort="WP" WPRnk="'+ WPInfo.wporder +'">WP '+ WPInfo.wporder +' : '+WPInfo.libelle + '</a></li>';
}

function ResetRankingWPList(e)
{
  $("[WPRnk]").remove();
  $("#RnkTabsUL").addClass("WPNotInited");
  
}

function CheckWPRankingList(Boat)
{
  let InitNeeded = $(".WPNotInited")

  if (typeof InitNeeded !== "undefined" && InitNeeded)
  {

    let index;

    for (index in Boat.RaceInfo.races_waypoints)
    {
      if (Boat.RaceInfo.races_waypoints[index])
      {
        let WPInfo = Boat.RaceInfo.races_waypoints[index];
        let html = GetWPrankingLI(WPInfo);
        $(InitNeeded).append(html);
      }
      
    }
    
  }

  $(InitNeeded).removeClass("WPNotInited");
  $(".JVLMTabs").tabs("refresh");
  
}

function SortRanking(style, WPNum)
{

  
  //$('#RankingTableBody').empty();
  var Boat = _CurPlayer.CurBoat;
  CheckWPRankingList(Boat);
  
  if (typeof Boat === "undefined" || !Boat)
  {
    return ;
  }

  switch (style)
  {
    case "WP":
      SetRankingColumns(style);
      WPNum = parseInt(WPNum,10)
      SortRankingData(Boat, style,WPNum);
      FillWPRanking(Boat,WPNum);
      break;

    case 'DNF':
    case 'HC':
    case 'ARR':
    case 'HTP':
    case 'ABD':
      SetRankingColumns(style);
      SortRankingData(Boat,style)
      FillStatusRanking(Boat,style);
      break;
    case 'RAC':
    default:
      SetRankingColumns('RAC');
      CurRnk = SortRankingData(Boat,'RAC')
      FillRacingRanking(Boat);
      
  }

}

function SetRankingColumns(style)
{
  switch (style)
  {
    case "WP":
      SetWPRankingColumns()
      break;

    case 'DNF':
    case 'HC':
    case 'ARR':
    case 'HTP':
    case 'ABD':
      SetNRClassRankingColumns()
      break;
    case 'RAC':
    default:
      SetRacingClassRankingColumns()
    
  }
}

let RACColumnHeader = ["Rank","Name" ,"Distance","Time","Loch" ,"Lon" ,"Lat","Last1h" ,"Last3h" ,"Last24h"]
let NRColumnHeader = ["Rank","Name" ,"Distance"]
let WPColumnHeader = ["Rank","Name" ,"Time","Loch"]
let RACColumnHeaderLabels = ["ranking","boatname" ,"distance","racingtime","Loch" ,"Lon" ,"Lat","Last1h" ,"Last3h" ,"Last24h"]
let NRColumnHeaderLabels = ["ranking","boatname" ,"status"]
let WPColumnHeaderLabels = ["ranking","boatname" ,"racingtime","ecart"]


function SetRacingClassRankingColumns()
{

  SetColumnsVisibility(RACColumnHeader, RACColumnHeaderLabels);

}

function SetNRClassRankingColumns()
{
  SetColumnsVisibility(NRColumnHeader, NRColumnHeaderLabels)
}

function SetWPRankingColumns()
{
  SetColumnsVisibility(WPColumnHeader, WPColumnHeaderLabels)
}

function SetColumnsVisibility(cols,labels)
{
  let index;
  for (index = 0 ; index < RankingFt.columns.array.length; index++)
  {
    if (RankingFt.columns.array[index])
    {
      let ColIdx = cols.indexOf(RankingFt.columns.array[index].name)
      if (ColIdx > -1)
      {
        //RankingFt.columns.array[index].title = GetLocalizedString( labels[ColIdx])
        $("[data-name='"+cols[ColIdx]+"']").attr("I18n",labels[ColIdx]);

      }
      RankingFt.columns.array[index].visible = ColIdx>-1;
      
    }
  }

  // use localization to change titles. Hummz creative but title does not seem to update the column header.
  LocalizeItem($("[I18n][data-name]").get());

}

function RnkIsArrived(rnk)
{
  if (typeof rnk === "undefined" || typeof rnk.status === "undefined" || !rnk.status)
  {
    return false;
  }
  return BoatArrivedStatus.indexOf(rnk.status ) !== -1;
}

function RnkIsRacing(rnk)
{
  if (typeof rnk === "undefined" || typeof rnk.status === "undefined" || !rnk.status)
  {
    return false;
  }
  return BoatRacingStatus.indexOf(rnk.status ) !== -1;
}

function Sort2ArrivedBoats(rnk1,rnk2)
{
  let Total1 = parseInt(rnk1.duration,10)+parseInt(rnk1.penalty,10);
  let Total2 = parseInt(rnk2.duration,10)+parseInt(rnk2.penalty,10);
  
  if (Total1 > Total2)
  {
    DebugRacerSort(rnk1,rnk2,1)
    return 1; 
  }
  else if (Total1 < Total2)
  {
    DebugRacerSort(rnk1,rnk2,-1)
    return -1;
  }
  else
  {
    DebugRacerSort(rnk1,rnk2,0)
    return 0;
  }
}

function Sort2RacingBoats(rnk1,rnk2)
{
  let nwp1 = parseInt(rnk1.nwp,10);
  let nwp2 = parseInt(rnk2.nwp,10);
  
  if (nwp1 === nwp2)
  {
    let dnm1 = parseFloat(rnk1.dnm);
    let dnm2 = parseFloat(rnk2.dnm);
    
    if (dnm1 > dnm2)
    {
      DebugRacerSort(rnk1,rnk2,1)
      return 1;
    }
    else if (dnm1 === dnm2)
    {
      DebugRacerSort(rnk1,rnk2,0)
      return 0;
    }
    else
    {
      DebugRacerSort(rnk1,rnk2,-1)
      return -1;
    }

  }
  else if (nwp1 > nwp2)
  {
    DebugRacerSort(rnk1,rnk2,-1)
    return -1; 
  }
  else
  {
    DebugRacerSort(rnk1,rnk2,1)
    return 1
  }

  // Should not happen
  DebugRacerSort(rnk1,rnk2,0)
  return 0;
}

function GetWPDuration(Rnk, WPNum)
{
  if (Rnk && Rnk["WP"] && Rnk["WP"][WPNum-1] && Rnk["WP"][WPNum-1].duration )
  {
    return parseInt(Rnk["WP"][WPNum-1].duration,10);
  }
  else
  {
    return 9999999999;
  }
}

function WPRaceSort(index)
{
  return function(a,b)
    {
      let wp1 = GetWPDuration(a,index);
      let wp2 = GetWPDuration(b,index);
      
      return wp1 - wp2;
    }
}

function RacersSort(rnk1, rnk2)
{

  if (RnkIsRacing(rnk1) && RnkIsRacing(rnk2))
  {
    return Sort2RacingBoats(rnk1, rnk2)
  }
  else if (RnkIsArrived(rnk1) && RnkIsArrived(rnk2))
  {
    return Sort2ArrivedBoats(rnk1,rnk2) 
  }
  else if (RnkIsArrived(rnk1))
  {
    DebugRacerSort(rnk1,rnk2,-1)
    return -1
  }
  else if (RnkIsArrived(rnk2) )
  {
    DebugRacerSort(rnk1,rnk2,1)
    return 1 
  }
  else if (RnkIsRacing(rnk1))
  {
    DebugRacerSort(rnk1,rnk2,1)
    return -1 
  }
  else if (RnkIsRacing(rnk2))
  {
    DebugRacerSort(rnk1,rnk2,1)
    return 1 
  }
  else
  {
    return Sort2NonRacing(rnk1,rnk2);
  }
}

let DebugCount = 1;
function DebugRacerSort(rnk1,rnk2,res)
{
  let debug= false;

  if (debug)
  {
    console.log((DebugCount++)+ "sort " + rnk1.idusers + " vs " + rnk2.idusers + " =>" + res);
  }
  
}

function Sort2NonRacing(rnk1,rnk2)
{

  if (typeof rnk1.idusers !== "undefined" && typeof rnk2.idusers !== "undefined")
  {
    let IdUser1 = parseInt(rnk1.idusers,10)
    let IdUser2 = parseInt(rnk2.idusers,10)
    
    if ( IdUser1> IdUser2)
    {
      DebugRacerSort(rnk1,rnk2,1)
      return 1 ;
    }
    else if (IdUser1 < IdUser2)
    {
      DebugRacerSort(rnk1,rnk2,-1)
      return -1;
    }
    else
    {
      DebugRacerSort(rnk1,rnk2,0)
      return 0;
    }
  }
  else if (typeof IdUser1 !== "undefined")
  {
    return -1;
  }
  else if (typeof IdUser2 !== "undefined")
  {
    return -1;
  }
  else
  {  
    let ar = [rnk1,rnk2];
    ar.sort()

    if (ar[0]=== rnk1)
    {
      return 1
    }
    else
    {
      return -1
    }
  }
}

function SortRankingData(Boat, SortType,WPNum)
{
  
  if (typeof Boat.RnkObject.RacerRanking === "undefined")
  {
    let index ;

    Boat.RnkObject.RacerRanking=[];
    
    for (index in Boat.RnkObject)
    {
      if (Boat.RnkObject[index] && !isNaN(parseInt(index,10)))
      {
        Boat.RnkObject[index].idusers=index;
        Boat.RnkObject.RacerRanking.push(Boat.RnkObject[index]);
      }
    }
  }

  switch (SortType)
  {
    case "WP":
      Boat.RnkObject.RacerRanking.sort(WPRaceSort(WPNum));
      break;
      
    case 'RAC':
    case 'DNF':
    case 'HC':
    case 'HTP':
    case 'ABD':
    case 'ARR':
    
      Boat.RnkObject.RacerRanking.sort(RacersSort);
      break;

    default:
      VLMAlertInfo("unexpected sort option : "+SortType);
    
  }
  
  let =rnk = 1;
  let index = 0;

  for (index in Boat.RnkObject.RacerRanking)
  {
    if (Boat.RnkObject.RacerRanking[index] && Boat.IdBoat === index)
      {
        rnk  = index +1;
        break;
      }

  }

  return rnk;
}

function FillWPRanking(Boat,WPNum)
{
  let index;
  let RowNum = 1;
  let BestTime = 0;
  let Rows = [];

  if (!Boat || RankingFt.DrawPending)
  {
    return;
  }

  for (index in Boat.RnkObject.RacerRanking)
  {
    if (Boat.RnkObject.RacerRanking[index])
    {    
      let RnkBoat = Boat.RnkObject.RacerRanking[index];

      if (RnkBoat.WP && RnkBoat.WP[WPNum -1] && !RnkBoat.WP[WPNum -1].Delta)
      {
        if (!BestTime)
        {
          BestTime = RnkBoat.WP[WPNum -1].duration;
          RnkBoat.WP[WPNum -1].Delta = 0;
          RnkBoat.WP[WPNum -1].Pct = 0;
        }
        else
        {
          RnkBoat.WP[WPNum -1].Delta = RnkBoat.WP[WPNum -1].duration - BestTime;
          RnkBoat.WP[WPNum -1].Pct = 100 *(RnkBoat.WP[WPNum -1].duration / BestTime - 1);
        }
      }

      if (RnkBoat.WP && RnkBoat.WP[WPNum-1])
      {
        Rows.push(GetRankingObject(RnkBoat,parseInt(index,10)+1,WPNum));
        if (Boat.IdBoat === parseInt(RnkBoat.idusers,10))
        {
          RowNum = Rows.length;
        }
      }
    }
  }

  let TargetPage = RoundPow(RowNum / 20,0) + (RowNum%20 >= 10?0:1);
  RankingFt.loadRows(Rows);
  RankingFt.TargetPage = TargetPage;
  RankingFt.DrawPending = true;
}

function FillStatusRanking(Boat,Status)
{
  let index;
  let RowNum = 1;
  let Rows = [];
  for (index in Boat.RnkObject.RacerRanking)
  {
    if (Boat.RnkObject.RacerRanking[index])
    {    
      let RnkBoat = Boat.RnkObject.RacerRanking[index];
      
      if (RnkBoat.status === Status)
      {
        Rows.push(GetRankingObject(RnkBoat,parseInt(index,10)+1,null));
        if (Boat.IdBoat === parseInt(RnkBoat.idusers,10))
        {
          RowNum = Rows.length;
        }
      }
    }
  }

  let TargetPage = RoundPow(RowNum / 20,0) + (RowNum%20 >= 10?0:1);
  RankingFt.loadRows(Rows);
  RankingFt.TargetPage = TargetPage;
  RankingFt.DrawPending = true;
}

function FillRacingRanking(Boat)
{
  let index;
  let Rows = [];

  for (index in Boat.RnkObject.RacerRanking)
  {
    if (Boat.RnkObject.RacerRanking[index])
    {    
      let RnkBoat = Boat.RnkObject.RacerRanking[index];

      if (Boat.IdBoat === parseInt(RnkBoat.idusers,10))
      {
        RowNum = Rows.length;
      }
      
      if (RnkIsArrived(RnkBoat) || RnkIsRacing(RnkBoat))
      {
        Rows.push(GetRankingObject(RnkBoat,parseInt(index,10)+1,null));
      }
      else
      {
        break;
      }
    }
  }
  
  let TargetPage = RoundPow(RowNum / 20,0) + (RowNum%20 >= 10?0:1);
  RankingFt.loadRows(Rows);
  RankingFt.TargetPage = TargetPage;
  RankingFt.DrawPending = true;
}

function GetBoatInfoLink(IdUser,BoatName)
{

  IdUser = parseInt(IdUser,10);
  let ret = '<a href="/palmares.php?type=user&idusers='+IdUser+'" target ="_'+IdUser +'">'+BoatName+'</a>';

  return ret;
}

function GetRankingObject(RankBoat, rank, WPNum)
{
  let boatsearchstring = ''//'<img class="BoatFinder" src="images/search.png" id=RnkUsr"'+RankBoat.idusers+'"></img>   '
  boatsearchstring+=  GetBoatInfoLink(RankBoat['idusers'],RankBoat['boatpseudo'])
  let RetObject = {
    Rank : rank,
    Name :boatsearchstring,
    Distance : "",
    Time : "",
    Loch : "",
    Lon : "",
    Lat : "",
    Last1h : "",
    Last3h : "",
    Last24h : ""
  };
  
  if (RnkIsRacing(RankBoat) && !WPNum)
  {
    // General ranking layout
    let NextMark = '['+RankBoat['nwp'] +'] -=> '+ RoundPow(RankBoat['dnm'],2)
    RetObject["Distance"]=NextMark
    let RacingTime = Math.round((new Date() - new Date(parseInt(RankBoat['deptime'],10)*1000))/1000);
    RetObject["Time"]= (RankBoat['deptime']==="-1"?"": GetFormattedChronoString(RacingTime));
    RetObject["Loch"]= RankBoat['loch']
    RetObject["Lon"]= RankBoat['longitude']
    RetObject["Lat"]= RankBoat['latitude']
    RetObject["Last1h"]=RankBoat['last1h']
    RetObject["Last3h"]= RankBoat['last3h']
    RetObject["Last24h"]= RankBoat['last24h']
  }
  else if (!WPNum)
  {
    // Non General ranking layout
    let NextMark = GetLocalizedString("status_" + RankBoat['status'])
    RetObject["Distance"]=NextMark
    RetObject["Time"]=GetFormattedChronoString(parseInt(RankBoat['duration'],10));
    RetObject["Loch"]=RankBoat['loch']
    RetObject["Lon"]= RankBoat['longitude']
    RetObject["Lat"]= RankBoat['latitude']
  }
  else
  {
    RetObject["Time"] = GetFormattedChronoString(parseInt(RankBoat.WP[WPNum-1].duration,10));
    let DeltaStr
    if (RankBoat.WP[WPNum-1].Delta)
    {
      let PctString = RoundPow(RankBoat.WP[WPNum-1].Pct,2);
      DeltaStr = GetFormattedChronoString(RankBoat.WP[WPNum-1].Delta) + " (+"+PctString + " %)"
    }
    else
    {
      DeltaStr = GetLocalizedString("winner")
    }
    RetObject["Loch"] = DeltaStr
  }


  return RetObject
}


function HandleShowMapPrefs(e)
{
  //Load prefs
  $("#DisplayReals").attr('checked',VLM2Prefs.MapPrefs.ShowReals);
  $("#DisplayNames").attr('checked',VLM2Prefs.MapPrefs.ShowOppName);
  $("#EstTrackMouse").attr('checked',VLM2Prefs.MapPrefs.EstTrackMouse);
  $("#TrackEstForecast").attr('checked',VLM2Prefs.MapPrefs.TrackEstForecast);

  $('#DDMapSelOption:first-child').html(
  '<span Mode='+ VLM2Prefs.MapPrefs.MapOppShow +'>'+VLM2Prefs.MapPrefs.GetOppModeString(VLM2Prefs.MapPrefs.MapOppShow)+'</span>'+
  '<span class="caret"></span>'
  );

  $("#VacPol").val(VLM2Prefs.MapPrefs.PolarVacCount);

}

function HandleMapPrefOptionChange(e)
{
  var target=e.target;

  if (typeof target === "undefined" || typeof target.attributes['id'] === "undefined")
  {
    return;
  }

  var Id = target.attributes['id'].value;
  var Value =target.checked;
  
  switch (Id)
  {
    case "DisplayReals":
      VLM2Prefs.MapPrefs.ShowReals = Value
      break;
    case "DisplayNames":
      VLM2Prefs.MapPrefs.ShowOppName = Value
      break;

    case "EstTrackMouse":
    case "TrackEstForecast":
      VLM2Prefs.MapPrefs[Id] = Value
      break;
    
    case "VacPol":
      var VacPol = parseInt($("#VacPol").val(),10);

      if (VacPol > 0 && VacPol < 120)
      {
        VLM2Prefs.MapPrefs.PolarVacCount = VacPol;
      }
      else
      {
        $("#VacPol").value(12);
      }
      break;
      
    default:
      return;
      
  }

  VLM2Prefs.Save();
  RefreshCurrentBoat(false,false);
}

function SafeHTMLColor(Color)
{
    Color = "" + Color;

    if (Color.length < 6)
    {
      Color = ("000000"+Color).slice(-6);
    }

    if (Color.substring(0,1) !== "#")
    {
      Color = "#" + Color
    }
    else if (Color.substring(1,2) === "#")
    {
      Color = Color.substring(1);
    }

    return Color;
}

function HandleMapOppModeChange(e)
{
  var t = e.target;
  var Mode = parseInt(t.attributes["Mode"].value,10);

  VLM2Prefs.MapPrefs.MapOppShow=Mode;
  VLM2Prefs.Save();
  HandleShowMapPrefs(e);

}

function SetActiveStyleSheet(title) 
{
  var i, a, main;
  for(i=0; (a = document.getElementsByTagName("link")[i]); i++) 
  {
    if((a.getAttribute("rel").indexOf("style") !== -1) && a.getAttribute("title")) 
    {
      a.disabled = true;
      if(a.getAttribute("title") === title)
      {
        a.disabled = false;
      } 
    }
  }
}

function SetDDTheme(Theme)
{
  SetActiveStyleSheet(Theme);
  $("#SelectionThemeDropDown:first-child").html(Theme+'<span class="caret"></span>');
  $("#SelectionThemeDropDown").attr("SelTheme",Theme);
}

function HandleDDlineClick(e)
{
  var Target = e.target;
  //var Theme = Target.closest(".DDTheme").attributes["DDTheme"].value;
  var Theme = e.target.attributes['ddtheme'].value;

  SetDDTheme(Theme);
}

var AlertTemplate;
function InitAlerts()
{
  // Init default alertbox
  $("#AlertBox").css("display","block")
  AlertTemplate = $("#AlertBox")[0];
  $("#AlertBoxContainer").empty();
  $("#AlertBoxContainer").removeClass("hidden");
  
}

function VLMAlertSuccess(Text)
{
  VLMAlert(Text,"alert-success");
}

function VLMAlertDanger(Text)
{
  VLMAlert(Text,"alert-danger");
}

function VLMAlertInfo(Text)
{
  VLMAlert(Text,"alert-info");
}

let AlertIntervalId = null;

function VLMAlert(Text,Style)
{
  if (AlertIntervalId)
  {
    clearInterval(AlertIntervalId);
  } 

  if (typeof Style === "undefined" || !Style)
  {
    Style="alert-info";
  }

  $("#AlertBoxContainer").empty().append(AlertTemplate).show();

  $("#AlertText").text(Text);
  $("#AlertBox").removeClass("alert-sucess");
  $("#AlertBox").removeClass("alert-warning");
  $("#AlertBox").removeClass("alert-info");
  $("#AlertBox").removeClass("alert-danger");
  $("#AlertBox").addClass(Style);
  $("#AlertBox").show();
  $("#AlertCloseBox").unbind().on('click',AutoCloseVLMAlert)

  if (AlertIntervalId)
  {
    clearInterval(AlertIntervalId);
  }  
  AlertIntervalId = setTimeout(AutoCloseVLMAlert,5000); 
}

function AutoCloseVLMAlert()
{
  $("#AlertBox").hide();
}

function GetUserConfirmation(Question,IsYesNo,CallBack)
{
  $("#ConfirmDialog").modal('show');
  if (IsYesNo)
  {
    $("#OKBtn").hide();
    $("#CancelBtn").hide();
    $("#YesBtn").show();
    $("#NoBtn").show();
  }
  else
  {
    $("#OKBtn").show();
    $("#CancelBtn").show();
    $("#YesBtn").hide();
    $("#NoBtn").hide();    
  }
  $("#ConfirmText").text(Question);
  $(".OKBtn").unbind().on("click",function(){$("#ConfirmDialog").modal('hide');CallBack(true)});
  $(".NOKBtn").unbind().on("click",function(){$("#ConfirmDialog").modal('hide');CallBack(false)});

}