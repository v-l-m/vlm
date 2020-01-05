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
        let LastNo = VLM2Prefs.GConsentLastNo;

        if (LastNo)
        {
          LastNo  = new Date(LastNo);
        }

        if (VLM2Prefs.GConsentDate===null && (VLM2Prefs.GConsentLastNo===null || LastNo.getTime() + 6 * 30 * 24 * 3600000 < CurDate))
        {
          $(".GConsentToggle").on("click", this.HandleGconsentToggle.bind(this));
          //this.SetConsent(false);
          $("#GConsentModal").modal(
          {
            backdrop: 'static',
            keyboard: false
          });
        }
      }

      return;
    };

    this.HandleGconsentToggle = function(e)
    {
      let BtnId = e.currentTarget.id;
      let Btn = $("#"+ BtnId);

      if (BtnId === "GConsentToggleNo")
      {
        $("#GConsentToggleNo").addClass("btn-danger").removeClass("btn-default");
        $("#GConsentToggleYes").removeClass("btn-success").addClass("btn-default");
        this.SetConsent(false);
      }
      else
      {
        $("#GConsentToggleNo").removeClass("btn-danger").addClass("btn-default");
        $("#GConsentToggleYes").addClass("btn-success").removeClass("btn-default");
        this.SetConsent(true);
      }
      $("#GConsentCloseFormBtn").removeClass("ui-state-disabled");
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

    this.Stat = function(Evt, EvtCategory, EvtLabel, EvtValue)
    {
      if (!this.NoStat && typeof(gtag) !== "undefined" && gtag)
      {
        if (typeof(EvtCategory) === "undefined" || !EvtCategory)
        {
          EvtCategory = Evt;
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