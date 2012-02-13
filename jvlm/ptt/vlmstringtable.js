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