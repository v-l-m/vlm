/* global OpenLayers */
//
// VLMBoat layer handling displaying vlm boats, traj
//

var BOAT_ICON=0;

var MapOptions = {
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

var LastRequestedBoat=-1;

function CheckBoatRefreshRequired(Boat)
{
  var CurDate = new Date();
  var NextUpdate = new Date(0);

  
  if (typeof Boat != 'undefined' && 
      typeof Boat.VLMInfo != 'undefined' && typeof Boat.VLMInfo.LUP != 'undefined')
  {
    NextUpdate.setUTCSeconds(Boat.VLMInfo.LUP);
  }
  
  if (typeof Boat== 'undefined' ||  
      CurDate >= NextUpdate )
  {
    // request current boat info
    ShowPb("#PbGetBoatProgress");
    $.get("/ws/boatinfo.php?forcefmt=json&select_idu="+Boat.IdBoat,
            function(result)
            {
              // Check that boat Id Matches expectations
              if (Boat.IdBoat == result.IDU)
              {
                // Set Current Boat for player
                _CurPlayer.CurBoat=Boat;

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
                  
                  // Update Boat info in main menu bar
                  UpdateInMenuBoatInfo(Boat);

                  LastRequestedBoat =result.IDU;

                  if (typeof Boat.RaceInfo.idraces == 'undefined')
                  {
                    // Get race info if first request for the boat
                    $.get("/ws/raceinfo.php?idrace="+Boat.VLMInfo.RAC,
                      function(result)
                      {
                        // Save raceinfo with boat
                        Boat.RaceInfo=result;

                        DrawRaceGates(Boat.RaceInfo);
                      }
                    );
                    
                  }
                  
                }                
              }
              HidePb("#PbGetBoatProgress");
            }
          )
  }
}

function DrawBoat(Boat)
{
  var Pos = new OpenLayers.Geometry.Point(Boat.VLMInfo.LON/1000, Boat.VLMInfo.LAT/1000);
  var PosTransformed = Pos.transform(MapOptions.displayProjection, MapOptions.projection)
       
  if (Boat.OLBoatFeatures.length == 0)
  {
    Boat.OLBoatFeatures.push( new OpenLayers.Feature.Vector(
      PosTransformed,
      {"Id":Boat.IdBoat},
      {externalGraphic: 'images/target.svg', graphicHeight: 64, graphicWidth: 64,rotation: Boat.VLMInfo.HDG}
      )
    );
    
    VLMBoatsLayer.addFeatures(Boat.OLBoatFeatures[BOAT_ICON]);

  }
  else
  {
    Boat.OLBoatFeatures[BOAT_ICON].lonlat = PosTransformed;
    Boat.OLBoatFeatures[BOAT_ICON].style.rotation= Boat.VLMInfo.HDG;
  };
  
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
        label : "WP ${name} - ${Coords}",
        
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

// Background load controller from ext html file
function GetBoatControllerPopup()
{
  $("#BoatController").load("BoatController.html")
  return '<div id="BoatController"></div>';
}

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

 function DrawRaceGates(RaceInfo)
 {

   // Loop all gates
   for (index in RaceInfo.races_waypoints)
   {
      // Draw a single race gates
      var WP = RaceInfo.races_waypoints[index];
      // Draw WP1
      
      var WP1_Pos = new OpenLayers.Geometry.Point(WP.longitude1/1000, WP.latitude1/1000);
      var WP1_PosTransformed = WP1_Pos.transform(MapOptions.displayProjection, MapOptions.projection)
      var WP1= new OpenLayers.Feature.Vector(WP1_PosTransformed,
                                              {
                                                "name":"WP"+index,
                                                "Coords":WP1_Pos
                                              }
                                              );
      
    VLMBoatsLayer.addFeatures(WP1);

   }


 }
     
const PM_HEADING=1;
const PM_ANGLE=2;
const PM_ORTHO=3;
const PM_VMG=4;
const PM_VBVMG=5;

function SendVLMBoatOrder(Mode, AngleOrLon, Lat, WPAt)
{
  var request={};;

  var verb="pilot_set";

  if (typeof _CurPlayer == 'undefined' || typeof _CurPlayer.CurBoat == 'undefined')
  {
    alert ("Must select a boat to send an order");
    return;
  }
  switch (Mode)
  {
    case PM_HEADING:
    case PM_ANGLE:
      request={idu:_CurPlayer.CurBoat.IdBoat,pim:Mode,pip:AngleOrLon};
      break;

    case PM_ORTHO:
    case PM_VBVMG:
    case PM_VMG:
      request={idu:_CurPlayer.CurBoat.IdBoat,pim:Mode,pip:AngleOrLon};
      break;

    default:
      return;

  }

  // Post request
  PostBoatSetupOrder (_CurPlayer.CurBoat.IdBoat,verb,request);

  
}         

function PostBoatSetupOrder(idu, verb, orderdata)
{
  // Now Post the order
  $.post("/ws/boatsetup/"+verb+".php?selectidu"+ idu,
     "parms="+ JSON.stringify(orderdata),
    function(Data, TextStatus)
    {
      if (TextStatus == 'success')
      {

      }
      else
      {
        Alert(GetLocalizedString("BoatSetupError"))
      }
    });

}
          