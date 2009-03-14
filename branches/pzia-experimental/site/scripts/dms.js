//****convertir degré décimal en degré/minute/seconde****
function convertdmslat()
{
    var latdegre=eval(document.coordonnees.targetlat.value);
    if ( latdegre != undefined ) {
  var latsign = 'N';
  if ( latdegre < 0 ) {
            latsign='S';
  }
    latdegre=Math.abs(latdegre);
  
  var latdegent=parseInt(latdegre);
  var latdegdec=Math.round(((latdegre)-(latdegent))*10000000)/10000000;
  
  var latmintot=(latdegdec)*60;
  var latminent=eval(parseInt(latmintot));
  var latmindec=Math.round(((latmintot)-(latminent))*10000000)/10000000;
  
  var latsectot=(latmindec)*60;
  var latsecent=parseInt(latsectot);
  var latsecdec=Math.round(((latsectot)-(latsecent))*10000000)/10000000;
  var latsecmil=Math.round((latsectot)*10000)/10000;
  
  //dms concaténés pour la latitude
  document.coordonnees.latdms.value = (latdegent)+"° "+(latminent)+"' "+(latsecmil)+"\" "+(latsign);
  document.coordonnees.targetlat.title = (latdegent)+"° "+(latminent)+"' "+(latsecmil)+"\" "+(latsign);
    }
}

function convertdmslong()
{
    var longdegre=eval(document.coordonnees.targetlong.value);
    if ( longdegre != undefined ) {
  var longsign='E';
  if ( longdegre < 0 ) {
            longsign='W';
  }
  longdegre=Math.abs(longdegre);
  
  var longdegent=parseInt(longdegre);
  var longdegdec=Math.round(((longdegre)-(longdegent))*10000000)/10000000;
  
  var longmintot=(longdegdec)*60;
  var longminent=eval(parseInt(longmintot));
  var longmindec=Math.round(((longmintot)-(longminent))*10000000)/10000000;

  var longsectot=(longmindec)*60;
  var longsecent=parseInt(longsectot);
  var longsecdec=Math.round(((longsectot)-(longsecent))*10000000)/10000000;
  var longsecmil=Math.round((longsectot)*10000)/10000;

  //dms concaténés pour la longitude
  document.coordonnees.longdms.value=(longdegent)+"° "+(longminent)+"' "+(longsecmil)+"\" "+(longsign);
    }

}

function toggle_andhdg()
{
    if ( document.coordonnees.andhdg.checked ) {
  document.coordonnees.targetandhdg.disabled = false ;
        if ( document.coordonnees.targetandhdg.value != -1 ) {
            document.coordonnees.targetandhdg.value = Math.abs(document.coordonnees.targetandhdg.value)  ;
        } else {
            document.coordonnees.targetandhdg.value = "";
        }
    } else {
  document.coordonnees.targetandhdg.disabled = true ;
  document.coordonnees.targetandhdg.value = -1 * Math.abs(document.coordonnees.targetandhdg.value)  ;
    }
}

//****fin conversion degré décimal en degré/minute/seconde****
