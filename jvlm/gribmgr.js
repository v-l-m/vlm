// Create and init a manager

// Beaufort Scale
var BeaufortScale = [1, 4, 7, 11, 17, 22, 28, 34, 41, 48, 56, 64];
var MaxBeaufort = 12;
class GribData
{
  constructor(InitStruct)
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
      return Math.sqrt(this.UGRD * this.UGRD + this.VGRD * this.VGRD) * 1.9438445; //* 3.6 / 1.852
    };
    this.Direction = function()
    {
      let t_speed = Math.sqrt(this.UGRD * this.UGRD + this.VGRD * this.VGRD);
      let dir = Math.acos(-this.VGRD / t_speed);
      if (this.UGRD > 0)
      {
        dir = 2 * Math.PI - dir;
      }
      dir = (dir / Math.PI * 180) % 360;
      if (dir < 0)
      {
        dir += 360;
      }
      else if (dir >= 360)
      {
        dir -= 360;
      }
      return dir;
    };
  }
}

class WindData
{
  constructor(InitStruct)
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
}

class VLM2GribManager
{
  constructor()
  {
    this.Tables = [];
    this.TableTimeStamps = [];
    this.Inited = false;
    this.Initing = false;
    this.MinWindStamp = 0;
    this.MaxWindStamp = 0;
    this.WindTableLength = 0;
    this.LoadQueue = [];
    this.GribStep = 0.5; // Grib Grid resolution
    this.LastGribDate = new Date(0);
    this.BeaufortCache = [];
    this.Init = function()
    {
      if (this.Inited || this.Initing)
      {
        return;
      }
      this.Initing = true;
      $.get("/ws/windinfo/list.php?v=" + Math.round(new Date().getTime() / 1000 / 60 / 3), this.HandleGribList.bind(this));
    };

    //TODO Put Back the unreadable dichytomy selection 
    this.GetBeaufort = function(wspeed, min, max)
    {
      if (!min && !max)
      {
        wspeed = Math.floor(wspeed);
        if (this.BeaufortCache[wspeed])
        {
          return this.BeaufortCache[wspeed];
        }
        let MidBeaufort = Math.floor(MaxBeaufort / 2);
        if (wspeed < BeaufortScale[MidBeaufort])
        {
          return this.GetBeaufort(wspeed, 0, MidBeaufort);
        }
        else
        {
          return this.GetBeaufort(wspeed, MidBeaufort, MaxBeaufort);
        }
      }
      else
      {
        //debugger;
        let Mid = Math.floor((max + min) / 2);
        if (wspeed === BeaufortScale[Mid])
        {
          this.BeaufortCache[wspeed] = Mid;
          return BeaufortScale[Md];
        }

        else if (wspeed < BeaufortScale[Mid])
        {
          if (Mid !== min)
          {
            return this.GetBeaufort(wspeed, min, Mid);
          }
          else
          {
            this.BeaufortCache[wspeed] = mid;
            return BeaufortScale[mid];
          }
        }
        else
        {
          if (Mid + 1 < max)
          {
            return this.GetBeaufort(wspeed, Mid, max);
          }
          else
          {
            this.BeaufortCache[wspeed] = max;
            return BeaufortScale[max];
          }
        }
      }

    };
    this.HandleGribList = function(e)
    {
      this.TableTimeStamps = e.grib_timestamps;
      this.Inited = true;
      this.Initing = false;
      this.MinWindStamp = new Date(this.TableTimeStamps[0] * 1000);
      this.MaxWindStamp = new Date(this.TableTimeStamps[this.TableTimeStamps.length - 1] * 1000);
      this.WindTableLength = this.TableTimeStamps.length;
    };
    this.WindAtPointInTime = function(Time, Lat, Lon, callback)
    {
      if (!this.Inited)
      {
        return false;
      }
      const GribGrain = 3.0 * 3600.0; // 1 grib every 3 hours.
      var TableIndex = Math.floor((Time / 1000.0 - this.MinWindStamp / 1000) / (GribGrain));
      if (TableIndex < 0)
      {
        // Before avaible grib 
        return false;
      }
      if (TableIndex + 1 >= this.TableTimeStamps.length)
      {
        // To far in the future
        return false;
      }
      let RetInfo = new WindData();
      if (Math.abs(Lat) > 85)
      {
        RetInfo.Heading = 0;
        RetInfo.Speed = 0;
        return RetInfo;
      }
      // Precheck to force loading the second grib, and avoid optimization not checking 2nd when first is needs loading
      let t1 = this.CheckGribLoaded(TableIndex, Lat, NormalizeLongitudeDeg(Lon), callback);
      let t2 = this.CheckGribLoaded(TableIndex + 1, Lat + this.GribStep, NormalizeLongitudeDeg(Lon + this.GribStep), callback);
      if (t1 && !t2)
      {
        //alert("anomaly at "+Lat+this.GribStep+ "/" + NormalizeLongitudeDeg(Lon+this.GribStep))
        t2 = this.CheckGribLoaded(TableIndex + 1, Lat + this.GribStep, NormalizeLongitudeDeg(Lon + this.GribStep), callback);
      }
      if (!t1 || !t2)
      {
        return false;
      }
      // Ok, now we have the grib data in the table before and after requested time for requested position
      var MI0 = this.GetHydbridMeteoAtTimeIndex(TableIndex, Lat, Lon);
      var MI1 = this.GetHydbridMeteoAtTimeIndex(TableIndex + 1, Lat, Lon);
      var u0 = MI0.UGRD;
      var v0 = MI0.VGRD;
      var u1 = MI1.UGRD;
      var v1 = MI1.VGRD;
      var DteOffset = Time / 1000 - this.TableTimeStamps[TableIndex];
      var GInfo = new GribData(
      {
        UGRD: u0 + DteOffset / GribGrain * (u1 - u0),
        VGRD: v0 + DteOffset / GribGrain * (v1 - v0)
      });
      RetInfo.Heading = GInfo.Direction();
      RetInfo.Speed = MI0.TWS + DteOffset / GribGrain * (MI1.TWS - MI0.TWS);
      return RetInfo;
    };
    this.GetHydbridMeteoAtTimeIndex = function(TableIndex, Lat, Lon)
    {
      // Normalize coords
      let Lon_pos = Lon;
      while (Lon_pos < 0)
      {
        Lon_pos += 360;
      }
      while (Lon_pos > 360)
      {
        Lon_pos -= 360;
      }
      let Lat_pos = Lat;
      while (Lat_pos < 0)
      {
        Lat_pos += 90;
      }
      while (Lat_pos > 90)
      {
        Lat_pos -= 90;
      }
      // Compute grid index to get the values
      let LonIdx1 = 180 / this.GribStep + Math.floor(Lon / this.GribStep);
      let LatIdx1 = 90 / this.GribStep + Math.floor(Lat / this.GribStep);
      let LonIdx2 = (LonIdx1 + 1) % (360 / this.GribStep);
      let LatIdx2 = (LatIdx1 + 1) % (180 / this.GribStep);
      let dX = (Lon_pos / this.GribStep - Math.floor(Lon_pos / this.GribStep));
      let dY = (Lat_pos / this.GribStep - Math.floor(Lat_pos / this.GribStep));
      // Get UVS for each 4 grid points
      let U00 = this.Tables[TableIndex][LonIdx1][LatIdx1].UGRD;
      let U01 = this.Tables[TableIndex][LonIdx1][LatIdx2].UGRD;
      let U10 = this.Tables[TableIndex][LonIdx2][LatIdx1].UGRD;
      let U11 = this.Tables[TableIndex][LonIdx2][LatIdx2].UGRD;
      let V00 = this.Tables[TableIndex][LonIdx1][LatIdx1].VGRD;
      let V01 = this.Tables[TableIndex][LonIdx1][LatIdx2].VGRD;
      let V10 = this.Tables[TableIndex][LonIdx2][LatIdx1].VGRD;
      let V11 = this.Tables[TableIndex][LonIdx2][LatIdx2].VGRD;
      let S00 = this.Tables[TableIndex][LonIdx1][LatIdx1].Strength();
      let S01 = this.Tables[TableIndex][LonIdx1][LatIdx2].Strength();
      let S10 = this.Tables[TableIndex][LonIdx2][LatIdx1].Strength();
      let S11 = this.Tables[TableIndex][LonIdx2][LatIdx2].Strength();
      let tws = this.QuadraticAverage(S00, S01, S10, S11, dX, dY);
      let retmeteo = new GribData(
      {
        UGRD: this.QuadraticAverage(U00, U01, U10, U11, dX, dY),
        VGRD: this.QuadraticAverage(V00, V01, V10, V11, dX, dY),
        TWS: tws
      });
      return retmeteo;
    };
    this.QuadraticAverage = function(V00, V01, v10, V11, dX, dY)
    {
      var V0 = V00 + dY * (V01 - V00);
      var V1 = v10 + dY * (V11 - v10);
      return V0 + dX * (V1 - V0);
    };
    this.CheckGribLoaded = function(TableIndex, Lat, Lon, callback)
    {
      var LonIdx1 = 180 / this.GribStep + Math.floor(Lon / this.GribStep);
      var LatIdx1 = 90 / this.GribStep + Math.floor(Lat / this.GribStep);
      var LonIdx2 = 180 / this.GribStep + Math.ceil(Lon / this.GribStep);
      var LatIdx2 = 90 / this.GribStep + Math.ceil(Lat / this.GribStep);
      if (TableIndex in this.Tables)
      {
        if (this.Tables[TableIndex][LonIdx1] && this.Tables[TableIndex][LonIdx1][LatIdx1] && this.Tables[TableIndex][LonIdx1][LatIdx2] &&
          this.Tables[TableIndex][LonIdx2] && this.Tables[TableIndex][LonIdx2][LatIdx1] && this.Tables[TableIndex][LonIdx2][LatIdx2])
        {
          return true;
        }
      }
      //console.log("need "+Lat+" " +Lon);
      this.CheckGribLoadedIdx(TableIndex, LonIdx1, LatIdx1, callback);
      this.CheckGribLoadedIdx(TableIndex, LonIdx1, LatIdx2, callback);
      this.CheckGribLoadedIdx(TableIndex, LonIdx2, LatIdx1, callback);
      this.CheckGribLoadedIdx(TableIndex, LonIdx2, LatIdx2, callback);
      return false;
    };
    this.CheckGribLoadedIdx = function(TableIndex, LonIdx, LatIdx, callback)
    {
      if (isNaN(LonIdx) || isNaN(LatIdx))
      {
        var dbgpt = 0;
      }
      if (this.Tables.length && this.Tables[TableIndex] && this.Tables[TableIndex][LonIdx] && this.Tables[TableIndex][LonIdx][LatIdx])
      {
        return;
      }
      //Getting there means we need to load from server
      // Get samrtgrib list for the current request position
      var RequestSize = 5; // Assume 5° zone even though VLM request is for 15°. Most request will only return 1 zone.
      var Lat = (LatIdx * this.GribStep - 90);
      var Lon = (LonIdx * this.GribStep - 180);
      var SouthStep = Math.floor(Lat / RequestSize) * RequestSize;
      var WestStep = Math.floor(Lon / RequestSize) * RequestSize;
      var NorthStep, EastStep;
      if (Lat < SouthStep)
      {
        NorthStep = SouthStep;
        SouthStep = NorthStep - 2 * RequestSize;
      }
      else
      {
        NorthStep = SouthStep + 2 * RequestSize;
      }
      if (Lon < WestStep)
      {
        EastStep = WestStep;
        WestStep = EastStep - 2 * RequestSize;
      }
      else
      {
        EastStep = WestStep + 2 * RequestSize;
      }
      if (EastStep > 180)
      {
        EastStep = 180;
        this.CheckGribLoadedIdx(TableIndex, 0, LatIdx, callback);
      }
      if (WestStep < -180)
      {
        WestStep = -180;
        this.CheckGribLoadedIdx(TableIndex, 180 / this.GribStep - 1, LatIdx, callback);
      }
      let LoadKey = "0/" + WestStep + "/" + EastStep + "/" + NorthStep + "/" + SouthStep;
      this.AddGribLoadKey(LoadKey, NorthStep, SouthStep, WestStep, EastStep, callback);
    };
    this.AddGribLoadKey = function(LoadKey, NorthStep, SouthStep, WestStep, EastStep, callback)
    {
      if (!(LoadKey in this.LoadQueue))
      {
        //console.log("requesting " + LoadKey );
        this.LoadQueue[LoadKey] = {
          length: 0,
          CallBacks: [callback]
        };
        $.get(GribMap.ServerURL() + "/ws/windinfo/smartgribs.php?north=" + NorthStep + "&south=" + (SouthStep) + "&west=" + (WestStep) + "&east=" + (EastStep) + "&seed=" + (0 + new Date()), this.HandleGetSmartGribList.bind(this, LoadKey));
      }
      else if (typeof callback !== "undefined" && callback)
      {
        this.LoadQueue[LoadKey].CallBacks.push(callback);
        //console.log("Adding to callback load queue "+ LoadKey + ":"+this.LoadQueue[LoadKey].CallBacks.length);
      }
    };
    this.HandleGetSmartGribList = function(LoadKey, e)
    {
      if (e.success)
      {
        // Handle grib change
        if (this.LastGribDate !== parseInt(e.GribCacheIndex, 10))
        {
          // Grib changed, record, and clear Tables, force reinit
          this.LastGribDate = parseInt(e.GribCacheIndex, 10);
          this.Tables = [];
          this.Inited = false;
          this.Init();
        }
        for (let index in e.gribs_url)
        {
          if (e.gribs_url[index])
          {
            let url = e.gribs_url[index].replace(".grb", ".txt");
            let seed = 0; //parseInt((new Date).getTime());
            //console.log("smartgrib points out " + url);
            $.get("/cache/gribtiles/" + url + "&v=" + seed, this.HandleSmartGribData.bind(this, LoadKey, url));
          }
        }
      }
      else
      {
        console.log(e);
      }
    };
    this.HandleSmartGribData = function(LoadKey, Url, e)
    {
      let DataOK = this.ProcessInputGribData(Url, e, LoadKey);
      //this.LoadQueue[LoadKey].Length--;
      if (DataOK && this.LoadQueue[LoadKey])
      {
        // Successfull load of one item from the loadqueue
        // Clear all pending callbacks for this call
        for (let index in this.LoadQueue[LoadKey].CallBacks)
        {
          if (this.LoadQueue[LoadKey].CallBacks[index])
          {
            this.LoadQueue[LoadKey].CallBacks[index]();
          }
        }
        delete this.LoadQueue[LoadKey];
      }
    };
    this.ForceReloadGribCache = function(LoadKey, Url)
    {
      var Seed = 0; //parseInt(new Date().getTime(),10);
      $.get("/cache/gribtiles/" + Url + "&force=yes&seed=" + Seed, this.HandleSmartGribData.bind(this, LoadKey, Url));
    };
    this.ProcessInputGribData = function(Url, Data, LoadKey)
    {
      let Lines = Data.split("\n");
      let TotalLines = Lines.length;
      let Catalog = [];
      let HeaderCompleted = false;
      let DataStartIndex = 0;
      // Handle cache mess
      if (Data === "--\n")
      {
        /*var Parms = Url.split("/")
        this.LoadQueue[LoadKey]++;
        if (Parms[2] != 15)
        {
          var i = 0;
        }
        //$.get("/gribtiles.php?south="+ Parms[0]+"&west="+Parms[1]+"&step="+ Parms[2]+"&fmt=txt",this.HandleSmartGribData .bind(this,LoadKey, Url));
        */
        this.ForceReloadGribCache(LoadKey, Url);
        return false;
      }
      else if (Data.search("invalid") !== -1)
      {
        console.log("invalid request :" + Url);
        return false;
      }
      // Loop data catalog
      for (let i = 0; i < TotalLines; i++)
      {
        var Line = Lines[i];
        if (Line === "--")
        {
          DataStartIndex = i + 1;
          break;
        }
        // Filter out GRID lines
        if (Line && Line.search("GRID:") === -1)
        {
          Catalog.push(this.ProcessCatalogLine(Line));
        }
      }
      if (Catalog.length < this.WindTableLength)
      {
        // Force reloading, it table is shorter than windlist
        this.ForceReloadGribCache(LoadKey, Url);
        return;
      }
      // Now Process the data
      var ZoneOffsets = Url.split("/");
      for (let i = 0; i < Catalog.length; i++)
      {
        if (typeof Lines[DataStartIndex] === "undefined" || Lines[DataStartIndex] === "")
        {
          // Somehow sometimes, the data is incomplete, just get out, until next request.
          //console.log("Incomplete data file. Forcing rebuild..." + Url);
          this.ForceReloadGribCache(LoadKey, Url);
          break;
        }
        var DataSize = Lines[DataStartIndex].split(" ");
        var NbLon = parseInt(DataSize[0], 10);
        var NbLat = parseInt(DataSize[1], 10);
        var StartLon = 180 / this.GribStep + parseInt(ZoneOffsets[1], 10) / this.GribStep;
        for (var LonIdx = 0; LonIdx < NbLon; LonIdx++)
        {
          // Offset by NbLat in grib since the zone is reference by bottom lat, but counts down from top lat
          var StartLat = NbLat + 90 / this.GribStep + parseInt(ZoneOffsets[0], 10) / this.GribStep;
          for (var LatIdx = 0; LatIdx < NbLat; LatIdx++)
          {
            if (!(Catalog[i].DateIndex in this.Tables))
            {
              this.Tables[Catalog[i].DateIndex] = [];
            }
            var CurTable = this.Tables[Catalog[i].DateIndex];
            if (!(StartLon + LonIdx in CurTable))
            {
              CurTable[StartLon + LonIdx] = [];
            }
            if (!((StartLat - LatIdx - 1) in CurTable[StartLon + LonIdx]))
            {
              CurTable[StartLon + LonIdx][StartLat - LatIdx - 1] = null;
            }
            var GribPoint = this.Tables[Catalog[i].DateIndex][StartLon + LonIdx][StartLat - LatIdx - 1];
            if (typeof GribPoint === "undefined" || !GribPoint)
            {
              GribPoint = new GribData();
              this.Tables[Catalog[i].DateIndex][StartLon + LonIdx][StartLat - LatIdx - 1] = GribPoint;
            }
            GribPoint[Catalog[i].Type] = parseFloat(Lines[DataStartIndex + 1 + LatIdx * NbLon + LonIdx]);
          }
        }
        /*console.log("Loaded table "+ Catalog[i].DateIndex);
        console.log("Loaded lon index  "+ StartLon + "->" + (StartLon+NbLon));
        console.log("Loaded lat index  "+ (StartLat-1) + "->" + (StartLat-NbLat-1));
        */
        DataStartIndex += NbLon * NbLat + 1;
      }
      return true;
    };
    this.ProcessCatalogLine = function(Line)
    {
      const POS_TYPE = 3;
      const POS_INDEX = 12;
      var Ret = new WindCatalogLine();
      var Fields = Line.split(":");
      Ret.Type = Fields[POS_TYPE];
      if ((typeof Fields[POS_INDEX] === "undefined") || (Fields[POS_INDEX] === "anl"))
      {
        Ret.DateIndex = 0;
      }
      else
      {
        Ret.DateIndex = parseInt(Fields[POS_INDEX].substring(0, Fields[POS_INDEX].indexOf("hr")), 10) / 3;
      }
      return Ret;
    };
  }
}

class WindCatalogLine
{
  constructor()
  {
    this.Type = "";
    this.DateIndex = 0;
  }
}

class WindTable
{
  constructor()
  {
    this.GribStep = 0.5;
    this.Table = [];
    this.TableDate = 0;
    this.Init = function(TableDate)
    {
      for (lat = -90; lat <= 90; lat += this.GribStep)
      {
        for (lon = -90; lon <= 90; lon += this.GribStep)
        {
          this.Table[lat][lon] = null;
        }
      }
    };
  }
}

var GribMgr = new VLM2GribManager();

GribMgr.Init();


function HandleGribTestClick(e)
{
  var Boat = _CurPlayer.CurBoat;

  for (var index = 0; index <= 0; index++)
  {
    var time = new Date(Boat.VLMInfo.LUP * 1000 + index * Boat.VLMInfo.VAC * 1000);
    var Mi = GribMgr.WindAtPointInTime(time, Boat.VLMInfo.LAT, Boat.VLMInfo.LON);

    if (Mi)
    {
      console.log(time + " " + Mi.Speed + "@" + Mi.Heading);
    }
    else
    {
      console.log("no meteo yet at time : " + time);
    }
  }
}