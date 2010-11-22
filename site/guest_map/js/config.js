// CONFIGURATION FILE FOR SPECTATOR MODE

webhost = window.location.hostname;
if (webhost.match("virtual-loup-de-mer.org"))
{
// virtual-loup-de-mer.org
gmap_key = "ABQIAAAAU9L35N6EdAtdkT4Cb2abDRR9fpxOiyHPEX_8YzC8CNXvq83W-hRDmTj4GD1F8DLKiaJ97BAfcB5i7w";
}

if (webhost.match("caraibes.hd.free.fr"))
{
// caraibes.hd.free.fr
gmap_key = "ABQIAAAAok_IBFtVMWx0xZX21zbpJRQMI8aBYvnUh4A1aH4V8c-4mNhOzxRN6Ev5QoDS02IpbpY4DsfPaT4QmQ";
}

if (webhost.match("zigszags.com"))
{
// zigszags.com
gmap_key = "ABQIAAAAok_IBFtVMWx0xZX21zbpJRTlhEcjKhu1zbEPvkpXJWixh659yBThzICEnW2yhGGYfsPoLArh73nKaA";
}

today = new Date();
var secs = today.getSeconds();
var mns = today.getMinutes();
var hrs = today.getHours();

var dday = today.getDate();
var dmonth = today.getMonth() + 1;
var dyear = today.getFullYear();

current_date = dday + "/" + dmonth + "/" + dyear + " " + hrs + ":" + mns + ":" + secs;


//now()-7200
cur_tsp = Math.ceil(new Date().getTime()/1000);
// 2 heures
//starttime = cur_tsp-7200;
// 8 heures
starttime = cur_tsp-28800;
endtime = cur_tsp;

user_pass_ajax = "username=la.playa@free.fr&password=la.playa";
username = "la.playa@free.fr";
password = "la.playa";
