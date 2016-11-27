

function Estimator()
{
    this.MaxVacEstimate = 7*24*3600;      // Compute for 7 Days

    this.Start = function(Boat)
    {
        GribMgr.Init();
        setTimeout(this.Estimate(Boat),2000)
    }

    this.Estimate = function(Boat)
    {

        if ((typeof Boat === "undefined") || ! Boat || (typeof Boat.VlmInfo === "undefined"))
        {
            return;
        }

        var CurTime = new Date( parseInt(Boat.VlmInfo.LUP,10));
        var VarMaxTime = new Date (Curtime + this.MaxVacEstimate*1000);
        var VacInterval = Boat.VlmInfo.VAC;
        var CurPos = new latlon( Boat.VlmInfo.LON, Boat.VlmInfo.LAT)

        while (CurTime < VarMaxTime)
        {
            CurTime = new Date (CurTime + VacInterval*1000)

            //var WindInfo = 
        }
    }

}