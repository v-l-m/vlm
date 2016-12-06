

function BoatEstimate()
{
    this.Position;
    this.Date;
    this.Mode;
    this.Value;
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

        if ((this.CurEstimate.Mode == PM_HEADING) || (this.CurEstimate.Mode == PM_ANGLE))
        {
            this.CurEstimate.Value = parseFloat(this.Boat.VLMInfo.PIP);
        }
        this.CurEstimate.PilOrders = this.Boat.VLMInfo.PIL;

        // FixMe : replace with actual grib horizon from gribmgr.
        this.MaxVacEstimate = 7*24*3600; 
        setTimeout(this.Estimate.bind(this),2000)
    }

    this.Estimate = function(Boat)
    {
        var VarMaxTime = new Date ((new Date()/1000 + this.MaxVacEstimate)*1000);
        
        if (this.CurEstimate.Date >= VarMaxTime)
        {
            //Estimate complete
            return;
        }

        var MI = GribMgr.WindAtPointInTime(this.CurEstimate.Date,this.CurEstimate.Position.Lat.Value,this.CurEstimate.Position.Lon.Value)
        if (!MI)
        {
            setTimeout(this.Estimate(),2000);
            return;
        }

        // Ok, got meteo, move the boat, and ask for new METEO

        // Check if an update is required from AutoPilot;
        for (index in this.CurEstimate.PilOrders)
        {
            var i = 0;
            //if (this.CurEstimate.PilOrders[Index])
            throw "pilot not supported yet...."
        }

        switch (parseInt(this.CurEstimate.Mode,10))
        {
            case PM_HEADING:
                // Going fixed bearing, get boat speed, move along loxo
                //
                
                var Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,MI.Speed,MI.Heading,this.CurEstimate.Value);
                var NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed/this.Boat.VLMInfo.VAC)
                console.log(this.CurEstimate.Date + " " + NewPos.toString)
                this.CurEstimate.Position = NewPos;

                break;
/*const PM_ANGLE = 2;
const PM_ORTHO = 3;
const PM_VMG = 4;
const PM_VBVMG = 5;*/
            default:
                throw "Unsupported pilotmode for estimate..." + this.CurEstimate.Mode
        }

        // Start next point computation....
        this.CurEstimate.Date = new Date((this.CurEstimate.Date/1000+this.Boat.VLMInfo.VAC)*1000)
        setTimeout(this.Estimate.bind(this),0);
    }

    this.Start()

}

function HandleEstimatorStart(e)
{
    var e = new Estimator(_CurPlayer.CurBoat);
}