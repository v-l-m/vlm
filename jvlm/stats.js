class StatsManager
{
  constructor()
  {
    this.NoStat = true;

    this.CheckGConsent = function()
    {
      if (VLM2Prefs && VLM2Prefs.GConsentDate)
      {
        this.NoStat = !isNaN(VLM2Prefs.GConsentDate);
      }
      else if (VLM2Prefs)
      {
        let CurDate = new Date().getTime();
        let LastNo = new Date(VLM2Prefs.GConsentLastNo);

        if (isNaN(LastNo) || LastNo.getTime() + 6 * 30 * 24 * 3600000 < CurDate)
        {
          $("#GConsentToggle").on("click", this.HandleGconsentToggle.bind(this));
          this.SetConsent(false);
          $("#GConsentModal").modal('show');
        }
      }

      return;
    };

    this.HandleGconsentToggle = function(e)
    {
      let Btn = $("#GConsentToggle");

      if (Btn.hasClass("btn-danger"))
      {
        Btn.removeClass("btn-danger").addClass("btn-success").html(GetLocalizedString("Yes"));
        this.SetConsent(true);
      }
      else
      {
        Btn.addClass("btn-danger").removeClass("btn-success").html(GetLocalizedString("No"));
        this.SetConsent(false);
      }
    };

    this.SetConsent = function(status)
    {
      this.NoStat = status;
      if (status)
      {
        VLM2Prefs.GConsentDate = new Date();
        VLM2Prefs.GConsentLastNo = null;
      }
      else
      {
        VLM2Prefs.GConsentLastNo = new Date();
        VLM2Prefs.GConsentDate = null;
      }
      VLM2Prefs.Save();
    };

    this.Stat = function(Evt, EvtCategory, EvtLabel,  EvtValue)
    {
      if (!this.NoStat && typeof(gtag) !== "undefined" && gtag)
      {
        if (typeof (EvtCategory) === "undefined")
        {
          EvtCategory = " ";
        }

        if (typeof(EvtValue) === "number")
        {
          gtag('event', Evt,
          {
            'event_category': EvtCategory,
            'event_label': EvtLabel,
            'value': EvtValue
          });
        }
        else
        {
          gtag('event', Evt,
          {
            'event_category': EvtCategory,
            'event_label': EvtLabel
          });
        }
      }
    };
  }
}

var StatMGR = new StatsManager();