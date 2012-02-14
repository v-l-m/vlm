// CONFIGURATION FILE FOR SPECTATOR MODE

webhost = window.location.hostname;
baseurl = "http://virtual-loup-de-mer.org";

if (webhost.match("virtual-loup-de-mer.org"))
{
    // virtual-loup-de-mer.org
    baseurl = "";
    gmap_key = "ABQIAAAAU9L35N6EdAtdkT4Cb2abDRR9fpxOiyHPEX_8YzC8CNXvq83W-hRDmTj4GD1F8DLKiaJ97BAfcB5i7w";
}


if (webhost.match("v-l-m.org"))
{
    // virtual-loup-de-mer.org
    baseurl = "";
    gmap_key = "ABQIAAAAt-TNu1jygAUkY20DVJC9EBQiRJWtMgCXYC-aY29DJsyyjFhxYRRiuD66kgnawCurNFPlPb4Rfiznqw";
}



if (webhost.match("paparazzia.info")) {
    baseurl = "";
    gmap_key = "ABQIAAAAt-TNu1jygAUkY20DVJC9EBQzFwCcMhfsDTv_S48r6nw_wx5DpRRymYw5m7qN4i_Kpwt-w5ZRvgYCQA";
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

cur_tsp = Math.ceil(new Date().getTime()/1000);

// 12 heures
//starttime = cur_tsp-43200;

// 24 heures
starttime = cur_tsp-86400;

endtime = cur_tsp;

