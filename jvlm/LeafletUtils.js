var BuoyMarker = L.Icon.extend(
{
  options:
  {
    iconSize: [36, 72],
    iconAnchor: [18, 36],
    popupAnchor: [0, -72]
  }
});

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