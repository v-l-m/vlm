function initPilototo() {
	$("#pttzone").find("div#tabs").remove();
	//$.getJSON('meso.json', 
	$.getJSON('/ws/boatinfo.php?select_idu=0', 
		{format: "json"}, 
		function(json){ 
			new Pilototo("relative onload",json);
			//debug("Boatinfo initialized.");
			Pilototo.prototype.render();
			//debug("Render done");
			}
		);
}

//PT : Pilototo 
function Pilototo(_orig,_json) {
	this.orig=_orig;
	Pilototo.TTS = _json.NOW;
	debug("Pilototo.TTS :" + _json.NOW);
	Pilototo.idu = _json.IDU;
	Pilototo.nom=_json.IDB;
	Pilototo.currentpip=_json.PIP;
	Pilototo.currentpim=_json.PIM;
	//this.wp=_json.WPLAT+","+_json.WPLON+"@"+_json["H@WP"];
	Pilototo.PILS=new Array;
	Pilototo.thcell;
	wa = _json.HDG - _json.TWD;
	Pilototo.twac=(wa < -180 ? wa + 360:(wa > 180 ? wa - 360 : wa)).toFixed(4);
	Pilototo.HDG = _json.HDG;

	Pilototo.initTable = function() {
		var mytable = $('<TABLE/>', {'class':'ptt'});
		$('<TFOOT/>').appendTo(mytable);
		$('<TR/>').appendTo($("tfoot", mytable));
		$('<TH/>', {'colspan':'2','scope':'col'}).appendTo($("tfoot>tr", mytable));
		$('<p/>', {'id': 'GMTTring','name': 'GMTTring', 'text' : (VST.initialized?VST.dico["VLM Programmable Auto Pilot"]:"V-L-M pilototo") + " for " + Pilototo.nom + " (" + Pilototo.idu + ")"}).addClass('ui-state-default ui-corner-all').css({'font-size': '11px'}).appendTo($("tfoot>tr>th", mytable));
		$('<TBODY/>').appendTo(mytable);
		return mytable;
	}

	function createDivTab(_idu) {
		var divtabs = $('<DIV/>', {'id': 'tabs'}).appendTo("#pttzone");
		$('<UL/>').appendTo(divtabs);
		return divtabs;
	}
	function addToDivTab(idx,_orderHTMLElement,_status,_pip,_pim,_TTS) {
		//debug(" > divtab0 ");
		var liN = $('<LI/>').appendTo(Pilototo.myPttDivTab.find("ul"));
		var myLabel;
		//debug(" > divtab1 ");
		switch (_status) {
				case "pending" :
					var myDiff = parseInt(_TTS)-Pilototo.TTS;
					var myDisp;
					var nbDay=parseInt(myDiff/86400);
					myDiff -= nbDay*86400;
					var nbHour=parseInt(myDiff/3600);
					myDiff -= nbHour*3600;
					var nbMin=parseInt(myDiff/60);
					var nbSec=myDiff%60;
					myDisp=(nbDay>0?nbDay+'d':'') + (nbHour>0?nbHour+'h':'') + (nbMin>0?nbMin+'\'':'') + (nbSec>0?nbSec+'\'\'':'');		
					myLabel= (myDisp !='' ? myDisp : '-late order-'); 
					break;
				case "done" : 
					myLabel="Done";
					break;
				case "new" :
					myLabel="New order";
					break;
				default :
					myLabel="??";
		}			
		//debug(" > divtab2 ");
		var monA=$('<A/>', {'href': '#tabs-' + idx.toString(), 'text': myLabel}).appendTo(liN);
		if (_status!="new"){
			monRem$=$('<SPAN/>', {'class':'ui-icon', 'TID':idx.toString()});
			monRem$.addClass("ui-icon-close");
			monRem$.appendTo(liN);
			monRem$.bind("click", function(event) {
				var index = $( "li", Pilototo.myPttDivTab.tabs()).index($(this).parent() );
				//debug("click tid : " + $(this).attr("tid") + "-idx=" + index);
				var mypost= '/ws/boatsetup/pilototo_delete.php?parms=' + escape(Pilototo.PILS[$(this).attr("tid")].getDeletion());
				//debug("--> : " + mypost);
				$.post(mypost, function(data) {
					if (!data.success) {
						alertModal(data.error.msg , data.error.code, data.error.custom_error_string);
					} else {
						//debug("Successfully removed " + data.request.taskid + " for " + data.request.idu);
						Pilototo.myPttDivTab.tabs( "remove", index );
						//TODO remettre éventuellement un new order...
					}
				}, 'json');
			});
		}
		
		var divtab1 = $('<DIV/>', {'id': 'tabs-'+idx.toString()})
			.appendTo(Pilototo.myPttDivTab);
		$(_orderHTMLElement).addClass('ui-corner-all');
		$(_orderHTMLElement).find("tbody>tr>td>input").addClass('ui-corner-all').css({'font-size': '11px'});
		$(_orderHTMLElement).find("tbody>tr>td>select").addClass('ui-corner-all').css({'font-size': '11px'});
		$(_orderHTMLElement).find("tbody>tr>td>p").addClass('ui-corner-all').css({'font-size': '11px'});
		var divform$=$('<FORM/>', {'name':idx, 'action':"javascript:"});
		divform$.append(_orderHTMLElement);
		var mysubmit$ = $('<input/>',{'type':'submit'}).appendTo(divform$);
		if ($.browser.msie) {
			mysubmit$.css({'height': '0px', 'width': '0px'}); //mysubmit$.css({'display':'none'});
		} else {
			mysubmit$.css({'visibility': 'hidden'}); //mysubmit$.css({'display': 'none'});
		}
		//debug(" > divtab3 ");

		divtab1.append(divform$);
		return divform$;
	}


	// chargement des ordres existants
	//debug("Chargement " + _json["PIL"].length);
	for (pils=0;pils < _json["PIL"].length;pils++) {
		Pilototo.PILS[_json["PIL"][pils].TID]=new EO(Pilototo.idu,_json["PIL"][pils],pils);
	}
	// préparation du nouvel ordre
	if (_json["PIL"].length<5){ 
		Pilototo.PILS['new']=new NO(Pilototo.idu, Pilototo.TTS, Pilototo.currentpip, Pilototo.currentpim,_json.WPLAT,_json.WPLON,_json["H@WP"],_json.TWD);
	}

	if(typeof Pilototo.initialized == "undefined") {
		// display du pilototo et de ses MAX5élémnents
		Pilototo.prototype.render = function() {
			Pilototo.myPttDivTab = createDivTab(this._idu);
			//debug("into render step1");
		
			for (order in Pilototo.PILS) { 
				//debug("treating order "+ order);

				if (Pilototo.PILS[order].to=='NO') {
					myform$=addToDivTab(order,Pilototo.PILS[order].render(),'new','','','');
					myform$.submit(function() {
						if (Pilototo.PILS["new"].myGO().validOrder()) {
							var myorder=Pilototo.PILS["new"].myGO().getOrder(); //
							var mypost= '/ws/boatsetup/pilototo_add.php?parms=' + escape(myorder);
							$.post(mypost, function(data) {
								if (!data.success) {
									alertModal(data.error.msg , data.error.code, data.error.custom_error_string);
								} else {
									//alert("Successfully added for " + data.request.idu);
									initPilototo();
								}
							}, 'json');
						}
						return false;
					});		
				} else {
					myform$ = addToDivTab(order, 
						Pilototo.PILS[order].render(), 
						Pilototo.PILS[order].status, 
						Pilototo.PILS[order].pip, 
						Pilototo.PILS[order].pim,
						Pilototo.PILS[order].TTS);

					myform$.dblclick( function (event) { 
						//alert("DblClick on form : " + $(event.target).closest("form").get(0).name); 
						//$(event.target).closest("form").get(0).name
						Pilototo.PILS[$(event.target).closest("form").get(0).name].bascEdit();
					});
					myform$.submit(function() {
						//debug('GUI update order : ' + this.name + ' with ' + this.TTS.value +',' + this.PIM.value + ',' + this.PIP.value)
						var myJSONObject = {};
						
						switch(this.PIM.value) // ou switch($(this).find('input[name$="PIM"]').val())
						{
							case '1' : case '2':
								myJSONObject.pip=parseFloat(this.PIP.value);
								break;
							case '3': case '4': case '5' :
								var myPIP={};
								var reg=new RegExp("[,@]+", "g");
								var elts=this.PIP.value.split(reg);
								myPIP.targetlat=parseFloat(elts[0]);
								myPIP.targetlong=parseFloat(elts[1]);
								myPIP.targetandhdg=parseFloat(elts[2]);
								myJSONObject.pip=myPIP;
						}
						//debug("chrome2");
						myJSONObject.idu=Pilototo.idu;
						myJSONObject.tasktime = parseInt(this.TTS.value);
						myJSONObject.taskid = parseInt(this.name);
						myJSONObject.pim=parseInt(this.PIM.value);
						myJSONObject.debug=true;

						var mypost= '/ws/boatsetup/pilototo_update.php?parms=' + escape(JSON.stringify(myJSONObject,null));
						debug(mypost);
						$.post(mypost, function(data) {
							if (!data.success) {
								alertModal(data.error.msg , data.error.code, data.error.custom_error_string);
							} else {
								debug("Successfully modified for " + data.request.idu);
								initPilototo();
							}
						}, 'json');

						return false;
					});		

				}
			}
			//debug("into render step2");

			// UI JQuery Tabs
			Pilototo.myPttDivTab.tabs({event: "mouseover"});
			Pilototo.myPttDivTab.tabs().addClass('ui-tabs-vertical ui-helper-clearfix');
			Pilototo.myPttDivTab.tabs().children('ul').children('li').removeClass('ui-corner-top').addClass('ui-corner-left');

			//positionne l'index sur le new order ... to be fixed if existing (<5)
			Pilototo.myPttDivTab.tabs('select', '#tabs-new');
			//un peu de confort 
			$(":input:visible:first").focus();
		}
		if ($.browser.msie) {
			$.getScript('includes/json2.js', function() {});
		}
		Pilototo.initialized=true;
	} 

}
