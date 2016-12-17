
// Create and init a manager
var GribMgr = new VLM2GribManager();

GribMgr.Init();

function GribData(InitStruct)
{
  this.UGRD = NaN;
  this.VGRD = NaN;
  this.TWS = NaN;

  if (typeof InitStruct !== "undefined")
  {
    this.UGRD = InitStruct.UGRD;
    this.VGRD = InitStruct.VGRD;
    this.TWS = InitStruct.TWS;
  }

  this.Strength = function()
  {
    return Math.sqrt(this.UGRD * this.UGRD + this.VGRD * this.VGRD) * 3.6 / 1.852
  }

  this.Direction = function()
  {
    var t_speed  = Math.sqrt(this.UGRD * this.UGRD + this.VGRD * this.VGRD)
    dir = Math.acos(-this.VGRD / t_speed)
    
    if (this.UGRD > 0) 
    {
      dir = 2 * Math.PI - dir
    }
    dir = (dir / Math.PI * 180) % 360

    if (dir < 0) 
    {
      dir += 360;
    }
    else if (dir >= 360) 
    {
      dir -= 360;
    }
    
    return dir
  }
}

function WindData(InitStruct)
{
  this.Speed = NaN;
  this.Heading = NaN;
  this.IsValid = function()
  {
    return (!isNaN(this.Speed)) && (!isNaN(this.Heading));
  };

  if (typeof InitStruct !== "undefined")
  {
    this.Speed = InitStruct.Speed;
    this.Heading = InitStruct.Heading;
  }

}

function VLM2GribManager()
{
  this.Tables = [];
  this.TableTimeStamps = [];
  this.Inited = false;
  this.Initing = false;
  this.MinWindStamp = 0;
  this.MaxWindStamp = 0;
  this.LoadQueue = [];
  this.GribStep = 0.5;    // Grib Grid resolution

  this.Init= function()
  {
    if (this.Inited || this.Initing)
    {
      return;
    }
    this.Initing = true;
    $.get("/ws/windinfo/list.php",this.HandleGribList.bind(this));
  }

  this.HandleGribList = function(e)
  {
    this.TableTimeStamps = e.grib_timestamps;
    this.Inited = true;
    this.Initing = false;
    this.MinWindStamp = new Date(this.TableTimeStamps[0]*1000);
    this.MaxWindStamp = new Date(this.TableTimeStamps[this.TableTimeStamps-1]*1000);
    
  }

  this.WindAtPointInTime= function(Time, Lat, Lon)
  {
    if (!this.Inited)
    {
      return false;
    }

    const GribGrain = 3.*3600. ;  // 1 grib every 3 hours.
    var TableIndex = Math.floor((Time/1000. - this.MinWindStamp/1000)/(GribGrain))

    if (TableIndex < 0 )
    {
      // Before avaible grib 
      return false;
    }

    if (TableIndex + 1 >= this.TableTimeStamps.length)
    {
      // To far in the future
      return false;
    }

    // Precheck to force loading the second grib, and avoid optimization not checking 2nd when first is needs loading
    var t1 = this.CheckGribLoaded(TableIndex, Lat,Lon);
    var t2 = this.CheckGribLoaded(TableIndex+1,Lat+this.GribStep,Lon+this.GribStep);

    if (!t1 || !t2 )
    {
      return false;
    }

    // Ok, now we have the grib data in the table before and after requested time for requested position
    var MI0 = this.GetHydbridMeteoAtTimeIndex (TableIndex,Lat,Lon)
    var MI1 = this.GetHydbridMeteoAtTimeIndex (TableIndex+1,Lat,Lon)

    var  RetInfo = new WindData();

    var u0 = MI0.UGRD
    var v0 = MI0.VGRD
    var u1 = MI1.UGRD
    var v1 = MI1.VGRD

    var DteOffset = Time/1000 - this.TableTimeStamps[TableIndex];
    

    var GInfo = new GribData({
      UGRD : u0 + DteOffset / GribGrain * (u1 - u0),
      VGRD : v0 + DteOffset / GribGrain * (v1 - v0)
    })

    RetInfo.Heading = GInfo.Direction();
    RetInfo.Speed = MI0.TWS + DteOffset / GribGrain * (MI1.TWS - MI0.TWS)

    return RetInfo;
  }

  this.GetHydbridMeteoAtTimeIndex = function (TableIndex, Lat,Lon)
  {

    // Compute grid index to get the values
    var LonIdx1 = 180/this.GribStep + Math.floor(Lon / this.GribStep)
    var LatIdx1 = 90/this.GribStep + Math.floor(Lat / this.GribStep)
    var LonIdx2 = (LonIdx1 +1) % (360/this.GribStep)
    var LatIdx2 = (LatIdx1 +1) % (360/this.GribStep)
    
    var dX =  (Lon/this.GribStep - Math.floor(Lon/this.GribStep))
    var dY =  ( Lat/this.GribStep - Math.floor(Lat/this.GribStep)) 

    // Get UVS for each 4 grid points
    var U00  = this.Tables[TableIndex][LonIdx1][LatIdx1].UGRD
    var U01  = this.Tables[TableIndex][LonIdx1][LatIdx2].UGRD
    var U10  = this.Tables[TableIndex][LonIdx2][LatIdx1].UGRD
    var U11  = this.Tables[TableIndex][LonIdx2][LatIdx2].UGRD

    var V00  = this.Tables[TableIndex][LonIdx1][LatIdx1].VGRD
    var V01  = this.Tables[TableIndex][LonIdx1][LatIdx2].VGRD
    var V10  = this.Tables[TableIndex][LonIdx2][LatIdx1].VGRD
    var V11  = this.Tables[TableIndex][LonIdx2][LatIdx2].VGRD

    var S00  = this.Tables[TableIndex][LonIdx1][LatIdx1].Strength();
    var S01  = this.Tables[TableIndex][LonIdx1][LatIdx2].Strength();
    var S10  = this.Tables[TableIndex][LonIdx2][LatIdx1].Strength();
    var S11  = this.Tables[TableIndex][LonIdx2][LatIdx2].Strength();

    tws = this.QuadraticAverage(S00, S01, S10, S11, dX, dY)

    var retmeteo = new GribData ({UGRD : this.QuadraticAverage(U00, U01, U10, U11, dX, dY), 
                                VGRD : this.QuadraticAverage(V00, V01, V10, V11, dX, dY),
                                TWS : tws
                              })

   return retmeteo;
  }

  this.QuadraticAverage = function( V00 ,  V01 ,  v10 ,  V11 ,  dX ,  dY ) 
  {      
    var V0 = V00 + dY * (V01 - V00)
    var V1 = v10 + dY * (V11 - v10)
    return V0 + dX * (V1 - V0)
  }

  this.CheckGribLoaded = function(TableIndex, Lat,Lon)
  {
    if (TableIndex in this.Tables)
    {
      var LonIdx1 = 180/this.GribStep + Math.floor(Lon/this.GribStep);
      var LatIdx1 = 90/this.GribStep + Math.floor(Lat/this.GribStep);
      var LonIdx2 = 180/this.GribStep + Math.ceil(Lon/this.GribStep);
      var LatIdx2 = 90/this.GribStep + Math.ceil(Lat/this.GribStep);
      
      if (this.Tables[TableIndex][LonIdx1] && this.Tables[TableIndex][LonIdx1][LatIdx1]  && this.Tables[TableIndex][LonIdx1][LatIdx2] 
          && this.Tables[TableIndex][LonIdx2] && this.Tables[TableIndex][LonIdx2][LatIdx1] && this.Tables[TableIndex][LonIdx2][LatIdx2])
      {
        return true;
      }
    }

    //Getting there means we need to load from server
    // Get samrtgrib list for the current request position
    var RequestSize = 5;    // Assume 5° zone even though VLM request is for 15°. Most request will only return 1 zone.
    var SouthStep = Math.floor(Lat/RequestSize)*RequestSize;
    var WestStep = Math.floor(Lon/RequestSize)*RequestSize;
    var NorthStep, EastStep;
    
    if (Lat < SouthStep)
    {
      NorthStep = SouthStep
      SouthStep = NorthStep - 10
    }
    else
    {
      NorthStep = SouthStep + 10
    }
    
    if (Lat < WestStep)
    {
      EastStep = WestStep
      WestStep = EastStep - 10;
    }
    else
    {
      EastStep = WestStep + 10;
    }
    
    var LoadKey = TableIndex + "/" + WestStep + "/" + EastStep + "/" + NorthStep + "/" + SouthStep 
    if (!(LoadKey in this.LoadQueue))
    {
      this.LoadQueue[LoadKey]=0;
      $.get("/ws/windinfo/smartgribs.php?north="+NorthStep+"&south="+(SouthStep)+"&west="+(WestStep) +"&east="+(EastStep),
          this.HandleGetSmartGribList.bind(this, LoadKey));
    }

    return false;  
      
  }

  this.HandleGetSmartGribList = function (LoadKey, e)
  {
    if (e.success)
    {
      for (index in e.gribs_url)
      {
        var url = e.gribs_url[index].replace(".grb",".txt")
        $.get("/cache/gribtiles/"+url,this.HandleSmartGribData.bind(this,LoadKey, url));
        this.LoadQueue[LoadKey]++;
      }

    }
    else
    {
      console.log(e);
    }
    
  }

  this.HandleSmartGribData=function(LoadKey, Url,e)
  {
    this.ProcessInputGribData(Url,e);

    this.LoadQueue[LoadKey]--;

    if (!this.LoadQueue[LoadKey])
    {
      delete this.LoadQueue[LoadKey];
    }
  }

  this.ProcessInputGribData = function (Url, Data)
  {
    var Lines = Data.split("\n");
    var TotalLines = Lines.length
    var Catalog = [];
    var HeaderCompleted = false
    // Loop data catalog
    for (var i =0; i< TotalLines ; i++)
    {
      var Line = Lines[i];

      if (Line === "--")
      {
        break;
      }
      Catalog.push(this.ProcessCatalogLine(Line));
    }

    // Now Process the data
    var ZoneOffsets = Url.split("/");
    var DataStartIndex = Catalog.length+1
    for (var i = 0; i< Catalog.length; i++)
    {
      if (typeof Lines[DataStartIndex] === "undefined")
      {
        // Somehow sometimes, the data is incomplete, just get out, until next request.
        break;
      }
      var DataSize = Lines[DataStartIndex].split(" ");
      var NbLon = parseInt(DataSize[0],10);
      var NbLat = parseInt(DataSize[1],10);
      
      var StartLon = 180/this.GribStep+parseInt(ZoneOffsets[1],10)/this.GribStep;
    
      for (var LonIdx = 0 ; LonIdx < NbLon ; LonIdx ++)
      {
        // Offset by NbLat in grib since the zone is reference by bottom lat, but counts down from top lat
        var StartLat = NbLat +90/this.GribStep+parseInt(ZoneOffsets[0],10)/this.GribStep;
    
        for ( var LatIdx = 0 ; LatIdx < NbLat ; LatIdx ++)
        {
          if ( !(Catalog[i].DateIndex in this.Tables))
          {
            this.Tables[Catalog[i].DateIndex]=[];
          }

          var CurTable = this.Tables[Catalog[i].DateIndex]

          if ( ! (StartLon+LonIdx in CurTable ))
          {
            CurTable[StartLon+LonIdx]=[];
          }
          if ( ! ((StartLat-LatIdx-1) in CurTable[StartLon+LonIdx] ))
          {
            CurTable[StartLon+LonIdx][StartLat-LatIdx-1]=null;
          }
          
          var GribPoint = this.Tables[Catalog[i].DateIndex][StartLon+LonIdx][StartLat-LatIdx-1]

          if (typeof GribPoint === "undefined" || ! GribPoint)
          {
            GribPoint = new GribData();
            this.Tables[Catalog[i].DateIndex][StartLon+LonIdx][StartLat-LatIdx-1] = GribPoint;
          }
          
          GribPoint[Catalog[i].Type] = parseFloat(Lines[DataStartIndex+1+LatIdx*NbLon+LonIdx]);
        }
      }

      DataStartIndex += NbLon*NbLat+1;
    }

  }

  this.ProcessCatalogLine = function(Line)
  {
    const POS_TYPE = 3;
    const POS_INDEX = 12;
    var Ret = new WindCatalogLine();
    var Fields = Line.split(":");

    Ret.Type=Fields[POS_TYPE];
    if (Fields[POS_INDEX]==="anl")
    {
      Ret.DateIndex = 0;
    }
    else
    {
      Ret.DateIndex =parseInt( Fields[POS_INDEX].substring(0,Fields[POS_INDEX].indexOf("hr")),10)/3;
    }

    return Ret;
  }
}

function WindCatalogLine()
{
  this.Type="";
  this.DateIndex = 0;
}

function WindTable()
{
  this.GribStep = 0.5;
  this.Table= [];
  this.TableDate = 0;

  this.Init = function(TableDate)
  {
    for (lat = -90; lat <= 90; lat+=this.GribStep)
    {
      for (lon = -90; lon <= 90; lon+=this.GribStep)
      {
        this.Table[lat][lon]=null;
      }
    }
  }
}

function HandleGribTestClick(e)
{
  var Boat = _CurPlayer.CurBoat;

  for (var index = -1; index<=3 ; index++)
  {
    var time = new Date(Boat.VLMInfo.LUP*1000+index * Boat.VLMInfo.VAC*1000)
    var Mi = GribMgr.WindAtPointInTime(time,Boat.VLMInfo.LAT,Boat.VLMInfo.LON);
    console.log(time + " " +Mi.Speed + "@"+ Mi.Heading);
  }
}