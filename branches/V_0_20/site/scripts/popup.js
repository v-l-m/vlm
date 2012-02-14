  function popup_small(URL,NOM) {
      window.open(URL, NOM, 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=850,height=500');
  }

  function popUp(URL,NOM) {
      eval("page" + NOM + " = window.open(URL, NOM, 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=800,height=595');");
  }

  function confirmation_abandon(messtr) {
      var answer = confirm(messtr);
      if (answer){
          alert("Bye bye !");
          document.abandon.submit();
      } else {
          alert("Ouf !");
      }
  }

function sendLogout()
{ 
    var xhr; 
    try {
        xhr = new ActiveXObject('Msxml2.XMLHTTP');
    } catch (e) {
        try {
            xhr = new ActiveXObject('Microsoft.XMLHTTP');
        } catch (e2) {
          try {
              xhr = new XMLHttpRequest();
          } catch (e3) { xhr = false;   }
        }
     }

   xhr.open( "GET", "/ws/logout.php",  false, 'test', 'ko'); 
   xhr.send(null);
   return true;
} 
