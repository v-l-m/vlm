<<<<<<< HEAD
"use strict";

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

var MAP_OP_SHOW_SEL = 0;
var VLM2Prefs = new PrefMgr();
VLM2Prefs.Init();

function LoadLocalPref(PrefName, PrefDfaultValue) {
  var ret = store.get(PrefName);

  if (typeof ret === "undefined") {
    ret = PrefDfaultValue;
  }

  return ret;
}

function PrefMgr() {
  this.MapPrefs = new MapPrefs();
  this.CurTheme = "bleu-noir";
  this.MapPrefs = new MapPrefs();

  this.Init = function () {
    this.MapPrefs.Load();
    this.Load();
  };

  this.Load = function () {
    if (store.enabled) {
      this.CurTheme = LoadLocalPref('CurTheme', "bleu-noir");
    }
  };

  this.Save = function () {
    if (store.enabled) {
      store.set('ColorTheme', this.CurTheme);
    }

    this.MapPrefs.Save();
  };

  this.UpdateVLMPrefs = function (p) {
    switch (p.mapOpponents) {
      case "mylist":
      case "mapselboats":
      case "NULL":
      case "null":
      case "all":
        this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.ShowSel;
        break;

      case "meandtop10":
        this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.ShowTop10;
        break;

      case "my10opps":
        this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.Show10Around;
        break;

      case "my5opps":
        this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.Show5Around;
        break;

      case "maponlyme":
        this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.Show5Around;
        break;

      case "myboat":
        this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.ShowMineOnly;
        break;

      default:
        VLMAlertDanger("unexepected mapping option : " + p.mapOpponents);
    }
  };
}

function MapPrefs() {
  this.ShowReals = true; // Do we show reals?

  this.ShowOppNumbers = true; // Do we show opponents names?

  this.MapOppShow = null; // Which opponents do we show on the map

  this.MapOppShowOptions = {
    ShowSel: 0,
    ShowMineOnly: 1,
    Show5Around: 2,
    ShowTop10: 3,
    Show10Around: 4
  };
  this.WindArrowsSpacing = 64; // Spacing steps for wind arrow drawing

  this.MapZoomLevel = 4;
  this.PolarVacCount = 12; // How many vacs for drawing the polar line

  this.UseUTC = false; // USe local of UTC time format for display

  this.EstTrackMouse = false;
  this.TrackEstForecast = true;
  this.ShowTopCount = 50;

  this.Load = function () {
    if (store.enabled) {
      this.ShowReals = LoadLocalPref('#ShowReals', true);
      this.ShowOppNumbers = LoadLocalPref("#ShowOppNumbers", false);
      this.MapZoomLevel = LoadLocalPref("#MapZoomLevel", 4);
      this.UseUTC = LoadLocalPref("#UseUTC", false);
      this.EstTrackMouse = LoadLocalPref("#EstTrackMouse", true);
      this.TrackEstForecast = LoadLocalPref("#TrackEstForecast", false);
      this.PolarVacCount = LoadLocalPref("#PolarVacCount", 12);

      if (!this.PolarVacCount) {
        // Fallback if invalid value is stored
        this.PolarVacCount = 12;
      }

      this.ShowTopCount = LoadLocalPref('ShowTopCount', 50);
    }
  };

  this.Save = function () {
    if (store.enabled) {
      store.set("#ShowReals", this.ShowReals);
      store.set("#ShowOppNumbers", this.ShowOppName);
      store.set("#MapZoomLevel", this.MapZoomLevel);
      store.set("#PolarVacCount", this.PolarVacCount);
      store.set("#UseUTC", this.UseUTC);
      store.set("#TrackEstForecast", this.TrackEstForecast);
      store.set("#EstTrackMouse", this.EstTrackMouse);
      store.set("ShowTopCount", this.ShowTopCount);
    }

    var MapPrefVal = "mapselboats";

    switch (this.MapOppShow) {
      case this.MapOppShowOptions.ShowMineOnly:
        MapPrefVal = "myboat";
        break;

      case this.MapOppShowOptions.Show5Around:
        MapPrefVal = "my5opps";
        break;

      case this.MapOppShowOptions.ShowTop10:
        MapPrefVal = "meandtop10";
        break;

      case this.MapOppShowOptions.Show10Around:
        MapPrefVal = "my10opps";
        break;
    }

    var NewVals = {
      mapOpponents: MapPrefVal
    };

    if (typeof _CurPlayer !== "undefined" && _CurPlayer) {
      UpdateBoatPrefs(_CurPlayer.CurBoat, {
        prefs: NewVals
      });
    }
  };

  this.GetOppModeString = function (Mode) {
    switch (Mode) {
      case this.MapOppShowOptions.ShowSel:
        return GetLocalizedString("mapselboats");

      case this.MapOppShowOptions.ShowMineOnly:
        return GetLocalizedString("maponlyme");

      case this.MapOppShowOptions.Show5Around:
        return GetLocalizedString("mapmy5opps");

      case this.MapOppShowOptions.ShowTop10:
        return GetLocalizedString("mapmeandtop10");

      case this.MapOppShowOptions.Show10Around:
        return GetLocalizedString("mapmy10opps");

      default:
        return Mode;
    }
  };
} //
// Class to handle autopilot orders and services
//


function AutoPilotOrder(Boat, Number) {
  // Default construction
  this.Date = new Date(new Date().getTime() - new Date().getTime() % (5 * 60 * 1000) + 5 * 1.5 * 60 * 1000);
  this.PIM = PM_HEADING;
  this.PIP_Value = 0;
  this.PIP_Coords = new VLMPosition(0, 0);
  this.PIP_WPAngle = -1;
  this.ID = -1;

  if (typeof Boat !== 'undefined' && Boat) {
    if (!(Number - 1 in Boat.VLMInfo.PIL)) {
      alert("Invalid Pilototo order number. Report error to devs.");
      return;
    }

    var PilOrder = Boat.VLMInfo.PIL[Number - 1];
    this.Date = new Date(parseInt(PilOrder.TTS, 10) * 1000);
    this.PIM = parseInt(PilOrder.PIM, 10);
    this.ID = parseInt(PilOrder.TID, 10);

    switch (this.PIM) {
      case PM_ANGLE:
      case PM_HEADING:
        this.PIP_Value = parseInt(PilOrder.PIP, 10);
        break;

      case PM_ORTHO:
      case PM_VMG:
      case PM_VBVMG:
        var l1 = PilOrder.PIP.split(",");
        var l2 = l1[1].split("@");
        this.PIP_Coords.Lat.Value = parseFloat(l1[0]);
        this.PIP_Coords.Lon.Value = parseFloat(l2[0]);
        this.PIP_WPAngle = parseFloat(l2[1]);
        break;
    }
  }

  this.GetOrderDateString = function () {
    return this.Date.getDate() + "/" + (this.Date.getMonth() + 1) + "/" + this.Date.getFullYear();
  };

  this.GetOrderTimeString = function () {
    return this.Date.getHours() + ":" + this.Date.getMinutes() + ":15";
  };

  this.GetPIMString = function () {
    switch (this.PIM) {
      case PM_HEADING:
        return GetLocalizedString("autopilotengaged");

      case PM_ANGLE:
        return GetLocalizedString("constantengaged");

      case PM_ORTHO:
        return GetLocalizedString("orthodromic");

      case PM_VMG:
        return "VMG";

      case PM_VBVMG:
        return "VBVMG";
    }
  };

  this.GetPIPString = function () {
    switch (this.PIM) {
      case PM_HEADING:
      case PM_ANGLE:
        return this.PIP_Value;

      case PM_ORTHO:
      case PM_VMG:
      case PM_VBVMG:
        return this.PIP_Coords.GetVLMString() + "@" + PIP_WPAngle;
    }
  };
}

function HandleSendAPUpdate(e) {
  var verb = 'add';

  if (typeof _CurAPOrder === "undefined" || !_CurAPOrder) {
    return;
  }

  var OrderData = {
    idu: _CurPlayer.CurBoat.IdBoat,
    tasktime: Math.round(_CurAPOrder.Date / 1000),
    pim: _CurAPOrder.PIM
  };

  if (_CurAPOrder.ID !== -1) {
    verb = "update";
    OrderData.taskid = _CurAPOrder.ID;
  }

  switch (_CurAPOrder.PIM) {
    case PM_HEADING:
    case PM_ANGLE:
      OrderData.pip = _CurAPOrder.PIP_Value;
      break;

    case PM_ORTHO:
    case PM_VMG:
    case PM_VBVMG:
      OrderData.pip = {};
      OrderData.pip.targetlat = _CurAPOrder.PIP_Coords.Lat.Value;
      OrderData.pip.targetlong = _CurAPOrder.PIP_Coords.Lon.Value;
      OrderData.pip.targetandhdg = _CurAPOrder.PIP_WPAngle === -1 ? null : _CurAPOrder.PIP_WPAngle;
      break;
  }

  $.post('/ws/boatsetup/pilototo_' + verb + '.php', "parms=" + JSON.stringify(OrderData), function (ap_return) {
    if (ap_return.success) {
      // Order Success
      RefreshCurrentBoat(false, true, 'AutoPilot');
    } else {
      alert(ap_return.error.msg);
    }
  });
}

function HandleAPFieldChange(e) {
  var Target = e.target;

  if (typeof Target.attributes.id === "undefined") {
    return;
  }

  switch (Target.attributes.id.value) {
    case "AP_PIP":
      _CurAPOrder.PIP_Value = parseFloat(Target.value);

      if (_CurAPOrder.PIP_Value.toString() !== Target.Value) {
        Target.value = _CurAPOrder.PIP_Value.toString();
      }

      break;

    case "AP_WPLat":
      CheckFloatInput(_CurAPOrder.PIP_Coords.Lat, Target);
      break;

    case "AP_WPLon":
      CheckFloatInput(_CurAPOrder.PIP_Coords.Lon, Target);
      break;

    case "AP_WPAt":
      var Stub = {}; // beurk beurk

      Stub.Value = _CurAPOrder.PIP_WPAngle;
      CheckFloatInput(Stub, Target);
      _CurAPOrder.PIP_WPAngle = Stub.Value;
      break;
  }
}

function CheckFloatInput(DestObj, SrcObj) {
  var ObjValue;

  if (_typeof(DestObj) === "object") {
    DestObj.Value = parseFloat(SrcObj.value);
    ObjValue = DestObj.Value;
  } else {
    DestObj = parseFloat(SrcObj.value);
    ObjValue = DestObj;
  }

  if (ObjValue.toString() !== SrcObj.Value) {
    SrcObj.value = ObjValue.toString();
  }
}

function BoatEstimate(Est) {
  this.Position = null;
  this.Date = null;
  this.PrevDate = null;
  this.Mode = null;
  this.Value = null;
  this.Meteo = null;
  this.CurWP = new VLMPosition(0, 0);
  this.HdgAtWP = -1;
  this.RaceWP = 1;
  this.Heading = null;

  if (typeof Est !== "undefined" && Est) {
    this.Position = new VLMPosition(Est.Position.Lon.Value, Est.Position.Lat.Value);
    this.Date = new Date(Est.Date);
    this.PrevDate = new Date(Est.PrevDate);
    this.Mode = Est.Mode;
    this.Value = Est.Value;

    if (typeof Est.Meteo !== "undefined" && Est.Meteo) {
      this.Meteo = new WindData({
        Speed: Est.Meteo.Speed,
        Heading: Est.Meteo.Heading
      });
    }

    this.CurWP = Est.CurWP;
    this.RaceWP = Est.RaceWP;
    this.Heading = Est.Heading;
  }
}

function Estimator(Boat) {
  if (typeof Boat === 'undefined' || !Boat) {
    throw "Boat must exist for tracking....";
  }

  this.Boat = Boat;
  this.MaxVacEstimate = 0;
  this.CurEstimate = new BoatEstimate();
  this.Running = false;
  this.EstimateTrack = [];
  this.ProgressCallBack = null;
  this.ErrorCount = 0;
  this.EstimateMapFeatures = []; // Current estimate position

  this.Stop = function () {
    // Stop the estimator if Running
    if (this.Running) {
      this.Running = false;
      this.ReportProgress(true); //Estimate complete, DrawBoat track

      DrawBoat(this.Boat);
    }

    return;
  };

  this.Start = function (ProgressCallBack) {
    this.ProgressCallBack = ProgressCallBack;

    if (this.Running) {
      return;
    }

    this.Running = true;
    GribMgr.Init();

    if (typeof this.Boat.VLMInfo === "undefined") {
      this.Stop();
      return;
    }

    this.CurEstimate.Position = new VLMPosition(this.Boat.VLMInfo.LON, this.Boat.VLMInfo.LAT);
    this.CurEstimate.Date = new Date(this.Boat.VLMInfo.LUP * 1000 + 1000 * this.Boat.VLMInfo.VAC);
    this.CurEstimate.PrevDate = this.CurEstimate.Date;

    if (this.CurEstimate.Date < new Date()) {
      if (typeof this.Boat.RaceInfo === "undefined") {
        // Use cur date for estimate before start
        this.CurEstimate.Date = new Date();
      } else {
        // Set Start to 1st VAC after start +6s 
        this.CurEstimate.PrevDate = new Date(parseInt(this.Boat.RaceInfo.deptime, 10) * 1000 + 6000);

        if (this.CurEstimate.PrevDate < new Date()) {
          // If this is before current date then set to next current vac time
          var VacTime = new Date().getTime() / 1000;
          VacTime -= VacTime % this.Boat.VLMInfo.VAC;
          this.CurEstimate.PrevDate = new Date(VacTime * 1000 + 6000);
        }

        var StartDate = new Date(this.CurEstimate.PrevDate.getTime() + 1000 * this.Boat.VLMInfo.VAC);
        this.CurEstimate.Date = StartDate;
      }
    }

    this.CurEstimate.Mode = parseInt(this.Boat.VLMInfo.PIM, 10);
    this.CurEstimate.CurWP = new VLMPosition(this.Boat.VLMInfo.WPLON, this.Boat.VLMInfo.WPLAT);
    this.CurEstimate.HdgAtWP = parseFloat(this.Boat.VLMInfo["H@WP"]);
    this.CurEstimate.RaceWP = parseInt(this.Boat.VLMInfo.NWP, 10);

    if (this.CurEstimate.Mode == PM_HEADING || this.CurEstimate.Mode == PM_ANGLE) {
      this.CurEstimate.Value = parseFloat(this.Boat.VLMInfo.PIP);
    }

    this.CurEstimate.PilOrders = [];

    for (var _index in this.Boat.VLMInfo.PIL) {
      var Order = this.Boat.VLMInfo.PIL[_index];
      var NewOrder = {
        PIP: Order.PIP,
        PIM: Order.PIM,
        STS: Order.STS,
        TTS: Order.TTS
      };
      this.CurEstimate.PilOrders.push(NewOrder);
    }

    this.EstimateTrack = [];
    this.MaxVacEstimate = new Date(GribMgr.MaxWindStamp);
    this.ReportProgress(false); // Add Start point to estimate track

    this.EstimateTrack.push(new BoatEstimate(this.CurEstimate));
    this.ErrorCount = 0;
    setTimeout(this.Estimate.bind(this), 0);
  };

  this.Estimate = function (Boat) {
    if (!this.Running || this.CurEstimate.Date >= this.MaxVacEstimate) {
      this.Stop();
      return;
    }

    var MI; // let Lat = RoundPow(1000.0 * this.CurEstimate.Position.Lat.Value, 0) / 1000.0;
    // let Lon = RoundPow(1000.0 * this.CurEstimate.Position.Lon.Value, 0) / 1000.0;

    var Lat = this.CurEstimate.Position.Lat.Value;
    var Lon = this.CurEstimate.Position.Lon.Value;

    do {
      MI = GribMgr.WindAtPointInTime(this.CurEstimate.PrevDate, Lat, Lon);

      if (!MI) {
        if (this.ErrorCount > 10) {
          this.Stop();
          return;
        }

        this.ErrorCount++;
        setTimeout(this.Estimate.bind(this), 1000);
        return;
      }

      this.ErrorCount = 0;

      if (isNaN(MI.Speed)) {
        var Bkpt = 1;
        alert("Looping on NaN WindSpeed");
      }
    } while (isNaN(MI.Speed));

    this.CurEstimate.Meteo = MI; // Ok, got meteo, move the boat, and ask for new METEO
    // Check if an update is required from AutoPilot;

    for (var _index2 in this.CurEstimate.PilOrders) {
      var Order = this.CurEstimate.PilOrders[_index2];

      if (Order && Order.STS === "pending") {
        var OrderTime = new Date(parseInt(Order.TTS, 10) * 1000.0);

        if (OrderTime <= this.CurEstimate.Date) {
          // Use pilot order to update the current Mode
          this.CurEstimate.Mode = parseInt(Order.PIM, 10);

          switch (this.CurEstimate.Mode) {
            case PM_ANGLE:
            case PM_HEADING:
              this.CurEstimate.Value = parseFloat(Order.PIP);
              break;

            case PM_ORTHO:
            case PM_VMG:
            case PM_VBVMG:
              var p1 = Order.PIP.split("@");

              var _Dest = p1[0].split(",");

              this.CurEstimate.CurWP = new VLMPosition(parseFloat(_Dest[1]), parseFloat(_Dest[0]));
              this.CurEstimate.HdgAtWP = parseFloat(p1[1]);
              break;

            default:
              alert("unsupported pilototo mode");
              this.Stop();
              return;
          }

          this.CurEstimate.PilOrders[_index2] = null;
          break;
        }
      }
    }

    var Hdg = this.CurEstimate.Value;
    var Speed = 0;
    var NewPos = null;
    var Dest = null;

    switch (this.CurEstimate.Mode) {
      case PM_ANGLE:
        // This goes just before Heading, since we only update the Hdg, rest is the same
        // Going fixed angle, get bearing, compute speed, move
        Hdg = MI.Heading + this.CurEstimate.Value;
        Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, Hdg);
        NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed / 3600.0 * this.Boat.VLMInfo.VAC, Hdg);
        break;

      case PM_HEADING:
        // Going fixed bearing, get boat speed, move along loxo
        Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, Hdg);
        NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed / 3600.0 * this.Boat.VLMInfo.VAC, Hdg);
        break;

      case PM_ORTHO:
      case PM_VMG:
      case PM_VBVMG:
        Dest = this.GetNextWPCoords(this.CurEstimate);

        if (this.CurEstimate.Mode == PM_ORTHO) {
          Hdg = this.CurEstimate.Position.GetOrthoCourse(Dest);
          Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, Hdg);
          NewPos = this.CurEstimate.Position.ReachDistOrtho(Speed / 3600.0 * this.Boat.VLMInfo.VAC, Hdg);
        } else {
          if (this.CurEstimate.Mode == PM_VMG) {
            Hdg = PolarsManager.GetVMGCourse(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, this.CurEstimate.Position, Dest);
          } else {
            Hdg = PolarsManager.GetVBVMGCourse(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, this.CurEstimate.Position, Dest);
          }

          Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, Hdg);
          NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed / 3600.0 * this.Boat.VLMInfo.VAC, Hdg);
        }

        this.CheckWPReached(Dest, this.CurEstimate.Position, NewPos);
        break;

      default:
        throw "Unsupported pilotmode for estimate..." + this.CurEstimate.Mode;
    }

    console.log(this.CurEstimate.Date + this.CurEstimate.Position.toString(true) + "=> " + NewPos.Lon.toString(true) + " " + NewPos.Lat.toString(true) + " Wind : " + RoundPow(MI.Speed, 4) + "@" + RoundPow(MI.Heading, 4) + " Boat " + RoundPow(Speed, 4) + "kts" + RoundPow((Hdg + 360.0) % 360.0, 4));
    var RaceComplete = false;

    if (this.CheckGateValidation(NewPos)) {
      RaceComplete = this.GetNextRaceWP();
    }

    this.CurEstimate.Heading = Hdg;
    this.CurEstimate.Position = NewPos;
    this.EstimateTrack.push(new BoatEstimate(this.CurEstimate)); // Start next point computation....

    this.CurEstimate.Date = new Date((this.CurEstimate.Date / 1000 + this.Boat.VLMInfo.VAC) * 1000);
    this.CurEstimate.PrevDate = this.CurEstimate.Date;

    if (RaceComplete) {
      this.Stop();
      return;
    } else {
      setTimeout(this.Estimate.bind(this), 0);
      this.ReportProgress(false);
    }
  };

  this.GetNextRaceWP = function () {
    var NbWP = Object.keys(this.Boat.RaceInfo.races_waypoints).length;

    if (this.CurEstimate.RaceWP === NbWP) {
      //Race Complete
      return true;
    }

    for (var i = this.CurEstimate.RaceWP + 1; i <= NbWP; i++) {
      if (!(this.Boat.RaceInfo.races_waypoints[i].wpformat & WP_ICE_GATE)) {
        this.CurEstimate.RaceWP = i;
        break;
      }
    }

    return false;
  };

  this.CheckGateValidation = function (NewPos) {
    var GateSeg = this.GetNextGateSegment(this.CurEstimate);
    var Gate = this.Boat.RaceInfo.races_waypoints[this.CurEstimate.RaceWP];
    var CurSeg = {
      P1: this.CurEstimate.Position,
      P2: NewPos
    };
    var RetVal = VLMMercatorTransform.SegmentsIntersect(GateSeg, CurSeg);
    return RetVal;
  };

  this.CheckWPReached = function (Dest, PrevPos, NewPos) {
    if (!this.CurEstimate.CurWP.Lat.value && !this.CurEstimate.CurWP.Lon.Value) {
      // AutoWP, nothing to do
      return;
    } // VLM REF from CheckWayPointCrossing
    // On lache le WP perso si il est plus pres que la distance parcourue à la dernière VAC.
    //if ( $distAvant < $fullUsersObj->boatspeed*$fullUsersObj->hours || $distApres < $fullUsersObj->boatspeed*$fullUsersObj->hours ) {


    var BeforeDist = Dest.GetOrthoDist(PrevPos);
    var AfterDist = Dest.GetOrthoDist(NewPos);
    var CurDist = PrevPos.GetOrthoDist(NewPos);

    if (BeforeDist < CurDist || AfterDist < CurDist) {
      // WP Reached revert to AutoWP
      this.CurEstimate.CurWP = new VLMPosition(0, 0);

      if (this.CurEstimate.HdgAtWP != -1) {
        this.CurEstimate.Mode = PM_HEADING;
        this.CurEstimate.Value = this.CurEstimate.HdgAtWP;
      }

      console.log("WP Reached");
    }
  };

  this.GetNextWPCoords = function (Estimate) {
    if (Estimate.CurWP.Lat.value || Estimate.CurWP.Lon.Value) {
      return Estimate.CurWP;
    } else {
      return this.Boat.GetNextWPPosition(Estimate.RaceWP, Estimate.Position, Estimate.CurWP);
    }
  };

  this.GetNextGateSegment = function (Estimate) {
    return this.Boat.GetNextGateSegment(Estimate.RaceWP);
  };

  this.ReportProgress = function (Complete) {
    var Pct = 0;

    if (this.ProgressCallBack) {
      if (!Complete) {
        if (this.EstimateTrack.length > 1) {
          Pct = (this.MaxVacEstimate - this.EstimateTrack[this.EstimateTrack.length - 1].Date) / (this.MaxVacEstimate - this.EstimateTrack[0].Date);
          Pct = RoundPow((1 - Pct) * 100.0, 1);
        }
      }

      this.ProgressCallBack(Complete, Pct, this.CurEstimate.Date);
    }
  };

  this.GetClosestEstimatePoint = function (Param) {
    if (Param instanceof VLMPosition) {
      return this.GetClosestEstimatePointFromPosition(Param);
    } else if (Param instanceof Date) {
      return this.GetClosestEstimatePointFromTime(Param);
    } else {
      return null;
    }
  };

  this.GetClosestEstimatePointFromTime = function (Time) {
    if (!Time || !Object.keys(this.EstimateTrack).length) {
      return null;
    }

    var Index = 0;
    var Delta;

    for (Index = 0; Index < Object.keys(this.EstimateTrack).length; Index++) {
      if (this.EstimateTrack[Index]) {
        if (Time > this.EstimateTrack[Index].Date) {
          Delta = Time - this.EstimateTrack[Index].Date;
        } else {
          break;
        }
      }
    }

    if (Index < Object.keys(this.EstimateTrack).length && typeof this.EstimateTrack[Index + 1] !== "undefined" && this.EstimateTrack[Index + 1]) {
      var Delta2 = Time - this.EstimateTrack[Index + 1].Date;

      if (Math.abs(Delta2) < Math.abs(Delta)) {
        Index++;
      }
    }

    var RetValue = this.EstimateTrack[Index];
    return RetValue;
  };

  this.GetClosestEstimatePointFromPosition = function (Pos) {
    if (!Pos) {
      return null;
    }

    var Dist = 1e30;
    var index;
    var RetValue = null;

    for (index = 0; index < Object.keys(this.EstimateTrack).length; index++) {
      if (this.EstimateTrack[index]) {
        var d = Pos.GetEuclidianDist2(this.EstimateTrack[index].Position);

        if (d < Dist) {
          RetValue = this.EstimateTrack[index];
          Dist = d;
        }
      }
    }

    return RetValue;
  };

  this.ClearEstimatePosition = function (Boat) {
    this.ShowEstimatePosition(Boat, null);
  };

  this.ShowEstimatePosition = function (Boat, Estimate) {
    var Features = GetRaceMapFeatures(Boat);

    if (Boat && Estimate && Estimate.Position && (Boat.VLMInfo.LON !== Estimate.Position.Lon.Value || Boat.VLMInfo.LAT !== Estimate.Position.Lat.Value)) {
      if (!Features) {
        return;
      }

      var Position = [Estimate.Position.Lat.Value, Estimate.Position.Lon.Value];

      if (Features.BoatEstimateMarker) {
        Features.BoatEstimateMarker.setLatLng(Position).addTo(map);
      } else {
        // Estimate point marker
        var Marker = GetBoatEstimateMarker();
        Features.BoatEstimateMarker = L.marker(Position, {
          icon: Marker
        }).addTo(map);
      }

      if (Features.BoatEstimateMarker) {
        Features.BoatEstimateMarker.setRotationAngle(Estimate.Heading);
      }

      if (typeof Estimate.Meteo !== "undefined" && Estimate.Meteo) {
        var StartPos = new VLMPosition(Position[1], Position[0]);
        var Polar = BuildPolarLine(Boat, StartPos, VLM2Prefs.MapPrefs.PolarVacCount, Estimate.Date);
        Features.BoatEstimateMarkerPolar = DefinePolarMarker(Polar, Features.BoatEstimateMarkerPolar);
      }
    } else if (Features) {
      if (Features.BoatEstimateMarker) {
        Features.BoatEstimateMarker.remove();
      }

      if (Features.BoatEstimateMarkerPolar) {
        Features.BoatEstimateMarkerPolar.remove();
      }
    }
  };

  this.GetEstimateTracks = function () {
    var RetTracks = [];
    var PrevIndex = null;
    var PrevPoint = null;

    if (this.EstimateTrack && this.EstimateTrack[0]) {
      var TrackStartTick = new Date().getTime();
      var GridOffset = TrackStartTick % (6 * 3600000);
      var TrackIndexStartTick = TrackStartTick - GridOffset + 3.5 * 3600000;

      for (var _index3 in this.EstimateTrack) {
        if (this.EstimateTrack[_index3]) {
          var est = this.EstimateTrack[_index3];
          var Delta = est.Date.getTime() - TrackIndexStartTick;
          var CurTrackInDex = Math.floor(Delta / 6 / 3600000);

          if (CurTrackInDex < 0) {
            CurTrackInDex = 0;
          } else if (CurTrackInDex > 2) {
            CurTrackInDex = 2;
          }

          if (typeof RetTracks[CurTrackInDex] === "undefined") {
            RetTracks[CurTrackInDex] = [];
          }

          if (CurTrackInDex !== PrevIndex && PrevPoint) {
            // Push prev point before starting a new track
            RetTracks[CurTrackInDex].push([PrevPoint.Position.Lat.Value, PrevPoint.Position.Lon.Value]);
          }

          RetTracks[CurTrackInDex].push([est.Position.Lat.Value, est.Position.Lon.Value]);
          PrevPoint = est;
          PrevIndex = CurTrackInDex;
        }
      }
    }

    return RetTracks;
  };
} //
// Coords Class
// Basic coordinates conversions and formating
//
//


function Coords(v, IsLon) {
  if (typeof v == 'number') {
    this.Value = v;
  } else {
    this.Value = parseFloat(v);
  }

  this.IsLon = IsLon; // Returns the degree part of a coordinate is floating format

  this.Deg = function () {
    return Math.abs(this.Value);
  }; // Returns the minutes part of a coordinate in floating format


  this.Min = function () {
    return (Math.abs(this.Value) - Math.floor(this.Deg())) * 60;
  }; // Returns the second part of a coordinate in floating format


  this.Sec = function () {
    return (this.Min() - Math.floor(this.Min())) * 60;
  }; // Basic string formatting of a floating coordinate


  this.toString = function (Raw) {
    if (Raw) {
      return this.Value;
    } else {
      var Side = "";

      if (typeof this.IsLon == 'undefined' || this.IsLon == 0) {
        Side = this.Value >= 0 ? ' N' : ' S';
      } else {
        Side = this.Value >= 0 ? ' E' : ' W';
      }

      return Math.floor(this.Deg()) + "° " + Math.floor(this.Min()) + "' " + RoundPow(this.Sec(), 2) + '"' + Side;
    }
  };
} //
// Returns the deg, min, sec parts of a coordinate in decimal number
//


function GetDegMinSecFromNumber(n, d, m, s) {
  var DecPart;
  SplitNumber(n, d, DecPart);
  SplitNumber(DecPart * 60, m, DecPart);
  SplitNumber(DecPart * 60, s, DecPart);
  return;
} //
// Split a number between its integer and decimal part


function SplitNumber(n, i, d) {
  i = Math.floor(n);
  d = n - i;
}

var SrvIndex = 1; // Global GribMap Manager

var GribMap = {};

GribMap.ServerURL = function () {
  if (typeof WindGridServers !== "undefined" && WindGridServers) {
    SrvIndex = (SrvIndex + 1) % WindGridServers.length;

    if (SrvIndex === 0) {
      SrvIndex = 1;
    }

    return WindGridServers[SrvIndex];
  } else {
    return "";
  }
}; // point in the canvas


var Pixel = function Pixel(x, y) {
  _classCallCheck(this, Pixel);

  this.x = x;
  this.y = y;

  this.moveBy = function (offset) {
    //this is the same as the base.offset() func, but without cloning the object
    this.x += offset.x;
    this.y += offset.y;
  };

  this.moveByPolar = function (ro, theta) {
    var angle = (theta - 90.0) * Math.PI / 180.0;
    this.x += ro * Math.cos(angle);
    this.y += ro * Math.sin(angle);
  };
}; // Leaflet Extension layet to draw wind arrows
// Highly inspired from Leaflet Heat plugin
// https://github.com/Leaflet/Leaflet.heat/blob/gh-pages/src/HeatLayer.js


GribMap.Layer = L.Layer.extend({
  initialize: function initialize(config) {
    if (!config) {
      config = {};
    }

    this.cfg = config;
    this._canvas = L.DomUtil.create('canvas');
    this._data = [];
    this._max = 1;
    this._min = 0;
    this.cfg.container = this._canvas;
    this._Density = 10;
    this._Time = new Date();
    this.DrawWindDebugCnt = 0;
    this.DrawWindDebugDepth = 0;
  },
  SetGribMapTime: function SetGribMapTime(epoch) {
    this._Time = epoch;

    this._update();
  },
  _CheckDensity: function _CheckDensity() {
    if (this._width < 500 || this.height < 500) {
      this._Density = 5;
    } else {
      this._Density = 10;
    }
  },
  onAdd: function onAdd(map) {
    var size = map.getSize();
    this._map = map;
    this._width = size.x;
    this._height = size.y;
    this._canvas.width = size.x;
    this._canvas.height = size.y;

    this._CheckDensity();

    this._canvas.style.width = size.x + 'px';
    this._canvas.style.height = size.y + 'px';
    this._canvas.style.position = 'absolute';
    this._origin = this._map.layerPointToLatLng(new L.Point(0, 0));
    map.getPanes().overlayPane.appendChild(this._canvas); // this resets the origin and redraws whenever
    // the zoom changed or the map has been moved

    map.on('moveend', this._reset, this);

    this._reset();

    this._draw();
  },
  addTo: function addTo(map) {
    map.addLayer(this);
    return this;
  },
  onRemove: function onRemove(map) {
    // remove layer's DOM elements and listeners
    map.getPanes().overlayPane.removeChild(this._canvas);
    map.off('moveend', this._reset, this);
  },
  _draw: function _draw() {
    if (!this._map) {
      return;
    }

    this._update();
  },
  _update: function _update(InCallBack) {
    var ctx = this._canvas.getContext('2d');

    ctx.clearRect(0, 0, this._canvas.width, this._canvas.height);

    this._DrawWindArea(ctx, InCallBack);
  },
  _DrawWindArea: function _DrawWindArea(ctx, InCallBack) {
    var _this = this;

    this.DrawWindDebugCnt++;
    this.DrawWindDebugDepth = InCallBack ? this.DrawWindDebugDepth + 1 : 0; // " + this.DrawWindDebugCnt + " " + this.DrawWindDebugDepth);

    var bstep = this.arrowstep;
    var bounds, zoom;
    var ErrorCatching = 1;
    bounds = this._map.getBounds();
    zoom = this._map.getZoom();

    if (zoom < 5) {
      return;
    }

    var MinX = bounds.getWest();
    var MaxX = bounds.getEast();
    var MaxY = bounds.getNorth();
    var MinY = bounds.getSouth();
    var DX = (MaxX - MinX) / this._Density;
    var DY = (MaxY - MinY) / this._Density;
    var LatLng = L.latLng(MaxY, MinX);
    var p0 = map.project(LatLng, zoom);
    var StopGribRequets = false;
    var MI = null;

    for (var x = MinX; x <= MaxX; x += DX) {
      for (var y = MinY; y <= MaxY; y += DY) {
        //Récupère le vent et l'affiche en l'absence d'erreur
        try {
          //winfo = windarea.getWindInfo2(LonLat.lat, LonLat.lon, this.time, wante, wpost);
          //this.drawWind(ctx, p.x, p.y, winfo);
          if (!StopGribRequets) {
            (function () {
              var self = _this;
              MI = GribMgr.WindAtPointInTime(_this._Time, y, x,
              /* jshint -W083*/
              InCallBack ? null : function () {
                self._update(true);
              });
              /*jshint +W083*/

              if (!MI) {
                StopGribRequets = true;
              }
            })();
          }

          var _LatLng = L.latLng(y, x);

          var p = map.project(_LatLng, zoom);

          if (MI) {
            this._drawWind(ctx, p.x - p0.x, p.y - p0.y, zoom, MI.Speed, MI.Heading);
          } else {
            this._drawWind(ctx, p.x - p0.x, p.y - p0.y, zoom, 0, 0);
          }
        } catch (error) {
          if (ErrorCatching > 0) {
            alert('_DrawWindArea ' + x + " / " + y + " / <br>" + error);
            ErrorCatching -= 1;
          }
        }
      }
    }
  },
  _drawWind: function _drawWind(context, x, y, z, WindSpeed, WindHeading) {
    var YOffset = this._drawWindTriangle(context, x, y, WindSpeed, WindHeading);

    context.fillStyle = '#626262';

    this._drawWindText(context, x, y + YOffset, WindSpeed, WindHeading);
  },
  // draw wind information around the arrow
  // parameters:
  // context, the canvas context
  // x, y, the coordinates in the window
  // wspeed, wheading, wind speed and wind heading
  _drawWindText: function _drawWindText(context, x, y, WindSpeed, WindHeading) {
    var text_x = x;
    var text_y = y;
    var TextString = '?? / ???';

    if (WindSpeed || WindHeading) {
      if (WindHeading > 90.0 && WindHeading < 270.0) {
        //  text_y +=10;
        text_y += 13 + 5 * Math.cos(WindHeading * Math.PI / 180.0);
      } else {
        //  text_y -=5;
        text_y += 7 - 5 * Math.cos(WindHeading * Math.PI / 180.0);
      }

      TextString = "" + RoundPow(WindSpeed, 1) + "/" + RoundPow(WindHeading, 1) + "°";
    }

    var xoffset = context.measureText(TextString).width / 2;
    context.fillText(TextString, text_x - xoffset, text_y); //console.log("Drawing "+x+"/"+y+" "+TextString);
  },
  _drawWindTriangle: function _drawWindTriangle(context, x, y, WindSpeed, WindHeading) {
    var a, b, c, bary, offset;
    var wheading;
    var wspdlog;

    if (!WindHeading && !WindSpeed) {
      return 0;
    }

    var windarrow_minsize = 4; // FIXME external constants ?

    var windarrow_minwidth = 0;
    wspdlog = Math.log(WindSpeed + 1);
    wheading = (WindHeading + 180.0) % 360.0;
    a = new Pixel(x, y);
    b = new Pixel(x, y);
    c = new Pixel(x, y);
    a.moveByPolar(windarrow_minsize + wspdlog * 4.0, wheading);
    b.moveByPolar(windarrow_minwidth + wspdlog * 2.0, wheading - 135.0);
    c.moveByPolar(windarrow_minwidth + wspdlog * 2.0, wheading + 135.0);
    bary = new Pixel((a.x + b.x + c.x) / 3, (a.y + b.y + c.y) / 3);
    offset = new Pixel(x - bary.x, y - bary.y);
    a.moveBy(offset);
    b.moveBy(offset);
    c.moveBy(offset);
    var color = this.windSpeedToColor(WindSpeed);
    context.fillStyle = color;
    context.strokeStyle = color;
    context.beginPath();
    context.moveTo(a.x, a.y);
    context.lineTo(b.x, b.y);
    context.lineTo(c.x, c.y);
    context.fill();
    context.stroke();
    context.closePath();
    var RetY = Math.max(a.y, b.y, c.y);
    return RetY;
  },
  // return the color based on the wind speed
  // parameters:
  // wspeed: the wind speed.
  windSpeedToColor: function windSpeedToColor(wspeed) {
    if (wspeed <= 1.0) {
      return '#FFFFFF';
    }

    if (wspeed <= 3.0) {
      return '#9696E1';
    }

    if (wspeed <= 6.0) {
      return '#508CCD';
    }

    if (wspeed <= 10.0) {
      return '#3C64B4';
    }

    if (wspeed <= 15.0) {
      return '#41B464';
    }

    if (wspeed <= 21.0) {
      return '#B4CD0A';
    }

    if (wspeed <= 26.0) {
      return '#D2D216';
    }

    if (wspeed <= 33.0) {
      return '#E1D220';
    }

    if (wspeed <= 40.0) {
      return '#FFB300';
    }

    if (wspeed <= 47.0) {
      return '#FF6F00';
    }

    if (wspeed <= 55.0) {
      return '#FF2B00';
    }

    if (wspeed <= 63.0) {
      return '#E60000';
    }

    return '#7F0000';
  },
  _reset: function _reset() {
    var topLeft = this._map.containerPointToLayerPoint([0, 0]);

    L.DomUtil.setTransform(this._canvas, topLeft, 1);

    var size = this._map.getSize();

    if (this._width !== size.x) {
      this._canvas.width = size.x;
      this._width = size.x;

      this._CheckDensity();
    }

    if (this._height !== size.y) {
      this._canvas.height = size.y;
      this._height = size.y;

      this._CheckDensity();
    }

    this._draw();
  },
  _animateZoom: function _animateZoom(e) {
    var scale = this._map.getZoomScale(e.zoom),
        offset = this._map._getCenterOffset(e.center)._multiplyBy(-scale).subtract(this._map._getMapPanePos());

    if (L.DomUtil.setTransform) {
      L.DomUtil.setTransform(this._canvas, offset, scale);
    } else {
      this._canvas.style[L.DomUtil.TRANSFORM] = L.DomUtil.getTranslateString(offset) + ' scale(' + scale + ')';
    }
  }
}); // Create and init a manager

var GribData = function GribData(InitStruct) {
  _classCallCheck(this, GribData);

  this.UGRD = NaN;
  this.VGRD = NaN;
  this.TWS = NaN;

  if (typeof InitStruct !== "undefined") {
    this.UGRD = InitStruct.UGRD;
    this.VGRD = InitStruct.VGRD;
    this.TWS = InitStruct.TWS;
  }

  this.Strength = function () {
    return Math.sqrt(this.UGRD * this.UGRD + this.VGRD * this.VGRD) * 1.9438445; //* 3.6 / 1.852
  };

  this.Direction = function () {
    var t_speed = Math.sqrt(this.UGRD * this.UGRD + this.VGRD * this.VGRD);
    var dir = Math.acos(-this.VGRD / t_speed);

    if (this.UGRD > 0) {
      dir = 2 * Math.PI - dir;
    }

    dir = dir / Math.PI * 180 % 360;

    if (dir < 0) {
      dir += 360;
    } else if (dir >= 360) {
      dir -= 360;
    }

    return dir;
  };
};

var WindData = function WindData(InitStruct) {
  _classCallCheck(this, WindData);

  this.Speed = NaN;
  this.Heading = NaN;

  this.IsValid = function () {
    return !isNaN(this.Speed) && !isNaN(this.Heading);
  };

  if (typeof InitStruct !== "undefined") {
    this.Speed = InitStruct.Speed;
    this.Heading = InitStruct.Heading;
  }
};

var VLM2GribManager = function VLM2GribManager() {
  _classCallCheck(this, VLM2GribManager);

  this.Tables = [];
  this.TableTimeStamps = [];
  this.Inited = false;
  this.Initing = false;
  this.MinWindStamp = 0;
  this.MaxWindStamp = 0;
  this.WindTableLength = 0;
  this.LoadQueue = [];
  this.GribStep = 0.5; // Grib Grid resolution

  this.LastGribDate = new Date(0);

  this.Init = function () {
    if (this.Inited || this.Initing) {
      return;
    }

    this.Initing = true;
    $.get("/ws/windinfo/list.php?v=" + Math.round(new Date().getTime() / 1000 / 60 / 3), this.HandleGribList.bind(this));
  };

  this.HandleGribList = function (e) {
    this.TableTimeStamps = e.grib_timestamps;
    this.Inited = true;
    this.Initing = false;
    this.MinWindStamp = new Date(this.TableTimeStamps[0] * 1000);
    this.MaxWindStamp = new Date(this.TableTimeStamps[this.TableTimeStamps.length - 1] * 1000);
    this.WindTableLength = this.TableTimeStamps.length;
  };

  this.WindAtPointInTime = function (Time, Lat, Lon, callback) {
    if (!this.Inited) {
      return false;
    }

    var GribGrain = 3.0 * 3600.0; // 1 grib every 3 hours.

    var TableIndex = Math.floor((Time / 1000.0 - this.MinWindStamp / 1000) / GribGrain);

    if (TableIndex < 0) {
      // Before avaible grib 
      return false;
    }

    if (TableIndex + 1 >= this.TableTimeStamps.length) {
      // To far in the future
      return false;
    }

    var RetInfo = new WindData();

    if (Math.abs(Lat) > 85) {
      RetInfo.Heading = 0;
      RetInfo.Speed = 0;
      return RetInfo;
    } // Precheck to force loading the second grib, and avoid optimization not checking 2nd when first is needs loading


    var t1 = this.CheckGribLoaded(TableIndex, Lat, NormalizeLongitudeDeg(Lon), callback);
    var t2 = this.CheckGribLoaded(TableIndex + 1, Lat + this.GribStep, NormalizeLongitudeDeg(Lon + this.GribStep), callback);

    if (t1 && !t2) {
      //alert("anomaly at "+Lat+this.GribStep+ "/" + NormalizeLongitudeDeg(Lon+this.GribStep))
      t2 = this.CheckGribLoaded(TableIndex + 1, Lat + this.GribStep, NormalizeLongitudeDeg(Lon + this.GribStep), callback);
    }

    if (!t1 || !t2) {
      return false;
    } // Ok, now we have the grib data in the table before and after requested time for requested position


    var MI0 = this.GetHydbridMeteoAtTimeIndex(TableIndex, Lat, Lon);
    var MI1 = this.GetHydbridMeteoAtTimeIndex(TableIndex + 1, Lat, Lon);
    var u0 = MI0.UGRD;
    var v0 = MI0.VGRD;
    var u1 = MI1.UGRD;
    var v1 = MI1.VGRD;
    var DteOffset = Time / 1000 - this.TableTimeStamps[TableIndex];
    var GInfo = new GribData({
      UGRD: u0 + DteOffset / GribGrain * (u1 - u0),
      VGRD: v0 + DteOffset / GribGrain * (v1 - v0)
    });
    RetInfo.Heading = GInfo.Direction();
    RetInfo.Speed = MI0.TWS + DteOffset / GribGrain * (MI1.TWS - MI0.TWS);
    return RetInfo;
  };

  this.GetHydbridMeteoAtTimeIndex = function (TableIndex, Lat, Lon) {
    // Normalize coords
    var Lon_pos = Lon;

    while (Lon_pos < 0) {
      Lon_pos += 360;
    }

    while (Lon_pos > 360) {
      Lon_pos -= 360;
    }

    var Lat_pos = Lat;

    while (Lat_pos < 0) {
      Lat_pos += 90;
    }

    while (Lat_pos > 90) {
      Lat_pos -= 90;
    } // Compute grid index to get the values


    var LonIdx1 = 180 / this.GribStep + Math.floor(Lon / this.GribStep);
    var LatIdx1 = 90 / this.GribStep + Math.floor(Lat / this.GribStep);
    var LonIdx2 = (LonIdx1 + 1) % (360 / this.GribStep);
    var LatIdx2 = (LatIdx1 + 1) % (180 / this.GribStep);
    var dX = Lon_pos / this.GribStep - Math.floor(Lon_pos / this.GribStep);
    var dY = Lat_pos / this.GribStep - Math.floor(Lat_pos / this.GribStep); // Get UVS for each 4 grid points

    var U00 = this.Tables[TableIndex][LonIdx1][LatIdx1].UGRD;
    var U01 = this.Tables[TableIndex][LonIdx1][LatIdx2].UGRD;
    var U10 = this.Tables[TableIndex][LonIdx2][LatIdx1].UGRD;
    var U11 = this.Tables[TableIndex][LonIdx2][LatIdx2].UGRD;
    var V00 = this.Tables[TableIndex][LonIdx1][LatIdx1].VGRD;
    var V01 = this.Tables[TableIndex][LonIdx1][LatIdx2].VGRD;
    var V10 = this.Tables[TableIndex][LonIdx2][LatIdx1].VGRD;
    var V11 = this.Tables[TableIndex][LonIdx2][LatIdx2].VGRD;
    var S00 = this.Tables[TableIndex][LonIdx1][LatIdx1].Strength();
    var S01 = this.Tables[TableIndex][LonIdx1][LatIdx2].Strength();
    var S10 = this.Tables[TableIndex][LonIdx2][LatIdx1].Strength();
    var S11 = this.Tables[TableIndex][LonIdx2][LatIdx2].Strength();
    var tws = this.QuadraticAverage(S00, S01, S10, S11, dX, dY);
    var retmeteo = new GribData({
      UGRD: this.QuadraticAverage(U00, U01, U10, U11, dX, dY),
      VGRD: this.QuadraticAverage(V00, V01, V10, V11, dX, dY),
      TWS: tws
    });
    return retmeteo;
  };

  this.QuadraticAverage = function (V00, V01, v10, V11, dX, dY) {
    var V0 = V00 + dY * (V01 - V00);
    var V1 = v10 + dY * (V11 - v10);
    return V0 + dX * (V1 - V0);
  };

  this.CheckGribLoaded = function (TableIndex, Lat, Lon, callback) {
    var LonIdx1 = 180 / this.GribStep + Math.floor(Lon / this.GribStep);
    var LatIdx1 = 90 / this.GribStep + Math.floor(Lat / this.GribStep);
    var LonIdx2 = 180 / this.GribStep + Math.ceil(Lon / this.GribStep);
    var LatIdx2 = 90 / this.GribStep + Math.ceil(Lat / this.GribStep);

    if (TableIndex in this.Tables) {
      if (this.Tables[TableIndex][LonIdx1] && this.Tables[TableIndex][LonIdx1][LatIdx1] && this.Tables[TableIndex][LonIdx1][LatIdx2] && this.Tables[TableIndex][LonIdx2] && this.Tables[TableIndex][LonIdx2][LatIdx1] && this.Tables[TableIndex][LonIdx2][LatIdx2]) {
        return true;
      }
    } //console.log("need "+Lat+" " +Lon);


    this.CheckGribLoadedIdx(TableIndex, LonIdx1, LatIdx1, callback);
    this.CheckGribLoadedIdx(TableIndex, LonIdx1, LatIdx2, callback);
    this.CheckGribLoadedIdx(TableIndex, LonIdx2, LatIdx1, callback);
    this.CheckGribLoadedIdx(TableIndex, LonIdx2, LatIdx2, callback);
    return false;
  };

  this.CheckGribLoadedIdx = function (TableIndex, LonIdx, LatIdx, callback) {
    if (isNaN(LonIdx) || isNaN(LatIdx)) {
      var dbgpt = 0;
    }

    if (this.Tables.length && this.Tables[TableIndex] && this.Tables[TableIndex][LonIdx] && this.Tables[TableIndex][LonIdx][LatIdx]) {
      return;
    } //Getting there means we need to load from server
    // Get samrtgrib list for the current request position


    var RequestSize = 5; // Assume 5° zone even though VLM request is for 15°. Most request will only return 1 zone.

    var Lat = LatIdx * this.GribStep - 90;
    var Lon = LonIdx * this.GribStep - 180;
    var SouthStep = Math.floor(Lat / RequestSize) * RequestSize;
    var WestStep = Math.floor(Lon / RequestSize) * RequestSize;
    var NorthStep, EastStep;

    if (Lat < SouthStep) {
      NorthStep = SouthStep;
      SouthStep = NorthStep - 2 * RequestSize;
    } else {
      NorthStep = SouthStep + 2 * RequestSize;
    }

    if (Lon < WestStep) {
      EastStep = WestStep;
      WestStep = EastStep - 2 * RequestSize;
    } else {
      EastStep = WestStep + 2 * RequestSize;
    }

    if (EastStep > 180) {
      EastStep = 180;
      this.CheckGribLoadedIdx(TableIndex, 0, LatIdx, callback);
    }

    if (WestStep < -180) {
      WestStep = -180;
      this.CheckGribLoadedIdx(TableIndex, 180 / this.GribStep - 1, LatIdx, callback);
    }

    var LoadKey = "0/" + WestStep + "/" + EastStep + "/" + NorthStep + "/" + SouthStep;
    this.AddGribLoadKey(LoadKey, NorthStep, SouthStep, WestStep, EastStep, callback);
  };

  this.AddGribLoadKey = function (LoadKey, NorthStep, SouthStep, WestStep, EastStep, callback) {
    if (!(LoadKey in this.LoadQueue)) {
      //console.log("requesting " + LoadKey );
      this.LoadQueue[LoadKey] = {
        length: 0,
        CallBacks: [callback]
      };
      $.get(GribMap.ServerURL() + "/ws/windinfo/smartgribs.php?north=" + NorthStep + "&south=" + SouthStep + "&west=" + WestStep + "&east=" + EastStep + "&seed=" + (0 + new Date()), this.HandleGetSmartGribList.bind(this, LoadKey));
    } else if (typeof callback !== "undefined" && callback) {
      this.LoadQueue[LoadKey].CallBacks.push(callback); //console.log("Adding to callback load queue "+ LoadKey + ":"+this.LoadQueue[LoadKey].CallBacks.length);
    }
  };

  this.HandleGetSmartGribList = function (LoadKey, e) {
    if (e.success) {
      // Handle grib change
      if (this.LastGribDate !== parseInt(e.GribCacheIndex, 10)) {
        // Grib changed, record, and clear Tables, force reinit
        this.LastGribDate = parseInt(e.GribCacheIndex, 10);
        this.Tables = [];
        this.Inited = false;
        this.Init();
      }

      for (var _index4 in e.gribs_url) {
        if (e.gribs_url[_index4]) {
          var url = e.gribs_url[_index4].replace(".grb", ".txt");

          var seed = 0; //parseInt((new Date).getTime());
          //console.log("smartgrib points out " + url);

          $.get("/cache/gribtiles/" + url + "&v=" + seed, this.HandleSmartGribData.bind(this, LoadKey, url));
        }
      }
    } else {
      console.log(e);
    }
  };

  this.HandleSmartGribData = function (LoadKey, Url, e) {
    var DataOK = this.ProcessInputGribData(Url, e, LoadKey); //this.LoadQueue[LoadKey].Length--;

    if (DataOK && this.LoadQueue[LoadKey]) {
      // Successfull load of one item from the loadqueue
      // Clear all pending callbacks for this call
      for (var _index5 in this.LoadQueue[LoadKey].CallBacks) {
        if (this.LoadQueue[LoadKey].CallBacks[_index5]) {
          this.LoadQueue[LoadKey].CallBacks[_index5]();
        }
      }

      delete this.LoadQueue[LoadKey];
    }
  };

  this.ForceReloadGribCache = function (LoadKey, Url) {
    var Seed = 0; //parseInt(new Date().getTime(),10);

    $.get("/cache/gribtiles/" + Url + "&force=yes&seed=" + Seed, this.HandleSmartGribData.bind(this, LoadKey, Url));
  };

  this.ProcessInputGribData = function (Url, Data, LoadKey) {
    var Lines = Data.split("\n");
    var TotalLines = Lines.length;
    var Catalog = [];
    var HeaderCompleted = false;
    var DataStartIndex = 0; // Handle cache mess

    if (Data === "--\n") {
      /*var Parms = Url.split("/")
      this.LoadQueue[LoadKey]++;
      if (Parms[2] != 15)
      {
        var i = 0;
      }
      //$.get("/gribtiles.php?south="+ Parms[0]+"&west="+Parms[1]+"&step="+ Parms[2]+"&fmt=txt",this.HandleSmartGribData .bind(this,LoadKey, Url));
      */
      this.ForceReloadGribCache(LoadKey, Url);
      return false;
    } else if (Data.search("invalid") !== -1) {
      console.log("invalid request :" + Url);
      return false;
    } // Loop data catalog


    for (var i = 0; i < TotalLines; i++) {
      var Line = Lines[i];

      if (Line === "--") {
        DataStartIndex = i + 1;
        break;
      } // Filter out GRID lines


      if (Line && Line.search("GRID:") === -1) {
        Catalog.push(this.ProcessCatalogLine(Line));
      }
    }

    if (Catalog.length < this.WindTableLength) {
      // Force reloading, it table is shorter than windlist
      this.ForceReloadGribCache(LoadKey, Url);
      return;
    } // Now Process the data


    var ZoneOffsets = Url.split("/");

    for (var _i = 0; _i < Catalog.length; _i++) {
      if (typeof Lines[DataStartIndex] === "undefined" || Lines[DataStartIndex] === "") {
        // Somehow sometimes, the data is incomplete, just get out, until next request.
        //console.log("Incomplete data file. Forcing rebuild..." + Url);
        this.ForceReloadGribCache(LoadKey, Url);
        break;
      }

      var DataSize = Lines[DataStartIndex].split(" ");
      var NbLon = parseInt(DataSize[0], 10);
      var NbLat = parseInt(DataSize[1], 10);
      var StartLon = 180 / this.GribStep + parseInt(ZoneOffsets[1], 10) / this.GribStep;

      for (var LonIdx = 0; LonIdx < NbLon; LonIdx++) {
        // Offset by NbLat in grib since the zone is reference by bottom lat, but counts down from top lat
        var StartLat = NbLat + 90 / this.GribStep + parseInt(ZoneOffsets[0], 10) / this.GribStep;

        for (var LatIdx = 0; LatIdx < NbLat; LatIdx++) {
          if (!(Catalog[_i].DateIndex in this.Tables)) {
            this.Tables[Catalog[_i].DateIndex] = [];
          }

          var CurTable = this.Tables[Catalog[_i].DateIndex];

          if (!(StartLon + LonIdx in CurTable)) {
            CurTable[StartLon + LonIdx] = [];
          }

          if (!(StartLat - LatIdx - 1 in CurTable[StartLon + LonIdx])) {
            CurTable[StartLon + LonIdx][StartLat - LatIdx - 1] = null;
          }

          var GribPoint = this.Tables[Catalog[_i].DateIndex][StartLon + LonIdx][StartLat - LatIdx - 1];

          if (typeof GribPoint === "undefined" || !GribPoint) {
            GribPoint = new GribData();
            this.Tables[Catalog[_i].DateIndex][StartLon + LonIdx][StartLat - LatIdx - 1] = GribPoint;
          }

          GribPoint[Catalog[_i].Type] = parseFloat(Lines[DataStartIndex + 1 + LatIdx * NbLon + LonIdx]);
        }
      }
      /*console.log("Loaded table "+ Catalog[i].DateIndex);
      console.log("Loaded lon index  "+ StartLon + "->" + (StartLon+NbLon));
      console.log("Loaded lat index  "+ (StartLat-1) + "->" + (StartLat-NbLat-1));
      */


      DataStartIndex += NbLon * NbLat + 1;
    }

    return true;
  };

  this.ProcessCatalogLine = function (Line) {
    var POS_TYPE = 3;
    var POS_INDEX = 12;
    var Ret = new WindCatalogLine();
    var Fields = Line.split(":");
    Ret.Type = Fields[POS_TYPE];

    if (typeof Fields[POS_INDEX] === "undefined" || Fields[POS_INDEX] === "anl") {
      Ret.DateIndex = 0;
    } else {
      Ret.DateIndex = parseInt(Fields[POS_INDEX].substring(0, Fields[POS_INDEX].indexOf("hr")), 10) / 3;
    }

    return Ret;
  };
};

var WindCatalogLine = function WindCatalogLine() {
  _classCallCheck(this, WindCatalogLine);

  this.Type = "";
  this.DateIndex = 0;
};

var WindTable = function WindTable() {
  _classCallCheck(this, WindTable);

  this.GribStep = 0.5;
  this.Table = [];
  this.TableDate = 0;

  this.Init = function (TableDate) {
    for (lat = -90; lat <= 90; lat += this.GribStep) {
      for (lon = -90; lon <= 90; lon += this.GribStep) {
        this.Table[lat][lon] = null;
      }
    }
  };
};

var GribMgr = new VLM2GribManager();
GribMgr.Init();

function HandleGribTestClick(e) {
  var Boat = _CurPlayer.CurBoat;

  for (var index = 0; index <= 0; index++) {
    var time = new Date(Boat.VLMInfo.LUP * 1000 + index * Boat.VLMInfo.VAC * 1000);
    var Mi = GribMgr.WindAtPointInTime(time, Boat.VLMInfo.LAT, Boat.VLMInfo.LON);

    if (Mi) {
      console.log(time + " " + Mi.Speed + "@" + Mi.Heading);
    } else {
      console.log("no meteo yet at time : " + time);
    }
  }
} //
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
var MAX_PILOT_ORDERS = 5;
var BoatRacingStatus = ["RAC", "CST", "LOC", "DNS"];
var BoatArrivedStatus = ["ARR"];
var BoatNotRacingStatus = ["DNF", "HC", "HTP"];
var BoatRacingClasses = {
  "RAC": "ft_class_racing",
  "CST": "ft_class_oncoast",
  "LOC": "ft_class_locked",
  "DNS": "ft_class_dns"
}; // Globals (beurk).

var SetWPPending = false;
var WPPendingTarget = null;
var GribWindController = null; //Global map object

var map = null;
var VLMBoatsLayer = null; // Ranking related globals

var Rankings = [];
var PilototoFt = null;
var RankingFt = null;
var RaceHistFt = null;
var ICS_WPft = null;
var NSZ_WPft = null;
var VLMINdexFt = null;
var RC_PwdResetReq = null;
var RC_PwdResetConfirm = null;
var OnPlayerLoadedCallBack = null; // On ready get started with vlm management

$(document).ready(function () {
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
    error: function error(x, status, _error) {
      if (x.status === 401 || x.status === 403) {
        window.location.replace("/"); //on access denied try reviving the session
        //OnLoginRequest();
      } else if (x.status === 404) {// Code removed until ranking exist for not started races.
        //$("#ErrorRedirectPanel").modal('show');
      } else {
        VLMAlertDanger("An error occurred: " + status + "nError: " + _error);
      }
    }
  }); // Start converse
  //InitXmpp();
  // Init maps

  LeafletInit(); // Load translation strings

  InitLocale(); // Init Menus()

  InitMenusAndButtons(); // Start-Up Polars manager

  PolarsManager.Init(); // Init Alerts

  InitAlerts(); // Handle page parameters if any

  CheckPageParameters(); // Start the page clocks

  setInterval(PageClock, 1000); // Load flags list (keep at the end since it takes a lot of time)

  GetFlagsList();
});
var COMPASS_SIZE = 350;

function LeafletInit() {
  //Init map object
  map = L.map('jVlmMap'
  /*,{preferCanvas:true}*/
  ).setView([0, 0], 8); // Tiles

  var src = tileUrlSrv;
  L.tileLayer(src, {
    attribution: 'gshhsv2',
    maxZoom: 20,
    tms: false,
    id: 'vlm',
    detectRetina: true,
    subdomains: tilesUrlArray
  }).addTo(map); // Wind Layer

  map.GribMap = new GribMap.Layer().addTo(map);
  map.Compass = new L.marker([0, 0], {
    icon: new L.icon({
      iconSize: [350, 341],
      iconAnchor: [175, 170],
      iconUrl: 'images/compas-transparent.gif'
    }),
    draggable: true
  }).addTo(map);
  map.Compass.on("dragend", HandleCompassDragEnd);
  map.Compass.on("mousemove", HandleCompassMouseMove);
  map.Compass.on("mouseout", HandleCompassMouseOut);
  map.on('mousemove', HandleMapMouseMove);
  map.on('moveend', HandleMapGridZoom);
  map.on('click', HandleMapMouseClick);
  map.on("zoomend", HandleMapGridZoom);
}

function HandleCompassMouseOut(e) {
  map.Compass.dragging.enable();
}

function HandleCompassMouseMove(e) {
  var z = map.getZoom();
  var p = map.project(map.Compass.getLatLng(), z);
  var m = map.project(map.mouseEventToLatLng(e.originalEvent), z);
  var dx = p.x - m.x;
  var dy = p.y - m.y;

  if (dx * dx + dy * dy < COMPASS_SIZE * COMPASS_SIZE / 8) {
    map.Compass.dragging.disable(); //console.log ( " " + dx + " " + dy + " disabled " + ((dx*dx)+(dy*dy)) + " < "+ 0.81*COMPASS_SIZE * COMPASS_SIZE/4);
  } else {
    map.Compass.dragging.enable(); //console.log ( " " + dx + " " + dy + " enabled" );
  }
}

function HandleCompassDragEnd(e) {
  if (_CurPlayer && _CurPlayer.CurBoat && _CurPlayer.CurBoat.VLMInfo.LAT && _CurPlayer.CurBoat.VLMInfo.LON) {
    var _Boat = _CurPlayer.CurBoat;
    var B = [_CurPlayer.CurBoat.VLMInfo.LAT, _CurPlayer.CurBoat.VLMInfo.LON];
    var C = map.Compass.getLatLng();
    var Features = GetRaceMapFeatures(_Boat);

    if (!Features.Compass) {
      Features.Compass = {};
    }

    var z = map.getZoom();
    var P1 = map.project(B, z);
    var P2 = map.project(C, z);

    if (Math.abs(P1.x - P2.x) < BOAT_MARKET_SIZE / 2 && Math.abs(P1.y - P2.y) < BOAT_MARKET_SIZE / 2) {
      Features.Compass.Lat = -1;
      Features.Compass.Lon = -1;
    } else {
      Features.Compass.Lat = C.lat;
      Features.Compass.Lon = C.lng;
    }
  }
}

function HandleMapGridZoom(e) {
  var m = e.sourceTarget;
  var z = m.getZoom();
  var b = m.getBounds();
  var DX = b._northEast.lng - b._southWest.lng;
  var DY = b._northEast.lat - b._southWest.lat;
  var S = DX;

  if (DY < DX) {
    S = DY;
  }

  S = Math.pow(0.25, Math.ceil(Math.log(S) / Math.log(0.25)));

  if (S > 5) {
    S = Math.pow(5, Math.floor(Math.log(S) / Math.log(5)));
  } else if (S < 0.25) {
    S = 0.25;
  }

  if (typeof m.GridLayer == "undefined") {
    m.Grid = [];
    m.GridLayer = L.layerGroup().addTo(m);
  } else {
    m.GridLayer.clearLayers();
  }

  var GridLabelOpacity = 0.4;
  var GridLineStyle = {
    weight: 1,
    opacity: GridLabelOpacity,
    color: 'black'
  };
  var GridLabelStyle1 = {
    permanent: true,
    opacity: GridLabelOpacity,
    offset: [0, -10]
  };
  var GridLabelStyle2 = {
    permanent: true,
    opacity: GridLabelOpacity,
    offset: [0, 30]
  };
  var GridLabelStyle3 = {
    permanent: true,
    opacity: GridLabelOpacity,
    offset: [10, 0]
  };
  var GridLabelStyle4 = {
    permanent: true,
    opacity: GridLabelOpacity,
    offset: [-10, 0]
  };
  var index = 0;

  for (var x = Math.floor(b._southWest.lng); x <= b._northEast.lng; x += S) {
    var P = [[b._southWest.lat, x], [b._northEast.lat, x]];
    m.Grid[index] = L.polyline(P, GridLineStyle);
    m.GridLayer.addLayer(m.Grid[index++]);
    var xlabel = RoundPow(4 * x, 0) / 4;
    m.Grid[index] = L.circleMarker(P[0], {
      radius: 1
    }).bindTooltip("" + xlabel, GridLabelStyle1);
    m.GridLayer.addLayer(m.Grid[index++]);
    m.Grid[index] = L.circleMarker(P[1], {
      radius: 1
    }).bindTooltip("" + xlabel, GridLabelStyle2);
    m.GridLayer.addLayer(m.Grid[index++]);
  }

  for (var y = Math.floor(b._southWest.lat); y <= b._northEast.lat; y += S) {
    var _P = [[y, b._southWest.lng], [y, b._northEast.lng]];

    var _xlabel = RoundPow(4 * y, 0) / 4;

    m.Grid[index] = L.polyline(_P, GridLineStyle);
    m.GridLayer.addLayer(m.Grid[index]);
    m.Grid[index] = L.circleMarker(_P[0], {
      radius: 1
    }).bindTooltip("" + _xlabel, GridLabelStyle3);
    m.GridLayer.addLayer(m.Grid[index++]);
    m.Grid[index] = L.circleMarker(_P[1], {
      radius: 1
    }).bindTooltip("" + _xlabel, GridLabelStyle4);
    m.GridLayer.addLayer(m.Grid[index++]);
  } //console.log("Zoom Level " + z);

}

var PasswordResetInfo = [];

function HandlePasswordResetLink(PwdKey) {
  PasswordResetInfo = unescape(PwdKey).split("|");
  initrecaptcha(false, true);
  $("#ResetaPasswordConfirmation").modal("show");
}

function CheckPageParameters() {
  var url = window.location.search;
  var RacingBarMode = true;

  if (url) {
    var getQuery = url.split('?')[1];
    var params = getQuery.split('&'); // params is ['param1=value', 'param2=value2'] 

    for (var param in params) {
      if (params[param]) {
        (function () {
          var PArray = params[param].split("=");

          switch (PArray[0]) {
            case "PwdResetKey":
              HandlePasswordResetLink(PArray[1]);
              break;

            case "RaceRank":
              RacingBarMode = false;
              /* jshint -W083*/

              RankingFt.OnReadyTable = function () {
                HandleShowOtherRaceRank(PArray[1]);
              };
              /* jshint +W083*/


              break;

            case "VLMIndex":
              RacingBarMode = false;
              /* jshint -W083*/

              VLMINdexFt.OnReadyTable = function () {
                HandleShowIndex(PArray[1]);
              };
              /* jshint +W083*/


              break;

            case "ICSRace":
              RacingBarMode = false;
              HandleShowICS(PArray[1]);
              break;
          }
        })();
      }
    }
  }

  if (RacingBarMode) {
    $(".RaceNavBar").css("display", "inherit");
    $(".OffRaceNavBar").css("display", "none");
  } else {
    $(".RaceNavBar").css("display", "none");
    $(".OffRaceNavBar").css("display", "inherit");
    ShowApropos(false);
  }
}

function HandleShowICS(raceid) {
  var CallBack = function CallBack(result) {
    if (result) {
      FillRaceInstructions(result);
      $("#RacesInfoForm").modal("show");
    }
  };

  LoadRaceInfo(raceid, null, CallBack);
}

function LoadRaceInfo(RaceId, RaceVersion, CallBack) {
  if (!RaceVersion) {
    RaceVersion = '';
  }

  $.get("/ws/raceinfo/desc.php?idrace=" + RaceId + "&v=" + RaceVersion, CallBack);
}

function HandleVLMIndex(result) {
  if (result) {
    $("#Ranking-Panel").show();

    var _index6;

    var rank = 1;

    for (_index6 in result) {
      if (result[_index6]) {
        result[_index6].rank = rank;
        rank++;
      }
    }

    BackupVLMIndexTable();
    VLMINdexFt.loadRows(result);
    $("#DivVlmIndex").removeClass("hidden");
    $("#RnkTabsUL").addClass("hidden");
    $("#DivRnkRAC").addClass("hidden");
    ShowApropos(true);
  }
}

function HandleShowIndex(IndexType) {
  var CallBack = HandleVLMIndex;
  $.get("/cache/rankings/VLMIndex_" + IndexType + ".json", CallBack);
}

function HandleShowOtherRaceRank(RaceId) {
  OnPlayerLoadedCallBack = function OnPlayerLoadedCallBack() {
    var CallBack = function CallBack(Result) {
      FillRaceInfoHeader(Result);
    };

    LoadRaceInfo(RaceId, 0, CallBack);
    LoadRankings(RaceId, OtherRaceRankingLoaded);
    RankingFt.RaceRankingId = RaceId;
  };

  if (typeof _CurPlayer !== "undefined" && _CurPlayer && _CurPlayer.CurBoat) {
    OnPlayerLoadedCallBack();
    OnPlayerLoadedCallBack = null;
  }
}

function OtherRaceRankingLoaded() {
  $("#Ranking-Panel").show();
  SortRanking("RAC");
  console.log("off race ranking loaded");
}

function initrecaptcha(InitPasswordReset, InitResetConfirm) {
  if (InitPasswordReset && !RC_PwdResetReq) {
    RC_PwdResetReq = grecaptcha.render('recaptcha-PwdReset1');
  }

  if (InitResetConfirm && !RC_PwdResetConfirm) {
    RC_PwdResetConfirm = grecaptcha.render('recaptcha-PwdReset2');
  }
}

function InitMenusAndButtons() {
  // Handle modal sizing to fit screen
  $('div.vresp.modal').on('show.bs.modal', function () {
    $(this).show();
    setModalMaxHeight(this);
  });
  $(window).resize(function () {
    if ($('.modal.in').length != 0) {
      setModalMaxHeight($('.modal.in'));
    }
  }); // Handle password change button

  $("#BtnChangePassword").on("click", function (e) {
    e.preventDefault();
    HandlePasswordChangeRequest(e);
  }); // Handle password reset request, and confirmation

  $("#ResetPasswordButton").on("click", function (e) {
    if (RC_PwdResetReq !== null) {
      grecaptcha.execute(RC_PwdResetReq);
    }
  });
  $("#ConfirmResetPasswordButton").on("click", function (e) {
    if (RC_PwdResetConfirm !== null) {
      grecaptcha.execute(RC_PwdResetConfirm);
    }
  }); // Handle showing/hide of a-propos depending on login dialog status

  $("#LoginForm").on('show.bs.modal', function (e) {
    ShowApropos(false);
  });
  $("#LoginForm").on('hide.bs.modal', function (e) {
    ShowApropos(true);
  });
  $(".logindlgButton").on('click', function (e) {
    // Show Login form
    // hide apropos
    $("#LoginForm").modal('show');
  });
  $(".logOutButton").on('click', function (e) {
    // Logout user
    Logout();
  });
  $("#Menu").menu();
  $("#Menu").hide();
  $("input[type=submit],button").button().click(function (event) {
    event.preventDefault();
  }); // Theme tabs

  $(".JVLMTabs").tabs(); // Hide all progressbars

  HidePb("#PbLoginProgress");
  HidePb("#PbGetBoatProgress");
  HidePb("#PbGribLoginProgress"); // Add handler to set the WPMode controller in the proper tab

  $(".BCPane.WP_PM_Mode").click(function () {
    // Beurk , direct access by indexes :(
    // Assumes second class element is the id of target
    var target = "#" + $(this)[0].classList[2];
    MoveWPBoatControlerDiv(target);
  }); // Display setting dialog

  $(".BtnRaceList").click(function () {
    LoadRacesList();
    $("#RacesListForm").modal("show");
  }); // Handle clicking on ranking button, and ranking sub tabs

  InitRankingEvents(); // Init event handlers
  // Login button click event handler

  $("#LoginButton").click(function () {
    OnLoginRequest();
  }); //valide par touche retour

  $('#LoginPanel').keypress(function (e) {
    if (e.which === '13') {
      OnLoginRequest();
      $('#LoginForm').modal('hide');
    }
  }); // Display setting dialog

  $("#BtnSetting").click(function () {
    LoadVLMPrefs();
    SetDDTheme(VLM2Prefs.CurTheme);
    $("#SettingsForm").modal("show");
  }); // Handle SettingsSave button

  $('#SettingValidateButton').click(SaveBoatAndUserPrefs); // Handle SettingsSave button

  $('#SettingCancelButton').click(function () {
    LoadVLMPrefs();
    SetDDTheme(VLM2Prefs.CurTheme);
    $("#SettingsForm").modal("show");
  }); // Handle SettingsSave button

  $('#SettingValidateButton').click(SaveBoatAndUserPrefs); // Handle SettingsSave button

  $('#SettingCancelButton').click(function () {
    SetDDTheme(VLM2Prefs.CurTheme);
  }); // Do fixed heading button

  $("#BtnPM_Heading").click(function () {
    SendVLMBoatOrder(PM_HEADING, $("#PM_Heading")[0].value);
  }); // Do fixed angle button

  $("#BtnPM_Angle").click(function () {
    SendVLMBoatOrder(PM_ANGLE, $("#PM_Angle")[0].value);
  }); // Tack

  $("#BtnPM_Tack").click(function () {
    $("#PM_Angle")[0].value = -$("#PM_Angle")[0].value;
  });
  $("#BtnCreateAccount").click(function () {
    HandleCreateUser();
  });
  $('.CreatePassword').pstrength();
  $('#NewPlayerEMail').blur(function (e) {
    $("#NewPlayerEMail").verimail({
      messageElement: "#verimailstatus",
      language: _CurLocale
    });
  }); // Force create user form reset on show

  $('#InscriptForm').on("shown.bs.modal", function () {
    $(this).find('div.modal-body :input').val("");
  }); // Handler for Set WP on click

  $("#SetWPOnClick").click(HandleStartSetWPOnClick);
  $("#SetWPOffClick").click(HandleCancelSetWPOnClick);
  HandleCancelSetWPOnClick(); // Add handlers for autopilot buttons

  $('body').on('click', '.PIL_EDIT', HandlePilotEditDelete);
  $('body').on('click', '.PIL_DELETE', HandlePilotEditDelete);
  $("#AutoPilotAddButton").click(HandleOpenAutoPilotSetPoint);
  $("#AP_SetTargetWP").click(HandleClickToSetWP); // AP datetime pickers

  $("#AP_Time").datetimepicker({
    locale: _CurLocale,
    format: 'DD MM YYYY, HH:mm:ss' //language: 'fr-FR',
    //parentEl: '#AutoPilotSettingDlg'

  });
  $("#AP_Time").on('dp.change', HandleDateChange);
  $("#APValidateButton").click(HandleSendAPUpdate);
  $(".APField").on('change', HandleAPFieldChange);
  $(".APMode").on('click', HandleAPModeDDClick); // Draggable info window

  $(".Draggable").draggable({
    handle: ".modal-header,.modal-body"
  });
  $("#MapPrefsToggle").click(HandleShowMapPrefs);
  $(".chkprefstore").on('change', HandleMapPrefOptionChange);
  $(".MapOppShowLi").click(HandleMapOppModeChange);
  $(".DDTheme").click(HandleDDlineClick); // Handle Start Boat Estimator button

  $("#StartEstimator").on('click', HandleStartEstimator);
  $("#EstimatorStopButton").on('click', HandleStopEstimator);
  InitGribSlider();
  InitFootables(); // Handle clicking on ranking table link

  $(document.body).on('click', ".RaceHistLink", function (e) {
    HandleShowBoatRaceHistory(e);
  }); // Add handler to refresh content of eth pilototo table when showing tab content

  $("[PilRefresh]").on('click', HandleUpdatePilototoTable); // Handler for not racing boat palmares

  $("#HistRankingButton").on('click', function (e) {
    ShowUserRaceHistory(_CurPlayer.CurBoat.IdBoat);
  }); // Go To WP Ortho, VMG, VBVMG Modes

  $("#BtnPM_Ortho, #BtnPM_VMG, #BtnPM_VBVMG").click(function () {
    var WpH = -1;
    var PMMode = PM_ORTHO;
    var Lat = $("#PM_Lat")[0].value;
    var Lon = $("#PM_Lon")[0].value;
    WpH = parseInt($("#PM_WPHeading")[0].value, 10);

    switch ($(this)[0].id) {
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
  }); // InitCalendar link

  $("#CalendarPanel").on("shown.bs.modal", function (e) {
    HandleShowAgenda();
  }); // Handle boat selector selection change
  //

  $(".BoatSelectorDropDownList").on("click", HandleBoatSelectionChange);
  $('#cp11').colorpicker({
    useAlpha: false,
    format: false
  });
  $(document.body).on('click', ".ShowICSButton", function (e) {
    HandleFillICSButton(e);
  });
  $(document.body).on('click', ".ShowRaceInSpectatorMode", function (e) {
    HandleGoToRaceSpectator(e);
  });
  $("#PolarTab").on("click", HandlePolarTabClik);
  CheckLogin();
  UpdateVersionLine();
}

function InitRankingEvents() {
  $("#Ranking-Panel").on('shown.bs.collapse', function (e) {
    HandleRaceSortChange(e);
  });
  $(document.body).on('click', ".RankingButton", function (e) {
    var RaceId = $(e.currentTarget).attr("IdRace");

    if (typeof RaceId !== "undefined" && RaceId) {
      window.open('/jvlm?RaceRank=' + RaceId, "RankTab");
    }
  }); // Handle clicking on ranking button, and ranking sub tabs

  $(document.body).on('click', "[RnkSort]", function (e) {
    HandleRaceSortChange(e);
  });
  $("#Ranking-Panel").on('hide.bs.collapse', function (e) {
    ResetRankingWPList(e);
  });
}

function UpdateVersionLine() {
  var Build = new moment(BuildDate);
  $("#BuildDate").text("Build : " + Build.fromNow());
  $('[data-toggle="tooltip"]').tooltip();
}

var _CachedRaceInfo = null;

function HandlePolarTabClik() {
  if (_CachedRaceInfo) {
    DrawPolar(_CachedRaceInfo);
  }
}

function InitPolar(RaceInfo) {
  _CachedRaceInfo = RaceInfo;
}

function HandleGoToRaceSpectator(e) {
  if (typeof e !== "undefined" && e) {
    var b = e.target;
    var RaceId = $(e.currentTarget).attr('idRace');

    if (typeof RaceId !== "undefined" && RaceId) {
      window.open("/guest_map/index.html?idr=" + RaceId, "Spec_" + RaceId);
      return;
    }
  }
}

function HandleFillICSButton(e) {
  // Race Instruction
  if (typeof e !== "undefined" && e) {
    var b = e.target;
    var RaceId = $(e.currentTarget).attr('idRace');

    if (typeof RaceId !== "undefined" && RaceId) {
      HandleShowICS(RaceId);
      return;
    }
  }

  if (typeof _CurPlayer !== "undefined" && _CurPlayer && _CurPlayer.CurBoat && _CurPlayer.CurBoat.RaceInfo) {
    FillRaceInstructions(_CurPlayer.CurBoat.RaceInfo);
  }
}

var VLMAgenda = null;

function HandleShowAgenda() {
  if (VLMAgenda) {
    VLMAgenda.destroy();
  }

  var CalEl = jQuery('#Calendar')[0];
  VLMAgenda = new FullCalendar.Calendar(CalEl, {
    plugins: ['dayGrid'],
    locale: _CurLocale,
    editable: false,
    header: {
      left: 'title',
      center: '',
      right: 'today prev,next'
    },
    firstDay: 1,
    events: "/feed/races.fullcalendar.php",
    data: function data() {
      // a function that returns an object
      return {
        jvlm: 1
      };
    },
    timeFormat: 'H:mm',
    loading: function loading(bool) {
      if (bool) jQuery('#loading').show();else jQuery('#loading').hide();
    }
  });
  VLMAgenda.render();
  $("#Infos").modal("hide");
}

function HandlePasswordChangeRequest(e) {
  // Check non empty value for oldpassword
  var OldPwd = $("#CurPassword")[0].value;
  var NewPwd1 = $("#NewPassword1")[0].value;
  var NewPwd2 = $("#NewPassword2")[0].value;
  $(".Password").val("");

  if (!OldPwd || OldPwd === "") {
    VLMAlertDanger(GetLocalizedString("CurPwdRequired"));
    return;
  } else if (NewPwd1 !== NewPwd2) {
    VLMAlertDanger(GetLocalizedString("CurPwdRequired"));
    return;
  } else if (NewPwd1 === "") {
    VLMAlertDanger(GetLocalizedString("NewPwdRequired"));
    return;
  }

  var PostData = {
    OldPwd: OldPwd,
    NewPwd: NewPwd1
  };
  $.post("/ws/playersetup/password_change.php", "parms=" + JSON.stringify(PostData), function (e) {
    HandlePasswordChangeResult(e);
  });
}

function HandlePasswordChangeResult(e) {
  if (e.success) {
    VLMAlertInfo();
  } else {
    VLMAlertDanger(GetLocalizedString(e.error.msg));
  }
}

function SendResetPassword(RecaptchaCode) {
  var PostData = {
    email: PasswordResetInfo[0],
    seed: PasswordResetInfo[1],
    key: RecaptchaCode
  };
  $.get("/ws/playersetup/password_reset.php?email=" + PasswordResetInfo[0] + "&seed=" + PasswordResetInfo[1] + "&key=" + RecaptchaCode, function (e) {
    HandlePasswordReset(e, true);
  });
}

function SendResetPasswordLink(RecaptchaCode) {
  var UserMail = $(".UserName").val();

  if (UserMail === "") {
    VLMAlertDanger(GetLocalizedString("Enter your email for resetting your password"));
    grecaptcha.reset(RC_PwdResetReq);
    return;
  }

  var PostData = {
    email: UserMail,
    key: RecaptchaCode
  };
  $.post("/ws/playersetup/password_reset.php", "parms=" + JSON.stringify(PostData), function (e) {
    HandlePasswordReset(e, false);
  });
}

function HandlePasswordReset(e, Validation) {
  if (e.success) {
    if (Validation) {
      VLMAlertInfo(GetLocalizedString('Check your inbox to get your new password.'));
      grecaptcha.reset(RC_PwdResetReq);
    } else {
      VLMAlertInfo(GetLocalizedString('An email has been sent. Click on the link to validate.'));
      grecaptcha.reset(RC_PwdResetConfirm);
    }
  } else {
    VLMAlertDanger("Something went wrong :(");
    grecaptcha.reset(RC_PwdResetReq);
    grecaptcha.reset(RC_PwdResetConfirm);
  }
}

function InitFooTable(Id) {
  var ret = FooTable.init("#" + Id, {
    'name': Id,
    'on': {
      'ready.ft.table': HandleReadyTable,
      'after.ft.paging': HandlePagingComplete,
      'postdraw.ft.table': HandleTableDrawComplete
    }
  });
  ret.DrawPending = true;
  ret.CallbackPending = null;
  return ret;
}

function InitFootables() {
  // Handle race discontinuation request
  $("#DiscontinueRaceButton").on('click', HandleDiscontinueRaceRequest); // Init Pilototo footable, and get pointer to object          

  PilototoFt = InitFooTable("PilototoTable");
  RankingFt = InitFooTable("RankingTable");
  RaceHistFt = InitFooTable("BoatRaceHist");
  ICS_WPft = InitFooTable("RaceWayPoints");
  NSZ_WPft = InitFooTable("NSZPoints");
  VLMINdexFt = InitFooTable("VLMIndexTable");
}

function HandleUpdatePilototoTable(e) {
  UpdatePilotInfo(_CurPlayer.CurBoat);
}

function InitSlider(SliderId, HandleId, min, max, value, SlideCallback) {
  var handle = $("#" + HandleId);
  $("#" + SliderId).slider({
    orientation: "vertical",
    min: min,
    max: max,
    value: value,
    create: function create() {
      handle.text($(this).slider("value"));
    },
    slide: function slide(event, ui) {
      SlideCallback(event, ui);
    }
  });
}

function InitGribSlider() {
  InitSlider("GribSlider", "GribSliderHandle", 0, 72, 0, HandleGribSlideMove);
}

function HandleRaceSortChange(e) {
  var Target = $(e.currentTarget).attr('rnksort'); //$("[rnksort]").removeClass("active")

  switch (Target) {
    case 'WP':
      SortRanking(Target, $(e.currentTarget).attr('WPRnk'));
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

function HandleGribSlideMove(event, ui) {
  var handle = $("#GribSliderHandle");
  handle.text(ui.value);
  var GribEpoch = new Date().getTime();
  map.GribMap.SetGribMapTime(GribEpoch + ui.value * 3600000);

  if (VLM2Prefs.MapPrefs.TrackEstForecast && _CurPlayer.CurBoat.Estimator) {
    var EstPos = _CurPlayer.CurBoat.GetClosestEstimatePoint(new Date(GribEpoch + ui.value * 3600 * 1000));

    RefreshEstPosLabels(EstPos);
    StartEstimateTimeout();
  }
}

function HandleDiscontinueRaceRequest() {
  GetUserConfirmation(GetLocalizedString('unsubscribe'), true, HandleRaceDisContinueConfirmation);
}

function HandleRaceDisContinueConfirmation(State) {
  if (State) {
    //construct base
    var BoatId = _CurPlayer.CurBoat.IdBoat;
    var RaceId = _CurPlayer.CurBoat.Engaged;
    DiconstinueRace(BoatId, RaceId);
    $("#ConfirmDialog").modal('hide');
    $("#RacesInfoForm").modal('hide');
  } else {
    VLMAlertDanger("Ouf!");
  }
}

function HandleStopEstimator(e) {
  var CurBoat = _CurPlayer.CurBoat;

  if (typeof CurBoat === "undefined" || !CurBoat) {
    // Something's wrong, just ignore
    return;
  }

  CurBoat.Estimator.Stop();
}

function HandleStartEstimator(e) {
  var CurBoat = _CurPlayer.CurBoat;

  if (typeof CurBoat === "undefined" || !CurBoat) {
    // Something's wrong, just ignore
    return;
  }

  CurBoat.Estimator.Start(HandleEstimatorProgress);
}

var LastPctRefresh = -1;
var LastPctDraw = -1;

function HandleEstimatorProgress(Complete, Pct, Dte) {
  if (Complete) {
    $("#StartEstimator").removeClass("hidden");
    $("#PbEstimatorProgressBar").addClass("hidden"); //$("#PbEstimatorProgressText").addClass("hidden")

    $("#EstimatorStopButton").addClass("hidden");
    LastPctRefresh = -1;
    LastPctDraw = -1;
  } else if (Pct - LastPctRefresh > 0.15) {
    $("#EstimatorStopButton").removeClass("hidden");
    $("#StartEstimator").addClass("hidden");
    $("#PbEstimatorProgressBar").removeClass("hidden");
    $("#PbEstimatorProgressText").removeClass("hidden");
    $("#PbEstimatorProgressText").text(Pct);
    $("#PbEstimatorProgress").css("width", Pct + "%");
    $("#PbEstimatorProgress").attr("aria-valuenow", Pct);
    $("#PbEstimatorProgress").attr("aria-valuetext", Pct);
    LastPctRefresh = Pct;
  } else if (Pct - LastPctDraw > 1) {
    DrawBoatEstimateTrack(_CurPlayer.CurBoat, GetRaceMapFeatures(_CurPlayer.CurBoat));
    LastPctDraw = Pct;
  }
}

function HandleFlagLineClick(e) {
  var Flag = e.target.attributes.flag.value;
  SelectCountryDDFlag(Flag);
}

function HandleCancelSetWPOnClick() {
  SetWPPending = false;
  $("#SetWPOnClick").show();
  $("#SetWPOffClick").hide();
}

function HandleStartSetWPOnClick() {
  SetWPPending = true;
  WPPendingTarget = "WP";
  $("#SetWPOnClick").hide();
  $("#SetWPOffClick").show();
}

function ClearBoatSelector() {
  $(".BoatSelectorDropDownList").empty();
}

function AddBoatToSelector(boat, isfleet) {
  BuildUserBoatList(boat, isfleet);
}

function BuildUserBoatList(boat, IsFleet) {
  $(".BoatSelectorDropDownList").append(GetBoatDDLine(boat, IsFleet));
}

function GetBoatDDLine(Boat, IsFleet) {
  var Line = '<li class="DDLine" BoatID="' + Boat.IdBoat + '">';
  Line = Line + GetBoatInfoLine(Boat, IsFleet) + '</li>';
  return Line;
}

function GetBoatInfoLine(Boat, IsFleet) {
  var Line = "";
  var BoatStatus = "racing";

  if (!Boat.Engaged) {
    BoatStatus = "Docked";
  }

  if (typeof Boat.VLMInfo !== "undefined" && Boat.VLMInfo["S&G"]) {
    BoatStatus = "stranded";
  }

  if (!IsFleet) {
    Line = Line + '<span class="badge">BS';
  }

  Line = Line + '<img class="BoatStatusIcon" src="images/' + BoatStatus + '.png" />';

  if (!IsFleet) {
    Line = Line + '</span>';
  }

  Line = Line + '<span>-</span><span>' + HTMLDecode(Boat.BoatName) + '</span>';
  return Line;
}

function ShowBgLoad() {
  $("#BgLoadProgress").css("display", "block");
}

function HideBgLoad() {
  $("#BgLoadProgress").css("display", "block");
}

function ShowPb(PBName) {
  $(PBName).show(); //LocalizeString();
}

function HidePb(PBName) {
  $(PBName).hide();
}

function DisplayLoggedInMenus(LoggedIn) {
  var LoggedInDisplay;
  var LoggedOutDisplay;

  if (LoggedIn) {
    LoggedInDisplay = "block";
    LoggedOutDisplay = "none";
  } else {
    LoggedInDisplay = "none";
    LoggedOutDisplay = "block";
  }

  $("[LoggedInNav='true']").css("display", LoggedInDisplay);
  $("[LoggedInNav='false']").css("display", LoggedOutDisplay);

  if (typeof _CurPlayer !== 'undefined' && _CurPlayer && _CurPlayer.IsAdmin) {
    $("[AdminNav='true']").css("display", "block");
  } else {
    $("[AdminNav='true']").css("display", "none");
  } // Display apropos


  ShowApropos(LoggedIn);
}

function ShowApropos(DisplayModal) {
  $('#Apropos').modal(DisplayModal ? 'hide' : 'show');
}

function HandleRacingDockingButtons(IsRacing) {
  if (IsRacing) {
    $('[RacingBtn="true"]').removeClass("hidden");
    $('[RacingBtn="false"]').addClass("hidden");
  } else {
    $('[RacingBtn="true"]').addClass("hidden");
    $('[RacingBtn="false"]').removeClass("hidden");
  }
}

function UpdateInMenuDockingBoatInfo(Boat) {
  var IsRacing = typeof Boat !== "undefined" && typeof Boat.VLMInfo !== "undefined" && parseInt(Boat.VLMInfo.RAC, 10);
  HandleRacingDockingButtons(IsRacing);
}

function SetTWASign(Boat) {
  var twd = Boat.VLMInfo.TWD;
  var heading = Boat.VLMInfo.HDG;
  var twa = twd - heading;

  if (twa < -180) {
    twa += 360;
  }

  if (twa > 180) {
    twa -= 360;
  }

  var winddir = (360 - twd) % 360 + 90;
  var boatdir = (360 - heading) % 360 + 90;

  if (twa * Boat.VLMInfo.TWA > 0) {
    Boat.VLMInfo.TWA = -Boat.VLMInfo.TWA;
  }
}

function UpdateInMenuRacingBoatInfo(Boat, TargetTab) {
  var NorthSouth;
  var EastWest;

  if (!Boat || typeof Boat === "undefined") {
    return;
  }

  HandleRacingDockingButtons(true); // Put a sign to the TWA

  SetTWASign(Boat); // Fix HDG when boat is mooring

  if (Boat.VLMInfo.PIM === "2" && Boat.VLMInfo.PIP === "0") {
    // Mooring 
    Boat.VLMInfo.HDG = Boat.VLMInfo.TWD;
    Boat.VLMInfo.BSP = 0;
  } // Update GUI for current player
  // Todo Get Rid of Coords Class


  var lon = new Coords(Boat.VLMInfo.LON, true);
  var lat = new Coords(Boat.VLMInfo.LAT); // Create field mapping array
  // 0 for text fields
  // 1 for input fields

  var BoatFieldMappings = [];
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatLon", lon.toString()]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#BoatLat", lat.toString()]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatSpeed", RoundPow(Boat.VLMInfo.BSP, 2)]);
  BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatHeading", RoundPow(Boat.VLMInfo.HDG, 1)]);
  BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Heading", RoundPow(Boat.VLMInfo.HDG, 2)]);
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
  var WP = new VLMPosition(Boat.VLMInfo.WPLON, Boat.VLMInfo.WPLAT);
  BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Lat", WP.Lat.Value]);
  BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Lon", WP.Lon.Value]);

  if (WP.Lon.Value === 0 && WP.Lat.Value === 0) {
    WP = Boat.GetNextWPPosition();
  }

  if (typeof WP !== "undefined" && WP) {
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#PM_CurWPLat", WP.Lat.toString()]);
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#PM_CurWPLon", WP.Lon.toString()]);
  } else {
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#PM_CurWPLat", "N/A"]);
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, "#PM_CurWPLon", "N/A"]);
  }

  if (parseInt(Boat.VLMInfo.PIM, 10) === PM_ANGLE) {
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatWindAngle", RoundPow(Math.abs(Boat.VLMInfo.PIP), 1)]);
    BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Angle", Boat.VLMInfo.PIP]);
  } else {
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatWindAngle", RoundPow(Math.abs(Boat.VLMInfo.TWA), 1)]);
    BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Angle", RoundPow(Boat.VLMInfo.TWA, 1)]);
  }

  FillFieldsFromMappingTable(BoatFieldMappings); // Change color depênding on windangle

  var WindColor = "lime";

  if (Boat.VLMInfo.TWA > 0) {
    WindColor = "red";
  }

  $(".BoatWindAngle").css("color", WindColor); // Get WindAngleImage

  var wHeading = Math.round((Boat.VLMInfo.TWD + 180) * 100) / 100;
  var wSpeed = Math.round(Boat.VLMInfo.TWS * 100) / 100;
  var BoatType = Boat.VLMInfo.POL;
  var BoatHeading = Math.round(Boat.VLMInfo.HDG * 100) / 100;
  var WindSpeed = Math.round(Boat.VLMInfo.TWS * 100) / 100;
  var OrthoToWP = Math.round(Boat.VLMInfo.ORT * 100) / 100;
  $("#ImgWindAngle").attr('src', 'windangle.php?wheading=' + wHeading + '&boatheading=' + BoatHeading + '&wspeed=' + WindSpeed + '&roadtoend=' + OrthoToWP + '&boattype=' + BoatType + "&jvlm=" + Boat.VLMInfo.NOW);
  $("#ImgWindAngle").css("transform", "rotate(" + wHeading + "deg)");
  $("#DeckImage").css("transform", "rotate(" + BoatHeading + "deg)"); // Set active PM mode display

  $(".PMActiveMode").css("display", "none");
  $(".BCPane").removeClass("active");
  var TabID = ".ActiveMode_";
  var ActivePane = "";

  switch (Boat.VLMInfo.PIM) {
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
  } // Override PIM Tab if requested

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

function FillFieldsFromMappingTable(MappingTable) {
  // Loop all mapped fields to their respective location
  for (var _index7 in MappingTable) {
    if (MappingTable[_index7]) {
      switch (MappingTable[_index7][0]) {
        case FIELD_MAPPING_TEXT:
          $(MappingTable[_index7][1]).text(MappingTable[_index7][2]);
          break;

        case FIELD_MAPPING_VALUE:
          $(MappingTable[_index7][1]).val(MappingTable[_index7][2]);
          break;

        case FIELD_MAPPING_CHECK:
          $(MappingTable[_index7][1]).prop('checked', MappingTable[_index7][2]);
          break;

        case FIELD_MAPPING_IMG:
          $(MappingTable[_index7][1]).attr('src', MappingTable[_index7][2]);
          break;

        case FIELD_MAPPING_CALLBACK:
          MappingTable[_index7][2](MappingTable[_index7][1]);

          break;

        case FIELD_MAPPING_STYLE:
          $(MappingTable[_index7][1]).css(MappingTable[_index7][2], MappingTable[_index7][3]);
      }
    }
  }
}

function FillRaceInstructions(RaceInfo) {
  if (typeof RaceInfo === "undefined" || !RaceInfo) {
    return;
  }

  var HideDiscontinueTab = true;

  if (typeof _CurPlayer !== "undefined" && _CurPlayer && _CurPlayer.CurBoat && _CurPlayer.CurBoat.RaceInfo) {
    HideDiscontinueTab = _CurPlayer.CurBoat.RaceInfo.idraces !== RaceInfo.idraces;
  }

  if (HideDiscontinueTab) {
    $("#DiscontinueRaceTab").addClass("hidden");
  } else {
    $("#DiscontinueRaceTab").removeClass("hidden");
  }

  var Instructions = [];
  FillRaceInfoHeader(RaceInfo);
  FillRaceWaypointList(RaceInfo);
  InitPolar(RaceInfo);
  $.get("/ws/raceinfo/exclusions.php?idr=" + RaceInfo.idraces + "&v=" + RaceInfo.VER, function (result) {
    if (result && result.success) {
      FillNSZList(result.Exclusions);
    }
  });
}

var PolarSliderInited = false;

function FillRaceInfoHeader(RaceInfo) {
  if (typeof RaceInfo === 'undefined' || !RaceInfo) {
    return;
  }

  var BoatFieldMappings = [];
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

function HandlePolarSpeedSlide(event, ui, RaceInfo) {
  var handle = $("#PolarSpeedHandle");
  handle.text(ui.value);
  DrawPolar(RaceInfo);
}

function DrawPolar(RaceInfo) {
  var Canvas = $("#PolarCanvas")[0];
  var WindSpeed = 25;

  if (PolarSliderInited) {
    WindSpeed = parseFloat($("#PolarSpeedHandle").text());
  }

  var PolarLine = PolarsManager.GetPolarLine(RaceInfo.boattype, WindSpeed, function () {
    DrawPolar(RaceInfo);
  }, null, 1);

  if (PolarLine) {
    if (!PolarSliderInited) {
      InitSlider("PolarSpeedSlider", "PolarSpeedHandle", 0, 60, WindSpeed, function (e, ui) {
        HandlePolarSpeedSlide(e, ui, RaceInfo);
      });
      PolarSliderInited = true;
    }

    Canvas.width = $("#PolarCanvas").width();
    Canvas.height = Canvas.width;
    var Context = Canvas.getContext("2d");
    var First = true;
    var dAlpha = Math.PI / PolarLine.length; // Not counting 0 helps here

    var Cx = 3;
    var Cy = Canvas.width / 2;
    var S = Canvas.width / 2;
    var MaxSpeed = PolarsManager.GetPolarMaxSpeed(RaceInfo.boattype, WindSpeed);
    var PrevL = 0;
    var VMGAngle = 0;
    var RedZone = true;
    var PrevX;
    var PrevY;
    Context.beginPath();
    Context.lineWidth = "1";
    Context.strokeStyle = "#FF0000";

    for (var _index8 in PolarLine) {
      if (PolarLine[_index8]) {
        var l = PolarLine[_index8];
        _index8 = parseInt(_index8, 10);
        var a = _index8 * dAlpha;
        var y = Cy + S * l * Math.cos(a);
        var x = Cx + S * l * Math.sin(a);
        var VMG = Math.cos(a + VMGAngle) * l;

        if (RedZone && VMG <= PrevL) {
          Context.stroke();
          Context.beginPath();
          Context.moveTo(PrevX, PrevY);
          Context.strokeStyle = "#FFFFFF";
          RedZone = false;
        } else if (!RedZone && VMG >= PrevL) {
          Context.stroke();
          Context.beginPath();
          Context.moveTo(PrevX, PrevY);
          Context.strokeStyle = "#FF0000";
          RedZone = true;
        }

        PrevL = VMG;

        if (First) {
          Context.moveTo(x, y);
          First = false;
        } else {
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
    Context.stroke(); // Draw Speed circles & legends

    var As = Math.round(MaxSpeed / 5);

    if (!As) {
      As = 1;
    }

    for (var _index9 = 1; As * _index9 - 1 <= MaxSpeed; _index9++) {
      Context.beginPath();
      Context.strokeStyle = "#7FFFFF";
      Context.arc(Cx, Cy, S * _index9 * As / MaxSpeed, Math.PI / 2, 1.5 * Math.PI, true);
      Context.stroke();
      Context.strokeText(" " + As * _index9, Cx + 1 + As * S * _index9 / MaxSpeed, Cy + 10);
    }
  }
}

function UpdatePolarImages(Boat) {
  var PolarName = Boat.VLMInfo.POL.substring(5);
  var Angle;
  var HTML = "";

  for (Angle = 0; Angle <= 45; Angle += 15) {
    HTML += '<li><img class="polaire" src="/scaledspeedchart.php?boattype=boat_' + PolarName + '&amp;minws=' + Angle + '&amp;maxws=' + (Angle + 15) + '&amp;pas=2" alt="speedchart"></li>';
  }

  $("#PolarList").empty();
  $("#PolarList").append(HTML);
}

function BackupFooTable(ft, TableId, RestoreId) {
  if (!ft.DOMBackup) {
    ft.DOMBackup = $(TableId);
    ft.RestoreId = RestoreId;
  } else if (typeof $(TableId)[0] === "undefined") {
    $(ft.RestoreId).append(ft.DOMBackup);
    console.log("Restored footable " + TableId);
  }
}

function UpdatePilotInfo(Boat) {
  if (typeof Boat === "undefined" || !Boat || PilototoFt.DrawPending) {
    return;
  }

  BackupFooTable(PilototoFt, "#PilototoTable", "#PilototoTableInsertPoint");
  var PilRows = [];

  if (Boat && Boat.VLMInfo && Boat.VLMInfo.PIL && Boat.VLMInfo.PIL.length > 0) {
    for (var _index10 in Boat.VLMInfo.PIL) {
      if (Boat.VLMInfo.PIL[_index10]) {
        var PilLine = GetPilototoTableLigneObject(Boat, _index10);
        PilRows.push(PilLine);
      }
    }

    if (Boat.VLMInfo.PIL.length < MAX_PILOT_ORDERS) {
      $("#AutoPilotAddButton").removeClass("hidden");
    } else {
      $("#AutoPilotAddButton").addClass("hidden");
    }
  }

  PilototoFt.DrawPending = true;
  PilototoFt.loadRows(PilRows, false);
  console.log("loaded pilototo table");
  UpdatePilotBadge(Boat);
}

function HandleReadyTable(e, ft) {
  console.log("Table ready" + ft);
  ft.DrawPending = false;

  if (ft.OnReadyTable) {
    ft.OnReadyTable();
  }
}

function HandlePagingComplete(e, ft) {
  var classes = {
    ft_class_myboat: "rnk-myboat",
    ft_class_friend: "rnk-friend",
    ft_class_oncoast: "rnk-oncoast",
    ft_class_racing: "rnk-racing",
    ft_class_locked: "rnk-locked",
    ft_class_dns: "rnk-dns"
  };
  var index;

  for (var _index11 in classes) {
    if (classes[_index11]) {
      $('td').closest('tr').removeClass(classes[_index11]);
    }
  }

  for (index in classes) {
    if (classes[index]) {
      $('td:contains("' + index + '")').closest('tr').addClass(classes[index]);
    }
  }

  ft.DrawPending = false;
}

function HandleTableDrawComplete(e, ft) {
  console.log("TableDrawComplete " + ft.id);
  ft.DrawPending = false;

  if (ft === RankingFt) {
    setTimeout(function () {
      DeferedGotoPage(e, ft);
    }, 500);
  } else if (ft.CallbackPending) {
    setTimeout(function () {
      ft.CallbackPending();
      ft.CallbackPending = null;
    }, 500);
    return;
  }
}

function DeferedGotoPage(e, ft) {
  if (RankingFt.TargetPage) {
    RankingFt.gotoPage(RankingFt.TargetPage);
    RankingFt.TargetPage = 0;
  }

  setTimeout(function () {
    DeferedPagingStyle(e, ft);
  }, 200);
}

function DeferedPagingStyle(e, ft) {
  HandlePagingComplete(e, ft);
}

function GetPilototoTableLigneObject(Boat, Index) {
  var PilOrder = Boat.VLMInfo.PIL[Index];
  var OrderDate = GetLocalUTCTime(PilOrder.TTS * 1000, true, true);
  var PIMText = GetPilotModeName(PilOrder.PIM); // Force as number and rebase from 1

  Index = parseInt(Index, 10) + 1; // Adapt the template to current order

  $("#EditCellTemplate .PIL_EDIT").attr('pil_id', Index);
  $("#DeleteCellTemplate .PIL_DELETE").attr("TID", PilOrder.TID).attr('pil_id', Index);
  var Ret = {
    date: OrderDate,
    PIM: PIMText,
    PIP: PilOrder.PIP,
    Status: PilOrder.STS,
    Edit: $("#EditCellTemplate").first().html(),
    Delete: $("#DeleteCellTemplate").first().html()
  };
  return Ret;
}

function ShowAutoPilotLine(Boat, Index) {
  var Id = "#PIL" + Index;
  var PilOrder = Boat.VLMInfo.PIL[Index - 1];
  var OrderDate = new Date(PilOrder.TTS * 1000);
  var PIMText = GetPilotModeName(PilOrder.PIM);

  if (typeof $(Id)[0] === "undefined") {
    var bpkt = 0;
  }

  $(Id)[0].attributes.TID = PilOrder.TID;
  SetSubItemValue(Id, "#PIL_DATE", OrderDate);
  SetSubItemValue(Id, "#PIL_PIM", PIMText);
  SetSubItemValue(Id, "#PIL_PIP", PilOrder.PIP);
  SetSubItemValue(Id, "#PIL_STATUS", PilOrder.STS);
  $(Id).show();
}

function GetPILIdParentElement(item) {
  var done = false;
  var RetValue = item;

  do {
    if (typeof RetValue === "undefined") {
      return;
    }

    if ('id' in RetValue.attributes) {
      var ItemId = RetValue.attributes.id.value;

      if (ItemId.length === 4 && ItemId.substring(0, 3) === "PIL") {
        return RetValue;
      }
    }

    RetValue = RetValue.parentElement;
  } while (!done);
}

function HandlePilotEditDelete(e) {
  var ClickedItem = $(this)[0];
  var ItemId = ClickedItem.attributes['class'].value;
  var Boat = _CurPlayer.CurBoat;
  var OrderIndex = parseInt(ClickedItem.attributes.pil_id.value, 10);

  if (ItemId === "PIL_EDIT") {
    HandleOpenAutoPilotSetPoint(e);
  } else if (ItemId === "PIL_DELETE") {
    DeletePilotOrder(Boat, ClickedItem.attributes.TID.value);
  }
}

function GetPilotModeName(PIM) {
  switch (parseInt(PIM, 10)) {
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

function SetSubItemValue(SourceElementName, TargetElementName, NewVaue) {
  var El = $(SourceElementName).find(TargetElementName);

  if (El.length > 0) {
    El.text(NewVaue);
  }
}

function UpdatePilotBadge(Boat) {
  var index;
  var PendingOrdersCount = 0;

  if (typeof Boat === "undefined" || !Boat) {
    return;
  }

  var Pilot = Boat.VLMInfo.PIL;

  if (typeof Pilot !== "undefined" && Pilot && Pilot.length) {
    for (index in Pilot) {
      if (Pilot[index].STS === "pending") {
        PendingOrdersCount++;
      }
    }
  }

  if (PendingOrdersCount > 0) {
    $(".PilotOrdersBadge").show();
    $(".PilotOrdersBadge").text(PendingOrdersCount);
  } else {
    $(".PilotOrdersBadge").hide();
  }
}

function MoveWPBoatControlerDiv(target) {
  var div = $(target).prepend($("#PM_WPMode_Div"));
}

function UpdatePrefsDialog(Boat) {
  // Hide prefs setting button is not boat or no vlminfo yet...
  if (typeof Boat === "undefined") {
    $("#BtnSetting").addClass("hidden");
  } else {
    $("#BtnSetting").removeClass("hidden");
    $("#pref_boatname").val(Boat.BoatName);

    if (typeof Boat.VLMInfo !== 'undefined') {
      SelectCountryDDFlag(Boat.VLMInfo.CNT);
      var ColString = SafeHTMLColor(Boat.VLMInfo.COL);
      $("#pref_boatcolor").val(ColString);
      $("#cp11").colorpicker({
        useAlpha: false,
        format: false,
        color: ColString
      });
    }
  }
}

var RaceSorter = function RaceSortEvaluator(r1, r2) {
  if (r1.CanJoin === r2.CanJoin) {
    if (r1.deptime > r2.deptime) {
      return -1;
    } else if (r1.deptime === r2.deptime) {
      if (r1.racename > r2.racename) {
        return 1;
      } else if (r1.racename === r2.racename) {
        return 0;
      } else {
        return -1;
      }
    } else {
      return 1;
    }
  } else if (r1.CanJoin) {
    return 1;
  } else {
    return -1;
  }
};

function LoadRacesList() {
  var CurUser = _CurPlayer.CurBoat.IdBoat;
  $("#RaceListPanel").empty().append("<H4>...</H4>");
  $.get("/ws/raceinfo/list.php?iduser=" + CurUser + "&v=" + new Date().getTime(), function (result) {
    var racelist = result; // Clear previous elements

    $("#RaceListPanel").empty();
    var RaceArray = [];

    for (var _index12 in racelist) {
      if (racelist[_index12]) {
        RaceArray.push(racelist[_index12]);
      }
    }

    RaceArray.sort(RaceSorter);

    for (var _index13 in RaceArray) {
      if (RaceArray[_index13]) {
        AddRaceToList(RaceArray[_index13]);
      }
    } // Resize button height to be uniform with highest one.      


    var highestBox = 0;
    $('#RaceListPanel .btn-group .btn-md').each(function () {
      if ($(this).height() > highestBox) {
        highestBox = $(this).height();
      }
    });
    $('#RaceListPanel .btn-group .btn-md').height(highestBox);
  });
}

function AddRaceToList(race) {
  var base = $("#RaceListPanel").first();
  var d = new Date(0); // The there is the key, which sets the date to the epoch
  //d.setUTCSeconds(utcSeconds);

  var RaceJoinStateClass;
  var StartMoment;

  if (_CurPlayer && _CurPlayer.CurBoat && _CurPlayer.CurBoat.RaceInfo && _CurPlayer.CurBoat.RaceInfo.idraces) {
    race.CanJoin = race.CanJoin & "0" === _CurPlayer.CurBoat.RaceInfo.idraces;
  }

  if (race.CanJoin) {
    var Now = new Date();
    var RaceStart = new Date(race.deptime * 1000);

    if (RaceStart <= Now) {
      RaceJoinStateClass = 'CanJoinRace';
      StartMoment = GetLocalizedString("closerace") + " " + moment("/date(" + race.closetime * 1000 + ")/").fromNow();
    } else {
      RaceJoinStateClass = 'CanJoinRaceNotStarted';
      StartMoment = GetLocalizedString("departuredate") + " " + moment("/date(" + race.deptime * 1000 + ")/").fromNow();
    }
  } else {
    RaceJoinStateClass = 'NoJoinRace';
  }

  var code = '<div class="raceheaderline panel panel-default ' + RaceJoinStateClass + '" )>' + '  <div data-toggle="collapse" href="#RaceDescription' + race.idraces + '" class="panel-body collapsed " data-parent="#RaceListPanel" aria-expanded="false">' + '    <div class="col-xs-12">' + '      <div class="col-xs-3">' + '        <img class="racelistminimap" src="/cache/minimaps/' + race.idraces + '.png" ></img>' + '      </div>' + '      <div class="col-xs-9">' + '        <div class="col-xs-12">' + '          <span ">' + race.racename + '          </span>' + '        </div>' + '        <div class="btn-group col-xs-12">' + '          <button id="JoinRaceButton" type="button" class="' + (race.CanJoin ? '' : 'hidden') + ' btn-default btn-md col-xs-4" IdRace="' + race.idraces + '"  >' + GetLocalizedString("subscribe") + '          </button>' + '          <button id="SpectateRaceButton" type="button" class="ShowRaceInSpectatorMode btn-default btn-md col-xs-4" IdRace="' + race.idraces + '"  >' + GetLocalizedString("Spectator") + '          </button>' + '          <button type="button" class="ShowICSButton btn-default btn-md col-xs-4" IdRace="' + race.idraces + '"  >' + GetLocalizedString('ic') + '          </button>' + '          <button type="button" class="RankingButton btn-default btn-md col-xs-4" IdRace="' + race.idraces + '"  >' + GetLocalizedString('ranking') + '          </button>' + '        </div>' + '      </div>' + '    </div>' + (StartMoment ? '    <div class="col-xs-12">' + '       <span "> ' + StartMoment + '       </span>' + '    </div>' : "") + '  </div>' + '  <div id="RaceDescription' + race.idraces + '" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">' + '    <div class="panel-body">' + '      <div class="col-xs-12"><img class="img-responsive" src="/cache/racemaps/' + race.idraces + '.png" width="530px"></div>' + '        <div class="col-xs-9"><p>' + GetLocalizedString('race') + ' : ' + race.racename + '</p>' + '          <p>Départ : ' + GetLocalUTCTime(race.deptime * 1000, true, true) + '</p>' + '          <p>' + GetLocalizedString('boattype') + ' : ' + race.boattype.substring(5) + '</p>' + '          <p>' + GetLocalizedString('crank') + ' : ' + race.vacfreq + '\'</p>' + '          <p>' + GetLocalizedString('locktime') + parseInt(race.coastpenalty, 10) / 60.0 + ' \'</p>' + '          <p>' + GetLocalizedString('closerace') + GetLocalUTCTime(race.closetime * 1000, true, true) + '</p>' + '        </div>' + '      </div>' + '    </div>' + '  </div>';
  base.prepend(code); // Handler for the join race button

  $("#JoinRaceButton").click(function (e) {
    var RaceId = e.currentTarget.attributes.idrace.value;
    EngageBoatInRace(RaceId, _CurPlayer.CurBoat.IdBoat);
  });
}

function PageClock() {
  if (typeof _CurPlayer !== "undefined" && _CurPlayer && typeof _CurPlayer.CurBoat !== "undefined") {
    // Display race clock if a racing boat is selected
    var CurBoat = _CurPlayer.CurBoat;

    if (typeof CurBoat !== "undefined" && typeof CurBoat.RaceInfo !== "undefined") {
      var ClockValue = GetRaceClock(CurBoat.RaceInfo, CurBoat.VLMInfo.UDT);
      var Chrono = $(".RaceChrono");

      if (ClockValue < 0) {
        Chrono.removeClass("ChronoRaceStarted").addClass("ChronoRacePending");
      } else {
        Chrono.addClass("ChronoRaceStarted").removeClass("ChronoRacePending");
      }

      $("#RefreshAge").text(moment(_CurPlayer.CurBoat.LastRefresh).fromNow());
      var LastBoatUpdate = new Date(CurBoat.VLMInfo.LUP * 1000);
      var TotalVac = CurBoat.VLMInfo.VAC;
      var TimeToNextUpdate = TotalVac - (new Date() - LastBoatUpdate) / 1000 % TotalVac;
      var Delay = 1000;

      if (TimeToNextUpdate >= TotalVac - 1) {
        Delay = 100;
      }

      $("#pbar_innerdivvac").css("width", +Math.round(TimeToNextUpdate % 60 * 100.0 / 60.0) + "px");
      $("#pbar_innerdivmin").css("width", Math.round(TimeToNextUpdate / TotalVac * 100.0) + "px");
      Chrono.text(GetFormattedChronoString(ClockValue));
    }
  }
}

function GetRaceClock(RaceInfo, UserStartTimeString) {
  var CurDate = new Date();
  var Epoch = new Date(RaceInfo.deptime * 1000);

  if (!(RaceInfo.racetype & RACE_TYPE_RECORD)) {
    // non Permanent race chrono counts from race start time
    return Math.floor((CurDate - Epoch) / 1000);
  } else {
    var UDT = parseInt(UserStartTimeString, 10);

    if (UDT === -1) {
      return 0;
    } else {
      var StartDate = new Date(UDT * 1000);
      return Math.floor((CurDate - StartDate) / 1000);
    }
  }
}

function DisplayCurrentDDSelectedBoat(Boat) {
  $('.BoatDropDown:first-child').html('<span BoatID=' + Boat.IdBoat + '>' + GetBoatInfoLine(Boat, Boat.IdBoat in _CurPlayer.Fleet) + '</span>' + '<span class="caret"></span>');
}

function PadLeftZero(v) {
  if (v < 100) {
    return ("00" + v).slice(-2);
  } else {
    return v;
  }
}

function GetFormattedChronoString(Value) {
  if (Value < 0) {
    Value = -Value;
  } else if (Value === 0) {
    return "--:--:--";
  }

  var Sec = PadLeftZero(Value % 60);
  var Min = PadLeftZero(Math.floor(Value / 60) % 60);
  var Hrs = PadLeftZero(Math.floor(Value / 3600) % 24);
  var Days = PadLeftZero(Math.floor(Value / 3600 / 24));
  var Ret = Hrs.toString() + ":" + Min.toString() + ":" + Sec.toString();

  if (Days > 0) {
    Ret = Days.toString() + " d " + Ret;
  }

  return Ret;
}

function RefreshCurrentBoat(SetCenterOnBoat, ForceRefresh, TargetTab) {
  var BoatIDSpan = $('.BoatDropDown > span');

  if (typeof BoatIDSpan !== "undefined" && typeof BoatIDSpan[0] !== "undefined" && ('BoatId' in BoatIDSpan[0].attributes || 'boatid' in BoatIDSpan[0].attributes)) {
    var BoatId = BoatIDSpan[0].attributes.BoatID.value;
    SetCurrentBoat(GetBoatFromIdu(BoatId), SetCenterOnBoat, ForceRefresh, TargetTab);
  }
}

function UpdateLngDropDown() {
  // Init the language combo to current language
  var lng = GetCurrentLocale();
  $('#SelectionLanguageDropDown:first-child').html('<img class=" LngFlag" lang="' + lng + '" src="images/lng-' + lng + '.png" alt="' + lng + '">' + '<span class="caret"></span>');
}

var _CurAPOrder = null;

function HandleOpenAutoPilotSetPoint(e) {
  var Target = e.target;
  var TargetId;

  if ('id' in Target.attributes) {
    TargetId = Target.attributes.id.nodeValue;
  } else if ('class' in Target.attributes) {
    TargetId = Target.attributes["class"].nodeValue;
  } else {
    VLMAlert("Something bad has happened reload this page....", "alert-danger");
    return;
  }

  switch (TargetId) {
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

function RefreshAPDialogFields() {
  // Update dialog content from APOrder object
  $("#AP_Time").data('DateTimePicker').date(_CurAPOrder.Date);
  $('#AP_PIM:first-child').html('<span>' + _CurAPOrder.GetPIMString() + '</span>' + '<span class="caret"></span>');
  $("#AP_PIP").val(_CurAPOrder.PIP_Value);
  $("#AP_WPLat").val(_CurAPOrder.PIP_Coords.Lat.Value);
  $("#AP_WPLon").val(_CurAPOrder.PIP_Coords.Lon.Value);
  $("#AP_WPAt").val(_CurAPOrder.PIP_WPAngle);
  UpdatePIPFields(_CurAPOrder.PIM);
}

function HandleDateChange(ev) {
  _CurAPOrder.Date = ev.date;
}

function HandleClickToSetWP() {
  SetWPPending = true;
  WPPendingTarget = "AP";
  $("#AutoPilotSettingForm").modal("hide");
}

function HandleAPModeDDClick(e) {
  var NewMode = e.target.attributes.PIM.value;
  _CurAPOrder.PIM = parseInt(NewMode, 10);
  $('#AP_PIM:first-child').html('<span>' + _CurAPOrder.GetPIMString() + '</span>' + '<span class="caret"></span>');
  UpdatePIPFields(_CurAPOrder.PIM);
}

function UpdatePIPFields(PIM) {
  var IsPip = true;

  switch (PIM) {
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

  if (IsPip) {
    $(".AP_PIPRow").removeClass("hidden");
    $(".AP_WPRow").addClass("hidden");
  } else {
    $(".AP_PIPRow").addClass("hidden");
    $(".AP_WPRow").removeClass("hidden");
  }
}

function SaveBoatAndUserPrefs(e) {
  // Check boat prefs
  var NewVals = {};
  var BoatUpdateRequired = false;
  var PlayerUpdateRequired = false; // Get Theme

  var NewTheme = $("#SelectionThemeDropDown").attr("SelTheme");

  if (typeof NewTheme !== "undefined") {
    VLM2Prefs.CurTheme = NewTheme;
  }

  VLM2Prefs.Save();

  if (!ComparePrefString($("#pref_boatname")[0].value, _CurPlayer.CurBoat.BoatName)) {
    NewVals.boatname = encodeURIComponent($("#pref_boatname")[0].value);
    BoatUpdateRequired = true;
  }

  if (!ComparePrefString($("#pref_boatcolor")[0].value, SafeHTMLColor(_CurPlayer.CurBoat.VLMInfo.COL))) {
    NewVals.color = $("#pref_boatcolor")[0].value.substring(1);
    BoatUpdateRequired = true;
  }

  var NewCountry = GetPrefSelFlag();

  if (!ComparePrefString(NewCountry, _CurPlayer.CurBoat.VLMInfo.CNT)) {
    NewVals.country = encodeURIComponent(NewCountry);
    BoatUpdateRequired = true;
  } //NewVals["country"]=$("#FlagSelector")[0].value;
  //NewVals["color"]=$("#pref_boatcolor")[0].value;


  if (BoatUpdateRequired && typeof _CurPlayer !== "undefined" && _CurPlayer) {
    UpdateBoatPrefs(_CurPlayer.CurBoat, {
      prefs: NewVals
    });
  }
}

function GetPrefSelFlag() {
  var Item = $('#CountryDropDown:first-child [flag]')[0];
  return Item.attributes.flag.value;
}

function ComparePrefString(Obj1, Obj2) {
  return Obj1.toString() === Obj2.toString();
}

function SelectCountryDDFlag(Country) {
  $('#CountryDropDown:first-child').html('<div>' + GetCountryDropDownSelectorHTML(Country, false) + '<span class="caret"></span></div>');
}

function ResetCollapsiblePanels(e) {
  $(".collapse").collapse("hide");
}

function HandleBoatSelectionChange(e) {
  ResetCollapsiblePanels();
  var BoatId = $(e.target).closest('li').attr('BoatID');
  var Boat = GetBoatFromIdu(BoatId);

  if (typeof Boat === "undefined" || !Boat) {
    VLMAlertDanger(GetLocalizedString('Error Reload'));
    return;
  }

  SetCurrentBoat(Boat, true, false);
  DisplayCurrentDDSelectedBoat(Boat);
}

var LastMouseMoveCall = 0;
var ShowEstTimeOutHandle = null;

function HandleMapMouseClick(e) {
  if (SetWPPending) {
    if (WPPendingTarget === "WP") {
      CompleteWPSetPosition(e);
      HandleCancelSetWPOnClick();
    } else if (WPPendingTarget === "AP") {
      SetWPPending = false;
      _CurAPOrder.PIP_Coords = new VLMPosition(e.latlng.lng, e.latlng.lat);
      $("#AutoPilotSettingForm").modal("show");
      RefreshAPDialogFields();
    } else {
      SetWPPending = false;
    }
  }
}

function HandleMapMouseMove(e) {
  var LatLng = e.latlng;

  if (typeof _CurPlayer !== "undefined" && _CurPlayer && typeof _CurPlayer.CurBoat !== 'undefined' && typeof _CurPlayer.CurBoat.VLMInfo !== "undefined") {
    var Pos = new VLMPosition(LatLng.lng, LatLng.lat);
    var CurPos = new VLMPosition(_CurPlayer.CurBoat.VLMInfo.LON, _CurPlayer.CurBoat.VLMInfo.LAT);

    var WPPos = _CurPlayer.CurBoat.GetNextWPPosition();

    var EstimatePos = null;
    var Estimated = new Date() - LastMouseMoveCall > 300;

    if (VLM2Prefs.MapPrefs.EstTrackMouse && Estimated) {
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

    if (typeof WPPos !== "undefined" && WPPos) {
      $("#MI_WPLoxoDist").text(WPPos.GetLoxoDist(Pos, 2) + " nM");
      $("#MI_WPOrthoDist").text(WPPos.GetOrthoDist(Pos, 2) + " nM");
      $("#MI_WPLoxo").text(WPPos.GetLoxoCourse(Pos, 2) + " °");
      $("#MI_WPOrtho").text(WPPos.GetOrthoCourse(Pos, 2) + " °");
    } else {
      $("#MI_WPLoxoDist").text("--- nM");
      $("#MI_WPOrthoDist").text("--- nM");
      $("#MI_WPLoxo").text("--- °");
      $("#MI_WPOrtho").text("--- °");
    }

    if (GribMgr) {
      var m = "-- N/A --";
      var GribAgeText = "-- N/A --";
      var GribSpanText = "-- N/A --";

      if (GribMgr.LastGribDate) {
        m = moment("/date(" + GribMgr.LastGribDate * 1000 + ")/").fromNow();
        var ts_start = moment("/date(" + GribMgr.TableTimeStamps[0] * 1000 + ")/");
        var ts_end = moment("/date(" + GribMgr.TableTimeStamps[GribMgr.TableTimeStamps.length - 1] * 1000 + ")/");
        var span = moment.duration(ts_end.diff(ts_start));
        GribAgeText = GetLocalUTCTime(ts_start.add(3.5, "h"), true, true);
        GribSpanText = "" + span.asHours() + " h";
        var now = new Date().getTime() / 1000;

        if (now - ts_start.local().unix() > 7 * 3600) {
          $("#GribLoadOK").addClass("GribNotOK");
        } else if (now - ts_start.local().unix() > 6 * 3600) {
          $("#GribLoadOK").addClass("GribGetsOld");
        } else {
          $("#GribLoadOK").removeClass("GribNotOK");
        }
      }

      $("#MI_SrvrGribAge").text(m);
      $("#MI_LocalGribAge").text(GribAgeText);
      $("#MI_LocalGribSpan").text(GribSpanText);
    }

    if (Estimated) {
      RefreshEstPosLabels(EstimatePos);
    }
  }
}

function StartEstimateTimeout() {
  ShowEstTimeOutHandle = setTimeout(function () {
    _CurPlayer.CurBoat.GetClosestEstimatePoint(null);

    RefreshEstPosLabels(null);
  }, 5000);
}

function RefreshEstPosLabels(Pos) {
  if (Pos && typeof Pos.Date !== "undefined") {
    $("#MI_EstDate").text(GetLocalUTCTime(Pos.Date, false, true));
  } else {
    $("#MI_EstDate").text("");
  }
}

function GetWPrankingLI(WPInfo) {
  return '<li id="RnkWP' + WPInfo.wporder + '" RnkSort="WP" WPRnk="' + WPInfo.wporder + '"><a href="#DivRnkRAC" RnkSort="WP" WPRnk="' + WPInfo.wporder + '">WP ' + WPInfo.wporder + ' : ' + WPInfo.libelle + '</a></li>';
}

function ResetRankingWPList(e) {
  $("[WPRnk]").remove();
  $("#RnkTabsUL").addClass("WPNotInited");
}

function CheckWPRankingList(Boat, OtherRaceWPs) {
  var InitNeeded = $(".WPNotInited");
  var RaceId = GetRankingRaceId(Boat);
  var InitComplete = false;

  if (typeof InitNeeded !== "undefined" && InitNeeded && RaceId) {
    var _index14;

    if (typeof Boat !== "undefined" && Boat && typeof Boat.RaceInfo !== "undefined" && Boat.RaceInfo && RaceId === Boat.RaceInfo.RaceId) {
      BuildWPTabList(_index14, InitNeeded);
      InitComplete = true;
    } else if (OtherRaceWPs) {
      BuildWPTabList(OtherRaceWPs, InitNeeded);
      InitComplete = true;
    } else {
      var Version = 0;

      if (typeof Boat.VLMInfo !== "undefined") {
        Version = Boat.VLMInfo.VER;
      }

      $.get("/ws/raceinfo/desc.php?idrace=" + RaceId + "&v=" + Version, function (result) {
        CheckWPRankingList(Boat, result);
      });
    }
  }

  if (InitComplete) {
    $(InitNeeded).removeClass("WPNotInited");
    $(".JVLMTabs").tabs("refresh");
  }
}

function BuildWPTabList(WPInfos, TabsInsertPoint) {
  var index;

  if (typeof TabsInsertPoint === "undefined" || !TabsInsertPoint) {
    return;
  }

  if (typeof WPInfos === "undefined" || !WPInfos) {
    WPInfos = Boat.RaceInfo.races_waypoints;
  }

  for (index in WPInfos.races_waypoints) {
    if (WPInfos.races_waypoints[index]) {
      var WPInfo = WPInfos.races_waypoints[index];
      var html = GetWPrankingLI(WPInfo);
      $(TabsInsertPoint).append(html);
    }
  }
}

function SortRanking(style, WPNum) {
  //$('#RankingTableBody').empty();
  var Boat = _CurPlayer.CurBoat;
  CheckWPRankingList(Boat);

  if (typeof Boat === "undefined" || !Boat) {
    return;
  }

  var Friends = null;

  if (Boat.VLMPrefs && Boat.VLMPrefs.mapPrefOpponents) {
    Friends = Boat.VLMPrefs.mapPrefOpponents.split(",");
  }

  switch (style) {
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

function SetRankingColumns(style) {
  switch (style) {
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

var RACColumnHeader = ["Rank", "Name", "Distance", "Time", "Loch", "Lon", "Lat", "Last1h", "Last3h", "Last24h", "Delta1st"];
var NRColumnHeader = ["Rank", "Name", "Distance"];
var WPColumnHeader = ["Rank", "Name", "Time", "Loch"];
var RACColumnHeaderLabels = ["ranking", "boatname", "distance", "racingtime", "Loch", "Lon", "Lat", "Last1h", "Last3h", "Last24h", "ecart"];
var NRColumnHeaderLabels = ["ranking", "boatname", "status"];
var WPColumnHeaderLabels = ["ranking", "boatname", "racingtime", "ecart"];

function SetRacingClassRankingColumns() {
  SetColumnsVisibility(RACColumnHeader, RACColumnHeaderLabels);
}

function SetNRClassRankingColumns() {
  SetColumnsVisibility(NRColumnHeader, NRColumnHeaderLabels);
}

function SetWPRankingColumns() {
  SetColumnsVisibility(WPColumnHeader, WPColumnHeaderLabels);
}

function SetColumnsVisibility(cols, labels) {
  var index;

  for (index = 0; index < RankingFt.columns.array.length; index++) {
    if (RankingFt.columns.array[index]) {
      var ColIdx = cols.indexOf(RankingFt.columns.array[index].name);

      if (ColIdx > -1) {
        //RankingFt.columns.array[index].title = GetLocalizedString( labels[ColIdx])
        $("[data-name='" + cols[ColIdx] + "']").attr("I18n", labels[ColIdx]);
      }

      RankingFt.columns.array[index].visible = ColIdx > -1;
    }
  } // use localization to change titles. Hummz creative but title does not seem to update the column header.


  LocalizeItem($("[I18n][data-name]").get());
}

function RnkIsArrived(rnk) {
  if (typeof rnk === "undefined" || typeof rnk.status === "undefined" || !rnk.status) {
    return false;
  }

  return BoatArrivedStatus.indexOf(rnk.status) !== -1;
}

function RnkIsRacing(rnk) {
  if (typeof rnk === "undefined" || typeof rnk.status === "undefined" || !rnk.status) {
    return false;
  }

  return BoatRacingStatus.indexOf(rnk.status) !== -1;
}

function Sort2ArrivedBoats(rnk1, rnk2) {
  var Total1 = parseInt(rnk1.duration, 10) + parseInt(rnk1.penalty, 10);
  var Total2 = parseInt(rnk2.duration, 10) + parseInt(rnk2.penalty, 10);

  if (Total1 > Total2) {
    DebugRacerSort(rnk1, rnk2, 1);
    return 1;
  } else if (Total1 < Total2) {
    DebugRacerSort(rnk1, rnk2, -1);
    return -1;
  } else {
    DebugRacerSort(rnk1, rnk2, 0);
    return 0;
  }
}

function Sort2RacingBoats(rnk1, rnk2) {
  var nwp1 = parseInt(rnk1.nwp, 10);
  var nwp2 = parseInt(rnk2.nwp, 10);

  if (nwp1 === nwp2) {
    var dnm1 = parseFloat(rnk1.dnm);
    var dnm2 = parseFloat(rnk2.dnm);

    if (dnm1 > dnm2) {
      DebugRacerSort(rnk1, rnk2, 1);
      return 1;
    } else if (dnm1 === dnm2) {
      DebugRacerSort(rnk1, rnk2, 0);
      var SortFlag = rnk1.country > rnk2.country ? 1 : rnk1.country === rnk2.country ? 0 : -1;

      if (SortFlag) {
        return SortFlag;
      } else {
        var SortIdu = rnk1.idusers > rnk2.idusers ? 1 : rnk1.idusers === rnk2.idusers ? 0 : -1;
        return SortIdu;
      }
    } else {
      DebugRacerSort(rnk1, rnk2, -1);
      return -1;
    }
  } else if (nwp1 > nwp2) {
    DebugRacerSort(rnk1, rnk2, -1);
    return -1;
  } else {
    DebugRacerSort(rnk1, rnk2, 1);
    return 1;
  }
}

function GetWPDuration(Rnk, WPNum) {
  if (Rnk && Rnk.WP && Rnk.WP[WPNum - 1] && Rnk.WP[WPNum - 1].duration) {
    return parseInt(Rnk.WP[WPNum - 1].duration, 10);
  } else {
    return 9999999999;
  }
}

function WPRaceSort(index) {
  return function (a, b) {
    var wp1 = GetWPDuration(a, index);
    var wp2 = GetWPDuration(b, index);
    return wp1 - wp2;
  };
}

function RacersSort(rnk1, rnk2) {
  if (RnkIsRacing(rnk1) && RnkIsRacing(rnk2)) {
    return Sort2RacingBoats(rnk1, rnk2);
  } else if (RnkIsArrived(rnk1) && RnkIsArrived(rnk2)) {
    return Sort2ArrivedBoats(rnk1, rnk2);
  } else if (RnkIsArrived(rnk1)) {
    DebugRacerSort(rnk1, rnk2, -1);
    return -1;
  } else if (RnkIsArrived(rnk2)) {
    DebugRacerSort(rnk1, rnk2, 1);
    return 1;
  } else if (RnkIsRacing(rnk1)) {
    DebugRacerSort(rnk1, rnk2, 1);
    return -1;
  } else if (RnkIsRacing(rnk2)) {
    DebugRacerSort(rnk1, rnk2, 1);
    return 1;
  } else {
    return Sort2NonRacing(rnk1, rnk2);
  }
}

var DebugCount = 1;

function DebugRacerSort(rnk1, rnk2, res) {
  var debug = false;

  if (debug) {
    console.log(DebugCount++ + "sort " + rnk1.idusers + " vs " + rnk2.idusers + " =>" + res);
  }
}

function Sort2NonRacing(rnk1, rnk2) {
  if (typeof rnk1.idusers !== "undefined" && typeof rnk2.idusers !== "undefined") {
    var SortFlag = rnk1.country > rnk2.country ? 1 : rnk1.country === rnk2.country ? 0 : -1;

    if (SortFlag) {
      return SortFlag;
    } else {
      var _IdUser = parseInt(rnk1.idusers, 10);

      var _IdUser2 = parseInt(rnk2.idusers, 10);

      if (_IdUser > _IdUser2) {
        DebugRacerSort(rnk1, rnk2, 1);
        return 1;
      } else if (_IdUser < _IdUser2) {
        DebugRacerSort(rnk1, rnk2, -1);
        return -1;
      } else {
        DebugRacerSort(rnk1, rnk2, 0);
        return 0;
      }
    }
  } else if (typeof IdUser1 !== "undefined") {
    return -1;
  } else if (typeof IdUser2 !== "undefined") {
    return -1;
  } else {
    var ar = [rnk1, rnk2];
    ar.sort();

    if (ar[0] === rnk1) {
      return 1;
    } else {
      return -1;
    }
  }
}

function GetRankingRaceId(Boat, RaceId) {
  if (!RaceId && !RankingFt.RaceRankingId) {
    return Boat.Engaged;
  } else if (!RaceId) {
    return RankingFt.RaceRankingId;
  } else {
    return RaceId;
  }
}

function SortRankingData(Boat, SortType, WPNum, RaceId) {
  RaceId = GetRankingRaceId(Boat, RaceId);

  if (!Boat || !Rankings[RaceId]) {
    return;
  }

  if (Rankings && Rankings[RaceId] && typeof Rankings[RaceId].RacerRanking === "undefined") //|| Rankings[RaceId].RacerRanking.length !== Rankings[RaceId]+1))
    {
      var _index15;

      Rankings[RaceId].RacerRanking = [];

      for (_index15 in Rankings[RaceId]) {
        if (Rankings[RaceId][_index15]) {
          //Rankings[index].idusers=index;
          Rankings[RaceId].RacerRanking.push(Rankings[RaceId][_index15]);
        }
      }
    }

  switch (SortType) {
    case "WP":
      Rankings[RaceId].RacerRanking.sort(WPRaceSort(WPNum));
      break;

    case 'RAC':
    case 'DNF':
    case 'HC':
    case 'HTP':
    case 'ABD':
    case 'ARR':
      Rankings[RaceId].RacerRanking.sort(RacersSort);
      break;

    default:
      VLMAlertInfo("unexpected sort option : " + SortType);
  }

  var rnk = 1;
  var index = 0;

  for (index in Rankings[RaceId].RacerRanking) {
    if (Rankings[RaceId].RacerRanking[index] && Boat.IdBoat === index) {
      rnk = index + 1;
      break;
    }
  }

  return rnk;
}

function FillWPRanking(Boat, WPNum, Friends) {
  var index;
  var RowNum = 1;
  var BestTime = 0;
  var Rows = [];

  if (!Boat || !RankingFt || RankingFt.DrawPending) {
    return;
  }

  var RaceId = GetRankingRaceId(Boat);
  BackupRankingTable();

  for (index in Rankings[RaceId].RacerRanking) {
    if (Rankings[RaceId].RacerRanking[index]) {
      var RnkBoat = Rankings[RaceId].RacerRanking[index];

      if (RnkBoat.WP && RnkBoat.WP[WPNum - 1] && !RnkBoat.WP[WPNum - 1].Delta) {
        if (!BestTime) {
          BestTime = RnkBoat.WP[WPNum - 1].duration;
          RnkBoat.WP[WPNum - 1].Delta = 0;
          RnkBoat.WP[WPNum - 1].Pct = 0;
        } else {
          RnkBoat.WP[WPNum - 1].Delta = RnkBoat.WP[WPNum - 1].duration - BestTime;
          RnkBoat.WP[WPNum - 1].Pct = 100 * (RnkBoat.WP[WPNum - 1].duration / BestTime - 1);
        }
      }

      if (RnkBoat.WP && RnkBoat.WP[WPNum - 1]) {
        Rows.push(GetRankingObject(RnkBoat, parseInt(index, 10) + 1, WPNum, Friends));

        if (Boat.IdBoat === parseInt(RnkBoat.idusers, 10)) {
          RowNum = Rows.length;
        }
      }
    }
  }

  var TargetPage = RoundPow(RowNum / 20, 0) + (RowNum % 20 >= 10 ? 0 : 1);
  RankingFt.DrawPending = true;
  RankingFt.loadRows(Rows);
  RankingFt.TargetPage = TargetPage;
}

function BackupICS_WPTable() {
  BackupFooTable(ICS_WPft, "#RaceWayPoints", "#RaceWayPointsInsertPoint");
}

function getWaypointHTMLSymbols(WPFormat) {
  var WPSymbols = "";

  switch (WPFormat & (WP_CROSS_CLOCKWISE | WP_CROSS_ANTI_CLOCKWISE)) {
    case WP_CROSS_ANTI_CLOCKWISE:
      WPSymbols += "&#x21BA; ";
      break;

    case WP_CROSS_CLOCKWISE:
      WPSymbols += "&#x21BB; ";
      break;

    default:
  }

  if ((WPFormat & WP_CROSS_ONCE) == WP_CROSS_ONCE) {
    WPSymbols += "&#x2285; ";
  }

  switch (WPFormat & (WP_ICE_GATE_N | WP_ICE_GATE_S)) {
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

function getWaypointHTMLSymbolsDescription(WPFormat) {
  var WPDesc = "";

  switch (WPFormat & (WP_CROSS_CLOCKWISE | WP_CROSS_ANTI_CLOCKWISE)) {
    case WP_CROSS_ANTI_CLOCKWISE:
      WPDesc += GetLocalizedString("Anti-clockwise") + " ";
      break;

    case WP_CROSS_CLOCKWISE:
      WPDesc += GetLocalizedString("Clockwise") + " ";
      break;

    default:
  }

  if ((WPFormat & WP_CROSS_ONCE) == WP_CROSS_ONCE) {
    WPDesc += GetLocalizedString("Only once");
  }

  switch (WPFormat & (WP_ICE_GATE_N | WP_ICE_GATE_S)) {
    case WP_ICE_GATE_S:
      WPDesc += GetLocalizedString("Ice gate") + "(" + GetLocalizedString("South") + ") ";
      break;

    case WP_ICE_GATE_N:
      WPDesc += GetLocalizedString("Ice gate") + "(" + GetLocalizedString("North") + ") ";
      break;

    default:
  }

  if (WPDesc !== "") {
    WPDesc = GetLocalizedString("Crossing") + " : " + WPDesc;
  }

  return WPDesc.trim();
}

function NormalizeRaceInfo(RaceInfo) {
  if (typeof RaceInfo === "undefined" || !RaceInfo || RaceInfo.IsNormalized) {
    return;
  }

  RaceInfo.startlat /= VLM_COORDS_FACTOR;
  RaceInfo.startlong /= VLM_COORDS_FACTOR;

  for (var _index16 in RaceInfo.races_waypoints) {
    if (RaceInfo.races_waypoints[_index16]) {
      var WP = RaceInfo.races_waypoints[_index16];
      WP.latitude1 /= VLM_COORDS_FACTOR;
      WP.longitude1 /= VLM_COORDS_FACTOR;

      if (typeof WP.latitude2 !== "undefined") {
        WP.latitude2 /= VLM_COORDS_FACTOR;
        WP.longitude2 /= VLM_COORDS_FACTOR;
      }
    }
  }

  RaceInfo.IsNormalized = true;
}

function FillRaceWaypointList(RaceInfo) {
  if (ICS_WPft.DrawPending) {
    if (!ICS_WPft.CallbackPending) {
      ICS_WPft.CallbackPending = function () {
        FillRaceWaypointList(RaceInfo);
      };
    }

    return;
  }

  BackupICS_WPTable();

  if (RaceInfo) {
    NormalizeRaceInfo(RaceInfo);
    var Rows = []; // Insert the start point

    var Row = {};
    Row.WaypointId = 0;
    Row.WP1 = RaceInfo.startlat + "<BR>" + RaceInfo.startlong;
    Row.WP2 = "";
    Row.Spec = "";
    Row.Type = GetLocalizedString("startmap");
    Row.Name = "";
    Rows.push(Row);

    for (var _index17 in RaceInfo.races_waypoints) {
      if (RaceInfo.races_waypoints[_index17]) {
        var WP = RaceInfo.races_waypoints[_index17];
        var _Row = {};
        var WPSpec = void 0;
        _Row.WaypointId = WP.wporder;
        _Row.WP1 = WP.latitude1 + "<BR>" + WP.longitude1;

        if (typeof WP.latitude2 !== "undefined") {
          _Row.WP2 = WP.latitude2 + "<BR>" + WP.longitude2;
        } else {
          _Row.WP2 = "@" + WP.laisser_au;
        }

        _Row.Spec = "<span title='" + getWaypointHTMLSymbolsDescription(WP.wpformat) + "'>" + getWaypointHTMLSymbols(WP.wpformat) + "</span>";
        _Row.Type = GetLocalizedString(WP.wptype);
        _Row.Name = WP.libelle;
        Rows.push(_Row);
      }
    }

    ICS_WPft.loadRows(Rows);
  }
}

function BackupNSZ_Table() {
  BackupFooTable(NSZ_WPft, "NSZPoints", "NSZPointsInsertPoint");
}

function FillNSZList(Exclusions) {
  if (NSZ_WPft.DrawPending) {
    if (!NSZ_WPft.CallbackPending) {
      NSZ_WPft.CallbackPending = function () {
        FillNSZList(Exclusions);
      };
    }

    return;
  }

  BackupNSZ_Table();

  if (Exclusions) {
    var Rows = [];

    for (var _index18 in Exclusions) {
      if (Exclusions[_index18]) {
        var Seg = Exclusions[_index18];
        var row = {};
        row.NSZId = _index18;
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

function BackupRankingTable() {
  BackupFooTable(RankingFt, "#RankingTable", "#my-rank-content");
}

function BackupVLMIndexTable() {
  BackupFooTable(VLMINdexFt, "#VLMIndexTable", "#my-vlmindex-content");
}

function FillStatusRanking(Boat, Status, Friends) {
  var index;
  var RowNum = 1;
  var Rows = [];
  var RaceId = GetRankingRaceId(Boat);
  BackupRankingTable();

  for (index in Rankings[RaceId].RacerRanking) {
    if (Rankings[RaceId].RacerRanking[index]) {
      var RnkBoat = Rankings[RaceId].RacerRanking[index];

      if (RnkBoat.status === Status) {
        Rows.push(GetRankingObject(RnkBoat, parseInt(index, 10) + 1, null, Friends));

        if (Boat.IdBoat === parseInt(RnkBoat.idusers, 10)) {
          RowNum = Rows.length;
        }
      }
    }
  }

  var TargetPage = RoundPow(RowNum / 20, 0) + (RowNum % 20 >= 10 ? 0 : 1);
  RankingFt.loadRows(Rows);
  RankingFt.TargetPage = TargetPage;
  RankingFt.DrawPending = true;
}

function FillRacingRanking(Boat, Friends) {
  var index;
  var Rows = [];
  var RowNum = 0;
  var Refs = {
    Arrived1stTime: null,
    Racer1stPos: null
  };
  BackupRankingTable();
  var RaceId = GetRankingRaceId(Boat);
  var CurWP = 0;

  if (RaceId && typeof Rankings !== "undefined" && typeof Rankings[RaceId] !== "undefined" && Rankings[RaceId] && Rankings[RaceId].RacerRanking) {
    for (index in Rankings[RaceId].RacerRanking) {
      if (Rankings[RaceId].RacerRanking[index]) {
        var RnkBoat = Rankings[RaceId].RacerRanking[index];

        if (Boat.IdBoat === parseInt(RnkBoat.idusers, 10)) {
          RowNum = Rows.length;
        }

        if (RnkIsArrived(RnkBoat) || RnkIsRacing(RnkBoat)) {
          if (!Refs.Arrived1stTime && RnkIsArrived(RnkBoat)) {
            // First arrived, store time
            Refs.Arrived1stTime = parseInt(RnkBoat.duration, 10);
          }

          if (RnkIsRacing(RnkBoat) && (!Refs.Racer1stPos || RnkBoat.nwp !== CurWP)) {
            Refs.Racer1stPos = RnkBoat.dnm;
            CurWP = RnkBoat.nwp;
          }

          Rows.push(GetRankingObject(RnkBoat, parseInt(index, 10) + 1, null, Friends, Refs));
        } else {
          break;
        }
      }
    }
  }

  var TargetPage = RoundPow(RowNum / 20, 0) + (RowNum % 20 >= 10 ? 0 : 1);
  RankingFt.loadRows(Rows);
  RankingFt.TargetPage = TargetPage;
  RankingFt.DrawPending = true;
}

function GetBoatInfoLink(RnkBoat) {
  var IdUser = parseInt(RnkBoat.idusers, 10);
  var BoatName = RnkBoat.boatname;
  var ret = "";

  if (RnkBoat.country) {
    ret = GetCountryFlagImgHTML(RnkBoat.country);

    if (typeof ret === "undefined") {
      ret = "";
    }
  } //ret += '<a class="RaceHistLink" href="/palmares.php?type=user&idusers='+IdUser+'" target ="_'+IdUser +'">'+BoatName+'</a>';


  ret += '<a class="RaceHistLink" boatid ="' + IdUser + '"data-toggle="tooltip" title="' + IdUser + '" >' + BoatName + '</a>';
  return ret;
}

function GetRankingObject(RankBoat, rank, WPNum, Friends, Refs) {
  var boatsearchstring = ''; //'<img class="BoatFinder" src="images/search.png" id=RnkUsr"'+RankBoat.idusers+'"></img>   '

  if (typeof RankBoat.Challenge !== "undefined" && RankBoat.Challenge[1]) {
    boatsearchstring = '<img class="RnkLMNH" src="images/LMNH.png"></img>' + boatsearchstring;
  }

  boatsearchstring += GetBoatInfoLink(RankBoat);
  var RetObject = {
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

  if (parseInt(RankBoat.idusers, 10) === _CurPlayer.CurBoat.IdBoat) {
    RetObject.Class += " ft_class_myboat";
  }

  if (typeof Friends !== "undefined" && Friends) {
    if (Friends.indexOf(RankBoat.idusers) !== -1) {
      RetObject.Class += " ft_class_friend";
    }
  }

  if (RnkIsRacing(RankBoat) && !WPNum) {
    // General ranking layout
    var NextMark = '[' + RankBoat.nwp + '] -=> ' + RoundPow(RankBoat.dnm, 2);

    if (rank > 1 && Refs && Refs.Racer1stPos) {
      var P = new VLMPosition(RankBoat.longitude, RankBoat.latitude);
      RetObject.Delta1st = RoundPow(RankBoat.dnm - Refs.Racer1stPos, 2);
    }

    RetObject.Distance = NextMark;
    var RacingTime = Math.round((new Date() - new Date(parseInt(RankBoat.deptime, 10) * 1000)) / 1000);
    RetObject.Time = RankBoat.deptime === "-1" ? "" : GetFormattedChronoString(RacingTime);
    RetObject.Loch = RankBoat.loch;
    RetObject.Lon = FormatLon(RankBoat.longitude);
    RetObject.Lat = FormatLat(RankBoat.latitude);
    RetObject.Last1h = RankBoat.last1h;
    RetObject.Last3h = RankBoat.last3h;
    RetObject.Last24h = RankBoat.last24h;

    for (var _index19 in BoatRacingStatus) {
      if (RankBoat.status === BoatRacingStatus[_index19]) {
        RetObject.Class += "  " + BoatRacingClasses[BoatRacingStatus[_index19]];
      }
    }
  } else if (!WPNum) {
    // Non General ranking layout
    var _NextMark = GetLocalizedString("status_" + RankBoat.status);

    RetObject.Distance = _NextMark;
    var Duration = parseInt(RankBoat.duration, 10);
    RetObject.Time = GetFormattedChronoString(Duration);

    if (Refs && Duration !== Refs.Arrived1stTime) {
      RetObject.Time += " ( +" + RoundPow(Duration / Refs.Arrived1stTime * 100 - 100, 2) + "% )";
      RetObject.Delta1st = GetFormattedChronoString(Duration - Refs.Arrived1stTime);
    } else if (Refs && Duration == Refs.Arrived1stTime) {
      RetObject.Delta1st = GetLocalizedString("winner");
    }

    RetObject.Loch = RankBoat.loch; //RetObject.Lon = FormatLon(RankBoat.longitude);
    //RetObject.Lat = FormatLat(RankBoat.latitude);
  } else {
    RetObject.Time = GetFormattedChronoString(parseInt(RankBoat.WP[WPNum - 1].duration, 10));
    var DeltaStr;

    if (RankBoat.WP[WPNum - 1].Delta) {
      var PctString = RoundPow(RankBoat.WP[WPNum - 1].Pct, 2);
      DeltaStr = GetFormattedChronoString(RankBoat.WP[WPNum - 1].Delta) + " (+" + PctString + " %)";
    } else {
      DeltaStr = GetLocalizedString("winner");
    } // Column name is wrong but it works because of cols renaming and hiding


    RetObject.Loch = DeltaStr;
  }

  return RetObject;
}

function formatCoords(v) {
  v = Math.abs(v);
  var D = Math.trunc(v);
  var M = Math.trunc((v - D) * 60);
  var S = RoundPow((v - D) * 3600 % 60.0, 4);
  return "" + D + "° " + M + "' " + S + '"';
}

function FormatLon(v) {
  var EW = v > 0 ? "W" : "E";
  return formatCoords(v) + EW;
}

function FormatLat(v) {
  var NS = v > 0 ? "N" : "S";
  return formatCoords(v) + NS;
}

function HandleShowMapPrefs(e) {
  //Load prefs
  $("#DisplayReals").attr('checked', VLM2Prefs.MapPrefs.ShowReals);
  $("#DisplayNames").attr('checked', VLM2Prefs.MapPrefs.ShowOppNumbers);
  $("#EstTrackMouse").attr('checked', VLM2Prefs.MapPrefs.EstTrackMouse);
  $("#TrackEstForecast").attr('checked', VLM2Prefs.MapPrefs.TrackEstForecast);
  $("#UseUTC").attr('checked', VLM2Prefs.MapPrefs.UseUTC);
  $('#DDMapSelOption:first-child').html('<span Mode=' + VLM2Prefs.MapPrefs.MapOppShow + '>' + VLM2Prefs.MapPrefs.GetOppModeString(VLM2Prefs.MapPrefs.MapOppShow) + '</span>' + '<span class="caret"></span>');

  if (VLM2Prefs.MapPrefs.MapOppShow === VLM2Prefs.MapPrefs.MapOppShowOptions.ShowTop10) {
    $("#NbDisplayBoat").removeClass("hidden");
    $("#NbDisplayBoat").val(VLM2Prefs.MapPrefs.ShowTopCount);
  } else {
    $("#NbDisplayBoat").addClass("hidden");
  }

  $("#VacPol").val(VLM2Prefs.MapPrefs.PolarVacCount);
}

function HandleMapPrefOptionChange(e) {
  var target = e.target;

  if (typeof target === "undefined" || typeof target.attributes.id === "undefined") {
    return;
  }

  var Id = target.attributes.id.value;
  var Value = target.checked;

  switch (Id) {
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

    case "VacPol":
      var VacPol = parseInt($("#VacPol").val(), 10);

      if (VacPol > 0 && VacPol < 120) {
        VLM2Prefs.MapPrefs.PolarVacCount = VacPol;
      } else {
        $("#VacPol").value(12);
      }

      break;

    case "NbDisplayBoat":
      var TopCount = parseInt($("#NbDisplayBoat").val(), 10);
      VLM2Prefs.MapPrefs.ShowTopCount = TopCount;
      break;

    default:
      console.log("unknown pref storage called : " + Id);
      return;
  }

  VLM2Prefs.Save();
  RefreshCurrentBoat(false, false);
}

function SafeHTMLColor(Color) {
  if (typeof Color === "undefined") {
    Color = "#000000";
  }

  Color = "" + Color;

  if (Color.length < 6) {
    Color = ("000000" + Color).slice(-6);
  }

  if (Color.substring(0, 1) !== "#") {
    Color = "#" + Color;
  } else if (Color.substring(1, 2) === "#") {
    Color = Color.substring(1);
  }

  return Color;
}

function HandleMapOppModeChange(e) {
  var t = e.target;

  if (typeof t !== "undefined" && t && typeof t.attributes !== "undefined" && t.attributes.Mode !== "undefined" && t.attributes.Mode) {
    var Mode = parseInt(t.attributes.Mode.value, 10);
    VLM2Prefs.MapPrefs.MapOppShow = Mode;
    VLM2Prefs.Save();
    HandleShowMapPrefs(e);
  }
}

function SetActiveStyleSheet(title) {
  var i, a, main;

  for (i = 0; a = document.getElementsByTagName("link")[i]; i++) {
    if (a.getAttribute("rel").indexOf("style") !== -1 && a.getAttribute("title")) {
      a.disabled = true;

      if (a.getAttribute("title") === title) {
        a.disabled = false;
      }
    }
  }
}

function SetDDTheme(Theme) {
  SetActiveStyleSheet(Theme);
  $("#SelectionThemeDropDown:first-child").html(Theme + '<span class="caret"></span>');
  $("#SelectionThemeDropDown").attr("SelTheme", Theme);
}

function HandleDDlineClick(e) {
  var Target = e.target; //var Theme = Target.closest(".DDTheme").attributes["DDTheme"].value;

  var Theme = e.target.attributes.ddtheme.value;
  SetDDTheme(Theme);
}

var AlertTemplate;

function InitAlerts() {
  // Init default alertbox
  $("#AlertBox").css("display", "block");
  AlertTemplate = $("#AlertBox")[0];
  $("#AlertBoxContainer").empty();
  $("#AlertBoxContainer").removeClass("hidden");
}

function VLMAlertSuccess(Text) {
  VLMAlert(Text, "alert-success");
}

function VLMAlertDanger(Text) {
  VLMAlert(Text, "alert-danger");
}

function VLMAlertInfo(Text) {
  VLMAlert(Text, "alert-info");
}

var AlertIntervalId = null;

function VLMAlert(Text, Style) {
  if (AlertIntervalId) {
    clearInterval(AlertIntervalId);
  }

  if (typeof Style === "undefined" || !Style) {
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

  if (AlertIntervalId) {
    clearInterval(AlertIntervalId);
  }

  AlertIntervalId = setTimeout(AutoCloseVLMAlert, 5000);
}

function AutoCloseVLMAlert() {
  $("#AlertBox").hide();
}

function GetUserConfirmation(Question, IsYesNo, CallBack) {
  $("#ConfirmDialog").modal('show');

  if (IsYesNo) {
    $("#OKBtn").hide();
    $("#CancelBtn").hide();
    $("#YesBtn").show();
    $("#NoBtn").show();
  } else {
    $("#OKBtn").show();
    $("#CancelBtn").show();
    $("#YesBtn").hide();
    $("#NoBtn").hide();
  }

  $("#ConfirmText").text(Question);
  $(".OKBtn").unbind().on("click", function () {
    $("#ConfirmDialog").modal('hide');
    CallBack(true);
  });
  $(".NOKBtn").unbind().on("click", function () {
    $("#ConfirmDialog").modal('hide');
    CallBack(false);
  });
}

function GetRaceRankingLink(RaceInfo) {
  return '<a href="/jvlm?RaceRank=' + RaceInfo.idrace + '" target="RankTab">' + RaceInfo.racename + '</a>';
}

function FillBoatPalmares(data, status, b, c, d, f) {
  var index;

  if (status === "success") {
    var rows = [];

    for (index in data.palmares) {
      if (data.palmares[index]) {
        var palmares = data.palmares[index];
        var RowsData = {
          RaceId: data.palmares[index].idrace,
          RaceName: GetRaceRankingLink(data.palmares[index]),
          Ranking: palmares.ranking.rank + " / " + palmares.ranking.racercount
        };
        rows.push(RowsData);
      }
    }

    RaceHistFt.loadRows(rows);
  }

  var str = GetLocalizedString("palmares");
  str = str.replace("%s", data.boat.name);
  $("#palmaresheaderline").text(str);
}

function ShowUserRaceHistory(BoatId) {
  $("#RaceHistory").modal("show");
  $.get("/ws/boatinfo/palmares.php?idu=" + BoatId, function (e, a, b, c, d, f) {
    FillBoatPalmares(e, a, b, c, d, f);
  });
}

function HandleShowBoatRaceHistory(e) {
  var BoatId = $(e.target).attr("boatid");

  if (BoatId) {
    ShowUserRaceHistory(BoatId);
  }
}

function HandleCreateUserResult(data, status) {
  if (status === "success" && data) {
    $(".ValidationMark").addClass("hidden");

    if (data.success) {
      $(".ValidationMark.Valid").removeClass("hidden");
      VLMAlertSuccess(GetLocalizedString('An email has been sent. Click on the link to validate.'));
      $("#InscriptForm").modal("hide");
      $("#LoginForm").modal("hide");
    } else if (data.request && data.request.errorstring) {
      VLMAlertDanger(GetLocalizedString(data.request.errorstring));
    } else {
      VLMAlertDanger(GetLocalizedString(data.error.msg));
    }

    if (data.request) {
      if (data.request.MailOK) {
        $(".ValidationMark.Email.Valid").removeClass("hidden");
      } else {
        $(".ValidationMark.Email.Invalid").removeClass("hidden");
      }

      if (data.request.PasswordOK) {
        $(".ValidationMark.Password.Valid").removeClass("hidden");
      } else {
        $(".ValidationMark.Password.Invalid").removeClass("hidden");
      }

      if (data.request.PlayerNameOK) {
        $(".ValidationMark.Pseudo.Valid").removeClass("hidden");
      } else {
        $(".ValidationMark.Pseudo.Invalid").removeClass("hidden");
      }
    } else if (data.error) {
      switch (data.error.code) {
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

function HandleCreateUser() {
  var txtplayername = $("#NewPlayerPseudo")[0].value;
  var txtemail = $("#NewPlayerEMail")[0].value;
  var txtPwd = $("#NewPlayerPassword")[0].value;
  var PostData = {
    emailid: txtemail,
    password: txtPwd,
    pseudo: txtplayername
  };
  $("#BtnCreateAccount").hide();
  $.post("/ws/playerinfo/player_create.php", PostData, function (e, status) {
    HandleCreateUserResult(e, status);
  });
}

function setModalMaxHeight(element) {
  var $element = $(element);
  var $content = $element.find('.modal-content');
  var borderWidth = $content.outerHeight() - $content.innerHeight();
  var dialogMargin = $(window).width() < 768 ? 20 : 60;
  var contentHeight = $(window).height() - (dialogMargin + borderWidth);
  var headerHeight = $element.find('.modal-header').outerHeight() || 0;
  var footerHeight = $element.find('.modal-footer').outerHeight() || 0;
  var maxHeight = contentHeight - (headerHeight + footerHeight);
  $content.css({
    'overflow': 'hidden'
  });
  $element.find('.modal-body').css({
    'max-height': maxHeight,
    'overflow-y': 'auto'
  });
} // Return a moment in UTC or Local according to VLM2 Local Pref


function GetLocalUTCTime(d, IsUTC, AsString) {
  var m = d;
  var UTCSuffix = "";

  if (!moment.isMoment(d)) {
    if (IsUTC) {
      m = moment(d).utc();
    } else {
      m = moment(d);
    }
  }

  if (VLM2Prefs.MapPrefs.UseUTC) {
    if (m.isLocal()) {
      m = m.utc();
    }

    UTCSuffix = " Z";
  } else {
    if (!m.isLocal()) {
      m = m.local();
    }
  }

  if (AsString) {
    return m.format("LLLL") + UTCSuffix;
  } else {
    return m;
  }
}
/**!
 * jQuery Progress Timer - v1.0.5 - 6/8/2015
 * http://www.thomasnorberg.com
 * Copyright (c) 2015 Thomas Norberg;
 * Licensed MIT
 */

/*
 <div class="progress">
 <div class="progress-bar progress-bar-success progress-bar-striped"
 role="progressbar" aria-valuenow="40" aria-valuemin="0"
 aria-valuemax="100" style="width: 40%">
 <span class="sr-only">40% Complete (success)</span>
 </div>
 </div>
 */


if (typeof jQuery === "undefined") {
  throw new Error("jQuery progress timer requires jQuery");
}
/*!
 * jQuery lightweight plugin boilerplate
 * Original author: @ajpiano
 * Further changes, comments: @addyosmani
 * Licensed under the MIT license
 */


(function ($, window, document, undefined) {
  "use strict"; // undefined is used here as the undefined global
  // variable in ECMAScript 3 and is mutable (i.e. it can
  // be changed by someone else). undefined isn't really
  // being passed in so we can ensure that its value is
  // truly undefined. In ES5, undefined can no longer be
  // modified.
  // window and document are passed through as local
  // variables rather than as globals, because this (slightly)
  // quickens the resolution process and can be more
  // efficiently minified (especially when both are
  // regularly referenced in your plugin).
  // Create the defaults once

  var pluginName = "progressTimer",
      defaults = {
    //total number of seconds
    timeLimit: 60,
    //seconds remaining triggering switch to warning color
    warningThreshold: 5,
    //invoked once the timer expires
    onFinish: function onFinish() {},
    //bootstrap progress bar style at the beginning of the timer
    baseStyle: "",
    //bootstrap progress bar style in the warning phase
    warningStyle: "progress-bar-danger",
    //bootstrap progress bar style at completion of timer
    completeStyle: "progress-bar-success",
    //show html on progress bar div area
    showHtmlSpan: true,
    //set the error text when error occurs
    errorText: "ERROR!",
    //set the success text when succes occurs
    successText: "100%"
  }; // The actual plugin constructor

  var Plugin = function Plugin(element, options) {
    this.element = element;
    this.$elem = $(element);
    this.options = $.extend({}, defaults, options);
    this._defaults = defaults;
    this._name = pluginName;
    this.metadata = this.$elem.data("plugin-options");
    this.init();
  };

  Plugin.prototype.constructor = Plugin;

  Plugin.prototype.init = function () {
    var t = this;
    $(t.element).empty();
    t.span = $("<span/>");
    t.barContainer = $("<div>").addClass("progress");
    t.bar = $("<div>").addClass("progress-bar active progress-bar-striped").addClass(t.options.baseStyle).attr("role", "progressbar").attr("aria-valuenow", "0").attr("aria-valuemin", "0").attr("aria-valuemax", t.options.timeLimit);
    t.span.appendTo(t.bar);

    if (!t.options.showHtmlSpan) {
      t.span.addClass("sr-only");
    }

    t.bar.appendTo(t.barContainer);
    t.barContainer.appendTo(t.element);
    t.start = new Date();
    t.limit = t.options.timeLimit * 1000;
    t.warningThreshold = t.options.warningThreshold * 1000;
    t.interval = window.setInterval(function () {
      t._run.call(t);
    }, 250);
    t.bar.data("progress-interval", t.interval);
    return true;
  };

  Plugin.prototype.destroy = function () {
    this.$elem.removeData();
  };

  Plugin.prototype._run = function () {
    var t = this;
    var elapsed = new Date() - t.start,
        width = elapsed / t.limit * 100;
    t.bar.attr("aria-valuenow", width);
    t.bar.width(width + "%");
    var percentage = width.toFixed(2);

    if (percentage >= 100) {
      percentage = 100;
    }

    if (t.options.showHtmlSpan) {
      t.span.html(percentage + "%");
    }

    if (elapsed >= t.warningThreshold) {
      t.bar.removeClass(this.options.baseStyle).removeClass(this.options.completeStyle).addClass(this.options.warningStyle);
    }

    if (elapsed >= t.limit) {
      t.complete.call(t);
    }

    return true;
  };

  Plugin.prototype.removeInterval = function () {
    var t = this,
        bar = $(".progress-bar", t.element);

    if (typeof bar.data("progress-interval") !== "undefined") {
      var interval = bar.data("progress-interval");
      window.clearInterval(interval);
    }

    return bar;
  };

  Plugin.prototype.complete = function () {
    var t = this,
        bar = t.removeInterval.call(t),
        args = arguments;

    if (args.length !== 0 && _typeof(args[0]) === "object") {
      t.options = $.extend({}, t.options, args[0]);
    }

    bar.removeClass(t.options.baseStyle).removeClass(t.options.warningStyle).addClass(t.options.completeStyle);
    bar.width("100%");

    if (t.options.showHtmlSpan) {
      $("span", bar).html(t.options.successText);
    }

    bar.attr("aria-valuenow", 100);
    setTimeout(function () {
      t.options.onFinish.call(bar);
    }, 500);
    t.destroy.call(t);
  };

  Plugin.prototype.error = function () {
    var t = this,
        bar = t.removeInterval.call(t),
        args = arguments;

    if (args.length !== 0 && _typeof(args[0]) === "object") {
      t.options = $.extend({}, t.options, args[0]);
    }

    bar.removeClass(t.options.baseStyle).addClass(t.options.warningStyle);
    bar.width("100%");

    if (t.options.showHtmlSpan) {
      $("span", bar).html(t.options.errorText);
    }

    bar.attr("aria-valuenow", 100);
    setTimeout(function () {
      t.options.onFinish.call(bar);
    }, 500);
    t.destroy.call(t);
  }; // A really lightweight plugin wrapper around the constructor,
  // preventing against multiple instantiations


  $.fn[pluginName] = function (options) {
    var args = arguments;

    if (options === undefined || _typeof(options) === "object") {
      // Creates a new plugin instance
      return this.each(function () {
        if (!$.data(this, "plugin_" + pluginName)) {
          $.data(this, "plugin_" + pluginName, new Plugin(this, options));
        }
      });
    } else if (typeof options === "string" && options[0] !== "_" && options !== "init") {
      // Call a public plugin method (not starting with an underscore) and different
      // from the "init" one
      if (Array.prototype.slice.call(args, 1).length === 0 && $.inArray(options, $.fn[pluginName].getters) !== -1) {
        // If the user does not pass any arguments and the method allows to
        // work as a getter then break the chainability so we can return a value
        // instead the element reference.
        var instance = $.data(this[0], "plugin_" + pluginName);
        return instance[options].apply(instance, Array.prototype.slice.call(args, 1));
      } else {
        // Invoke the specified method on each selected element
        return this.each(function () {
          var instance = $.data(this, "plugin_" + pluginName);

          if (instance instanceof Plugin && typeof instance[options] === "function") {
            instance[options].apply(instance, Array.prototype.slice.call(args, 1));
          }
        });
      }
    }
  };

  $.fn[pluginName].getters = ["complete", "error"];
})(jQuery, window, document, undefined);
/*!
 * jQuery UI Touch Punch 0.2.3
 *
 * Copyright 2011–2014, Dave Furfero
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Depends:
 *  jquery.ui.widget.js
 *  jquery.ui.mouse.js
 */


(function ($) {
  // Detect touch support
  $.support.touch = 'ontouchend' in document; // Ignore browsers without touch support

  if (!$.support.touch) {
    return;
  }

  var mouseProto = $.ui.mouse.prototype,
      _mouseInit = mouseProto._mouseInit,
      _mouseDestroy = mouseProto._mouseDestroy,
      touchHandled;
  /**
   * Simulate a mouse event based on a corresponding touch event
   * @param {Object} event A touch event
   * @param {String} simulatedType The corresponding mouse event
   */

  function simulateMouseEvent(event, simulatedType) {
    // Ignore multi-touch events
    if (event.originalEvent.touches.length > 1) {
      return;
    }

    event.preventDefault();
    var touch = event.originalEvent.changedTouches[0],
        simulatedEvent = document.createEvent('MouseEvents'); // Initialize the simulated mouse event using the touch event's coordinates

    simulatedEvent.initMouseEvent(simulatedType, // type
    true, // bubbles                    
    true, // cancelable                 
    window, // view                       
    1, // detail                     
    touch.screenX, // screenX                    
    touch.screenY, // screenY                    
    touch.clientX, // clientX                    
    touch.clientY, // clientY                    
    false, // ctrlKey                    
    false, // altKey                     
    false, // shiftKey                   
    false, // metaKey                    
    0, // button                     
    null // relatedTarget              
    ); // Dispatch the simulated event to the target element

    event.target.dispatchEvent(simulatedEvent);
  }
  /**
   * Handle the jQuery UI widget's touchstart events
   * @param {Object} event The widget element's touchstart event
   */


  mouseProto._touchStart = function (event) {
    var self = this; // Ignore the event if another widget is already being handled

    if (touchHandled || !self._mouseCapture(event.originalEvent.changedTouches[0])) {
      return;
    } // Set the flag to prevent other widgets from inheriting the touch event


    touchHandled = true; // Track movement to determine if interaction was a click

    self._touchMoved = false; // Simulate the mouseover event

    simulateMouseEvent(event, 'mouseover'); // Simulate the mousemove event

    simulateMouseEvent(event, 'mousemove'); // Simulate the mousedown event

    simulateMouseEvent(event, 'mousedown');
  };
  /**
   * Handle the jQuery UI widget's touchmove events
   * @param {Object} event The document's touchmove event
   */


  mouseProto._touchMove = function (event) {
    // Ignore event if not handled
    if (!touchHandled) {
      return;
    } // Interaction was not a click


    this._touchMoved = true; // Simulate the mousemove event

    simulateMouseEvent(event, 'mousemove');
  };
  /**
   * Handle the jQuery UI widget's touchend events
   * @param {Object} event The document's touchend event
   */


  mouseProto._touchEnd = function (event) {
    // Ignore event if not handled
    if (!touchHandled) {
      return;
    } // Simulate the mouseup event


    simulateMouseEvent(event, 'mouseup'); // Simulate the mouseout event

    simulateMouseEvent(event, 'mouseout'); // If the touch interaction did not move, it should trigger a click

    if (!this._touchMoved) {
      // Simulate the click event
      simulateMouseEvent(event, 'click');
    } // Unset the flag to allow other widgets to inherit the touch event


    touchHandled = false;
  };
  /**
   * A duck punch of the $.ui.mouse _mouseInit method to support touch events.
   * This method extends the widget with bound touch event handlers that
   * translate touch events to mouse events and pass them to the widget's
   * original mouse event handling methods.
   */


  mouseProto._mouseInit = function () {
    var self = this; // Delegate the touch handlers to the widget's element

    self.element.bind({
      touchstart: $.proxy(self, '_touchStart'),
      touchmove: $.proxy(self, '_touchMove'),
      touchend: $.proxy(self, '_touchEnd')
    }); // Call the original $.ui.mouse init method

    _mouseInit.call(self);
  };
  /**
   * Remove the touch event handlers
   */


  mouseProto._mouseDestroy = function () {
    var self = this; // Delegate the touch handlers to the widget's element

    self.element.unbind({
      touchstart: $.proxy(self, '_touchStart'),
      touchmove: $.proxy(self, '_touchMove'),
      touchend: $.proxy(self, '_touchEnd')
    }); // Call the original $.ui.mouse destroy method

    _mouseDestroy.call(self);
  };
})(jQuery);

var BuoyMarker = L.Icon.extend({
  options: {
    iconSize: [36, 72],
    iconAnchor: [18, 36],
    popupAnchor: [0, -36]
  }
});
var TrackWPMarker = L.Icon.extend({
  options: {
    iconSize: [48, 48],
    iconAnchor: [24, 24],
    iconUrl: 'images/WP_Marker.gif'
  }
});
var BOAT_MARKET_SIZE = 48;
var BOAT_EST_MARKET_SIZE = 24;
var BoatMarker = L.Icon.extend({
  options: {
    iconSize: [BOAT_MARKET_SIZE, BOAT_MARKET_SIZE],
    iconAnchor: [BOAT_MARKET_SIZE / 2, BOAT_MARKET_SIZE / 2],
    iconUrl: 'images/target.png',
    rotationOrigin: [BOAT_MARKET_SIZE / 2, BOAT_MARKET_SIZE / 2]
  }
});
var BoatEstMarker = L.Icon.extend({
  options: {
    iconSize: [BOAT_EST_MARKET_SIZE, BOAT_EST_MARKET_SIZE],
    iconAnchor: [BOAT_EST_MARKET_SIZE / 2, BOAT_EST_MARKET_SIZE / 2],
    iconUrl: 'images/target.png',
    rotationOrigin: [BOAT_EST_MARKET_SIZE / 2, BOAT_EST_MARKET_SIZE / 2]
  }
});
var IceGateMarker = L.Icon.extend({
  options: {
    iconSize: [48, 48],
    iconAnchor: [24, -18],
    iconUrl: 'images/icegate.png'
  }
});
var GateDirMarker = L.Icon.extend({
  options: {
    iconSize: [48, 48],
    iconAnchor: [24, 24],
    rotationOrigin: [24, 24]
  }
});

function GetBuoyMarker(Buoy1) {
  var RetMark = null;

  if (Buoy1) {
    RetMark = new BuoyMarker({
      iconUrl: 'images/Buoy1.png'
    });
  } else {
    RetMark = new BuoyMarker({
      iconUrl: 'images/Buoy2.png',
      color: "red"
    });
  }

  RetMark.IsCWBuoy = Buoy1;
  return RetMark;
}

function GetBoatMarker(idboat) {
  var ret = new BoatMarker();
  ret.MarkerOppId = idboat;
  return ret;
}

function GetBoatEstimateMarker() {
  var ret = new BoatEstMarker();
  return ret;
}

function GetTrackWPMarker() {
  return new TrackWPMarker();
}

function GetGateTypeMarker(Marker, IsIceGate) {
  if (IsIceGate) {
    return IceGateMarker;
  } else {
    var ret = new GateDirMarker({
      iconUrl: "images/" + Marker
    });
    return ret;
  }
}

function GetOpponentMarker(OppData) {
  /* let OppData = {
    "name": Opponent.idusers,
    "Coords": Opp_Coords.toString(),
    "type": 'opponent',
    "idboat": Opponent.idusers,
    "rank": Opponent.rank,
    "Last1h": Opponent.last1h,
    "Last3h": Opponent.last3h,
    "Last24h": Opponent.last24h,
    "IsTeam": (Opponent.country == Boat.VLMInfo.CNT) ? "team" : "",
    "IsFriend": (isFriend ? ZFactor * 2 : ZFactor),
    "color": Opponent.color
  }; */
  var ret = new L.Icon({
    iconUrl: "images/opponent" + OppData.IsTeam + ".png",
    iconAnchor: [OppData.IsFriend / 2, OppData.IsFriend / 2],
    iconSize: [OppData.IsFriend, OppData.IsFriend]
  });
  ret.MarkerOppId = OppData.idboat;
  return ret;
}

function ClearCurrentMapMarker(Boat) {
  if (Boat && Boat.RaceMapFeatures) {
    if (Boat.RaceMapFeatures.OppPopup && Boat.RaceMapFeatures.OppPopup.PrevOpp) {
      Boat.RaceMapFeatures.OppPopup.PrevOpp.unbindPopup(Boat.RaceMapFeatures.OppPopup);
    }

    RemoveFromMap(Boat.RaceMapFeatures);
  }
}

function EnsureMarkersVisible(Boat) {
  if (Boat) {
    RestoreMarkersOnMap(Boat.RaceMapFeatures);
  }
}

function RestoreMarkersOnMap(Feat) {
  if (!Feat || typeof Feat === "function") {
    return;
  }

  if (Array.isArray(Feat)) {
    for (var _index20 in Feat) {
      RestoreMarkersOnMap(Feat[_index20]);
    }
  } else if (_typeof(Feat) === "object" && typeof Feat._leaflet_id === "undefined") {
    for (var member in Feat) {
      RestoreMarkersOnMap(Feat[member]);
    }
  } else if (Feat._leaflet_id && !Feat._map) {
    Feat.addTo(map);
  }
}

function RemoveFromMap(Feat) {
  if (!Feat || typeof Feat === "function") {
    return;
  }

  if (Array.isArray(Feat)) {
    for (var _index21 in Feat) {
      RemoveFromMap(Feat[_index21]);
    }
  } else if (_typeof(Feat) === "object" && typeof Feat._leaflet_id === "undefined") {
    for (var member in Feat) {
      RemoveFromMap(Feat[member]);
    }
  } else if (Feat._leaflet_id) {
    Feat.removeFrom(map);
  }
} // LeafletMarkerRotation from 
//https://github.com/bbecquet/Leaflet.RotatedMarker/blob/master/leaflet.rotatedMarker.js


(function () {
  // save these original methods before they are overwritten
  var proto_initIcon = L.Marker.prototype._initIcon;
  var proto_setPos = L.Marker.prototype._setPos;
  var oldIE = L.DomUtil.TRANSFORM === 'msTransform';
  L.Marker.addInitHook(function () {
    var iconOptions = this.options.icon && this.options.icon.options;
    var iconAnchor = iconOptions && this.options.icon.options.iconAnchor;

    if (iconAnchor) {
      iconAnchor = iconAnchor[0] + 'px ' + iconAnchor[1] + 'px';
    }

    this.options.rotationOrigin = this.options.rotationOrigin || iconAnchor || 'center bottom';
    this.options.rotationAngle = this.options.rotationAngle || 0; // Ensure marker keeps rotated during dragging

    this.on('drag', function (e) {
      e.target._applyRotation();
    });
  });
  L.Marker.include({
    _initIcon: function _initIcon() {
      proto_initIcon.call(this);
    },
    _setPos: function _setPos(pos) {
      proto_setPos.call(this, pos);

      this._applyRotation();
    },
    _applyRotation: function _applyRotation() {
      if (this.options.rotationAngle) {
        this._icon.style[L.DomUtil.TRANSFORM + 'Origin'] = this.options.rotationOrigin;

        if (oldIE) {
          // for IE 9, use the 2D rotation
          this._icon.style[L.DomUtil.TRANSFORM] = 'rotate(' + this.options.rotationAngle + 'deg)';
        } else {
          // for modern browsers, prefer the 3D accelerated version
          this._icon.style[L.DomUtil.TRANSFORM] += ' rotateZ(' + this.options.rotationAngle + 'deg)';
        }
      }
    },
    setRotationAngle: function setRotationAngle(angle) {
      this.options.rotationAngle = angle;
      this.update();
      return this;
    },
    setRotationOrigin: function setRotationOrigin(origin) {
      this.options.rotationOrigin = origin;
      this.update();
      return this;
    }
  });
})();

var _LocaleDict;

var _EnDict;

var _CurLocale = 'en'; // Default to english unless otherwise posted

function LocalizeString() {
  //console.log("Localizing...");
  LocalizeItem($("[I18n]").get()); // Handle flag clicks

  $(".LngFlag").click(function (event, ui) {
    OnLangFlagClick($(this).attr('lang'));
    UpdateLngDropDown();
  });
  return true;
}

function OnLangFlagClick(Lang) {
  InitLocale(Lang);
}

function LocalizeItem(Elements) {
  try {
    var child; //console.log(Elements);

    for (child in Elements) {
      var el = Elements[child];
      var Attr = el.attributes.I18n.value;

      if (typeof _LocaleDict != "undefined") {
        el.innerHTML = GetLocalizedString(Attr);
      }
    }
  } finally {}

  return true;
}

function InitLocale(Lang) {
  var query = "/ws/serverinfo/translation.php";

  if (Lang) {
    query += "?lang=" + Lang;
  }

  $.get(query, function (result) {
    if (result.success == true) {
      _CurLocale = result.request.lang;
      _LocaleDict = result.strings;
      moment.locale(_CurLocale);
      LocalizeString();
      UpdateLngDropDown();
    } else {
      alert("Localization string table load failure....");
    }
  });

  if (typeof _EnDict == 'undefined') {
    // Load english dictionnary as fall back on 1st call
    $.get("/ws/serverinfo/translation.php?lang=en", function (result) {
      if (result.success == true) {
        _EnDict = result.strings;
      } else {
        alert("Fallback localization string table load failure....");
      }
    });
  }
}

function HTMLDecode(String) {
  var txt = document.createElement("textarea");
  txt.innerHTML = String;
  var RetString = txt.value;
  var EOLSigns = ["\n\r", "\r\n", "\n", "\r"];

  for (var _index22 in EOLSigns) {
    while (EOLSigns[_index22] && RetString.indexOf(EOLSigns[_index22]) !== -1) {
      RetString = RetString.replace(EOLSigns[_index22], "<br>");
    }
  }

  return RetString;
}

function GetLocalizedString(StringId) {
  var RetString = "";

  if (typeof _LocaleDict !== "undefined" && _LocaleDict && StringId in _LocaleDict) {
    RetString = HTMLDecode(_LocaleDict[StringId]);
  } else if (typeof _EnDict !== "undefined" && _EnDict && StringId in _EnDict) {
    RetString = HTMLDecode(_EnDict[StringId]);
  } else {
    RetString = StringId;
  }

  return RetString;
}

function GetCurrentLocale() {
  return _CurLocale;
}

var VLMMercatorTransform = new MercatorTransform();

function MercatorTransform() {
  this.Width = 10000;
  this.Height = 10000;
  this.LonOffset = 0;
  this.LatOffset = 0;
  this.Scale = 10000 / 180;

  this.LonToMapX = function (Lon) {
    return this.Width / 2.0 + (Lon - this.LonOffset) * this.Scale;
  };

  this.LatToMapY = function (Lat) {
    Lat = Deg2Rad(Lat);
    Lat = Math.log(Math.tan(Lat) + 1 / Math.cos(Lat));
    Lat = Rad2Deg(Lat);
    return this.Height / 2.0 - (Lat - this.LatOffset) * this.Scale;
  };

  this.SegmentsIntersect = function (Seg1, Seg2) {
    var Ax = this.LonToMapX(Seg1.P1.Lon.Value);
    var Ay = this.LatToMapY(Seg1.P1.Lat.Value);
    var Bx = this.LonToMapX(Seg1.P2.Lon.Value);
    var By = this.LatToMapY(Seg1.P2.Lat.Value);
    var Cx = this.LonToMapX(Seg2.P1.Lon.Value);
    var Cy = this.LatToMapY(Seg2.P1.Lat.Value);
    var Dx = this.LonToMapX(Seg2.P2.Lon.Value);
    var Dy = this.LatToMapY(Seg2.P2.Lat.Value); //  Fail if either line is undefined.

    if (Seg1.P1.Lon.Value === Seg1.P2.Lon.Value && Seg1.P1.Lat.Value === Seg1.P2.Lat.Value || Seg2.P1.Lon.Value === Seg2.P2.Lon.Value && Seg2.P1.Lat.Value === Seg2.P2.Lat.Value) {
      return false;
    } // (1) Translate the system so that point A is on the origin.


    Bx -= Ax;
    By -= Ay;
    Cx -= Ax;
    Cy -= Ay;
    Dx -= Ax;
    Dy -= Ay;
    Ax = 0;
    Ay = 0; // Discover the length of segment A-B.

    var DistAB = Math.sqrt(Bx * Bx + By * By); // (2) Rotate the system so that point B is on the positive X axis.

    var theCos = Bx / DistAB;
    var theSin = By / DistAB;
    var newX = Cx * theCos + Cy * theSin;
    Cy = Cy * theCos - Cx * theSin;
    Cx = newX;
    newX = Dx * theCos + Dy * theSin;
    Dy = Dy * theCos - Dx * theSin;
    Dx = newX; // Fail if the lines are parallel.

    if (Cy === Dy) {
      return false;
    } //  (3) Discover the position of the intersection point along line A-B.


    var ABpos = Dx + (Cx - Dx) * Dy / (Dy - Cy);
    var Ratio = ABpos / DistAB;

    if (Ratio >= 0 && Ratio <= 1) {
      // Possible Success
      // Check other segment ratio
      // Get Intersect coords
      var Ix = Ax + ABpos;
      var Iy = Ay;
      var Ratio2;

      if (Dx - Cx) {
        // Seg is not vertical
        Ratio2 = (Ix - Cx) / (Dx - Cx);
      } else if (Dy - Cy) {
        // Seg is vertical
        Ratio2 = (Iy - Cy) / (Dy - Cy);
      } else {
        // No segment !!
        return false;
      }

      if (Ratio2 >= 0 && Ratio2 <= 1) {
        return true;
      } else {
        return false;
      }
    } else {
      // Segments do not intersect
      return false;
    }
  };
  /*
  Public Function CanvasToLat(ByVal C As Double) Implements IMapTransform.CanvasToLat
    Debug.Assert(Scale <> 0)
    var Ret = (ActualHeight / 2 - C) / Scale + LatOffset
    Ret = Ret / 180 * PI
    return (Math.Atan(Math.Sinh(Ret)) / PI * 180)
  End Function
   Public Function CanvasToLon(ByVal V As Double) Implements IMapTransform.CanvasToLon
    Debug.Assert(Scale <> 0)
    var Ret = ((V - ActualWidth / 2) / Scale + LonOffset) 
    return Ret
  End Function
  */

} //
// Module for handling boat polars
//


var PolarManagerClass = function PolarManagerClass() {
  _classCallCheck(this, PolarManagerClass);

  this.Polars = [];
  this.PolarLoaderQueue = {};

  this.Init = function () {
    this.Polars = []; // Bg load the list of boats with a polar in VLM

    $.get("/ws/polarlist.php", function (Data) {
      //Parse WS data, build polarlist and URL
      // Build list of boat for lazy loading
      for (var _index23 in Data.list) {
        PolarsManager.Polars["boat_" + Data.list[_index23]] = null;
      }
    });
  };

  this.GetBoatSpeed = function (PolarName, WindSpeed, WindBearing, BoatBearing) {
    if (!(PolarName in this.Polars)) {
      return NaN;
    }

    if (!this.Polars[PolarName]) {
      // Polar not loaded yet, load it
      // TODO Remove dead code
      //$.get("/Polaires/"+ PolarName +".csv",this.HandlePolarLoaded.bind(this, PolarName,null, null));
      this.LoadPolar(PolarName, null, null);
      return NaN;
    } else {
      var alpha = WindAngle(BoatBearing, WindBearing);
      var Speed = GetPolarAngleSpeed(this.Polars[PolarName], alpha, WindSpeed);
      return Speed;
    }
  };

  this.HandlePolarLoaded = function (PolarName, Boat, data) {
    var polar = $.csv.toArrays(data, {
      separator: ";"
    }); // Convert back all values to floats.

    for (var row in polar) {
      if (polar[row]) {
        for (var col in polar[row]) {
          if (polar[row][col]) {
            polar[row][col] = parseFloat(polar[row][col]);
          }
        }
      }
    }

    PolarsManager.Polars[PolarName] = {};
    PolarsManager.Polars[PolarName].SpeedPolar = polar;
    PolarsManager.Polars[PolarName].WindLookup = [];
    PolarsManager.Polars[PolarName].AngleLookup = [];

    for (var _index24 in this.PolarLoaderQueue[PolarName].callbacks) {
      var callback = this.PolarLoaderQueue[PolarName].callbacks[_index24];

      if (callback && Boat) {
        callback(Boat);
      } else if (callback) {
        callback();
      }
    }

    this.PolarLoaderQueue[PolarName] = null;
  };

  this.GetPolarLine = function (PolarName, WindSpeed, callback, boat, Step) {
    if (!Step) {
      Step = 5;
    }

    if (typeof this.Polars[PolarName] === "undefined") {
      alert("Unexpected polarname : " + PolarName);
      return null;
    }

    if (this.Polars[PolarName] === null) {
      // Polar not loaded yet, load it
      this.LoadPolar(PolarName, callback, boat);
    } else {
      var RetPolar = [];
      var alpha;
      var MaxSpeed = 0; // Loop to get speedvalue per angle

      for (alpha = 0; alpha <= 180; alpha += Step) {
        var Speed = GetPolarAngleSpeed(this.Polars[PolarName], alpha, WindSpeed);

        if (MaxSpeed < Speed) {
          MaxSpeed = Speed;
        }

        RetPolar.push(Speed);
      } // Scale Polar to 1


      for (var _index25 in RetPolar) {
        if (RetPolar[_index25]) {
          RetPolar[_index25] /= MaxSpeed;
        }
      }

      return RetPolar;
    }
  };

  var DebugVMG = 0;

  this.GetVMGCourse = function (Polar, WindSpeed, WindBearing, StartPos, DestPos) {
    var OrthoBearing = StartPos.GetOrthoCourse(DestPos);
    var BestAngle = 0;
    var BestVMG = -1e10;

    for (var dir = -1; dir <= 1; dir += 2) {
      for (var angle = 0.0; angle <= 90; angle += 0.1) {
        var CurSpeed = this.GetBoatSpeed(Polar, WindSpeed, WindBearing, OrthoBearing + angle * dir);
        var CurVMG = CurSpeed * Math.cos(Deg2Rad(angle));

        if (DebugVMG) {
          console.log("VMG " + RoundPow((OrthoBearing + angle * dir + 360.0) % 360.0, 3) + " " + RoundPow(CurSpeed, 3) + " " + RoundPow(CurVMG, 3) + " " + RoundPow(BestVMG, 3) + " " + (CurVMG >= BestVMG ? "BEST" : ""));
        }

        if (CurVMG >= BestVMG) {
          BestVMG = CurVMG;
          BestAngle = OrthoBearing + angle * dir;
        }
      }
    }

    DebugVMG = 0;
    return BestAngle;
  };

  this.GetVBVMGCourse = function (Polar, WindSpeed, WindBearing, StartPos, DestPos) {
    var Dist = StartPos.GetOrthoDist(DestPos);
    var CapOrtho = StartPos.GetOrthoCourse(DestPos);
    var b_Alpha = 0;
    var b_Beta = 0;
    var SpeedAlpha = 0;
    var SpeedBeta = 0;
    var t_min = 0;
    var ISigne = 1;
    var Speed = this.GetBoatSpeed(Polar, WindSpeed, WindBearing, CapOrtho);

    if (Speed > 0) {
      t_min = Dist / Speed;
    } else {
      t_min = 365 * 24;
    }

    var angle = WindBearing - CapOrtho;

    if (angle < -90) {
      angle += 360;
    } else if (angle > 90) {
      angle -= 360;
    }

    if (angle > 0) {
      ISigne = -1;
    } else {
      ISigne = 1;
    }

    for (var i = 1; i <= 90; i++) {
      var alpha = i * Math.PI / 180;
      var TanAlpha = Math.tan(alpha);
      var D1HypotRatio = Math.sqrt(1 + TanAlpha * TanAlpha);
      var SpeedT1 = this.GetBoatSpeed(Polar, WindSpeed, WindBearing, CapOrtho - i * ISigne);

      if (isNaN(SpeedT1)) {
        throw "Nan SpeedT1 exception";
      }

      if (SpeedT1 > 0) {
        for (var j = -89; j <= 0; j++) {
          var beta = j * Math.PI / 180;
          var D1 = Dist * (Math.tan(-beta) / (TanAlpha + Math.tan(-beta)));
          var L1 = D1 * D1HypotRatio;
          var T1 = L1 / SpeedT1;

          if (T1 < 0 || T1 > t_min) {
            continue;
          }

          var D2 = Dist - D1;
          var SpeedT2 = this.GetBoatSpeed(Polar, WindSpeed, WindBearing, CapOrtho - j * ISigne);

          if (isNaN(SpeedT2)) {
            throw "Nan SpeedT2 exception";
          }

          if (SpeedT2 <= 0) {
            continue;
          }

          var TanBeta = Math.tan(-beta);
          var L2 = D2 * Math.sqrt(1 + TanBeta * TanBeta);
          var T2 = L2 / SpeedT2;
          var T = T1 + T2;

          if (T < t_min) {
            t_min = T;
            b_Alpha = i;
            b_Beta = j; // b_L1 = L1;
            // b_L2 = L2;
            // b_T1 = T1;
            // b_T2 = T2;

            SpeedAlpha = SpeedT1;
            SpeedBeta = SpeedT2;
          }
        }
      }
    }

    var VMGAlpha = SpeedAlpha * Math.cos(Deg2Rad(b_Alpha));
    var VMGBeta = SpeedBeta * Math.cos(Deg2Rad(b_Beta));

    if (isNaN(VMGAlpha) || isNaN(VMGBeta)) {
      throw "NaN VMG found";
    }

    if (VMGAlpha > VMGBeta) {
      return CapOrtho - b_Alpha * ISigne;
    } else {
      return CapOrtho - b_Beta * ISigne;
    }
  };

  this.GetPolarMaxSpeed = function (PolarName, WindSpeed) {
    // Assume polar is already loaded
    if (!this.Polars[PolarName]) {
      return null;
    }

    var Alpha;
    var MaxSpeed = 0;

    for (Alpha = 0; Alpha <= 180; Alpha += 1) {
      var S = GetPolarAngleSpeed(this.Polars[PolarName], Alpha, WindSpeed);

      if (S > MaxSpeed) {
        MaxSpeed = S;
      }
    }

    return MaxSpeed;
  };

  this.LoadPolar = function (PolarName, callback, boat) {
    if (this.PolarLoaderQueue[PolarName]) {
      //Polar load isPending, add callback to list
      if (callback) {
        this.PolarLoaderQueue[PolarName].callbacks.push(callback);
      }
    } else {
      this.PolarLoaderQueue[PolarName] = {};
      this.PolarLoaderQueue[PolarName].callbacks = [];

      if (callback) {
        this.PolarLoaderQueue[PolarName].callbacks.push(callback);
      }

      $.get("/Polaires/" + PolarName + ".csv", this.HandlePolarLoaded.bind(this, PolarName, boat));
    }
  };
}; // Global Polar Manager


var PolarsManager = new PolarManagerClass(); // Returns the speed at given angle for a polar

function GetPolarAngleSpeed(PolarObject, Alpha, WindSpeed) {
  var SpeedCol1;
  var SpeedCol2;
  var AlphaRow1;
  var AlphaRow2; // Loop and index index <= Speed

  var Polar = PolarObject.SpeedPolar;
  var IntWind = Math.floor(WindSpeed);

  if (typeof PolarObject.WindLookup !== "undefined" && IntWind in PolarObject.WindLookup) {
    SpeedCol1 = PolarObject.WindLookup[IntWind];
  } else {
    for (var _index26 in Polar[0]) {
      if (_index26 > 0 && Polar[0][_index26] > WindSpeed) {
        break;
      }

      PolarObject.WindLookup[IntWind] = Math.floor(_index26);
      SpeedCol1 = Math.floor(_index26);
    }
  }

  SpeedCol2 = SpeedCol1 < Polar[0].length - 1 ? SpeedCol1 + 1 : SpeedCol1; // loop Rows to find angle <= alpha

  while (Alpha < 0) {
    Alpha += 360.0;
  }

  if (Alpha >= 360) {
    Alpha %= 360.0;
  }

  if (Alpha > 180.0) {
    Alpha = 360.0 - Alpha;
  }

  var IntAlpha = Math.floor(Alpha);

  if (typeof PolarObject.AngleLookup !== "undefined" && IntAlpha in PolarObject.AngleLookup) {
    AlphaRow1 = PolarObject.AngleLookup[IntAlpha];
  } else {
    for (var _index27 in Polar) {
      if (_index27 > 0 && Polar[_index27][0] > Alpha) {
        break;
      }

      PolarObject.AngleLookup[IntAlpha] = Math.floor(_index27);
      AlphaRow1 = Math.floor(_index27);
    }
  }

  AlphaRow2 = AlphaRow1 < Polar.length - 1 ? AlphaRow1 + 1 : AlphaRow1;
  var v1 = GetAvgValue(WindSpeed, Polar[0][SpeedCol1], Polar[0][SpeedCol2], Polar[AlphaRow1][SpeedCol1], Polar[AlphaRow1][SpeedCol2]);
  var v2 = GetAvgValue(WindSpeed, Polar[0][SpeedCol1], Polar[0][SpeedCol2], Polar[AlphaRow2][SpeedCol1], Polar[AlphaRow2][SpeedCol2]);
  var RetValue = GetAvgValue(Alpha, Polar[AlphaRow1][0], Polar[AlphaRow2][0], v1, v2);

  if (isNaN(RetValue)) {
    // Start over for debugging (will crash the stack!!)
    // GetPolarAngleSpeed  (PolarObject,Alpha, WindSpeed)
    throw "GetAvgValue was NaN";
  }

  return RetValue;
}

function WindAngle(BoatBearing, WindBearing) {
  var I = 0;

  if (BoatBearing >= WindBearing) {
    if (BoatBearing - WindBearing <= 180.0) {
      I = BoatBearing - WindBearing;
    } else {
      I = 360 - BoatBearing + WindBearing;
    }
  } else {
    if (WindBearing - BoatBearing <= 180.0) {
      I = WindBearing - BoatBearing;
    } else {
      I = 360 - WindBearing + BoatBearing;
    }
  }

  return I;
} // Return Linear interpolated y for x on line (Rx1,Ry1)(Rx2,Ry2)


function GetAvgValue(x, Rx1, Rx2, Ry1, Ry2) {
  // Cast all params as numbers

  /*x=parseFloat(x);
  Rx1=parseFloat(Rx1);
  Rx2=parseFloat(Rx2);
  Ry1=parseFloat(Ry1);
  Ry2=parseFloat(Ry2);
  */
  if (x === Rx1 || Rx1 === Rx2 || Ry1 === Ry2) {
    // Trivial & corner cases
    return Ry1;
  } else {
    return Ry1 + (x - Rx1) / (Rx2 - Rx1) * (Ry2 - Ry1);
  }
} //
// Position class
//
// Formating, conversion, and geo computation
//
//


var POS_FORMAT_DEFAULT = 0; // Earth radius for all calculation of distance in Naut. Miles

var EARTH_RADIUS = 3443.84;
var VLM_DIST_ORTHO = 1; // Fmod for javascript from https://gist.github.com/wteuber/6241786

Math.fmod = function (a, b) {
  return Number((a - Math.floor(a / b) * b).toPrecision(8));
};

function Deg2Rad(v) {
  return v / 180.0 * Math.PI;
}

function Rad2Deg(v) {
  return v / Math.PI * 180.0;
}

function RoundPow(v, P) {
  if (typeof P !== 'undefined') {
    var Div = Math.pow(10, P);
    return Math.round(v * Div) / Div;
  } else {
    return v;
  }
}

function NormalizeLongitudeDeg(Lon) {
  if (Lon < -180) {
    Lon += 360;
  } else if (Lon > 180) {
    Lon -= 360;
  }

  return Lon;
} // Constructor


function VLMPosition(lon, lat, format) {
  if (typeof format == 'undefined' || format == POS_FORMAT_DEFAULT) {
    // Default constructor, lon and lat in degs flaoting format
    this.Lon = new Coords(lon, 1);
    this.Lat = new Coords(lat, 0);
  } // Default string formating


  this.toString = function (Raw) {
    return this.Lat.toString(Raw) + " " + this.Lon.toString(Raw);
  };

  this.GetEuclidianDist2 = function (P) {
    var dLat = (this.Lat.Value - P.Lat.Value) % 90;
    var dLon = (this.Lon.Value - P.Lon.Value) % 180;
    return dLat * dLat + dLon * dLon;
  }; // function GetLoxoDist
  // Returns the loxodromic distance to another point


  this.GetLoxoDist = function (P, Precision) {
    var Lat1 = Deg2Rad(this.Lat.Value);
    var Lat2 = Deg2Rad(P.Lat.Value);
    var Lon1 = -Deg2Rad(this.Lon.Value);
    var Lon2 = -Deg2Rad(P.Lon.Value);
    var TOL = 0.000000000000001;
    var d = 0;
    var q = 0;

    if (Math.abs(Lat2 - Lat1) < Math.sqrt(TOL)) {
      q = Math.cos(Lat1);
    } else {
      q = (Lat2 - Lat1) / Math.log(Math.tan(Lat2 / 2 + Math.PI / 4) / Math.tan(Lat1 / 2 + Math.PI / 4));
    }

    d = Math.sqrt(Math.pow(Lat2 - Lat1, 2) + q * q * (Lon2 - Lon1) * (Lon2 - Lon1));
    var RetVal = EARTH_RADIUS * d;
    return RoundPow(RetVal, Precision);
  }; //  Reaches a point from position using VLM Formula.
  // Compute the position of point at r * distance to point P is 1st param is a Position
  // Computes the position at Distance P, and heading r if P is a number
  // Along loxodrome from this to P


  this.ReachDistLoxo = function (PosOrDistance, RatioOrHeading) {
    var d = 0;
    var tc = 0;

    if (isNaN(RatioOrHeading)) {
      throw "unsupported reaching NaN distance";
    }

    if (typeof PosOrDistance == "number") {
      d = Deg2Rad(PosOrDistance / 60.0);
      tc = Deg2Rad(RatioOrHeading % 360.0);
    } else {
      d = this.GetLoxoDist(PosOrDistance) / EARTH_RADIUS * RatioOrHeading;
      tc = Deg2Rad(this.GetLoxoCourse(PosOrDistance));
    }

    var Lat1 = Deg2Rad(this.Lat.Value);
    var Lon1 = Deg2Rad(this.Lon.Value);
    var Lat = 0;
    var Lon = 0;
    Lat = Lat1 + d * Math.cos(tc);
    var t_lat = (Lat1 + Lat) / 2.0;
    Lon = Lon1 + d * Math.sin(tc) / Math.cos(t_lat);

    if (Lon > Math.PI) {
      Lon -= 2 * Math.PI;
    } else if (Lon < -Math.PI) {
      Lon += 2 * Math.PI;
    }

    if (isNaN(Lon) || isNaN(Lat)) {
      throw "Reached Nan Position!!!";
    }

    Lon = Rad2Deg(Lon);
    Lat = Rad2Deg(Lat);
    return new VLMPosition(NormalizeLongitudeDeg(Lon), Lat);
  }; // Reaches a point from position using rhumbline from aviation formulary.
  // Compute the position of point at r * distance to point P is 1st param is a Position
  // Computes the position at Distance P, and heading r if P is a number
  // Along loxodrome from this to P
  // this.ReachDistLoxo = function(P, r)
  // {
  //   var d = 0;
  //   var tc = 0;
  //   if (isNaN(r))
  //   {
  //     throw "unsupported reaching NaN distance";
  //   }
  //   if (typeof P == "number")
  //   {
  //     d = P / EARTH_RADIUS;
  //     tc = Deg2Rad(r % 360);
  //   }
  //   else
  //   {
  //     d = this.GetLoxoDist(P) / EARTH_RADIUS * r;
  //     tc = Deg2Rad(this.GetLoxoCourse(P));
  //   }
  //   var Lat1 = Deg2Rad(this.Lat.Value);
  //   var Lon1 = -Deg2Rad(this.Lon.Value);
  //   var Lat = 0;
  //   var Lon = 0;
  //   var TOL = 0.000000000000001;
  //   var q = 0;
  //   var dPhi = 0;
  //   var dlon = 0;
  //   Lat = Lat1 + d * Math.cos(tc);
  //   if (Math.abs(Lat) > Math.PI / 2)
  //   {
  //     //'"d too large. You can't go this far along this rhumb line!"
  //     throw "Invalid distance, can't go that far";
  //   }
  //   if (Math.abs(Lat - Lat1) < Math.sqrt(TOL))
  //   {
  //     q = Math.cos(Lat1);
  //   }
  //   else
  //   {
  //     dPhi = Math.log(Math.tan(Lat / 2 + Math.PI / 4) / Math.tan(Lat1 / 2 + Math.PI / 4));
  //     q = (Lat - Lat1) / dPhi;
  //   }
  //   dlon = -d * Math.sin(tc) / q;
  //   Lon = -(((Lon1 + dlon + Math.PI) % (2 * Math.PI) - Math.PI));
  //   if (isNaN(Lon) || isNaN(Lat))
  //   {
  //     throw "Reached Nan Position!!!";
  //   }
  //   Lon = RoundPow(Rad2Deg(Lon), 9);
  //   Lat = RoundPow(Rad2Deg(Lat), 9);
  //   return new VLMPosition(NormalizeLongitudeDeg(Lon), Lat);
  // };
  //
  // Return loxodromic course from this to P in °
  //


  this.GetLoxoCourse = function (P, Precision) {
    var Lon1 = -Deg2Rad(this.Lon.Value);
    var Lon2 = -Deg2Rad(P.Lon.Value);
    var Lat1 = Deg2Rad(this.Lat.Value);
    var Lat2 = Deg2Rad(P.Lat.Value);

    if (typeof Precision == "undefined" || typeof Precision != "number") {
      Precision = 17;
    }
    /*if (Lon1 > 0)
    {
        Lon2 += 2 * Math.PI
    }
    else
    {   
        Lon2 -= 2 * Math.PI
    }*/


    var dlon_w = (Lon2 - Lon1) % (2 * Math.PI);
    var dlon_e = (Lon1 - Lon2) % (2 * Math.PI);
    var dphi = Math.log(Math.tan(Lat2 / 2 + Math.PI / 4) / Math.tan(Lat1 / 2 + Math.PI / 4));
    var tc;

    if (dlon_w < dlon_e) {
      // Westerly rhumb line is the shortest
      tc = Math.atan2(dlon_w, dphi) % (2 * Math.PI);
    } else {
      tc = Math.atan2(-dlon_e, dphi) % (2 * Math.PI);
    }

    var ret = (720 - tc / Math.PI * 180) % 360;
    return RoundPow(ret, Precision);
  };

  if (VLM_DIST_ORTHO) {
    // Function GetOrthoDist
    // Return ortho distance from this to P
    this.GetOrthoDist = function (P, Precision) {
      var lon1 = -Deg2Rad(this.Lon.Value);
      var lon2 = -Deg2Rad(P.Lon.Value);
      var lat1 = Deg2Rad(this.Lat.Value);
      var lat2 = Deg2Rad(P.Lat.Value);

      if (typeof Precision == "undefined" || typeof Precision != "number") {
        Precision = 17;
      } //d=acos(sin(lat1)*sin(lat2)+cos(lat1)*cos(lat2)*cos(lon1-lon2))


      var retval = Math.acos(Math.sin(lat1) * Math.sin(lat2) + Math.cos(lat1) * Math.cos(lat2) * Math.cos(lon1 - lon2));
      return RoundPow(60 * Rad2Deg(retval), Precision);
    }; //
    // Return orthodromic course from this to P
    //


    this.GetOrthoCourse = function (P, Precision) {
      var lon1 = Deg2Rad(this.Lon.Value);
      var lon2 = Deg2Rad(P.Lon.Value);
      var lat1 = Deg2Rad(this.Lat.Value);
      var lat2 = Deg2Rad(P.Lat.Value);
      var retval;

      if (typeof Precision == "undefined" || typeof Precision != "number") {
        Precision = 17;
      }
      /*IF sin(lon2-lon1)<0       
        tc1=acos((sin(lat2)-sin(lat1)*cos(d))/(sin(d)*cos(lat1)))    
      ELSE       
        tc1=2*pi-acos((sin(lat2)-sin(lat1)*cos(d))/(sin(d)*cos(lat1)))    
      ENDIF*/

      /* var d = Deg2Rad(this.GetOrthoDist(P) / 60);
      var retval = (Math.sin(lat2) - Math.sin(lat1) * Math.cos(d)) / (Math.sin(d) * Math.cos(lat1));
      if ((retval >= -1) && (retval <= 1))
      {
        if (Math.sin(lon2 - lon1) < 0)
        {
          retval = Math.acos(retval);
        }
        else
        {
          retval = 2 * Math.PI - Math.acos(retval);
        }
      }
      else if (lat1 < lat2)
      {
        retval = 0;
      }
      else
      {
        retval = Math.PI;
      }
        */


      var g, d, den;
      g = Math.fmod(lon2 - lon1, 2 * Math.PI);

      if (Math.abs(g) < 0.0000001) {
        /* close enough to vertical, clamp to vertical*/
        den = lat2 - lat1;
        retval = den > 0 ? 0 : Math.PI;
      } else {
        if (g <= -Math.PI) {
          g += 2 * Math.PI;
        } else if (g > Math.PI) {
          g -= 2 * Math.PI;
        }

        d = Math.acos(Math.sin(lat2) * Math.sin(lat1) + Math.cos(lat2) * Math.cos(lat1) * Math.cos(g));
        den = Math.cos(lat1) * Math.sin(d);

        if (g < 0) {
          retval = 2 * Math.PI - Math.acos((Math.sin(lat2) - Math.sin(lat1) * Math.cos(d)) / den);
        } else {
          retval = Math.acos((Math.sin(lat2) - Math.sin(lat1) * Math.cos(d)) / den);
        }
      }

      retval = Rad2Deg(retval % (2 * Math.PI));
      return RoundPow(retval, Precision);
    };
  } else {
    // Function GetOrthoDist
    // Return ortho distance from this to P
    this.GetOrthoDist = function (P, Precision) {
      var lon1 = -Deg2Rad(this.Lon.Value);
      var lon2 = -Deg2Rad(P.Lon.Value);
      var lat1 = Deg2Rad(this.Lat.Value);
      var lat2 = Deg2Rad(P.Lat.Value);

      if (typeof Precision == "undefined" || typeof Precision != "number") {
        Precision = 17;
      } //        d=2*asin(sqrt((sin((lat1-lat2)/2))^2 + 
      //                 cos(lat1)*cos(lat2)*(sin((lon1-lon2)/2))^2))


      var retval = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin((lat1 - lat2) / 2), 2) + Math.pow(Math.cos(lat1) * Math.cos(lat2) * Math.sin((lon1 - lon2) / 2), 2)));
      return RoundPow(EARTH_RADIUS * retval, Precision);
    }; //
    // Return orthodromic course from this to P
    //


    this.GetOrthoCourse = function (P, Precision) {
      var lon1 = -Deg2Rad(this.Lon.Value);
      var lon2 = -Deg2Rad(P.Lon.Value);
      var lat1 = Deg2Rad(this.Lat.Value);
      var lat2 = Deg2Rad(P.Lat.Value);

      if (typeof Precision == "undefined" || typeof Precision != "number") {
        Precision = 17;
      } //tc1=mod(atan2(sin(lon1-lon2)*cos(lat2),
      //   cos(lat1)*sin(lat2)-sin(lat1)*cos(lat2)*cos(lon1-lon2)), 2*pi)


      var retval = Math.atan2(Math.sin(lon1 - lon2) * Math.cos(lat2), Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1) * Math.cos(lat2) * Math.cos(lon1 - lon2));
      retval = Rad2Deg(retval % (2 * Math.PI));
      return RoundPow(retval, Precision);
    };
  }

  this.ReachDistOrtho = function (dist, bearing) {
    var lat;
    var dlon;
    var d = dist / EARTH_RADIUS;
    var tc = Deg2Rad(bearing);
    var CurLat = Deg2Rad(this.Lat.Value);
    var CurLon = Deg2Rad(-this.Lon.Value);
    lat = Math.asin(Math.sin(CurLat) * Math.cos(d) + Math.cos(CurLat) * Math.sin(d) * Math.cos(tc));
    dlon = Math.atan2(Math.sin(tc) * Math.sin(d) * Math.cos(CurLat), Math.cos(d) - Math.sin(CurLat) * Math.sin(lat));
    lon = (CurLon - dlon + Math.PI) % (2 * Math.PI) - Math.PI;
    return new VLMPosition(NormalizeLongitudeDeg(Rad2Deg(-lon)), Rad2Deg(lat));
  };

  this.GetVLMString = function () {
    return this.Lat.toString() + ',' + this.Lon.toString();
  };
}

var _IsLoggedIn;

function Boat(vlmboat) {
  // Default init
  this.IdBoat = -1;
  this.Engaged = false;
  this.BoatName = '';
  this.BoatPseudo = '';
  this.VLMInfo = {}; // LastBoatInfoResult

  this.RaceInfo = {}; // Race Info for the boat

  this.Exclusions = []; // Exclusions Zones for this boat

  this.Track = []; // Last 24H of boat Track

  this.RnkObject = {}; // Ranking table

  this.OppTrack = []; // Opponents tracks table

  this.OppList = []; // Opponents list to limit how many boats are shown

  this.Reals = []; // Reals Boat array

  this.VLMPrefs = []; // Preferences Array;

  this.NextServerRequestDate = null; // Next VAC Start date

  this.Estimator = new Estimator(this); // Estimator object for current boat

  this.EstimatePos = null; // Position marker on estimate track

  if (typeof vlmboat !== 'undefined') {
    this.IdBoat = vlmboat.idu;
    this.Engaged = vlmboat.engaged;
    this.BoatName = vlmboat.boatname;
    this.BoatPseudo = vlmboat.boatpseudo;
    this.VLMInfo = vlmboat.VLMInfo;
    this.RaceInfo = vlmboat.RaceInfo;
    this.Exclusions = vlmboat.Exclusions;
    this.Track = vlmboat.Track;
    this.RnkObject = vlmboat.RnkObject;
  }

  this.GetNextGateSegment = function (NWP) {
    if (typeof NWP === "string") {
      NWP = parseInt(NWP, 10);
    }

    if (typeof this.RaceInfo === "undefined") {
      return null;
    }

    var Gate = this.RaceInfo.races_waypoints[NWP]; // Loop to look for next racing gate. ICE_Gate are not used to AutoWP routing

    do {
      // Make sure gate type is handled as a number
      if (typeof Gate === "string") {
        Gate = parseInt(Gate, 10);
      }

      if (Gate.wpformat & WP_ICE_GATE) {
        NWP++;

        if (NWP >= this.RaceInfo.races_waypoints) {
          throw "Oops could not find requested gate type";
        }

        Gate = this.RaceInfo.races_waypoints[NWP];
      }
    } while (Gate.wpformat & WP_ICE_GATE);

    var P1 = new VLMPosition(Gate.longitude1, Gate.latitude1);
    var P2 = {};

    if ((Gate.format & WP_GATE_BUOY_MASK) === WP_TWO_BUOYS) {
      P2 = new VLMPosition(Gate.longitude2, Gate.latitude2);
    } else {
      throw "not implemented 1 buoy gate";
    }

    return {
      P1: P1,
      P2: P2
    };
  };

  this.GetClosestEstimatePoint = function (Pos) {
    if (typeof Pos === "undefined" || !Pos) {
      if (this.Estimator) {
        this.Estimator.ClearEstimatePosition(this.Estimator.Boat);
      }

      return null;
    }

    if (this.Estimator) {
      var Est = this.Estimator.GetClosestEstimatePoint(Pos);

      if (Est) {
        this.Estimator.ShowEstimatePosition(this.Estimator.Boat, Est);
      } else {
        this.Estimator.ClearEstimatePosition(this.Estimator.Boat);
      }

      return Est;
    } else {
      this.Estimator.ShowEstimatePosition(null, null);
      return null;
    }
  };

  this.GetNextWPPosition = function (NWP, Position, NWPPosition) {
    if (typeof this.VLMInfo === "undefined") {
      // Should not come here without some kind of VLMInfo...
      return null;
    } // Assume if we get there, there is a boat with RaceInfo and VLMInfo loaded


    var WPIndex = this.VLMInfo.NWP; //If there is a defined WP, then return it

    if ((typeof NWPPosition === "undefined" || !NWPPosition) && (this.VLMInfo.WPLON !== "0" || this.VLMInfo.WPLAT !== "0")) {
      return new VLMPosition(this.VLMInfo.WPLON, this.VLMInfo.WPLAT);
    } else if (typeof NWPPosition !== "undefined" && NWPPosition && NWPPosition.Lon.Value !== 0 && NWPPosition.Lat.Value !== 0) {
      return new VLMPosition(NWPPosition.Lon.Value, NWPPosition.Lat.Value);
    } else {
      // Get CurRaceWP
      // Compute closest point (using bad euclidian method)
      // Return computed point
      var CurWP = this.VLMInfo.NWP;

      if (typeof NWP !== "undefined" && NWP) {
        CurWP = NWP;
      }

      var Seg = this.GetNextGateSegment(CurWP);

      if (typeof Seg === "undefined" || !Seg) {
        return null;
      }

      var Loxo1 = Seg.P1.GetLoxoCourse(Seg.P2);
      var CurPos;

      if (typeof Position !== "undefined" && Position) {
        CurPos = Position;
      } else {
        CurPos = new VLMPosition(this.VLMInfo.LON, this.VLMInfo.LAT);
      }

      var Loxo2 = Seg.P1.GetLoxoCourse(CurPos);
      var Delta = Loxo1 - Loxo2;

      if (Delta > 180) {
        Delta -= 360.0;
      } else if (Delta < -180) {
        Delta += 360.0;
      }

      Delta = Math.abs(Delta);

      if (Delta > 90) {
        return Seg.P1;
      } else {
        var PointDist = Seg.P1.GetLoxoDist(CurPos);

        try {
          var SegDist = PointDist * Math.cos(Deg2Rad(Delta));
          var SegLength = Seg.P1.GetLoxoDist(Seg.P2);

          if (SegLength > SegDist) {
            return Seg.P1.ReachDistLoxo(SegDist, Loxo1);
          } else {
            return Seg.P2;
          }
        } catch (e) {
          return null;
        }
      }
    }
  };
}

function User() {
  this.IdPlayer = -1;
  this.IsAdmin = false;
  this.PlayerName = '';
  this.PlayerJID = '';
  this.Fleet = [];
  this.BSFleet = [];
  this.CurBoat = {};
  this.LastLogin = 0;

  this.KeepAlive = function () {
    console.log("Keeping login alive...");
    CheckLogin();
  }; // Send Login every 10'


  setInterval(this.KeepAlive, 600000);
}

function IsLoggedIn() {
  return _IsLoggedIn;
}

function OnLoginRequest() {
  CheckLogin(true);
}

function GetPHPSessId() {
  var Session = document.cookie.split(";");
  var index;

  for (index in Session) {
    if (Session[index]) {
      var f = Session[index].split("=");

      if (f[0] && f[0].trim() === "PHPSESSID") {
        return f[0];
      }
    }
  }

  return null;
}

function CheckLogin(GuiRequest) {
  var user = $(".UserName").val();
  var password = $(".UserPassword").val();
  var PhpSessId = GetPHPSessId();

  if (PhpSessId || typeof user === "string" && typeof password === "string" && user.trim().length > 0 && password.trim().length > 0) {
    ShowPb("#PbLoginProgress");
    $.post("/ws/login.php", {
      VLM_AUTH_USER: user.trim(),
      VLM_AUTH_PW: password.trim()
    }, function (result) {
      var LoginResult = JSON.parse(result);
      var CurLoginStatus = _IsLoggedIn;
      var CurBoatID = null;

      if (CurLoginStatus) {
        CurBoatID = _CurPlayer.CurBoatID;
      }

      _IsLoggedIn = LoginResult.success === true;
      HandleCheckLoginResponse(GuiRequest);

      if (CurBoatID) {
        SetCurrentBoat(GetBoatFromIdu(select), false);
      }
    });
  } else {
    HandleCheckLoginResponse(GuiRequest);
  }
}

function HandleCheckLoginResponse(GuiRequest) {
  if (_IsLoggedIn) {
    GetPlayerInfo();
  } else if (GuiRequest) {
    VLMAlertDanger(GetLocalizedString("authfailed"));
    $(".UserPassword").val(""); // Reopened login dialog

    setTimeout(function () {
      $("#LoginForm").modal("hide").modal("show");
    }, 1000);
    initrecaptcha(true, false);
    $("#ResetPasswordLink").removeClass("hidden");
  }

  HidePb("#PbLoginProgress");
  DisplayLoggedInMenus(_IsLoggedIn);
}

function Logout() {
  DisplayLoggedInMenus(false);
  $.post("/ws/logout.php", function (result) {
    var i = result;

    if (!result.success) {
      VLMAlertDanger("Something bad happened while logging out. Restart browser...");
      windows.location.reload();
    } else {
      window.location.reload();
    }
  });
  _IsLoggedIn = false;
} // Global handle to the current player object


var _CurPlayer = null;

function GetPlayerInfo() {
  ShowBgLoad();
  $.get("/ws/playerinfo/profile.php", function (result) {
    if (result.success) {
      // Ok, create a user from profile
      if (typeof _CurPlayer === 'undefined' || !_CurPlayer) {
        _CurPlayer = new User();
      }

      _CurPlayer.IdPlayer = result.profile.idp;
      _CurPlayer.IsAdmin = result.profile.admin;
      _CurPlayer.PlayerName = result.profile.playername;
      $.get("/ws/playerinfo/fleet_private.php", HandleFleetInfoLoaded);
      RefreshPlayerMenu();
    } else {
      // Something's wrong, act as not logged in
      Logout();
      return;
    }
  });
}

function HandleFleetInfoLoaded(result) {
  var i = result;
  var select;

  if (typeof _CurPlayer === 'undefined') {
    _CurPlayer = new User();
  }

  if (typeof _CurPlayer.Fleet === "undefined") {
    _CurPlayer.Fleet = [];
  }

  for (var boat in result.fleet) {
    if (typeof _CurPlayer.Fleet[boat] === "undefined") {
      _CurPlayer.Fleet[boat] = new Boat(result.fleet[boat]);

      if (typeof select === "undefined") {
        select = _CurPlayer.Fleet[boat];
      }
    }
  }

  if (typeof _CurPlayer.fleet_boatsit === "undefined") {
    _CurPlayer.fleet_boatsit = [];
  }

  for (var _boat in result.fleet_boatsit) {
    if (typeof _CurPlayer.BSFleet[_boat] === "undefined") {
      _CurPlayer.BSFleet[_boat] = new Boat(result.fleet_boatsit[_boat]);
    }
  }

  RefreshPlayerMenu();

  if (typeof select !== "undefined" && select) {
    DisplayCurrentDDSelectedBoat(select);
    SetCurrentBoat(GetBoatFromIdu(select), true);
    RefreshCurrentBoat(true, false);
  }
}

function RefreshPlayerMenu() {
  // Update GUI for current player
  $("#PlayerId").text(_CurPlayer.PlayerName); // Update the combo to select the current boat

  ClearBoatSelector();

  for (var boat in _CurPlayer.Fleet) {
    AddBoatToSelector(_CurPlayer.Fleet[boat], true);
  }

  for (var _boat2 in _CurPlayer.BSFleet) {
    if (_CurPlayer.BSFleet[_boat2]) {
      AddBoatToSelector(_CurPlayer.BSFleet[_boat2], false);
    }
  }

  DisplayLoggedInMenus(true);
  HideBgLoad("#PbLoginProgress");
}

function SetupUserMenu() {
  // Set position in center of screen
  var destx = $(document).width() / 2 - $(".UserMenu").width() / 2 + 'px';
  var desty = 0; // Show Panel

  $(".UserMenu").show();
  $(".UserMenu").animate({
    left: destx,
    top: desty
  }, 0);
}

function GetBoatFromIdu(Id) {
  if (typeof _CurPlayer === "undefined") {
    return;
  }

  var RetBoat = GetBoatFromBoatArray(_CurPlayer.Fleet, Id);

  if (typeof RetBoat === 'undefined') {
    RetBoat = GetBoatFromBoatArray(_CurPlayer.BSFleet, Id);
  }

  return RetBoat;
}

function GetBoatFromBoatArray(BoatsArray, Id) {
  Id = parseInt(Id, 10);

  for (var boat in BoatsArray) {
    if (BoatsArray[boat] && BoatsArray[boat].IdBoat === Id) {
      return BoatsArray[boat];
    }
  }

  return;
}

function GetFlagsList() {
  $.get("/ws/serverinfo/flags.php", function (result) {
    var i = result;

    if (result.success) {
      var DropDown = $("#CountryDropDownList");
      var flagindex = 0;

      for (var _index28 in result.flags) {
        if (result.flags[_index28]) {
          var title = result.flags[_index28];
          DropDown.append("<li class='FlagLine DDLine' flag='" + title + "'>" + GetCountryDropDownSelectorHTML(title, true, flagindex++) + "</li>");
        }
      }
    } // Catch flag selection change


    $(".FlagLine").on('click', HandleFlagLineClick);
  });
}

var FlagsIndexCache = [];

function GetCountryDropDownSelectorHTML(title, loadflag, CountryIndex) {
  if (loadflag) {
    // Get line to build DropDown
    //var RetString1 = " <img class='flag' src='/cache/flags/flagsmap.png' flag='"+title+"' title='"+title+"' alt='"+title+"'></img>"
    var RetString1 = GetCountryFlagImg(title, CountryIndex);

    var _RetString = " <span  class='FlagLabel' flag='" + title + "'> - " + title + "</span>";

    FlagsIndexCache[title] = RetString1;
  }

  var RetString2 = " <span  class='FlagLabel' flag='" + title + "'> - " + title + "</span>";
  return FlagsIndexCache[title] + RetString2;
}

function GetCountryFlagImgHTML(country) {
  return FlagsIndexCache[country];
}

function GetCountryFlagImg(Title, CountryIndex) {
  var row = 20 * Math.floor(CountryIndex / 16);
  var col = 30 * (CountryIndex % 16);
  var RetString1 = " <div class='FlagIcon' style='background-position: -" + col + "px -" + row + "px' flag='" + Title + "'></div>";
  return RetString1;
} //
// VLMBoat layer handling displaying vlm boats, traj
//


var VLM_COORDS_FACTOR = 1000; // Default map options
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

function SetCurrentBoat(Boat, CenterMapOnBoat, ForceRefresh, TargetTab) {
  if (_CurPlayer && _CurPlayer.CurBoat && Boat) {
    if (_CurPlayer.CurBoat.IdBoat !== Boat.IdBoat) {
      ClearCurrentMapMarker(_CurPlayer.CurBoat);
    }

    EnsureMarkersVisible(Boat);
  }

  CheckBoatRefreshRequired(Boat, CenterMapOnBoat, ForceRefresh, TargetTab);
}

var BoatLoading = new Date(0);

function CheckBoatRefreshRequired(Boat, CenterMapOnBoat, ForceRefresh, TargetTab) {
  // Check Params.
  if (typeof Boat === "undefined" || !Boat) {
    return;
  }

  var CurDate = new Date();
  var NeedPrefsRefresh = typeof Boat !== "undefined" && (typeof Boat.VLMInfo === "undefined" || typeof Boat.VLMInfo.AVG === "undefined"); // Update preference screen according to current selected boat

  UpdatePrefsDialog(Boat);

  if (typeof Boat.VLMInfo === 'undefined' || typeof Boat.VLMInfo.LUP === 'undefined') {
    ForceRefresh = true;
  } //if ((CurDate > BoatLoading) && (ForceRefresh || CurDate >= Boat.NextServerRequestDate))


  if (ForceRefresh || CurDate >= Boat.NextServerRequestDate) {
    BoatLoading = CurDate + 3000;
    console.log("Loading boat info from server...."); // request current boat info

    ShowPb("#PbGetBoatProgress");
    $.get("/ws/boatinfo.php?forcefmt=json&select_idu=" + Boat.IdBoat, function (result) {
      // Check that boat Id Matches expectations
      if (Boat.IdBoat === parseInt(result.IDU, 10)) {
        // Set Current Boat for player
        _CurPlayer.CurBoat = Boat; // LoadPrefs

        LoadVLMPrefs(); // Store BoatInfo, update map

        Boat.VLMInfo = result; // Store next request Date (once per minute)

        Boat.NextServerRequestDate = new Date((parseInt(Boat.VLMInfo.LUP, 10) + parseInt(Boat.VLMInfo.VAC, 10)) * 1000);
        Boat.LastRefresh = new Date(); // Fix Lon, and Lat scale

        Boat.VLMInfo.LON /= VLM_COORDS_FACTOR;
        Boat.VLMInfo.LAT /= VLM_COORDS_FACTOR;

        if ('Prod' !== 'Dev') {
          //console.log(GribMgr.WindAtPointInTime(new Date(Boat.VLMInfo.LUP*1000),Boat.VLMInfo.LAT,Boat.VLMInfo.LON ));
          console.log("DBG WIND "); //49.753227868452, -8.9971082951315

          var MI = GribMgr.WindAtPointInTime(new Date(1566149443 * 1000), 49.753227868452, -8.9971082951315);

          if (MI) {
            var Hdg = MI.Heading + 40;
            var Speed = PolarsManager.GetBoatSpeed("boat_figaro2", MI.Speed, MI.Heading, Hdg);

            if (!isNaN(Speed)) {
              var P = new VLMPosition(49.753227868452, -8.9971082951315);
              var dest = P.ReachDistLoxo(Speed / 3600.0 * 300, Hdg);
              var bkp1 = 0;
            }
          }
        } // force refresh of settings if was not initialized


        if (NeedPrefsRefresh) {
          UpdatePrefsDialog(Boat);
        } // update map if racing


        if (Boat.VLMInfo.RAC !== "0") {
          if (typeof Boat.RaceInfo === "undefined" || typeof Boat.RaceInfo.idraces === 'undefined') {
            // Get race info if first request for the boat
            GetRaceInfoFromServer(Boat, TargetTab);
            GetRaceExclusionsFromServer(Boat);
          } // Get boat track for the last 24h


          GetTrackFromServer(Boat); // Get Rankings

          if (Boat.VLMInfo && Boat.VLMInfo.RAC) {
            LoadRankings(Boat.VLMInfo.RAC);
          } // Get Reals


          LoadRealsList(Boat); // Draw Boat, course, tracks....

          DrawBoat(Boat, CenterMapOnBoat); // Update Boat info in main menu bar

          UpdateInMenuRacingBoatInfo(Boat, TargetTab);
        } else {
          // Boat is not racing
          //GetLastRacehistory();
          UpdateInMenuDockingBoatInfo(Boat);
        }
      }

      HidePb("#PbGetBoatProgress");

      if (OnPlayerLoadedCallBack) {
        OnPlayerLoadedCallBack();
        OnPlayerLoadedCallBack = null;
      }
    });
  } else if (Boat) {
    // Set Current Boat for player
    _CurPlayer.CurBoat = Boat; // Draw from last request

    UpdateInMenuDockingBoatInfo(Boat);
    UpdateInMenuRacingBoatInfo(Boat, TargetTab);
    DrawBoat(Boat, CenterMapOnBoat);
  }
} // Get Track from server for last 48 hours.


function GetTrackFromServer(Boat) {
  var end = Math.floor(new Date() / 1000);
  var start = end - 48 * 3600;
  $.get("/ws/boatinfo/tracks_private.php?idu=" + Boat.IdBoat + "&idr=" + Boat.VLMInfo.RAC + "&starttime=" + start + "&endtime=" + end, function (result) {
    if (result.success) {
      if (typeof Boat.Track !== "undefined") {
        Boat.Track.length = 0;
      } else {
        Boat.Track = [];
      }

      for (var _index29 in result.tracks) {
        if (result.tracks[_index29]) {
          var P = new VLMPosition(result.tracks[_index29][1] / 1000.0, result.tracks[_index29][2] / 1000.0);
          Boat.Track.push(P);
        }
      }

      DrawBoat(Boat);
    }
  });
}

function GetRaceExclusionsFromServer(Boat) {
  $.get("/ws/raceinfo/exclusions.php?idrace=" + Boat.VLMInfo.RAC + "&v=" + Boat.VLMInfo.VER, function (result) {
    if (result.success) {
      var Polygons = [];
      var CurEndPoint;
      var CurPolyPointsList = [];

      var _index30;

      for (_index30 in result.Exclusions) {
        if (result.Exclusions[_index30]) {
          var Seg = result.Exclusions[_index30];

          if (typeof CurEndPoint === 'undefined' || CurEndPoint[0] !== Seg[0][0] && CurEndPoint[1] !== Seg[0][1]) {
            if (typeof CurEndPoint !== 'undefined') {
              // Changing Polygons
              Polygons.push(CurPolyPointsList);
              CurPolyPointsList = [];
            } // Add segment Start to current point list


            CurPolyPointsList.push(Seg[0]);
          }

          CurEndPoint = Seg[1]; // Add segment end  to current point list

          CurPolyPointsList.push(Seg[1]);
        }
      }

      Polygons.push(CurPolyPointsList);
      Boat.Exclusions = Polygons;
      DrawRaceExclusionZones(Boat, Polygons);
    }
  });
}

function GetRaceInfoFromServer(Boat, TargetTab) {
  $.get("/ws/raceinfo/desc.php?idrace=" + Boat.VLMInfo.RAC + "&v=" + Boat.VLMInfo.VER, function (result) {
    // Save raceinfo with boat
    Boat.RaceInfo = result;
    DrawRaceGates(Boat);
    UpdateInMenuRacingBoatInfo(Boat, TargetTab);
  });
}

var DrawBoatTimeOutHandle = null;
var DeferredCenterValue = false;

function DrawBoat(Boat, CenterMapOnBoat) {
  if (typeof CenterMapOnBoat !== "undefined") {
    DeferredCenterValue = DeferredCenterValue || CenterMapOnBoat;
  }

  console.log("Call DrawbBoat (" + CenterMapOnBoat + ") deferred : " + DeferredCenterValue);

  if (DrawBoatTimeOutHandle) {
    console.log("Pushed DrawBoat");
    clearTimeout(DrawBoatTimeOutHandle);
  }

  DrawBoatTimeOutHandle = setTimeout(ActualDrawBoat, 100, Boat, DeferredCenterValue);
}

function GetRaceMapFeatures(Boat) {
  if (!Boat) {
    throw "Should not GetRaceFeature unless a boat is defined";
  }

  if (typeof Boat.RaceMapFeatures === "undefined") {
    Boat.RaceMapFeatures = {};
  }

  return Boat.RaceMapFeatures;
}

function ActualDrawBoat(Boat, CenterMapOnBoat) {
  var ZFactor = map.zoom; //console.log("ClearDrawBoat " + CenterMapOnBoat + " Z level "+ );

  DeferredCenterValue = false;
  DrawBoatTimeOutHandle = null;

  if (typeof Boat === "undefined" || !Boat) {
    if (typeof _CurPlayer !== "undefined" && _CurPlayer && typeof _CurPlayer.CurBoat !== "undefined" && _CurPlayer.CurBoat) {
      // Fallback to currently selected Boat
      Boat = _CurPlayer.CurBoat;
    } else {
      // Ignore call, if no boat is provided...
      return;
    }
  }

  if (typeof Boat === "undefined" || !Boat) {
    // Should not be there
    return;
  }

  var RaceFeatures = GetRaceMapFeatures(Boat); //WP Marker

  var WPFeature = RaceFeatures.TrackWP;
  var WP = null;

  if (typeof Boat !== "undefined" && Boat) {
    WP = Boat.GetNextWPPosition();
  }

  if (typeof WP !== "undefined" && WP && !isNaN(WP.Lat.Value) && !isNaN(WP.Lon.Value)) {
    if (WPFeature) {
      WPFeature.setLatLng([WP.Lat.Value, WP.Lon.Value]);
    } else {
      // Track Waypoint marker    
      var WPMarker = GetTrackWPMarker();
      RaceFeatures.TrackWP = L.marker([WP.Lat.Value, WP.Lon.Value], {
        icon: WPMarker,
        draggable: true
      }).addTo(map).on("dragend", HandleWPDragEnded);
    }
  } // Boat Marker


  if (_typeof(Boat.VLMInfo) !== undefined && Boat.VLMInfo && (Boat.VLMInfo.LON || Boat.VLMInfo.LAT)) {
    var BoatIcon = RaceFeatures.BoatMarker;

    if (BoatIcon) {
      BoatIcon.setLatLng([Boat.VLMInfo.LAT, Boat.VLMInfo.LON]);
      BoatIcon.setRotationAngle(Boat.VLMInfo.HDG);
    } else {
      BoatIcon = GetBoatMarker(Boat.VLMInfo.idusers);
      RaceFeatures.BoatMarker = L.marker([Boat.VLMInfo.LAT, Boat.VLMInfo.LON], {
        icon: BoatIcon,
        rotationAngle: Boat.VLMInfo.HDG
      }).addTo(map).on('click', HandleOpponentClick);
    } //Draw polar


    if (typeof map !== "undefined" && map) {
      DrawBoatPolar(Boat, CenterMapOnBoat, RaceFeatures);
    }
  } // Cur Boat track  


  if (typeof Boat.Track !== "undefined" && Boat.Track.length > 0) {
    DrawBoatTrack(Boat, RaceFeatures);
  } // Forecast Track


  DrawBoatEstimateTrack(Boat, RaceFeatures); // opponents  

  DrawOpponents(Boat);

  if (CenterMapOnBoat && typeof Boat.VLMInfo !== "undefined" && Boat.VLMInfo) {
    if (typeof map !== "undefined" && map) {
      map.setView([Boat.VLMInfo.LAT, Boat.VLMInfo.LON]);
    }
  } // Position Compas according to current boat pos


  {
    RepositionCompass(Boat);
  }
  console.log("ActualDrawBoatComplete");
}

function DrawBoatEstimateTrack(Boat, RaceFeatures) {
  if (typeof Boat.Estimator !== "undefined" && Boat.Estimator) {
    var tracks = Boat.Estimator.GetEstimateTracks();
    var TrackColors = ['green', 'orange', 'red'];

    for (var _index31 in tracks) {
      if (RaceFeatures.EstimateTracks && RaceFeatures.EstimateTracks[_index31]) {
        if (typeof tracks[_index31] !== "undefined") {
          RaceFeatures.EstimateTracks[_index31].setLatLngs(tracks[_index31]);
        } else {
          RaceFeatures.EstimateTracks[_index31].remove();

          RaceFeatures.EstimateTracks[_index31] = null;
        }
      } else {
        if (typeof RaceFeatures.EstimateTracks === "undefined") {
          RaceFeatures.EstimateTracks = [];
        }

        if (tracks[_index31]) {
          var Options = {
            weight: 2,
            opacity: 1,
            color: TrackColors[_index31]
          };
          RaceFeatures.EstimateTracks[_index31] = L.polyline(tracks[_index31], Options).addTo(map);
        }
      }
    }
  }
}

function RepositionCompass(Boat) {
  if (!Boat) {
    return;
  }

  var Features = GetRaceMapFeatures(Boat);

  if (map.Compass) {
    if (Features.Compass && Features.Compass.Lat == -1 && Features.Compass.Lon == -1 || Boat.VLMInfo && (Boat.VLMInfo.LAT || Boat.VLMInfo.LON)) {
      map.Compass.setLatLng([Boat.VLMInfo.LAT, Boat.VLMInfo.LON]);
    } else if (Features.Compass && !isNaN(Features.Compass.Lat) && !isNaN(Features.Compass.Lon)) {
      map.Compass.setLatLng([Features.Compass.Lat, Features.Compass.Lon]);
    }
  }
}

function DrawBoatTrack(Boat, RaceFeatures) {
  var PointList = [];
  var TrackLength = Boat.Track.length;
  var PrevLon = 99999;
  var LonOffSet = 0;

  for (var _index32 = 0; _index32 < TrackLength; _index32++) {
    var P = Boat.Track[_index32];
    PointList.push([P.Lat.Value, P.Lon.Value]);
  }

  var TrackColor = Boat.VLMInfo.COL;
  TrackColor = SafeHTMLColor(TrackColor);
  var TrackFeature = RaceFeatures.BoatTrack;

  if (TrackFeature) {
    TrackFeature.setLatLngs(PointList);
  } else {
    RaceFeatures.BoatTrack = L.polyline(PointList, {
      "type": "HistoryTrack",
      "color": TrackColor,
      "weight": 1.2
    }).addTo(map);
  }
}

function DrawBoatPolar(Boat, CenterMapOnBoat, RaceFeatures) {
  var Polar = [];
  var StartPos = new VLMPosition(Boat.VLMInfo.LON, Boat.VLMInfo.LAT);
  Polar = BuildPolarLine(Boat, StartPos, VLM2Prefs.MapPrefs.PolarVacCount, new Date(Boat.VLMInfo.LUP * 1000), function () {
    DrawBoatPolar(Boat, CenterMapOnBoat, RaceFeatures);
  });
  RaceFeatures.Polar = DefinePolarMarker(Polar, RaceFeatures.Polar);
}

function DefinePolarMarker(Polar, PolarFeature) {
  if (Polar) {
    if (PolarFeature) {
      PolarFeature.setLatLngs(Polar).addTo(map);
    } else {
      var PolarStyle = {
        color: "white",
        opacity: 0.6,
        weight: 1
      };
      PolarFeature = L.polyline(Polar, PolarStyle);
      PolarFeature.addTo(map);
    }
  } else {
    if (PolarFeature) {
      PolarFeature.remove();
    }

    PolarFeature = null;
  }

  return PolarFeature;
}

function BuildPolarLine(Boat, StartPos, scale, StartDate, Callback) {
  var CurDate = StartDate;
  var Polar = null;

  if (Boat && Boat.VLMInfo && Boat.VLMInfo.VAC) {
    // set time 1 vac back
    CurDate -= Boat.VLMInfo.VAC * 1000;
  }

  if (!CurDate || CurDate < new Date().getTime()) {
    CurDate = new Date().getTime();
  }

  var MI = null;

  if (StartPos && StartPos.Lat && StartPos.Lon) {
    MI = GribMgr.WindAtPointInTime(CurDate, StartPos.Lat.Value, StartPos.Lon.Value, Callback);
  }

  if (MI) {
    var hdg = parseFloat(Boat.VLMInfo.HDG);

    var _index33;

    var tmpPolar = [];

    for (_index33 = 0; _index33 <= 180; _index33 += 5) {
      var Speed = PolarsManager.GetBoatSpeed(Boat.VLMInfo.POL, MI.Speed, MI.Heading, MI.Heading + _index33);

      if (isNaN(Speed)) {
        // Just abort in case of not yet loaded polar. Next display should fix it.
        // FixMe - Should we try later or will luck do it for us??
        return;
      }

      for (var Side = -1; Side <= 1; Side += 2) {
        var PolarPos = StartPos.ReachDistLoxo(Speed / 3600.0 * Boat.VLMInfo.VAC * scale, MI.Heading + _index33 * Side);
        var PixPos = [PolarPos.Lat.Value, PolarPos.Lon.Value];
        tmpPolar[Side * _index33 + 180] = PixPos;
      }
    }

    Polar = [];

    for (var _index34 in tmpPolar) {
      if (tmpPolar[_index34]) {
        Polar.push(tmpPolar[_index34]);
      }
    }
  }

  return Polar;
}

function GetVLMPositionFromClick(pixel) {
  if (map) {
    var dest = map.getLonLatFromPixel(pixel);
    var WGSDest = dest.transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));
    return new VLMPosition(WGSDest.lon, WGSDest.lat);
  } else {
    return null;
  }
}

function CompleteWPSetPosition(WPMarker) {
  var pos = null;

  if (WPMarker.getLatLng) {
    pos = WPMarker.getLatLng();
  } else if (WPMarker.latlng) {
    pos = WPMarker.latlng;
  } else {
    VLMAlertDanger("Unexpected Object when setting WP report to devs.");
    return;
  }

  var PDest = new VLMPosition(pos.lng, pos.lat); // Use CurPlayer, since the drag layer is not associated to the proper boat

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


var WP_TWO_BUOYS = 0;
var WP_ONE_BUOY = 1;
var WP_GATE_BUOY_MASK = 0x000F;
/* leave space for 0-15 types of gates using buoys
   next is bitmasks */

var WP_DEFAULT = 0;
var WP_ICE_GATE_N = 1 << 4;
var WP_ICE_GATE_S = 1 << 5;
var WP_ICE_GATE_E = 1 << 6;
var WP_ICE_GATE_W = 1 << 7;
var WP_ICE_GATE = WP_ICE_GATE_E | WP_ICE_GATE_N | WP_ICE_GATE_S | WP_ICE_GATE_W;
var WP_GATE_KIND_MASK = 0xFFF0;
/* allow crossing in one direction only */

var WP_CROSS_CLOCKWISE = 1 << 8;
var WP_CROSS_ANTI_CLOCKWISE = 1 << 9;
/* for future releases */

var WP_CROSS_ONCE = 1 << 10;
var Exclusions = [];

function DrawRaceGates(Boat) {
  if (typeof Boat === "undefined" || !Boat || !Boat.RaceInfo) {
    // Not Ready to draw
    return;
  }

  var RaceInfo = Boat.RaceInfo;
  var NextGate = Boat.VLMInfo.NWP;
  var RaceFeature = GetRaceMapFeatures(Boat); // Loop all gates

  if (_typeof(RaceInfo) !== undefined && RaceInfo && typeof RaceInfo.races_waypoints !== "undefined" && RaceInfo.races_waypoints) {
    for (var _index35 in RaceInfo.races_waypoints) {
      if (!RaceFeature.Gates) {
        RaceFeature.Gates = [];
      }

      if (!RaceFeature.Gates[_index35]) {
        RaceFeature.Gates[_index35] = {};
      }

      var GateFeatures = RaceFeature.Gates[_index35];

      if (RaceInfo.races_waypoints[_index35]) {
        var WPMarker = GateFeatures.Buoy1; // Draw a single race gates

        var WP = RaceInfo.races_waypoints[_index35]; // Fix coords scales

        NormalizeRaceInfo(RaceInfo);
        var cwgate = !(WP.wpformat & WP_CROSS_ANTI_CLOCKWISE); // Draw WP1

        var Pos = new VLMPosition(WP.longitude1, WP.latitude1);
        GateFeatures.Buoy1 = AddBuoyMarker(WPMarker, "WP" + _index35 + " " + WP.libelle + '<BR>' + Pos.toString(), WP.longitude1, WP.latitude1, cwgate); // Second buoy (if any)

        if ((WP.wpformat & WP_GATE_BUOY_MASK) === WP_TWO_BUOYS) {
          // Add 2nd buoy marker
          var _WPMarker = GateFeatures.Buoy2;

          var _Pos = new VLMPosition(WP.longitude2, WP.latitude2);

          GateFeatures.Buoy2 = AddBuoyMarker(_WPMarker, "WP" + _index35 + " " + WP.libelle + '<BR>' + _Pos.toString(), WP.longitude2, WP.latitude2, !cwgate);
        } else {
          // No Second buoy, compute segment end
          var P = new VLMPosition(WP.longitude1, WP.latitude1);
          var complete = false;
          var Dist = 2500;
          var Dest = null;

          while (!complete) {
            try {
              Dest = P.ReachDistLoxo(Dist, 180 + parseFloat(WP.laisser_au));
              complete = true;
            } catch (e) {
              Dist *= 0.7;
            }
          }

          WP.longitude2 = Dest.Lon.Value;
          WP.latitude2 = Dest.Lat.Value;
        } // Draw Gate Segment


        _index35 = parseInt(_index35, 10);
        NextGate = parseInt(NextGate, 10);
        AddGateSegment(GateFeatures, WP.longitude1, WP.latitude1, WP.longitude2, WP.latitude2, NextGate === _index35, _index35 < NextGate, WP.wpformat & WP_GATE_KIND_MASK);
      }
    }
  }
}

function DrawRaceExclusionZones(Boat, Zones) {
  if (!Boat) {
    return;
  }

  var Features = GetRaceMapFeatures(Boat);

  for (var _index36 in Zones) {
    if (Zones[_index36]) {
      DrawRaceExclusionZone(Features, Zones, _index36);
    }
  }
}

function DrawRaceExclusionZone(Features, ExclusionZones, ZoneIndex) {
  var PointList = [];
  var HasZones = false;

  for (var _index37 in ExclusionZones[ZoneIndex]) {
    if (ExclusionZones[ZoneIndex][_index37]) {
      var P = [ExclusionZones[ZoneIndex][_index37][0], ExclusionZones[ZoneIndex][_index37][1]];
      PointList.push(P);
      HasZones = true;
    }
  }

  if (HasZones) {
    if (typeof Features.Exclusions === "undefined") {
      Features.Exclusions = [];
    }

    if (Features.Exclusions[ZoneIndex]) {
      Features.Exclusions[ZoneIndex].setLatLngs(PointList).addTo(map);
    } else {
      Features.Exclusions[ZoneIndex] = L.polygon(PointList, {
        color: "red",
        opacity: 0.25,
        weight: 3
      }).addTo(map);
    }
  } else if (Features.Exclusions && Features.Exclusions[index]) {
    Features.Exclusions[ZoneIndex].remove();
    Features.Exclusions[ZoneIndex] = null;
  }
}

function GetLonOffset(L1, L2) {
  if (L1 * L2 >= 0) {
    return 0;
  } else if (Math.abs(L2 - L1) > 90) {
    if (L1 > 0) {
      return 360;
    } else {
      return -360;
    }
  }

  return 0;
}

function AddGateSegment(GateFeatures, lon1, lat1, lon2, lat2, IsNextWP, IsValidated, GateType) {
  var Points = [[lat1, lon1], [lat2, lon2]];
  var color = "";
  var strokeOpacity = 0.75;
  var strokeWidth = 1;

  if (IsNextWP) {
    color = "green";
  } else if (IsValidated) {
    color = "blue";
  } else {
    color = "red";
  }

  if (GateType & WP_CROSS_ONCE) {
    if (GateFeatures.Segment2) {
      GateFeatures.Segment2.setLatLngs(Points);
    } else {
      // Draw the segment again as dashed line for cross once gates
      GateFeatures.Segment2 = L.polyline(Points, {
        color: 'black',
        dashArray: '20,10,5,10',
        weight: strokeWidth * 2,
        opacity: strokeOpacity
      }).addTo(map);
    }
  }

  if (GateFeatures.Segment) {
    GateFeatures.Segment.setLatLngs(Points);
    GateFeatures.Segment.color = color;
  } else {
    GateFeatures.Segment = L.polyline(Points, {
      color: color,
      weight: strokeWidth,
      opacity: strokeOpacity
    }).addTo(map);
  }

  if (GateType !== WP_DEFAULT) {
    var P1 = new VLMPosition(lon1, lat1);
    var P2 = new VLMPosition(lon2, lat2);
    var MarkerDir = P1.GetLoxoCourse(P2);
    var MarkerPos = P1.ReachDistLoxo(P2, 0.5); // Gate has special features, add markers

    if (GateType & WP_CROSS_ANTI_CLOCKWISE) {
      MarkerDir -= 90;
      AddGateDirMarker(GateFeatures, MarkerPos.Lon.Value, MarkerPos.Lat.Value, MarkerDir);
    } else if (GateType & WP_CROSS_CLOCKWISE) {
      MarkerDir += 90;
      AddGateDirMarker(GateFeatures, MarkerPos.Lon.Value, MarkerPos.Lat.Value, MarkerDir);
    } else if (GateType & WP_ICE_GATE) {
      AddGateIceGateMarker(GateFeatures, MarkerPos.Lon.Value, MarkerPos.Lat.Value);
    }
  }
}

var MAX_BUOY_INDEX = 16;
var BuoyIndex = Math.floor(Math.random() * MAX_BUOY_INDEX);

function AddGateDirMarker(GateFeatures, Lon, Lat, Dir) {
  AddGateCenterMarker(GateFeatures, Lon, Lat, "BuoyDirs/BuoyDir" + BuoyIndex + ".png", Dir, false); // Rotate dir marker...

  BuoyIndex++;
  BuoyIndex %= MAX_BUOY_INDEX + 1;
}

function AddGateIceGateMarker(GateFeatures, Lon, Lat) {
  AddGateCenterMarker(GateFeatures, Lon, Lat, "icegate.png", true);
}

function AddGateCenterMarker(GateFeatures, Lon, Lat, Marker, Dir, IsIceGate) {
  var MarkerCoords = [Lat, Lon];

  if (GateFeatures.GateMarker) {
    GateFeatures.GateMarker.setLatLng(MarkerCoords);
  } else {
    var MarkerObj = GetGateTypeMarker(Marker, IsIceGate);
    GateFeatures.GateMarker = L.marker(MarkerCoords, {
      icon: MarkerObj
    }).addTo(map);

    if (!IsIceGate) {
      GateFeatures.GateMarker.setRotationAngle(Dir);
    }
  }
}

function AddBuoyMarker(Marker, Name, Lon, Lat, CW_Crossing) {
  var WP = GetBuoyMarker(CW_Crossing);

  if (Marker) {
    if (Marker.IsCWBuoy !== CW_Crossing) {
      // Change marker direction
      Marker.remove();
    } else {
      return Marker.setLatLng([Lat, Lon]);
    }
  }

  return L.marker([Lat, Lon], {
    icon: WP
  }).addTo(map).bindPopup(Name);
}

var PM_HEADING = 1;
var PM_ANGLE = 2;
var PM_ORTHO = 3;
var PM_VMG = 4;
var PM_VBVMG = 5;

function SendVLMBoatWPPos(Boat, P) {
  var orderdata = {
    idu: Boat.IdBoat,
    pip: {
      targetlat: P.Lat.Value,
      targetlong: P.Lon.Value,
      targetandhdg: -1 //Boat.VLMInfo.H@WP

    }
  };
  PostBoatSetupOrder(Boat.IdBoat, 'target_set', orderdata);
}

function SendVLMBoatOrder(Mode, AngleOrLon, Lat, WPAt) {
  var request = {};
  var verb = "pilot_set";

  if (typeof _CurPlayer === 'undefined' || typeof _CurPlayer.CurBoat === 'undefined') {
    VLMAlertDanger("Must select a boat to send an order");
    return;
  } // Build WS command accoridng to required pilot mode


  switch (Mode) {
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
        pip: {
          targetlong: parseFloat(AngleOrLon),
          targetlat: parseFloat(Lat),
          targetandhdg: WPAt
        }
      }; //PostBoatSetupOrder (_CurPlayer.CurBoat.IdBoat,"target_set",request);

      break;

    default:
      return;
  } // Post request


  PostBoatSetupOrder(_CurPlayer.CurBoat.IdBoat, verb, request);
}

function PostBoatSetupOrder(idu, verb, orderdata) {
  // Now Post the order
  $.post("/ws/boatsetup/" + verb + ".php?selectidu" + idu, "parms=" + JSON.stringify(orderdata), function (Data, TextStatus) {
    if (Data.success) {
      RefreshCurrentBoat(false, true);
    } else {
      VLMAlertDanger(GetLocalizedString("BoatSetupError") + '\n' + Data.error.code + " " + Data.error.msg);
    }
  });
}

function EngageBoatInRace(RaceID, BoatID) {
  $.post("/ws/boatsetup/race_subscribe.php", "parms=" + JSON.stringify({
    idu: BoatID,
    idr: parseInt(RaceID, 10)
  }), function (data) {
    if (data.success) {
      var Msg = GetLocalizedString("youengaged");
      $("#RacesListForm").modal('hide');
      VLMAlertSuccess(Msg);
    } else {
      var _Msg = data.error.msg + '\n' + data.error.custom_error_string;

      VLMAlertDanger(_Msg);
    }
  });
}

function DiconstinueRace(BoatId, RaceId) {
  $.post("/ws/boatsetup/race_unsubscribe.php", "parms=" + JSON.stringify({
    idu: BoatId,
    idr: parseInt(RaceId, 10)
  }), function (data) {
    if (data.success) {
      VLMAlertSuccess("Bye Bye!");
    } else {
      var Msg = data.error.msg + '\n' + data.error.custom_error_string;
      VLMAlertDanger(Msg);
    }
  });
}

function LoadRealsList(Boat) {
  if (typeof Boat === "undefined" || !Boat || typeof Boat.VLMInfo === "undefined") {
    return;
  }

  $.get("/ws/realinfo/realranking.php?idr=" + Boat.VLMInfo.RAC, function (result) {
    if (result.success) {
      Boat.Reals = result;
      DrawBoat(Boat, false);
    } else {
      Boat.Reals = [];
    }
  });
}

function LoadRankings(RaceId, CallBack) {
  if (RaceId && _typeof(RaceId) === 'object') {
    VLMAlertDanger("Not updated call to LoadRankings");
  }
  /*if ((typeof Boat === "undefined") || !Boat || (typeof Boat.VLMInfo === "undefined"))
  {
    return;
  }*/


  $.get("/cache/rankings/rnk_" + RaceId + ".json"
  /*?d=" + (new Date().getTime())*/
  , function (result) {
    if (result) {
      Rankings[RaceId] = result.Boats;

      if (CallBack) {
        CallBack();
      } else {
        DrawBoat(null, false);
      }
    } else {
      Rankings[RaceId] = null;
    }
  });
}

function contains(a, obj) {
  for (var i = 0; i < a.length; i++) {
    if (a[i] === obj) {
      return true;
    }
  }

  return false;
}

function DrawOpponents(Boat) {
  if (!Boat || typeof Rankings === "undefined") {
    return;
  } // Get Friends


  var friends = [];
  var index; // Map friend only if selection is active

  if (VLM2Prefs.MapPrefs.MapOppShow === VLM2Prefs.MapPrefs.MapOppShowOptions.ShowSel) {
    if (typeof Boat.VLMInfo !== "undefined" && typeof Boat.VLMInfo.MPO !== "undefined") {
      friends = Boat.VLMInfo.MPO.split(',');
    }

    if (friends.length !== 0) {
      var RaceId = Boat.VLMInfo.RAC;

      for (index in friends) {
        if (friends[index] && Rankings[RaceId]) {
          var _Opp = Rankings[RaceId][friends[index]];

          if (typeof _Opp !== 'undefined' && parseInt(_Opp.idusers, 10) !== Boat.IdBoat) {
            AddOpponent(Boat, VLMBoatsLayer, BoatFeatures, _Opp, true);
          }
        }
      }
    }
  } // Get Reals


  if (VLM2Prefs.MapPrefs.ShowReals && typeof Boat.Reals !== "undefined" && typeof Boat.Reals.ranking !== "undefined") for (index in Boat.Reals.ranking) {
    var RealOpp = Boat.Reals.ranking[index];
    AddOpponent(Boat, VLMBoatsLayer, BoatFeatures, RealOpp, true);
  }
  var MAX_LEN = 150;
  var ratio = MAX_LEN / Object.keys(Rankings).length;
  var count = 0;
  var BoatList = Rankings;

  if (typeof Boat.OppList !== "undefined" && Boat.OppList.length > 0) {
    BoatList = Boat.OppList;
    ratio = 1;
  }

  switch (VLM2Prefs.MapPrefs.MapOppShow) {
    case VLM2Prefs.MapPrefs.MapOppShowOptions.Show10Around:
      BoatList = GetClosestOpps(Boat, 10);
      ratio = 1;
      break;

    case VLM2Prefs.MapPrefs.MapOppShowOptions.Show5Around:
      BoatList = GetClosestOpps(Boat, 5);
      ratio = 1;
      break;

    case VLM2Prefs.MapPrefs.MapOppShowOptions.ShowTop10:
      var BoatCount = 0;
      var RaceID = Boat.Engaged;
      MAX_LEN = VLM2Prefs.MapPrefs.ShowTopCount;
      BoatList = [];

      for (index in Rankings[RaceID]) {
        if (Rankings[RaceID][index].rank <= VLM2Prefs.MapPrefs.ShowTopCount) {
          BoatList[index] = Rankings[RaceID][index];
          BoatCount++;

          if (BoatCount > MAX_LEN) {
            break;
          }
        }
      }

      if (BoatCount > MAX_LEN) {
        MAX_LEN = BoatCount;
      }

      ratio = 1;
      break;

    case VLM2Prefs.MapPrefs.MapOppShowOptions.ShowMineOnly:
      BoatList = [];
      ratio = 1;
      break;
  } // Sort racers to be able to show proper opponents


  SortRankingData(Boat, 'RAC', null, Boat.Engaged);

  if (Boat.Engaged && typeof Rankings[Boat.Engaged] !== "undefined" && typeof Rankings[Boat.Engaged].RacerRanking !== "undefined" && Rankings[Boat.Engaged].RacerRanking) {
    var RaceFeatures = GetRaceMapFeatures(Boat);

    for (index in Rankings[Boat.Engaged].RacerRanking) {
      if (index in Rankings[Boat.Engaged].RacerRanking) {
        var Opp = Rankings[Boat.Engaged].RacerRanking[index];

        if (parseInt(Opp.idusers, 10) !== Boat.IdBoat && BoatList[Opp.idusers] && !contains(friends, Opp.idusers) && RnkIsRacing(Opp) && Math.random() <= ratio && count < MAX_LEN) {
          AddOpponent(Boat, RaceFeatures, Opp, false);
          count += 1;

          if (typeof Boat.OppList === "undefined") {
            Boat.OppList = [];
          }

          Boat.OppList[index] = Opp;
        } else if (count >= MAX_LEN) {
          break;
        }
      }
    }
  } // Draw OppTracks, if any is selected


  if (typeof Boat.RaceMapFeatures !== "undefined" && Object.keys(Boat.OppTrack).length > 0) {
    var _RaceFeatures = Boat.RaceMapFeatures;

    for (var TrackIndex in Boat.OppTrack) {
      var T = Boat.OppTrack[TrackIndex];

      if (T && T.Visible && T.DatePos.length > 1) {
        if (!T.OppTrackPoints) {
          var TrackPoints = [];
          var TLen = Object.keys(T.DatePos).length;

          for (var PointIndex = 0; PointIndex < TLen; PointIndex++) {
            var k = Object.keys(T.DatePos)[PointIndex];
            var P = T.DatePos[k];
            var Pi = [P.lat, P.lon];
            TrackPoints.push(Pi);
          }

          T.OppTrackPoints = TrackPoints;
        }

        if (typeof _RaceFeatures.OppTrack === "undefined") {
          _RaceFeatures.OppTrack = [];
        }

        if (_RaceFeatures.OppTrack[TrackIndex]) {
          _RaceFeatures.OppTrack[TrackIndex].setLatLngs(T.OppTrackPoints).addTo(map);
        } else {
          var color = 'black';

          if (typeof T.TrackColor !== "undefined") {
            color = T.TrackColor;
          }

          var TrackStyle = {
            color: color,
            weight: 1,
            opacity: 0.75
          };
          _RaceFeatures.OppTrack[TrackIndex] = L.polyline(T.OppTrackPoints, TrackStyle).addTo(map);
        }

        T.LastShow = new Date();
      } else if (Boat.RaceMapFeatures.OppTrack && Boat.RaceMapFeatures.OppTrack[TrackIndex]) {
        Boat.RaceMapFeatures.OppTrack[TrackIndex].remove();
      }
    }
  }
}

function CompareDist(a, b) {
  if (a.dnm < b.dnm) return -1;
  if (a.dnm > b.dnm) return 1;
  return 0;
}

function GetClosestOpps(Boat, NbOpps) {
  var RaceId = null;

  if (Boat && Boat.VLMInfo) {
    RaceId = Boat.VLMInfo.RAC;
  }

  var RetArray = [];

  if (RaceId && Rankings[RaceId]) {
    var CurBoat = Rankings[RaceId][Boat.IdBoat];

    if (typeof CurBoat === 'undefined' || !Boat) {
      CurBoat = {
        dnm: 0,
        nwp: 1
      };
    }

    var CurDnm = parseFloat(CurBoat.dnm);
    var CurWP = CurBoat.nwp;
    var List = [];

    for (var _index38 in Rankings[RaceId]) {
      if (Rankings[RaceId][_index38]) {
        if (CurWP === Rankings[RaceId][_index38].nwp) {
          var O = {
            id: _index38,
            dnm: Math.abs(CurDnm - parseFloat(Rankings[RaceId][_index38].dnm))
          };
          List.push(O);
        }
      }
    }

    List = List.sort(CompareDist);

    for (var _index39 in List.slice(0, NbOpps - 1)) {
      RetArray[List[_index39].id] = Rankings[RaceId][List[_index39].id];
    }
  }

  return RetArray;
}

function AddOpponent(Boat, RaceFeatures, Opponent, isFriend) {
  var Opp_Coords = [Opponent.latitude, Opponent.longitude];
  var ZFactor = map.getZoom();
  var OppData = {
    "name": Opponent.idusers,
    "Coords": new VLMPosition(Opponent.longitude, Opponent.latitude).toString(),
    "type": 'opponent',
    "idboat": Opponent.idusers,
    "rank": Opponent.rank,
    "Last1h": Opponent.last1h,
    "Last3h": Opponent.last3h,
    "Last24h": Opponent.last24h,
    "IsTeam": Opponent.country == Boat.VLMInfo.CNT ? "team" : "",
    "IsFriend": isFriend ? ZFactor * 2 : ZFactor,
    "color": Opponent.color
  };

  if (!VLM2Prefs.MapPrefs.ShowOppNumbers) {
    OppData.name = "";
  }

  if (typeof RaceFeatures.Opponents === "undefined") {
    RaceFeatures.Opponents = [];
  }

  if (RaceFeatures.Opponents[Opponent.idusers]) {
    RaceFeatures.Opponents[Opponent.idusers].setLatLng(Opp_Coords);
  } else {
    var OppMarker = GetOpponentMarker(OppData);
    RaceFeatures.Opponents[Opponent.idusers] = L.marker(Opp_Coords, {
      icon: OppMarker
    }).addTo(map);
    RaceFeatures.Opponents[Opponent.idusers].on('click', HandleOpponentClick);
    RaceFeatures.Opponents[Opponent.idusers].on('mouseover', HandleOpponentOver);
    RaceFeatures.Opponents[Opponent.idusers].IdUsers = Opponent.idusers;
  }
}

function ShowOpponentPopupInfo(e) {
  var Opp = e.sourceTarget;

  if (Opp && Opp.options && Opp.options.icon && typeof Opp.options.icon.MarkerOppId !== "undefined") {
    var _Boat2 = GetOppBoat(Opp.options.icon.MarkerOppId);

    if (_Boat2) {
      var Pos = new VLMPosition(_Boat2.longitude, _Boat2.latitude);
      var Features = GetRaceMapFeatures(_CurPlayer.CurBoat);

      if (Features) {
        var PopupStr = BuildBoatPopupInfo(_Boat2);

        if (!Features.OppPopup) {
          Features.OppPopup = L.popup(PopupStr);
        } else {
          Features.OppPopup.setContent(PopupStr);
        }

        if (Features.OppPopup.PrevOpp) {
          Features.OppPopup.PrevOpp.unbindPopup(Features.OppPopup);
        }

        Opp.bindPopup(Features.OppPopup).openPopup();
        Features.OppPopup.PrevOpp = Opp;
        var PopupFields = [];
        var OppId = Opp.options.icon.MarkerOppId;
        Opp.openPopup();
        PopupFields.push([FIELD_MAPPING_TEXT, "#__BoatName" + OppId, _Boat2.boatname]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__BoatId" + OppId, _Boat2.idusers]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__BoatRank" + OppId, _Boat2.rank]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__BoatLoch" + OppId, RoundPow(parseFloat(_Boat2.loch), 2)]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__BoatNWP" + OppId, "[" + _Boat2.nwp + "] " + RoundPow(parseFloat(_Boat2.dnm), 2)]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__BoatPosition" + OppId, Pos.GetVLMString()]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__Boat1HAvg" + OppId, RoundPow(parseFloat(_Boat2.last1h), 2)]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__Boat3HAvg" + OppId, RoundPow(parseFloat(_Boat2.last3h), 2)]);
        PopupFields.push([FIELD_MAPPING_TEXT, "#__Boat24HAvg" + OppId, RoundPow(parseFloat(_Boat2.last24h), 2)]);
        PopupFields.push([FIELD_MAPPING_STYLE, "#__BoatColor" + OppId, "background-color", SafeHTMLColor(_Boat2.color)]);
        FillFieldsFromMappingTable(PopupFields);
      }
    }
  }
}

function GetOppBoat(BoatId) {
  var CurBoat = _CurPlayer.CurBoat;

  if (typeof CurBoat !== "undefined" && CurBoat && CurBoat.OppList) {
    for (var i in CurBoat.OppList) {
      if (CurBoat.OppList[i]) {
        var Opp = CurBoat.OppList[i];

        if (Opp.idusers === BoatId) {
          return Opp;
        }
      }
    }

    if (CurBoat.Reals && CurBoat.Reals.ranking) {
      for (var _i2 in CurBoat.Reals.ranking) {
        if (CurBoat.Reals.ranking[_i2]) {
          var _Opp2 = CurBoat.Reals.ranking[_i2];

          if (_Opp2.idusers === BoatId) {
            return _Opp2;
          }
        }
      }
    }
  }

  return null;
}

function BuildBoatPopupInfo(Boat) {
  if (!Boat || !Boat.idusers) {
    return null;
  }

  var BoatId = Boat.idusers;
  var RetStr = '<div class="MapPopup_InfoHeader">' + GetCountryFlagImgHTML(Boat.country) + ' <span id="__BoatName' + BoatId + '" class="PopupBoatNameNumber ">BoatName</span>' + ' <span id="__BoatId' + BoatId + '" class="PopupBoatNameNumber ">BoatNumber</span>' + ' <div id="__BoatRank' + BoatId + '" class="TxtRank">Rank</div>' + '</div>' + '<div id="__BoatColor' + BoatId + '" style="height: 2px;"></div>' + '<div class="MapPopup_InfoBody">' + ' <fieldset>' + '   <span class="PopupHeadText " I18n="loch">' + GetLocalizedString('loch') + '</span><span class="PopupText"> : </span><span id="__BoatLoch' + BoatId + '" class="loch PopupText">0.9563544</span>' + '   <BR><span class="PopupHeadText " I18n="position">' + GetLocalizedString('position') + '</span><span class="PopupText"> : </span><span id="__BoatPosition' + BoatId + '" class=" PopupText">0.9563544</span>' + '   <BR><span class="PopupHeadText " I18n="NextWP">' + GetLocalizedString('NextWP') + '</span><span class="strong"> : </span><span id="__BoatNWP' + BoatId + '" class="PopupText">[1] 4.531856536865234</span>' + '   <BR><span class="PopupHeadText " I18n="Moyennes">' + GetLocalizedString('Moyennes') + ' </span><span class="PopupText"> : </span>' + '   <span class="PopupHeadText ">[1h]</span><span id="__Boat1HAvg' + BoatId + '" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span>' + '   <span class="PopupHeadText ">[3h]</span><span id="__Boat3HAvg' + BoatId + '" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span>' + '   <span class="PopupHeadText ">[24h]</span><span id="__Boat24HAvg' + BoatId + '" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span>' + ' </fieldset>' + '</div>';
  return RetStr;
}

function HandleOpponentOver(e) {
  var Opponent = e.sourceTarget;
  var index;
  var RaceFeatures = GetRaceMapFeatures(_CurPlayer.CurBoat);
  var OppIndex = Opponent.IdUsers;

  if (OppIndex) {
    for (index in RaceFeatures.OppTrack) {
      var ShowTrack = index === OppIndex;
      _CurPlayer.CurBoat.OppTrack[index].Visible = ShowTrack;
    }

    DrawOpponentTrack(OppIndex, RaceFeatures.Opponents[OppIndex]);
  }
}

function HandleOpponentClick(e) {
  // Clicking oppenent will show the track, and popup info (later)
  HandleOpponentOver(e);
  ShowOpponentPopupInfo(e);
}

function HandleFeatureOut(e) {
  if (typeof _CurPlayer === "undefined" || !_CurPlayer || typeof _CurPlayer.CurBoat === "undefined" || !_CurPlayer.CurBoat || typeof _CurPlayer.CurBoat.OppTrack === "undefined") {
    return;
  } // Clear previously displayed tracks.


  for (var _index40 in _CurPlayer.CurBoat.OppTrack) {
    _CurPlayer.CurBoat.OppTrack[_index40].Visible = false;
  }
}

var TrackPendingRequests = [];
var LastTrackRequest = 0;

function DrawOpponentTrack(IdBoat, OppInfo) {
  var B = _CurPlayer.CurBoat;
  var CurDate = new Date();
  var PendingID = null;

  if (typeof B !== "undefined" && B && CurDate > LastTrackRequest) {
    LastTrackRequest = new Date(CurDate / 1000 + 0.5);

    if (typeof B.OppTrack !== "undefined" || !(IdBoat in B.OppTrack) || IdBoat in B.OppTrack && B.OppTrack[IdBoat].LastShow <= new Date(B.VLMInfo.LUP * 1000)) {
      var _StartTime = new Date() / 1000 - 48 * 3600;

      var IdRace = B.VLMInfo.RAC;

      var _CurDate = new Date();

      PendingID = IdBoat.toString() + "/" + IdRace.toString();

      if (IdBoat in B.OppTrack) {
        B.OppTrack[IdBoat].Visible = true;
      }

      if (!(PendingID in TrackPendingRequests) || _CurDate > TrackPendingRequests[PendingID]) {
        TrackPendingRequests[PendingID] = new Date(_CurDate.getTime() + 60 * 1000);
        console.log("GetTrack " + PendingID + " " + _StartTime);

        if (parseInt(IdBoat) > 0) {
          GetBoatTrack(B, IdBoat, IdRace, _StartTime, OppInfo);
        } else if (parseInt(IdBoat)) {
          GetRealBoatTrack(B, IdBoat, IdRace, _StartTime, OppInfo);
        }
      }
    } else {
      console.log(" GetTrack ignore before next update" + PendingID + " " + StartTime);
    }

    DrawBoat(B);
  }
}

function GetRealBoatTrack(Boat, IdBoat, IdRace, StartTime, OppInfo) {
  $.get("/ws/realinfo/tracks.php?idr=" + IdRace + "&idreals=" + -IdBoat + "&starttime=" + StartTime, function (e) {
    if (e.success) {
      AddBoatOppTrackPoints(Boat, IdBoat, e.tracks, OppInfo.color);
      RefreshCurrentBoat(false, false);
    }
  });
}

var TrackRequestPending = false;

function GetBoatTrack(Boat, IdBoat, IdRace, StartTime, OppInfo) {
  if (TrackRequestPending) {
    return;
  } else {
    TrackRequestPending = true;
  }

  $.get("/ws/boatinfo/smarttracks.php?idu=" + IdBoat + "&idr=" + IdRace + "&starttime=" + StartTime, function (e) {
    TrackRequestPending = false;

    if (e.success) {
      var index;
      AddBoatOppTrackPoints(Boat, IdBoat, e.tracks, OppInfo.Color);

      for (index in e.tracks_url) {
        if (index > 10) {
          break;
        }
        /* jshint -W083*/


        $.get('/cache/tracks/' + e.tracks_url[index], function (e) {
          if (e.success) {
            AddBoatOppTrackPoints(Boat, IdBoat, e.tracks, OppInfo.Color);
            DrawOpponents(Boat);
          }
        });
        /* jshint +W083*/
      }

      DrawOpponents(Boat);
    }
  });
}

function AddBoatOppTrackPoints(Boat, IdBoat, Track, TrackColor) {
  if (!(IdBoat in Boat.OppTrack)) {
    TrackColor = SafeHTMLColor(TrackColor);
    Boat.OppTrack[IdBoat] = {
      LastShow: 0,
      TrackColor: TrackColor,
      DatePos: [],
      Visible: true,
      OppTrackPoints: null
    };
  } //


  for (var _index41 in Track) {
    var Pos = Track[_index41];
    Boat.OppTrack[IdBoat].DatePos[Pos[0]] = {
      lat: Pos[2] / 1000,
      lon: Pos[1] / 1000
    };
  }

  Boat.OppTrack[IdBoat].LastShow = 0;
  Boat.OppTrack[IdBoat].OppTrackPoints = [];
  Boat.OppTrack[IdBoat].OppTrackPoints = null;
}

function DeletePilotOrder(Boat, OrderId) {
  $.post("/ws/boatsetup/pilototo_delete.php?", "parms=" + JSON.stringify({
    idu: Boat.IdBoat,
    taskid: parseInt(OrderId)
  }), function (e) {
    if (e.success) {
      RefreshCurrentBoat(false, true, 'AutoPilot');
    }
  });
}

function UpdateBoatPrefs(Boat, NewVals) {
  NewVals.idu = Boat.IdBoat;
  $.post("/ws/boatsetup/prefs_set.php", "parms=" + JSON.stringify(NewVals), function (e) {
    if (e.success) {
      // avoid forced full round trip
      RefreshCurrentBoat(false, false);
    } else {
      VLMAlertDanger(GetLocalizedString("UpdateFailed"));
    }
  });
}

function LoadVLMPrefs() {
  var Boat;

  if (typeof _CurPlayer === "undefined") {
    return;
  }

  Boat = _CurPlayer.CurBoat;
  SetDDTheme(VLM2Prefs.CurTheme);
  $.get("/ws/boatinfo/prefs.php?idu=" + Boat.IdBoat, HandlePrefsLoaded);
}

function HandlePrefsLoaded(e) {
  if (e.success) {
    var Boat = _CurPlayer.CurBoat;
    Boat.VLMPrefs = e.prefs;
    VLM2Prefs.UpdateVLMPrefs(e.prefs);
  } else {
    VLMAlertDanger("Error communicating with VLM, try reloading the browser page...");
  }
}

function HandleWPDragEnded(e) {
  var bkp = 0;
  var Marker = _CurPlayer.CurBoat.RaceMapFeatures.TrackWP;
  CompleteWPSetPosition(Marker);
  VLMAlertInfo("User WP moved to " + Marker.getLatLng());
} //require [converse];


function InitXmpp() {
  converse.initialize({
    bosh_service_url: 'https://bind.conversejs.org',
    // Please use this connection manager only for testing purposes
    i18n: locales.en,
    // Refer to ./locale/locales.js to see which locales are supported
    show_controlbox_by_default: true,
    roster_groups: true
  });
}
=======
"use strict";function _classCallCheck(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function _typeof(e){return(_typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}var MAP_OP_SHOW_SEL=0,VLM2Prefs=new PrefMgr;function LoadLocalPref(e,t){var a=store.get(e);return void 0===a&&(a=t),a}function PrefMgr(){this.MapPrefs=new MapPrefs,this.CurTheme="bleu-noir",this.MapPrefs=new MapPrefs,this.Init=function(){this.MapPrefs.Load(),this.Load()},this.Load=function(){store.enabled&&(this.CurTheme=LoadLocalPref("CurTheme","bleu-noir"))},this.Save=function(){store.enabled&&store.set("ColorTheme",this.CurTheme),this.MapPrefs.Save()},this.UpdateVLMPrefs=function(e){switch(e.mapOpponents){case"mylist":case"mapselboats":case"NULL":case"null":case"all":this.MapPrefs.MapOppShow=this.MapPrefs.MapOppShowOptions.ShowSel;break;case"meandtop10":this.MapPrefs.MapOppShow=this.MapPrefs.MapOppShowOptions.ShowTop10;break;case"my10opps":this.MapPrefs.MapOppShow=this.MapPrefs.MapOppShowOptions.Show10Around;break;case"my5opps":case"maponlyme":this.MapPrefs.MapOppShow=this.MapPrefs.MapOppShowOptions.Show5Around;break;case"myboat":this.MapPrefs.MapOppShow=this.MapPrefs.MapOppShowOptions.ShowMineOnly;break;default:VLMAlertDanger("unexepected mapping option : "+e.mapOpponents)}}}function MapPrefs(){this.ShowReals=!0,this.ShowOppNumbers=!0,this.MapOppShow=null,this.MapOppShowOptions={ShowSel:0,ShowMineOnly:1,Show5Around:2,ShowTop10:3,Show10Around:4},this.WindArrowsSpacing=64,this.MapZoomLevel=4,this.PolarVacCount=12,this.UseUTC=!1,this.EstTrackMouse=!1,this.TrackEstForecast=!0,this.ShowTopCount=50,this.Load=function(){store.enabled&&(this.ShowReals=LoadLocalPref("#ShowReals",!0),this.ShowOppNumbers=LoadLocalPref("#ShowOppNumbers",!1),this.MapZoomLevel=LoadLocalPref("#MapZoomLevel",4),this.UseUTC=LoadLocalPref("#UseUTC",!1),this.EstTrackMouse=LoadLocalPref("#EstTrackMouse",!0),this.TrackEstForecast=LoadLocalPref("#TrackEstForecast",!1),this.PolarVacCount=LoadLocalPref("#PolarVacCount",12),this.PolarVacCount||(this.PolarVacCount=12),this.ShowTopCount=LoadLocalPref("ShowTopCount",50))},this.Save=function(){store.enabled&&(store.set("#ShowReals",this.ShowReals),store.set("#ShowOppNumbers",this.ShowOppName),store.set("#MapZoomLevel",this.MapZoomLevel),store.set("#PolarVacCount",this.PolarVacCount),store.set("#UseUTC",this.UseUTC),store.set("#TrackEstForecast",this.TrackEstForecast),store.set("#EstTrackMouse",this.EstTrackMouse),store.set("ShowTopCount",this.ShowTopCount));var e="mapselboats";switch(this.MapOppShow){case this.MapOppShowOptions.ShowMineOnly:e="myboat";break;case this.MapOppShowOptions.Show5Around:e="my5opps";break;case this.MapOppShowOptions.ShowTop10:e="meandtop10";break;case this.MapOppShowOptions.Show10Around:e="my10opps"}var t={mapOpponents:e};void 0!==_CurPlayer&&_CurPlayer&&UpdateBoatPrefs(_CurPlayer.CurBoat,{prefs:t})},this.GetOppModeString=function(e){switch(e){case this.MapOppShowOptions.ShowSel:return GetLocalizedString("mapselboats");case this.MapOppShowOptions.ShowMineOnly:return GetLocalizedString("maponlyme");case this.MapOppShowOptions.Show5Around:return GetLocalizedString("mapmy5opps");case this.MapOppShowOptions.ShowTop10:return GetLocalizedString("mapmeandtop10");case this.MapOppShowOptions.Show10Around:return GetLocalizedString("mapmy10opps");default:return e}}}function AutoPilotOrder(e,t){if(this.Date=new Date((new Date).getTime()-(new Date).getTime()%3e5+45e4),this.PIM=PM_HEADING,this.PIP_Value=0,this.PIP_Coords=new VLMPosition(0,0),this.PIP_WPAngle=-1,this.ID=-1,void 0!==e&&e){if(!(t-1 in e.VLMInfo.PIL))return void alert("Invalid Pilototo order number. Report error to devs.");var a=e.VLMInfo.PIL[t-1];switch(this.Date=new Date(1e3*parseInt(a.TTS,10)),this.PIM=parseInt(a.PIM,10),this.ID=parseInt(a.TID,10),this.PIM){case PM_ANGLE:case PM_HEADING:this.PIP_Value=parseInt(a.PIP,10);break;case PM_ORTHO:case PM_VMG:case PM_VBVMG:var o=a.PIP.split(","),n=o[1].split("@");this.PIP_Coords.Lat.Value=parseFloat(o[0]),this.PIP_Coords.Lon.Value=parseFloat(n[0]),this.PIP_WPAngle=parseFloat(n[1])}}this.GetOrderDateString=function(){return this.Date.getDate()+"/"+(this.Date.getMonth()+1)+"/"+this.Date.getFullYear()},this.GetOrderTimeString=function(){return this.Date.getHours()+":"+this.Date.getMinutes()+":15"},this.GetPIMString=function(){switch(this.PIM){case PM_HEADING:return GetLocalizedString("autopilotengaged");case PM_ANGLE:return GetLocalizedString("constantengaged");case PM_ORTHO:return GetLocalizedString("orthodromic");case PM_VMG:return"VMG";case PM_VBVMG:return"VBVMG"}},this.GetPIPString=function(){switch(this.PIM){case PM_HEADING:case PM_ANGLE:return this.PIP_Value;case PM_ORTHO:case PM_VMG:case PM_VBVMG:return this.PIP_Coords.GetVLMString()+"@"+PIP_WPAngle}}}function HandleSendAPUpdate(e){var t="add";if(void 0!==_CurAPOrder&&_CurAPOrder){var a={idu:_CurPlayer.CurBoat.IdBoat,tasktime:Math.round(_CurAPOrder.Date/1e3),pim:_CurAPOrder.PIM};switch(-1!==_CurAPOrder.ID&&(t="update",a.taskid=_CurAPOrder.ID),_CurAPOrder.PIM){case PM_HEADING:case PM_ANGLE:a.pip=_CurAPOrder.PIP_Value;break;case PM_ORTHO:case PM_VMG:case PM_VBVMG:a.pip={},a.pip.targetlat=_CurAPOrder.PIP_Coords.Lat.Value,a.pip.targetlong=_CurAPOrder.PIP_Coords.Lon.Value,a.pip.targetandhdg=-1===_CurAPOrder.PIP_WPAngle?null:_CurAPOrder.PIP_WPAngle}$.post("/ws/boatsetup/pilototo_"+t+".php","parms="+JSON.stringify(a),function(e){e.success?RefreshCurrentBoat(!1,!0,"AutoPilot"):alert(e.error.msg)})}}function HandleAPFieldChange(e){var t=e.target;if(void 0!==t.attributes.id)switch(t.attributes.id.value){case"AP_PIP":_CurAPOrder.PIP_Value=parseFloat(t.value),_CurAPOrder.PIP_Value.toString()!==t.Value&&(t.value=_CurAPOrder.PIP_Value.toString());break;case"AP_WPLat":CheckFloatInput(_CurAPOrder.PIP_Coords.Lat,t);break;case"AP_WPLon":CheckFloatInput(_CurAPOrder.PIP_Coords.Lon,t);break;case"AP_WPAt":var a={};a.Value=_CurAPOrder.PIP_WPAngle,CheckFloatInput(a,t),_CurAPOrder.PIP_WPAngle=a.Value}}function CheckFloatInput(e,t){var a;"object"===_typeof(e)?(e.Value=parseFloat(t.value),a=e.Value):a=e=parseFloat(t.value),a.toString()!==t.Value&&(t.value=a.toString())}function BoatEstimate(e){this.Position=null,this.Date=null,this.PrevDate=null,this.Mode=null,this.Value=null,this.Meteo=null,this.CurWP=new VLMPosition(0,0),this.HdgAtWP=-1,this.RaceWP=1,this.Heading=null,void 0!==e&&e&&(this.Position=new VLMPosition(e.Position.Lon.Value,e.Position.Lat.Value),this.Date=new Date(e.Date),this.PrevDate=new Date(e.PrevDate),this.Mode=e.Mode,this.Value=e.Value,void 0!==e.Meteo&&e.Meteo&&(this.Meteo=new WindData({Speed:e.Meteo.Speed,Heading:e.Meteo.Heading})),this.CurWP=e.CurWP,this.RaceWP=e.RaceWP,this.Heading=e.Heading)}function Estimator(e){if(void 0===e||!e)throw"Boat must exist for tracking....";this.Boat=e,this.MaxVacEstimate=0,this.CurEstimate=new BoatEstimate,this.Running=!1,this.EstimateTrack=[],this.ProgressCallBack=null,this.ErrorCount=0,this.EstimateMapFeatures=[],this.Stop=function(){this.Running&&(this.Running=!1,this.ReportProgress(!0),DrawBoat(this.Boat))},this.Start=function(e){if(this.ProgressCallBack=e,!this.Running)if(this.Running=!0,GribMgr.Init(),void 0!==this.Boat.VLMInfo){if(this.CurEstimate.Position=new VLMPosition(this.Boat.VLMInfo.LON,this.Boat.VLMInfo.LAT),this.CurEstimate.Date=new Date(1e3*this.Boat.VLMInfo.LUP+1e3*this.Boat.VLMInfo.VAC),this.CurEstimate.PrevDate=this.CurEstimate.Date,this.CurEstimate.Date<new Date)if(void 0===this.Boat.RaceInfo)this.CurEstimate.Date=new Date;else{if(this.CurEstimate.PrevDate=new Date(1e3*parseInt(this.Boat.RaceInfo.deptime,10)+6e3),this.CurEstimate.PrevDate<new Date){var t=(new Date).getTime()/1e3;t-=t%this.Boat.VLMInfo.VAC,this.CurEstimate.PrevDate=new Date(1e3*t+6e3)}var a=new Date(this.CurEstimate.PrevDate.getTime()+1e3*this.Boat.VLMInfo.VAC);this.CurEstimate.Date=a}for(var o in this.CurEstimate.Mode=parseInt(this.Boat.VLMInfo.PIM,10),this.CurEstimate.CurWP=new VLMPosition(this.Boat.VLMInfo.WPLON,this.Boat.VLMInfo.WPLAT),this.CurEstimate.HdgAtWP=parseFloat(this.Boat.VLMInfo["H@WP"]),this.CurEstimate.RaceWP=parseInt(this.Boat.VLMInfo.NWP,10),this.CurEstimate.Mode!=PM_HEADING&&this.CurEstimate.Mode!=PM_ANGLE||(this.CurEstimate.Value=parseFloat(this.Boat.VLMInfo.PIP)),this.CurEstimate.PilOrders=[],this.Boat.VLMInfo.PIL){var n=this.Boat.VLMInfo.PIL[o],i={PIP:n.PIP,PIM:n.PIM,STS:n.STS,TTS:n.TTS};this.CurEstimate.PilOrders.push(i)}this.EstimateTrack=[],this.MaxVacEstimate=new Date(GribMgr.MaxWindStamp),this.ReportProgress(!1),this.EstimateTrack.push(new BoatEstimate(this.CurEstimate)),this.ErrorCount=0,setTimeout(this.Estimate.bind(this),0)}else this.Stop()},this.Estimate=function(e){if(!this.Running||this.CurEstimate.Date>=this.MaxVacEstimate)this.Stop();else{var t,a=this.CurEstimate.Position.Lat.Value,o=this.CurEstimate.Position.Lon.Value;do{if(!(t=GribMgr.WindAtPointInTime(this.CurEstimate.PrevDate,a,o)))return this.ErrorCount>10?void this.Stop():(this.ErrorCount++,void setTimeout(this.Estimate.bind(this),1e3));if(this.ErrorCount=0,isNaN(t.Speed)){alert("Looping on NaN WindSpeed")}}while(isNaN(t.Speed));for(var n in this.CurEstimate.Meteo=t,this.CurEstimate.PilOrders){var i=this.CurEstimate.PilOrders[n];if(i&&"pending"===i.STS)if(new Date(1e3*parseInt(i.TTS,10))<=this.CurEstimate.Date){switch(this.CurEstimate.Mode=parseInt(i.PIM,10),this.CurEstimate.Mode){case PM_ANGLE:case PM_HEADING:this.CurEstimate.Value=parseFloat(i.PIP);break;case PM_ORTHO:case PM_VMG:case PM_VBVMG:var r=i.PIP.split("@"),s=r[0].split(",");this.CurEstimate.CurWP=new VLMPosition(parseFloat(s[1]),parseFloat(s[0])),this.CurEstimate.HdgAtWP=parseFloat(r[1]);break;default:return alert("unsupported pilototo mode"),void this.Stop()}this.CurEstimate.PilOrders[n]=null;break}}var l=this.CurEstimate.Value,u=0,d=null,c=null;switch(this.CurEstimate.Mode){case PM_ANGLE:if(l=t.Heading+this.CurEstimate.Value,u=PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,t.Speed,t.Heading,l),isNaN(u))return VLMAlertDanger("PM_ANGLE : Error getting boatSpeed try again later..."),void this.Stop();d=this.CurEstimate.Position.ReachDistLoxo(u/3600*this.Boat.VLMInfo.VAC,l);break;case PM_HEADING:if(u=PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,t.Speed,t.Heading,l),isNaN(u))return VLMAlertDanger("PM_ANGLE : Error getting boatSpeed try again later..."),void this.Stop();d=this.CurEstimate.Position.ReachDistLoxo(u/3600*this.Boat.VLMInfo.VAC,l);break;case PM_ORTHO:case PM_VMG:case PM_VBVMG:if(c=this.GetNextWPCoords(this.CurEstimate),this.CurEstimate.Mode==PM_ORTHO){if(l=this.CurEstimate.Position.GetOrthoCourse(c),u=PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,t.Speed,t.Heading,l),isNaN(u))return VLMAlertDanger("PM_ANGLE : Error getting boatSpeed try again later..."),void this.Stop();d=this.CurEstimate.Position.ReachDistOrtho(u/3600*this.Boat.VLMInfo.VAC,l)}else{if(l=this.CurEstimate.Mode==PM_VMG?PolarsManager.GetVMGCourse(this.Boat.VLMInfo.POL,t.Speed,t.Heading,this.CurEstimate.Position,c):PolarsManager.GetVBVMGCourse(this.Boat.VLMInfo.POL,t.Speed,t.Heading,this.CurEstimate.Position,c),u=PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,t.Speed,t.Heading,l),isNaN(u))return VLMAlertDanger("PM_ANGLE : Error getting boatSpeed try again later..."),void this.Stop();d=this.CurEstimate.Position.ReachDistLoxo(u/3600*this.Boat.VLMInfo.VAC,l)}this.CheckWPReached(c,this.CurEstimate.Position,d);break;default:throw"Unsupported pilotmode for estimate..."+this.CurEstimate.Mode}console.log(this.CurEstimate.Date+this.CurEstimate.Position.toString(!0)+"=> "+d.Lon.toString(!0)+" "+d.Lat.toString(!0)+" Wind : "+RoundPow(t.Speed,4)+"@"+RoundPow(t.Heading,4)+" Boat "+RoundPow(u,4)+"kts"+RoundPow((l+360)%360,4));var p=!1;this.CheckGateValidation(d)&&(p=this.GetNextRaceWP()),this.CurEstimate.Heading=l,this.CurEstimate.Position=d,this.EstimateTrack.push(new BoatEstimate(this.CurEstimate)),this.CurEstimate.Date=new Date(1e3*(this.CurEstimate.Date/1e3+this.Boat.VLMInfo.VAC)),this.CurEstimate.PrevDate=this.CurEstimate.Date,p?this.Stop():(setTimeout(this.Estimate.bind(this),0),this.ReportProgress(!1))}},this.GetNextRaceWP=function(){var e=Object.keys(this.Boat.RaceInfo.races_waypoints).length;if(this.CurEstimate.RaceWP===e)return!0;for(var t=this.CurEstimate.RaceWP+1;t<=e;t++)if(!(this.Boat.RaceInfo.races_waypoints[t].wpformat&WP_ICE_GATE)){this.CurEstimate.RaceWP=t;break}return!1},this.CheckGateValidation=function(e){var t=this.GetNextGateSegment(this.CurEstimate),a=(this.Boat.RaceInfo.races_waypoints[this.CurEstimate.RaceWP],{P1:this.CurEstimate.Position,P2:e});return VLMMercatorTransform.SegmentsIntersect(t,a)},this.CheckWPReached=function(e,t,a){if(this.CurEstimate.CurWP.Lat.value||this.CurEstimate.CurWP.Lon.Value){var o=e.GetOrthoDist(t),n=e.GetOrthoDist(a),i=t.GetOrthoDist(a);(o<i||n<i)&&(this.CurEstimate.CurWP=new VLMPosition(0,0),-1!=this.CurEstimate.HdgAtWP&&(this.CurEstimate.Mode=PM_HEADING,this.CurEstimate.Value=this.CurEstimate.HdgAtWP),console.log("WP Reached"))}},this.GetNextWPCoords=function(e){return e.CurWP.Lat.value||e.CurWP.Lon.Value?e.CurWP:this.Boat.GetNextWPPosition(e.RaceWP,e.Position,e.CurWP)},this.GetNextGateSegment=function(e){return this.Boat.GetNextGateSegment(e.RaceWP)},this.ReportProgress=function(e){var t=0;this.ProgressCallBack&&(e||this.EstimateTrack.length>1&&(t=RoundPow(100*(1-(t=(this.MaxVacEstimate-this.EstimateTrack[this.EstimateTrack.length-1].Date)/(this.MaxVacEstimate-this.EstimateTrack[0].Date))),1)),this.ProgressCallBack(e,t,this.CurEstimate.Date))},this.GetClosestEstimatePoint=function(e){return e instanceof VLMPosition?this.GetClosestEstimatePointFromPosition(e):e instanceof Date?this.GetClosestEstimatePointFromTime(e):null},this.GetClosestEstimatePointFromTime=function(e){if(!e||!Object.keys(this.EstimateTrack).length)return null;var t,a=0;for(a=0;a<Object.keys(this.EstimateTrack).length;a++)if(this.EstimateTrack[a]){if(!(e>this.EstimateTrack[a].Date))break;t=e-this.EstimateTrack[a].Date}if(a<Object.keys(this.EstimateTrack).length&&void 0!==this.EstimateTrack[a+1]&&this.EstimateTrack[a+1]){var o=e-this.EstimateTrack[a+1].Date;Math.abs(o)<Math.abs(t)&&a++}return this.EstimateTrack[a]},this.GetClosestEstimatePointFromPosition=function(e){if(!e)return null;var t,a=1e30,o=null;for(t=0;t<Object.keys(this.EstimateTrack).length;t++)if(this.EstimateTrack[t]){var n=e.GetEuclidianDist2(this.EstimateTrack[t].Position);n<a&&(o=this.EstimateTrack[t],a=n)}return o},this.ClearEstimatePosition=function(e){this.ShowEstimatePosition(e,null)},this.ShowEstimatePosition=function(e,t){var a=GetRaceMapFeatures(e);if(e&&t&&t.Position&&(e.VLMInfo.LON!==t.Position.Lon.Value||e.VLMInfo.LAT!==t.Position.Lat.Value)){if(!a)return;var o=[t.Position.Lat.Value,t.Position.Lon.Value];if(a.BoatEstimateMarker)a.BoatEstimateMarker.setLatLng(o).addTo(map);else{var n=GetBoatEstimateMarker();a.BoatEstimateMarker=L.marker(o,{icon:n}).addTo(map)}if(a.BoatEstimateMarker&&a.BoatEstimateMarker.setRotationAngle(t.Heading),void 0!==t.Meteo&&t.Meteo){var i=BuildPolarLine(e,new VLMPosition(o[1],o[0]),VLM2Prefs.MapPrefs.PolarVacCount,t.Date);a.BoatEstimateMarkerPolar=DefinePolarMarker(i,a.BoatEstimateMarkerPolar)}}else a&&(a.BoatEstimateMarker&&a.BoatEstimateMarker.remove(),a.BoatEstimateMarkerPolar&&a.BoatEstimateMarkerPolar.remove())},this.GetEstimateTracks=function(){var e=[],t=null,a=null;if(this.EstimateTrack&&this.EstimateTrack[0]){var o=(new Date).getTime(),n=o-o%216e5+126e5;for(var i in this.EstimateTrack)if(this.EstimateTrack[i]){var r=this.EstimateTrack[i],s=r.Date.getTime()-n,l=Math.floor(s/6/36e5);l<0?l=0:l>2&&(l=2),void 0===e[l]&&(e[l]=[]),l!==t&&a&&e[l].push([a.Position.Lat.Value,a.Position.Lon.Value]),e[l].push([r.Position.Lat.Value,r.Position.Lon.Value]),a=r,t=l}}return e}}function Coords(e,t){this.Value="number"==typeof e?e:parseFloat(e),this.IsLon=t,this.Deg=function(){return Math.abs(this.Value)},this.Min=function(){return 60*(Math.abs(this.Value)-Math.floor(this.Deg()))},this.Sec=function(){return 60*(this.Min()-Math.floor(this.Min()))},this.toString=function(e){if(e)return this.Value;var t="";return t=void 0===this.IsLon||0==this.IsLon?this.Value>=0?" N":" S":this.Value>=0?" E":" W",Math.floor(this.Deg())+"° "+Math.floor(this.Min())+"' "+RoundPow(this.Sec(),2)+'"'+t}}function GetDegMinSecFromNumber(e,t,a,o){SplitNumber(e,t,void 0),SplitNumber(NaN,a,void 0),SplitNumber(NaN,o,void 0)}function SplitNumber(e,t,a){Math.floor(e)}VLM2Prefs.Init();var SrvIndex=1,GribMap={ServerURL:function(){return"undefined"!=typeof WindGridServers&&WindGridServers?(0===(SrvIndex=(SrvIndex+1)%WindGridServers.length)&&(SrvIndex=1),WindGridServers[SrvIndex]):""}},Pixel=function e(t,a){_classCallCheck(this,e),this.x=t,this.y=a,this.moveBy=function(e){this.x+=e.x,this.y+=e.y},this.moveByPolar=function(e,t){var a=(t-90)*Math.PI/180;this.x+=e*Math.cos(a),this.y+=e*Math.sin(a)}};GribMap.Layer=L.Layer.extend({initialize:function(e){e||(e={}),this.cfg=e,this._canvas=L.DomUtil.create("canvas"),this._data=[],this._max=1,this._min=0,this.cfg.container=this._canvas,this._Density=10,this._Time=new Date,this.DrawWindDebugCnt=0,this.DrawWindDebugDepth=0},SetGribMapTime:function(e){this._Time=e,this._update()},_CheckDensity:function(){this._width<500||this.height<500?this._Density=5:this._Density=10},onAdd:function(e){var t=e.getSize();this._map=e,this._width=t.x,this._height=t.y,this._canvas.width=t.x,this._canvas.height=t.y,this._CheckDensity(),this._canvas.style.width=t.x+"px",this._canvas.style.height=t.y+"px",this._canvas.style.position="absolute",this._origin=this._map.layerPointToLatLng(new L.Point(0,0)),e.getPanes().overlayPane.appendChild(this._canvas),e.on("moveend",this._reset,this),this._reset(),this._draw()},addTo:function(e){return e.addLayer(this),this},onRemove:function(e){e.getPanes().overlayPane.removeChild(this._canvas),e.off("moveend",this._reset,this)},_draw:function(){this._map&&this._update()},_update:function(e){var t=this._canvas.getContext("2d");t.clearRect(0,0,this._canvas.width,this._canvas.height),this._DrawWindArea(t,e)},_DrawWindArea:function(e,t){var a=this;this.DrawWindDebugCnt++,this.DrawWindDebugDepth=t?this.DrawWindDebugDepth+1:0;this.arrowstep;var o,n,i=1;if(o=this._map.getBounds(),!((n=this._map.getZoom())<5))for(var r=o.getWest(),s=o.getEast(),l=o.getNorth(),u=o.getSouth(),d=(s-r)/this._Density,c=(l-u)/this._Density,p=L.latLng(l,r),h=map.project(p,n),P=!1,f=null,g=r;g<=s;g+=d)for(var M=u;M<=l;M+=c)try{P||function(){var e=a;(f=GribMgr.WindAtPointInTime(a._Time,M,g,t?null:function(){e._update(!0)}))||(P=!0)}();var m=L.latLng(M,g),C=map.project(m,n);f?this._drawWind(e,C.x-h.x,C.y-h.y,n,f.Speed,f.Heading):this._drawWind(e,C.x-h.x,C.y-h.y,n,0,0)}catch(e){i>0&&(alert("_DrawWindArea "+g+" / "+M+" / <br>"+e),i-=1)}},_drawWind:function(e,t,a,o,n,i){var r=this._drawWindTriangle(e,t,a,n,i);e.fillStyle="#626262",this._drawWindText(e,t,a+r,n,i)},_drawWindText:function(e,t,a,o,n){var i=t,r=a,s="?? / ???";(o||n)&&(r+=n>90&&n<270?13+5*Math.cos(n*Math.PI/180):7-5*Math.cos(n*Math.PI/180),s=RoundPow(o,1)+"/"+RoundPow(n,1)+"°");var l=e.measureText(s).width/2;e.fillText(s,i-l,r)},_drawWindTriangle:function(e,t,a,o,n){var i,r,s,l,u,d,c;if(!n&&!o)return 0;c=Math.log(o+1),d=(n+180)%360,i=new Pixel(t,a),r=new Pixel(t,a),s=new Pixel(t,a),i.moveByPolar(4+4*c,d),r.moveByPolar(0+2*c,d-135),s.moveByPolar(0+2*c,d+135),l=new Pixel((i.x+r.x+s.x)/3,(i.y+r.y+s.y)/3),u=new Pixel(t-l.x,a-l.y),i.moveBy(u),r.moveBy(u),s.moveBy(u);var p=this.windSpeedToColor(o);return e.fillStyle=p,e.strokeStyle=p,e.beginPath(),e.moveTo(i.x,i.y),e.lineTo(r.x,r.y),e.lineTo(s.x,s.y),e.fill(),e.stroke(),e.closePath(),Math.max(i.y,r.y,s.y)},windSpeedToColor:function(e){return e<=1?"#FFFFFF":e<=3?"#9696E1":e<=6?"#508CCD":e<=10?"#3C64B4":e<=15?"#41B464":e<=21?"#B4CD0A":e<=26?"#D2D216":e<=33?"#E1D220":e<=40?"#FFB300":e<=47?"#FF6F00":e<=55?"#FF2B00":e<=63?"#E60000":"#7F0000"},_reset:function(){var e=this._map.containerPointToLayerPoint([0,0]);L.DomUtil.setTransform(this._canvas,e,1);var t=this._map.getSize();this._width!==t.x&&(this._canvas.width=t.x,this._width=t.x,this._CheckDensity()),this._height!==t.y&&(this._canvas.height=t.y,this._height=t.y,this._CheckDensity()),this._draw()},_animateZoom:function(e){var t=this._map.getZoomScale(e.zoom),a=this._map._getCenterOffset(e.center)._multiplyBy(-t).subtract(this._map._getMapPanePos());L.DomUtil.setTransform?L.DomUtil.setTransform(this._canvas,a,t):this._canvas.style[L.DomUtil.TRANSFORM]=L.DomUtil.getTranslateString(a)+" scale("+t+")"}});var GribData=function e(t){_classCallCheck(this,e),this.UGRD=NaN,this.VGRD=NaN,this.TWS=NaN,void 0!==t&&(this.UGRD=t.UGRD,this.VGRD=t.VGRD,this.TWS=t.TWS),this.Strength=function(){return 1.9438445*Math.sqrt(this.UGRD*this.UGRD+this.VGRD*this.VGRD)},this.Direction=function(){var e=Math.sqrt(this.UGRD*this.UGRD+this.VGRD*this.VGRD),t=Math.acos(-this.VGRD/e);return this.UGRD>0&&(t=2*Math.PI-t),(t=t/Math.PI*180%360)<0?t+=360:t>=360&&(t-=360),t}},WindData=function e(t){_classCallCheck(this,e),this.Speed=NaN,this.Heading=NaN,this.IsValid=function(){return!isNaN(this.Speed)&&!isNaN(this.Heading)},void 0!==t&&(this.Speed=t.Speed,this.Heading=t.Heading)},VLM2GribManager=function e(){_classCallCheck(this,e),this.Tables=[],this.TableTimeStamps=[],this.Inited=!1,this.Initing=!1,this.MinWindStamp=0,this.MaxWindStamp=0,this.WindTableLength=0,this.LoadQueue=[],this.GribStep=.5,this.LastGribDate=new Date(0),this.Init=function(){this.Inited||this.Initing||(this.Initing=!0,$.get("/ws/windinfo/list.php?v="+Math.round((new Date).getTime()/1e3/60/3),this.HandleGribList.bind(this)))},this.HandleGribList=function(e){this.TableTimeStamps=e.grib_timestamps,this.Inited=!0,this.Initing=!1,this.MinWindStamp=new Date(1e3*this.TableTimeStamps[0]),this.MaxWindStamp=new Date(1e3*this.TableTimeStamps[this.TableTimeStamps.length-1]),this.WindTableLength=this.TableTimeStamps.length},this.WindAtPointInTime=function(e,t,a,o){if(!this.Inited)return!1;var n=Math.floor((e/1e3-this.MinWindStamp/1e3)/10800);if(n<0)return!1;if(n+1>=this.TableTimeStamps.length)return!1;var i=new WindData;if(Math.abs(t)>85)return i.Heading=0,i.Speed=0,i;var r=this.CheckGribLoaded(n,t,NormalizeLongitudeDeg(a),o),s=this.CheckGribLoaded(n+1,t+this.GribStep,NormalizeLongitudeDeg(a+this.GribStep),o);if(r&&!s&&(s=this.CheckGribLoaded(n+1,t+this.GribStep,NormalizeLongitudeDeg(a+this.GribStep),o)),!r||!s)return!1;var l=this.GetHydbridMeteoAtTimeIndex(n,t,a),u=this.GetHydbridMeteoAtTimeIndex(n+1,t,a),d=l.UGRD,c=l.VGRD,p=u.UGRD,h=u.VGRD,P=e/1e3-this.TableTimeStamps[n],f=new GribData({UGRD:d+P/10800*(p-d),VGRD:c+P/10800*(h-c)});return i.Heading=f.Direction(),i.Speed=l.TWS+P/10800*(u.TWS-l.TWS),i},this.GetHydbridMeteoAtTimeIndex=function(e,t,a){for(var o=a;o<0;)o+=360;for(;o>360;)o-=360;for(var n=t;n<0;)n+=90;for(;n>90;)n-=90;var i=180/this.GribStep+Math.floor(a/this.GribStep),r=90/this.GribStep+Math.floor(t/this.GribStep),s=(i+1)%(360/this.GribStep),l=(r+1)%(180/this.GribStep),u=o/this.GribStep-Math.floor(o/this.GribStep),d=n/this.GribStep-Math.floor(n/this.GribStep),c=this.Tables[e][i][r].UGRD,p=this.Tables[e][i][l].UGRD,h=this.Tables[e][s][r].UGRD,P=this.Tables[e][s][l].UGRD,f=this.Tables[e][i][r].VGRD,g=this.Tables[e][i][l].VGRD,L=this.Tables[e][s][r].VGRD,M=this.Tables[e][s][l].VGRD,m=this.Tables[e][i][r].Strength(),C=this.Tables[e][i][l].Strength(),v=this.Tables[e][s][r].Strength(),I=this.Tables[e][s][l].Strength(),_=this.QuadraticAverage(m,C,v,I,u,d);return new GribData({UGRD:this.QuadraticAverage(c,p,h,P,u,d),VGRD:this.QuadraticAverage(f,g,L,M,u,d),TWS:_})},this.QuadraticAverage=function(e,t,a,o,n,i){var r=e+i*(t-e);return r+n*(a+i*(o-a)-r)},this.CheckGribLoaded=function(e,t,a,o){var n=180/this.GribStep+Math.floor(a/this.GribStep),i=90/this.GribStep+Math.floor(t/this.GribStep),r=180/this.GribStep+Math.ceil(a/this.GribStep),s=90/this.GribStep+Math.ceil(t/this.GribStep);return!!(e in this.Tables&&this.Tables[e][n]&&this.Tables[e][n][i]&&this.Tables[e][n][s]&&this.Tables[e][r]&&this.Tables[e][r][i]&&this.Tables[e][r][s])||(this.CheckGribLoadedIdx(e,n,i,o),this.CheckGribLoadedIdx(e,n,s,o),this.CheckGribLoadedIdx(e,r,i,o),this.CheckGribLoadedIdx(e,r,s,o),!1)},this.CheckGribLoadedIdx=function(e,t,a,o){if(isNaN(t)||isNaN(a));if(!(this.Tables.length&&this.Tables[e]&&this.Tables[e][t]&&this.Tables[e][t][a])){var n,i,r=a*this.GribStep-90,s=t*this.GribStep-180,l=5*Math.floor(r/5),u=5*Math.floor(s/5);r<l?l=(n=l)-10:n=l+10,s<u?u=(i=u)-10:i=u+10,i>180&&(i=180,this.CheckGribLoadedIdx(e,0,a,o)),u<-180&&(u=-180,this.CheckGribLoadedIdx(e,180/this.GribStep-1,a,o));var d="0/"+u+"/"+i+"/"+n+"/"+l;this.AddGribLoadKey(d,n,l,u,i,o)}},this.AddGribLoadKey=function(e,t,a,o,n,i){e in this.LoadQueue?void 0!==i&&i&&this.LoadQueue[e].CallBacks.push(i):(this.LoadQueue[e]={length:0,CallBacks:[i]},$.get(GribMap.ServerURL()+"/ws/windinfo/smartgribs.php?north="+t+"&south="+a+"&west="+o+"&east="+n+"&seed="+(0+new Date),this.HandleGetSmartGribList.bind(this,e)))},this.HandleGetSmartGribList=function(e,t){if(t.success){for(var a in this.LastGribDate!==parseInt(t.GribCacheIndex,10)&&(this.LastGribDate=parseInt(t.GribCacheIndex,10),this.Tables=[],this.Inited=!1,this.Init()),t.gribs_url)if(t.gribs_url[a]){var o=t.gribs_url[a].replace(".grb",".txt");$.get("/cache/gribtiles/"+o+"&v=0",this.HandleSmartGribData.bind(this,e,o))}}else console.log(t)},this.HandleSmartGribData=function(e,t,a){if(this.ProcessInputGribData(t,a,e)&&this.LoadQueue[e]){for(var o in this.LoadQueue[e].CallBacks)this.LoadQueue[e].CallBacks[o]&&this.LoadQueue[e].CallBacks[o]();delete this.LoadQueue[e]}},this.ForceReloadGribCache=function(e,t){$.get("/cache/gribtiles/"+t+"&force=yes&seed=0",this.HandleSmartGribData.bind(this,e,t))},this.ProcessInputGribData=function(e,t,a){var o=t.split("\n"),n=o.length,i=[],r=0;if("--\n"===t)return this.ForceReloadGribCache(a,e),!1;if(-1!==t.search("invalid"))return console.log("invalid request :"+e),!1;for(var s=0;s<n;s++){var l=o[s];if("--"===l){r=s+1;break}l&&-1===l.search("GRID:")&&i.push(this.ProcessCatalogLine(l))}if(!(i.length<this.WindTableLength)){for(var u=e.split("/"),d=0;d<i.length;d++){if(void 0===o[r]||""===o[r]){this.ForceReloadGribCache(a,e);break}for(var c=o[r].split(" "),p=parseInt(c[0],10),h=parseInt(c[1],10),P=180/this.GribStep+parseInt(u[1],10)/this.GribStep,f=0;f<p;f++)for(var g=h+90/this.GribStep+parseInt(u[0],10)/this.GribStep,L=0;L<h;L++){i[d].DateIndex in this.Tables||(this.Tables[i[d].DateIndex]=[]);var M=this.Tables[i[d].DateIndex];P+f in M||(M[P+f]=[]),g-L-1 in M[P+f]||(M[P+f][g-L-1]=null);var m=this.Tables[i[d].DateIndex][P+f][g-L-1];void 0!==m&&m||(m=new GribData,this.Tables[i[d].DateIndex][P+f][g-L-1]=m),m[i[d].Type]=parseFloat(o[r+1+L*p+f])}r+=p*h+1}return!0}this.ForceReloadGribCache(a,e)},this.ProcessCatalogLine=function(e){var t=new WindCatalogLine,a=e.split(":");return t.Type=a[3],void 0===a[12]||"anl"===a[12]?t.DateIndex=0:t.DateIndex=parseInt(a[12].substring(0,a[12].indexOf("hr")),10)/3,t}},WindCatalogLine=function e(){_classCallCheck(this,e),this.Type="",this.DateIndex=0},WindTable=function e(){_classCallCheck(this,e),this.GribStep=.5,this.Table=[],this.TableDate=0,this.Init=function(e){for(lat=-90;lat<=90;lat+=this.GribStep)for(lon=-90;lon<=90;lon+=this.GribStep)this.Table[lat][lon]=null}},GribMgr=new VLM2GribManager;function HandleGribTestClick(e){for(var t=_CurPlayer.CurBoat,a=0;a<=0;a++){var o=new Date(1e3*t.VLMInfo.LUP+a*t.VLMInfo.VAC*1e3),n=GribMgr.WindAtPointInTime(o,t.VLMInfo.LAT,t.VLMInfo.LON);n?console.log(o+" "+n.Speed+"@"+n.Heading):console.log("no meteo yet at time : "+o)}}GribMgr.Init();var RACE_TYPE_CLASSIC=0,RACE_TYPE_RECORD=1,RACE_TYPE_OMORMB=2,FIELD_MAPPING_TEXT=0,FIELD_MAPPING_VALUE=1,FIELD_MAPPING_CHECK=2,FIELD_MAPPING_IMG=3,FIELD_MAPPING_CALLBACK=4,FIELD_MAPPING_STYLE=5,MAX_PILOT_ORDERS=5,BoatRacingStatus=["RAC","CST","LOC","DNS"],BoatArrivedStatus=["ARR"],BoatNotRacingStatus=["DNF","HC","HTP"],BoatRacingClasses={RAC:"ft_class_racing",CST:"ft_class_oncoast",LOC:"ft_class_locked",DNS:"ft_class_dns"},SetWPPending=!1,WPPendingTarget=null,GribWindController=null,map=null,VLMBoatsLayer=null,Rankings=[],PilototoFt=null,RankingFt=null,RaceHistFt=null,ICS_WPft=null,NSZ_WPft=null,VLMINdexFt=null,RC_PwdResetReq=null,RC_PwdResetConfirm=null,OnPlayerLoadedCallBack=null;$(document).ready(function(){$.ajaxSetup({error:function(e,t,a){401===e.status||403===e.status?window.location.replace("/"):404===e.status||VLMAlertDanger("An error occurred: "+t+"nError: "+a)}}),LeafletInit(),InitLocale(),InitMenusAndButtons(),PolarsManager.Init(),InitAlerts(),CheckPageParameters(),setInterval(PageClock,1e3),GetFlagsList()});var COMPASS_SIZE=350;function LeafletInit(){map=L.map("jVlmMap").setView([0,0],8);var e=tileUrlSrv;L.tileLayer(e,{attribution:"gshhsv2",maxZoom:20,tms:!1,id:"vlm",detectRetina:!0,subdomains:tilesUrlArray}).addTo(map),map.GribMap=(new GribMap.Layer).addTo(map),map.Compass=new L.marker([0,0],{icon:new L.icon({iconSize:[350,341],iconAnchor:[175,170],iconUrl:"images/compas-transparent.gif"}),draggable:!0}).addTo(map),map.Compass.on("dragend",HandleCompassDragEnd),map.Compass.on("mousemove",HandleCompassMouseMove),map.Compass.on("mouseout",HandleCompassMouseOut),map.on("mousemove",HandleMapMouseMove),map.on("moveend",HandleMapGridZoom),map.on("click",HandleMapMouseClick),map.on("zoomend",HandleMapGridZoom)}function HandleCompassMouseOut(e){map.Compass.dragging.enable()}function HandleCompassMouseMove(e){var t=map.getZoom(),a=map.project(map.Compass.getLatLng(),t),o=map.project(map.mouseEventToLatLng(e.originalEvent),t),n=a.x-o.x,i=a.y-o.y;n*n+i*i<COMPASS_SIZE*COMPASS_SIZE/8?map.Compass.dragging.disable():map.Compass.dragging.enable()}function HandleCompassDragEnd(e){if(_CurPlayer&&_CurPlayer.CurBoat&&_CurPlayer.CurBoat.VLMInfo.LAT&&_CurPlayer.CurBoat.VLMInfo.LON){var t=_CurPlayer.CurBoat,a=[_CurPlayer.CurBoat.VLMInfo.LAT,_CurPlayer.CurBoat.VLMInfo.LON],o=map.Compass.getLatLng(),n=GetRaceMapFeatures(t);n.Compass||(n.Compass={});var i=map.getZoom(),r=map.project(a,i),s=map.project(o,i);Math.abs(r.x-s.x)<BOAT_MARKET_SIZE/2&&Math.abs(r.y-s.y)<BOAT_MARKET_SIZE/2?(n.Compass.Lat=-1,n.Compass.Lon=-1):(n.Compass.Lat=o.lat,n.Compass.Lon=o.lng)}}function HandleMapGridZoom(e){var t=e.sourceTarget,a=(t.getZoom(),t.getBounds()),o=a._northEast.lng-a._southWest.lng,n=a._northEast.lat-a._southWest.lat,i=o;n<o&&(i=n),(i=Math.pow(.25,Math.ceil(Math.log(i)/Math.log(.25))))>5?i=Math.pow(5,Math.floor(Math.log(i)/Math.log(5))):i<.25&&(i=.25),void 0===t.GridLayer?(t.Grid=[],t.GridLayer=L.layerGroup().addTo(t)):t.GridLayer.clearLayers();for(var r={weight:1,opacity:.4,color:"black"},s={permanent:!0,opacity:.4,offset:[0,-10]},l={permanent:!0,opacity:.4,offset:[0,30]},u={permanent:!0,opacity:.4,offset:[10,0]},d={permanent:!0,opacity:.4,offset:[-10,0]},c=0,p=Math.floor(a._southWest.lng);p<=a._northEast.lng;p+=i){var h=[[a._southWest.lat,p],[a._northEast.lat,p]];t.Grid[c]=L.polyline(h,r),t.GridLayer.addLayer(t.Grid[c++]);var P=RoundPow(4*p,0)/4;t.Grid[c]=L.circleMarker(h[0],{radius:1}).bindTooltip(""+P,s),t.GridLayer.addLayer(t.Grid[c++]),t.Grid[c]=L.circleMarker(h[1],{radius:1}).bindTooltip(""+P,l),t.GridLayer.addLayer(t.Grid[c++])}for(var f=Math.floor(a._southWest.lat);f<=a._northEast.lat;f+=i){var g=[[f,a._southWest.lng],[f,a._northEast.lng]],M=RoundPow(4*f,0)/4;t.Grid[c]=L.polyline(g,r),t.GridLayer.addLayer(t.Grid[c]),t.Grid[c]=L.circleMarker(g[0],{radius:1}).bindTooltip(""+M,u),t.GridLayer.addLayer(t.Grid[c++]),t.Grid[c]=L.circleMarker(g[1],{radius:1}).bindTooltip(""+M,d),t.GridLayer.addLayer(t.Grid[c++])}}var PasswordResetInfo=[];function HandlePasswordResetLink(e){PasswordResetInfo=unescape(e).split("|"),initrecaptcha(!1,!0),$("#ResetaPasswordConfirmation").modal("show")}function CheckPageParameters(){var e=window.location.search,t=!0;if(e){var a=e.split("?")[1].split("&");for(var o in a)a[o]&&function(){var e=a[o].split("=");switch(e[0]){case"PwdResetKey":HandlePasswordResetLink(e[1]);break;case"RaceRank":t=!1,RankingFt.OnReadyTable=function(){HandleShowOtherRaceRank(e[1])};break;case"VLMIndex":t=!1,VLMINdexFt.OnReadyTable=function(){HandleShowIndex(e[1])};break;case"ICSRace":t=!1,HandleShowICS(e[1])}}()}t?($(".RaceNavBar").css("display","inherit"),$(".OffRaceNavBar").css("display","none")):($(".RaceNavBar").css("display","none"),$(".OffRaceNavBar").css("display","inherit"),ShowApropos(!1))}function HandleShowICS(e){LoadRaceInfo(e,null,function(e){e&&(FillRaceInstructions(e),$("#RacesInfoForm").modal("show"))})}function LoadRaceInfo(e,t,a){t||(t=""),$.get("/ws/raceinfo/desc.php?idrace="+e+"&v="+t,a)}function HandleVLMIndex(e){if(e){var t;$("#Ranking-Panel").show();var a=1;for(t in e)e[t]&&(e[t].rank=a,a++);BackupVLMIndexTable(),VLMINdexFt.loadRows(e),$("#DivVlmIndex").removeClass("hidden"),$("#RnkTabsUL").addClass("hidden"),$("#DivRnkRAC").addClass("hidden"),ShowApropos(!0)}}function HandleShowIndex(e){var t=HandleVLMIndex;$.get("/cache/rankings/VLMIndex_"+e+".json",t)}function HandleShowOtherRaceRank(e){OnPlayerLoadedCallBack=function(){LoadRaceInfo(e,0,function(e){FillRaceInfoHeader(e)}),LoadRankings(e,OtherRaceRankingLoaded),RankingFt.RaceRankingId=e},void 0!==_CurPlayer&&_CurPlayer&&_CurPlayer.CurBoat&&(OnPlayerLoadedCallBack(),OnPlayerLoadedCallBack=null)}function OtherRaceRankingLoaded(){$("#Ranking-Panel").show(),SortRanking("RAC"),console.log("off race ranking loaded")}function initrecaptcha(e,t){e&&!RC_PwdResetReq&&(RC_PwdResetReq=grecaptcha.render("recaptcha-PwdReset1")),t&&!RC_PwdResetConfirm&&(RC_PwdResetConfirm=grecaptcha.render("recaptcha-PwdReset2"))}function InitMenusAndButtons(){$("div.vresp.modal").on("show.bs.modal",function(){$(this).show(),setModalMaxHeight(this)}),$(window).resize(function(){0!=$(".modal.in").length&&setModalMaxHeight($(".modal.in"))}),$("#BtnChangePassword").on("click",function(e){e.preventDefault(),HandlePasswordChangeRequest(e)}),$("#ResetPasswordButton").on("click",function(e){null!==RC_PwdResetReq&&grecaptcha.execute(RC_PwdResetReq)}),$("#ConfirmResetPasswordButton").on("click",function(e){null!==RC_PwdResetConfirm&&grecaptcha.execute(RC_PwdResetConfirm)}),$("#LoginForm").on("show.bs.modal",function(e){ShowApropos(!1)}),$("#LoginForm").on("hide.bs.modal",function(e){ShowApropos(!0)}),$(".logindlgButton").on("click",function(e){$("#LoginForm").modal("show")}),$(".logOutButton").on("click",function(e){Logout()}),$("#Menu").menu(),$("#Menu").hide(),$("input[type=submit],button").button().click(function(e){e.preventDefault()}),$(".JVLMTabs").tabs(),HidePb("#PbLoginProgress"),HidePb("#PbGetBoatProgress"),HidePb("#PbGribLoginProgress"),$(".BCPane.WP_PM_Mode").click(function(){MoveWPBoatControlerDiv("#"+$(this)[0].classList[2])}),$(".BtnRaceList").click(function(){LoadRacesList(),$("#RacesListForm").modal("show")}),InitRankingEvents(),$("#LoginButton").click(function(){OnLoginRequest()}),$("#LoginPanel").keypress(function(e){"13"===e.which&&(OnLoginRequest(),$("#LoginForm").modal("hide"))}),$("#BtnSetting").click(function(){LoadVLMPrefs(),SetDDTheme(VLM2Prefs.CurTheme),$("#SettingsForm").modal("show")}),$("#SettingValidateButton").click(SaveBoatAndUserPrefs),$("#SettingCancelButton").click(function(){LoadVLMPrefs(),SetDDTheme(VLM2Prefs.CurTheme),$("#SettingsForm").modal("show")}),$("#SettingValidateButton").click(SaveBoatAndUserPrefs),$("#SettingCancelButton").click(function(){SetDDTheme(VLM2Prefs.CurTheme)}),$("#BtnPM_Heading").click(function(){SendVLMBoatOrder(PM_HEADING,$("#PM_Heading")[0].value)}),$("#BtnPM_Angle").click(function(){SendVLMBoatOrder(PM_ANGLE,$("#PM_Angle")[0].value)}),$("#BtnPM_Tack").click(function(){$("#PM_Angle")[0].value=-$("#PM_Angle")[0].value}),$("#BtnCreateAccount").click(function(){HandleCreateUser()}),$(".CreatePassword").pstrength(),$("#NewPlayerEMail").blur(function(e){$("#NewPlayerEMail").verimail({messageElement:"#verimailstatus",language:_CurLocale})}),$("#InscriptForm").on("shown.bs.modal",function(){$(this).find("div.modal-body :input").val("")}),$("#SetWPOnClick").click(HandleStartSetWPOnClick),$("#SetWPOffClick").click(HandleCancelSetWPOnClick),HandleCancelSetWPOnClick(),$("body").on("click",".PIL_EDIT",HandlePilotEditDelete),$("body").on("click",".PIL_DELETE",HandlePilotEditDelete),$("#AutoPilotAddButton").click(HandleOpenAutoPilotSetPoint),$("#AP_SetTargetWP").click(HandleClickToSetWP),$("#AP_Time").datetimepicker({locale:_CurLocale,format:"DD MM YYYY, HH:mm:ss"}),$("#AP_Time").on("dp.change",HandleDateChange),$("#APValidateButton").click(HandleSendAPUpdate),$(".APField").on("change",HandleAPFieldChange),$(".APMode").on("click",HandleAPModeDDClick),$(".Draggable").draggable({handle:".modal-header,.modal-body"}),$("#MapPrefsToggle").click(HandleShowMapPrefs),$(".chkprefstore").on("change",HandleMapPrefOptionChange),$(".MapOppShowLi").click(HandleMapOppModeChange),$(".DDTheme").click(HandleDDlineClick),$("#StartEstimator").on("click",HandleStartEstimator),$("#EstimatorStopButton").on("click",HandleStopEstimator),InitGribSlider(),InitFootables(),$(document.body).on("click",".RaceHistLink",function(e){HandleShowBoatRaceHistory(e)}),$("[PilRefresh]").on("click",HandleUpdatePilototoTable),$("#HistRankingButton").on("click",function(e){ShowUserRaceHistory(_CurPlayer.CurBoat.IdBoat)}),$("#BtnPM_Ortho, #BtnPM_VMG, #BtnPM_VBVMG").click(function(){var e,t=PM_ORTHO,a=$("#PM_Lat")[0].value,o=$("#PM_Lon")[0].value;switch(e=parseInt($("#PM_WPHeading")[0].value,10),$(this)[0].id){case"BtnPM_Ortho":t=PM_ORTHO;break;case"BtnPM_VMG":t=PM_VMG;break;case"BtnPM_VBVMG":t=PM_VBVMG}SendVLMBoatOrder(t,o,a,e)}),$("#CalendarPanel").on("shown.bs.modal",function(e){HandleShowAgenda()}),$(".BoatSelectorDropDownList").on("click",HandleBoatSelectionChange),$("#cp11").colorpicker({useAlpha:!1,format:!1}),$(document.body).on("click",".ShowICSButton",function(e){HandleFillICSButton(e)}),$(document.body).on("click",".ShowRaceInSpectatorMode",function(e){HandleGoToRaceSpectator(e)}),$("#PolarTab").on("click",HandlePolarTabClik),CheckLogin(),UpdateVersionLine()}function InitRankingEvents(){$("#Ranking-Panel").on("shown.bs.collapse",function(e){HandleRaceSortChange(e)}),$(document.body).on("click",".RankingButton",function(e){var t=$(e.currentTarget).attr("IdRace");void 0!==t&&t&&window.open("/jvlm?RaceRank="+t,"RankTab")}),$(document.body).on("click","[RnkSort]",function(e){HandleRaceSortChange(e)}),$("#Ranking-Panel").on("hide.bs.collapse",function(e){ResetRankingWPList(e)})}function UpdateVersionLine(){var e=new moment(BuildDate);$("#BuildDate").text("Build : "+e.fromNow()),$('[data-toggle="tooltip"]').tooltip()}var _CachedRaceInfo=null;function HandlePolarTabClik(){_CachedRaceInfo&&DrawPolar(_CachedRaceInfo)}function InitPolar(e){_CachedRaceInfo=e}function HandleGoToRaceSpectator(e){if(void 0!==e&&e){e.target;var t=$(e.currentTarget).attr("idRace");if(void 0!==t&&t)return void window.open("/guest_map/index.html?idr="+t,"Spec_"+t)}}function HandleFillICSButton(e){if(void 0!==e&&e){e.target;var t=$(e.currentTarget).attr("idRace");if(void 0!==t&&t)return void HandleShowICS(t)}void 0!==_CurPlayer&&_CurPlayer&&_CurPlayer.CurBoat&&_CurPlayer.CurBoat.RaceInfo&&FillRaceInstructions(_CurPlayer.CurBoat.RaceInfo)}var CalInited=!1;function HandleShowAgenda(){jQuery("#Calendar").fullCalendar("destroy"),jQuery("#Calendar").fullCalendar({locale:_CurLocale,editable:!1,header:{left:"title",center:"",right:"today prev,next"},firstDay:1,events:"/feed/races.fullcalendar.php",data:function(){return{jvlm:1}},timeFormat:"H:mm",loading:function(e){e?jQuery("#loading").show():jQuery("#loading").hide()}}),CalInited=!0,$("#Infos").modal("hide")}function HandlePasswordChangeRequest(e){var t=$("#CurPassword")[0].value,a=$("#NewPassword1")[0].value,o=$("#NewPassword2")[0].value;if($(".Password").val(""),t&&""!==t)if(a===o)if(""!==a){var n={OldPwd:t,NewPwd:a};$.post("/ws/playersetup/password_change.php","parms="+JSON.stringify(n),function(e){HandlePasswordChangeResult(e)})}else VLMAlertDanger(GetLocalizedString("NewPwdRequired"));else VLMAlertDanger(GetLocalizedString("CurPwdRequired"));else VLMAlertDanger(GetLocalizedString("CurPwdRequired"))}function HandlePasswordChangeResult(e){e.success?VLMAlertInfo():VLMAlertDanger(GetLocalizedString(e.error.msg))}function SendResetPassword(e){PasswordResetInfo[0],PasswordResetInfo[1];$.get("/ws/playersetup/password_reset.php?email="+PasswordResetInfo[0]+"&seed="+PasswordResetInfo[1]+"&key="+e,function(e){HandlePasswordReset(e,!0)})}function SendResetPasswordLink(e){var t=$(".UserName").val();if(""===t)return VLMAlertDanger(GetLocalizedString("Enter your email for resetting your password")),void grecaptcha.reset(RC_PwdResetReq);var a={email:t,key:e};$.post("/ws/playersetup/password_reset.php","parms="+JSON.stringify(a),function(e){HandlePasswordReset(e,!1)})}function HandlePasswordReset(e,t){e.success?t?(VLMAlertInfo(GetLocalizedString("Check your inbox to get your new password.")),grecaptcha.reset(RC_PwdResetReq)):(VLMAlertInfo(GetLocalizedString("An email has been sent. Click on the link to validate.")),grecaptcha.reset(RC_PwdResetConfirm)):(VLMAlertDanger("Something went wrong :("),grecaptcha.reset(RC_PwdResetReq),grecaptcha.reset(RC_PwdResetConfirm))}function InitFooTable(e){var t=FooTable.init("#"+e,{name:e,on:{"ready.ft.table":HandleReadyTable,"after.ft.paging":HandlePagingComplete,"postdraw.ft.table":HandleTableDrawComplete}});return t.DrawPending=!0,t.CallbackPending=null,t}function InitFootables(){$("#DiscontinueRaceButton").on("click",HandleDiscontinueRaceRequest),PilototoFt=InitFooTable("PilototoTable"),RankingFt=InitFooTable("RankingTable"),RaceHistFt=InitFooTable("BoatRaceHist"),ICS_WPft=InitFooTable("RaceWayPoints"),NSZ_WPft=InitFooTable("NSZPoints"),VLMINdexFt=InitFooTable("VLMIndexTable")}function HandleUpdatePilototoTable(e){UpdatePilotInfo(_CurPlayer.CurBoat)}function InitSlider(e,t,a,o,n,i){var r=$("#"+t);$("#"+e).slider({orientation:"vertical",min:a,max:o,value:n,create:function(){r.text($(this).slider("value"))},slide:function(e,t){i(e,t)}})}function InitGribSlider(){InitSlider("GribSlider","GribSliderHandle",0,72,0,HandleGribSlideMove)}function HandleRaceSortChange(e){var t=$(e.currentTarget).attr("rnksort");switch(t){case"WP":SortRanking(t,$(e.currentTarget).attr("WPRnk"));break;case"DNF":case"HTP":case"HC":case"ABD":case"RAC":case"ARR":SortRanking(t);break;default:console.log("Sort change request"+e)}}function HandleGribSlideMove(e,t){$("#GribSliderHandle").text(t.value);var a=(new Date).getTime();(map.GribMap.SetGribMapTime(a+36e5*t.value),VLM2Prefs.MapPrefs.TrackEstForecast&&_CurPlayer.CurBoat.Estimator)&&(RefreshEstPosLabels(_CurPlayer.CurBoat.GetClosestEstimatePoint(new Date(a+3600*t.value*1e3))),StartEstimateTimeout())}function HandleDiscontinueRaceRequest(){GetUserConfirmation(GetLocalizedString("unsubscribe"),!0,HandleRaceDisContinueConfirmation)}function HandleRaceDisContinueConfirmation(e){e?(DiconstinueRace(_CurPlayer.CurBoat.IdBoat,_CurPlayer.CurBoat.Engaged),$("#ConfirmDialog").modal("hide"),$("#RacesInfoForm").modal("hide")):VLMAlertDanger("Ouf!")}function HandleStopEstimator(e){var t=_CurPlayer.CurBoat;void 0!==t&&t&&t.Estimator.Stop()}function HandleStartEstimator(e){var t=_CurPlayer.CurBoat;void 0!==t&&t&&t.Estimator.Start(HandleEstimatorProgress)}var LastPctRefresh=-1,LastPctDraw=-1;function HandleEstimatorProgress(e,t,a){e?($("#StartEstimator").removeClass("hidden"),$("#PbEstimatorProgressBar").addClass("hidden"),$("#EstimatorStopButton").addClass("hidden"),LastPctRefresh=-1,LastPctDraw=-1):t-LastPctRefresh>.15?($("#EstimatorStopButton").removeClass("hidden"),$("#StartEstimator").addClass("hidden"),$("#PbEstimatorProgressBar").removeClass("hidden"),$("#PbEstimatorProgressText").removeClass("hidden"),$("#PbEstimatorProgressText").text(t),$("#PbEstimatorProgress").css("width",t+"%"),$("#PbEstimatorProgress").attr("aria-valuenow",t),$("#PbEstimatorProgress").attr("aria-valuetext",t),LastPctRefresh=t):t-LastPctDraw>1&&(DrawBoatEstimateTrack(_CurPlayer.CurBoat,GetRaceMapFeatures(_CurPlayer.CurBoat)),LastPctDraw=t)}function HandleFlagLineClick(e){SelectCountryDDFlag(e.target.attributes.flag.value)}function HandleCancelSetWPOnClick(){SetWPPending=!1,$("#SetWPOnClick").show(),$("#SetWPOffClick").hide()}function HandleStartSetWPOnClick(){SetWPPending=!0,WPPendingTarget="WP",$("#SetWPOnClick").hide(),$("#SetWPOffClick").show()}function ClearBoatSelector(){$(".BoatSelectorDropDownList").empty()}function AddBoatToSelector(e,t){BuildUserBoatList(e,t)}function BuildUserBoatList(e,t){$(".BoatSelectorDropDownList").append(GetBoatDDLine(e,t))}function GetBoatDDLine(e,t){var a='<li class="DDLine" BoatID="'+e.IdBoat+'">';return a=a+GetBoatInfoLine(e,t)+"</li>"}function GetBoatInfoLine(e,t){var a="",o="racing";return e.Engaged||(o="Docked"),void 0!==e.VLMInfo&&e.VLMInfo["S&G"]&&(o="stranded"),t||(a+='<span class="badge">BS'),a=a+'<img class="BoatStatusIcon" src="images/'+o+'.png" />',t||(a+="</span>"),a=a+"<span>-</span><span>"+HTMLDecode(e.BoatName)+"</span>"}function ShowBgLoad(){$("#BgLoadProgress").css("display","block")}function HideBgLoad(){$("#BgLoadProgress").css("display","block")}function ShowPb(e){$(e).show()}function HidePb(e){$(e).hide()}function DisplayLoggedInMenus(e){var t,a;e?(t="block",a="none"):(t="none",a="block"),$("[LoggedInNav='true']").css("display",t),$("[LoggedInNav='false']").css("display",a),void 0!==_CurPlayer&&_CurPlayer&&_CurPlayer.IsAdmin?$("[AdminNav='true']").css("display","block"):$("[AdminNav='true']").css("display","none"),ShowApropos(e)}function ShowApropos(e){$("#Apropos").modal(e?"hide":"show")}function HandleRacingDockingButtons(e){e?($('[RacingBtn="true"]').removeClass("hidden"),$('[RacingBtn="false"]').addClass("hidden")):($('[RacingBtn="true"]').addClass("hidden"),$('[RacingBtn="false"]').removeClass("hidden"))}function UpdateInMenuDockingBoatInfo(e){HandleRacingDockingButtons(void 0!==e&&void 0!==e.VLMInfo&&parseInt(e.VLMInfo.RAC,10))}function SetTWASign(e){var t=e.VLMInfo.TWD,a=e.VLMInfo.HDG,o=t-a;o<-180&&(o+=360),o>180&&(o-=360);o*e.VLMInfo.TWA>0&&(e.VLMInfo.TWA=-e.VLMInfo.TWA)}function UpdateInMenuRacingBoatInfo(e,t){if(e&&void 0!==e){HandleRacingDockingButtons(!0),SetTWASign(e),"2"===e.VLMInfo.PIM&&"0"===e.VLMInfo.PIP&&(e.VLMInfo.HDG=e.VLMInfo.TWD,e.VLMInfo.BSP=0);var a=new Coords(e.VLMInfo.LON,!0),o=new Coords(e.VLMInfo.LAT),n=[];n.push([FIELD_MAPPING_TEXT,"#BoatLon",a.toString()]),n.push([FIELD_MAPPING_TEXT,"#BoatLat",o.toString()]),n.push([FIELD_MAPPING_TEXT,".BoatSpeed",RoundPow(e.VLMInfo.BSP,2)]),n.push([FIELD_MAPPING_TEXT,".BoatHeading",RoundPow(e.VLMInfo.HDG,1)]),n.push([FIELD_MAPPING_VALUE,"#PM_Heading",RoundPow(e.VLMInfo.HDG,2)]),n.push([FIELD_MAPPING_TEXT,"#BoatAvg",RoundPow(e.VLMInfo.AVG,1)]),n.push([FIELD_MAPPING_TEXT,"#BoatDNM",RoundPow(e.VLMInfo.DNM,1)]),n.push([FIELD_MAPPING_TEXT,"#BoatLoch",RoundPow(e.VLMInfo.LOC,1)]),n.push([FIELD_MAPPING_TEXT,"#BoatOrtho",RoundPow(e.VLMInfo.ORT,1)]),n.push([FIELD_MAPPING_TEXT,"#BoatLoxo",RoundPow(e.VLMInfo.LOX,1)]),n.push([FIELD_MAPPING_TEXT,"#BoatVMG",RoundPow(e.VLMInfo.VMG,1)]),n.push([FIELD_MAPPING_TEXT,".BoatWindSpeed",RoundPow(e.VLMInfo.TWS,1)]),n.push([FIELD_MAPPING_TEXT,"#BoatWindDirection",RoundPow(e.VLMInfo.TWD,1)]),n.push([FIELD_MAPPING_CHECK,"#PM_WithWPHeading","-1.0"!==e.VLMInfo["H@WP"]]),n.push([FIELD_MAPPING_TEXT,"#RankingBadge",e.VLMInfo.RNK]),n.push([FIELD_MAPPING_VALUE,"#PM_WPHeading",e.VLMInfo["H@WP"]]),n.push([FIELD_MAPPING_TEXT,".BoatClass",e.VLMInfo.POL.substring(5)]),n.push([FIELD_MAPPING_TEXT,".RaceName",e.VLMInfo.RAN]);var i=new VLMPosition(e.VLMInfo.WPLON,e.VLMInfo.WPLAT);n.push([FIELD_MAPPING_VALUE,"#PM_Lat",i.Lat.Value]),n.push([FIELD_MAPPING_VALUE,"#PM_Lon",i.Lon.Value]),0===i.Lon.Value&&0===i.Lat.Value&&(i=e.GetNextWPPosition()),void 0!==i&&i?(n.push([FIELD_MAPPING_TEXT,"#PM_CurWPLat",i.Lat.toString()]),n.push([FIELD_MAPPING_TEXT,"#PM_CurWPLon",i.Lon.toString()])):(n.push([FIELD_MAPPING_TEXT,"#PM_CurWPLat","N/A"]),n.push([FIELD_MAPPING_TEXT,"#PM_CurWPLon","N/A"])),parseInt(e.VLMInfo.PIM,10)===PM_ANGLE?(n.push([FIELD_MAPPING_TEXT,".BoatWindAngle",RoundPow(Math.abs(e.VLMInfo.PIP),1)]),n.push([FIELD_MAPPING_VALUE,"#PM_Angle",e.VLMInfo.PIP])):(n.push([FIELD_MAPPING_TEXT,".BoatWindAngle",RoundPow(Math.abs(e.VLMInfo.TWA),1)]),n.push([FIELD_MAPPING_VALUE,"#PM_Angle",RoundPow(e.VLMInfo.TWA,1)])),FillFieldsFromMappingTable(n);var r="lime";e.VLMInfo.TWA>0&&(r="red"),$(".BoatWindAngle").css("color",r);var s=Math.round(100*(e.VLMInfo.TWD+180))/100,l=(Math.round(100*e.VLMInfo.TWS),e.VLMInfo.POL),u=Math.round(100*e.VLMInfo.HDG)/100,d=Math.round(100*e.VLMInfo.TWS)/100,c=Math.round(100*e.VLMInfo.ORT)/100;$("#ImgWindAngle").attr("src","windangle.php?wheading="+s+"&boatheading="+u+"&wspeed="+d+"&roadtoend="+c+"&boattype="+l+"&jvlm="+e.VLMInfo.NOW),$("#ImgWindAngle").css("transform","rotate("+s+"deg)"),$("#DeckImage").css("transform","rotate("+u+"deg)"),$(".PMActiveMode").css("display","none"),$(".BCPane").removeClass("active");var p=".ActiveMode_",h="";switch(e.VLMInfo.PIM){case"1":p+="Heading",h="BearingMode";break;case"2":p+="Angle",h="AngleMode";break;case"3":p+="Ortho",h="OrthoMode";break;case"4":p+="VMG",h="VMGMode";break;case"5":p+="VBVMG",h="VBVMGMode";break;default:VLMAlert("Unsupported VLM PIM Mode, expect the unexpected....","alert-info")}$(p).css("display","inline"),$("."+h).addClass("active"),$("#"+h).addClass("active"),UpdatePilotInfo(e),UpdatePolarImages(e)}}function FillFieldsFromMappingTable(e){for(var t in e)if(e[t])switch(e[t][0]){case FIELD_MAPPING_TEXT:$(e[t][1]).text(e[t][2]);break;case FIELD_MAPPING_VALUE:$(e[t][1]).val(e[t][2]);break;case FIELD_MAPPING_CHECK:$(e[t][1]).prop("checked",e[t][2]);break;case FIELD_MAPPING_IMG:$(e[t][1]).attr("src",e[t][2]);break;case FIELD_MAPPING_CALLBACK:e[t][2](e[t][1]);break;case FIELD_MAPPING_STYLE:$(e[t][1]).css(e[t][2],e[t][3])}}function FillRaceInstructions(e){if(void 0!==e&&e){var t=!0;void 0!==_CurPlayer&&_CurPlayer&&_CurPlayer.CurBoat&&_CurPlayer.CurBoat.RaceInfo&&(t=_CurPlayer.CurBoat.RaceInfo.idraces!==e.idraces),t?$("#DiscontinueRaceTab").addClass("hidden"):$("#DiscontinueRaceTab").removeClass("hidden");FillRaceInfoHeader(e),FillRaceWaypointList(e),InitPolar(e),$.get("/ws/raceinfo/exclusions.php?idr="+e.idraces+"&v="+e.VER,function(e){e&&e.success&&FillNSZList(e.Exclusions)})}}var PolarSliderInited=!1;function FillRaceInfoHeader(e){if(void 0!==e&&e){var t=[];t.push([FIELD_MAPPING_TEXT,".ICSRaceName",e.racename]),t.push([FIELD_MAPPING_TEXT,".RaceId",e.idraces]),t.push([FIELD_MAPPING_TEXT,".BoatType",e.boattype.substring(5)]),t.push([FIELD_MAPPING_TEXT,".VacFreq",parseInt(e.vacfreq,10)]),t.push([FIELD_MAPPING_TEXT,"#LockTime",parseInt(e.coastpenalty,10)/60]),t.push([FIELD_MAPPING_TEXT,"#EndRace",parseInt(e.firstpcttime,10)]),t.push([FIELD_MAPPING_TEXT,"#RaceStartDate",GetLocalUTCTime(1e3*parseInt(e.deptime,10),!0,!0)]),t.push([FIELD_MAPPING_TEXT,"#RaceLineClose",GetLocalUTCTime(1e3*parseInt(e.closetime,10),!0,!0)]),t.push([FIELD_MAPPING_IMG,"#RaceImageMap","/cache/racemaps/"+e.idraces+".png"]),FillFieldsFromMappingTable(t)}}function HandlePolarSpeedSlide(e,t,a){$("#PolarSpeedHandle").text(t.value),DrawPolar(a)}function DrawPolar(e){var t=$("#PolarCanvas")[0],a=25;PolarSliderInited&&(a=parseFloat($("#PolarSpeedHandle").text()));var o=PolarsManager.GetPolarLine(e.boattype,a,function(){DrawPolar(e)},null,1);if(o){PolarSliderInited||(InitSlider("PolarSpeedSlider","PolarSpeedHandle",0,60,a,function(t,a){HandlePolarSpeedSlide(t,a,e)}),PolarSliderInited=!0),t.width=$("#PolarCanvas").width(),t.height=t.width;var n,i,r=t.getContext("2d"),s=!0,l=Math.PI/o.length,u=t.width/2,d=t.width/2,c=PolarsManager.GetPolarMaxSpeed(e.boattype,a),p=0,h=!0;for(var P in r.beginPath(),r.lineWidth="1",r.strokeStyle="#FF0000",o)if(o[P]){var f=o[P],g=(P=parseInt(P,10))*l,L=u+d*f*Math.cos(g),M=3+d*f*Math.sin(g),m=Math.cos(g+0)*f;h&&m<=p?(r.stroke(),r.beginPath(),r.moveTo(n,i),r.strokeStyle="#FFFFFF",h=!1):!h&&m>=p&&(r.stroke(),r.beginPath(),r.moveTo(n,i),r.strokeStyle="#FF0000",h=!0),p=m,s?(r.moveTo(M,L),s=!1):r.lineTo(M,L),n=M,i=L}r.stroke(),r.beginPath(),r.lineWidth="1",r.strokeStyle="#00FF00",r.moveTo(3,0),r.lineTo(3,t.height),r.stroke(),r.moveTo(2,t.height/2),r.lineTo(3+t.width,t.height/2),r.stroke();var C=Math.round(c/5);C||(C=1);for(var v=1;C*v-1<=c;v++)r.beginPath(),r.strokeStyle="#7FFFFF",r.arc(3,u,d*v*C/c,Math.PI/2,1.5*Math.PI,!0),r.stroke(),r.strokeText(" "+C*v,4+C*d*v/c,u+10)}}function UpdatePolarImages(e){var t,a=e.VLMInfo.POL.substring(5),o="";for(t=0;t<=45;t+=15)o+='<li><img class="polaire" src="/scaledspeedchart.php?boattype=boat_'+a+"&amp;minws="+t+"&amp;maxws="+(t+15)+'&amp;pas=2" alt="speedchart"></li>';$("#PolarList").empty(),$("#PolarList").append(o)}function BackupFooTable(e,t,a){e.DOMBackup?void 0===$(t)[0]&&($(e.RestoreId).append(e.DOMBackup),console.log("Restored footable "+t)):(e.DOMBackup=$(t),e.RestoreId=a)}function UpdatePilotInfo(e){if(void 0!==e&&e&&!PilototoFt.DrawPending){BackupFooTable(PilototoFt,"#PilototoTable","#PilototoTableInsertPoint");var t=[];if(e&&e.VLMInfo&&e.VLMInfo.PIL&&e.VLMInfo.PIL.length>0){for(var a in e.VLMInfo.PIL)if(e.VLMInfo.PIL[a]){var o=GetPilototoTableLigneObject(e,a);t.push(o)}e.VLMInfo.PIL.length<MAX_PILOT_ORDERS?$("#AutoPilotAddButton").removeClass("hidden"):$("#AutoPilotAddButton").addClass("hidden")}PilototoFt.DrawPending=!0,PilototoFt.loadRows(t,!1),console.log("loaded pilototo table"),UpdatePilotBadge(e)}}function HandleReadyTable(e,t){console.log("Table ready"+t),t.DrawPending=!1,t.OnReadyTable&&t.OnReadyTable()}function HandlePagingComplete(e,t){var a,o={ft_class_myboat:"rnk-myboat",ft_class_friend:"rnk-friend",ft_class_oncoast:"rnk-oncoast",ft_class_racing:"rnk-racing",ft_class_locked:"rnk-locked",ft_class_dns:"rnk-dns"};for(var n in o)o[n]&&$("td").closest("tr").removeClass(o[n]);for(a in o)o[a]&&$('td:contains("'+a+'")').closest("tr").addClass(o[a]);t.DrawPending=!1}function HandleTableDrawComplete(e,t){if(console.log("TableDrawComplete "+t.id),t.DrawPending=!1,t===RankingFt)setTimeout(function(){DeferedGotoPage(e,t)},500);else if(t.CallbackPending)return void setTimeout(function(){t.CallbackPending(),t.CallbackPending=null},500)}function DeferedGotoPage(e,t){RankingFt.TargetPage&&(RankingFt.gotoPage(RankingFt.TargetPage),RankingFt.TargetPage=0),setTimeout(function(){DeferedPagingStyle(e,t)},200)}function DeferedPagingStyle(e,t){HandlePagingComplete(e,t)}function GetPilototoTableLigneObject(e,t){var a=e.VLMInfo.PIL[t],o=GetLocalUTCTime(1e3*a.TTS,!0,!0),n=GetPilotModeName(a.PIM);return t=parseInt(t,10)+1,$("#EditCellTemplate .PIL_EDIT").attr("pil_id",t),$("#DeleteCellTemplate .PIL_DELETE").attr("TID",a.TID).attr("pil_id",t),{date:o,PIM:n,PIP:a.PIP,Status:a.STS,Edit:$("#EditCellTemplate").first().html(),Delete:$("#DeleteCellTemplate").first().html()}}function ShowAutoPilotLine(e,t){var a="#PIL"+t,o=e.VLMInfo.PIL[t-1],n=new Date(1e3*o.TTS),i=GetPilotModeName(o.PIM);if(void 0===$(a)[0]);$(a)[0].attributes.TID=o.TID,SetSubItemValue(a,"#PIL_DATE",n),SetSubItemValue(a,"#PIL_PIM",i),SetSubItemValue(a,"#PIL_PIP",o.PIP),SetSubItemValue(a,"#PIL_STATUS",o.STS),$(a).show()}function GetPILIdParentElement(e){for(var t=e;;){if(void 0===t)return;if("id"in t.attributes){var a=t.attributes.id.value;if(4===a.length&&"PIL"===a.substring(0,3))return t}t=t.parentElement}}function HandlePilotEditDelete(e){var t=$(this)[0],a=t.attributes.class.value,o=_CurPlayer.CurBoat;parseInt(t.attributes.pil_id.value,10);"PIL_EDIT"===a?HandleOpenAutoPilotSetPoint(e):"PIL_DELETE"===a&&DeletePilotOrder(o,t.attributes.TID.value)}function GetPilotModeName(e){switch(parseInt(e,10)){case 1:return GetLocalizedString("autopilotengaged");case 2:return GetLocalizedString("constantengaged");case 3:return GetLocalizedString("orthoengaged");case 4:return GetLocalizedString("bestvmgengaged");case 5:return GetLocalizedString("vbvmgengaged");default:return"PIM ???"+e+"???"}}function SetSubItemValue(e,t,a){var o=$(e).find(t);o.length>0&&o.text(a)}function UpdatePilotBadge(e){var t,a=0;if(void 0!==e&&e){var o=e.VLMInfo.PIL;if(void 0!==o&&o&&o.length)for(t in o)"pending"===o[t].STS&&a++;a>0?($(".PilotOrdersBadge").show(),$(".PilotOrdersBadge").text(a)):$(".PilotOrdersBadge").hide()}}function MoveWPBoatControlerDiv(e){$(e).prepend($("#PM_WPMode_Div"))}function UpdatePrefsDialog(e){if(void 0===e)$("#BtnSetting").addClass("hidden");else if($("#BtnSetting").removeClass("hidden"),$("#pref_boatname").val(e.BoatName),void 0!==e.VLMInfo){SelectCountryDDFlag(e.VLMInfo.CNT);var t=SafeHTMLColor(e.VLMInfo.COL);$("#pref_boatcolor").val(t),$("#cp11").colorpicker({useAlpha:!1,format:!1,color:t})}}var RaceSorter=function(e,t){return e.CanJoin===t.CanJoin?e.deptime>t.deptime?-1:e.deptime===t.deptime?e.racename>t.racename?1:e.racename===t.racename?0:-1:1:e.CanJoin?1:-1};function LoadRacesList(){var e=_CurPlayer.CurBoat.IdBoat;$("#RaceListPanel").empty().append("<H4>...</H4>"),$.get("/ws/raceinfo/list.php?iduser="+e+"&v="+(new Date).getTime(),function(e){var t=e;$("#RaceListPanel").empty();var a=[];for(var o in t)t[o]&&a.push(t[o]);for(var n in a.sort(RaceSorter),a)a[n]&&AddRaceToList(a[n]);var i=0;$("#RaceListPanel .btn-group .btn-md").each(function(){$(this).height()>i&&(i=$(this).height())}),$("#RaceListPanel .btn-group .btn-md").height(i)})}function AddRaceToList(e){var t,a,o=$("#RaceListPanel").first();new Date(0);if(_CurPlayer&&_CurPlayer.CurBoat&&_CurPlayer.CurBoat.RaceInfo&&_CurPlayer.CurBoat.RaceInfo.idraces&&(e.CanJoin=e.CanJoin&"0"===_CurPlayer.CurBoat.RaceInfo.idraces),e.CanJoin){var n=new Date;new Date(1e3*e.deptime)<=n?(t="CanJoinRace",a=GetLocalizedString("closerace")+" "+moment("/date("+1e3*e.closetime+")/").fromNow()):(t="CanJoinRaceNotStarted",a=GetLocalizedString("departuredate")+" "+moment("/date("+1e3*e.deptime+")/").fromNow())}else t="NoJoinRace";var i='<div class="raceheaderline panel panel-default '+t+'" )>  <div data-toggle="collapse" href="#RaceDescription'+e.idraces+'" class="panel-body collapsed " data-parent="#RaceListPanel" aria-expanded="false">    <div class="col-xs-12">      <div class="col-xs-3">        <img class="racelistminimap" src="/cache/minimaps/'+e.idraces+'.png" ></img>      </div>      <div class="col-xs-9">        <div class="col-xs-12">          <span ">'+e.racename+'          </span>        </div>        <div class="btn-group col-xs-12">          <button id="JoinRaceButton" type="button" class="'+(e.CanJoin?"":"hidden")+' btn-default btn-md col-xs-4" IdRace="'+e.idraces+'"  >'+GetLocalizedString("subscribe")+'          </button>          <button id="SpectateRaceButton" type="button" class="ShowRaceInSpectatorMode btn-default btn-md col-xs-4" IdRace="'+e.idraces+'"  >'+GetLocalizedString("Spectator")+'          </button>          <button type="button" class="ShowICSButton btn-default btn-md col-xs-4" IdRace="'+e.idraces+'"  >'+GetLocalizedString("ic")+'          </button>          <button type="button" class="RankingButton btn-default btn-md col-xs-4" IdRace="'+e.idraces+'"  >'+GetLocalizedString("ranking")+"          </button>        </div>      </div>    </div>"+(a?'    <div class="col-xs-12">       <span "> '+a+"       </span>    </div>":"")+'  </div>  <div id="RaceDescription'+e.idraces+'" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">    <div class="panel-body">      <div class="col-xs-12"><img class="img-responsive" src="/cache/racemaps/'+e.idraces+'.png" width="530px"></div>        <div class="col-xs-9"><p>'+GetLocalizedString("race")+" : "+e.racename+"</p>          <p>Départ : "+GetLocalUTCTime(1e3*e.deptime,!0,!0)+"</p>          <p>"+GetLocalizedString("boattype")+" : "+e.boattype.substring(5)+"</p>          <p>"+GetLocalizedString("crank")+" : "+e.vacfreq+"'</p>          <p>"+GetLocalizedString("locktime")+parseInt(e.coastpenalty,10)/60+" '</p>          <p>"+GetLocalizedString("closerace")+GetLocalUTCTime(1e3*e.closetime,!0,!0)+"</p>        </div>      </div>    </div>  </div>";o.prepend(i),$("#JoinRaceButton").click(function(e){EngageBoatInRace(e.currentTarget.attributes.idrace.value,_CurPlayer.CurBoat.IdBoat)})}function PageClock(){if(void 0!==_CurPlayer&&_CurPlayer&&void 0!==_CurPlayer.CurBoat){var e=_CurPlayer.CurBoat;if(void 0!==e&&void 0!==e.RaceInfo){var t=GetRaceClock(e.RaceInfo,e.VLMInfo.UDT),a=$(".RaceChrono");t<0?a.removeClass("ChronoRaceStarted").addClass("ChronoRacePending"):a.addClass("ChronoRaceStarted").removeClass("ChronoRacePending"),$("#RefreshAge").text(moment(_CurPlayer.CurBoat.LastRefresh).fromNow());var o=new Date(1e3*e.VLMInfo.LUP),n=e.VLMInfo.VAC,i=n-(new Date-o)/1e3%n;i>=n-1&&100,$("#pbar_innerdivvac").css("width",+Math.round(i%60*100/60)+"px"),$("#pbar_innerdivmin").css("width",Math.round(i/n*100)+"px"),a.text(GetFormattedChronoString(t))}}}function GetRaceClock(e,t){var a=new Date,o=new Date(1e3*e.deptime);if(e.racetype&RACE_TYPE_RECORD){var n=parseInt(t,10);if(-1===n)return 0;var i=new Date(1e3*n);return Math.floor((a-i)/1e3)}return Math.floor((a-o)/1e3)}function DisplayCurrentDDSelectedBoat(e){$(".BoatDropDown:first-child").html("<span BoatID="+e.IdBoat+">"+GetBoatInfoLine(e,e.IdBoat in _CurPlayer.Fleet)+'</span><span class="caret"></span>')}function PadLeftZero(e){return e<100?("00"+e).slice(-2):e}function GetFormattedChronoString(e){if(e<0)e=-e;else if(0===e)return"--:--:--";var t=PadLeftZero(e%60),a=PadLeftZero(Math.floor(e/60)%60),o=PadLeftZero(Math.floor(e/3600)%24),n=PadLeftZero(Math.floor(e/3600/24)),i=o.toString()+":"+a.toString()+":"+t.toString();return n>0&&(i=n.toString()+" d "+i),i}function RefreshCurrentBoat(e,t,a){var o=$(".BoatDropDown > span");void 0!==o&&void 0!==o[0]&&("BoatId"in o[0].attributes||"boatid"in o[0].attributes)&&SetCurrentBoat(GetBoatFromIdu(o[0].attributes.BoatID.value),e,t,a)}function UpdateLngDropDown(){var e=GetCurrentLocale();$("#SelectionLanguageDropDown:first-child").html('<img class=" LngFlag" lang="'+e+'" src="images/lng-'+e+'.png" alt="'+e+'"><span class="caret"></span>')}var _CurAPOrder=null;function HandleOpenAutoPilotSetPoint(e){var t,a=e.target;if("id"in a.attributes)t=a.attributes.id.nodeValue;else{if(!("class"in a.attributes))return void VLMAlert("Something bad has happened reload this page....","alert-danger");t=a.attributes.class.nodeValue}switch(t){case"AutoPilotAddButton":_CurAPOrder=new AutoPilotOrder;break;case"PIL_EDIT":var o=parseInt(a.attributes.pil_id.value,10);_CurAPOrder=new AutoPilotOrder(_CurPlayer.CurBoat,o),$("#AutoPilotSettingForm").modal("show");break;default:return void VLMalert("Something bad has happened reload this page....","alert-danger")}RefreshAPDialogFields()}function RefreshAPDialogFields(){$("#AP_Time").data("DateTimePicker").date(_CurAPOrder.Date),$("#AP_PIM:first-child").html("<span>"+_CurAPOrder.GetPIMString()+'</span><span class="caret"></span>'),$("#AP_PIP").val(_CurAPOrder.PIP_Value),$("#AP_WPLat").val(_CurAPOrder.PIP_Coords.Lat.Value),$("#AP_WPLon").val(_CurAPOrder.PIP_Coords.Lon.Value),$("#AP_WPAt").val(_CurAPOrder.PIP_WPAngle),UpdatePIPFields(_CurAPOrder.PIM)}function HandleDateChange(e){_CurAPOrder.Date=e.date}function HandleClickToSetWP(){SetWPPending=!0,WPPendingTarget="AP",$("#AutoPilotSettingForm").modal("hide")}function HandleAPModeDDClick(e){var t=e.target.attributes.PIM.value;_CurAPOrder.PIM=parseInt(t,10),$("#AP_PIM:first-child").html("<span>"+_CurAPOrder.GetPIMString()+'</span><span class="caret"></span>'),UpdatePIPFields(_CurAPOrder.PIM)}function UpdatePIPFields(e){var t=!0;switch(e){case PM_HEADING:case PM_ANGLE:t=!0;break;case PM_ORTHO:case PM_VMG:case PM_VBVMG:t=!1}t?($(".AP_PIPRow").removeClass("hidden"),$(".AP_WPRow").addClass("hidden")):($(".AP_PIPRow").addClass("hidden"),$(".AP_WPRow").removeClass("hidden"))}function SaveBoatAndUserPrefs(e){var t={},a=!1,o=$("#SelectionThemeDropDown").attr("SelTheme");void 0!==o&&(VLM2Prefs.CurTheme=o),VLM2Prefs.Save(),ComparePrefString($("#pref_boatname")[0].value,_CurPlayer.CurBoat.BoatName)||(t.boatname=encodeURIComponent($("#pref_boatname")[0].value),a=!0),ComparePrefString($("#pref_boatcolor")[0].value,SafeHTMLColor(_CurPlayer.CurBoat.VLMInfo.COL))||(t.color=$("#pref_boatcolor")[0].value.substring(1),a=!0);var n=GetPrefSelFlag();ComparePrefString(n,_CurPlayer.CurBoat.VLMInfo.CNT)||(t.country=encodeURIComponent(n),a=!0),a&&void 0!==_CurPlayer&&_CurPlayer&&UpdateBoatPrefs(_CurPlayer.CurBoat,{prefs:t})}function GetPrefSelFlag(){return $("#CountryDropDown:first-child [flag]")[0].attributes.flag.value}function ComparePrefString(e,t){return e.toString()===t.toString()}function SelectCountryDDFlag(e){$("#CountryDropDown:first-child").html("<div>"+GetCountryDropDownSelectorHTML(e,!1)+'<span class="caret"></span></div>')}function ResetCollapsiblePanels(e){$(".collapse").collapse("hide")}function HandleBoatSelectionChange(e){ResetCollapsiblePanels();var t=GetBoatFromIdu($(e.target).closest("li").attr("BoatID"));void 0!==t&&t?(SetCurrentBoat(t,!0,!1),DisplayCurrentDDSelectedBoat(t)):VLMAlertDanger(GetLocalizedString("Error Reload"))}var LastMouseMoveCall=0,ShowEstTimeOutHandle=null;function HandleMapMouseClick(e){SetWPPending&&("WP"===WPPendingTarget?(CompleteWPSetPosition(e),HandleCancelSetWPOnClick()):"AP"===WPPendingTarget?(SetWPPending=!1,_CurAPOrder.PIP_Coords=new VLMPosition(e.latlng.lng,e.latlng.lat),$("#AutoPilotSettingForm").modal("show"),RefreshAPDialogFields()):SetWPPending=!1)}function HandleMapMouseMove(e){var t=e.latlng;if(void 0!==_CurPlayer&&_CurPlayer&&void 0!==_CurPlayer.CurBoat&&void 0!==_CurPlayer.CurBoat.VLMInfo){var a=new VLMPosition(t.lng,t.lat),o=new VLMPosition(_CurPlayer.CurBoat.VLMInfo.LON,_CurPlayer.CurBoat.VLMInfo.LAT),n=_CurPlayer.CurBoat.GetNextWPPosition(),i=null,r=new Date-LastMouseMoveCall>300;if(VLM2Prefs.MapPrefs.EstTrackMouse&&r&&(i=_CurPlayer.CurBoat.GetClosestEstimatePoint(a),LastMouseMoveCall=new Date,clearTimeout(ShowEstTimeOutHandle),StartEstimateTimeout()),$("#MI_Lat").text(a.Lat.toString()),$("#MI_Lon").text(a.Lon.toString()),$("#MI_LoxoDist").text(o.GetLoxoDist(a,2)+" nM"),$("#MI_OrthoDist").text(o.GetOrthoDist(a,2)+" nM"),$("#MI_Loxo").text(o.GetLoxoCourse(a,2)+" °"),$("#MI_Ortho").text(o.GetOrthoCourse(a,2)+" °"),void 0!==n&&n?($("#MI_WPLoxoDist").text(n.GetLoxoDist(a,2)+" nM"),$("#MI_WPOrthoDist").text(n.GetOrthoDist(a,2)+" nM"),$("#MI_WPLoxo").text(n.GetLoxoCourse(a,2)+" °"),$("#MI_WPOrtho").text(n.GetOrthoCourse(a,2)+" °")):($("#MI_WPLoxoDist").text("--- nM"),$("#MI_WPOrthoDist").text("--- nM"),$("#MI_WPLoxo").text("--- °"),$("#MI_WPOrtho").text("--- °")),GribMgr){var s="-- N/A --",l="-- N/A --",u="-- N/A --";if(GribMgr.LastGribDate){s=moment("/date("+1e3*GribMgr.LastGribDate+")/").fromNow();var d=moment("/date("+1e3*GribMgr.TableTimeStamps[0]+")/"),c=moment("/date("+1e3*GribMgr.TableTimeStamps[GribMgr.TableTimeStamps.length-1]+")/"),p=moment.duration(c.diff(d));l=GetLocalUTCTime(d.add(3.5,"h"),!0,!0),u=p.asHours()+" h";var h=(new Date).getTime()/1e3;h-d.local().unix()>25200?$("#GribLoadOK").addClass("GribNotOK"):h-d.local().unix()>21600?$("#GribLoadOK").addClass("GribGetsOld"):$("#GribLoadOK").removeClass("GribNotOK")}$("#MI_SrvrGribAge").text(s),$("#MI_LocalGribAge").text(l),$("#MI_LocalGribSpan").text(u)}r&&RefreshEstPosLabels(i)}}function StartEstimateTimeout(){ShowEstTimeOutHandle=setTimeout(function(){_CurPlayer.CurBoat.GetClosestEstimatePoint(null),RefreshEstPosLabels(null)},5e3)}function RefreshEstPosLabels(e){e&&void 0!==e.Date?$("#MI_EstDate").text(GetLocalUTCTime(e.Date,!1,!0)):$("#MI_EstDate").text("")}function GetWPrankingLI(e){return'<li id="RnkWP'+e.wporder+'" RnkSort="WP" WPRnk="'+e.wporder+'"><a href="#DivRnkRAC" RnkSort="WP" WPRnk="'+e.wporder+'">WP '+e.wporder+" : "+e.libelle+"</a></li>"}function ResetRankingWPList(e){$("[WPRnk]").remove(),$("#RnkTabsUL").addClass("WPNotInited")}function CheckWPRankingList(e,t){var a=$(".WPNotInited"),o=GetRankingRaceId(e),n=!1;if(void 0!==a&&a&&o)if(void 0!==e&&e&&void 0!==e.RaceInfo&&e.RaceInfo&&o===e.RaceInfo.RaceId)BuildWPTabList(void 0,a),n=!0;else if(t)BuildWPTabList(t,a),n=!0;else{var i=0;void 0!==e.VLMInfo&&(i=e.VLMInfo.VER),$.get("/ws/raceinfo/desc.php?idrace="+o+"&v="+i,function(t){CheckWPRankingList(e,t)})}n&&($(a).removeClass("WPNotInited"),$(".JVLMTabs").tabs("refresh"))}function BuildWPTabList(e,t){var a;if(void 0!==t&&t)for(a in void 0!==e&&e||(e=Boat.RaceInfo.races_waypoints),e.races_waypoints)if(e.races_waypoints[a]){var o=GetWPrankingLI(e.races_waypoints[a]);$(t).append(o)}}function SortRanking(e,t){var a=_CurPlayer.CurBoat;if(CheckWPRankingList(a),void 0!==a&&a){var o=null;switch(a.VLMPrefs&&a.VLMPrefs.mapPrefOpponents&&(o=a.VLMPrefs.mapPrefOpponents.split(",")),e){case"WP":SetRankingColumns(e),SortRankingData(a,e,t=parseInt(t,10)),FillWPRanking(a,t,o);break;case"DNF":case"HC":case"ARR":case"HTP":case"ABD":SetRankingColumns(e),SortRankingData(a,e),FillStatusRanking(a,e,o);break;default:SetRankingColumns("RAC"),SortRankingData(a,"RAC"),FillRacingRanking(a,o)}}}function SetRankingColumns(e){switch(e){case"WP":SetWPRankingColumns();break;case"DNF":case"HC":case"ARR":case"HTP":case"ABD":SetNRClassRankingColumns();break;default:SetRacingClassRankingColumns()}}var RACColumnHeader=["Rank","Name","Distance","Time","Loch","Lon","Lat","Last1h","Last3h","Last24h","Delta1st"],NRColumnHeader=["Rank","Name","Distance"],WPColumnHeader=["Rank","Name","Time","Loch"],RACColumnHeaderLabels=["ranking","boatname","distance","racingtime","Loch","Lon","Lat","Last1h","Last3h","Last24h","ecart"],NRColumnHeaderLabels=["ranking","boatname","status"],WPColumnHeaderLabels=["ranking","boatname","racingtime","ecart"];function SetRacingClassRankingColumns(){SetColumnsVisibility(RACColumnHeader,RACColumnHeaderLabels)}function SetNRClassRankingColumns(){SetColumnsVisibility(NRColumnHeader,NRColumnHeaderLabels)}function SetWPRankingColumns(){SetColumnsVisibility(WPColumnHeader,WPColumnHeaderLabels)}function SetColumnsVisibility(e,t){var a;for(a=0;a<RankingFt.columns.array.length;a++)if(RankingFt.columns.array[a]){var o=e.indexOf(RankingFt.columns.array[a].name);o>-1&&$("[data-name='"+e[o]+"']").attr("I18n",t[o]),RankingFt.columns.array[a].visible=o>-1}LocalizeItem($("[I18n][data-name]").get())}function RnkIsArrived(e){return!(void 0===e||void 0===e.status||!e.status)&&-1!==BoatArrivedStatus.indexOf(e.status)}function RnkIsRacing(e){return!(void 0===e||void 0===e.status||!e.status)&&-1!==BoatRacingStatus.indexOf(e.status)}function Sort2ArrivedBoats(e,t){var a=parseInt(e.duration,10)+parseInt(e.penalty,10),o=parseInt(t.duration,10)+parseInt(t.penalty,10);return a>o?(DebugRacerSort(e,t,1),1):a<o?(DebugRacerSort(e,t,-1),-1):(DebugRacerSort(e,t,0),0)}function Sort2RacingBoats(e,t){var a=parseInt(e.nwp,10),o=parseInt(t.nwp,10);if(a===o){var n=parseFloat(e.dnm),i=parseFloat(t.dnm);if(n>i)return DebugRacerSort(e,t,1),1;if(n===i){DebugRacerSort(e,t,0);var r=e.country>t.country?1:e.country===t.country?0:-1;return r||(e.idusers>t.idusers?1:e.idusers===t.idusers?0:-1)}return DebugRacerSort(e,t,-1),-1}return a>o?(DebugRacerSort(e,t,-1),-1):(DebugRacerSort(e,t,1),1)}function GetWPDuration(e,t){return e&&e.WP&&e.WP[t-1]&&e.WP[t-1].duration?parseInt(e.WP[t-1].duration,10):9999999999}function WPRaceSort(e){return function(t,a){return GetWPDuration(t,e)-GetWPDuration(a,e)}}function RacersSort(e,t){return RnkIsRacing(e)&&RnkIsRacing(t)?Sort2RacingBoats(e,t):RnkIsArrived(e)&&RnkIsArrived(t)?Sort2ArrivedBoats(e,t):RnkIsArrived(e)?(DebugRacerSort(e,t,-1),-1):RnkIsArrived(t)?(DebugRacerSort(e,t,1),1):RnkIsRacing(e)?(DebugRacerSort(e,t,1),-1):RnkIsRacing(t)?(DebugRacerSort(e,t,1),1):Sort2NonRacing(e,t)}var AlertTemplate,DebugCount=1;function DebugRacerSort(e,t,a){}function Sort2NonRacing(e,t){if(void 0!==e.idusers&&void 0!==t.idusers){var a=e.country>t.country?1:e.country===t.country?0:-1;if(a)return a;var o=parseInt(e.idusers,10),n=parseInt(t.idusers,10);return o>n?(DebugRacerSort(e,t,1),1):o<n?(DebugRacerSort(e,t,-1),-1):(DebugRacerSort(e,t,0),0)}if("undefined"!=typeof IdUser1)return-1;if("undefined"!=typeof IdUser2)return-1;var i=[e,t];return i.sort(),i[0]===e?1:-1}function GetRankingRaceId(e,t){return t||RankingFt.RaceRankingId?t||RankingFt.RaceRankingId:e.Engaged}function SortRankingData(e,t,a,o){if(o=GetRankingRaceId(e,o),e&&Rankings[o]){var n;if(Rankings&&Rankings[o]&&void 0===Rankings[o].RacerRanking)for(n in Rankings[o].RacerRanking=[],Rankings[o])Rankings[o][n]&&Rankings[o].RacerRanking.push(Rankings[o][n]);switch(t){case"WP":Rankings[o].RacerRanking.sort(WPRaceSort(a));break;case"RAC":case"DNF":case"HC":case"HTP":case"ABD":case"ARR":Rankings[o].RacerRanking.sort(RacersSort);break;default:VLMAlertInfo("unexpected sort option : "+t)}var i=1,r=0;for(r in Rankings[o].RacerRanking)if(Rankings[o].RacerRanking[r]&&e.IdBoat===r){i=r+1;break}return i}}function FillWPRanking(e,t,a){var o,n=1,i=0,r=[];if(e&&RankingFt&&!RankingFt.DrawPending){var s=GetRankingRaceId(e);for(o in BackupRankingTable(),Rankings[s].RacerRanking)if(Rankings[s].RacerRanking[o]){var l=Rankings[s].RacerRanking[o];l.WP&&l.WP[t-1]&&!l.WP[t-1].Delta&&(i?(l.WP[t-1].Delta=l.WP[t-1].duration-i,l.WP[t-1].Pct=100*(l.WP[t-1].duration/i-1)):(i=l.WP[t-1].duration,l.WP[t-1].Delta=0,l.WP[t-1].Pct=0)),l.WP&&l.WP[t-1]&&(r.push(GetRankingObject(l,parseInt(o,10)+1,t,a)),e.IdBoat===parseInt(l.idusers,10)&&(n=r.length))}var u=RoundPow(n/20,0)+(n%20>=10?0:1);RankingFt.DrawPending=!0,RankingFt.loadRows(r),RankingFt.TargetPage=u}}function BackupICS_WPTable(){BackupFooTable(ICS_WPft,"#RaceWayPoints","#RaceWayPointsInsertPoint")}function getWaypointHTMLSymbols(e){var t="";switch(e&(WP_CROSS_CLOCKWISE|WP_CROSS_ANTI_CLOCKWISE)){case WP_CROSS_ANTI_CLOCKWISE:t+="&#x21BA; ";break;case WP_CROSS_CLOCKWISE:t+="&#x21BB; "}switch((e&WP_CROSS_ONCE)==WP_CROSS_ONCE&&(t+="&#x2285; "),e&(WP_ICE_GATE_N|WP_ICE_GATE_S)){case WP_ICE_GATE_S:t+="&#x27F0;";break;case WP_ICE_GATE_N:t+="&#x27F1;"}return t.trim()}function getWaypointHTMLSymbolsDescription(e){var t="";switch(e&(WP_CROSS_CLOCKWISE|WP_CROSS_ANTI_CLOCKWISE)){case WP_CROSS_ANTI_CLOCKWISE:t+=GetLocalizedString("Anti-clockwise")+" ";break;case WP_CROSS_CLOCKWISE:t+=GetLocalizedString("Clockwise")+" "}switch((e&WP_CROSS_ONCE)==WP_CROSS_ONCE&&(t+=GetLocalizedString("Only once")),e&(WP_ICE_GATE_N|WP_ICE_GATE_S)){case WP_ICE_GATE_S:t+=GetLocalizedString("Ice gate")+"("+GetLocalizedString("South")+") ";break;case WP_ICE_GATE_N:t+=GetLocalizedString("Ice gate")+"("+GetLocalizedString("North")+") "}return""!==t&&(t=GetLocalizedString("Crossing")+" : "+t),t.trim()}function NormalizeRaceInfo(e){if(void 0!==e&&e&&!e.IsNormalized){for(var t in e.startlat/=VLM_COORDS_FACTOR,e.startlong/=VLM_COORDS_FACTOR,e.races_waypoints)if(e.races_waypoints[t]){var a=e.races_waypoints[t];a.latitude1/=VLM_COORDS_FACTOR,a.longitude1/=VLM_COORDS_FACTOR,void 0!==a.latitude2&&(a.latitude2/=VLM_COORDS_FACTOR,a.longitude2/=VLM_COORDS_FACTOR)}e.IsNormalized=!0}}function FillRaceWaypointList(e){if(ICS_WPft.DrawPending)ICS_WPft.CallbackPending||(ICS_WPft.CallbackPending=function(){FillRaceWaypointList(e)});else if(BackupICS_WPTable(),e){NormalizeRaceInfo(e);var t=[],a={WaypointId:0};for(var o in a.WP1=e.startlat+"<BR>"+e.startlong,a.WP2="",a.Spec="",a.Type=GetLocalizedString("startmap"),a.Name="",t.push(a),e.races_waypoints)if(e.races_waypoints[o]){var n=e.races_waypoints[o],i={};i.WaypointId=n.wporder,i.WP1=n.latitude1+"<BR>"+n.longitude1,void 0!==n.latitude2?i.WP2=n.latitude2+"<BR>"+n.longitude2:i.WP2="@"+n.laisser_au,i.Spec="<span title='"+getWaypointHTMLSymbolsDescription(n.wpformat)+"'>"+getWaypointHTMLSymbols(n.wpformat)+"</span>",i.Type=GetLocalizedString(n.wptype),i.Name=n.libelle,t.push(i)}ICS_WPft.loadRows(t)}}function BackupNSZ_Table(){BackupFooTable(NSZ_WPft,"NSZPoints","NSZPointsInsertPoint")}function FillNSZList(e){if(NSZ_WPft.DrawPending)NSZ_WPft.CallbackPending||(NSZ_WPft.CallbackPending=function(){FillNSZList(e)});else if(BackupNSZ_Table(),e){var t=[];for(var a in e)if(e[a]){var o=e[a],n={};n.NSZId=a,n.Lon1=o[0][1],n.Lat1=o[0][0],n.Lon2=o[1][1],n.Lat2=o[1][0],t.push(n)}NSZ_WPft.loadRows(t)}}function BackupRankingTable(){BackupFooTable(RankingFt,"#RankingTable","#my-rank-content")}function BackupVLMIndexTable(){BackupFooTable(VLMINdexFt,"#VLMIndexTable","#my-vlmindex-content")}function FillStatusRanking(e,t,a){var o,n=1,i=[],r=GetRankingRaceId(e);for(o in BackupRankingTable(),Rankings[r].RacerRanking)if(Rankings[r].RacerRanking[o]){var s=Rankings[r].RacerRanking[o];s.status===t&&(i.push(GetRankingObject(s,parseInt(o,10)+1,null,a)),e.IdBoat===parseInt(s.idusers,10)&&(n=i.length))}var l=RoundPow(n/20,0)+(n%20>=10?0:1);RankingFt.loadRows(i),RankingFt.TargetPage=l,RankingFt.DrawPending=!0}function FillRacingRanking(e,t){var a,o=[],n=0,i={Arrived1stTime:null,Racer1stPos:null};BackupRankingTable();var r=GetRankingRaceId(e),s=0;if(r&&void 0!==Rankings&&void 0!==Rankings[r]&&Rankings[r]&&Rankings[r].RacerRanking)for(a in Rankings[r].RacerRanking)if(Rankings[r].RacerRanking[a]){var l=Rankings[r].RacerRanking[a];if(e.IdBoat===parseInt(l.idusers,10)&&(n=o.length),!RnkIsArrived(l)&&!RnkIsRacing(l))break;!i.Arrived1stTime&&RnkIsArrived(l)&&(i.Arrived1stTime=parseInt(l.duration,10)),!RnkIsRacing(l)||i.Racer1stPos&&l.nwp===s||(i.Racer1stPos=l.dnm,s=l.nwp),o.push(GetRankingObject(l,parseInt(a,10)+1,null,t,i))}var u=RoundPow(n/20,0)+(n%20>=10?0:1);RankingFt.loadRows(o),RankingFt.TargetPage=u,RankingFt.DrawPending=!0}function GetBoatInfoLink(e){var t=parseInt(e.idusers,10),a=e.boatname,o="";return e.country&&void 0===(o=GetCountryFlagImgHTML(e.country))&&(o=""),o+='<a class="RaceHistLink" boatid ="'+t+'"data-toggle="tooltip" title="'+t+'" >'+a+"</a>"}function GetRankingObject(e,t,a,o,n){var i="";void 0!==e.Challenge&&e.Challenge[1]&&(i='<img class="RnkLMNH" src="images/LMNH.png"></img>'+i);var r={Rank:t,Name:i+=GetBoatInfoLink(e),Distance:"",Time:"",Loch:"",Lon:"",Lat:"",Last1h:"",Last3h:"",Last24h:"",Class:"",Delta1st:""};if(parseInt(e.idusers,10)===_CurPlayer.CurBoat.IdBoat&&(r.Class+=" ft_class_myboat"),void 0!==o&&o&&-1!==o.indexOf(e.idusers)&&(r.Class+=" ft_class_friend"),RnkIsRacing(e)&&!a){var s="["+e.nwp+"] -=> "+RoundPow(e.dnm,2);if(t>1&&n&&n.Racer1stPos){new VLMPosition(e.longitude,e.latitude);r.Delta1st=RoundPow(e.dnm-n.Racer1stPos,2)}r.Distance=s;var l=Math.round((new Date-new Date(1e3*parseInt(e.deptime,10)))/1e3);for(var u in r.Time="-1"===e.deptime?"":GetFormattedChronoString(l),r.Loch=e.loch,r.Lon=FormatLon(e.longitude),r.Lat=FormatLat(e.latitude),r.Last1h=e.last1h,r.Last3h=e.last3h,r.Last24h=e.last24h,BoatRacingStatus)e.status===BoatRacingStatus[u]&&(r.Class+="  "+BoatRacingClasses[BoatRacingStatus[u]])}else if(a){var d;if(r.Time=GetFormattedChronoString(parseInt(e.WP[a-1].duration,10)),e.WP[a-1].Delta){var c=RoundPow(e.WP[a-1].Pct,2);d=GetFormattedChronoString(e.WP[a-1].Delta)+" (+"+c+" %)"}else d=GetLocalizedString("winner");r.Loch=d}else{var p=GetLocalizedString("status_"+e.status);r.Distance=p;var h=parseInt(e.duration,10);r.Time=GetFormattedChronoString(h),n&&h!==n.Arrived1stTime?(r.Time+=" ( +"+RoundPow(h/n.Arrived1stTime*100-100,2)+"% )",r.Delta1st=GetFormattedChronoString(h-n.Arrived1stTime)):n&&h==n.Arrived1stTime&&(r.Delta1st=GetLocalizedString("winner")),r.Loch=e.loch}return r}function formatCoords(e){e=Math.abs(e);var t=Math.trunc(e);return t+"° "+Math.trunc(60*(e-t))+"' "+RoundPow(3600*(e-t)%60,4)+'"'}function FormatLon(e){var t=e>0?"W":"E";return formatCoords(e)+t}function FormatLat(e){var t=e>0?"N":"S";return formatCoords(e)+t}function HandleShowMapPrefs(e){$("#DisplayReals").attr("checked",VLM2Prefs.MapPrefs.ShowReals),$("#DisplayNames").attr("checked",VLM2Prefs.MapPrefs.ShowOppNumbers),$("#EstTrackMouse").attr("checked",VLM2Prefs.MapPrefs.EstTrackMouse),$("#TrackEstForecast").attr("checked",VLM2Prefs.MapPrefs.TrackEstForecast),$("#UseUTC").attr("checked",VLM2Prefs.MapPrefs.UseUTC),$("#DDMapSelOption:first-child").html("<span Mode="+VLM2Prefs.MapPrefs.MapOppShow+">"+VLM2Prefs.MapPrefs.GetOppModeString(VLM2Prefs.MapPrefs.MapOppShow)+'</span><span class="caret"></span>'),VLM2Prefs.MapPrefs.MapOppShow===VLM2Prefs.MapPrefs.MapOppShowOptions.ShowTop10?($("#NbDisplayBoat").removeClass("hidden"),$("#NbDisplayBoat").val(VLM2Prefs.MapPrefs.ShowTopCount)):$("#NbDisplayBoat").addClass("hidden"),$("#VacPol").val(VLM2Prefs.MapPrefs.PolarVacCount)}function HandleMapPrefOptionChange(e){var t=e.target;if(void 0!==t&&void 0!==t.attributes.id){var a=t.attributes.id.value,o=t.checked;switch(a){case"DisplayReals":case"ShowReals":case"UseUTC":case"DisplayNames":case"ShowOppNumbers":case"EstTrackMouse":case"TrackEstForecast":VLM2Prefs.MapPrefs[a]=o;break;case"VacPol":var n=parseInt($("#VacPol").val(),10);n>0&&n<120?VLM2Prefs.MapPrefs.PolarVacCount=n:$("#VacPol").value(12);break;case"NbDisplayBoat":var i=parseInt($("#NbDisplayBoat").val(),10);VLM2Prefs.MapPrefs.ShowTopCount=i;break;default:return void console.log("unknown pref storage called : "+a)}VLM2Prefs.Save(),RefreshCurrentBoat(!1,!1)}}function SafeHTMLColor(e){return void 0===e&&(e="#000000"),(e=""+e).length<6&&(e=("000000"+e).slice(-6)),"#"!==e.substring(0,1)?e="#"+e:"#"===e.substring(1,2)&&(e=e.substring(1)),e}function HandleMapOppModeChange(e){var t=e.target;if(void 0!==t&&t&&void 0!==t.attributes&&"undefined"!==t.attributes.Mode&&t.attributes.Mode){var a=parseInt(t.attributes.Mode.value,10);VLM2Prefs.MapPrefs.MapOppShow=a,VLM2Prefs.Save(),HandleShowMapPrefs(e)}}function SetActiveStyleSheet(e){var t,a;for(t=0;a=document.getElementsByTagName("link")[t];t++)-1!==a.getAttribute("rel").indexOf("style")&&a.getAttribute("title")&&(a.disabled=!0,a.getAttribute("title")===e&&(a.disabled=!1))}function SetDDTheme(e){SetActiveStyleSheet(e),$("#SelectionThemeDropDown:first-child").html(e+'<span class="caret"></span>'),$("#SelectionThemeDropDown").attr("SelTheme",e)}function HandleDDlineClick(e){e.target;SetDDTheme(e.target.attributes.ddtheme.value)}function InitAlerts(){$("#AlertBox").css("display","block"),AlertTemplate=$("#AlertBox")[0],$("#AlertBoxContainer").empty(),$("#AlertBoxContainer").removeClass("hidden")}function VLMAlertSuccess(e){VLMAlert(e,"alert-success")}function VLMAlertDanger(e){VLMAlert(e,"alert-danger")}function VLMAlertInfo(e){VLMAlert(e,"alert-info")}var AlertIntervalId=null;function VLMAlert(e,t){AlertIntervalId&&clearInterval(AlertIntervalId),void 0!==t&&t||(t="alert-info"),$("#AlertBoxContainer").empty().append(AlertTemplate).show(),$("#AlertText").text(e),$("#AlertBox").removeClass("alert-sucess"),$("#AlertBox").removeClass("alert-warning"),$("#AlertBox").removeClass("alert-info"),$("#AlertBox").removeClass("alert-danger"),$("#AlertBox").addClass(t),$("#AlertBox").show(),$("#AlertCloseBox").unbind().on("click",AutoCloseVLMAlert),AlertIntervalId&&clearInterval(AlertIntervalId),AlertIntervalId=setTimeout(AutoCloseVLMAlert,5e3)}function AutoCloseVLMAlert(){$("#AlertBox").hide()}function GetUserConfirmation(e,t,a){$("#ConfirmDialog").modal("show"),t?($("#OKBtn").hide(),$("#CancelBtn").hide(),$("#YesBtn").show(),$("#NoBtn").show()):($("#OKBtn").show(),$("#CancelBtn").show(),$("#YesBtn").hide(),$("#NoBtn").hide()),$("#ConfirmText").text(e),$(".OKBtn").unbind().on("click",function(){$("#ConfirmDialog").modal("hide"),a(!0)}),$(".NOKBtn").unbind().on("click",function(){$("#ConfirmDialog").modal("hide"),a(!1)})}function GetRaceRankingLink(e){return'<a href="/jvlm?RaceRank='+e.idrace+'" target="RankTab">'+e.racename+"</a>"}function FillBoatPalmares(e,t,a,o,n,i){var r;if("success"===t){var s=[];for(r in e.palmares)if(e.palmares[r]){var l=e.palmares[r],u={RaceId:e.palmares[r].idrace,RaceName:GetRaceRankingLink(e.palmares[r]),Ranking:l.ranking.rank+" / "+l.ranking.racercount};s.push(u)}RaceHistFt.loadRows(s)}var d=GetLocalizedString("palmares");d=d.replace("%s",e.boat.name),$("#palmaresheaderline").text(d)}function ShowUserRaceHistory(e){$("#RaceHistory").modal("show"),$.get("/ws/boatinfo/palmares.php?idu="+e,function(e,t,a,o,n,i){FillBoatPalmares(e,t,a,o,n,i)})}function HandleShowBoatRaceHistory(e){var t=$(e.target).attr("boatid");t&&ShowUserRaceHistory(t)}function HandleCreateUserResult(e,t){if("success"===t&&e)if($(".ValidationMark").addClass("hidden"),e.success?($(".ValidationMark.Valid").removeClass("hidden"),VLMAlertSuccess(GetLocalizedString("An email has been sent. Click on the link to validate.")),$("#InscriptForm").modal("hide"),$("#LoginForm").modal("hide")):e.request&&e.request.errorstring?VLMAlertDanger(GetLocalizedString(e.request.errorstring)):VLMAlertDanger(GetLocalizedString(e.error.msg)),e.request)e.request.MailOK?$(".ValidationMark.Email.Valid").removeClass("hidden"):$(".ValidationMark.Email.Invalid").removeClass("hidden"),e.request.PasswordOK?$(".ValidationMark.Password.Valid").removeClass("hidden"):$(".ValidationMark.Password.Invalid").removeClass("hidden"),e.request.PlayerNameOK?$(".ValidationMark.Pseudo.Valid").removeClass("hidden"):$(".ValidationMark.Pseudo.Invalid").removeClass("hidden");else if(e.error)switch(e.error.code){case"NEWPLAYER01":$(".ValidationMark.Email.Invalid").removeClass("hidden");break;case"NEWPLAYER02":$(".ValidationMark.Pseudo.Invalid").removeClass("hidden");break;case"NEWPLAYER03":$(".ValidationMark.Password.Invalid").removeClass("hidden")}$("#BtnCreateAccount").show()}function HandleCreateUser(){var e=$("#NewPlayerPseudo")[0].value,t={emailid:$("#NewPlayerEMail")[0].value,password:$("#NewPlayerPassword")[0].value,pseudo:e};$("#BtnCreateAccount").hide(),$.post("/ws/playerinfo/player_create.php",t,function(e,t){HandleCreateUserResult(e,t)})}function setModalMaxHeight(e){var t=$(e),a=t.find(".modal-content"),o=a.outerHeight()-a.innerHeight(),n=$(window).width()<768?20:60,i=$(window).height()-(n+o)-((t.find(".modal-header").outerHeight()||0)+(t.find(".modal-footer").outerHeight()||0));a.css({overflow:"hidden"}),t.find(".modal-body").css({"max-height":i,"overflow-y":"auto"})}function GetLocalUTCTime(e,t,a){var o=e,n="";return moment.isMoment(e)||(o=t?moment(e).utc():moment(e)),VLM2Prefs.MapPrefs.UseUTC?(o.isLocal()&&(o=o.utc()),n=" Z"):o.isLocal()||(o=o.local()),a?o.format("LLLL")+n:o}if("undefined"==typeof jQuery)throw new Error("jQuery progress timer requires jQuery");!function(e,t,a,o){var n="progressTimer",i={timeLimit:60,warningThreshold:5,onFinish:function(){},baseStyle:"",warningStyle:"progress-bar-danger",completeStyle:"progress-bar-success",showHtmlSpan:!0,errorText:"ERROR!",successText:"100%"},r=function(t,a){this.element=t,this.$elem=e(t),this.options=e.extend({},i,a),this._defaults=i,this._name=n,this.metadata=this.$elem.data("plugin-options"),this.init()};r.prototype.constructor=r,r.prototype.init=function(){var a=this;return e(a.element).empty(),a.span=e("<span/>"),a.barContainer=e("<div>").addClass("progress"),a.bar=e("<div>").addClass("progress-bar active progress-bar-striped").addClass(a.options.baseStyle).attr("role","progressbar").attr("aria-valuenow","0").attr("aria-valuemin","0").attr("aria-valuemax",a.options.timeLimit),a.span.appendTo(a.bar),a.options.showHtmlSpan||a.span.addClass("sr-only"),a.bar.appendTo(a.barContainer),a.barContainer.appendTo(a.element),a.start=new Date,a.limit=1e3*a.options.timeLimit,a.warningThreshold=1e3*a.options.warningThreshold,a.interval=t.setInterval(function(){a._run.call(a)},250),a.bar.data("progress-interval",a.interval),!0},r.prototype.destroy=function(){this.$elem.removeData()},r.prototype._run=function(){var e=this,t=new Date-e.start,a=t/e.limit*100;e.bar.attr("aria-valuenow",a),e.bar.width(a+"%");var o=a.toFixed(2);return o>=100&&(o=100),e.options.showHtmlSpan&&e.span.html(o+"%"),t>=e.warningThreshold&&e.bar.removeClass(this.options.baseStyle).removeClass(this.options.completeStyle).addClass(this.options.warningStyle),t>=e.limit&&e.complete.call(e),!0},r.prototype.removeInterval=function(){var a=e(".progress-bar",this.element);if(void 0!==a.data("progress-interval")){var o=a.data("progress-interval");t.clearInterval(o)}return a},r.prototype.complete=function(){var t=this,a=t.removeInterval.call(t),o=arguments;0!==o.length&&"object"===_typeof(o[0])&&(t.options=e.extend({},t.options,o[0])),a.removeClass(t.options.baseStyle).removeClass(t.options.warningStyle).addClass(t.options.completeStyle),a.width("100%"),t.options.showHtmlSpan&&e("span",a).html(t.options.successText),a.attr("aria-valuenow",100),setTimeout(function(){t.options.onFinish.call(a)},500),t.destroy.call(t)},r.prototype.error=function(){var t=this,a=t.removeInterval.call(t),o=arguments;0!==o.length&&"object"===_typeof(o[0])&&(t.options=e.extend({},t.options,o[0])),a.removeClass(t.options.baseStyle).addClass(t.options.warningStyle),a.width("100%"),t.options.showHtmlSpan&&e("span",a).html(t.options.errorText),a.attr("aria-valuenow",100),setTimeout(function(){t.options.onFinish.call(a)},500),t.destroy.call(t)},e.fn[n]=function(t){var a=arguments;if(t===o||"object"===_typeof(t))return this.each(function(){e.data(this,"plugin_"+n)||e.data(this,"plugin_"+n,new r(this,t))});if("string"==typeof t&&"_"!==t[0]&&"init"!==t){if(0===Array.prototype.slice.call(a,1).length&&-1!==e.inArray(t,e.fn[n].getters)){var i=e.data(this[0],"plugin_"+n);return i[t].apply(i,Array.prototype.slice.call(a,1))}return this.each(function(){var o=e.data(this,"plugin_"+n);o instanceof r&&"function"==typeof o[t]&&o[t].apply(o,Array.prototype.slice.call(a,1))})}},e.fn[n].getters=["complete","error"]}(jQuery,window,document,void 0),function(e){if(e.support.touch="ontouchend"in document,e.support.touch){var t,a=e.ui.mouse.prototype,o=a._mouseInit,n=a._mouseDestroy;a._touchStart=function(e){!t&&this._mouseCapture(e.originalEvent.changedTouches[0])&&(t=!0,this._touchMoved=!1,i(e,"mouseover"),i(e,"mousemove"),i(e,"mousedown"))},a._touchMove=function(e){t&&(this._touchMoved=!0,i(e,"mousemove"))},a._touchEnd=function(e){t&&(i(e,"mouseup"),i(e,"mouseout"),this._touchMoved||i(e,"click"),t=!1)},a._mouseInit=function(){this.element.bind({touchstart:e.proxy(this,"_touchStart"),touchmove:e.proxy(this,"_touchMove"),touchend:e.proxy(this,"_touchEnd")}),o.call(this)},a._mouseDestroy=function(){this.element.unbind({touchstart:e.proxy(this,"_touchStart"),touchmove:e.proxy(this,"_touchMove"),touchend:e.proxy(this,"_touchEnd")}),n.call(this)}}function i(e,t){if(!(e.originalEvent.touches.length>1)){e.preventDefault();var a=e.originalEvent.changedTouches[0],o=document.createEvent("MouseEvents");o.initMouseEvent(t,!0,!0,window,1,a.screenX,a.screenY,a.clientX,a.clientY,!1,!1,!1,!1,0,null),e.target.dispatchEvent(o)}}}(jQuery);var _LocaleDict,_EnDict,BuoyMarker=L.Icon.extend({options:{iconSize:[36,72],iconAnchor:[18,36],popupAnchor:[0,-36]}}),TrackWPMarker=L.Icon.extend({options:{iconSize:[48,48],iconAnchor:[24,24],iconUrl:"images/WP_Marker.gif"}}),BOAT_MARKET_SIZE=48,BOAT_EST_MARKET_SIZE=24,BoatMarker=L.Icon.extend({options:{iconSize:[BOAT_MARKET_SIZE,BOAT_MARKET_SIZE],iconAnchor:[BOAT_MARKET_SIZE/2,BOAT_MARKET_SIZE/2],iconUrl:"images/target.png",rotationOrigin:[BOAT_MARKET_SIZE/2,BOAT_MARKET_SIZE/2]}}),BoatEstMarker=L.Icon.extend({options:{iconSize:[BOAT_EST_MARKET_SIZE,BOAT_EST_MARKET_SIZE],iconAnchor:[BOAT_EST_MARKET_SIZE/2,BOAT_EST_MARKET_SIZE/2],iconUrl:"images/target.png",rotationOrigin:[BOAT_EST_MARKET_SIZE/2,BOAT_EST_MARKET_SIZE/2]}}),IceGateMarker=L.Icon.extend({options:{iconSize:[48,48],iconAnchor:[24,-18],iconUrl:"images/icegate.png"}}),GateDirMarker=L.Icon.extend({options:{iconSize:[48,48],iconAnchor:[24,24],rotationOrigin:[24,24]}});function GetBuoyMarker(e){var t=null;return(t=new BuoyMarker(e?{iconUrl:"images/Buoy1.png"}:{iconUrl:"images/Buoy2.png",color:"red"})).IsCWBuoy=e,t}function GetBoatMarker(e){var t=new BoatMarker;return t.MarkerOppId=e,t}function GetBoatEstimateMarker(){return new BoatEstMarker}function GetTrackWPMarker(){return new TrackWPMarker}function GetGateTypeMarker(e,t){return t?IceGateMarker:new GateDirMarker({iconUrl:"images/"+e})}function GetOpponentMarker(e){var t=new L.Icon({iconUrl:"images/opponent"+e.IsTeam+".png",iconAnchor:[e.IsFriend/2,e.IsFriend/2],iconSize:[e.IsFriend,e.IsFriend]});return t.MarkerOppId=e.idboat,t}function ClearCurrentMapMarker(e){e&&e.RaceMapFeatures&&(e.RaceMapFeatures.OppPopup&&e.RaceMapFeatures.OppPopup.PrevOpp&&e.RaceMapFeatures.OppPopup.PrevOpp.unbindPopup(e.RaceMapFeatures.OppPopup),RemoveFromMap(e.RaceMapFeatures))}function EnsureMarkersVisible(e){e&&RestoreMarkersOnMap(e.RaceMapFeatures)}function RestoreMarkersOnMap(e){if(e&&"function"!=typeof e)if(Array.isArray(e))for(var t in e)RestoreMarkersOnMap(e[t]);else if("object"===_typeof(e)&&void 0===e._leaflet_id)for(var a in e)RestoreMarkersOnMap(e[a]);else e._leaflet_id&&!e._map&&e.addTo(map)}function RemoveFromMap(e){if(e&&"function"!=typeof e)if(Array.isArray(e))for(var t in e)RemoveFromMap(e[t]);else if("object"===_typeof(e)&&void 0===e._leaflet_id)for(var a in e)RemoveFromMap(e[a]);else e._leaflet_id&&e.removeFrom(map)}!function(){var e=L.Marker.prototype._initIcon,t=L.Marker.prototype._setPos,a="msTransform"===L.DomUtil.TRANSFORM;L.Marker.addInitHook(function(){var e=this.options.icon&&this.options.icon.options&&this.options.icon.options.iconAnchor;e&&(e=e[0]+"px "+e[1]+"px"),this.options.rotationOrigin=this.options.rotationOrigin||e||"center bottom",this.options.rotationAngle=this.options.rotationAngle||0,this.on("drag",function(e){e.target._applyRotation()})}),L.Marker.include({_initIcon:function(){e.call(this)},_setPos:function(e){t.call(this,e),this._applyRotation()},_applyRotation:function(){this.options.rotationAngle&&(this._icon.style[L.DomUtil.TRANSFORM+"Origin"]=this.options.rotationOrigin,a?this._icon.style[L.DomUtil.TRANSFORM]="rotate("+this.options.rotationAngle+"deg)":this._icon.style[L.DomUtil.TRANSFORM]+=" rotateZ("+this.options.rotationAngle+"deg)")},setRotationAngle:function(e){return this.options.rotationAngle=e,this.update(),this},setRotationOrigin:function(e){return this.options.rotationOrigin=e,this.update(),this}})}();var _CurLocale="en";function LocalizeString(){return LocalizeItem($("[I18n]").get()),$(".LngFlag").click(function(e,t){OnLangFlagClick($(this).attr("lang")),UpdateLngDropDown()}),!0}function OnLangFlagClick(e){InitLocale(e)}function LocalizeItem(e){try{var t;for(t in e){var a=e[t],o=a.attributes.I18n.value;void 0!==_LocaleDict&&(a.innerHTML=GetLocalizedString(o))}}finally{}return!0}function InitLocale(e){var t="/ws/serverinfo/translation.php";e&&(t+="?lang="+e),$.get(t,function(e){1==e.success?(_CurLocale=e.request.lang,_LocaleDict=e.strings,moment.locale(_CurLocale),LocalizeString(),UpdateLngDropDown()):alert("Localization string table load failure....")}),void 0===_EnDict&&$.get("/ws/serverinfo/translation.php?lang=en",function(e){1==e.success?_EnDict=e.strings:alert("Fallback localization string table load failure....")})}function HTMLDecode(e){var t=document.createElement("textarea");t.innerHTML=e;var a=t.value,o=["\n\r","\r\n","\n","\r"];for(var n in o)for(;o[n]&&-1!==a.indexOf(o[n]);)a=a.replace(o[n],"<br>");return a}function GetLocalizedString(e){return void 0!==_LocaleDict&&_LocaleDict&&e in _LocaleDict?HTMLDecode(_LocaleDict[e]):void 0!==_EnDict&&_EnDict&&e in _EnDict?HTMLDecode(_EnDict[e]):e}function GetCurrentLocale(){return _CurLocale}var VLMMercatorTransform=new MercatorTransform;function MercatorTransform(){this.Width=1e4,this.Height=1e4,this.LonOffset=0,this.LatOffset=0,this.Scale=1e4/180,this.LonToMapX=function(e){return this.Width/2+(e-this.LonOffset)*this.Scale},this.LatToMapY=function(e){return e=Deg2Rad(e),e=Rad2Deg(e=Math.log(Math.tan(e)+1/Math.cos(e))),this.Height/2-(e-this.LatOffset)*this.Scale},this.SegmentsIntersect=function(e,t){var a=this.LonToMapX(e.P1.Lon.Value),o=this.LatToMapY(e.P1.Lat.Value),n=this.LonToMapX(e.P2.Lon.Value),i=this.LatToMapY(e.P2.Lat.Value),r=this.LonToMapX(t.P1.Lon.Value),s=this.LatToMapY(t.P1.Lat.Value),l=this.LonToMapX(t.P2.Lon.Value),u=this.LatToMapY(t.P2.Lat.Value);if(e.P1.Lon.Value===e.P2.Lon.Value&&e.P1.Lat.Value===e.P2.Lat.Value||t.P1.Lon.Value===t.P2.Lon.Value&&t.P1.Lat.Value===t.P2.Lat.Value)return!1;n-=a,i-=o,r-=a,s-=o,l-=a,u-=o,a=0,o=0;var d=Math.sqrt(n*n+i*i),c=n/d,p=i/d,h=r*c+s*p;if(s=s*c-r*p,r=h,h=l*c+u*p,u=u*c-l*p,l=h,s===u)return!1;var P=l+(r-l)*u/(u-s),f=P/d;if(f>=0&&f<=1){var g,L=o;if(l-r)g=(a+P-r)/(l-r);else{if(!(u-s))return!1;g=(L-s)/(u-s)}return g>=0&&g<=1}return!1}}var PolarManagerClass=function e(){_classCallCheck(this,e),this.Polars=[],this.PolarLoaderQueue={},this.Init=function(){this.Polars=[],$.get("/ws/polarlist.php",function(e){for(var t in e.list)PolarsManager.Polars["boat_"+e.list[t]]=null})},this.GetBoatSpeed=function(e,t,a,o){if(!(e in this.Polars))return NaN;if(this.Polars[e]){var n=WindAngle(o,a);return GetPolarAngleSpeed(this.Polars[e],n,t)}return this.LoadPolar(e,null,null),NaN},this.HandlePolarLoaded=function(e,t,a){var o=$.csv.toArrays(a,{separator:";"});for(var n in o)if(o[n])for(var i in o[n])o[n][i]&&(o[n][i]=parseFloat(o[n][i]));for(var r in PolarsManager.Polars[e]={},PolarsManager.Polars[e].SpeedPolar=o,PolarsManager.Polars[e].WindLookup=[],PolarsManager.Polars[e].AngleLookup=[],this.PolarLoaderQueue[e].callbacks){var s=this.PolarLoaderQueue[e].callbacks[r];s&&t?s(t):s&&s()}this.PolarLoaderQueue[e]=null},this.GetPolarLine=function(e,t,a,o,n){if(n||(n=5),void 0===this.Polars[e])return alert("Unexpected polarname : "+e),null;if(null!==this.Polars[e]){var i,r=[],s=0;for(i=0;i<=180;i+=n){var l=GetPolarAngleSpeed(this.Polars[e],i,t);s<l&&(s=l),r.push(l)}for(var u in r)r[u]&&(r[u]/=s);return r}this.LoadPolar(e,a,o)};var t=0;this.GetVMGCourse=function(e,a,o,n,i){for(var r=n.GetOrthoCourse(i),s=0,l=-1e10,u=-1;u<=1;u+=2)for(var d=0;d<=90;d+=.1){var c=this.GetBoatSpeed(e,a,o,r+d*u),p=c*Math.cos(Deg2Rad(d));t&&console.log("VMG "+RoundPow((r+d*u+360)%360,3)+" "+RoundPow(c,3)+" "+RoundPow(p,3)+" "+RoundPow(l,3)+" "+(p>=l?"BEST":"")),p>=l&&(l=p,s=r+d*u)}return t=0,s},this.GetVBVMGCourse=function(e,t,a,o,n){var i=o.GetOrthoDist(n),r=o.GetOrthoCourse(n),s=0,l=0,u=0,d=0,c=0,p=1,h=this.GetBoatSpeed(e,t,a,r);c=h>0?i/h:8760;var P=a-r;P<-90?P+=360:P>90&&(P-=360),p=P>0?-1:1;for(var f=1;f<=90;f++){var g=f*Math.PI/180,L=Math.tan(g),M=Math.sqrt(1+L*L),m=this.GetBoatSpeed(e,t,a,r-f*p);if(isNaN(m))throw"Nan SpeedT1 exception";if(m>0)for(var C=-89;C<=0;C++){var v=C*Math.PI/180,I=i*(Math.tan(-v)/(L+Math.tan(-v))),_=I*M/m;if(!(_<0||_>c)){var R=i-I,S=this.GetBoatSpeed(e,t,a,r-C*p);if(isNaN(S))throw"Nan SpeedT2 exception";if(!(S<=0)){var T=Math.tan(-v),k=_+R*Math.sqrt(1+T*T)/S;k<c&&(c=k,s=f,l=C,u=m,d=S)}}}}var D=u*Math.cos(Deg2Rad(s)),w=d*Math.cos(Deg2Rad(l));if(isNaN(D)||isNaN(w))throw"NaN VMG found";return D>w?r-s*p:r-l*p},this.GetPolarMaxSpeed=function(e,t){if(!this.Polars[e])return null;var a,o=0;for(a=0;a<=180;a+=1){var n=GetPolarAngleSpeed(this.Polars[e],a,t);n>o&&(o=n)}return o},this.LoadPolar=function(e,t,a){this.PolarLoaderQueue[e]?t&&this.PolarLoaderQueue[e].callbacks.push(t):(this.PolarLoaderQueue[e]={},this.PolarLoaderQueue[e].callbacks=[],t&&this.PolarLoaderQueue[e].callbacks.push(t),$.get("/Polaires/"+e+".csv",this.HandlePolarLoaded.bind(this,e,a)))}},PolarsManager=new PolarManagerClass;function GetPolarAngleSpeed(e,t,a){var o,n,i,r,s=e.SpeedPolar,l=Math.floor(a);if(void 0!==e.WindLookup&&l in e.WindLookup)o=e.WindLookup[l];else for(var u in s[0]){if(u>0&&s[0][u]>a)break;e.WindLookup[l]=Math.floor(u),o=Math.floor(u)}for(n=o<s[0].length-1?o+1:o;t<0;)t+=360;t>=360&&(t%=360),t>180&&(t=360-t);var d=Math.floor(t);if(void 0!==e.AngleLookup&&d in e.AngleLookup)i=e.AngleLookup[d];else for(var c in s){if(c>0&&s[c][0]>t)break;e.AngleLookup[d]=Math.floor(c),i=Math.floor(c)}r=i<s.length-1?i+1:i;var p=GetAvgValue(a,s[0][o],s[0][n],s[i][o],s[i][n]),h=GetAvgValue(a,s[0][o],s[0][n],s[r][o],s[r][n]),P=GetAvgValue(t,s[i][0],s[r][0],p,h);if(isNaN(P))throw"GetAvgValue was NaN";return P}function WindAngle(e,t){return e>=t?e-t<=180?e-t:360-e+t:t-e<=180?t-e:360-t+e}function GetAvgValue(e,t,a,o,n){return e===t||t===a||o===n?o:o+(e-t)/(a-t)*(n-o)}var _IsLoggedIn,POS_FORMAT_DEFAULT=0,EARTH_RADIUS=3443.84,VLM_DIST_ORTHO=1;function Deg2Rad(e){return e/180*Math.PI}function Rad2Deg(e){return e/Math.PI*180}function RoundPow(e,t){if(void 0!==t){var a=Math.pow(10,t);return Math.round(e*a)/a}return e}function NormalizeLongitudeDeg(e){return e<-180?e+=360:e>180&&(e-=360),e}function VLMPosition(e,t,a){void 0!==a&&a!=POS_FORMAT_DEFAULT||(this.Lon=new Coords(e,1),this.Lat=new Coords(t,0)),this.toString=function(e){return this.Lat.toString(e)+" "+this.Lon.toString(e)},this.GetEuclidianDist2=function(e){var t=(this.Lat.Value-e.Lat.Value)%90,a=(this.Lon.Value-e.Lon.Value)%180;return t*t+a*a},this.GetLoxoDist=function(e,t){var a,o=Deg2Rad(this.Lat.Value),n=Deg2Rad(e.Lat.Value),i=-Deg2Rad(this.Lon.Value),r=-Deg2Rad(e.Lon.Value),s=0;return s=Math.abs(n-o)<Math.sqrt(1e-15)?Math.cos(o):(n-o)/Math.log(Math.tan(n/2+Math.PI/4)/Math.tan(o/2+Math.PI/4)),a=Math.sqrt(Math.pow(n-o,2)+s*s*(r-i)*(r-i)),RoundPow(EARTH_RADIUS*a,t)},this.ReachDistLoxo=function(e,t){var a=0,o=0;if(isNaN(t))throw"unsupported reaching NaN distance";"number"==typeof e?(a=Deg2Rad(e/60),o=Deg2Rad(t%360)):(a=this.GetLoxoDist(e)/EARTH_RADIUS*t,o=Deg2Rad(this.GetLoxoCourse(e)));var n=Deg2Rad(this.Lat.Value),i=Deg2Rad(this.Lon.Value),r=0,s=0,l=(n+(r=n+a*Math.cos(o)))/2;if((s=i+a*Math.sin(o)/Math.cos(l))>Math.PI?s-=2*Math.PI:s<-Math.PI&&(s+=2*Math.PI),isNaN(s)||isNaN(r))throw"Reached Nan Position!!!";return s=Rad2Deg(s),r=Rad2Deg(r),new VLMPosition(NormalizeLongitudeDeg(s),r)},this.GetLoxoCourse=function(e,t){var a=-Deg2Rad(this.Lon.Value),o=-Deg2Rad(e.Lon.Value),n=Deg2Rad(this.Lat.Value),i=Deg2Rad(e.Lat.Value);void 0!==t&&"number"==typeof t||(t=17);var r=(o-a)%(2*Math.PI),s=(a-o)%(2*Math.PI),l=Math.log(Math.tan(i/2+Math.PI/4)/Math.tan(n/2+Math.PI/4));return RoundPow((720-(r<s?Math.atan2(r,l)%(2*Math.PI):Math.atan2(-s,l)%(2*Math.PI))/Math.PI*180)%360,t)},VLM_DIST_ORTHO?(this.GetOrthoDist=function(e,t){var a=-Deg2Rad(this.Lon.Value),o=-Deg2Rad(e.Lon.Value),n=Deg2Rad(this.Lat.Value),i=Deg2Rad(e.Lat.Value);return void 0!==t&&"number"==typeof t||(t=17),RoundPow(60*Rad2Deg(Math.acos(Math.sin(n)*Math.sin(i)+Math.cos(n)*Math.cos(i)*Math.cos(a-o))),t)},this.GetOrthoCourse=function(e,t){var a,o,n,i,r=Deg2Rad(this.Lon.Value),s=Deg2Rad(e.Lon.Value),l=Deg2Rad(this.Lat.Value),u=Deg2Rad(e.Lat.Value);return void 0!==t&&"number"==typeof t||(t=17),o=Math.fmod(s-r,2*Math.PI),Math.abs(o)<1e-7?a=(i=u-l)>0?0:Math.PI:(o<=-Math.PI?o+=2*Math.PI:o>Math.PI&&(o-=2*Math.PI),n=Math.acos(Math.sin(u)*Math.sin(l)+Math.cos(u)*Math.cos(l)*Math.cos(o)),i=Math.cos(l)*Math.sin(n),a=o<0?2*Math.PI-Math.acos((Math.sin(u)-Math.sin(l)*Math.cos(n))/i):Math.acos((Math.sin(u)-Math.sin(l)*Math.cos(n))/i)),RoundPow(a=Rad2Deg(a%(2*Math.PI)),t)}):(this.GetOrthoDist=function(e,t){var a=-Deg2Rad(this.Lon.Value),o=-Deg2Rad(e.Lon.Value),n=Deg2Rad(this.Lat.Value),i=Deg2Rad(e.Lat.Value);void 0!==t&&"number"==typeof t||(t=17);var r=2*Math.asin(Math.sqrt(Math.pow(Math.sin((n-i)/2),2)+Math.pow(Math.cos(n)*Math.cos(i)*Math.sin((a-o)/2),2)));return RoundPow(EARTH_RADIUS*r,t)},this.GetOrthoCourse=function(e,t){var a=-Deg2Rad(this.Lon.Value),o=-Deg2Rad(e.Lon.Value),n=Deg2Rad(this.Lat.Value),i=Deg2Rad(e.Lat.Value);void 0!==t&&"number"==typeof t||(t=17);var r=Math.atan2(Math.sin(a-o)*Math.cos(i),Math.cos(n)*Math.sin(i)-Math.sin(n)*Math.cos(i)*Math.cos(a-o));return RoundPow(r=Rad2Deg(r%(2*Math.PI)),t)}),this.ReachDistOrtho=function(t,a){var o,n,i=t/EARTH_RADIUS,r=Deg2Rad(a),s=Deg2Rad(this.Lat.Value),l=Deg2Rad(-this.Lon.Value);return o=Math.asin(Math.sin(s)*Math.cos(i)+Math.cos(s)*Math.sin(i)*Math.cos(r)),n=Math.atan2(Math.sin(r)*Math.sin(i)*Math.cos(s),Math.cos(i)-Math.sin(s)*Math.sin(o)),new VLMPosition(NormalizeLongitudeDeg(Rad2Deg(-(e=(l-n+Math.PI)%(2*Math.PI)-Math.PI))),Rad2Deg(o))},this.GetVLMString=function(){return this.Lat.toString()+","+this.Lon.toString()}}function Boat(e){this.IdBoat=-1,this.Engaged=!1,this.BoatName="",this.BoatPseudo="",this.VLMInfo={},this.RaceInfo={},this.Exclusions=[],this.Track=[],this.RnkObject={},this.OppTrack=[],this.OppList=[],this.Reals=[],this.VLMPrefs=[],this.NextServerRequestDate=null,this.Estimator=new Estimator(this),this.EstimatePos=null,void 0!==e&&(this.IdBoat=e.idu,this.Engaged=e.engaged,this.BoatName=e.boatname,this.BoatPseudo=e.boatpseudo,this.VLMInfo=e.VLMInfo,this.RaceInfo=e.RaceInfo,this.Exclusions=e.Exclusions,this.Track=e.Track,this.RnkObject=e.RnkObject),this.GetNextGateSegment=function(e){if("string"==typeof e&&(e=parseInt(e,10)),void 0===this.RaceInfo)return null;var t=this.RaceInfo.races_waypoints[e];do{if("string"==typeof t&&(t=parseInt(t,10)),t.wpformat&WP_ICE_GATE){if(++e>=this.RaceInfo.races_waypoints)throw"Oops could not find requested gate type";t=this.RaceInfo.races_waypoints[e]}}while(t.wpformat&WP_ICE_GATE);var a=new VLMPosition(t.longitude1,t.latitude1);if((t.format&WP_GATE_BUOY_MASK)!==WP_TWO_BUOYS)throw"not implemented 1 buoy gate";return{P1:a,P2:new VLMPosition(t.longitude2,t.latitude2)}},this.GetClosestEstimatePoint=function(e){if(void 0===e||!e)return this.Estimator&&this.Estimator.ClearEstimatePosition(this.Estimator.Boat),null;if(this.Estimator){var t=this.Estimator.GetClosestEstimatePoint(e);return t?this.Estimator.ShowEstimatePosition(this.Estimator.Boat,t):this.Estimator.ClearEstimatePosition(this.Estimator.Boat),t}return this.Estimator.ShowEstimatePosition(null,null),null},this.GetNextWPPosition=function(e,t,a){if(void 0===this.VLMInfo)return null;this.VLMInfo.NWP;if(!(void 0!==a&&a||"0"===this.VLMInfo.WPLON&&"0"===this.VLMInfo.WPLAT))return new VLMPosition(this.VLMInfo.WPLON,this.VLMInfo.WPLAT);if(void 0!==a&&a&&0!==a.Lon.Value&&0!==a.Lat.Value)return new VLMPosition(a.Lon.Value,a.Lat.Value);var o=this.VLMInfo.NWP;void 0!==e&&e&&(o=e);var n=this.GetNextGateSegment(o);if(void 0===n||!n)return null;var i,r=n.P1.GetLoxoCourse(n.P2);i=void 0!==t&&t?t:new VLMPosition(this.VLMInfo.LON,this.VLMInfo.LAT);var s=r-n.P1.GetLoxoCourse(i);if(s>180?s-=360:s<-180&&(s+=360),(s=Math.abs(s))>90)return n.P1;var l=n.P1.GetLoxoDist(i);try{var u=l*Math.cos(Deg2Rad(s));return n.P1.GetLoxoDist(n.P2)>u?n.P1.ReachDistLoxo(u,r):n.P2}catch(e){return null}}}function User(){this.IdPlayer=-1,this.IsAdmin=!1,this.PlayerName="",this.PlayerJID="",this.Fleet=[],this.BSFleet=[],this.CurBoat={},this.LastLogin=0,this.KeepAlive=function(){console.log("Keeping login alive..."),CheckLogin()},setInterval(this.KeepAlive,6e5)}function IsLoggedIn(){return _IsLoggedIn}function OnLoginRequest(){CheckLogin(!0)}function GetPHPSessId(){var e,t=document.cookie.split(";");for(e in t)if(t[e]){var a=t[e].split("=");if(a[0]&&"PHPSESSID"===a[0].trim())return a[0]}return null}function CheckLogin(e){var t=$(".UserName").val(),a=$(".UserPassword").val();GetPHPSessId()||"string"==typeof t&&"string"==typeof a&&t.trim().length>0&&a.trim().length>0?(ShowPb("#PbLoginProgress"),$.post("/ws/login.php",{VLM_AUTH_USER:t.trim(),VLM_AUTH_PW:a.trim()},function(t){var a=JSON.parse(t),o=null;_IsLoggedIn&&(o=_CurPlayer.CurBoatID),_IsLoggedIn=!0===a.success,HandleCheckLoginResponse(e),o&&SetCurrentBoat(GetBoatFromIdu(select),!1)})):HandleCheckLoginResponse(e)}function HandleCheckLoginResponse(e){_IsLoggedIn?GetPlayerInfo():e&&(VLMAlertDanger(GetLocalizedString("authfailed")),$(".UserPassword").val(""),setTimeout(function(){$("#LoginForm").modal("hide").modal("show")},1e3),initrecaptcha(!0,!1),$("#ResetPasswordLink").removeClass("hidden")),HidePb("#PbLoginProgress"),DisplayLoggedInMenus(_IsLoggedIn)}function Logout(){DisplayLoggedInMenus(!1),$.post("/ws/logout.php",function(e){e.success?window.location.reload():(VLMAlertDanger("Something bad happened while logging out. Restart browser..."),windows.location.reload())}),_IsLoggedIn=!1}Math.fmod=function(e,t){return Number((e-Math.floor(e/t)*t).toPrecision(8))};var _CurPlayer=null;function GetPlayerInfo(){ShowBgLoad(),$.get("/ws/playerinfo/profile.php",function(e){e.success?(void 0!==_CurPlayer&&_CurPlayer||(_CurPlayer=new User),_CurPlayer.IdPlayer=e.profile.idp,_CurPlayer.IsAdmin=e.profile.admin,_CurPlayer.PlayerName=e.profile.playername,$.get("/ws/playerinfo/fleet_private.php",HandleFleetInfoLoaded),RefreshPlayerMenu()):Logout()})}function HandleFleetInfoLoaded(e){var t;for(var a in void 0===_CurPlayer&&(_CurPlayer=new User),void 0===_CurPlayer.Fleet&&(_CurPlayer.Fleet=[]),e.fleet)void 0===_CurPlayer.Fleet[a]&&(_CurPlayer.Fleet[a]=new Boat(e.fleet[a]),void 0===t&&(t=_CurPlayer.Fleet[a]));for(var o in void 0===_CurPlayer.fleet_boatsit&&(_CurPlayer.fleet_boatsit=[]),e.fleet_boatsit)void 0===_CurPlayer.BSFleet[o]&&(_CurPlayer.BSFleet[o]=new Boat(e.fleet_boatsit[o]));RefreshPlayerMenu(),void 0!==t&&t&&(DisplayCurrentDDSelectedBoat(t),SetCurrentBoat(GetBoatFromIdu(t),!0),RefreshCurrentBoat(!0,!1))}function RefreshPlayerMenu(){for(var e in $("#PlayerId").text(_CurPlayer.PlayerName),ClearBoatSelector(),_CurPlayer.Fleet)AddBoatToSelector(_CurPlayer.Fleet[e],!0);for(var t in _CurPlayer.BSFleet)_CurPlayer.BSFleet[t]&&AddBoatToSelector(_CurPlayer.BSFleet[t],!1);DisplayLoggedInMenus(!0),HideBgLoad("#PbLoginProgress")}function SetupUserMenu(){var e=$(document).width()/2-$(".UserMenu").width()/2+"px";$(".UserMenu").show(),$(".UserMenu").animate({left:e,top:0},0)}function GetBoatFromIdu(e){if(void 0!==_CurPlayer){var t=GetBoatFromBoatArray(_CurPlayer.Fleet,e);return void 0===t&&(t=GetBoatFromBoatArray(_CurPlayer.BSFleet,e)),t}}function GetBoatFromBoatArray(e,t){for(var a in t=parseInt(t,10),e)if(e[a]&&e[a].IdBoat===t)return e[a]}function GetFlagsList(){$.get("/ws/serverinfo/flags.php",function(e){if(e.success){var t=$("#CountryDropDownList"),a=0;for(var o in e.flags)if(e.flags[o]){var n=e.flags[o];t.append("<li class='FlagLine DDLine' flag='"+n+"'>"+GetCountryDropDownSelectorHTML(n,!0,a++)+"</li>")}}$(".FlagLine").on("click",HandleFlagLineClick)})}var FlagsIndexCache=[];function GetCountryDropDownSelectorHTML(e,t,a){if(t){var o=GetCountryFlagImg(e,a);FlagsIndexCache[e]=o}var n=" <span  class='FlagLabel' flag='"+e+"'> - "+e+"</span>";return FlagsIndexCache[e]+n}function GetCountryFlagImgHTML(e){return FlagsIndexCache[e]}function GetCountryFlagImg(e,t){return" <div class='FlagIcon' style='background-position: -"+t%16*30+"px -"+20*Math.floor(t/16)+"px' flag='"+e+"'></div>"}var VLM_COORDS_FACTOR=1e3,OppPopups=[],StartSetWPOnClick=!1;function SetCurrentBoat(e,t,a,o){_CurPlayer&&_CurPlayer.CurBoat&&e&&(_CurPlayer.CurBoat.IdBoat!==e.IdBoat&&ClearCurrentMapMarker(_CurPlayer.CurBoat),EnsureMarkersVisible(e)),CheckBoatRefreshRequired(e,t,a,o)}var BoatLoading=new Date(0);function CheckBoatRefreshRequired(e,t,a,o){if(void 0!==e&&e){var n=new Date,i=void 0!==e&&(void 0===e.VLMInfo||void 0===e.VLMInfo.AVG);UpdatePrefsDialog(e),void 0!==e.VLMInfo&&void 0!==e.VLMInfo.LUP||(a=!0),a||n>=e.NextServerRequestDate?(BoatLoading=n+3e3,console.log("Loading boat info from server...."),ShowPb("#PbGetBoatProgress"),$.get("/ws/boatinfo.php?forcefmt=json&select_idu="+e.IdBoat,function(a){if(e.IdBoat===parseInt(a.IDU,10)){_CurPlayer.CurBoat=e,LoadVLMPrefs(),e.VLMInfo=a,e.NextServerRequestDate=new Date(1e3*(parseInt(e.VLMInfo.LUP,10)+parseInt(e.VLMInfo.VAC,10))),e.LastRefresh=new Date,e.VLMInfo.LON/=VLM_COORDS_FACTOR,e.VLMInfo.LAT/=VLM_COORDS_FACTOR,console.log("DBG WIND ");var n=GribMgr.WindAtPointInTime(new Date(1566149443e3),49.753227868452,-8.9971082951315);if(n){var r=n.Heading+40,s=PolarsManager.GetBoatSpeed("boat_figaro2",n.Speed,n.Heading,r);if(!isNaN(s))new VLMPosition(49.753227868452,-8.9971082951315).ReachDistLoxo(s/3600*300,r)}i&&UpdatePrefsDialog(e),"0"!==e.VLMInfo.RAC?(void 0!==e.RaceInfo&&void 0!==e.RaceInfo.idraces||(GetRaceInfoFromServer(e,o),GetRaceExclusionsFromServer(e)),GetTrackFromServer(e),e.VLMInfo&&e.VLMInfo.RAC&&LoadRankings(e.VLMInfo.RAC),LoadRealsList(e),DrawBoat(e,t),UpdateInMenuRacingBoatInfo(e,o)):UpdateInMenuDockingBoatInfo(e)}HidePb("#PbGetBoatProgress"),OnPlayerLoadedCallBack&&(OnPlayerLoadedCallBack(),OnPlayerLoadedCallBack=null)})):e&&(_CurPlayer.CurBoat=e,UpdateInMenuDockingBoatInfo(e),UpdateInMenuRacingBoatInfo(e,o),DrawBoat(e,t))}}function GetTrackFromServer(e){var t=Math.floor(new Date/1e3),a=t-172800;$.get("/ws/boatinfo/tracks_private.php?idu="+e.IdBoat+"&idr="+e.VLMInfo.RAC+"&starttime="+a+"&endtime="+t,function(t){if(t.success){for(var a in void 0!==e.Track?e.Track.length=0:e.Track=[],t.tracks)if(t.tracks[a]){var o=new VLMPosition(t.tracks[a][1]/1e3,t.tracks[a][2]/1e3);e.Track.push(o)}DrawBoat(e)}})}function GetRaceExclusionsFromServer(e){$.get("/ws/raceinfo/exclusions.php?idrace="+e.VLMInfo.RAC+"&v="+e.VLMInfo.VER,function(t){if(t.success){var a,o,n=[],i=[];for(o in t.Exclusions)if(t.Exclusions[o]){var r=t.Exclusions[o];(void 0===a||a[0]!==r[0][0]&&a[1]!==r[0][1])&&(void 0!==a&&(n.push(i),i=[]),i.push(r[0])),a=r[1],i.push(r[1])}n.push(i),e.Exclusions=n,DrawRaceExclusionZones(e,n)}})}function GetRaceInfoFromServer(e,t){$.get("/ws/raceinfo/desc.php?idrace="+e.VLMInfo.RAC+"&v="+e.VLMInfo.VER,function(a){e.RaceInfo=a,DrawRaceGates(e),UpdateInMenuRacingBoatInfo(e,t)})}var DrawBoatTimeOutHandle=null,DeferredCenterValue=!1;function DrawBoat(e,t){void 0!==t&&(DeferredCenterValue=DeferredCenterValue||t),console.log("Call DrawbBoat ("+t+") deferred : "+DeferredCenterValue),DrawBoatTimeOutHandle&&(console.log("Pushed DrawBoat"),clearTimeout(DrawBoatTimeOutHandle)),DrawBoatTimeOutHandle=setTimeout(ActualDrawBoat,100,e,DeferredCenterValue)}function GetRaceMapFeatures(e){if(!e)throw"Should not GetRaceFeature unless a boat is defined";return void 0===e.RaceMapFeatures&&(e.RaceMapFeatures={}),e.RaceMapFeatures}function ActualDrawBoat(e,t){map.zoom;if(DeferredCenterValue=!1,DrawBoatTimeOutHandle=null,void 0===e||!e){if(void 0===_CurPlayer||!_CurPlayer||void 0===_CurPlayer.CurBoat||!_CurPlayer.CurBoat)return;e=_CurPlayer.CurBoat}if(void 0!==e&&e){var a=GetRaceMapFeatures(e),o=a.TrackWP,n=null;if(void 0!==e&&e&&(n=e.GetNextWPPosition()),void 0!==n&&n&&!isNaN(n.Lat.Value)&&!isNaN(n.Lon.Value))if(o)o.setLatLng([n.Lat.Value,n.Lon.Value]);else{var i=GetTrackWPMarker();a.TrackWP=L.marker([n.Lat.Value,n.Lon.Value],{icon:i,draggable:!0}).addTo(map).on("dragend",HandleWPDragEnded)}if(void 0!==_typeof(e.VLMInfo)&&e.VLMInfo&&(e.VLMInfo.LON||e.VLMInfo.LAT)){var r=a.BoatMarker;r?(r.setLatLng([e.VLMInfo.LAT,e.VLMInfo.LON]),r.setRotationAngle(e.VLMInfo.HDG)):(r=GetBoatMarker(e.VLMInfo.idusers),a.BoatMarker=L.marker([e.VLMInfo.LAT,e.VLMInfo.LON],{icon:r,rotationAngle:e.VLMInfo.HDG}).addTo(map).on("click",HandleOpponentClick)),void 0!==map&&map&&DrawBoatPolar(e,t,a)}void 0!==e.Track&&e.Track.length>0&&DrawBoatTrack(e,a),DrawBoatEstimateTrack(e,a),DrawOpponents(e),t&&void 0!==e.VLMInfo&&e.VLMInfo&&void 0!==map&&map&&map.setView([e.VLMInfo.LAT,e.VLMInfo.LON]),RepositionCompass(e),console.log("ActualDrawBoatComplete")}}function DrawBoatEstimateTrack(e,t){if(void 0!==e.Estimator&&e.Estimator){var a=e.Estimator.GetEstimateTracks(),o=["green","orange","red"];for(var n in a)if(t.EstimateTracks&&t.EstimateTracks[n])void 0!==a[n]?t.EstimateTracks[n].setLatLngs(a[n]):(t.EstimateTracks[n].remove(),t.EstimateTracks[n]=null);else if(void 0===t.EstimateTracks&&(t.EstimateTracks=[]),a[n]){var i={weight:2,opacity:1,color:o[n]};t.EstimateTracks[n]=L.polyline(a[n],i).addTo(map)}}}function RepositionCompass(e){if(e){var t=GetRaceMapFeatures(e);map.Compass&&(t.Compass&&-1==t.Compass.Lat&&-1==t.Compass.Lon||e.VLMInfo&&(e.VLMInfo.LAT||e.VLMInfo.LON)?map.Compass.setLatLng([e.VLMInfo.LAT,e.VLMInfo.LON]):!t.Compass||isNaN(t.Compass.Lat)||isNaN(t.Compass.Lon)||map.Compass.setLatLng([t.Compass.Lat,t.Compass.Lon]))}}function DrawBoatTrack(e,t){for(var a=[],o=e.Track.length,n=0;n<o;n++){var i=e.Track[n];a.push([i.Lat.Value,i.Lon.Value])}var r=e.VLMInfo.COL;r=SafeHTMLColor(r);var s=t.BoatTrack;s?s.setLatLngs(a):t.BoatTrack=L.polyline(a,{type:"HistoryTrack",color:r,weight:1.2}).addTo(map)}function DrawBoatPolar(e,t,a){var o,n=new VLMPosition(e.VLMInfo.LON,e.VLMInfo.LAT);o=BuildPolarLine(e,n,VLM2Prefs.MapPrefs.PolarVacCount,new Date(1e3*e.VLMInfo.LUP),function(){DrawBoatPolar(e,t,a)}),a.Polar=DefinePolarMarker(o,a.Polar)}function DefinePolarMarker(e,t){if(e)if(t)t.setLatLngs(e).addTo(map);else{(t=L.polyline(e,{color:"white",opacity:.6,weight:1})).addTo(map)}else t&&t.remove(),t=null;return t}function BuildPolarLine(e,t,a,o,n){var i=o,r=null;e&&e.VLMInfo&&e.VLMInfo.VAC&&(i-=1e3*e.VLMInfo.VAC),(!i||i<(new Date).getTime())&&(i=(new Date).getTime());var s=null;if(t&&t.Lat&&t.Lon&&(s=GribMgr.WindAtPointInTime(i,t.Lat.Value,t.Lon.Value,n)),s){parseFloat(e.VLMInfo.HDG);var l,u=[];for(l=0;l<=180;l+=5){var d=PolarsManager.GetBoatSpeed(e.VLMInfo.POL,s.Speed,s.Heading,s.Heading+l);if(isNaN(d))return;for(var c=-1;c<=1;c+=2){var p=t.ReachDistLoxo(d/3600*e.VLMInfo.VAC*a,s.Heading+l*c),h=[p.Lat.Value,p.Lon.Value];u[c*l+180]=h}}for(var P in r=[],u)u[P]&&r.push(u[P])}return r}function GetVLMPositionFromClick(e){if(map){var t=map.getLonLatFromPixel(e).transform(new OpenLayers.Projection("EPSG:900913"),new OpenLayers.Projection("EPSG:4326"));return new VLMPosition(t.lon,t.lat)}return null}function CompleteWPSetPosition(e){var t=null;if(e.getLatLng)t=e.getLatLng();else{if(!e.latlng)return void VLMAlertDanger("Unexpected Object when setting WP report to devs.");t=e.latlng}var a=new VLMPosition(t.lng,t.lat);SendVLMBoatWPPos(_CurPlayer.CurBoat,a)}var WP_TWO_BUOYS=0,WP_ONE_BUOY=1,WP_GATE_BUOY_MASK=15,WP_DEFAULT=0,WP_ICE_GATE_N=16,WP_ICE_GATE_S=32,WP_ICE_GATE_E=64,WP_ICE_GATE_W=128,WP_ICE_GATE=WP_ICE_GATE_E|WP_ICE_GATE_N|WP_ICE_GATE_S|WP_ICE_GATE_W,WP_GATE_KIND_MASK=65520,WP_CROSS_CLOCKWISE=256,WP_CROSS_ANTI_CLOCKWISE=512,WP_CROSS_ONCE=1024,Exclusions=[];function DrawRaceGates(e){if(void 0!==e&&e&&e.RaceInfo){var t=e.RaceInfo,a=e.VLMInfo.NWP,o=GetRaceMapFeatures(e);if(void 0!==_typeof(t)&&t&&void 0!==t.races_waypoints&&t.races_waypoints)for(var n in t.races_waypoints){o.Gates||(o.Gates=[]),o.Gates[n]||(o.Gates[n]={});var i=o.Gates[n];if(t.races_waypoints[n]){var r=i.Buoy1,s=t.races_waypoints[n];NormalizeRaceInfo(t);var l=!(s.wpformat&WP_CROSS_ANTI_CLOCKWISE),u=new VLMPosition(s.longitude1,s.latitude1);if(i.Buoy1=AddBuoyMarker(r,"WP"+n+" "+s.libelle+"<BR>"+u.toString(),s.longitude1,s.latitude1,l),(s.wpformat&WP_GATE_BUOY_MASK)===WP_TWO_BUOYS){var d=i.Buoy2,c=new VLMPosition(s.longitude2,s.latitude2);i.Buoy2=AddBuoyMarker(d,"WP"+n+" "+s.libelle+"<BR>"+c.toString(),s.longitude2,s.latitude2,!l)}else{for(var p=new VLMPosition(s.longitude1,s.latitude1),h=!1,P=2500,f=null;!h;)try{f=p.ReachDistLoxo(P,180+parseFloat(s.laisser_au)),h=!0}catch(e){P*=.7}s.longitude2=f.Lon.Value,s.latitude2=f.Lat.Value}n=parseInt(n,10),a=parseInt(a,10),AddGateSegment(i,s.longitude1,s.latitude1,s.longitude2,s.latitude2,a===n,n<a,s.wpformat&WP_GATE_KIND_MASK)}}}}function DrawRaceExclusionZones(e,t){if(e){var a=GetRaceMapFeatures(e);for(var o in t)t[o]&&DrawRaceExclusionZone(a,t,o)}}function DrawRaceExclusionZone(e,t,a){var o=[],n=!1;for(var i in t[a])if(t[a][i]){var r=[t[a][i][0],t[a][i][1]];o.push(r),n=!0}n?(void 0===e.Exclusions&&(e.Exclusions=[]),e.Exclusions[a]?e.Exclusions[a].setLatLngs(o).addTo(map):e.Exclusions[a]=L.polygon(o,{color:"red",opacity:.25,weight:3}).addTo(map)):e.Exclusions&&e.Exclusions[index]&&(e.Exclusions[a].remove(),e.Exclusions[a]=null)}function GetLonOffset(e,t){return e*t>=0?0:Math.abs(t-e)>90?e>0?360:-360:0}function AddGateSegment(e,t,a,o,n,i,r,s){var l=[[a,t],[n,o]],u="";if(u=i?"green":r?"blue":"red",s&WP_CROSS_ONCE&&(e.Segment2?e.Segment2.setLatLngs(l):e.Segment2=L.polyline(l,{color:"black",dashArray:"20,10,5,10",weight:2,opacity:.75}).addTo(map)),e.Segment?(e.Segment.setLatLngs(l),e.Segment.color=u):e.Segment=L.polyline(l,{color:u,weight:1,opacity:.75}).addTo(map),s!==WP_DEFAULT){var d=new VLMPosition(t,a),c=new VLMPosition(o,n),p=d.GetLoxoCourse(c),h=d.ReachDistLoxo(c,.5);s&WP_CROSS_ANTI_CLOCKWISE?(p-=90,AddGateDirMarker(e,h.Lon.Value,h.Lat.Value,p)):s&WP_CROSS_CLOCKWISE?(p+=90,AddGateDirMarker(e,h.Lon.Value,h.Lat.Value,p)):s&WP_ICE_GATE&&AddGateIceGateMarker(e,h.Lon.Value,h.Lat.Value)}}var MAX_BUOY_INDEX=16,BuoyIndex=Math.floor(Math.random()*MAX_BUOY_INDEX);function AddGateDirMarker(e,t,a,o){AddGateCenterMarker(e,t,a,"BuoyDirs/BuoyDir"+BuoyIndex+".png",o,!1),BuoyIndex++,BuoyIndex%=MAX_BUOY_INDEX+1}function AddGateIceGateMarker(e,t,a){AddGateCenterMarker(e,t,a,"icegate.png",!0)}function AddGateCenterMarker(e,t,a,o,n,i){var r=[a,t];if(e.GateMarker)e.GateMarker.setLatLng(r);else{var s=GetGateTypeMarker(o,i);e.GateMarker=L.marker(r,{icon:s}).addTo(map),i||e.GateMarker.setRotationAngle(n)}}function AddBuoyMarker(e,t,a,o,n){var i=GetBuoyMarker(n);if(e){if(e.IsCWBuoy===n)return e.setLatLng([o,a]);e.remove()}return L.marker([o,a],{icon:i}).addTo(map).bindPopup(t)}var PM_HEADING=1,PM_ANGLE=2,PM_ORTHO=3,PM_VMG=4,PM_VBVMG=5;function SendVLMBoatWPPos(e,t){var a={idu:e.IdBoat,pip:{targetlat:t.Lat.Value,targetlong:t.Lon.Value,targetandhdg:-1}};PostBoatSetupOrder(e.IdBoat,"target_set",a)}function SendVLMBoatOrder(e,t,a,o){var n={};if(void 0!==_CurPlayer&&void 0!==_CurPlayer.CurBoat){switch(e){case PM_HEADING:case PM_ANGLE:n={idu:_CurPlayer.CurBoat.IdBoat,pim:e,pip:t};break;case PM_ORTHO:case PM_VBVMG:case PM_VMG:n={idu:_CurPlayer.CurBoat.IdBoat,pim:e,pip:{targetlong:parseFloat(t),targetlat:parseFloat(a),targetandhdg:o}};break;default:return}PostBoatSetupOrder(_CurPlayer.CurBoat.IdBoat,"pilot_set",n)}else VLMAlertDanger("Must select a boat to send an order")}function PostBoatSetupOrder(e,t,a){$.post("/ws/boatsetup/"+t+".php?selectidu"+e,"parms="+JSON.stringify(a),function(e,t){e.success?RefreshCurrentBoat(!1,!0):VLMAlertDanger(GetLocalizedString("BoatSetupError")+"\n"+e.error.code+" "+e.error.msg)})}function EngageBoatInRace(e,t){$.post("/ws/boatsetup/race_subscribe.php","parms="+JSON.stringify({idu:t,idr:parseInt(e,10)}),function(e){if(e.success){var t=GetLocalizedString("youengaged");$("#RacesListForm").modal("hide"),VLMAlertSuccess(t)}else{VLMAlertDanger(e.error.msg+"\n"+e.error.custom_error_string)}})}function DiconstinueRace(e,t){$.post("/ws/boatsetup/race_unsubscribe.php","parms="+JSON.stringify({idu:e,idr:parseInt(t,10)}),function(e){e.success?VLMAlertSuccess("Bye Bye!"):VLMAlertDanger(e.error.msg+"\n"+e.error.custom_error_string)})}function LoadRealsList(e){void 0!==e&&e&&void 0!==e.VLMInfo&&$.get("/ws/realinfo/realranking.php?idr="+e.VLMInfo.RAC,function(t){t.success?(e.Reals=t,DrawBoat(e,!1)):e.Reals=[]})}function LoadRankings(e,t){e&&"object"===_typeof(e)&&VLMAlertDanger("Not updated call to LoadRankings"),$.get("/cache/rankings/rnk_"+e+".json",function(a){a?(Rankings[e]=a.Boats,t?t():DrawBoat(null,!1)):Rankings[e]=null})}function contains(e,t){for(var a=0;a<e.length;a++)if(e[a]===t)return!0;return!1}function DrawOpponents(e){if(e&&void 0!==Rankings){var t,a=[];if(VLM2Prefs.MapPrefs.MapOppShow===VLM2Prefs.MapPrefs.MapOppShowOptions.ShowSel&&(void 0!==e.VLMInfo&&void 0!==e.VLMInfo.MPO&&(a=e.VLMInfo.MPO.split(",")),0!==a.length)){var o=e.VLMInfo.RAC;for(t in a)if(a[t]&&Rankings[o]){var n=Rankings[o][a[t]];void 0!==n&&parseInt(n.idusers,10)!==e.IdBoat&&AddOpponent(e,VLMBoatsLayer,BoatFeatures,n,!0)}}if(VLM2Prefs.MapPrefs.ShowReals&&void 0!==e.Reals&&void 0!==e.Reals.ranking)for(t in e.Reals.ranking){var i=e.Reals.ranking[t];AddOpponent(e,VLMBoatsLayer,BoatFeatures,i,!0)}var r=150,s=r/Object.keys(Rankings).length,l=0,u=Rankings;switch(void 0!==e.OppList&&e.OppList.length>0&&(u=e.OppList,s=1),VLM2Prefs.MapPrefs.MapOppShow){case VLM2Prefs.MapPrefs.MapOppShowOptions.Show10Around:u=GetClosestOpps(e,10),s=1;break;case VLM2Prefs.MapPrefs.MapOppShowOptions.Show5Around:u=GetClosestOpps(e,5),s=1;break;case VLM2Prefs.MapPrefs.MapOppShowOptions.ShowTop10:var d=0,c=e.Engaged;for(t in r=VLM2Prefs.MapPrefs.ShowTopCount,u=[],Rankings[c])if(Rankings[c][t].rank<=VLM2Prefs.MapPrefs.ShowTopCount&&(u[t]=Rankings[c][t],++d>r))break;d>r&&(r=d),s=1;break;case VLM2Prefs.MapPrefs.MapOppShowOptions.ShowMineOnly:u=[],s=1}if(SortRankingData(e,"RAC",null,e.Engaged),e.Engaged&&void 0!==Rankings[e.Engaged]&&void 0!==Rankings[e.Engaged].RacerRanking&&Rankings[e.Engaged].RacerRanking){var p=GetRaceMapFeatures(e);for(t in Rankings[e.Engaged].RacerRanking)if(t in Rankings[e.Engaged].RacerRanking){var h=Rankings[e.Engaged].RacerRanking[t];if(parseInt(h.idusers,10)!==e.IdBoat&&u[h.idusers]&&!contains(a,h.idusers)&&RnkIsRacing(h)&&Math.random()<=s&&l<r)AddOpponent(e,p,h,!1),l+=1,void 0===e.OppList&&(e.OppList=[]),e.OppList[t]=h;else if(l>=r)break}}if(void 0!==e.RaceMapFeatures&&Object.keys(e.OppTrack).length>0){var P=e.RaceMapFeatures;for(var f in e.OppTrack){var g=e.OppTrack[f];if(g&&g.Visible&&g.DatePos.length>1){if(!g.OppTrackPoints){for(var M=[],m=Object.keys(g.DatePos).length,C=0;C<m;C++){var v=Object.keys(g.DatePos)[C],I=g.DatePos[v],_=[I.lat,I.lon];M.push(_)}g.OppTrackPoints=M}if(void 0===P.OppTrack&&(P.OppTrack=[]),P.OppTrack[f])P.OppTrack[f].setLatLngs(g.OppTrackPoints).addTo(map);else{var R="black";void 0!==g.TrackColor&&(R=g.TrackColor);var S={color:R,weight:1,opacity:.75};P.OppTrack[f]=L.polyline(g.OppTrackPoints,S).addTo(map)}g.LastShow=new Date}else e.RaceMapFeatures.OppTrack&&e.RaceMapFeatures.OppTrack[f]&&e.RaceMapFeatures.OppTrack[f].remove()}}}}function CompareDist(e,t){return e.dnm<t.dnm?-1:e.dnm>t.dnm?1:0}function GetClosestOpps(e,t){var a=null;e&&e.VLMInfo&&(a=e.VLMInfo.RAC);var o=[];if(a&&Rankings[a]){var n=Rankings[a][e.IdBoat];void 0!==n&&e||(n={dnm:0,nwp:1});var i=parseFloat(n.dnm),r=n.nwp,s=[];for(var l in Rankings[a])if(Rankings[a][l]&&r===Rankings[a][l].nwp){var u={id:l,dnm:Math.abs(i-parseFloat(Rankings[a][l].dnm))};s.push(u)}for(var d in(s=s.sort(CompareDist)).slice(0,t-1))o[s[d].id]=Rankings[a][s[d].id]}return o}function AddOpponent(e,t,a,o){var n=[a.latitude,a.longitude],i=map.getZoom(),r={name:a.idusers,Coords:new VLMPosition(a.longitude,a.latitude).toString(),type:"opponent",idboat:a.idusers,rank:a.rank,Last1h:a.last1h,Last3h:a.last3h,Last24h:a.last24h,IsTeam:a.country==e.VLMInfo.CNT?"team":"",IsFriend:o?2*i:i,color:a.color};if(VLM2Prefs.MapPrefs.ShowOppNumbers||(r.name=""),void 0===t.Opponents&&(t.Opponents=[]),t.Opponents[a.idusers])t.Opponents[a.idusers].setLatLng(n);else{var s=GetOpponentMarker(r);t.Opponents[a.idusers]=L.marker(n,{icon:s}).addTo(map),t.Opponents[a.idusers].on("click",HandleOpponentClick),t.Opponents[a.idusers].on("mouseover",HandleOpponentOver),t.Opponents[a.idusers].IdUsers=a.idusers}}function ShowOpponentPopupInfo(e){var t=e.sourceTarget;if(t&&t.options&&t.options.icon&&void 0!==t.options.icon.MarkerOppId){var a=GetOppBoat(t.options.icon.MarkerOppId);if(a){var o=new VLMPosition(a.longitude,a.latitude),n=GetRaceMapFeatures(_CurPlayer.CurBoat);if(n){var i=BuildBoatPopupInfo(a);n.OppPopup?n.OppPopup.setContent(i):n.OppPopup=L.popup(i),n.OppPopup.PrevOpp&&n.OppPopup.PrevOpp.unbindPopup(n.OppPopup),t.bindPopup(n.OppPopup).openPopup(),n.OppPopup.PrevOpp=t;var r=[],s=t.options.icon.MarkerOppId;t.openPopup(),r.push([FIELD_MAPPING_TEXT,"#__BoatName"+s,a.boatname]),r.push([FIELD_MAPPING_TEXT,"#__BoatId"+s,a.idusers]),r.push([FIELD_MAPPING_TEXT,"#__BoatRank"+s,a.rank]),r.push([FIELD_MAPPING_TEXT,"#__BoatLoch"+s,RoundPow(parseFloat(a.loch),2)]),r.push([FIELD_MAPPING_TEXT,"#__BoatNWP"+s,"["+a.nwp+"] "+RoundPow(parseFloat(a.dnm),2)]),r.push([FIELD_MAPPING_TEXT,"#__BoatPosition"+s,o.GetVLMString()]),r.push([FIELD_MAPPING_TEXT,"#__Boat1HAvg"+s,RoundPow(parseFloat(a.last1h),2)]),r.push([FIELD_MAPPING_TEXT,"#__Boat3HAvg"+s,RoundPow(parseFloat(a.last3h),2)]),r.push([FIELD_MAPPING_TEXT,"#__Boat24HAvg"+s,RoundPow(parseFloat(a.last24h),2)]),r.push([FIELD_MAPPING_STYLE,"#__BoatColor"+s,"background-color",SafeHTMLColor(a.color)]),FillFieldsFromMappingTable(r)}}}}function GetOppBoat(e){var t=_CurPlayer.CurBoat;if(void 0!==t&&t&&t.OppList){for(var a in t.OppList)if(t.OppList[a]){var o=t.OppList[a];if(o.idusers===e)return o}if(t.Reals&&t.Reals.ranking)for(var n in t.Reals.ranking)if(t.Reals.ranking[n]){var i=t.Reals.ranking[n];if(i.idusers===e)return i}}return null}function BuildBoatPopupInfo(e){if(!e||!e.idusers)return null;var t=e.idusers;return'<div class="MapPopup_InfoHeader">'+GetCountryFlagImgHTML(e.country)+' <span id="__BoatName'+t+'" class="PopupBoatNameNumber ">BoatName</span> <span id="__BoatId'+t+'" class="PopupBoatNameNumber ">BoatNumber</span> <div id="__BoatRank'+t+'" class="TxtRank">Rank</div></div><div id="__BoatColor'+t+'" style="height: 2px;"></div><div class="MapPopup_InfoBody"> <fieldset>   <span class="PopupHeadText " I18n="loch">'+GetLocalizedString("loch")+'</span><span class="PopupText"> : </span><span id="__BoatLoch'+t+'" class="loch PopupText">0.9563544</span>   <BR><span class="PopupHeadText " I18n="position">'+GetLocalizedString("position")+'</span><span class="PopupText"> : </span><span id="__BoatPosition'+t+'" class=" PopupText">0.9563544</span>   <BR><span class="PopupHeadText " I18n="NextWP">'+GetLocalizedString("NextWP")+'</span><span class="strong"> : </span><span id="__BoatNWP'+t+'" class="PopupText">[1] 4.531856536865234</span>   <BR><span class="PopupHeadText " I18n="Moyennes">'+GetLocalizedString("Moyennes")+' </span><span class="PopupText"> : </span>   <span class="PopupHeadText ">[1h]</span><span id="__Boat1HAvg'+t+'" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span>   <span class="PopupHeadText ">[3h]</span><span id="__Boat3HAvg'+t+'" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span>   <span class="PopupHeadText ">[24h]</span><span id="__Boat24HAvg'+t+'" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span> </fieldset></div>'}function HandleOpponentOver(e){var t,a=e.sourceTarget,o=GetRaceMapFeatures(_CurPlayer.CurBoat),n=a.IdUsers;if(n){for(t in o.OppTrack){var i=t===n;_CurPlayer.CurBoat.OppTrack[t].Visible=i}DrawOpponentTrack(n,o.Opponents[n])}}function HandleOpponentClick(e){HandleOpponentOver(e),ShowOpponentPopupInfo(e)}function HandleFeatureOut(e){if(void 0!==_CurPlayer&&_CurPlayer&&void 0!==_CurPlayer.CurBoat&&_CurPlayer.CurBoat&&void 0!==_CurPlayer.CurBoat.OppTrack)for(var t in _CurPlayer.CurBoat.OppTrack)_CurPlayer.CurBoat.OppTrack[t].Visible=!1}var TrackPendingRequests=[],LastTrackRequest=0;function DrawOpponentTrack(e,t){var a=_CurPlayer.CurBoat,o=new Date,n=null;if(void 0!==a&&a&&o>LastTrackRequest){if(LastTrackRequest=new Date(o/1e3+.5),void 0!==a.OppTrack||!(e in a.OppTrack)||e in a.OppTrack&&a.OppTrack[e].LastShow<=new Date(1e3*a.VLMInfo.LUP)){var i=new Date/1e3-172800,r=a.VLMInfo.RAC,s=new Date;n=e.toString()+"/"+r.toString(),e in a.OppTrack&&(a.OppTrack[e].Visible=!0),n in TrackPendingRequests&&!(s>TrackPendingRequests[n])||(TrackPendingRequests[n]=new Date(s.getTime()+6e4),console.log("GetTrack "+n+" "+i),parseInt(e)>0?GetBoatTrack(a,e,r,i,t):parseInt(e)&&GetRealBoatTrack(a,e,r,i,t))}else console.log(" GetTrack ignore before next update"+n+" "+StartTime);DrawBoat(a)}}function GetRealBoatTrack(e,t,a,o,n){$.get("/ws/realinfo/tracks.php?idr="+a+"&idreals="+-t+"&starttime="+o,function(a){a.success&&(AddBoatOppTrackPoints(e,t,a.tracks,n.color),RefreshCurrentBoat(!1,!1))})}var TrackRequestPending=!1;function GetBoatTrack(e,t,a,o,n){TrackRequestPending||(TrackRequestPending=!0,$.get("/ws/boatinfo/smarttracks.php?idu="+t+"&idr="+a+"&starttime="+o,function(a){if(TrackRequestPending=!1,a.success){var o;for(o in AddBoatOppTrackPoints(e,t,a.tracks,n.Color),a.tracks_url){if(o>10)break;$.get("/cache/tracks/"+a.tracks_url[o],function(a){a.success&&(AddBoatOppTrackPoints(e,t,a.tracks,n.Color),DrawOpponents(e))})}DrawOpponents(e)}}))}function AddBoatOppTrackPoints(e,t,a,o){for(var n in t in e.OppTrack||(o=SafeHTMLColor(o),e.OppTrack[t]={LastShow:0,TrackColor:o,DatePos:[],Visible:!0,OppTrackPoints:null}),a){var i=a[n];e.OppTrack[t].DatePos[i[0]]={lat:i[2]/1e3,lon:i[1]/1e3}}e.OppTrack[t].LastShow=0,e.OppTrack[t].OppTrackPoints=[],e.OppTrack[t].OppTrackPoints=null}function DeletePilotOrder(e,t){$.post("/ws/boatsetup/pilototo_delete.php?","parms="+JSON.stringify({idu:e.IdBoat,taskid:parseInt(t)}),function(e){e.success&&RefreshCurrentBoat(!1,!0,"AutoPilot")})}function UpdateBoatPrefs(e,t){t.idu=e.IdBoat,$.post("/ws/boatsetup/prefs_set.php","parms="+JSON.stringify(t),function(e){e.success?RefreshCurrentBoat(!1,!1):VLMAlertDanger(GetLocalizedString("UpdateFailed"))})}function LoadVLMPrefs(){var e;void 0!==_CurPlayer&&(e=_CurPlayer.CurBoat,SetDDTheme(VLM2Prefs.CurTheme),$.get("/ws/boatinfo/prefs.php?idu="+e.IdBoat,HandlePrefsLoaded))}function HandlePrefsLoaded(e){e.success?(_CurPlayer.CurBoat.VLMPrefs=e.prefs,VLM2Prefs.UpdateVLMPrefs(e.prefs)):VLMAlertDanger("Error communicating with VLM, try reloading the browser page...")}function HandleWPDragEnded(e){var t=_CurPlayer.CurBoat.RaceMapFeatures.TrackWP;CompleteWPSetPosition(t),VLMAlertInfo("User WP moved to "+t.getLatLng())}function InitXmpp(){converse.initialize({bosh_service_url:"https://bind.conversejs.org",i18n:locales.en,show_controlbox_by_default:!0,roster_groups:!0})}
>>>>>>> V22_Hotfix
