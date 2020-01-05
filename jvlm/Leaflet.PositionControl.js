//
// Mouse Coords Control with ZoomLevel and Wind Arrow
//

L.Control.WindMouseControl = L.Control.extend(
{
  options:
  {
    position: 'bottomleft',
  },
  _GetControlHTML: function()
  {
    let ret = "<table><tr>" +
      "<td class='leaflet-control-windmouse_zoomcol'><span>Zoom : </span> <span id='LWM_ZoomLevel'></span></td>" +
      "<td class='leaflet-control-windmouse_latlon'><span>Lat : </span> <span id='LWM_Lat'></span></td>" +
      "<td class='leaflet-control-windmouse_latlon'><span>Lon : </span> <span id='LWM_Lon'></span></td></tr><tr>" +
      "<td class='leaflet-control-windmouse_wndimg'><img class='BeaufortImg'></td>" +
      "<td class='leaflet-control-windmouse_wndhdg'><span id='LWM_Hdg'></span></td>" +
      "<td class='leaflet-control-windmouse_wndspd'><span id='LWM_Spd'></span></td>" +
      "</tr></table>";

    return ret;
  },
  onAdd: function(map)
  {
    this._map = map;
    let FrameClassName = "leaflet-control-windmouse-frame";
    this._Container = L.DomUtil.create('div', FrameClassName);
    this._ZContainer = L.DomUtil.create('div', "", this._Container);
    this._ZContainer.innerHTML = this._GetControlHTML();

    map.on("mousemove", this._Update, this);
    map.on("zoomend", this._ZoomEnd, this);

    this._SetZoom(map.getZoom());
    return this._Container;

  },
  _Update: function(e)
  {

    if (typeof map.GribMap === "undefined")
    {
      return;
    }
    
    let Lat = (e.latlng.lat);
    let Lon = (e.latlng.lng);
    let CurZoom = this._map.getZoom();

    let MI = null;
    
    if (CurZoom>=MIN_MAP_ZOOM)
    {
      MI=GribMgr.WindAtPointInTime(map.GribMap.GetGribMapTime(), Lat, Lon);
    }

    let FieldMappings = [];
    FieldMappings.push([FIELD_MAPPING_TEXT, "#LWM_Lat", RoundPow(Lat, 3)]);
    FieldMappings.push([FIELD_MAPPING_TEXT, "#LWM_Lon", RoundPow(Lon, 3)]);
    if (MI)
    {
      FieldMappings.push([FIELD_MAPPING_TEXT, "#LWM_Hdg", RoundPow(MI.Speed, 2) + " kts"]);
      FieldMappings.push([FIELD_MAPPING_TEXT, "#LWM_Spd", RoundPow(MI.Heading, 2) + " °"]);
      let Beaufort = GribMgr.GetBeaufort(MI.Speed);
      $(".BeaufortImg").css("background-position",'-0px -'+ 24*Beaufort +'px');
      let Angle = MI.Heading - 56;
      $(".BeaufortImg").css("transform",'rotate(' + Angle + 'deg)');
    }
    else
    {
      FieldMappings.push([FIELD_MAPPING_TEXT, "#LWM_Hdg", "-- kts"]);
      FieldMappings.push([FIELD_MAPPING_TEXT, "#LWM_Spd", "-- °"]);
      $(".BeaufortImg").css('style="background-position: -0px -0px"');

    }

    FillFieldsFromMappingTable(FieldMappings);
    this._SetZoom(CurZoom);
  },
  _ZoomEnd: function(e)
  {
    this._SetZoom(this._map.getZoom());
  },
  _SetZoom(z)
  {
    let FieldMappings = [];
    FieldMappings.push([FIELD_MAPPING_TEXT, "#LWM_ZoomLevel", RoundPow(z, 1)]);

    FillFieldsFromMappingTable(FieldMappings);
  }
});

L.control.WindMouseControl = function(options)
{
  return new L.Control.WindMouseControl(options);
};