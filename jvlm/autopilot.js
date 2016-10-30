//
// Class to handle autopilot orders and services
//

function AutoPilotOrder(Boat,Number)
{
    // Default construction
    this.Date = new Date();
    this.PIM = PM_HEADING;
    this.PIP = 0;
    this.ID = -1;

    if (typeof boat !== 'undefined' && Boat)
    {
        var PilOrder = Boat.VLMInfo.PIL[OrderNumber];

        this.Date = new Date(PilOrder.TTS);
        this.PIM = PilOrder.PIM;
    }

    this.GetOrderDateString = new function()
    {
        return this.Date.getDate() +"/"+this.Date.getMonth()+"/"+this.Date.getFullYear();
    }

     this.GetOrderTimeString = function()
    {
        return this.Date.getHours() +":"+this.Date.getMinutes()+"/"+this.Date.getSeconds();
    }

}