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

    this.GetPolarLine=function(PolarName,WindSpeed, callback, boat)
    {
        if (typeof this.Polars[PolarName] == "undefined")
        {
            alert("Unexpected polarname : " + PolarName)
            return null;
        }
        if (this.Polars[PolarName]==null)
        {
            // Polar not loaded yet, load it
            $.get("/Polaires/"+ PolarName +".csv",
                   function(data)
                   {
                       var polar = $.csv.toArrays(data,{separator:";"});
                       
                       PolarsManager.Polars[PolarName]=polar;

                       callback(boat);
                       
                   }
            )
        }
        else
        {
            var RetPolar = [];

            var alpha;
            var MaxSpeed = 0;
            // Loop to get speedvalue per angle

            for (alpha = 0; alpha <= 360 ; alpha+=5)
            {
                var Speed = GetPolarAngleSpeed(this.Polars[PolarName],alpha, WindSpeed);

                if (MaxSpeed < Speed)
                {
                    MaxSpeed=Speed;
                }
                RetPolar.push(Speed);
            }

            // Scale Polar to 1
            for (index in RetPolar)
            {
                RetPolar[index]/=MaxSpeed;
            }

            return RetPolar;
        }
    }

}

// Returns the speed at given angle for a polar
function GetPolarAngleSpeed  (Polar,Alpha, WindSpeed)
{
    var SpeedCol1;
    var SpeedCol2;
    var AlphaRow1;
    var AlphaRow2;

    // Loop and index index <= Speed
    for (index in Polar[0])
    {
        if ((index >0) && (Polar[0][index])>WindSpeed)
        {
            break;
        }
        SpeedCol1=Math.floor(index);
    }

    SpeedCol2=(SpeedCol1 < Polar[0].length-1)?SpeedCol1+1:SpeedCol1;

    // loop Rows to find angle <= alpha
    Alpha%=360.;
    if (Alpha > 180)
    {
        Alpha = 360 - Alpha;
    }
    for (index in Polar)
    {
        if ((index > 0) && (Polar[index][0]>Alpha))
        {
            break;
        }
        AlphaRow1=Math.floor(index);
    }
    AlphaRow2=(AlphaRow1< Polar.length-1)?AlphaRow1+1:AlphaRow1;

    var v1 = GetAvgValue(WindSpeed,Polar[0][SpeedCol1], Polar[0][SpeedCol2],Polar[AlphaRow1][SpeedCol1], Polar[AlphaRow1][SpeedCol2]);
    var v2 = GetAvgValue(WindSpeed,Polar[0][SpeedCol1], Polar[0][SpeedCol2],Polar[AlphaRow2][SpeedCol1], Polar[AlphaRow2][SpeedCol2]);
    return  GetAvgValue(Alpha,Polar[AlphaRow1][0], Polar[AlphaRow2][0],v1,v2);   

}

// Return Linear interpolated y for x on line (Rx1,Ry1)(Rx2,Ry2)
function GetAvgValue(x,Rx1,Rx2,Ry1,Ry2)
{
    // Cast all params as numbers
    x=parseFloat(x);
    Rx1=parseFloat(Rx1);
    Rx2=parseFloat(Rx2);
    Ry1=parseFloat(Ry1);
    Ry2=parseFloat(Ry2);
    
    if ((x==Rx1) || (Rx1 == Rx2) || (Ry1==Ry2) )
    {
        // Trivial & corner cases
        return Ry1;
    }
    else
    {
        return Ry1+(x-Rx1)/(Rx2-Rx1)*(Ry2-Ry1);
    }
}