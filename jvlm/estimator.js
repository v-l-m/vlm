

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

  this.Start = function()
  {
    GribMgr.Init();

    if (typeof this.Boat.VLMInfo === "undefined")
    {
      return;
    }

    this.CurEstimate.Position = new VLMPosition(this.Boat.VLMInfo.LON,this.Boat.VLMInfo.LAT)
    this.CurEstimate.Date = new Date (this.Boat.VLMInfo.LUP*1000 + 1000* this.Boat.VLMInfo.VAC)
    this.CurEstimate.Mode = parseInt(this.Boat.VLMInfo.PIM,10);
    this.CurEstimate.CurWP = new VLMPosition(this.Boat.VLMInfo.WPLON, this.Boat.VLMInfo.WPLAT)

    if ((this.CurEstimate.Mode == PM_HEADING) || (this.CurEstimate.Mode == PM_ANGLE))
    {
      this.CurEstimate.Value = parseFloat(this.Boat.VLMInfo.PIP);
    }
    this.CurEstimate.PilOrders = this.Boat.VLMInfo.PIL;
    this.Boat.EstimateTrack=[];
    this.Boat.EstimatePoints=[];

    // FixMe : replace with actual grib horizon from gribmgr.
    this.MaxVacEstimate = new Date(VLM2GribManager.MaxWindStamp); 
    setTimeout(this.Estimate.bind(this),2000)
  }

  this.Estimate = function(Boat)
  {
      
    if (this.CurEstimate.Date >= this.MaxVacEstimate)
    {
      //Estimate complete, DrawBoat track
      DrawBoat(this.Boat);
      return;
    }

    var MI = GribMgr.WindAtPointInTime(this.CurEstimate.Date,this.CurEstimate.Position.Lat.Value,this.CurEstimate.Position.Lon.Value)
    if (!MI)
    {
      setTimeout(this.Estimate.bind(this),1000);
      return;
    }
    
    this.CurEstimate.Meteo = MI;

    // Ok, got meteo, move the boat, and ask for new METEO

    // Check if an update is required from AutoPilot;
    for (index in this.CurEstimate.PilOrders)
    {
      var i = 0;
      //if (this.CurEstimate.PilOrders[Index])
      throw "pilot not supported yet...."
    }

      var Hdg = this.CurEstimate.Value;
      switch (this.CurEstimate.Mode)
      {
        case PM_ANGLE:  // This goes just before Heading, since we only update the Hdg, rest is the same
          // Going fixed angle, get bearing, compute speed, move
          Hdg = MI.Heading+this.CurEstimate.Value;
              
        case PM_HEADING:
          // Going fixed bearing, get boat speed, move along loxo
          
          var Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,MI.Speed,MI.Heading,Hdg);
          var NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed/3600.*this.Boat.VLMInfo.VAC, Hdg);
          console.log(this.CurEstimate.Date + " " + NewPos.Lon.ToString() + " " + NewPos.Lat.ToString())
          
          break;

        case PM_ORTHO:
        case PM_VMG:
        case PM_VBVMG:
          var Dest = this.GetNextWPCoords(this.CurEstimate)
          
          if (this.CurEstimate.Mode == PM_ORTHO)
          {
            Hdg = this.CurEstimate.Position.GetOrthoCourse(Dest);
            var Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,MI.Speed,MI.Heading,Hdg);
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
            var Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,MI.Speed,MI.Heading,Hdg);
            var NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed/3600.*this.Boat.VLMInfo.VAC, Hdg);
          
          }
          break;


        default:
          throw "Unsupported pilotmode for estimate..." + this.CurEstimate.Mode
      }

      console.log(this.CurEstimate.Date + " " + NewPos.Lon.ToString() + " " + NewPos.Lat.ToString())
      this.CurEstimate.Position = NewPos;
      this.Boat.EstimateTrack.push(new BoatEstimate( this.CurEstimate))

      // Start next point computation....
      this.CurEstimate.Date = new Date((this.CurEstimate.Date/1000+this.Boat.VLMInfo.VAC)*1000)
      setTimeout(this.Estimate.bind(this),0);
  }

  this.GetNextWPCoords = function (Estimate)
  {
    if (Estimate.CurWP.Lat.value || Estimate.CurWP.Lon.Value)
    {
      return Estimate.CurWP;
    }
    else
    {
      throw "unsupported estimating to AUTOWP"
      // Get CurRaceWP
      // Compute closest point (using bad euclidian method)
      // Return computed point
    }
    
  }

  this.Start()

}

function HandleEstimatorStart(e)
{
  var e = new Estimator(_CurPlayer.CurBoat);
}
