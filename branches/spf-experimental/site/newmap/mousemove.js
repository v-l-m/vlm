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

	document.Show.MouseX.value = tempX - 30;
	document.Show.MouseY.value = tempY - 20;

	return true;
}

