class PlotHorizon
{
  constructor(Horizon)
  {
    this.LabelFormat = [];
    this.LabelFormat['1Day'] = this.TickFormat_1Day;
    this.LabelFormat['1Week'] = this.TickFormat_1Week;
    this.LabelFormat['1Month'] = this.TickFormat_1Month;
    this.LabelFormat['1Year'] = this.TickFormat_1Year;
    this.SetHorizon(Horizon);

  }

  MinPlotDate()
  {
    switch (this.Horizon)
    {
      case "1Week":
        return moment().add(-7, 'd').toDate();

      case "1Month":
        return moment().add(-1, 'M').toDate();

      case "1Year":
        return moment().add(-1, 'y').toDate();

      case "1Day":
      default:
        return moment().add(-1, 'd').toDate();

    }
  }
  SetHorizon(Horizon)
  {
    switch (Horizon)
    {
      case "1Day":
      case "1Week":
      case "1Day":
      case "1Year":
        this.Horizon = Horizon;
        break;
      default:
        this.Horizon = "1Day";
    }
  }

  LabelFormatter(value, index, values)
  {
    this.SetHorizon(this.Horizon);
    return this.LabelFormat[this.Horizon](value, index, values);
  }

  TickFormat_1Day(value, index, values)
  {
    //if (values[index].major)
    {
      return moment(value).format("LT");
    }
    //else
    {
      return null;
    }
  }

  TickFormat_1Week(value, index, values)
  {
    if (values[index].major)
    {
      return moment(value).format("D-M LT");
    }
    else
    {
      return null;
    }
  }

  TickFormat_1Month(value, index, values)
  {
    if (values[index].major)
    {
      return moment(value).format("D-M");
    }
    else
    {
      return null;
    }
  }

  TickFormat_1Year(value, index, values)
  {
    if (values[index].major)
    {
      return moment(value).format("M");
    }
    else
    {
      return null;
    }
  }

}


class ServerStatsMgrClass
{
  constructor()
  {
    this.TemplateDom = $("#StatIndicatorTemplate");
    $(".StatIndicatorTile").on('click', this.HandleTileClick.bind(this));
    this.TileInfo = {
      "Stat_MailQStat":
      {
        Threshold: [0, 10, 25],
        Colors: ["#00FF00", "#FFA500", "#FF0000"],
        Unit: "msg",
        Image: "./images/Stats_Msg.png"
      },
      "Stat_VolumeSpace":
      {
        Threshold: [0, 90, 95],
        Colors: ["#00FF00", "#FFA500", "#FF0000"],
        Unit: "%",
        Image: "./images/Stats_Disk.png"
      },
      "Stat_MySQLStats":
      {
        Threshold: [0, 500, 700],
        Colors: ["#00FF00", "#FFA500", "#FF0000"],
        Unit: null,
        Image: "./images/Stats_Connections.png"
      },
      "Stat_EngineStats":
      {
        Threshold: [0,50, 90, 180],
        Colors: ["#FF0000","#FF0000", "#FFA500", "#00FF00"],
        Unit: "Boats/s",
        Image: "./images/Stats_Speed.png",
        Default:true,
      },
      "BoatCount":
      {
        Threshold: [0],
        Colors: ["#00FF00"],
        Unit: null,
        Default:true,
      },
      "RaceCount":
      {
        Threshold: [0],
        Colors: ["#00FF00"],
        Unit: null,
        Default:true,
      },
      "max_connections":
      {
        Threshold: [0],
        Colors: ["#0000FF"],
        Image: "./images/Stats_Connections.png"
      },
      "NTP Offset (ms)":
      {
        Threshold: [-5,-1,1,5],
        Colors: ["#0000FF","#00FF00","#00FF00","#0000FF"],
        Image: "./images/Stats_Speed.png"
      },
    };
    this.PlotHorizon = new PlotHorizon("1Day");
  }

  SetHorizon(horizon)
  {
    this.PlotHorizon.Horizon=horizon;
    if (this.CurSetID)
    {
      this.PlotTileData(this.CurSetID);
    }
  }

  LoadStats()
  {
    $("#StatsPreloader").removeClass("hidden");
    this.TimeOutHandle = this.GetStats(0);
  }

  GetStats(interval_ms)
  {
    return setTimeout(() => {
      $.get("/ws/serverinfo/ServerStatus.php?v=" + Math.round(new Date().getTime() / 1000 / 60 / 3), this.HandleStatLoaded.bind(this));  
    }, interval_ms);
    
  }


  HandleStatLoaded(e)
  {
    this.Stats = e;
    this.DataSetTiles = [];
    this.DisplayCurrentValues();
    this.PlotTileData("Stt_BoatCount");
    $("#StatsPreloader").addClass("hidden");    
    this.TimeOutHandle = this.GetStats(30000);
  }

  DisplayCurrentValues()
  {

    $("#StatsGen").text("Stats from : "+ moment(this.Stats.Generated*1000).format());
    $("#StatsGenDur").text("Gen. in :"+ RoundPow( this.Stats.GenerationTime*1000,1)  + " ms");
    this.Stats.Data.sort();
    for (let index in this.Stats.Data)
    {
      if ( this.Stats.Data[index])
      {
        let TypedDataRow = this.Stats.Data[index];
        let color = null;

        if (this.TileInfo[TypedDataRow.TypeName])
        {
          color = this.TileInfo[TypedDataRow.TypeName];
          if (!VLM2Prefs.AdvancedStats && !color.Default)
          {
            continue;
          }
        }
        else if (!VLM2Prefs.AdvancedStats)
        {
          continue;
        }

        TypedDataRow.Data = TypedDataRow.Data.sort(this.TypedRowSorter);
        for (let ValueIndex in TypedDataRow.Data)
        {
          let DataRow = TypedDataRow.Data[ValueIndex];
          DataRow.Values = DataRow.Values.sort(this.DataRowSorter);

          if (DataRow.Name)
          {
            let Name = DataRow.Name;
            let Values = DataRow.Values;
            let Value = Values[Values.length - 1].value;
            let LocalColor = color;
            if (this.TileInfo[Name])
            {
              LocalColor = this.TileInfo[Name];
            }
            let TileId = this.UpdateStatTile(Name, Value, LocalColor);

            this.DataSetTiles[TileId] = DataRow;
            this.DataSetTiles[TileId].TileInfo = LocalColor;
          }
        }
      }
    }
  }

  TypedRowSorter(r1, r2)
  {
    if (r1.Name && r2.Name)
    {
      if (r1.Name > r2.Name)
      {
        return 1;
      }
      else if (r1.Name < r2.Name)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }
    else
    {
      if (r1 >= r2)
      {
        return 1;
      }
      else
      {
        return -1;
      }
    }
  }

  DataRowSorter(r1, r2)
  {
    if (r1.date && r2.date)
    {
      if (r1.date === r2.date)
      {
        if (r1.value > r2.value)
        {
          return 1;
        }
        else
        {
          return -1;
        }
      }
      else if (r1.date > r2.date)
      {
        return 1;
      }
      else
      {
        return -1;
      }
    }
    else
    {
      if (r1 >= r2)
      {
        return 1;
      }
      else
      {
        return -1;
      }
    }
  }

  UpdateStatTile(Name, Value, TileInfo)
  {
    let TileId = "Stt_" + Name.replace(/[\/\(\ \)]/g, "_");
    let Tile = $("#" + TileId)[0];

    if (!Tile)
    {
      // Create a new tile and add to Dom
      let NewTile = this.TemplateDom.clone().removeClass("hidden")[0];
      $(NewTile).attr("Id", TileId);
      $(NewTile).find("[Fld_Id='title']").text(Name);
      $(NewTile).on('click', this.HandleTileClick.bind(this));
      if (TileInfo.Unit)
      {
        $(NewTile).find("[Fld_Id='unit']").text(TileInfo.Unit);
      }
      if (TileInfo.Image)
      {
        $(NewTile).find("[src]").attr("src", TileInfo.Image);
      }
      else
      {
        $(NewTile).find("[src]").addClass("hidden");
      }
      $("#CountersList").append(NewTile);
      Tile = NewTile;
    }
    $(Tile).find("[Fld_Id='value']").text(RoundPow(Value, 2));
    let color = "lightgrey";
    if (TileInfo)
    {
      let index = 0;
      while (Value >= TileInfo.Threshold[index])
      {
        color = TileInfo.Colors[index];
        index++;
      }
    }
    $(Tile).find(".StatusColor").css("background-color", color);
    return TileId;

  }

  HandleTileClick(e)
  {
    let DataSetId = e.currentTarget.attributes.Id.value;

    if (this.DataSetTiles[DataSetId])
    {
      this.PlotTileData(DataSetId);
    }

  }

  CancelStats()
  {
    if (this.TimeOutHandle)
    {
      clearTimeout(this.TimeOutHandle);
      this.TimeOutHandle=null;
    }
  }

  PlotTileData(DataSetId)
  {
    let Values = [];
    let DataName = this.DataSetTiles[DataSetId].Name;
    let MinDate = null;
    let MaxDate = null;
    let MaxValue = null;
    let BoundsMaxValue = null;
    this.CurSetID=DataSetId;
    for (let index in this.DataSetTiles[DataSetId].Values)
    {
      let Data = this.DataSetTiles[DataSetId].Values[index];
      let PointDate = new Date(Data.date * 1000);
      if (PointDate >= this.PlotHorizon.MinPlotDate())
      {
        Values.push(
        {
          x: PointDate,
          y: Data.value
        });
        if (!MinDate)
        {
          MinDate = PointDate;
        }
        if (!MaxDate || MaxDate < PointDate)
        {
          MaxDate = PointDate;
        }

        if (!MaxValue || MaxValue < Data.value)
        {
          MaxValue = Data.value;
        }
      }

    }

    let DataSets = [];


    DataSets.push(
    {
      label: DataName,
      backgroundColor: 'AliceBlue',
      borderColor: 'AliceBlue',
      data: Values,
      pointRadius: 1,
      borderWidth: 1,
      fill: false,
      steppedLine: 'middle',
      //showLine: false
    });

    if (this.DataSetTiles[DataSetId].TileInfo)
    {
      let TI = this.DataSetTiles[DataSetId].TileInfo;
      let CurSet = {};

      for (let infoindex in TI.Threshold)
      {
        CurSet = {
          label : "Thr. "+TI.Threshold[infoindex],
          borderWidth: 2,
          pointRadius: 0,
          fill: false,
          steppedLine: 'middle',
          borderColor: TI.Colors[infoindex],
          data: [
          {
            x: MinDate,
            y: TI.Threshold[infoindex]
          },
          {
            x: MaxDate,
            y: TI.Threshold[infoindex]
          }],
        };
        DataSets.push(CurSet);
      }

    }

    if (!this.Chart)
    {
      let Height = $("#StatsContainer").css("Height");
      $("#StatsPlotCanvas").css("Height", Height);
      let ctx = $("#StatsPlotCanvas")[0].getContext("2d");
      this.Chart = new Chart(ctx,
      {
        // The type of chart we want to create
        type: 'line',
        // The data for our dataset
        data:
        {
          //labels: Labels,
          datasets: DataSets
        },
        // Configuration options go here
        options:
        {
          scales:
          {
            yAxes: [
            {
              ticks:
              {
                tickLength: 5,
                //suggestedMax: BoundsMaxValue,
              },
              gridLines:
              {
                zeroLineColor: "#FFFFFF55",
                color: "#FFFFFF55",
              },

            }],
            xAxes: [
            {
              type: 'time',
              time:
              {
                displayFormats:
                {
                  minute: 'LT',
                  hour: 'D/LT',
                  day: "D-M",
                  quarter: 'MMM YYYY'
                }

              },
              gridLines:
              {
                zeroLineColor: "white",
                color: "#FFFFFF55",
              },
              ticks:
              {
                // Include a dollar sign in the ticks
                //callback: this.PlotHorizon.LabelFormatter.bind(this.PlotHorizon),
                minRotation: 0,
                maxRotation: 0,
                autoSkipPadding: 5,
                autoSkip: true,
                tickLength: 5,
              },
            }],
          },
          legend:
          {
            display: true,
          },
          title:
          {
            display: true,
            text: DataName,
          }
        }
      });
    }
    this.Chart.config.data.datasets = DataSets;
    this.Chart.options.title.text=DataName;
    this.Chart.update();
    StatMGR.Stat("PlotStats",this.PlotHorizon.Horizon,DataName);
  }
}



var ServerStatsMgr = new ServerStatsMgrClass();