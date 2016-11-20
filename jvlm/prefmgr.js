
var MAP_OP_SHOW_SEL = 0;


var VLM2Prefs = new PrefMgr()

VLM2Prefs.Init();

function PrefMgr()
{
    this.MapPrefs=new MapPrefs();

    this.MapPrefs
    this.Init = function()
    {
        this.MapPrefs.Load();
    } 

    this.Save = function()
    {
        this.MapPrefs.Save();
    }

    this.UpdateVLMPrefs = function(p)
    {
        switch (p.mapOpponents)
        {
            case "mylist":
            case "NULL":
            case "null":
                this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.ShowSel;
                break;

            case "meandtop10":
                this.MapPrefs.MapOppShow = this.MapPrefs.MapOppShowOptions.ShowTop10;
                break;

            default:
                alert ("unexepected mapping option : " + p.mapOpponents)
        }
    }
    
}

function MapPrefs()
{
    this.ShowReals=true;
    this.ShowOppName=true;
    this.MapOppShow = null;
    this.MapOppShowOptions = {
        ShowSel : 0,
        ShowMineOnly : 1,
        Show5Around : 2,
        ShowTop10 : 3,
        Show10Around : 4
    }

    this.Load = function()
    {
        if (store.enabled)
        {
            this.ShowReals = store.get('#ShowReals');
            this.ShowOppName = store.get("#ShowOppName");
        } 
    }

    this.Save = function()
    {
        if(store.enabled)
        {
            store.set("#ShowReals",this.ShowReals);
            store.set("#ShowOppName",this.ShowOppName);            
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