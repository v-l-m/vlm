

function Coords(v)
{
  this.value=Math.abs(v);
  this.Deg=function()
  {
    return this.value;
  };
  this.Min=function()
  {
    return (this.value - Math.floor(this.Deg()))*60;
  };
  this.Sec=function()
  {
    return (this.Min() - Math.floor(this.Min()))*60;
  };
  
  this.ToString=function()
  {
    return Math.floor(this.Deg()) +"° "+ Math.floor(this.Min()) + "' " + Math.floor(this.Sec()) + '"';
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