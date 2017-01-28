
var VLMMercatorTransform = new MercatorTransform

function MercatorTransform ()
{
  this.Width = 10000;
  this.Height = 10000;
  this.LonOffset = 0;
  this.LatOffset = 0
  this.Scale = 10000/180;

  this.LonToMapX = function(Lon)
  {
    return this.Width / 2.0 + (Lon - this.LonOffset) * this.Scale
  }

  this.LatToMapY = function(Lat)
  {
    Lat = Deg2Rad(Lat)
    Lat = Math.log(Math.tan(Lat) + 1 / Math.cos(Lat))
    Lat = Rad2Deg(Lat)
    
    return this.Height / 2.0 - (Lat - this.LatOffset) * this.Scale
     
  }

  this.SegmentsIntersect = function (Seg1, Seg2)
  {
    
    var Ax = this.LonToMapX(Seg1.P1.Lon.Value)
    var Ay = this.LatToMapY(Seg1.P1.Lat.Value)
    var Bx = this.LonToMapX(Seg1.P2.Lon.Value)
    var By = this.LatToMapY(Seg1.P2.Lat.Value)
    var Cx = this.LonToMapX(Seg2.P1.Lon.Value)
    var Cy = this.LatToMapY(Seg2.P1.Lat.Value)
    var Dx = this.LonToMapX(Seg2.P2.Lon.Value)
    var Dy = this.LatToMapY(Seg2.P2.Lat.Value)

    //  Fail if either line is undefined.
    if ((Seg1.P1.Lon.Value === Seg1.P2.Lon.Value && Seg1.P1.Lat.Value === Seg1.P2.Lat.Value) || 
        (Seg2.P1.Lon.Value === Seg2.P2.Lon.Value && Seg2.P1.Lat.Value === Seg2.P2.Lat.Value)) 
    {
      return false
    }


    // (1) Translate the system so that point A is on the origin.
    Bx -= Ax
    By -= Ay
    Cx -= Ax
    Cy -= Ay
    Dx -= Ax
    Dy -= Ay
    Ax = 0
    Ay = 0

    // Discover the length of segment A-B.
    var DistAB = Math.sqrt(Bx * Bx + By * By)
        
    // (2) Rotate the system so that point B is on the positive X axis.
    var theCos = Bx / DistAB
    var theSin = By / DistAB
    var newX = Cx * theCos + Cy * theSin
    Cy = Cy * theCos - Cx * theSin
    Cx = newX
    newX = Dx * theCos + Dy * theSin
    Dy = Dy * theCos - Dx * theSin
    Dx = newX

    // Fail if the lines are parallel.
    if (Cy === Dy)
    {
      return false
    }

    //  (3) Discover the position of the intersection point along line A-B.
    var ABpos = Dx + (Cx - Dx) * Dy / (Dy - Cy)


    var Ratio = ABpos / DistAB


    if (Ratio >= 0 && Ratio <= 1) 
    {

      // Possible Success
      // Check other segment ratio

      // Get Intersect coords
      var Ix = Ax + ABpos
      var Iy = Ay
      var Ratio2 

      if (Dx - Cx) 
      {
        // Seg is not vertical
        Ratio2 = (Ix - Cx) / (Dx - Cx)
      }
      else if  (Dy - Cy) 
      {
        // Seg is vertical
        Ratio2 = (Iy - Cy) / (Dy - Cy)
      }
      else 
      {
        // No segment !!
        return false
      }

      if ((Ratio2 >= 0) && (Ratio2 <= 1)) 
      {
        return true
      }
      else 
      {
        return false
      }
    }
    else 
    {
      // Segments do not intersect
      return false
    }
  }
  
  /*
  Public Function CanvasToLat(ByVal C As Double) Implements IMapTransform.CanvasToLat
    Debug.Assert(Scale <> 0)
    var Ret = (ActualHeight / 2 - C) / Scale + LatOffset
    Ret = Ret / 180 * PI
    return (Math.Atan(Math.Sinh(Ret)) / PI * 180)
  End Function

  Public Function CanvasToLon(ByVal V As Double) Implements IMapTransform.CanvasToLon
    Debug.Assert(Scale <> 0)
    var Ret = ((V - ActualWidth / 2) / Scale + LonOffset) 
    return Ret
  End Function
  */
}