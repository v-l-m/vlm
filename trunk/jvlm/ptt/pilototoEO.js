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
	function bascToEdit(key) {
		//NO.mytable.parent().submit();
		//key=$(event.target).get(0).name;
		// debug('Being implemented ' + key);
		mytabletosw$ = $("#tabs-" + key + ">form>table>tbody");
		mytabletosw$.children().remove();
		//$("tbody", mytable).children().remove();

		row$ = $('<TR/>', {'name':'TTS'}).appendTo(mytabletosw$);
		$('<p/>', {'text': 'TTS'}).appendTo($('<TD/>').appendTo(row$));			
		this.cellTTS$ = $('<input/>', {'name': 'TTS', 'type': 'text', 'value': Pilototo.PILS[key].TTS}).appendTo($('<TD/>').appendTo(row$));
		this.cellTTS$.addClass('ui-corner-all').css({'font-size': '11px'}).focus();

		row$ = $('<TR/>', {'name':'PIM'}).appendTo(mytabletosw$);
		$('<p/>', {'text': 'PIM'}).appendTo($('<TD/>').appendTo(row$));			
		cellpim$ = $('<input/>', {'name': 'PIM', 'type': 'text', 'value': Pilototo.PILS[key].pim}).appendTo($('<TD/>').appendTo(row$));
		cellpim$.addClass('ui-corner-all').css({'font-size': '11px'});


		row$ = $('<TR/>', {'name':'PIP'}).appendTo(mytabletosw$);
		$('<p/>', {'text': 'PIP'}).appendTo($('<TD/>').appendTo(row$));			
		cellpip$ = $('<input/>', {'name': 'PIP', 'type': 'text', 'value': Pilototo.PILS[key].pip}).appendTo($('<TD/>').appendTo(row$));
		cellpip$.addClass('ui-corner-all').css({'font-size': '11px'});
	}

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
			row$ = $('<TR/>', {'name':"ModHeader"}).appendTo($("tbody", this.myTb$));
			$('<p/>', {'text': "Order " + this.TID}).appendTo($('<TD/>').appendTo(row$));			
			$('<p/>', {'text': this.TTS}).appendTo($('<TD/>').appendTo(row$));	

			row$ = $('<TR/>', {'name':"ModDetail"}).appendTo($("tbody", this.myTb$));
			$('<p/>', {'text': "PIM : " + this.pim}).appendTo($('<TD/>').appendTo(row$));			
			$('<p/>', {'text': this.pip}).appendTo($('<TD/>').appendTo(row$));	
			//alert("bascule EDIT : " + this.TID + "-" + this.TTS);
			EO.myGO.insertGO(this.myTb$,this.pim,eval(this.TTS),this.hdg, Pilototo.twac, this.wplat, this.wplon, this.hwp);
			
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
/*
			if (this.status=="done" || this.status=="pending") {
				$('<IMG/>', {'src': 'includes/imgrem.gif', 'name': 'Remove', 'key':this.TID,'title': 'Remove'})
					.appendTo(action$)
					.css({'border': '2px dotted #fff'})
					.hover(function(){ $(this).css({'border': '2px dotted yellow'}); }, function(){ $(this).css({'border': '2px solid #fff'});});
				}
*/
			if (this.status=="pending") {
				$('<IMG/>', {'src': 'includes/imgupd.gif', 'name': this.TID, 'title':'Edit this element to modify order'})
					.appendTo(action$)
					.css({'border': '2px dotted #fff'})
					.hover(function(){ $(this).css({'border': '2px dotted red'}); }, function(){ $(this).css({'border': '2px solid #fff'});})
					.bind("click", function(event) {
						//bascToEdit($(event.target).get(0).name)
						debug("invalide");
					});
					//.dblclick( function () { alert("Hello World!"); });
			}
			return mytable;
		}
		EO.initialized=true;
	}
}