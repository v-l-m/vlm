//
// Position class
//
// Formating, conversion, and geo computation
//
//

const POS_FORMAT_DEFAULT = 0;
// Earth radius for all calculation of distance in Naut. Miles
const EARTH_RADIUS  = 3443.84;
        

function Deg2Rad(v)
{
    return v/180.0*Math.PI;
}

function Rad2Deg(v)
{
    return v/Math.PI*180.0;
}

function RoundPow(v,P)
{
    if(P)
    {
        var Div = Math.pow(10,P);
        return Math.round(v*Div)/Div;
    }
    else
    {
        return v
    }
}


// Constructor
function VLMPosition(lon, lat,  format)
{   
    if (typeof format == 'undefined' || format == POS_FORMAT_DEFAULT)
    {
        // Default constructor, lon and lat in degs flaoting format
        this.Lon=new Coords(lon,1);
        this.Lat=new Coords(lat,0);
    }

    // Default string formating
    this.ToString=function()
    {
        return this.Lat.ToString() + " " + this.Lon.ToString();
    }

    // Function GetOrthoDist
    // Return ortho distance from this to P
    this.GetOrthoDist = function(P,Precision)
    {
        var lon1  = -Deg2Rad(this.Lon.Value);
        var lon2  = -Deg2Rad(P.Lon.Value);
        var lat1  = Deg2Rad(this.Lat.Value);
        var lat2  = Deg2Rad(P.Lat.Value);

//        d=2*asin(sqrt((sin((lat1-lat2)/2))^2 + 
//                 cos(lat1)*cos(lat2)*(sin((lon1-lon2)/2))^2))

        var retval = 2*Math.asin(Math.sqrt(Math.pow((Math.sin((lat1-lat2)/2)),2) + 
                 Math.pow(Math.cos(lat1)*Math.cos(lat2)*(Math.sin((lon1-lon2)/2)),2)))

        return RoundPow(EARTH_RADIUS* retval,Precision);
    }
    
    // function GetLoxoDist
    // Returns the loxodromic distance to another point
    this.GetLoxoDist= function(P,Precision)
    {

        var Lat1  = Deg2Rad(this.Lat.Value);
        var Lat2  = Deg2Rad(P.Lat.Value);
        var Lon1  = -Deg2Rad(this.Lon.Value);
        var Lon2  = -Deg2Rad(P.Lon.Value);


        var TOL  = 0.000000000000001;
        var d =0;
        var q=0;
        if (Math.abs(Lat2 - Lat1) < Math.sqrt(TOL)) 
        {
            q = Math.cos(Lat1);
        }
        else
        {
        	 q = (Lat2 - Lat1) / Math.log(Math.tan(Lat2 / 2 + Math.PI / 4) / Math.tan(Lat1 / 2 +Math.PI / 4));
        }

        d= Math.sqrt(Math.pow(Lat2 - Lat1, 2) + q * q * (Lon2 - Lon1) * (Lon2 - Lon1) );
        var RetVal = EARTH_RADIUS *d;
        
        
        return  RoundPow(RetVal,Precision);
    }

    // Reaches a point from position using rhumbline.
    // Compute the position of point at r * distance to point P is 1st param is a Position
    // Computes the position at Distance P, and heading r if P is a number
    // Along loxodrome from this to P
    this.ReachDistLoxo = function(P, r)
    {
        var d = 0;
        var tc= 0;

       if (typeof P == "number")
        {
            d=P/EARTH_RADIUS;
            tc=Deg2Rad(r % 360);
        }
        else
        {
            d=this.GetLoxoDist(P)/EARTH_RADIUS*r;
            tc  = Deg2Rad(this.GetLoxoCourse(P));
        }
        
        var Lat1  = Deg2Rad(this.Lat.Value);
        var Lon1  = -Deg2Rad(this.Lon.Value);
        var Lat =0; 
        var Lon =0;
        var TOL  = 0.000000000000001;
        var q =0;
        var dPhi =0;
        var dlon =0;

        Lat = Lat1 + d * Math.cos(tc);
        if (Math.abs(Lat) > Math.PI / 2) 
        {
            //'"d too large. You can't go this far along this rhumb line!"
            throw "Invalid distance, can't go that far";
        }

        if (Math.abs(Lat - Lat1) < Math.sqrt(TOL))
        {
            q = Math.cos(Lat1);
        }
        else
        {
            dPhi = Math.log(Math.tan(Lat / 2 + Math.PI / 4) / Math.tan(Lat1 / 2 +Math.PI / 4));
            q = (Lat - Lat1) / dPhi;
        }
        dlon = -d * Math.sin(tc) / q;
        Lon = -(((Lon1 + dlon +Math.PI) % (2 *Math.PI) - Math.PI));

        return new VLMPosition(Rad2Deg(Lon),Rad2Deg(Lat));


    };

    //
    // Return loxodromic course from this to P in °
    //
    this.GetLoxoCourse = function(P,Precision)
    {
        var Lon1  = -Deg2Rad(this.Lon.Value);
        var Lon2  = -Deg2Rad(P.Lon.Value);
        var Lat1  = Deg2Rad(this.Lat.Value);
        var Lat2  = Deg2Rad(P.Lat.Value);

        /*if (Lon1 > 0)
        {
            Lon2 += 2 * Math.PI
        }
        else
        {   
            Lon2 -= 2 * Math.PI
        }*/;
        var dlon_w  = (Lon2 - Lon1) % (2 * Math.PI);
        var dlon_e  = (Lon1 - Lon2) % (2 * Math.PI);
        var dphi  = Math.log(Math.tan(Lat2 / 2 + Math.PI / 4) / Math.tan(Lat1 / 2 + Math.PI / 4));
        var tc ;

        
        if (dlon_w < dlon_e) 
        { // Westerly rhumb line is the shortest
            tc = Math.atan2(dlon_w, dphi) % (2 * Math.PI);
            
        }
        else
        {
            tc = Math.atan2(-dlon_e, dphi) % (2 * Math.PI);
           

        }

        var ret  = (720 - (tc / Math.PI * 180)) % 360;

        return RoundPow( ret,Precision);
    };

    //
    // Return orthodromic course from this to P
    //
    this.GetOrthoCourse = function(P,Precision)
    {
        var lon1  = -Deg2Rad(this.Lon.Value);
        var lon2  = -Deg2Rad(P.Lon.Value);
        var lat1  = Deg2Rad(this.Lat.Value);
        var lat2  = Deg2Rad(P.Lat.Value);

        //tc1=mod(atan2(sin(lon1-lon2)*cos(lat2),
        //   cos(lat1)*sin(lat2)-sin(lat1)*cos(lat2)*cos(lon1-lon2)), 2*pi)
        var retval = tc1=Math.atan2(Math.sin(lon1-lon2)*Math.cos(lat2),Math.cos(lat1)*Math.sin(lat2)-Math.sin(lat1)*Math.cos(lat2)*Math.cos(lon1-lon2));
        retval = Rad2Deg( retval % (2 * Math.PI));
        return RoundPow( retval,Precision);
    }
    
    this.ReachDistOrtho=function(dist,bearing)
    {
        var lat;
        var dlon;
        var d=dist/EARTH_RADIUS;
        var tc = Deg2Rad(bearing);
        var CurLat = Deg2Rad(this.Lat.Value);
        var CurLon = Deg2Rad(-this.Lon.Value);

        lat =Math.asin(Math.sin(CurLat)*Math.cos(d)+Math.cos(CurLat)*Math.sin(d)*Math.cos(tc))
        dlon=Math.atan2(Math.sin(tc)*Math.sin(d)*Math.cos(CurLat),Math.cos(d)-Math.sin(CurLat)*Math.sin(lat))
        lon=(( CurLon-dlon +Math.PI)%(2*Math.PI ))-Math.PI;
        return new VLMPosition(Rad2Deg(-lon), Rad2Deg(lat));

    }

    this.GetVLMString=function()
    {
        return lat.ToString() +','+lon.ToString();
    }
};



