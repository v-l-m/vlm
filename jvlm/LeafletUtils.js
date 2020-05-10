var BuoyMarker = L.Icon.extend(
{
  options:
  {
    iconSize: [36, 72],
    iconAnchor: [18, 36],
    popupAnchor: [0, -36]
  }
});

var TrackWPMarker = L.Icon.extend(
{
  options:
  {
    iconSize: [48, 48],
    iconAnchor: [24, 24],
    iconUrl: 'images/WP_Marker.gif',
  }
});

var PilototoMarker = L.Icon.extend(
{
  options:
  {
    iconSize: [24, 24],
    iconAnchor: [6, 24],
    iconUrl: 'images/Pilototo.png',
  }
});

const BOAT_MARKET_SIZE = 48;
const BOAT_EST_MARKET_SIZE = 24;
var BoatMarker = L.Icon.extend(
{
  options:
  {
    iconSize: [BOAT_MARKET_SIZE, BOAT_MARKET_SIZE],
    iconAnchor: [BOAT_MARKET_SIZE / 2, BOAT_MARKET_SIZE / 2],
    iconUrl: 'images/target.png',
    rotationOrigin: [BOAT_MARKET_SIZE / 2, BOAT_MARKET_SIZE / 2]
  }
});

var BoatEstMarker = L.Icon.extend(
{
  options:
  {
    iconSize: [BOAT_EST_MARKET_SIZE, BOAT_EST_MARKET_SIZE],
    iconAnchor: [BOAT_EST_MARKET_SIZE / 2, BOAT_EST_MARKET_SIZE / 2],
    iconUrl: 'images/target.png',
    rotationOrigin: [BOAT_EST_MARKET_SIZE / 2, BOAT_EST_MARKET_SIZE / 2]
  }
});

var IceGateMarker = L.Icon.extend(
{
  options:
  {
    iconSize: [48, 48],
    iconAnchor: [24, -18],
    iconUrl: 'images/icegate.png',
  }
});

var GateDirMarker = L.Icon.extend(
{
  options:
  {
    iconSize: [48, 48],
    iconAnchor: [24, 24],
    rotationOrigin: [24, 24]
  }
});

function GetPilototoMarker(Order,OrderIndex)
{
  let ret= new PilototoMarker();
  ret.Order = Order;
  ret.OrderIndex=OrderIndex;

  return ret;
}

function GetBuoyMarker(Buoy1)
{
  let RetMark = null;
  if (Buoy1)
  {
    RetMark = new BuoyMarker(
    {
      iconUrl: 'images/Buoy1.png'
    });
  }
  else
  {
    RetMark = new BuoyMarker(
    {
      iconUrl: 'images/Buoy2.png',
      color: "red"
    });
  }

  RetMark.IsCWBuoy = Buoy1;
  return RetMark;
}

function GetBoatMarker(idboat)
{

  let ret = new BoatMarker();
  ret.MarkerOppId = idboat;
  return ret;
}

function GetBoatEstimateMarker()
{
  let ret = new BoatEstMarker();
  return ret;
}

function GetTrackWPMarker()
{
  return new TrackWPMarker();
}

function GetGateTypeMarker(Marker, IsIceGate)
{
  if (IsIceGate)
  {
    return IceGateMarker;
  }
  else
  {
    let ret = new GateDirMarker(
    {
      iconUrl: "images/" + Marker
    });

    return ret;
  }
}

function GetOpponentMarker(OppData)
{
  /* let OppData = {
    "name": Opponent.idusers,
    "Coords": Opp_Coords.toString(),
    "type": 'opponent',
    "idboat": Opponent.idusers,
    "rank": Opponent.rank,
    "Last1h": Opponent.last1h,
    "Last3h": Opponent.last3h,
    "Last24h": Opponent.last24h,
    "IsTeam": (Opponent.country == Boat.VLMInfo.CNT) ? "team" : "",
    "IsFriend": (isFriend ? ZFactor * 2 : ZFactor),
    "color": Opponent.color
  }; */
  let ret = new L.Icon(
  {
    iconUrl: "images/opponent" + OppData.IsTeam + ".png",
    iconAnchor: [OppData.IsFriend / 2, OppData.IsFriend / 2],
    iconSize: [OppData.IsFriend, OppData.IsFriend]
  });

  ret.MarkerOppId = OppData.idboat;

  return ret;
}

function ClearCurrentMapMarkers(Boat)
{
  if (Boat && Boat.RaceMapFeatures)
  {
    if (Boat.RaceMapFeatures.OppPopup && Boat.RaceMapFeatures.OppPopup.PrevOpp)
    {
      Boat.RaceMapFeatures.OppPopup.PrevOpp.closePopup();
      Boat.RaceMapFeatures.OppPopup.PrevOpp.unbindPopup();
    }
    RemoveFromMap(Boat.RaceMapFeatures,map);
  }
}

function EnsureMarkersVisible(Boat)
{
  if (Boat)
  {
    RestoreMarkersOnMap(Boat.RaceMapFeatures,map);
  }

}

function RestoreMarkersOnMap(Feat,map)
{
  if (!Feat || typeof(Feat) === "function")
  {
    return;
  }

  if (Array.isArray(Feat))
  {
    for (let index in Feat)
    {
      RestoreMarkersOnMap(Feat[index],map);
    }
  }
  else if (typeof Feat === "object" && typeof Feat._leaflet_id === "undefined")
  {
    for (let member in Feat)
    {
      switch (member)
      {
        // Special restore handling (ie do not show opppopup again)
        case "OppPopup":
          break;
        default:
          RestoreMarkersOnMap(Feat[member],map);
          break;
      }

    }
  }
  else if (Feat._leaflet_id && !Feat._map)
  {
    Feat.addTo(map);
  }
}


function RemoveFromMap(Feat,map)
{
  if (!Feat || typeof(Feat) === "function")
  {
    return;
  }

  if (Array.isArray(Feat))
  {
    for (let index in Feat)
    {
      RemoveFromMap(Feat[index],map);
    }
  }
  else if (typeof Feat === "object" && typeof Feat._leaflet_id === "undefined")
  {
    for (let member in Feat)
    {
      RemoveFromMap(Feat[member],map);
    }
  }
  else if (Feat._leaflet_id)
  {
    Feat.removeFrom(map);
  }
}

// LeafletMarkerRotation from 
//https://github.com/bbecquet/Leaflet.RotatedMarker/blob/master/leaflet.rotatedMarker.js
(function()
{
  // save these original methods before they are overwritten
  var proto_initIcon = L.Marker.prototype._initIcon;
  var proto_setPos = L.Marker.prototype._setPos;

  var oldIE = (L.DomUtil.TRANSFORM === 'msTransform');

  L.Marker.addInitHook(function()
  {
    var iconOptions = this.options.icon && this.options.icon.options;
    var iconAnchor = iconOptions && this.options.icon.options.iconAnchor;
    if (iconAnchor)
    {
      iconAnchor = (iconAnchor[0] + 'px ' + iconAnchor[1] + 'px');
    }
    this.options.rotationOrigin = this.options.rotationOrigin || iconAnchor || 'center bottom';
    this.options.rotationAngle = this.options.rotationAngle || 0;

    // Ensure marker keeps rotated during dragging
    this.on('drag', function(e)
    {
      e.target._applyRotation();
    });
  });

  L.Marker.include(
  {
    _initIcon: function()
    {
      proto_initIcon.call(this);
    },

    _setPos: function(pos)
    {
      proto_setPos.call(this, pos);
      this._applyRotation();
    },

    _applyRotation: function()
    {
      if (this.options.rotationAngle)
      {
        this._icon.style[L.DomUtil.TRANSFORM + 'Origin'] = this.options.rotationOrigin;

        if (oldIE)
        {
          // for IE 9, use the 2D rotation
          this._icon.style[L.DomUtil.TRANSFORM] = 'rotate(' + this.options.rotationAngle + 'deg)';
        }
        else
        {
          // for modern browsers, prefer the 3D accelerated version
          this._icon.style[L.DomUtil.TRANSFORM] += ' rotateZ(' + this.options.rotationAngle + 'deg)';
        }
      }
    },

    setRotationAngle: function(angle)
    {
      this.options.rotationAngle = angle;
      this.update();
      return this;
    },

    setRotationOrigin: function(origin)
    {
      this.options.rotationOrigin = origin;
      this.update();
      return this;
    }
  });
})();