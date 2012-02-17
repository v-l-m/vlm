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
	this.cellTTS$;
	this.myTb$;
	this.hdg="180";
	this.twac="100"; 
	this.wplat="20";
	this.wplon="30";
	this.hwp="180";
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


		EO.prototype.bascEdit = function() {
			//hdg pour le pim=1, twa pour pim=2
			//mytabletosw$ = $("#tabs-" + this.key + ">form>table>tbody");
			//mytabletosw$.
			$("tbody", this.myTb$).children().remove();
			//pilototo_prog_upd
/*
			row$ = $('<TR/>', {'name':"ModHeader"}).appendTo($("tbody", this.myTb$));
			$('<p/>', {'text': "Order " + this.TID}).appendTo($('<TD/>').appendTo(row$));			
			$('<p/>', {'text': this.TTS}).appendTo($('<TD/>').appendTo(row$));	

			row$ = $('<TR/>', {'name':"ModDetail"}).appendTo($("tbody", this.myTb$));
			$('<p/>', {'text': "PIM : " + this.pim}).appendTo($('<TD/>').appendTo(row$));			
			$('<p/>', {'text': this.pip}).appendTo($('<TD/>').appendTo(row$));	
			//alert("bascule EDIT : " + this.TID + "-" + this.TTS);
*/
			EO.myGO.insertGO(this.myTb$,this.pim,eval(this.TTS),this.hdg, Pilototo.twac, this.wplat, this.wplon, this.hwp, true);
			this.myTb$.closest("form").find(':input').addClass('ui-corner-all').css({'font-size': '11px'});

		}


		EO.prototype.render = function() {
			//debug(" EO.Render1 ");
			var mytable = Pilototo.initTable();
			this.myTb$=mytable;
			//debug(" EO.Render2 ");
			row$ = $('<TR/>', {'name':this.key}).appendTo($("tbody", mytable));
			$('<p/>', {'text': this.TID}).appendTo($('<TD/>').appendTo(row$));			
			$('<p/>', {'text': this.TTS}).appendTo($('<TD/>').appendTo(row$));			
			row$ = $('<TR/>').appendTo($("tbody", mytable));
			$('<p/>', {'text': this.pim}).appendTo($('<TD/>').appendTo(row$));			
			$('<p/>', {'text': disp_pip(this.pip,this.pim) }).appendTo($('<TD/>').appendTo(row$));			
			row$ = $('<TR/>').appendTo($("tbody", mytable));
			$('<p/>', {'text': this.status}).appendTo($('<TD/>').appendTo(row$));			
			action$=$('<TD/>').appendTo(row$)
			//debug(" EO.Render3 ");
			if (this.status=="pending") {
				$('<IMG/>', {'src': 'ptt/img/imgupd.gif', 'name': this.TID, 'title':'Edit this element to modify order'})
					.appendTo(action$)
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