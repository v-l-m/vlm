// Class to persist race infos
class Race
{
  constructor(RaceId)
  {
    this.RaceId=RaceId;
    this.LastUpdate = new Date(0);
  }
}


// Returns true if race has been updated since last call
// Stores the race update date in the local storage as well
// Returns false if race updates have happened
function CheckRaceUpdates(RaceInfo)
{
  let RetOK = true;

  if (RaceInfo)
  {
    let RaceId = RaceInfo.idraces;
    let UpdateDate = new Date(parseInt(RaceInfo.VER, 10)*1000);

    let RI = VLM2Prefs.GetRaceFromStorage(RaceId);
    RI.LastUpdate = new Date(RI.LastUpdate);
    
    if ( RI.LastUpdate < UpdateDate)
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
}

