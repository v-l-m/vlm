
var SrvIndex = 1;

// Global GribMap Manager
var Gribmap = {};

Gribmap.ServerURL = function()
{
  if (typeof WindGridServers !== "undefined" && WindGridServers)
  {
    SrvIndex = ((SrvIndex + 1) % WindGridServers.length);
    if (SrvIndex === 0)
    {
      SrvIndex = 1;
    }
    return WindGridServers[SrvIndex];
  }
  else
  {
    return "";
  }
};