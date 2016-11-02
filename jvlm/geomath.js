
//
// Coords Class
// Basic coordinates conversions and formating
//
//
function Coords(v, IsLon)
{
  if (typeof v == 'number')
  {
    this.Value=v;
  }
  else
  {
    this.Value=parseFloat(v);
  }
  this.IsLon = IsLon;
  
  // Returns the degree part of a coordinate is floating format
  this.Deg=function()
  {
    return Math.abs(this.Value);
  };

  // Returns the minutes part of a coordinate in floating format
  this.Min=function()
  {
    return (Math.abs(this.Value) - Math.floor(this.Deg()))*60;
  };

  // Returns the second part of a coordinate in floating format
  this.Sec=function()
  {
    return (this.Min() - Math.floor(this.Min()))*60;
  };
  
  // Basic string formatting of a floating coordinate
  this.ToString=function()
  {
    var Side=""

    if (typeof this.IsLon == 'undefined' || this.IsLon==0)
    {
      Side = (this.Value>=0?' N':' S')
    }
    else
    {
      Side = (this.Value>=0?' E':' W')
    }
    
    return Math.floor(this.Deg()) +"° "+ Math.floor(this.Min()) + "' " + Math.floor(this.Sec()) + '"' + Side;
  };
}

//
// Returns the deg, min, sec parts of a coordinate in decimal number
//
function GetDegMinSecFromNumber(n,d,m,s)
{
  var DecPart;
  
  SplitNumber(n,d,DecPart);
  SplitNumber(DecPart*60,m,DecPart);
  SplitNumber(DecPart*60,s,DecPart);
  return;
}

//
// Split a number between its integer and decimal part
function SplitNumber(n,i,d)
{
  i=Math.floor(n);
  d=n-i;
}