/* global webhost */
//webhost
webhost = window.location.hostname;

//Url for the cache
//tilesUrlArray = "/cache/gshhstiles/${z}/${x}/${y}.png";
//You may put an array here, example :
var tilesUrlArray = [ "https://c1.v-l-m.org/gshhstiles/${z}/${x}/${y}.png", "https://c2.v-l-m.org/gshhstiles/${z}/${x}/${y}.png", "https://c3.v-l-m.org/gshhstiles/${z}/${x}/${y}.png", "https://c4.v-l-m.org/gshhstiles/${z}/${x}/${y}.png" ];

if (webhost == 'vlm-dev.ddns.org')
{
    tilesUrlArray='/cache/gshhstiles/${z}/${x}/${y}.png'
}

