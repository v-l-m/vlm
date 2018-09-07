"use strict";

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

var MAP_OP_SHOW_SEL = 0,
    VLM2Prefs = new PrefMgr();

function LoadLocalPref(e, t) {
  var a = store.get(e);
  return void 0 === a && (a = t), a;
}

function PrefMgr() {
  this.MapPrefs = new MapPrefs(), this.CurTheme = "bleu-noir", this.MapPrefs = new MapPrefs(), this.Init = function () {
    this.MapPrefs.Load(), this.Load();
  }, this.Load = function () {
    store.enabled && (this.CurTheme = LoadLocalPref("CurTheme", "bleu-noir"));
  }, this.Save = function () {
    store.enabled && store.set("ColorTheme", this.CurTheme), this.MapPrefs.Save();
  }, this.UpdateVLMPrefs = function (e) {
    switch (e.mapOpponents) {
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
      case "maponlyme":
        this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.Show5Around;
        break;

      case "myboat":
        this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.ShowMineOnly;
        break;

      default:
        VLMAlertDanger("unexepected mapping option : " + e.mapOpponents);
    }
  };
}

function MapPrefs() {
  this.ShowReals = !0, this.ShowOppNames = !0, this.MapOppShow = null, this.MapOppShowOptions = {
    ShowSel: 0,
    ShowMineOnly: 1,
    Show5Around: 2,
    ShowTop10: 3,
    Show10Around: 4
  }, this.WindArrowsSpacing = 64, this.MapZoomLevel = 4, this.PolarVacCount = 12, this.UseUTC = !1, this.EstTrackMouse = !1, this.TrackEstForecast = !0, this.ShowTopCount = 50, this.Load = function () {
    store.enabled && (this.ShowReals = LoadLocalPref("#ShowReals", !0), this.ShowOppNames = LoadLocalPref("#ShowOppNames", !1), this.MapZoomLevel = LoadLocalPref("#MapZoomLevel", 4), this.UseUTC = LoadLocalPref("#UseUTC", !1), this.EstTrackMouse = LoadLocalPref("#EstTrackMouse", !0), this.TrackEstForecast = LoadLocalPref("#TrackEstForecast", !1), this.PolarVacCount = LoadLocalPref("#PolarVacCount", 12), this.PolarVacCount || (this.PolarVacCount = 12), this.ShowTopCount = LoadLocalPref("ShowTopCount", 50));
  }, this.Save = function () {
    store.enabled && (store.set("#ShowReals", this.ShowReals), store.set("#ShowOppNames", this.ShowOppName), store.set("#MapZoomLevel", this.MapZoomLevel), store.set("#PolarVacCount", this.PolarVacCount), store.set("#UseUTC", this.UseUTC), store.set("#TrackEstForecast", this.TrackEstForecast), store.set("#EstTrackMouse", this.EstTrackMouse), store.set("ShowTopCount", this.ShowTopCount));
    var e = "mapselboats";

    switch (this.MapOppShow) {
      case this.MapOppShowOptions.ShowMineOnly:
        e = "myboat";
        break;

      case this.MapOppShowOptions.Show5Around:
        e = "my5opps";
        break;

      case this.MapOppShowOptions.ShowTop10:
        e = "meandtop10";
        break;

      case this.MapOppShowOptions.Show10Around:
        e = "my10opps";
    }

    var t = {
      mapOpponents: e
    };
    void 0 !== _CurPlayer && _CurPlayer && UpdateBoatPrefs(_CurPlayer.CurBoat, {
      prefs: t
    });
  }, this.GetOppModeString = function (e) {
    switch (e) {
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
        return e;
    }
  };
}

function AutoPilotOrder(e, t) {
  if (this.Date = new Date(new Date().getTime() - new Date().getTime() % 3e5 + 45e4), this.PIM = PM_HEADING, this.PIP_Value = 0, this.PIP_Coords = new VLMPosition(0, 0), this.PIP_WPAngle = -1, this.ID = -1, void 0 !== e && e) {
    if (!(t - 1 in e.VLMInfo.PIL)) return void alert("Invalid Pilototo order number. Report error to devs.");
    var a = e.VLMInfo.PIL[t - 1];

    switch (this.Date = new Date(1e3 * parseInt(a.TTS, 10)), this.PIM = parseInt(a.PIM, 10), this.ID = parseInt(a.TID, 10), this.PIM) {
      case PM_ANGLE:
      case PM_HEADING:
        this.PIP_Value = parseInt(a.PIP, 10);
        break;

      case PM_ORTHO:
      case PM_VMG:
      case PM_VBVMG:
        var i = a.PIP.split(","),
            n = i[1].split("@");
        this.PIP_Coords.Lat.Value = parseFloat(i[0]), this.PIP_Coords.Lon.Value = parseFloat(n[0]), this.PIP_WPAngle = parseFloat(n[1]);
    }
  }

  this.GetOrderDateString = function () {
    return this.Date.getDate() + "/" + (this.Date.getMonth() + 1) + "/" + this.Date.getFullYear();
  }, this.GetOrderTimeString = function () {
    return this.Date.getHours() + ":" + this.Date.getMinutes() + ":15";
  }, this.GetPIMString = function () {
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
  }, this.GetPIPString = function () {
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
  var t = "add";

  if (void 0 !== _CurAPOrder && _CurAPOrder) {
    var a = {
      idu: _CurPlayer.CurBoat.IdBoat,
      tasktime: Math.round(_CurAPOrder.Date / 1e3),
      pim: _CurAPOrder.PIM
    };

    switch (-1 !== _CurAPOrder.ID && (t = "update", a.taskid = _CurAPOrder.ID), _CurAPOrder.PIM) {
      case PM_HEADING:
      case PM_ANGLE:
        a.pip = _CurAPOrder.PIP_Value;
        break;

      case PM_ORTHO:
      case PM_VMG:
      case PM_VBVMG:
        a.pip = {}, a.pip.targetlat = _CurAPOrder.PIP_Coords.Lat.Value, a.pip.targetlong = _CurAPOrder.PIP_Coords.Lon.Value, a.pip.targetandhdg = -1 === _CurAPOrder.PIP_WPAngle ? null : _CurAPOrder.PIP_WPAngle;
    }

    $.post("/ws/boatsetup/pilototo_" + t + ".php", "parms=" + JSON.stringify(a), function (e) {
      e.success ? RefreshCurrentBoat(!1, !0, "AutoPilot") : alert(e.error.msg);
    });
  }
}

function HandleAPFieldChange(e) {
  var t = e.target;
  if (void 0 !== t.attributes.id) switch (t.attributes.id.value) {
    case "AP_PIP":
      _CurAPOrder.PIP_Value = parseFloat(t.value), _CurAPOrder.PIP_Value.toString() !== t.Value && (t.value = _CurAPOrder.PIP_Value.toString());
      break;

    case "AP_WPLat":
      CheckFloatInput(_CurAPOrder.PIP_Coords.Lat, t);
      break;

    case "AP_WPLon":
      CheckFloatInput(_CurAPOrder.PIP_Coords.Lon, t);
      break;

    case "AP_WPAt":
      var a = {};
      a.Value = _CurAPOrder.PIP_WPAngle, CheckFloatInput(a, t), _CurAPOrder.PIP_WPAngle = a.Value;
  }
}

function CheckFloatInput(e, t) {
  var a;
  "object" == _typeof(e) ? (e.Value = parseFloat(t.value), a = e.Value) : a = e = parseFloat(t.value), a.toString() !== t.Value && (t.value = a.toString());
}

function BoatEstimate(e) {
  this.Position = null, this.Date = null, this.PrevDate = null, this.Mode = null, this.Value = null, this.Meteo = null, this.CurWP = new VLMPosition(0, 0), this.HdgAtWP = -1, this.RaceWP = 1, this.Heading = null, void 0 !== e && e && (this.Position = new VLMPosition(e.Position.Lon.Value, e.Position.Lat.Value), this.Date = new Date(e.Date), this.PrevDate = new Date(e.PrevDate), this.Mode = e.Mode, this.Value = e.Value, void 0 !== e.Meteo && e.Meteo && (this.Meteo = new WindData({
    Speed: e.Meteo.Speed,
    Heading: e.Meteo.Heading
  })), this.CurWP = e.CurWP, this.RaceWP = e.RaceWP, this.Heading = e.Heading);
}

function Estimator(e) {
  if (void 0 === e || !e) throw "Boat must exist for tracking....";
  this.Boat = e, this.MaxVacEstimate = 0, this.CurEstimate = new BoatEstimate(), this.Running = !1, this.EstimateTrack = [], this.EstimatePoints = [], this.ProgressCallBack = null, this.ErrorCount = 0, this.EstimateMapFeatures = [], this.Stop = function () {
    this.Running && (this.Running = !1, this.ReportProgress(!0), DrawBoat(this.Boat));
  }, this.Start = function (e) {
    if (this.ProgressCallBack = e, !this.Running) if (this.Running = !0, GribMgr.Init(), void 0 !== this.Boat.VLMInfo) {
      if (this.CurEstimate.Position = new VLMPosition(this.Boat.VLMInfo.LON, this.Boat.VLMInfo.LAT), this.CurEstimate.PrevDate = new Date(1e3 * this.Boat.VLMInfo.LUP), this.CurEstimate.Date = new Date(1e3 * this.Boat.VLMInfo.LUP + 1e3 * this.Boat.VLMInfo.VAC), this.CurEstimate.Date < new Date()) if (void 0 === this.Boat.RaceInfo) this.CurEstimate.Date = new Date();else {
        var _e = new Date(1e3 * parseInt(this.Boat.RaceInfo.deptime, 10) + 1e3 * this.Boat.VLMInfo.VAC + 6e3);

        this.CurEstimate.Date = _e, this.CurEstimate.PrevDate = new Date(1e3 * parseInt(this.Boat.RaceInfo.deptime, 10));
      }
      this.CurEstimate.Mode = parseInt(this.Boat.VLMInfo.PIM, 10), this.CurEstimate.CurWP = new VLMPosition(this.Boat.VLMInfo.WPLON, this.Boat.VLMInfo.WPLAT), this.CurEstimate.HdgAtWP = parseFloat(this.Boat.VLMInfo["H@WP"]), this.CurEstimate.RaceWP = parseInt(this.Boat.VLMInfo.NWP, 10), this.CurEstimate.Mode != PM_HEADING && this.CurEstimate.Mode != PM_ANGLE || (this.CurEstimate.Value = parseFloat(this.Boat.VLMInfo.PIP)), this.CurEstimate.PilOrders = [];

      for (var _e2 in this.Boat.VLMInfo.PIL) {
        var t = this.Boat.VLMInfo.PIL[_e2],
            a = {
          PIP: t.PIP,
          PIM: t.PIM,
          STS: t.STS,
          TTS: t.TTS
        };
        this.CurEstimate.PilOrders.push(a);
      }

      this.EstimateTrack = [], this.EstimatePoints = [], this.MaxVacEstimate = new Date(GribMgr.MaxWindStamp), this.ReportProgress(!1), this.EstimateTrack.push(new BoatEstimate(this.CurEstimate)), this.ErrorCount = 0, setTimeout(this.Estimate.bind(this), 0);
    } else this.Stop();
  }, this.Estimate = function (e) {
    if (!this.Running || this.CurEstimate.Date >= this.MaxVacEstimate) return void this.Stop();
    var t;

    do {
      if (!(t = GribMgr.WindAtPointInTime(this.CurEstimate.PrevDate, this.CurEstimate.Position.Lat.Value, this.CurEstimate.Position.Lon.Value))) return this.ErrorCount > 10 ? void this.Stop() : (this.ErrorCount++, void setTimeout(this.Estimate.bind(this), 1e3));

      if (this.ErrorCount = 0, isNaN(t.Speed)) {
        alert("Looping on NaN WindSpeed");
      }
    } while (isNaN(t.Speed));

    this.CurEstimate.Meteo = t;

    for (var _e3 in this.CurEstimate.PilOrders) {
      var a = this.CurEstimate.PilOrders[_e3];
      if (a && "pending" === a.STS) if (new Date(1e3 * parseInt(a.TTS, 10)) <= this.CurEstimate.Date) {
        switch (this.CurEstimate.Mode = parseInt(a.PIM, 10), this.CurEstimate.Mode) {
          case PM_ANGLE:
          case PM_HEADING:
            this.CurEstimate.Value = parseFloat(a.PIP);
            break;

          case PM_ORTHO:
          case PM_VMG:
          case PM_VBVMG:
            var _e4 = a.PIP.split("@"),
                _t = _e4[0].split(",");

            this.CurEstimate.CurWP = new VLMPosition(parseFloat(_t[1]), parseFloat(_t[0])), this.CurEstimate.HdgAtWP = parseFloat(_e4[1]);
            break;

          default:
            return alert("unsupported pilototo mode"), void this.Stop();
        }

        this.CurEstimate.PilOrders[_e3] = null;
        break;
      }
    }

    var i = this.CurEstimate.Value,
        n = 0,
        o = null,
        r = null;

    switch (this.CurEstimate.Mode) {
      case PM_ANGLE:
        i = t.Heading + this.CurEstimate.Value, n = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, t.Speed, t.Heading, i), o = this.CurEstimate.Position.ReachDistLoxo(n / 3600 * this.Boat.VLMInfo.VAC, i);
        break;

      case PM_HEADING:
        n = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, t.Speed, t.Heading, i), o = this.CurEstimate.Position.ReachDistLoxo(n / 3600 * this.Boat.VLMInfo.VAC, i);
        break;

      case PM_ORTHO:
      case PM_VMG:
      case PM_VBVMG:
        r = this.GetNextWPCoords(this.CurEstimate), this.CurEstimate.Mode == PM_ORTHO ? (i = this.CurEstimate.Position.GetOrthoCourse(r), n = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, t.Speed, t.Heading, i), o = this.CurEstimate.Position.ReachDistOrtho(n / 3600 * this.Boat.VLMInfo.VAC, i)) : (i = this.CurEstimate.Mode == PM_VMG ? PolarsManager.GetVMGCourse(this.Boat.VLMInfo.POL, t.Speed, t.Heading, this.CurEstimate.Position, r) : PolarsManager.GetVBVMGCourse(this.Boat.VLMInfo.POL, t.Speed, t.Heading, this.CurEstimate.Position, r), n = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, t.Speed, t.Heading, i), o = this.CurEstimate.Position.ReachDistLoxo(n / 3600 * this.Boat.VLMInfo.VAC, i)), this.CheckWPReached(r, this.CurEstimate.Position, o);
        break;

      default:
        throw "Unsupported pilotmode for estimate..." + this.CurEstimate.Mode;
    }

    console.log(this.CurEstimate.Date + this.CurEstimate.Position.ToString(!0) + "=> " + o.Lon.ToString(!0) + " " + o.Lat.ToString(!0) + " Wind : " + RoundPow(t.Speed, 4) + "@" + RoundPow(t.Heading, 4) + " Boat " + RoundPow(n, 4) + "kts" + RoundPow((i + 360) % 360, 4));
    var s = !1;
    this.CheckGateValidation(o) && (s = this.GetNextRaceWP()), this.CurEstimate.Heading = i, this.CurEstimate.Position = o, this.EstimateTrack.push(new BoatEstimate(this.CurEstimate)), this.CurEstimate.PrevDate = this.CurEstimate.Date, this.CurEstimate.Date = new Date(1e3 * (this.CurEstimate.Date / 1e3 + this.Boat.VLMInfo.VAC)), s ? this.Stop() : (setTimeout(this.Estimate.bind(this), 0), this.ReportProgress(!1));
  }, this.GetNextRaceWP = function () {
    var e = Object.keys(this.Boat.RaceInfo.races_waypoints).length;
    if (this.CurEstimate.RaceWP === e) return !0;

    for (var t = this.CurEstimate.RaceWP + 1; t <= e; t++) {
      if (!(this.Boat.RaceInfo.races_waypoints[t].wpformat & WP_ICE_GATE)) {
        this.CurEstimate.RaceWP = t;
        break;
      }
    }

    return !1;
  }, this.CheckGateValidation = function (e) {
    var t = this.GetNextGateSegment(this.CurEstimate),
        a = (this.Boat.RaceInfo.races_waypoints[this.CurEstimate.RaceWP], {
      P1: this.CurEstimate.Position,
      P2: e
    });
    return VLMMercatorTransform.SegmentsIntersect(t, a);
  }, this.CheckWPReached = function (e, t, a) {
    if (!this.CurEstimate.CurWP.Lat.value && !this.CurEstimate.CurWP.Lon.Value) return;
    var i = e.GetOrthoDist(t),
        n = e.GetOrthoDist(a),
        o = t.GetOrthoDist(a);
    (i < o || n < o) && (this.CurEstimate.CurWP = new VLMPosition(0, 0), -1 != this.CurEstimate.HdgAtWP && (this.CurEstimate.Mode = PM_HEADING, this.CurEstimate.Value = this.CurEstimate.HdgAtWP), console.log("WP Reached"));
  }, this.GetNextWPCoords = function (e) {
    return e.CurWP.Lat.value || e.CurWP.Lon.Value ? e.CurWP : this.Boat.GetNextWPPosition(e.RaceWP, e.Position, e.CurWP);
  }, this.GetNextGateSegment = function (e) {
    return this.Boat.GetNextGateSegment(e.RaceWP);
  }, this.ReportProgress = function (e) {
    var t = 0;
    this.ProgressCallBack && (e || this.EstimateTrack.length > 1 && (t = RoundPow(100 * (1 - (t = (this.MaxVacEstimate - this.EstimateTrack[this.EstimateTrack.length - 1].Date) / (this.MaxVacEstimate - this.EstimateTrack[0].Date))), 1)), this.ProgressCallBack(e, t, this.CurEstimate.Date));
  }, this.GetClosestEstimatePoint = function (e) {
    return e instanceof VLMPosition ? this.GetClosestEstimatePointFromPosition(e) : e instanceof Date ? this.GetClosestEstimatePointFromTime(e) : null;
  }, this.GetClosestEstimatePointFromTime = function (e) {
    if (!e || !Object.keys(this.EstimateTrack).length) return null;
    var t,
        a = 0;

    for (a = 0; a < Object.keys(this.EstimateTrack).length; a++) {
      if (this.EstimateTrack[a]) {
        if (!(e > this.EstimateTrack[a].Date)) break;
        t = e - this.EstimateTrack[a].Date;
      }
    }

    if (a < Object.keys(this.EstimateTrack).length) {
      var i = e - this.EstimateTrack[a + 1].Date;
      Math.abs(i) < Math.abs(t) && a++;
    }

    return RetValue = this.EstimateTrack[a], RetValue;
  }, this.GetClosestEstimatePointFromPosition = function (e) {
    if (!e) return null;
    var t,
        a = 1e30,
        i = null;

    for (t = 0; t < Object.keys(this.EstimateTrack).length; t++) {
      if (this.EstimateTrack[t]) {
        var n = e.GetEuclidianDist2(this.EstimateTrack[t].Position);
        n < a && (i = this.EstimateTrack[t], a = n);
      }
    }

    return i;
  }, this.ClearEstimatePosition = function (e) {
    this.ShowEstimatePosition(e, null);
  }, this.ShowEstimatePosition = function (e, t) {
    if (this.EstimateMapFeatures) {
      for (var _e5 in this.EstimateMapFeatures) {
        this.EstimateMapFeatures[_e5] && VLMBoatsLayer.removeFeatures(this.EstimateMapFeatures);
      }

      this.EstimateMapFeatures = [];
    }

    if (t && t.Position && e.VLMInfo.LON !== t.Position.Lon.Value && e.VLMInfo.LAT !== t.Position.Lat.Value) {
      var r = t.Position,
          s = new OpenLayers.Geometry.Point(r.Lon.Value, r.Lat.Value).transform(MapOptions.displayProjection, MapOptions.projection);
      var a = new OpenLayers.Feature.Vector(s, {}, {
        externalGraphic: "images/target.svg",
        opacity: .8,
        graphicHeight: 48,
        graphicWidth: 48,
        rotation: t.Heading
      });

      if (VLMBoatsLayer.addFeatures(a), this.EstimateMapFeatures.push(a), void 0 !== t.Meteo) {
        var i = VLM2Prefs.MapPrefs.PolarVacCount,
            n = [];
        BuildPolarLine(e, PolarsManager.GetPolarLine(e.VLMInfo.POL, t.Meteo.Speed, DrawBoat, e), n, r, i, t.Date);
        var o = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.LineString(n), {
          type: "Polar",
          WindDir: t.Meteo.Heading
        });
        this.EstimateMapFeatures.push(o), VLMBoatsLayer.addFeatures(o);
      }
    }
  };
}

function Coords(e, t) {
  this.Value = "number" == typeof e ? e : parseFloat(e), this.IsLon = t, this.Deg = function () {
    return Math.abs(this.Value);
  }, this.Min = function () {
    return 60 * (Math.abs(this.Value) - Math.floor(this.Deg()));
  }, this.Sec = function () {
    return 60 * (this.Min() - Math.floor(this.Min()));
  }, this.ToString = function (e) {
    if (e) return this.Value;
    {
      var _e6 = "";
      return _e6 = void 0 === this.IsLon || 0 == this.IsLon ? this.Value >= 0 ? " N" : " S" : this.Value >= 0 ? " E" : " W", Math.floor(this.Deg()) + "° " + Math.floor(this.Min()) + "' " + Math.floor(this.Sec()) + '"' + _e6;
    }
  };
}

function GetDegMinSecFromNumber(e, t, a, i) {
  SplitNumber(e, t, void 0), SplitNumber(NaN, a, void 0), SplitNumber(NaN, i, void 0);
}

function SplitNumber(e, t, a) {
  Math.floor(e);
}

VLM2Prefs.Init(), OpenLayers.Control.ControlSwitch = OpenLayers.Class(OpenLayers.Control, {
  label: "controlswitch",
  roundedCorner: !0,
  roundedCornerColor: "darkblue",
  baseDiv: null,
  minimizeDiv: null,
  maximizeDiv: null,
  initialize: function initialize(e) {
    OpenLayers.Control.prototype.initialize.apply(this, arguments);
  },
  destroy: function destroy() {
    OpenLayers.Event.stopObservingElement(this.div), OpenLayers.Event.stopObservingElement(this.minimizeDiv), OpenLayers.Event.stopObservingElement(this.maximizeDiv), OpenLayers.Control.prototype.destroy.apply(this, arguments);
  },
  draw: function draw() {
    return OpenLayers.Control.prototype.draw.apply(this), this.loadContents(), this.outsideViewport || this.minimizeControl(), this.redraw(), this.div;
  },
  redraw: function redraw() {
    return this.baseDiv.innerHTML = "", this.drawBaseDiv(), this.div;
  },
  drawBaseDiv: function drawBaseDiv() {
    this.baseDiv.innerHTML = "Base Class, Control Switch";
  },
  maximizeControl: function maximizeControl(e) {
    this.div.style.width = "", this.div.style.height = "", this.showControls(!1), null != e && OpenLayers.Event.stop(e);
  },
  minimizeControl: function minimizeControl(e) {
    this.div.style.width = "0px", this.div.style.height = "0px", this.showControls(!0), null != e && OpenLayers.Event.stop(e);
  },
  showControls: function showControls(e) {
    this.maximizeDiv.style.display = e ? "" : "none", this.minimizeDiv.style.display = e ? "none" : "", this.baseDiv.style.display = e ? "none" : "";
  },
  loadContents: function loadContents() {
    OpenLayers.Event.observe(this.div, "mouseup", OpenLayers.Function.bindAsEventListener(this.mouseUp, this)), OpenLayers.Event.observe(this.div, "click", this.ignoreEvent), OpenLayers.Event.observe(this.div, "mousedown", OpenLayers.Function.bindAsEventListener(this.mouseDown, this)), OpenLayers.Event.observe(this.div, "dblclick", this.ignoreEvent), this.baseDiv = document.createElement("div"), this.baseDiv.id = this.id + "_baseDiv", OpenLayers.Element.addClass(this.baseDiv, "baseDiv"), this.div.appendChild(this.baseDiv), this.roundedCorner && (OpenLayers.Rico.Corner.round(this.div, {
      corners: "tl bl",
      bgColor: "transparent",
      color: this.roundedCornerColor,
      blend: !1
    }), OpenLayers.Rico.Corner.changeOpacity(this.baseDiv, .75));
    var e = OpenLayers.Util.getImagesLocation(),
        t = new OpenLayers.Size(18, 18),
        a = e + "layer-switcher-maximize.png";
    this.maximizeDiv = OpenLayers.Util.createAlphaImageDiv("OpenLayers_Control_MaximizeDiv", null, t, a, "absolute"), OpenLayers.Element.addClass(this.maximizeDiv, "maximizeDiv"), this.maximizeDiv.style.display = "none", OpenLayers.Event.observe(this.maximizeDiv, "click", OpenLayers.Function.bindAsEventListener(this.maximizeControl, this)), this.div.appendChild(this.maximizeDiv), a = e + "layer-switcher-minimize.png", t = new OpenLayers.Size(18, 18), this.minimizeDiv = OpenLayers.Util.createAlphaImageDiv("OpenLayers_Control_MinimizeDiv", null, t, a, "absolute"), OpenLayers.Element.addClass(this.minimizeDiv, "minimizeDiv"), this.minimizeDiv.style.display = "none", OpenLayers.Event.observe(this.minimizeDiv, "click", OpenLayers.Function.bindAsEventListener(this.minimizeControl, this)), this.div.appendChild(this.minimizeDiv);
  },
  ignoreEvent: function ignoreEvent(e) {
    OpenLayers.Event.stop(e);
  },
  mouseDown: function mouseDown(e) {
    this.isMouseDown = !0, this.ignoreEvent(e);
  },
  mouseUp: function mouseUp(e) {
    this.isMouseDown && (this.isMouseDown = !1, this.ignoreEvent(e));
  },
  CLASS_NAME: "OpenLayers.Control.ControlSwitch"
}), document.createElement("canvas").getContext || function () {
  var e = Math,
      t = e.round,
      a = e.sin,
      i = e.cos,
      n = e.abs,
      o = e.sqrt,
      r = 10,
      s = r / 2;
  navigator.userAgent.match(/MSIE ([\d.]+)?/)[1];

  function l() {
    return this.context_ || (this.context_ = new T(this));
  }

  var d = Array.prototype.slice;

  function u(e) {
    return String(e).replace(/&/g, "&amp;").replace(/"/g, "&quot;");
  }

  function c(e, t, a) {
    e.namespaces[t] || e.namespaces.add(t, a, "#default#VML");
  }

  function h(e) {
    if (c(e, "g_vml_", "urn:schemas-microsoft-com:vml"), c(e, "g_o_", "urn:schemas-microsoft-com:office:office"), !e.styleSheets.ex_canvas_) {
      var t = e.createStyleSheet();
      t.owningElement.id = "ex_canvas_", t.cssText = "canvas{display:inline-block;overflow:hidden;text-align:left;width:300px;height:150px}";
    }
  }

  h(document);
  var p = {
    init: function init(e) {
      var t = e || document;
      t.createElement("canvas"), t.attachEvent("onreadystatechange", function (e, t, a) {
        var i = d.call(arguments, 2);
        return function () {
          return e.apply(t, i.concat(d.call(arguments)));
        };
      }(this.init_, this, t));
    },
    init_: function init_(e) {
      for (var t = e.getElementsByTagName("canvas"), a = 0; a < t.length; a++) {
        this.initElement(t[a]);
      }
    },
    initElement: function initElement(e) {
      if (!e.getContext) {
        e.getContext = l, h(e.ownerDocument), e.innerHTML = "", e.attachEvent("onpropertychange", f), e.attachEvent("onresize", P);
        var t = e.attributes;
        t.width && t.width.specified ? e.style.width = t.width.nodeValue + "px" : e.width = e.clientWidth, t.height && t.height.specified ? e.style.height = t.height.nodeValue + "px" : e.height = e.clientHeight;
      }

      return e;
    }
  };

  function f(e) {
    var t = e.srcElement;

    switch (e.propertyName) {
      case "width":
        t.getContext().clearRect(), t.style.width = t.attributes.width.nodeValue + "px", t.firstChild.style.width = t.clientWidth + "px";
        break;

      case "height":
        t.getContext().clearRect(), t.style.height = t.attributes.height.nodeValue + "px", t.firstChild.style.height = t.clientHeight + "px";
    }
  }

  function P(e) {
    var t = e.srcElement;
    t.firstChild && (t.firstChild.style.width = t.clientWidth + "px", t.firstChild.style.height = t.clientHeight + "px");
  }

  p.init();

  for (var g = [], L = 0; L < 16; L++) {
    for (var m = 0; m < 16; m++) {
      g[16 * L + m] = L.toString(16) + m.toString(16);
    }
  }

  function M(e, t) {
    for (var a = [[1, 0, 0], [0, 1, 0], [0, 0, 1]], i = 0; i < 3; i++) {
      for (var n = 0; n < 3; n++) {
        for (var o = 0, r = 0; r < 3; r++) {
          o += e[i][r] * t[r][n];
        }

        a[i][n] = o;
      }
    }

    return a;
  }

  function C(e, t) {
    t.fillStyle = e.fillStyle, t.lineCap = e.lineCap, t.lineJoin = e.lineJoin, t.lineWidth = e.lineWidth, t.miterLimit = e.miterLimit, t.shadowBlur = e.shadowBlur, t.shadowColor = e.shadowColor, t.shadowOffsetX = e.shadowOffsetX, t.shadowOffsetY = e.shadowOffsetY, t.strokeStyle = e.strokeStyle, t.globalAlpha = e.globalAlpha, t.font = e.font, t.textAlign = e.textAlign, t.textBaseline = e.textBaseline, t.arcScaleX_ = e.arcScaleX_, t.arcScaleY_ = e.arcScaleY_, t.lineScale_ = e.lineScale_;
  }

  var y = {
    aliceblue: "#F0F8FF",
    antiquewhite: "#FAEBD7",
    aquamarine: "#7FFFD4",
    azure: "#F0FFFF",
    beige: "#F5F5DC",
    bisque: "#FFE4C4",
    black: "#000000",
    blanchedalmond: "#FFEBCD",
    blueviolet: "#8A2BE2",
    brown: "#A52A2A",
    burlywood: "#DEB887",
    cadetblue: "#5F9EA0",
    chartreuse: "#7FFF00",
    chocolate: "#D2691E",
    coral: "#FF7F50",
    cornflowerblue: "#6495ED",
    cornsilk: "#FFF8DC",
    crimson: "#DC143C",
    cyan: "#00FFFF",
    darkblue: "#00008B",
    darkcyan: "#008B8B",
    darkgoldenrod: "#B8860B",
    darkgray: "#A9A9A9",
    darkgreen: "#006400",
    darkgrey: "#A9A9A9",
    darkkhaki: "#BDB76B",
    darkmagenta: "#8B008B",
    darkolivegreen: "#556B2F",
    darkorange: "#FF8C00",
    darkorchid: "#9932CC",
    darkred: "#8B0000",
    darksalmon: "#E9967A",
    darkseagreen: "#8FBC8F",
    darkslateblue: "#483D8B",
    darkslategray: "#2F4F4F",
    darkslategrey: "#2F4F4F",
    darkturquoise: "#00CED1",
    darkviolet: "#9400D3",
    deeppink: "#FF1493",
    deepskyblue: "#00BFFF",
    dimgray: "#696969",
    dimgrey: "#696969",
    dodgerblue: "#1E90FF",
    firebrick: "#B22222",
    floralwhite: "#FFFAF0",
    forestgreen: "#228B22",
    gainsboro: "#DCDCDC",
    ghostwhite: "#F8F8FF",
    gold: "#FFD700",
    goldenrod: "#DAA520",
    grey: "#808080",
    greenyellow: "#ADFF2F",
    honeydew: "#F0FFF0",
    hotpink: "#FF69B4",
    indianred: "#CD5C5C",
    indigo: "#4B0082",
    ivory: "#FFFFF0",
    khaki: "#F0E68C",
    lavender: "#E6E6FA",
    lavenderblush: "#FFF0F5",
    lawngreen: "#7CFC00",
    lemonchiffon: "#FFFACD",
    lightblue: "#ADD8E6",
    lightcoral: "#F08080",
    lightcyan: "#E0FFFF",
    lightgoldenrodyellow: "#FAFAD2",
    lightgreen: "#90EE90",
    lightgrey: "#D3D3D3",
    lightpink: "#FFB6C1",
    lightsalmon: "#FFA07A",
    lightseagreen: "#20B2AA",
    lightskyblue: "#87CEFA",
    lightslategray: "#778899",
    lightslategrey: "#778899",
    lightsteelblue: "#B0C4DE",
    lightyellow: "#FFFFE0",
    limegreen: "#32CD32",
    linen: "#FAF0E6",
    magenta: "#FF00FF",
    mediumaquamarine: "#66CDAA",
    mediumblue: "#0000CD",
    mediumorchid: "#BA55D3",
    mediumpurple: "#9370DB",
    mediumseagreen: "#3CB371",
    mediumslateblue: "#7B68EE",
    mediumspringgreen: "#00FA9A",
    mediumturquoise: "#48D1CC",
    mediumvioletred: "#C71585",
    midnightblue: "#191970",
    mintcream: "#F5FFFA",
    mistyrose: "#FFE4E1",
    moccasin: "#FFE4B5",
    navajowhite: "#FFDEAD",
    oldlace: "#FDF5E6",
    olivedrab: "#6B8E23",
    orange: "#FFA500",
    orangered: "#FF4500",
    orchid: "#DA70D6",
    palegoldenrod: "#EEE8AA",
    palegreen: "#98FB98",
    paleturquoise: "#AFEEEE",
    palevioletred: "#DB7093",
    papayawhip: "#FFEFD5",
    peachpuff: "#FFDAB9",
    peru: "#CD853F",
    pink: "#FFC0CB",
    plum: "#DDA0DD",
    powderblue: "#B0E0E6",
    rosybrown: "#BC8F8F",
    royalblue: "#4169E1",
    saddlebrown: "#8B4513",
    salmon: "#FA8072",
    sandybrown: "#F4A460",
    seagreen: "#2E8B57",
    seashell: "#FFF5EE",
    sienna: "#A0522D",
    skyblue: "#87CEEB",
    slateblue: "#6A5ACD",
    slategray: "#708090",
    slategrey: "#708090",
    snow: "#FFFAFA",
    springgreen: "#00FF7F",
    steelblue: "#4682B4",
    tan: "#D2B48C",
    thistle: "#D8BFD8",
    tomato: "#FF6347",
    turquoise: "#40E0D0",
    violet: "#EE82EE",
    wheat: "#F5DEB3",
    whitesmoke: "#F5F5F5",
    yellowgreen: "#9ACD32"
  };

  function w(e) {
    var t = e.indexOf("(", 3),
        a = e.indexOf(")", t + 1),
        i = e.substring(t + 1, a).split(",");
    return 4 == i.length && "a" == e.charAt(3) || (i[3] = 1), i;
  }

  function v(e) {
    return parseFloat(e) / 100;
  }

  function _(e, t, a) {
    return Math.min(a, Math.max(t, e));
  }

  function I(e, t, a) {
    return a < 0 && a++, a > 1 && a--, 6 * a < 1 ? e + 6 * (t - e) * a : 2 * a < 1 ? t : 3 * a < 2 ? e + (t - e) * (2 / 3 - a) * 6 : e;
  }

  var S = {};

  function R(e) {
    if (e in S) return S[e];
    var t,
        a = 1;
    if ("#" == (e = String(e)).charAt(0)) t = e;else if (/^rgb/.test(e)) {
      var i = w(e);

      var _t2,
          _o = "#";

      for (var n = 0; n < 3; n++) {
        _t2 = -1 != i[n].indexOf("%") ? Math.floor(255 * v(i[n])) : +i[n], _o += g[_(_t2, 0, 255)];
      }

      a = +i[3];
    } else if (/^hsl/.test(e)) {
      var _i = w(e);

      t = function (e) {
        var t, a, i, n, o, r;
        if ((n = parseFloat(e[0]) / 360 % 360) < 0 && n++, o = _(v(e[1]), 0, 1), r = _(v(e[2]), 0, 1), 0 == o) t = a = i = r;else {
          var s = r < .5 ? r * (1 + o) : r + o - r * o,
              l = 2 * r - s;
          t = I(l, s, n + 1 / 3), a = I(l, s, n), i = I(l, s, n - 1 / 3);
        }
        return "#" + g[Math.floor(255 * t)] + g[Math.floor(255 * a)] + g[Math.floor(255 * i)];
      }(_i), a = _i[3];
    } else t = y[e] || e;
    return S[e] = {
      color: t,
      alpha: a
    }, S[e];
  }

  var b = {
    style: "normal",
    variant: "normal",
    weight: "normal",
    size: 10,
    family: "sans-serif"
  },
      D = {};
  var k = {
    butt: "flat",
    round: "round"
  };

  function T(e) {
    this.m_ = [[1, 0, 0], [0, 1, 0], [0, 0, 1]], this.mStack_ = [], this.aStack_ = [], this.currentPath_ = [], this.strokeStyle = "#000", this.fillStyle = "#000", this.lineWidth = 1, this.lineJoin = "miter", this.lineCap = "butt", this.miterLimit = 1 * r, this.globalAlpha = 1, this.font = "10px sans-serif", this.textAlign = "left", this.textBaseline = "alphabetic", this.canvas = e;
    var t = "width:" + e.clientWidth + "px;height:" + e.clientHeight + "px;overflow:hidden;position:absolute",
        a = e.ownerDocument.createElement("div");
    a.style.cssText = t, e.appendChild(a);
    var i = a.cloneNode(!1);
    i.style.backgroundColor = "red", i.style.filter = "alpha(opacity=0)", e.appendChild(i), this.element_ = a, this.arcScaleX_ = 1, this.arcScaleY_ = 1, this.lineScale_ = 1;
  }

  var O = T.prototype;

  function A(e, t, a, i) {
    e.currentPath_.push({
      type: "bezierCurveTo",
      cp1x: t.x,
      cp1y: t.y,
      cp2x: a.x,
      cp2y: a.y,
      x: i.x,
      y: i.y
    }), e.currentX_ = i.x, e.currentY_ = i.y;
  }

  function E(e, t) {
    var a,
        i = R(e.strokeStyle),
        n = i.color,
        o = i.alpha * e.globalAlpha,
        r = e.lineScale_ * e.lineWidth;
    r < 1 && (o *= r), t.push("<g_vml_:stroke", ' opacity="', o, '"', ' joinstyle="', e.lineJoin, '"', ' miterlimit="', e.miterLimit, '"', ' endcap="', (a = e.lineCap, k[a] || "square"), '"', ' weight="', r, 'px"', ' color="', n, '" />');
  }

  function G(t, a, i, n) {
    var o = t.fillStyle,
        s = t.arcScaleX_,
        l = t.arcScaleY_,
        d = n.x - i.x,
        u = n.y - i.y;

    if (o instanceof F) {
      var c = 0,
          h = {
        x: 0,
        y: 0
      },
          p = 0,
          f = 1;

      if ("gradient" == o.type_) {
        var P = o.x0_ / s,
            g = o.y0_ / l,
            L = o.x1_ / s,
            m = o.y1_ / l,
            M = V(t, P, g),
            C = V(t, L, m),
            y = C.x - M.x,
            w = C.y - M.y;
        (c = 180 * Math.atan2(y, w) / Math.PI) < 0 && (c += 360), c < 1e-6 && (c = 0);
      } else {
        var _a = V(t, o.x0_, o.y0_);

        h = {
          x: (_a.x - i.x) / d,
          y: (_a.y - i.y) / u
        }, d /= s * r, u /= l * r;
        var v = e.max(d, u);
        p = 2 * o.r0_ / v, f = 2 * o.r1_ / v - p;
      }

      var _ = o.colors_;

      _.sort(function (e, t) {
        return e.offset - t.offset;
      });

      for (var I = _.length, S = _[0].color, b = _[I - 1].color, D = _[0].alpha * t.globalAlpha, k = _[I - 1].alpha * t.globalAlpha, T = [], O = 0; O < I; O++) {
        var A = _[O];
        T.push(A.offset * f + p + " " + A.color);
      }

      a.push('<g_vml_:fill type="', o.type_, '"', ' method="none" focus="100%"', ' color="', S, '"', ' color2="', b, '"', ' colors="', T.join(","), '"', ' opacity="', k, '"', ' g_o_:opacity2="', D, '"', ' angle="', c, '"', ' focusposition="', h.x, ",", h.y, '" />');
    } else if (o instanceof x) {
      if (d && u) {
        var E = -i.x,
            G = -i.y;
        a.push("<g_vml_:fill", ' position="', E / d * s * s, ",", G / u * l * l, '"', ' type="tile"', ' src="', o.src_, '" />');
      }
    } else {
      var B = R(t.fillStyle),
          W = B.color,
          N = B.alpha * t.globalAlpha;
      a.push('<g_vml_:fill color="', W, '" opacity="', N, '" />');
    }
  }

  function V(e, t, a) {
    var i = e.m_;
    return {
      x: r * (t * i[0][0] + a * i[1][0] + i[2][0]) - s,
      y: r * (t * i[0][1] + a * i[1][1] + i[2][1]) - s
    };
  }

  function B(e, t, a) {
    if (function (e) {
      return isFinite(e[0][0]) && isFinite(e[0][1]) && isFinite(e[1][0]) && isFinite(e[1][1]) && isFinite(e[2][0]) && isFinite(e[2][1]);
    }(t) && (e.m_ = t, a)) {
      var i = t[0][0] * t[1][1] - t[0][1] * t[1][0];
      e.lineScale_ = o(n(i));
    }
  }

  function F(e) {
    this.type_ = e, this.x0_ = 0, this.y0_ = 0, this.r0_ = 0, this.x1_ = 0, this.y1_ = 0, this.r1_ = 0, this.colors_ = [];
  }

  function x(e, t) {
    switch (function (e) {
      e && 1 == e.nodeType && "IMG" == e.tagName || W("TYPE_MISMATCH_ERR");
      "complete" != e.readyState && W("INVALID_STATE_ERR");
    }(e), t) {
      case "repeat":
      case null:
      case "":
        this.repetition_ = "repeat";
        break;

      case "repeat-x":
      case "repeat-y":
      case "no-repeat":
        this.repetition_ = t;
        break;

      default:
        W("SYNTAX_ERR");
    }

    this.src_ = e.src, this.width_ = e.width, this.height_ = e.height;
  }

  function W(e) {
    throw new N(e);
  }

  function N(e) {
    this.code = this[e], this.message = e + ": DOM Exception " + this.code;
  }

  O.clearRect = function () {
    this.textMeasureEl_ && (this.textMeasureEl_.removeNode(!0), this.textMeasureEl_ = null), this.element_.innerHTML = "";
  }, O.beginPath = function () {
    this.currentPath_ = [];
  }, O.moveTo = function (e, t) {
    var a = V(this, e, t);
    this.currentPath_.push({
      type: "moveTo",
      x: a.x,
      y: a.y
    }), this.currentX_ = a.x, this.currentY_ = a.y;
  }, O.lineTo = function (e, t) {
    var a = V(this, e, t);
    this.currentPath_.push({
      type: "lineTo",
      x: a.x,
      y: a.y
    }), this.currentX_ = a.x, this.currentY_ = a.y;
  }, O.bezierCurveTo = function (e, t, a, i, n, o) {
    var r = V(this, n, o);
    A(this, V(this, e, t), V(this, a, i), r);
  }, O.quadraticCurveTo = function (e, t, a, i) {
    var n = V(this, e, t),
        o = V(this, a, i),
        r = {
      x: this.currentX_ + 2 / 3 * (n.x - this.currentX_),
      y: this.currentY_ + 2 / 3 * (n.y - this.currentY_)
    };
    A(this, r, {
      x: r.x + (o.x - this.currentX_) / 3,
      y: r.y + (o.y - this.currentY_) / 3
    }, o);
  }, O.arc = function (e, t, n, o, l, d) {
    n *= r;
    var u = d ? "at" : "wa",
        c = e + i(o) * n - s,
        h = t + a(o) * n - s,
        p = e + i(l) * n - s,
        f = t + a(l) * n - s;
    c != p || d || (c += .125);
    var P = V(this, e, t),
        g = V(this, c, h),
        L = V(this, p, f);
    this.currentPath_.push({
      type: u,
      x: P.x,
      y: P.y,
      radius: n,
      xStart: g.x,
      yStart: g.y,
      xEnd: L.x,
      yEnd: L.y
    });
  }, O.rect = function (e, t, a, i) {
    this.moveTo(e, t), this.lineTo(e + a, t), this.lineTo(e + a, t + i), this.lineTo(e, t + i), this.closePath();
  }, O.strokeRect = function (e, t, a, i) {
    var n = this.currentPath_;
    this.beginPath(), this.moveTo(e, t), this.lineTo(e + a, t), this.lineTo(e + a, t + i), this.lineTo(e, t + i), this.closePath(), this.stroke(), this.currentPath_ = n;
  }, O.fillRect = function (e, t, a, i) {
    var n = this.currentPath_;
    this.beginPath(), this.moveTo(e, t), this.lineTo(e + a, t), this.lineTo(e + a, t + i), this.lineTo(e, t + i), this.closePath(), this.fill(), this.currentPath_ = n;
  }, O.createLinearGradient = function (e, t, a, i) {
    var n = new F("gradient");
    return n.x0_ = e, n.y0_ = t, n.x1_ = a, n.y1_ = i, n;
  }, O.createRadialGradient = function (e, t, a, i, n, o) {
    var r = new F("gradientradial");
    return r.x0_ = e, r.y0_ = t, r.r0_ = a, r.x1_ = i, r.y1_ = n, r.r1_ = o, r;
  }, O.drawImage = function (a, i) {
    var n,
        o,
        s,
        l,
        d,
        u,
        c,
        h,
        p = a.runtimeStyle.width,
        f = a.runtimeStyle.height;
    a.runtimeStyle.width = "auto", a.runtimeStyle.height = "auto";
    var P = a.width,
        g = a.height;
    if (a.runtimeStyle.width = p, a.runtimeStyle.height = f, 3 == arguments.length) n = arguments[1], o = arguments[2], d = u = 0, c = s = P, h = l = g;else if (5 == arguments.length) n = arguments[1], o = arguments[2], s = arguments[3], l = arguments[4], d = u = 0, c = P, h = g;else {
      if (9 != arguments.length) throw Error("Invalid number of arguments");
      d = arguments[1], u = arguments[2], c = arguments[3], h = arguments[4], n = arguments[5], o = arguments[6], s = arguments[7], l = arguments[8];
    }
    var L = V(this, n, o),
        m = [];

    if (m.push(" <g_vml_:group", ' coordsize="', 10 * r, ",", 10 * r, '"', ' coordorigin="0,0"', ' style="width:', 10, "px;height:", 10, "px;position:absolute;"), 1 != this.m_[0][0] || this.m_[0][1] || 1 != this.m_[1][1] || this.m_[1][0]) {
      var M = [];
      M.push("M11=", this.m_[0][0], ",", "M12=", this.m_[1][0], ",", "M21=", this.m_[0][1], ",", "M22=", this.m_[1][1], ",", "Dx=", t(L.x / r), ",", "Dy=", t(L.y / r), "");
      var C = L,
          y = V(this, n + s, o),
          w = V(this, n, o + l),
          v = V(this, n + s, o + l);
      C.x = e.max(C.x, y.x, w.x, v.x), C.y = e.max(C.y, y.y, w.y, v.y), m.push("padding:0 ", t(C.x / r), "px ", t(C.y / r), "px 0;filter:progid:DXImageTransform.Microsoft.Matrix(", M.join(""), ", sizingmethod='clip');");
    } else m.push("top:", t(L.y / r), "px;left:", t(L.x / r), "px;");

    m.push(' ">', '<g_vml_:image src="', a.src, '"', ' style="width:', r * s, "px;", " height:", r * l, 'px"', ' cropleft="', d / P, '"', ' croptop="', u / g, '"', ' cropright="', (P - d - c) / P, '"', ' cropbottom="', (g - u - h) / g, '"', " />", "</g_vml_:group>"), this.element_.insertAdjacentHTML("BeforeEnd", m.join(""));
  }, O.stroke = function (e) {
    var a = [];
    a.push("<g_vml_:shape", ' filled="', !!e, '"', ' style="position:absolute;width:', 10, "px;height:", 10, 'px;"', ' coordorigin="0,0"', ' coordsize="', 10 * r, ",", 10 * r, '"', ' stroked="', !e, '"', ' path="');

    for (var i = {
      x: null,
      y: null
    }, n = {
      x: null,
      y: null
    }, o = 0; o < this.currentPath_.length; o++) {
      var s = this.currentPath_[o];

      switch (s.type) {
        case "moveTo":
          s, a.push(" m ", t(s.x), ",", t(s.y));
          break;

        case "lineTo":
          a.push(" l ", t(s.x), ",", t(s.y));
          break;

        case "close":
          a.push(" x "), s = null;
          break;

        case "bezierCurveTo":
          a.push(" c ", t(s.cp1x), ",", t(s.cp1y), ",", t(s.cp2x), ",", t(s.cp2y), ",", t(s.x), ",", t(s.y));
          break;

        case "at":
        case "wa":
          a.push(" ", s.type, " ", t(s.x - this.arcScaleX_ * s.radius), ",", t(s.y - this.arcScaleY_ * s.radius), " ", t(s.x + this.arcScaleX_ * s.radius), ",", t(s.y + this.arcScaleY_ * s.radius), " ", t(s.xStart), ",", t(s.yStart), " ", t(s.xEnd), ",", t(s.yEnd));
      }

      s && ((null == i.x || s.x < i.x) && (i.x = s.x), (null == n.x || s.x > n.x) && (n.x = s.x), (null == i.y || s.y < i.y) && (i.y = s.y), (null == n.y || s.y > n.y) && (n.y = s.y));
    }

    a.push(' ">'), e ? G(this, a, i, n) : E(this, a), a.push("</g_vml_:shape>"), this.element_.insertAdjacentHTML("beforeEnd", a.join(""));
  }, O.fill = function () {
    this.stroke(!0);
  }, O.closePath = function () {
    this.currentPath_.push({
      type: "close"
    });
  }, O.save = function () {
    var e = {};
    C(this, e), this.aStack_.push(e), this.mStack_.push(this.m_), this.m_ = M([[1, 0, 0], [0, 1, 0], [0, 0, 1]], this.m_);
  }, O.restore = function () {
    this.aStack_.length && (C(this.aStack_.pop(), this), this.m_ = this.mStack_.pop());
  }, O.translate = function (e, t) {
    B(this, M([[1, 0, 0], [0, 1, 0], [e, t, 1]], this.m_), !1);
  }, O.rotate = function (e) {
    var t = i(e),
        n = a(e);
    B(this, M([[t, n, 0], [-n, t, 0], [0, 0, 1]], this.m_), !1);
  }, O.scale = function (e, t) {
    this.arcScaleX_ *= e, this.arcScaleY_ *= t, B(this, M([[e, 0, 0], [0, t, 0], [0, 0, 1]], this.m_), !0);
  }, O.transform = function (e, t, a, i, n, o) {
    B(this, M([[e, t, 0], [a, i, 0], [n, o, 1]], this.m_), !0);
  }, O.setTransform = function (e, t, a, i, n, o) {
    B(this, [[e, t, 0], [a, i, 0], [n, o, 1]], !0);
  }, O.drawText_ = function (e, a, i, n, o) {
    var s,
        l = this.m_,
        d = 0,
        c = 1e3,
        h = {
      x: 0,
      y: 0
    },
        p = [],
        f = function (e, t) {
      var a = {};

      for (var i in e) {
        a[i] = e[i];
      }

      var n = parseFloat(t.currentStyle.fontSize),
          o = parseFloat(e.size);
      return "number" == typeof e.size ? a.size = e.size : -1 != e.size.indexOf("px") ? a.size = o : -1 != e.size.indexOf("em") ? a.size = n * o : -1 != e.size.indexOf("%") ? a.size = n / 100 * o : -1 != e.size.indexOf("pt") ? a.size = o / .75 : a.size = n, a.size *= .981, a;
    }(function (e) {
      if (D[e]) return D[e];
      var t = document.createElement("div").style;

      try {
        t.font = e;
      } catch (e) {}

      return D[e] = {
        style: t.fontStyle || b.style,
        variant: t.fontVariant || b.variant,
        weight: t.fontWeight || b.weight,
        size: t.fontSize || b.size,
        family: t.fontFamily || b.family
      }, D[e];
    }(this.font), this.element_),
        P = (s = f).style + " " + s.variant + " " + s.weight + " " + s.size + "px " + s.family,
        g = this.element_.currentStyle,
        L = this.textAlign.toLowerCase();

    switch (L) {
      case "left":
      case "center":
      case "right":
        break;

      case "end":
        L = "ltr" == g.direction ? "right" : "left";
        break;

      case "start":
        L = "rtl" == g.direction ? "right" : "left";
        break;

      default:
        L = "left";
    }

    switch (this.textBaseline) {
      case "hanging":
      case "top":
        h.y = f.size / 1.75;
        break;

      case "middle":
        break;

      default:
      case null:
      case "alphabetic":
      case "ideographic":
      case "bottom":
        h.y = -f.size / 2.25;
    }

    switch (L) {
      case "right":
        d = 1e3, c = .05;
        break;

      case "center":
        d = c = 500;
    }

    var m = V(this, a + h.x, i + h.y);
    p.push('<g_vml_:line from="', -d, ' 0" to="', c, ' 0.05" ', ' coordsize="100 100" coordorigin="0 0"', ' filled="', !o, '" stroked="', !!o, '" style="position:absolute;width:1px;height:1px;">'), o ? E(this, p) : G(this, p, {
      x: -d,
      y: 0
    }, {
      x: c,
      y: f.size
    });
    var M = l[0][0].toFixed(3) + "," + l[1][0].toFixed(3) + "," + l[0][1].toFixed(3) + "," + l[1][1].toFixed(3) + ",0,0",
        C = t(m.x / r) + "," + t(m.y / r);
    p.push('<g_vml_:skew on="t" matrix="', M, '" ', ' offset="', C, '" origin="', d, ' 0" />', '<g_vml_:path textpathok="true" />', '<g_vml_:textpath on="true" string="', u(e), '" style="v-text-align:', L, ";font:", u(P), '" /></g_vml_:line>'), this.element_.insertAdjacentHTML("beforeEnd", p.join(""));
  }, O.fillText = function (e, t, a, i) {
    this.drawText_(e, t, a, i, !1);
  }, O.strokeText = function (e, t, a, i) {
    this.drawText_(e, t, a, i, !0);
  }, O.measureText = function (e) {
    if (!this.textMeasureEl_) {
      this.element_.insertAdjacentHTML("beforeEnd", '<span style="position:absolute;top:-20000px;left:0;padding:0;margin:0;border:none;white-space:pre;"></span>'), this.textMeasureEl_ = this.element_.lastChild;
    }

    var t = this.element_.ownerDocument;
    return this.textMeasureEl_.innerHTML = "", this.textMeasureEl_.style.font = this.font, this.textMeasureEl_.appendChild(t.createTextNode(e)), {
      width: this.textMeasureEl_.offsetWidth
    };
  }, O.clip = function () {}, O.arcTo = function () {}, O.createPattern = function (e, t) {
    return new x(e, t);
  }, F.prototype.addColorStop = function (e, t) {
    t = R(t), this.colors_.push({
      offset: e,
      color: t.color,
      alpha: t.alpha
    });
  };
  var $ = N.prototype = new Error();
  $.INDEX_SIZE_ERR = 1, $.DOMSTRING_SIZE_ERR = 2, $.HIERARCHY_REQUEST_ERR = 3, $.WRONG_DOCUMENT_ERR = 4, $.INVALID_CHARACTER_ERR = 5, $.NO_DATA_ALLOWED_ERR = 6, $.NO_MODIFICATION_ALLOWED_ERR = 7, $.NOT_FOUND_ERR = 8, $.NOT_SUPPORTED_ERR = 9, $.INUSE_ATTRIBUTE_ERR = 10, $.INVALID_STATE_ERR = 11, $.SYNTAX_ERR = 12, $.INVALID_MODIFICATION_ERR = 13, $.NAMESPACE_ERR = 14, $.INVALID_ACCESS_ERR = 15, $.VALIDATION_ERR = 16, $.TYPE_MISMATCH_ERR = 17, G_vmlCanvasManager = p, CanvasRenderingContext2D = T, CanvasGradient = F, CanvasPattern = x, DOMException = N;
}();
var Gribmap = {},
    ErrorCatching = -1,
    SrvIndex = 1;

function Wind(e, t) {
  this.wspeed = e, this.wheading = t;
}

function normalizeLongitude0(e) {
  var t;
  return (t = e % 360) > 180 ? t -= 360 : t <= -180 && (t += 360), t;
}

function normalizeLongitude360(e) {
  var t;
  return (t = e % 360) < 0 && (t += 360), t;
}

Gribmap.ServerURL = function () {
  return "undefined" != typeof WindGridServers && WindGridServers ? (0 === (SrvIndex = (SrvIndex + 1) % WindGridServers.length) && (SrvIndex = 1), WindGridServers[SrvIndex]) : "";
}, Gribmap.windgrid_uribase = function () {
  return Gribmap.ServerURL() + "/ws/windinfo/windgrid.php";
}, Gribmap.griblist_uribase = "/ws/windinfo/list.php", Gribmap.Pixel = OpenLayers.Class(OpenLayers.Pixel, {
  moveBy: function moveBy(e) {
    this.x += e.x, this.y += e.y;
  },
  moveByPolar: function moveByPolar(e, t) {
    var a = (t - 90) * Math.PI / 180;
    this.x += e * Math.cos(a), this.y += e * Math.sin(a);
  },
  CLASS_NAME: "Gribmap.Pixel"
}), Gribmap.WindLevel = OpenLayers.Class({
  basestep: .5,
  gribLevel: 0,
  blocx: 360,
  blocy: 180,
  step: 2,
  stepmultiple: 4,
  windAreas: {},
  layer: null,
  initialize: function initialize(e, t, a, i, n) {
    this.gribLevel = e, this.windAreas = [], this.stepmultiple = t, this.step = this.basestep * t, this.blocx = a, this.blocy = i, this.layer = n;
  },
  getGribLeftLimit: function getGribLeftLimit(e) {
    return this.getGribLeftId(e) * this.blocx - 180;
  },
  getGribLeftId: function getGribLeftId(e) {
    return Math.floor((e + 180) / this.blocx);
  },
  getGribBottomLimit: function getGribBottomLimit(e) {
    return this.getGribBottomId(e) * this.blocy - 90;
  },
  getGribBottomId: function getGribBottomId(e) {
    return Math.floor((e + 90) / this.blocy);
  },
  notifyLoad: function notifyLoad(e, t) {
    this.layer && this.gribLevel === this.layer.gribLevel && this.layer.isInTimeRange(e) && this.layer.getExtent().transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326")).intersectsBounds(t) && (this.extendWindArea(t), this.layer.redraw());
  },
  getWindAreas: function getWindAreas(e) {
    for (var t = [], a = 0, i = this.getGribLeftLimit(e.left), n = null, o = null; i < e.right;) {
      for (n = this.getGribBottomLimit(e.bottom); n < e.top;) {
        o = new Gribmap.WindArea(i, n, this), t[a] = this.checkWindArea(o), n += this.blocy, a += 1;
      }

      i += this.blocx;
    }

    return t;
  },
  checkWindArea: function checkWindArea(e) {
    return void 0 === this.windAreas[e.toString()] ? this.windAreas[e.toString()] = e : e = this.windAreas[e.toString()], e.checkWindArray(this.layer.gribtimeBefore), e.checkWindArray(this.layer.gribtimeAfter), e;
  },
  extendWindArea: function extendWindArea(e) {
    for (var t = this.layer.getGribTimeList(), a = 0; a < t.length; a++) {
      e.checkWindArray(t[a]);
    }
  },
  getWindInfo: function getWindInfo(e, t) {
    var a = this.getGribLeftLimit(t),
        i = this.getGribBottomLimit(e),
        n = new Gribmap.WindArea(a, i, this);

    try {
      var o = this.windAreas[n.toString()];
      return void 0 !== o && o ? o.getWindInfo(e, t, this.layer.time, this.layer.gribtimeBefore, this.layer.gribtimeAfter) : null;
    } catch (e) {
      return null;
    }
  },
  CLASS_NAME: "Gribmap.WindLevel"
}), Gribmap.WindArray = OpenLayers.Class({
  time: null,
  winddatas: null,
  status: "void",
  windArea: null,
  initialize: function initialize(e, t) {
    this.status = "void", this.time = e, this.windArea = t;
  },
  isLoaded: function isLoaded() {
    return "loaded" == this.status;
  },
  isLoading: function isLoading() {
    return "loading" == this.status;
  },
  notifyLoad: function notifyLoad() {
    this.windArea && this.windArea.notifyLoad(this.time);
  },
  handleWindGridReply: function handleWindGridReply(e) {
    var t;
    200 == e.status ? (t = JSON.parse(e.responseText), this.winddatas = this.transformRawWindArray(t), this.status = "loaded", this.notifyLoad(), HidePb("#PbGribLoginProgress")) : this.status = "void";
  },
  transformRawWindArray: function transformRawWindArray(e) {
    var t,
        a,
        i,
        n = [];

    for (t in e) {
      e[t] && (void 0 === n[(a = e[t]).lat] && (n[a.lat] = []), i = new Wind(a.wspd, a.whdg), n[a.lat][a.lon] = i, 180 === a.lon && (n[a.lat][-a.lon] = i));
    }

    return n;
  },
  getWindGrid: function getWindGrid() {
    if (!this.isLoaded() && !this.isLoading() && 0 != this.time) {
      this.status = "loading";
      var e = this.windArea.right % 360,
          t = this.windArea.left % 360;
      e < -180 && (e += 360), e > 180 && (e -= 360), t < -180 && (t += 360), t > 180 && (t -= 360), ShowPb("#PbGribLoginProgress");
      OpenLayers.Request.GET({
        url: Gribmap.windgrid_uribase(),
        params: {
          north: this.windArea.top,
          south: this.windArea.bottom,
          east: e,
          west: t,
          timerequest: this.time,
          stepmultiple: this.windArea.windlevel.stepmultiple
        },
        async: !0,
        headers: {
          Accept: "application/json"
        },
        callback: this.handleWindGridReply,
        scope: this
      });
    }
  },
  CLASS_NAME: "Gribmap.WindArray"
}), Gribmap.WindArea = OpenLayers.Class(OpenLayers.Bounds, {
  windlevel: null,
  windArrays: null,
  initialize: function initialize(e, t, a) {
    this.windlevel = a, this.windArrays = [], this.left = e, this.bottom = t, this.right = e + a.blocx, this.top = t + a.blocy;
  },
  notifyLoad: function notifyLoad(e) {
    null != this.windlevel && this.windlevel.notifyLoad(e, this);
  },
  checkWindArray: function checkWindArray(e) {
    this.exists(e) || 0 == e || (this.windArrays[e] = new Gribmap.WindArray(e, this), this.windArrays[e].getWindGrid());
  },
  exists: function exists(e) {
    return void 0 !== this.windArrays[e];
  },
  isLoaded: function isLoaded(e) {
    return this.exists(e) && this.windArrays[e].isLoaded();
  },
  isLoading: function isLoading(e) {
    return this.exists(e) && this.windArrays[e].isLoading();
  },
  toString: function toString() {
    return "gribresol=(" + this.windlevel.griblevel + ") " + OpenLayers.Bounds.prototype.toString.apply(this, arguments);
  },
  getWindInfo: function getWindInfo(e, t, a, i, n) {
    return this.getWindInfo2(e, t, a, this.windArrays[i], this.windArrays[n]);
  },
  getWindInfo2: function getWindInfo2(e, t, a, i, n) {
    var o,
        r,
        s,
        l,
        d,
        u,
        c,
        h,
        p,
        f,
        P,
        g,
        L,
        m,
        M,
        C,
        y,
        w,
        v,
        _,
        I,
        S,
        R,
        b,
        D,
        k,
        T,
        O,
        A,
        E,
        G = this.windlevel.step;

    return t = normalizeLongitude0(t), d = Math.floor(e / G) * G, u = Math.ceil(e / G) * G, h = Math.floor(t / G) * G, c = Math.ceil(t / G) * G, A = (t - h) / G, E = (e - d) / G, void 0 !== i.winddatas && i.winddatas && u in i.winddatas && d in i.winddatas && c in i.winddatas[u] && h in i.winddatas[u] && c in i.winddatas[d] && h in i.winddatas[d] ? (o = i.winddatas[u][c], r = i.winddatas[u][h], s = i.winddatas[d][c], l = i.winddatas[d][h], p = r.wspeed + A * (o.wspeed - r.wspeed), g = (f = l.wspeed + A * (s.wspeed - l.wspeed)) + E * (p - f), m = r.wheading * Math.PI / 180, M = o.wheading * Math.PI / 180, w = (y = r.wspeed * Math.cos(m)) + A * (o.wspeed * Math.cos(M) - y), v = (y = r.wspeed * Math.sin(m)) + A * (o.wspeed * Math.sin(M) - y), m = l.wheading * Math.PI / 180, M = s.wheading * Math.PI / 180, S = (_ = (y = l.wspeed * Math.cos(m)) + A * (s.wspeed * Math.cos(M) - y)) + E * (w - _), R = (I = (y = l.wspeed * Math.sin(m)) + A * (s.wspeed * Math.sin(M) - y)) + E * (v - I), void 0 !== n.winddatas && n.winddatas && u in n.winddatas && d in n.winddatas && c in n.winddatas[u] && h in n.winddatas[u] && c in n.winddatas[d] && h in n.winddatas[d] ? (o = n.winddatas[u][c], r = n.winddatas[u][h], s = n.winddatas[d][c], l = n.winddatas[d][h], p = r.wspeed + A * (o.wspeed - r.wspeed), L = (f = l.wspeed + A * (s.wspeed - l.wspeed)) + E * (p - f), m = r.wheading * Math.PI / 180, M = o.wheading * Math.PI / 180, w = (y = r.wspeed * Math.cos(m)) + A * (o.wspeed * Math.cos(M) - y), v = (y = r.wspeed * Math.sin(m)) + A * (o.wspeed * Math.sin(M) - y), m = l.wheading * Math.PI / 180, M = s.wheading * Math.PI / 180, b = (_ = (y = l.wspeed * Math.cos(m)) + A * (s.wspeed * Math.cos(M) - y)) + E * (w - _), D = (I = (y = l.wspeed * Math.sin(m)) + A * (s.wspeed * Math.sin(M) - y)) + E * (v - I), P = g + (O = (a - i.time) / (n.time - i.time)) * (L - g), k = S + O * (b - S), T = R + O * (D - R), C = 180 * Math.acos(k / Math.sqrt(k * k + T * T)) / Math.PI, T < 0 && (C = 360 - C), new Wind(P, C)) : null) : null;
  },
  CLASS_NAME: "Gribmap.WindArea"
}), Gribmap.Layer = OpenLayers.Class(OpenLayers.Layer, {
  isBaseLayer: !1,
  canvas: null,
  windLevels: [],
  arrowstep: VLM2Prefs.MapPrefs.WindArrowsSpacing,
  timeoffset: 0,
  time: 0,
  gribtimeBefore: 0,
  gribtimeAfter: 0,
  griblist: null,
  timeDelta: 21600,
  initialize: function initialize(e, t) {
    OpenLayers.Layer.prototype.initialize.apply(this, arguments), this.getGribList(), this.windLevels[0] = new Gribmap.WindLevel(0, 4, 120, 60, this), this.windLevels[1] = new Gribmap.WindLevel(1, 2, 60, 30, this), this.windLevels[2] = new Gribmap.WindLevel(2, 1, 20, 20, this), this.canvas = document.createElement("canvas"), "undefined" != typeof G_vmlCanvasManager && G_vmlCanvasManager.initElement(this.canvas), this.canvas.style.position = "absolute";
    var a = document.createElement("div");
    a.appendChild(this.canvas), this.div.appendChild(a);
  },
  addTimeOffset: function addTimeOffset(e) {
    this.timeoffset += e, this.setTimeSegmentFromOffset();
  },
  timereset: function timereset() {
    this.addTimeOffset(-this.timeoffset);
  },
  timeforward: function timeforward() {
    this.addTimeOffset(3600);
  },
  timebackward: function timebackward() {
    this.addTimeOffset(-3600);
  },
  getGribList: function getGribList() {
    OpenLayers.Request.GET({
      url: Gribmap.griblist_uribase,
      async: !0,
      headers: {
        Accept: "application/json"
      },
      callback: this.handleGribListReply,
      scope: this
    });
  },
  handleGribListReply: function handleGribListReply(e) {
    if (200 == e.status) {
      var t = JSON.parse(e.responseText);
      this.griblist = t.grib_timestamps, this.gribupdatetime = t.update_time, this.maxtime = Math.max.apply(null, this.griblist), this.mintime = Math.min.apply(null, this.griblist);
    }

    var a = new Date();
    this.setTimeSegment(a.getTime() / 1e3);
  },
  setTimeSegmentFromOffset: function setTimeSegmentFromOffset() {
    var e = new Date();
    this.setTimeSegment(e.getTime() / 1e3 + this.timeoffset);
  },
  isInTimeRange: function isInTimeRange(e) {
    return e >= this.gribtimeBefore && e <= this.gribtimeAfter;
  },
  getGribTimeList: function getGribTimeList() {
    for (var e = [], t = 0; t < this.griblist.length; t++) {
      this.griblist[t] >= this.time - this.timeDelta && this.griblist[t] <= this.time + this.timeDelta && e.push(this.griblist[t]);
    }

    return e;
  },
  setTimeSegment: function setTimeSegment(e) {
    e = Math.floor(e);
    var t = 0,
        a = this.mintime,
        i = this.maxtime;

    for (t = 0; t < this.griblist.length; t++) {
      this.griblist[t] >= a && this.griblist[t] <= e && (a = this.griblist[t]), this.griblist[t] <= i && this.griblist[t] >= e && (i = this.griblist[t]);
    }

    this.gribtimeBefore = a, this.gribtimeAfter = i, this.time = e, this.redraw();
  },
  setGribLevel: function setGribLevel(e) {
    var t,
        a = Math.abs(e.left - e.right),
        i = Math.abs(e.top - e.bottom);

    for (t = this.windLevels.length - 1; t >= 0 && !(a < 2 * this.windLevels[t].blocx && i < 2 * this.windLevels[t].blocy); t--) {
      ;
    }

    return this.gribLevel = Math.max(t, 0), t;
  },
  windAtPosition: function windAtPosition(e) {
    return this.windLevels[this.gribLevel].getWindInfo(e.lat, e.lon);
  },
  moveTo: function moveTo(e, t, a) {
    var i, n;
    if (OpenLayers.Layer.prototype.moveTo.apply(this, arguments), a) return;
    var o = this.map.getLayerPxFromLonLat(new OpenLayers.LonLat(e.left, e.top)),
        r = this.map.getLayerPxFromLonLat(new OpenLayers.LonLat(e.right, e.bottom));
    r.x -= o.x, r.y -= o.y;
    var s = e.transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326")),
        l = Math.abs(e.left - e.right) > 30 || Math.abs(e.top - e.bottom) > 30;
    this.UpdateGribMap(l);
    var d = this.canvas.getContext("2d");

    if (d.canvas.style.left = o.x + "px", d.canvas.style.top = o.y + "px", d.canvas.width = r.x, d.canvas.height = r.y, this.drawContext(d), l) {
      this.setGribLevel(s), n = this.windLevels[this.gribLevel].getWindAreas(s);

      for (var _t3 = 0; _t3 < n.length; _t3++) {
        if (!(i = n[_t3]).isLoaded(this.gribtimeBefore) || !i.isLoaded(this.gribtimeAfter)) continue;
        (e = i.clone()).transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));

        var _a2 = this.map.getLayerPxFromLonLat(new OpenLayers.LonLat(e.left, e.top)),
            _s = this.map.getLayerPxFromLonLat(new OpenLayers.LonLat(e.right, e.bottom));

        _a2.x -= o.x, _a2.y -= o.y, _s.x -= o.x, _s.y -= o.y, _a2.x = Math.ceil(_a2.x / this.arrowstep) * this.arrowstep, _a2.y = Math.ceil(_a2.y / this.arrowstep) * this.arrowstep, _a2.x < 0 && (_a2.x = 0), _a2.y < 0 && (_a2.y = 0), _s.x > r.x && (_s.x = r.x), _s.y > r.y && (_s.y = r.y), this.drawWindAreaBig(_a2, _s, i, d);
      }
    } else {
      e.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));

      var _t4 = this.map.getLayerPxFromLonLat(new OpenLayers.LonLat(e.left, e.top)),
          _a3 = this.map.getLayerPxFromLonLat(new OpenLayers.LonLat(e.right, e.bottom));

      _t4.x -= o.x, _t4.y -= o.y, _a3.x -= o.x, _a3.y -= o.y, _t4.x = Math.ceil(_t4.x / this.arrowstep) * this.arrowstep, _t4.y = Math.ceil(_t4.y / this.arrowstep) * this.arrowstep, _t4.x < 0 && (_t4.x = 0), _t4.y < 0 && (_t4.y = 0), _a3.x > r.x && (_a3.x = r.x), _a3.y > r.y && (_a3.y = r.y), this.drawWindAreaSmall(_t4, _a3, i, d);
    }
  },
  drawWindArea: function drawWindArea(e, t, a, i, n) {
    throw "Deprecated drawWindArea";
  },
  drawWindAreaBig: function drawWindAreaBig(e, t, a, i, n) {
    for (var o = this.arrowstep, r = a.windArrays[this.gribtimeBefore], s = a.windArrays[this.gribtimeAfter]; e.x < t.x;) {
      for (e.y = 0; e.y < t.y;) {
        var _t5 = this.map.getLonLatFromPixel(e).transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));

        try {
          winfo = a.getWindInfo2(_t5.lat, _t5.lon, this.time, r, s), this.drawWind(i, e.x, e.y, winfo);
        } catch (e) {
          ErrorCatching > 0 && (alert(_t5 + " / " + winfo.wspeed + " / " + winfo.wheading), ErrorCatching -= 1);
        }

        e.y += o;
      }

      e.x += o;
    }
  },
  UpdateGribMap: function UpdateGribMap(e) {
    e ? ($(".BigGrib").css("display", "block"), $(".SmallGrib").css("display", "none")) : ($(".BigGrib").css("display", "none"), $(".SmallGrib").css("display", "block"));
  },
  drawWindAreaSmall: function drawWindAreaSmall(e, t, a, i, n) {
    var _this = this;

    var o = this.arrowstep;

    for (; e.x < t.x;) {
      for (e.y = 0; e.y < t.y;) {
        var r = this.map.getLonLatFromPixel(e).transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));

        try {
          (function () {
            var o = _this,
                s = GribMgr.WindAtPointInTime(new Date(1e3 * _this.time), r.lat, r.lon, n ? null : function () {
              o.drawWindArea(e, t, a, i, !0);
            });

            if (s) {
              var _t6 = new Wind(s.Speed, s.Heading);

              _this.drawWind(i, e.x, e.y, _t6);
            }
          })();
        } catch (e) {
          ErrorCatching > 0 && (alert(r + " / " + winfo.wspeed + " / " + winfo.wheading), ErrorCatching -= 1);
        }

        e.y += o;
      }

      e.x += o;
    }
  },
  windSpeedToColor: function windSpeedToColor(e) {
    return e <= 10 ? e <= 3 ? e <= 1 ? "#FFFFFF" : "#9696E1" : e <= 6 ? "#508CCD" : "#3C64B4" : e <= 33 ? e <= 21 ? e <= 15 ? "#41B464" : "#B4CD0A" : e <= 26 ? "#D2D216" : "#E1D220" : e <= 40 ? "#FFB300" : e <= 47 ? "#FF6F00" : e <= 55 ? "#FF2B00" : e <= 63 ? "#E60000" : "#7F0000";
  },
  drawWindTriangle: function drawWindTriangle(e, t, a, i) {
    var n, o, r, s, l, d, u;
    windarrow_minsize = 4, windarrow_minwidth = 0, u = Math.log(i.wspeed + 1), d = (i.wheading + 180) % 360, n = new Gribmap.Pixel(t, a), o = new Gribmap.Pixel(t, a), r = new Gribmap.Pixel(t, a), n.moveByPolar(windarrow_minsize + 4 * u, d), o.moveByPolar(windarrow_minwidth + u, d - 90), r.moveByPolar(windarrow_minwidth + u, d + 90), s = new Gribmap.Pixel((n.x + o.x + r.x) / 3, (n.y + o.y + r.y) / 3), l = new Gribmap.Pixel(t - s.x, a - s.y), n.moveBy(l), o.moveBy(l), r.moveBy(l), e.toffset = l, e.midx = (n.x + t) / 2, e.beginPath(), e.moveTo(n.x, n.y), e.lineTo(o.x, o.y), e.lineTo(r.x, r.y), e.fill(), e.stroke(), e.closePath();
  },
  drawWindText: function drawWindText(e, t, a, i) {
    var n = e.midx,
        o = a + e.toffset.y,
        r = i.wheading;
    r > 90 && r < 270 ? o += 13 + 5 * Math.cos(r * Math.PI / 180) : o -= 7 - 5 * Math.cos(r * Math.PI / 180), e.fillText(Math.round(i.wspeed) + "/" + Math.round(r) + "°", n, o);
  },
  drawContext: function drawContext(e) {
    e.font = "8px sans-serif", e.textAlign = "center", e.strokeStyle = "#fff", e.lineWidth = .5;
  },
  drawWind: function drawWind(e, t, a, i) {
    null !== i && (e.fillStyle = this.windSpeedToColor(i.wspeed), this.drawWindTriangle(e, t, a, i), e.fillStyle = "#626262", this.drawWindText(e, t, a, i));
  },
  CLASS_NAME: "Gribmap.Layer"
}), Gribmap.ControlWind = OpenLayers.Class(OpenLayers.Control.ControlSwitch, {
  label: "Gribmap.ControlWind",
  timeOffsetSpan: null,
  initialize: function initialize(e) {
    OpenLayers.Control.prototype.initialize.apply(this, arguments);
  },
  drawBaseDiv: function drawBaseDiv() {
    this.baseDiv.appendChild(this.imgButton("west-mini.png", "Gribmap_Backward", this.onClickBackward)), this.timeOffsetSpan = this.textButton(" 0h ", "reset", this.onClickReset), this.baseDiv.appendChild(this.timeOffsetSpan), this.baseDiv.appendChild(this.imgButton("east-mini.png", "Gribmap_Forward", this.onClickForward));
  },
  imgButton: function imgButton(e, t, a) {
    var i = OpenLayers.Util.getImagesLocation(),
        n = new OpenLayers.Size(18, 18),
        o = i + e,
        r = OpenLayers.Util.createAlphaImageDiv(t, null, n, o, "relative");
    return OpenLayers.Event.observe(r, "click", OpenLayers.Function.bind(a, this, o)), r;
  },
  textButton: function textButton(e, t, a) {
    var i = document.createElement("span");
    return OpenLayers.Element.addClass(i, t), i.innerHTML = e, OpenLayers.Event.observe(i, "click", OpenLayers.Function.bind(a, this, i)), i;
  },
  getGribmapLayer: function getGribmapLayer() {
    return this.gribmap ? this.gribmap : (this.map && (this.gribmap = this.map.getLayersByClass("Gribmap.Layer")[0]), this.gribmap);
  },
  onClickReset: function onClickReset(e, t) {
    OpenLayers.Event.stop(t || window.event), l = this.getGribmapLayer(), l.timereset(), this.timeOffsetSpan.innerHTML = " " + Math.round(l.timeoffset / 3600) + "h ";
  },
  onClickForward: function onClickForward(e, t) {
    OpenLayers.Event.stop(t || window.event), l = this.getGribmapLayer(), l.timeforward(), this.timeOffsetSpan.innerHTML = " " + Math.round(l.timeoffset / 3600) + "h ";
  },
  onClickBackward: function onClickBackward(e, t) {
    OpenLayers.Event.stop(t || window.event), l = this.getGribmapLayer(), l.timebackward(), this.timeOffsetSpan.innerHTML = " " + Math.round(l.timeoffset / 3600) + "h ";
  },
  CLASS_NAME: "Gribmap.ControlWind hidden"
}), Gribmap.MousePosition = OpenLayers.Class(OpenLayers.Control.MousePosition, {
  gribmap: null,
  initialize: function initialize(e) {
    OpenLayers.Control.prototype.initialize.apply(this, arguments);
  },
  formatOutput: function formatOutput(e) {
    var t = OpenLayers.Util.getFormattedLonLat(e.lat, "lat", "dms");
    t += " " + OpenLayers.Util.getFormattedLonLat(e.lon, "lon", "dms"), GM_Pos = e;
    var a = GribMgr.WindAtPointInTime(new Date(), e.lat, e.lon);

    if (a) {
      new Wind(a.Speed, a.Heading);
      t += " - " + Math.round(10 * a.Speed) / 10 + "n / " + Math.round(10 * a.Heading) / 10 + "°";
    }

    return t;
  },
  CLASS_NAME: "Gribmap.MousePosition"
});
var GribMgr = new VLM2GribManager();

function GribData(e) {
  this.UGRD = NaN, this.VGRD = NaN, this.TWS = NaN, void 0 !== e && (this.UGRD = e.UGRD, this.VGRD = e.VGRD, this.TWS = e.TWS), this.Strength = function () {
    return 1.9438445 * Math.sqrt(this.UGRD * this.UGRD + this.VGRD * this.VGRD);
  }, this.Direction = function () {
    var e = Math.sqrt(this.UGRD * this.UGRD + this.VGRD * this.VGRD),
        t = Math.acos(-this.VGRD / e);
    return this.UGRD > 0 && (t = 2 * Math.PI - t), (t = t / Math.PI * 180 % 360) < 0 ? t += 360 : t >= 360 && (t -= 360), t;
  };
}

function WindData(e) {
  this.Speed = NaN, this.Heading = NaN, this.IsValid = function () {
    return !isNaN(this.Speed) && !isNaN(this.Heading);
  }, void 0 !== e && (this.Speed = e.Speed, this.Heading = e.Heading);
}

function VLM2GribManager() {
  this.Tables = [], this.TableTimeStamps = [], this.Inited = !1, this.Initing = !1, this.MinWindStamp = 0, this.MaxWindStamp = 0, this.WindTableLength = 0, this.LoadQueue = [], this.GribStep = .5, this.LastGribDate = new Date(0), this.Init = function () {
    this.Inited || this.Initing || (this.Initing = !0, $.get("/ws/windinfo/list.php?v=" + Math.round(new Date().getTime() / 1e3 / 60 / 3), this.HandleGribList.bind(this)));
  }, this.HandleGribList = function (e) {
    this.TableTimeStamps = e.grib_timestamps, this.Inited = !0, this.Initing = !1, this.MinWindStamp = new Date(1e3 * this.TableTimeStamps[0]), this.MaxWindStamp = new Date(1e3 * this.TableTimeStamps[this.TableTimeStamps.length - 1]), this.WindTableLength = this.TableTimeStamps.length;
  }, this.WindAtPointInTime = function (e, t, a, i) {
    if (!this.Inited) return !1;
    var n = Math.floor((e / 1e3 - this.MinWindStamp / 1e3) / 10800);
    if (n < 0) return !1;
    if (n + 1 >= this.TableTimeStamps.length) return !1;
    var o = new WindData();
    if (Math.abs(t) > 85) return o.Heading = 0, o.Speed = 0, o;
    var r = this.CheckGribLoaded(n, t, NormalizeLongitudeDeg(a)),
        s = this.CheckGribLoaded(n + 1, t + this.GribStep, NormalizeLongitudeDeg(a + this.GribStep), i);
    if (r && !s && (s = this.CheckGribLoaded(n + 1, t + this.GribStep, NormalizeLongitudeDeg(a + this.GribStep))), !r || !s) return !1;
    var l = this.GetHydbridMeteoAtTimeIndex(n, t, a),
        d = this.GetHydbridMeteoAtTimeIndex(n + 1, t, a),
        u = l.UGRD,
        c = l.VGRD,
        h = d.UGRD,
        p = d.VGRD,
        f = e / 1e3 - this.TableTimeStamps[n],
        P = new GribData({
      UGRD: u + f / 10800 * (h - u),
      VGRD: c + f / 10800 * (p - c)
    });
    return o.Heading = P.Direction(), o.Speed = l.TWS + f / 10800 * (d.TWS - l.TWS), o;
  }, this.GetHydbridMeteoAtTimeIndex = function (e, t, a) {
    var i = 180 / this.GribStep + Math.floor(a / this.GribStep),
        n = 90 / this.GribStep + Math.floor(t / this.GribStep),
        o = (i + 1) % (360 / this.GribStep),
        r = (n + 1) % (360 / this.GribStep),
        s = a / this.GribStep - Math.floor(a / this.GribStep),
        l = t / this.GribStep - Math.floor(t / this.GribStep),
        d = this.Tables[e][i][n].UGRD,
        u = this.Tables[e][i][r].UGRD,
        c = this.Tables[e][o][n].UGRD,
        h = this.Tables[e][o][r].UGRD,
        p = this.Tables[e][i][n].VGRD,
        f = this.Tables[e][i][r].VGRD,
        P = this.Tables[e][o][n].VGRD,
        g = this.Tables[e][o][r].VGRD,
        L = this.Tables[e][i][n].Strength(),
        m = this.Tables[e][i][r].Strength(),
        M = this.Tables[e][o][n].Strength(),
        C = this.Tables[e][o][r].Strength(),
        y = this.QuadraticAverage(L, m, M, C, s, l);
    return new GribData({
      UGRD: this.QuadraticAverage(d, u, c, h, s, l),
      VGRD: this.QuadraticAverage(p, f, P, g, s, l),
      TWS: y
    });
  }, this.QuadraticAverage = function (e, t, a, i, n, o) {
    var r = e + o * (t - e);
    return r + n * (a + o * (i - a) - r);
  }, this.CheckGribLoaded = function (e, t, a, i) {
    var n = 180 / this.GribStep + Math.floor(a / this.GribStep),
        o = 90 / this.GribStep + Math.floor(t / this.GribStep),
        r = 180 / this.GribStep + Math.ceil(a / this.GribStep),
        s = 90 / this.GribStep + Math.ceil(t / this.GribStep);
    return !!(e in this.Tables && this.Tables[e][n] && this.Tables[e][n][o] && this.Tables[e][n][s] && this.Tables[e][r] && this.Tables[e][r][o] && this.Tables[e][r][s]) || (this.CheckGribLoadedIdx(e, n, o, i), this.CheckGribLoadedIdx(e, n, s, i), this.CheckGribLoadedIdx(e, r, o, i), this.CheckGribLoadedIdx(e, r, s, i), !1);
  }, this.CheckGribLoadedIdx = function (e, t, a, i) {
    if (isNaN(t) || isNaN(a)) ;
    if (this.Tables.length && this.Tables[e] && this.Tables[e][t] && this.Tables[e][t][a]) return;
    var n,
        o,
        r = a * this.GribStep - 90,
        s = t * this.GribStep - 180,
        l = 5 * Math.floor(r / 5),
        d = 5 * Math.floor(s / 5);
    r < l ? l = (n = l) - 10 : n = l + 10, s < d ? d = (o = d) - 10 : o = d + 10, o > 180 && (o = 180, this.CheckGribLoadedIdx(e, 0, a, i)), d < -180 && (d = -180, this.CheckGribLoadedIdx(e, 180 / this.GribStep - 1, a, i));
    var u = "0/" + d + "/" + o + "/" + n + "/" + l;
    this.AddGribLoadKey(u, n, l, d, o);
  }, this.AddGribLoadKey = function (e, t, a, i, n) {
    e in this.LoadQueue || (this.LoadQueue[e] = {
      length: 0,
      CallBacks: []
    }, this.LoadQueue[e].Length = 0, $.get(Gribmap.ServerURL() + "/ws/windinfo/smartgribs.php?north=" + t + "&south=" + a + "&west=" + i + "&east=" + n + "&seed=" + (0 + new Date()), this.HandleGetSmartGribList.bind(this, e))), "undefined" != typeof callback && callback && this.LoadQueue[e].CallBacks.push(callback);
  }, this.HandleGetSmartGribList = function (e, t) {
    if (t.success) {
      this.LastGribDate !== parseInt(t.GribCacheIndex, 10) && (this.LastGribDate = t.GribCacheIndex, this.Tables = [], this.Inited = !1, this.Init());

      for (var a in t.gribs_url) {
        if (t.gribs_url[a]) {
          var i = t.gribs_url[a].replace(".grb", ".txt"),
              n = 0;
          $.get("/cache/gribtiles/" + i + "&v=" + n, this.HandleSmartGribData.bind(this, e, i)), this.LoadQueue[e].Length++;
        }
      }
    } else console.log(t);
  }, this.HandleSmartGribData = function (e, t, a) {
    if (this.ProcessInputGribData(t, a, e), this.LoadQueue[e].Length--, !this.LoadQueue[e].Length) {
      for (var _t7 in this.LoadQueue[e].CallBacks) {
        this.LoadQueue[e].CallBacks[_t7] && this.LoadQueue[e].CallBacks[_t7]();
      }

      delete this.LoadQueue[e];
    }
  }, this.ForceReloadGribCache = function (e, t) {
    $.get("/cache/gribtiles/" + t + "&force=yes&seed=0", this.HandleSmartGribData.bind(this, e, t)), this.LoadQueue[e].Length++;
  }, this.ProcessInputGribData = function (e, t, a) {
    var i = t.split("\n"),
        n = i.length,
        o = [];
    if ("--\n" !== t) {
      if (-1 === t.search("invalid")) {
        for (var _e7 = 0; _e7 < n; _e7++) {
          var r = i[_e7];
          if ("--" === r) break;
          o.push(this.ProcessCatalogLine(r));
        }

        if (o.length < this.WindTableLength) this.ForceReloadGribCache(a, e);else {
          var s = e.split("/"),
              l = o.length + 1;

          for (var _t8 = 0; _t8 < o.length; _t8++) {
            if (void 0 === i[l] || "" === i[l]) {
              this.ForceReloadGribCache(a, e);
              break;
            }

            for (var d = i[l].split(" "), u = parseInt(d[0], 10), c = parseInt(d[1], 10), h = 180 / this.GribStep + parseInt(s[1], 10) / this.GribStep, p = 0; p < u; p++) {
              for (var f = c + 90 / this.GribStep + parseInt(s[0], 10) / this.GribStep, P = 0; P < c; P++) {
                o[_t8].DateIndex in this.Tables || (this.Tables[o[_t8].DateIndex] = []);
                var g = this.Tables[o[_t8].DateIndex];
                h + p in g || (g[h + p] = []), f - P - 1 in g[h + p] || (g[h + p][f - P - 1] = null);
                var L = this.Tables[o[_t8].DateIndex][h + p][f - P - 1];
                void 0 !== L && L || (L = new GribData(), this.Tables[o[_t8].DateIndex][h + p][f - P - 1] = L), L[o[_t8].Type] = parseFloat(i[l + 1 + P * u + p]);
              }
            }

            l += u * c + 1;
          }
        }
      } else console.log("invalid request :" + e);
    } else this.ForceReloadGribCache(a, e);
  }, this.ProcessCatalogLine = function (e) {
    var t = new WindCatalogLine(),
        a = e.split(":");
    return t.Type = a[3], void 0 === a[12] || "anl" === a[12] ? t.DateIndex = 0 : t.DateIndex = parseInt(a[12].substring(0, a[12].indexOf("hr")), 10) / 3, t;
  };
}

function WindCatalogLine() {
  this.Type = "", this.DateIndex = 0;
}

function WindTable() {
  this.GribStep = .5, this.Table = [], this.TableDate = 0, this.Init = function (e) {
    for (lat = -90; lat <= 90; lat += this.GribStep) {
      for (lon = -90; lon <= 90; lon += this.GribStep) {
        this.Table[lat][lon] = null;
      }
    }
  };
}

function HandleGribTestClick(e) {
  for (var t = _CurPlayer.CurBoat, a = 0; a <= 0; a++) {
    var i = new Date(1e3 * t.VLMInfo.LUP + a * t.VLMInfo.VAC * 1e3),
        n = GribMgr.WindAtPointInTime(i, t.VLMInfo.LAT, t.VLMInfo.LON);
    n ? console.log(i + " " + n.Speed + "@" + n.Heading) : console.log("no meteo yet at time : " + i);
  }
}

GribMgr.Init();
var RACE_TYPE_CLASSIC = 0,
    RACE_TYPE_RECORD = 1,
    RACE_TYPE_OMORMB = 2,
    FIELD_MAPPING_TEXT = 0,
    FIELD_MAPPING_VALUE = 1,
    FIELD_MAPPING_CHECK = 2,
    FIELD_MAPPING_IMG = 3,
    FIELD_MAPPING_CALLBACK = 4,
    MAX_PILOT_ORDERS = 5,
    BoatRacingStatus = ["RAC", "CST", "LOC", "DNS"],
    BoatArrivedStatus = ["ARR"],
    BoatNotRacingStatus = ["DNF", "HC", "HTP"],
    BoatRacingClasses = {
  RAC: "ft_class_racing",
  CST: "ft_class_oncoast",
  LOC: "ft_class_locked",
  DNS: "ft_class_dns"
},
    GM_Pos = null,
    SetWPPending = !1,
    WPPendingTarget = null,
    GribWindController = null,
    map = null,
    Rankings = [],
    PilototoFt = null,
    RankingFt = null,
    RaceHistFt = null,
    ICS_WPft = null,
    NSZ_WPft = null,
    RC_PwdResetReq = null,
    RC_PwdResetConfirm = null,
    OnPlayerLoadedCallBack = null;
$(document).ready(function () {
  $.ajaxSetup({
    error: function error(e, t, a) {
      401 === e.status || 403 === e.status ? window.location.replace("jvlm?login") : 404 === e.status || VLMAlertDanger("An error occurred: " + t + "nError: " + a);
    }
  }), OLInit(), InitLocale(), InitMenusAndButtons(), PolarsManager.Init(), InitAlerts(), CheckPageParameters(), setInterval(PageClock, 1e3), GetFlagsList();
});
var PasswordResetInfo = [];

function HandlePasswordResetLink(e) {
  PasswordResetInfo = unescape(e).split("|"), initrecaptcha(!1, !0), $("#ResetaPasswordConfirmation").modal("show");
}

function CheckPageParameters() {
  var e = window.location.search,
      t = !0;

  if (e) {
    var a = e.split("?")[1].split("&");

    for (var _e8 in a) {
      if (a[_e8]) {
        (function () {
          var i = a[_e8].split("=");

          switch (i[0]) {
            case "PwdResetKey":
              HandlePasswordResetLink(i[1]);
              break;

            case "RaceRank":
              t = !1, RankingFt.OnReadyTable = function () {
                HandleShowOtherRaceRank(i[1]);
              };
              break;

            case "ICSRace":
              t = !1, HandleShowICS(i[1]);
          }
        })();
      }
    }
  }

  t ? ($(".RaceNavBar").css("display", "inherit"), $(".OffRaceNavBar").css("display", "none")) : ($(".RaceNavBar").css("display", "none"), $(".OffRaceNavBar").css("display", "inherit"), ShowApropos(!1));
}

function HandleShowICS(e) {
  LoadRaceInfo(e, null, function (e) {
    e && (FillRaceInstructions(e), $("#RacesInfoForm").modal("show"));
  });
}

function LoadRaceInfo(e, t, a) {
  t || (t = ""), $.get("/ws/raceinfo/desc.php?idrace=" + e + "&v=" + t, a);
}

function HandleShowOtherRaceRank(e) {
  OnPlayerLoadedCallBack = function OnPlayerLoadedCallBack() {
    LoadRaceInfo(e, 0, function (e) {
      FillRaceInfoHeader(e);
    }), LoadRankings(e, OtherRaceRankingLoaded), RankingFt.RaceRankingId = e;
  }, void 0 !== _CurPlayer && _CurPlayer && _CurPlayer.CurBoat && (OnPlayerLoadedCallBack(), OnPlayerLoadedCallBack = null);
}

function OtherRaceRankingLoaded() {
  $("#Ranking-Panel").show(), SortRanking("RAC"), console.log("off race ranking loaded");
}

function OLInit() {
  OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;
  var e = 4;
  void 0 !== VLM2Prefs && VLM2Prefs.MapPrefs && (e = VLM2Prefs.MapPrefs.MapZoomLevel);
  var t = {
    sphericalMercator: !0,
    transitionEffect: "resize",
    wrapDateLine: !0
  };
  map = new OpenLayers.Map("jVlmMap", MapOptions);
  var a = tilesUrlArray,
      i = new OpenLayers.Layer.XYZ("VLM Layer", a, t),
      n = new Gribmap.Layer("Gribmap", t),
      o = i.clone();
  map.addLayers([n, VLMBoatsLayer, i]), map.addControl(new Gribmap.MousePosition({
    gribmap: n
  })), map.addControl(new OpenLayers.Control.ScaleLine()), map.addControl(new OpenLayers.Control.Permalink("permalink")), map.addControl(new OpenLayers.Control.Graticule()), map.addControl(new OpenLayers.Control.KeyboardDefaults()), GribWindController = new Gribmap.ControlWind(), map.addControl(GribWindController);
  var r = map.getControlsByClass("OpenLayers.Control.Navigation")[0];
  r.handlers.wheel.cumulative = !1, r.handlers.wheel.interval = 100;
  var s = {
    maximized: !0,
    layers: [o]
  };

  if (map.addControl(new OpenLayers.Control.OverviewMap(s)), !map.getCenter()) {
    var l = new OpenLayers.LonLat(-30, 45.5);
    l.transform(MapOptions.displayProjection, MapOptions.projection), map.setCenter(l, e);
  }

  var d = new OpenLayers.Control.Click();
  map.addControl(d), d.activate();
}

function initrecaptcha(e, t) {
  e && !RC_PwdResetReq && (RC_PwdResetReq = grecaptcha.render("recaptcha-PwdReset1")), t && !RC_PwdResetConfirm && (RC_PwdResetConfirm = grecaptcha.render("recaptcha-PwdReset2"));
}

function InitMenusAndButtons() {
  $("div.vresp.modal").on("show.bs.modal", function () {
    $(this).show(), setModalMaxHeight(this);
  }), $(window).resize(function () {
    0 != $(".modal.in").length && setModalMaxHeight($(".modal.in"));
  }), $("#BtnChangePassword").on("click", function (e) {
    e.preventDefault(), HandlePasswordChangeRequest(e);
  }), $("#ResetPasswordButton").on("click", function (e) {
    null !== RC_PwdResetReq && grecaptcha.execute(RC_PwdResetReq);
  }), $("#ConfirmResetPasswordButton").on("click", function (e) {
    null !== RC_PwdResetConfirm && grecaptcha.execute(RC_PwdResetConfirm);
  }), $("#LoginForm").on("show.bs.modal", function (e) {
    ShowApropos(!1);
  }), $("#LoginForm").on("hide.bs.modal", function (e) {
    ShowApropos(!0);
  }), $(".logindlgButton").on("click", function (e) {
    $("#LoginForm").modal("show");
  }), $(".logOutButton").on("click", function (e) {
    Logout();
  }), $("#Menu").menu(), $("#Menu").hide(), $("input[type=submit],button").button().click(function (e) {
    e.preventDefault();
  }), $(".JVLMTabs").tabs(), HidePb("#PbLoginProgress"), HidePb("#PbGetBoatProgress"), HidePb("#PbGribLoginProgress"), $(".BCPane.WP_PM_Mode").click(function () {
    MoveWPBoatControlerDiv("#" + $(this)[0].classList[2]);
  }), $(".BtnRaceList").click(function () {
    LoadRacesList(), $("#RacesListForm").modal("show");
  }), $("#Ranking-Panel").on("shown.bs.collapse", function (e) {
    HandleRaceSortChange(e);
  }), $(document.body).on("click", "[RnkSort]", function (e) {
    HandleRaceSortChange(e);
  }), $("#Ranking-Panel").on("hide.bs.collapse", function (e) {
    ResetRankingWPList(e);
  }), $("#LoginButton").click(function () {
    OnLoginRequest();
  }), $("#LoginPanel").keypress(function (e) {
    "13" === e.which && (OnLoginRequest(), $("#LoginForm").modal("hide"));
  }), $("#BtnSetting").click(function () {
    LoadVLMPrefs(), SetDDTheme(VLM2Prefs.CurTheme), $("#SettingsForm").modal("show");
  }), $("#SettingValidateButton").click(SaveBoatAndUserPrefs), $("#SettingCancelButton").click(function () {
    LoadVLMPrefs(), SetDDTheme(VLM2Prefs.CurTheme), $("#SettingsForm").modal("show");
  }), $("#SettingValidateButton").click(SaveBoatAndUserPrefs), $("#SettingCancelButton").click(function () {
    SetDDTheme(VLM2Prefs.CurTheme);
  }), $("#BtnPM_Heading").click(function () {
    SendVLMBoatOrder(PM_HEADING, $("#PM_Heading")[0].value);
  }), $("#BtnPM_Angle").click(function () {
    SendVLMBoatOrder(PM_ANGLE, $("#PM_Angle")[0].value);
  }), $("#BtnPM_Tack").click(function () {
    $("#PM_Angle")[0].value = -$("#PM_Angle")[0].value;
  }), $("#BtnCreateAccount").click(function () {
    HandleCreateUser();
  }), $(".CreatePassword").pstrength(), $("#NewPlayerEMail").blur(function (e) {
    $("#NewPlayerEMail").verimail({
      messageElement: "#verimailstatus",
      language: _CurLocale
    });
  }), $("#SetWPOnClick").click(HandleStartSetWPOnClick), $("#SetWPOffClick").click(HandleCancelSetWPOnClick), HandleCancelSetWPOnClick(), $("body").on("click", ".PIL_EDIT", HandlePilotEditDelete), $("body").on("click", ".PIL_DELETE", HandlePilotEditDelete), $("#AutoPilotAddButton").click(HandleOpenAutoPilotSetPoint), $("#AP_SetTargetWP").click(HandleClickToSetWP), $("#AP_Time").datetimepicker({
    locale: _CurLocale,
    format: "DD MM YYYY, HH:mm:ss"
  }), $("#AP_Time").on("dp.change", HandleDateChange), $("#APValidateButton").click(HandleSendAPUpdate), $(".APField").on("change", HandleAPFieldChange), $(".APMode").on("click", HandleAPModeDDClick), $(".Draggable").draggable({
    handle: ".modal-header,.modal-body"
  }), $("#MapPrefsToggle").click(HandleShowMapPrefs), $(".chkprefstore").on("change", HandleMapPrefOptionChange), $(".MapOppShowLi").click(HandleMapOppModeChange), $(".DDTheme").click(HandleDDlineClick), $("#StartEstimator").on("click", HandleStartEstimator), $("#EstimatorStopButton").on("click", HandleStopEstimator), InitGribSlider(), InitFootables(), $(document.body).on("click", ".RaceHistLink", function (e) {
    HandleShowBoatRaceHistory(e);
  }), $("[PilRefresh]").on("click", HandleUpdatePilototoTable), $("#HistRankingButton").on("click", function (e) {
    ShowUserRaceHistory(_CurPlayer.CurBoat.IdBoat);
  }), $("#BtnPM_Ortho, #BtnPM_VMG, #BtnPM_VBVMG").click(function () {
    var e,
        t = PM_ORTHO,
        a = $("#PM_Lat")[0].value,
        i = $("#PM_Lon")[0].value;

    switch (e = parseInt($("#PM_WPHeading")[0].value, 10), $(this)[0].id) {
      case "BtnPM_Ortho":
        t = PM_ORTHO;
        break;

      case "BtnPM_VMG":
        t = PM_VMG;
        break;

      case "BtnPM_VBVMG":
        t = PM_VBVMG;
    }

    SendVLMBoatOrder(t, i, a, e);
  }), $("#CalendarPanel").on("shown.bs.modal", function (e) {
    HandleShowAgenda();
  }), $(".BoatSelectorDropDownList").on("click", HandleBoatSelectionChange), $("#cp11").colorpicker({
    useAlpha: !1,
    format: !1
  }), $(document.body).on("click", ".ShowICSButton", function (e) {
    HandleFillICSButton(e);
  }), $("#PolarTab").on("click", HandlePolarTabClik), CheckLogin(), UpdateVersionLine();
}

function UpdateVersionLine() {
  var e = new moment(BuildDate);
  $("#BuildDate").text("Build : " + e.fromNow()), $('[data-toggle="tooltip"]').tooltip();
}

var _CachedRaceInfo = null;

function HandlePolarTabClik() {
  _CachedRaceInfo && DrawPolar(_CachedRaceInfo);
}

function InitPolar(e) {
  _CachedRaceInfo = e;
}

function HandleFillICSButton(e) {
  if (void 0 !== _CurPlayer && _CurPlayer && _CurPlayer.CurBoat && _CurPlayer.CurBoat.RaceInfo) FillRaceInstructions(_CurPlayer.CurBoat.RaceInfo);else if (void 0 !== e && e) {
    e.target;
    var t = $(e.currentTarget).attr("idRace");
    void 0 !== t && t && HandleShowICS(t);
  }
}

var CalInited = !1;

function HandleShowAgenda() {
  jQuery("#Calendar").fullCalendar("destroy"), jQuery("#Calendar").fullCalendar({
    locale: _CurLocale,
    editable: !1,
    header: {
      left: "title",
      center: "",
      right: "today prev,next"
    },
    firstDay: 1,
    events: "/feed/races.fullcalendar.php",
    data: function data() {
      return {
        jvlm: 1
      };
    },
    timeFormat: "H:mm",
    loading: function loading(e) {
      e ? jQuery("#loading").show() : jQuery("#loading").hide();
    }
  }), CalInited = !0, $("#Infos").modal("hide");
}

function HandlePasswordChangeRequest(e) {
  var t = $("#CurPassword")[0].value,
      a = $("#NewPassword1")[0].value,
      i = $("#NewPassword2")[0].value;
  if ($(".Password").val(""), !t || "" === t) return void VLMAlertDanger(GetLocalizedString("CurPwdRequired"));
  if (a !== i) return void VLMAlertDanger(GetLocalizedString("CurPwdRequired"));
  if ("" === a) return void VLMAlertDanger(GetLocalizedString("NewPwdRequired"));
  var n = {
    OldPwd: t,
    NewPwd: a
  };
  $.post("/ws/playersetup/password_change.php", "parms=" + JSON.stringify(n), function (e) {
    HandlePasswordChangeResult(e);
  });
}

function HandlePasswordChangeResult(e) {
  e.success ? VLMAlertInfo() : VLMAlertDanger(GetLocalizedString(e.error.msg));
}

function SendResetPassword(e) {
  PasswordResetInfo[0], PasswordResetInfo[1];
  $.get("/ws/playersetup/password_reset.php?email=" + PasswordResetInfo[0] + "&seed=" + PasswordResetInfo[1] + "&key=" + e, function (e) {
    HandlePasswordReset(e, !0);
  });
}

function SendResetPasswordLink(e) {
  var t = $(".UserName").val();
  if ("" === t) return VLMAlertDanger(GetLocalizedString("Enter your email for resetting your password")), void grecaptcha.reset(RC_PwdResetReq);
  var a = {
    email: t,
    key: e
  };
  $.post("/ws/playersetup/password_reset.php", "parms=" + JSON.stringify(a), function (e) {
    HandlePasswordReset(e, !1);
  });
}

function HandlePasswordReset(e, t) {
  e.success ? t ? (VLMAlertInfo(GetLocalizedString("Check your inbox to get your new password.")), grecaptcha.reset(RC_PwdResetReq)) : (VLMAlertInfo(GetLocalizedString("An email has been sent. Click on the link to validate.")), grecaptcha.reset(RC_PwdResetConfirm)) : (VLMAlertDanger("Something went wrong :("), grecaptcha.reset(RC_PwdResetReq), grecaptcha.reset(RC_PwdResetConfirm));
}

function InitFooTable(e) {
  var t = FooTable.init("#" + e, {
    name: e,
    on: {
      "ready.ft.table": HandleReadyTable,
      "after.ft.paging": HandlePagingComplete,
      "postdraw.ft.table": HandleTableDrawComplete
    }
  });
  return t.DrawPending = !0, t.CallbackPending = null, t;
}

function InitFootables() {
  $("#DiscontinueRaceButton").on("click", HandleDiscontinueRaceRequest), PilototoFt = InitFooTable("PilototoTable"), RankingFt = InitFooTable("RankingTable"), RaceHistFt = InitFooTable("BoatRaceHist"), ICS_WPft = InitFooTable("RaceWayPoints"), NSZ_WPft = InitFooTable("NSZPoints");
}

function HandleUpdatePilototoTable(e) {
  UpdatePilotInfo(_CurPlayer.CurBoat);
}

function InitSlider(e, t, a, i, n, o) {
  var r = $("#" + t);
  $("#" + e).slider({
    orientation: "vertical",
    min: a,
    max: i,
    value: n,
    create: function create() {
      r.text($(this).slider("value"));
    },
    slide: function slide(e, t) {
      o(e, t);
    }
  });
}

function InitGribSlider() {
  InitSlider("GribSlider", "GribSliderHandle", 0, 72, 0, HandleGribSlideMove);
}

function HandleRaceSortChange(e) {
  var t = $(e.currentTarget).attr("rnksort");

  switch (t) {
    case "WP":
      SortRanking(t, $(e.currentTarget).attr("WPRnk"));
      break;

    case "DNF":
    case "HTP":
    case "HC":
    case "ABD":
    case "RAC":
    case "ARR":
      SortRanking(t);
      break;

    default:
      console.log("Sort change request" + e);
  }
}

function HandleGribSlideMove(e, t) {
  $("#GribSliderHandle").text(t.value);
  var a = GribWindController.getGribmapLayer(),
      i = new Date().getTime();

  if (a.setTimeSegment(i / 1e3 + 3600 * t.value), VLM2Prefs.MapPrefs.TrackEstForecast && _CurPlayer.CurBoat.Estimator) {
    RefreshEstPosLabels(_CurPlayer.CurBoat.GetClosestEstimatePoint(new Date(i + 3600 * t.value * 1e3)));
  }
}

function HandleDiscontinueRaceRequest() {
  GetUserConfirmation(GetLocalizedString("unsubscribe"), !0, HandleRaceDisContinueConfirmation);
}

function HandleRaceDisContinueConfirmation(e) {
  if (e) {
    DiconstinueRace(_CurPlayer.CurBoat.IdBoat, _CurPlayer.CurBoat.Engaged), $("#ConfirmDialog").modal("hide"), $("#RacesInfoForm").modal("hide");
  } else VLMAlertDanger("Ouf!");
}

function HandleStopEstimator(e) {
  var t = _CurPlayer.CurBoat;
  void 0 !== t && t && t.Estimator.Stop();
}

function HandleStartEstimator(e) {
  var t = _CurPlayer.CurBoat;
  void 0 !== t && t && t.Estimator.Start(HandleEstimatorProgress);
}

var LastPct = -1;

function HandleEstimatorProgress(e, t, a) {
  e ? ($("#StartEstimator").removeClass("hidden"), $("#PbEstimatorProgressBar").addClass("hidden"), $("#EstimatorStopButton").addClass("hidden"), LastPct = -1) : t - LastPct > .15 && ($("#EstimatorStopButton").removeClass("hidden"), $("#StartEstimator").addClass("hidden"), $("#PbEstimatorProgressBar").removeClass("hidden"), $("#PbEstimatorProgressText").removeClass("hidden"), $("#PbEstimatorProgressText").text(t), $("#PbEstimatorProgress").css("width", t + "%"), $("#PbEstimatorProgress").attr("aria-valuenow", t), $("#PbEstimatorProgress").attr("aria-valuetext", t), LastPct = t);
}

function HandleFlagLineClick(e) {
  SelectCountryDDFlag(e.target.attributes.flag.value);
}

function HandleCancelSetWPOnClick() {
  SetWPPending = !1, $("#SetWPOnClick").show(), $("#SetWPOffClick").hide();
}

function HandleStartSetWPOnClick() {
  SetWPPending = !0, WPPendingTarget = "WP", $("#SetWPOnClick").hide(), $("#SetWPOffClick").show();
}

function ClearBoatSelector() {
  $(".BoatSelectorDropDownList").empty();
}

function AddBoatToSelector(e, t) {
  BuildUserBoatList(e, t);
}

function BuildUserBoatList(e, t) {
  $(".BoatSelectorDropDownList").append(GetBoatDDLine(e, t));
}

function GetBoatDDLine(e, t) {
  var a = '<li class="DDLine" BoatID="' + e.IdBoat + '">';
  return a = a + GetBoatInfoLine(e, t) + "</li>";
}

function GetBoatInfoLine(e, t) {
  var a = "",
      i = "racing";
  return e.Engaged || (i = "Docked"), void 0 !== e.VLMInfo && e.VLMInfo["S&G"] && (i = "stranded"), t || (a += '<span class="badge">BS'), a = a + '<img class="BoatStatusIcon" src="images/' + i + '.png" />', t || (a += "</span>"), a = a + "<span>-</span><span>" + HTMLDecode(e.BoatName) + "</span>";
}

function ShowBgLoad() {
  $("#BgLoadProgress").css("display", "block");
}

function HideBgLoad() {
  $("#BgLoadProgress").css("display", "block");
}

function ShowPb(e) {
  $(e).show();
}

function HidePb(e) {
  $(e).hide();
}

function DisplayLoggedInMenus(e) {
  var t, a;
  e ? (t = "block", a = "none") : (t = "none", a = "block"), $("[LoggedInNav='true']").css("display", t), $("[LoggedInNav='false']").css("display", a), void 0 !== _CurPlayer && _CurPlayer && _CurPlayer.IsAdmin ? $("[AdminNav='true']").css("display", "block") : $("[AdminNav='true']").css("display", "none"), ShowApropos(e);
}

function ShowApropos(e) {
  $("#Apropos").modal(e ? "hide" : "show");
}

function HandleRacingDockingButtons(e) {
  e ? ($('[RacingBtn="true"]').removeClass("hidden"), $('[RacingBtn="false"]').addClass("hidden")) : ($('[RacingBtn="true"]').addClass("hidden"), $('[RacingBtn="false"]').removeClass("hidden"));
}

function UpdateInMenuDockingBoatInfo(e) {
  HandleRacingDockingButtons(void 0 !== e && void 0 !== e.VLMInfo && parseInt(e.VLMInfo.RAC, 10));
}

function SetTWASign(e) {
  var t = e.VLMInfo.TWD,
      a = e.VLMInfo.HDG,
      i = t - a;
  i < -180 && (i += 360), i > 180 && (i -= 360);
  i * e.VLMInfo.TWA > 0 && (e.VLMInfo.TWA = -e.VLMInfo.TWA);
}

function UpdateInMenuRacingBoatInfo(e, t) {
  if (!e || void 0 === e) return;
  HandleRacingDockingButtons(!0), SetTWASign(e), "2" === e.VLMInfo.PIM && "0" === e.VLMInfo.PIP && (e.VLMInfo.HDG = e.VLMInfo.TWD, e.VLMInfo.BSP = 0);
  var a = new Coords(e.VLMInfo.LON, !0),
      i = new Coords(e.VLMInfo.LAT),
      n = [];
  n.push([FIELD_MAPPING_TEXT, "#BoatLon", a.ToString()]), n.push([FIELD_MAPPING_TEXT, "#BoatLat", i.ToString()]), n.push([FIELD_MAPPING_TEXT, ".BoatSpeed", RoundPow(e.VLMInfo.BSP, 2)]), n.push([FIELD_MAPPING_TEXT, ".BoatHeading", RoundPow(e.VLMInfo.HDG, 1)]), n.push([FIELD_MAPPING_VALUE, "#PM_Heading", RoundPow(e.VLMInfo.HDG, 2)]), n.push([FIELD_MAPPING_TEXT, "#BoatAvg", RoundPow(e.VLMInfo.AVG, 1)]), n.push([FIELD_MAPPING_TEXT, "#BoatDNM", RoundPow(e.VLMInfo.DNM, 1)]), n.push([FIELD_MAPPING_TEXT, "#BoatLoch", RoundPow(e.VLMInfo.LOC, 1)]), n.push([FIELD_MAPPING_TEXT, "#BoatOrtho", RoundPow(e.VLMInfo.ORT, 1)]), n.push([FIELD_MAPPING_TEXT, "#BoatLoxo", RoundPow(e.VLMInfo.LOX, 1)]), n.push([FIELD_MAPPING_TEXT, "#BoatVMG", RoundPow(e.VLMInfo.VMG, 1)]), n.push([FIELD_MAPPING_TEXT, ".BoatWindSpeed", RoundPow(e.VLMInfo.TWS, 1)]), n.push([FIELD_MAPPING_TEXT, "#BoatWindDirection", RoundPow(e.VLMInfo.TWD, 1)]), n.push([FIELD_MAPPING_CHECK, "#PM_WithWPHeading", "-1.0" !== e.VLMInfo["H@WP"]]), n.push([FIELD_MAPPING_TEXT, "#RankingBadge", e.VLMInfo.RNK]), n.push([FIELD_MAPPING_VALUE, "#PM_WPHeading", e.VLMInfo["H@WP"]]), n.push([FIELD_MAPPING_TEXT, ".BoatClass", e.VLMInfo.POL.substring(5)]), n.push([FIELD_MAPPING_TEXT, ".RaceName", e.VLMInfo.RAN]);
  var o = new VLMPosition(e.VLMInfo.WPLON, e.VLMInfo.WPLAT);
  n.push([FIELD_MAPPING_VALUE, "#PM_Lat", o.Lat.Value]), n.push([FIELD_MAPPING_VALUE, "#PM_Lon", o.Lon.Value]), 0 === o.Lon.Value && 0 === o.Lat.Value && (o = e.GetNextWPPosition()), void 0 !== o && o ? (n.push([FIELD_MAPPING_TEXT, "#PM_CurWPLat", o.Lat.ToString()]), n.push([FIELD_MAPPING_TEXT, "#PM_CurWPLon", o.Lon.ToString()])) : (n.push([FIELD_MAPPING_TEXT, "#PM_CurWPLat", "N/A"]), n.push([FIELD_MAPPING_TEXT, "#PM_CurWPLon", "N/A"])), parseInt(e.VLMInfo.PIM, 10) === PM_ANGLE ? (n.push([FIELD_MAPPING_TEXT, ".BoatWindAngle", RoundPow(Math.abs(e.VLMInfo.PIP), 1)]), n.push([FIELD_MAPPING_VALUE, "#PM_Angle", e.VLMInfo.PIP])) : (n.push([FIELD_MAPPING_TEXT, ".BoatWindAngle", RoundPow(Math.abs(e.VLMInfo.TWA), 1)]), n.push([FIELD_MAPPING_VALUE, "#PM_Angle", RoundPow(e.VLMInfo.TWA, 1)])), FillFieldsFromMappingTable(n);
  var r = "lime";
  e.VLMInfo.TWA > 0 && (r = "red"), $(".BoatWindAngle").css("color", r);
  var s = Math.round(100 * (e.VLMInfo.TWD + 180)) / 100,
      l = (Math.round(100 * e.VLMInfo.TWS), e.VLMInfo.POL),
      d = Math.round(100 * e.VLMInfo.HDG) / 100,
      u = Math.round(100 * e.VLMInfo.TWS) / 100,
      c = Math.round(100 * e.VLMInfo.ORT) / 100;
  $("#ImgWindAngle").attr("src", "windangle.php?wheading=" + s + "&boatheading=" + d + "&wspeed=" + u + "&roadtoend=" + c + "&boattype=" + l + "&jvlm=" + e.VLMInfo.NOW), $("#ImgWindAngle").css("transform", "rotate(" + s + "deg)"), $("#DeckImage").css("transform", "rotate(" + d + "deg)"), $(".PMActiveMode").css("display", "none"), $(".BCPane").removeClass("active");
  var h = ".ActiveMode_",
      p = "";

  switch (e.VLMInfo.PIM) {
    case "1":
      h += "Heading", p = "BearingMode";
      break;

    case "2":
      h += "Angle", p = "AngleMode";
      break;

    case "3":
      h += "Ortho", p = "OrthoMode";
      break;

    case "4":
      h += "VMG", p = "VMGMode";
      break;

    case "5":
      h += "VBVMG", p = "VBVMGMode";
      break;

    default:
      VLMAlert("Unsupported VLM PIM Mode, expect the unexpected....", "alert-info");
  }

  $(h).css("display", "inline"), $("." + p).addClass("active"), $("#" + p).addClass("active"), UpdatePilotInfo(e), UpdatePolarImages(e);
}

function FillFieldsFromMappingTable(e) {
  for (var t in e) {
    if (e[t]) switch (e[t][0]) {
      case FIELD_MAPPING_TEXT:
        $(e[t][1]).text(e[t][2]);
        break;

      case FIELD_MAPPING_VALUE:
        $(e[t][1]).val(e[t][2]);
        break;

      case FIELD_MAPPING_CHECK:
        $(e[t][1]).prop("checked", e[t][2]);
        break;

      case FIELD_MAPPING_IMG:
        $(e[t][1]).attr("src", e[t][2]);
        break;

      case FIELD_MAPPING_CALLBACK:
        e[t][2](e[t][1]);
    }
  }
}

function FillRaceInstructions(e) {
  if (void 0 === e || !e) return;
  var t = !0;
  void 0 !== _CurPlayer && _CurPlayer && _CurPlayer.CurBoat && _CurPlayer.CurBoat.RaceInfo && (t = _CurPlayer.CurBoat.RaceInfo.idraces !== e.idraces), t ? $("#DiscontinueRaceTab").addClass("hidden") : $("#DiscontinueRaceTab").removeClass("hidden");
  FillRaceInfoHeader(e), FillRaceWaypointList(e), InitPolar(e), $.get("/ws/raceinfo/exclusions.php?idr=" + e.idraces + "&v=" + e.VER, function (e) {
    e && e.success && FillNSZList(e.Exclusions);
  });
}

var PolarSliderInited = !1;

function FillRaceInfoHeader(e) {
  if (void 0 === e || !e) return;
  var t = [];
  t.push([FIELD_MAPPING_TEXT, ".ICSRaceName", e.racename]), t.push([FIELD_MAPPING_TEXT, ".RaceId", e.idraces]), t.push([FIELD_MAPPING_TEXT, ".BoatType", e.boattype.substring(5)]), t.push([FIELD_MAPPING_TEXT, ".VacFreq", parseInt(e.vacfreq, 10)]), t.push([FIELD_MAPPING_TEXT, "#EndRace", parseInt(e.firstpcttime, 10)]), t.push([FIELD_MAPPING_TEXT, "#RaceStartDate", GetLocalUTCTime(1e3 * parseInt(e.deptime, 10), !0, !0)]), t.push([FIELD_MAPPING_TEXT, "#RaceLineClose", GetLocalUTCTime(1e3 * parseInt(e.closetime, 10), !0, !0)]), t.push([FIELD_MAPPING_IMG, "#RaceImageMap", "/cache/racemaps/" + e.idraces + ".png"]), FillFieldsFromMappingTable(t);
}

function HandlePolarSpeedSlide(e, t, a) {
  $("#PolarSpeedHandle").text(t.value), DrawPolar(a);
}

function DrawPolar(e) {
  var t = $("#PolarCanvas")[0],
      a = 25;
  PolarSliderInited && (a = parseFloat($("#PolarSpeedHandle").text()));
  var i = PolarsManager.GetPolarLine(e.boattype, a, function () {
    DrawPolar(e);
  }, null, 1);

  if (i) {
    PolarSliderInited || (InitSlider("PolarSpeedSlider", "PolarSpeedHandle", 0, 60, a, function (t, a) {
      HandlePolarSpeedSlide(t, a, e);
    }), PolarSliderInited = !0), t.width = $("#PolarCanvas").width(), t.height = t.width;

    var n,
        o,
        r = t.getContext("2d"),
        s = !0,
        _l = Math.PI / i.length,
        d = 3,
        u = t.width / 2,
        c = t.width / 2,
        h = PolarsManager.GetPolarMaxSpeed(e.boattype, a),
        p = 0,
        f = 0,
        P = !0;

    r.beginPath(), r.lineWidth = "1", r.strokeStyle = "#FF0000";

    for (var _e9 in i) {
      if (i[_e9]) {
        var _t9 = i[_e9],
            _a4 = (_e9 = parseInt(_e9, 10)) * _l,
            _h = u + c * _t9 * Math.cos(_a4),
            _g = d + c * _t9 * Math.sin(_a4),
            L = Math.cos(_a4 + f) * _t9;

        P && L <= p ? (r.stroke(), r.beginPath(), r.moveTo(n, o), r.strokeStyle = "#FFFFFF", P = !1) : !P && L >= p && (r.stroke(), r.beginPath(), r.moveTo(n, o), r.strokeStyle = "#FF0000", P = !0), p = L, s ? (r.moveTo(_g, _h), s = !1) : r.lineTo(_g, _h), n = _g, o = _h;
      }
    }

    r.stroke(), r.beginPath(), r.lineWidth = "1", r.strokeStyle = "#00FF00", r.moveTo(d, 0), r.lineTo(d, t.height), r.stroke(), r.moveTo(d - 1, t.height / 2), r.lineTo(d + t.width, t.height / 2), r.stroke();
    var g = Math.round(h / 5);

    for (g || (g = 1), index = 1; g * index - 1 <= h; index++) {
      r.beginPath(), r.strokeStyle = "#7FFFFF", r.arc(d, u, c * index * g / h, Math.PI / 2, 1.5 * Math.PI, !0), r.stroke(), r.strokeText(" " + g * index, d + 1 + g * c * index / h, u + 10);
    }
  }
}

function UpdatePolarImages(e) {
  var t,
      a = e.VLMInfo.POL.substring(5),
      i = "";

  for (t = 0; t <= 45; t += 15) {
    i += '<li><img class="polaire" src="/scaledspeedchart.php?boattype=boat_' + a + "&amp;minws=" + t + "&amp;maxws=" + (t + 15) + '&amp;pas=2" alt="speedchart"></li>';
  }

  $("#PolarList").empty(), $("#PolarList").append(i);
}

function BackupFooTable(e, t, a) {
  e.DOMBackup ? void 0 === $(t)[0] && ($(e.RestoreId).append(e.DOMBackup), console.log("Restored footable " + t)) : (e.DOMBackup = $(t), e.RestoreId = a);
}

function UpdatePilotInfo(e) {
  if (void 0 === e || !e || PilototoFt.DrawPending) return;
  BackupFooTable(PilototoFt, "#PilototoTable", "#PilototoTableInsertPoint");
  var t = [];

  if (e && e.VLMInfo && e.VLMInfo.PIL && e.VLMInfo.PIL.length > 0) {
    for (var i in e.VLMInfo.PIL) {
      if (e.VLMInfo.PIL[i]) {
        var a = GetPilototoTableLigneObject(e, i);
        t.push(a);
      }
    }

    e.VLMInfo.PIL.length < MAX_PILOT_ORDERS ? $("#AutoPilotAddButton").removeClass("hidden") : $("#AutoPilotAddButton").addClass("hidden");
  }

  PilototoFt.DrawPending = !0, PilototoFt.loadRows(t, !1), console.log("loaded pilototo table"), UpdatePilotBadge(e);
}

function HandleReadyTable(e, t) {
  console.log("Table ready" + t), t.DrawPending = !1, t.OnReadyTable && t.OnReadyTable();
}

function HandlePagingComplete(e, t) {
  var a,
      i = {
    ft_class_myboat: "rnk-myboat",
    ft_class_friend: "rnk-friend",
    ft_class_oncoast: "rnk-oncoast",
    ft_class_racing: "rnk-racing",
    ft_class_locked: "rnk-locked",
    ft_class_dns: "rnk-dns"
  };

  for (var _e10 in i) {
    i[_e10] && $("td").closest("tr").removeClass(i[_e10]);
  }

  for (a in i) {
    i[a] && $('td:contains("' + a + '")').closest("tr").addClass(i[a]);
  }

  t.DrawPending = !1;
}

function HandleTableDrawComplete(e, t) {
  if (console.log("TableDrawComplete " + t.id), t.DrawPending = !1, t === RankingFt) setTimeout(function () {
    DeferedGotoPage(e, t);
  }, 500);else if (t.CallbackPending) return void setTimeout(function () {
    t.CallbackPending(), t.CallbackPending = null;
  }, 500);
}

function DeferedGotoPage(e, t) {
  RankingFt.TargetPage && (RankingFt.gotoPage(RankingFt.TargetPage), RankingFt.TargetPage = 0), setTimeout(function () {
    DeferedPagingStyle(e, t);
  }, 200);
}

function DeferedPagingStyle(e, t) {
  HandlePagingComplete(e, t);
}

function GetPilototoTableLigneObject(e, t) {
  var a = e.VLMInfo.PIL[t],
      i = GetLocalUTCTime(1e3 * a.TTS, !0, !0),
      n = GetPilotModeName(a.PIM);
  return t = parseInt(t, 10) + 1, $("#EditCellTemplate .PIL_EDIT").attr("pil_id", t), $("#DeleteCellTemplate .PIL_DELETE").attr("TID", a.TID).attr("pil_id", t), {
    date: i,
    PIM: n,
    PIP: a.PIP,
    Status: a.STS,
    Edit: $("#EditCellTemplate").first().html(),
    Delete: $("#DeleteCellTemplate").first().html()
  };
}

function ShowAutoPilotLine(e, t) {
  var a = "#PIL" + t,
      i = e.VLMInfo.PIL[t - 1],
      n = new Date(1e3 * i.TTS),
      o = GetPilotModeName(i.PIM);
  if (void 0 === $(a)[0]) ;
  $(a)[0].attributes.TID = i.TID, SetSubItemValue(a, "#PIL_DATE", n), SetSubItemValue(a, "#PIL_PIM", o), SetSubItemValue(a, "#PIL_PIP", i.PIP), SetSubItemValue(a, "#PIL_STATUS", i.STS), $(a).show();
}

function GetPILIdParentElement(e) {
  for (var t = e;;) {
    if (void 0 === t) return;

    if ("id" in t.attributes) {
      var a = t.attributes.id.value;
      if (4 === a.length && "PIL" === a.substring(0, 3)) return t;
    }

    t = t.parentElement;
  }
}

function HandlePilotEditDelete(e) {
  var t = $(this)[0],
      a = t.attributes.class.value,
      i = _CurPlayer.CurBoat;
  parseInt(t.attributes.pil_id.value, 10);
  "PIL_EDIT" === a ? HandleOpenAutoPilotSetPoint(e) : "PIL_DELETE" === a && DeletePilotOrder(i, t.attributes.TID.value);
}

function GetPilotModeName(e) {
  switch (parseInt(e, 10)) {
    case 1:
      return GetLocalizedString("autopilotengaged");

    case 2:
      return GetLocalizedString("constantengaged");

    case 3:
      return GetLocalizedString("orthoengaged");

    case 4:
      return GetLocalizedString("bestvmgengaged");

    case 5:
      return GetLocalizedString("vbvmgengaged");

    default:
      return "PIM ???" + e + "???";
  }
}

function SetSubItemValue(e, t, a) {
  var i = $(e).find(t);
  i.length > 0 && i.text(a);
}

function UpdatePilotBadge(e) {
  var t,
      a = 0;

  if (void 0 !== e && e) {
    var i = e.VLMInfo.PIL;
    if (i.length) for (t in i) {
      "pending" === i[t].STS && a++;
    }
    a > 0 ? ($(".PilotOrdersBadge").show(), $(".PilotOrdersBadge").text(a)) : $(".PilotOrdersBadge").hide();
  }
}

function MoveWPBoatControlerDiv(e) {
  $(e).prepend($("#PM_WPMode_Div"));
}

function UpdatePrefsDialog(e) {
  if (void 0 === e) $("#BtnSetting").addClass("hidden");else if ($("#BtnSetting").removeClass("hidden"), $("#pref_boatname").val(e.BoatName), void 0 !== e.VLMInfo) {
    SelectCountryDDFlag(e.VLMInfo.CNT);
    var t = SafeHTMLColor(e.VLMInfo.COL);
    $("#pref_boatcolor").val(t), $("#cp11").colorpicker({
      useAlpha: !1,
      format: !1,
      color: t
    });
  }
}

var RaceSorter = function RaceSorter(e, t) {
  return e.CanJoin === t.CanJoin ? e.deptime > t.deptime ? -1 : e.deptime === t.deptime ? e.racename > t.racename ? 1 : e.racename === t.racename ? 0 : -1 : 1 : e.CanJoin ? 1 : -1;
};

function LoadRacesList() {
  var e = _CurPlayer.CurBoat.IdBoat;
  $.get("/ws/raceinfo/list.php?iduser=" + e, function (e) {
    var t = e;
    $("#RaceListPanel").empty();
    var a = [];

    for (var _e11 in t) {
      t[_e11] && a.push(t[_e11]);
    }

    a.sort(RaceSorter);

    for (var _e12 in a) {
      a[_e12] && AddRaceToList(a[_e12]);
    }
  });
}

function AddRaceToList(e) {
  var t,
      a,
      i = $("#RaceListPanel").first();
  new Date(0);

  if (e.CanJoin) {
    var _i2 = new Date();

    new Date(1e3 * e.deptime) <= _i2 ? (t = "CanJoinRace", a = GetLocalizedString("closerace") + " " + moment("/date(" + 1e3 * e.closetime + ")/").fromNow()) : (t = "CanJoinRaceNotStarted", a = GetLocalizedString("departuredate") + " " + moment("/date(" + 1e3 * e.deptime + ")/").fromNow());
  } else t = "NoJoinRace";

  var n = '<div class="raceheaderline panel panel-default ' + t + '" )>  <div data-toggle="collapse" href="#RaceDescription' + e.idraces + '" class="panel-body collapsed " data-parent="#RaceListPanel" aria-expanded="false">    <div class="col-xs-4">      <img class="racelistminimap" src="/cache/minimaps/' + e.idraces + '.png" ></img>    </div>    <div class="col-xs-4">      <span ">' + e.racename + '      </span>    </div>    <div class="' + (e.CanJoin ? "" : "hidden") + ' col-xs-4">      <button id="JoinRaceButton" type="button" class="btn-default btn-md" IdRace="' + e.idraces + '"  >' + GetLocalizedString("subscribe") + "      </button>    </div>" + (a ? '    <div class="col-xs-12">       <span "> ' + a + "       </span>    </div>" : "") + '  <div id="RaceDescription' + e.idraces + '" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">  <div class="panel-body">   <div class="col-xs-12"><img class="img-responsive" src="/cache/racemaps/' + e.idraces + '.png" width="530px"></div>    <div class="col-xs-9"><p>' + GetLocalizedString("race") + " : " + e.racename + "</p>     <p>Départ : " + GetLocalUTCTime(1e3 * e.deptime, !0, !0) + "</p>     <p>" + GetLocalizedString("boattype") + " : " + e.boattype.substring(5) + "</p>     <p>" + GetLocalizedString("crank") + " : " + e.vacfreq + "'</p>     <p>" + GetLocalizedString("closerace") + GetLocalUTCTime(1e3 * e.closetime, !0, !0) + '</p>    </div>    <div class="col-xs-3"><p>     <div class="col-xs-12">      <button type="button" class="ShowICSButton btn-default btn-md" IdRace="' + e.idraces + '"  >' + GetLocalizedString("ic") + '     </div>     <div class="col-xs-12 hidden">      <button type="button" class="ShowRankingButton btn-default btn-md" IdRace="' + e.idraces + '"  >' + GetLocalizedString("ranking") + "     </div>    </div>   </div>  </div>";
  i.prepend(n), $("#JoinRaceButton").click(function (e) {
    EngageBoatInRace(e.currentTarget.attributes.idrace.value, _CurPlayer.CurBoat.IdBoat);
  });
}

function PageClock() {
  if (void 0 !== _CurPlayer && _CurPlayer && void 0 !== _CurPlayer.CurBoat) {
    var e = _CurPlayer.CurBoat;

    if (void 0 !== e && void 0 !== e.RaceInfo) {
      var t = GetRaceClock(e.RaceInfo, e.VLMInfo.UDT),
          a = $(".RaceChrono");
      t < 0 ? a.removeClass("ChronoRaceStarted").addClass("ChronoRacePending") : a.addClass("ChronoRaceStarted").removeClass("ChronoRacePending"), $("#RefreshAge").text(moment(_CurPlayer.CurBoat.LastRefresh).fromNow());
      var i = new Date(1e3 * e.VLMInfo.LUP),
          n = e.VLMInfo.VAC,
          o = n - (new Date() - i) / 1e3 % n;
      o >= n - 1 && 100, $("#pbar_innerdivvac").css("width", +Math.round(o % 60 * 100 / 60) + "px"), $("#pbar_innerdivmin").css("width", Math.round(o / n * 100) + "px"), a.text(GetFormattedChronoString(t));
    }
  }
}

function GetRaceClock(e, t) {
  var a = new Date(),
      i = new Date(1e3 * e.deptime);

  if (e.racetype & RACE_TYPE_RECORD) {
    var n = parseInt(t, 10);
    if (-1 === n) return 0;
    var o = new Date(1e3 * n);
    return Math.floor((a - o) / 1e3);
  }

  return Math.floor((a - i) / 1e3);
}

function DisplayCurrentDDSelectedBoat(e) {
  $(".BoatDropDown:first-child").html("<span BoatID=" + e.IdBoat + ">" + GetBoatInfoLine(e, e.IdBoat in _CurPlayer.Fleet) + '</span><span class="caret"></span>');
}

function PadLeftZero(e) {
  return e < 100 ? ("00" + e).slice(-2) : e;
}

function GetFormattedChronoString(e) {
  if (e < 0) e = -e;else if (0 === e) return "--:--:--";
  var t = PadLeftZero(e % 60),
      a = PadLeftZero(Math.floor(e / 60) % 60),
      i = PadLeftZero(Math.floor(e / 3600) % 24),
      n = PadLeftZero(Math.floor(e / 3600 / 24)),
      o = i.toString() + ":" + a.toString() + ":" + t.toString();
  return n > 0 && (o = n.toString() + " d " + o), o;
}

function RefreshCurrentBoat(e, t, a) {
  var i = $(".BoatDropDown > span");

  if (void 0 !== i && void 0 !== i[0] && ("BoatId" in i[0].attributes || "boatid" in i[0].attributes)) {
    SetCurrentBoat(GetBoatFromIdu(i[0].attributes.BoatID.value), e, t, a);
  }
}

function UpdateLngDropDown() {
  var e = GetCurrentLocale();
  $("#SelectionLanguageDropDown:first-child").html('<img class=" LngFlag" lang="' + e + '" src="images/lng-' + e + '.png" alt="' + e + '"><span class="caret"></span>');
}

var _CurAPOrder = null;

function HandleOpenAutoPilotSetPoint(e) {
  var t,
      a = e.target;
  if ("id" in a.attributes) t = a.attributes.id.nodeValue;else {
    if (!("class" in a.attributes)) return void VLMAlert("Something bad has happened reload this page....", "alert-danger");
    t = a.attributes.class.nodeValue;
  }

  switch (t) {
    case "AutoPilotAddButton":
      _CurAPOrder = new AutoPilotOrder();
      break;

    case "PIL_EDIT":
      var i = parseInt(a.attributes.pil_id.value, 10);
      _CurAPOrder = new AutoPilotOrder(_CurPlayer.CurBoat, i), $("#AutoPilotSettingForm").modal("show");
      break;

    default:
      return void VLMalert("Something bad has happened reload this page....", "alert-danger");
  }

  RefreshAPDialogFields();
}

function RefreshAPDialogFields() {
  $("#AP_Time").data("DateTimePicker").date(_CurAPOrder.Date), $("#AP_PIM:first-child").html("<span>" + _CurAPOrder.GetPIMString() + '</span><span class="caret"></span>'), $("#AP_PIP").val(_CurAPOrder.PIP_Value), $("#AP_WPLat").val(_CurAPOrder.PIP_Coords.Lat.Value), $("#AP_WPLon").val(_CurAPOrder.PIP_Coords.Lon.Value), $("#AP_WPAt").val(_CurAPOrder.PIP_WPAngle), UpdatePIPFields(_CurAPOrder.PIM);
}

function HandleDateChange(e) {
  _CurAPOrder.Date = e.date;
}

function HandleClickToSetWP() {
  SetWPPending = !0, WPPendingTarget = "AP", $("#AutoPilotSettingForm").modal("hide");
}

function HandleAPModeDDClick(e) {
  var t = e.target.attributes.PIM.value;
  _CurAPOrder.PIM = parseInt(t, 10), $("#AP_PIM:first-child").html("<span>" + _CurAPOrder.GetPIMString() + '</span><span class="caret"></span>'), UpdatePIPFields(_CurAPOrder.PIM);
}

function UpdatePIPFields(e) {
  var t = !0;

  switch (e) {
    case PM_HEADING:
    case PM_ANGLE:
      t = !0;
      break;

    case PM_ORTHO:
    case PM_VMG:
    case PM_VBVMG:
      t = !1;
  }

  t ? ($(".AP_PIPRow").removeClass("hidden"), $(".AP_WPRow").addClass("hidden")) : ($(".AP_PIPRow").addClass("hidden"), $(".AP_WPRow").removeClass("hidden"));
}

function SaveBoatAndUserPrefs(e) {
  var t = {},
      a = !1,
      i = $("#SelectionThemeDropDown").attr("SelTheme");
  void 0 !== i && (VLM2Prefs.CurTheme = i), VLM2Prefs.Save(), ComparePrefString($("#pref_boatname")[0].value, _CurPlayer.CurBoat.BoatName) || (t.boatname = encodeURIComponent($("#pref_boatname")[0].value), a = !0), ComparePrefString($("#pref_boatcolor")[0].value, SafeHTMLColor(_CurPlayer.CurBoat.VLMInfo.COL)) || (t.color = $("#pref_boatcolor")[0].value.substring(1), a = !0);
  var n = GetPrefSelFlag();
  ComparePrefString(n, _CurPlayer.CurBoat.VLMInfo.CNT) || (t.country = encodeURIComponent(n), a = !0), a && void 0 !== _CurPlayer && _CurPlayer && UpdateBoatPrefs(_CurPlayer.CurBoat, {
    prefs: t
  });
}

function GetPrefSelFlag() {
  return $("#CountryDropDown:first-child [flag]")[0].attributes.flag.value;
}

function ComparePrefString(e, t) {
  return e.toString() === t.toString();
}

function SelectCountryDDFlag(e) {
  $("#CountryDropDown:first-child").html("<div>" + GetCountryDropDownSelectorHTML(e, !1) + '<span class="caret"></span></div>');
}

function ResetCollapsiblePanels(e) {
  $(".collapse").collapse("hide");
}

function HandleBoatSelectionChange(e) {
  ResetCollapsiblePanels();
  var t = GetBoatFromIdu($(e.target).closest("li").attr("BoatID"));
  void 0 !== t && t ? (SetCurrentBoat(t, !0, !1), DisplayCurrentDDSelectedBoat(t)) : VLMAlertDanger(GetLocalizedString("Error Reload"));
}

var LastMouseMoveCall = 0;

function HandleMapMouseMove(e) {
  if (GM_Pos && void 0 !== _CurPlayer && void 0 !== _CurPlayer.CurBoat && void 0 !== _CurPlayer.CurBoat.VLMInfo) {
    var t = new VLMPosition(GM_Pos.lon, GM_Pos.lat),
        a = new VLMPosition(_CurPlayer.CurBoat.VLMInfo.LON, _CurPlayer.CurBoat.VLMInfo.LAT),
        i = _CurPlayer.CurBoat.GetNextWPPosition(),
        n = null,
        o = new Date() - LastMouseMoveCall > 300;

    VLM2Prefs.MapPrefs.EstTrackMouse && o && (n = _CurPlayer.CurBoat.GetClosestEstimatePoint(t), LastMouseMoveCall = new Date()), $("#MI_Lat").text(t.Lat.ToString()), $("#MI_Lon").text(t.Lon.ToString()), $("#MI_LoxoDist").text(a.GetLoxoDist(t, 2) + " nM"), $("#MI_OrthoDist").text(a.GetOrthoDist(t, 2) + " nM"), $("#MI_Loxo").text(a.GetLoxoCourse(t, 2) + " °"), $("#MI_Ortho").text(a.GetOrthoCourse(t, 2) + " °"), void 0 !== i && i ? ($("#MI_WPLoxoDist").text(i.GetLoxoDist(t, 2) + " nM"), $("#MI_WPOrthoDist").text(i.GetOrthoDist(t, 2) + " nM"), $("#MI_WPLoxo").text(i.GetLoxoCourse(t, 2) + " °"), $("#MI_WPOrtho").text(i.GetOrthoCourse(t, 2) + " °")) : ($("#MI_WPLoxoDist").text("--- nM"), $("#MI_WPOrthoDist").text("--- nM"), $("#MI_WPLoxo").text("--- °"), $("#MI_WPOrtho").text("--- °")), o && RefreshEstPosLabels(n);
  }
}

function RefreshEstPosLabels(e) {
  e && void 0 !== e.Date ? $("#MI_EstDate").text(GetLocalUTCTime(e.Date, !1, !0)) : $("#MI_EstDate").text("");
}

function GetWPrankingLI(e) {
  return '<li id="RnkWP' + e.wporder + '" RnkSort="WP" WPRnk="' + e.wporder + '"><a href="#DivRnkRAC" RnkSort="WP" WPRnk="' + e.wporder + '">WP ' + e.wporder + " : " + e.libelle + "</a></li>";
}

function ResetRankingWPList(e) {
  $("[WPRnk]").remove(), $("#RnkTabsUL").addClass("WPNotInited");
}

function CheckWPRankingList(e, t) {
  var a = $(".WPNotInited"),
      i = GetRankingRaceId(e),
      n = !1;

  if (void 0 !== a && a && i) {
    var o;
    if (void 0 !== e && e && void 0 !== e.RaceInfo && e.RaceInfo && i === e.RaceInfo.RaceId) BuildWPTabList(o, a), n = !0;else if (t) BuildWPTabList(t, a), n = !0;else {
      var _t10 = 0;
      void 0 !== e.VLMInfo && (_t10 = e.VLMInfo.VER), $.get("/ws/raceinfo/desc.php?idrace=" + i + "&v=" + _t10, function (t) {
        CheckWPRankingList(e, t);
      });
    }
  }

  n && ($(a).removeClass("WPNotInited"), $(".JVLMTabs").tabs("refresh"));
}

function BuildWPTabList(e, t) {
  var a;
  if (void 0 !== t && t) for (a in void 0 !== e && e || (e = Boat.RaceInfo.races_waypoints), e.races_waypoints) {
    if (e.races_waypoints[a]) {
      var i = GetWPrankingLI(e.races_waypoints[a]);
      $(t).append(i);
    }
  }
}

function SortRanking(e, t) {
  var a = _CurPlayer.CurBoat;
  if (CheckWPRankingList(a), void 0 === a || !a) return;
  var i = null;

  switch (a.VLMPrefs && a.VLMPrefs.mapPrefOpponents && (i = a.VLMPrefs.mapPrefOpponents.split(",")), e) {
    case "WP":
      SetRankingColumns(e), SortRankingData(a, e, t = parseInt(t, 10)), FillWPRanking(a, t, i);
      break;

    case "DNF":
    case "HC":
    case "ARR":
    case "HTP":
    case "ABD":
      SetRankingColumns(e), SortRankingData(a, e), FillStatusRanking(a, e, i);
      break;

    default:
      SetRankingColumns("RAC"), SortRankingData(a, "RAC"), FillRacingRanking(a, i);
  }
}

function SetRankingColumns(e) {
  switch (e) {
    case "WP":
      SetWPRankingColumns();
      break;

    case "DNF":
    case "HC":
    case "ARR":
    case "HTP":
    case "ABD":
      SetNRClassRankingColumns();
      break;

    default:
      SetRacingClassRankingColumns();
  }
}

var RACColumnHeader = ["Rank", "Name", "Distance", "Time", "Loch", "Lon", "Lat", "Last1h", "Last3h", "Last24h", "Delta1st"],
    NRColumnHeader = ["Rank", "Name", "Distance"],
    WPColumnHeader = ["Rank", "Name", "Time", "Loch"],
    RACColumnHeaderLabels = ["ranking", "boatname", "distance", "racingtime", "Loch", "Lon", "Lat", "Last1h", "Last3h", "Last24h", "ecart"],
    NRColumnHeaderLabels = ["ranking", "boatname", "status"],
    WPColumnHeaderLabels = ["ranking", "boatname", "racingtime", "ecart"];

function SetRacingClassRankingColumns() {
  SetColumnsVisibility(RACColumnHeader, RACColumnHeaderLabels);
}

function SetNRClassRankingColumns() {
  SetColumnsVisibility(NRColumnHeader, NRColumnHeaderLabels);
}

function SetWPRankingColumns() {
  SetColumnsVisibility(WPColumnHeader, WPColumnHeaderLabels);
}

function SetColumnsVisibility(e, t) {
  var a;

  for (a = 0; a < RankingFt.columns.array.length; a++) {
    if (RankingFt.columns.array[a]) {
      var i = e.indexOf(RankingFt.columns.array[a].name);
      i > -1 && $("[data-name='" + e[i] + "']").attr("I18n", t[i]), RankingFt.columns.array[a].visible = i > -1;
    }
  }

  LocalizeItem($("[I18n][data-name]").get());
}

function RnkIsArrived(e) {
  return !(void 0 === e || void 0 === e.status || !e.status) && -1 !== BoatArrivedStatus.indexOf(e.status);
}

function RnkIsRacing(e) {
  return !(void 0 === e || void 0 === e.status || !e.status) && -1 !== BoatRacingStatus.indexOf(e.status);
}

function Sort2ArrivedBoats(e, t) {
  var a = parseInt(e.duration, 10) + parseInt(e.penalty, 10),
      i = parseInt(t.duration, 10) + parseInt(t.penalty, 10);
  return a > i ? (DebugRacerSort(e, t, 1), 1) : a < i ? (DebugRacerSort(e, t, -1), -1) : (DebugRacerSort(e, t, 0), 0);
}

function Sort2RacingBoats(e, t) {
  var a = parseInt(e.nwp, 10),
      i = parseInt(t.nwp, 10);

  if (a === i) {
    var _a5 = parseFloat(e.dnm),
        _i3 = parseFloat(t.dnm);

    if (_a5 > _i3) return DebugRacerSort(e, t, 1), 1;

    if (_a5 === _i3) {
      DebugRacerSort(e, t, 0);

      var _a6 = e.country > t.country ? 1 : e.country === t.country ? 0 : -1;

      if (_a6) return _a6;
      return e.idusers > t.idusers ? 1 : e.idusers === t.idusers ? 0 : -1;
    }

    return DebugRacerSort(e, t, -1), -1;
  }

  return a > i ? (DebugRacerSort(e, t, -1), -1) : (DebugRacerSort(e, t, 1), 1);
}

function GetWPDuration(e, t) {
  return e && e.WP && e.WP[t - 1] && e.WP[t - 1].duration ? parseInt(e.WP[t - 1].duration, 10) : 9999999999;
}

function WPRaceSort(e) {
  return function (t, a) {
    return GetWPDuration(t, e) - GetWPDuration(a, e);
  };
}

function RacersSort(e, t) {
  return RnkIsRacing(e) && RnkIsRacing(t) ? Sort2RacingBoats(e, t) : RnkIsArrived(e) && RnkIsArrived(t) ? Sort2ArrivedBoats(e, t) : RnkIsArrived(e) ? (DebugRacerSort(e, t, -1), -1) : RnkIsArrived(t) ? (DebugRacerSort(e, t, 1), 1) : RnkIsRacing(e) ? (DebugRacerSort(e, t, 1), -1) : RnkIsRacing(t) ? (DebugRacerSort(e, t, 1), 1) : Sort2NonRacing(e, t);
}

var DebugCount = 1;

function DebugRacerSort(e, t, a) {}

function Sort2NonRacing(e, t) {
  if (void 0 !== e.idusers && void 0 !== t.idusers) {
    var a = e.country > t.country ? 1 : e.country === t.country ? 0 : -1;
    if (a) return a;
    {
      var _a7 = parseInt(e.idusers, 10),
          i = parseInt(t.idusers, 10);

      return _a7 > i ? (DebugRacerSort(e, t, 1), 1) : _a7 < i ? (DebugRacerSort(e, t, -1), -1) : (DebugRacerSort(e, t, 0), 0);
    }
  }

  if ("undefined" != typeof IdUser1) return -1;
  if ("undefined" != typeof IdUser2) return -1;
  {
    var _a8 = [e, t];
    return _a8.sort(), _a8[0] === e ? 1 : -1;
  }
}

function GetRankingRaceId(e, t) {
  return t || RankingFt.RaceRankingId ? t || RankingFt.RaceRankingId : e.Engaged;
}

function SortRankingData(e, t, a, i) {
  if (i = GetRankingRaceId(e, i), !e || !Rankings[i]) return;

  if (Rankings && Rankings[i] && void 0 === Rankings[i].RacerRanking) {
    var _e13;

    for (_e13 in Rankings[i].RacerRanking = [], Rankings[i]) {
      Rankings[i][_e13] && Rankings[i].RacerRanking.push(Rankings[i][_e13]);
    }
  }

  switch (t) {
    case "WP":
      Rankings[i].RacerRanking.sort(WPRaceSort(a));
      break;

    case "RAC":
    case "DNF":
    case "HC":
    case "HTP":
    case "ABD":
    case "ARR":
      Rankings[i].RacerRanking.sort(RacersSort);
      break;

    default:
      VLMAlertInfo("unexpected sort option : " + t);
  }

  var n = 1,
      o = 0;

  for (o in Rankings[i].RacerRanking) {
    if (Rankings[i].RacerRanking[o] && e.IdBoat === o) {
      n = o + 1;
      break;
    }
  }

  return n;
}

function FillWPRanking(e, t, a) {
  var i,
      n = 1,
      o = 0,
      r = [];
  if (!e || !RankingFt || RankingFt.DrawPending) return;
  var s = GetRankingRaceId(e);

  for (i in BackupRankingTable(), Rankings[s].RacerRanking) {
    if (Rankings[s].RacerRanking[i]) {
      var _l2 = Rankings[s].RacerRanking[i];
      _l2.WP && _l2.WP[t - 1] && !_l2.WP[t - 1].Delta && (o ? (_l2.WP[t - 1].Delta = _l2.WP[t - 1].duration - o, _l2.WP[t - 1].Pct = 100 * (_l2.WP[t - 1].duration / o - 1)) : (o = _l2.WP[t - 1].duration, _l2.WP[t - 1].Delta = 0, _l2.WP[t - 1].Pct = 0)), _l2.WP && _l2.WP[t - 1] && (r.push(GetRankingObject(_l2, parseInt(i, 10) + 1, t, a)), e.IdBoat === parseInt(_l2.idusers, 10) && (n = r.length));
    }
  }

  var l = RoundPow(n / 20, 0) + (n % 20 >= 10 ? 0 : 1);
  RankingFt.DrawPending = !0, RankingFt.loadRows(r), RankingFt.TargetPage = l;
}

function BackupICS_WPTable() {
  BackupFooTable(ICS_WPft, "#RaceWayPoints", "#RaceWayPointsInsertPoint");
}

function getWaypointHTMLSymbols(e) {
  var t = "";

  switch (e & (WP_CROSS_CLOCKWISE | WP_CROSS_ANTI_CLOCKWISE)) {
    case WP_CROSS_ANTI_CLOCKWISE:
      t += "&#x21BA; ";
      break;

    case WP_CROSS_CLOCKWISE:
      t += "&#x21BB; ";
  }

  switch ((e & WP_CROSS_ONCE) == WP_CROSS_ONCE && (t += "&#x2285; "), e & (WP_ICE_GATE_N | WP_ICE_GATE_S)) {
    case WP_ICE_GATE_S:
      t += "&#x27F0;";
      break;

    case WP_ICE_GATE_N:
      t += "&#x27F1;";
  }

  return t.trim();
}

function getWaypointHTMLSymbolsDescription(e) {
  var t = "";

  switch (e & (WP_CROSS_CLOCKWISE | WP_CROSS_ANTI_CLOCKWISE)) {
    case WP_CROSS_ANTI_CLOCKWISE:
      t += GetLocalizedString("Anti-clockwise") + " ";
      break;

    case WP_CROSS_CLOCKWISE:
      t += GetLocalizedString("Clockwise") + " ";
  }

  switch ((e & WP_CROSS_ONCE) == WP_CROSS_ONCE && (t += GetLocalizedString("Only once")), e & (WP_ICE_GATE_N | WP_ICE_GATE_S)) {
    case WP_ICE_GATE_S:
      t += GetLocalizedString("Ice gate") + "(" + GetLocalizedString("South") + ") ";
      break;

    case WP_ICE_GATE_N:
      t += GetLocalizedString("Ice gate") + "(" + GetLocalizedString("North") + ") ";
  }

  return "" !== t && (t = GetLocalizedString("Crossing") + " : " + t), t.trim();
}

function NormalizeRaceInfo(e) {
  if (void 0 !== e && e && !e.IsNormalized) {
    e.startlat /= VLM_COORDS_FACTOR, e.startlong /= VLM_COORDS_FACTOR;

    for (var t in e.races_waypoints) {
      if (e.races_waypoints[t]) {
        var a = e.races_waypoints[t];
        a.latitude1 /= VLM_COORDS_FACTOR, a.longitude1 /= VLM_COORDS_FACTOR, void 0 !== a.latitude2 && (a.latitude2 /= VLM_COORDS_FACTOR, a.longitude2 /= VLM_COORDS_FACTOR);
      }
    }

    e.IsNormalized = !0;
  }
}

function FillRaceWaypointList(e) {
  if (ICS_WPft.DrawPending) ICS_WPft.CallbackPending || (ICS_WPft.CallbackPending = function () {
    FillRaceWaypointList(e);
  });else if (BackupICS_WPTable(), e) {
    NormalizeRaceInfo(e);
    var t = [],
        a = {
      WaypointId: 0
    };
    a.WP1 = e.startlat + "<BR>" + e.startlong, a.WP2 = "", a.Spec = "", a.Type = GetLocalizedString("startmap"), a.Name = "", t.push(a);

    for (var _a9 in e.races_waypoints) {
      if (e.races_waypoints[_a9]) {
        var i = e.races_waypoints[_a9],
            n = {};
        n.WaypointId = i.wporder, n.WP1 = i.latitude1 + "<BR>" + i.longitude1, void 0 !== i.latitude2 ? n.WP2 = i.latitude2 + "<BR>" + i.longitude2 : n.WP2 = "@" + i.laisser_au, n.Spec = "<span title='" + getWaypointHTMLSymbolsDescription(i.wpformat) + "'>" + getWaypointHTMLSymbols(i.wpformat) + "</span>", n.Type = GetLocalizedString(i.wptype), n.Name = i.libelle, t.push(n);
      }
    }

    ICS_WPft.loadRows(t);
  }
}

function BackupNSZ_Table() {
  BackupFooTable(NSZ_WPft, "NSZPoints", "NSZPointsInsertPoint");
}

function FillNSZList(e) {
  if (NSZ_WPft.DrawPending) NSZ_WPft.CallbackPending || (NSZ_WPft.CallbackPending = function () {
    FillNSZList(e);
  });else if (BackupNSZ_Table(), e) {
    var t = [];

    for (var a in e) {
      if (e[a]) {
        var i = e[a],
            n = {};
        n.NSZId = a, n.Lon1 = i[0][1], n.Lat1 = i[0][0], n.Lon2 = i[1][1], n.Lat2 = i[1][0], t.push(n);
      }
    }

    NSZ_WPft.loadRows(t);
  }
}

function BackupRankingTable() {
  BackupFooTable(RankingFt, "#RankingTable", "#my-rank-content");
}

function FillStatusRanking(e, t, a) {
  var i,
      n = 1,
      o = [],
      r = GetRankingRaceId(e);

  for (i in BackupRankingTable(), Rankings[r].RacerRanking) {
    if (Rankings[r].RacerRanking[i]) {
      var _s2 = Rankings[r].RacerRanking[i];
      _s2.status === t && (o.push(GetRankingObject(_s2, parseInt(i, 10) + 1, null, a)), e.IdBoat === parseInt(_s2.idusers, 10) && (n = o.length));
    }
  }

  var s = RoundPow(n / 20, 0) + (n % 20 >= 10 ? 0 : 1);
  RankingFt.loadRows(o), RankingFt.TargetPage = s, RankingFt.DrawPending = !0;
}

function FillRacingRanking(e, t) {
  var a,
      i = [],
      n = 0,
      o = {
    Arrived1stTime: null,
    Racer1stPos: null
  };
  BackupRankingTable();
  var r = GetRankingRaceId(e),
      s = 0;
  if (r && void 0 !== Rankings && void 0 !== Rankings[r] && Rankings[r] && Rankings[r].RacerRanking) for (a in Rankings[r].RacerRanking) {
    if (Rankings[r].RacerRanking[a]) {
      var _l3 = Rankings[r].RacerRanking[a];
      if (e.IdBoat === parseInt(_l3.idusers, 10) && (n = i.length), !RnkIsArrived(_l3) && !RnkIsRacing(_l3)) break;
      !o.Arrived1stTime && RnkIsArrived(_l3) && (o.Arrived1stTime = parseInt(_l3.duration, 10)), !RnkIsRacing(_l3) || o.Racer1stPos && _l3.nwp === s || (o.Racer1stPos = _l3.dnm, s = _l3.nwp), i.push(GetRankingObject(_l3, parseInt(a, 10) + 1, null, t, o));
    }
  }
  var l = RoundPow(n / 20, 0) + (n % 20 >= 10 ? 0 : 1);
  RankingFt.loadRows(i), RankingFt.TargetPage = l, RankingFt.DrawPending = !0;
}

function GetBoatInfoLink(e) {
  var t = parseInt(e.idusers, 10),
      a = e.boatname,
      i = "";
  return e.country && void 0 === (i = GetCountryFlagImgHTML(e.country)) && (i = ""), i += '<a class="RaceHistLink" boatid ="' + t + '">' + a + "</a>";
}

function GetRankingObject(e, t, a, i, n) {
  var o = "";
  void 0 !== e.Challenge && e.Challenge[1] && (o = '<img class="RnkLMNH" src="images/LMNH.png"></img>' + o);
  var r = {
    Rank: t,
    Name: o += GetBoatInfoLink(e),
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

  if (parseInt(e.idusers, 10) === _CurPlayer.CurBoat.IdBoat && (r.Class += " ft_class_myboat"), void 0 !== i && i && -1 !== i.indexOf(e.idusers) && (r.Class += " ft_class_friend"), RnkIsRacing(e) && !a) {
    var _a10 = "[" + e.nwp + "] -=> " + RoundPow(e.dnm, 2);

    if (t > 1 && n && n.Racer1stPos) {
      new VLMPosition(e.longitude, e.latitude);
      r.Delta1st = RoundPow(e.dnm - n.Racer1stPos, 2);
    }

    r.Distance = _a10;

    var _i4 = Math.round((new Date() - new Date(1e3 * parseInt(e.deptime, 10))) / 1e3);

    r.Time = "-1" === e.deptime ? "" : GetFormattedChronoString(_i4), r.Loch = e.loch, r.lon = e.longitude, r.Lat = e.latitude, r.Last1h = e.last1h, r.Last3h = e.last3h, r.Last24h = e.last24h;

    for (var _t11 in BoatRacingStatus) {
      e.status === BoatRacingStatus[_t11] && (r.Class += "  " + BoatRacingClasses[BoatRacingStatus[_t11]]);
    }
  } else if (a) {
    var _t12;

    if (r.Time = GetFormattedChronoString(parseInt(e.WP[a - 1].duration, 10)), e.WP[a - 1].Delta) {
      var _i5 = RoundPow(e.WP[a - 1].Pct, 2);

      _t12 = GetFormattedChronoString(e.WP[a - 1].Delta) + " (+" + _i5 + " %)";
    } else _t12 = GetLocalizedString("winner");

    r.Loch = _t12;
  } else {
    var _t13 = GetLocalizedString("status_" + e.status);

    r.Distance = _t13;

    var _a11 = parseInt(e.duration, 10);

    r.Time = GetFormattedChronoString(_a11), n && _a11 !== n.Arrived1stTime && (r.Time += " ( +" + RoundPow(_a11 / n.Arrived1stTime * 100 - 100, 2) + "% )"), r.Loch = e.loch, r.lon = e.longitude, r.Lat = e.latitude;
  }

  return r;
}

function HandleShowMapPrefs(e) {
  $("#DisplayReals").attr("checked", VLM2Prefs.MapPrefs.ShowReals), $("#DisplayNames").attr("checked", VLM2Prefs.MapPrefs.ShowOppNames), $("#EstTrackMouse").attr("checked", VLM2Prefs.MapPrefs.EstTrackMouse), $("#TrackEstForecast").attr("checked", VLM2Prefs.MapPrefs.TrackEstForecast), $("#UseUTC").attr("checked", VLM2Prefs.MapPrefs.UseUTC), $("#DDMapSelOption:first-child").html("<span Mode=" + VLM2Prefs.MapPrefs.MapOppShow + ">" + VLM2Prefs.MapPrefs.GetOppModeString(VLM2Prefs.MapPrefs.MapOppShow) + '</span><span class="caret"></span>'), VLM2Prefs.MapPrefs.MapOppShow === VLM2Prefs.MapPrefs.MapOppShowOptions.ShowTop10 ? ($("#NbDisplayBoat").removeClass("hidden"), $("#NbDisplayBoat").val(VLM2Prefs.MapPrefs.ShowTopCount)) : $("#NbDisplayBoat").addClass("hidden"), $("#VacPol").val(VLM2Prefs.MapPrefs.PolarVacCount);
}

function HandleMapPrefOptionChange(e) {
  var t = e.target;

  if (void 0 !== t && void 0 !== t.attributes.id) {
    var a = t.attributes.id.value,
        i = t.checked;

    switch (a) {
      case "DisplayReals":
      case "ShowReals":
      case "UseUTC":
      case "DisplayNames":
      case "ShowOppNames":
      case "EstTrackMouse":
      case "TrackEstForecast":
        VLM2Prefs.MapPrefs[a] = i;
        break;

      case "VacPol":
        var _e14 = parseInt($("#VacPol").val(), 10);

        _e14 > 0 && _e14 < 120 ? VLM2Prefs.MapPrefs.PolarVacCount = _e14 : $("#VacPol").value(12);
        break;

      case "NbDisplayBoat":
        var _t14 = parseInt($("#NbDisplayBoat").val(), 10);

        VLM2Prefs.MapPrefs.ShowTopCount = _t14;
        break;

      default:
        return void console.log("unknown pref storage called : " + a);
    }

    VLM2Prefs.Save(), RefreshCurrentBoat(!1, !1);
  }
}

function SafeHTMLColor(e) {
  return (e = "" + e).length < 6 && (e = ("000000" + e).slice(-6)), "#" !== e.substring(0, 1) ? e = "#" + e : "#" === e.substring(1, 2) && (e = e.substring(1)), e;
}

function HandleMapOppModeChange(e) {
  var t = e.target,
      a = parseInt(t.attributes.Mode.value, 10);
  VLM2Prefs.MapPrefs.MapOppShow = a, VLM2Prefs.Save(), HandleShowMapPrefs(e);
}

function SetActiveStyleSheet(e) {
  var t, a;

  for (t = 0; a = document.getElementsByTagName("link")[t]; t++) {
    -1 !== a.getAttribute("rel").indexOf("style") && a.getAttribute("title") && (a.disabled = !0, a.getAttribute("title") === e && (a.disabled = !1));
  }
}

function SetDDTheme(e) {
  SetActiveStyleSheet(e), $("#SelectionThemeDropDown:first-child").html(e + '<span class="caret"></span>'), $("#SelectionThemeDropDown").attr("SelTheme", e);
}

function HandleDDlineClick(e) {
  e.target;
  SetDDTheme(e.target.attributes.ddtheme.value);
}

var AlertTemplate;

function InitAlerts() {
  $("#AlertBox").css("display", "block"), AlertTemplate = $("#AlertBox")[0], $("#AlertBoxContainer").empty(), $("#AlertBoxContainer").removeClass("hidden");
}

function VLMAlertSuccess(e) {
  VLMAlert(e, "alert-success");
}

function VLMAlertDanger(e) {
  VLMAlert(e, "alert-danger");
}

function VLMAlertInfo(e) {
  VLMAlert(e, "alert-info");
}

var AlertIntervalId = null;

function VLMAlert(e, t) {
  AlertIntervalId && clearInterval(AlertIntervalId), void 0 !== t && t || (t = "alert-info"), $("#AlertBoxContainer").empty().append(AlertTemplate).show(), $("#AlertText").text(e), $("#AlertBox").removeClass("alert-sucess"), $("#AlertBox").removeClass("alert-warning"), $("#AlertBox").removeClass("alert-info"), $("#AlertBox").removeClass("alert-danger"), $("#AlertBox").addClass(t), $("#AlertBox").show(), $("#AlertCloseBox").unbind().on("click", AutoCloseVLMAlert), AlertIntervalId && clearInterval(AlertIntervalId), AlertIntervalId = setTimeout(AutoCloseVLMAlert, 5e3);
}

function AutoCloseVLMAlert() {
  $("#AlertBox").hide();
}

function GetUserConfirmation(e, t, a) {
  $("#ConfirmDialog").modal("show"), t ? ($("#OKBtn").hide(), $("#CancelBtn").hide(), $("#YesBtn").show(), $("#NoBtn").show()) : ($("#OKBtn").show(), $("#CancelBtn").show(), $("#YesBtn").hide(), $("#NoBtn").hide()), $("#ConfirmText").text(e), $(".OKBtn").unbind().on("click", function () {
    $("#ConfirmDialog").modal("hide"), a(!0);
  }), $(".NOKBtn").unbind().on("click", function () {
    $("#ConfirmDialog").modal("hide"), a(!1);
  });
}

function GetRaceRankingLink(e) {
  return '<a href="/jvlm?RaceRank=' + e.idrace + '" target="RankTab">' + e.racename + "</a>";
}

function FillBoatPalmares(e, t, a, i, n, o) {
  var r;

  if ("success" === t) {
    var _t15 = [];

    for (r in e.palmares) {
      if (e.palmares[r]) {
        var _a12 = e.palmares[r],
            _i6 = {
          RaceId: e.palmares[r].idrace,
          RaceName: GetRaceRankingLink(e.palmares[r]),
          Ranking: _a12.ranking.rank + " / " + _a12.ranking.racercount
        };

        _t15.push(_i6);
      }
    }

    RaceHistFt.loadRows(_t15);
  }

  var s = GetLocalizedString("palmares");
  s = s.replace("%s", e.boat.name), $("#palmaresheaderline").text(s);
}

function ShowUserRaceHistory(e) {
  $("#RaceHistory").modal("show"), $.get("/ws/boatinfo/palmares.php?idu=" + e, function (e, t, a, i, n, o) {
    FillBoatPalmares(e, t, a, i, n, o);
  });
}

function HandleShowBoatRaceHistory(e) {
  var t = $(e.target).attr("boatid");
  t && ShowUserRaceHistory(t);
}

function HandleCreateUserResult(e, t) {
  if ("success" === t && e) if ($(".ValidationMark").addClass("hidden"), e.success ? ($(".ValidationMark.Valid").removeClass("hidden"), VLMAlertSuccess(GetLocalizedString("An email has been sent. Click on the link to validate.")), $("#InscriptForm").modal("hide"), $("#LoginForm").modal("hide")) : e.request && e.request.errorstring ? VLMAlertDanger(GetLocalizedString(e.request.errorstring)) : VLMAlertDanger(GetLocalizedString(e.error.msg)), e.request) e.request.MailOK ? $(".ValidationMark.Email.Valid").removeClass("hidden") : $(".ValidationMark.Email.Invalid").removeClass("hidden"), e.request.PasswordOK ? $(".ValidationMark.Password.Valid").removeClass("hidden") : $(".ValidationMark.Password.Invalid").removeClass("hidden"), e.request.PlayerNameOK ? $(".ValidationMark.Pseudo.Valid").removeClass("hidden") : $(".ValidationMark.Pseudo.Invalid").removeClass("hidden");else if (e.error) switch (e.error.code) {
    case "NEWPLAYER01":
      $(".ValidationMark.Email.Invalid").removeClass("hidden");
      break;

    case "NEWPLAYER02":
      $(".ValidationMark.Pseudo.Invalid").removeClass("hidden");
      break;

    case "NEWPLAYER03":
      $(".ValidationMark.Password.Invalid").removeClass("hidden");
  }
  $("#BtnCreateAccount").show();
}

function HandleCreateUser() {
  var e = $("#NewPlayerPseudo")[0].value,
      t = {
    emailid: $("#NewPlayerEMail")[0].value,
    password: $("#NewPlayerPassword")[0].value,
    pseudo: e
  };
  $("#BtnCreateAccount").hide(), $.post("/ws/playerinfo/player_create.php", t, function (e, t) {
    HandleCreateUserResult(e, t);
  });
}

function setModalMaxHeight(e) {
  var t = $(e),
      a = t.find(".modal-content"),
      i = a.outerHeight() - a.innerHeight(),
      n = $(window).width() < 768 ? 20 : 60,
      o = $(window).height() - (n + i) - ((t.find(".modal-header").outerHeight() || 0) + (t.find(".modal-footer").outerHeight() || 0));
  a.css({
    overflow: "hidden"
  }), t.find(".modal-body").css({
    "max-height": o,
    "overflow-y": "auto"
  });
}

function GetLocalUTCTime(e, t, a) {
  var i = e,
      n = "";
  return moment.isMoment(e) || (i = t ? moment(e).utc() : moment(e)), VLM2Prefs.MapPrefs.UseUTC ? (t || (i = i.utc()), n = " Z") : t && (i = i.local()), a ? i.format("LLLL") + n : i;
}

if ("undefined" == typeof jQuery) throw new Error("jQuery progress timer requires jQuery");

var _LocaleDict, _EnDict;

!function (e, t, a, i) {
  "use strict";

  var n = "progressTimer",
      o = {
    timeLimit: 60,
    warningThreshold: 5,
    onFinish: function onFinish() {},
    baseStyle: "",
    warningStyle: "progress-bar-danger",
    completeStyle: "progress-bar-success",
    showHtmlSpan: !0,
    errorText: "ERROR!",
    successText: "100%"
  },
      r = function r(t, a) {
    this.element = t, this.$elem = e(t), this.options = e.extend({}, o, a), this._defaults = o, this._name = n, this.metadata = this.$elem.data("plugin-options"), this.init();
  };

  r.prototype.constructor = r, r.prototype.init = function () {
    var a = this;
    return e(a.element).empty(), a.span = e("<span/>"), a.barContainer = e("<div>").addClass("progress"), a.bar = e("<div>").addClass("progress-bar active progress-bar-striped").addClass(a.options.baseStyle).attr("role", "progressbar").attr("aria-valuenow", "0").attr("aria-valuemin", "0").attr("aria-valuemax", a.options.timeLimit), a.span.appendTo(a.bar), a.options.showHtmlSpan || a.span.addClass("sr-only"), a.bar.appendTo(a.barContainer), a.barContainer.appendTo(a.element), a.start = new Date(), a.limit = 1e3 * a.options.timeLimit, a.warningThreshold = 1e3 * a.options.warningThreshold, a.interval = t.setInterval(function () {
      a._run.call(a);
    }, 250), a.bar.data("progress-interval", a.interval), !0;
  }, r.prototype.destroy = function () {
    this.$elem.removeData();
  }, r.prototype._run = function () {
    var e = this,
        t = new Date() - e.start,
        a = t / e.limit * 100;
    e.bar.attr("aria-valuenow", a), e.bar.width(a + "%");
    var i = a.toFixed(2);
    return i >= 100 && (i = 100), e.options.showHtmlSpan && e.span.html(i + "%"), t >= e.warningThreshold && e.bar.removeClass(this.options.baseStyle).removeClass(this.options.completeStyle).addClass(this.options.warningStyle), t >= e.limit && e.complete.call(e), !0;
  }, r.prototype.removeInterval = function () {
    var a = e(".progress-bar", this.element);

    if (void 0 !== a.data("progress-interval")) {
      var i = a.data("progress-interval");
      t.clearInterval(i);
    }

    return a;
  }, r.prototype.complete = function () {
    var t = this,
        a = t.removeInterval.call(t),
        i = arguments;
    0 !== i.length && "object" == _typeof(i[0]) && (t.options = e.extend({}, t.options, i[0])), a.removeClass(t.options.baseStyle).removeClass(t.options.warningStyle).addClass(t.options.completeStyle), a.width("100%"), t.options.showHtmlSpan && e("span", a).html(t.options.successText), a.attr("aria-valuenow", 100), setTimeout(function () {
      t.options.onFinish.call(a);
    }, 500), t.destroy.call(t);
  }, r.prototype.error = function () {
    var t = this,
        a = t.removeInterval.call(t),
        i = arguments;
    0 !== i.length && "object" == _typeof(i[0]) && (t.options = e.extend({}, t.options, i[0])), a.removeClass(t.options.baseStyle).addClass(t.options.warningStyle), a.width("100%"), t.options.showHtmlSpan && e("span", a).html(t.options.errorText), a.attr("aria-valuenow", 100), setTimeout(function () {
      t.options.onFinish.call(a);
    }, 500), t.destroy.call(t);
  }, e.fn[n] = function (t) {
    var a = arguments;
    if (t === i || "object" == _typeof(t)) return this.each(function () {
      e.data(this, "plugin_" + n) || e.data(this, "plugin_" + n, new r(this, t));
    });

    if ("string" == typeof t && "_" !== t[0] && "init" !== t) {
      if (0 === Array.prototype.slice.call(a, 1).length && -1 !== e.inArray(t, e.fn[n].getters)) {
        var o = e.data(this[0], "plugin_" + n);
        return o[t].apply(o, Array.prototype.slice.call(a, 1));
      }

      return this.each(function () {
        var i = e.data(this, "plugin_" + n);
        i instanceof r && "function" == typeof i[t] && i[t].apply(i, Array.prototype.slice.call(a, 1));
      });
    }
  }, e.fn[n].getters = ["complete", "error"];
}(jQuery, window, document, void 0), function (e) {
  if (e.support.touch = "ontouchend" in document, e.support.touch) {
    var t,
        a = e.ui.mouse.prototype,
        i = a._mouseInit,
        n = a._mouseDestroy;
    a._touchStart = function (e) {
      !t && this._mouseCapture(e.originalEvent.changedTouches[0]) && (t = !0, this._touchMoved = !1, o(e, "mouseover"), o(e, "mousemove"), o(e, "mousedown"));
    }, a._touchMove = function (e) {
      t && (this._touchMoved = !0, o(e, "mousemove"));
    }, a._touchEnd = function (e) {
      t && (o(e, "mouseup"), o(e, "mouseout"), this._touchMoved || o(e, "click"), t = !1);
    }, a._mouseInit = function () {
      this.element.bind({
        touchstart: e.proxy(this, "_touchStart"),
        touchmove: e.proxy(this, "_touchMove"),
        touchend: e.proxy(this, "_touchEnd")
      }), i.call(this);
    }, a._mouseDestroy = function () {
      this.element.unbind({
        touchstart: e.proxy(this, "_touchStart"),
        touchmove: e.proxy(this, "_touchMove"),
        touchend: e.proxy(this, "_touchEnd")
      }), n.call(this);
    };
  }

  function o(e, t) {
    if (!(e.originalEvent.touches.length > 1)) {
      e.preventDefault();
      var a = e.originalEvent.changedTouches[0],
          i = document.createEvent("MouseEvents");
      i.initMouseEvent(t, !0, !0, window, 1, a.screenX, a.screenY, a.clientX, a.clientY, !1, !1, !1, !1, 0, null), e.target.dispatchEvent(i);
    }
  }
}(jQuery);
var _CurLocale = "en";

function LocalizeString() {
  return LocalizeItem($("[I18n]").get()), $(".LngFlag").click(function (e, t) {
    OnLangFlagClick($(this).attr("lang")), UpdateLngDropDown();
  }), !0;
}

function OnLangFlagClick(e) {
  InitLocale(e);
}

function LocalizeItem(e) {
  try {
    var t;

    for (t in e) {
      var a = e[t],
          i = a.attributes.I18n.value;
      void 0 !== _LocaleDict && (a.innerHTML = GetLocalizedString(i));
    }
  } finally {}

  return !0;
}

function InitLocale(e) {
  var t = "/ws/serverinfo/translation.php";
  e && (t += "?lang=" + e), $.get(t, function (e) {
    1 == e.success ? (_CurLocale = e.request.lang, _LocaleDict = e.strings, moment.locale(_CurLocale), LocalizeString(), UpdateLngDropDown()) : alert("Localization string table load failure....");
  }), void 0 === _EnDict && $.get("/ws/serverinfo/translation.php?lang=en", function (e) {
    1 == e.success ? _EnDict = e.strings : alert("Fallback localization string table load failure....");
  });
}

function HTMLDecode(e) {
  var t = document.createElement("textarea");
  t.innerHTML = e;
  var a = t.value,
      i = ["\n\r", "\r\n", "\n", "\r"];

  for (var _e15 in i) {
    for (; i[_e15] && -1 !== a.indexOf(i[_e15]);) {
      a = a.replace(i[_e15], "<br>");
    }
  }

  return a;
}

function GetLocalizedString(e) {
  var t = "";
  return t = e in _LocaleDict ? HTMLDecode(_LocaleDict[e]) : void 0 !== _EnDict && _EnDict && e in _EnDict ? HTMLDecode(_EnDict[e]) : e;
}

function GetCurrentLocale() {
  return _CurLocale;
}

var VLMMercatorTransform = new MercatorTransform();

function MercatorTransform() {
  this.Width = 1e4, this.Height = 1e4, this.LonOffset = 0, this.LatOffset = 0, this.Scale = 1e4 / 180, this.LonToMapX = function (e) {
    return this.Width / 2 + (e - this.LonOffset) * this.Scale;
  }, this.LatToMapY = function (e) {
    return e = Deg2Rad(e), e = Rad2Deg(e = Math.log(Math.tan(e) + 1 / Math.cos(e))), this.Height / 2 - (e - this.LatOffset) * this.Scale;
  }, this.SegmentsIntersect = function (e, t) {
    var a = this.LonToMapX(e.P1.Lon.Value),
        i = this.LatToMapY(e.P1.Lat.Value),
        n = this.LonToMapX(e.P2.Lon.Value),
        o = this.LatToMapY(e.P2.Lat.Value),
        r = this.LonToMapX(t.P1.Lon.Value),
        s = this.LatToMapY(t.P1.Lat.Value),
        l = this.LonToMapX(t.P2.Lon.Value),
        d = this.LatToMapY(t.P2.Lat.Value);
    if (e.P1.Lon.Value === e.P2.Lon.Value && e.P1.Lat.Value === e.P2.Lat.Value || t.P1.Lon.Value === t.P2.Lon.Value && t.P1.Lat.Value === t.P2.Lat.Value) return !1;
    n -= a, o -= i, r -= a, s -= i, l -= a, d -= i, a = 0, i = 0;
    var u = Math.sqrt(n * n + o * o),
        c = n / u,
        h = o / u,
        p = r * c + s * h;
    if (s = s * c - r * h, r = p, p = l * c + d * h, d = d * c - l * h, l = p, s === d) return !1;
    var f = l + (r - l) * d / (d - s),
        P = f / u;

    if (P >= 0 && P <= 1) {
      var g,
          L = i;
      if (l - r) g = (a + f - r) / (l - r);else {
        if (!(d - s)) return !1;
        g = (L - s) / (d - s);
      }
      return g >= 0 && g <= 1;
    }

    return !1;
  };
}

var PolarsManager = new PolarManagerClass();

function PolarManagerClass() {
  this.Polars = [], this.Init = function () {
    this.Polars = [], $.get("/ws/polarlist.php", function (e) {
      for (var t in e.list) {
        PolarsManager.Polars["boat_" + e.list[t]] = null;
      }
    });
  }, this.GetBoatSpeed = function (e, t, a, i) {
    if (!(e in this.Polars)) return NaN;

    if (this.Polars[e]) {
      var n = WindAngle(i, a);
      return GetPolarAngleSpeed(this.Polars[e], n, t);
    }

    return $.get("/Polaires/" + e + ".csv", this.HandlePolarLoaded.bind(this, e, null, null)), NaN;
  }, this.HandlePolarLoaded = function (e, t, a, i) {
    var n = $.csv.toArrays(i, {
      separator: ";"
    });

    for (var _e16 in n) {
      if (n[_e16]) for (var _t16 in n[_e16]) {
        n[_e16][_t16] && (n[_e16][_t16] = parseFloat(n[_e16][_t16]));
      }
    }

    PolarsManager.Polars[e] = {}, PolarsManager.Polars[e].SpeedPolar = n, PolarsManager.Polars[e].WindLookup = [], PolarsManager.Polars[e].AngleLookup = [], t && a ? t(a) : t && t();
  }, this.GetPolarLine = function (e, t, a, i, n) {
    if (n || (n = 5), void 0 === this.Polars[e]) return alert("Unexpected polarname : " + e), null;

    if (null !== this.Polars[e]) {
      var o,
          r = [],
          s = 0;

      for (o = 0; o <= 180; o += n) {
        var l = GetPolarAngleSpeed(this.Polars[e], o, t);
        s < l && (s = l), r.push(l);
      }

      for (var _e17 in r) {
        r[_e17] && (r[_e17] /= s);
      }

      return r;
    }

    $.get("/Polaires/" + e + ".csv", this.HandlePolarLoaded.bind(this, e, a, i));
  };
  var e = 0;
  this.GetVMGCourse = function (t, a, i, n, o) {
    for (var r = n.GetOrthoCourse(o), s = 0, l = -1e10, d = -1; d <= 1; d += 2) {
      for (var u = 0; u <= 90; u += .1) {
        var c = this.GetBoatSpeed(t, a, i, r + u * d),
            h = c * Math.cos(Deg2Rad(u));
        e && console.log("VMG " + RoundPow((r + u * d + 360) % 360, 3) + " " + RoundPow(c, 3) + " " + RoundPow(h, 3) + " " + RoundPow(l, 3) + " " + (h >= l ? "BEST" : "")), h >= l && (l = h, s = r + u * d);
      }
    }

    return e = 0, s;
  }, this.GetVBVMGCourse = function (e, t, a, i, n) {
    var o = i.GetOrthoDist(n),
        r = i.GetOrthoCourse(n),
        s = 0,
        l = 0,
        d = 0,
        u = 0,
        c = 0,
        h = 1,
        p = this.GetBoatSpeed(e, t, a, r);
    c = p > 0 ? o / p : 8760;
    var f = a - r;
    f < -90 ? f += 360 : f > 90 && (f -= 360), h = f > 0 ? -1 : 1;

    for (var _i7 = 1; _i7 <= 90; _i7++) {
      var _n = _i7 * Math.PI / 180,
          _p = Math.tan(_n),
          _f = Math.sqrt(1 + _p * _p),
          _P = this.GetBoatSpeed(e, t, a, r - _i7 * h);

      if (isNaN(_P)) throw "Nan SpeedT1 exception";
      if (_P > 0) for (var _n2 = -89; _n2 <= 0; _n2++) {
        var _g2 = _n2 * Math.PI / 180,
            L = o * (Math.tan(-_g2) / (_p + Math.tan(-_g2))),
            m = L * _f / _P;

        if (m < 0 || m > c) continue;
        var M = o - L,
            C = this.GetBoatSpeed(e, t, a, r - _n2 * h);
        if (isNaN(C)) throw "Nan SpeedT2 exception";
        if (C <= 0) continue;
        var y = Math.tan(-_g2),
            w = m + M * Math.sqrt(1 + y * y) / C;
        w < c && (c = w, s = _i7, l = _n2, d = _P, u = C);
      }
    }

    var P = d * Math.cos(Deg2Rad(s)),
        g = u * Math.cos(Deg2Rad(l));
    if (isNaN(P) || isNaN(g)) throw "NaN VMG found";
    return P > g ? r - s * h : r - l * h;
  }, this.GetPolarMaxSpeed = function (e, t) {
    if (!this.Polars[e]) return null;
    var a,
        i = 0;

    for (a = 0; a <= 180; a += 1) {
      var n = GetPolarAngleSpeed(this.Polars[e], a, t);
      n > i && (i = n);
    }

    return i;
  };
}

function GetPolarAngleSpeed(e, t, a) {
  var i,
      n,
      o,
      r,
      s = e.SpeedPolar,
      l = Math.floor(a);
  if (void 0 !== e.WindLookup && l in e.WindLookup) i = e.WindLookup[l];else for (var _t17 in s[0]) {
    if (_t17 > 0 && s[0][_t17] > a) break;
    e.WindLookup[l] = Math.floor(_t17), i = Math.floor(_t17);
  }

  for (n = i < s[0].length - 1 ? i + 1 : i; t < 0;) {
    t += 360;
  }

  t >= 360 && (t %= 360), t > 180 && (t = 360 - t);
  var d = Math.floor(t);
  if (void 0 !== e.AngleLookup && d in e.AngleLookup) o = e.AngleLookup[d];else for (var _a13 in s) {
    if (_a13 > 0 && s[_a13][0] > t) break;
    e.AngleLookup[d] = Math.floor(_a13), o = Math.floor(_a13);
  }
  r = o < s.length - 1 ? o + 1 : o;
  var u = GetAvgValue(a, s[0][i], s[0][n], s[o][i], s[o][n]),
      c = GetAvgValue(a, s[0][i], s[0][n], s[r][i], s[r][n]),
      h = GetAvgValue(t, s[o][0], s[r][0], u, c);
  if (isNaN(h)) throw "GetAvgValue was NaN";
  return h;
}

function WindAngle(e, t) {
  return e >= t ? e - t <= 180 ? e - t : 360 - e + t : t - e <= 180 ? t - e : 360 - t + e;
}

function GetAvgValue(e, t, a, i, n) {
  return e === t || t === a || i === n ? i : i + (e - t) / (a - t) * (n - i);
}

var POS_FORMAT_DEFAULT = 0,
    EARTH_RADIUS = 3443.84,
    VLM_DIST_ORTHO = 1;

function Deg2Rad(e) {
  return e / 180 * Math.PI;
}

function Rad2Deg(e) {
  return e / Math.PI * 180;
}

function RoundPow(e, t) {
  if (void 0 !== t) {
    var a = Math.pow(10, t);
    return Math.round(e * a) / a;
  }

  return e;
}

function NormalizeLongitudeDeg(e) {
  return e < -180 ? e += 360 : e > 180 && (e -= 360), e;
}

function VLMPosition(e, t, a) {
  void 0 !== a && a != POS_FORMAT_DEFAULT || (this.Lon = new Coords(e, 1), this.Lat = new Coords(t, 0)), this.ToString = function (e) {
    return this.Lat.ToString(e) + " " + this.Lon.ToString(e);
  }, this.GetEuclidianDist2 = function (e) {
    var t = (this.Lat.Value - e.Lat.Value) % 90,
        a = (this.Lon.Value - e.Lon.Value) % 180;
    return t * t + a * a;
  }, this.GetLoxoDist = function (e, t) {
    var a,
        i = Deg2Rad(this.Lat.Value),
        n = Deg2Rad(e.Lat.Value),
        o = -Deg2Rad(this.Lon.Value),
        r = -Deg2Rad(e.Lon.Value),
        s = 0;
    return s = Math.abs(n - i) < Math.sqrt(1e-15) ? Math.cos(i) : (n - i) / Math.log(Math.tan(n / 2 + Math.PI / 4) / Math.tan(i / 2 + Math.PI / 4)), a = Math.sqrt(Math.pow(n - i, 2) + s * s * (r - o) * (r - o)), RoundPow(EARTH_RADIUS * a, t);
  }, this.ReachDistLoxo = function (e, t) {
    var a = 0,
        i = 0;
    if (isNaN(t)) throw "unsupported reaching NaN distance";
    "number" == typeof e ? (a = e / EARTH_RADIUS, i = Deg2Rad(t % 360)) : (a = this.GetLoxoDist(e) / EARTH_RADIUS * t, i = Deg2Rad(this.GetLoxoCourse(e)));
    var n = Deg2Rad(this.Lat.Value),
        o = -Deg2Rad(this.Lon.Value),
        r = 0,
        s = 0,
        l = 0;
    if (r = n + a * Math.cos(i), Math.abs(r) > Math.PI / 2) throw "Invalid distance, can't go that far";
    if (l = Math.abs(r - n) < Math.sqrt(1e-15) ? Math.cos(n) : (r - n) / Math.log(Math.tan(r / 2 + Math.PI / 4) / Math.tan(n / 2 + Math.PI / 4)), s = -((o + -a * Math.sin(i) / l + Math.PI) % (2 * Math.PI) - Math.PI), isNaN(s) || isNaN(r)) throw "Reached Nan Position!!!";
    return s = RoundPow(Rad2Deg(s), 9), r = RoundPow(Rad2Deg(r), 9), new VLMPosition(NormalizeLongitudeDeg(s), r);
  }, this.GetLoxoCourse = function (e, t) {
    var a = -Deg2Rad(this.Lon.Value),
        i = -Deg2Rad(e.Lon.Value),
        n = Deg2Rad(this.Lat.Value),
        o = Deg2Rad(e.Lat.Value);
    void 0 !== t && "number" == typeof t || (t = 17);
    var r = (i - a) % (2 * Math.PI),
        s = (a - i) % (2 * Math.PI),
        l = Math.log(Math.tan(o / 2 + Math.PI / 4) / Math.tan(n / 2 + Math.PI / 4));
    return RoundPow((720 - (r < s ? Math.atan2(r, l) % (2 * Math.PI) : Math.atan2(-s, l) % (2 * Math.PI)) / Math.PI * 180) % 360, t);
  }, VLM_DIST_ORTHO ? (this.GetOrthoDist = function (e, t) {
    var a = -Deg2Rad(this.Lon.Value),
        i = -Deg2Rad(e.Lon.Value),
        n = Deg2Rad(this.Lat.Value),
        o = Deg2Rad(e.Lat.Value);
    return void 0 !== t && "number" == typeof t || (t = 17), RoundPow(60 * Rad2Deg(Math.acos(Math.sin(n) * Math.sin(o) + Math.cos(n) * Math.cos(o) * Math.cos(a - i))), t);
  }, this.GetOrthoCourse = function (e, t) {
    var a = -Deg2Rad(this.Lon.Value),
        i = -Deg2Rad(e.Lon.Value),
        n = Deg2Rad(this.Lat.Value),
        o = Deg2Rad(e.Lat.Value);
    void 0 !== t && "number" == typeof t || (t = 17);
    var r = Deg2Rad(this.GetOrthoDist(e) / 60),
        s = (Math.sin(o) - Math.sin(n) * Math.cos(r)) / (Math.sin(r) * Math.cos(n));
    return RoundPow(s = Rad2Deg((s = s >= -1 && s <= 1 ? Math.sin(i - a) < 0 ? Math.acos(s) : 2 * Math.PI - Math.acos(s) : n < o ? 0 : Math.PI) % (2 * Math.PI)), t);
  }) : (this.GetOrthoDist = function (e, t) {
    var a = -Deg2Rad(this.Lon.Value),
        i = -Deg2Rad(e.Lon.Value),
        n = Deg2Rad(this.Lat.Value),
        o = Deg2Rad(e.Lat.Value);
    void 0 !== t && "number" == typeof t || (t = 17);
    var r = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin((n - o) / 2), 2) + Math.pow(Math.cos(n) * Math.cos(o) * Math.sin((a - i) / 2), 2)));
    return RoundPow(EARTH_RADIUS * r, t);
  }, this.GetOrthoCourse = function (e, t) {
    var a = -Deg2Rad(this.Lon.Value),
        i = -Deg2Rad(e.Lon.Value),
        n = Deg2Rad(this.Lat.Value),
        o = Deg2Rad(e.Lat.Value);
    void 0 !== t && "number" == typeof t || (t = 17);
    var r = Math.atan2(Math.sin(a - i) * Math.cos(o), Math.cos(n) * Math.sin(o) - Math.sin(n) * Math.cos(o) * Math.cos(a - i));
    return RoundPow(r = Rad2Deg(r % (2 * Math.PI)), t);
  }), this.ReachDistOrtho = function (t, a) {
    var i,
        n,
        o = t / EARTH_RADIUS,
        r = Deg2Rad(a),
        s = Deg2Rad(this.Lat.Value),
        l = Deg2Rad(-this.Lon.Value);
    return i = Math.asin(Math.sin(s) * Math.cos(o) + Math.cos(s) * Math.sin(o) * Math.cos(r)), n = Math.atan2(Math.sin(r) * Math.sin(o) * Math.cos(s), Math.cos(o) - Math.sin(s) * Math.sin(i)), new VLMPosition(NormalizeLongitudeDeg(Rad2Deg(-(e = (l - n + Math.PI) % (2 * Math.PI) - Math.PI))), Rad2Deg(i));
  }, this.GetVLMString = function () {
    return this.Lat.ToString() + "," + this.Lon.ToString();
  };
}

var _IsLoggedIn;

function Boat(e) {
  this.IdBoat = -1, this.Engaged = !1, this.BoatName = "", this.BoatPseudo = "", this.VLMInfo = {}, this.RaceInfo = {}, this.Exclusions = [], this.Track = [], this.RnkObject = {}, this.OppTrack = [], this.OppList = [], this.Reals = [], this.VLMPrefs = [], this.NextServerRequestDate = null, this.Estimator = new Estimator(this), this.EstimatePos = null, void 0 !== e && (this.IdBoat = e.idu, this.Engaged = e.engaged, this.BoatName = e.boatname, this.BoatPseudo = e.boatpseudo, this.VLMInfo = e.VLMInfo, this.RaceInfo = e.RaceInfo, this.Exclusions = e.Exclusions, this.Track = e.Track, this.RnkObject = e.RnkObject), this.GetNextGateSegment = function (e) {
    if ("string" == typeof e && (e = parseInt(e, 10)), void 0 === this.RaceInfo) return null;
    var t = this.RaceInfo.races_waypoints[e];

    do {
      if ("string" == typeof t && (t = parseInt(t, 10)), t.wpformat & WP_ICE_GATE) {
        if (++e >= this.RaceInfo.races_waypoints) throw "Oops could not find requested gate type";
        t = this.RaceInfo.races_waypoints[e];
      }
    } while (t.wpformat & WP_ICE_GATE);

    var a = new VLMPosition(t.longitude1, t.latitude1);
    if ((t.format & WP_GATE_BUOY_MASK) !== WP_TWO_BUOYS) throw "not implemented 1 buoy gate";
    return {
      P1: a,
      P2: new VLMPosition(t.longitude2, t.latitude2)
    };
  }, this.GetClosestEstimatePoint = function (e) {
    if (void 0 === e || !e) return null;

    if (this.Estimator) {
      var t = this.Estimator.GetClosestEstimatePoint(e);
      return t ? this.Estimator.ShowEstimatePosition(this.Estimator.Boat, t) : this.Estimator.ClearEstimatePosition(this.Estimator.Boat), t;
    }

    return null;
  }, this.GetNextWPPosition = function (e, t, a) {
    if (void 0 === this.VLMInfo) return null;
    this.VLMInfo.NWP;
    if (!(void 0 !== a && a || 0 === this.VLMInfo.WPLON && 0 === this.VLMInfo.WPLAT)) return new VLMPosition(this.VLMInfo.WPLON, this.VLMInfo.WPLAT);
    if (void 0 !== a && a && 0 !== a.Lon.Value && 0 !== a.Lat.Value) return new VLMPosition(a.Lon.Value, a.Lat.Value);
    var i = this.VLMInfo.NWP;
    void 0 !== e && e && (i = e);
    var n = this.GetNextGateSegment(i);
    if (void 0 === n || !n) return null;
    var o,
        r = n.P1.GetLoxoCourse(n.P2);
    o = void 0 !== t && t ? t : new VLMPosition(this.VLMInfo.LON, this.VLMInfo.LAT);
    var s = r - n.P1.GetLoxoCourse(o);
    if (s > 180 ? s -= 360 : s < -180 && (s += 360), (s = Math.abs(s)) > 90) return n.P1;
    var l = n.P1.GetLoxoDist(o);

    try {
      var d = l * Math.cos(Deg2Rad(s));
      return n.P1.GetLoxoDist(n.P2) > d ? n.P1.ReachDistLoxo(d, r) : n.P2;
    } catch (e) {
      return null;
    }
  };
}

function User() {
  this.IdPlayer = -1, this.IsAdmin = !1, this.PlayerName = "", this.PlayerJID = "", this.Fleet = [], this.BSFleet = [], this.CurBoat = {}, this.LastLogin = 0, this.KeepAlive = function () {
    console.log("Keeping login alive..."), CheckLogin();
  }, setInterval(this.KeepAlive, 6e5);
}

function IsLoggedIn() {
  return _IsLoggedIn;
}

function OnLoginRequest() {
  CheckLogin(!0);
}

function GetPHPSessId() {
  var e,
      t = document.cookie.split(";");

  for (e in t) {
    if (t[e]) {
      var a = t[e].split("=");
      if (a[0] && "PHPSESSID" === a[0].trim()) return a[0];
    }
  }

  return null;
}

function CheckLogin(e) {
  var t = $(".UserName").val(),
      a = $(".UserPassword").val();
  GetPHPSessId() || "string" == typeof t && "string" == typeof a && t.trim().length > 0 && a.trim().length > 0 ? (ShowPb("#PbLoginProgress"), $.post("/ws/login.php", {
    VLM_AUTH_USER: t.trim(),
    VLM_AUTH_PW: a.trim()
  }, function (t) {
    var a = JSON.parse(t),
        i = null;
    _IsLoggedIn && (i = _CurPlayer.CurBoatID), _IsLoggedIn = !0 === a.success, HandleCheckLoginResponse(e), i && SetCurrentBoat(GetBoatFromIdu(select), !1);
  })) : HandleCheckLoginResponse(e);
}

function HandleCheckLoginResponse(e) {
  _IsLoggedIn ? GetPlayerInfo() : e && (VLMAlertDanger(GetLocalizedString("authfailed")), $(".UserPassword").val(""), setTimeout(function () {
    $("#LoginForm").modal("hide").modal("show");
  }, 1e3), initrecaptcha(!0, !1), $("#ResetPasswordLink").removeClass("hidden")), HidePb("#PbLoginProgress"), DisplayLoggedInMenus(_IsLoggedIn);
}

function Logout() {
  DisplayLoggedInMenus(!1), $.post("/ws/logout.php", function (e) {
    e.success ? window.location.reload() : (VLMAlertDanger("Something bad happened while logging out. Restart browser..."), windows.location.reload());
  }), _IsLoggedIn = !1;
}

var _CurPlayer = null;

function GetPlayerInfo() {
  ShowBgLoad(), $.get("/ws/playerinfo/profile.php", function (e) {
    e.success ? (void 0 !== _CurPlayer && _CurPlayer || (_CurPlayer = new User()), _CurPlayer.IdPlayer = e.profile.idp, _CurPlayer.IsAdmin = e.profile.admin, _CurPlayer.PlayerName = e.profile.playername, $.get("/ws/playerinfo/fleet_private.php", HandleFleetInfoLoaded), RefreshPlayerMenu()) : Logout();
  });
}

function HandleFleetInfoLoaded(e) {
  var t;
  void 0 === _CurPlayer && (_CurPlayer = new User()), void 0 === _CurPlayer.Fleet && (_CurPlayer.Fleet = []);

  for (var a in e.fleet) {
    void 0 === _CurPlayer.Fleet[a] && (_CurPlayer.Fleet[a] = new Boat(e.fleet[a]), void 0 === t && (t = _CurPlayer.Fleet[a]));
  }

  void 0 === _CurPlayer.fleet_boatsit && (_CurPlayer.fleet_boatsit = []);

  for (var _t18 in e.fleet_boatsit) {
    void 0 === _CurPlayer.BSFleet[_t18] && (_CurPlayer.BSFleet[_t18] = new Boat(e.fleet_boatsit[_t18]));
  }

  RefreshPlayerMenu(), void 0 !== t && t && (DisplayCurrentDDSelectedBoat(t), SetCurrentBoat(GetBoatFromIdu(t), !0), RefreshCurrentBoat(!0, !1));
}

function RefreshPlayerMenu() {
  $("#PlayerId").text(_CurPlayer.PlayerName), ClearBoatSelector();

  for (var e in _CurPlayer.Fleet) {
    AddBoatToSelector(_CurPlayer.Fleet[e], !0);
  }

  for (var _e18 in _CurPlayer.BSFleet) {
    _CurPlayer.BSFleet[_e18] && AddBoatToSelector(_CurPlayer.BSFleet[_e18], !1);
  }

  DisplayLoggedInMenus(!0), HideBgLoad("#PbLoginProgress");
}

function SetupUserMenu() {
  var e = $(document).width() / 2 - $(".UserMenu").width() / 2 + "px";
  $(".UserMenu").show(), $(".UserMenu").animate({
    left: e,
    top: 0
  }, 0);
}

function GetBoatFromIdu(e) {
  if (void 0 !== _CurPlayer) {
    var t = GetBoatFromBoatArray(_CurPlayer.Fleet, e);
    return void 0 === t && (t = GetBoatFromBoatArray(_CurPlayer.BSFleet, e)), t;
  }
}

function GetBoatFromBoatArray(e, t) {
  t = parseInt(t, 10);

  for (var a in e) {
    if (e[a] && e[a].IdBoat === t) return e[a];
  }
}

function GetFlagsList() {
  $.get("/ws/serverinfo/flags.php", function (e) {
    if (e.success) {
      var t = $("#CountryDropDownList"),
          a = 0;

      for (var i in e.flags) {
        if (e.flags[i]) {
          var n = e.flags[i];
          t.append("<li class='FlagLine DDLine' flag='" + n + "'>" + GetCountryDropDownSelectorHTML(n, !0, a++) + "</li>");
        }
      }
    }

    $(".FlagLine").on("click", HandleFlagLineClick);
  });
}

var FlagsIndexCache = [];

function GetCountryDropDownSelectorHTML(e, t, a) {
  if (t) {
    var _t19 = GetCountryFlagImg(e, a);

    FlagsIndexCache[e] = _t19;
  }

  var i = " <span  class='FlagLabel' flag='" + e + "'> - " + e + "</span>";
  return FlagsIndexCache[e] + i;
}

function GetCountryFlagImgHTML(e) {
  return FlagsIndexCache[e];
}

function GetCountryFlagImg(e, t) {
  return " <div class='FlagIcon' style='background-position: -" + t % 16 * 30 + "px -" + 20 * Math.floor(t / 16) + "px' flag='" + e + "'></div>";
}

var VLM_COORDS_FACTOR = 1e3;
var MapOptions = {
  projection: new OpenLayers.Projection("EPSG:900913"),
  displayProjection: new OpenLayers.Projection("EPSG:4326"),
  units: "m",
  maxResolution: 156543.0339,
  maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34, 20037508.34),
  restrictedExtent: new OpenLayers.Bounds(-40037508.34, -20037508.34, 40037508.34, 20037508.34),
  eventListeners: {
    zoomend: HandleMapZoomEnd,
    featureover: HandleFeatureOver,
    featureout: HandleFeatureOut,
    featureclick: HandleFeatureClick,
    mousemove: HandleMapMouseMove
  }
};
OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {
  defaultHandlerOptions: {
    single: !0,
    double: !1,
    pixelTolerance: 0,
    stopSingle: !1,
    stopDouble: !1
  },
  initialize: function initialize(e) {
    this.handlerOptions = OpenLayers.Util.extend({}, this.defaultHandlerOptions), OpenLayers.Control.prototype.initialize.apply(this, arguments), this.handler = new OpenLayers.Handler.Click(this, {
      click: this.trigger
    }, this.handlerOptions);
  },
  trigger: function trigger(e) {
    var t = GetVLMPositionFromClick(e.xy);
    "object" == _typeof(GM_Pos) && GM_Pos || (GM_Pos = {}), GM_Pos.lon = t.Lon.Value, GM_Pos.lat = t.Lat.Value, HandleMapMouseMove(e), SetWPPending && ("WP" === WPPendingTarget ? (CompleteWPSetPosition(e, e.xy), HandleCancelSetWPOnClick()) : "AP" === WPPendingTarget ? (SetWPPending = !1, _CurAPOrder.PIP_Coords = GetVLMPositionFromClick(e.xy), $("#AutoPilotSettingForm").modal("show"), RefreshAPDialogFields()) : SetWPPending = !1);
  }
});
var BoatFeatures = [],
    OppPopups = [],
    StartSetWPOnClick = !1;

function SetCurrentBoat(e, t, a, i) {
  CheckBoatRefreshRequired(e, t, a, i);
}

var BoatLoading = new Date(0);

function CheckBoatRefreshRequired(e, t, a, i) {
  if (void 0 !== e && e) {
    var n = new Date(),
        o = void 0 !== e && (void 0 === e.VLMInfo || void 0 === e.VLMInfo.AVG);
    UpdatePrefsDialog(e), void 0 !== e.VLMInfo && void 0 !== e.VLMInfo.LUP || (a = !0), a || n >= e.NextServerRequestDate ? (BoatLoading = n + 3e3, console.log("Loading boat info from server...."), ShowPb("#PbGetBoatProgress"), $.get("/ws/boatinfo.php?forcefmt=json&select_idu=" + e.IdBoat, function (a) {
      e.IdBoat === parseInt(a.IDU, 10) && (_CurPlayer.CurBoat = e, LoadVLMPrefs(), e.VLMInfo = a, e.NextServerRequestDate = new Date(1e3 * (parseInt(e.VLMInfo.LUP, 10) + parseInt(e.VLMInfo.VAC, 10))), e.LastRefresh = new Date(), e.VLMInfo.LON /= VLM_COORDS_FACTOR, e.VLMInfo.LAT /= VLM_COORDS_FACTOR, o && UpdatePrefsDialog(e), "0" !== e.VLMInfo.RAC ? (void 0 === e.RaceInfo || void 0 === e.RaceInfo.idraces ? (GetRaceInfoFromServer(e, i), GetRaceExclusionsFromServer(e)) : (DrawRaceGates(e.RaceInfo, e.VLMInfo.NWP), DrawRaceExclusionZones(VLMBoatsLayer, e.Exclusions)), GetTrackFromServer(e), e.VLMInfo && e.VLMInfo.RAC && LoadRankings(e.VLMInfo.RAC), LoadRealsList(e), DrawBoat(e, t), UpdateInMenuRacingBoatInfo(e, i)) : UpdateInMenuDockingBoatInfo(e)), HidePb("#PbGetBoatProgress"), OnPlayerLoadedCallBack && (OnPlayerLoadedCallBack(), OnPlayerLoadedCallBack = null);
    })) : e && (UpdateInMenuDockingBoatInfo(e), DrawBoat(e, t), DrawRaceGates(e.RaceInfo, e.VLMInfo.NWP), DrawRaceExclusionZones(VLMBoatsLayer, e.Exclusions));
  }
}

function GetTrackFromServer(e) {
  var t = Math.floor(new Date() / 1e3),
      a = t - 86400;
  $.get("/ws/boatinfo/tracks_private.php?idu=" + e.IdBoat + "&idr=" + e.VLMInfo.RAC + "&starttime=" + a + "&endtime=" + t, function (t) {
    if (t.success) {
      void 0 !== e.Track ? e.Track.length = 0 : e.Track = [];

      for (var i in t.tracks) {
        if (t.tracks[i]) {
          var a = new VLMPosition(t.tracks[i][1] / 1e3, t.tracks[i][2] / 1e3);
          e.Track.push(a);
        }
      }

      DrawBoat(e);
    }
  });
}

function GetRaceExclusionsFromServer(e) {
  $.get("/ws/raceinfo/exclusions.php?idrace=" + e.VLMInfo.RAC + "&v=" + e.VLMInfo.VER, function (t) {
    if (t.success) {
      var i,
          n,
          o = [],
          r = [];

      for (n in t.Exclusions) {
        if (t.Exclusions[n]) {
          var a = t.Exclusions[n];
          (void 0 === i || i[0] !== a[0][0] && i[1] !== a[0][1]) && (void 0 !== i && (o.push(r), r = []), r.push(a[0])), i = a[1], r.push(a[1]);
        }
      }

      o.push(r), e.Exclusions = o, DrawRaceExclusionZones(VLMBoatsLayer, o);
    }
  });
}

function GetRaceInfoFromServer(e, t) {
  $.get("/ws/raceinfo/desc.php?idrace=" + e.VLMInfo.RAC + "&v=" + e.VLMInfo.VER, function (a) {
    e.RaceInfo = a, DrawRaceGates(e.RaceInfo, e.VLMInfo.NWP), UpdateInMenuRacingBoatInfo(e, t);
  });
}

var DrawBoatTimeOutHandle = null,
    DeferredCenterValue = !1;

function DrawBoat(e, t) {
  void 0 !== t && (DeferredCenterValue = DeferredCenterValue || t), console.log("Call DrawbBoat (" + t + ") deferred : " + DeferredCenterValue), DrawBoatTimeOutHandle && (console.log("Pushed DrawBoat"), clearTimeout(DrawBoatTimeOutHandle)), DrawBoatTimeOutHandle = setTimeout(ActualDrawBoat, 100, e, DeferredCenterValue);
}

function ActualDrawBoat(e, t) {
  if (console.log("ClearDrawBoat " + t), DeferredCenterValue = !1, DrawBoatTimeOutHandle = null, void 0 === e || !e) {
    if (void 0 === _CurPlayer || !_CurPlayer || void 0 === _CurPlayer.CurBoat || !_CurPlayer.CurBoat) return;
    e = _CurPlayer.CurBoat;
  }

  for (var _e19 in BoatFeatures) {
    BoatFeatures[_e19] && VLMBoatsLayer.removeFeatures(BoatFeatures[_e19]);
  }

  BoatFeatures = [];
  var a = null;

  if (void 0 !== e && e && e.GetNextWPPosition && (a = e.GetNextWPPosition()), void 0 !== a && a) {
    var _e20 = new OpenLayers.Geometry.Point(a.Lon.Value, a.Lat.Value).transform(MapOptions.displayProjection, MapOptions.projection),
        _t20 = new OpenLayers.Feature.Vector(_e20, {}, {
      externalGraphic: "images/WP_Marker.gif",
      graphicHeight: 48,
      graphicWidth: 48
    });

    BoatFeatures.push(_t20), VLMBoatsLayer.addFeatures(_t20);
  }

  if (void 0 !== _typeof(e.VLMInfo) && e.VLMInfo && (e.VLMInfo.LON || e.VLMInfo.LAT)) {
    var _a14 = new OpenLayers.Geometry.Point(e.VLMInfo.LON, e.VLMInfo.LAT).transform(MapOptions.displayProjection, MapOptions.projection),
        _r = new OpenLayers.Feature.Vector(_a14, {
      Id: e.IdBoat
    }, {
      externalGraphic: "images/target.svg",
      graphicHeight: 64,
      graphicWidth: 64,
      rotation: e.VLMInfo.HDG
    });

    VLMBoatsLayer.addFeatures(_r), BoatFeatures.push(_r);
    var i = PolarsManager.GetPolarLine(e.VLMInfo.POL, e.VLMInfo.TWS, DrawBoat, e),
        n = [];

    if (void 0 !== map && map) {
      map.getViewPortPxFromLonLat(_a14);

      var _r2 = VLM2Prefs.MapPrefs.PolarVacCount,
          _s3 = new VLMPosition(e.VLMInfo.LON, e.VLMInfo.LAT);

      BuildPolarLine(e, i, n, _s3, _r2, new Date(1e3 * e.VLMInfo.LUP), function () {
        DrawBoat(e, t);
      });
      var o = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.LineString(n), {
        type: "Polar",
        WindDir: e.VLMInfo.TWD
      });
      BoatFeatures.push(o), VLMBoatsLayer.addFeatures(o);
    }
  }

  if (void 0 !== e.Track && e.Track.length > 0) {
    var _t21 = [],
        _a15 = e.Track.length,
        _i8 = 99999,
        _n3 = 0;

    for (var _o2 = 0; _o2 < _a15; _o2++) {
      var _a16 = e.Track[_o2];
      99999 !== _i8 && (_n3 += GetLonOffset(_i8, _a16.Lon.Value)), _i8 = _a16.Lon.Value;

      var _r3 = new OpenLayers.Geometry.Point(_a16.Lon.Value + _n3, _a16.Lat.Value).transform(MapOptions.displayProjection, MapOptions.projection);

      _t21.push(_r3);
    }

    var r = e.VLMInfo.COL;
    "#" !== r[0] && (r = "#" + r);
    var s = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.LineString(_t21), {
      type: "HistoryTrack",
      TrackColor: r
    });
    VLMBoatsLayer.addFeatures(s), BoatFeatures.push(s);
  }

  if (e.Estimator && e.Estimator.EstimateTrack.length !== e.Estimator.EstimatePoints.length) {
    e.Estimator.EstimatePoints[0] = [];
    var _t22 = 0,
        _a17 = 99999,
        _i9 = 0;

    for (var _n4 in e.Estimator.EstimateTrack) {
      if (e.Estimator.EstimateTrack[_n4]) {
        var _o3 = e.Estimator.EstimateTrack[_n4];
        99999 !== _a17 && (_i9 += GetLonOffset(_a17, _o3.Position.Lon.Value)), _a17 = _o3.Position.Lon.Value;

        var _r4 = new OpenLayers.Geometry.Point(_o3.Position.Lon.Value + _i9, _o3.Position.Lat.Value).transform(MapOptions.displayProjection, MapOptions.projection);

        e.Estimator.EstimatePoints[_t22].push(_r4);
      }
    }
  }

  if (void 0 !== e.Estimator && e.Estimator && e.Estimator.EstimatePoints) for (var _t23 in e.Estimator.EstimatePoints) {
    if (e.Estimator.EstimatePoints[_t23]) {
      var l = e.Estimator.EstimatePoints[_t23],
          d = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.LineString(l), {
        type: "ForecastPos"
      });
      BoatFeatures.push(d), VLMBoatsLayer.addFeatures(d);
    }
  }
  if (DrawOpponents(e, VLMBoatsLayer, BoatFeatures), void 0 !== e.OppTrack && Object.keys(e.OppTrack).length > 0) for (var _t24 in e.OppTrack) {
    var u = e.OppTrack[_t24];

    if (u && u.Visible && u.DatePos.length > 1) {
      if (!u.OLTrackLine) {
        var _e22 = [],
            _t25 = Object.keys(u.DatePos).length;

        for (var _a18 = 0; _a18 < _t25; _a18++) {
          var _t26 = Object.keys(u.DatePos)[_a18],
              _i10 = u.DatePos[_t26],
              _n5 = new OpenLayers.Geometry.Point(_i10.lon, _i10.lat).transform(MapOptions.displayProjection, MapOptions.projection);

          _e22.push(_n5);
        }

        u.OLTrackLine = _e22;
      }

      var _e21 = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.LineString(u.OLTrackLine), {
        type: "HistoryTrack",
        TrackColor: u.TrackColor
      });

      u.LastShow = new Date(), VLMBoatsLayer.addFeatures(_e21), BoatFeatures.push(_e21);
    }
  }

  if (t && void 0 !== e.VLMInfo && e.VLMInfo) {
    var c = new OpenLayers.LonLat(e.VLMInfo.LON, e.VLMInfo.LAT).transform(MapOptions.displayProjection, MapOptions.projection);
    if (isNaN(c.lat) || isNaN(c.lon)) ;
    void 0 !== map && map && map.setCenter(c);
  } else if (t) ;

  console.log("ActualDrawBoatComplete");
}

function BuildPolarLine(e, t, a, i, n, o, r) {
  var s = o;
  (!s || s < new Date().getTime()) && (s = new Date().getTime());
  var l = GribMgr.WindAtPointInTime(s, i.Lat.Value, i.Lon.Value, r);

  if (l) {
    var _t27;

    parseFloat(e.VLMInfo.HDG);

    for (_t27 = 0; _t27 <= 180; _t27 += 5) {
      var _o4 = PolarsManager.GetBoatSpeed(e.VLMInfo.POL, l.Speed, l.Heading, l.Heading + _t27);

      if (isNaN(_o4)) return;
      var d;

      for (d = -1; d <= 1; d += 2) {
        var _r5 = i.ReachDistLoxo(_o4 / 3600 * e.VLMInfo.VAC * n, l.Heading + _t27 * d),
            _s4 = new OpenLayers.Geometry.Point(_r5.Lon.Value, _r5.Lat.Value).transform(MapOptions.displayProjection, MapOptions.projection);

        a[180 + d * _t27] = _s4;
      }
    }
  }
}

function GetVLMPositionFromClick(e) {
  if (map) {
    var t = map.getLonLatFromPixel(e).transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));
    return new VLMPosition(t.lon, t.lat);
  }

  return null;
}

function CompleteWPSetPosition(e, t) {
  var a = GetVLMPositionFromClick(t);
  console.log("DragComplete " + e.id), VLMBoatsLayer.removeFeatures(e), SendVLMBoatWPPos(_CurPlayer.CurBoat, a);
}

var renderer = OpenLayers.Util.getParameters(window.location.href).renderer;
renderer = renderer ? [renderer] : OpenLayers.Layer.Vector.prototype.renderers;
var VectorStyles = new OpenLayers.Style({
  strokeColor: "#00FF00",
  strokeOpacity: 1,
  strokeWidth: 3,
  fillColor: "#FF5500",
  fillOpacity: .5
}, {
  rules: [new OpenLayers.Rule({
    filter: new OpenLayers.Filter.Comparison({
      type: OpenLayers.Filter.Comparison.EQUAL_TO,
      property: "type",
      value: "buoy"
    }),
    symbolizer: {
      label: "${name}\n${Coords}",
      pointerEvents: "visiblePainted",
      fontSize: "1.5em",
      labelAlign: "left",
      labelXOffset: "4",
      labelYOffset: "-12",
      externalGraphic: "images/${GateSide}",
      graphicWidth: 36,
      graphicHeight: 72,
      fillOpacity: 1
    }
  }), new OpenLayers.Rule({
    filter: new OpenLayers.Filter.Comparison({
      type: OpenLayers.Filter.Comparison.EQUAL_TO,
      property: "type",
      value: "crossonce"
    }),
    symbolizer: {
      xOffset: 1,
      yOffset: 1,
      strokeColor: "black",
      strokeOpacity: .5,
      strokeWidth: 4,
      strokeDashstyle: "dashdot"
    }
  }), new OpenLayers.Rule({
    filter: new OpenLayers.Filter.Comparison({
      type: OpenLayers.Filter.Comparison.EQUAL_TO,
      property: "type",
      value: "marker"
    }),
    symbolizer: {
      externalGraphic: "images/${BuoyName}",
      rotation: "${CrossingDir}",
      graphicWidth: 48
    }
  }), new OpenLayers.Rule({
    filter: new OpenLayers.Filter.Comparison({
      type: OpenLayers.Filter.Comparison.EQUAL_TO,
      property: "type",
      value: "NextGate"
    }),
    symbolizer: {
      strokeColor: "#FF0000",
      strokeOpacity: 1,
      strokeWidth: 3
    }
  }), new OpenLayers.Rule({
    filter: new OpenLayers.Filter.Comparison({
      type: OpenLayers.Filter.Comparison.EQUAL_TO,
      property: "type",
      value: "ValidatedGate"
    }),
    symbolizer: {
      strokeColor: "#0000FF",
      strokeOpacity: .5,
      strokeWidth: 3
    }
  }), new OpenLayers.Rule({
    filter: new OpenLayers.Filter.Comparison({
      type: OpenLayers.Filter.Comparison.EQUAL_TO,
      property: "type",
      value: "FutureGate"
    }),
    symbolizer: {
      strokeColor: "#FF0000",
      strokeOpacity: .5,
      strokeWidth: 3
    }
  }), new OpenLayers.Rule({
    filter: new OpenLayers.Filter.Comparison({
      type: OpenLayers.Filter.Comparison.EQUAL_TO,
      property: "type",
      value: "ForecastPos"
    }),
    symbolizer: {
      strokeColor: "black",
      strokeOpacity: .75,
      strokeWidth: 1
    }
  }), new OpenLayers.Rule({
    filter: new OpenLayers.Filter.Comparison({
      type: OpenLayers.Filter.Comparison.EQUAL_TO,
      property: "type",
      value: "HistoryTrack"
    }),
    symbolizer: {
      strokeOpacity: .5,
      strokeWidth: 2,
      strokeColor: "${TrackColor}"
    }
  }), new OpenLayers.Rule({
    filter: new OpenLayers.Filter.Comparison({
      type: OpenLayers.Filter.Comparison.EQUAL_TO,
      property: "type",
      value: "Polar"
    }),
    symbolizer: {
      strokeColor: "white",
      strokeOpacity: .75,
      strokeWidth: 2
    }
  }), new OpenLayers.Rule({
    filter: new OpenLayers.Filter.Comparison({
      type: OpenLayers.Filter.Comparison.EQUAL_TO,
      property: "type",
      value: "ExclusionZone"
    }),
    symbolizer: {
      strokeColor: "red",
      strokeOpacity: .95,
      strokeWidth: 2,
      fillColor: "#FF5500",
      fillOpacity: .5
    }
  }), new OpenLayers.Rule({
    filter: new OpenLayers.Filter.Comparison({
      type: OpenLayers.Filter.Comparison.EQUAL_TO,
      property: "type",
      value: "opponent"
    }),
    symbolizer: {
      label: "${name}",
      pointerEvents: "visiblePainted",
      fontSize: "1.5em",
      labelAlign: "left",
      labelXOffset: "4",
      labelYOffset: "-12",
      externalGraphic: "images/opponent${IsTeam}.png",
      graphicWidth: "${IsFriend}",
      fillOpacity: 1
    }
  }), new OpenLayers.Rule({
    elsefilter: !0,
    symbolizer: {}
  })]
}),
    VLMBoatsLayer = new OpenLayers.Layer.Vector("VLM Boats and tracks", {
  styleMap: new OpenLayers.StyleMap(VectorStyles),
  renderers: renderer
});

function GetBoatControllerPopup() {
  return $("#BoatController").load("BoatController.html"), '<div id="BoatController"></div>';
}

var WP_TWO_BUOYS = 0,
    WP_ONE_BUOY = 1,
    WP_GATE_BUOY_MASK = 15,
    WP_DEFAULT = 0,
    WP_ICE_GATE_N = 16,
    WP_ICE_GATE_S = 32,
    WP_ICE_GATE_E = 64,
    WP_ICE_GATE_W = 128,
    WP_ICE_GATE = 64 | WP_ICE_GATE_N | WP_ICE_GATE_S | 128,
    WP_GATE_KIND_MASK = 65520,
    WP_CROSS_CLOCKWISE = 256,
    WP_CROSS_ANTI_CLOCKWISE = 512,
    WP_CROSS_ONCE = 1024;
var RaceGates = [],
    Exclusions = [];

function DrawRaceGates(e, t) {
  for (var _e23 in RaceGates) {
    RaceGates[_e23] && VLMBoatsLayer.removeFeatures(RaceGates[_e23]);
  }

  for (var n in e.races_waypoints) {
    if (e.races_waypoints[n]) {
      var a = e.races_waypoints[n];
      NormalizeRaceInfo(e);
      var i = !(a.wpformat & WP_CROSS_ANTI_CLOCKWISE);
      if (AddBuoyMarker(VLMBoatsLayer, RaceGates, "WP" + n + " " + a.libelle + "\n", a.longitude1, a.latitude1, i), (a.wpformat & WP_GATE_BUOY_MASK) === WP_TWO_BUOYS) AddBuoyMarker(VLMBoatsLayer, RaceGates, "", a.longitude2, a.latitude2, !i);else {
        var _e24 = new VLMPosition(a.longitude1, a.latitude1),
            _t28 = !1,
            _i11 = 2500,
            _n6 = null;

        for (; !_t28;) {
          try {
            _n6 = _e24.ReachDistLoxo(_i11, 180 + parseFloat(a.laisser_au)), _t28 = !0;
          } catch (e) {
            _i11 *= .7;
          }
        }

        a.longitude2 = _n6.Lon.Value, a.latitude2 = _n6.Lat.Value;
      }
      n = parseInt(n, 10), t = parseInt(t, 10), AddGateSegment(VLMBoatsLayer, RaceGates, a.longitude1, a.latitude1, a.longitude2, a.latitude2, t === n, n < t, a.wpformat & WP_GATE_KIND_MASK);
    }
  }
}

function DrawRaceExclusionZones(e, t) {
  var a;

  for (a in Exclusions) {
    Exclusions[a] && e.removeFeatures(Exclusions[a]);
  }

  for (a in t) {
    t[a] && DrawRaceExclusionZone(e, Exclusions, t[a]);
  }
}

function DrawRaceExclusionZone(e, t, a) {
  var i,
      n = [];

  for (i in a) {
    if (a[i]) {
      var o = new OpenLayers.Geometry.Point(a[i][1], a[i][0]).transform(MapOptions.displayProjection, MapOptions.projection);
      n.push(o);
    }
  }

  var r;
  r = {
    type: "ExclusionZone"
  };
  var s = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon(new OpenLayers.Geometry.LinearRing(n)), r, null);
  e.addFeatures(s), t.push(s);
}

function GetLonOffset(e, t) {
  return e * t >= 0 ? 0 : Math.abs(t - e) > 90 ? e > 0 ? 360 : -360 : 0;
}

function AddGateSegment(e, t, a, i, n, o, r, s, l) {
  var d = new OpenLayers.Geometry.Point(a, i),
      u = GetLonOffset(a, n),
      c = new OpenLayers.Geometry.Point(n + u, o);
  var h = d.transform(MapOptions.displayProjection, MapOptions.projection),
      p = c.transform(MapOptions.displayProjection, MapOptions.projection),
      f = [];
  f.push(h), f.push(p);
  var P = null;
  P = r ? {
    type: "NextGate"
  } : s ? {
    type: "ValidatedGate"
  } : {
    type: "FutureGate"
  };
  var g = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.LineString(f), P, null);

  if (e.addFeatures(g), t.push(g), l !== WP_DEFAULT) {
    d = new VLMPosition(a, i), c = new VLMPosition(n, o);
    var L = d.GetLoxoCourse(c),
        m = d.ReachDistLoxo(c, .5);
    l & WP_CROSS_ANTI_CLOCKWISE ? (L -= 90, AddGateDirMarker(VLMBoatsLayer, t, m.Lon.Value, m.Lat.Value, L)) : l & WP_CROSS_CLOCKWISE ? (L += 90, AddGateDirMarker(VLMBoatsLayer, t, m.Lon.Value, m.Lat.Value, L)) : l & WP_ICE_GATE && AddGateIceGateMarker(VLMBoatsLayer, t, m.Lon.Value, m.Lat.Value), l & WP_CROSS_ONCE && (g = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.LineString(f), {
      type: "crossonce"
    }, null), e.addFeatures(g), t.push(g));
  }
}

var MAX_BUOY_INDEX = 16;
var BuoyIndex = Math.floor(Math.random() * MAX_BUOY_INDEX);

function AddGateDirMarker(e, t, a, i, n) {
  AddGateCenterMarker(e, t, a, i, "BuoyDirs/BuoyDir" + BuoyIndex + ".png", n, !0), BuoyIndex++, BuoyIndex %= MAX_BUOY_INDEX + 1;
}

function AddGateIceGateMarker(e, t, a, i) {
  AddGateCenterMarker(e, t, a, i, "icegate.png", "");
}

function AddGateCenterMarker(e, t, a, i, n, o, r) {
  var s = new VLMPosition(a, i),
      l = new OpenLayers.Geometry.Point(s.Lon.Value, s.Lat.Value).transform(MapOptions.displayProjection, MapOptions.projection),
      d = new OpenLayers.Feature.Vector(l, {
    type: "marker",
    BuoyName: n,
    CrossingDir: o,
    yOffset: r ? -18 : 0
  });
  e.addFeatures(d), t.push(n);
}

function AddBuoyMarker(e, t, a, i, n, o) {
  var r,
      s = new VLMPosition(i, n),
      l = new OpenLayers.Geometry.Point(s.Lon.Value, s.Lat.Value).transform(MapOptions.displayProjection, MapOptions.projection);
  r = o ? new OpenLayers.Feature.Vector(l, {
    name: a,
    Coords: s.ToString(),
    type: "buoy",
    GateSide: "Buoy1.png"
  }) : new OpenLayers.Feature.Vector(l, {
    name: a,
    Coords: s.ToString(),
    type: "buoy",
    GateSide: "Buoy2.png"
  }), e.addFeatures(r), t.push(r);
}

var PM_HEADING = 1,
    PM_ANGLE = 2,
    PM_ORTHO = 3,
    PM_VMG = 4,
    PM_VBVMG = 5;

function SendVLMBoatWPPos(e, t) {
  var a = {
    idu: e.IdBoat,
    pip: {
      targetlat: t.Lat.Value,
      targetlong: t.Lon.Value,
      targetandhdg: -1
    }
  };
  PostBoatSetupOrder(e.IdBoat, "target_set", a);
}

function SendVLMBoatOrder(e, t, a, i) {
  var n = {};

  if (void 0 !== _CurPlayer && void 0 !== _CurPlayer.CurBoat) {
    switch (e) {
      case PM_HEADING:
      case PM_ANGLE:
        n = {
          idu: _CurPlayer.CurBoat.IdBoat,
          pim: e,
          pip: t
        };
        break;

      case PM_ORTHO:
      case PM_VBVMG:
      case PM_VMG:
        n = {
          idu: _CurPlayer.CurBoat.IdBoat,
          pim: e,
          pip: {
            targetlong: parseFloat(t),
            targetlat: parseFloat(a),
            targetandhdg: i
          }
        };
        break;

      default:
        return;
    }

    PostBoatSetupOrder(_CurPlayer.CurBoat.IdBoat, "pilot_set", n);
  } else VLMAlertDanger("Must select a boat to send an order");
}

function PostBoatSetupOrder(e, t, a) {
  $.post("/ws/boatsetup/" + t + ".php?selectidu" + e, "parms=" + JSON.stringify(a), function (e, t) {
    e.success ? RefreshCurrentBoat(!1, !0) : VLMAlertDanger(GetLocalizedString("BoatSetupError") + "\n" + e.error.code + " " + e.error.msg);
  });
}

function EngageBoatInRace(e, t) {
  $.post("/ws/boatsetup/race_subscribe.php", "parms=" + JSON.stringify({
    idu: t,
    idr: parseInt(e, 10)
  }), function (e) {
    if (e.success) {
      var _e25 = GetLocalizedString("youengaged");

      $("#RacesListForm").modal("hide"), VLMAlertSuccess(_e25);
    } else {
      VLMAlertDanger(e.error.msg + "\n" + e.error.custom_error_string);
    }
  });
}

function DiconstinueRace(e, t) {
  $.post("/ws/boatsetup/race_unsubscribe.php", "parms=" + JSON.stringify({
    idu: e,
    idr: parseInt(t, 10)
  }), function (e) {
    e.success ? VLMAlertSuccess("Bye Bye!") : VLMAlertDanger(e.error.msg + "\n" + e.error.custom_error_string);
  });
}

function HandleMapZoomEnd(e, t) {
  var a = VLMBoatsLayer.getZoomForResolution(VLMBoatsLayer.getResolution());
  VLM2Prefs.MapPrefs.MapZoomLevel = a, VLM2Prefs.Save(), RefreshCurrentBoat(!1);
}

function LoadRealsList(e) {
  void 0 !== e && e && void 0 !== e.VLMInfo && $.get("/ws/realinfo/realranking.php?idr=" + e.VLMInfo.RAC, function (t) {
    t.success ? (e.Reals = t, DrawBoat(e, !1)) : e.Reals = [];
  });
}

function LoadRankings(e, t) {
  e && "object" == _typeof(e) && VLMAlertDanger("NOt updated call to LoadRankings"), $.get("/cache/rankings/rnk_" + e + ".json?d=" + new Date().getTime(), function (a) {
    a ? (Rankings[e] = a.Boats, t ? t() : DrawBoat(null, !1)) : Rankings[e] = null;
  });
}

function contains(e, t) {
  for (var a = 0; a < e.length; a++) {
    if (e[a] === t) return !0;
  }

  return !1;
}

function DrawOpponents(e, t, a) {
  if (!e || void 0 === Rankings) return;
  var i,
      n = [];
  if (VLM2Prefs.MapPrefs.MapOppShow === VLM2Prefs.MapPrefs.MapOppShowOptions.ShowSel) for (i in void 0 !== e.VLMInfo && void 0 !== e.VLMInfo.MPO && (n = e.VLMInfo.MPO.split(",")), n) {
    if (n[i]) {
      var _o5 = Rankings[n[i]];
      void 0 !== _o5 && parseInt(_o5.idusers, 10) !== e.IdBoat && AddOpponent(e, t, a, _o5, !0);
    }
  }
  if (VLM2Prefs.MapPrefs.ShowReals && void 0 !== e.Reals && void 0 !== e.Reals.ranking) for (i in e.Reals.ranking) {
    AddOpponent(e, t, a, e.Reals.ranking[i], !0);
  }
  var o = 150,
      r = o / Object.keys(Rankings).length,
      s = 0,
      l = Rankings;

  switch (void 0 !== e.OppList && e.OppList.length > 0 && (l = e.OppList, r = 1), VLM2Prefs.MapPrefs.MapOppShow) {
    case VLM2Prefs.MapPrefs.MapOppShowOptions.Show10Around:
      l = GetClosestOpps(e, 10), r = 1;
      break;

    case VLM2Prefs.MapPrefs.MapOppShowOptions.Show5Around:
      l = GetClosestOpps(e, 5), r = 1;
      break;

    case VLM2Prefs.MapPrefs.MapOppShowOptions.ShowTop10:
      var _t29 = 0,
          _a19 = e.Engaged;

      for (i in o = VLM2Prefs.MapPrefs.ShowTopCount, l = [], Rankings[_a19]) {
        if (Rankings[_a19][i].rank <= VLM2Prefs.MapPrefs.ShowTopCount && (l[i] = Rankings[_a19][i], ++_t29 > o)) break;
      }

      _t29 > o && (o = _t29), r = 1;
      break;

    case VLM2Prefs.MapPrefs.MapOppShowOptions.ShowMineOnly:
      l = [], r = 1;
  }

  if (SortRankingData(e, "RAC", null, e.Engaged), e.Engaged && void 0 !== Rankings[e.Engaged] && void 0 !== Rankings[e.Engaged].RacerRanking && Rankings[e.Engaged].RacerRanking) for (i in Rankings[e.Engaged].RacerRanking) {
    if (i in Rankings[e.Engaged].RacerRanking) {
      var d = Rankings[e.Engaged].RacerRanking[i];
      if (parseInt(d.idusers, 10) !== e.IdBoat && l[d.idusers] && !contains(n, d.idusers) && RnkIsRacing(d) && Math.random() <= r && s < o) AddOpponent(e, t, a, d, !1), s += 1, void 0 === e.OppList && (e.OppList = []), e.OppList[i] = d;else if (s >= o) break;
    }
  }
}

function CompareDist(e, t) {
  return e.dnm < t.dnm ? -1 : e.dnm > t.dnm ? 1 : 0;
}

function GetClosestOpps(e, t) {
  var a = Rankings[e.IdBoat];
  void 0 !== a && e || (a = {
    dnm: 0,
    nwm: 1
  });
  var i = null;
  e && e.VLMInfo && (i = e.VLMInfo.RAC);
  var n = [];

  if (i) {
    var _e26 = parseFloat(a.dnm),
        r = a.nwp,
        s = [];

    for (var _t30 in Rankings[i]) {
      if (Rankings[i][_t30] && r === Rankings[i][_t30].nwp) {
        var o = {
          id: _t30,
          dnm: Math.abs(_e26 - parseFloat(Rankings[i][_t30].dnm))
        };
        s.push(o);
      }
    }

    s = s.sort(CompareDist);

    for (var _e27 in s.slice(0, t - 1)) {
      n[s[_e27].id] = Rankings[i][s[_e27].id];
    }
  }

  return n;
}

function AddOpponent(e, t, a, i, n) {
  var o,
      r = new VLMPosition(i.longitude, i.latitude),
      s = new OpenLayers.Geometry.Point(r.Lon.Value, r.Lat.Value).transform(MapOptions.displayProjection, MapOptions.projection),
      l = {
    name: i.idusers + " - " + i.boatname,
    Coords: r.ToString(),
    type: "opponent",
    idboat: i.idusers,
    rank: i.rank,
    Last1h: i.last1h,
    Last3h: i.last3h,
    Last24h: i.last24h,
    IsTeam: i.country == e.VLMInfo.CNT ? "team" : "",
    IsFriend: n ? 24 : 12,
    color: i.color
  };
  VLM2Prefs.MapPrefs.ShowOppNames || (l.name = ""), o = new OpenLayers.Feature.Vector(s, l), t.addFeatures(o), a.push(o);
}

function ShowOpponentPopupInfo(e) {
  if ("opponent" == e.feature.data.type) {
    var t = GetOppBoat(e.feature.attributes.idboat),
        a = new VLMPosition(t.longitude, t.latitude),
        i = [],
        n = e.feature;
    OppPopups[e.feature.attributes.idboat] && (map.removePopup(OppPopups[e.feature.attributes.idboat]), OppPopups[e.feature.attributes.idboat] = null);
    var o = new OpenLayers.Popup.FramedCloud("popup", OpenLayers.LonLat.fromString(n.geometry.toShortString()), null, BuildBoatPopupInfo(e.feature.attributes.idboat), null, !0, null);
    o.autoSize = !0, o.maxSize = new OpenLayers.Size(400, 800), o.fixedRelativePosition = !0, n.popup = o, map.addPopup(o), OppPopups[e.feature.attributes.idboat] = o, i.push([FIELD_MAPPING_TEXT, "#__BoatName" + e.feature.attributes.idboat, t.boatname]), i.push([FIELD_MAPPING_TEXT, "#__BoatId" + e.feature.attributes.idboat, e.feature.attributes.idboat]), i.push([FIELD_MAPPING_TEXT, "#__BoatRank" + e.feature.attributes.idboat, e.feature.attributes.rank]), i.push([FIELD_MAPPING_TEXT, "#__BoatLoch" + e.feature.attributes.idboat, RoundPow(t.loch)]), i.push([FIELD_MAPPING_TEXT, "#__BoatPosition" + e.feature.attributes.idboat, a.GetVLMString()]), i.push([FIELD_MAPPING_TEXT, "#__Boat1HAvg" + e.feature.attributes.idboat, RoundPow(parseFloat(t.last1h), 2)]), i.push([FIELD_MAPPING_TEXT, "#__Boat3HAvg" + e.feature.attributes.idboat, RoundPow(parseFloat(t.last3h), 2)]), i.push([FIELD_MAPPING_TEXT, "#__Boat24HAvg" + e.feature.attributes.idboat, RoundPow(parseFloat(t.last24h), 2)]), FillFieldsFromMappingTable(i);
  }
}

function GetOppBoat(e) {
  var t = _CurPlayer.CurBoat;
  if (void 0 !== t && t && t.OppList) for (var a in t.OppList) {
    if (t.OppList[a]) {
      var i = t.OppList[a];
      if (i.idusers === e) return i;
    }
  }
  return null;
}

function BuildBoatPopupInfo(e) {
  return '<div class="MapPopup_InfoHeader"> <img class="flag" src="https://v-l-m.org/cache/flags/ZZ-T4F.png"> <span id="__BoatName' + e + '" class="PopupBoatNameNumber ">BoatName</span> <span id="__BoatId' + e + '" class="PopupBoatNameNumber ">BoatNumber</span> <div id="__BoatRank' + e + '" class="TxtRank">Rank</div></div><div class="MapPopup_InfoBody"> <fieldset>   <span class="PopupHeadText " I18n="loch">' + GetLocalizedString("loch") + '</span><span class="PopupText"> : </span><span id="__BoatLoch' + e + '" class="loch PopupText">0.9563544</span>   <BR><span class="PopupHeadText " I18n="position">' + GetLocalizedString("position") + '</span><span class="PopupText"> : </span><span id="__BoatPosition' + e + '" class=" PopupText">0.9563544</span>   <BR><span class="PopupHeadText " I18n="NextWP">' + GetLocalizedString("NextWP") + '</span><span class="strong"> : </span><span id="__BoatNWP' + e + '" class="PopupText">[1] 4.531856536865234</span>   <BR><span class="PopupHeadText " I18n="Moyennes">' + GetLocalizedString("Moyennes") + ' </span><span class="PopupText"> : </span>   <span class="PopupHeadText ">[1h]</span><span id="__Boat1HAvg' + e + '" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span>   <span class="PopupHeadText ">[3h]</span><span id="__Boat3HAvg' + e + '" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span>   <span class="PopupHeadText ">[24h]</span><span id="__Boat24HAvg' + e + '" class="PopupText">[1H] </strong>0.946785,[3H] 0.946785,[24H] 0.946785 </span> </fieldset></div>';
}

function HandleFeatureOver(e) {
  var t;

  if ("opponent" == e.feature.data.type) {
    for (t in _CurPlayer.CurBoat.OppTrack) {
      _CurPlayer.CurBoat.OppTrack[t].Visible = !1;
    }

    DrawOpponentTrack(e.feature.data);
  }
}

function HandleFeatureClick(e) {
  HandleFeatureOver(e), ShowOpponentPopupInfo(e);
}

function HandleFeatureOut(e) {
  if (void 0 !== _CurPlayer && void 0 !== _CurPlayer.CurBoat && void 0 !== _CurPlayer.CurBoat.OppTrack) for (var _e28 in _CurPlayer.CurBoat.OppTrack) {
    _CurPlayer.CurBoat.OppTrack[_e28].Visible = !1;
  }
}

var TrackPendingRequests = [],
    LastTrackRequest = 0;

function DrawOpponentTrack(e) {
  var t = _CurPlayer.CurBoat,
      a = e.idboat,
      i = new Date(),
      n = null;

  if (void 0 !== t && t && i > LastTrackRequest) {
    if (LastTrackRequest = new Date(i / 1e3 + .5), void 0 !== t.OppTrack || !(a in t.OppTrack) || a in t.OppTrack && t.OppTrack[a].LastShow <= new Date(1e3 * t.VLMInfo.LUP)) {
      var _i12 = new Date() / 1e3 - 172800,
          o = t.VLMInfo.RAC,
          r = new Date();

      n = a.toString() + "/" + o.toString(), a in t.OppTrack && (t.OppTrack[a].Visible = !0), n in TrackPendingRequests && !(r > TrackPendingRequests[n]) || (TrackPendingRequests[n] = new Date(r.getTime() + 6e4), console.log("GetTrack " + n + " " + _i12), parseInt(a) > 0 ? GetBoatTrack(t, a, o, _i12, e) : parseInt(a) && GetRealBoatTrack(t, a, o, _i12, e));
    } else console.log(" GetTrack ignore before next update" + n + " " + StartTime);

    DrawBoat(t);
  }
}

function GetRealBoatTrack(e, t, a, i, n) {
  $.get("/ws/realinfo/tracks.php?idr=" + a + "&idreals=" + -t + "&starttime=" + i, function (a) {
    a.success && (AddBoatOppTrackPoints(e, t, a.tracks, n.color), RefreshCurrentBoat(!1, !1));
  });
}

var TrackRequestPending = !1;

function GetBoatTrack(e, t, a, i, n) {
  TrackRequestPending || (TrackRequestPending = !0, $.get("/ws/boatinfo/smarttracks.php?idu=" + t + "&idr=" + a + "&starttime=" + i, function (a) {
    if (TrackRequestPending = !1, a.success) {
      var i;

      for (i in AddBoatOppTrackPoints(e, t, a.tracks, n.color), a.tracks_url) {
        if (i > 10) break;
        $.get("/cache/tracks/" + a.tracks_url[i], function (a) {
          a.success && (AddBoatOppTrackPoints(e, t, a.tracks, n.color), RefreshCurrentBoat(!1, !1));
        });
      }

      RefreshCurrentBoat(!1, !1);
    }
  }));
}

function AddBoatOppTrackPoints(e, t, a, i) {
  t in e.OppTrack || (i = SafeHTMLColor(i), e.OppTrack[t] = {
    LastShow: 0,
    TrackColor: i,
    DatePos: [],
    Visible: !0,
    OLTrackLine: null
  });

  for (var _i13 in a) {
    var n = a[_i13];
    e.OppTrack[t].DatePos[n[0]] = {
      lat: n[2] / 1e3,
      lon: n[1] / 1e3
    };
  }

  e.OppTrack[t].LastShow = 0, e.OppTrack[t].OLTrackLine = null;
}

function DeletePilotOrder(e, t) {
  $.post("/ws/boatsetup/pilototo_delete.php?", "parms=" + JSON.stringify({
    idu: e.IdBoat,
    taskid: parseInt(t)
  }), function (e) {
    e.success && RefreshCurrentBoat(!1, !0, "AutoPilot");
  });
}

function UpdateBoatPrefs(e, t) {
  t.idu = e.IdBoat, $.post("/ws/boatsetup/prefs_set.php", "parms=" + JSON.stringify(t), function (e) {
    e.success ? RefreshCurrentBoat(!1, !1) : VLMAlertDanger(GetLocalizedString("UpdateFailed"));
  });
}

function LoadVLMPrefs() {
  var e;
  void 0 !== _CurPlayer && (e = _CurPlayer.CurBoat, SetDDTheme(VLM2Prefs.CurTheme), $.get("/ws/boatinfo/prefs.php?idu=" + e.IdBoat, HandlePrefsLoaded));
}

function HandlePrefsLoaded(e) {
  e.success ? (_CurPlayer.CurBoat.VLMPrefs = e.prefs, VLM2Prefs.UpdateVLMPrefs(e.prefs)) : VLMAlertDanger("Error communicating with VLM, try reloading the browser page...");
}

function InitXmpp() {
  converse.initialize({
    bosh_service_url: "https://bind.conversejs.org",
    i18n: locales.en,
    show_controlbox_by_default: !0,
    roster_groups: !0
  });
}