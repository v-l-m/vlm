

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
    this.MaxVacEstimate = 0;      // Compute for 7 Days
    this.CurEstimate = new BoatEstimate()

    this.Start = function()
    {
        GribMgr.Init();

        this.CurEstimate.Position = new VLMPosition(this.Boat.VlmInfo.LON,this.Boat.VlmInfo.LAT)
            .Date = this.Boat.VlmInfo.LUP
            .Mode = this.Boat.VlmInfo.PIM
            .Value = this.Boat.VlmInfo.PIP
            .PilOrders = new array(this.Boat.VlmInfo.PIL);
            
        setTimeout(this.Estimate(),2000)
    }

    this.Estimate = function(Boat)
    {
        var VarMaxTime = new Date (Curtime + this.MaxVacEstimate*1000);
        
        if (this.CurEstimateTime >= VarMaxTime)
        {
            //Estimate complete
            return;
        }
        var VacInterval = Boat.VlmInfo.VAC;
        var CurPos = new latlon( Boat.VlmInfo.LON, Boat.VlmInfo.LAT)

        while (CurTime < VarMaxTime)
        {
            CurTime = new Date (CurTime + VacInterval*1000)

            //var WindInfo = 
        }
    }

    this.Start()

}