// Class to persist race infos
class Race
{
  constructor(RaceId)
  {
    if (typeof RaceId == "number")
    {
      this.RaceId = RaceId;
    }
    else
    {
      this.RaceId = parseInt(RaceId, 10);
    }

    this.LastUpdate = new Date(0);

    // Clear data from preferences
    this.ClearData = function()
    {
      VLM2Prefs.ClearRaceData(this.RaceId);
    };

    // Returns true if race has been updated since last call
    // Stores the race update date in the local storage as well
    // Returns false if race updates have happened
    this.CheckRaceUpdates = function(RaceInfo)
    {
      let RetOK = true;

      if (RaceInfo)
      {
        let RaceId = RaceInfo.idraces;
        let UpdateDate = new Date(parseInt(RaceInfo.VER, 10) * 1000);

        let RI = VLM2Prefs.GetRaceFromStorage(RaceId);
        RI.LastUpdate = new Date(RI.LastUpdate);

        if (RI.LastUpdate < UpdateDate)
        {
          if (VLM2Prefs)
          {
            RI.LastUpdate = UpdateDate;
            VLM2Prefs.Save();
            RetOK = false;
          }
        }
      }
      return RetOK;
    };

    this.HasSave = function()
    {
      return VLM2Prefs.HasRaceStorage(this.RaceId);      
    };

    this.UpdatedForRaceStart = function(RaceInfo)
    {
      let RetOk = false;

      if (RaceInfo)
      {
        RetOK = (new Date(RaceInfo.deptime * 1000) == new Date(RaceInfo.VER * 1000));
      }

      return RetOk;
    };

    this.Subscribe = function(BoatId)
    {
      $.post("/ws/boatsetup/race_subscribe.php",
        "parms=" + JSON.stringify(
        {
          idu: BoatId,
          idr: this.RaceId
        }),
        function(data)
        {

          if (data.success)
          {

            $("#RacesListForm").modal("hide");
            let Not = new RaceNewsHandler("", GetLocalizedString("youengaged"));
            StatMGR.Stat("RaceSubscribe", RaceId);
            Not.Show();
            let RI = VLM2Prefs.GetRaceFromStorage(RaceId);
            RI.LastUpdate = new Date();
            VLM2Prefs.Save();
          }
          else
          {
            let Msg = data.error.msg + '\n' + data.error.custom_error_string;
            VLMAlertDanger(Msg);
          }
        }
      );
    };
  }
}