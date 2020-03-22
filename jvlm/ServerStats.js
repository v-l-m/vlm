class ServerStatsMgrClass
{
  constructor()
  {
    this.TemplateDom = $("#StatIndicatorTemplate");
    $(".StatIndicatorTile").on('click', this.HandleTileClick.bind(this));
    this.PlotVisible = false;
    this.Colors = {
      "Stat_MailQStat":
      {
        Threshold: [0, 10, 25],
        Colors: ["lime", "orange", "red"],
        Unit:"msg"
      },
      "Stat_VolumeSpace":
      {
        Threshold: [0, 90, 95],
        Colors: ["lime", "orange", "red"],
        Unit:"%"
      },
      "Stat_MySQLStats":
      {
        Threshold: [0, 500, 700],
        Colors: ["lime", "orange", "red"],
        Unit:null
      },
      "Stat_EngineStats":
      {
        Threshold: [0, 100, 200],
        Colors: ["red", "orange", "lime"],
        Unit:"Boats/s"
      },
      "BoatCount":
      {
        Threshold: [0],
        Colors: ["lime"],
        Unit:null
      },
      "RaceCount":
      {
        Threshold: [0],
        Colors: ["lime"],
        Unit:null
      },
      "max_connections":
      {
        Threshold: [0],
        Colors: ["blue"]
      },
    };
  }

  LoadStats()
  {
    $("#StatsPreloader").removeClass("hidden");
    $.get("/ws/serverinfo/ServerStatus.php?v=" + Math.round(new Date().getTime() / 1000 / 60 / 3), this.HandleStatLoaded.bind(this));
  }

  HandleStatLoaded(e)
  {
    this.Stats = e;
    this.DisplayCurrentValues();
    $("#StatsPreloader").addClass("hidden");
  }

  DisplayCurrentValues()
  {
    for (let index in this.Stats)
    {
      if (this.Stats[index] && this.Stats[index].Names)
      {
        for (let NameIndex in this.Stats[index].Names)
        {
          let color = null;
          if (this.Colors[this.Stats[index].TypeName])
          {
            color = this.Colors[this.Stats[index].TypeName];
          }

          if (this.Stats[index].Names[NameIndex])
          {
            let Name = this.Stats[index].Names[NameIndex];
            let Values = this.Stats[index].Values[NameIndex];
            let Value = Values[Values.length - 1].value;
            let LocalColor = color;
            if (this.Colors[Name])
            {
              LocalColor = this.Colors[Name];
            }
            this.UpdateStatTile(Name, Value, LocalColor);
          }
        }
      }
    }
  }

  UpdateStatTile(Name, Value, ColorInfo)
  {
    let TileId = "Stt_" + Name.replace(/\//g, "_");
    let Tile = $("#" + TileId)[0];

    if (!Tile)
    {
      // Create a new tile and add to Dom
      let NewTile = this.TemplateDom.clone().removeClass("hidden")[0];
      $(NewTile).attr("Id", TileId);
      $(NewTile).find("[Fld_Id='title']").text(Name);
      if (ColorInfo)
      {
        $(NewTile).find("[Fld_Id='unit']").text(ColorInfo.Unit);
      }
      $("#CountersList").append(NewTile);
      Tile = NewTile;
    }
    $(Tile).find("[Fld_Id='value']").text(RoundPow(Value, 2));
    let color = "lightgrey";
    if (ColorInfo)
    {
      let index = 0;
      while (Value >= ColorInfo.Threshold[index])
      {
        color = ColorInfo.Colors[index];
        index++;
      }

    }
    $(Tile).find(".StatusColor").css("background-color", color);

  }

  HandleTileClick()
  {
    this.PlotVisible = !this.PlotVisible;
    if (this.PlotVisible)
    {
      $("#CounterPlot").removeClass("hidden");
      $("#CounterList").removeClass("col-xs-12").addClass("col-xs-4");
    }
    else
    {
      $("#CounterPlot").addClass("hidden");
      $("#CounterList").addClass("col-xs-12").removeClass("col-xs-4");
    }
  }


}

var ServerStatsMgr = new ServerStatsMgrClass();