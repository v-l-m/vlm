//
// VLMBoat layer handling displaying vlm boats, traj
//

const BOAT_ICON=0;
const BOAT_WP_MARKER=1;
const BOAT_TRACK = 2;
const BOAT_FORECAST_TRACK = 3;
const BOAT_POLAR = 4;

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

// Control to handle drag of User WP
var DrawControl = null ;

function SetCurrentBoat(Boat)
{
  CheckBoatRefreshRequired(Boat);
}

var LastRequestedBoat=-1;

function CheckBoatRefreshRequired(Boat)
{
  var CurDate = new Date();
  var NextUpdate = new Date(0);
  var NeedPrefsRefresh = typeof Boat.VLMInfo.AVG === "undefined";

  // Update preference screen according to current selected boat
  UpdatePrefsDialog(Boat);
  
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

                // force refresh of settings if was not initialized
                if (NeedPrefsRefresh)
                {
                  UpdatePrefsDialog(Boat);
                }
                
                // update map is racing
                
                if (Boat.VLMInfo.RAC != "0")
                {
                  // Set Map Center to current boat position
                  var l = new OpenLayers.LonLat(Boat.VLMInfo.LON, Boat.VLMInfo.LAT).transform(MapOptions.displayProjection, MapOptions.projection);
                  
                  // Fix Me : find a way to use a proper zoom factor (dist to next WP??)
                  map.setCenter(l);
                  
                  // Draw Boat, course, tracks....
                  DrawBoat(Boat);
                  
                  // Update Boat info in main menu bar
                  UpdateInMenuRacingBoatInfo(Boat);

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

                        // Update the racename display if needed
                        var RaceName = $("#RaceName").first();

                        if (typeof RaceName!="undefined" )
                        {
                          if (_CurPlayer.CurBoat == Boat)
                          {
                            $("#RaceName").text(Boat.RaceInfo.racename);
                          }  
                        }
                      }

                    );
                    
                  }


                  // Get boat track for the last 24h
                  var end = Math.floor(new Date()/1000.)
                  var start = end - 24*3600
                  $.get("/ws/boatinfo/tracks_private.php?idu="+Boat.IdBoat+"&idr="+Boat.VLMInfo.RAC+"&starttime="+start+"&endtime="+end,
                    function(result)
                    {
                      if (result.success)
                      {
                        Boat.Track.length=0;
                        for (index in result.tracks)
                        {
                          var P = new VLMPosition(result.tracks[index][1]/1000., result.tracks[index][2]/1000. )
                          Boat.Track.push(P);
                        }
                        DrawBoat(Boat)
                      }
                    }
                  )
                  
                }    
                else
                {
                  // Boat is not racing
                  //GetLastRacehistory();
                  UpdateInMenuDockingBoatInfo(Boat);
                }            
              }
              
              HidePb("#PbGetBoatProgress");
            }
          )

    
  }
}

function DrawBoat(Boat)
{
  var Pos = new OpenLayers.Geometry.Point( Boat.VLMInfo.LON, Boat.VLMInfo.LAT);
  var PosTransformed = Pos.transform(MapOptions.displayProjection, MapOptions.projection)
  //WP Marker
  var WP = Boat.GetNextWPPosition();
  var WPTransformed = new OpenLayers.Geometry.Point(WP.Lon.Value,WP.Lat.Value).transform(MapOptions.displayProjection, MapOptions.projection);
  var UpdatedFeatures=[];

  var ForecastPos = new VLMPosition (Boat.VLMInfo.LON, Boat.VLMInfo.LAT).ReachDistLoxo(12*Boat.VLMInfo.BSP*Boat.VLMInfo.VAC/3600,Boat.VLMInfo.HDG);
  var ForecastPosTransformed = new OpenLayers.Geometry.Point(ForecastPos.Lon.Value,ForecastPos.Lat.Value).transform(MapOptions.displayProjection, MapOptions.projection);
    
  // Remove features, before recreate and re-add
  // Can't figure how to move/update the features properly
  if (Boat.OLBoatFeatures.length !=0)
  {
    UpdatedFeatures.push(Boat.OLBoatFeatures[BOAT_ICON]);
    //UpdatedFeatures.push(Boat.OLBoatFeatures[BOAT_WP_MARKER]);
    UpdatedFeatures.push(Boat.OLBoatFeatures[BOAT_TRACK]);
    UpdatedFeatures.push(Boat.OLBoatFeatures[BOAT_FORECAST_TRACK]);
    UpdatedFeatures.push(Boat.OLBoatFeatures[BOAT_POLAR]);
    
    VLMBoatsLayer.removeFeatures(UpdatedFeatures);
    VLMDragLayer.removeFeatures(Boat.OLBoatFeatures[BOAT_WP_MARKER]);
    // Cleanup OLBoatFeatures
    Boat.OLBoatFeatures.length=0;
    
  }

  if (DrawControl==null)
  {
    DrawControl = new OpenLayers.Control.DragFeature(VLMDragLayer,{
                              /*onDrag: function(feature,pixel)
                                      {
                                        var i  = 0;
                                      },*/
                              onComplete: function(feature,pixel)
                                      {
                                        var dest = map.getLonLatFromPixel(pixel);
                                        var WGSDest = dest.transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));
                                        var PDest = new VLMPosition (WGSDest.lon, WGSDest.lat);
                                        
                                        // Use CurPlayer, since the drag layer is not associated to the proper boat
                                        SendVLMBoatWPPos(_CurPlayer.CurBoat,PDest)
                                      }
                                    }
    );
    map.addControl(DrawControl)
    DrawControl.activate();
    //Boat.DrawControl.modify.mode = OpenLayers.Control.ModifyFeature.DRAG;
  }
  
  
  
  // Boat Marker
  Boat.OLBoatFeatures.push( new OpenLayers.Feature.Vector(
    PosTransformed,
    {"Id":Boat.IdBoat},
    {externalGraphic: 'images/target.svg', graphicHeight: 64, graphicWidth: 64,rotation: Boat.VLMInfo.HDG}
    )
  );
  VLMBoatsLayer.addFeatures(Boat.OLBoatFeatures[BOAT_ICON]);

  // Waypoint marker    
  Boat.OLBoatFeatures.push( new OpenLayers.Feature.Vector(
    WPTransformed,
    {},
    {externalGraphic: 'images/WP_Marker.gif', graphicHeight: 64, graphicWidth: 64}
    )
  );

  VLMDragLayer.addFeatures(Boat.OLBoatFeatures[BOAT_WP_MARKER]);

  // Last 24h track  
  if (Boat.Track.length > 0)
  {
    var PointList = [];

    for (index in Boat.Track)
    {
      var P = Boat.Track[index];
      var P1 = new OpenLayers.Geometry.Point(P.Lon.Value,P.Lat.Value);
      var P1_PosTransformed = P1.transform(MapOptions.displayProjection, MapOptions.projection)

      PointList.push(P1_PosTransformed)

    }

    Boat.OLBoatFeatures[BOAT_TRACK]= new OpenLayers.Feature.Vector(
              new OpenLayers.Geometry.LineString(PointList),
              {
                "type":"HistoryTrack",
                "TrackColor":"#"+Boat.VLMInfo.COL
              });
  
    VLMBoatsLayer.addFeatures(Boat.OLBoatFeatures[BOAT_TRACK]);
  }
  else
  {
    // Add single point out of the map for later having a feature to remove
    var PointList = [];

    var P = new VLMPosition(-180,90);
    var P1 = new OpenLayers.Geometry.Point(P.Lon.Value,P.Lat.Value);
    var P1_PosTransformed = P1.transform(MapOptions.displayProjection, MapOptions.projection)

    PointList.push(PosTransformed)

  
    Boat.OLBoatFeatures[BOAT_TRACK]= new OpenLayers.Feature.Vector(
              new OpenLayers.Geometry.LineString(PointList),
                  {
                    "type":"HistoryTrack",
                    "TrackColor":"#"+Boat.VLMInfo.COL
                  });
  
    VLMBoatsLayer.addFeatures(Boat.OLBoatFeatures[BOAT_TRACK]);
  }

  // Forecast Track
  var TrackPointList=[];
  TrackPointList.push(P1_PosTransformed);
  TrackPointList.push(ForecastPosTransformed);

  Boat.OLBoatFeatures[BOAT_FORECAST_TRACK]= new OpenLayers.Feature.Vector(
              new OpenLayers.Geometry.LineString(TrackPointList),
              {
                "type":"ForecastPos"
              });
  
    VLMBoatsLayer.addFeatures(Boat.OLBoatFeatures[BOAT_FORECAST_TRACK]);

  // Draw polar
  var PolarPointList = PolarsManager.GetPolarLine(Boat.VLMInfo.POL, Boat.VLMInfo.BSP, DrawBoat, Boat);
  var Polar=[];

  // MakePolar in a 200x200 square
  //var BoatPosPixel = map.getPixelFromLonLat(new OpenLayers.LonLat(Boat.VLMInfo.LON, Boat.VLMInfo.LAT));
  var BoatPosPixel = map.getViewPortPxFromLonLat(PosTransformed);
  var scale = 50 * map.resolution;
  for (index in PolarPointList)
  {
    var Alpha=5*Math.floor(index);
    var Speed = parseFloat(PolarPointList[index]);

    var PixPos = new OpenLayers.Geometry.Point(
                      PosTransformed.x + Math.sin(Deg2Rad(Alpha+ Boat.VLMInfo.TWD))*scale*Speed,
                      PosTransformed.y + Math.cos(Deg2Rad(Alpha+ Boat.VLMInfo.TWD))*scale*Speed);

    //var P = map.getLonLatFromPixel(PixPos);
    //var PPoint = new OpenLayers.Geometry.Point(PixPos);
    Polar.push(PixPos);
  }
  Boat.OLBoatFeatures[BOAT_POLAR]= new OpenLayers.Feature.Vector(
              new OpenLayers.Geometry.LineString(Polar),
                  {
                    "type":"Polar",
                    "WindDir":Boat.VLMInfo.TWD
                  });
  
  VLMBoatsLayer.addFeatures(Boat.OLBoatFeatures[BOAT_POLAR]);

  
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
                //pointRadius: 6,
                pointerEvents: "visiblePainted",
                // label with \n linebreaks
                
                //fontColor: "${favColor}",
                fontSize: "1.5em",
                //fontFamily: "Courier New, monospace",
                //fontWeight: "bold",
                labelAlign: "left", //${align}",
                labelXOffset: "4",//${xOffset}",
                labelYOffset: "-12",//${yOffset}",
                //labelOutlineColor: "white",
                //labelOutlineWidth: 2
                externalGraphic:"images/${GateSide}",
                graphicWidth:48,
                fillOpacity:1

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
                externalGraphic: "images/BuoyDirs/${BuoyName}",
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
              filter: new OpenLayers.Filter.Comparison({
                  type: OpenLayers.Filter.Comparison.EQUAL_TO,
                  property: "type", // the "foo" feature attribute
                  value: "ForecastPos"
              }),
              symbolizer:{
                  strokeColor:"black",
                  strokeOpacity:0.75,
                  strokeWidth:1,
                  strokeDashstyle:"dot"
                              
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
                  value: "HistoryTrack"
              }),
              symbolizer:{
                  strokeOpacity:0.5,
                  strokeWidth:2,
                  strokeColor:"${TrackColor}"
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
                  value: "Polar"
              }),
              symbolizer:{
                  strokeColor:"white",
                  strokeOpacity:0.75,
                  strokeWidth:2
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

var LayerListeners = {
    featureclick: function(e) {
        console.log(e.object.name + " says: " + e.feature.id + " clicked.");
        return false;
    },
    nofeatureclick: function(e) {
        console.log(e.object.name + " says: No feature clicked.");
    }
};

var VLMBoatsLayer = new OpenLayers.Layer.Vector("VLM Boats and tracks", {
    styleMap: new OpenLayers.StyleMap(VectorStyles),
    renderers: renderer
});

var VLMDragLayer = new OpenLayers.Layer.Vector("VLM Waypoints", {
    styleMap: new OpenLayers.StyleMap(VectorStyles),
    renderers: renderer,
    eventListeners: LayerListeners
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
      
      var cwgate = !(WP.wpformat & WP_CROSS_ANTI_CLOCKWISE);

      // Draw WP1
      AddBuoyMarker(VLMBoatsLayer, "WP"+index+" "+WP.libelle+'\n' , WP.longitude1, WP.latitude1, cwgate);
      

      // Second buoy (if any)
      if ((WP.wpformat & WP_GATE_BUOY_MASK) == WP_TWO_BUOYS)
      {
        // Add 2nd buoy marker
        AddBuoyMarker(VLMBoatsLayer,"",WP.longitude2, WP.latitude2, ! cwgate);
      }
      else
      {
        // No Second buoy, compute segment end
        var P = new VLMPosition(WP.longitude1, WP.latitude1);
        var Dest = P.ReachDistLoxo(2500,180 + parseFloat( WP.laisser_au));
        WP.longitude2=Dest.Lon.Value;
        WP.latitude2 = Dest.Lat.Value;
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
    /*{
      // Rumb line LAX-JFK = 2164.6 nm
      var P1 = new Position(  -(118+(24/60)),33+ (57/60));
      var P2 = new Position (-(73+(47/60)),40+(38/60));
      console.log("loxo dist : " + P1.GetLoxoDist(P2));
      console.log("loxo angle: " + P1.GetLoxoCourse(P2));

    }*/
    var P1 = new VLMPosition(lon1,lat1); 
    var P2 = new VLMPosition(lon2,lat2);
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
      AddGateDirMarker(VLMBoatsLayer,MarkerPos.Lon.Value, MarkerPos.Lat.Value,MarkerDir);
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

const MAX_BUOY_INDEX=16;
var BuoyIndex = Math.floor(Math.random()*MAX_BUOY_INDEX);
 function AddGateDirMarker(Layer, Lon, Lat,Dir)
 {
    var MarkerCoords= new VLMPosition(Lon,Lat);    
    var MarkerPos = new OpenLayers.Geometry.Point(MarkerCoords.Lon.Value, MarkerCoords.Lat.Value);
    var MarkerPosTransformed = MarkerPos.transform(MapOptions.displayProjection, MapOptions.projection)
    var Marker= new OpenLayers.Feature.Vector(MarkerPosTransformed,
                                {
                                  "type": 'marker',
                                  "BuoyName" :"BuoyDir"+BuoyIndex+".png",
                                  "CrossingDir":Dir
                                }
                                );
    // Rotate buoys...
    BuoyIndex++;
    BuoyIndex%=(MAX_BUOY_INDEX+1);
    
    Layer.addFeatures(Marker);
 }


 function AddBuoyMarker(Layer, Name ,Lon, Lat,CW_Crossing)
 {
    var WP_Coords= new VLMPosition(Lon,Lat);    
    var WP_Pos = new OpenLayers.Geometry.Point(WP_Coords.Lon.Value, WP_Coords.Lat.Value);
    var WP_PosTransformed = WP_Pos.transform(MapOptions.displayProjection, MapOptions.projection)
    var WP;
    
    if (CW_Crossing)
    {
      WP= new OpenLayers.Feature.Vector(WP_PosTransformed,
                                          {
                                            "name":Name,
                                            "Coords": WP_Coords.ToString(),
                                            "type": 'buoy',
                                            "GateSide":"Buoy1.png"
                                          }
                                          );
    }
    else
    {
      WP = new OpenLayers.Feature.Vector(WP_PosTransformed,
                                          {
                                            "name":Name,
                                            "Coords": WP_Coords.ToString(),
                                            "type": 'buoy',
                                            "GateSide":"Buoy2.png"
                                          }
                                          );
    }
    
    
    Layer.addFeatures(WP);
 }
     
const PM_HEADING=1;
const PM_ANGLE=2;
const PM_ORTHO=3;
const PM_VMG=4;
const PM_VBVMG=5;

function SendVLMBoatWPPos(Boat,P)
{
  var orderdata = {
      idu:Boat.IdBoat,
      pip:{
        targetlat:P.Lat.Value,
        targetlong:P.Lon.Value,
        targetandhdg:-1 //Boat.VLMInfo.H@WP
      }

  }
  
  PostBoatSetupOrder (Boat.IdBoat,'target_set',orderdata);
}

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
          // TODO : Force reload of boat info from server after successfull post.
      }
      else
      {
        alert(GetLocalizedString("BoatSetupError") + '\n' + Data.error.code + " " + Data.error.msg)
      }
    });

}

function EngageBoatInRace(RaceID,BoatID)
{
  $.post("/ws/boatsetup/race_subscribe.php",
    "parms="+JSON.stringify(
        { 
          idu:BoatID,
          idr:parseInt(RaceID)
        }
      ),
    function(data)
    {
      var i = 0;
    }
  );
}
          

