"use strict";

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var MAP_OP_SHOW_SEL = 0;

var PrefMgr = function PrefMgr() {
  _classCallCheck(this, PrefMgr);

  this.CurTheme = "bleu-noir";
  this.MapPrefs = new MapPrefs();
  this.GConsentDate = null;
  this.GConsentLastNo = null; //TODO need a GUI for this pref

  this.InputDigits = 3;
  this.RacesStorageLUT = [];
  this.VLMRacesStorage = [];

  this.ClearRaceData = function (RaceId) {
    if (this.RacesStorageLUT[RaceId]) {
      VLM2Prefs.VLMRacesStorage.splice(this.RacesStorageLUT[RaceId], 1);
      this.RacesStorageLUT.splice(RaceId, 1);
      this.Save();
    }

    return;
  };

  this.HasRaceStorage = function (RaceId) {
    return typeof this.RacesStorageLUT !== "undefined" && typeof this.RacesStorageLUT[RaceId] !== "undefined";
  };

  this.Init = function () {
    this.MapPrefs.Load();
    this.Load();
  };

  this.Load = function () {
    if (store.enabled) {
      this.GConsentDate = LoadLocalPref("GConsentDate", null);
      this.GConsentLastNo = LoadLocalPref("GConsentLastNo", null);

      try {
        // Work around json errors or tampering
        this.VLMRacesStorage = JSON.parse(LoadLocalPref("VLMRacesStorage", null));
      } catch (e) {
        this.VLMRacesStorage = null;
      }

      this.InputDigits = JSON.parse(LoadLocalPref("InputDigits", 3));
      this.CurTheme = LoadLocalPref('CurTheme', "bleu-noir");

      if (!this.VLMRacesStorage) {
        this.VLMRacesStorage = [];
      }

      for (var _index in this.VLMRacesStorage) {
        if (this.VLMRacesStorage[_index]) {
          this.RacesStorageLUT[this.VLMRacesStorage[_index].RaceId] = _index;
        }
      }
    }
  };

  this.Save = function () {
    if (store.enabled) {
      store.set('ColorTheme', this.CurTheme);
    }

    this.MapPrefs.Save();
    store.set("GConsentDate", this.GConsentDate);
    store.set("GConsentLastNo", this.GConsentLastNo);
    store.set("VLMRacesStorage", JSON.stringify(this.VLMRacesStorage));
    store.set("InputDigits", this.InputDigits);
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
        VLMAlertDanger("unexpected mapping option : " + p.mapOpponents);
    }
  };

  this.GetRaceFromStorage = function (RaceId) {
    if (this.RacesStorageLUT[RaceId]) {
      return this.VLMRacesStorage[this.RacesStorageLUT[RaceId]];
    } else {
      var RaceStorage = new Race(RaceId);
      var _index2 = this.VLMRacesStorage.length;
      this.VLMRacesStorage[_index2] = RaceStorage;
      this.RacesStorageLUT[RaceId] = _index2;
      this.Save();
      return RaceStorage;
    }
  };
};

var MapPrefs = function MapPrefs() {
  _classCallCheck(this, MapPrefs);

  this.ShowReals = true; // Do we show reals?

  this.ShowCompass = true; // Do we show reals?

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
      this.ShowCompass = LoadLocalPref('#ShowCompass', true);
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
      store.set("#ShowCompass", this.ShowCompass);
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

    if (typeof _CurPlayer !== "undefined" && _CurPlayer && typeof NewVals !== "undefined" && NewVals) {
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
};

var VLM2Prefs = new PrefMgr();
VLM2Prefs.Init();

function LoadLocalPref(PrefName, PrefDfaultValue) {
  var ret = store.get(PrefName);

  if (typeof ret === "undefined") {
    ret = PrefDfaultValue;
  }

  return ret;
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
      if (this.EstimateTrack) {
        StatMGR.Stat("Estimator_Stop", null, null, this.EstimateTrack.length);
      } else {
        StatMGR.Stat("Estimator_Stop", null, null, 0);
      }

      this.Running = false;
      this.ReportProgress(true);
      this.LastPctRefresh = -1;
      this.LastPctDraw = -1; //Estimate complete, DrawBoat track
      //DrawBoat(this.Boat);

      this.ReportProgress(true);
    }

    return;
  };

  this.Start = function (ProgressCallBack) {
    this.ProgressCallBack = ProgressCallBack;

    if (this.Running) {
      return;
    }

    this.Running = true;
    this.LastPctRefresh = 0;
    this.LastPctDraw = 0;
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

    for (var _index3 in this.Boat.VLMInfo.PIL) {
      var Order = this.Boat.VLMInfo.PIL[_index3];
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
    StatMGR.Stat("Estimator_Start");
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

    for (var _index4 in this.CurEstimate.PilOrders) {
      var Order = this.CurEstimate.PilOrders[_index4];

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

          this.CurEstimate.PilOrders[_index4].STS = "Planned";
          this.CurEstimate.PilOrders[_index4].Pos = this.CurEstimate.Position;
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

        if (isNaN(Speed)) {
          VLMAlertDanger("PM_ANGLE : Error getting boatSpeed try again later...");
          this.Stop();
          return;
        }

        NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed / 3600.0 * this.Boat.VLMInfo.VAC, Hdg);
        break;

      case PM_HEADING:
        // Going fixed bearing, get boat speed, move along loxo
        Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, Hdg);

        if (isNaN(Speed)) {
          VLMAlertDanger("PM_ANGLE : Error getting boatSpeed try again later...");
          this.Stop();
          return;
        }

        NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed / 3600.0 * this.Boat.VLMInfo.VAC, Hdg);
        break;

      case PM_ORTHO:
      case PM_VMG:
      case PM_VBVMG:
        Dest = this.GetNextWPCoords(this.CurEstimate);

        if (this.CurEstimate.Mode == PM_ORTHO) {
          Hdg = this.CurEstimate.Position.GetOrthoCourse(Dest);
          Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, Hdg);

          if (isNaN(Speed)) {
            VLMAlertDanger("PM_ANGLE : Error getting boatSpeed try again later...");
            this.Stop();
            return;
          }

          NewPos = this.CurEstimate.Position.ReachDistOrtho(Speed / 3600.0 * this.Boat.VLMInfo.VAC, Hdg);
        } else {
          if (this.CurEstimate.Mode == PM_VMG) {
            Hdg = PolarsManager.GetVMGCourse(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, this.CurEstimate.Position, Dest);
          } else {
            Hdg = PolarsManager.GetVBVMGCourse(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, this.CurEstimate.Position, Dest);
            /* if (isNaN(Hdg))
            {
              let bkp=1;
            } */
          }

          Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, Hdg);

          if (isNaN(Speed)) {
            VLMAlertDanger("PM_ANGLE : Error getting boatSpeed try again later...");
            this.Stop();
            return;
          }

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

  this.GetPilotPoints = function () {
    var RetPoints = []; // Check if an update is required from AutoPilot;

    for (var _index5 in this.CurEstimate.PilOrders) {
      var Order = this.CurEstimate.PilOrders[_index5];

      if (Order && typeof Order.Pos !== "undefined") {
        RetPoints.push(Order);
      }
    }

    return RetPoints;
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

  this.GetEstimateTracks = function (Track) {
    var RetTracks = [];
    var PrevIndex = null;
    var PrevPoint = null;

    if (typeof track === "undefined" || !Track) {
      if (this.EstimateTrack && this.EstimateTrack[0]) {
        Track = this.EstimateTrack;
      } else {
        return null;
      }
    }

    var TrackStartTick = new Date().getTime();
    var GridOffset = TrackStartTick % (6 * 3600000);
    var TrackIndexStartTick = TrackStartTick - GridOffset + 3.5 * 3600000;

    for (var _index6 in Track) {
      if (Track[_index6]) {
        var est = Track[_index6];
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
} // Server index for multiple grib/tile servers


var SrvIndex = 1; // Minimum map zoom fo rgrib requests

var MIN_MAP_ZOOM = 5; // Global GribMap Manager

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
};

var BeaufortColors = ['#FFFFFF', '#9696E1', '#508CCD', '#3C64B4', '#41B464', '#B4CD0A', '#D2D216', '#E1D220', '#FFB300', '#FF6F00', '#FF2B00', '#E60000', '#7F0000']; // Leaflet Extension layet to draw wind arrows
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
  GetGribMapTime: function GetGribMapTime() {
    return this._Time;
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
  _DrawWindArea: function _DrawWindArea(ctx, InCallBack, CallBackX, CallBackY) {
    var _this = this;

    this.DrawWindDebugCnt++;
    this.DrawWindDebugDepth = InCallBack ? this.DrawWindDebugDepth + 1 : 0; // " + this.DrawWindDebugCnt + " " + this.DrawWindDebugDepth);

    var bstep = this.arrowstep;
    var bounds, zoom;
    var ErrorCatching = 1;
    bounds = this._map.getBounds();
    zoom = this._map.getZoom();

    if (zoom < MIN_MAP_ZOOM) {
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
    var MI = null;

    if (InCallBack && typeof CallBackX !== "undefined" && typeof CallBackY !== "undefined") {
      MinX = CallBackX;
      MaxX = CallBackX;
      MinY = CallBackY;
      MaxY = CallBackY;
    }

    var _loop = function _loop(x) {
      var _loop2 = function _loop2(y) {
        //Récupère le vent et l'affiche en l'absence d'erreur
        try {
          //winfo = windarea.getWindInfo2(LonLat.lat, LonLat.lon, this.time, wante, wpost);
          //this.drawWind(ctx, p.x, p.y, winfo);
          var self = _this;
          MI = GribMgr.WindAtPointInTime(_this._Time, y, x,
          /* jshint -W083*/
          InCallBack ? null : function () {
            self._update(true, x, y);
          });
          /*jshint +W083*/

          if (MI) {
            var _LatLng = L.latLng(y, x);

            var p = map.project(_LatLng, zoom);

            _this._drawWind(ctx, p.x - p0.x, p.y - p0.y, zoom, MI.Speed, MI.Heading);
          }
          /*else
          {
            this._drawWind(ctx, p.x - p0.x, p.y - p0.y, zoom, 0, 0);
          }*/

        } catch (error) {
          if (ErrorCatching > 0) {
            console.log('_DrawWindArea ' + x + " / " + y + " / <br>" + error);
            ErrorCatching -= 1;
          }
        }
      };

      for (var y = MinY; y <= MaxY; y += DY) {
        _loop2(y);
      }
    };

    for (var x = MinX; x <= MaxX; x += DX) {
      _loop(x);
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
    var Beaufort = GribMgr.GetBeaufort(wspeed);
    return BeaufortColors[Beaufort];
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
// Beaufort Scale

var BeaufortScale = [1, 4, 7, 11, 17, 22, 28, 34, 41, 48, 56, 64];
var MaxBeaufort = 12;

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
  this.BeaufortCache = [];
  this.BeaufortCacheHits = 0;
  this.BeaufortRecursionHits = 0;
  this.BeaufortCacheRatio = 0;

  this.Init = function () {
    if (this.Inited || this.Initing) {
      return;
    }

    this.Initing = true;
    $.get("/ws/windinfo/list.php?v=" + Math.round(new Date().getTime() / 1000 / 60 / 3), this.HandleGribList.bind(this));
  }; //TODO Put Back the unreadable dichytomy selection 


  this.GetBeaufort = function (wspeed, min, max) {
    if (!min && !max) {
      wspeed = Math.floor(wspeed);

      if (this.BeaufortCache[wspeed]) {
        this.BeaufortCacheHits++;
        this.BeaufortCacheRatio = this.BeaufortCacheHits / (this.BeaufortCacheHits + this.BeaufortRecursionHits);
        return this.BeaufortCache[wspeed];
      }

      var MidBeaufort = Math.floor(MaxBeaufort / 2);

      if (wspeed < BeaufortScale[MidBeaufort]) {
        return this.GetBeaufort(wspeed, 0, MidBeaufort);
      } else {
        return this.GetBeaufort(wspeed, MidBeaufort, MaxBeaufort);
      }
    } else {
      //debugger;
      this.BeaufortRecursionHits++;
      var Mid = Math.floor((max + min) / 2);

      if (wspeed === BeaufortScale[Mid]) {
        this.BeaufortCache[wspeed] = Mid;
        return Mid;
      } else if (wspeed < BeaufortScale[Mid]) {
        if (Mid !== min) {
          return this.GetBeaufort(wspeed, min, Mid);
        } else {
          this.BeaufortCache[wspeed] = Mid;
          return Mid;
        }
      } else {
        if (Mid + 1 < max) {
          return this.GetBeaufort(wspeed, Mid, max);
        } else {
          this.BeaufortCache[wspeed] = max;
          return max;
        }
      }
    }
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


    var MI0 = this.GetHydbridMeteoAtTimeIndex(TableIndex, Lat, NormalizeLongitudeDeg(Lon));

    if (!MI0) {
      return false;
    }

    var MI1 = this.GetHydbridMeteoAtTimeIndex(TableIndex + 1, Lat, NormalizeLongitudeDeg(Lon));

    if (!MI1) {
      return false;
    }

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


    var LonIdx1 = (180 / this.GribStep + Math.floor(Lon / this.GribStep) + 360.0 / this.GribStep) % (360 / this.GribStep);
    var LatIdx1 = (90 / this.GribStep + Math.floor(Lat / this.GribStep) + 180.0 / this.GribStep) % (180 / this.GribStep);
    var LonIdx2 = (LonIdx1 + 1) % (360 / this.GribStep);
    var LatIdx2 = (LatIdx1 + 1) % (180 / this.GribStep);
    var dX = Lon_pos / this.GribStep - Math.floor(Lon_pos / this.GribStep);
    var dY = Lat_pos / this.GribStep - Math.floor(Lat_pos / this.GribStep);
    /*// Get UVS for each 4 grid points
    if (!this.Tables[TableIndex] || !this.Tables[TableIndex][LonIdx1] || !this.Tables[TableIndex][LonIdx2] ||
      !this.Tables[TableIndex][LonIdx1][LatIdx1] || !this.Tables[TableIndex][LonIdx1][LatIdx2] ||
      !this.Tables[TableIndex][LonIdx2][LatIdx2] || !this.Tables[TableIndex][LonIdx2][LatIdx1])
    {
      return null;
    }*/

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

      for (var _index7 in e.gribs_url) {
        if (e.gribs_interim_url) {
          if (e.gribs_interim_url[_index7]) {
            var url = e.gribs_interim_url[_index7].replace(".grb", ".txt");

            var seed = 0; //parseInt((new Date).getTime());
            //console.log("smartgrib points out " + url);

            $.get("/cache/gribtiles/" + url + "&v=" + seed, this.HandleSmartGribData.bind(this, LoadKey, url));
          }
        } else if (e.gribs_url[_index7]) {
          var _url = e.gribs_url[_index7].replace(".grb", ".txt");

          var _seed = 0; //parseInt((new Date).getTime());
          //console.log("smartgrib points out " + url);

          $.get("/cache/gribtiles/" + _url + "&v=" + _seed, this.HandleSmartGribData.bind(this, LoadKey, _url));
        }
      }
    } else {
      console.log(e);
    }
  }; // Callback handling processing of grib data from a smartgrib URL


  this.HandleSmartGribData = function (LoadKey, Url, e) {
    var DataOK = this.ProcessInputGribData(Url, e, LoadKey); //this.LoadQueue[LoadKey].Length--;

    if (DataOK && this.LoadQueue[LoadKey]) {
      // Successfull load of one item from the loadqueue
      // Clear all pending callbacks for this call
      for (var _index8 in this.LoadQueue[LoadKey].CallBacks) {
        if (this.LoadQueue[LoadKey].CallBacks[_index8]) {
          this.LoadQueue[LoadKey].CallBacks[_index8]();
        }
      }

      delete this.LoadQueue[LoadKey];
    }
  };

  this.ForceReloadGribCache = function (LoadKey, Url) {
    var Seed = 0; //parseInt(new Date().getTime(),10);

    $.get("/cache/gribtiles/" + Url + "&force=yes&seed=" + Seed, this.HandleSmartGribData.bind(this, LoadKey, Url));
  }; // Read Grib Data


  this.ProcessInputGribData = function (Url, Data, LoadKey) {
    var Lines = Data.split("\n");
    var TotalLines = Lines.length;
    var Catalog = [];
    var HeaderCompleted = false;
    var DataStartIndex = 0; // Handle cache mess

    if (Data === "--\n") {
      /*let Parms = Url.split("/")
      this.LoadQueue[LoadKey]++;
      if (Parms[2] != 15)
      {
        let i = 0;
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

      if (Line === "--" || Line.search(":") == -1) {
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
      Ret.DateIndex = parseInt(Fields[POS_INDEX].substring(0, Fields[POS_INDEX].indexOf("hr")), 10) / 3; // Expand weather date duration if found in the catalog

      var ForecastHour = new Date(this.MinWindStamp.getTime() + 3 * 3600000 * Ret.DateIndex);

      if (ForecastHour > this.MaxWindStamp) {
        this.MaxWindStamp = ForecastHour;
        this.WindTableLength = Ret.DateIndex + 1;
        this.TableTimeStamps[Ret.DateIndex] = ForecastHour.getTime() / 1000;
      }
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

  for (var _index9 = 0; _index9 <= 0; _index9++) {
    var time = new Date(Boat.VLMInfo.LUP * 1000 + _index9 * Boat.VLMInfo.VAC * 1000);
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
  // Load translation strings

  InitLocale(); // Init Menus()

  InitMenusAndButtons(); // Start-Up Polars manager

  PolarsManager.Init(); // Init Alerts

  InitAlerts(); // Handle page parameters if any

  var NoMap = CheckPageParameters(); // Start the page clocks

  setInterval(PageClock, 1000); // Init maps

  if (!NoMap) {
    CheckLogin();
    LeafletInit(); // Async init of weathermap to current map

    setTimeout(function () {
      // Wind Layer
      map.GribMap = new GribMap.Layer().addTo(map);
    }, 500);
  } // Load flags list (keep at the end since it takes a lot of time)


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
  }).addTo(map); // Wind & Mouse Pos Control

  map.WMControl = L.control.WindMouseControl().addTo(map); // Draggable compass

  DrawCompass(); // Map Events

  map.on('mousemove', HandleMapMouseMove);
  map.on('moveend', HandleMapGridZoom);
  map.on('click', HandleMapMouseClick);
  map.on("zoomend", HandleMapGridZoom);
}

function DrawCompass() {
  if (VLM2Prefs.MapPrefs.ShowCompass) {
    if (map.Compass) {
      map.Compass.addTo(map);
      map.Compass.on("dragend", HandleCompassDragEnd);
      map.Compass.on("mousemove", HandleCompassMouseMove);
      map.Compass.on("mouseout", HandleCompassMouseOut);
    } else {
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
    }
  } else if (map.Compass) {
    map.Compass.off("dragend", HandleCompassDragEnd);
    map.Compass.off("mousemove", HandleCompassMouseMove);
    map.Compass.off("mouseout", HandleCompassMouseOut);
    map.Compass.remove();
  }
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
  var retstop = false;
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


              retstop = true;
              break;

            case "VLMIndex":
              RacingBarMode = false;
              /* jshint -W083*/

              VLMINdexFt.OnReadyTable = function () {
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

  return retstop;
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

    var _index10;

    var rank = 1;

    for (_index10 in result) {
      if (result[_index10]) {
        result[_index10].rank = rank;
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
  //OnPlayerLoadedCallBack = function()
  //{
  var CallBack = function CallBack(Result) {
    FillRaceInfoHeader(Result);
  };

  RankingFt.RaceRankingId = RaceId;
  LoadRaceInfo(RaceId, 0, CallBack);
  LoadRankings(RaceId, OtherRaceRankingLoaded); //};

  /*if (typeof _CurPlayer !== "undefined" && _CurPlayer && _CurPlayer.CurBoat)
  {
    OnPlayerLoadedCallBack();
    OnPlayerLoadedCallBack = null;
  }*/
}

function OtherRaceRankingLoaded(RaceId) {
  $("#Ranking-Panel").show();
  SortRanking("RAC", 0, RaceId);
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
  /*$("#Ranking-Panel").on('show.bs.collapse', function(e)
  {
    ResetRankingWPList(e);
  });*/
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
  if (typeof map.GribMap === "undefined") {
    return;
  }

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

  var RaceFeatures = GetRaceMapFeatures(CurBoat);

  if (RaceFeatures) {
    for (var _index11 in RaceFeatures.PilotMarkers) {
      if (RaceFeatures.PilotMarkers[_index11]) {
        RaceFeatures.PilotMarkers[_index11].remove();

        RaceFeatures.PilotMarkers[_index11].unbindPopup(RaceFeatures.PilotMarkers[_index11].getPopup());
      }
    }
  }

  CurBoat.Estimator.Start(HandleEstimatorProgress);
}

function HandleEstimatorProgress(Complete, Pct, Dte) {
  var Est = _CurPlayer.CurBoat.Estimator;

  if (!Est) {
    return;
  }

  if (Complete) {
    $("#StartEstimator").removeClass("hidden");
    $("#PbEstimatorProgressBar").addClass("hidden"); //$("#PbEstimatorProgressText").addClass("hidden")

    $("#EstimatorStopButton").addClass("hidden");
    Est.LastPctRefresh = -1;
    Est.LastPctDraw = -1;
    DrawBoat(_CurPlayer.CurBoat);
  } else if (Pct - Est.LastPctRefresh > 0.25) {
    $("#EstimatorStopButton").removeClass("hidden");
    $("#StartEstimator").addClass("hidden");
    $("#PbEstimatorProgressBar").removeClass("hidden");
    $("#PbEstimatorProgressText").removeClass("hidden");
    $("#PbEstimatorProgressText").text(Pct);
    $("#PbEstimatorProgress").css("width", Pct + "%");
    $("#PbEstimatorProgress").attr("aria-valuenow", Pct);
    $("#PbEstimatorProgress").attr("aria-valuetext", Pct);
    Est.LastPctRefresh = Pct;
  } else if (Pct - Est.LastPctDraw > 1) {
    DrawBoatEstimateTrack(_CurPlayer.CurBoat, GetRaceMapFeatures(_CurPlayer.CurBoat));
    Est.LastPctDraw = Pct;
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
    BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Angle", RoundPow(Boat.VLMInfo.PIP, VLM2Prefs.InputDigits)]);
  } else {
    BoatFieldMappings.push([FIELD_MAPPING_TEXT, ".BoatWindAngle", RoundPow(Math.abs(Boat.VLMInfo.TWA), 1)]);
    BoatFieldMappings.push([FIELD_MAPPING_VALUE, "#PM_Angle", RoundPow(Boat.VLMInfo.TWA, VLM2Prefs.InputDigits)]);
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
  for (var _index12 in MappingTable) {
    if (MappingTable[_index12]) {
      switch (MappingTable[_index12][0]) {
        case FIELD_MAPPING_TEXT:
          $(MappingTable[_index12][1]).text(MappingTable[_index12][2]);
          break;

        case FIELD_MAPPING_VALUE:
          $(MappingTable[_index12][1]).val(MappingTable[_index12][2]);
          break;

        case FIELD_MAPPING_CHECK:
          $(MappingTable[_index12][1]).prop('checked', MappingTable[_index12][2]);
          break;

        case FIELD_MAPPING_IMG:
          $(MappingTable[_index12][1]).attr('src', MappingTable[_index12][2]);
          break;

        case FIELD_MAPPING_CALLBACK:
          MappingTable[_index12][2](MappingTable[_index12][1]);

          break;

        case FIELD_MAPPING_STYLE:
          $(MappingTable[_index12][1]).css(MappingTable[_index12][2], MappingTable[_index12][3]);
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

    for (var _index13 in PolarLine) {
      if (PolarLine[_index13]) {
        var l = PolarLine[_index13];
        _index13 = parseInt(_index13, 10);
        var a = _index13 * dAlpha;
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

    for (var _index14 = 1; As * _index14 - 1 <= MaxSpeed; _index14++) {
      Context.beginPath();
      Context.strokeStyle = "#7FFFFF";
      Context.arc(Cx, Cy, S * _index14 * As / MaxSpeed, Math.PI / 2, 1.5 * Math.PI, true);
      Context.stroke();
      Context.strokeText(" " + As * _index14, Cx + 1 + As * S * _index14 / MaxSpeed, Cy + 10);
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
    for (var _index15 in Boat.VLMInfo.PIL) {
      if (Boat.VLMInfo.PIL[_index15]) {
        var PilLine = GetPilototoTableLigneObject(Boat, _index15);
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

  for (var _index16 in classes) {
    if (classes[_index16]) {
      $('td').closest('tr').removeClass(classes[_index16]);
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

    for (var _index17 in racelist) {
      if (racelist[_index17]) {
        RaceArray.push(racelist[_index17]);
      }
    }

    RaceArray.sort(RaceSorter);

    for (var _index18 in RaceArray) {
      if (RaceArray[_index18]) {
        AddRaceToList(RaceArray[_index18]);
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
  var d = new Date(0); // The 0 there is the key, which sets the date to the epoch
  //d.setUTCSeconds(utcSeconds);

  var RaceJoinStateClass;
  var StartMoment;
  var RecordRace = (race.racetype & RACE_TYPE_RECORD) == RACE_TYPE_RECORD;

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

  var code = '<div class="raceheaderline panel panel-default ' + RaceJoinStateClass + '" )>' + '  <div data-toggle="collapse" href="#RaceDescription' + race.idraces + '" class="panel-body collapsed " data-parent="#RaceListPanel" aria-expanded="false">' + '    <div class="col-xs-12">' + '      <div class="col-xs-3">' + '        <img class="racelistminimap" src="/cache/minimaps/' + race.idraces + '.png" ></img>' + '      </div>' + '      <div class="col-xs-9">' + '        <div class="col-xs-12">' + (RecordRace ? '<span class="PRecordRace">P</span>' : '') + '          <span ">' + race.racename + (race.racelength !== "0" ? ' (' + race.racelength + ' Nm)' : '') + '          </span>' + '        </div>' + '        <div class="btn-group col-xs-12">' + '          <button id="JoinRaceButton" type="button" class="' + (race.CanJoin ? '' : 'hidden') + ' btn-default btn-md col-xs-4" IdRace="' + race.idraces + '"  >' + GetLocalizedString("subscribe") + '          </button>' + '          <button id="SpectateRaceButton" type="button" class="ShowRaceInSpectatorMode btn-default btn-md col-xs-4" IdRace="' + race.idraces + '"  >' + GetLocalizedString("Spectator") + '          </button>' + '          <button type="button" class="ShowICSButton btn-default btn-md col-xs-4" IdRace="' + race.idraces + '"  >' + GetLocalizedString('ic') + '          </button>' + '          <button type="button" class="RankingButton btn-default btn-md col-xs-4" IdRace="' + race.idraces + '"  >' + GetLocalizedString('ranking') + '          </button>' + '        </div>' + '      </div>' + '    </div>' + (StartMoment ? '    <div class="col-xs-12">' + '       <span "> ' + StartMoment + '       </span>' + '    </div>' : "") + '  </div>' + '  <div id="RaceDescription' + race.idraces + '" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">' + '    <div class="panel-body">' + '      <div class="col-xs-12"><img class="img-responsive" src="/cache/racemaps/' + race.idraces + '.png" width="530px"></div>' + '        <div class="col-xs-12"><p>' + GetLocalizedString('race') + ' : ' + race.racename + '</p>' + '          <p>Départ : ' + GetLocalUTCTime(race.deptime * 1000, true, true) + '</p>' + '          <p>' + GetLocalizedString('boattype') + ' : ' + race.boattype.substring(5) + '</p>' + '          <p>' + GetLocalizedString('crank') + ' : ' + race.vacfreq + '\'</p>' + '          <p>' + GetLocalizedString('locktime') + parseInt(race.coastpenalty, 10) / 60.0 + ' \'</p>' + '          <p>' + GetLocalizedString('closerace') + GetLocalUTCTime(race.closetime * 1000, true, true) + '</p>' + (!RecordRace ? (race.RaceCloseDate ? '          <p>' + GetLocalizedString('endrace') + GetLocalUTCTime(race.RaceCloseDate * 1000, true, true) + '</p>' : '          <p>' + GetLocalizedString('endrace') + (100 + race.firstpcttime) + '%') + '</p>' : '') + '        </div>' + '      </div>' + '    </div>' + '  </div>';
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
          $("#GribLoadOK").removeClass("GribNotOK").removeClass("GribGetsOld");
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
    var _index19;

    if (typeof Boat !== "undefined" && Boat && typeof Boat.RaceInfo !== "undefined" && Boat.RaceInfo && RaceId === Boat.RaceInfo.RaceId) {
      BuildWPTabList(_index19, InitNeeded);
      InitComplete = true;
    } else if (_typeof(OtherRaceWPs) === "object" && OtherRaceWPs) {
      BuildWPTabList(OtherRaceWPs, InitNeeded);
      InitComplete = true;
    } else {
      var Version = 0;

      if (typeof Boat !== "undefined" && Boat && typeof Boat.VLMInfo !== "undefined") {
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

function SortRanking(style, WPNum, OtherRaceWPs) {
  var Boat = null; //$('#RankingTableBody').empty();

  if (typeof _CurPlayer !== "undefined" && _CurPlayer) {
    Boat = _CurPlayer.CurBoat;
  }

  CheckWPRankingList(Boat, OtherRaceWPs); // Fix Me use logged player (if any to avoid that)
  //if (typeof Boat === "undefined" || !Boat)
  //{
  //  return;
  //}

  var Friends = null;

  if (typeof Boat !== "undefined" && Boat && Boat.VLMPrefs && Boat.VLMPrefs.mapPrefOpponents) {
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

  if (!Boat && !Rankings[RaceId]) {
    return;
  }

  if (Rankings && Rankings[RaceId] && typeof Rankings[RaceId].RacerRanking === "undefined") //|| Rankings[RaceId].RacerRanking.length !== Rankings[RaceId]+1))
    {
      var _index20;

      Rankings[RaceId].RacerRanking = [];

      for (_index20 in Rankings[RaceId]) {
        if (Rankings[RaceId][_index20]) {
          //Rankings[index].idusers=index;
          Rankings[RaceId].RacerRanking.push(Rankings[RaceId][_index20]);
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
    if (Rankings[RaceId].RacerRanking[index] && Boat && Boat.IdBoat === index) {
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

  if (!Boat && (!RankingFt || RankingFt.DrawPending)) {
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

        if (typeof Boat !== "undefined" && Boat && Boat.IdBoat === parseInt(RnkBoat.idusers, 10)) {
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

  for (var _index21 in RaceInfo.races_waypoints) {
    if (RaceInfo.races_waypoints[_index21]) {
      var WP = RaceInfo.races_waypoints[_index21];
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

    for (var _index22 in RaceInfo.races_waypoints) {
      if (RaceInfo.races_waypoints[_index22]) {
        var WP = RaceInfo.races_waypoints[_index22];
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

    for (var _index23 in Exclusions) {
      if (Exclusions[_index23]) {
        var Seg = Exclusions[_index23];
        var row = {};
        row.NSZId = _index23;
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

        if (typeof Boat !== "undefined" && Boat && Boat.IdBoat === parseInt(RnkBoat.idusers, 10)) {
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

        if (typeof Boat !== "undefined" && Boat && Boat.IdBoat === parseInt(RnkBoat.idusers, 10)) {
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
  var boatsearchstring = '<img class="BoatFinder" src="images/search.png" idu=' + RankBoat.idusers + ' ></img>   ';

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

  if (typeof _CurPlayer !== "undefined" && _CurPlayer && typeof _CurPlayer.GetRankingObject !== "undefined" && parseInt(RankBoat.idusers, 10) === _CurPlayer.CurBoat.IdBoat) {
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

    for (var _index24 in BoatRacingStatus) {
      if (RankBoat.status === BoatRacingStatus[_index24]) {
        RetObject.Class += "  " + BoatRacingClasses[BoatRacingStatus[_index24]];
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
  $("#ShowCompass").attr('checked', VLM2Prefs.MapPrefs.ShowCompass);
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

    case "ShowCompass":
      VLM2Prefs.MapPrefs[Id] = Value;
      DrawCompass();
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
})(jQuery); //
// Mouse Coords Control with ZoomLevel and Wind Arrow
//


L.Control.WindMouseControl = L.Control.extend({
  options: {
    position: 'bottomleft'
  },
  _GetControlHTML: function _GetControlHTML() {
    var ret = "<table><tr>" + "<td class='leaflet-control-windmouse_zoomcol'><span>Zoom : </span> <span id='LWM_ZoomLevel'></span></td>" + "<td class='leaflet-control-windmouse_latlon'><span>Lat : </span> <span id='LWM_Lat'></span></td>" + "<td class='leaflet-control-windmouse_latlon'><span>Lon : </span> <span id='LWM_Lon'></span></td></tr><tr>" + "<td class='leaflet-control-windmouse_wndimg'><img class='BeaufortImg'></td>" + "<td class='leaflet-control-windmouse_wndhdg'><span id='LWM_Hdg'></span></td>" + "<td class='leaflet-control-windmouse_wndspd'><span id='LWM_Spd'></span></td>" + "</tr></table>";
    return ret;
  },
  onAdd: function onAdd(map) {
    this._map = map;
    var FrameClassName = "leaflet-control-windmouse-frame";
    this._Container = L.DomUtil.create('div', FrameClassName);
    this._ZContainer = L.DomUtil.create('div', "", this._Container);
    this._ZContainer.innerHTML = this._GetControlHTML();
    map.on("mousemove", this._Update, this);
    map.on("zoomend", this._ZoomEnd, this);

    this._SetZoom(map.getZoom());

    return this._Container;
  },
  _Update: function _Update(e) {
    if (typeof map.GribMap === "undefined") {
      return;
    }

    var Lat = e.latlng.lat;
    var Lon = e.latlng.lng;

    var CurZoom = this._map.getZoom();

    var MI = null;

    if (CurZoom >= MIN_MAP_ZOOM) {
      MI = GribMgr.WindAtPointInTime(map.GribMap.GetGribMapTime(), Lat, Lon);
    }

    var FieldMappings = [];
    FieldMappings.push([FIELD_MAPPING_TEXT, "#LWM_Lat", RoundPow(Lat, 3)]);
    FieldMappings.push([FIELD_MAPPING_TEXT, "#LWM_Lon", RoundPow(Lon, 3)]);

    if (MI) {
      FieldMappings.push([FIELD_MAPPING_TEXT, "#LWM_Hdg", RoundPow(MI.Speed, 2) + " kts"]);
      FieldMappings.push([FIELD_MAPPING_TEXT, "#LWM_Spd", RoundPow(MI.Heading, 2) + " °"]);
      var Beaufort = GribMgr.GetBeaufort(MI.Speed);
      $(".BeaufortImg").css("background-position", '-0px -' + 24 * Beaufort + 'px');
      var Angle = MI.Heading - 56;
      $(".BeaufortImg").css("transform", 'rotate(' + Angle + 'deg)');
    } else {
      FieldMappings.push([FIELD_MAPPING_TEXT, "#LWM_Hdg", "-- kts"]);
      FieldMappings.push([FIELD_MAPPING_TEXT, "#LWM_Spd", "-- °"]);
      $(".BeaufortImg").css('style="background-position: -0px -0px"');
    }

    FillFieldsFromMappingTable(FieldMappings);

    this._SetZoom(CurZoom);
  },
  _ZoomEnd: function _ZoomEnd(e) {
    this._SetZoom(this._map.getZoom());
  },
  _SetZoom: function _SetZoom(z) {
    var FieldMappings = [];
    FieldMappings.push([FIELD_MAPPING_TEXT, "#LWM_ZoomLevel", RoundPow(z, 1)]);
    FillFieldsFromMappingTable(FieldMappings);
  }
});

L.control.WindMouseControl = function (options) {
  return new L.Control.WindMouseControl(options);
};

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
var PilototoMarker = L.Icon.extend({
  options: {
    iconSize: [24, 24],
    iconAnchor: [6, 24],
    iconUrl: 'images/Pilototo.png'
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

function GetPilototoMarker(Order, OrderIndex) {
  var ret = new PilototoMarker();
  ret.Order = Order;
  ret.OrderIndex = OrderIndex;
  return ret;
}

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

function ClearCurrentMapMarkers(Boat) {
  if (Boat && Boat.RaceMapFeatures) {
    if (Boat.RaceMapFeatures.OppPopup && Boat.RaceMapFeatures.OppPopup.PrevOpp) {
      Boat.RaceMapFeatures.OppPopup.PrevOpp.closePopup();
      Boat.RaceMapFeatures.OppPopup.PrevOpp.unbindPopup();
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
    for (var _index25 in Feat) {
      RestoreMarkersOnMap(Feat[_index25]);
    }
  } else if (_typeof(Feat) === "object" && typeof Feat._leaflet_id === "undefined") {
    for (var member in Feat) {
      switch (member) {
        // Special restore handling (ie do not show opppopup again)
        case "OppPopup":
          break;

        default:
          RestoreMarkersOnMap(Feat[member]);
          break;
      }
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
    for (var _index26 in Feat) {
      RemoveFromMap(Feat[_index26]);
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

  for (var _index27 in EOLSigns) {
    while (EOLSigns[_index27] && RetString.indexOf(EOLSigns[_index27]) !== -1) {
      RetString = RetString.replace(EOLSigns[_index27], "<br>");
    }
  }

  return RetString;
}

function GetLocalizedString(StringId, params) {
  var RetString = "";

  if (typeof _LocaleDict !== "undefined" && _LocaleDict && StringId in _LocaleDict) {
    RetString = HTMLDecode(_LocaleDict[StringId]);
  } else if (typeof _EnDict !== "undefined" && _EnDict && StringId in _EnDict) {
    RetString = HTMLDecode(_EnDict[StringId]);
  } else {
    RetString = StringId;
  }

  if (params) {
    RetString = vsprintf(RetString, params);
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
      for (var _index28 in Data.list) {
        PolarsManager.Polars["boat_" + Data.list[_index28]] = null;
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

    for (var _index29 in this.PolarLoaderQueue[PolarName].callbacks) {
      var callback = this.PolarLoaderQueue[PolarName].callbacks[_index29];

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


      for (var _index30 in RetPolar) {
        if (RetPolar[_index30]) {
          RetPolar[_index30] /= MaxSpeed;
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
        return NaN;
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
            return NaN;
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
      return NaN;
    }

    var RetVal = 0;

    if (VMGAlpha > VMGBeta) {
      RetVal = CapOrtho - b_Alpha * ISigne;
    } else {
      RetVal = CapOrtho - b_Beta * ISigne;
    }
    /* if (isNaN(RetVal)) 
    {
      let bkp=0;        
    } 
    */


    return RetVal;
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
    for (var _index31 in Polar[0]) {
      if (_index31 > 0 && Polar[0][_index31] > WindSpeed) {
        break;
      }

      PolarObject.WindLookup[IntWind] = Math.floor(_index31);
      SpeedCol1 = Math.floor(_index31);
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
    for (var _index32 in Polar) {
      if (_index32 > 0 && Polar[_index32][0] > Alpha) {
        break;
      }

      PolarObject.AngleLookup[IntAlpha] = Math.floor(_index32);
      AlphaRow1 = Math.floor(_index32);
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

    if (Lon * this.Lon.Value < 0 && Math.abs(this.Lon.Value - Lon) > 90) {
      var Sign = 1; // Antimeridien crossing

      if (this.Lon.Value - Lon < 0) {
        Sign = -1;
      }

      return new VLMPosition(Lon + 360 * Sign, Lat);
    } else {
      return new VLMPosition(Lon, Lat);
    }
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
        var lcos = (Math.sin(lat2) - Math.sin(lat1) * Math.cos(d)) / den;

        if (lcos > 1) {
          lcos = 1;
          console.log("Nan Catch pos");
        } else if (lcos < -1) {
          lcos = -1;
          console.log("Nan Catch neg");
        }

        if (g < 0) {
          retval = 2 * Math.PI - Math.acos(lcos);
        } else {
          retval = Math.acos(lcos);
        }
      }

      retval = Rad2Deg(retval % (2 * Math.PI)); // if (isNaN(retval))
      // {
      //   let bkp=0;
      // }

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
} // Class to persist race infos


var Race = function Race(RaceId) {
  _classCallCheck(this, Race);

  if (typeof RaceId == "number") {
    this.RaceId = RaceId;
  } else {
    this.RaceId = parseInt(RaceId, 10);
  }

  this.LastUpdate = new Date(0); // Clear data from preferences

  this.ClearData = function () {
    VLM2Prefs.ClearRaceData(this.RaceId);
  }; // Returns true if race has been updated since last call
  // Stores the race update date in the local storage as well
  // Returns false if race updates have happened


  this.CheckRaceUpdates = function (RaceInfo) {
    var RetOK = true;

    if (RaceInfo) {
      var _RaceId = RaceInfo.idraces;
      var UpdateDate = new Date(parseInt(RaceInfo.VER, 10) * 1000);
      var RI = VLM2Prefs.GetRaceFromStorage(_RaceId);
      RI.LastUpdate = new Date(RI.LastUpdate);

      if (RI.LastUpdate < UpdateDate) {
        if (VLM2Prefs) {
          RI.LastUpdate = UpdateDate;
          VLM2Prefs.Save();
          RetOK = false;
        }
      }
    }

    return RetOK;
  };

  this.HasSave = function () {
    return VLM2Prefs.HasRaceStorage(this.RaceId);
  };

  this.UpdatedForRaceStart = function (RaceInfo) {
    var RetOk = false;

    if (RaceInfo) {
      RetOK = new Date(RaceInfo.deptime * 1000) == new Date(RaceInfo.VER * 1000);
    }

    return RetOk;
  };

  this.Subscribe = function (BoatId) {
    $.post("/ws/boatsetup/race_subscribe.php", "parms=" + JSON.stringify({
      idu: BoatId,
      idr: this.RaceId
    }), function (data) {
      if (data.success) {
        $("#RacesListForm").modal("hide");
        var Not = new RaceNewsHandler("", GetLocalizedString("youengaged"));
        StatMGR.Stat("RaceSubscribe", RaceId);
        Not.Show();
        var RI = VLM2Prefs.GetRaceFromStorage(RaceId);
        RI.LastUpdate = new Date();
        VLM2Prefs.Save();
      } else {
        var Msg = data.error.msg + '\n' + data.error.custom_error_string;
        VLMAlertDanger(Msg);
      }
    });
  };
};

var RaceNewsHandler = function RaceNewsHandler(Title, Message) {
  _classCallCheck(this, RaceNewsHandler);

  this.Title = Title;
  this.Message = Message;

  this.Show = function () {
    $("#RaceNewsBox_Title").text(Title);
    $("#NewsBody1").html(Message);
    $("#RaceNewsBox").modal({
      backdrop: 'static',
      keyboard: false
    });
  };
};

var StatsManager = function StatsManager() {
  _classCallCheck(this, StatsManager);

  this.NoStat = true;

  this.CheckGConsent = function () {
    if (VLM2Prefs && VLM2Prefs.GConsentDate) {
      this.NoStat = !isNaN(VLM2Prefs.GConsentDate);
    } else if (VLM2Prefs) {
      var CurDate = new Date().getTime();
      var LastNo = VLM2Prefs.GConsentLastNo;

      if (LastNo) {
        LastNo = new Date(LastNo);
      }

      if (VLM2Prefs.GConsentDate === null && (VLM2Prefs.GConsentLastNo === null || LastNo.getTime() + 6 * 30 * 24 * 3600000 < CurDate)) {
        $(".GConsentToggle").on("click", this.HandleGconsentToggle.bind(this)); //this.SetConsent(false);

        $("#GConsentModal").modal({
          backdrop: 'static',
          keyboard: false
        });
      }
    }

    return;
  };

  this.HandleGconsentToggle = function (e) {
    var BtnId = e.currentTarget.id;
    var Btn = $("#" + BtnId);

    if (BtnId === "GConsentToggleNo") {
      $("#GConsentToggleNo").addClass("btn-danger").removeClass("btn-default");
      $("#GConsentToggleYes").removeClass("btn-success").addClass("btn-default");
      this.SetConsent(false);
    } else {
      $("#GConsentToggleNo").removeClass("btn-danger").addClass("btn-default");
      $("#GConsentToggleYes").addClass("btn-success").removeClass("btn-default");
      this.SetConsent(true);
    }

    $("#GConsentCloseFormBtn").removeClass("ui-state-disabled");
  };

  this.SetConsent = function (status) {
    this.NoStat = status;

    if (status) {
      VLM2Prefs.GConsentDate = new Date();
      VLM2Prefs.GConsentLastNo = null;
    } else {
      VLM2Prefs.GConsentLastNo = new Date();
      VLM2Prefs.GConsentDate = null;
    }

    VLM2Prefs.Save();
  };

  this.Stat = function (Evt, EvtCategory, EvtLabel, EvtValue) {
    if (!this.NoStat && typeof gtag !== "undefined" && gtag) {
      if (typeof EvtCategory === "undefined" || !EvtCategory) {
        EvtCategory = Evt;
      }

      if (typeof EvtValue === "number") {
        gtag('event', Evt, {
          'event_category': EvtCategory,
          'event_label': EvtLabel,
          'value': EvtValue
        });
      } else {
        gtag('event', Evt, {
          'event_category': EvtCategory,
          'event_label': EvtLabel
        });
      }
    }
  };
};

var StatMGR = new StatsManager();

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

      var Loxo2 = Seg.P1.GetOrthoCourse(CurPos);
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
  if (_IsLoggedIn) {
    StatMGR.CheckGConsent();
  }

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
    StatMGR.CheckGConsent();
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

      for (var _index33 in result.flags) {
        if (result.flags[_index33]) {
          var title = result.flags[_index33];
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
      ClearCurrentMapMarkers(_CurPlayer.CurBoat);
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
          NotifyEndOfRace(Boat.IdBoat); //GetLastRacehistory();

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
}

function NotifyEndOfRace(BoatId) {
  $.get("/ws/boatinfo/palmares.php?idu=" + BoatId, function (e) {
    var index;

    if (e.success) {
      for (index in e.palmares) {
        if (e.palmares[index]) {
          var RaceInfo = e.palmares[index];
          var RaceObj = new Race(RaceInfo.idrace);

          if (RaceObj.HasSave()) {
            var EORMessage = GetLocalizedString('EndOfRaceMessage', [RaceInfo.racename, RaceInfo.ranking.rank, RaceInfo.ranking.racercount]);
            var RaceNews = new RaceNewsHandler(GetLocalizedString('EndOfRaceTitle'), EORMessage);
            RaceNews.Show();
            RaceObj.ClearData();
            StatMGR.Stat("EndOfRaceMessage", RaceInfo.raceid);
          }

          break;
        }
      }
    }
  });
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

      for (var _index34 in result.tracks) {
        if (result.tracks[_index34]) {
          var P = new VLMPosition(result.tracks[_index34][1] / 1000.0, result.tracks[_index34][2] / 1000.0);
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

      var _index35;

      for (_index35 in result.Exclusions) {
        if (result.Exclusions[_index35]) {
          var Seg = result.Exclusions[_index35];

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
    var RaceObj = new Race(Boat.VLMInfo.RAC);

    if (!RaceObj.CheckRaceUpdates(Boat.RaceInfo)) {
      if (RaceObj.UpdatedRaceForStart) {
        var title = GetLocalizedString("RaceStartedNotice");
        var message = '<H2>' + GetLocalizedString("racestarted", [Boat.VLMInfo.racename, Boat.VLMInfo.deptime]) + '</H2>';
        var Notify = new RaceNewsHandler(title, message);
        Notify.Show();
      } else {
        var _title = GetLocalizedString("RaceChangeNotice");

        var _message = '<H2>' + Boat.RaceInfo.UpdateReason + '</H2>' + GetLocalizedString("RaceChangeNoticeText");

        var _Notify = new RaceNewsHandler(_title, _message);

        _Notify.Show(); //VLMAlertInfo("Race update" + Boat.RaceInfo.UpdateReason);

      }
    }

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

function GetNextPilOrderDate(PilIndex) {
  if (Boat && Boat.VLMInfo && Boat.VLM.PIL) {
    NextPilOrder = null;

    for (var _index36 in Boat.VLM.PIL) {
      if (Boat.VLM.PIL[_index36]) {
        if (Boat.VLM.PIL[_index36].STS === "pending") {
          if (_index36 > PilIndex) {
            return _index36;
          }
        }
      }
    }
  }

  return -1;
}

function GetPilototoMarkerText(Order) {
  var OrderMoment = moment("/date(" + Order.TTS * 1000 + ")/");
  var Text = "Date : " + GetLocalUTCTime(OrderMoment, true, true) + '<BR>' + OrderMoment.fromNow();

  switch (parseInt(Order.PIM, 10)) {
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

function DrawBoatEstimateTrack(Boat, RaceFeatures) {
  if (typeof Boat.Estimator !== "undefined" && Boat.Estimator) {
    var tracks = Boat.Estimator.GetEstimateTracks();
    var TrackColors = ['green', 'yellow', 'white'];

    for (var _index37 in tracks) {
      if (RaceFeatures.EstimateTracks && RaceFeatures.EstimateTracks[_index37]) {
        if (typeof tracks[_index37] !== "undefined") {
          RaceFeatures.EstimateTracks[_index37].setLatLngs(tracks[_index37]);
        } else {
          RaceFeatures.EstimateTracks[_index37].remove();

          RaceFeatures.EstimateTracks[_index37] = null;
        }
      } else {
        if (typeof RaceFeatures.EstimateTracks === "undefined") {
          RaceFeatures.EstimateTracks = [];
        }

        if (tracks[_index37]) {
          var Options = {
            weight: 2,
            opacity: 1,
            color: TrackColors[_index37]
          };
          RaceFeatures.EstimateTracks[_index37] = L.polyline(tracks[_index37], Options).addTo(map);
        }
      }
    }

    var PilotPoints = Boat.Estimator.GetPilotPoints();

    if (typeof RaceFeatures.PilotMarkers === "undefined") {
      RaceFeatures.PilotMarkers = [];
    }

    for (var _index38 in PilotPoints) {
      if (PilotPoints[_index38]) {
        var Order = PilotPoints[_index38];
        var _Coords = [Order.Pos.Lat.Value, Order.Pos.Lon.Value];
        var SetText = false;

        if (RaceFeatures.PilotMarkers[_index38] && !RaceFeatures.PilotMarkers[_index38]._map) {
          RaceFeatures.PilotMarkers[_index38].setLatLng(_Coords);

          SetText = true;
        } else if (!RaceFeatures.PilotMarkers[_index38]) {
          var Marker = GetPilototoMarker(Order, _index38);
          RaceFeatures.PilotMarkers[_index38] = L.marker(_Coords, {
            icon: Marker
          }).on("popupopen", HandlePilototoPopup);
          SetText = true;
        }

        if (SetText) {
          var MarkerText = GetPilototoMarkerText(Order);

          RaceFeatures.PilotMarkers[_index38].addTo(map).bindPopup(MarkerText);
        }
      }
    }
  }
}

function HandlePilototoPopup(e) {
  var Marker = e.popup._source.options.icon;

  if (!Marker.Opening) {
    Marker.Opening = true;
    var MarkerText = GetPilototoMarkerText(Marker.Order);
    e.sourceTarget.unbindPopup().bindPopup(MarkerText).closePopup().openPopup();
    Marker.Opening = null;
  }
}

function RepositionCompass(Boat) {
  if (!Boat) {
    return;
  }

  var Features = GetRaceMapFeatures(Boat);

  if (map.Compass) {
    if (Features.Compass && Features.Compass.Lat == -1 && Features.Compass.Lon == -1 || !Features.Compass && Boat.VLMInfo && (Boat.VLMInfo.LAT || Boat.VLMInfo.LON)) {
      map.Compass.setLatLng([Boat.VLMInfo.LAT, Boat.VLMInfo.LON]);
    } else if (Features.Compass && !isNaN(Features.Compass.Lat) && !isNaN(Features.Compass.Lon)) {
      map.Compass.setLatLng([Features.Compass.Lat, Features.Compass.Lon]);
    }
  }
}

function DrawBoatTrack(Boat, RaceFeatures) {
  var PointList = GetSafeTrackPointList(Boat.Track);
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

function GetSafeTrackPointList(Track) {
  var PointList = [];
  var TrackLength = Track.length;
  var PrevLon = 0;
  var LonOffSet = 0;

  for (var _index39 = TrackLength - 1; _index39 >= 0; _index39--) {
    var P = Track[_index39];

    if (PrevLon * P.Lon.Value < 0 && Math.abs(P.Lon.Value - PrevLon) > 90) {
      if (PrevLon < 0) {
        LonOffSet -= 360;
      } else {
        LonOffSet += 360;
      }
    }

    PointList.unshift([P.Lat.Value, P.Lon.Value + LonOffSet]);
    PrevLon = P.Lon.Value;
  }

  return PointList;
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

    var _index40;

    var tmpPolar = [];

    for (_index40 = 0; _index40 <= 180; _index40 += 5) {
      var Speed = PolarsManager.GetBoatSpeed(Boat.VLMInfo.POL, MI.Speed, MI.Heading, MI.Heading + _index40);

      if (isNaN(Speed)) {
        // Just abort in case of not yet loaded polar. Next display should fix it.
        // FixMe - Should we try later or will luck do it for us??
        return;
      }

      for (var Side = -1; Side <= 1; Side += 2) {
        var PolarPos = StartPos.ReachDistLoxo(Speed / 3600.0 * Boat.VLMInfo.VAC * scale, MI.Heading + _index40 * Side);
        var PixPos = [PolarPos.Lat.Value, PolarPos.Lon.Value];
        tmpPolar[Side * _index40 + 180] = PixPos;
      }
    }

    Polar = [];

    for (var _index41 in tmpPolar) {
      if (tmpPolar[_index41]) {
        Polar.push(tmpPolar[_index41]);
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
    for (var _index42 in RaceInfo.races_waypoints) {
      if (!RaceFeature.Gates) {
        RaceFeature.Gates = [];
      }

      if (!RaceFeature.Gates[_index42]) {
        RaceFeature.Gates[_index42] = {};
      }

      var GateFeatures = RaceFeature.Gates[_index42];

      if (RaceInfo.races_waypoints[_index42]) {
        var WPMarker = GateFeatures.Buoy1; // Draw a single race gates

        var WP = RaceInfo.races_waypoints[_index42]; // Fix coords scales

        NormalizeRaceInfo(RaceInfo);
        var cwgate = !(WP.wpformat & WP_CROSS_ANTI_CLOCKWISE); // Draw WP1

        var Pos = new VLMPosition(WP.longitude1, WP.latitude1);
        GateFeatures.Buoy1 = AddBuoyMarker(WPMarker, "WP" + _index42 + " " + WP.libelle + '<BR>' + Pos.toString(), WP.longitude1, WP.latitude1, cwgate); // Second buoy (if any)

        if ((WP.wpformat & WP_GATE_BUOY_MASK) === WP_TWO_BUOYS) {
          // Add 2nd buoy marker
          var _WPMarker = GateFeatures.Buoy2;

          var _Pos = new VLMPosition(WP.longitude2, WP.latitude2);

          GateFeatures.Buoy2 = AddBuoyMarker(_WPMarker, "WP" + _index42 + " " + WP.libelle + '<BR>' + _Pos.toString(), WP.longitude2, WP.latitude2, !cwgate);
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


        _index42 = parseInt(_index42, 10);
        NextGate = parseInt(NextGate, 10);
        AddGateSegment(GateFeatures, WP.longitude1, WP.latitude1, WP.longitude2, WP.latitude2, NextGate === _index42, _index42 < NextGate, WP.wpformat & WP_GATE_KIND_MASK);
      }
    }
  }
}

function DrawRaceExclusionZones(Boat, Zones) {
  if (!Boat) {
    return;
  }

  var Features = GetRaceMapFeatures(Boat);

  for (var _index43 in Zones) {
    if (Zones[_index43]) {
      DrawRaceExclusionZone(Features, Zones, _index43);
    }
  }
}

function DrawRaceExclusionZone(Features, ExclusionZones, ZoneIndex) {
  var PointList = [];
  var HasZones = false;

  for (var _index44 in ExclusionZones[ZoneIndex]) {
    if (ExclusionZones[ZoneIndex][_index44]) {
      var P = [ExclusionZones[ZoneIndex][_index44][0], ExclusionZones[ZoneIndex][_index44][1]];
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
  $.post("/ws/boatsetup/" + verb + ".php?selectidu=" + idu, "parms=" + JSON.stringify(orderdata), function (Data, TextStatus) {
    if (Data.success) {
      RefreshCurrentBoat(false, true);
    } else {
      VLMAlertDanger(GetLocalizedString("BoatSetupError") + '\n' + Data.error.code + " " + Data.error.msg);
    }
  });
}

function EngageBoatInRace(RaceID, BoatID) {
  var RaceObj = new Race(RaceID);
  RaceObj.Subscribe(BoatID);
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
      ResetRankingWPList();

      if (CallBack) {
        CallBack(RaceId);
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
  var index;
  var RaceFeatures = GetRaceMapFeatures(Boat); // Map friend only if selection is active

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
            AddOpponent(Boat, RaceFeatures, _Opp, true);
          }
        }
      }
    }
  } // Get Reals


  if (VLM2Prefs.MapPrefs.ShowReals && typeof Boat.Reals !== "undefined" && typeof Boat.Reals.ranking !== "undefined") for (index in Boat.Reals.ranking) {
    var RealOpp = Boat.Reals.ranking[index];
    AddOpponent(Boat, RaceFeatures, RealOpp, true);
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
            var Pi = new VLMPosition(P.lon, P.lat);
            TrackPoints.push(Pi);
          }

          T.OppTrackPoints = GetSafeTrackPointList(TrackPoints);
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

    for (var _index45 in Rankings[RaceId]) {
      if (Rankings[RaceId][_index45]) {
        if (CurWP === Rankings[RaceId][_index45].nwp) {
          var O = {
            id: _index45,
            dnm: Math.abs(CurDnm - parseFloat(Rankings[RaceId][_index45].dnm))
          };
          List.push(O);
        }
      }
    }

    List = List.sort(CompareDist);

    for (var _index46 in List.slice(0, NbOpps - 1)) {
      RetArray[List[_index46].id] = Rankings[RaceId][List[_index46].id];
    }
  }

  return RetArray;
}

function AddOpponent(Boat, RaceFeatures, Opponent, isFriend) {
  var Opp_Coords = [Opponent.latitude, Opponent.longitude];
  var ZFactor = 8; //map.getZoom();

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
        }

        if (Features.OppPopup.PrevOpp) {
          Features.OppPopup.PrevOpp.unbindPopup();
        }

        Opp.bindPopup(Features.OppPopup);
        Features.OppPopup.setContent(PopupStr);
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
  var RetStr = '<div class="MapPopup_InfoHeader">' + GetCountryFlagImgHTML(Boat.country) + ' <span id="__BoatName' + BoatId + '" class="PopupBoatNameNumber ">BoatName</span>' + ' <span id="__BoatId' + BoatId + '" class="PopupBoatNameNumber ">BoatNumber</span>' + ' <div id="__BoatRank' + BoatId + '" class="TxtRank">Rank</div>' + '</div>' + '<div id="__BoatColor' + BoatId + '" style="height: 2px;"></div>' + '<div class="MapPopup_InfoBody">' + ' <fieldset>' + '   <span class="PopupHeadText " I18n="loch">' + GetLocalizedString('loch') + '</span><span class="PopupText"> : </span><span id="__BoatLoch' + BoatId + '" class="loch PopupText">0.9563544</span>' + '   <BR><span class="PopupHeadText " I18n="position">' + GetLocalizedString('position') + '</span><span class="PopupText"> : </span><span id="__BoatPosition' + BoatId + '" class=" PopupText">0.9563544</span>' + '   <BR><span class="PopupHeadText " I18n="NextWP">' + GetLocalizedString('NextWP') + '</span><span class="strong"> : </span><span id="__BoatNWP' + BoatId + '" class="PopupText">[1] 4.531856536865234</span>' + '   <BR><span class="PopupHeadText " I18n="Moyennes">' + GetLocalizedString('Moyennes') + ' </span><span class="PopupText"> : </span>' + '   <span class="PopupHeadText ">[1h]</span><span id="__Boat1HAvg' + BoatId + '" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span>' + '   <span class="PopupHeadText ">[3h]</span><span id="__Boat3HAvg' + BoatId + '" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span>' + '   <span class="PopupHeadText ">[24h]</span><span id="__Boat24HAvg' + BoatId + '" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span>' + ' </fieldset>' + ' <BR><img class="AddOppWatch" src="images/AddWatch.png">' + '</div>';
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


  for (var _index47 in _CurPlayer.CurBoat.OppTrack) {
    _CurPlayer.CurBoat.OppTrack[_index47].Visible = false;
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
    } //DrawBoat(B);

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


  for (var _index48 in Track) {
    var Pos = Track[_index48];
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
  // Avoid sending invalid stuff to the server
  if (typeof Boat === "undefined" || typeof Boat.IdBoat === "undefined" || typeof NewVals === "undefined") {
    return;
  }

  NewVals.idu = Boat.IdBoat;
  $.post("/ws/boatsetup/prefs_set.php", "parms=" + JSON.stringify(NewVals), function (e) {
    if (e.success) {
      // avoid forced full round trip
      RefreshCurrentBoat(false, false);
    } else {
      VLMAlertDanger("Save Prefs To Server " + GetLocalizedString("UpdateFailed"));
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