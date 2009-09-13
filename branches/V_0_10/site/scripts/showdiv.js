
var IE = document.all?true:false;
if (!IE) document.captureEvents(Event.MOUSEMOVE)
document.onmousemove = getMouseXY;

var tempX = 0;
var tempY = 0;

function getMouseXY(e) {
    if (IE) { // grab the x-y pos.s if browser is IE
  tempX = event.clientX + document.body.scrollLeft;
  tempY = event.clientY + document.body.scrollTop;
    }
    else {  // grab the x-y pos.s if browser is NS
  tempX = e.pageX;
  tempY = e.pageY;
    }  
    if (tempX < 0){tempX = 0;}
    if (tempY < 0){tempY = 0;}  

    return true;
}



function toggleDisplay(id){
    if (id.style.display=="none"){
  id.style.display="inline";
    }
    else {
  id.style.display="none";
    }
}

function showDivTopLeft(id, text, xSize , ySize ) {

    if ( xSize == 0 ) { xSize = "auto"; } 
    if ( ySize == 0 ) { ySize = "auto"; }

    document.getElementById(id).style.width = parseInt(xSize) + 'px';
    document.getElementById(id).style.height = parseInt(ySize) + 'px';

    document.getElementById(id).style.position = 'fixed';
    document.getElementById(id).style.left  = parseInt(50) + 'px';
    document.getElementById(id).style.top   = parseInt(50) + 'px';

    //document.getElementById(id).style.display = 'inline';
    document.getElementById(id).style.visibility = 'visible';
    document.getElementById(id).innerHTML = text;
}

function showDivLeft(id, text, xSize , ySize ) {

    if ( xSize == 0 ) { xSize = "auto"; } 
    if ( ySize == 0 ) { ySize = "auto"; }

    document.getElementById(id).style.width = parseInt(xSize) + 'px';
    document.getElementById(id).style.height = parseInt(ySize) + 'px';

    document.getElementById(id).style.left = parseInt(tempX+15) + 'px';
    document.getElementById(id).style.top  = parseInt(tempY+15) + 'px';

    //document.getElementById(id).style.display = 'inline';
    document.getElementById(id).style.visibility = 'visible';
    document.getElementById(id).innerHTML = text;
}

function showDivRight(id, text, xSize , ySize ) {

    if ( xSize == 0 ) { 
        xSize = 400 ; 
    } 
    document.getElementById(id).style.width = parseInt(xSize) + 'px';

    if ( ySize == 0 ) { 
  document.getElementById(id).style.height = 'auto';
    } else {
  document.getElementById(id).style.height = parseInt(ySize) + 'px';
    }

    document.getElementById(id).style.left = parseInt(tempX - parseInt(document.getElementById(id).style.width)) + 'px';
    document.getElementById(id).style.top  = parseInt(tempY+15) + 'px';

    //document.getElementById(id).style.display = 'inline';
    document.getElementById(id).style.visibility = 'visible';
    document.getElementById(id).innerHTML = text;
}

function hideDiv(id){
    //document.getElementById(id).style.display = 'none';
    document.getElementById(id).style.visibility = 'hidden';
    document.getElementById(id).innerHTML = "";
}
