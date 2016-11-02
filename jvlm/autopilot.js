//
// Class to handle autopilot orders and services
//

function AutoPilotOrder(Boat,Number)
{
  // Default construction
  this.Date = new Date(new Date().getTime()+5*60*1000);
  this.PIM = PM_HEADING;
  this.PIP_Value = 0;
  this.PIP_Coords = new VLMPosition(0,0);
  this.PIP_WPAngle = -1;
  this.ID = -1;

  if (typeof boat !== 'undefined' && Boat)
  {
    var PilOrder = Boat.VLMInfo.PIL[OrderNumber];

    this.Date = new Date(PilOrder.TTS);
    this.PIM = PilOrder.PIM;
  }

  this.GetOrderDateString = function()
  {
    return this.Date.getDate() +"/"+(this.Date.getMonth()+1)+"/"+this.Date.getFullYear();
  }

  this.GetOrderTimeString = function()
  {
    return this.Date.getHours() +":"+this.Date.getMinutes()+":15";
  }

  this.GetPIMString = function ()
  {
    switch (this.PIM)
    {
      case PM_HEADING:
          return GetLocalizedString("autopilotengaged")
      case PM_ANGLE:
          return GetLocalizedString("constantengaged")
      case PM_ORTHO:
          return GetLocalizedString("orthodromic")
      case PM_VMG:
          return "VMG"
      case PM_VBVMG:
          return "VBVMG"
    }
  }

  this.GetPIPString = function()
  {
    switch (this.PIM)
    {
      case PM_HEADING:
      case PM_ANGLE:
          return this.PIP_Value
      case PM_ORTHO:
      case PM_VMG:
      case PM_VBVMG:
          return this.PIP_Coords.GetVLMString()+"@"+PIP_WPAngle;
    }
  }
}

function HandleSendAPUpdate(e)
{
  var verb= 'add';

  if ((typeof _CurAPOrder === "undefined") || (!_CurAPOrder))
  {
    return;
  }

  if (_CurAPOrder.ID!=-1)
  {
    verb ="update";
  }
  var OrderData={
                  idu:_CurPlayer.CurBoat.IdBoat,
                  tasktime: Math.round(_CurAPOrder.Date/1000),
                  pim:_CurAPOrder.PIM
                }

  switch (_CurAPOrder.PIM)
  {
    case PM_HEADING:
    case PM_ANGLE:
        OrderData["pip"]=_CurAPOrder.PIP_Value;
        break;
    case PM_ORTHO:
    case PM_VMG:
    case PM_VBVMG:
        OrderData["targetlat"]=_CurAPOrder.PIP_Coords.lat;
        OrderData["targetlon"]=_CurAPOrder.PIP_Coords.lon;
        OrderData["targetlat"]=(_CurAPOrder.PIP_WPAngle==-1?null:_CurAPOrder.PIP_WPAngle);
        break;
  }             

  $.post('/ws/boatsetup/pilototo_'+verb+'.php',
          "parms="+JSON.stringify(OrderData),
          function (ap_return)
          {
            if (ap_return.success)
            {
              // Order Success
              RefreshCurrentBoat(false,true);
            }
            else
            {
              alert(ap_return.error.msg);
            }
          }
        )
}

function HandleAPFieldChange(e)
{
  var Target = e.target;

  if (typeof Target.attributes["id"] === "undefined")
  {
    return;
  }

  switch(Target.attributes["id"].value)
  {
    case "AP_PIP":
      _CurAPOrder.PIP_Value=parseFloat(Target.value);
      if (_CurAPOrder.PIP_Value.toString() != Target.Value)
      {
        Target.value=_CurAPOrder.PIP_Value.toString();
      }
      break;
  }
}