class WPMinimapHandlerClass
{
  constructor()
  {
    this.Minimap = $("#WPMiniMap")[0];
    this.TimeOut = null;
    this.LMap = null;
    this.Races = {};

    this.FindRaceInfo = function(RaceId)
    {
      if (!_CurPlayer)
      {
        return null;
      }

      if (_CurPlayer.RaceInfo && _CurPlayer.RaceInfo[RaceId])
      {
        return _CurPlayer.RaceInfo[RaceId];
      }

      let fleets = [_CurPlayer.Fleet, _CurPlayer.fleet_boatsit];

      for (let index in fleets)
      {
        if (fleets[index])
        {
          let F = fleets[index];

          for (let boatindex in F)
          {
            if (F[boatindex])
            {
              let Boat = F[boatindex];
              if ((Boat.Engaged() == RaceId) && Boat.RaceInfo)
              {
                return Boat.RaceInfo;
              }
            }
          }
        }
      }

      return null;
    };

    this.GetWayPoint = function(MouseEvent)
    {
      let Info = MouseEvent.currentTarget;
      let RaceId = null;
      let WPID = null;

      if (Info.attributes.raceid)
      {
        RaceId = Info.attributes.raceid.value;
      }

      if (Info.attributes.wp_id)
      {
        WPID = Info.attributes.wp_id.value;
      }

      let RaceInfo = this.FindRaceInfo(RaceId);

      if (!RaceInfo)
      {
        return;
      }

      for (let index in RaceInfo.races_waypoints)
      {
        if (RaceInfo.races_waypoints[index] && RaceInfo.races_waypoints[index].idwaypoint == WPID)
        {
          let MiniMapFieldMapping = [];

          MiniMapFieldMapping.push([FIELD_MAPPING_TEXT, ".MiniMapRaceName", RaceInfo.racename]);
          MiniMapFieldMapping.push([FIELD_MAPPING_TEXT, ".MiniMapWPIndex", RaceInfo.races_waypoints[index].wporder + " - " + RaceInfo.races_waypoints[index].libelle]);
          FillFieldsFromMappingTable(MiniMapFieldMapping);
          
          return RaceInfo.races_waypoints[index];
        }
      }

      return null;

    };

    this.Show = function(e)
    {
      let WP = this.GetWayPoint(e);

      if (!WP)
      {
        return;
      }

      if (!WP.MapCenter)
      {
        if (typeof WP.longitude2 === "undefined" || typeof WP.latitude2 === "undefined")
        {
          WP.MapCenter = [WP.latitude1, WP.longitude1];
          WP.MapZoom = WP.maparea - 1;
        }
        else
        {
          WP.MapCenter = [(WP.latitude1 + WP.latitude2) / 2, (WP.longitude1 + WP.longitude2) / 2];
          let DLat = Math.abs(WP.latitude1 - WP.latitude2);
          let DLon = Math.abs(WP.longitude1 - WP.longitude2);
          WP.MapZoom = 0;
          if (DLat > 2 * DLon)
          {
            WP.MapZoom = Math.log(90 / DLat) / Math.log(2);
          }
          else
          {
            WP.MapZoom = Math.log(360 / DLon) / Math.log(2);
          }
        }
      }


      if (!this.LMap)
      {
        this.InitMap(WP.MapCenter, WP.MapZoom);
        this.LMap.MinimapFeature = {};
      }
      else
      {
        this.LMap.setView(WP.MapCenter, WP.MapZoom);
        RemoveFromMap(this.LMap.MinimapFeature,this.LMap);
        this.LMap.MinimapFeature.GateMarker=null;
      }

      let WPIndex = WP.wporder;
      MakeSingleGateMapFeatures(this.LMap, WP, WPIndex, this.LMap.MinimapFeature, true);
      RestoreMarkersOnMap(this.LMap.MinimapFeature,this.LMap);
      
      if (this.TimeOut)
      {
        clearTimeout(this.TimeOut);
      }
      //this.TimeOut = setTimeout(this.Hide.bind(this), 5000);
      let item = e.currentTarget;
      let r = item.getBoundingClientRect();
      //$(this.Minimap).removeClass("hidden");
      //$("#WPMiniMapDialog").modal("hide");
      if (!$("#WPMiniMapDialog").hasClass("in"))
      {
        $("#WPMiniMapDialog").modal("show");
      }
      this.LMap.invalidateSize();

      //$(this.Minimap).css("margin-top", -$("#InfosRace")[0].getBoundingClientRect().height + $("#WPInfos")[0].getBoundingClientRect().top + r.top - 50);
    };

    this.Hide = function()
    {
      $(this.Minimap).addClass("hidden");
    };

    this.InitMap = function(StartPos, StartZoom)
    {
      this.LMap = L.map('WPMiniMap' /*,{preferCanvas:true}*/ );

      // Tiles
      let src = tileUrlSrv;
      new L.tileLayer(src,
      {
        attribution: 'gshhsv2',
        maxZoom: 20,
        tms: false,
        id: 'WPMinimap',
        detectRetina: true,
        subdomains: tilesUrlArray,

      }).addTo(this.LMap);

      if (!StartPos)
      {
        StartPos = [0, 0];
      }
      if (!StartZoom)
      {
        StartZoom = 0;
      }

      this.LMap.setView(StartPos, StartZoom);
      $("#WPMiniMapDialog").on('shown.bs.modal', function()
      {
        $.Deferred(WPMiniMapHandler.OnModalShown.bind(WPMiniMapHandler));
      });

    };

    this.OnModalShown = function(e)
    {
      this.LMap.invalidateSize();
    };

  }

}

var WPMiniMapHandler = null;


function HandleWPMiniMapHover(e)
{
  if (!WPMiniMapHandler)
  {
    WPMiniMapHandler = new WPMinimapHandlerClass();
  }

  WPMiniMapHandler.Show(e);
}