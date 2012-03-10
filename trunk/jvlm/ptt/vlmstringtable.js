//VST : VLM String Table 
function VST() {
	VST.dico;
	if(typeof VST.initialized == "undefined") {
		VST.prototype.init = function(_stringtable) {
			VST.dico = _stringtable.strings;
		}
		VST.initialized=true;
	}
}

function initStringTable() {
	// appel WS synchrone sinon les String Table ne sont pas chargees lors de la construction de l'IHM...
	// TODO : le cache: true c'est comme faire pipi dans un script...
 	var tS=new Date().getTime();
 	$.ajaxSetup({ cache: true });
	$.ajax({  
		type: "GET",
		//data: {timestamp:tS},
		//data: { 'cache': 'true' },
		cache: true,
		url: ((typeof strlocvlmstringtable=="undefined")?'/ws/serverinfo/translation.php':strlocvlmstringtable),
		dataType: 'json',  
		async: false,  
		success: function(stringtable){  
			new VST().init(stringtable);
			  }  
		});  
}

