
//
//
// Some consts 
var RACE_TYPE_CLASSIC = 0;
var RACE_TYPE_RECORD = 1;
var RACE_TYPE_OMORMB = 2;

// On ready get started with vlm management
$(document).ready(
  function(){
    
    // Init maps
    OLInit();
    
    // Load translation strings
    InitLocale();
    
    // Init Menus()
    InitMenusAndButtons();
    
    // Start-Up Polars manager
    PolarsManager.Init();
    
    

    // Go To WP OrthoMode
    $("#BtnPM_Ortho, #BtnPM_VMG, #BtnPM_VBVMG").click(
      function()
      {
        var WpH=-1;
        var PMMode=PM_ORTHO;
        var Lat = $("#PM_Lat")[0].value;
        var Lon = $("#PM_Lon")[0].value;
        

        if ($("#PM_WithWPHeading")[0].value)
        {
          Wph = $("#PM_WPHeading").value
        }

        switch ($(this)[0].id)
        {
          case "BtnPM_Ortho":
            PMMode=PM_ORTHO;
            break;

          case "BtnPM_VMG":
            PMMode=PM_VMG;
            break;

          case "VBVMGMode":
            PMMode=PM_VBVMG;
            break;

        }
        SendVLMBoatOrder(PMMode, Lon,Lat,WpH);
      }
    )
    
    $("#logindlgButton").on ('click',
          function (e)
          {
            // Show Login form
            $("#LoginForm").modal('show');
          }
    );

    $("#logOutButton").on ('click',
          function (e)
          {
            // Logout user
            Logout();
          }
    );

    
    // Set BoatSelector as JQuery UI Selector 
    // Handle boat selector selection change
    //
    $("#BoatSelector").selectmenu();  
    $("#BoatSelector").on( "selectmenuselect", function(event,ui)
      {
        SetCurrentBoat(GetBoatFromIdu(ui.item.value),true);
      }
    );

    // Remove JQuery/bootstrap conflict $("#FlagSelector").selectmenu();  
    
    $('#cp11').colorpicker();
     
    // CheckLogin
    CheckLogin();

    // Start the page clocks
    setInterval('PageClock()',1000);
    
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

    
     
    //Le calque de vent made in Vlm
    var grib = new Gribmap.Layer("Gribmap", layeroption);
    //grib.setOpacity(0.9); //FIXME: faut il garder une transparence du vent ?

    //La minimap utilise le layer VLM
    var vlmoverview = vlm.clone();

    //Et on ajoute tous les layers à la map.
    //map.addLayers([ VLMBoatsLayer,vlm, wms, bingroad, bingaerial, binghybrid, gphy, ghyb, gsat, grib]);
    map.addLayers([ VLMBoatsLayer, VLMDragLayer,vlm, grib]);
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


function InitMenusAndButtons()
{
  $( "#Menu" ).menu();
  $( "#Menu" ).hide();
  
  $( "input[type=submit],button" )
      .button()
      .click(function( event ) 
        {
          event.preventDefault();
        }
      );
  
  
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

  // Handle clicking on ICS button
  $("#ICSButton").click(
    function()
    {
      var win = window.open("/ics.php?idraces="+_CurPlayer.CurBoat.VLMInfo.RAC)
      win.focus();
    }
  )

  // Handle clicking on ranking button
  $("#RankingButton").click(
    function()
    {
      var win = window.open("/races.php?type=racing&idraces="+ _CurPlayer.CurBoat.VLMInfo.RAC+ "&startnum="+_CurPlayer.CurBoat.VLMInfo.RNK)
      win.focus();
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

    // Display setting dialog
    $("#BtnSetting").click(
      function()
      {
        $("#SettingsForm").modal("show");
      }
    )

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
        alert("Function not implemented yet");
      }
    )

}

function ClearBoatSelector()
{
  $("#BoatSelector").empty();
}

function AddBoatToSelector(boat, isfleet)
{
  var boatclass='';
  if (boat.Engaged && isfleet)
  {
    boatclass = 'RacingBoat';
  }
  else if (boat.Engaged)
  {
    boatclass = 'RacingBSBoat';
  }
  else if (isfleet)
  {
    boatclass = 'Boat';
  }
  else
  {
    boatclass = 'BSBoat';
  }
  
  $("#BoatSelector").append($('<option />',
                                { 
                                  value: boat.IdBoat,
                                  text: boat.BoatName,
                                }
                              )
                            )
                            
  $("option[value="+ boat.IdBoat +"]").toggleClass(false).addClass(boatclass);
}

function   ShowUserBoatSelector()
{
  //$("#BoatSelector").show();
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
  LocalizeString();
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
  $("ul[LoggedInNav='true']").css("display",LoggedInDisplay);
  $("ul[LoggedInNav='false']").css("display",LoggedOutDisplay);
  
  //$("#BoatSelector").selectmenu("refresh");

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
  HandleRacingDockingButtons(false);

}

function UpdateInMenuRacingBoatInfo(Boat)
{
  var NorthSouth;
  var EastWest;

  HandleRacingDockingButtons(true);
  // Put a sign to the TWA
  if (Boat.VLMInfo.TWD+360 < parseInt(Boat.VLMInfo.HDG)+360)
  {
    Boat.VLMInfo.TWA = -Boat.VLMInfo.TWA;
  }
  
  // Update GUI for current player
  if (Boat.VLMInfo.LON >=0)
  {
    EastWest = "E";
  }
  else
  {
    EastWest = "W";
  }
  if (Boat.VLMInfo.LAT >=0)
  {
    NorthSouth = "N";
  }
  else
  {
    NorthSouth = "S";
  }
 
  var lon = new Coords(Boat.VLMInfo.LON);
  var lat = new Coords(Boat.VLMInfo.LAT);

  // Create field mapping array
  // 0 for text fields
  // 1 for input fields
  var BoatFieldMappings=[];
  BoatFieldMappings.push([0,"#BoatLon",lon.ToString() + ' ' + EastWest]);
  BoatFieldMappings.push([0,"#BoatLat",lat.ToString() + ' ' + NorthSouth]);
  BoatFieldMappings.push([0,"#BoatSpeed",Math.round(Boat.VLMInfo.BSP * 10)/10]);
  BoatFieldMappings.push([0,"#BoatHeading",Math.round(Boat.VLMInfo.HDG * 10)/10]);
  BoatFieldMappings.push([1,"#PM_Heading",Math.round(Boat.VLMInfo.HDG * 10)/10]);
  BoatFieldMappings.push([0,"#BoatAvg",Math.round(Boat.VLMInfo.AVG * 10)/10 ]);
  BoatFieldMappings.push([0,"#BoatDNM",Math.round(Boat.VLMInfo.DNM * 10)/10 ]);
  BoatFieldMappings.push([0,"#BoatLoch",Math.round(Boat.VLMInfo.LOC * 10)/10 ]);
  BoatFieldMappings.push([0,"#BoatOrtho",Math.round(Boat.VLMInfo.ORT * 10)/10 ]);
  BoatFieldMappings.push([0,"#BoatLoxo",Math.round(Boat.VLMInfo.LOX * 10)/10 ]);
  BoatFieldMappings.push([0,"#BoatVMG",Math.round(Boat.VLMInfo.VMG * 10)/10 ]);
  BoatFieldMappings.push([0,"#BoatWindSpeed",Math.round(Boat.VLMInfo.TWS * 10)/10 ]);
  BoatFieldMappings.push([0,"#BoatWindDirection",Math.round(Boat.VLMInfo.TWD * 10)/10 ]);
  BoatFieldMappings.push([0,"#BoatWindAngle",Math.round(Math.abs(Boat.VLMInfo.TWA) * 10)/10 ]);
  WP = new VLMPosition(Boat.VLMInfo.WPLON,Boat.VLMInfo.WPLAT);
  if ((WP.Lon.Value)==0 && (WP.Lat.Value==0))
  {
    WP = Boat.GetNextWPPosition();
  }
  BoatFieldMappings.push([1,"#PM_Lat", WP.Lat.Value]);
  BoatFieldMappings.push([1,"#PM_Lon", WP.Lon.Value]);
  BoatFieldMappings.push([0,"#PM_CurWPLat", WP.Lat.ToString()]);
  BoatFieldMappings.push([0,"#PM_CurWPLon", WP.Lon.ToString()]);
  BoatFieldMappings.push([0,"#RankingBadge", Boat.VLMInfo.RNK]);
  //BoatFieldMappings.push([0,"#BoatWindAngle",Math.round(Boat.VLMInfo.H@WP * 10)/10 ]);
  
  if (Boat.VLMInfo.PIM==PM_ANGLE)
  {
    BoatFieldMappings.push([1,"#PM_Angle",Boat.VLMInfo.PIP ]);
  }
  else
  {
    BoatFieldMappings.push([1,"#PM_Angle",Math.round(Boat.VLMInfo.TWA * 10)/10 ]);
  }

  // Loop all mapped fields to their respective location
  for (index in BoatFieldMappings)
  {
    switch (BoatFieldMappings[index][0])
    {
      case 0:
        $(BoatFieldMappings[index][1]).text(BoatFieldMappings[index][2]);
        break;

      case 1:
        $(BoatFieldMappings[index][1]).val(BoatFieldMappings[index][2]);
        break;
    }
  }
 
  // Change color depênding on windangle
  var WindColor="lime"
  if (Boat.VLMInfo.TWA >0)
  {
    WindColor="red"
  }
  $("#BoatWindAngle").css("color",WindColor);

  // Get WindAngleImage
  var wHeading=Math.round(Boat.VLMInfo.TWD * 100)/100;
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
      alert("Unsupported VLM PIM Mode, expect the unexpected....")
      
   }

    $(TabID).css("display","inline");
    $("."+ActivePane).addClass("active");
    $("#"+ActivePane).addClass("active");

    // Add race name
    $("#RaceName").text(Boat.RaceInfo.racename);

} 

function MoveWPBoatControlerDiv(target)
{
  var div = $(target).prepend($("#PM_WPMode_Div"));
}

function UpdatePrefsDialog(Boat)
{
  // Hide prefs setting button is not boat or no vlminfo yet...
  if (typeof Boat === "undefined" || typeof Boat.VLMInfo.AVG === "undefined")
  {
    $("#BtnSetting").addClass("hidden");
  }
  else
  {
    $("#BtnSetting").removeClass("hidden");
  }

  $("#pref_boatname").val(Boat.BoatName);
  $("#FlagSelector option[value='"+Boat.VLMInfo.CNT+"']").prop('selected', true);
  $("#pref_boatcolor").val("#"+Boat.VLMInfo.COL);
  

}

function LoadRacesList()
{
  $.get("/ws/raceinfo/list.php",
    function (result)
    {
      var racelist= result;

      // Clear previous elements
      $("#RaceListPanel").empty();
  
      for (index in racelist)
      {
        AddRaceToList(racelist[index]);
      }
    }
  )
}

function AddRaceToList(race)
{
  var base = $("#RaceListPanel").first();

  
  var d = new Date(0); // The there is the key, which sets the date to the epoch
  //d.setUTCSeconds(utcSeconds);

  var code = '<div class="raceheaderline panel panel-default")>' +
             '  <div class="panel panel-body">'+
             '    <div class="col-xs-2">'+
             '      <img class="racelistminimap" src="/cache/minimaps/'+race.idraces+'.png" ></img>'+
             '    </div>'+
             '    <div class="col-xs-5">'+
             '      <span>'+ race.racename +
             '      </span>'+
             '    </div>'+
             '    <div class="col-xs-3">'+
             '      <button id="JoinRaceButton" type="button" class="btn btn-default" IdRace="'+ race.idraces +'"  >'+GetLocalizedString("subscribe")+
             '      </button>'+
             '    </div>'
             '  </div>'+
             ' </div>'

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

    // Display race clock is a racing boat is selected
    var CurBoat = _CurPlayer.CurBoat;

    if (typeof CurBoat != "undefined" && typeof CurBoat.RaceInfo != "undefined")
    {
      var ClockValue=GetRaceClock(CurBoat.RaceInfo, CurBoat.VLMInfo.UDT);
      var Chrono = $("#RaceChrono");
      if (ClockValue < 0 )
      {
        Chrono.removeClass("ChronoRaceStarted").addClass("ChronoRacePending");
      }
      else
      {
        Chrono.addClass("ChronoRaceStarted").removeClass("ChronoRacePending");
      }

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
    // non Permanent  race chrono counts from race start time
    return Math.floor((CurDate-Epoch)/1000);
  }
  else
  {
    var UDT = parseInt(UserStartTimeString);

    if (UDT == -1)
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

function GetFormattedChronoString(Value)
{
  if (Value < 0)
  {
    Value = -Value;
  }
  else if (Value == 0)
  {
    return "--:--:--";
  }

  var Sec = Value % 60;
  var Min = Math.floor(Value/60) % 60;
  var Hrs = Math.floor(Value/3600 ) % 24;
  var Days = Math.floor(Value / 3600 / 24 );

  var Ret = Hrs.toString() + ":" + Min.toString() +":"+Sec.toString(); 
  if (Days > 0)
  {
    Ret = Days.toString()+" d "+Ret;
  }
  return Ret;
}

function RefreshCurrentBoat(SetCenterOnBoat)
{
  SetCurrentBoat(GetBoatFromIdu($("#BoatSelector").val()), SetCenterOnBoat)
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