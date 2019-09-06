/* global webhost */
//webhost
/* jshint -W020*/
webhost = window.location.hostname;
/* jshint +W020*/

//Url for the cache
//tilesUrlArray = "/cache/gshhstiles/${z}/${x}/${y}.png";
//You may put an array here, example :
var tilesUrlArray = ["https://c1.v-l-m.org/gshhstiles/${z}/${x}/${y}.png", "https://c2.v-l-m.org/gshhstiles/${z}/${x}/${y}.png", "https://c3.v-l-m.org/gshhstiles/${z}/${x}/${y}.png", "https://c4.v-l-m.org/gshhstiles/${z}/${x}/${y}.png"];

// URI to distribute windgridrequest amount servers
//var WindGridServers = ["https://c1.v-l-m.org", "https://c2.v-l-m.org", "https://c3.v-l-m.org", "https://c4.v-l-m.org"];

if (webhost == 'vlm-dev.ddns.net')
{
  tilesUrlArray = '/cache/gshhstiles/${z}/${x}/${y}.png';

  WindGridServers = ['https://vlm-dev.ddns.net', 'https://vlm-dev.ddns.net', 'https://vlm-dev.ddns.net', 'https://vlm-dev.ddns.net'];

}
if (webhost == 'testing.v-l-m.org' || webhost == 'www.testing.v-l-m.org')
{
  tilesUrlArray = ["https://c1.testing.v-l-m.org/gshhstiles/${z}/${x}/${y}.png", "https://c2.testing.v-l-m.org/gshhstiles/${z}/${x}/${y}.png", "https://c3.testing.v-l-m.org/gshhstiles/${z}/${x}/${y}.png", "https://c4.testing.v-l-m.org/gshhstiles/${z}/${x}/${y}.png"];

  // URI to distribute windgridrequest amount servers
  WindGridServers = ["https://testing.v-l-m.org", "https://testing.v-l-m.org", "https://testing.v-l-m.org", "https://testing.v-l-m.org"];
  
}