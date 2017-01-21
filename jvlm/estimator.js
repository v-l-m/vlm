

function BoatEstimate(Est)
{
  this.Position;
  this.Date;
  this.Mode;
  this.Value;
  this.Meteo;
  this.CurWP = new VLMPosition(0,0);
  this.RaceWP = 1;
  
  if (typeof Est!== "undefined" && Est)
  {
    this.Position =  new VLMPosition(Est.Position.Lon.Value, Est.Position.Lat.Value);
    this.Date = new Date(Est.Date);
    this.Mode = Est.Mode;
    this.Value = Est.Value;
    this.Meteo = new WindData(
                        {
                          Speed : Est.Meteo.Speed,
                          Heading : Est.Meteo.Heading
                        });
    this.CurWP = Est.CurWP;
    this.RaceWP = Est.RaceWP;
  }

}

function Estimator(Boat)
{
  if (typeof Boat === 'undefined' || ! Boat)
  {
    throw "Boat must exist for tracking...."
  }

  this.Boat = Boat;
  this.MaxVacEstimate = 0;      
  this.CurEstimate = new BoatEstimate()
  this.Running = false;
  this.EstimateTrack=[];
  this.EstimatePoints=[];
  this.ProgressCallBack = null;

  this.Start = function(ProgressCallBack)
  {
    this.ProgressCallBack = ProgressCallBack
    if (this.Running)
    {
      return;
    }

    this.Running = true;
    GribMgr.Init();

    if (typeof this.Boat.VLMInfo === "undefined")
    {
      this.Running = false;
      this.ReportProgress(true);
      return;
    }

    this.CurEstimate.Position = new VLMPosition(this.Boat.VLMInfo.LON,this.Boat.VLMInfo.LAT)
    this.CurEstimate.Date = new Date (this.Boat.VLMInfo.LUP*1000 + 1000* this.Boat.VLMInfo.VAC)
    this.CurEstimate.Mode = parseInt(this.Boat.VLMInfo.PIM,10);
    this.CurEstimate.CurWP = new VLMPosition(this.Boat.VLMInfo.WPLON, this.Boat.VLMInfo.WPLAT)
    this.CurEstimate.RaceWP = this.Boat.VLMInfo.NWP;

    if ((this.CurEstimate.Mode == PM_HEADING) || (this.CurEstimate.Mode == PM_ANGLE))
    {
      this.CurEstimate.Value = parseFloat(this.Boat.VLMInfo.PIP);
    }

    this.CurEstimate.PilOrders = [];
    for (index in this.Boat.VLMInfo.PIL)
    {
      var Order =  this.Boat.VLMInfo.PIL[index];
      var NewOrder = {PIP: Order.PIP,
                      PIM: Order.PIM,
                      STS: Order.STS,
                      TTS: Order.TTS
                    }
      this.CurEstimate.PilOrders.push(NewOrder);
    }
    
    this.EstimateTrack=[];
    this.EstimatePoints=[];

    this.MaxVacEstimate = new Date(GribMgr.MaxWindStamp); 
    setTimeout(this.Estimate.bind(this),2000)
    this.ReportProgress(false);
  }

  this.Estimate = function(Boat)
  {
      
    if (this.CurEstimate.Date >= this.MaxVacEstimate)
    {
      this.Running = false;
      //Estimate complete, DrawBoat track
      DrawBoat(this.Boat);
      this.ReportProgress(true)
      return;
    }

    var MI = GribMgr.WindAtPointInTime(this.CurEstimate.Date,this.CurEstimate.Position.Lat.Value,this.CurEstimate.Position.Lon.Value)
    if (!MI)
    {
      // FIXME : have a way of going out if needed
      setTimeout(this.Estimate.bind(this),1000);
      return;
    }
    
    this.CurEstimate.Meteo = MI;

    // Ok, got meteo, move the boat, and ask for new METEO

    // Check if an update is required from AutoPilot;
    for (index in this.CurEstimate.PilOrders)
    {
      var Order = this.CurEstimate.PilOrders[index];

      if (Order && Order.STS === "pending")
      {
        var OrderTime = new Date(parseInt(Order.TTS,10)*1000.)

        if (OrderTime <= this.CurEstimate.Date)
        {
          // Use pilot order to update the current Mode
          this.CurEstimate.Mode = parseInt(Order.PIM,10);

          switch(this.CurEstimate.Mode)
          {
            case PM_ANGLE:
            case PM_HEADING:
              this.CurEstimate.Value = parseFloat(Order.PIP);
              break;

            case PM_ORTHO:
            case PM_VMG:
            case PM_VBVMG:
              var p1 = Order.PIP.split("@")
              var Dest = p1[0].split(",")
              this.CurEstimate.CurWP = new VLMPosition(parseFloat(Dest[1]),parseFloat(Dest[0])) 
              break;
              
            default :
              alert("unsupported pilototo mode");
              this.ReportProgress(true);
              return;
          }


          this.CurEstimate.PilOrders[index]=null;
          break;
        }
      }
      
    }

    var Hdg = this.CurEstimate.Value;
    var Speed = 0;
    switch (this.CurEstimate.Mode)
    {
      case PM_ANGLE:  // This goes just before Heading, since we only update the Hdg, rest is the same
        // Going fixed angle, get bearing, compute speed, move
        Hdg = MI.Heading+this.CurEstimate.Value;
            
      case PM_HEADING:
        // Going fixed bearing, get boat speed, move along loxo
        
        var Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,MI.Speed,MI.Heading,Hdg);
        var NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed/3600.*this.Boat.VLMInfo.VAC, Hdg);
        
        break;

      case PM_ORTHO:
      case PM_VMG:
      case PM_VBVMG:
        var Dest = this.GetNextWPCoords(this.CurEstimate)
        
        if (this.CurEstimate.Mode == PM_ORTHO)
        {
          Hdg = this.CurEstimate.Position.GetOrthoCourse(Dest);
          Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,MI.Speed,MI.Heading,Hdg);
          var NewPos = this.CurEstimate.Position.ReachDistOrtho(Speed/3600.*this.Boat.VLMInfo.VAC, Hdg);          
        }
        else
        {
          if (this.CurEstimate.Mode == PM_VMG)
          {
            Hdg = PolarsManager.GetVMGCourse(this.Boat.VLMInfo.POL,MI.Speed,MI.Heading,this.CurEstimate.Position, Dest);
          }
          else
          {
            Hdg = PolarsManager.GetVBVMGCourse(this.Boat.VLMInfo.POL,MI.Speed,MI.Heading,this.CurEstimate.Position, Dest);
          }
          Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,MI.Speed,MI.Heading,Hdg);
          var NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed/3600.*this.Boat.VLMInfo.VAC, Hdg);
        
        }

        this.CheckWPReached(Dest,this.CurEstimate.Position,NewPos)
        break;


      default:
        throw "Unsupported pilotmode for estimate..." + this.CurEstimate.Mode
    }

    console.log(this.CurEstimate.Date + " " + NewPos.Lon.ToString() + " " + NewPos.Lat.ToString() + " Wind : " + RoundPow(MI.Speed,4) + "@" + RoundPow(MI.Heading,4) + " Boat " + RoundPow(Speed,4) + "kts" + RoundPow(((Hdg+360.)%360.),4))

    var RaceComplete = false;

    if (this.CheckGateValidation(NewPos))
    {
      RaceComplete = this.GetNextRaceWP()
    }

    this.CurEstimate.Position = NewPos;
    this.EstimateTrack.push(new BoatEstimate( this.CurEstimate))

    // Start next point computation....
    this.CurEstimate.Date = new Date((this.CurEstimate.Date/1000+this.Boat.VLMInfo.VAC)*1000)
    if (RaceComplete)
    {
      this.ReportProgress(true);
      return;
    }
    else
    {
      setTimeout(this.Estimate.bind(this),0);
      this.ReportProgress(false)
    }
  }

  this.GetNextRaceWP = function()
  {
    if ( this.CurEstimate.RaceWP+1 === this.Boat.RaceWP.races_waypoints.length-1)
    {
      //Race Complete
      return true;
    }
    for (i = this.CurEstimate.RaceWP+1; i < this.Boat.RaceWP.races_waypoints.length; i++)
    {
        if (!(this.Boat.RaceWP.races_waypoints & WP_GATE_KIND_MASK))
        {
          this.CurEstimate.RaceWP = i;
          break;
        }
    }
    return false;
  }

  this.CheckGateValidation = function( NewPos)
  {
    var GateSeg = this.GetNextGateSegment(this.CurEstimate)
    var Gate = this.Boat.RaceInfo.races_waypoints[this.CurEstimate.RaceWP];
    var GateType = Gate.format;
    var CurSeg = {P1 : this.CurEstimate.Position, P2 : NewPos}

    return VLMMercatorTransform.SegmentsIntersect(GateSeg,CurSeg)
    
  }

  this.CheckWPReached = function (Dest,PrevPos,NewPos)
  {
    if (!this.CurEstimate.CurWP.Lat.value && !this.CurEstimate.CurWP.Lon.Value)
    {
      // AutoWP, nothing to do
      return;
    }
    // VLM REF from CheckWayPointCrossing
    // On lache le WP perso si il est plus pres que la distance parcourue à la dernière VAC.
    //if ( $distAvant < $fullUsersObj->boatspeed*$fullUsersObj->hours || $distApres < $fullUsersObj->boatspeed*$fullUsersObj->hours ) {
    var BeforeDist = Dest.GetOrthoDist(PrevPos)
    var AfterDist = Dest.GetOrthoDist(NewPos)
    var CurDist = PrevPos.GetOrthoDist(NewPos)
    if ((BeforeDist < CurDist)|| AfterDist < CurDist)
    {
      // WP Reached revert to AutoWP
      this.CurEstimate.CurWP = new VLMPosition(0,0)
      console.log("WP Reached");
    }
    
  }

  this.GetNextWPCoords = function (Estimate)
  {
    if (Estimate.CurWP.Lat.value || Estimate.CurWP.Lon.Value)
    {
      return Estimate.CurWP;
    }
    else
    {
      // Get CurRaceWP
      // Compute closest point (using bad euclidian method)
      // Return computed point

      var Seg = this.GetNextGateSegment(Estimate);
      
      var Loxo1 = Seg.P1.GetLoxoCourse(Seg.P2);
      var Loxo2 = Seg.P1.GetLoxoCourse(Estimate.Position);
      var Delta = Loxo1 - Loxo2;

      if (Delta > 180) 
      {
        Delta -= 360.; 
      }
      else if ( Delta < -180) 
      {
       Delta += 360.; 
      }

      Delta = Math.abs(Delta);

      if (Delta > 90)
      {
        return Seg.P1;
      }
      else
      {
        var PointDist = Seg.P1.GetLoxoDist(Estimate.Position);
        return Seg.P1.ReachDistLoxo(Loxo2,PointDist*Math.cos(Deg2Rad(Delta)));
      }
    }
    
  }

  this.GetNextGateSegment = function(Estimate, GateKind )
  {

    var NWP = Estimate.RaceWP;
    var Gate = this.Boat.RaceInfo.races_waypoints[NWP];

    if (typeof GateKind === "undefined")
    {
      GateKind = WP_DEFAULT;
    }

    do
    {
      if ((Gate.wpformat & WP_GATE_KIND_MASK) != GateKind)
      {
        NWP++;
        if (NWP >= this.Boat.RaceInfo.races_waypoints)
        {
          throw "Oops could not find requested gate type"
        }
        Gate = this.Boat.RaceInfo.races_waypoints[NWP];
      }

    } while  ((Gate.wpformat & WP_GATE_KIND_MASK) != GateKind)

    var P1 = new VLMPosition (Gate.longitude1, Gate.latitude1);
    var P2 = {}
    if ((Gate.format & WP_GATE_BUOY_MASK) === WP_TWO_BUOYS)
    {
      P2 = new VLMPosition (Gate.longitude2, Gate.latitude2);
    }
    else
    {
      throw "not implemented 1 buoy gate"
    }

    return { P1 : P1, P2 : P2};


  }

  this.ReportProgress = function (Complete)
  {
    var Pct = 0;

    if (this.ProgressCallBack)
    {
      if (!Complete)
      {
        if (this.EstimateTrack.length > 1)
        {
          Pct =  (this.MaxVacEstimate - this.EstimateTrack[this.EstimateTrack.length - 1].Date)/ (this.MaxVacEstimate - this.EstimateTrack[0].Date)
          Pct = RoundPow((1 - Pct)*100.,1)
        }
      }
      this.ProgressCallBack(Complete,Pct);
    }
  }

}

/*
function HandleEstimatorStart(e)
{
  var e = new Estimator(_CurPlayer.CurBoat);
}
*/
