//
// Position class
//
// Formating, conversion, and geo computation
//
//

const POS_FORMAT_DEFAULT=0;



// Constructor
function Position(lon, lat, format)
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
}



