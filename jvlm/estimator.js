

function BoatEstimate(Est)
{
  this.Position = null;
  this.Date = null;
  this.PrevDate = null;
  this.Mode = null;
  this.Value = null;
  this.Meteo = null;
  this.CurWP = new VLMPosition(0,0);
  this.HdgAtWP = -1;
  this.RaceWP = 1;
  this.Heading = null;
  
  if (typeof Est!== "undefined" && Est)
  {
    this.Position =  new VLMPosition(Est.Position.Lon.Value, Est.Position.Lat.Value);
    this.Date = new Date(Est.Date);
    this.PrevDate = new Date(Est.PrevDate);
    this.Mode = Est.Mode;
    this.Value = Est.Value;

    if (typeof Est.Meteo !== "undefined" && Est.Meteo)
    {
      this.Meteo = new WindData(
                        {
                          Speed : Est.Meteo.Speed,
                          Heading : Est.Meteo.Heading
                        });
    }
    this.CurWP = Est.CurWP;
    this.RaceWP = Est.RaceWP;
    this.Heading = Est.Heading;
  }

}

function Estimator(Boat)
{
  if (typeof Boat === 'undefined' || ! Boat)
  {
    throw "Boat must exist for tracking....";
  }

  this.Boat = Boat;
  this.MaxVacEstimate = 0;      
  this.CurEstimate = new BoatEstimate();
  this.Running = false;
  this.EstimateTrack=[];
  this.EstimatePoints=[];
  this.ProgressCallBack = null;
  this.ErrorCount = 0;
  this.EstimateMapFeatures = []; // Current estimate position
  
  this.Stop = function ()
  {
    // Stop the estimator if Running
    if (this.Running)
    {
      this.Running=false;
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

    this.CurEstimate.Position = new VLMPosition(this.Boat.VLMInfo.LON,this.Boat.VLMInfo.LAT);
    this.CurEstimate.PrevDate = new Date (this.Boat.VLMInfo.LUP*1000);
    this.CurEstimate.Date = new Date (this.Boat.VLMInfo.LUP*1000 + 1000* this.Boat.VLMInfo.VAC);
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
        let StartDate = new Date(parseInt(this.Boat.RaceInfo.deptime,10)*1000+ 1000* this.Boat.VLMInfo.VAC+6000);
        this.CurEstimate.Date = StartDate;
        this.CurEstimate.PrevDate =  new Date(parseInt(this.Boat.RaceInfo.deptime,10)*1000);
      }
      
    }


    
    this.CurEstimate.Mode = parseInt(this.Boat.VLMInfo.PIM,10);
    this.CurEstimate.CurWP = new VLMPosition(this.Boat.VLMInfo.WPLON, this.Boat.VLMInfo.WPLAT);
    this.CurEstimate.HdgAtWP = parseFloat(this.Boat.VLMInfo["H@WP"]);
    this.CurEstimate.RaceWP = parseInt(this.Boat.VLMInfo.NWP,10);

    if ((this.CurEstimate.Mode == PM_HEADING) || (this.CurEstimate.Mode == PM_ANGLE))
    {
      this.CurEstimate.Value = parseFloat(this.Boat.VLMInfo.PIP);
    }

    this.CurEstimate.PilOrders = [];
    for (let index in this.Boat.VLMInfo.PIL)
    {
      var Order =  this.Boat.VLMInfo.PIL[index];
      var NewOrder = {PIP: Order.PIP,
                      PIM: Order.PIM,
                      STS: Order.STS,
                      TTS: Order.TTS
                    };
      this.CurEstimate.PilOrders.push(NewOrder);
    }
    
    this.EstimateTrack=[];
    this.EstimatePoints=[];

    this.MaxVacEstimate = new Date(GribMgr.MaxWindStamp); 
    this.ReportProgress(false);
    // Add Start point to estimate track
    this.EstimateTrack.push(new BoatEstimate( this.CurEstimate));
    this.ErrorCount = 0;
    setTimeout(this.Estimate.bind(this),0);
    
  };

  this.Estimate = function(Boat)
  {
      
    if (!this.Running || this.CurEstimate.Date >= this.MaxVacEstimate)
    {
      this.Stop();
      return;
    }

    let MI;
    do
    {
      MI = GribMgr.WindAtPointInTime(this.CurEstimate.PrevDate,this.CurEstimate.Position.Lat.Value,this.CurEstimate.Position.Lon.Value);
      
      if (!MI)
      {
        if (this.ErrorCount > 10)
        {
          this.Stop();
          return;
        }
        this.ErrorCount ++;
        setTimeout(this.Estimate.bind(this),1000);
        return;
      }

      this.ErrorCount=0;

      if (isNaN(MI.Speed))
      {
        var Bkpt=1;
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
        var OrderTime = new Date(parseInt(Order.TTS,10)*1000.0);

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
              let p1 = Order.PIP.split("@");
              let Dest = p1[0].split(",");
              this.CurEstimate.CurWP = new VLMPosition(parseFloat(Dest[1]),parseFloat(Dest[0]));
              this.CurEstimate.HdgAtWP = parseFloat(p1[1]);
              break;
              
            default :
              alert("unsupported pilototo mode");
              this.Stop();
              return;
          }


          this.CurEstimate.PilOrders[index]=null;
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
      case PM_ANGLE:  // This goes just before Heading, since we only update the Hdg, rest is the same
        // Going fixed angle, get bearing, compute speed, move
        Hdg = MI.Heading+this.CurEstimate.Value;
        Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,MI.Speed,MI.Heading,Hdg);
        NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed/3600.0*this.Boat.VLMInfo.VAC, Hdg);
        break;    

      case PM_HEADING:
        // Going fixed bearing, get boat speed, move along loxo
        
        Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,MI.Speed,MI.Heading,Hdg);
        NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed/36000*this.Boat.VLMInfo.VAC, Hdg);
        
        break;

      case PM_ORTHO:
      case PM_VMG:
      case PM_VBVMG:
        Dest = this.GetNextWPCoords(this.CurEstimate);
        
        if (this.CurEstimate.Mode == PM_ORTHO)
        {
          Hdg = this.CurEstimate.Position.GetOrthoCourse(Dest);
          Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,MI.Speed,MI.Heading,Hdg);
          NewPos = this.CurEstimate.Position.ReachDistOrtho(Speed/3600.0*this.Boat.VLMInfo.VAC, Hdg);          
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
          NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed/3600.0*this.Boat.VLMInfo.VAC, Hdg);
        
        }

        this.CheckWPReached(Dest,this.CurEstimate.Position,NewPos);
        break;


      default:
        throw "Unsupported pilotmode for estimate..." + this.CurEstimate.Mode;
    }

    console.log(this.CurEstimate.Date + this.CurEstimate.Position.ToString(true) + "=> " + NewPos.Lon.ToString(true) + " " + NewPos.Lat.ToString(true) + " Wind : " + RoundPow(MI.Speed,4) + "@" + RoundPow(MI.Heading,4) + " Boat " + RoundPow(Speed,4) + "kts" + RoundPow(((Hdg+360.0)%360.0),4));

    var RaceComplete = false;

    if (this.CheckGateValidation(NewPos))
    {
      RaceComplete = this.GetNextRaceWP();
    }

    this.CurEstimate.Heading = Hdg;
    this.CurEstimate.Position = NewPos;
    this.EstimateTrack.push(new BoatEstimate( this.CurEstimate));

    // Start next point computation....
    this.CurEstimate.PrevDate=this.CurEstimate.Date;
    this.CurEstimate.Date = new Date((this.CurEstimate.Date/1000+this.Boat.VLMInfo.VAC)*1000);
    if (RaceComplete)
    {
      this.Stop();
      return;
    }
    else
    {
      setTimeout(this.Estimate.bind(this),0);
      this.ReportProgress(false);
    }
  };

  this.GetNextRaceWP = function()
  {
    var NbWP = Object.keys(this.Boat.RaceInfo.races_waypoints).length;
    if ( this.CurEstimate.RaceWP === NbWP)
    {
      //Race Complete
      return true;
    }
    for (i = this.CurEstimate.RaceWP+1; i <= NbWP; i++)
    {
        if (!(this.Boat.RaceInfo.races_waypoints[i].wpformat & WP_ICE_GATE))
        {
          this.CurEstimate.RaceWP = i;
          break;
        }
    }
    return false;
  };

  this.CheckGateValidation = function( NewPos)
  {
    let GateSeg = this.GetNextGateSegment(this.CurEstimate);
    let Gate = this.Boat.RaceInfo.races_waypoints[this.CurEstimate.RaceWP];
    let CurSeg = {P1 : this.CurEstimate.Position, P2 : NewPos};

    let RetVal =  VLMMercatorTransform.SegmentsIntersect(GateSeg,CurSeg);
    return RetVal;
    
  };

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
    let BeforeDist = Dest.GetOrthoDist(PrevPos);
    let AfterDist = Dest.GetOrthoDist(NewPos);
    let CurDist = PrevPos.GetOrthoDist(NewPos);
    if ((BeforeDist < CurDist)|| AfterDist < CurDist)
    {
      // WP Reached revert to AutoWP
      this.CurEstimate.CurWP = new VLMPosition(0,0);
      if (this.CurEstimate.HdgAtWP != -1)
      {
        this.CurEstimate.Mode = PM_HEADING;
        this.CurEstimate.Value = this.CurEstimate.HdgAtWP;

      }
      console.log("WP Reached");
    }
    
  };

  this.GetNextWPCoords = function (Estimate)
  {
    if (Estimate.CurWP.Lat.value || Estimate.CurWP.Lon.Value)
    {
      return Estimate.CurWP;
    }
    else
    {
     return this.Boat.GetNextWPPosition (Estimate.RaceWP, Estimate.Position, Estimate.CurWP);
    }
  };

  this.GetNextGateSegment = function(Estimate)
  {
    return this.Boat.GetNextGateSegment(Estimate.RaceWP);
  };

  this.ReportProgress = function (Complete)
  {
    let Pct = 0;

    if (this.ProgressCallBack)
    {
      if (!Complete)
      {
        if (this.EstimateTrack.length > 1)
        {
          Pct =  (this.MaxVacEstimate - this.EstimateTrack[this.EstimateTrack.length - 1].Date)/ (this.MaxVacEstimate - this.EstimateTrack[0].Date);
          Pct = RoundPow((1 - Pct)*100.0,1);
        }
      }
      this.ProgressCallBack(Complete,Pct, this.CurEstimate.Date);
    }
  };

  this.GetClosestEstimatePoint = function (Param)
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

  this.GetClosestEstimatePointFromTime = function (Time)
  {
    if (!Time || !Object.keys(this.EstimateTrack).length)
    {
      return null;
    }

    let Index = 0;
    let Delta;

    for (Index = 0; Index < Object.keys(this.EstimateTrack).length;Index++)
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

    if (Index< Object.keys(this.EstimateTrack).length)
    {
      let Delta2 = Time - this.EstimateTrack[Index+1].Date;

      if (Math.abs(Delta2)< Math.abs(Delta))
      {
        Index++;
      }
    }

    RetValue = this.EstimateTrack[Index];
    return RetValue;
  };

  this.GetClosestEstimatePointFromPosition = function(Pos)
  {
    if (!Pos)
    {
      return null;
    }

    var Dist = 1e30;
    var index;
    var RetValue = null;

    for (index = 0; index < Object.keys(this.EstimateTrack).length;index++)
    {
      if (this.EstimateTrack[index])
      {
        var d = Pos.GetEuclidianDist2(this.EstimateTrack[index].Position);

        if (d < Dist)
        {
          RetValue = this.EstimateTrack[index];
          Dist=d;
        }
      }
    }

    return RetValue;
  };

  this.ClearEstimatePosition = function(Boat)
  {
    this.ShowEstimatePosition(Boat,null);
  };

  this.ShowEstimatePosition = function(Boat, Estimate)
  {
    // Track Estimate closest point to mousemove
    if (this.EstimateMapFeatures)
    {
      for (let index in this.EstimateMapFeatures)
      {
        if (this.EstimateMapFeatures[index])
        {
          VLMBoatsLayer.removeFeatures(this.EstimateMapFeatures);
        }
      }
      this.EstimateMapFeatures = [];
    }

    if (Estimate && Estimate.Position && Boat.VLMInfo.LON !== Estimate.Position.Lon.Value && Boat.VLMInfo.LAT !== Estimate.Position.Lat.Value)
    {
      let Position = Estimate.Position;
      let EstPos = new OpenLayers.Geometry.Point(Position.Lon.Value, Position.Lat.Value);
      let EstPos_Transformed = EstPos.transform(MapOptions.displayProjection, MapOptions.projection);

      // Estimate point marker
      var Marker=  new OpenLayers.Feature.Vector(
        EstPos_Transformed,
        {},
        { externalGraphic: 'images/target.svg',
          opacity: 0.8, 
          graphicHeight: 48, 
          graphicWidth: 48,
          rotation: Estimate.Heading }
      );
      VLMBoatsLayer.addFeatures(Marker);
      this.EstimateMapFeatures.push(Marker);

      if (typeof Estimate.Meteo !== "undefined")
      {
        var scale = VLM2Prefs.MapPrefs.PolarVacCount;
        var PolarPointList = PolarsManager.GetPolarLine(Boat.VLMInfo.POL, Estimate.Meteo.Speed, DrawBoat, Boat);
        var Polar = [];

        BuildPolarLine(Boat, PolarPointList, Polar, Position, scale, Estimate.Date);
        var BoatPolar = new OpenLayers.Feature.Vector(
          new OpenLayers.Geometry.LineString(Polar),
          {
            "type": "Polar",
            "WindDir": Estimate.Meteo.Heading
          });

        this.EstimateMapFeatures.push(BoatPolar);
        VLMBoatsLayer.addFeatures(BoatPolar);
      }  
    }
  };

}

/*
function HandleEstimatorStart(e)
{
  var e = new Estimator(_CurPlayer.CurBoat);
}
*/
