clicEnCours = false;
position_x = 100;
position_y = 150;
netscape = false;

if (navigator.appName.substring(0,8) == "Netscape") {
  netscape = true;
}

function boutonPresse() {
  origine_x = x - position_x;
  origine_y = y - position_y;
  clicEnCours = true;
}

function boutonRelache() {
  clicEnCours = false;
}

function deplacementSouris(e) {
  x = (netscape) ? e.pageX : event.x + document.body.scrollLeft;
  y = (netscape) ? e.pageY : event.y + document.body.scrollTop;

  if (clicEnCours && document.getElementById) {
    position_x = x - origine_x;
    position_y = y - origine_y;
    document.getElementById("deplacable").setAttribute('style','left:'+position_x+'px;top:'+position_y+'px;position:absolute;');
  }
}
/*
if (netscape) {
  document.captureEvents(Event.MOUSEMOVE);
}
*/
document.onmousemove = deplacementSouris;
