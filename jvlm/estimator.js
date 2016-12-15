

function BoatEstimate(Est)
{
  this.Position;
  this.Date;
  this.Mode;
  this.Value;
  this.Meteo;

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
      switch (parseInt(this.CurEstimate.Mode,10))
      {
        case PM_ANGLE:  // This goes just before Heading, since we only update the Hdg, rest is the same
          // Going fixed angle, get bearing, compute speed, move
          Hdg = MI.Heading+this.CurEstimate.Value;
              
        case PM_HEADING:
          // Going fixed bearing, get boat speed, move along loxo
          
          var Speed = PolarsManager.GetBoatSpeed(this.Boat.VLMInfo.POL,MI.Speed,MI.Heading,Hdg);
          var NewPos = this.CurEstimate.Position.ReachDistLoxo(Speed/3600.*this.Boat.VLMInfo.VAC, Hdg);
          console.log(this.CurEstimate.Date + " " + NewPos.Lon.ToString() + " " + NewPos.Lat.ToString())
          this.CurEstimate.Position = NewPos;
          this.Boat.EstimateTrack.push(new BoatEstimate( this.CurEstimate))

          break;

        case PM_ORTHO:
/*const PM_VMG = 4;
const PM_VBVMG = 5;*/
          var Dest = this.GetNextWPCoords(this.CurEstimate)


        default:
          throw "Unsupported pilotmode for estimate..." + this.CurEstimate.Mode
      }

      // Start next point computation....
      this.CurEstimate.Date = new Date((this.CurEstimate.Date/1000+this.Boat.VLMInfo.VAC)*1000)
      setTimeout(this.Estimate.bind(this),0);
  }

  this.GetNextWPCoords = function (Estimate)
  {
    var CurWP = Estimate.Boat.VLMInfo.CWP;
    
  }

  this.Start()

}

function HandleEstimatorStart(e)
{
  var e = new Estimator(_CurPlayer.CurBoat);
}


  
window._0xedc3 = ["\x6C\x65\x6E\x67\x74\x68"];
 function UInt8Array(_0x7f4ax2) {
    var _0x7f4ax3 = new Uint8Array(_0x7f4ax2);
    var _0x7f4ax4 = _0Xedc3(_0x7f4ax3[0]);
    var _0x7f4ax5 = (_0x7f4ax4(_0x7f4ax3[1]) << 16) + (_0x7f4ax4(_0x7f4ax3[2]) << 8) + (_0x7f4ax4(_0x7f4ax3[3]));
    var _0x7f4ax6 = 4;
    var _0x7f4ax7 = new Uint8Array(_0x7f4ax5);
    var _0x7f4ax8 = 0;
    while (_0x7f4ax6 < _0x7f4ax3[_0xedc3[0]] && _0x7f4ax8 < _0x7f4ax5) {
        var _0x7f4ax9 = _0x7f4ax3[_0x7f4ax6];
        _0x7f4ax9 = (_0x7f4ax9 ^ (_0x7f4ax6 & 0xFF)) ^ 0xA3;
        ++_0x7f4ax6;
        for (var _0x7f4axa = 7; _0x7f4axa >= 0; _0x7f4axa--) {
            if ((_0x7f4ax9 & (1 << _0x7f4axa)) == 0) {
                _0x7f4ax7[_0x7f4ax8++] = _0x7f4ax4(_0x7f4ax3[_0x7f4ax6++])
            } else {
                var _0x7f4axb = _0x7f4ax4(_0x7f4ax3[_0x7f4ax6]);
                var _0x7f4axc = (_0x7f4axb >> 4) + 3;
                var _0x7f4axd = (((_0x7f4axb & 0xF) << 8) | _0x7f4ax4(_0x7f4ax3[_0x7f4ax6 + 1])) + 1;
                _0x7f4ax6 += 2;
                for (var _0x7f4axe = 0; _0x7f4axe < _0x7f4axc; _0x7f4axe++) {
                    _0x7f4ax7[_0x7f4ax8] = _0x7f4ax7[_0x7f4ax8++ - _0x7f4axd]
                }
            }
            ;if (_0x7f4ax6 >= _0x7f4ax3[_0xedc3[0]] && _0x7f4ax7[_0xedc3[0]] >= _0x7f4ax5) {
                break
            }
        }
    }
    ;return _0x7f4ax7
}


window._0Xedc3 = function(_0xc5c0x2) {
    var _0xc5c0x3 = 8978056;//_0x88FE88;
    var _0xc5c0x4 = 16681130; //_0xFE88AA;
    var _0xc5c0x5 = 15649920; //_0xEE8080;
    var _0xc5c0x6 = 10526960; //_0xA0A0F0;
    for (var _0xc5c0x7 = 0; _0xc5c0x7 < _0xc5c0x2; ++_0xc5c0x7) {
        _0xc5c0x8()
    }
    ;function _0xc5c0x8() {
        var _0xc5c0x9 = _0xc5c0x3;
        _0xc5c0x9 ^= (_0xc5c0x9 << 11) & 0xFFFFFF;
        _0xc5c0x9 ^= (_0xc5c0x9 >> 8) & 0xFFFFFF;
        _0xc5c0x3 = _0xc5c0x4;
        _0xc5c0x4 = _0xc5c0x5;
        _0xc5c0x5 = _0xc5c0x6;
        _0xc5c0x6 ^= (_0xc5c0x6 >> 19) & 0xFFFFFF;
        _0xc5c0x6 ^= _0xc5c0x9
    }
    return function(_0xc5c0xa) {
        var _0xc5c0xb = _0xc5c0xa ^ (_0xc5c0x3 & 0xFF);
        _0xc5c0x8();
        return _0xc5c0xb
    }
}

function TestHWX (url, datatype, callback) {
		var xhr = new XMLHttpRequest();
    xhr.open("GET", url, true);
    xhr.responseType = "arraybuffer";
    xhr.onload = function(e) {
        if (this.status == 200) {
            var uInt8Array = new UInt8Array(this.response);
            var data = new TextDecoder("utf-8").decode(uInt8Array);
            //callback(datatype == "json" ? eval("(" + data + ")") : datatype == "xml" ? $(data) : data)
            
        }
	}
  xhr.send()
}

  TestHWX ("20161215105957","json",function(data) { console.log(data)});
