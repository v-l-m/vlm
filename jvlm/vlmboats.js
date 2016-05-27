//
// VLMBoat layer handling displaying vlm boats, traj
//

VlmBoats = {};

MapOptions = {
                  //Projection mercator sphérique (type google map ou osm)
                  projection: new OpenLayers.Projection("EPSG:900913"),
                  //projection pour l'affichage des coordonnées
                  displayProjection: new OpenLayers.Projection("EPSG:4326"),
                  //unité : le m
                  units: "m",
                  maxResolution: 156543.0339,
                  maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34,
                          20037508.34, 20037508.34),
                  restrictedExtent: new OpenLayers.Bounds(-40037508.34, -20037508.34,
                          40037508.34, 20037508.34)
              };

function SetCurrentBoat(Boat)
{
  CheckBoatRefreshRequired(Boat);
}

function CheckBoatRefreshRequired(Boat)
{
  var CurDate = new Date();
  var NextUpdate = new Date(0);

  
  if (typeof Boat != 'undefined' && 
      typeof Boat.VLMInfo != 'undefined')
  {
    NextUpdate=NextUpdate.setUTCSeconds(Boat.VLMInfo.LUP);
  }
  
  if (typeof Boat== 'undefined' ||  
      CurDate >= NextUpdate)
  {
    // request current boat info
    ShowPb("#PbGetBoatProgress");
    $.get("/ws/boatinfo.php?forcefmt=json&select_idu="+Boat.IdBoat,
            function(result)
            {
              // Check that boat Id Matches expectations
              if (Boat.IdBoat == result.IDU)
              {
                // Store BoatInfo, update map
                Boat.VLMInfo = result;
                // update map is racing
                
                if (Boat.VLMInfo.RAC != "0")
                {
                  // Set Map Center to current boat position
                  var l = new OpenLayers.LonLat(Boat.VLMInfo.LON/1000, Boat.VLMInfo.LAT/1000).transform(MapOptions.displayProjection, MapOptions.projection);
                  
                  // Fix Me : find a way to use a proper zoom factor (dist to next WP??)
                  map.setCenter(l,7);
                  
                  // Draw Boat, course, track....
                  DrawBoat(Boat);
                }                
              }
              HidePb("#PbGetBoatProgress");
            }
          )
  }
}

function DrawBoat(Boat)
{
  var l = new OpenLayers.Geometry.Point(Boat.VLMInfo.LON/1000, Boat.VLMInfo.LAT/1000).transform(MapOptions.displayProjection, MapOptions.projection);
                  
  var feature = new OpenLayers.Feature.Vector(
    l,
    {some:'Boat.IdBoat'},
    {externalGraphic: 'images/target.svg', graphicHeight: 64, graphicWidth: 64}
  );
  VLMBoatsLayer.addFeatures(feature);
}
// allow testing of specific renderers via "?renderer=Canvas", etc
var renderer = OpenLayers.Util.getParameters(window.location.href).renderer;
renderer = (renderer) ? [renderer] : OpenLayers.Layer.Vector.prototype.renderers;

var VLMBoatsLayer = new OpenLayers.Layer.Vector("Simple Geometry", {
    styleMap: new OpenLayers.StyleMap({'default':{
        strokeColor: "#00FF00",
        strokeOpacity: 1,
        strokeWidth: 3,
        fillColor: "#FF5500",
        fillOpacity: 0.5,
        pointRadius: 6,
        pointerEvents: "visiblePainted",
        // label with \n linebreaks
        label : "name: ${name}\n\nage: ${age}",
        
        fontColor: "${favColor}",
        fontSize: "12px",
        fontFamily: "Courier New, monospace",
        fontWeight: "bold",
        labelAlign: "${align}",
        labelXOffset: "${xOffset}",
        labelYOffset: "${yOffset}",
        labelOutlineColor: "white",
        labelOutlineWidth: 3
    }}),
    renderers: renderer
});
//VlmBoats.Layer = OpenLayers.Class(OpenLayers.Layer.Vector, 
//  {
      /* APIProperty: isBaseLayer
       * {Boolean} vlmboats layer is never a base layer.
       */
      //isBaseLayer: false,

      /* Property: canvas
       * {DOMElement} Canvas element.
       */
      //canvas: null,

      /* Constructor: Gribmap.Layer
       * Create a gribmap layer.
       *
       * Parameters:
       * name - {String} Name of the Layer
       * options - {Object} Hashtable of extra options to tag onto the layer
       */
      /*initialize: function(name, options) 
        {
          var i;
          OpenLayers.Layer.prototype.initialize.apply(this, arguments);

          this.canvas = document.createElement('canvas');

          // code for IE browsers
          if (typeof G_vmlCanvasManager != 'undefined') {
              G_vmlCanvasManager.initElement(this.canvas);
          }
          this.canvas.style.position = 'absolute';

          // For some reason OpenLayers.Layer.setOpacity assumes there is
          // an additional div between the layer's div and its contents.
          var sub = document.createElement('div');
          sub.appendChild(this.canvas);
          this.div.appendChild(sub);

        },*/
//        CLASS_NAME: "VlmBoats.Layer"
//  }
          
 //);
          
          