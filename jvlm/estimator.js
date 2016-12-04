

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

        this.CurEstimate.Position = new VLMPosition(this.Boat.VLMInfo.LON,this.Boat.VLMInfo.LAT)
        this.CurEstimate.Date = new Date (this.Boat.VLMInfo.LUP*1000 + 1000* this.Boat.VLMInfo.VAC)
        this.CurEstimate.Mode = this.Boat.VLMInfo.PIM
        this.CurEstimate.Value = this.Boat.VLMInfo.PIP
        this.CurEstimate.PilOrders = new Array(this.Boat.VLMInfo.PIL);

        // FixMe : replace with actual grib horizon from gribmgr.
        this.MaxVacEstimate = 7*24*3600; 
        setTimeout(this.Estimate(),2000)
    }

    this.Estimate = function(Boat)
    {
        var VarMaxTime = new Date (new Date() + this.MaxVacEstimate*1000);
        
        if (this.CurEstimate.Date >= this.MaxVacEstimate)
        {
            //Estimate complete
            return;
        }

        var MI = GribMgr.WindAtPointInTime(this.CurEstimate.Date,This.CurEstimate.Position.LAT,This.CurEstimate.Position.LON)
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

        }
        switch (this.Cur)
        {
            
        }
    }

    this.Start()

}

function HandleEstimatorStart(e)
{
    var e = new Estimator(_CurPlayer.CurBoat);
}