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
    iconUrl: 'images/WP_Marker.gif'
  }
});

var BoatMarker = L.Icon.extend(
{
  options:
  {
    iconSize: [48, 48],
    iconAnchor: [24, 24],
    iconUrl: 'images/target.png',
    rotationOrigin :[24,24]
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

function GetBoatMarker()
{
  return new BoatMarker();
}

function GetTrackWPMarker()
{
  return new TrackWPMarker();
}

// LeafletMarkerRotation from 
//https://github.com/bbecquet/Leaflet.RotatedMarker/blob/master/leaflet.rotatedMarker.js
(function() {
  // save these original methods before they are overwritten
  var proto_initIcon = L.Marker.prototype._initIcon;
  var proto_setPos = L.Marker.prototype._setPos;

  var oldIE = (L.DomUtil.TRANSFORM === 'msTransform');

  L.Marker.addInitHook(function () {
      var iconOptions = this.options.icon && this.options.icon.options;
      var iconAnchor = iconOptions && this.options.icon.options.iconAnchor;
      if (iconAnchor) {
          iconAnchor = (iconAnchor[0] + 'px ' + iconAnchor[1] + 'px');
      }
      this.options.rotationOrigin = this.options.rotationOrigin || iconAnchor || 'center bottom' ;
      this.options.rotationAngle = this.options.rotationAngle || 0;

      // Ensure marker keeps rotated during dragging
      this.on('drag', function(e) { e.target._applyRotation(); });
  });

  L.Marker.include({
      _initIcon: function() {
          proto_initIcon.call(this);
      },

      _setPos: function (pos) {
          proto_setPos.call(this, pos);
          this._applyRotation();
      },

      _applyRotation: function () {
          if(this.options.rotationAngle) {
              this._icon.style[L.DomUtil.TRANSFORM+'Origin'] = this.options.rotationOrigin;

              if(oldIE) {
                  // for IE 9, use the 2D rotation
                  this._icon.style[L.DomUtil.TRANSFORM] = 'rotate(' + this.options.rotationAngle + 'deg)';
              } else {
                  // for modern browsers, prefer the 3D accelerated version
                  this._icon.style[L.DomUtil.TRANSFORM] += ' rotateZ(' + this.options.rotationAngle + 'deg)';
              }
          }
      },

      setRotationAngle: function(angle) {
          this.options.rotationAngle = angle;
          this.update();
          return this;
      },

      setRotationOrigin: function(origin) {
          this.options.rotationOrigin = origin;
          this.update();
          return this;
      }
  });
})();