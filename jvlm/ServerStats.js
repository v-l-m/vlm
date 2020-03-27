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
        Colors: ["lime", "orange", "red"],
        Unit: "msg",
        Image: "./images/Stats_Msg.png"
      },
      "Stat_VolumeSpace":
      {
        Threshold: [0, 90, 95],
        Colors: ["lime", "orange", "red"],
        Unit: "%",
        Image: "./images/Stats_Disk.png"
      },
      "Stat_MySQLStats":
      {
        Threshold: [0, 500, 700],
        Colors: ["lime", "orange", "red"],
        Unit: null,
        Image: "./images/Stats_Connections.png"
      },
      "Stat_EngineStats":
      {
        Threshold: [0, 100, 200],
        Colors: ["red", "orange", "lime"],
        Unit: "Boats/s",
        Image: "./images/Stats_Speed.png"
      },
      "BoatCount":
      {
        Threshold: [0],
        Colors: ["lime"],
        Unit: null
      },
      "RaceCount":
      {
        Threshold: [0],
        Colors: ["lime"],
        Unit: null
      },
      "max_connections":
      {
        Threshold: [0],
        Colors: ["blue"],
        Image: "./images/Stats_Connections.png"
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
    this.DataSetTiles=[];
    this.DisplayCurrentValues();
    $("#StatsPreloader").addClass("hidden");
  }

  DisplayCurrentValues()
  {
    this.Stats.Data.sort();
    for (let index in this.Stats.Data)
    {
      if (this.Stats.Data[index])
      {
        let TypedDataRow = this.Stats.Data[index];
        let color = null;

        if (this.TileInfo[TypedDataRow.TypeName])
        {
          color = this.TileInfo[TypedDataRow.TypeName];
        }

        TypedDataRow.Data=TypedDataRow.Data.sort(this.TypedRowSorter);
        for (let ValueIndex in TypedDataRow.Data)
        {
          let DataRow = TypedDataRow.Data[ValueIndex];
          DataRow.Values=DataRow.Values.sort(this.DataRowSorter);

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
            let TileId=this.UpdateStatTile(Name, Value, LocalColor);
            this.DataSetTiles[TileId]=DataRow;
          }
        }
      }
    }
  }

  TypedRowSorter(r1,r2)
  {
    if (r1.Name && r2.Name)
    {
      if (r1.Name> r2.Name)
      {
        return 1;
      }
      else if (r1.Name < r2.Name)
      {
        return -1     ;   
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
        return sort(r1.value,r2.value);
      }
      else if (r1.date > r2.date)
      {
        return -1;
      }
      else
      {
        return 1;
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
    let TileId = "Stt_" + Name.replace(/\//g, "_");
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
      let Labels=[];
      let Values=[];
      let DataName = this.DataSetTiles[DataSetId].Name;
      for (let index in this.DataSetTiles[DataSetId].Values)
      {
        let Data=this.DataSetTiles[DataSetId].Values[index];
        Labels.push(new moment(Data.date*1000));
        Values.push(Data.value);
        
      }
      let Height=$("#StatsContainer").css("Height");
      $("#StatsPlotCanvas").css("Height",Height);
      let ctx= $("#StatsPlotCanvas")[0].getContext("2d");
      var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: 'line',
    
        // The data for our dataset
        data: {
            labels: Labels,
            datasets: [{
                label: DataName,
                backgroundColor: '#fccd49',
                borderColor: 'rgb(255, 99, 132)',
                data: Values,
                showLine:false
            }]
        },
    
        // Configuration options go here
        options: {}
    });
    }
    
  }


}

var ServerStatsMgr = new ServerStatsMgrClass();