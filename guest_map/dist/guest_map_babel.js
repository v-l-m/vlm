define([], function () {
  "use strict";

  function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

  // CONFIGURATION FILE FOR SPECTATOR MODE
  var webhost = window.location.hostname;
  var baseurl = "//v-l-m.org";

  if (location.protocol === 'https:') {
    baseurl = 'https:' + baseurl;
  } else {
    baseurl = 'http:' + baseurl;
  }

  var today = new Date();
  var secs = today.getSeconds();
  var mns = today.getMinutes();
  var hrs = today.getHours();
  var dday = today.getDate();
  var dmonth = today.getMonth() + 1;
  var dyear = today.getFullYear();
  var current_date = dday + "/" + dmonth + "/" + dyear + " " + hrs + ":" + mns + ":" + secs;
  var cur_tsp = Math.ceil(new Date().getTime() / 1000); // 12 heures
  //starttime = cur_tsp-43200;
  // 24 heures

  var starttime = cur_tsp - 86400;
  var endtime = cur_tsp; // *************** SPECTATOR MODE *****************
  // 2010 => Virtual Loup De Mer
  // http://www.v-l-m.org
  // ************************************************

  var LMap = null;
  var boat_idu = [];
  var boat_rank = [];
  var boat_color = [];
  var boat_pos = [];
  var boat_mark = [];
  var boat_texte = [];
  var boat_win = [];
  var boat_info = [];
  var boat_track = [];
  var map_lat;
  var map_lon;
  var boats = [];
  var idr; // EXTEND JQUERY WITH A FUNCTION TO GET VARS IN QUERY STRING

  $.extend({
    getUrlVars: function getUrlVars() {
      var vars = [],
          hash;
      var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');

      for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
      }

      return vars;
    },
    getUrlVar: function getUrlVar(name) {
      return $.getUrlVars()[name];
    }
  }); // MASTER FUNCTION

  function start() {
    idr = $.getUrlVar('idr');

    if (typeof idr == 'undefined') {
      display_races_list();
    } else {
      boats = refresh_ranking(idr);
      display_race();
    }
  } // REFRESH ONLY THE RANKING AND THE BOATS


  function refresh_all() {
    // REMOVE ALL BOATS MARKER
    if (boat_mark) {
      for (var i in boat_mark) {
        if (boat_mark[i]) {
          boat_mark[i].removeFrom(LMap);
        }
      }

      boat_mark.length = 0;
    }

    boats = refresh_ranking(idr);
    draw_all_boats();
  } // DISPLAY RACES LIST


  function display_races_list() {
    //document.getElementById('tab_listrace').innerHTML = "<div align='center' style='width: 800px; height: 700px;'><br/><br/><img src='img/ajax-loader.gif'/></div>";
    // ex : {"81":{"idraces":81,"racename":"C5-BP5 : Creac'h - les 3 Caps - Creac'h","started":0,"deptime":1291104000,"startlong":-5.1,"startlat":48.5,"boattype":"boat_C5bp5","closetime":1291190400,"racetype":0,"firstpcttime":200,"depend_on":"0","qualifying_races":"","idchallenge":null,"coastpenalty":3600,"bobegin":0,"boend":0,"maxboats":0,"theme":"0","vacfreq":5,"updated":"2010-08-31 08:46:22"}
    $.ajax({
      async: false,
      url: "/ws/raceinfo/list.php",
      dataType: "json",
      cache: false,
      success: function success(answer) {
        var races = "";
        var race_started = "";
        var race_open = "";
        var classc1 = "";
        var classc2 = "";

        for (var k in answer) {
          if (answer[k].started > 0) {
            race_started = "Commenc&eacute;e";
            classc1 = "TxtRaceRun";
          } else {
            race_started = "En attente";
            classc1 = "TxtRaceOpen";
          }

          if (answer[k].closetime < cur_tsp) {
            race_open = "Ferm&eacute;e";
            classc2 = "TxtRaceClosed";
          } else {
            race_open = "Ouverte";
            classc2 = "TxtRaceOpen";
          }

          races = races + "<tr bgcolor='#ffffff'><td class='txtbold1'>" + answer[k].idraces + "</td><td align='center' class='txtbold1'><a href='index.html?idr=" + answer[k].idraces + "' align='center'>" + answer[k].racename + "</a></td><td class='" + classc1 + "' align='center'>" + race_started + "</td><td class='" + classc2 + "' align='center'>" + race_open + "</td><td><a href='index.html?idr=" + answer[k].idraces + "'><img src='" + baseurl + "/images/site/cartemarine.png' border='0'><a/></tr>\n";
        }

        document.getElementById('tab_listrace').innerHTML = "<div align='center'><h2>Courses en cours ou courses dont le d&eacute;part est &agrave; venir</h2><br/><br/><table bgcolor='#000000'><tr class='STxtRank'><td></td><td>Course</td><td>Etat</td><td>Inscription</td><td>Carte</td></tr>" + races + "</table></div><br/><br/><br/><br/><br/><br/>";
      },
      error: function error() {
        alert("erreur => display_races_list()!");
      }
    });
  } // DISPLAY RACE : initialize map and boats array


  function display_race() {
    var map = new L.map('map_canvas', {
      zoom: 9
    }); //google.maps.Map(document.getElementById("map_canvas"), myOptions);

    if (!LMap) {
      LMap = map; // tileLayer for VLM coast lines. Nouw using OpenSeaMap
      // L.tileLayer('https://c1.v-l-m.org/gshhstiles/{z}/{x}/{y}.png',
      // {
      //   attribution: 'VLM Maps',
      //   maxZoom: 18,
      //   id: 'VLM GSHHS'
      // }).addTo(LMap);
      // create the tile layer with correct attribution

      var osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
      var oseamUrl = 'https://t1.openseamap.org/seamark//{z}/{x}/{y}.png';
      var osmAttrib = 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';
      var oseamAttrib = 'Map data © <a href="http://openSeamap.org">OpenSeaMap</a> contributors';
      var osm = new L.TileLayer(osmUrl, {
        minZoom: 1,
        maxZoom: 18,
        attribution: osmAttrib
      });
      osm.addTo(LMap);
      var oseam = new L.TileLayer(oseamUrl, {
        minZoom: 1,
        maxZoom: 18,
        attribution: oseamAttrib
      });
      oseam.addTo(LMap);
    } //RACE INFOS


    get_raceinfo(map, idr); //SHOW BOATS

    draw_all_boats();
    return map;
  } // REFRESH RACE


  function refresh_race(idr) {
    //document.getElementById('tab_ranking').innerHTML = "<div align='center' style='width: 200px; height: 200px;'><br/><br/><img src='img/ajax-loader.gif'/></div>";
    boats = liste_boats(idr);
    refresh_ranking(idr);

    for (var i = 1; i < boats.length; i++) {
      carte.removeOverlay(bateau[i]);
    } //TRACAGE DES BATEAUX


    bateau = [];

    for (var _i = 1; _i < boats.length; _i++) {
      label = one_boat_label(boats, _i);
      bateau[_i] = icon_boat(carte, boats[_i].latitude, boats[_i].longitude, boats[_i].rank, label);
    }
  } // GET ALL INFOS FOR A RACE AND INITIALIZE racename, race wps (


  function get_raceinfo(map, idr) {
    $.ajax({
      async: false,
      url: "/ws/raceinfo/desc.php?idrace=" + idr,
      dataType: "json",
      cache: false,
      success: function success(answer) {
        // INFOS GENERALES COURSE
        // "idraces" "racename" "started" "deptime" "startlong" "startlat" "boattype" "closetime" "racetype" "firstpcttime" "depend_on" "qualifying_races" "idchallenge" "coastpenalty" "bobegin" "boend" "maxboats" "theme" "vacfreq" "races_waypoints"
        var racename = answer.racename;
        var titre_carte = "<span class='txtbold2'>&nbsp;&nbsp;&nbsp;Course : " + racename + "</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class='txtbold1'>Situation des 500 premiers bateaux en course - " + current_date + "</span>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='retour' value='Liste des courses' class='bouton1' onclick=\"document.location.href='index.html';\" />&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='refresh' value='Actualiser' class='bouton1'  onclick=\"refresh_all();\" />";
        document.getElementById('titre_carte').innerHTML = titre_carte;
        var startlong = answer.startlong / 1000;
        var startlat = answer.startlat / 1000; // fix #507 If position null for the first boat or no boats => center map on the start

        if (typeof map_lat === "undefined" || typeof map_lon === "undefined" || map_lat == "0" && map_lon == "0") {
          map_lat = startlat;
          map_lon = startlong; //new_map_latlon = new google.maps.LatLng(map_lat,map_lon);
        }

        LMap.setView([map_lat, map_lon]); // AFFICHAGE DU DEPART

        var StartMarker = L.icon({
          iconUrl: 'img/beachflag.png',
          shadowUrl: 'img/beachflag_shadow.png',
          iconSize: [20, 32],
          // size of the icon
          shadowSize: [20, 32],
          // size of the shadow
          iconAnchor: [0, 32],
          // point of the icon which will correspond to marker's location
          shadowAnchor: [0, 32],
          // the same for the shadow
          popupAnchor: [-3, -76] // point from which the popup should open relative to the iconAnchor

        });
        var depart_txt = "<b>START</b><br/><h3>" + racename + "</h3>";
        L.marker([startlat, startlong], {
          icon: StartMarker
        }).bindPopup(depart_txt).addTo(LMap); // Marques du parcours

        var rwps = answer.races_waypoints; //test = "";

        var i = 0; //mark_wp = [];

        var wp_pos = [];
        var texte = []; // add start to the race track

        var path = [[startlat, startlong]];

        for (var k in rwps) {
          // "idwaypoint" "wpformat" "wporder" "laisser_au" "wptype" "latitude1" "longitude1" "latitude2" "longitude2" "libelle" "maparea"
          var wporder = rwps[k].wporder;
          var wptype = rwps[k].wptype;
          var lat1 = rwps[k].latitude1 / 1000;
          var long1 = rwps[k].longitude1 / 1000;
          path.push([lat1, long1]); // 	wp_pos[i] = new google.maps.LatLng(lat1,long1);

          texte[i] = "<span class=\'txtbold2\'>" + rwps[k].libelle + "</span><hr><strong>Latitude : </strong>" + lat1 + ", <strong>Longitude : </strong>" + long1 + "<br> <strong>Ordre : </strong>" + wporder + "<br><strong>Type WP : </strong>" + wptype;
          race_wps(LMap, [lat1, long1], wptype, texte[i], i);
          i = i + 1;

          if (rwps[k].latitude2) {
            var lat2 = rwps[k].latitude2 / 1000;
            var long2 = rwps[k].longitude2 / 1000; // 	wp_pos[i] = new google.maps.LatLng(lat2,long2);

            texte[i] = "<span class=\'txtbold2\'>" + rwps[k].libelle + "</span><hr><strong>Latitude : </strong>" + lat2 + ", <strong>Longitude : </strong>" + long2 + "<br> <strong>Ordre : </strong>" + wporder + "<br><strong>Type WP : </strong>" + wptype;
            race_wps(LMap, [lat2, long2], wptype, texte[i], i);
          } // 	i=i+1;

        }

        L.polyline(path, {
          color: "#006699",
          opacity: 0.2
        }).addTo(LMap); // 	}
        // },
        // error:  function() { alert("erreur => get_raceinfo !");
      }
    });
  } // display marks of race wps (mark_wp array) and call for infos windows


  function race_wps(map, wpos, wptype, texte, i) {
    // var contentString = texte;
    // var info = new google.maps.InfoWindow(
    // {
    //   content: contentString
    // });
    if (wptype == "Finish") {
      var FinishMarker = L.icon({
        iconUrl: 'img/beachflag.png',
        shadowUrl: 'img/beachflag_shadow.png',
        iconSize: [20, 32],
        // size of the icon
        shadowSize: [20, 32],
        // size of the shadow
        iconAnchor: [0, 32],
        // point of the icon which will correspond to marker's location
        shadowAnchor: [0, 32],
        // the same for the shadow
        popupAnchor: [0, -32] // point from which the popup should open relative to the iconAnchor

      });
      L.marker(wpos, {
        icon: FinishMarker
      }).bindPopup(texte).addTo(LMap);
      var shape = {
        coord: [1, 1, 1, 20, 18, 20, 18, 1],
        type: 'poly'
      };
    } else {
      var WPMarker = L.icon({
        iconUrl: 'img/placemark_circle.png',
        //shadowUrl: 'img/placemark_circle_shadow.png',
        iconSize: [32, 32],
        // size of the icon
        shadowSize: [32, 32],
        // size of the shadow
        iconAnchor: [16, 16],
        // point of the icon which will correspond to marker's location
        shadowAnchor: [16, 16],
        // the same for the shadow
        popupAnchor: [0, -4] // point from which the popup should open relative to the iconAnchor

      });
      L.marker(wpos, {
        icon: WPMarker
      }).bindPopup(texte).addTo(LMap);
    }
  } // display boats for the first time and call for infos windows


  function draw_all_boats() {
    //"idusers","boatpseudo","boatname","color","country","nwp","dnm","deptime","loch","releasetime","latitude","longitude","last1h","last3h","last24h","status","rank"
    if (typeof boats === "undefined" || _typeof(boats) !== "object") {
      return;
    }

    var i = 0;

    for (var k in boats) {
      if (boats[k]) {
        boat_idu[k] = boats[k].idusers;
        boat_rank[k] = boats[k].rank;
        boat_color[k] = boats[k].color;
        boat_pos[k] = {
          Lat: boats[k].latitude,
          Lon: boats[k].longitude
        };
        boat_texte[k] = make_boat_texte(boats[k].idusers);
        var img_b = void 0;

        if (boat_rank[k] == "1") {
          //first_idu = boat_idu[k];
          //first_color = boat_color[k];
          img_b = 'img/boat.php?idu=' + boat_idu[k] + '&rank=1';
        } else {
          img_b = 'img/boat.php?idu=' + boat_idu[k] + '&rank=n';
        }

        var BoatMarker = L.icon({
          iconUrl: img_b,
          //shadowUrl: 'img/placemark_circle_shadow.png',
          iconSize: [40, 32],
          // size of the icon
          //shadowSize: [32, 32], // size of the shadow
          iconAnchor: [20, 16],
          // point of the icon which will correspond to marker's location
          shadowAnchor: [16, 16],
          // the same for the shadow
          popupAnchor: [-4, -15] // point from which the popup should open relative to the iconAnchor

        });
        boat_mark[k] = L.marker([boats[k].latitude, boats[k].longitude], {
          icon: BoatMarker
        }).bindPopup(boat_texte[k]).addTo(LMap);
        i++; // BOAT DISPLAY LIMIT

        if (i > 500) {
          return;
        }
      }
    } //for k

  } // Point and display one boat when is called in the ranking


  function draw_one_boat(idu) {
    //if(boats[idu].rank > 32)
    //{
    //boat_mark[idu].setMap(null);
    get_track(idu, boats[idu].color);
    boat_texte[idu] = make_boat_texte(boats[idu].idusers);
    boat_pos[idu] = [boats[idu].latitude, boats[idu].longitude];
    var q_rank = "n";

    if (boats[idu].rank == "1") {
      q_rank = "1";
    }

    var img_b = 'img/boat.php?idu=' + idu + '&rank=' + q_rank;
    var BoatMarker = L.icon({
      iconUrl: img_b,
      //shadowUrl: 'img/placemark_circle_shadow.png',
      iconSize: [40, 32],
      // size of the icon
      //shadowSize: [32, 32], // size of the shadow
      iconAnchor: [20, 16],
      // point of the icon which will correspond to marker's location
      shadowAnchor: [16, 16],
      // the same for the shadow
      popupAnchor: [-4, -15] // point from which the popup should open relative to the iconAnchor

    });
    boat_mark[idu] = L.marker([boats[idu].latitude, boats[idu].longitude], {
      icon: BoatMarker
    }).bindPopup(boat_texte[idu]).addTo(LMap); //} 	
  } // get boat track


  function get_track(idu, color) {
    $.ajax({
      async: true,
      url: "/ws/boatinfo/tracks.php?idu=" + idu + "&starttime=" + starttime,
      dataType: "json",
      cache: false,
      //data: user_pass_ajax,
      //username: username,
      //password: password,
      success: function success(answer) {
        if (boat_track[idu]) {
          boat_track[idu].removeFrom(LMap);
        }

        var tracks = answer.tracks;
        var path = [];

        for (var k in tracks) {
          var lon = tracks[k][1] / 1000;
          var lat = tracks[k][2] / 1000; //$("#test").append(idu + " => " + lat + " - " + lon + "<br/>");
          //if(lat > 0 && lon > 0)
          //{

          var latLng = [lat, lon];
          path.push(latLng); //}
        }

        boat_track[idu] = L.polyline(path, {
          color: "#" + color,
          opacity: 0.4,
          weigth: 2
        }).addTo(LMap);
      },
      error: function error() {
        alert("erreur => get_track ! ");
      }
    });
  } // get and display the ranking


  function refresh_ranking(idr) {
    var test_engaged;
    document.getElementById('tab_ranking').innerHTML = "<div class='loading' align='center' style='width:210px;'><br/><br/><img src='img/ajax-loader.gif'/></div>";
    $.ajax({
      async: false,
      type: "GET",
      url: "/ws/raceinfo/ranking.php?idr=" + idr,
      //url: "unittest.json",
      dataType: "json",
      cache: false,
      //data: user_pass_ajax,
      //username: username,
      //password: password,
      success: function success(answer) {
        if (answer != null) {
          test_engaged = answer.nb_engaged;

          if (test_engaged === "0") {
            map_lat = 0;
            map_lon = 0;
            tab_ranking = "<table bgcolor='#ffffff' height='100'><tr class='txtbold2'><td>Pas de bateaux engag&eacute;s dans cette course pour le moment</td></tr></table>";
            document.getElementById('tab_ranking').innerHTML = tab_ranking;
          } else {
            var mytable = $('<TABLE/>', {
              'id': 'tbranking'
            }).appendTo($("DIV#tab_ranking"));
            $('<THEAD/>').appendTo(mytable);
            $('<TR/>').appendTo($("thead", mytable));
            $('<TH/>', {
              'data-placeholder': '>2',
              'scope': 'col',
              'html': '&nbsp;'
            }).css({
              'width': '20px'
            }).addClass('STxtRank').appendTo($("thead>tr", mytable));
            $('<TH/>', {
              'data-placeholder': '>2',
              'scope': 'col',
              'html': '#'
            }).css({
              'width': '20px'
            }).addClass('STxtRank').appendTo($("thead>tr", mytable));
            $('<TH/>', {
              'data-placeholder': '',
              'scope': 'col',
              'html': 'navigateur'
            }).css({
              'width': '160px'
            }).addClass('STxtRank').appendTo($("thead>tr", mytable));
            var th$ = $('<TH/>', {
              'scope': 'col',
              'html': ''
            }).css({
              'width': '20px'
            }).addClass('STxtRank').appendTo($("thead>tr", mytable));
            var a$ = $('<A/>', {
              'href': '#'
            }).addClass('reset').appendTo(th$);
            $('<IMG/>', {
              'src': './img/reset.gif',
              'alt': 'RAZ des filtres'
            }).appendTo(a$);
            $('<TBODY/>').appendTo(mytable);
            var nl;
            var nb; //Old20130128
            //tab_ranking = "<table bgcolor='#000000'><tr class='txtbold1' bgcolor='#ffffff'><td>Pos</td><td>Navigateur</td></tr>";

            var d2 = answer.ranking;
            boats = [];

            for (var k2 in d2) {
              // console.log('treating:'+k2 + ' - rank:' + d2[k2].rank);
              //"idusers","boatpseudo","boatname","color","country","nwp","dnm","deptime","loch","releasetime","latitude","longitude","last1h","last3h","last24h","status","rank"
              var i = d2[k2].idusers;
              boats[i] = d2[k2];

              if (boats[i].rank === "1") {
                map_lat = boats[i].latitude;
                map_lon = boats[i].longitude;
              }

              var bgcolor = "ffffff";
              var statusb = d2[k2].status;
              var colorb = d2[k2].color;

              if (statusb == "on_coast") {
                bgcolor = "999999";
              }

              if (statusb == "locked") {
                bgcolor = "ff6600";
              } // when paparazzia is in white no we can see then in the ranking


              if (colorb == "ffffff") {
                colorb = "cccccc";
              } //Old20130128
              // tab_ranking = tab_ranking + "<tr class='txt1' bgcolor='#" + bgcolor + "'><td width='25' class='STxtRank' align='center'>"+ d2[k2].rank + "</td><td width='175'><div  onclick='get_boat(" + d2[k2].idusers + ");' onmouseover=\"this.style.cursor='help';\" onmouseout=\"this.style.cursor='auto';\"><font color='"+ colorb + "'><img src='" + baseurl + "/cache/flags/" + d2[k2].country + ".png' width='30' height='20'>No "+ d2[k2].idusers + " - " + d2[k2].boatpseudo + "</font></div></td></tr>";


              nl = $('<TR/>', {
                'scope': 'col'
              }).addClass('txt1').css({
                'background-color': '#' + bgcolor
              }).appendTo($("tbody", mytable)); // col pos

              nb = $('<TD/>').addClass('STxtRank').appendTo(nl);
              $('<P/>', {
                'html': d2[k2].rank
              }).css({
                'font-color': colorb
              }).appendTo(nb); // col boat#

              nb = $('<TD/>').appendTo(nl);
              $('<P/>', {
                'boat': d2[k2].idusers,
                'html': d2[k2].idusers
              }).addClass('clckb').css({
                'font-color': colorb
              }).appendTo(nb); //col boat name

              nb = $('<TD/>').appendTo(nl);
              $('<P/>', {
                'boat': d2[k2].idusers,
                'html': d2[k2].boatpseudo
              }).addClass('clckb').css({
                'font-color': colorb
              }).appendTo(nb); //col flag

              $('<IMG/>', {
                'src': baseurl + '/cache/flags/' + d2[k2].country + '.png'
              }).css({
                width: '30',
                height: '20px'
              }).appendTo($('<TD/>')).appendTo(nl);
            } // Old20130128
            // tab_ranking = tab_ranking + "</table>";
            // document.getElementById('tab_ranking').innerHTML = tab_ranking;
            // bind tout paragraphe de classe clckb


            $(".clckb").bind('click', function (event) {
              get_boat($(this).attr("boat"));
            }); // TABLESORT $("table#tbranking")

            mytable.tablesorter({
              theme: 'blue',
              widthFixed: true,
              widgets: ["filter"],
              headers: {
                0: {
                  sorter: true,
                  filter: true
                },
                1: {
                  sorter: true,
                  filter: true
                },
                2: {
                  sorter: true,
                  filter: true
                },
                3: {
                  sorter: false,
                  filter: false
                }
              },
              widgetOptions: {
                filter_childRows: false,
                filter_columnFilters: true,
                filter_cssFilter: 'tablesorter-filter',
                filter_functions: null,
                filter_hideFilters: true,
                filter_ignoreCase: true,
                filter_reset: 'a.reset',
                filter_searchDelay: 100,
                filter_startsWith: false,
                filter_useParsedData: false
              },
              sortList: [[0, 0]]
            });
            $(".loading").hide();
          }
        }
      },
      error: function error() {
        alert("erreur => refresh_ranking !");
      }
    });

    if (test_engaged == "0") {
      return "0";
    } else {
      return boats;
    }
  } // point on a boat when is called from ranking


  function get_boat(idu) {
    if (typeof boats !== "undefined" && boats && typeof boats[idu] !== "undefined" && boats[idu]) {
      var new_map_lat = boats[idu].latitude;
      var new_map_lon = boats[idu].longitude;
      var new_map_latlon = [new_map_lat, new_map_lon];
      LMap.setView(new_map_latlon);
      draw_one_boat(idu);
    }
  } // make content for boat info window


  function make_boat_texte(idu) {
    var boat_texte = "";

    if (typeof boats !== "undefined" && boats && typeof boats[idu] !== "undefined" && boats[idu]) {
      boat_texte = "<img src='" + baseurl + "/cache/flags/" + boats[idu].country + ".png' width='30' height='20'>" + "&nbsp;&nbsp;<span class='txtbold2'>" + boats[idu].boatpseudo + "</span>&nbsp;&nbsp;<i>" + boats[idu].idusers + "</i>&nbsp;&nbsp;&nbsp;&nbsp;<span class='TxtRank'>&nbsp;" + boats[idu].rank + "&nbsp;</span><hr>" + "<strong>Distance parcourue : </strong>" + boats[idu].loch + "<br>" + "<strong>Latitude : </strong>" + Math.round(boats[idu].latitude * 1000) / 1000 + ",<strong>Longitude : </strong>" + Math.round(boats[idu].longitude * 1000) / 1000 + "<br>" + "<strong>Next WP : </strong>[" + boats[idu].nwp + "] " + boats[idu].dnm + "<br>" + "<strong>Moyennes : [1H] </strong>" + boats[idu].last1h + ",[3H] " + boats[idu].last3h + ",[24H] " + boats[idu].last24h;
    }

    return boat_texte;
  }
});