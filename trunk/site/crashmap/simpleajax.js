function loadwind(URL) {
	try {
		// Moz supports XMLHttpRequest. IE uses ActiveX.
		// browser detction is bad. object detection works for any browser
		xmlhttp = window.XMLHttpRequest?new XMLHttpRequest(): new ActiveXObject("Microsoft.XMLHTTP");
	} catch (e) {
		// browser doesn't support ajax. handle however you want
	}
	// the xmlhttp object triggers an event everytime the status changes
	// triggered() function handles the events
	xmlhttp.onreadystatechange = triggered;
	// open takes in the HTTP method and url.
	xmlhttp.open("GET", URL);
	// send the request. if this is a POST request we would have
	// sent post variables: send("name=aleem&gender=male)
	// Moz is fine with just send(); but
	// IE expects a value here, hence we do send(null);
	xmlhttp.send(null);
}

function triggered() {
	// if the readyState code is 4 (Completed)
	// and http status is 200 (OK) we go ahead and get the responseText
	// other readyState codes:
	// 0=Uninitialised 1=Loading 2=Loaded 3=Interactive
	if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
	// xmlhttp.responseText object contains the response.
		document.getElementById("wind").innerHTML = xmlhttp.responseText;
		//document.getElementById("wind").style = xmlhttp.responseText;
	}
}


function setBackgroundImage (id, URL, x, y) {
  imageURL=URL + '&zoom=' + zoom + "&lat=" + lat + "&long=" + lon;
  if (document.layers)
    document[id].background.src = imageURL == 'none' ? null : imageURL;
  else if (document.all)
    document.all[id].style.backgroundImage = imageURL == 'none' ? 'none' : 'url(' + imageURL + ')';
  else if (document.getElementById)
    document.getElementById(id).style.backgroundImage = imageURL == 'none' ? 'none' : 'url(' + imageURL + ')';
}


function DisplayPngByBrowser ( browser, img_path, width, height ) {
  if (browser == 'Microsoft Internet Explorer') {
      document.write('<img src="images/site/blank.gif" style="width:'+width+'px; height:'+height+'px; filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''+img_path+'\', sizingMethod=\'scale\');" >');
  }
  else if (browser == 'Netscape')
            document.write("<img src='"+img_path+"' />");
       else
            document.write("<img src='"+img_path+"' />");
  }

