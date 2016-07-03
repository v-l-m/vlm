//
// VLMBoat layer handling displaying vlm boats, traj
//

const BOAT_ICON=0;
const BOAT_WP_MARKER=1;

const VLM_COORDS_FACTOR=1000;

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

                // Fix Lon, and Lat scale
                Boat.VLMInfo.LON /= VLM_COORDS_FACTOR;
                Boat.VLMInfo.LAT /= VLM_COORDS_FACTOR;
                
                // update map is racing
                
                if (Boat.VLMInfo.RAC != "0")
                {
                  // Set Map Center to current boat position
                  var l = new OpenLayers.LonLat(Boat.VLMInfo.LON, Boat.VLMInfo.LAT).transform(MapOptions.displayProjection, MapOptions.projection);
                  
                  // Fix Me : find a way to use a proper zoom factor (dist to next WP??)
                  map.setCenter(l);
                  
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

                        DrawRaceGates(Boat.RaceInfo, Boat.VLMInfo.NWP);
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
  var Pos = new OpenLayers.Geometry.Point(Boat.VLMInfo.LON, Boat.VLMInfo.LAT);
  var PosTransformed = Pos.transform(MapOptions.displayProjection, MapOptions.projection)
  //WP Marker
  var WP = Boat.GetNextWPPosition();
  var WPTransformed = new OpenLayers.Geometry.Point(WP.Lon.Value,WP.Lat.Value).transform(MapOptions.displayProjection, MapOptions.projection);
  var UpdatedFeatures=[];
    
  // Remove features, before recreate and re-add
  // Can't figure how to move/update the features properly
  if (Boat.OLBoatFeatures.length !=0)
  {
    UpdatedFeatures.push(Boat.OLBoatFeatures[BOAT_ICON]);
    UpdatedFeatures.push(Boat.OLBoatFeatures[BOAT_WP_MARKER]);
    
    VLMBoatsLayer.removeFeatures(UpdatedFeatures);

    // Cleanup OLBoatFeatures
    Boat.OLBoatFeatures.length=0;
    
  }
  //if (Boat.OLBoatFeatures.length == 0)
  {
    // On first pass, create all markers
    // Boat Marker
    Boat.OLBoatFeatures.push( new OpenLayers.Feature.Vector(
      PosTransformed,
      {"Id":Boat.IdBoat},
      {externalGraphic: 'images/target.svg', graphicHeight: 64, graphicWidth: 64,rotation: Boat.VLMInfo.HDG}
      )
    );
    VLMBoatsLayer.addFeatures(Boat.OLBoatFeatures[BOAT_ICON]);

    
    Boat.OLBoatFeatures.push( new OpenLayers.Feature.Vector(
      WPTransformed,
      {},
      {externalGraphic: 'images/WP_Marker.gif', graphicHeight: 64, graphicWidth: 64}
      )
    );
    VLMBoatsLayer.addFeatures(Boat.OLBoatFeatures[BOAT_WP_MARKER]);

  }
  /*else
  {
    
    Boat.OLBoatFeatures[BOAT_ICON].style.rotation= Boat.VLMInfo.HDG;
    Boat.OLBoatFeatures[BOAT_ICON].move(PosTransformed);
    Boat.OLBoatFeatures[BOAT_WP_MARKER].move(WPTransformed);

    VLMBoatsLayer.addFeatures(UpdatedFeatures);

  };*/
  
}
// allow testing of specific renderers via "?renderer=Canvas", etc
var renderer = OpenLayers.Util.getParameters(window.location.href).renderer;
renderer = (renderer) ? [renderer] : OpenLayers.Layer.Vector.prototype.renderers;

var VectorStyles = new OpenLayers.Style(
  {
    strokeColor: "#00FF00",
    strokeOpacity: 1,
    strokeWidth: 3,
    fillColor: "#FF5500",
    fillOpacity: 0.5,
    
  },
  {
    rules:
    [
      new OpenLayers.Rule
          (
            {
              // a rule contains an optional filter
              filter: new OpenLayers.Filter.Comparison({
                  type: OpenLayers.Filter.Comparison.EQUAL_TO,
                  property: "type", // the "foo" feature attribute
                  value: "buoy"
              }),
              symbolizer:{
                // if a feature matches the above filter, use this symbolizer
                label : "${name}${Coords}",
                pointRadius: 6,
                pointerEvents: "visiblePainted",
                // label with \n linebreaks
                
                //fontColor: "${favColor}",
                fontSize: "1.5em",
                //fontFamily: "Courier New, monospace",
                //fontWeight: "bold",
                labelAlign: "left", //${align}",
                labelXOffset: "${xOffset}",
                labelYOffset: "-12",//${yOffset}",
                labelOutlineColor: "white",
                labelOutlineWidth: 2 
              }
            }
          ),
        new OpenLayers.Rule
          (
            {
              // a rule contains an optional filter
              filter: new OpenLayers.Filter.Comparison({
                  type: OpenLayers.Filter.Comparison.EQUAL_TO,
                  property: "type", // the "foo" feature attribute
                  value: "crossonce"
              }),
              symbolizer:{
                xOffset:1,
                yOffset:1,
                strokeColor:"black",
                strokeOpacity:0.5,
                strokeWidth:4,
                strokeDashstyle:"dashdot"
              }
            }
          ),
        
        new OpenLayers.Rule
          (
            {
              // a rule contains an optional filter
              filter: new OpenLayers.Filter.Comparison({
                  type: OpenLayers.Filter.Comparison.EQUAL_TO,
                  property: "type", // the "foo" feature attribute
                  value: "marker"
              }),
              symbolizer:{
                externalGraphic: "images/${BuoyName}",
                rotation:"${CrossingDir}",
                graphicWidth:48
              }
            }
          ),
        new OpenLayers.Rule
          (
            {
              // a rule contains an optional filter
              filter: new OpenLayers.Filter.Comparison({
                  type: OpenLayers.Filter.Comparison.EQUAL_TO,
                  property: "type", // the "foo" feature attribute
                  value: "NextGate"
              }),
              symbolizer:{
                strokeColor: "#FF0000",
                  strokeOpacity: 1,
                  strokeWidth: 3              
              }
            }
          ),
        new OpenLayers.Rule
          (
            {
              // a rule contains an optional filter
              filter: new OpenLayers.Filter.Comparison({
                  type: OpenLayers.Filter.Comparison.EQUAL_TO,
                  property: "type", // the "foo" feature attribute
                  value: "ValidatedGate"
              }),
              symbolizer:{
                strokeColor: "#0000FF",
                  strokeOpacity: 0.5,
                  strokeWidth: 3              
              }
            }
          ),
        new OpenLayers.Rule
          (
            {
              // a rule contains an optional filter
              filter: new OpenLayers.Filter.Comparison({
                  type: OpenLayers.Filter.Comparison.EQUAL_TO,
                  property: "type", // the "foo" feature attribute
                  value: "FutureGate"
              }),
              symbolizer:{
                strokeColor: "#FF0000",
                  strokeOpacity: 0.5,
                  strokeWidth: 3              
              }
            }
          ),
        new OpenLayers.Rule
            (
              {
                // a rule contains an optional filter
                elsefilter: true,
                symbolizer:{
                }
              }
          
            )


    ]
  }
);

var VLMBoatsLayer = new OpenLayers.Layer.Vector("Simple Geometry", {
    styleMap: new OpenLayers.StyleMap(VectorStyles),
    renderers: renderer
});

// Background load controller from ext html file
function GetBoatControllerPopup()
{
  $("#BoatController").load("BoatController.html")
  return '<div id="BoatController"></div>';
}

const WP_TWO_BUOYS =0
const WP_ONE_BUOY  =1
const WP_GATE_BUOY_MASK =0x000F
/* leave space for 0-15 types of gates using buoys
   next is bitmasks */
const WP_DEFAULT              = 0
const WP_ICE_GATE_N           = (1 <<  4)
const WP_ICE_GATE_S           = (1 <<  5)
const WP_ICE_GATE_E           = (1 <<  6)
const WP_ICE_GATE_W           = (1 <<  7)
const WP_GATE_KIND_MASK       = 0xFFF0
/* allow crossing in one direction only */
const WP_CROSS_CLOCKWISE      = (1 <<  8)
const WP_CROSS_ANTI_CLOCKWISE = (1 <<  9)
/* for future releases */
const WP_CROSS_ONCE           = (1 << 10)

 function DrawRaceGates(RaceInfo, NextGate)
 {

   // Loop all gates
   for (index in RaceInfo.races_waypoints)
   {
      // Draw a single race gates
      var WP = RaceInfo.races_waypoints[index];
      
      // Fix coords scales
      WP.longitude1/= VLM_COORDS_FACTOR;
      WP.latitude1/=VLM_COORDS_FACTOR;
      WP.longitude2/= VLM_COORDS_FACTOR;
      WP.latitude2/=VLM_COORDS_FACTOR;
      
      // Draw WP1
      AddBuoyMarker(VLMBoatsLayer, "WP"+index+" "+WP.libelle+'\n' , WP.longitude1, WP.latitude1);
      

      // Second buoy (if any)
      if ((WP.wpformat & WP_GATE_BUOY_MASK) == WP_TWO_BUOYS)
      {
        // Add 2nd buoy marker
        AddBuoyMarker(VLMBoatsLayer,"",WP.longitude2, WP.latitude2);
      }
      {
        // No Second buoy, compute segment end
        // Todo
      }

      // Draw Gate Segment
      AddGateSegment(VLMBoatsLayer,WP.longitude1, WP.latitude1, WP.longitude2, WP.latitude2, (NextGate==index),(index < NextGate),(WP.wpformat & WP_GATE_KIND_MASK));

   }
 }

function AddGateSegment(Layer,lon1, lat1, lon2, lat2, IsNextWP, IsValidated, GateType)
{
  var P1 = new OpenLayers.Geometry.Point(lon1,lat1);
  var P2 = new OpenLayers.Geometry.Point(lon2,lat2);
  var P1_PosTransformed = P1.transform(MapOptions.displayProjection, MapOptions.projection)
  var P2_PosTransformed = P2.transform(MapOptions.displayProjection, MapOptions.projection)
  var PointList = [];

  PointList.push(P1_PosTransformed);
  PointList.push(P2_PosTransformed);

  var Attr=null;

  if (IsNextWP)
  {
    Attr={type:"NextGate"};
  }
  else if (IsValidated)
  {
    Attr={type:"ValidatedGate"};
  }
  else
  {
    Attr={type:"FutureGate"};
  }
  var WP= new OpenLayers.Feature.Vector(
                new OpenLayers.Geometry.LineString(PointList),
                Attr
                ,null);
    
  Layer.addFeatures(WP);

  if (GateType != WP_DEFAULT)
  {
    // Debug testing of the geo calculation functions
    {
      // Rumb line LAX-JFK = 2164.6 nm
      var P1 = new Position(  -(118+(24/60)),33+ (57/60));
      var P2 = new Position (-(73+(47/60)),40+(38/60));
      console.log("loxo dist : " + P1.GetLoxoDist(P2));
      console.log("loxo angle: " + P1.GetLoxoCourse(P2));

    }
    var P1 = new Position(lon1,lat1); 
    var P2 = new Position(lon2,lat2);
    var MarkerDir = P1.GetLoxoCourse(P2);
    var MarkerPos = P1.ReachDistLoxo(P2,0.5);
    // Gate has special features, add markers
    if (GateType & WP_CROSS_ANTI_CLOCKWISE)
    {
      MarkerDir-=90;
      AddGateDirMarker(VLMBoatsLayer,MarkerPos.Lon.Value, MarkerPos.Lat.Value,MarkerDir);
    }
    else if (GateType & WP_CROSS_CLOCKWISE)
    {
      MarkerDir+=90;
      AddGateDirMarker(VLMBoatsLayer,"toto",MarkerPos.Lon.Value, MarkerPos.Lat.Value,MarkerDir);
    }

    if (GateType & WP_CROSS_ONCE)
    {
      // Draw the segment again as dashed line for cross once gates
      var WP= new OpenLayers.Feature.Vector(
                new OpenLayers.Geometry.LineString(PointList),
                {type:"crossonce"}
                ,null);
    
      Layer.addFeatures(WP);

    }

  }
  

}

 function AddGateDirMarker(Layer, Lon, Lat,Dir)
 {
    var MarkerCoords= new Position(Lon,Lat);    
    var MarkerPos = new OpenLayers.Geometry.Point(MarkerCoords.Lon.Value, MarkerCoords.Lat.Value);
    var MarkerPosTransformed = MarkerPos.transform(MapOptions.displayProjection, MapOptions.projection)
    var Marker= new OpenLayers.Feature.Vector(MarkerPosTransformed,
                                {
                                  "type": 'marker',
                                  "BuoyName" :"BuoyDir.png",
                                  "CrossingDir":Dir
                                }
                                );
    
    Layer.addFeatures(Marker);
 }


 function AddBuoyMarker(Layer, Name ,Lon, Lat)
 {
    var WP_Coords= new Position(Lon,Lat);    
    var WP_Pos = new OpenLayers.Geometry.Point(WP_Coords.Lon.Value, WP_Coords.Lat.Value);
    var WP_PosTransformed = WP_Pos.transform(MapOptions.displayProjection, MapOptions.projection)
    var WP= new OpenLayers.Feature.Vector(WP_PosTransformed,
                                          {
                                            "name":Name,
                                            "Coords": WP_Coords.ToString(),
                                            "type": 'buoy'
                                          }
                                          );
    
  Layer.addFeatures(WP);
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

  // Build WS command accoridng to required pilot mode
  switch (Mode)
  {
    case PM_HEADING:
    case PM_ANGLE:
      request={idu:_CurPlayer.CurBoat.IdBoat,pim:Mode,pip:AngleOrLon};
      break;

    case PM_ORTHO:
    case PM_VBVMG:
    case PM_VMG:
      request={idu:_CurPlayer.CurBoat.IdBoat,
                pim:Mode,
                pip:
                {
                  targetlong:parseFloat(AngleOrLon),
                  targetlat:parseFloat(Lat),
                  targetandhdg:WPAt
                }
              };
      //PostBoatSetupOrder (_CurPlayer.CurBoat.IdBoat,"target_set",request);
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
      if (Data.success)
      {

      }
      else
      {
        alert(GetLocalizedString("BoatSetupError") + '\n' + Data.error.code + " " + Data.error.msg)
      }
    });

}
          