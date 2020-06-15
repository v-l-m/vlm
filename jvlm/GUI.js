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
var FIELD_MAPPING_STYLE = 5;
var FIELD_MAPPING_POPUP = 6;

var MAX_PILOT_ORDERS = 5;

var BoatRacingStatus = ["RAC", "CST", "LOC", "DNS"];
var BoatArrivedStatus = ["ARR"];
var BoatNotRacingStatus = ["DNF", "HC", "HTP"];
var BoatRacingClasses = {
  "RAC": "ft_class_racing",
  "CST": "ft_class_oncoast",
  "LOC": "ft_class_locked",
  "DNS": "ft_class_dns"
};

// Globals (beurk).
var SetWPPending = false;
var WPPendingTarget = null;
var GribWindController = null;

//Global map object
var map = null;
var VLMBoatsLayer = null;

// Ranking related globals
var Rankings = [];
var PilototoFt = null;
var RankingFt = null;
var RaceHistFt = null;
var ICS_WPft = null;
var NSZ_WPft = null;
var VLMINdexFt = null;

var RC_PwdResetReq = null;
var RC_PwdResetConfirm = null;
var OnPlayerLoadedCallBack = null;

// FindIndex for IE


// On ready get started with vlm management
$(document).ready(
  function()
  {

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
    $.ajaxSetup(
    {
      error: function(x, status, error)
      {
        if ((x.status === 401) || (x.status === 403) || (x.status === "error"))
        {
          window.location.replace("/");
          //on access denied try reviving the session
          //OnLoginRequest();
        }
        else if (x.status === 404)
        {
          // Code removed until ranking exist for not started races.
          $("#ErrorRedirectPanel").modal('show');
        }
        else
        {
          VLMAlertDanger("An error occurred: " + status + "nError: " + error);
        }
      }
    });

    // Start converse
    //InitXmpp();

    // Load translation strings
    InitLocale();

    // Init Menus()
    InitMenusAndButtons();

    // Start-Up Polars manager
    PolarsManager.Init();

    // Init Alerts
    InitAlerts();
    // Handle page parameters if any
    let NoMap = CheckPageParameters();

    // Start the page clocks
    setInterval(PageClock, 1000);

    // Init maps
    if (!NoMap)
    {
      CheckLogin();

      LeafletInit();
      // Async init of weathermap to current map
      setTimeout(function()
      { // Wind Layer
        map.GribMap = new GribMap.Layer().addTo(map);
      }, 500);
    }

    // Load flags list (keep at the end since it takes a lot of time)
    GetFlagsList();
  }
);

const COMPASS_SIZE = 350;

function LeafletInit()
{
  //Init map object
  map = L.map('jVlmMap' /*,{preferCanvas:true}*/ ).setView([0, 0], 8);

  // Tiles
  let src = tileUrlSrv;
  L.tileLayer(src,
  {
    attribution: 'gshhsv2',
    maxZoom: 20,
    tms: false,
    id: 'vlm',
    detectRetina: true,
    subdomains: tilesUrlArray,

  }).addTo(map);


  // Wind & Mouse Pos Control
  map.WMControl = L.control.WindMouseControl().addTo(map);

  // Draggable compass
  DrawCompass();


  // Map Events
  map.on('mousemove', HandleMapMouseMove);
  map.on('moveend', HandleMapGridZoom);
  map.on('click', HandleMapMouseClick);
  map.on("zoomend", HandleMapGridZoom);
}

function DrawCompass()
{
  if (VLM2Prefs.MapPrefs.ShowCompass)
  {
    if (map.Compass)
    {
      map.Compass.addTo(map);
      map.Compass.on("dragend", HandleCompassDragEnd);
      map.Compass.on("mousemove", HandleCompassMouseMove);
      map.Compass.on("mouseout", HandleCompassMouseOut);
    }
    else
    {
      map.Compass = new L.marker([0, 0],
      {
        icon: new L.icon(
        {
          iconSize: [350, 341],
          iconAnchor: [175, 170],
          iconUrl: 'images/compas-transparent.gif',
        }),
        draggable: true,
        zIndexOffset: -1000
      }).addTo(map);
      map.Compass.on("dragend", HandleCompassDragEnd);
      map.Compass.on("mousemove", HandleCompassMouseMove);
      map.Compass.on("mouseout", HandleCompassMouseOut);
    }
  }
  else if (map.Compass)
  {
    map.Compass.off("dragend", HandleCompassDragEnd);
    map.Compass.off("mousemove", HandleCompassMouseMove);
    map.Compass.off("mouseout", HandleCompassMouseOut);
    map.Compass.remove();
  }

}

function HandleCompassMouseOut(e)
{
  map.Compass.dragging.enable();
}

function HandleCompassMouseMove(e)
{
  let z = map.getZoom();
  let p = map.project(map.Compass.getLatLng(), z);
  let m = map.project(map.mouseEventToLatLng(e.originalEvent), z);
  let dx = p.x - m.x;
  let dy = p.y - m.y;

  if (((dx * dx) + (dy * dy)) < COMPASS_SIZE * COMPASS_SIZE / 8)
  {
    map.Compass.dragging.disable();
    //console.log ( " " + dx + " " + dy + " disabled " + ((dx*dx)+(dy*dy)) + " < "+ 0.81*COMPASS_SIZE * COMPASS_SIZE/4);
  }
  else
  {
    map.Compass.dragging.enable();
    //console.log ( " " + dx + " " + dy + " enabled" );
  }

}

function HandleCompassDragEnd(e)
{
  if (_CurPlayer && _CurPlayer.CurBoat && _CurPlayer.CurBoat.VLMInfo.LAT && _CurPlayer.CurBoat.VLMInfo.LON)
  {
    let Boat = _CurPlayer.CurBoat;
    let B = [_CurPlayer.CurBoat.VLMInfo.LAT, _CurPlayer.CurBoat.VLMInfo.LON];
    let C = map.Compass.getLatLng();

    let Features = GetRaceMapFeatures(Boat);
    if (!Features.Compass)
    {
      Features.Compass = {};
    }

    let z = map.getZoom();
    let P1 = map.project(B, z);
    let P2 = map.project(C, z);
    if ((Math.abs(P1.x - P2.x) < BOAT_MARKET_SIZE / 2) && (Math.abs(P1.y - P2.y) < BOAT_MARKET_SIZE / 2))
    {

      Features.Compass.Lat = -1;
      Features.Compass.Lon = -1;
    }
    else
    {
      Features.Compass.Lat = C.lat;
      Features.Compass.Lon = C.lng;
    }

  }
}

function HandleMapGridZoom(e)
{
  let m = e.sourceTarget;
  let z = m.getZoom();
  let b = m.getBounds();

  if (_CurPlayer.CurBoat && _CurPlayer.CurBoat.IdBoat && _CurPlayer.CurBoat.IdBoat())
  {
    VLM2Prefs.SetLastZoom(_CurPlayer.CurBoat.IdBoat(), z);
  }

  let DX = b._northEast.lng - b._southWest.lng;
  let DY = b._northEast.lat - b._southWest.lat;
  let S = DX;
  if (DY < DX)
  {
    S = DY;
  }
  S = Math.pow(0.25, Math.ceil(Math.log(S) / Math.log(0.25)));

  if (S > 5)
  {
    S = Math.pow(5, Math.floor(Math.log(S) / Math.log(5)));
  }
  else if (S < 0.25)
  {
    S = 0.25;
  }

  if (typeof m.GridLayer == "undefined")
  {
    m.Grid = [];
    m.GridLayer = L.layerGroup().addTo(m);
  }
  else
  {
    m.GridLayer.clearLayers();
  }

  let GridLabelOpacity = 0.4;
  let GridLineStyle = {
    weight: 1,
    opacity: GridLabelOpacity,
    color: 'black'
  };
  let GridLabelStyle1 = {
    permanent: true,
    opacity: GridLabelOpacity,
    offset: [0, -10]
  };
  let GridLabelStyle2 = {
    permanent: true,
    opacity: GridLabelOpacity,
    offset: [0, 30]
  };
  let GridLabelStyle3 = {
    permanent: true,
    opacity: GridLabelOpacity,
    offset: [10, 0]
  };
  let GridLabelStyle4 = {
    permanent: true,
    opacity: GridLabelOpacity,
    offset: [-10, 0]
  };

  let index = 0;

  for (let x = Math.floor(b._southWest.lng); x <= b._northEast.lng; x += S)
  {
    let P = [
      [b._southWest.lat, x],
      [b._northEast.lat, x]
    ];

    m.Grid[index] = L.polyline(P, GridLineStyle);
    m.GridLayer.addLayer(m.Grid[index++]);
    let xlabel = RoundPow(4 * x, 0) / 4;

    m.Grid[index] = L.circleMarker(P[0],
    {
      radius: 1
    }).bindTooltip("" + xlabel, GridLabelStyle1);
    m.GridLayer.addLayer(m.Grid[index++]);
    m.Grid[index] = L.circleMarker(P[1],
    {
      radius: 1
    }).bindTooltip("" + xlabel, GridLabelStyle2);
    m.GridLayer.addLayer(m.Grid[index++]);

  }

  for (let y = Math.floor(b._southWest.lat); y <= b._northEast.lat; y += S)
  {
    let P = [
      [y, b._southWest.lng],
      [y, b._northEast.lng]
    ];
    let xlabel = RoundPow(4 * y, 0) / 4;

    m.Grid[index] = L.polyline(P, GridLineStyle);
    m.GridLayer.addLayer(m.Grid[index]);
    m.Grid[index] = L.circleMarker(P[0],
    {
      radius: 1
    }).bindTooltip("" + xlabel, GridLabelStyle3);
    m.GridLayer.addLayer(m.Grid[index++]);
    m.Grid[index] = L.circleMarker(P[1],
    {
      radius: 1
    }).bindTooltip("" + xlabel, GridLabelStyle4);
    m.GridLayer.addLayer(m.Grid[index++]);

  }
  //console.log("Zoom Level " + z);
}

let PasswordResetInfo = [];

function HandlePasswordResetLink(PwdKey)
{
  PasswordResetInfo = unescape(PwdKey).split("|");
  initrecaptcha(false, true);
  $("#ResetaPasswordConfirmation").modal("show");
}

function CheckPageParameters()
{
  let retstop = false;
  let url = window.location.search;
  let RacingBarMode = true;

  if (url)
  {
    let getQuery = url.split('?')[1];
    let params = getQuery.split('&');
    // params is ['param1=value', 'param2=value2'] 
    for (let param in params)
    {
      if (params[param])
      {
        let PArray = params[param].split("=");

        switch (PArray[0])
        {
          case "PwdResetKey":
            HandlePasswordResetLink(PArray[1]);
            break;

          case "RaceRank":
            RacingBarMode = false;
            /* jshint -W083*/
            RankingFt.OnReadyTable = function()
            {
              HandleShowOtherRaceRank(PArray[1]);
            };
            /* jshint +W083*/
            retstop = true;
            break;

          case "VLMIndex":
            RacingBarMode = false;
            /* jshint -W083*/
            VLMINdexFt.OnReadyTable = function()
            {
              HandleShowIndex(PArray[1]);
            };
            /* jshint +W083*/
            retstop = true;
            break;

          case "ICSRace":
            RacingBarMode = false;
            HandleShowICS(PArray[1]);
            retstop = true;
            break;
        }
      }
    }
  }

  if (!retstop)
  {
    if (RacingBarMode)
    {
      $(".RaceNavBar").css("display", "inherit");
      $(".OffRaceNavBar").css("display", "none");
    }
    else
    {
      $(".RaceNavBar").css("display", "none");
      $(".OffRaceNavBar").css("display", "inherit");
      ShowApropos(false);
    }
  }
  return retstop;
}

function HandleShowICS(raceid)
{
  let CallBack = function(result)
  {
    if (result)
    {
      FillRaceInstructions(result);
      $("#RacesInfoForm").modal("show");
    }
  };
  LoadRaceInfo(raceid, null, CallBack);
}


function LoadRaceInfo(RaceId, RaceVersion, CallBack)
{
  if (!RaceVersion)
  {
    RaceVersion = '';
  }
  $.get("/ws/raceinfo/desc.php?idrace=" + RaceId + "&v=" + RaceVersion,
    function(e)
    {
      if (_CurPlayer)
      {
        if (!_CurPlayer.RaceInfo)
        {
          _CurPlayer.RaceInfo = {};
        }
        _CurPlayer.RaceInfo[RaceId] = e;
      }

      CallBack(e);
    }
  );
}

function HandleVLMIndex(result)
{
  if (result)
  {
    $("#Ranking-Panel").show();
    let index;
    let rank = 1;
    for (index in result)
    {
      if (result[index])
      {
        result[index].rank = rank;
        result[index].vlmindex = '<span data-toggle="tooltip" title="' + result[index].vlmindex + '">' + parseInt(result[index].vlmindex, 10) + "</span>";
        rank++;
      }
    }
    BackupVLMIndexTable();
    VLMINdexFt.loadRows(result);
    $("#DivVlmIndex").removeClass("hidden");
    $("#RnkTabsUL").addClass("hidden");
    $("#DivRnkRAC").addClass("hidden");
    //ShowApropos(true);
  }
}

function HandleShowIndex(IndexType)
{
  let CallBack = HandleVLMIndex;

  $.get("/cache/rankings/VLMIndex_" + IndexType + ".json", CallBack);
}

function HandleShowOtherRaceRank(RaceId)
{
  //OnPlayerLoadedCallBack = function()
  //{
  let CallBack = function(Result)
  {
    FillRaceInfoHeader(Result);
  };
  RankingFt.RaceRankingId = RaceId;
  LoadRaceInfo(RaceId, 0, CallBack);
  LoadRankings(RaceId, OtherRaceRankingLoaded);
  //};

  /*if (typeof _CurPlayer !== "undefined" && _CurPlayer && _CurPlayer.CurBoat)
  {
    OnPlayerLoadedCallBack();
    OnPlayerLoadedCallBack = null;
  }*/
}

function OtherRaceRankingLoaded(RaceId)
{
  $("#Ranking-Panel").show();
  SortRanking("RAC", 0, RaceId);
  console.log("off race ranking loaded");
}

function initrecaptcha(InitPasswordReset, InitResetConfirm)
{
  if (InitPasswordReset && !RC_PwdResetReq)
  {
    RC_PwdResetReq = grecaptcha.render('recaptcha-PwdReset1');
  }

  if (InitResetConfirm && !RC_PwdResetConfirm)
  {
    RC_PwdResetConfirm = grecaptcha.render('recaptcha-PwdReset2');
  }
}

function HandleShowServerInfoModal(e)
{
  //$("#Infos").modal("hide");
  //$("#ServerInfo").show();
  ServerStatsMgr.LoadStats();
}

function InitMenusAndButtons()
{
  // Handle modal sizing to fit screen
  $('div.vresp.modal').on('shown.bs.modal', function()
  {
    $(this).show();
    setModalMaxHeight(this);
  });

  $("#ServerStatsLink").click(HandleShowServerInfoModal);
  $("#ServerInfo").on("hide.bs.modal", ServerStatsMgr.CancelStats.bind(ServerStatsMgr));

  // Add resize handler and force resize of small login image (just in case)
  HandleAproposSizing();

  $(window).resize(function()
  {
    HandleAproposSizing();
    if ($('.modal.in').length != 0)
    {
      setModalMaxHeight($('.modal.in'));
    }
  });

  // Handle password change button
  $("#BtnChangePassword").on("click", function(e)
  {
    e.preventDefault();
    HandlePasswordChangeRequest(e);
  });

  // Handle password reset request, and confirmation
  $("#ResetPasswordButton").on("click", function(e)
  {
    if (RC_PwdResetReq !== null)
    {
      grecaptcha.execute(RC_PwdResetReq);
    }
  });
  $("#ConfirmResetPasswordButton").on("click", function(e)
  {
    if (RC_PwdResetConfirm !== null)
    {
      grecaptcha.execute(RC_PwdResetConfirm);
    }
  });

  // Handle showing/hide of a-propos depending on login dialog status
  $("#LoginForm").on('show.bs.modal', function(e)
  {
    ShowApropos(false);
  });
  $("#LoginForm").on('hide.bs.modal', function(e)
  {
    ShowApropos(false);
  });
  $(".logindlgButton").on('click',
    function(e)
    {
      // Show Login form
      // hide apropos
      $("#LoginForm").modal('show');
    }
  );


  $(".logOutButton").on('click',
    function(e)
    {
      // Logout user
      Logout();
    }
  );

  $("#Menu").menu();
  $("#Menu").hide();

  $("input[type=submit],button")
    .button()
    .click(function(event)
    {
      event.preventDefault();
    });

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
      var target = "#" + $(this)[0].classList[2];
      MoveWPBoatControlerDiv(target);
    }
  );

  // Display setting dialog
  $(".BtnRaceList").click(
    function()
    {
      LoadRacesList(0);
      $("#RacesListForm").modal("show");
    }
  );

  // Handle clicking on ranking button, and ranking sub tabs
  InitRankingEvents();


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
      if (e.which === '13')
      {
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
  );

  // Handle SettingsSave button
  $('#SettingValidateButton').click(SaveBoatAndUserPrefs);

  // Handle SettingsSave button
  $('#SettingCancelButton').click(
    function()
    {
      LoadVLMPrefs();
      SetDDTheme(VLM2Prefs.CurTheme);
      $("#SettingsForm").modal("show");
    }
  );

  // Do fixed heading button
  $("#BtnPM_Heading").click(
    function()
    {
      SendVLMBoatOrder(PM_HEADING, $("#PM_Heading")[0].value);
    }

  );

  // Do fixed angle button
  $("#BtnPM_Angle").click(
    function()
    {
      SendVLMBoatOrder(PM_ANGLE, $("#PM_Angle")[0].value);
    }

  );

  // Tack
  $("#BtnPM_Tack").click(
    function()
    {
      $("#PM_Angle")[0].value = -$("#PM_Angle")[0].value;
    }
  );

  $("#BtnCreateAccount").click(
    function()
    {
      HandleCreateUser();
    }
  );

  $('.CreatePassword').pstrength();
  $('#NewPlayerEMail').blur(
    function(e)
    {
      $("#NewPlayerEMail").verimail(
      {
        messageElement: "#verimailstatus",
        language: _CurLocale
      });
    }

  );

  // Force create user form reset on show
  $('#InscriptForm').on("shown.bs.modal",
    function()
    {
      $(this).find('div.modal-body :input').val("");
    }
  );

  // Handler for Set WP on click
  $("#SetWPOnClick").click(HandleStartSetWPOnClick);
  $("#SetWPOffClick").click(HandleCancelSetWPOnClick);
  HandleCancelSetWPOnClick();

  // Add handlers for autopilot buttons
  $('body').on('click', '.PIL_EDIT', HandlePilotEditDelete);
  $('body').on('click', '.PIL_DELETE', HandlePilotEditDelete);

  $("#AutoPilotAddButton").click(HandleOpenAutoPilotSetPoint);
  $("#AP_SetTargetWP").click(HandleClickToSetWP);

  // AP datetime pickers
  InitDateTimePicker();
  $("#AP_Time").on('dp.change', HandleDateChange);
  $("#APValidateButton").click(HandleSendAPUpdate);
  $(".APField").on('change', HandleAPFieldChange);
  $(".APMode").on('click', HandleAPModeDDClick);

  // Draggable info window
  $(".Draggable").draggable(
  {
    handle: ".modal-header,.modal-body"
  });

  $("#MapPrefsToggle").click(HandleShowMapPrefs);

  $(".chkprefstore").on('change', HandleMapPrefOptionChange);
  $(".MapOppShowLi").click(HandleMapOppModeChange);

  $(".DDTheme").click(HandleDDlineClick);

  // Handle Start Boat Estimator button
  $("#StartEstimator").on('click', HandleStartEstimator);
  $("#EstimatorStopButton").on('click', HandleStopEstimator);

  InitGribSlider();

  InitFootables();

  // Handle clicking on ranking table link
  $(document.body).on('click', ".RaceHistLink", function(e)
  {
    HandleShowBoatRaceHistory(e);
  });


  // Add handler to refresh content of the pilototo table when showing tab content
  $("[PilRefresh]").on('click', HandleUpdatePilototoTable);

  // Handler for not racing boat palmares
  $("#HistRankingButton").on('click', function(e)
  {
    ShowUserRaceHistory(e, _CurPlayer.CurBoat.IdBoat());
  });

  $(".RaceListTab").on("click", function(e)
  {

    ShowUserRaceHistory(e, _CurPlayer.CurBoat.IdBoat());
  });

  $("#RacesFilterText").on("input", HandleRacesListFilterChange);

  //Handler for the races list form
  $(".RaceListFormTab").on("click", function(e)
  {
    FillRacesListForm(e);
  });


  // Go To WP Ortho, VMG, VBVMG Modes
  $("#BtnPM_Ortho, #BtnPM_VMG, #BtnPM_VBVMG").click(
    function()
    {
      var WpH = -1;
      var PMMode = PM_ORTHO;
      var Lat = $("#PM_Lat")[0].value;
      var Lon = $("#PM_Lon")[0].value;

      WpH = parseInt($("#PM_WPHeading")[0].value, 10);

      switch ($(this)[0].id)
      {
        case "BtnPM_Ortho":
          PMMode = PM_ORTHO;
          break;

        case "BtnPM_VMG":
          PMMode = PM_VMG;
          break;

        case "BtnPM_VBVMG":
          PMMode = PM_VBVMG;
          break;

      }
      SendVLMBoatOrder(PMMode, Lon, Lat, WpH);
    }
  );

  // InitCalendar link
  $("#CalendarPanel").on("shown.bs.modal", function(e)
  {
    HandleShowAgenda();
  });

  // Handle boat selector selection change
  //
  $(".BoatSelectorDropDownList").on("click", HandleBoatSelectionChange);

  $('#cp11').colorpicker(
  {
    useAlpha: false,
    format: false
  });

  $(document.body).on('click', ".ShowICSButton",
    function(e)
    {
      HandleFillICSButton(e);
    }
  );

  $(document.body).on('click', ".ShowRaceInSpectatorMode",
    function(e)
    {
      HandleGoToRaceSpectator(e);
    }
  );
  $("#PolarTab").on("click", HandlePolarTabClik);


  // Add boat to fleet link:
  InitPrefsDialogHandlers();

  UpdateVersionLine();

  function HandleAproposSizing()
  {
    let SmallHeight = $(window).width(); //* 0.9;
    let FillerHeight = $(window).width() * 0.12;
    let AproposHeight = $(".VLMSplash").height() - FillerHeight - 180;
    $(".VLMSplashUpperFiller").css("height", FillerHeight);
    $(".VLMSplashSmall").css("height", SmallHeight).css("background-position-y", 0.1 * $(window).width());
    $(".Apropos-text").css("max-height", AproposHeight);
  }

  $(document.body).on('mouseover', ".HoverShowMiniMap", HandleWPMiniMapHover);

}



function InitDateTimePicker()
{
  let Options = {
    locale: _CurLocale,
    format: 'DD MM YYYY, HH:mm:ss',
    timeZone: ''
  };

  if (VLM2Prefs.MapPrefs.UseUTC)
  {
    Options.format = 'DD MM YYYY, HH:mm:ss [Z]';
    Options.timeZone = 'UTC';
  }

  $("#AP_Time").datetimepicker(Options);
  //$('#AP_Time').data("DateTimePicker").OPTION(Options);
}

function InitPrefsDialogHandlers()
{
  $("#AddBoatToFleet").on('click', HandleAddBoatToFleetRequest);
}

function HandleAddBoatToFleetRequest()
{
  $("#AddBoatToFleet").addClass("hidden");
  $("#PnlAddBoat").removeClass("hidden");
}

function InitRankingEvents()
{
  /*$("#Ranking-Panel").on('shown.bs.collapse', function(e)
  {
    HandleRaceSortChange(e);
  });
*/
  $(document.body).on('click', ".RankingButton", function(e)
  {
    let RaceId = $(e.currentTarget).attr("IdRace");

    if (typeof RaceId !== "undefined" && RaceId)
    {
      window.open('/jvlm?RaceRank=' + RaceId, "RankTab");
    }
  });

  // Handle clicking on ranking button, and ranking sub tabs
  $(document.body).on('click', "[RnkSort]", function(e)
  {
    HandleRaceSortChange(e);
  });
  $("#Ranking-Panel").on('show.bs.collapse', function(e)
  {
    ResetRankingWPList(e);
  });
}

function UpdateVersionLine()
{
  let Build = new moment(BuildDate);
  $("#BuildDate").text("Build : " + Build.fromNow());
  $('[data-toggle="tooltip"]').tooltip();
}
let _CachedRaceInfo = null;

function HandlePolarTabClik()
{
  if (_CachedRaceInfo)
  {
    DrawPolar(_CachedRaceInfo);
  }
}

function InitPolar(RaceInfo)
{
  _CachedRaceInfo = RaceInfo;
}

function HandleGoToRaceSpectator(e)
{
  if (typeof e !== "undefined" && e)
  {
    let b = e.target;
    let RaceId = $(e.currentTarget).attr('idRace');

    if (typeof RaceId !== "undefined" && RaceId)
    {
      window.open("/guest_map/index.html?idr=" + RaceId, "Spec_" + RaceId);
      return;
    }
  }
}

function HandleFillICSButton(e)
{

  // Race Instruction
  if (typeof e !== "undefined" && e)
  {
    let b = e.target;
    let RaceId = $(e.currentTarget).attr('idRace');

    if (typeof RaceId !== "undefined" && RaceId)
    {
      HandleShowICS(RaceId);
      return;
    }
  }
  if (typeof _CurPlayer !== "undefined" && _CurPlayer && _CurPlayer.CurBoat && _CurPlayer.CurBoat.RaceInfo)
  {
    FillRaceInstructions(_CurPlayer.CurBoat.RaceInfo);
  }

}

let VLMAgenda = null;

function HandleShowAgenda()
{
  if (VLMAgenda)
  {
    VLMAgenda.destroy();
  }
  let CalEl = jQuery('#Calendar')[0];
  VLMAgenda = new FullCalendar.Calendar(CalEl,
  {
    plugins: ['dayGrid'],
    locale: _CurLocale,
    editable: false,
    header:
    {
      left: 'title',
      center: '',
      right: 'today prev,next'
    },
    firstDay: 1,
    events: "/feed/races.fullcalendar.php",
    data: function()
    { // a function that returns an object
      return {
        jvlm: 1
      };
    },
    timeFormat: 'H:mm',
    loading: function(bool)
    {
      if (bool) jQuery('#loading').show();
      else jQuery('#loading').hide();
    }
  });

  VLMAgenda.render();

  $("#Infos").modal("hide");
}

function HandlePasswordChangeRequest(e)
{
  // Check non empty value for oldpassword
  let OldPwd = $("#CurPassword")[0].value;
  let NewPwd1 = $("#NewPassword1")[0].value;
  let NewPwd2 = $("#NewPassword2")[0].value;

  $(".Password").val("");

  if (!OldPwd || OldPwd === "")
  {
    VLMAlertDanger(GetLocalizedString("CurPwdRequired"));
    return;
  }
  else if (NewPwd1 !== NewPwd2)
  {
    VLMAlertDanger(GetLocalizedString("CurPwdRequired"));
    return;
  }
  else if (NewPwd1 === "")
  {
    VLMAlertDanger(GetLocalizedString("NewPwdRequired"));
    return;
  }

  let PostData = {
    OldPwd: OldPwd,
    NewPwd: NewPwd1
  };

  $.post(
    "/ws/playersetup/password_change.php",
    "parms=" + JSON.stringify(PostData),
    function(e)
    {
      HandlePasswordChangeResult(e);
    }
  );

}

function HandlePasswordChangeResult(e)
{
  if (e.success)
  {
    VLMAlertInfo();
  }
  else
  {
    VLMAlertDanger(GetLocalizedString(e.error.msg));
  }
}

function SendResetPassword(RecaptchaCode)
{
  let PostData = {
    email: PasswordResetInfo[0],
    seed: PasswordResetInfo[1],
    key: RecaptchaCode
  };

  $.get("/ws/playersetup/password_reset.php?email=" + PasswordResetInfo[0] + "&seed=" + PasswordResetInfo[1] + "&key=" + RecaptchaCode,
    function(e)
    {
      HandlePasswordReset(e, true);
    });
}

function SendResetPasswordLink(RecaptchaCode)
{
  let UserMail = $(".UserName").val();


  if (UserMail === "")
  {
    VLMAlertDanger(GetLocalizedString("Enter your email for resetting your password"));
    grecaptcha.reset(RC_PwdResetReq);
    return;
  }

  let PostData = {
    email: UserMail,
    key: RecaptchaCode
  };

  $.post("/ws/playersetup/password_reset.php",
    "parms=" + JSON.stringify(PostData),
    function(e)
    {
      HandlePasswordReset(e, false);
    });
}

function HandlePasswordReset(e, Validation)
{
  if (e.success)
  {
    if (Validation)
    {
      VLMAlertInfo(GetLocalizedString('Check your inbox to get your new password.'));
      grecaptcha.reset(RC_PwdResetReq);
    }
    else
    {
      VLMAlertInfo(GetLocalizedString('An email has been sent. Click on the link to validate.'));
      grecaptcha.reset(RC_PwdResetConfirm);
    }
  }
  else
  {
    VLMAlertDanger("Something went wrong :(");
    grecaptcha.reset(RC_PwdResetReq);
    grecaptcha.reset(RC_PwdResetConfirm);
  }

}

function InitFooTable(Id)
{
  let ret = FooTable.init("#" + Id,
  {
    'name': Id,
    'on':
    {
      'ready.ft.table': HandleReadyTable,
      'after.ft.paging': HandlePagingComplete,
      'postdraw.ft.table': HandleTableDrawComplete
    }
  });
  ret.DrawPending = true;
  ret.CallbackPending = null;
  return ret;
}

function InitFootables()
{
  // Handle race discontinuation request
  $("#DiscontinueRaceButton").on('click', HandleDiscontinueRaceRequest);

  // Init Pilototo footable, and get pointer to object          
  PilototoFt = InitFooTable("PilototoTable");
  RankingFt = InitFooTable("RankingTable");
  //RankingFt.LastWPFill = new Date();  
  RaceHistFt = InitFooTable("BoatRaceHist");
  ICS_WPft = InitFooTable("RaceWayPoints");
  NSZ_WPft = InitFooTable("NSZPoints");
  VLMINdexFt = InitFooTable("VLMIndexTable");
}

function HandleUpdatePilototoTable(e)
{
  UpdatePilotInfo(_CurPlayer.CurBoat);
}

function InitSlider(SliderId, HandleId, min, max, value, SlideCallback)
{
  let handle = $("#" + HandleId);
  $("#" + SliderId).slider(
  {
    orientation: "vertical",
    min: min,
    max: max,
    value: value,
    create: function()
    {
      handle.text($(this).slider("value"));
    },
    slide: function(event, ui)
    {
      SlideCallback(event, ui);
    }
  });

}

function InitGribSlider()
{
  InitSlider("GribSlider", "GribSliderHandle", 0, 72, 0, HandleGribSlideMove);
}

function HandleRaceSortChange(e)
{
  let Target = $(e.currentTarget).attr('rnksort');

  //$("[rnksort]").removeClass("active")
  switch (Target)
  {
    case 'WP':
      if (!RankingFt.DrawPending)
      {
        SortRanking(Target, $(e.currentTarget).attr('WPRnk'));
      }
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
      console.log("Sort change request" + e);
  }

}

function HandleGribSlideMove(event, ui)
{
  if (typeof map.GribMap === "undefined")
  {
    return;
  }

  let handle = $("#GribSliderHandle");
  handle.text(ui.value);
  let GribEpoch = new Date().getTime();
  map.GribMap.SetGribMapTime(GribEpoch + ui.value * 3600000);

  if (VLM2Prefs.MapPrefs.TrackEstForecast && _CurPlayer.CurBoat.Estimator)
  {
    let EstPos = _CurPlayer.CurBoat.GetClosestEstimatePoint(new Date(GribEpoch + ui.value * 3600 * 1000));
    RefreshEstPosLabels(EstPos);
    StartEstimateTimeout();
  }
}

function HandleDiscontinueRaceRequest()
{
  GetUserConfirmation(GetLocalizedString('unsubscribe'), true, HandleRaceDisContinueConfirmation);
}

function HandleRaceDisContinueConfirmation(State)
{
  if (State)
  {
    //construct base
    let BoatId = _CurPlayer.CurBoat.IdBoat();
    let RaceId = _CurPlayer.CurBoat.Engaged();
    DiconstinueRace(BoatId, RaceId);
    $("#ConfirmDialog").modal('hide');
    $("#RacesInfoForm").modal('hide');
  }
  else
  {
    VLMAlertDanger("Ouf!");
  }
}

function HandleStopEstimator(e)
{
  var CurBoat = _CurPlayer.CurBoat;

  if (typeof CurBoat === "undefined" || !CurBoat)
  {
    // Something's wrong, just ignore
    return;
  }

  CurBoat.Estimator.Stop();

}

function HandleStartEstimator(e)
{
  var CurBoat = _CurPlayer.CurBoat;

  if (typeof CurBoat === "undefined" || !CurBoat)
  {
    // Something's wrong, just ignore
    return;
  }

  let RaceFeatures = GetRaceMapFeatures(CurBoat);
  if (RaceFeatures)
  {
    for (let index in RaceFeatures.PilotMarkers)
    {
      if (RaceFeatures.PilotMarkers[index])
      {
        RaceFeatures.PilotMarkers[index].remove();
        RaceFeatures.PilotMarkers[index].unbindPopup(RaceFeatures.PilotMarkers[index].getPopup());
      }
    }
  }
  CurBoat.Estimator.Start(HandleEstimatorProgress);
}


function HandleEstimatorProgress(Complete, Pct, Dte)
{
  let Est = _CurPlayer.CurBoat.Estimator;

  if (!Est)
  {
    return;
  }
  if (Complete)
  {
    $("#StartEstimator").removeClass("hidden");
    $("#PbEstimatorProgressBar").addClass("hidden");
    //$("#PbEstimatorProgressText").addClass("hidden")
    $("#EstimatorStopButton").addClass("hidden");
    Est.LastPctRefresh = -1;
    Est.LastPctDraw = -1;
    DrawBoat(_CurPlayer.CurBoat);

  }
  else if (Pct - Est.LastPctRefresh > 0.25)
  {
    $("#EstimatorStopButton").removeClass("hidden");
    $("#StartEstimator").addClass("hidden");
    $("#PbEstimatorProgressBar").removeClass("hidden");
    $("#PbEstimatorProgressText").removeClass("hidden");
    $("#PbEstimatorProgressText").text(Pct);
    $("#PbEstimatorProgress").css("width", Pct + "%");
    $("#PbEstimatorProgress").attr("aria-valuenow", Pct);
    $("#PbEstimatorProgress").attr("aria-valuetext", Pct);
    Est.LastPctRefresh = Pct;
  }
  else if (Pct - Est.LastPctDraw > 1)
  {
    DrawBoatEstimateTrack(_CurPlayer.CurBoat, GetRaceMapFeatures(_CurPlayer.CurBoat));
    Est.LastPctDraw = Pct;
  }
}

function HandleFlagLineClick(e)
{
  var Flag = e.target.attributes.flag.value;

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
  BuildUserBoatList(boat, isfleet);
}

function BuildUserBoatList(boat, IsFleet)
{
  $(".BoatSelectorDropDownList").append(GetBoatDDLine(boat, IsFleet));
}

function GetBoatDDLine(Boat, IsFleet)
{
  var Line = '<li class="DDLine" BoatID="' + Boat.IdBoat() + '">';
  Line = Line + GetBoatInfoLine(Boat, IsFleet) + '</li>';
  return Line;
}

function UpdateBoatList(SingleBoat)
{
  if (!_CurPlayer || !_CurPlayer.Fleet)
  {
    return;
  }

  let Fleet = _CurPlayer.Fleet.concat(_CurPlayer.BSFleet);

  for (let index in Fleet)
  {
    if (Fleet[index])
    {
      let Boat = Fleet[index];
      if (!SingleBoat || SingleBoat.IdBoat() == Boat.IdBoat())
      {
        UpdateBoatStatusAndName(Boat);
        if (SingleBoat)
        {
          return;
        }
      }
    }
  }
}

function UpdateBoatStatusAndName(Boat)
{
  if (!Boat)
  {
    return;
  }

  let DDLine = $("Div[boatid='" + Boat.IdBoat() + "'] img");
  DDLine.removeClass("BStatus_Docked").removeClass("BStatus_Racing").removeClass("BStatus_Stranded");

  if (!Boat.Engaged())
  {
    DDLine.addClass("BStatus_Docked");
  }
  else if (Boat.VLMInfo && Boat.VLMInfo["S&G"])
  {
    DDLine.addClass("BStatus_Stranded");
  }
  else if (Boat.VLMInfo)
  {
    DDLine.addClass("BStatus_Racing");
  }
  let DDLineBoatName = $(".BoatStatusName[boatid='" + Boat.IdBoat() + "']");
  DDLineBoatName.text(Boat.BoatName());

}

function GetBoatInfoLine(Boat, IsFleet)
{
  var Line = '<div boatid=' + Boat.IdBoat() + '>';
  var BoatStatus = "BStatus_Racing";

  if (!Boat.Engaged())
  {
    BoatStatus = "BStatus_Docked";
  }

  if ((typeof Boat.VLMInfo !== "undefined") && Boat.VLMInfo["S&G"])
  {
    BoatStatus = "BStatus_Stranded";
  }

  if (!IsFleet)
  {
    Line = Line + '<span class="badge">BS';
  }

  Line = Line + '<img class="BoatStatusIcon ' + BoatStatus + '" />';
  if (!IsFleet)
  {
    Line = Line + '</span>';
  }

  Line = Line + '<span>-</span><span class="BoatStatusName" boatid=' + Boat.IdBoat() + '>' + HTMLDecode(Boat.BoatName()) + '</span></div>';
  return Line;
}

function ShowBgLoad()
{
  $("#BgLoadProgress").css("display", "block");
}

function HideBgLoad()
{
  $("#BgLoadProgress").css("display", "block");
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
    LoggedInDisplay = "block";
    LoggedOutDisplay = "none";
  }
  else
  {
    LoggedInDisplay = "none";
    LoggedOutDisplay = "block";
  }
  $("[LoggedInNav='true']").css("display", LoggedInDisplay);
  $("[LoggedInNav='false']").css("display", LoggedOutDisplay);

  if (typeof _CurPlayer !== 'undefined' && _CurPlayer && _CurPlayer.IsAdmin)
  {
    $("[AdminNav='true']").css("display", "block");
  }
  else
  {
    $("[AdminNav='true']").css("display", "none");
  }

  // Display apropos
  ShowApropos(LoggedIn);

}

function ShowApropos(LoggedIn)
{
  if (!LoggedIn)
  {
    $(".VLMSplash").css("visibility", "visible");
    $(".MapContainer").css("visibility", "hidden");
  }
  else
  {
    $(".VLMSplash").css("visibility", "hidden");
    $(".MapContainer").css("visibility", "visible");
  }
  //$('#Apropos').modal(LoggedIn ? 'hide' : 'show');
}

function HandleRacingDockingButtons(IsRacing)
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
  let IsRacing = (typeof Boat !== "undefined") && (typeof Boat.VLMInfo !== "undefined") && parseInt(Boat.VLMInfo.RAC, 10);
  HandleRacingDockingButtons(IsRacing);
}

function SetTWASign(Boat)
{
  let twd = Boat.VLMInfo.TWD;
  let heading = Boat.VLMInfo.HDG;

  let twa = twd - heading;
  if (twa < -180)
  {
    twa += 360;
  }

  if (twa > 180)
  {
    twa -= 360;
  }


  let winddir = (360 - twd) % 360 + 90;
  let boatdir = (360 - heading) % 360 + 90;

  if (twa * Boat.VLMInfo.TWA > 0)
  {
    Boat.VLMInfo.TWA = -Boat.VLMInfo.TWA;
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
  SetTWASign(Boat);

  // Fix HDG when boat is mooring
  if (Boat.VLMInfo.PIM === "2" && Boat.VLMInfo.PIP === "0")
  {
    // Mooring 
    Boat.VLMInfo.HDG = Boat.VLMInfo.TWD;
    Boat.VLMInfo.BSP = 0;
  }

  if (Boat.VLMInfo)
  {
    $(".NWPBadge").css("visibility", "visible");
    $(".NWPBadge").text(Boat.VLMInfo.NWP);
  }
  else
  {
    $(".NWPBadge").css("visibility", "hidden");
  }


  // Update GUI for current player
  // Todo Get Rid of Coords Class
  let lon = new Coords(Boat.VLMInfo.LON, true);
  let lat = new Coords(Boat.VLMInfo.LAT);

  // Create field mapping array
  // 0 for text fields
  // 1 for input fields
  let BoatFieldMappings = [];
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatLon", lon.toString()]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatLat", lat.toString()]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatSpeed", RoundPow(Boat.VLMInfo.BSP, 2)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatHeading", RoundPow(Boat.VLMInfo.HDG, 1)]);
  BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Heading", RoundPow(Boat.VLMInfo.HDG, VLM2Prefs.InputDigits)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatAvg", RoundPow(Boat.VLMInfo.AVG, 1)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatDNM", RoundPow(Boat.VLMInfo.DNM, 1)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatLoch", RoundPow(Boat.VLMInfo.LOC, 1)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatOrtho", RoundPow(Boat.VLMInfo.ORT, 1)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatLoxo", RoundPow(Boat.VLMInfo.LOX, 1)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatVMG", RoundPow(Boat.VLMInfo.VMG, 1)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatWindSpeed", RoundPow(Boat.VLMInfo.TWS, 1)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatWindDirection", RoundPow(Boat.VLMInfo.TWD, 1)]);
  BoatFieldMappings.push([FIELD_MAPPING_CHECK, "#PM_WithWPHeading", Boat.VLMInfo['H@WP'] !== "-1.0"]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#RankingBadge", Boat.VLMInfo.RNK]);
  BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_WPHeading", Boat.VLMInfo['H@WP']]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatClass", Boat.VLMInfo.POL.substring(5)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".RaceName", Boat.VLMInfo.RAN]);

  let WP = new VLMPosition(Boat.VLMInfo.WPLON, Boat.VLMInfo.WPLAT);
  BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Lat", WP.Lat.Value]);
  BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Lon", WP.Lon.Value]);

  if ((WP.Lon.Value === 0) && (WP.Lat.Value === 0))
  {
    WP = Boat.GetNextWPPosition();
  }

  if (typeof WP !== "undefined" && WP)
  {
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#PM_CurWPLat", WP.Lat.toString()]);
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#PM_CurWPLon", WP.Lon.toString()]);
  }
  else
  {
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#PM_CurWPLat", "N/A"]);
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#PM_CurWPLon", "N/A"]);
  }

  if (parseInt(Boat.VLMInfo.PIM, 10) === PM_ANGLE)
  {
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatWindAngle", RoundPow(Math.abs(Boat.VLMInfo.PIP), 1)]);
    BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Angle", RoundPow(Boat.VLMInfo.PIP, VLM2Prefs.InputDigits)]);
  }
  else
  {
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatWindAngle", RoundPow(Math.abs(Boat.VLMInfo.TWA), 1)]);
    BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Angle", RoundPow(Boat.VLMInfo.TWA, VLM2Prefs.InputDigits)]);
  }

  FillFieldsFromMappingTable(BoatFieldMappings);

  // Change color depênding on windangle
  var WindColor = "lime";
  if (Boat.VLMInfo.TWA > 0)
  {
    WindColor = "red";
  }
  $(".BoatWindAngle").css("color", WindColor);

  // Get WindAngleImage
  var wHeading = Math.round((Boat.VLMInfo.TWD + 180) * 100) / 100;
  var wSpeed = Math.round(Boat.VLMInfo.TWS * 100) / 100;
  var BoatType = Boat.VLMInfo.POL;
  var BoatHeading = Math.round(Boat.VLMInfo.HDG * 100) / 100;
  var WindSpeed = Math.round(Boat.VLMInfo.TWS * 100) / 100;
  var OrthoToWP = Math.round(Boat.VLMInfo.ORT * 100) / 100;


  $("#ImgWindAngle").attr('src', 'windangle.php?wheading=' + wHeading + '&boatheading=' + BoatHeading + '&wspeed=' + WindSpeed + '&roadtoend=' + OrthoToWP + '&boattype=' + BoatType + "&jvlm=" + Boat.VLMInfo.NOW);
  $("#ImgWindAngle").css("transform", "rotate(" + wHeading + "deg)");
  $("#DeckImage").css("transform", "rotate(" + BoatHeading + "deg)");


  // Set active PM mode display
  $(".PMActiveMode").css("display", "none");
  $(".BCPane").removeClass("active");
  var TabID = ".ActiveMode_";
  var ActivePane = "";

  switch (Boat.VLMInfo.PIM)
  {
    case "1":
      TabID += 'Heading';
      ActivePane = "BearingMode";
      break;
    case "2":
      TabID += 'Angle';
      ActivePane = "AngleMode";
      break;
    case "3":
      TabID += 'Ortho';
      ActivePane = "OrthoMode";
      break;
    case "4":
      TabID += 'VMG';
      ActivePane = "VMGMode";
      break;
    case "5":
      TabID += 'VBVMG';
      ActivePane = "VBVMGMode";
      break;

    default:
      VLMAlert("Unsupported VLM PIM Mode, expect the unexpected....", "alert-info");

  }

  // Override PIM Tab if requested
  /*if (typeof TargetTab !== "undefined" && TargetTab=='AutoPilot')
  {
    TabID+='AutoPilotTab';
    ActivePane=TargetTab;
    UpdatePilotInfo(Boat);
  }*/

  $(TabID).css("display", "inline");
  $("." + ActivePane).addClass("active");
  $("#" + ActivePane).addClass("active");

  UpdatePilotInfo(Boat);
  UpdatePolarImages(Boat);

}

function FillFieldsFromMappingTable(MappingTable)
{
  // Loop all mapped fields to their respective location
  for (let index in MappingTable)
  {
    if (MappingTable[index])
    {
      switch (MappingTable[index][0])
      {
        case FIELD_MAPPING_TEXT:
          $(MappingTable[index][1]).text(MappingTable[index][2]);
          break;

        case FIELD_MAPPING_VALUE:
          $(MappingTable[index][1]).val(MappingTable[index][2]);
          break;

        case FIELD_MAPPING_CHECK:
          $(MappingTable[index][1]).prop('checked', (MappingTable[index][2]));
          break;

        case FIELD_MAPPING_IMG:
          $(MappingTable[index][1]).attr('src', (MappingTable[index][2]));
          break;

        case FIELD_MAPPING_CALLBACK:
          MappingTable[index][2](MappingTable[index][1]);
          break;

        case FIELD_MAPPING_STYLE:
          $(MappingTable[index][1]).css(MappingTable[index][2], MappingTable[index][3]);
          break;

        case FIELD_MAPPING_POPUP:
          $(MappingTable[index][1]).attr("title", MappingTable[index][2]);
          break;

      }
    }
  }
}

function FillRaceInstructions(RaceInfo)
{

  if (typeof RaceInfo === "undefined" || !RaceInfo)
  {
    return;
  }

  let HideDiscontinueTab = true;
  if (typeof _CurPlayer !== "undefined" && _CurPlayer && _CurPlayer.CurBoat && _CurPlayer.CurBoat.RaceInfo)
  {
    HideDiscontinueTab = (_CurPlayer.CurBoat.RaceInfo.idraces !== RaceInfo.idraces);
  }

  if (HideDiscontinueTab)
  {
    $("#DiscontinueRaceTab").addClass("hidden");
  }
  else
  {
    $("#DiscontinueRaceTab").removeClass("hidden");
  }

  let Instructions = [];
  FillRaceInfoHeader(RaceInfo);
  FillRaceWaypointList(RaceInfo);
  InitPolar(RaceInfo);

  $.get("/ws/raceinfo/exclusions.php?idr=" + RaceInfo.idraces + "&v=" + RaceInfo.VER,
    function(result)
    {
      if (result && result.success)
      {
        FillNSZList(result.Exclusions);
      }
    }
  );

}

let PolarSliderInited = false;

function FillRaceInfoHeader(RaceInfo)
{
  if (typeof RaceInfo === 'undefined' || !RaceInfo)
  {
    return;
  }
  let BoatFieldMappings = [];
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".ICSRaceName", RaceInfo.racename]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".RaceId", RaceInfo.idraces]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatType", RaceInfo.boattype.substring(5)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".VacFreq", parseInt(RaceInfo.vacfreq, 10)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#LockTime", parseInt(RaceInfo.coastpenalty, 10) / 60]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#EndRace", parseInt(RaceInfo.firstpcttime, 10)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#RaceStartDate", GetLocalUTCTime(parseInt(RaceInfo.deptime, 10) * 1000, true, true)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#RaceLineClose", GetLocalUTCTime(parseInt(RaceInfo.closetime, 10) * 1000, true, true)]);
  BoatFieldMappings.push([FIELD_MAPPING_IMG, "#RaceImageMap", "/cache/racemaps/" + RaceInfo.idraces + ".png"]);
  FillFieldsFromMappingTable(BoatFieldMappings);
}

function HandlePolarSpeedSlide(event, ui, RaceInfo)
{
  let handle = $("#PolarSpeedHandle");
  handle.text(ui.value);
  DrawPolar(RaceInfo);
}

function DrawPolar(RaceInfo)
{
  let Canvas = $("#PolarCanvas")[0];
  let WindSpeed = 25;

  if (PolarSliderInited)
  {
    WindSpeed = parseFloat($("#PolarSpeedHandle").text());
  }
  let PolarLine = PolarsManager.GetPolarLine(RaceInfo.boattype, WindSpeed, function()
  {
    DrawPolar(RaceInfo);
  }, null, 1);

  if (PolarLine)
  {
    if (!PolarSliderInited)
    {
      InitSlider("PolarSpeedSlider", "PolarSpeedHandle", 0, 60, WindSpeed, function(e, ui)
      {
        HandlePolarSpeedSlide(e, ui, RaceInfo);
      });
      PolarSliderInited = true;
    }

    Canvas.width = $("#PolarCanvas").width();
    Canvas.height = Canvas.width;
    let Context = Canvas.getContext("2d");
    let First = true;
    let dAlpha = Math.PI / PolarLine.length; // Not counting 0 helps here
    let Cx = 3;
    let Cy = Canvas.width / 2;
    let S = Canvas.width / 2;
    let MaxSpeed = PolarsManager.GetPolarMaxSpeed(RaceInfo.boattype, WindSpeed);
    let PrevL = 0;
    let VMGAngle = 0;
    let RedZone = true;
    let PrevX;
    let PrevY;

    Context.beginPath();
    Context.lineWidth = "1";
    Context.strokeStyle = "#FF0000";

    for (let index in PolarLine)
    {
      if (PolarLine[index])
      {
        let l = PolarLine[index];
        index = parseInt(index, 10);
        let a = index * dAlpha;
        let y = Cy + S * l * Math.cos(a);
        let x = Cx + S * l * Math.sin(a);

        let VMG = Math.cos(a + VMGAngle) * l;
        if (RedZone && (VMG <= PrevL))
        {
          Context.stroke();
          Context.beginPath();
          Context.moveTo(PrevX, PrevY);
          Context.strokeStyle = "#FFFFFF";
          RedZone = false;
        }
        else if (!RedZone && (VMG >= PrevL))
        {
          Context.stroke();
          Context.beginPath();
          Context.moveTo(PrevX, PrevY);
          Context.strokeStyle = "#FF0000";
          RedZone = true;
        }

        PrevL = VMG;

        if (First)
        {
          Context.moveTo(x, y);
          First = false;
        }
        else
        {
          Context.lineTo(x, y);
        }
        PrevX = x;
        PrevY = y;
      }
    }
    Context.stroke(); // Draw it
    // Draw axes
    Context.beginPath();
    Context.lineWidth = "1";
    Context.strokeStyle = "#00FF00";
    Context.moveTo(Cx, 0);
    Context.lineTo(Cx, Canvas.height);
    Context.stroke();
    Context.moveTo(Cx - 1, Canvas.height / 2);
    Context.lineTo(Cx + Canvas.width, Canvas.height / 2);
    Context.stroke();

    // Draw Speed circles & legends
    let As = Math.round(MaxSpeed / 5);

    if (!As)
    {
      As = 1;
    }

    for (let index = 1; As * index - 1 <= MaxSpeed; index++)
    {
      Context.beginPath();
      Context.strokeStyle = "#7FFFFF";
      Context.arc(Cx, Cy, S * index * As / MaxSpeed, Math.PI / 2, 1.5 * Math.PI, true);
      Context.stroke();
      Context.strokeText(" " + As * index, Cx + 1 + As * S * index / MaxSpeed, Cy + 10);
    }
  }


}

function UpdatePolarImages(Boat)
{
  var PolarName = Boat.VLMInfo.POL.substring(5);
  var Angle;
  var HTML = "";
  for (Angle = 0; Angle <= 45; Angle += 15)
  {
    HTML += '<li><img class="polaire" src="/scaledspeedchart.php?boattype=boat_' + PolarName + '&amp;minws=' + Angle + '&amp;maxws=' + (Angle + 15) + '&amp;pas=2" alt="speedchart"></li>';
  }

  $("#PolarList").empty();
  $("#PolarList").append(HTML);
}

function BackupFooTable(ft, TableId, RestoreId)
{
  if (!ft.DOMBackup)
  {
    ft.DOMBackup = $(TableId);
    ft.RestoreId = RestoreId;
  }
  else if (typeof $(TableId)[0] === "undefined")
  {
    $(ft.RestoreId).append(ft.DOMBackup);
    console.log("Restored footable " + TableId + " " + new Date());
  }
}

function UpdatePilotInfo(Boat)
{
  if ((typeof Boat === "undefined") || (!Boat) || PilototoFt.DrawPending)
  {
    return;
  }

  BackupFooTable(PilototoFt, "#PilototoTable", "#PilototoTableInsertPoint");


  let PilRows = [];
  if (Boat && Boat.VLMInfo && Boat.VLMInfo.PIL && Boat.VLMInfo.PIL.length > 0)
  {
    for (let index in Boat.VLMInfo.PIL)
    {
      if (Boat.VLMInfo.PIL[index])
      {
        var PilLine = GetPilototoTableLigneObject(Boat, index);
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

  PilototoFt.DrawPending = true;
  PilototoFt.loadRows(PilRows, false);
  console.log("loaded pilototo table");

  UpdatePilotBadge(Boat);
}

function HandleReadyTable(e, ft)
{
  console.log("Table ready" + ft);
  ft.DrawPending = false;
  if (ft.OnReadyTable)
  {
    ft.OnReadyTable();
  }
}

function HandlePagingComplete(e, ft)
{
  let classes = {
    ft_class_myboat: "rnk-myboat",
    ft_class_friend: "rnk-friend",
    ft_class_oncoast: "rnk-oncoast",
    ft_class_racing: "rnk-racing",
    ft_class_locked: "rnk-locked",
    ft_class_dns: "rnk-dns"
  };

  let index;

  for (let index in classes)
  {
    if (classes[index])
    {
      $('td').closest('tr').removeClass(classes[index]);
    }
  }
  for (index in classes)
  {
    if (classes[index])
    {
      $('td:contains("' + index + '")').closest('tr').addClass(classes[index]);
    }
  }

  ft.DrawPending = false;

}

function HandleTableDrawComplete(e, ft)
{
  console.log("TableDrawComplete " + ft.id);
  ft.DrawPending = false;
  if (ft === RankingFt)
  {
    setTimeout(function()
    {
      DeferedGotoPage(e, ft);
    }, 500);
  }
  else if (ft.CallbackPending)
  {
    setTimeout(function()
    {
      ft.CallbackPending();
      ft.CallbackPending = null;
    }, 500);
    return;
  }
}

function DeferedGotoPage(e, ft)
{
  if (RankingFt.TargetPage)
  {
    RankingFt.gotoPage(RankingFt.TargetPage);
    RankingFt.TargetPage = 0;
  }
  setTimeout(function()
  {
    DeferedPagingStyle(e, ft);
  }, 200);
}

function DeferedPagingStyle(e, ft)
{
  HandlePagingComplete(e, ft);
}

function GetPilototoTableLigneObject(Boat, Index)
{
  let PilOrder = Boat.VLMInfo.PIL[Index];
  let OrderDate = GetLocalUTCTime(PilOrder.TTS * 1000, true, true);
  let PIMText = GetPilotModeName(PilOrder.PIM);

  // Force as number and rebase from 1
  Index = parseInt(Index, 10) + 1;

  // Adapt the template to current order
  $("#EditCellTemplate .PIL_EDIT").attr('pil_id', Index);
  $("#DeleteCellTemplate .PIL_DELETE").attr("TID", PilOrder.TID).attr('pil_id', Index);

  let Ret = {
    date: OrderDate,
    PIM: PIMText,
    PIP: PilOrder.PIP,
    Status: PilOrder.STS,
    Edit: $("#EditCellTemplate").first().html(),
    Delete: $("#DeleteCellTemplate").first().html()
  };


  return Ret;
}

function ShowAutoPilotLine(Boat, Index)
{
  var Id = "#PIL" + Index;
  var PilOrder = Boat.VLMInfo.PIL[Index - 1];
  var OrderDate = new Date(PilOrder.TTS * 1000);
  var PIMText = GetPilotModeName(PilOrder.PIM);

  if (typeof $(Id)[0] === "undefined")
  {
    let bpkt = 0;
  }

  $(Id)[0].attributes.TID = PilOrder.TID;
  SetSubItemValue(Id, "#PIL_DATE", OrderDate);
  SetSubItemValue(Id, "#PIL_PIM", PIMText);
  SetSubItemValue(Id, "#PIL_PIP", PilOrder.PIP);
  SetSubItemValue(Id, "#PIL_STATUS", PilOrder.STS);
  $(Id).show();
}

function GetPILIdParentElement(item)
{
  var done = false;
  var RetValue = item;
  do {
    if (typeof RetValue === "undefined")
    {
      return;
    }
    if ('id' in RetValue.attributes)
    {
      var ItemId = RetValue.attributes.id.value;
      if ((ItemId.length === 4) && (ItemId.substring(0, 3) === "PIL"))
      {
        return RetValue;
      }
    }

    RetValue = RetValue.parentElement;

  } while (!done);
}

function HandlePilotEditDelete(e)
{
  var ClickedItem = $(this)[0];
  var ItemId = ClickedItem.attributes['class'].value;
  var Boat = _CurPlayer.CurBoat;

  var OrderIndex = parseInt(ClickedItem.attributes.pil_id.value, 10);

  if (ItemId === "PIL_EDIT")
  {
    HandleOpenAutoPilotSetPoint(e);
  }
  else if (ItemId === "PIL_DELETE")
  {
    DeletePilotOrder(Boat, ClickedItem.attributes.TID.value);
  }

}

function GetPilotModeName(PIM)
{
  switch (parseInt(PIM, 10))
  {
    case 1:
      return GetLocalizedString('autopilotengaged');

    case 2:
      return GetLocalizedString('constantengaged');

    case 3:
      return GetLocalizedString('orthoengaged');

    case 4:
      return GetLocalizedString('bestvmgengaged');

    case 5:
      return GetLocalizedString('vbvmgengaged');

    default:
      return "PIM ???" + PIM + "???";
  }
}

function SetSubItemValue(SourceElementName, TargetElementName, NewVaue)
{
  let El = $(SourceElementName).find(TargetElementName);
  if (El.length > 0)
  {
    El.text(NewVaue);
  }
}


function UpdatePilotBadge(Boat)
{
  var index;
  var PendingOrdersCount = 0;

  if ((typeof Boat === "undefined") || (!Boat))
  {
    return;
  }

  var Pilot = Boat.VLMInfo.PIL;

  if (typeof Pilot !== "undefined" && Pilot && Pilot.length)
  {
    for (index in Pilot)
    {
      if (Pilot[index].STS === "pending")
      {
        PendingOrdersCount++;
      }
    }
  }

  if (PendingOrdersCount > 0)
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
    $("#pref_boatname").val(Boat.BoatName());

    if (typeof Boat.VLMInfo !== 'undefined')
    {
      SelectCountryDDFlag(Boat.VLMInfo.CNT);
      var ColString = SafeHTMLColor(Boat.VLMInfo.COL);

      $("#pref_boatcolor").val(ColString);
      $("#cp11").colorpicker(
      {
        useAlpha: false,
        format: false,
        color: ColString
      });
    }
  }

}

class RaceSorterclass
{
  constructor(OldRacesSortMode)
  {
    this.OldRacesSortMode = 1;
    if (OldRacesSortMode)
    {
      this.OldRacesSortMode = -1;
    }
    this.sort = function(r1, r2)
    {
      if (r1.CanJoin === r2.CanJoin)
      {
        if (r1.deptime > r2.deptime)
        {
          return 1 * this.OldRacesSortMode;
        }
        else if (r1.deptime === r2.deptime)
        {
          if (r1.racename > r2.racename)
          {
            return 1 * this.OldRacesSortMode;
          }
          else if (r1.racename === r2.racename)
          {
            return 0;
          }
          else
          {
            return -1 * this.OldRacesSortMode;
          }
        }
        else
        {
          return -1 * this.OldRacesSortMode;
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
    };
  }
}


function FillRacesListForm(e)
{
  let Count = null;
  if (e && e.currentTarget && e.currentTarget.value)
  {
    Count = 200;
  }
  LoadRacesList(Count);
}

var OldRaceArray = [];
var FilterTimerHandler = null;

function HandleRacesListFilterChange(e)
{
  if (FilterTimerHandler)
  {
    clearTimeout(FilterTimerHandler);
  }
  FilterTimerHandler = setTimeout(function(f)
  {
    RaceListFilterChange(e);
  }, 300);
}

function RaceListFilterChange(e)
{
  let t = e.currentTarget;
  //let StartTick = new Date().getTime();
  let Races = $(".raceheaderline");
  if (t)
  {
    $("#RaceListPanel").empty();
    let filter = t.value.trim().toLowerCase();
    let FilterRaces = function(r1)
    {
      let raceid = $(Races[r1]).attr("racelistid");
      let r = OldRaceArray[OldRaceArray.findIndex(function(e)
      {
        return e.idraces == raceid;
      })];

      let visible = filter === "" || r.racename.toLowerCase().includes(filter);

      if (visible)
      {
        $(Races[r1]).removeClass("hidden");
      }
      else
      {
        $(Races[r1]).addClass("hidden");
      }
    };


    Races.map(FilterRaces);
    $("#RaceListPanel").append(Races);
  }

  //let EndTick = new Date().getTime();
  //console.log(OldRaceArray.length + " rows search in " + (EndTick - StartTick) + " µs");

}

function LoadRacesList(e)
{
  let MaxFinishedRacesLength = 0;
  let CurUser = _CurPlayer.CurBoat.IdBoat();
  let CurLength = 0;
  let filter = $("#RacesFilterText").val();
  $('.racelistpreloader').removeClass("hidden");
  $('#BtnMoreOldRaces').addClass("hidden");

  if (e)
  {
    CurLength = parseInt($('#BtnMoreOldRaces').addClass("hidden").attr("PageLength"), 10);

    if (typeof CurLength === "undefined" || isNaN(CurLength))
    {
      CurLength = 0;
      $("#RaceListPanel").empty();
      $('#BtnMoreOldRaces').removeClass("hidden").attr("PageLength", 100);
    }
    else if (CurLength == -1)
    {
      return;
    }
    MaxFinishedRacesLength = e + CurLength;
  }
  else
  {
    $("#RaceListPanel").empty();
    OldRaceArray = [];
    $(".RaceListTab").removeClass("Active");
    $(".RaceListTab [value=0]").addClass("Active");
    $('#BtnMoreOldRaces').attr("PageLength", 100);
  }
  $.get("/ws/raceinfo/list.php?iduser=" + CurUser + "&OldRaces=" + MaxFinishedRacesLength + "&v=" + (new Date().getTime()),
    function(result)
    {
      let racelist = result;
      let AddedRace = false;
      let FileRaceCount = 0;
      let FileRaceAdded = 0;

      for (let index in racelist)
      {

        if (racelist[index])
        {
          /*jshint -W083 */
          let find = function(r)
          {
            return r.idraces === racelist[index].idraces;
          };
          /*jshint +W083 */

          if (OldRaceArray.findIndex(find) === -1)
          {
            OldRaceArray.push(racelist[index]);
            AddedRace = true;
            FileRaceAdded += 1;
          }
        }
        FileRaceCount += 1;
      }

      let RaceSorter = new RaceSorterclass(e !== null);

      OldRaceArray.sort(RaceSorter.sort.bind(RaceSorter));

      for (let index in OldRaceArray)
      {
        if (OldRaceArray[index] && !OldRaceArray[index].Loaded)
        {
          OldRaceArray[index].Loaded = true;
          AddRaceToList(OldRaceArray[index], filter);
        }
      }
      console.log("RaceList COunters" + FileRaceCount + "/" + FileRaceAdded + "/" + OldRaceArray.length);
      $('.racelistpreloader').addClass("hidden");
      if (e && AddedRace)
      {
        $('#BtnMoreOldRaces').removeClass("hidden").attr("PageLength", CurLength + e);
      }
      else
      {
        $('#BtnMoreOldRaces').attr("PageLength", 0);
      }

      // Resize button height to be uniform with highest one.      
      let highestBox = 0;
      $('#RaceListPanel .btn-group .btn-md').each(function()
      {
        if ($(this).height() > highestBox)
        {
          highestBox = $(this).height();
        }
      });
      $('#RaceListPanel .btn-group .btn-md').height(highestBox);


    }
  );
}

function AddRaceToList(race, filter)
{
  let base = $("#RaceListPanel").first();


  let d = new Date(0); // The 0 there is the key, which sets the date to the epoch
  //d.setUTCSeconds(utcSeconds);
  let RaceJoinStateClass;
  let StartMoment;
  let RecordRace = ((race.racetype & RACE_TYPE_RECORD) == RACE_TYPE_RECORD);
  let RaceTerminated = race.started === -1;
  let RaceHidden = false;
  if (filter)
  {
    if (!race.racename.toLowerCase().includes(filter.toLowerCase()))
    {
      RaceHidden = true;
    }
  }

  if (_CurPlayer && _CurPlayer.CurBoat && _CurPlayer.CurBoat.RaceInfo && _CurPlayer.CurBoat.RaceInfo.idraces)
  {
    race.CanJoin = race.CanJoin & ("0" === _CurPlayer.CurBoat.RaceInfo.idraces);
  }

  //if (race.CanJoin)
  {
    let Now = new Date();
    let RaceStart = new Date(race.deptime * 1000);
    let RaceLineClose = new Date(race.closetime * 1000);

    if (RaceLineClose <= Now)
    {
      RaceJoinStateClass = 'NoJoinRace';
    }
    else if (RaceStart <= Now)
    {
      RaceJoinStateClass = 'CanJoinRace';
      StartMoment = GetLocalizedString("closerace") + " " + moment("/date(" + race.closetime * 1000 + ")/").fromNow();
    }
    else
    {
      RaceJoinStateClass = 'CanJoinRaceNotStarted';
      StartMoment = GetLocalizedString("departuredate") + " " + moment("/date(" + race.deptime * 1000 + ")/").fromNow();
    }
  }
  /*else
  {
    RaceJoinStateClass = 'NoJoinRace';
  }*/

  let BoatType = "???";

  if (race.boattype)
  {
    BoatType = race.boattype.substring(5);
  }

  let code = '<div class="raceheaderline panel panel-default ' + RaceJoinStateClass + ' ' + (RaceHidden ? "hidden" : "") + '" racelistid="' + race.idraces + '">' +
    '  <div data-toggle="collapse" href="#RaceDescription' + race.idraces + '" class="panel-body collapsed " data-parent="#RaceListPanel" aria-expanded="false">' +
    '    <div class="row">' +
    '      <div class="col-xs-3">' +
    '        <img class="racelistminimap" src="/cache/minimaps/' + race.idraces + '.png" ></img>' +
    '      </div>' +
    '      <div class="col-xs-9">' +
    '        <div class="row">' +
    (RecordRace ? '<span class="PRecordRace">P</span>' : '') +
    '          <span ">' + race.racename + (race.racelength !== "0" ? ' (' + race.racelength + ' Nm)' : '') + (race.engaged ? ' (' + race.racing + '/' + race.engaged + ')' : '') +
    '          </span>' +
    '        </div>' +
    '        <div class="btn-group col-xs-12">' +
    '          <button id="JoinRaceButton" type="button" class="' + (race.CanJoin ? '' : 'hidden') + ' BtnRaceList btn-default btn-sm col-xs-4 col-md-3" IdRace="' + race.idraces + '"  >' + GetLocalizedString("subscribe") +
    '          </button>' +
    '          <button id="SpectateRaceButton" type="button" class="' + (RaceTerminated ? 'hidden' : '') + ' BtnRaceList  ShowRaceInSpectatorMode btn-default btn-sm col-xs-4 col-md-3" IdRace="' + race.idraces + '"  >' + GetLocalizedString("Spectator") +
    '          </button>' +
    '          <button type="button" class="ShowICSButton btn-default BtnRaceList btn-sm col-xs-4 col-md-3" IdRace="' + race.idraces + '"  >' + GetLocalizedString('ic') +
    '          </button>' +
    '          <button type="button" class="RankingButton btn-default BtnRaceList btn-sm col-xs-4 col-md-3" IdRace="' + race.idraces + '"  >' + GetLocalizedString('ranking') +
    '          </button>' +
    '        </div>' +
    (StartMoment ?
      '      <div class="row">' +
      '       <span "> ' + StartMoment +
      '       </span>' +
      '      </div>' : "") +

    '      </div>' +
    '    </div>' +
    '  </div>' +
    '  <div id="RaceDescription' + race.idraces + '" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">' +
    '    <div class="panel-body">' +
    '      <div class="col-xs-12"><img class="img-responsive" src="/cache/racemaps/' + race.idraces + '.png" width="530px"></div>' +
    '        <div class="col-xs-12"><p>' + GetLocalizedString('race') + ' : ' + race.racename + '</p>' +
    '          <p>Départ : ' + GetLocalUTCTime(race.deptime * 1000, true, true) + '</p>' +
    '          <p>' + GetLocalizedString('boattype') + ' : ' + BoatType + '</p>' +
    '          <p>' + GetLocalizedString('crank') + ' : ' + race.vacfreq + '\'</p>' +
    '          <p>' + GetLocalizedString('locktime') + parseInt(race.coastpenalty, 10) / 60.0 + ' \'</p>' +
    '          <p>' + GetLocalizedString('closerace') + GetLocalUTCTime(race.closetime * 1000, true, true) + '</p>' +
    (!RecordRace ?
      (race.RaceCloseDate ?
        '          <p>' + GetLocalizedString('endrace') + GetLocalUTCTime(race.RaceCloseDate * 1000, true, true) + '</p>' : '          <p>' + GetLocalizedString('endrace') + (100 + race.firstpcttime) + '%') + '</p>' : '') +
    '        </div>' +
    '      </div>' +
    '    </div>' +
    '  </div>';

  base.prepend(code);

  // Handler for the join race button
  $("#JoinRaceButton").click(
    function(e)
    {
      var RaceId = e.currentTarget.attributes.idrace.value;

      EngageBoatInRace(RaceId, _CurPlayer.CurBoat.IdBoat());


    }
  );

}

function PageClock()
{

  if (typeof _CurPlayer !== "undefined" && _CurPlayer && typeof _CurPlayer.CurBoat !== "undefined")
  {

    // Display race clock if a racing boat is selected
    let CurBoat = _CurPlayer.CurBoat;

    if (typeof CurBoat !== "undefined" && typeof CurBoat.RaceInfo !== "undefined")
    {
      let ClockValue = GetRaceClock(CurBoat.RaceInfo, CurBoat.VLMInfo.UDT);
      let Chrono = $(".RaceChrono");
      if (ClockValue < 0)
      {
        Chrono.removeClass("ChronoRaceStarted").addClass("ChronoRacePending");
      }
      else
      {
        Chrono.addClass("ChronoRaceStarted").removeClass("ChronoRacePending");
      }

      $("#RefreshAge").text(moment(_CurPlayer.CurBoat.LastRefresh).fromNow());

      var LastBoatUpdate = new Date(CurBoat.VLMInfo.LUP * 1000);
      var TotalVac = CurBoat.VLMInfo.VAC;
      var TimeToNextUpdate = TotalVac - ((new Date() - LastBoatUpdate) / 1000) % TotalVac;
      var Delay = 1000;
      if (TimeToNextUpdate >= TotalVac - 1)
      {
        Delay = 100;
      }
      $("#pbar_innerdivvac").css("width", +Math.round((TimeToNextUpdate % 60) * 100.0 / 60.0) + "px");
      $("#pbar_innerdivmin").css("width", Math.round((TimeToNextUpdate / TotalVac) * 100.0) + "px");

      if (CurBoat.VLMInfo && CurBoat.VLMInfo['S&G'])
      {
        let PenEnd = moment(CurBoat.VLMInfo['S&G'] * 1000);
        let CurTick = moment();
        if (CurTick < PenEnd)
        {
          $("#PenaltyDuration").text(CurTick.to(PenEnd));
          $(".RaceChrono").addClass("hidden");
          $("#PenaltyBadge").removeClass("hidden");
        }
        else
        {
          $("#PenaltyBadge").addClass("hidden");
          $(".RaceChrono").removeClass("hidden");
        }
      }
      else
      {
        $("#PenaltyBadge").addClass("hidden");
        $(".RaceChrono").removeClass("hidden");
      }
      Chrono.text(GetFormattedChronoString(ClockValue));
    }
  }
}

function GetRaceClock(RaceInfo, UserStartTimeString)
{
  var CurDate = new Date();
  var Epoch = new Date(RaceInfo.deptime * 1000);

  if (!(RaceInfo.racetype & RACE_TYPE_RECORD))
  {
    // non Permanent race chrono counts from race start time
    return Math.floor((CurDate - Epoch) / 1000);
  }
  else
  {
    var UDT = parseInt(UserStartTimeString, 10);

    if (UDT === -1)
    {
      return 0;
    }
    else
    {
      var StartDate = new Date(UDT * 1000);
      return Math.floor((CurDate - StartDate) / 1000);
    }
  }


}

function DisplayCurrentDDSelectedBoat(Boat)
{
  $('.BoatDropDown:first-child').html(
    '<span BoatID=' + Boat.IdBoat() + '>' + GetBoatInfoLine(Boat, Boat.IdBoat() in _CurPlayer.Fleet) + '</span>' +
    '<span class="caret"></span>'
  );
}

function PadLeftZero(v)
{
  if (v < 100)
  {
    return ("00" + v).slice(-2);
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
  var Min = PadLeftZero(Math.floor(Value / 60) % 60);
  var Hrs = PadLeftZero(Math.floor(Value / 3600) % 24);
  var Days = PadLeftZero(Math.floor(Value / 3600 / 24));

  var Ret = Hrs.toString() + ":" + Min.toString() + ":" + Sec.toString();
  if (Days > 0)
  {
    Ret = Days.toString() + " d " + Ret;
  }
  return Ret;
}

function RefreshCurrentBoat(SetCenterOnBoat, ForceRefresh, TargetTab)
{
  let BoatIDSpan = $('.BoatDropDown > span');

  if (typeof BoatIDSpan !== "undefined" && typeof BoatIDSpan[0] !== "undefined" && ('BoatId' in BoatIDSpan[0].attributes || 'boatid' in BoatIDSpan[0].attributes))
  {
    let BoatId = BoatIDSpan[0].attributes.BoatID.value;

    SetCurrentBoat(GetBoatFromIdu(BoatId), SetCenterOnBoat, ForceRefresh, TargetTab);
  }

}

function UpdateLngDropDown()
{
  // Init the language combo to current language
  var lng = GetCurrentLocale();

  $('#SelectionLanguageDropDown:first-child').html(
    '<img class=" LngFlag" lang="' + lng + '" src="images/lng-' + lng + '.png" alt="' + lng + '">' +
    '<span class="caret"></span>'
  );

}

var _CurAPOrder = null;

function HandleOpenAutoPilotSetPoint(e)
{
  var Target = e.target;
  var TargetId;

  if ('id' in Target.attributes)
  {
    TargetId = Target.attributes.id.nodeValue;
  }
  else if ('class' in Target.attributes)
  {
    TargetId = Target.attributes["class"].nodeValue;
  }
  else
  {
    VLMAlert("Something bad has happened reload this page....", "alert-danger");
    return;
  }
  switch (TargetId)
  {
    case "AutoPilotAddButton":
      // Create a new autopilot order
      _CurAPOrder = new AutoPilotOrder();
      break;
    case "PIL_EDIT":
      // Load AP Order from vlminfo structure
      var OrderIndex = parseInt(Target.attributes.pil_id.value, 10);
      _CurAPOrder = new AutoPilotOrder(_CurPlayer.CurBoat, OrderIndex);

      $("#AutoPilotSettingForm").modal('show');
      break;
    default:
      VLMalert("Something bad has happened reload this page....", "alert-danger");
      return;

  }

  RefreshAPDialogFields();

}

function RefreshAPDialogFields()
{
  // Update dialog content from APOrder object
  InitDateTimePicker();
  $("#AP_Time").data('DateTimePicker').date(_CurAPOrder.Date);

  $('#AP_PIM:first-child').html(
    '<span>' + _CurAPOrder.GetPIMString() + '</span>' +
    '<span class="caret"></span>'
  );
  $("#AP_PIP").val(_CurAPOrder.PIP_Value);
  $("#AP_WPLat").val(_CurAPOrder.PIP_Coords.Lat.Value);
  $("#AP_WPLon").val(_CurAPOrder.PIP_Coords.Lon.Value);
  $("#AP_WPAt").val(_CurAPOrder.PIP_WPAngle);


  UpdatePIPFields(_CurAPOrder.PIM);

}

function HandleDateChange(ev)
{
  _CurAPOrder.Date = ev.date;
}

function HandleClickToSetWP()
{
  SetWPPending = true;
  WPPendingTarget = "AP";
  $("#AutoPilotSettingForm").modal("hide");
}

function HandleAPModeDDClick(e)
{
  var NewMode = e.target.attributes.PIM.value;

  _CurAPOrder.PIM = parseInt(NewMode, 10);
  $('#AP_PIM:first-child').html(
    '<span>' + _CurAPOrder.GetPIMString() + '</span>' +
    '<span class="caret"></span>'
  );

  UpdatePIPFields(_CurAPOrder.PIM);

}

function UpdatePIPFields(PIM)
{
  var IsPip = true;
  switch (PIM)
  {
    case PM_HEADING:
    case PM_ANGLE:
      IsPip = true;
      break;
    case PM_ORTHO:
    case PM_VMG:
    case PM_VBVMG:
      IsPip = false;
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
  var NewVals = {};
  var BoatUpdateRequired = false;
  var PlayerUpdateRequired = false;

  // Get Theme
  var NewTheme = $("#SelectionThemeDropDown").attr("SelTheme");

  if (typeof NewTheme !== "undefined")
  {
    VLM2Prefs.CurTheme = NewTheme;

  }
  VLM2Prefs.AdvancedStats = $("#AdvancedStats").is(":checked");
  VLM2Prefs.Save();

  if (!ComparePrefString($("#pref_boatname")[0].value, _CurPlayer.CurBoat.BoatName()))
  {
    NewVals.boatname = encodeURIComponent($("#pref_boatname")[0].value);
    BoatUpdateRequired = true;
  }

  if (!ComparePrefString($("#pref_boatcolor")[0].value, SafeHTMLColor(_CurPlayer.CurBoat.VLMInfo.COL)))
  {
    NewVals.color = $("#pref_boatcolor")[0].value.substring(1);
    BoatUpdateRequired = true;
  }

  var NewCountry = GetPrefSelFlag();
  if (!ComparePrefString(NewCountry, _CurPlayer.CurBoat.VLMInfo.CNT))
  {
    NewVals.country = encodeURIComponent(NewCountry);
    BoatUpdateRequired = true;
  }

  //NewVals["country"]=$("#FlagSelector")[0].value;
  //NewVals["color"]=$("#pref_boatcolor")[0].value;
  if (BoatUpdateRequired && typeof _CurPlayer !== "undefined" && _CurPlayer)
  {
    UpdateBoatPrefs(_CurPlayer.CurBoat,
    {
      prefs: NewVals
    });
  }
}

function GetPrefSelFlag()
{
  var Item = $('#CountryDropDown:first-child [flag]')[0];
  return Item.attributes.flag.value;

}

function ComparePrefString(Obj1, Obj2)
{

  return Obj1.toString() === Obj2.toString();
}

function SelectCountryDDFlag(Country)
{
  $('#CountryDropDown:first-child').html('<div>' + GetCountryDropDownSelectorHTML(Country, false) + '<span class="caret"></span></div>');

}

function ResetCollapsiblePanels(e)
{
  $(".collapse").collapse("hide");

}

function HandleBoatSelectionChange(e)
{

  let BoatId = $(e.target).closest('li').attr('BoatID');
  if (!BoatId)
  {
    return;
  }
  else
  {
    BoatId = parseInt(BoatId, 10);
  }
  let Boat = GetBoatFromIdu(BoatId);
  ResetCollapsiblePanels();

  if (typeof Boat === "undefined" || !Boat)
  {
    VLMAlertDanger(GetLocalizedString('Error Reload'));
    return;
  }
  SetCurrentBoat(Boat, true, false);
  DisplayCurrentDDSelectedBoat(Boat);
}

var LastMouseMoveCall = 0;
var ShowEstTimeOutHandle = null;

function HandleMapMouseClick(e)
{
  if (SetWPPending)
  {
    if (WPPendingTarget === "WP")
    {
      CompleteWPSetPosition(e);
      HandleCancelSetWPOnClick();
    }
    else if (WPPendingTarget === "AP")
    {
      SetWPPending = false;
      _CurAPOrder.PIP_Coords = new VLMPosition(e.latlng.lng, e.latlng.lat);
      $("#AutoPilotSettingForm").modal("show");
      RefreshAPDialogFields();

    }
    else
    {
      SetWPPending = false;
    }
  }
}

function HandleMapMouseMove(e)
{
  let LatLng = e.latlng;
  if ((typeof _CurPlayer !== "undefined") && _CurPlayer && (typeof _CurPlayer.CurBoat !== 'undefined') && (typeof _CurPlayer.CurBoat.VLMInfo !== "undefined"))
  {
    var Pos = new VLMPosition(LatLng.lng, LatLng.lat);
    var CurPos = new VLMPosition(_CurPlayer.CurBoat.VLMInfo.LON, _CurPlayer.CurBoat.VLMInfo.LAT);
    var WPPos = _CurPlayer.CurBoat.GetNextWPPosition();
    var EstimatePos = null;
    var Estimated = new Date() - LastMouseMoveCall > 300;

    if (VLM2Prefs.MapPrefs.EstTrackMouse && Estimated)
    {
      // Throttle estimate update to 3/sec
      EstimatePos = _CurPlayer.CurBoat.GetClosestEstimatePoint(Pos);
      LastMouseMoveCall = new Date();
      clearTimeout(ShowEstTimeOutHandle);
      StartEstimateTimeout();
    }


    $("#MI_Lat").text(Pos.Lat.toString());
    $("#MI_Lon").text(Pos.Lon.toString());
    $("#MI_LoxoDist").text(CurPos.GetLoxoDist(Pos, 2) + " nM");
    $("#MI_OrthoDist").text(CurPos.GetOrthoDist(Pos, 2) + " nM");
    $("#MI_Loxo").text(CurPos.GetLoxoCourse(Pos, 2) + " °");
    $("#MI_Ortho").text(CurPos.GetOrthoCourse(Pos, 2) + " °");

    if (typeof WPPos !== "undefined" && WPPos)
    {
      $("#MI_WPLoxoDist").text(WPPos.GetLoxoDist(Pos, 2) + " nM");
      $("#MI_WPOrthoDist").text(WPPos.GetOrthoDist(Pos, 2) + " nM");
      $("#MI_WPLoxo").text(WPPos.GetLoxoCourse(Pos, 2) + " °");
      $("#MI_WPOrtho").text(WPPos.GetOrthoCourse(Pos, 2) + " °");
    }
    else
    {
      $("#MI_WPLoxoDist").text("--- nM");
      $("#MI_WPOrthoDist").text("--- nM");
      $("#MI_WPLoxo").text("--- °");
      $("#MI_WPOrtho").text("--- °");
    }

    if (GribMgr)
    {
      let m = "-- N/A --";
      let GribAgeText = "-- N/A --";
      let GribSpanText = "-- N/A --";

      if (GribMgr.LastGribDate)
      {
        m = moment("/date(" + GribMgr.LastGribDate * 1000 + ")/").fromNow();
        let ts_start = moment("/date(" + GribMgr.TableTimeStamps[0] * 1000 + ")/");
        let ts_end = moment("/date(" + GribMgr.TableTimeStamps[GribMgr.TableTimeStamps.length - 1] * 1000 + ")/");
        let span = moment.duration(ts_end.diff(ts_start));
        GribAgeText = GetLocalUTCTime(ts_start.add(3.5, "h"), true, true);
        GribSpanText = "" + span.asHours() + " h";

        let now = new Date().getTime() / 1000;
        if ((now - ts_start.local().unix()) > 7 * 3600)
        {
          $("#GribLoadOK").addClass("GribNotOK");
        }
        else if ((now - ts_start.local().unix()) > 6 * 3600)
        {
          $("#GribLoadOK").addClass("GribGetsOld");
        }
        else
        {
          $("#GribLoadOK").removeClass("GribNotOK").removeClass("GribGetsOld");
        }
      }

      $("#MI_SrvrGribAge").text(m);
      $("#MI_LocalGribAge").text(GribAgeText);
      $("#MI_LocalGribSpan").text(GribSpanText);


    }

    if (Estimated)
    {
      RefreshEstPosLabels(EstimatePos);
    }

  }
}

function StartEstimateTimeout()
{
  ShowEstTimeOutHandle = setTimeout(function()
  {
    _CurPlayer.CurBoat.GetClosestEstimatePoint(null);
    RefreshEstPosLabels(null);

  }, 5000);
}

function RefreshEstPosLabels(Pos)
{
  if (Pos && typeof Pos.Date !== "undefined")
  {
    $("#MI_EstDate").text(GetLocalUTCTime(Pos.Date, false, true));
  }
  else
  {
    $("#MI_EstDate").text("");
  }
}

function GetWPrankingLI(WPInfo)
{
  return '<li id="RnkWP' + WPInfo.wporder + '" RnkSort="WP" WPRnk="' + WPInfo.wporder + '"><a href="#DivRnkRAC" RnkSort="WP" WPRnk="' + WPInfo.wporder + '">WP ' + WPInfo.wporder + ' : ' + WPInfo.libelle + '</a></li>';
}

function ResetRankingWPList(e)
{
  $("[WPRnk]").remove();
  $("#RnkTabsUL").addClass("WPNotInited");

}

function CheckWPRankingList(Boat, OtherRaceWPs)
{
  let InitNeeded = $(".WPNotInited");
  let RaceId = GetRankingRaceId(Boat);
  let InitComplete = false;

  if (typeof InitNeeded !== "undefined" && InitNeeded && RaceId)
  {

    let index;

    if (typeof Boat !== "undefined" && Boat && (typeof Boat.RaceInfo !== "undefined") && Boat.RaceInfo && (RaceId === Boat.RaceInfo.RaceId))
    {
      BuildWPTabList(index, InitNeeded);
      InitComplete = true;
    }
    else if (typeof OtherRaceWPs === "object" && OtherRaceWPs)
    {
      BuildWPTabList(OtherRaceWPs, InitNeeded);
      InitComplete = true;
    }
    else
    {
      let Version = 0;

      if (typeof Boat !== "undefined" && Boat && typeof Boat.VLMInfo !== "undefined")
      {
        Version = Boat.VLMInfo.VER;
      }
      $.get("/ws/raceinfo/desc.php?idrace=" + RaceId + "&v=" + Version,
        function(result)
        {
          CheckWPRankingList(Boat, result);
        }
      );
    }

  }

  if (InitComplete)
  {
    $(InitNeeded).removeClass("WPNotInited");
    $(".JVLMTabs").tabs("refresh");
  }

}

function BuildWPTabList(WPInfos, TabsInsertPoint)
{
  let index;

  if (typeof TabsInsertPoint === "undefined" || !TabsInsertPoint)
  {
    return;
  }
  if (typeof WPInfos === "undefined" || !WPInfos)
  {
    WPInfos = Boat.RaceInfo.races_waypoints;
  }

  for (index in WPInfos.races_waypoints)
  {
    if (WPInfos.races_waypoints[index])
    {
      let WPInfo = WPInfos.races_waypoints[index];
      let html = GetWPrankingLI(WPInfo);
      $(TabsInsertPoint).append(html);
    }

  }

}


function SortRanking(style, WPNum, OtherRaceWPs)
{
  let Boat = null;
  //$('#RankingTableBody').empty();
  if (typeof _CurPlayer !== "undefined" && _CurPlayer)
  {
    Boat = _CurPlayer.CurBoat;
  }
  CheckWPRankingList(Boat, OtherRaceWPs);

  // Fix Me use logged player (if any to avoid that)
  //if (typeof Boat === "undefined" || !Boat)
  //{
  //  return;
  //}

  let Friends = null;
  if (typeof Boat !== "undefined" && Boat && Boat.VLMPrefs && Boat.VLMPrefs.mapPrefOpponents)
  {
    Friends = Boat.VLMPrefs.mapPrefOpponents.split(",");
  }


  switch (style)
  {
    case "WP":
      SetRankingColumns(style);
      WPNum = parseInt(WPNum, 10);
      SortRankingData(Boat, style, WPNum);
      FillWPRanking(Boat, WPNum, Friends);
      break;

    case 'DNF':
    case 'HC':
    case 'ARR':
    case 'HTP':
    case 'ABD':
      SetRankingColumns(style);
      SortRankingData(Boat, style);
      FillStatusRanking(Boat, style, Friends);
      break;
      //case 'RAC':
    default:
      SetRankingColumns('RAC');
      SortRankingData(Boat, 'RAC');
      FillRacingRanking(Boat, Friends);

  }

}

function SetRankingColumns(style)
{
  switch (style)
  {
    case "WP":
      SetWPRankingColumns();
      break;

    case 'DNF':
    case 'HC':
    case 'ARR':
    case 'HTP':
    case 'ABD':
      SetNRClassRankingColumns();
      break;
      //case 'RAC':
    default:
      SetRacingClassRankingColumns();

  }
}

let RACColumnHeader = ["Rank", "Name", "Distance", "Time", "Loch", "Lon", "Lat", "Last1h", "Last3h", "Last24h", "Delta1st"];
let NRColumnHeader = ["Rank", "Name", "Distance"];
let WPColumnHeader = ["Rank", "Name", "Time", "Loch"];
let RACColumnHeaderLabels = ["ranking", "boatname", "distance", "racingtime", "Loch", "Lon", "Lat", "Last1h", "Last3h", "Last24h", "ecart"];
let NRColumnHeaderLabels = ["ranking", "boatname", "status"];
let WPColumnHeaderLabels = ["ranking", "boatname", "racingtime", "ecart"];


function SetRacingClassRankingColumns()
{

  SetColumnsVisibility(RACColumnHeader, RACColumnHeaderLabels);

}

function SetNRClassRankingColumns()
{
  SetColumnsVisibility(NRColumnHeader, NRColumnHeaderLabels);
}

function SetWPRankingColumns()
{
  SetColumnsVisibility(WPColumnHeader, WPColumnHeaderLabels);
}

function SetColumnsVisibility(cols, labels)
{
  let index;
  for (index = 0; index < RankingFt.columns.array.length; index++)
  {
    if (RankingFt.columns.array[index])
    {
      let ColIdx = cols.indexOf(RankingFt.columns.array[index].name);
      if (ColIdx > -1)
      {
        //RankingFt.columns.array[index].title = GetLocalizedString( labels[ColIdx])
        $("[data-name='" + cols[ColIdx] + "']").attr("I18n", labels[ColIdx]);

      }
      RankingFt.columns.array[index].visible = ColIdx > -1;

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
  return BoatArrivedStatus.indexOf(rnk.status) !== -1;
}

function RnkIsRacing(rnk)
{
  if (typeof rnk === "undefined" || typeof rnk.status === "undefined" || !rnk.status)
  {
    return false;
  }
  return BoatRacingStatus.indexOf(rnk.status) !== -1;
}

function Sort2ArrivedBoats(rnk1, rnk2)
{
  let Total1 = parseInt(rnk1.duration, 10) + parseInt(rnk1.penalty, 10);
  let Total2 = parseInt(rnk2.duration, 10) + parseInt(rnk2.penalty, 10);

  if (Total1 > Total2)
  {
    DebugRacerSort(rnk1, rnk2, 1);
    return 1;
  }
  else if (Total1 < Total2)
  {
    DebugRacerSort(rnk1, rnk2, -1);
    return -1;
  }
  else
  {
    DebugRacerSort(rnk1, rnk2, 0);
    return 0;
  }
}

function Sort2RacingBoats(rnk1, rnk2)
{
  let nwp1 = parseInt(rnk1.nwp, 10);
  let nwp2 = parseInt(rnk2.nwp, 10);

  if (nwp1 === nwp2)
  {
    let dnm1 = parseFloat(rnk1.dnm);
    let dnm2 = parseFloat(rnk2.dnm);

    if (dnm1 > dnm2)
    {
      DebugRacerSort(rnk1, rnk2, 1);
      return 1;
    }
    else if (dnm1 === dnm2)
    {
      DebugRacerSort(rnk1, rnk2, 0);
      let SortFlag = ((rnk1.country > rnk2.country) ? 1 : ((rnk1.country === rnk2.country) ? 0 : -1));

      if (SortFlag)
      {
        return SortFlag;
      }
      else
      {
        let SortIdu = ((rnk1.idusers > rnk2.idusers) ? 1 : ((rnk1.idusers === rnk2.idusers) ? 0 : -1));
        return SortIdu;
      }
    }
    else
    {
      DebugRacerSort(rnk1, rnk2, -1);
      return -1;
    }

  }
  else if (nwp1 > nwp2)
  {
    DebugRacerSort(rnk1, rnk2, -1);
    return -1;
  }
  else
  {
    DebugRacerSort(rnk1, rnk2, 1);
    return 1;
  }

}

function GetWPDuration(Rnk, WPNum)
{
  if (Rnk && Rnk.WP && Rnk.WP[WPNum - 1] && Rnk.WP[WPNum - 1].duration)
  {
    return parseInt(Rnk.WP[WPNum - 1].duration, 10);
  }
  else
  {
    return 9999999999;
  }
}

function WPRaceSort(index)
{
  return function(a, b)
  {
    let wp1 = GetWPDuration(a, index);
    let wp2 = GetWPDuration(b, index);

    return wp1 - wp2;
  };
}

function RacersSort(rnk1, rnk2)
{

  if (RnkIsRacing(rnk1) && RnkIsRacing(rnk2))
  {
    return Sort2RacingBoats(rnk1, rnk2);
  }
  else if (RnkIsArrived(rnk1) && RnkIsArrived(rnk2))
  {
    return Sort2ArrivedBoats(rnk1, rnk2);
  }
  else if (RnkIsArrived(rnk1))
  {
    DebugRacerSort(rnk1, rnk2, -1);
    return -1;
  }
  else if (RnkIsArrived(rnk2))
  {
    DebugRacerSort(rnk1, rnk2, 1);
    return 1;
  }
  else if (RnkIsRacing(rnk1))
  {
    DebugRacerSort(rnk1, rnk2, 1);
    return -1;
  }
  else if (RnkIsRacing(rnk2))
  {
    DebugRacerSort(rnk1, rnk2, 1);
    return 1;
  }
  else
  {
    return Sort2NonRacing(rnk1, rnk2);
  }
}

let DebugCount = 1;

function DebugRacerSort(rnk1, rnk2, res)
{
  let debug = false;

  if (debug)
  {
    console.log((DebugCount++) + "sort " + rnk1.idusers + " vs " + rnk2.idusers + " =>" + res);
  }

}

function Sort2NonRacing(rnk1, rnk2)
{

  if (typeof rnk1.idusers !== "undefined" && typeof rnk2.idusers !== "undefined")
  {
    let SortFlag = ((rnk1.country > rnk2.country) ? 1 : ((rnk1.country === rnk2.country) ? 0 : -1));

    if (SortFlag)
    {
      return SortFlag;
    }
    else
    {
      let IdUser1 = parseInt(rnk1.idusers, 10);
      let IdUser2 = parseInt(rnk2.idusers, 10);

      if (IdUser1 > IdUser2)
      {
        DebugRacerSort(rnk1, rnk2, 1);
        return 1;
      }
      else if (IdUser1 < IdUser2)
      {
        DebugRacerSort(rnk1, rnk2, -1);
        return -1;
      }
      else
      {
        DebugRacerSort(rnk1, rnk2, 0);
        return 0;
      }
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
    let ar = [rnk1, rnk2];
    ar.sort();

    if (ar[0] === rnk1)
    {
      return 1;
    }
    else
    {
      return -1;
    }
  }
}

function GetRankingRaceId(Boat, RaceId)
{
  if (!RaceId && !RankingFt.RaceRankingId)
  {
    return Boat.Engaged();
  }
  else if (!RaceId)
  {
    return RankingFt.RaceRankingId;
  }
  else
  {
    return RaceId;
  }
}

function SortRankingData(Boat, SortType, WPNum, RaceId)
{

  RaceId = GetRankingRaceId(Boat, RaceId);

  if (!Boat && !Rankings[RaceId])
  {
    return;
  }


  if (Rankings && Rankings[RaceId] &&
    (typeof Rankings[RaceId].RacerRanking === "undefined")) //|| Rankings[RaceId].RacerRanking.length !== Rankings[RaceId]+1))
  {
    let index;

    Rankings[RaceId].RacerRanking = [];

    for (index in Rankings[RaceId])
    {
      if (Rankings[RaceId][index])
      {
        //Rankings[index].idusers=index;
        Rankings[RaceId].RacerRanking.push(Rankings[RaceId][index]);
      }
    }
  }

  switch (SortType)
  {
    case "WP":
      Rankings[RaceId].RacerRanking.sort(WPRaceSort(WPNum));
      break;

    case 'RAC':
    case 'DNF':
    case 'HC':
    case 'HTP':
    case 'ABD':
    case 'ARR':
      if (typeof Rankings !== "undefined" && Rankings[RaceId] && Rankings[RaceId].RacerRanking)
      {
        Rankings[RaceId].RacerRanking.sort(RacersSort);
      }
      break;

    default:
      VLMAlertInfo("unexpected sort option : " + SortType);

  }

  let rnk = 1;
  let index = 0;


  if (Boat && Boat.IdBoat && Rankings && Rankings[RaceId])
  {
    for (index in Rankings[RaceId].RacerRanking)
    {
      if (Rankings[RaceId].RacerRanking[index] && Boat && Boat.IdBoat() === index)
      {
        rnk = index + 1;
        break;
      }

    }
  }

  return rnk;
}

function FillWPRanking(Boat, WPNum, Friends)
{
  let index;
  let RowNum = 1;
  let BestTime = 0;
  let Rows = [];

  BackupRankingTable();
  //TODO Remove DEADCDOE
  //console.log( 'Last Fill Delta ' + (new Date().getTime() - RankingFt.LastWPFill.getTime()).toString());
  /*if (!Boat && (!RankingFt || RankingFt.DrawPending || new Date().getTime() - RankingFt.LastWPFill.getTime() < 1000))
  {
    return;
  }*/
  //RankingFt.LastWPFill = new Date();
  let RaceId = GetRankingRaceId(Boat);


  for (index in Rankings[RaceId].RacerRanking)
  {
    if (Rankings[RaceId].RacerRanking[index])
    {
      let RnkBoat = Rankings[RaceId].RacerRanking[index];

      if (RnkBoat.WP && RnkBoat.WP[WPNum - 1] && !RnkBoat.WP[WPNum - 1].Delta)
      {
        if (!BestTime)
        {
          BestTime = RnkBoat.WP[WPNum - 1].duration;
          RnkBoat.WP[WPNum - 1].Delta = 0;
          RnkBoat.WP[WPNum - 1].Pct = 0;
        }
        else
        {
          RnkBoat.WP[WPNum - 1].Delta = RnkBoat.WP[WPNum - 1].duration - BestTime;
          RnkBoat.WP[WPNum - 1].Pct = 100 * (RnkBoat.WP[WPNum - 1].duration / BestTime - 1);
        }
      }

      if (RnkBoat.WP && RnkBoat.WP[WPNum - 1])
      {
        Rows.push(GetRankingObject(RnkBoat, parseInt(index, 10) + 1, WPNum, Friends));
        if (typeof Boat !== "undefined" && Boat && Boat.IdBoat && Boat.IdBoat() === parseInt(RnkBoat.idusers, 10))
        {
          RowNum = Rows.length;
        }
      }
    }
  }

  let TargetPage = RoundPow(RowNum / 20, 0) + (RowNum % 20 >= 10 ? 0 : 1);
  RankingFt.DrawPending = true;
  RankingFt.loadRows(Rows);
  RankingFt.TargetPage = TargetPage;

}

function BackupICS_WPTable()
{
  BackupFooTable(ICS_WPft, "#RaceWayPoints", "#RaceWayPointsInsertPoint");
}


function getWaypointHTMLSymbols(WPFormat)
{
  let WPSymbols = "";
  switch (WPFormat & (WP_CROSS_CLOCKWISE | WP_CROSS_ANTI_CLOCKWISE))
  {
    case WP_CROSS_ANTI_CLOCKWISE:
      WPSymbols += "&#x21BA; ";
      break;
    case WP_CROSS_CLOCKWISE:
      WPSymbols += "&#x21BB; ";
      break;
    default:
  }
  if ((WPFormat & WP_CROSS_ONCE) == WP_CROSS_ONCE)
  {
    WPSymbols += "&#x2285; ";
  }

  switch (WPFormat & (WP_ICE_GATE_N | WP_ICE_GATE_S))
  {
    case WP_ICE_GATE_S:
      WPSymbols += "&#x27F0;";
      break;
    case WP_ICE_GATE_N:
      WPSymbols += "&#x27F1;";
      break;
    default:
  }
  return WPSymbols.trim();
}

function getWaypointHTMLSymbolsDescription(WPFormat)
{
  let WPDesc = "";
  switch (WPFormat & (WP_CROSS_CLOCKWISE | WP_CROSS_ANTI_CLOCKWISE))
  {
    case WP_CROSS_ANTI_CLOCKWISE:
      WPDesc += GetLocalizedString("Anti-clockwise") + " ";
      break;
    case WP_CROSS_CLOCKWISE:
      WPDesc += GetLocalizedString("Clockwise") + " ";
      break;
    default:
  }

  if ((WPFormat & WP_CROSS_ONCE) == WP_CROSS_ONCE)
  {
    WPDesc += GetLocalizedString("Only once");
  }

  switch (WPFormat & (WP_ICE_GATE_N | WP_ICE_GATE_S))
  {
    case WP_ICE_GATE_S:
      WPDesc += GetLocalizedString("Ice gate") + "(" + GetLocalizedString("South") + ") ";
      break;

    case WP_ICE_GATE_N:
      WPDesc += GetLocalizedString("Ice gate") + "(" + GetLocalizedString("North") + ") ";
      break;

    default:
  }
  if (WPDesc !== "")
  {
    WPDesc = GetLocalizedString("Crossing") + " : " + WPDesc;
  }
  return WPDesc.trim();
}

function NormalizeRaceInfo(RaceInfo)
{
  if (typeof RaceInfo === "undefined" || !RaceInfo || RaceInfo.IsNormalized)
  {
    return;
  }
  RaceInfo.startlat /= VLM_COORDS_FACTOR;
  RaceInfo.startlong /= VLM_COORDS_FACTOR;

  for (let index in RaceInfo.races_waypoints)
  {
    if (RaceInfo.races_waypoints[index])
    {
      let WP = RaceInfo.races_waypoints[index];
      WP.latitude1 /= VLM_COORDS_FACTOR;
      WP.longitude1 /= VLM_COORDS_FACTOR;
      if (typeof WP.latitude2 !== "undefined")
      {
        WP.latitude2 /= VLM_COORDS_FACTOR;
        WP.longitude2 /= VLM_COORDS_FACTOR;
      }
    }
  }
  RaceInfo.IsNormalized = true;
}

function FillRaceWaypointList(RaceInfo)
{

  if (ICS_WPft.DrawPending)
  {
    if (!ICS_WPft.CallbackPending)
    {
      ICS_WPft.CallbackPending = function()
      {
        FillRaceWaypointList(RaceInfo);
      };
    }
    return;
  }

  BackupICS_WPTable();

  if (RaceInfo)
  {
    NormalizeRaceInfo(RaceInfo);
    let Rows = [];
    // Insert the start point
    let Row = {};
    Row.WaypointId = 0;
    Row.WP1 = RaceInfo.startlat + "<BR>" + RaceInfo.startlong;
    Row.WP2 = "";
    Row.Spec = "";
    Row.Type = GetLocalizedString("startmap");
    Row.Name = "";
    Rows.push(Row);

    for (let index in RaceInfo.races_waypoints)
    {
      if (RaceInfo.races_waypoints[index])
      {
        let WP = RaceInfo.races_waypoints[index];
        let Row = {};

        let WPSpec;
        Row.WaypointId = '<span class="HoverShowMiniMap" RaceId="' + RaceInfo.idraces + '"  WP_Id="' + WP.idwaypoint + '">' + WP.wporder + '</span>';
        Row.WP1 = WP.latitude1 + "<BR>" + WP.longitude1;
        if (typeof WP.latitude2 !== "undefined")
        {
          Row.WP2 = WP.latitude2 + "<BR>" + WP.longitude2;
        }
        else
        {
          Row.WP2 = "@" + WP.laisser_au;
        }
        Row.Spec = "<span title='" + getWaypointHTMLSymbolsDescription(WP.wpformat) + "'>" + getWaypointHTMLSymbols(WP.wpformat) + "</span>";
        Row.Type = GetLocalizedString(WP.wptype);
        Row.Name = WP.libelle;

        Rows.push(Row);
      }
    }

    ICS_WPft.loadRows(Rows);
  }
}

function BackupNSZ_Table()
{
  BackupFooTable(NSZ_WPft, "NSZPoints", "NSZPointsInsertPoint");
}

function FillNSZList(Exclusions)
{

  if (NSZ_WPft.DrawPending)
  {
    if (!NSZ_WPft.CallbackPending)
    {
      NSZ_WPft.CallbackPending = function()
      {
        FillNSZList(Exclusions);
      };
    }
    return;
  }

  BackupNSZ_Table();

  if (Exclusions)
  {
    let Rows = [];

    for (let index in Exclusions)
    {
      if (Exclusions[index])
      {
        let Seg = Exclusions[index];
        let row = {};
        row.NSZId = index;
        row.Lon1 = Seg[0][1];
        row.Lat1 = Seg[0][0];
        row.Lon2 = Seg[1][1];
        row.Lat2 = Seg[1][0];

        Rows.push(row);
      }
    }

    NSZ_WPft.loadRows(Rows);
  }
}

function BackupRankingTable()
{
  BackupFooTable(RankingFt, "#RankingTable", "#my-rank-content");
}

function BackupVLMIndexTable()
{
  BackupFooTable(VLMINdexFt, "#VLMIndexTable", "#my-vlmindex-content");
}

function FillStatusRanking(Boat, Status, Friends)
{
  let index;
  let RowNum = 1;
  let Rows = [];
  let RaceId = GetRankingRaceId(Boat);

  BackupRankingTable();

  for (index in Rankings[RaceId].RacerRanking)
  {
    if (Rankings[RaceId].RacerRanking[index])
    {
      let RnkBoat = Rankings[RaceId].RacerRanking[index];

      if (RnkBoat.status === Status)
      {
        Rows.push(GetRankingObject(RnkBoat, parseInt(index, 10) + 1, null, Friends));
        if (typeof Boat !== "undefined" && Boat && Boat.IdBoat() === parseInt(RnkBoat.idusers, 10))
        {
          RowNum = Rows.length;
        }
      }
    }
  }

  let TargetPage = RoundPow(RowNum / 20, 0) + (RowNum % 20 >= 10 ? 0 : 1);
  RankingFt.loadRows(Rows);
  RankingFt.TargetPage = TargetPage;
  RankingFt.DrawPending = true;
}

function FillRacingRanking(Boat, Friends)
{
  let index;
  let Rows = [];
  let RowNum = 0;
  let Refs = {
    Arrived1stTime: null,
    Racer1stPos: null
  };
  let PrevDuration = 0;
  let BoatRank = 0;

  BackupRankingTable();

  let RaceId = GetRankingRaceId(Boat);
  let CurWP = 0;
  if (RaceId && typeof Rankings !== "undefined" && typeof Rankings[RaceId] !== "undefined" && Rankings[RaceId] && Rankings[RaceId].RacerRanking)
  {
    for (index in Rankings[RaceId].RacerRanking)
    {
      if (Rankings[RaceId].RacerRanking[index])
      {
        let RnkBoat = Rankings[RaceId].RacerRanking[index];

        if (typeof Boat !== "undefined" && Boat && Boat.IdBoat && Boat.IdBoat() === parseInt(RnkBoat.idusers, 10))
        {
          RowNum = Rows.length;
        }

        if (RnkIsArrived(RnkBoat) || RnkIsRacing(RnkBoat))
        {
          if (RnkIsArrived(RnkBoat))
          {
            if (!PrevDuration || parseInt(RnkBoat.duration, 10) !== PrevDuration)
            {
              PrevDuration = parseInt(RnkBoat.duration, 10);
              BoatRank += 1;
            }

            if (!Refs.Arrived1stTime)
            {
              // First arrived, store time
              Refs.Arrived1stTime = parseInt(RnkBoat.duration, 10);
            }
          }
          else if (RnkIsRacing(RnkBoat) && (!Refs.Racer1stPos || RnkBoat.nwp !== CurWP))
          {
            Refs.Racer1stPos = RnkBoat.dnm;
            CurWP = RnkBoat.nwp;
            BoatRank += 1;
          }
        }
        else
        {
          break;
        }
        Rows.push(GetRankingObject(RnkBoat, BoatRank, null, Friends, Refs));
      }
    }
  }

  let TargetPage = RoundPow(RowNum / 20, 0) + (RowNum % 20 >= 10 ? 0 : 1);
  RankingFt.loadRows(Rows);
  RankingFt.TargetPage = TargetPage;
  RankingFt.DrawPending = true;
}

function GetBoatInfoLink(RnkBoat)
{
  let IdUser = parseInt(RnkBoat.idusers, 10);
  let BoatName = RnkBoat.boatname;
  let ret = "";

  if (RnkBoat.country)
  {
    ret = GetCountryFlagImgHTML(RnkBoat.country);

    if (typeof ret === "undefined")
    {
      ret = "";
    }
  }

  //ret += '<a class="RaceHistLink" href="/palmares.php?type=user&idusers='+IdUser+'" target ="_'+IdUser +'">'+BoatName+'</a>';
  ret += '<a class="RaceHistLink" boatid ="' + IdUser + '"data-toggle="tooltip" title="' + BoatName + '-' + IdUser + '" >' + RnkBoat.PlayerName + '</a>';

  return ret;
}

function GetRankingObject(RankBoat, rank, WPNum, Friends, Refs)
{

  let boatsearchstring = ''; //'<img class="BoatFinder" src="images/search.png" idu=' + RankBoat.idusers + ' ></img>   ';
  if (typeof RankBoat.Challenge !== "undefined" && RankBoat.Challenge[1])
  {
    boatsearchstring = '<img class="RnkLMNH" src="images/LMNH.png"></img>' + boatsearchstring;
  }

  boatsearchstring += GetBoatInfoLink(RankBoat);

  let RetObject = {
    Rank: rank,
    Name: boatsearchstring,
    Distance: "",
    Time: "",
    Loch: "",
    Lon: "",
    Lat: "",
    Last1h: "",
    Last3h: "",
    Last24h: "",
    Class: "",
    Delta1st: ""
  };

  if (typeof _CurPlayer !== "undefined" && _CurPlayer && typeof _CurPlayer.GetRankingObject !== "undefined" && parseInt(RankBoat.idusers, 10) === _CurPlayer.CurBoat.IdBoat())
  {
    RetObject.Class += " ft_class_myboat";
  }

  if (typeof Friends !== "undefined" && Friends)
  {
    if (Friends.indexOf(RankBoat.idusers) !== -1)
    {
      RetObject.Class += " ft_class_friend";
    }
  }

  if (RnkIsRacing(RankBoat) && !WPNum)
  {
    // General ranking layout
    let NextMark = '[' + RankBoat.nwp + '] -=> ' + RoundPow(RankBoat.dnm, 2);

    if (rank > 1 && Refs && Refs.Racer1stPos)
    {
      let P = new VLMPosition(RankBoat.longitude, RankBoat.latitude);
      RetObject.Delta1st = RoundPow(RankBoat.dnm - Refs.Racer1stPos, 2);
    }

    RetObject.Distance = NextMark;
    let RacingTime = Math.round((new Date() - new Date(parseInt(RankBoat.deptime, 10) * 1000)) / 1000);
    RetObject.Time = (RankBoat.deptime === "-1" ? "" : GetFormattedChronoString(RacingTime));
    RetObject.Loch = RankBoat.loch;
    RetObject.Lon = FormatLon(RankBoat.longitude);
    RetObject.Lat = FormatLat(RankBoat.latitude);
    RetObject.Last1h = RankBoat.last1h;
    RetObject.Last3h = RankBoat.last3h;
    RetObject.Last24h = RankBoat.last24h;

    for (let index in BoatRacingStatus)
    {
      if (RankBoat.status === BoatRacingStatus[index])
      {
        RetObject.Class += "  " + BoatRacingClasses[BoatRacingStatus[index]];
      }
    }
  }
  else if (!WPNum)
  {
    // Non General ranking layout
    let NextMark = GetLocalizedString("status_" + RankBoat.status);
    RetObject.Distance = NextMark;
    let Duration = parseInt(RankBoat.duration, 10);
    RetObject.Time = GetFormattedChronoString(Duration);
    if (Refs && Duration !== Refs.Arrived1stTime)
    {
      RetObject.Time += " ( +" + RoundPow(Duration / Refs.Arrived1stTime * 100 - 100, 2) + "% )";
      RetObject.Delta1st = GetFormattedChronoString(Duration - Refs.Arrived1stTime);
    }
    else if (Refs && Duration == Refs.Arrived1stTime)
    {
      RetObject.Delta1st = GetLocalizedString("winner");
    }


    RetObject.Loch = RankBoat.loch;
    //RetObject.Lon = FormatLon(RankBoat.longitude);
    //RetObject.Lat = FormatLat(RankBoat.latitude);
  }
  else
  {
    RetObject.Time = GetFormattedChronoString(parseInt(RankBoat.WP[WPNum - 1].duration, 10));
    let DeltaStr;
    if (RankBoat.WP[WPNum - 1].Delta)
    {
      let PctString = RoundPow(RankBoat.WP[WPNum - 1].Pct, 2);
      DeltaStr = GetFormattedChronoString(RankBoat.WP[WPNum - 1].Delta) + " (+" + PctString + " %)";
    }
    else
    {
      DeltaStr = GetLocalizedString("winner");
    }

    // Column name is wrong but it works because of cols renaming and hiding
    RetObject.Loch = DeltaStr;
  }

  return RetObject;
}

// Add polyfill for IE
Math.trunc = Math.trunc || function(x)
{
  if (isNaN(x))
  {
    return NaN;
  }
  if (x > 0)
  {
    return Math.floor(x);
  }
  return Math.ceil(x);
};

function formatCoords(v)
{
  v = Math.abs(v);
  let D = Math.trunc(v);
  let M = Math.trunc((v - D) * 60);
  let S = RoundPow(((v - D) * 3600) % 60.0, 4);

  return "" + D + "° " + M + "' " + S + '"';
}

function FormatLon(v)
{
  let EW = (v > 0 ? "W" : "E");
  return formatCoords(v) + EW;
}

function FormatLat(v)
{
  let NS = (v > 0 ? "N" : "S");
  return formatCoords(v) + NS;
}

function HandleShowMapPrefs(e)
{
  //Load prefs
  $("#DisplayReals").attr('checked', VLM2Prefs.MapPrefs.ShowReals);
  $("#ShowCompass").attr('checked', VLM2Prefs.MapPrefs.ShowCompass);
  $("#DisplayNames").attr('checked', VLM2Prefs.MapPrefs.ShowOppNumbers);
  $("#EstTrackMouse").attr('checked', VLM2Prefs.MapPrefs.EstTrackMouse);
  $("#TrackEstForecast").attr('checked', VLM2Prefs.MapPrefs.TrackEstForecast);
  $("#UseUTC").attr('checked', VLM2Prefs.MapPrefs.UseUTC);

  $('#DDMapSelOption:first-child').html(
    '<span Mode=' + VLM2Prefs.MapPrefs.MapOppShow + '>' + VLM2Prefs.MapPrefs.GetOppModeString(VLM2Prefs.MapPrefs.MapOppShow) + '</span>' +
    '<span class="caret"></span>'
  );

  if (VLM2Prefs.MapPrefs.MapOppShow === VLM2Prefs.MapPrefs.MapOppShowOptions.ShowTopN)
  {
    $("#NbDisplayBoat").removeClass("hidden");
    $("#NbDisplayBoat").val(VLM2Prefs.MapPrefs.ShowTopCount);
  }
  else
  {
    $("#NbDisplayBoat").addClass("hidden");
  }

  $("#VacPol").val(VLM2Prefs.MapPrefs.PolarVacCount);


}

function HandleMapPrefOptionChange(e)
{
  var target = e.target;

  if (typeof target === "undefined" || typeof target.attributes.id === "undefined")
  {
    return;
  }

  var Id = target.attributes.id.value;
  var Value = target.checked;

  switch (Id)
  {
    /*case "DisplayReals":
      //VLM2Prefs.MapPrefs.ShowReals = Value;
      //break;
    case "DisplayNames":
      //VLM2Prefs.MapPrefs.ShowOppName = Value;
      //break;*/

    case "DisplayReals":
    case "ShowReals":
    case "UseUTC":
    case "DisplayNames":
    case "ShowOppNumbers":
    case "EstTrackMouse":
    case "TrackEstForecast":
      VLM2Prefs.MapPrefs[Id] = Value;
      break;
    case "ShowCompass":
      VLM2Prefs.MapPrefs[Id] = Value;
      DrawCompass();
      break;
    case "VacPol":
      let VacPol = parseInt($("#VacPol").val(), 10);

      if (VacPol > 0 && VacPol < 120)
      {
        VLM2Prefs.MapPrefs.PolarVacCount = VacPol;
      }
      else
      {
        $("#VacPol").value(12);
      }
      break;

    case "NbDisplayBoat":
      let TopCount = parseInt($("#NbDisplayBoat").val(), 10);
      VLM2Prefs.MapPrefs.ShowTopCount = TopCount;
      break;

    default:
      console.log("unknown pref storage called : " + Id);
      return;

  }

  VLM2Prefs.Save();
  RefreshCurrentBoat(false, false);
}

function SafeHTMLColor(Color)
{
  if (typeof Color === "undefined")
  {
    Color = "#000000";
  }
  Color = "" + Color;

  if (Color.length < 6)
  {
    Color = ("000000" + Color).slice(-6);
  }

  if (Color.substring(0, 1) !== "#")
  {
    Color = "#" + Color;
  }
  else if (Color.substring(1, 2) === "#")
  {
    Color = Color.substring(1);
  }

  return Color;
}

function HandleMapOppModeChange(e)
{
  var t = e.target;
  if (typeof t !== "undefined" && t && typeof t.attributes !== "undefined" && t.attributes.Mode !== "undefined" && t.attributes.Mode)
  {
    let Mode = parseInt(t.attributes.Mode.value, 10);

    VLM2Prefs.MapPrefs.MapOppShow = Mode;
    VLM2Prefs.Save();
    HandleShowMapPrefs(e);
  }

}

function SetActiveStyleSheet(title)
{
  var i, a, main;
  for (i = 0;
    (a = document.getElementsByTagName("link")[i]); i++)
  {
    if ((a.getAttribute("rel").indexOf("style") !== -1) && a.getAttribute("title"))
    {
      a.disabled = true;
      if (a.getAttribute("title") === title)
      {
        a.disabled = false;
      }
    }
  }
}

function SetDDTheme(Theme)
{
  SetActiveStyleSheet(Theme);
  $("#SelectionThemeDropDown:first-child").html(Theme + '<span class="caret"></span>');
  $("#SelectionThemeDropDown").attr("SelTheme", Theme);
}

function HandleDDlineClick(e)
{
  var Target = e.target;
  //var Theme = Target.closest(".DDTheme").attributes["DDTheme"].value;
  var Theme = e.target.attributes.ddtheme.value;

  SetDDTheme(Theme);
}

var AlertTemplate;

function InitAlerts()
{
  // Init default alertbox
  $("#AlertBox").css("display", "block");
  AlertTemplate = $("#AlertBox")[0];
  $("#AlertBoxContainer").empty();
  $("#AlertBoxContainer").removeClass("hidden");

}

function VLMAlertSuccess(Text)
{
  VLMAlert(Text, "alert-success");
}

function VLMAlertDanger(Text)
{
  VLMAlert(Text, "alert-danger");
}

function VLMAlertInfo(Text)
{
  VLMAlert(Text, "alert-info");
}

let AlertIntervalId = null;

function VLMAlert(Text, Style)
{
  if (AlertIntervalId)
  {
    clearInterval(AlertIntervalId);
  }

  if (typeof Style === "undefined" || !Style)
  {
    Style = "alert-info";
  }

  $("#AlertBoxContainer").empty().append(AlertTemplate).show();

  $("#AlertText").text(Text);
  $("#AlertBox").removeClass("alert-sucess");
  $("#AlertBox").removeClass("alert-warning");
  $("#AlertBox").removeClass("alert-info");
  $("#AlertBox").removeClass("alert-danger");
  $("#AlertBox").addClass(Style);
  $("#AlertBox").show();
  $("#AlertCloseBox").unbind().on('click', AutoCloseVLMAlert);

  if (AlertIntervalId)
  {
    clearInterval(AlertIntervalId);
  }
  AlertIntervalId = setTimeout(AutoCloseVLMAlert, 5000);
}

function AutoCloseVLMAlert()
{
  $("#AlertBox").hide();
}

function GetUserConfirmation(Question, IsYesNo, CallBack)
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
  $(".OKBtn").unbind().on("click", function()
  {
    $("#ConfirmDialog").modal('hide');
    CallBack(true);
  });
  $(".NOKBtn").unbind().on("click", function()
  {
    $("#ConfirmDialog").modal('hide');
    CallBack(false);
  });

}

function GetRaceRankingLink(RaceInfo)
{
  return '<a href="/jvlm?RaceRank=' + RaceInfo.idrace + '" target="RankTab">' + RaceInfo.racename + '</a>';
}

function FillBoatPalmares(data, status)
{
  let index;

  if (status === "success")
  {
    let rows = [];
    for (index in data.palmares)
    {
      if (data.palmares[index])
      {
        let palmares = data.palmares[index];
        let RowsData = {
          RaceId: data.palmares[index].idrace,
          RaceName: GetRaceRankingLink(data.palmares[index]),
          Ranking: palmares.ranking.rank + " / " + palmares.ranking.racercount
        };

        rows.push(RowsData);
      }
    }
    RaceHistFt.loadRows(rows);
  }

  let str = GetLocalizedString("palmares");
  str = str.replace("%s", data.boat.name);
  $("#palmaresheaderline").text(str);
}

function ShowUserRaceHistory(e, BoatId)
{
  let Source = e.currentTarget;
  let DisplayType = Source.value;

  if (typeof DisplayType === "undefined")
  {
    $("#RaceHistory").modal("show");
    DisplayType = 0;
    $(".RaceListTab").attr("BoatId", BoatId);
  }
  else
  {
    BoatId = $(".RaceListTab").attr("BoatId");
  }
  switch (DisplayType)
  {

    case 2:
      break;
    case 1:
    default:
      let PlayerRaces = (DisplayType == 1 ? '&GetPlayerRaces=1' : '');
      StatMGR.Stat("BoatPalmares", (DisplayType == 1 ? 'Player' : 'Boat'));
      $("#RaceRankingsPreloader").removeClass("hidden");
      $(".racelistpreloader").addClass("hidden");
      $.get("/ws/boatinfo/palmares.php?idu=" + BoatId + PlayerRaces, function(e, a)
      {
        $("#RaceListDiv").removeClass("hidden");
        FillBoatPalmares(e, a);
        $(".racelistpreloader").addClass("hidden");
      });
  }

  $(".JVLMTabs").tabs("refresh");

}

function HandleShowBoatRaceHistory(e)
{
  let BoatId = $(e.target).attr("boatid");

  if (BoatId)
  {
    ShowUserRaceHistory(e, BoatId);
  }
}

function HandleCreateUserResult(data, status)
{
  if (status === "success" && data)
  {
    $(".ValidationMark").addClass("hidden");

    if (data.success)
    {
      $(".ValidationMark.Valid").removeClass("hidden");
      VLMAlertSuccess(GetLocalizedString('An email has been sent. Click on the link to validate.'));
      $("#InscriptForm").modal("hide");
      $("#LoginForm").modal("hide");
    }
    else if (data.request && data.request.errorstring)
    {
      VLMAlertDanger(GetLocalizedString(data.request.errorstring));
    }
    else
    {
      VLMAlertDanger(GetLocalizedString(data.error.msg));
    }


    if (data.request)
    {
      if (data.request.MailOK)
      {
        $(".ValidationMark.Email.Valid").removeClass("hidden");
      }
      else
      {
        $(".ValidationMark.Email.Invalid").removeClass("hidden");
      }
      if (data.request.PasswordOK)
      {
        $(".ValidationMark.Password.Valid").removeClass("hidden");
      }
      else
      {
        $(".ValidationMark.Password.Invalid").removeClass("hidden");
      }
      if (data.request.PlayerNameOK)
      {
        $(".ValidationMark.Pseudo.Valid").removeClass("hidden");
      }
      else
      {
        $(".ValidationMark.Pseudo.Invalid").removeClass("hidden");
      }
    }
    else if (data.error)
    {
      switch (data.error.code)
      {
        case "NEWPLAYER01":
          $(".ValidationMark.Email.Invalid").removeClass("hidden");
          break;

        case "NEWPLAYER02":
          $(".ValidationMark.Pseudo.Invalid").removeClass("hidden");
          break;

        case "NEWPLAYER03":
          $(".ValidationMark.Password.Invalid").removeClass("hidden");
          break;
      }
    }
  }
  $("#BtnCreateAccount").show();


}

function HandleCreateUser()
{
  let txtplayername = $("#NewPlayerPseudo")[0].value;
  let txtemail = $("#NewPlayerEMail")[0].value;
  let txtPwd = $("#NewPlayerPassword")[0].value;
  let PostData = {
    emailid: txtemail,
    password: txtPwd,
    pseudo: txtplayername
  };
  $("#BtnCreateAccount").hide();
  $.post("/ws/playerinfo/player_create.php",
    PostData,
    function(e, status)
    {
      HandleCreateUserResult(e, status);
    });
}

function setModalMaxHeight(element)
{
  let $element = $(element);
  let $content = $element.find('.modal-content');
  let borderWidth = $content.outerHeight() - $content.innerHeight();
  let dialogMargin = $(window).width() < 768 ? 20 : 60;
  let contentHeight = $(window).height() - (dialogMargin + borderWidth);
  let headerHeight = $element.find('.modal-header').outerHeight() || 0;
  let footerHeight = $element.find('.modal-footer').outerHeight() || 0;
  let maxHeight = contentHeight - (headerHeight + footerHeight);

  $content.css(
  {
    'overflow': 'hidden'
  });

  $element
    .find('.modal-body').css(
    {
      'max-height': maxHeight,
      'overflow-y': 'auto',
      'overflow-x': 'hidden'
    });
}

// Return a moment in UTC or Local according to VLM2 Local Pref
function GetLocalUTCTime(d, IsUTC, AsString)
{
  let m = d;
  let UTCSuffix = "";

  if (!moment.isMoment(d))
  {
    if (IsUTC)
    {
      m = moment(d).utc();
    }
    else
    {
      m = moment(d);
    }
  }
  if (VLM2Prefs.MapPrefs.UseUTC)
  {
    if (m.isLocal())
    {
      m = m.utc();
    }
    UTCSuffix = " Z";
  }
  else
  {
    if (!m.isLocal())
    {
      m = m.local();
    }
  }

  if (AsString)
  {
    return m.format("LLLL") + UTCSuffix;
  }
  else
  {
    return m;
  }


}