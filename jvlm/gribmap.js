/*
/* Contributors : paparazzia@gmail.com, ...
 * Code is licencesed under the AGPL license
 * See Copying file
 */

/* TODO:
 * this is rough draft
 * - for better drawing an BC compatibility with OL, the Grimap layer should use the renderer or the vector base layer (?)
 * - windarea are stored in an objet (pseudo key=>value array) => should be stored in arrays with indexes
 */

/**
 * @requires OpenLayers.js 
 * @requires ControlSwitch.js
 * @requires OpenLayers/Pixel.js
 */


Gribmap = {}; //Module container
ErrorCatching = -1; //DEBUG: Set this > 0 to catch pixel out of wind grid

//FIXME : should use a config file ?
Gribmap.windgrid_uribase = "/ws/windinfo/windgrid.php";
Gribmap.griblist_uribase = '/ws/windinfo/list.php';

// Class wind info - just a basic vector
function Wind(wspeed, wheading) {
    this.wspeed   = wspeed;
    this.wheading = wheading;
}

// Normalize longitude -180 < long <= 180
function normalizeLongitude0(longitude) {
    var l;
    l = longitude % 360.0;
    if (l > 180.0) {
        l -= 360.0;
    } else if (l <= -180.0) {
        l += 360.0;
    }
    return l;
}

// Normalize longitude 0 <= long < 360
function normalizeLongitude360(longitude) {
    var l;
    l = longitude % 360.0;
    if (l < 0.0) {
  l += 360.0;
    }
    return l;
}

// point in the canvas - subclassed from OL.Pixel to make transform easier
Gribmap.Pixel = OpenLayers.Class(OpenLayers.Pixel, {
    moveBy: function(offset) {
        //this is the same as the base.offset() func, but without cloning the object
        this.x += offset.x;
        this.y += offset.y;
    },

    moveByPolar: function(ro, theta) {
        var angle = (theta-90.0)*Math.PI/180.0;
        this.x += ro*Math.cos(angle);
        this.y += ro*Math.sin(angle);
    },
    
    CLASS_NAME: "Gribmap.Pixel"
});


//Store information (windAreas, i.e. bloc of grib datas)
Gribmap.WindLevel = OpenLayers.Class({
    basestep: 0.5,
    griblevel: 0,
    blocx: 360,
    blocy: 180,
    step: 2.0,
    stepmultiple: 4.0,
    windAreas: {},
    layer: null,

    initialize: function(griblevel, stepmultiple, blocx, blocy, layer) {
        this.griblevel = griblevel;
        this.windAreas = new Array();
        this.stepmultiple = stepmultiple;
        this.step = this.basestep*stepmultiple; //FIXME: useless without proper step handling
        this.blocx = blocx;
        this.blocy = blocy;
        this.layer = layer;
    },

    //FIXME : should we use native index in place of hash array ?
    getGribLeftLimit: function(lon) {
        return this.getGribLeftId(lon)*this.blocx-180;
    },

    getGribLeftId: function(lon) {
        return Math.floor((lon+180)/this.blocx);
    },
    
    getGribBottomLimit: function(lat) {
        return this.getGribBottomId(lat)*this.blocy-90;
    },
    
    getGribBottomId: function(lat) {
        return Math.floor((lat+90)/this.blocy);
    },

    //Get all wind areas inside bounds, and call checkWindArea() for each
    getWindAreas: function(bounds) {
        //bounds in LAT-LON
        var blocks = new Array();
        var bc = 0;
        var left = this.getGribLeftLimit(bounds.left);
        var bottom = null;
        var newblock = null;
        
        while (left < bounds.right) {
            bottom = this.getGribBottomLimit(bounds.bottom);
            while (bottom < bounds.top) {
                newblock = new Gribmap.WindArea(left, bottom, this);
                blocks[bc] = this.checkWindArea(newblock);
                bottom += this.blocy;
                bc += 1;
            }
            left += this.blocx;
        }
        return blocks;
    },

    checkWindArea: function(windarea) {
        if (typeof (this.windAreas[windarea.toString()]) == 'undefined') {
            //Unknown windarea, we just use it.
            this.windAreas[windarea.toString()] = windarea;
        } else {
            windarea = this.windAreas[windarea.toString()];
        }
        //FIXME : better test ?
        if (this.layer.gribtimeBefore != 0) {
            windarea.checkWindArray(this.layer.gribtimeBefore);
            windarea.checkWindArray(this.layer.gribtimeAfter);
        }
        return windarea;
    },
    
    getWindInfo: function(lat, lon) {
        var left = this.getGribLeftLimit(lon);
        var bottom = this.getGribBottomLimit(lat);
        var wa = new Gribmap.WindArea(left, bottom, this);
        //on n'appelle pas checkWindArea car on suppose que c'est déjà OK.
        //mais on mets ça dans une clausse d'exception pour ne pas avoir de soucis
        try {
            return(this.windAreas[wa.toString()].getWindInfo(lat, lon, this.layer.time, this.layer.gribtimeBefore, this.layer.gribtimeAfter));
        } catch (error) {
            return null;
        }
    },

    CLASS_NAME: "Gribmap.WindLevel"

});


//Wind array container
Gribmap.WindArray = OpenLayers.Class({
    time: null,
    winddatas: null,
    status: 'void',
    windArea: null, //for back notification after loading

    initialize: function(time, windArea) {
        this.status = 'void',
        this.time = time;
        this.windArea = windArea;
    },
    
    isLoaded: function() {
        return (this.status == 'loaded');
    },

    isLoading: function() {
        return (this.status == 'loading');
    },

    handleWindGridReply: function(request) {
        if (request.status == 200) {
            var jsonArray;
            jsonArray = JSON.parse(request.responseText);
            this.winddatas = this.transformRawWindArray(jsonArray);
            this.status = 'loaded';
            //FIXME
            if (this.windArea != null) this.windArea.redraw();
        } else {
            this.status = "void";
        }
    },
  
    // transform the information retrieved in JSON form the wind service
    // in VLM into a two-dimensional pseudo-array
    // parameter:
    // jsonArray, the raw array in JSON
    // return:
    // a two dimensional pseudo-array with index step 0.5
    transformRawWindArray: function(jsonArray) {
        var wind_array = new Array();
        var windNodeIdx, windNode, windInfo;

        for (windNodeIdx in jsonArray) {
            windNode = jsonArray[windNodeIdx];
            if (typeof (wind_array[windNode.lat]) == 'undefined') {
                wind_array[windNode.lat] = new Array();
            }
            windInfo = new Wind(windNode.wspd, windNode.whdg);
            wind_array[windNode.lat][windNode.lon] = windInfo;
            if (windNode.lon == 180.0) {
                wind_array[windNode.lat][-windNode.lon] = windInfo;
            }      
        }
        return wind_array;
    },

    getWindGrid: function() {
        if (this.isLoaded() || this.isLoading()) return;
        this.status = 'loading';

        var request = OpenLayers.Request.GET({
            url: Gribmap.windgrid_uribase,
            params: { north: this.windArea.top, south: this.windArea.bottom, east: this.windArea.right, west: this.windArea.left,
                      timerequest: this.time, stepmultiple: this.windArea.windlevel.stepmultiple},
            async: true,
            headers: {
                'Accept' : 'application/json',
            },
            callback: this.handleWindGridReply,
            scope: this,
        });
    },


    CLASS_NAME: "Gribmap.WindArray"

});

/* Class: WindArea
 */

Gribmap.WindArea = OpenLayers.Class(OpenLayers.Bounds, {
    windlevel : null, //pointer to WindLevel ?
    windArrays : null,

    initialize: function(left, bottom, windlevel) {
        this.windlevel = windlevel;
        this.windArrays = new Array();
        this.left = left;
        this.bottom = bottom
        this.right = left+windlevel.blocx
        this.top = bottom+windlevel.blocy;
    },

    redraw: function() {
        if (    (this.windlevel != null)
              && this.isLoaded(this.windlevel.layer.gribtimeBefore)
              && this.isLoaded(this.windlevel.layer.gribtimeAfter)
                ) {
            this.windlevel.layer.redraw();
        }
    },

    checkWindArray: function(ts) {
        if (this.exists(ts)) return;
        this.windArrays[ts] = new Gribmap.WindArray(ts, this);
        this.windArrays[ts].getWindGrid();
    },
    
    exists: function(ts) {
        return (typeof (this.windArrays[ts]) != 'undefined');
    },
    
    isLoaded: function(ts) {
        return (this.exists(ts) && this.windArrays[ts].isLoaded());
    },

    isLoading: function(ts) {
        return (this.exists(ts) && this.windArrays[ts].isLoading());
    },

    toString: function() {   
        return 'gribresol=('+this.windlevel['griblevel']+") "+OpenLayers.Bounds.prototype.toString.apply(this, arguments);
    },

    getWindInfo: function(lat, lon, time, time_ante, time_post) {
        //FIXME should clean all these API
        return this.getWindInfo2(lat, lon, time, this.windArrays[time_ante], this.windArrays[time_post]);
    },

    getWindInfo2: function(lat, lon, time, windarray_ante, windarray_post) {
        //You should be sure before calling this that all the grib data you need are already loaded.
        var ne_wind, nw_wind, se_wind, sw_wind;
        var s_limit, n_limit, e_limit, w_limit;
        var n_wspeed, s_wspeed, wspeed, wspeed_ante, wspeed_post;
        var t_angle1, t_angle2, wangle, t_val1, t_val2;
        var n_u, n_v, s_u, s_v, u_ante, v_ante, u_post, v_post, u, v;
        var stepwind = this.windlevel.step;
        var timecoeff, loncoeff, latcoeff;

        //Normalisation & coeff
        lon = normalizeLongitude0(lon);

        s_limit = Math.floor(lat/stepwind)*stepwind;
        n_limit = Math.ceil(lat/stepwind)*stepwind;
        w_limit = Math.floor(lon/stepwind)*stepwind;
        e_limit = Math.ceil(lon/stepwind)*stepwind;

        loncoeff = (lon-w_limit)/stepwind;
        latcoeff = (lat-s_limit)/stepwind;

        //ANTE

        //4 corners
        ne_wind = windarray_ante.winddatas[n_limit][e_limit];
        nw_wind = windarray_ante.winddatas[n_limit][w_limit];
        se_wind = windarray_ante.winddatas[s_limit][e_limit];
        sw_wind = windarray_ante.winddatas[s_limit][w_limit];

        //Windspeed : linear north, linear south, then linear        
        n_wspeed = nw_wind.wspeed + loncoeff*(ne_wind.wspeed-nw_wind.wspeed);
        s_wspeed = sw_wind.wspeed + loncoeff*(se_wind.wspeed-sw_wind.wspeed);
        wspeed_ante = s_wspeed + latcoeff*(n_wspeed-s_wspeed);

        //radians
        t_angle1 = nw_wind.wheading*Math.PI/180.0;
        t_angle2 = ne_wind.wheading*Math.PI/180.0;
        t_val1 = nw_wind.wspeed*Math.cos(t_angle1);
        t_val2 = ne_wind.wspeed*Math.cos(t_angle2);
        n_u = t_val1 + loncoeff*(t_val2 - t_val1);
        t_val1 = nw_wind.wspeed*Math.sin(t_angle1);
        t_val2 = ne_wind.wspeed*Math.sin(t_angle2);
        n_v = t_val1 + loncoeff*(t_val2 - t_val1);
        
        t_angle1 = sw_wind.wheading*Math.PI/180.0;
        t_angle2 = se_wind.wheading*Math.PI/180.0;
        t_val1 = sw_wind.wspeed*Math.cos(t_angle1);
        t_val2 = se_wind.wspeed*Math.cos(t_angle2);
        s_u = t_val1 + loncoeff*(t_val2 - t_val1);
        t_val1 = sw_wind.wspeed*Math.sin(t_angle1);
        t_val2 = se_wind.wspeed*Math.sin(t_angle2);
        s_v = t_val1 + loncoeff*(t_val2 - t_val1);

        u_ante = s_u + latcoeff*(n_u-s_u);
        v_ante = s_v + latcoeff*(n_v-s_v);

        //POST

        //4 corners
        ne_wind = windarray_post.winddatas[n_limit][e_limit];
        nw_wind = windarray_post.winddatas[n_limit][w_limit];
        se_wind = windarray_post.winddatas[s_limit][e_limit];
        sw_wind = windarray_post.winddatas[s_limit][w_limit];

        //Windspeed : linear north, linear south, then linear        
        n_wspeed = nw_wind.wspeed + loncoeff*(ne_wind.wspeed-nw_wind.wspeed);
        s_wspeed = sw_wind.wspeed + loncoeff*(se_wind.wspeed-sw_wind.wspeed);
        wspeed_post = s_wspeed + latcoeff*(n_wspeed-s_wspeed);

        //radians
        t_angle1 = nw_wind.wheading*Math.PI/180.0;
        t_angle2 = ne_wind.wheading*Math.PI/180.0;
        t_val1 = nw_wind.wspeed*Math.cos(t_angle1);
        t_val2 = ne_wind.wspeed*Math.cos(t_angle2);
        n_u = t_val1 + loncoeff*(t_val2 - t_val1);
        t_val1 = nw_wind.wspeed*Math.sin(t_angle1);
        t_val2 = ne_wind.wspeed*Math.sin(t_angle2);
        n_v = t_val1 + loncoeff*(t_val2 - t_val1);
        
        t_angle1 = sw_wind.wheading*Math.PI/180.0;
        t_angle2 = se_wind.wheading*Math.PI/180.0;
        t_val1 = sw_wind.wspeed*Math.cos(t_angle1);
        t_val2 = se_wind.wspeed*Math.cos(t_angle2);
        s_u = t_val1 + loncoeff*(t_val2 - t_val1);
        t_val1 = sw_wind.wspeed*Math.sin(t_angle1);
        t_val2 = se_wind.wspeed*Math.sin(t_angle2);
        s_v = t_val1 + loncoeff*(t_val2 - t_val1);

        u_post = s_u + latcoeff*(n_u-s_u);
        v_post = s_v + latcoeff*(n_v-s_v);

        //Interpolation temporelle
        timecoeff = (time-windarray_ante.time)/(windarray_post.time-windarray_ante.time);
        wspeed = wspeed_ante + timecoeff*(wspeed_post-wspeed_ante);
        u = u_ante + timecoeff*(u_post-u_ante);
        v = v_ante + timecoeff*(v_post-v_ante);
        wangle = 180.0*Math.acos(u/Math.sqrt(u*u+v*v))/Math.PI;
        if (v < 0) {
            wangle = 360.0 - wangle;
        }

        return new Wind(wspeed, wangle);
    },
    
    CLASS_NAME: "Gribmap.WindArea"
});

/* Class: Gribmap.
Layer
 * 
 * Inherits from:
 *  - <OpenLayers.Layer>
 */
Gribmap.Layer = OpenLayers.Class(OpenLayers.Layer, {

  /* APIProperty: isBaseLayer 
   * {Boolean} Gribmap layer is never a base layer.  
   */
  isBaseLayer: false,

  /* Property: canvas
   * {DOMElement} Canvas element.
   */
  canvas: null,
  
  /* List of windLevels */
  windLevels: [],

  /* define pixel grid */
  arrowstep: 48,

  /* offset from now */
  timeoffset: 0,
  
  /* current grib timestamp */
  time: 0,
  gribtimeBefore: 0,
  gribtimeAfter: 0,

  /* Property: griblist
   * List of timestamp for gribs
   */  
  griblist: null,

  /* Constructor: Gribmap.Layer
   * Create a gribmap layer.
   *
   * Parameters:
   * name - {String} Name of the Layer
   * options - {Object} Hashtable of extra options to tag onto the layer
   */
  initialize: function(name, options) {
      var i;
      OpenLayers.Layer.prototype.initialize.apply(this, arguments);
      
      this.getGribList(); //Async call

      //init resolutions      
      this.windLevels[0] = new Gribmap.WindLevel(0,   4,  120,  60, this);
      this.windLevels[1] = new Gribmap.WindLevel(1,   2,  60,  30, this);
      this.windLevels[2] = new Gribmap.WindLevel(2,   1,  20,  20, this);

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

  },

  //Time management
  addTimeOffset: function(delta) {
      this.timeoffset += delta;
      this.setTimeSegmentFromOffset();
//      this.redraw();
  },
  
  timereset: function() {
      this.addTimeOffset(-this.timeoffset);
  },

  timeforward: function() {
      this.addTimeOffset(3600);
  },
  
  timebackward: function() {
      this.addTimeOffset(-3600);
  },

  getGribList: function() {
      var request = OpenLayers.Request.GET({
          url: Gribmap.griblist_uribase,
          async: true,
          headers: {
              'Accept' : 'application/json',
          },
          callback: this.handleGribListReply,
          scope: this,
      });
  },

  handleGribListReply: function( request ) {
      if (request.status == 200) {
          this.griblist = JSON.parse(request.responseText);
          this.maxtime = Math.max.apply(null, this.griblist);
          this.mintime = Math.min.apply(null, this.griblist);
      }
      var now = new Date();
      this.setTimeSegment( now.getTime()/1000.0);
  },

  setTimeSegmentFromOffset: function() {
      var now = new Date();
      this.setTimeSegment(now.getTime()/1000+this.timeoffset);
  },

  setTimeSegment: function(time) {
      time = Math.floor(time);
      var i = 0;
      var gribtimebefore = this.mintime;
      var gribtimeafter = this.maxtime;
      for (i = 0; i < this.griblist.length; i++) {
          delta = this.griblist[i]-time
          if ( this.griblist[i] >= gribtimebefore && this.griblist[i] <= time) {
              gribtimebefore = this.griblist[i];
          }
          if ( this.griblist[i] <= gribtimeafter && this.griblist[i] >= time) {
              gribtimeafter = this.griblist[i];
          }
      }
      this.gribtimeBefore = gribtimebefore;
      this.gribtimeAfter = gribtimeafter;
      this.time = time;
      this.redraw();
  },

  setGribLevel: function(bounds) {
      //bounds in LATLON
      var i;
      widthlon = Math.abs(bounds.left - bounds.right);
      heightlat = Math.abs(bounds.top - bounds.bottom);
      for (i=this.windLevels.length-1; i >= 0; i--) {
          if ( (widthlon < 2*this.windLevels[i].blocx) && (heightlat < 2*this.windLevels[i].blocy) ) break;
      }
      this.gribLevel = Math.max(i,0);
      return(i);
  },

  windAtPosition: function(latlon) {
      return this.windLevels[this.gribLevel].getWindInfo(latlon.lat, latlon.lon);
  },

  /** 
   * Method: moveTo
   *
   * Parameters:
   * bounds - {<OpenLayers.Bounds>} 
   * zoomChanged - {Boolean} 
   * dragging - {Boolean} 
   */
  moveTo: function(bounds, zoomChanged, dragging) {
      var windarea, bounds;

      OpenLayers.Layer.prototype.moveTo.apply(this, arguments);

      // The code is currently too slow to update the rendering during dragging.
      if (dragging) return;

      //define region in pixel and in lat/lon
      var posstart = this.map.getLayerPxFromLonLat(new OpenLayers.LonLat(bounds.left, bounds.top));
      var poslimit = this.map.getLayerPxFromLonLat(new OpenLayers.LonLat(bounds.right, bounds.bottom));
      poslimit.x -= posstart.x;
      poslimit.y -= posstart.y;
      
      var boundsLonLat = bounds.transform(
                    new OpenLayers.Projection("EPSG:900913"), // from Spherical Mercator Projection
                    new OpenLayers.Projection("EPSG:4326") // transform to WGS 1984
                    );
                    
      //canvas object
      var ctx = this.canvas.getContext('2d');
      
      // Unfortunately OpenLayers does not currently support layers that
      // remain in a fixed position with respect to the screen location
      // of the base layer, so this puts this layer manually back into
      // that position using one point's offset as determined earlier.
      ctx.canvas.style.left = (posstart.x) + 'px';
      ctx.canvas.style.top = (posstart.y) + 'px';
      ctx.canvas.width = poslimit.x;
      ctx.canvas.height = poslimit.y;

      //Fix some feature of the canvas
      this.drawContext(ctx);

      //Get griblevel // FIXME : should use the native zoom level
      this.setGribLevel(boundsLonLat);        
//      if (this.gribLevel > 0) {
            //get windareas for current griblevel
            var bl = this.windLevels[this.gribLevel].getWindAreas(boundsLonLat);
/*            if (this.gribLevel == 1) alert('griblevel 1');
        } else {
          //Currently, we don't handle the multireso case
          ctx.canvas.width = 0;
          ctx.canvas.height = 0;
          return;
      }*/

      for (i = 0; i < bl.length; i++) {

          windarea = bl[i]; //la zone

          if (!windarea.isLoaded(this.gribtimeBefore) || !windarea.isLoaded(this.gribtimeAfter)) continue; //pas chargé, on passe

          //Passe en sphérique
          bounds = windarea.clone();
          bounds.transform(
                    new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
                    new OpenLayers.Projection("EPSG:900913") // to Spherical Mercator Projection
                    );

          //passe en pixel
          start = this.map.getLayerPxFromLonLat(new OpenLayers.LonLat(bounds.left, bounds.top));
          end = this.map.getLayerPxFromLonLat(new OpenLayers.LonLat(bounds.right, bounds.bottom));

          //réaligne le premier pixel de la zone
          start.x -= posstart.x;
          start.y -= posstart.y;
          end.x -= posstart.x;
          end.y -= posstart.y;

          //aligne le début des flêches a un multiple de la grille
          start.x = Math.ceil(start.x/this.arrowstep)*this.arrowstep;
          start.y = Math.ceil(start.y/this.arrowstep)*this.arrowstep;

          //On trace sur une partie visible
          if (start.x < 0) start.x = 0;
          if (start.y < 0) start.y = 0;
          if (end.x > poslimit.x) end.x = poslimit.x;
          if (end.y > poslimit.y) end.y = poslimit.y;

          //tracé proprement dit
          this.drawWindArea(start, end, windarea, ctx);
       }
  },

  drawWindArea: function(p, poslimit, windarea, ctx) {
      var bstep = this.arrowstep;
      var wante = windarea.windArrays[this.gribtimeBefore];
      var wpost = windarea.windArrays[this.gribtimeAfter];

      //FIXME: faire un bench pour comparer le cas de re création d'objet Pixel()    
      
      while (p.x < poslimit.x) {
          p.y = 0; //FIXME: pourquoi 0 ? on devrait stocker p.y et le réinjecter...
          while (p.y < poslimit.y) {
              //passage du pixel en latlon (géographique)
              LonLat = this.map.getLonLatFromPixel(p).transform(
                    new OpenLayers.Projection("EPSG:900913"), // from Spherical Mercator Projection
                    new OpenLayers.Projection("EPSG:4326") // transform to WGS 1984
                    );

              //Récupère le vent et l'affiche en l'absence d'erreur
              try {
                  winfo = windarea.getWindInfo2(LonLat.lat, LonLat.lon, this.time, wante, wpost);
                  this.drawWind(ctx, p.x, p.y, winfo);
              } catch (error) {
                  if (ErrorCatching > 0) {
                      alert(LonLat+" / "+winfo.wspeed+" / "+winfo.wheading);
                      ErrorCatching -= 1;
                  }
              }
              p.y += bstep;
          }
          p.x += bstep;
      }
  },     

  // return the color based on the wind speed
  // parameters:
  // wspeed: the wind speed.
  windSpeedToColor: function(wspeed) {
      if (wspeed <= 10.0) {
          if (wspeed <= 3.0) {
              if (wspeed <=  1.0) { return '#FFFFFF'; } else { return '#9696E1'; }
          } else {
              if (wspeed <=  6.0) { return '#508CCD'; } else { return '#3C64B4'; }
          }
      } else {
          if (wspeed <= 33.0) {
              if (wspeed <= 21.0) {
                  if (wspeed <= 15.0) { return '#41B464'; } else { return '#B4CD0A'; }
              } else {
                  if (wspeed <= 26.0) { return '#D2D216'; } else { return '#E1D220'; }
              }
          } else {
              if (wspeed <= 40.0) { return '#FFB300'; }
              if (wspeed <= 47.0) { return '#FF6F00'; }
              if (wspeed <= 55.0) { return '#FF2B00'; }
              if (wspeed <= 63.0) { return '#E60000'; }
          }
      }
      return '#7F0000';
  },

  drawWindTriangle: function(context, x, y, pos_wind) {
      var a, b, c, bary, offset;
      var wheading;
      var wspdlog;

      windarrow_minsize = 4; // FIXME external constants ?
      windarrow_minwidth = 0;

      wspdlog = Math.log(pos_wind.wspeed+1);
      wheading = (pos_wind.wheading + 180.0) % 360.0

      a = new Gribmap.Pixel(x, y);
      b = new Gribmap.Pixel(x, y);
      c = new Gribmap.Pixel(x, y);

      a.moveByPolar(windarrow_minsize+wspdlog*4.0, wheading);
      b.moveByPolar(windarrow_minwidth+wspdlog, wheading-90.0);
      c.moveByPolar(windarrow_minwidth+wspdlog, wheading+90.0);

      bary = new Gribmap.Pixel((a.x+b.x+c.x)/3, (a.y+b.y+c.y)/3);
      offset = new Gribmap.Pixel(x-bary.x, y-bary.y);
      a.moveBy(offset);
      b.moveBy(offset);
      c.moveBy(offset);

      context.toffset = offset;
      context.midx = (a.x+x)/2;

      context.beginPath();
      context.moveTo(a.x, a.y);
      context.lineTo(b.x, b.y);
      context.lineTo(c.x, c.y);
      context.fill();
      context.stroke()
      context.closePath();
  },

  // draw wind information around the arrow
  // parameters: 
  // context, the canvas context
  // x, y, the coordinates in the window
  // wspeed, wheading, wind speed and wind heading
  drawWindText: function(context, x, y, pos_wind) {
      var text_x = context.midx;
      var text_y = y+context.toffset.y;
      var wind_direction = pos_wind.wheading;

      if (wind_direction > 90.0 && wind_direction < 270.0) {
  //  text_y +=10;
          text_y += 13 + 5*Math.cos(wind_direction*Math.PI/180.0);
      } else {
  //  text_y -=5;
          text_y -= 7 - 5*Math.cos(wind_direction*Math.PI/180.0);
      }
      context.fillText(""+Math.round(pos_wind.wspeed)+"/"
           +Math.round(wind_direction)+"°",
           text_x, text_y);
  },

  drawContext: function(context) {
      context.font = '8px sans-serif';
      context.textAlign = 'center';
      context.strokeStyle = '#fff';
      context.lineWidth   = 0.5;

  },

  // draw wind information, wind arrows and text in the color relative
  // to the wind speed
  // parameters:
  // context, the canvas context
  // x, y, the coordinates in the window
  // wspeed, wheading, wind speed and wind heading
  drawWind: function(context, x, y, pos_wind) {
      context.fillStyle = this.windSpeedToColor(pos_wind.wspeed);
      this.drawWindTriangle(context, x, y, pos_wind);
      this.drawWindText(context, x, y, pos_wind);
  },


  CLASS_NAME: 'Gribmap.Layer'

});

/**
 * Class: Gribmap.ControlWind
 * 
 * Inherits from:
 *  - <OpenLayers.Control.ControlSwitch>
 */
Gribmap.ControlWind = 
  OpenLayers.Class(OpenLayers.Control.ControlSwitch, {

    label: "Gribmap.ControlWind",
    
    timeOffsetSpan: null,

    initialize: function(options) {
        OpenLayers.Control.prototype.initialize.apply(this, arguments);
    },

    drawBaseDiv: function() {
        this.baseDiv.appendChild(this.imgButton("west-mini.png", "Gribmap_Backward", this.onClickBackward ));
        this.timeOffsetSpan = this.textButton(" 0h ", "reset", this.onClickReset );
        this.baseDiv.appendChild(this.timeOffsetSpan);
        this.baseDiv.appendChild(this.imgButton("east-mini.png", "Gribmap_Forward", this.onClickForward ));

    },

    imgButton: function(imgname, imgid, callback) {
    
        var imgLocation = OpenLayers.Util.getImagesLocation();
        var sz = new OpenLayers.Size(18,18);        

        // maximize button div
        var img = imgLocation + imgname;
        var button = OpenLayers.Util.createAlphaImageDiv(
                                    imgid,
                                    null, 
                                    sz, 
                                    img, 
                                    "relative");
        OpenLayers.Event.observe(button, "click", OpenLayers.Function.bind(callback, this, img));
        return button;
    },

    textButton: function(text, textid, callback) {
        var textSpan = document.createElement("span");
        OpenLayers.Element.addClass(textSpan, textid);
        textSpan.innerHTML = text;
        OpenLayers.Event.observe(textSpan, "click", OpenLayers.Function.bind(callback, this, textSpan));
        return textSpan;
    },

    getGribmapLayer: function() {
        if (this.gribmap) return this.gribmap;
        if (this.map) {
            this.gribmap = this.map.getLayersByClass("Gribmap.Layer")[0];
        }
        return this.gribmap;
    },

    onClickReset: function (ctrl, evt) {
        OpenLayers.Event.stop(evt ? evt : window.event);
        l = this.getGribmapLayer();
        l.timereset();
        this.timeOffsetSpan.innerHTML = " "+Math.round(l.timeoffset/3600)+"h ";
    },

    onClickForward: function (ctrl, evt) {
        OpenLayers.Event.stop(evt ? evt : window.event);
        l = this.getGribmapLayer();
        l.timeforward();
        this.timeOffsetSpan.innerHTML = " "+Math.round(l.timeoffset/3600)+"h ";
    },

    onClickBackward: function (ctrl, evt) {
        OpenLayers.Event.stop(evt ? evt : window.event);
        l = this.getGribmapLayer();
        l.timebackward();
        this.timeOffsetSpan.innerHTML = " "+Math.round(l.timeoffset/3600)+"h ";
    },


    CLASS_NAME: "Gribmap.ControlWind"
});

/**
 * Class: Gribmap.MousePosition
 * 
 * Inherits from:
 *  - <OpenLayers.Control.MousePosition>
 */
Gribmap.MousePosition = 
  OpenLayers.Class(OpenLayers.Control.MousePosition, {

    gribmap: null,

    initialize: function(options) {
        OpenLayers.Control.prototype.initialize.apply(this, arguments);
    },
    
    formatOutput: function(lonLat) {
       var retstr = OpenLayers.Util.getFormattedLonLat(lonLat.lat, 'lat', 'dms');
       retstr += " "+OpenLayers.Util.getFormattedLonLat(lonLat.lon, 'lon', 'dms');
       var winfo = this.gribmap.windAtPosition(lonLat);
       if (winfo != null) retstr += " - "+Math.round(winfo.wspeed*10)/10+"n / "+Math.round(winfo.wheading*10)/10+"°";
       return retstr;
    },
    CLASS_NAME: "Gribmap.MousePosition"
});

