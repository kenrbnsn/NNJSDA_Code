<?php
if (!extension_loaded('json')) {
   $prefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
   dl($prefix . 'json.' . PHP_SHLIB_SUFFIX);
}
$subject = 'Form Data Tester';
$ver = "(0.04(2.3.0))";
$pageaddr = 'homepage';
include ('emailtracker.php.inc');
if (isset($_POST['trace'])) exit('ok');
if (isset($_POST['X'])) exit(print_r($_POST,true));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
	<head>
	<title>YUI getData Tester</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
                <link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.3.0/build/container/assets/skins/sam/container.css">
                <link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.3.0/build/fonts/fonts-min.css">
                <link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.3.0/build/calendar/assets/calendar.css">
                <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.3.0/build/button/assets/skins/sam/button.css">
                <link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.3.0/build/logger/assets/skins/sam/logger.css">
		<style>
		
		.x {
			display: block;
			margin-left: auto;
			margin-right: auto;
				margin-top: 50px;
			width: 800px;
		}

			#cal1Container #calMonthContainer{
				display: block;
				margin-left: auto;
				margin-right: auto;
				width: 800px;
				background-color: pink;
				overflow:visible;
			}
			
			
			#container {
				display: block;
				width: 800px;
			}
			
			#container0 {
				display: block;
				width: 775px;
			}
				
			.yui-calcontainer .title {
				display: block;
				width: 100%;
				text-align: center;
				color: Red;
				font-size: 150%;
				height: 35px;
			}

			.appt {
				background-color: Aqua;
			}
			.yui-calendar td.calcell.selected {
				background-color:#FFF;
			}
			
			.yui-calendar td.calcell.calcellhover {
				cursor:pointer;
				background-color:#B0E0E6;
				color: Blue;
			}
			
			#calMonthContainer .yui-calendar td.calcellhover {
				background-color: #B0E0E6;
				color: Blue;
			}
						
		#calMonthContainer .yui-calendar td.calcell  {
			width: 110px;
			height: 150px;
			text-align: right;
			vertical-align: top;
			overflow: visible;
		}

		.row {
			clear: both;
			padding-top: 0.2em;
			padding-left: 0.2em;
		}

		.row span.label2 {
			float: left;
			font-weight: bold;
			text-align: left;
			width: 25%;
		}
		
		.row span.formw {
			float: left;
			text-align: left;
			width: 74%;
		}

		.row span.thirda {
			float: left;
			text-align: left;
			width: 25%;
			max-width: 25%;
			display: block;
			padding: 0 2px 0 2px;
			font-size: 90%;
		}
		
		.boldcu {
			text-align: center;
			text-decoration: underline;
			font-weight: bold;
			width: 100%;
			display: block;
		}

		.boldu {
			font-weight: bold;
			text-decoration: underline;
		}
		
		.center {
			display: block;
			text-align: center;
			width: 100%;
		}
		
		.txtinp {
			width: 98%;
		}
		
                                          

		</style>

		
		<script type="text/javascript" src="http://yui.yahooapis.com/2.3.0/build/yahoo-dom-event/yahoo-dom-event.js"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.3.0/build/dragdrop/dragdrop-min.js"></script> 
		<script type="text/javascript" src="http://yui.yahooapis.com/2.3.0/build/element/element-beta-min.js"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.3.0/build/connection/connection-min.js"></script>

		<script type="text/javascript" src="http://yui.yahooapis.com/2.3.0/build/container/container-min.js"></script>

		<script type="text/javascript" src="http://yui.yahooapis.com/2.3.0/build/calendar/calendar-min.js"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.3.0/build/button/button-beta-min.js"></script>			
		<script type="text/javascript" src="http://yui.yahooapis.com/2.3.0/build/logger/logger-min.js"></script>
				
		<script>
				var dts = new Array();
				var All_Event_Ids = new Array();
				var Event_Ids = new Array();
				var evid = 0;
				var link_clicked = new Boolean;
				var dialog1, event_panel;
				var eventSaved = new Boolean;
				var savedEvent = new Array();
				eventSaved = false;
				link_clicked = false;
				function array_keys(ary) {
					var temp = new Array();
					var indx = 0;
					for (var i in ary) {
					    temp[indx++] = i;
					}
					return (temp);
				}

				function init() {
				var cal1;
				var myLogReader = new YAHOO.widget.LogReader('myLogger');
        var cbnull = function(){}

        var cb =
        {
                success:cbnull,
                failure:cbnull
        }

        var cbGetEvent = function(o){
		  		tmp = o.responseText.split('~');
				YAHOO.log(tmp[2],'trac','cbGetEvent, tmp[2]');
				var form_data = eval( '(' + tmp[2] + ')' );
				document.getElementById("dlgForm").reset()
				event_panel.setHeader(tmp[0]);
				for (var key in form_data) {
				    switch (key) {
					case 'evid':
						document.getElementById(key).value = form_data[key];
						break;
					case 'description':
					case 'url':
					case 'start_time':
					case 'stop_time':
					case 'location':
					case 'address':
					case 'city':
					case 'state':
					case 'zip':
					case 'caller':
					case 'cuer':
					case 'name':
					case 'date':
						document.getElementById('event_' + key).value = form_data[key];
						break;
					case 'rd_program':
					case 'sd_program':
						var tmpp = form_data[key].split(" ");
						YAHOO.log(form_data[key],'trac','cbGetEvent, form_data[' + key + ']');
						YAHOO.log(tmpp.length,'trac','cbGetEvent', tmpp.length);
						if (tmpp.length > 0) {
							for (var p=0;p<tmpp.length;p++) 
								if (tmpp[p] != '')
									document.getElementById('event_' + key + '_' + tmpp[p]).checked = true;
						}
						break;
					case 'org':
					case 'type':
						fd = form_data[key].replace(/ /g,'_');
						fd = fd.replace(/&/,'and');
						YAHOO.log(fd,'trac','cbGetEvent, form_data[' + key + ']');
						YAHOO.log('event_' + key + '_' + fd,'trac','cbGetEvent');
						document.getElementById('event_' + key + '_' + fd).selected = true;
						break;
					case 'contact_name':
					case 'contact_email':
					case 'contact_phone':
						for (var i=0;i<3;i++)
							document.getElementById('event_' + key + '_' + i).value = form_data[key][i];
						break;
				    }
				}
//				event_panel.setBody(tmp[2]);
				var myButtons = [ { text:"Update", handler:updateEvent, isDefault:true },
							{ text:"Delete", handler:deleteEvent },
							{ text:"Save Event", handler:saveEvent },
							{ text:"Restore Event", handler: restoreEvent },
							{ text:"Clear Saved Event", handler: clearEvent },
							{ text:"Cancel", handler:handleCancel } ];
				event_panel.cfg.queueProperty("buttons", myButtons);
				event_panel.render();
		  		waitPanel.hide();
				event_panel.show();
		  }

        var cbev =
        {
                success:cbGetEvent,
                failure:cbnull
        }

	function fnCallback(e) { 
		link_clicked = true;
		evid = this.id.split('_');
		pass_str = 'trace=1&loc=fnCallback&id=' + evid[1];
		gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
		pass_str = 'evid=' + evid[1];
		gc  = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cbev,pass_str);
		waitPanel.setBody('Retrieving Event Information');
		waitPanel.render();
		waitPanel.show();
		}
		
	renderAppt = function(workingDate, cell) {
	   this.renderCellDefault(workingDate, cell);
	   this.styleCellDefault(workingDate, cell);
		YAHOO.util.Dom.addClass(cell, "appt");
		return YAHOO.widget.Calendar.STOP_RENDER;
	}
		var mySelectHandler = function(type,args,obj) {
			var arrDates = cal1.getSelectedDates();
			var date = arrDates[0];
			test_date = date.getMonth()+1 + '/' + date.getFullYear();
			test_date1 = date.getMonth()+1 + '/' + date.getDate() + '/' + date.getFullYear();
			pass_str = 'date=' + test_date1 + '&trace=1&loc=mySelectHandler';
			gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			panelYear.hide();
			calMonth.cfg.setProperty("pagedate",test_date);
			calMonth.render();
			panel.show();
		}

		var myRenderHandler = function(type,args,obj) {
		      if (this.pages && this.pages.length > 0) {
		         // If CalendarGroup, use pagedate from 1st Cal page
		         var td = this.pages[0].cfg.getProperty("pagedate"); 
		      } else {
		         var td = this.cfg.getProperty("pagedate");
		      }
			var test_date = td.getMonth()+1 + '/' + td.getFullYear();
			var Event_Ids = new Array();
			var evi = 0;
			for (var i = 0;i< All_Event_Ids.length;i++) {
				test_mnt = All_Event_Ids[i].split(':');
				if (test_mnt[1] == test_date) {
					Event_Ids[evi++] = test_mnt[0];
				}
			}
			YAHOO.log('month_year = ' + test_date,'trac','myRenderHandler');
			YAHOO.log('Event_Ids = ' + Event_Ids,'trac','myRenderHandler');
               pass_str = 'trace=1&loc=myRenderHandler&month_year='+test_date+'&events='+Event_Ids.join(',');
               gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
					YAHOO.util.Event.addListener(Event_Ids, "click", fnCallback);
		};
		
		
		var monthSelectHandler = function(type,args,obj) {
			var arrDates = calMonth.getSelectedDates();
			var date = arrDates[0];
			YAHOO.log('date =' + date,'trac','monthSelectHandler');
			test_date =  date.getFullYear();
			test_date1 = date.getMonth()+1 + '/' + date.getDate() + '/' + date.getFullYear();
			pass_str = 'date=' + test_date1 + '&trace=1&loc=monthSelectHandler';
			gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			if (link_clicked) {
				link_clicked = false;
				}
			else {
				document.getElementById("dlgForm").reset()
				event_panel.setHeader('Add Event<br>' + test_date1);
				document.getElementById("event_date").value = test_date1;
				var myButtons = [ { text:"Add Event", handler:addEvent, isDefault:true },
										{ text:"Save Event", handler:saveEvent },
										{ text:"Restore Event", handler: restoreEvent },
										{ text:"Clear Saved Event", handler: clearEvent },
										{ text:"Cancel", handler:handleCancel } ];
				event_panel.cfg.queueProperty("buttons", myButtons);
				event_panel.render();
				event_panel.show();

			}
//			this.hide();
//			cal1.show();
		}

	var handleSubmit = function() {
	 var savedEvent = this.getData();
	 var tmp = new Array();
	 var i = 0;
	 for (var fld in savedEvent)
		tmp[i++] = fld + ' ==> ' + savedEvent[fld];
	 YAHOO.log('form data: [' + tmp.join(']\n[') + ']','trac','handleSubmit');
	 this.submit();
	 this.show();
	};
	var handleCancel = function() {
		document.getElementById("dlgForm").reset();
		this.cancel();
		this.show();
	};
	var handleSuccess = function(o) {
		var response = o.responseText;
		YAHOO.log(response,'trac','handleSuccess');
	};
	
	var AddUpDelSuccess = function(o) {
		var response = o.responseText;
		YAHOO.log(response,'trac','AddUpDelSuccess');
		waitPanel.hide();
	};
	
	var handleFailure = function(o) {
		YAHOO.log("Submission failed: " + o.status,'trac','handleFailure');
		waitPanel.hide();
	};
	
	
function displayComment(e){
	pass_str = 'trace=1&loc=displayComment';
	gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
	document.getElementById("comment_name").value = '';
	document.getElementById("comment_email").value = '';
	document.getElementById("comment_comment").value = '';
	dialog1.show();
}
function returnToYear(e) {
         pass_str = 'trace=1&loc=returnToYear';
         gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			panel.hide();
			for (var i = 0;i<Event_Ids.length;i++)
				YAHOO.util.Event.removeListener(Event_Ids[i], "click", fnCallback);
			panelYear.show();
    }

function updateEvent(e) {
         pass_str = 'trace=1&loc=updateEvent';
         gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			document.getElementById('add_up_del_flag').value='U';
			YAHOO.log('up_del_flag: ' + document.getElementById('add_up_del_flag').value,'trac','updateEvent');
			YAHOO.log('Current form data (add_up_del_flag): ' + event_panel.getData().add_up_del_flag,'trac','updateEvent');
			YAHOO.log('Current form data (evid): ' + event_panel.getData().evid,'trac','updateEvent');
			waitPanel.setBody('Updating Event Information');
			waitPanel.render();
			waitPanel.show();
			this.callback.success = AddUpDelSuccess;
			this.callback.failure = handleFailure;
			this.submit();
    }

function deleteEvent(e) {
         pass_str = 'trace=1&loc=deleteEvent';
         gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			document.getElementById('add_up_del_flag').value='D';
			YAHOO.log('up_del_flag: ' + document.getElementById('add_up_del_flag').value,'trac','deleteEvent');
			YAHOO.log('Current form data (up_del_flag): ' + event_panel.getData().add_up_del_flag,'trac','deleteEvent');
			YAHOO.log('Current form data (evid): ' + event_panel.getData().evid,'trac','deleteEvent');
			waitPanel.setBody('Deleting Event');
			waitPanel.render();
			waitPanel.show();
			this.callback.success = AddUpDelSuccess;
			this.callback.failure = handleFailure;
			this.submit();
    }

function saveEvent(e) {
         pass_str = 'trace=1&loc=saveEvent';
         gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
	 savedEvent = event_panel.getData();
	 eventSaved = true;
	 var tmp = new Array();
	 var i = 0;
	 for (var fld in savedEvent)
		tmp[i++] = fld + ' ==> ' + savedEvent[fld];
	 YAHOO.log('Current saved form data: [' + tmp.join(']\n[') + ']','trac','saveEvent');
    }

function restoreEvent(e) {
         pass_str = 'trace=1&loc=restoreEvent';
         gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
	 var tmp = new Array();
	 var i = 0;
	 for (var fld in savedEvent)
		tmp[i++] = fld + ' ==> ' + savedEvent[fld];
	 YAHOO.log('Current saved form data: [' + tmp.join('][') + ']','trac','restoreEvent');
    }

function clearEvent(e) {
         pass_str = 'trace=1&loc=clearEvent';
         gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
	 YAHOO.log('','trac','clearEvent');
	 savedEvent = '';
	 eventSaved = false;
    }

function addEvent(e) {
         pass_str = 'trace=1&loc=addEvent';
         gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			document.getElementById('add_up_del_flag').value='A';
			YAHOO.log('up_del_flag: ' + document.getElementById('add_up_del_flag').value,'trac','addEvent');
			YAHOO.log('Current form data (up_del_flag): ' + event_panel.getData().add_up_del_flag,'trac','addEvent');
			YAHOO.log('Current form data (evid): ' + event_panel.getData().evid,'trac','addEvent');
			waitPanel.setBody('Adding Event');
			waitPanel.render();
			waitPanel.show();
			this.callback.success = AddUpDelSuccess;
			this.callback.failure = handleFailure;
			this.submit();
    }

	addText = function(workingDate, cell) {
	   this.renderCellDefault(workingDate, cell);
	   this.styleCellDefault(workingDate, cell);
	
	   var div = document.createElement("div");
		dt = workingDate.getMonth()+1 + '/' + workingDate.getDate() + '/' + workingDate.getFullYear();
		evtmp = dts[dt].split('`');
		div.innerHTML = '';
		for (var i = 0;i<evtmp.length;i++) {
			ev_id = evtmp[i].split('~');
	   		div.innerHTML += "<div id='id_" + ev_id[1] + "'><span style='font-size:75%;text-align:left;color:red;font-weight:bold;width:100%;display:block'><a href='#'>" + ev_id[0] + "</a></span></div>";
		}
	   	cell.appendChild(div);
		YAHOO.util.Dom.addClass(cell, "appt");
		return YAHOO.widget.Calendar.STOP_RENDER;
	}

				
				event_panel = new YAHOO.widget.Dialog("event_panel", { width:"700x",
						  fixedcenter:false, 
						  close:false, 
						  draggable:true, 
						  modal:false,
						  visible:false,
							buttons: [{ text:"Submit", handler: handleSubmit, isDefault:true },
								  { text:"Cancel", handler:handleCancel } ]
						  } ); 
				event_panel.callback.success = handleSuccess;
				event_panel.render();
				event_panel.show();						  
				
				}
				YAHOO.util.Event.addListener(window, "load", init);
		</script>

	</head>
	<body class="yui-skin-sam">
		<div class="x">&nbsp;
		<div id="wait" style="visibility:hidden">
			<div id="hd" style="text-align:center">Please Wait</div>
			<div id="bd"></div>
			<div id="ft"></div>
		</div>
		<?php $tab_index = 0; ?>
		<div id="event_panel" style="display:block;width:700px;">
			<div class="hd" id="event_header" style="text-align:center">Get Data Tester</div>
			<div class="bd">
				 <form name="dlgForm" id="dlgForm" method=post action="<? echo $_SERVER['PHP_SELF'] ?>" >
				 <input type="hidden" name="evid" id="evid">
				 <input type="hidden" name="add_up_del_flag" id="add_up_del_flag">
				 <input type="hidden" name="X" value="X">
				 <div class="row">
				 <span class="label2">Square Dance Program:</span>
				 <span class="formw">
					<input tabindex="<? echo $tab_index++ ?>" type="checkbox" value="ms" id="event_sd_program_ms" name="event_sd_program[]">Mainstream&nbsp;
					<input tabindex="<? echo $tab_index++ ?>" type="checkbox" value="plus" id="event_sd_program_plus" name="event_sd_program[]">Plus&nbsp;
					<input tabindex="<? echo $tab_index++ ?>" type="checkbox" value="a1" id="event_sd_program_a1" name="event_sd_program[]">Advanced 1&nbsp;
					<input tabindex="<? echo $tab_index++ ?>" type="checkbox" value="a2" id="event_sd_program_a2" name="event_sd_program[]">Advanced 2&nbsp;
					<input tabindex="<? echo $tab_index++ ?>" type="checkbox" value="c1" id="event_sd_program_c1" name="event_sd_program[]">C1
				 </span>
				 </div>
				 <div class="row">
				 <span class="label2">Round Dance Program:</span>
				 <span class="formw">
					<input tabindex="<? echo $tab_index++ ?>" type="checkbox" value="I" id="event_rd_program_I" name="event_rd_program[]">Phase I&nbsp;
					<input tabindex="<? echo $tab_index++ ?>" type="checkbox" value="II" id="event_rd_program_II" name="event_rd_program[]">Phase II&nbsp;
					<input tabindex="<? echo $tab_index++ ?>" type="checkbox" value="III" id="event_rd_program_III" name="event_rd_program[]">Phase III&nbsp;
					<input tabindex="<? echo $tab_index++ ?>" type="checkbox" value="IV" id="event_rd_program_IV" name="event_rd_program[]">Phase IV&nbsp;
					<input tabindex="<? echo $tab_index++ ?>" type="checkbox" value="V" id="event_rd_program_V" name="event_rd_program[]">Phase V&nbsp;
					<input tabindex="<? echo $tab_index++ ?>" type="checkbox" value="VI" id="event_rd_program_VI" name="event_rd_program[]">Phase VI
				 </span>
				 </div>
					 <div class="row">
						 <span class="label2">Caller(s):</span>
						 <span class="formw"><input tabindex="<? echo $tab_index++ ?>" id="event_caller" name="event_caller" class="txtinp" type=text></span>
					 </div>
					 <div class="row">
						 <span class="label2">Cuer(s):</span>
						 <span class="formw"><input tabindex="<? echo $tab_index++ ?>" id="event_cuer" name="event_cuer" class="txtinp" type=text></span>
					 </div>
					 <div class="row">
						 <span class="label2">Event Contacts:</span>
						 <span class=thirda  style="width:20%;"><span class=boldcu>Name:</span><br>
							 <? 
							  for ($i=0;$i<3;$i++) {
								$ti = ($i * 3) + $tab_index;
							 	echo '<input type="text" tabindex="' . $ti . '" class="txtinp" name="event_contact_name[]" id="event_contact_name_' . $i . '">' . "\n";
							  }
							 ?>
						 </span>
						 <span class=thirda style="width:20%"><span class=boldcu>Email:</span><br>
							 <? for ($i=0;$i<3;$i++) {
								$ti = ($i * 3) + 1 + $tab_index;
							 	echo '<input type="text" tabindex="' . $ti . '"  class="txtinp" name="event_contact_email[]" id="event_contact_email_' . $i . '">' . "\n";
							    }
							 ?>
						 </span>
						 <span class=thirda style="width:20%"><span class=boldcu>Phone:</span><br>
							 <? for ($i=0;$i<3;$i++) {
								$ti = ($i * 3) + 2 + $tab_index;
							 	echo '<input type="text" tabindex="' . $ti . '"  class="txtinp" name="event_contact_phone[]" id="event_contact_phone_' . $i . '">' . "\n";
							    }
							 ?>
						 </span>
					 </div>
					 <div class="row">
					 	<span class="label2">Testing 123:</span>
						<span class="formw"><input type="text" name="testing123" id="testing123" class="txtinp"></span>
					 </div>
				 </form> 
			</div> 
			<div class="ft">
			</div>
		</div>
		<div id="myLogger" style="width:500px;font-size:110%"></div>
	</body>
</html>
