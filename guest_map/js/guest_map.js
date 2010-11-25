// *************** SPECTATOR MODE *****************
// 2010 => Virtual Loup De Mer
// http://www.virtual-loup-de-mer.org
// ************************************************

// EXTEND JQUERY WITH A FUNCTION TO GET VARS IN QUERY STRING
$.extend({
  getUrlVars: function(){
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
      hash = hashes[i].split('=');
      vars.push(hash[0]);
      vars[hash[0]] = hash[1];
    }
    return vars;
  },
  getUrlVar: function(name){
    return $.getUrlVars()[name];
  }
});

// MASTER FUNCTION
function start()
{
	idr = $.getUrlVar('idr');

	if(typeof(idr) == 'undefined')
		{ 
		display_races_list();
		}
	else
		{
		boats=refresh_ranking(idr);
		// Asynchronously Loading the GMAP API for best performances
		var script = document.createElement("script");
		script.type = "text/javascript";
		script.src = "http://maps.google.com/maps/api/js?sensor=false&amp;key=" + gmap_key + "&callback=display_race";
		document.body.appendChild(script);
		
		}
}

// REFRESH ONLY THE RANKING AND THE BOATS (NOT IN USE FOR NOW)
function refresh_all()
{
boats=refresh_ranking(idr);
draw_all_boats();
}

// DISPLAY RACES LIST
function display_races_list()
{
	document.getElementById('tab_listrace').innerHTML = "<div align='center' style='width: 800px; height: 700px;'><br/><br/><img src='img/ajax-loader.gif'/></div>";
// ex : {"81":{"idraces":81,"racename":"C5-BP5 : Creac'h - les 3 Caps - Creac'h","started":0,"deptime":1291104000,"startlong":-5.1,"startlat":48.5,"boattype":"boat_C5bp5","closetime":1291190400,"racetype":0,"firstpcttime":200,"depend_on":"0","qualifying_races":"","idchallenge":null,"coastpenalty":3600,"bobegin":0,"boend":0,"maxboats":0,"theme":"0","vacfreq":5,"updated":"2010-08-31 08:46:22"}
	$.ajax({
		async: false,
		url: "/ws/raceinfo/list.php",
		dataType: "json",
		cache: false,
		success: function(answer){
			races = "";
			for (k in answer)
			{
			if(answer[k].started > 0)
				{
					race_started = "Commenc&eacute;e";
					classc1 = "TxtRaceRun";
				}
				else
				{
					race_started = "En attente";
					classc1 = "TxtRaceOpen";
				}
				
			if(answer[k].closetime < cur_tsp)
				{
					race_open = "Ferm&eacute;e";
					classc2 = "TxtRaceClosed";
				}
				else
				{
					race_open = "Ouverte";
					classc2 = "TxtRaceOpen";
				}
			
			races = races + "<tr bgcolor='#ffffff'><td class='txtbold1'>" + answer[k].idraces + "</td><td align='center' class='txtbold1'><a href='index.html?idr=" + answer[k].idraces + "' align='center'>" + answer[k].racename + "</a></td><td class='" + classc1 + "' align='center'>" + race_started + "</td><td class='" + classc2 + "' align='center'>" + race_open + "</td><td><a href='index.html?idr=" + answer[k].idraces + "'><img src='http://virtual-loup-de-mer.org/images/site/cartemarine.png' border='0'><a/></tr>\n";
			}
			document.getElementById('tab_listrace').innerHTML = "<div align='center'><h2>Courses en cours ou courses dont le d&eacute;part est &agrave; venir</h2><br/><br/><table bgcolor='#000000'><tr class='STxtRank'><td></td><td>Course</td><td>Etat</td><td>Inscription</td><td>Carte</td></tr>" + races + "</table></div><br/><br/><br/><br/><br/><br/>";
	
		},
		error:  function() { alert("erreur => display_races_list()!");}
		});

}

// DISPLAY RACE : initialize map and boats array
function display_race() {
	document.getElementById('map_canvas').innerHTML = "<div align='center' style='width: 800px; height: 700px;'><br/><br/><img src='img/ajax-loader.gif'/></div>";
	//boats=refresh_ranking(idr);
	if( (map_lat == "0" && map_lon == "0") || (map_lat == "" && map_lon == "") || (typeof(map_lat) == 'undefined' && typeof(map_lon) == 'undefined') )
	{
		map_lat = "0";
		map_lon = "0";
		var centre = new google.maps.LatLng(map_lat,map_lon);
	}
	else
	{
		var centre = new google.maps.LatLng(map_lat,map_lon);
	}
	var myOptions = {
		zoom: 9,
		center: centre,
		mapTypeId: google.maps.MapTypeId.TERRAIN
		}

	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	
	//RACE INFOS
	get_raceinfo(map,idr);
	
	//SHOW BOATS
	draw_all_boats();
	
	return map;
 }

// REFRESH RACE
function refresh_race(idr)
	{
		document.getElementById('tab_ranking').innerHTML = "<div align='center' style='width: 200px; height: 200px;'><br/><br/><img src='img/ajax-loader.gif'/></div>";
		boats=liste_boats(idr);
		refresh_ranking(idr);
		for (var i=1 ; i<boats.length ; i++)
		{
		carte.removeOverlay(bateau[i]);
		}
		//TRACAGE DES BATEAUX
		bateau = new Array();
		for (var i=1 ; i<boats.length ; i++)
		{
		label = one_boat_label(boats,i);
		bateau[i] = icon_boat(carte,boats[i].latitude,boats[i].longitude,boats[i].rank,label);
		}
	}

// GET ALL INFOS FOR A RACE AND INITIALIZE racename, race wps (
function get_raceinfo(map,idr)
	{
	$.ajax({
		async: false,
		url: "/ws/raceinfo.php?idrace=" + idr,
		dataType: "json",
		cache: false,
		success: function(answer){
			// INFOS GENERALES COURSE
			// "idraces" "racename" "started" "deptime" "startlong" "startlat" "boattype" "closetime" "racetype" "firstpcttime" "depend_on" "qualifying_races" "idchallenge" "coastpenalty" "bobegin" "boend" "maxboats" "theme" "vacfreq" "races_waypoints"
			racename = answer.racename;
			titre_carte = "<span class='txtbold2'>&nbsp;&nbsp;&nbsp;Course : " + racename + "</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class='txtbold1'>Situation des 200 premiers bateaux en course - "+ current_date + "</span>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='retour' value='Liste des courses' class='bouton1' onclick=\"document.location.href='index.html';\" />";
			document.getElementById('titre_carte').innerHTML = titre_carte;
			startlong = answer.startlong/1000;
			startlat = answer.startlat/1000;
			
			// fix #507 If position null for the first boat or no boats => center map on the start
			if( (map_lat == "0" && map_lon == "0") )
			{
				map_lat = startlat;
				map_lon = startlong;
				new_map_latlon = new google.maps.LatLng(map_lat,map_lon);
				map.setCenter(new_map_latlon);
			}

			// AFFICHAGE DU DEPART
			var start = new google.maps.LatLng(startlat,startlong);

			var image = new google.maps.MarkerImage('img/beachflag.png',
				new google.maps.Size(20, 32),
				new google.maps.Point(0,0),
				new google.maps.Point(0,32));

			var shadow = new google.maps.MarkerImage('img/beachflag_shadow.png',
				new google.maps.Size(37, 32),
				new google.maps.Point(0,0),
				new google.maps.Point(0, 32));

			var shape = {
			coord: [1, 1, 1, 20, 18, 20, 18 , 1],
			type: 'poly' };

			var depart = new google.maps.Marker({
				position: start,
				map: map,
				shadow: shadow,
				icon: image,
				shape: shape
				});
			depart_txt = "<b>START</b><br/><h3>" + racename + "</h3>";
			attachInfoWindow(depart, depart_txt, 999);

			// Marques du parcours
			var rwps = answer.races_waypoints;
			test = "";
			var i=0;
			mark_wp = new Array();
			var wp_pos = new Array();
			var texte = new Array();
			
			// INITIALIZE RACE TRACK
			var RaceLineOptions = {
					geodesic: true,
					strokeColor: "#006699",
					strokeOpacity: 0.2,
					strokeWeight: 6
				};
			RaceLine = new google.maps.Polyline(RaceLineOptions);
			RaceLine.setMap(map);
			
			// add start to the race track
			var path = RaceLine.getPath();
			path.push(start);
			
			for(k in rwps)
				{ // "idwaypoint" "wpformat" "wporder" "laisser_au" "wptype" "latitude1" "longitude1" "latitude2" "longitude2" "libelle" "maparea"
				var wporder = rwps[k].wporder;
				var wptype = rwps[k].wptype;
				
				var lat1 = rwps[k].latitude1/1000;
				var long1 = rwps[k].longitude1/1000;
				
				// add WP to the race track
				WPlatLng = new google.maps.LatLng(lat1, long1);
				var path = RaceLine.getPath();
				path.push(WPlatLng);
				
				
				wp_pos[i] = new google.maps.LatLng(lat1,long1);
				texte[i] = "<span class=\'txtbold2\'>" + rwps[k].libelle + "</span><hr><strong>Latitude : </strong>" + lat1 + ", <strong>Longitude : </strong>" + long1 + "<br> <strong>Ordre : </strong>" + wporder + "<br><strong>Type WP : </strong>" + wptype;
				race_wps(map,wp_pos[i],wptype,texte[i],i);
				i=i+1;
				
				var lat2 = rwps[k].latitude2/1000;
				var long2 = rwps[k].longitude2/1000;
				wp_pos[i] = new google.maps.LatLng(lat2,long2);
				
				texte[i] = "<span class=\'txtbold2\'>" + rwps[k].libelle + "</span><hr><strong>Latitude : </strong>" + lat2 + ", <strong>Longitude : </strong>" + long2 + "<br> <strong>Ordre : </strong>" + wporder + "<br><strong>Type WP : </strong>" + wptype;
				race_wps(map,wp_pos[i],wptype,texte[i],i);
				i=i+1;
				
				}
			},
		error:  function() { alert("erreur => get_raceinfo !");}
		});
	}

// display marks of race wps (mark_wp array) and call for infos windows
function race_wps(map,wpos,wptype,texte,i)
	{
	var contentString = texte;
	var info = new google.maps.InfoWindow({
		content: contentString
	});
	
	if(wptype=="Finish") {
	var image = new google.maps.MarkerImage('img/beachflag.png',
		new google.maps.Size(20, 32),
		new google.maps.Point(0,0),
		new google.maps.Point(0,32));

	var shadow = new google.maps.MarkerImage('img/beachflag_shadow.png',
		new google.maps.Size(37, 32),
		new google.maps.Point(0,0),
		new google.maps.Point(0, 32));

	var shape = {
	coord: [1, 1, 1, 20, 18, 20, 18 , 1],
	type: 'poly' };
	}
	else
	{
	var image = new google.maps.MarkerImage('img/placemark_circle.png',
		new google.maps.Size(32, 32),
		new google.maps.Point(0,0),
		new google.maps.Point(16,16));
	
	var shape = {
		coord: [16,32,40],
		type: 'circle' };
	/*
	var shape = {
	coord: [1, 1, 1, 20, 18, 20, 18 , 1],
	type: 'poly' };
	*/
	}
	mark_wp[i] = new google.maps.Marker({
		position: wpos,
		map: map,
		shadow: shadow,
		icon: image,
		shape: shape
		});
	attachInfoWindow(mark_wp[i], texte, i);
		

	}

// display boats for the first time and call for infos windows
function draw_all_boats()
{
	//"idusers","boatpseudo","boatname","color","country","nwp","dnm","deptime","loch","releasetime","latitude","longitude","last1h","last3h","last24h","status","rank"
	boat_idu = new Array();
	boat_rank = new Array();
	boat_color = new Array();
	boat_pos = new Array();
	boat_mark = new Array();
	boat_texte = new Array();
	boat_win = new Array();
	boat_info = new Array();

	i=0;
	for(k in boats)
	{ 
	boat_idu[k] = boats[k].idusers; 
	boat_rank[k] = boats[k].rank;
	boat_color[k] = boats[k].color;
	boat_pos[k] = new google.maps.LatLng(boats[k].latitude,boats[k].longitude);
	boat_texte[k] = make_boat_texte(boats[k].idusers);

	if(boat_rank[k]=="1")
	{
		first_idu = boat_idu[k];
		first_color = boat_color[k];
		var img_b = 'img/bateauPremier.png';
	}
	else
	{
		var img_b = 'img/bateauEnCourse.png';
	}

	var image = new google.maps.MarkerImage(img_b,
		new google.maps.Size(32,32),
		new google.maps.Point(0,0),
		new google.maps.Point(16,16));

	var shape = {
		coord: [0,0,32],
		type: 'circle' };
/*
	var shape = {
		coord: [1, 1, 1, 20, 18, 20, 18 , 1],
		type: 'poly' };
*/
	boat_mark[k] = new google.maps.Marker({
		position: boat_pos[k],
		map: map,
        	icon: image,
        	shape: shape
		});

	attachInfoWindow(boat_mark[k], boat_texte[k], boat_rank[k], boat_idu[k], boat_color[k]);
	

	i++;
	
	
		
	if(i>200)
	{
		// if the race is started we draw the first boat track
		//if( first_idu != "")
			//{
			//get_track(first_idu,first_color);
			//}
		return;
	}
	
	} //for k
	

}

// Point and display one boat when is called in the ranking
function draw_one_boat(idu)
{
//if(boats[idu].rank > 32)
//{
get_track(idu,boats[idu].color);

boat_texte[idu] = make_boat_texte(boats[idu].idusers);
boat_pos[idu] = new google.maps.LatLng(boats[idu].latitude,boats[idu].longitude);
var img_b = 'img/bateauEnCourse.png';
var image = new google.maps.MarkerImage(img_b,
		new google.maps.Size(32,32),
		new google.maps.Point(0,0),
		new google.maps.Point(16,16));
	/*
	var shape = {
		coord: [1, 1, 1, 20, 18, 20, 18 , 1],
		type: 'poly' };
	*/
	var shape = {
		coord: [0,0,32],
		type: 'circle' };
		
	boat_mark[idu] = new google.maps.Marker({
		position: boat_pos[idu],
		map: map,
        	icon: image,
        	shape: shape
		});

//} 

attachOpenInfoWindow(boat_mark[idu], boat_texte[idu])

}

// attach info window to a mark (boat or a wp)
function attachInfoWindow(marker, content, number,idu,boat_color) {

  var infowindow = new google.maps.InfoWindow(
      { content: content,
        zIndex: number
      });

  google.maps.event.addListener(marker, 'click', function() {
    infowindow.open(map,marker);
	get_track(idu,boat_color);
  });

}

// attach info window to a mark (boat or a wp) and open it (calls in ranking)
function attachOpenInfoWindow(marker, content) {

	var infowindow = new google.maps.InfoWindow(
	{ content: content
		});

	infowindow.open(map,marker);

	google.maps.event.addListener(marker, 'click', function() {
	infowindow.open(map,marker); });

}

// get boat track
function get_track(idu,color)
{
	$.ajax({
		async: true,		
		url: "/ws/boatinfo/tracks.php?idu="+idu+"&starttime=" + starttime,
		dataType: "json",
		cache: false,
		data: user_pass_ajax,
		username: username,
		password: password,
		success: function(answer){
				var polyOptions = {
				 strokeColor: '#'+color,
				strokeOpacity: 0.4,
				strokeWeight: 2
				};

				poly = new google.maps.Polyline(polyOptions);
				poly.setMap(map);

				tracks = answer["tracks"];
				for (k in tracks) {
					lon = tracks[k][1]/1000;
					lat = tracks[k][2]/1000;
					//$("#test").append(idu + " => " + lat + " - " + lon + "<br/>");
					//if(lat > 0 && lon > 0)
					//{
					latLng = new google.maps.LatLng(lat, lon);
					var path = poly.getPath();
					path.push(latLng);
					//}
				}
		},
		error: function(){ alert("erreur => get_track ! "); }
		});
}

// get and display the ranking
function refresh_ranking(idr)
	{
	//document.getElementById('tab_ranking').innerHTML = "<div align='center' style='width:210px;'><br/><br/><img src='img/ajax-loader.gif'/></div>";
	$.ajax({
		async: false,
		url: "/ws/raceinfo/ranking.php?idr="+idr,
		dataType: "json",
		cache: false,
		data: user_pass_ajax,
		username: username,
		password: password,
		success: function(answer){
			test_engaged = answer['nb_engaged'];
			if(test_engaged == "0")
			{
				map_lat = 0;
				map_lon = 0;
				tab_ranking = "<table bgcolor='#ffffff' height='100'><tr class='txtbold2'><td>Pas de bateaux engag&eacute;s dans cette course pour le moment</td></tr></table>";
				document.getElementById('tab_ranking').innerHTML = tab_ranking;
			}
			else
			{
			for (k in answer) {
				
				if(k=="ranking")
					{
					tab_ranking = "<table bgcolor='#000000'><tr class='txtbold1' bgcolor='#ffffff'><td>Pos</td><td>Navigateur</td></tr>";
					var d2 = answer[k];
					
					boats = new Array();
					for (k2 in d2) {
						//"idusers","boatpseudo","boatname","color","country","nwp","dnm","deptime","loch","releasetime","latitude","longitude","last1h","last3h","last24h","status","rank"
						i = d2[k2].idusers
						boats[i] = d2[k2];
						if(boats[i].rank == "1" )
						{
							map_lat = boats[i].latitude;
							map_lon = boats[i].longitude;
						}
						bgcolor="ffffff";
						statusb = d2[k2].status;
						colorb = d2[k2].color;
						if(statusb == "on_coast") { bgcolor = "999999"; }
						if(statusb == "locked") { bgcolor = "ff6600"; }
						// when paparazzia is in white no we can see then in the ranking
						if(colorb == "ffffff") { colorb = "cccccc"; }
						
						tab_ranking = tab_ranking + "<tr class='txt1' bgcolor='#" + bgcolor + "'><td width='25' class='STxtRank' align='center'>"+ d2[k2].rank + "</td><td width='175'><div  onclick='get_boat(" + d2[k2].idusers + ");' onmouseover=\"this.style.cursor='help';\" onmouseout=\"this.style.cursor='auto';\"><font color='"+ colorb + "'><img src='http://www.virtual-loup-de-mer.org/flagimg.php?idflags=" + d2[k2].country + "' width='30' height='20'>No "+ d2[k2].idusers + " - " + d2[k2].boatpseudo + "</font></div></td></tr>";
						}
						
					tab_ranking = tab_ranking + "</table>";
					document.getElementById('tab_ranking').innerHTML = tab_ranking;
					}
				}
			}},
		error: function(){ alert("erreur => refresh_ranking !"); }
		});
	if(test_engaged == "0") { return "0"; } else { return  boats; }
	}

// point on a boat when is called from ranking
function get_boat(idu)
	{
		new_map_lat = boats[idu].latitude;
		new_map_lon = boats[idu].longitude;
		new_map_latlon = new google.maps.LatLng(new_map_lat, new_map_lon);
		map.setCenter(new_map_latlon);
		draw_one_boat(idu);
	}

// make content for boat info window
function make_boat_texte(idu)
{
var boat_texte = "<img src='http://www.virtual-loup-de-mer.org/flagimg.php?idflags=" + boats[idu].country + "' width='30' height='20'>" +
	"&nbsp;&nbsp;<span class='txtbold2'>" + boats[idu].boatpseudo + "</span>&nbsp;&nbsp;<i>" + boats[idu].idusers + "</i>&nbsp;&nbsp;&nbsp;&nbsp;<span class='TxtRank'>&nbsp;" + boats[idu].rank + "&nbsp;</span><hr>" +
	"<strong>Distance parcourue : </strong>" + boats[idu].loch + "<br>" +
	"<strong>Latitude : </strong>" + Math.round( (boats[idu].latitude) * 1000)/1000 + ",<strong>Longitude : </strong>" + Math.round( (boats[idu].longitude) * 1000)/1000 + "<br>" +
	"<strong>Next WP : </strong>[" + boats[idu].nwp + "] " + boats[idu].dnm + "<br>" +
	"<strong>Moyennes : [1H] </strong>" + boats[idu].last1h + ",[3H] " + boats[idu].last3h + ",[24H] " + boats[idu].last24h;

return boat_texte;
}