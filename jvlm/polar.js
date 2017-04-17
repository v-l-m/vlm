//
// Module for handling boat polars
//

var PolarsManager = new PolarManagerClass();
    

function PolarManagerClass()
{
  this.Polars =[];

  this.Init = function()
  {
    this.Polars=new Array();
    // Bg load the list of boats with a polar in VLM
    $.get("/ws/polarlist.php",
            function (Data)
            {
              //Parse WS data, build polarlist and URL
              // Build list of boat for lazy loading
              for (index in Data.list)
              {
                PolarsManager.Polars["boat_"+Data.list[index]]=null;
              }
            }
        )
  }

  this.GetBoatSpeed=function(PolarName, WindSpeed, WindBearing, BoatBearing)
  {
    if (! (PolarName in this.Polars))
    {
        return NaN;
    }
    if (!this.Polars[PolarName])
    {
      // Polar not loaded yet, load it
      $.get("/Polaires/"+ PolarName +".csv",this.HandlePolarLoaded.bind(this, PolarName,null, null))

      return NaN;
    }
    else
    {
      var alpha = WindAngle (BoatBearing , WindBearing)
      var Speed = GetPolarAngleSpeed(this.Polars[PolarName],alpha, WindSpeed);

      return Speed;
    }

  }
  
  this.HandlePolarLoaded = function(PolarName,callback,Boat, data)
  {
    var polar = $.csv.toArrays(data,{separator:";"});

    // Convert back all values to floats.
    for (row in polar)
    {
      if (polar[row])
      {
        for (col in polar[row])
        {
          if (polar[row][col])
          {
            polar[row][col]=parseFloat(polar[row][col]);
          }
        }
      }
    }
    PolarsManager.Polars[PolarName]={};
    PolarsManager.Polars[PolarName].SpeedPolar=polar;
    PolarsManager.Polars[PolarName].WindLookup=[];
    PolarsManager.Polars[PolarName].AngleLookup=[];

    if (callback && Boat)
    {
      callback(Boat);
    }
  }

  this.GetPolarLine=function(PolarName,WindSpeed, callback, boat)
  {
    if (typeof this.Polars[PolarName] === "undefined")
    {
        alert("Unexpected polarname : " + PolarName)
        return null;
    }
    if (this.Polars[PolarName] === null)
    {
      // Polar not loaded yet, load it
      $.get("/Polaires/"+ PolarName +".csv",this.HandlePolarLoaded.bind(this, PolarName,callback,boat))
    }
    else
    {
      var RetPolar = [];

      var alpha;
      var MaxSpeed = 0;
      // Loop to get speedvalue per angle

      for (alpha = 0; alpha <= 180 ; alpha+=5)
      {
        var Speed = GetPolarAngleSpeed(this.Polars[PolarName],alpha, WindSpeed);

        if (MaxSpeed < Speed)
        {
          MaxSpeed=Speed;
        }
        RetPolar.push (Speed);
      }

      // Scale Polar to 1
      for (index in RetPolar)
      {
        if (RetPolar[index])
        {
          RetPolar[index]/=MaxSpeed;
        }
      }

      return RetPolar;
    }
  }

  var DebugVMG = 0;
  this.GetVMGCourse = function(Polar,WindSpeed,WindBearing,StartPos, DestPos)
  {
    var OrthoBearing = StartPos.GetOrthoCourse(DestPos);
    var BestAngle = 0;
    var BestVMG = -1e10;

    for (var dir =-1 ; dir <= 1 ; dir +=2)
    {
      for (var angle = 0.; angle <=90; angle += 0.1)
      {
        var CurSpeed = this.GetBoatSpeed (Polar,WindSpeed,WindBearing,OrthoBearing + angle*dir)
        var CurVMG = CurSpeed * Math.cos(Deg2Rad(angle))

        if (DebugVMG )
        {
          console.log ("VMG "+ RoundPow((OrthoBearing + angle*dir+360.)%360.,3) + " " + RoundPow(CurSpeed,3) + " " + RoundPow(CurVMG,3) + " " + RoundPow(BestVMG,3) + " " + (CurVMG >= BestVMG?"BEST":"") )
        }

        if (CurVMG >= BestVMG)
        {
          BestVMG = CurVMG;
          BestAngle = OrthoBearing+ angle*dir;
        }
      }   
    }

    DebugVMG = 0;
    return BestAngle;
  }

  this.GetVBVMGCourse = function(Polar,WindSpeed,WindBearing,StartPos, DestPos)
  {
    var Dist = StartPos.GetOrthoDist(DestPos)
    var CapOrtho  = StartPos.GetOrthoCourse(DestPos)
    var b_Alpha = 0;
    var b_Beta = 0;
    var SpeedAlpha = 0;
    var SpeedBeta = 0;
        
    var Speed = this.GetBoatSpeed(Polar, WindSpeed, WindBearing, CapOrtho)
    if (Speed > 0) 
    {
      t_min = Dist / Speed
    }
    else
    {
      t_min = 365 * 24
    }
    var angle = WindBearing - CapOrtho

    if (angle < -90) 
    {
      angle += 360
    }
    else if (angle > 90) 
    {
      angle -= 360
    }
    
    if (angle > 0) 
    {
      ISigne = -1
    }
    else
    {
      ISigne = 1;
    }
    
    for (var i = 1; i<=  90; i++)
    {
      alpha = i * Math.PI / 180
      TanAlpha = Math.tan(alpha)
      D1HypotRatio = Math.sqrt(1 + TanAlpha * TanAlpha)
      SpeedT1 = this.GetBoatSpeed(Polar, WindSpeed, WindBearing, CapOrtho - i * ISigne)
      
      if (isNaN(SpeedT1))
      {
        throw "Nan SpeedT1 exception"
      }
      if (SpeedT1 > 0) 
      {

        for (j = -89 ; j<= 0; j++)
        {
          beta = j * Math.PI / 180
          D1 = Dist * (Math.tan(-beta) / (TanAlpha + Math.tan(-beta)))
          L1 = D1 * D1HypotRatio
          
          T1 = L1 / SpeedT1
          if ((T1 < 0) || (T1 > t_min)) 
          {
            continue 
          }
          
          D2 = Dist - D1
          
          SpeedT2 = this.GetBoatSpeed(Polar, WindSpeed, WindBearing, CapOrtho -j * ISigne)
          
          if (isNaN(SpeedT2))
          {
            throw "Nan SpeedT2 exception"
          }
          
          if (SpeedT2 <= 0) 
          {
            continue 
          }
          
          TanBeta = Math.tan(-beta)
          L2 = D2 * Math.sqrt(1 + TanBeta * TanBeta)
          
          T2 = L2 / SpeedT2
          
          T = T1 + T2
          if (T < t_min) 
          {
            t_min = T
            b_Alpha = i
            b_Beta = j
            b_L1 = L1
            b_L2 = L2
            b_T1 = T1
            b_T2 = T2
            SpeedAlpha = SpeedT1
            SpeedBeta = SpeedT2
          }
        }
      }

    }

    
    VMGAlpha = SpeedAlpha * Math.cos(Deg2Rad(b_Alpha))
    VMGBeta = SpeedBeta * Math.cos(Deg2Rad(b_Beta))

    if (isNaN(VMGAlpha) || isNaN(VMGBeta))
    {
      throw "NaN VMG found"
    }

    if (VMGAlpha > VMGBeta) 
    {
      return CapOrtho - b_Alpha * ISigne
    }
    else
    {
      return CapOrtho - b_Beta * ISigne
    }
  }

}

// Returns the speed at given angle for a polar
function GetPolarAngleSpeed  (PolarObject,Alpha, WindSpeed)
{
  var SpeedCol1;
  var SpeedCol2;
  var AlphaRow1;
  var AlphaRow2;

  while (Alpha < 0)
  {
    Alpha+=180.;
  }

  Alpha %= 180.000001;

  // Loop and index index <= Speed
  var Polar = PolarObject.SpeedPolar;
  var IntWind = Math.floor(WindSpeed);

  if ((typeof PolarObject.WindLookup !== "undefined") &&  (IntWind in PolarObject.WindLookup))
  {
    SpeedCol1=PolarObject.WindLookup[IntWind];
  }
  else
  {
    for (index in Polar[0])
    {
      if ((index >0) && (Polar[0][index])>WindSpeed)
      {
        break;
      }
      PolarObject.WindLookup[IntWind]=Math.floor(index);
      SpeedCol1=Math.floor(index);
    }
  }

  SpeedCol2=(SpeedCol1 < Polar[0].length-1)?SpeedCol1+1:SpeedCol1;

  // loop Rows to find angle <= alpha
  Alpha%=360.;
  if (Alpha > 180)
  {
    Alpha = 360 - Alpha;
  }

  var IntAlpha = Math.floor(Alpha);
  if ( (typeof PolarObject.AngleLookup !== "undefined") && (IntAlpha in PolarObject.AngleLookup))
  {
    AlphaRow1 = PolarObject.AngleLookup[IntAlpha];
  }
  else
  {

    for (index in Polar)
    {
      if ((index > 0) && (Polar[index][0]>Alpha))
      {
        break;
      }
      PolarObject.AngleLookup[IntAlpha] = Math.floor(index);
      AlphaRow1=Math.floor(index);
    }
  }
  AlphaRow2=(AlphaRow1< Polar.length-1)?AlphaRow1+1:AlphaRow1;

  var v1 = GetAvgValue(WindSpeed,Polar[0][SpeedCol1], Polar[0][SpeedCol2],Polar[AlphaRow1][SpeedCol1], Polar[AlphaRow1][SpeedCol2]);
  var v2 = GetAvgValue(WindSpeed,Polar[0][SpeedCol1], Polar[0][SpeedCol2],Polar[AlphaRow2][SpeedCol1], Polar[AlphaRow2][SpeedCol2]);
  return  GetAvgValue(Alpha,Polar[AlphaRow1][0], Polar[AlphaRow2][0],v1,v2);   

}

function WindAngle (BoatBearing, WindBearing)
{

  var I = 0

  if (BoatBearing >= WindBearing) 
  {
    if ((BoatBearing - WindBearing) <= 180.) 
    {
        I = BoatBearing - WindBearing
    }
    else
    {
      I = 360 - BoatBearing + WindBearing
    }
  }
  else
  {
    if ((WindBearing - BoatBearing) <= 180.) 
    {
      I = WindBearing - BoatBearing
    }
    else
    {
      I = 360 - WindBearing + BoatBearing
    }
  }

  return I

}

// Return Linear interpolated y for x on line (Rx1,Ry1)(Rx2,Ry2)
function GetAvgValue(x,Rx1,Rx2,Ry1,Ry2)
{
  // Cast all params as numbers
  /*x=parseFloat(x);
  Rx1=parseFloat(Rx1);
  Rx2=parseFloat(Rx2);
  Ry1=parseFloat(Ry1);
  Ry2=parseFloat(Ry2);
  */

  if ((x === Rx1) || (Rx1 === Rx2) || (Ry1 === Ry2) )
  {
    // Trivial & corner cases
    return Ry1;
  }
  else
  {
    return Ry1+(x-Rx1)/(Rx2-Rx1)*(Ry2-Ry1);
  }
}