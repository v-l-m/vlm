//
// VLMBoat layer handling displaying vlm boats, traj
//


const VLM_COORDS_FACTOR = 1000;

// Default map options


// Click handler for handling map clicks.
/* OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control,
{
  defaultHandlerOptions:
  {
    'single': true,
    'double': false,
    'pixelTolerance': 0,
    'stopSingle': false,
    'stopDouble': false
  },

  initialize: function(options)
  {
    this.handlerOptions = OpenLayers.Util.extend(
    {}, this.defaultHandlerOptions);
    OpenLayers.Control.prototype.initialize.apply(
      this, arguments
    );
    this.handler = new OpenLayers.Handler.Click(
      this,
      {
        'click': this.trigger
      }, this.handlerOptions
    );
  },

  trigger: function(e)
  {

    var MousePos = GetVLMPositionFromClick(e.xy);
    if (typeof GM_Pos !== "object" || !GM_Pos)
    {
      GM_Pos = {};
    }
    GM_Pos.lon = MousePos.Lon.Value;
    GM_Pos.lat = MousePos.Lat.Value;

    HandleMapMouseMove(e);
    if (SetWPPending)
    {
      if (WPPendingTarget === "WP")
      {
        CompleteWPSetPosition(e, e.xy);
        HandleCancelSetWPOnClick();
      }
      else if (WPPendingTarget === "AP")
      {
        SetWPPending = false;
        _CurAPOrder.PIP_Coords = GetVLMPositionFromClick(e.xy);
        $("#AutoPilotSettingForm").modal("show");
        RefreshAPDialogFields();

      }
      else
      {
        SetWPPending = false;
      }
    }
  }

}); */

// var DrawControl = null;
var OppPopups = [];
var StartSetWPOnClick = false;

function SetCurrentBoat(Boat, CenterMapOnBoat, ForceRefresh, TargetTab)
{
  if (_CurPlayer && _CurPlayer.CurBoat && Boat)
  {
    if (_CurPlayer.CurBoat.IdBoat !== Boat.IdBoat)
    {
      ClearCurrentMapMarkers(_CurPlayer.CurBoat);
    }
    EnsureMarkersVisible(Boat);
  }
  CheckBoatRefreshRequired(Boat, CenterMapOnBoat, ForceRefresh, TargetTab);
}

var BoatLoading = new Date(0);

function CheckBoatRefreshRequired(Boat, CenterMapOnBoat, ForceRefresh, TargetTab)
{
  // Check Params.
  if (typeof Boat === "undefined" || !Boat)
  {
    return;
  }
  let CurDate = new Date();
  let NeedPrefsRefresh = (typeof Boat !== "undefined" && (typeof Boat.VLMInfo === "undefined" || typeof Boat.VLMInfo.AVG === "undefined"));

  // Update preference screen according to current selected boat
  UpdatePrefsDialog(Boat);

  if ((typeof Boat.VLMInfo === 'undefined') || (typeof Boat.VLMInfo.LUP === 'undefined'))
  {
    ForceRefresh = true;
  }

  //if ((CurDate > BoatLoading) && (ForceRefresh || CurDate >= Boat.NextServerRequestDate))
  if ((ForceRefresh) || (CurDate >= Boat.NextServerRequestDate))
  {
    BoatLoading = CurDate + 3000;
    console.log("Loading boat info from server....");
    // request current boat info
    ShowPb("#PbGetBoatProgress");

    $.get("/ws/boatinfo.php?forcefmt=json&select_idu=" + Boat.IdBoat,
      function(result)
      {
        // Check that boat Id Matches expectations
        if (Boat.IdBoat === parseInt(result.IDU, 10))
        {
          // Set Current Boat for player
          _CurPlayer.CurBoat = Boat;


          // LoadPrefs
          LoadVLMPrefs();

          // Store BoatInfo, update map
          Boat.VLMInfo = result;

          // Store next request Date (once per minute)
          Boat.NextServerRequestDate = new Date((parseInt(Boat.VLMInfo.LUP, 10) + parseInt(Boat.VLMInfo.VAC, 10)) * 1000);
          Boat.LastRefresh = new Date();

          // Fix Lon, and Lat scale
          Boat.VLMInfo.LON /= VLM_COORDS_FACTOR;
          Boat.VLMInfo.LAT /= VLM_COORDS_FACTOR;
          if ('Prod' !== '@@BUILD_TYPE@@')
          {
            //console.log(GribMgr.WindAtPointInTime(new Date(Boat.VLMInfo.LUP*1000),Boat.VLMInfo.LAT,Boat.VLMInfo.LON ));
            console.log("DBG WIND ");
            //49.753227868452, -8.9971082951315
            let MI = GribMgr.WindAtPointInTime(new Date(1566149443 * 1000), 49.753227868452, -8.9971082951315);
            if (MI)
            {
              let Hdg = MI.Heading + 40;
              let Speed = PolarsManager.GetBoatSpeed("boat_figaro2", MI.Speed, MI.Heading, Hdg);
              if (!isNaN(Speed))
              {
                let P = new VLMPosition(49.753227868452, -8.9971082951315);
                let dest = P.ReachDistLoxo(Speed / 3600.0 * 300, Hdg);
                let bkp1 = 0;
              }
            }
          }

          // force refresh of settings if was not initialized
          if (NeedPrefsRefresh)
          {
            UpdatePrefsDialog(Boat);
          }

          // update map if racing
          if (Boat.VLMInfo.RAC !== "0")
          {

            if (typeof Boat.RaceInfo === "undefined" || typeof Boat.RaceInfo.idraces === 'undefined')
            {
              // Get race info if first request for the boat
              GetRaceInfoFromServer(Boat, TargetTab);
              GetRaceExclusionsFromServer(Boat);
            }

            // Get boat track for the last 24h
            GetTrackFromServer(Boat);

            // Get Rankings
            if (Boat.VLMInfo && Boat.VLMInfo.RAC)
            {
              LoadRankings(Boat.VLMInfo.RAC);
            }

            // Get Reals
            LoadRealsList(Boat);

            // Draw Boat, course, tracks....
            DrawBoat(Boat, CenterMapOnBoat);

            // Update Boat info in main menu bar
            UpdateInMenuRacingBoatInfo(Boat, TargetTab);

          }
          else
          {
            // Boat is not racing
            //GetLastRacehistory();
            UpdateInMenuDockingBoatInfo(Boat);
          }
        }

        HidePb("#PbGetBoatProgress");

        if (OnPlayerLoadedCallBack)
        {
          OnPlayerLoadedCallBack();
          OnPlayerLoadedCallBack = null;
        }
      }
    );


  }
  else if (Boat)
  {
    // Set Current Boat for player
    _CurPlayer.CurBoat = Boat;

    // Draw from last request
    UpdateInMenuDockingBoatInfo(Boat);
    UpdateInMenuRacingBoatInfo(Boat, TargetTab);
    DrawBoat(Boat, CenterMapOnBoat);

  }
}


// Get Track from server for last 48 hours.
function GetTrackFromServer(Boat)
{
  var end = Math.floor(new Date() / 1000);
  var start = end - 48 * 3600;
  $.get("/ws/boatinfo/tracks_private.php?idu=" + Boat.IdBoat + "&idr=" + Boat.VLMInfo.RAC + "&starttime=" + start + "&endtime=" + end, function(result)
  {
    if (result.success)
    {
      if (typeof Boat.Track !== "undefined")
      {
        Boat.Track.length = 0;
      }
      else
      {
        Boat.Track = [];
      }
      for (let index in result.tracks)
      {
        if (result.tracks[index])
        {
          var P = new VLMPosition(result.tracks[index][1] / 1000.0, result.tracks[index][2] / 1000.0);
          Boat.Track.push(P);
        }
      }
      DrawBoat(Boat);
    }
  });
}

function GetRaceExclusionsFromServer(Boat)
{
  $.get("/ws/raceinfo/exclusions.php?idrace=" + Boat.VLMInfo.RAC + "&v=" + Boat.VLMInfo.VER, function(result)
  {
    if (result.success)
    {
      let Polygons = [];
      let CurEndPoint;
      let CurPolyPointsList = [];
      let index;
      for (index in result.Exclusions)
      {
        if (result.Exclusions[index])
        {
          var Seg = result.Exclusions[index];
          if (typeof CurEndPoint === 'undefined' || (CurEndPoint[0] !== Seg[0][0] && CurEndPoint[1] !== Seg[0][1]))
          {
            if (typeof CurEndPoint !== 'undefined')
            {
              // Changing Polygons
              Polygons.push(CurPolyPointsList);
              CurPolyPointsList = [];
            }
            // Add segment Start to current point list
            CurPolyPointsList.push(Seg[0]);
          }
          CurEndPoint = Seg[1];
          // Add segment end  to current point list
          CurPolyPointsList.push(Seg[1]);
        }
      }
      Polygons.push(CurPolyPointsList);
      Boat.Exclusions = Polygons;
      DrawRaceExclusionZones(Boat, Polygons);
    }
  });
}

function GetRaceInfoFromServer(Boat, TargetTab)
{
  $.get("/ws/raceinfo/desc.php?idrace=" + Boat.VLMInfo.RAC + "&v=" + Boat.VLMInfo.VER, function(result)
  {
    // Save raceinfo with boat
    Boat.RaceInfo = result;
    if (!CheckRaceUpdates(Boat.RaceInfo))
    {
      VLMAlertInfo("Race update" + Boat.RaceInfo.UpdateReason);
    }
    DrawRaceGates(Boat);
    UpdateInMenuRacingBoatInfo(Boat, TargetTab);
  });
}

var DrawBoatTimeOutHandle = null;
var DeferredCenterValue = false;

function DrawBoat(Boat, CenterMapOnBoat)
{
  if (typeof CenterMapOnBoat !== "undefined")
  {
    DeferredCenterValue = (DeferredCenterValue || CenterMapOnBoat);
  }
  console.log("Call DrawbBoat (" + CenterMapOnBoat + ") deferred : " + DeferredCenterValue);
  if (DrawBoatTimeOutHandle)
  {
    console.log("Pushed DrawBoat");
    clearTimeout(DrawBoatTimeOutHandle);
  }
  DrawBoatTimeOutHandle = setTimeout(ActualDrawBoat, 100, Boat, DeferredCenterValue);
}

function GetRaceMapFeatures(Boat)
{
  if (!Boat)
  {
    throw "Should not GetRaceFeature unless a boat is defined";
  }


  if (typeof Boat.RaceMapFeatures === "undefined")
  {
    Boat.RaceMapFeatures = {};
  }

  return Boat.RaceMapFeatures;
}

function ActualDrawBoat(Boat, CenterMapOnBoat)
{
  let ZFactor = map.zoom;
  //console.log("ClearDrawBoat " + CenterMapOnBoat + " Z level "+ );
  DeferredCenterValue = false;
  DrawBoatTimeOutHandle = null;
  if (typeof Boat === "undefined" || !Boat)
  {
    if (typeof _CurPlayer !== "undefined" && _CurPlayer && typeof _CurPlayer.CurBoat !== "undefined" && _CurPlayer.CurBoat)
    {
      // Fallback to currently selected Boat
      Boat = _CurPlayer.CurBoat;
    }
    else
    {
      // Ignore call, if no boat is provided...
      return;
    }
  }

  if (typeof Boat === "undefined" || !Boat)
  {
    // Should not be there
    return;
  }

  let RaceFeatures = GetRaceMapFeatures(Boat);

  //WP Marker
  let WPFeature = RaceFeatures.TrackWP;
  let WP = null;

  if (typeof Boat !== "undefined" && Boat)
  {
    WP = Boat.GetNextWPPosition();
  }

  if (typeof WP !== "undefined" && WP && !isNaN(WP.Lat.Value) && !isNaN(WP.Lon.Value))
  {
    if (WPFeature)
    {
      WPFeature.setLatLng([WP.Lat.Value, WP.Lon.Value]);
    }
    else
    {
      // Track Waypoint marker    
      let WPMarker = GetTrackWPMarker();

      RaceFeatures.TrackWP = L.marker([WP.Lat.Value, WP.Lon.Value],
      {
        icon: WPMarker,
        draggable: true
      }).addTo(map).on("dragend", HandleWPDragEnded);
    }
  }

  // Boat Marker
  if (typeof Boat.VLMInfo !== undefined && Boat.VLMInfo && (Boat.VLMInfo.LON || Boat.VLMInfo.LAT))
  {
    let BoatIcon = RaceFeatures.BoatMarker;

    if (BoatIcon)
    {
      BoatIcon.setLatLng([Boat.VLMInfo.LAT, Boat.VLMInfo.LON]);
      BoatIcon.setRotationAngle(Boat.VLMInfo.HDG);
    }
    else
    {
      BoatIcon = GetBoatMarker(Boat.VLMInfo.idusers);
      RaceFeatures.BoatMarker = L.marker([Boat.VLMInfo.LAT, Boat.VLMInfo.LON],
      {
        icon: BoatIcon,
        rotationAngle: Boat.VLMInfo.HDG
      }).addTo(map).on('click', HandleOpponentClick);

    }

    //Draw polar
    if (typeof map !== "undefined" && map)
    {
      DrawBoatPolar(Boat, CenterMapOnBoat, RaceFeatures);
    }
  }

  // Cur Boat track  
  if (typeof Boat.Track !== "undefined" && Boat.Track.length > 0)
  {
    DrawBoatTrack(Boat, RaceFeatures);
  }

  // Forecast Track
  DrawBoatEstimateTrack(Boat, RaceFeatures);

  // opponents  
  DrawOpponents(Boat);

  if (CenterMapOnBoat && typeof Boat.VLMInfo !== "undefined" && Boat.VLMInfo)
  {
    if (typeof map !== "undefined" && map)
    {
      map.setView([Boat.VLMInfo.LAT, Boat.VLMInfo.LON]);
    }
  }

  // Position Compas according to current boat pos
  {
    RepositionCompass(Boat);
  }

  console.log("ActualDrawBoatComplete");

}

function GetNextPilOrderDate(PilIndex)
{
  if (Boat && Boat.VLMInfo && Boat.VLM.PIL)
  {
    NextPilOrder = null;
    for (let index in Boat.VLM.PIL)
    {
      if (Boat.VLM.PIL[index])
      {
        if (Boat.VLM.PIL[index].STS === "pending")
        {
          if (index > PilIndex)
          {
            return index;
          }
        }
      }
    }
  }
  return -1;
}

function GetPilototoMarkerText(Order)
{
  let OrderMoment = moment("/date(" + Order.TTS * 1000 + ")/");

  let Text = "Date : " + GetLocalUTCTime(OrderMoment, true, true) +
    '<BR>' + OrderMoment.fromNow();
  switch (parseInt(Order.PIM,10))
  {
    case PM_ANGLE:
        Text += '<BR>' + GetLocalizedString('constantengaged') + ' : ' + Order.PIP + '°';
      break;
    case PM_HEADING:
        Text += '<BR>' + GetLocalizedString('heading') + ' : ' + Order.PIP + '°';
      break;
    case PM_ORTHO:
        Text += '<BR>' + GetLocalizedString('OrthoToWP') + ' : ' + Order.PIP;
      break;
    case PM_VMG:
        Text += '<BR>' + GetLocalizedString('bestvmgengaged') + ' : ' + Order.PIP;
      break;
    case PM_VBVMG:
        Text += '<BR>' + GetLocalizedString('vbvmgengaged') + ' : ' + Order.PIP;
      break;
    default:
        Text += '<BR> Strange PIM' + Order.PIM + ' : ' + Order.PIP;
      break;

  }

  return Text;
}

function DrawBoatEstimateTrack(Boat, RaceFeatures)
{
  if (typeof Boat.Estimator !== "undefined" && Boat.Estimator)
  {
    let tracks = Boat.Estimator.GetEstimateTracks();
    let TrackColors = ['green', 'yellow', 'white'];
    for (let index in tracks)
    {
      if (RaceFeatures.EstimateTracks && RaceFeatures.EstimateTracks[index])
      {
        if (typeof tracks[index] !== "undefined")
        {
          RaceFeatures.EstimateTracks[index].setLatLngs(tracks[index]);
        }
        else
        {
          RaceFeatures.EstimateTracks[index].remove();
          RaceFeatures.EstimateTracks[index] = null;
        }
      }
      else
      {
        if (typeof RaceFeatures.EstimateTracks === "undefined")
        {
          RaceFeatures.EstimateTracks = [];
        }
        if (tracks[index])
        {
          let Options = {
            weight: 2,
            opacity: 1,
            color: TrackColors[index]
          };
          RaceFeatures.EstimateTracks[index] = L.polyline(tracks[index], Options).addTo(map);
        }
      }
    }

    let PilotPoints = Boat.Estimator.GetPilotPoints();

    if (typeof RaceFeatures.PilotMarkers === "undefined")
    {
      RaceFeatures.PilotMarkers = [];
    }

    for (let index in PilotPoints)
    {
      if (PilotPoints[index])
      {
        let Order = PilotPoints[index];
        let Coords = [Order.Pos.Lat.Value, Order.Pos.Lon.Value];
        let SetText = false;
        if (RaceFeatures.PilotMarkers[index] && ! RaceFeatures.PilotMarkers[index]._map)
        {
          RaceFeatures.PilotMarkers[index].setLatLng(Coords);
          SetText=true;
        }
        else if (!RaceFeatures.PilotMarkers[index])
        {
          let Marker = GetPilototoMarker(Order);
          RaceFeatures.PilotMarkers[index] = L.marker(Coords,
          {
            icon: Marker
          });
          SetText=true;
        }        

        if (SetText)
        {
          let MarkerText = GetPilototoMarkerText(Order);
          RaceFeatures.PilotMarkers[index].addTo(map).bindPopup(MarkerText);
        }
      }
    }

  }
}

function RepositionCompass(Boat)
{
  if (!Boat)
  {
    return;
  }
  let Features = GetRaceMapFeatures(Boat);
  if (map.Compass)
  {
    if ((Features.Compass && Features.Compass.Lat == -1 && Features.Compass.Lon == -1) || ((!Features.Compass) && Boat.VLMInfo && (Boat.VLMInfo.LAT || Boat.VLMInfo.LON)))
    {
      map.Compass.setLatLng([Boat.VLMInfo.LAT, Boat.VLMInfo.LON]);
    }
    else if (Features.Compass && !isNaN(Features.Compass.Lat) && !isNaN(Features.Compass.Lon))
    {
      map.Compass.setLatLng([Features.Compass.Lat, Features.Compass.Lon]);
    }
  }
}

function DrawBoatTrack(Boat, RaceFeatures)
{
  let PointList = [];
  let TrackLength = Boat.Track.length;
  let PrevLon = 99999;
  let LonOffSet = 0;

  for (let index = 0; index < TrackLength; index++)
  {
    let P = Boat.Track[index];
    PointList.push([P.Lat.Value, P.Lon.Value]);
  }
  var TrackColor = Boat.VLMInfo.COL;
  TrackColor = SafeHTMLColor(TrackColor);
  let TrackFeature = RaceFeatures.BoatTrack;
  if (TrackFeature)
  {
    TrackFeature.setLatLngs(PointList);
  }
  else
  {
    RaceFeatures.BoatTrack = L.polyline(PointList,
    {
      "type": "HistoryTrack",
      "color": TrackColor,
      "weight": 1.2
    }).addTo(map);
  }

}

function DrawBoatPolar(Boat, CenterMapOnBoat, RaceFeatures)
{
  let Polar = [];
  let StartPos = new VLMPosition(Boat.VLMInfo.LON, Boat.VLMInfo.LAT);
  Polar = BuildPolarLine(Boat, StartPos, VLM2Prefs.MapPrefs.PolarVacCount, new Date(Boat.VLMInfo.LUP * 1000), function()
  {
    DrawBoatPolar(Boat, CenterMapOnBoat, RaceFeatures);
  });

  RaceFeatures.Polar = DefinePolarMarker(Polar, RaceFeatures.Polar);
}

function DefinePolarMarker(Polar, PolarFeature)
{
  if (Polar)
  {
    if (PolarFeature)
    {
      PolarFeature.setLatLngs(Polar).addTo(map);
    }
    else
    {
      let PolarStyle = {
        color: "white",
        opacity: 0.6,
        weight: 1
      };
      PolarFeature = L.polyline(Polar, PolarStyle);
      PolarFeature.addTo(map);
    }
  }
  else
  {
    if (PolarFeature)
    {
      PolarFeature.remove();
    }
    PolarFeature = null;
  }

  return PolarFeature;
}

function BuildPolarLine(Boat, StartPos, scale, StartDate, Callback)
{
  let CurDate = StartDate;
  let Polar = null;

  if (Boat && Boat.VLMInfo && Boat.VLMInfo.VAC)
  {
    // set time 1 vac back
    CurDate -= Boat.VLMInfo.VAC * 1000;
  }

  if (!CurDate || CurDate < new Date().getTime())
  {
    CurDate = new Date().getTime();
  }

  let MI = null;

  if (StartPos && StartPos.Lat && StartPos.Lon)
  {
    MI = GribMgr.WindAtPointInTime(CurDate, StartPos.Lat.Value, StartPos.Lon.Value, Callback);
  }

  if (MI)
  {
    let hdg = parseFloat(Boat.VLMInfo.HDG);
    let index;
    let tmpPolar = [];
    for (index = 0; index <= 180; index += 5)
    {
      let Speed = PolarsManager.GetBoatSpeed(Boat.VLMInfo.POL, MI.Speed, MI.Heading, MI.Heading + index);

      if (isNaN(Speed))
      {
        // Just abort in case of not yet loaded polar. Next display should fix it.
        // FixMe - Should we try later or will luck do it for us??
        return;
      }


      for (let Side = -1; Side <= 1; Side += 2)
      {
        let PolarPos = StartPos.ReachDistLoxo(Speed / 3600.0 * Boat.VLMInfo.VAC * scale, MI.Heading + index * Side);
        let PixPos = [PolarPos.Lat.Value, PolarPos.Lon.Value];
        tmpPolar[Side * index + 180] = PixPos;
      }
    }

    Polar = [];
    for (let index in tmpPolar)
    {
      if (tmpPolar[index])
      {
        Polar.push(tmpPolar[index]);
      }
    }
  }
  return Polar;
}

function GetVLMPositionFromClick(pixel)
{
  if (map)
  {
    let dest = map.getLonLatFromPixel(pixel);
    let WGSDest = dest.transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));
    return new VLMPosition(WGSDest.lon, WGSDest.lat);
  }
  else
  {
    return null;
  }
}

function CompleteWPSetPosition(WPMarker)
{
  let pos = null;

  if (WPMarker.getLatLng)
  {
    pos = WPMarker.getLatLng();
  }
  else if (WPMarker.latlng)
  {
    pos = WPMarker.latlng;
  }
  else
  {
    VLMAlertDanger("Unexpected Object when setting WP report to devs.");
    return;
  }
  let PDest = new VLMPosition(pos.lng, pos.lat);

  // Use CurPlayer, since the drag layer is not associated to the proper boat
  SendVLMBoatWPPos(_CurPlayer.CurBoat, PDest);

}

/*// al low testing of specific renderers via "?renderer=Canvas", etc
var renderer = OpenLayers.Util.getParameters(window.location.href).renderer;
renderer = (renderer) ? [renderer] : OpenLayers.Layer.Vector.prototype.renderers;
 */
/* var VectorStyles = new OpenLayers.Style(
{
  strokeColor: "#00FF00",
  strokeOpacity: 1,
  strokeWidth: 3,
  fillColor: "#FF5500",
  fillOpacity: 0.5
},
{
  rules: [
    new OpenLayers.Rule(
    {
      // a rule contains an optional filter
      filter: new OpenLayers.Filter.Comparison(
      {
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: "type", // the "foo" feature attribute
        value: 'buoy'
      }),
      symbolizer:
      {
        // if a feature matches the above filter, use this symbolizer
        label: "${name}\n${Coords}",
        pointerEvents: "visiblePainted",
        fontSize: "1.5em",
        labelAlign: "left", //${align}",
        labelXOffset: "4", //${xOffset}",
        labelYOffset: "-12", //${yOffset}",
        externalGraphic: "images/${GateSide}",
        graphicWidth: 36,
        graphicHeight: 72,
        fillOpacity: 1
      }
    }),
    new OpenLayers.Rule(
    {
      // a rule contains an optional filter
      filter: new OpenLayers.Filter.Comparison(
      {
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: "type", // the "foo" feature attribute
        value: "crossonce"
      }),
      symbolizer:
      {
        xOffset: 1,
        yOffset: 1,
        strokeColor: "black",
        strokeOpacity: 0.5,
        strokeWidth: 4,
        strokeDashstyle: "dashdot"
      }
    }),

    new OpenLayers.Rule(
    {
      // a rule contains an optional filter
      filter: new OpenLayers.Filter.Comparison(
      {
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: "type", // the "foo" feature attribute
        value: "marker"
      }),
      symbolizer:
      {
        externalGraphic: "images/${BuoyName}",
        rotation: "${CrossingDir}",
        graphicWidth: 48
      }
    }),
    new OpenLayers.Rule(
    {
      // a rule contains an optional filter
      filter: new OpenLayers.Filter.Comparison(
      {
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: "type", // the "foo" feature attribute
        value: "NextGate"
      }),
      symbolizer:
      {
        strokeColor: "#FF0000",
        strokeOpacity: 1,
        strokeWidth: 3
      }
    }),
    new OpenLayers.Rule(
    {
      // a rule contains an optional filter
      filter: new OpenLayers.Filter.Comparison(
      {
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: "type", // the "foo" feature attribute
        value: "ValidatedGate"
      }),
      symbolizer:
      {
        strokeColor: "#0000FF",
        strokeOpacity: 0.5,
        strokeWidth: 3
      }
    }),
    new OpenLayers.Rule(
    {
      // a rule contains an optional filter
      filter: new OpenLayers.Filter.Comparison(
      {
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: "type", // the "foo" feature attribute
        value: "FutureGate"
      }),
      symbolizer:
      {
        strokeColor: "#FF0000",
        strokeOpacity: 0.5,
        strokeWidth: 3
      }
    }),
    new OpenLayers.Rule(
    {
      // a rule contains an optional filter
      filter: new OpenLayers.Filter.Comparison(
      {
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: "type", // the "foo" feature attribute
        value: "ForecastPos"
      }),
      symbolizer:
      {
        strokeColor: "black",
        strokeOpacity: 0.75,
        strokeWidth: 1
        //strokeDashstyle: "dot"
      }
    }),
    new OpenLayers.Rule(
    {
      // a rule contains an optional filter
      filter: new OpenLayers.Filter.Comparison(
      {
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: "type", // the "foo" feature attribute
        value: "HistoryTrack"
      }),
      symbolizer:
      {
        strokeOpacity: 0.5,
        strokeWidth: 2,
        strokeColor: "${TrackColor}"
      }
    }),
    new OpenLayers.Rule(
    {
      // a rule contains an optional filter
      filter: new OpenLayers.Filter.Comparison(
      {
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: "type", // the "foo" feature attribute
        value: "Polar"
      }),
      symbolizer:
      {
        strokeColor: "white",
        strokeOpacity: 0.75,
        strokeWidth: 2
      }
    }),
    new OpenLayers.Rule(
    {
      // a rule contains an optional filter
      filter: new OpenLayers.Filter.Comparison(
      {
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: "type", // the "foo" feature attribute
        value: "ExclusionZone"
      }),
      symbolizer:
      {
        strokeColor: "red",
        strokeOpacity: 0.95,
        strokeWidth: 2,
        fillColor: "#FF5500",
        fillOpacity: 0.5
      }
    }),
    new OpenLayers.Rule(
    {
      // a rule contains an optional filter
      filter: new OpenLayers.Filter.Comparison(
      {
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: "type", // the "foo" feature attribute
        value: 'opponent'
      }),
      symbolizer:
      {
        // if a feature matches the above filter, use this symbolizer
        label: "${name}",
        //pointRadius: 6,
        pointerEvents: "visiblePainted",
        // label with \n linebreaks

        //fontColor: "${favColor}",
        fontSize: "1.5em",
        //fontFamily: "Courier New, monospace",
        //fontWeight: "bold",
        labelAlign: "left", //${align}",
        labelXOffset: "4", //${xOffset}",
        labelYOffset: "-12", //${yOffset}",
        //labelOutlineColor: "white",
        //labelOutlineWidth: 2
        externalGraphic: "images/opponent${IsTeam}.png",
        graphicWidth: "${IsFriend}",
        fillOpacity: 1
      }
    }),
    new OpenLayers.Rule(
      {
        // a rule contains an optional filter
        elsefilter: true,
        symbolizer:
        {}
      }

    )


  ]
});
 */

const WP_TWO_BUOYS = 0;
const WP_ONE_BUOY = 1;
const WP_GATE_BUOY_MASK = 0x000F;
/* leave space for 0-15 types of gates using buoys
   next is bitmasks */
const WP_DEFAULT = 0;
const WP_ICE_GATE_N = (1 << 4);
const WP_ICE_GATE_S = (1 << 5);
const WP_ICE_GATE_E = (1 << 6);
const WP_ICE_GATE_W = (1 << 7);
const WP_ICE_GATE = (WP_ICE_GATE_E | WP_ICE_GATE_N | WP_ICE_GATE_S | WP_ICE_GATE_W);
const WP_GATE_KIND_MASK = 0xFFF0;
/* allow crossing in one direction only */
const WP_CROSS_CLOCKWISE = (1 << 8);
const WP_CROSS_ANTI_CLOCKWISE = (1 << 9);
/* for future releases */
const WP_CROSS_ONCE = (1 << 10);

var Exclusions = [];

function DrawRaceGates(Boat)
{
  if (typeof Boat === "undefined" || !Boat || !Boat.RaceInfo)
  {
    // Not Ready to draw
    return;
  }
  let RaceInfo = Boat.RaceInfo;
  let NextGate = Boat.VLMInfo.NWP;
  let RaceFeature = GetRaceMapFeatures(Boat);

  // Loop all gates
  if (typeof RaceInfo !== undefined && RaceInfo && typeof RaceInfo.races_waypoints !== "undefined" && RaceInfo.races_waypoints)
  {
    for (let index in RaceInfo.races_waypoints)
    {
      if (!RaceFeature.Gates)
      {
        RaceFeature.Gates = [];
      }

      if (!RaceFeature.Gates[index])
      {
        RaceFeature.Gates[index] = {};
      }
      let GateFeatures = RaceFeature.Gates[index];

      if (RaceInfo.races_waypoints[index])
      {

        let WPMarker = GateFeatures.Buoy1;

        // Draw a single race gates
        var WP = RaceInfo.races_waypoints[index];

        // Fix coords scales
        NormalizeRaceInfo(RaceInfo);
        var cwgate = !(WP.wpformat & WP_CROSS_ANTI_CLOCKWISE);

        // Draw WP1
        let Pos = new VLMPosition(WP.longitude1, WP.latitude1);
        GateFeatures.Buoy1 = AddBuoyMarker(WPMarker, "WP" + index + " " + WP.libelle + '<BR>' + Pos.toString(), WP.longitude1, WP.latitude1, cwgate);


        // Second buoy (if any)
        if ((WP.wpformat & WP_GATE_BUOY_MASK) === WP_TWO_BUOYS)
        {
          // Add 2nd buoy marker
          let WPMarker = GateFeatures.Buoy2;
          let Pos = new VLMPosition(WP.longitude2, WP.latitude2);
          GateFeatures.Buoy2 = AddBuoyMarker(WPMarker, "WP" + index + " " + WP.libelle + '<BR>' + Pos.toString(), WP.longitude2, WP.latitude2, !cwgate);
        }
        else
        {
          // No Second buoy, compute segment end
          let P = new VLMPosition(WP.longitude1, WP.latitude1);
          let complete = false;
          let Dist = 2500;
          let Dest = null;
          while (!complete)
          {
            try
            {
              Dest = P.ReachDistLoxo(Dist, 180 + parseFloat(WP.laisser_au));
              complete = true;
            }
            catch (e)
            {
              Dist *= 0.7;
            }
          }

          WP.longitude2 = Dest.Lon.Value;
          WP.latitude2 = Dest.Lat.Value;
        }

        // Draw Gate Segment
        index = parseInt(index, 10);
        NextGate = parseInt(NextGate, 10);
        AddGateSegment(GateFeatures, WP.longitude1, WP.latitude1, WP.longitude2, WP.latitude2, (NextGate === index), (index < NextGate), (WP.wpformat & WP_GATE_KIND_MASK));
      }
    }
  }
}

function DrawRaceExclusionZones(Boat, Zones)
{
  if (!Boat)
  {
    return;
  }

  let Features = GetRaceMapFeatures(Boat);
  for (let index in Zones)
  {
    if (Zones[index])
    {
      DrawRaceExclusionZone(Features, Zones, index);
    }
  }

}

function DrawRaceExclusionZone(Features, ExclusionZones, ZoneIndex)
{

  let PointList = [];
  let HasZones = false;

  for (let index in ExclusionZones[ZoneIndex])
  {
    if (ExclusionZones[ZoneIndex][index])
    {
      var P = [ExclusionZones[ZoneIndex][index][0], ExclusionZones[ZoneIndex][index][1]];

      PointList.push(P);
      HasZones = true;
    }
  }

  if (HasZones)
  {
    if (typeof Features.Exclusions === "undefined")
    {
      Features.Exclusions = [];
    }

    if (Features.Exclusions[ZoneIndex])
    {
      Features.Exclusions[ZoneIndex].setLatLngs(PointList).addTo(map);
    }
    else
    {
      Features.Exclusions[ZoneIndex] = L.polygon(PointList,
      {
        color: "red",
        opacity: 0.25,
        weight: 3,
      }).addTo(map);
    }
  }
  else if (Features.Exclusions && Features.Exclusions[index])
  {
    Features.Exclusions[ZoneIndex].remove();
    Features.Exclusions[ZoneIndex] = null;
  }

}


function GetLonOffset(L1, L2)
{
  if (L1 * L2 >= 0)
  {
    return 0;
  }
  else if (Math.abs(L2 - L1) > 90)
  {
    if (L1 > 0)
    {
      return 360;
    }
    else
    {
      return -360;
    }
  }

  return 0;
}

function AddGateSegment(GateFeatures, lon1, lat1, lon2, lat2, IsNextWP, IsValidated, GateType)
{

  let Points = [
    [lat1, lon1],
    [lat2, lon2]
  ];

  let color = "";
  let strokeOpacity = 0.75;
  let strokeWidth = 1;
  if (IsNextWP)
  {
    color = "green";
  }
  else if (IsValidated)
  {
    color = "blue";
  }
  else
  {
    color = "red";
  }

  if (GateType & WP_CROSS_ONCE)
  {
    if (GateFeatures.Segment2)
    {
      GateFeatures.Segment2.setLatLngs(Points);
    }
    else
    {
      // Draw the segment again as dashed line for cross once gates
      GateFeatures.Segment2 = L.polyline(Points,
      {
        color: 'black',
        dashArray: '20,10,5,10',
        weight: strokeWidth * 2,
        opacity: strokeOpacity
      }).addTo(map);
    }
  }

  if (GateFeatures.Segment)
  {
    GateFeatures.Segment.setLatLngs(Points);
    GateFeatures.Segment.color = color;
  }
  else
  {
    GateFeatures.Segment = L.polyline(Points,
    {
      color: color,
      weight: strokeWidth,
      opacity: strokeOpacity
    }).addTo(map);
  }

  if (GateType !== WP_DEFAULT)
  {

    let P1 = new VLMPosition(lon1, lat1);
    let P2 = new VLMPosition(lon2, lat2);
    var MarkerDir = P1.GetLoxoCourse(P2);
    var MarkerPos = P1.ReachDistLoxo(P2, 0.5);
    // Gate has special features, add markers
    if (GateType & WP_CROSS_ANTI_CLOCKWISE)
    {
      MarkerDir -= 90;
      AddGateDirMarker(GateFeatures, MarkerPos.Lon.Value, MarkerPos.Lat.Value, MarkerDir);
    }
    else if (GateType & WP_CROSS_CLOCKWISE)
    {
      MarkerDir += 90;
      AddGateDirMarker(GateFeatures, MarkerPos.Lon.Value, MarkerPos.Lat.Value, MarkerDir);
    }
    else if (GateType & WP_ICE_GATE)
    {
      AddGateIceGateMarker(GateFeatures, MarkerPos.Lon.Value, MarkerPos.Lat.Value);
    }
  }



}

const MAX_BUOY_INDEX = 16;
var BuoyIndex = Math.floor(Math.random() * MAX_BUOY_INDEX);

function AddGateDirMarker(GateFeatures, Lon, Lat, Dir)
{
  AddGateCenterMarker(GateFeatures, Lon, Lat, "BuoyDirs/BuoyDir" + BuoyIndex + ".png", Dir, false);
  // Rotate dir marker...
  BuoyIndex++;
  BuoyIndex %= (MAX_BUOY_INDEX + 1);

}

function AddGateIceGateMarker(GateFeatures, Lon, Lat)
{
  AddGateCenterMarker(GateFeatures, Lon, Lat, "icegate.png", true);
}

function AddGateCenterMarker(GateFeatures, Lon, Lat, Marker, Dir, IsIceGate)
{
  let MarkerCoords = [Lat, Lon];

  if (GateFeatures.GateMarker)
  {
    GateFeatures.GateMarker.setLatLng(MarkerCoords);
  }
  else
  {
    let MarkerObj = GetGateTypeMarker(Marker, IsIceGate);
    GateFeatures.GateMarker = L.marker(MarkerCoords,
    {
      icon: MarkerObj
    }).addTo(map);
    if (!IsIceGate)
    {
      GateFeatures.GateMarker.setRotationAngle(Dir);
    }
  }
}


function AddBuoyMarker(Marker, Name, Lon, Lat, CW_Crossing)
{
  let WP = GetBuoyMarker(CW_Crossing);

  if (Marker)
  {
    if (Marker.IsCWBuoy !== CW_Crossing)
    {
      // Change marker direction
      Marker.remove();
    }
    else
    {
      return Marker.setLatLng([Lat, Lon]);
    }
  }
  return L.marker([Lat, Lon],
  {
    icon: WP
  }).addTo(map).bindPopup(Name);

}

const PM_HEADING = 1;
const PM_ANGLE = 2;
const PM_ORTHO = 3;
const PM_VMG = 4;
const PM_VBVMG = 5;

function SendVLMBoatWPPos(Boat, P)
{
  var orderdata = {
    idu: Boat.IdBoat,
    pip:
    {
      targetlat: P.Lat.Value,
      targetlong: P.Lon.Value,
      targetandhdg: -1 //Boat.VLMInfo.H@WP
    }

  };

  PostBoatSetupOrder(Boat.IdBoat, 'target_set', orderdata);
}

function SendVLMBoatOrder(Mode, AngleOrLon, Lat, WPAt)
{
  var request = {};

  var verb = "pilot_set";

  if (typeof _CurPlayer === 'undefined' || typeof _CurPlayer.CurBoat === 'undefined')
  {
    VLMAlertDanger("Must select a boat to send an order");
    return;
  }

  // Build WS command accoridng to required pilot mode
  switch (Mode)
  {
    case PM_HEADING:
    case PM_ANGLE:
      request = {
        idu: _CurPlayer.CurBoat.IdBoat,
        pim: Mode,
        pip: AngleOrLon
      };
      break;

    case PM_ORTHO:
    case PM_VBVMG:
    case PM_VMG:
      request = {
        idu: _CurPlayer.CurBoat.IdBoat,
        pim: Mode,
        pip:
        {
          targetlong: parseFloat(AngleOrLon),
          targetlat: parseFloat(Lat),
          targetandhdg: WPAt
        }
      };
      //PostBoatSetupOrder (_CurPlayer.CurBoat.IdBoat,"target_set",request);
      break;

    default:
      return;

  }

  // Post request
  PostBoatSetupOrder(_CurPlayer.CurBoat.IdBoat, verb, request);


}

function PostBoatSetupOrder(idu, verb, orderdata)
{
  // Now Post the order
  $.post("/ws/boatsetup/" + verb + ".php?selectidu" + idu,
    "parms=" + JSON.stringify(orderdata),
    function(Data, TextStatus)
    {
      if (Data.success)
      {
        RefreshCurrentBoat(false, true);
      }
      else
      {
        VLMAlertDanger(GetLocalizedString("BoatSetupError") + '\n' + Data.error.code + " " + Data.error.msg);
      }
    });

}

function EngageBoatInRace(RaceID, BoatID)
{
  $.post("/ws/boatsetup/race_subscribe.php",
    "parms=" + JSON.stringify(
    {
      idu: BoatID,
      idr: parseInt(RaceID, 10)
    }),
    function(data)
    {

      if (data.success)
      {
        let Msg = GetLocalizedString("youengaged");
        $("#RacesListForm").modal('hide');
        VLMAlertSuccess(Msg);
      }
      else
      {
        let Msg = data.error.msg + '\n' + data.error.custom_error_string;
        VLMAlertDanger(Msg);
      }
    }
  );
}

function DiconstinueRace(BoatId, RaceId)
{
  $.post("/ws/boatsetup/race_unsubscribe.php",
    "parms=" + JSON.stringify(
    {
      idu: BoatId,
      idr: parseInt(RaceId, 10)
    }),
    function(data)
    {

      if (data.success)
      {
        VLMAlertSuccess("Bye Bye!");
      }
      else
      {
        var Msg = data.error.msg + '\n' + data.error.custom_error_string;
        VLMAlertDanger(Msg);
      }
    }
  );
}

function LoadRealsList(Boat)
{
  if ((typeof Boat === "undefined") || !Boat || (typeof Boat.VLMInfo === "undefined"))
  {
    return;
  }

  $.get("/ws/realinfo/realranking.php?idr=" + Boat.VLMInfo.RAC,
    function(result)
    {
      if (result.success)
      {
        Boat.Reals = result;
        DrawBoat(Boat, false);
      }
      else
      {
        Boat.Reals = [];
      }
    }
  );
}

function LoadRankings(RaceId, CallBack)
{
  if (RaceId && (typeof RaceId === 'object'))
  {
    VLMAlertDanger("Not updated call to LoadRankings");
  }


  /*if ((typeof Boat === "undefined") || !Boat || (typeof Boat.VLMInfo === "undefined"))
  {
    return;
  }*/

  $.get("/cache/rankings/rnk_" + RaceId + ".json" /*?d=" + (new Date().getTime())*/ ,
    function(result)
    {
      if (result)
      {
        Rankings[RaceId] = result.Boats;
        if (CallBack)
        {
          CallBack();
        }
        else
        {
          DrawBoat(null, false);
        }
      }
      else
      {
        Rankings[RaceId] = null;
      }
    }
  );


}

function contains(a, obj)
{
  for (var i = 0; i < a.length; i++)
  {
    if (a[i] === obj)
    {
      return true;
    }
  }
  return false;
}

function DrawOpponents(Boat)
{
  if (!Boat || typeof Rankings === "undefined")
  {
    return;
  }

  // Get Friends
  let friends = [];
  let index;
  let RaceFeatures = GetRaceMapFeatures(Boat);
    

  // Map friend only if selection is active
  if (VLM2Prefs.MapPrefs.MapOppShow === VLM2Prefs.MapPrefs.MapOppShowOptions.ShowSel)
  {
    if ((typeof Boat.VLMInfo !== "undefined") && (typeof Boat.VLMInfo.MPO !== "undefined"))
    {
      friends = Boat.VLMInfo.MPO.split(',');
    }

    if (friends.length !== 0)
    {
      let RaceId = Boat.VLMInfo.RAC;
      for (index in friends)
      {
        if (friends[index] && Rankings[RaceId])
        {
          let Opp = Rankings[RaceId][friends[index]];

          if ((typeof Opp !== 'undefined') && (parseInt(Opp.idusers, 10) !== Boat.IdBoat))
          {
            AddOpponent(Boat, RaceFeatures, Opp, true);      
          }
        }
      }
    }
  }
  // Get Reals
  if (VLM2Prefs.MapPrefs.ShowReals && (typeof Boat.Reals !== "undefined") && (typeof Boat.Reals.ranking !== "undefined"))
    for (index in Boat.Reals.ranking)
    {
      var RealOpp = Boat.Reals.ranking[index];
      AddOpponent(Boat, RaceFeatures, RealOpp, true);
    }

  let MAX_LEN = 150;
  let ratio = MAX_LEN / Object.keys(Rankings).length;
  let count = 0;
  let BoatList = Rankings;

  if (typeof Boat.OppList !== "undefined" && Boat.OppList.length > 0)
  {
    BoatList = Boat.OppList;
    ratio = 1;
  }

  switch (VLM2Prefs.MapPrefs.MapOppShow)
  {
    case VLM2Prefs.MapPrefs.MapOppShowOptions.Show10Around:
      BoatList = GetClosestOpps(Boat, 10);
      ratio = 1;
      break;
    case VLM2Prefs.MapPrefs.MapOppShowOptions.Show5Around:
      BoatList = GetClosestOpps(Boat, 5);
      ratio = 1;
      break;
    case VLM2Prefs.MapPrefs.MapOppShowOptions.ShowTop10:
      let BoatCount = 0;
      let RaceID = Boat.Engaged;
      MAX_LEN = VLM2Prefs.MapPrefs.ShowTopCount;

      BoatList = [];

      for (index in Rankings[RaceID])
      {
        if (Rankings[RaceID][index].rank <= VLM2Prefs.MapPrefs.ShowTopCount)
        {
          BoatList[index] = Rankings[RaceID][index];
          BoatCount++;
          if (BoatCount > MAX_LEN)
          {
            break;
          }
        }
      }

      if (BoatCount > MAX_LEN)
      {
        MAX_LEN = BoatCount;
      }
      ratio = 1;
      break;

    case VLM2Prefs.MapPrefs.MapOppShowOptions.ShowMineOnly:
      BoatList = [];
      ratio = 1;
      break;

  }

  // Sort racers to be able to show proper opponents
  SortRankingData(Boat, 'RAC', null, Boat.Engaged);

  if (Boat.Engaged && typeof Rankings[Boat.Engaged] !== "undefined" && typeof Rankings[Boat.Engaged].RacerRanking !== "undefined" && Rankings[Boat.Engaged].RacerRanking)
  {
    for (index in Rankings[Boat.Engaged].RacerRanking)
    {
      if (index in Rankings[Boat.Engaged].RacerRanking)
      {
        var Opp = Rankings[Boat.Engaged].RacerRanking[index];

        if ((parseInt(Opp.idusers, 10) !== Boat.IdBoat) && BoatList[Opp.idusers] && (!contains(friends, Opp.idusers)) && RnkIsRacing(Opp) && (Math.random() <= ratio) && (count < MAX_LEN))
        {
          AddOpponent(Boat, RaceFeatures, Opp, false);
          count += 1;
          if (typeof Boat.OppList === "undefined")
          {
            Boat.OppList = [];
          }
          Boat.OppList[index] = Opp;
        }
        else if (count >= MAX_LEN)
        {
          break;
        }
      }
    }
  }

  // Draw OppTracks, if any is selected
  if (typeof Boat.RaceMapFeatures !== "undefined" && Object.keys(Boat.OppTrack).length > 0)
  {
    let RaceFeatures = Boat.RaceMapFeatures;
    for (let TrackIndex in Boat.OppTrack)
    {
      var T = Boat.OppTrack[TrackIndex];

      if (T && T.Visible && T.DatePos.length > 1)
      {
        if (!T.OppTrackPoints)
        {
          let TrackPoints = [];
          let TLen = Object.keys(T.DatePos).length;
          for (let PointIndex = 0; PointIndex < TLen; PointIndex++)
          {
            let k = Object.keys(T.DatePos)[PointIndex];
            let P = T.DatePos[k];
            let Pi = [P.lat, P.lon];

            TrackPoints.push(Pi);
          }
          T.OppTrackPoints = TrackPoints;
        }

        if (typeof RaceFeatures.OppTrack === "undefined")
        {
          RaceFeatures.OppTrack = [];
        }

        if (RaceFeatures.OppTrack[TrackIndex])
        {
          RaceFeatures.OppTrack[TrackIndex].setLatLngs(T.OppTrackPoints).addTo(map);
        }
        else
        {
          let color = 'black';

          if (typeof T.TrackColor !== "undefined")
          {
            color = T.TrackColor;
          }
          let TrackStyle = {
            color: color,
            weight: 1,
            opacity: 0.75
          };
          RaceFeatures.OppTrack[TrackIndex] = L.polyline(T.OppTrackPoints, TrackStyle).addTo(map);
        }
        T.LastShow = new Date();
      }
      else if (Boat.RaceMapFeatures.OppTrack && Boat.RaceMapFeatures.OppTrack[TrackIndex])
      {
        Boat.RaceMapFeatures.OppTrack[TrackIndex].remove();
      }
    }
  }

}

function CompareDist(a, b)
{
  if (a.dnm < b.dnm)
    return -1;
  if (a.dnm > b.dnm)
    return 1;
  return 0;
}

function GetClosestOpps(Boat, NbOpps)
{
  let RaceId = null;

  if (Boat && Boat.VLMInfo)
  {
    RaceId = Boat.VLMInfo.RAC;
  }
  let RetArray = [];

  if (RaceId && Rankings[RaceId])
  {
    let CurBoat = Rankings[RaceId][Boat.IdBoat];

    if (typeof CurBoat === 'undefined' || !Boat)
    {
      CurBoat = {
        dnm: 0,
        nwp: 1
      };
    }
    let CurDnm = parseFloat(CurBoat.dnm);
    let CurWP = CurBoat.nwp;
    let List = [];

    for (let index in Rankings[RaceId])
    {
      if (Rankings[RaceId][index])
      {
        if (CurWP === Rankings[RaceId][index].nwp)
        {
          var O = {
            id: index,
            dnm: Math.abs(CurDnm - parseFloat(Rankings[RaceId][index].dnm))
          };
          List.push(O);
        }
      }
    }


    List = List.sort(CompareDist);
    for (let index in List.slice(0, NbOpps - 1))
    {
      RetArray[List[index].id] = Rankings[RaceId][List[index].id];
    }
  }
  return RetArray;

}

function AddOpponent(Boat, RaceFeatures, Opponent, isFriend)
{
  let Opp_Coords = [Opponent.latitude, Opponent.longitude];
  let ZFactor = 8; //map.getZoom();
  let OppData = {
    "name": Opponent.idusers,
    "Coords": new VLMPosition(Opponent.longitude, Opponent.latitude).toString(),
    "type": 'opponent',
    "idboat": Opponent.idusers,
    "rank": Opponent.rank,
    "Last1h": Opponent.last1h,
    "Last3h": Opponent.last3h,
    "Last24h": Opponent.last24h,
    "IsTeam": (Opponent.country == Boat.VLMInfo.CNT) ? "team" : "",
    "IsFriend": (isFriend ? ZFactor * 2 : ZFactor),
    "color": Opponent.color
  };

  if (!VLM2Prefs.MapPrefs.ShowOppNumbers)
  {
    OppData.name = "";
  }

  if (typeof RaceFeatures.Opponents === "undefined")
  {
    RaceFeatures.Opponents = [];
  }

  if (RaceFeatures.Opponents[Opponent.idusers])
  {
    RaceFeatures.Opponents[Opponent.idusers].setLatLng(Opp_Coords);
  }
  else
  {
    let OppMarker = GetOpponentMarker(OppData);
    RaceFeatures.Opponents[Opponent.idusers] = L.marker(Opp_Coords,
    {
      icon: OppMarker
    }).addTo(map);
    RaceFeatures.Opponents[Opponent.idusers].on('click', HandleOpponentClick);
    RaceFeatures.Opponents[Opponent.idusers].on('mouseover', HandleOpponentOver);
    RaceFeatures.Opponents[Opponent.idusers].IdUsers = Opponent.idusers;
  }

}

function ShowOpponentPopupInfo(e)
{
  let Opp = e.sourceTarget;

  if (Opp && Opp.options && Opp.options.icon && typeof Opp.options.icon.MarkerOppId !== "undefined")
  {
    let Boat = GetOppBoat(Opp.options.icon.MarkerOppId);
    if (Boat)
    {
      let Pos = new VLMPosition(Boat.longitude, Boat.latitude);
      let Features = GetRaceMapFeatures(_CurPlayer.CurBoat);

      if (Features)
      {

        let PopupStr = BuildBoatPopupInfo(Boat);
        if (!Features.OppPopup)
        {
          Features.OppPopup = L.popup(PopupStr);
        }
        if (Features.OppPopup.PrevOpp)
        {
          Features.OppPopup.PrevOpp.unbindPopup();
        }
        Opp.bindPopup(Features.OppPopup);
        Features.OppPopup.setContent(PopupStr);
        Features.OppPopup.PrevOpp = Opp;

        let PopupFields = [];
        let OppId = Opp.options.icon.MarkerOppId;
        Opp.openPopup();
        PopupFields.push([FIELD_MAPPING_TEXT, "#__BoatName" + OppId, Boat.boatname]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__BoatId" + OppId, Boat.idusers]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__BoatRank" + OppId, Boat.rank]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__BoatLoch" + OppId, RoundPow(parseFloat(Boat.loch), 2)]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__BoatNWP" + OppId, "[" + Boat.nwp + "] " + RoundPow(parseFloat(Boat.dnm), 2)]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__BoatPosition" + OppId, Pos.GetVLMString()]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__Boat1HAvg" + OppId, RoundPow(parseFloat(Boat.last1h), 2)]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__Boat3HAvg" + OppId, RoundPow(parseFloat(Boat.last3h), 2)]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__Boat24HAvg" + OppId, RoundPow(parseFloat(Boat.last24h), 2)]);
        PopupFields.push([FIELD_MAPPING_STYLE, "#__BoatColor" + OppId, "background-color", SafeHTMLColor(Boat.color)]);
        FillFieldsFromMappingTable(PopupFields);

      }
    }
  }

}

function GetOppBoat(BoatId)
{
  let CurBoat = _CurPlayer.CurBoat;

  if (typeof CurBoat !== "undefined" && CurBoat && CurBoat.OppList)
  {
    for (let i in CurBoat.OppList)
    {
      if (CurBoat.OppList[i])
      {
        let Opp = CurBoat.OppList[i];
        if (Opp.idusers === BoatId)
        {
          return Opp;
        }
      }
    }
    if (CurBoat.Reals && CurBoat.Reals.ranking)
    {
      for (let i in CurBoat.Reals.ranking)
      {
        if (CurBoat.Reals.ranking[i])
        {
          let Opp = CurBoat.Reals.ranking[i];
          if (Opp.idusers === BoatId)
          {
            return Opp;
          }
        }
      }
    }
  }

  return null;
}

function BuildBoatPopupInfo(Boat)
{
  if (!Boat || !Boat.idusers)
  {
    return null;
  }
  let BoatId = Boat.idusers;

  let RetStr =
    '<div class="MapPopup_InfoHeader">' +
    GetCountryFlagImgHTML(Boat.country) +
    ' <span id="__BoatName' + BoatId + '" class="PopupBoatNameNumber ">BoatName</span>' +
    ' <span id="__BoatId' + BoatId + '" class="PopupBoatNameNumber ">BoatNumber</span>' +
    ' <div id="__BoatRank' + BoatId + '" class="TxtRank">Rank</div>' +
    '</div>' +
    '<div id="__BoatColor' + BoatId + '" style="height: 2px;"></div>' +
    '<div class="MapPopup_InfoBody">' +
    ' <fieldset>' +
    '   <span class="PopupHeadText " I18n="loch">' + GetLocalizedString('loch') + '</span><span class="PopupText"> : </span><span id="__BoatLoch' + BoatId + '" class="loch PopupText">0.9563544</span>' +
    '   <BR><span class="PopupHeadText " I18n="position">' + GetLocalizedString('position') + '</span><span class="PopupText"> : </span><span id="__BoatPosition' + BoatId + '" class=" PopupText">0.9563544</span>' +
    '   <BR><span class="PopupHeadText " I18n="NextWP">' + GetLocalizedString('NextWP') + '</span><span class="strong"> : </span><span id="__BoatNWP' + BoatId + '" class="PopupText">[1] 4.531856536865234</span>' +
    '   <BR><span class="PopupHeadText " I18n="Moyennes">' + GetLocalizedString('Moyennes') + ' </span><span class="PopupText"> : </span>' +
    '   <span class="PopupHeadText ">[1h]</span><span id="__Boat1HAvg' + BoatId + '" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span>' +
    '   <span class="PopupHeadText ">[3h]</span><span id="__Boat3HAvg' + BoatId + '" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span>' +
    '   <span class="PopupHeadText ">[24h]</span><span id="__Boat24HAvg' + BoatId + '" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span>' +
    ' </fieldset>' +
    '</div>';


  return RetStr;
}

function HandleOpponentOver(e)
{
  let Opponent = e.sourceTarget;
  let index;
  let RaceFeatures = GetRaceMapFeatures(_CurPlayer.CurBoat);
  let OppIndex = Opponent.IdUsers;

  if (OppIndex)
  {
    for (index in RaceFeatures.OppTrack)
    {
      let ShowTrack = (index === OppIndex);
      _CurPlayer.CurBoat.OppTrack[index].Visible = ShowTrack;
    }

    DrawOpponentTrack(OppIndex, RaceFeatures.Opponents[OppIndex]);
  }
}

function HandleOpponentClick(e)
{
  // Clicking oppenent will show the track, and popup info (later)
  HandleOpponentOver(e);
  ShowOpponentPopupInfo(e);
}

function HandleFeatureOut(e)
{

  if (typeof _CurPlayer === "undefined" || (!_CurPlayer) || typeof _CurPlayer.CurBoat === "undefined" || (!_CurPlayer.CurBoat) || typeof _CurPlayer.CurBoat.OppTrack === "undefined")
  {
    return;
  }

  // Clear previously displayed tracks.
  for (let index in _CurPlayer.CurBoat.OppTrack)
  {
    _CurPlayer.CurBoat.OppTrack[index].Visible = false;
  }


}

var TrackPendingRequests = [];

var LastTrackRequest = 0;

function DrawOpponentTrack(IdBoat, OppInfo)
{
  let B = _CurPlayer.CurBoat;
  let CurDate = new Date();
  let PendingID = null;
  if (typeof B !== "undefined" && B && CurDate > LastTrackRequest)
  {
    LastTrackRequest = new Date(CurDate / 1000 + 0.5);
    if (typeof B.OppTrack !== "undefined" || !(IdBoat in B.OppTrack) || (IdBoat in B.OppTrack && (B.OppTrack[IdBoat].LastShow <= new Date(B.VLMInfo.LUP * 1000))))
    {

      let StartTime = new Date() / 1000 - 48 * 3600;
      let IdRace = B.VLMInfo.RAC;
      let CurDate = new Date();
      PendingID = IdBoat.toString() + "/" + IdRace.toString();

      if (IdBoat in B.OppTrack)
      {
        B.OppTrack[IdBoat].Visible = true;
      }

      if (!(PendingID in TrackPendingRequests) || (CurDate > TrackPendingRequests[PendingID]))
      {
        TrackPendingRequests[PendingID] = new Date(CurDate.getTime() + 60 * 1000);
        console.log("GetTrack " + PendingID + " " + StartTime);
        if (parseInt(IdBoat) > 0)
        {
          GetBoatTrack(B, IdBoat, IdRace, StartTime, OppInfo);
        }
        else if (parseInt(IdBoat))
        {
          GetRealBoatTrack(B, IdBoat, IdRace, StartTime, OppInfo);
        }
      }
    }
    else
    {
      console.log(" GetTrack ignore before next update" + PendingID + " " + StartTime);
    }
    DrawBoat(B);
  }
}

function GetRealBoatTrack(Boat, IdBoat, IdRace, StartTime, OppInfo)
{
  $.get("/ws/realinfo/tracks.php?idr=" + IdRace + "&idreals=" + (-IdBoat) + "&starttime=" + StartTime,
    function(e)
    {
      if (e.success)
      {
        AddBoatOppTrackPoints(Boat, IdBoat, e.tracks, OppInfo.color);
        RefreshCurrentBoat(false, false);
      }
    }
  );
}

var TrackRequestPending = false;

function GetBoatTrack(Boat, IdBoat, IdRace, StartTime, OppInfo)
{
  if (TrackRequestPending)
  {
    return;
  }
  else
  {
    TrackRequestPending = true;
  }
  $.get("/ws/boatinfo/smarttracks.php?idu=" + IdBoat + "&idr=" + IdRace + "&starttime=" + StartTime,
    function(e)
    {
      TrackRequestPending = false;
      if (e.success)
      {
        var index;

        AddBoatOppTrackPoints(Boat, IdBoat, e.tracks, OppInfo.Color);

        for (index in e.tracks_url)
        {
          if (index > 10)
          {
            break;
          }
          /* jshint -W083*/
          $.get('/cache/tracks/' + e.tracks_url[index],
            function(e)
            {
              if (e.success)
              {
                AddBoatOppTrackPoints(Boat, IdBoat, e.tracks, OppInfo.Color);
                DrawOpponents(Boat);
              }
            }
          );
          /* jshint +W083*/

        }
        DrawOpponents(Boat);
      }
    }
  );

}

function AddBoatOppTrackPoints(Boat, IdBoat, Track, TrackColor)
{


  if (!(IdBoat in Boat.OppTrack))
  {
    TrackColor = SafeHTMLColor(TrackColor);

    Boat.OppTrack[IdBoat] = {
      LastShow: 0,
      TrackColor: TrackColor,
      DatePos: [],
      Visible: true,
      OppTrackPoints: null
    };
  }

  //
  for (let index in Track)
  {
    var Pos = Track[index];

    Boat.OppTrack[IdBoat].DatePos[Pos[0]] = {
      lat: Pos[2] / 1000,
      lon: Pos[1] / 1000
    };
  }
  Boat.OppTrack[IdBoat].LastShow = 0;
  Boat.OppTrack[IdBoat].OppTrackPoints = [];
  Boat.OppTrack[IdBoat].OppTrackPoints = null;
}

function DeletePilotOrder(Boat, OrderId)
{
  $.post("/ws/boatsetup/pilototo_delete.php?", "parms=" + JSON.stringify(
    {
      idu: Boat.IdBoat,
      taskid: parseInt(OrderId)
    }),
    function(e)
    {
      if (e.success)
      {
        RefreshCurrentBoat(false, true, 'AutoPilot');
      }
    }
  );
}

function UpdateBoatPrefs(Boat, NewVals)
{
  NewVals.idu = Boat.IdBoat;
  $.post("/ws/boatsetup/prefs_set.php", "parms=" + JSON.stringify(NewVals),
    function(e)
    {
      if (e.success)
      {
        // avoid forced full round trip
        RefreshCurrentBoat(false, false);
      }
      else
      {
        VLMAlertDanger(GetLocalizedString("UpdateFailed"));
      }
    }
  );
}

function LoadVLMPrefs()
{
  var Boat;

  if (typeof _CurPlayer === "undefined")
  {
    return;
  }
  Boat = _CurPlayer.CurBoat;

  SetDDTheme(VLM2Prefs.CurTheme);

  $.get("/ws/boatinfo/prefs.php?idu=" + Boat.IdBoat, HandlePrefsLoaded);
}

function HandlePrefsLoaded(e)
{
  if (e.success)
  {
    var Boat = _CurPlayer.CurBoat;

    Boat.VLMPrefs = e.prefs;
    VLM2Prefs.UpdateVLMPrefs(e.prefs);
  }
  else
  {
    VLMAlertDanger("Error communicating with VLM, try reloading the browser page...");
  }
}

function HandleWPDragEnded(e)
{
  let bkp = 0;
  let Marker = _CurPlayer.CurBoat.RaceMapFeatures.TrackWP;
  CompleteWPSetPosition(Marker);
  VLMAlertInfo("User WP moved to " + Marker.getLatLng());
}