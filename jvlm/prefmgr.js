
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
    
}

function MapPrefs()
{
    this.ShowReals=true;
    this.ShowOppName=true;

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

}