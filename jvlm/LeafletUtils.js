var BuoyMarker = L.Icon.extend(
{
  options:
  {
    iconSize: [36, 72],
    iconAnchor: [18, 36],
    popupAnchor: [0, -72]
  }
});

var TrackWPMarker = L.Icon.extend(
  {
    options:
  {
    iconSize: [48, 48],
    iconAnchor: [24, 24],
    iconUrl: 'images/WP_Marker.gif'
  }
  }
);

var BoatMarker = L.Icon.extend(
  {
    options:
  {
    iconSize: [48, 48],
    iconAnchor: [24, 24],
    iconUrl: 'images/target.png'
  }
  }
);

function GetBuoyMarker(Buoy1)
{
  if (Buoy1)
  {
    return new BuoyMarker(
    {
      iconUrl: 'images/Buoy1.png'
    });
  }
  else
  {
    return new BuoyMarker(
    {
      iconUrl: 'images/Buoy2.png'
    });
  }
}

function GetBoatMarker()
{

}

function GetTrackWPMarker()
{
  return new  TrackWPMarker();
}