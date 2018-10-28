// CONFIGURATION FILE FOR SPECTATOR MODE

var webhost = window.location.hostname;
var baseurl = "//v-l-m.org";

if (location.protocol === 'https:')
{
    baseurl = 'https:' + baseurl;
}
else
{
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

var cur_tsp = Math.ceil(new Date().getTime()/1000);

// 12 heures
//starttime = cur_tsp-43200;

// 24 heures
var starttime = cur_tsp-86400;

var endtime = cur_tsp;

