// EO : Existing order
function EO(_idu,_order,_key) {
	this.to='EO';
	this.idu=_idu;
	this.key=_key;
	this.TID=_order.TID;	
	this.TTS=_order.TTS;
	this.pip=_order.PIP;
	this.pim=_order.PIM;
	this.status=_order.STS;
	//this.cellTTS$;
	this.myTb$;

	switch(this.pim) 
	{
		case '1' :
			this.hdg=_order.PIP;
			this.twac=Pilototo.twac; 
			this.wplat=Pilototo.WPLAT;
			this.wplon=Pilototo.WPLON;
			this.hwp=Pilototo.HWP;
			//debug( this.pim + '-' + _order.PIP);
			break;
		case '2':
			this.hdg=Pilototo.HDG;
			this.twac=_order.PIP; 
			this.wplat=Pilototo.WPLAT;
			this.wplon=Pilototo.WPLON;
			this.hwp=Pilototo.HWP;
			//debug( this.pim + '-' + _order.PIP);
			break;
		case '3': case '4': case '5' :
			var reg=new RegExp("[,@]+", "g");
			var elts=this.pip.split(reg);
			this.hdg=Pilototo.HDG;
			this.twac=Pilototo.twac; 
			this.wplat=elts[0];
			this.wplon=elts[1];
			this.hwp=elts[2];
			//debug( this.pim + '-' + parseFloat(elts[0]) + '(,)' + parseFloat(elts[1]) +'(at)' + parseFloat(elts[2]));
		}

	EO.myGO = new GO();	

	
	if(typeof EO.initialized == "undefined") {
		EO.prototype.getDeletion = function() {
			//hdg pour le pim=1, twa pour pim=2
			var myJSONObject = {};
			myJSONObject.idu=this.idu;
			myJSONObject.taskid = parseInt(this.TID);
			myJSONObject.debug=true;
			return JSON.stringify(myJSONObject,null);
		}
		EO.prototype.myGO = function() {return EO.myGO;}

		EO.prototype.bascEdit = function() {
			//hdg pour le pim=1, twa pour pim=2
			//mytabletosw$ = $("#tabs-" + this.key + ">form>table>tbody");
			$("tbody", this.myTb$).children().remove();
			//debug(_order.PIP);
			$("thead>tr>th#bt", this.myTb$).children().remove();
			EO.myGO.insertGO(this.myTb$,this.pim,eval(this.TTS),this.hdg, Pilototo.twac, this.wplat, this.wplon, this.hwp, true);
			this.myTb$.closest("form").find(':input').addClass('ui-corner-all').css({'font-size': '11px'});

		}


		EO.prototype.render = function() {
			//debug(" EO.Render1 ");
			var mytable = Pilototo.initTable(this.status + ' order #' + this.TID);
			this.myTb$=mytable;
			row$ = $('<TR/>', {'name':this.key}).appendTo($("tbody", mytable));
			var d = new Date(parseInt(this.TTS)*1000).toUTCString();
			var myp = $('<p/>', {'html': this.TTS, 'title':d}).appendTo($('<TD/>', {'colspan':'3'}).appendTo(row$));
			myp.hover(function(){ helpin(mytable,this.title);},function(){$("tfoot>tr>th", mytable).find("p#Aide").remove();});

			row$ = $('<TR/>').appendTo($("tbody", mytable));
			myp=$('<p/>', {'text': disp_pip(this.pip,this.pim), 'title' : render_pip(this.pip,this.pim) }).appendTo($('<TD/>', {'colspan':'2'}).appendTo(row$));
			myp.hover(function(){ helpin(mytable,this.title);},function(){$("tfoot>tr>th", mytable).find("p#Aide").remove();});

			myp=$('<p/>', {'text': this.pim, 'title':GO.pimData[this.pim].Text}).appendTo($('<TD/>', {'colspan':'2'}).appendTo(row$));
			myp.hover(function(){ helpin(mytable,this.title);},function(){$("tfoot>tr>th", mytable).find("p#Aide").remove();});

			if (this.status=="pending") {
				$('<IMG/>', {'src': 'ptt/img/imgupd.gif', 'name': this.TID, 'title':'Edit this element to modify order'})
					.appendTo($("thead>tr>th#bt", mytable)) 
					.css({'border': '2px dotted #fff'})
					.hover(function(){ $(this).css({'border': '2px dotted red'}); }, function(){ $(this).css({'border': '2px solid #fff'});})
					.bind("click", function(event) {
						Pilototo.PILS[this.name].bascEdit();
					});
			} 
			return mytable;
		}
		EO.initialized=true;
	}
}