function BoatEstimate(Est)
{
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

  if (typeof Est !== "undefined" && Est)
  {
    this.Position = new VLMPosition(Est.Position.Lon.Value, Est.Position.Lat.Value);
    this.Date = new Date(Est.Date);
    this.PrevDate = new Date(Est.PrevDate);
    this.Mode = Est.Mode;
    this.Value = Est.Value;

    if (typeof Est.Meteo !== "undefined" && Est.Meteo)
    {
      this.Meteo = new WindData(
      {
        Speed: Est.Meteo.Speed,
        Heading: Est.Meteo.Heading
      });
    }
    this.CurWP = Est.CurWP;
    this.RaceWP = Est.RaceWP;
    this.Heading = Est.Heading;
  }

}

function Estimator(Boat)
{
  if (typeof Boat === 'undefined' || !Boat)
  {
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

  this.Stop = function()
  {
    // Stop the estimator if Running
    if (this.Running)
    {
      this.Running = false;
      this.ReportProgress(true);

      //Estimate complete, DrawBoat track
      DrawBoat(this.Boat);

    }

    return;
  };

  this.Start = function(ProgressCallBack)
  {
    this.ProgressCallBack = ProgressCallBack;
    if (this.Running)
    {
      return;
    }

    this.Running = true;
    GribMgr.Init();

    if (typeof this.Boat.VLMInfo === "undefined")
    {
      this.Stop();
      return;
    }

    this.CurEstimate.Position = new VLMPosition(this.Boat.VLMInfo.LON, this.Boat.VLMInfo.LAT);
    this.CurEstimate.Date = new Date(this.Boat.VLMInfo.LUP * 1000 + 1000 * this.Boat.VLMInfo.VAC);
    this.CurEstimate.PrevDate = this.CurEstimate.Date;
    if (this.CurEstimate.Date < new Date())
    {
      if (typeof this.Boat.RaceInfo === "undefined")
      {
        // Use cur date for estimate before start
        this.CurEstimate.Date = new Date();
      }
      else
      {
        // Set Start to 1st VAC after start +6s 
        this.CurEstimate.PrevDate = new Date(parseInt(this.Boat.RaceInfo.deptime, 10) * 1000 + 6000);
        if (this.CurEstimate.PrevDate < new Date())
        {
          // If this is before current date then set to next current vac time
          let VacTime = new Date().getTime() / 1000;
          VacTime -= (VacTime % this.Boat.VLMInfo.VAC);
          this.CurEstimate.PrevDate = new Date(VacTime * 1000 + 6000);
        }
        let StartDate = new Date(this.CurEstimate.PrevDate.getTime() + 1000 * this.Boat.VLMInfo.VAC);
        this.CurEstimate.Date = StartDate;
      }

    }

    this.CurEstimate.Mode = parseInt(this.Boat.VLMInfo.PIM, 10);
    this.CurEstimate.CurWP = new VLMPosition(this.Boat.VLMInfo.WPLON, this.Boat.VLMInfo.WPLAT);
    this.CurEstimate.HdgAtWP = parseFloat(this.Boat.VLMInfo["H@WP"]);
    this.CurEstimate.RaceWP = parseInt(this.Boat.VLMInfo.NWP, 10);

    if ((this.CurEstimate.Mode == PM_HEADING) || (this.CurEstimate.Mode == PM_ANGLE))
    {
      this.CurEstimate.Value = parseFloat(this.Boat.VLMInfo.PIP);
    }

    this.CurEstimate.PilOrders = [];
    for (let index in this.Boat.VLMInfo.PIL)
    {
      var Order = this.Boat.VLMInfo.PIL[index];
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
    this.ReportProgress(false);
    // Add Start point to estimate track
    this.EstimateTrack.push(new BoatEstimate(this.CurEstimate));
    this.ErrorCount = 0;
    setTimeout(this.Estimate.bind(this), 0);

  };

  this.Estimate = function(Boat)
  {

    if (!this.Running || this.CurEstimate.Date >= this.MaxVacEstimate)
    {
      this.Stop();
      return;
    }

    let MI;
    // let Lat = RoundPow(1000.0 * this.CurEstimate.Position.Lat.Value, 0) / 1000.0;
    // let Lon = RoundPow(1000.0 * this.CurEstimate.Position.Lon.Value, 0) / 1000.0;
    let Lat = this.CurEstimate.Position.Lat.Value;
    let Lon = this.CurEstimate.Position.Lon.Value;
    do {

      MI = GribMgr.WindAtPointInTime(this.CurEstimate.PrevDate, Lat, Lon);

      if (!MI)
      {
        if (this.ErrorCount > 10)
        {
          this.Stop();
          return;
        }
        this.ErrorCount++;
        setTimeout(this.Estimate.bind(this), 1000);
        return;
      }

      this.ErrorCount = 0;

      if (isNaN(MI.Speed))
      {
        var Bkpt = 1;
        alert("Looping on NaN WindSpeed");
      }
    } while (isNaN(MI.Speed));

    this.CurEstimate.Meteo = MI;

    // Ok, got meteo, move the boat, and ask for new METEO

    // Check if an update is required from AutoPilot;
    for (let index in this.CurEstimate.PilOrders)
    {
      var Order = this.CurEstimate.PilOrders[index];

      if (Order && Order.STS === "pending")
      {
        var OrderTime = new Date(parseInt(Order.TTS, 10) * 1000.0);

        if (OrderTime <= this.CurEstimate.Date)
        {
          // Use pilot order to update the current Mode
          this.CurEstimate.Mode = parseInt(Order.PIM, 10);

          switch (this.CurEstimate.Mode)
          {
            case PM_ANGLE:
            case PM_HEADING:
              this.CurEstimate.Value = parseFloat(Order.PIP);
              break;

            case PM_ORTHO:
            case PM_VMG:
            case PM_VBVMG:
              let p1 = Order.PIP.split("@");
              let Dest = p1[0].split(",");
              this.CurEstimate.CurWP = new VLMPosition(parseFloat(Dest[1]), parseFloat(Dest[0]));
              this.CurEstimate.HdgAtWP = parseFloat(p1[1]);
              break;

            default:
              alert("unsupported pilototo mode");
              this.Stop();
              return;
          }


          this.CurEstimate.PilOrders[index].STS = "Planned";
          this.CurEstimate.PilOrders[index].Pos = this.CurEstimate.Position;
          break;
        }
      }

    }

    let Hdg = this.CurEstimate.Value;
    let Speed = 0;
    let NewPos = null;
    let Dest = null;
    switch (this.CurEstimate.Mode)
    {
      case PM_ANGLE: // This goes just before Heading, since we only update the Hdg, rest is the same
        // Going fixed angle, get bearing, compute speed, move
        Hdg = MI.Heading + this.CurEstimate.Value;
        Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, Hdg);
        if (isNaN(Speed))
        {
          VLMAlertDanger("PM_ANGLE : Error getting boatSpeed try again later...");
          this.Stop();
          return;
        }
        NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed / 3600.0 * this.Boat.VLMInfo.VAC, Hdg);
        break;

      case PM_HEADING:
        // Going fixed bearing, get boat speed, move along loxo

        Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, Hdg);
        if (isNaN(Speed))
        {
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

        if (this.CurEstimate.Mode == PM_ORTHO)
        {
          Hdg = this.CurEstimate.Position.GetOrthoCourse(Dest);
          Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, Hdg);
          if (isNaN(Speed))
          {
            VLMAlertDanger("PM_ANGLE : Error getting boatSpeed try again later...");
            this.Stop();
            return;
          }
          NewPos = this.CurEstimate.Position.ReachDistOrtho(Speed / 3600.0 * this.Boat.VLMInfo.VAC, Hdg);
        }
        else
        {
          if (this.CurEstimate.Mode == PM_VMG)
          {
            Hdg = PolarsManager.GetVMGCourse(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, this.CurEstimate.Position, Dest);
          }
          else
          {
            Hdg = PolarsManager.GetVBVMGCourse(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, this.CurEstimate.Position, Dest);
          }

          Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL, MI.Speed, MI.Heading, Hdg);
          if (isNaN(Speed))
          {
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

    console.log(this.CurEstimate.Date + this.CurEstimate.Position.toString(true) + "=> " + NewPos.Lon.toString(true) + " " + NewPos.Lat.toString(true) + " Wind : " + RoundPow(MI.Speed, 4) + "@" + RoundPow(MI.Heading, 4) + " Boat " + RoundPow(Speed, 4) + "kts" + RoundPow(((Hdg + 360.0) % 360.0), 4));

    var RaceComplete = false;

    if (this.CheckGateValidation(NewPos))
    {
      RaceComplete = this.GetNextRaceWP();
    }

    this.CurEstimate.Heading = Hdg;
    this.CurEstimate.Position = NewPos;
    this.EstimateTrack.push(new BoatEstimate(this.CurEstimate));

    // Start next point computation....
    this.CurEstimate.Date = new Date((this.CurEstimate.Date / 1000 + this.Boat.VLMInfo.VAC) * 1000);
    this.CurEstimate.PrevDate = this.CurEstimate.Date;
    if (RaceComplete)
    {
      this.Stop();
      return;
    }
    else
    {
      setTimeout(this.Estimate.bind(this), 0);
      this.ReportProgress(false);
    }
  };

  this.GetPilotPoints = function()
  {
    let RetPoints=[];
    // Check if an update is required from AutoPilot;
    for (let index in this.CurEstimate.PilOrders)
    {
      var Order = this.CurEstimate.PilOrders[index];

      if (Order && typeof Order.Pos !== "undefined")
      {
        RetPoints.push(Order);
      }
    }

    return RetPoints;
  };

  this.GetNextRaceWP = function()
  {
    let NbWP = Object.keys(this.Boat.RaceInfo.races_waypoints).length;
    if (this.CurEstimate.RaceWP === NbWP)
    {
      //Race Complete
      return true;
    }
    for (let i = this.CurEstimate.RaceWP + 1; i <= NbWP; i++)
    {
      if (!(this.Boat.RaceInfo.races_waypoints[i].wpformat & WP_ICE_GATE))
      {
        this.CurEstimate.RaceWP = i;
        break;
      }
    }
    return false;
  };

  this.CheckGateValidation = function(NewPos)
  {
    let GateSeg = this.GetNextGateSegment(this.CurEstimate);
    let Gate = this.Boat.RaceInfo.races_waypoints[this.CurEstimate.RaceWP];
    let CurSeg = {
      P1: this.CurEstimate.Position,
      P2: NewPos
    };

    let RetVal = VLMMercatorTransform.SegmentsIntersect(GateSeg, CurSeg);
    return RetVal;

  };

  this.CheckWPReached = function(Dest, PrevPos, NewPos)
  {
    if (!this.CurEstimate.CurWP.Lat.value && !this.CurEstimate.CurWP.Lon.Value)
    {
      // AutoWP, nothing to do
      return;
    }
    // VLM REF from CheckWayPointCrossing
    // On lache le WP perso si il est plus pres que la distance parcourue à la dernière VAC.
    //if ( $distAvant < $fullUsersObj->boatspeed*$fullUsersObj->hours || $distApres < $fullUsersObj->boatspeed*$fullUsersObj->hours ) {
    let BeforeDist = Dest.GetOrthoDist(PrevPos);
    let AfterDist = Dest.GetOrthoDist(NewPos);
    let CurDist = PrevPos.GetOrthoDist(NewPos);
    if ((BeforeDist < CurDist) || AfterDist < CurDist)
    {
      // WP Reached revert to AutoWP
      this.CurEstimate.CurWP = new VLMPosition(0, 0);
      if (this.CurEstimate.HdgAtWP != -1)
      {
        this.CurEstimate.Mode = PM_HEADING;
        this.CurEstimate.Value = this.CurEstimate.HdgAtWP;

      }
      console.log("WP Reached");
    }

  };

  this.GetNextWPCoords = function(Estimate)
  {
    if (Estimate.CurWP.Lat.value || Estimate.CurWP.Lon.Value)
    {
      return Estimate.CurWP;
    }
    else
    {
      return this.Boat.GetNextWPPosition(Estimate.RaceWP, Estimate.Position, Estimate.CurWP);
    }
  };

  this.GetNextGateSegment = function(Estimate)
  {
    return this.Boat.GetNextGateSegment(Estimate.RaceWP);
  };

  this.ReportProgress = function(Complete)
  {
    let Pct = 0;

    if (this.ProgressCallBack)
    {
      if (!Complete)
      {
        if (this.EstimateTrack.length > 1)
        {
          Pct = (this.MaxVacEstimate - this.EstimateTrack[this.EstimateTrack.length - 1].Date) / (this.MaxVacEstimate - this.EstimateTrack[0].Date);
          Pct = RoundPow((1 - Pct) * 100.0, 1);
        }
      }
      this.ProgressCallBack(Complete, Pct, this.CurEstimate.Date);
    }
  };

  this.GetClosestEstimatePoint = function(Param)
  {
    if (Param instanceof VLMPosition)
    {
      return this.GetClosestEstimatePointFromPosition(Param);
    }
    else if (Param instanceof Date)
    {
      return this.GetClosestEstimatePointFromTime(Param);
    }
    else
    {
      return null;
    }
  };

  this.GetClosestEstimatePointFromTime = function(Time)
  {
    if (!Time || !Object.keys(this.EstimateTrack).length)
    {
      return null;
    }

    let Index = 0;
    let Delta;

    for (Index = 0; Index < Object.keys(this.EstimateTrack).length; Index++)
    {
      if (this.EstimateTrack[Index])
      {
        if (Time > this.EstimateTrack[Index].Date)
        {
          Delta = Time - this.EstimateTrack[Index].Date;
        }
        else
        {
          break;
        }
      }
    }

    if (Index < Object.keys(this.EstimateTrack).length && typeof this.EstimateTrack[Index + 1] !== "undefined" && this.EstimateTrack[Index + 1])
    {
      let Delta2 = Time - this.EstimateTrack[Index + 1].Date;

      if (Math.abs(Delta2) < Math.abs(Delta))
      {
        Index++;
      }
    }

    let RetValue = this.EstimateTrack[Index];
    return RetValue;
  };

  this.GetClosestEstimatePointFromPosition = function(Pos)
  {
    if (!Pos)
    {
      return null;
    }

    let Dist = 1e30;
    let index;
    let RetValue = null;

    for (index = 0; index < Object.keys(this.EstimateTrack).length; index++)
    {
      if (this.EstimateTrack[index])
      {
        var d = Pos.GetEuclidianDist2(this.EstimateTrack[index].Position);

        if (d < Dist)
        {
          RetValue = this.EstimateTrack[index];
          Dist = d;
        }
      }
    }

    return RetValue;
  };

  this.ClearEstimatePosition = function(Boat)
  {
    this.ShowEstimatePosition(Boat, null);
  };

  this.ShowEstimatePosition = function(Boat, Estimate)
  {
    let Features = GetRaceMapFeatures(Boat);

    if (Boat && Estimate && Estimate.Position && (Boat.VLMInfo.LON !== Estimate.Position.Lon.Value || Boat.VLMInfo.LAT !== Estimate.Position.Lat.Value))
    {

      if (!Features)
      {
        return;
      }

      let Position = [Estimate.Position.Lat.Value, Estimate.Position.Lon.Value];

      if (Features.BoatEstimateMarker)
      {
        Features.BoatEstimateMarker.setLatLng(Position).addTo(map);
      }
      else
      {
        // Estimate point marker
        let Marker = GetBoatEstimateMarker();

        Features.BoatEstimateMarker = L.marker(Position,
        {
          icon: Marker
        }).addTo(map);

      }

      if (Features.BoatEstimateMarker)
      {
        Features.BoatEstimateMarker.setRotationAngle(Estimate.Heading);
      }

      if (typeof Estimate.Meteo !== "undefined" && Estimate.Meteo)
      {
        let StartPos = new VLMPosition(Position[1], Position[0]);
        let Polar = BuildPolarLine(Boat, StartPos, VLM2Prefs.MapPrefs.PolarVacCount, Estimate.Date);

        Features.BoatEstimateMarkerPolar = DefinePolarMarker(Polar, Features.BoatEstimateMarkerPolar);
      }
    }
    else if (Features)
    {
      if (Features.BoatEstimateMarker)
      {
        Features.BoatEstimateMarker.remove();
      }
      if (Features.BoatEstimateMarkerPolar)
      {
        Features.BoatEstimateMarkerPolar.remove();
      }
    }
  };

  this.GetEstimateTracks = function()
  {
    let RetTracks = [];
    let PrevIndex = null;
    let PrevPoint = null;

    if (this.EstimateTrack && this.EstimateTrack[0])
    {
      let TrackStartTick = new Date().getTime();
      let GridOffset = TrackStartTick % (6 * 3600000);

      let TrackIndexStartTick = TrackStartTick - GridOffset + 3.5 * 3600000;

      for (let index in this.EstimateTrack)
      {
        if (this.EstimateTrack[index])
        {
          let est = this.EstimateTrack[index];
          let Delta = est.Date.getTime() - TrackIndexStartTick;
          let CurTrackInDex = Math.floor(Delta / 6 / 3600000);

          if (CurTrackInDex < 0)
          {
            CurTrackInDex = 0;
          }
          else if (CurTrackInDex > 2)
          {
            CurTrackInDex = 2;
          }

          if (typeof RetTracks[CurTrackInDex] === "undefined")
          {
            RetTracks[CurTrackInDex] = [];
          }

          if (CurTrackInDex !== PrevIndex && PrevPoint)
          {
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

}