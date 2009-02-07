function DisplayPngByBrowser ( browser, img_path, width, height ) {
    var png_path;
    if (browser == 'Microsoft Internet Explorer') {
        document.write('<img src="blank.gif" style="width:'+width+'px; height:'+height+'px; filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''+img_path+'\', sizingMethod=\'scale\');" />');
    }
    else if (browser == 'Netscape')
        document.write("<img src='"+img_path+"' />");
    else
        document.write("<img src='"+img_path+"' />");
}

function boutonPresse()
{
    origine_x = x - position_x;
    origine_y = y - position_y;
    clicEnCours = true;
}

function boutonRelache()
{
    clicEnCours = false;
}

function deplacementSouris(e)
{
    x = (netscape) ? e.pageX : event.x + document.body.scrollLeft;
    y = (netscape) ? e.pageY : event.y + document.body.scrollTop;
    
    if (clicEnCours && document.getElementById)
    {
	position_x = x - origine_x;
	position_y = y - origine_y;
	document.getElementById("deplacable").style.left = position_x ;
	document.getElementById("deplacable").style.top = position_y ;
    }
}


function previousTimestamp() {
    vts=document.control.vts.value;
    if ( vts >0 ) vts--;
    document.control.vts.value=vts;
    //showvts();
    for (ts=0; ts<=24 ; ts++) {
	vt=eval("ts" . ts);
        document.getElementById(vt).style.display = 'none' ;
    }
    vt=eval("ts" . document.control.vts.value);
    document.getElementById(vt).style.display = '' ;
}

function nextTimestamp() {
    vts=document.control.vts.value;
    if ( vts <24 ) vts++;
    document.control.vts.value=vts;
    showvts();
}
