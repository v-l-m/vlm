function createdialog() {
	//return $(":div:first").dialog({id:"dialog-message",autoOpen: false,title: 'Pilototo'});
	return $("<div/>").dialog({id:"dialog-message",autoOpen: false,title: 'Pilototo'});
}
function alertModal(_msg , _code, _custom_error_string) {
	$dialog.get(0).innerHTML="<p><span class=\"ui-icon ui-icon-circle-check\" style=\"float:left; margin:0 7px 50px 0;\"></p>An error occured.</span><p><b>" + _msg +"</b>.</p><p>" + _code + "</p><p>"+ _custom_error_string + "</p>";
	$dialog.dialog('open');	
}
function htmlDecode(value){ 
  return $('<div/>').html(value).text(); 
}
/*
function htmlEncode(value){ 
    if (value) {
        return $('<div/>').text(value).html(); 
    } else {
        return '';
    }
}
*/
function debug(str) 
{
	$('<p/>', {text : str}).appendTo($("#tracezone"));
}
function helpin(table$,sztext) 
{
	$("tfoot>tr>th", table$).find("p#Aide").remove();
	$('<p/>', {id: 'Aide', name: 'Aide', html : sztext})
		.addClass('ui-state-default ui-corner-all help')
		.css({'font-size': '11px'})
		.appendTo($("tfoot>tr>th", table$));
}
function helpout(keyst){
	$("#helpzone").find("p#AideOut").remove();
	//debug(VST.dico[keyst]);
	//debug(htmlDecode(VST.dico[keyst]));
	$('<p/>', {id: 'AideOut', name: 'AideOut', html : VST.dico[keyst]}) 
		.addClass('ui-state-default ui-corner-all help')
		.css({'font-size': '11px', 'width': '31em'})
		.appendTo("#helpzone");
}
function pad(val)
{
    var s = val.toString();
    return s.length < 2 ? "0" + s : s
}
function validate_pim(pim$,pip1$,pip2$,pip3$) {
	var test;
	switch(pim$.find(":selected").val()) {
	case '1':
		var reg=new RegExp("^([0-9]{1}|[1-9][0-9]|[1-2][0-9][0-9]|3[0-5][0-9])([.]{1}[0-9]{1,5})?$","i");
		test = reg.test(pip1$.val());
		if(!test){alert('Incorrect heading.')};
		break;
	case '2':
		var reg=new RegExp("^[-]?(([1-9][0-9]|[0-9]{1}|1[0-7][0-9])([.]{1}[0-9]{1,5})?|180)$","i");
		test = reg.test(pip1$.val());
		if(!test){alert('Incorrect wind angle.')};
		break;
	case '3': case'4': case '5' :
		var reg1=new RegExp("^[-]?(([0-9]{1}|[1-8]{1}[0-9]{1})([.]{1}[0-9]{1,10})?|90)?$","i");
                if(!reg1.test(pip1$.val())){alert('Incorrect Lat.')};
		var reg2=new RegExp("^[-]?(([1-9][0-9]|[0-9]{1}|1[0-7][0-9])([.]{1}[0-9]{1,10})?|180)?$","i");
		if(!reg2.test(pip2$.val())){alert('Incorrect Lon.')};
		var reg3=new RegExp("^(([0-9]{1}|[1-9][0-9]|[1-2][0-9][0-9]|3[0-5][0-9])([.]{1}[0-9]{1,6})?|([-]1([.][0]{1,6})?)?)$","i");
		if(!reg3.test(pip3$.val())){alert('Incorrect @wph.')};
		test = reg1.test(pip1$.val()) && reg2.test(pip2$.val()) && reg3.test(pip3$.val());
	}
	return(test);
}
function disp_pip(_pip,_pim) {
	var disppip='';
	switch(_pim)
	{
		case '1' : case '2':
			disppip=parseFloat(_pip);
			break;
		case '3': case'4': case '5' :
			var reg=new RegExp("[,@]+", "g");
			var elts=_pip.split(reg);
			disppip = parseFloat(elts[0])+","+parseFloat(elts[1])+"@"+parseFloat(elts[2]);
	}
	return(disppip);
}
function render_pip(_pip,_pim) {
	var disppip='';
	switch(_pim)
	{
		case '1' : 
			disppip="HEADING:"+parseFloat(_pip);
			break;
		case '2':
			disppip="WIND ANGLE:"+parseFloat(_pip);
			break;
		case '3': case'4': case '5' :
			var reg=new RegExp("[,@]+", "g");
			var elts=_pip.split(reg);
			disppip = "LAT:"+parseFloat(elts[0])+"<br \/>LON:"+parseFloat(elts[1])+"<br \/>WPH:"+parseFloat(elts[2]);
	}
	return(disppip);
}
//GUI Order
function GO() {
	//pim constant 
	GO.pimData = {
	    "1":{ Text: "1:" + (VST.initialized?VST.dico["autopilotengaged"]:"Constant Heading"), Value: "1" },
	    "2":{ Text: "2:" + (VST.initialized?VST.dico["constantengaged"]:"Constant Wind Angle"), Value: "2" },
	    "3":{ Text: "3:" + (VST.initialized?VST.dico["orthoengaged"]:"Orthodromic pilot"), Value: "3"},
	    "4":{ Text: "4:" + (VST.initialized?VST.dico["bestvmgengaged"]:"Best VMG"), Value: "4"},
	    "5":{ Text: "5:" + (VST.initialized?VST.dico["vbvmgengaged"]:"VBVMG"), Value: "5"}};
	//GO.pimDataParsed = $.parseJSON( GO.pimData );//JSON.parse(GO.pimData);
	// declares graphical component
	GO.ttsinput; GO.selPIM; GO.pipcell; GO.pipinput1; GO.pipinput2; GO.pipinput3;

	if(typeof GO.initialized == "undefined") {

		GO.prototype.validOrder = function() {
			return validate_pim(GO.selPIM, GO.pipinput1,GO.pipinput2,GO.pipinput3);
		}
		GO.prototype.getOrder = function(_tid) {
			//hdg pour le pim=1, twa pour pim=2
			var myJSONObject = {};
			if (_tid != null) myJSONObject.taskid=_tid;
			myJSONObject.idu=NO.idu;
			myJSONObject.tasktime = parseInt(GO.ttsinput.val());
			myJSONObject.pim=parseInt(GO.selPIM.find(":selected").val());
			switch(GO.selPIM.find(":selected").val()) {
				case "1": 
					myJSONObject.pip=parseFloat(GO.pipinput1.val());
					break;
				case "2" :
					myJSONObject.pip=parseFloat(GO.pipinput1.val());
					break;
				case "3": case "4" : case "5" :
					var myPIP={};
					myPIP.targetlat=parseFloat(GO.pipinput1.val());
					myPIP.targetlong=parseFloat(GO.pipinput2.val());
					myPIP.targetandhdg=parseFloat(GO.pipinput3.val());
					myJSONObject.pip=myPIP;
					break;
			}
			myJSONObject.debug=true;
			//var test= JSON.encode(myJSONObject);

			return JSON.stringify(myJSONObject,null);
		}
		
		GO.prototype.renderPIM = function (_hdg, _twac, _wplat, _wplon, _hwp) {
			switch(GO.selPIM.find(":selected").val()) {
				case "1":
					GO.pipinput1 = $('<input/>', {'name': 'pipinput1', 'type': 'text', 'value': _hdg}).appendTo(GO.pipcell);
					break;
				case "2" :
					GO.pipinput1 = $('<input/>', {'name': 'pipinput1', 'type': 'text', 'value': _twac}).appendTo(GO.pipcell);
					break;
				case "3": case "4" : case "5" :
					GO.pipinput1 = $('<input/>', {'name': 'pipinput1', 'type': 'text', 'class': 'small', 'value': _wplat}).appendTo(GO.pipcell);
					GO.pipinput2 = $('<input/>', {'name': 'pipinput2', 'type': 'text', 'class': 'small', 'value': _wplon}).appendTo(GO.pipcell);
					//$('<br/>').appendTo(GO.pipcell);
					//$('<p/>', {'text':'@Wph'}).appendTo(GO.pipcell);
					GO.pipinput3 = $('<input/>', {'name': 'pipinput3', 'type': 'text', 'class': 'small', 'value': _hwp}).appendTo(GO.pipcell);
					break;
			}
		}
		
		GO.prototype.insertGO = function (_tb,_pim,_tts, _hdg, _twac, _wplat, _wplon, _hwp, _bupdate) {
			row$ = $('<TR/>').appendTo($("tbody", _tb));

			// CALENDRIER
			var datec = new Date(parseInt($(_tts).get(0))*1000);
			var datea = pad(datec.getUTCDate()) + '/' + pad((datec.getUTCMonth() + 1 )) + '/' + datec.getUTCFullYear() + ' ' +
						pad(datec.getUTCHours()) + ":" +  pad(datec.getUTCMinutes()) + ":" + pad(datec.getUTCSeconds());
			cell$=$('<TD/>', {'class':'neworder','colspan':'3'}).appendTo(row$)

			//TTSnew
			GO.ttsinput=$('<input/>', {'name': 'ttsinput', 'type': 'text', 'value': _tts})
				.appendTo(cell$);					
			GO.ttsinput.bind("change", function(){ 
				$(this).val(eval($(this).val()));
				var dc = new Date(parseInt($(this).val())*1000);
				var da = pad(dc.getUTCDate()) + '/' + pad((dc.getUTCMonth() + 1 )) + '/' + dc.getUTCFullYear() + ' ' +
						pad(dc.getUTCHours()) + ":" +  pad(dc.getUTCMinutes()) + ":" + pad(dc.getUTCSeconds());
				$("#calendar").val(da);
			});	
			GO.ttsinput.hover(
               		function(){ 
					var d = new Date(parseInt($(this).val())*1000).toUTCString();
					helpin(_tb,d);
					//var $tableHeaders = $("thead > th", _tb).filter(":not([colspan]),[colspan='1']");					
					helpout("pilototohelp3");
				},
				function(){  helpout(""); $("tfoot>tr>th", _tb).find("p#Aide").remove();
				}
			);
			
			dt$=$('<input/>', {'type': 'hidden', 'id': 'calendar', 'name': 'calendar', 'value': datea})
				.appendTo(cell$);
			dt$.datetimepicker({showOn: "button",
				buttonImage: "../externals/jscalendar/img.gif", buttonImageOnly: true,
				defaultDate: datea,	showSecond: true, dateFormat: 'dd/mm/yy', timeFormat: 'hh:mm:ss', showButtonPanel: false});
			//debug(datea + "-" + datec.getUTCHours());

			dt$.datetimepicker('setDate', datec );
			//dt$.datetimepicker('setTime', datec );
			//dt$.datetimepicker('setDate', (new Date()) );
			dt$.bind("change", function(){ 
				var reg=new RegExp("[/ :]+", "g");var elts=$(this).val().split(reg);
				var dt=new Date(parseInt(elts[2]),parseInt(elts[1])-1,parseInt(elts[0]),parseInt(elts[3]),parseInt(elts[4]),parseInt(elts[5]));
				var myEpoch = dt.getTime()/1000.0 - dt.getTimezoneOffset() * 60;
				GO.ttsinput.val(myEpoch);
			})
			// PIM : 
			row$ = $('<TR/>').appendTo($("tbody", _tb));
			GO.selPIM = $('<select/>', {'name': 'mypim'})
				.appendTo($('<TD/>', {'class':'neworder','colspan':'3'})
					.appendTo(row$));
			$.each(GO.pimData, function(val, text) {
    				$('<option/>', {'id': text.Value, 'value': text.Value, 'html': text.Text, 'selected':(_pim==text.Value)}).appendTo(GO.selPIM);
			});
			GO.selPIM.bind("change", function(){ 
				GO.pipcell.children().remove();
				GO.prototype.renderPIM(_hdg, _twac, _wplat, _wplon, _hwp);
				GO.pipcell.find("input").addClass('ui-corner-all').css({'font-size': '11px'});
			});
			GO.selPIM.hover(function(){ helpout("pilototohelp4")},function(){ helpout("")});

			row$ = $('<TR/>').appendTo($("tbody", _tb));
			// PIP
			GO.pipcell = $('<TD/>', {'class':'neworder','colspan':'3'}).appendTo(row$); 
			GO.pipcell.hover(function(){ helpout("pilototohelp5")},function(){ helpout("")});


			GO.prototype.renderPIM(_hdg, _twac, _wplat, _wplon, _hwp);
			// ACTIONS	
			img$=$('<IMG/>', {
					'src': (_bupdate ? 'ptt/img/actn022.gif' : 'ptt/img/imgplus.gif'), 
					'name': 'AddNewOrder', 
					'title': (VST.initialized?VST.dico[(_bupdate ? "pilototo_prog_upd":"pilototo_prog_add")]:(_bupdate ? "Modify the order":"Add the new order"))
				}).appendTo($("thead>tr>th#bt", _tb)); 
				//.appendTo($('<TD/>', {'class':'neworder'}).appendTo(row$)); 
			img$.css({'border': '2px dotted #fff'})
				.hover(
					function(){ $(this).css({'border': '2px dotted lightgreen'}); }, //$(this).get(0).src='includes/actn046.gif';
					function(){ $(this).css({'border': '2px solid #fff'});});
			img$.bind("click", function(event) {
				_tb.parent().submit();
			});
		}
		GO.initialized=true;
	}
}