// NO : New Order
function NO(_idu,_est,_pip,_pim, _wplat,_wplon,_hwp,_twd) {
	this.to='NO';
	NO.idu=_idu;
	NO.TTS=_est; // epoch server time
	NO.pip=_pip; // current PIP 
	NO.pim=_pim; // current PIM
	NO.status="new"; // status
	//NO.hdg=_hdg; // consequent heading
	NO.wplat=_wplat; // consequent mywp lat
	NO.wplon=_wplon; // consequent mywp long
	NO.hwp=_hwp; // consequent wph
	//NO.twa=_twa; // consequent wind angle
	NO.myGO = new GO();

	NO.twd=_twd; // wind direction
	NO.mytable;


	if(typeof NO.initialized == "undefined") {

		NO.prototype.myGO = function() {return NO.myGO;}

		NO.prototype.render = function() {
			NO.mytable= Pilototo.initTable();
			NO.myGO.insertGO(NO.mytable,NO.pim,NO.TTS,Pilototo.HDG, Pilototo.twac, NO.wplat, NO.wplon, NO.hwp);
			return NO.mytable;
		}
		NO.initialized=true;
	}
}