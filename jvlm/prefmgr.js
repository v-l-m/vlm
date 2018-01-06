
var MAP_OP_SHOW_SEL = 0;


var VLM2Prefs = new PrefMgr()

VLM2Prefs.Init();

function PrefMgr()
{
  this.MapPrefs=new MapPrefs();
  this.CurTheme = "bleu-noir"
  this.ShowTopCount = 50;

  this.MapPrefs
  this.Init = function()
  {
    this.MapPrefs.Load();
    this.Load();
  } 

  this.LoadLocalPref = function (PrefName, PrefDfaultValue)
  {
      
  }

  this.Load = function()
  {
    if (store.enabled)
    {
      this.CurTheme = store.get('ColorTheme');
      this.CurTheme = store.get('ShowTopCount');
      if (typeof this.CurTheme === "undefined")
      {
          this.CurTheme = "bleu-noir"
      }
    }
  }

  this.Save = function()
  {
    if (store.enabled)
    {
      store.set('ColorTheme',this.CurTheme);
    }

    this.MapPrefs.Save();
  }

  this.UpdateVLMPrefs = function(p)
  {
    switch (p.mapOpponents)
    {
      case "mylist":
      case "mapselboats":
      case "NULL":
      case "null":
      case "all":
        this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.ShowSel;
        break;

      case "meandtop10":
        this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.ShowTop10;
        break;

      case "my10opps":
        this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.Show10Around;
        break;

      case "my5opps":
        this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.Show5Around;
        break;

      case "maponlyme":
        this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.Show5Around;
        break;

      case "myboat":
        this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.ShowMineOnly;
        break;

      default:
        VLMAlertDanger ("unexepected mapping option : " + p.mapOpponents)
    }
  }
  
}

function MapPrefs()
{
  this.ShowReals=true;            // Do we show reals?
  this.ShowOppName=true;          // Do we show opponents names?
  this.MapOppShow = null;         // Which opponents do we show on the map
  this.MapOppShowOptions = {
      ShowSel : 0,
      ShowMineOnly : 1,
      Show5Around : 2,
      ShowTop10 : 3,
      Show10Around : 4
  }
  this.WindArrowsSpacing = 64;    // Spacing steps for wind arrow drawing
  this.MapZoomLevel = 4;
  this.PolarVacCount = 12;        // How many vacs for drawing the polar line
  this.EstTrackMouse = false;
  this.TrackEstForecast = true;

  this.Load = function()
  {
    if (store.enabled)
    {
      this.ShowReals = store.get('#ShowReals');
      this.ShowOppName = store.get("#ShowOppName");
      this.MapZoomLevel = store.get("#MapZoomLevel");
      this.PolarVacCount = store.get("#PolarVacCount");
      this.EstTrackMouse = store.get("#EstTrackMouse");
      this.TrackEstForecast = store.get("#TrackEstForecast");
      if (typeof this.PolarVacCount === "undefined" || !this.PolarVacCount)
      {
          // Fallback if invalid value is stored
          this.PolarVacCount = 12;
      }
    } 
  }

  this.Save = function()
  {
    if(store.enabled)
    {
      store.set("#ShowReals",this.ShowReals);
      store.set("#ShowOppName",this.ShowOppName);   
      store.set("#MapZoomLevel",this.MapZoomLevel); 
      store.set("#PolarVacCount",this.PolarVacCount);
      store.set("#TrackEstForecast",this.TrackEstForecast); 
      store.set("#EstTrackMouse",this.EstTrackMouse); 
                
    }

    var MapPrefVal="mapselboats"
    switch(this.MapOppShow)
    {
      case this.MapOppShowOptions.ShowMineOnly:
        MapPrefVal="myboat"
        break;

      case this.MapOppShowOptions.Show5Around:
        MapPrefVal="my5opps";
        break;
          
      case this.MapOppShowOptions.ShowTop10:
        MapPrefVal="meandtop10";
        break;
          
      case this.MapOppShowOptions.Show10Around:
        MapPrefVal="my10opps";
        break;
            
        
    }

    var NewVals={mapOpponents:MapPrefVal};
    if (typeof _CurPlayer !== "undefined")
    {
      UpdateBoatPrefs(_CurPlayer.CurBoat,{prefs:NewVals});
    }
  }

  this.GetOppModeString = function (Mode)
  {
    switch (Mode)
    {
      case this.MapOppShowOptions.ShowSel:
        return GetLocalizedString("mapselboats");

      case this.MapOppShowOptions.ShowMineOnly:
        return GetLocalizedString("maponlyme")
          
      case this.MapOppShowOptions.Show5Around:
        return GetLocalizedString("mapmy5opps")
          
      case this.MapOppShowOptions.ShowTop10:
        return GetLocalizedString("mapmeandtop10")
          
      case this.MapOppShowOptions.Show10Around:
        return GetLocalizedString("mapmy10opps")
          
      default:
        return Mode;
    }
  }

}