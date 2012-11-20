<?php
session_start();
if (!extension_loaded('json')) {
   $prefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
   dl($prefix . 'json.' . PHP_SHLIB_SUFFIX);
}
include ('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname);
$subject = 'NNJSDA Test Update Calendar';
$ver = "(0.06(2.2.2))";
$pageaddr = 'homepage';
include ('emailtracker.php.inc');
if (isset($_POST['trace'])) exit('ok');
if (isset($_POST['name'])) {
	$body = 'Name: ' . stripslashes($_POST['name']) . "\n";
	$body .= 'Email: ' .stripslashes($_POST['email']) . "\n";
	$body .= stripslashes($_POST['comment']);
	$to = 'kenrbnsn@rbnsn.com';
	$from ='From: Admin Calendar Comments <admincomments@nnjsda.org>';
	@mail($to,'New Admin Calendar Comments',$body,$from,'-f admincomments@nnjsda.org');
	exit(ok);
}
if (isset($_POST['add_up_del_flag']))
	switch ($_POST['add_up_del_flag']) {
		case 'A':
			$qtmp = array();
			foreach ($_POST as $fld => $val) {
				switch ($fld) {
					case 'event_date':
						$qtmp[] = $fld . " = '" . date('Y-m-d',strtotime($val)) . "'";
						break;
					case 'event_start_time':
					case 'event_stop_time':
						$qtmp[] = $fld . " = '" . date('G:i:00',strtotime($val)) . "'";
						break;
					case 'evid':
					case 'add_up_del_flag':
						break;
					case 'event_contact_name':
					case 'event_contact_email':
					case 'event_contact_phone':
						$qtmp[] = $fld . " = '" . mysql_real_escape_string(implode(',',$val)) . "'";
						break;
					case 'event_sd_program':
					case 'event_rd_program':
						$qtmp[] = $fld . " = '" . implode(' ',$val) . "'";
						break;
					default:
						$qtmp[] = $fld . " = '" . mysql_real_escape_string(trim(stripslashes($val))) . "'";
						break;
				}
			}
			$q = 'Insert into new_events set ' . implode(',',$qtmp);
			$rs = mysql_query($q);
			if ($rs) {
				$tmp = array();
				$temp = array();
				$tmp[] = htmlentities($_POST['event_org'],ENT_QUOTES);
				if ($_POST['event_name'] != '') $tmp[] = htmlentities($_POST['event_name'],ENT_QUOTES);
				$tmp[] = '--------------------'; 
				$dt_key = date('n/j/Y',strtotime($_POST['event_date']));
				$temp[$dt_key] .= implode('<br>',$tmp) . '~' . mysql_insert_id(). '~' . date('n/Y',strtotime($_POST['event_date']));
				exit('OK,'.json_encode($temp));
			}
			else
				exit('Not OK, query:' . $query . '` mysql_error:' . mysql_error());
			break;
		case 'U':
		case 'D':
			exit(print_r($_POST,true));
			break;
		case 'S':
			$_SESSION['saveEvent'] = $_POST;
			exit('Ok');
			break;
		case 'R':
			$formdata = array();
			foreach($_SESSION['saveEvent'] as $fld => $val) {
				list($d1,$d2) = explode('_',$fld);
				if ($d1 == 'event')
					$formdata[$fld] = $val;
			}
			echo json_encode($formdata);
			exit();
			break;
		case 'C':
			if(isset($_SESSION['saveEvent'])) unset ($_SESSION['saveEvent']);
			exit();
			break;
	}
$q = "select * from eventtypes";
$rs = mysql_query($q);
$et = array();
while ($rw = mysql_fetch_assoc($rs))
	$et[] = $rw['event_type'];
if (isset($_POST['evid'])) {
	$form_data = array();
	$q = "select * from new_events where ind = " . $_POST['evid'];
	$rs = mysql_query($q) or die("Error, problem with $q " . mysql_error());
	$rw = mysql_fetch_assoc($rs);
	$tmp = array();
	$tmp[] = date('l, F jS, Y',strtotime($rw['event_date']));
	$tmp[] = $rw['event_org'];
	$tmp[] = $rw['event_name'];
	$header = implode('<br>',$tmp);
	$event_info = $rw;
	$q = "Select * from clubs where club='" . urlencode($rw['event_org']) . "'";
	$rs = mysql_query($q) or die(mysql_error());
	$org_info = mysql_fetch_assoc($rs);
	$form_data['evid'] = $_POST['evid'];
	$form_data['org'] = $rw['event_org'];
	$form_data['name'] = $rw['event_name'];
	$form_data['url'] = eitheror('url',$event_info,$org_info);
	$form_data['location'] = eitheror('location',$event_info,$org_info);
	$form_data['address'] = eitheror('address',$event_info,$org_info);
	$form_data['city'] = eitheror('city',$event_info,$org_info);
	$form_data['state'] = eitheror('state',$event_info,$org_info);
	$form_data['zip'] = eitheror('zip',$event_info,$org_info);
	$form_data['type'] = $event_info['event_type'];
	$form_data['description'] = stripslashes($event_info['event_description']);
	$form_data['date'] = date('n/j/Y',strtotime($rw['event_date']));
	$form_data['start_time'] = date("g:i a",strtotime($event_info['event_start_time']));
	$form_data['stop_time'] = date("g:i a",strtotime($event_info['event_stop_time']));
	$form_data['sd_program'] = eitheror('sd_program',$event_info,$org_info);
	$form_data['rd_program'] = eitheror('rd_program',$event_info,$org_info);
	$form_data['caller'] = eitheror('caller',$event_info,$org_info);
	$form_data['cuer'] = eitheror('cuer',$event_info,$org_info);
	$cn=($org_info['contact_name'] == "")?array("","",""):unserialize($org_info['contact_name']);
	$ce=($org_info['contact_email'] == "")?array("","",""):unserialize($org_info['contact_email']);
	$cp=($org_info['contact_phone'] == "")?array("","",""):unserialize($org_info['contact_phone']);
	$ecn=($event_info['event_contact_name'] == "")?array("","",""):explode(',',$event_info['event_contact_name']);
	$ece=($event_info['event_contact_email'] == "")?array("","",""):explode(',',$event_info['event_contact_email']);
	$ecp=($event_info['event_contact_phone'] == "")?array("","",""):explode(',',$event_info['event_contact_phone']);
	for ($i=0;$i<3;$i++) {
		$form_data['contact_name'][$i] = eitheror1($ecn[$i],$cn[$i]);
		$form_data['contact_email'][$i] = eitheror1($ece[$i],$ce[$i]);
		$form_data['contact_phone'][$i] = eitheror1($ecp[$i],$cp[$i]);
	}
	echo $header . '~' . $url . '~' . json_encode($form_data);
	exit();
}
$q = "select * from new_events";
$rs = mysql_query($q) or die("Problem with $q<br>" . mysql_error());
$temp = array();
while ($rw = mysql_fetch_assoc($rs)) {
	$tmp = array();
	$tmp[] = htmlentities($rw['event_org'],ENT_QUOTES);
	if ($rw['event_name'] != '') $tmp[] = htmlentities($rw['event_name'],ENT_QUOTES);
	$tmp[] = '--------------------'; 
	$dt_key = date('n/j/Y',strtotime($rw['event_date']));
	if (array_key_exists($dt_key,$temp)) {
		$tmpx = explode('`',$temp[$dt_key]);
		$tmpx[] = implode('<br>',$tmp) . '~' . $rw['ind'];
		$temp[$dt_key] = implode('`',$tmpx); }
	else
		$temp[$dt_key] .= implode('<br>',$tmp) . '~' . $rw['ind'];
	}

function format_display($w,$event_info,$org_info) {
	$tmp_body = array();
	$tmp_body[] = '<div class="row">';
	$tmp_body[] = '<span class="label2">' . ucwords($w) . ':</span>';
	$tmp_body[] = '<span class="formw"><input type="text" class="txtinp" name="event_' . $w . '" value="' .  eitheror($w,$event_info,$org_info) . '"></span>';
	$tmp_body[] = '</div>';
	return(implode('',$tmp_body));
}

function eitheror($p, $evt_info, $org_info)
{
	if (!isSet($org_info[$p])) $org_info[$p] = "";
	return(($evt_info['event_'.$p] != "") ? $evt_info['event_'.$p] : urldecode($org_info[$p]));
}

function eitheror1($e, $o)
{
	if (($e == "") && ($o == "")) return($e);
	if (($e == "") && ($o != "")) return($o);
	return(($e != $o)?$e:$o);
}

function builddisp($n,$e,$end=false)
{
	if ($e == "")
		if ($end) return ("");
		else return($n);
	if ($end) return ("</a>");
	else return ('<a href="mailto:'.urlencode($e).'">'.$n."</a>");
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
	<head>
	<title>Experimental NNJSDA Calendar of Events</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.2.2/build/container/assets/container.css">				
		<link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.2.2/build/calendar/assets/calendar.css">
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.2.2/build/button/assets/button.css">
		<link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.2.2/build/logger/assets/logger.css">
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

		
		<script type="text/javascript" src="http://yui.yahooapis.com/2.2.2/build/yahoo-dom-event/yahoo-dom-event.js"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.2.2/build/dragdrop/dragdrop-min.js"></script> 
		<script type="text/javascript" src="http://yui.yahooapis.com/2.2.2/build/element/element-beta-min.js"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.2.2/build/connection/connection-min.js"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.2.2/build/container/container-min.js"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.2.2/build/calendar/calendar-min.js"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.2.2/build/button/button-beta-min.js"></script>			
		<script type="text/javascript" src="http://yui.yahooapis.com/2.2.2/build/logger/logger-min.js"></script>
				
		<script>
				var dts = new Array();
				var All_Event_Ids = new Array();
				var Event_Ids = new Array();
				var evid = 0;
				var link_clicked = new Boolean;
				var dialog1, event_panel, cal1;
				var eventSaved = new Boolean;
				var savedEvent = new Array();
				eventSaved = false;
				link_clicked = false;
				<?php
//				$fp = fopen('trace.txt','a');
//				fwrite($fp,print_r($temp,true) . "\r\n");
//				fclose($fp);
				foreach($temp as $dt => $evtx) {
					$etmp = explode('`',$evtx);
					echo "dts['" . $dt . "'] = '" . $evtx . "';\n";
					foreach ($etmp as $evt) {
						$evid = explode('~',$evt);
						echo "All_Event_Ids[evid++] = 'id_" . $evid[1] . ':' . date('n/Y',strtotime($dt)) . "';\n";
					}
				}
				?>
				function array_keys(ary) {
					var temp = new Array();
					var indx = 0;
					for (var i in ary) {
					    temp[indx++] = i;
					}
					return (temp);
				}

				function init() {
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
//				YAHOO.log(dump(form_data),'trac','cbGetEvent, form_data');
				event_panel.setHeader(tmp[0]);
				for (var key in form_data) {
				    switch (key) {
					case 'evid':
						YAHOO.log(key,'trac','cbGetEvent, key');
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
						YAHOO.log(key,'trac','cbGetEvent, key');
						document.getElementById('event_' + key).value = form_data[key];
						break;
					case 'rd_program':
					case 'sd_program':
						YAHOO.log(key,'trac','cbGetEvent, key');
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
						YAHOO.log(key,'trac','cbGetEvent, key');
						fd = fd.replace(/&/,'and');
						YAHOO.log(fd,'trac','cbGetEvent, form_data[' + key + ']');
						YAHOO.log('event_' + key + '_' + fd,'trac','cbGetEvent');
						document.getElementById('event_' + key + '_' + fd).selected = true;
						break;
					case 'contact_name':
					case 'contact_email':
					case 'contact_phone':
						YAHOO.log(key,'trac','cbGetEvent, key');
						YAHOO.log(form_data[key],'trac','cbGetEvent, form_data[key]');
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
				YAHOO.log('After setup of myButtons','trac','cbGetEvent');
				event_panel.cfg.queueProperty("buttons", myButtons);
				YAHOO.log('After queueProperty','trac','cbGetEvent');
		  		waitPanel.hide();
				YAHOO.log('After waitPanel.hide','trac','cbGetEvent');
				event_panel.render();
				YAHOO.log('After event_panel.render','trac','cbGetEvent');
				event_panel.show();
				YAHOO.log('End of cbGetEvent','trac','cbGetEvent');
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
		this.submit();
	};
	var handleCancel = function() {
		this.cancel();
	};
	var handleSuccess = function(o) {
		var response = o.responseText;
		YAHOO.log(response,'trac','handleSuccess');
		waitPanel.hide();
	};
	
	var AddSuccess = function(o) {
		var response = o.responseText;
		var dt = 0;
		YAHOO.log(response,'trac','AddSuccess');
		tmp1 = response.split(',');
		if (tmp1[0] == 'OK') {
			tmp = eval('(' + tmp1[1] + ')' );
			for (var dt in tmp) {
				tmp2 = tmp[dt].split('~');
				All_Event_Ids[All_Event_Ids.length] = 'id_' + tmp2[1] + ':' + tmp2[2];
				if (dts[dt] == undefined)
					dts[dt] = tmp2[0];
				else
					dts[dt] += '`' + tmp2[0];
			cal1.addRenderer(array_keys(dts).join(),renderAppt);
			calMonth.addRenderer(array_keys(dts).join(),addText);
			cal1.render();
			calMonth.render();
			calMonth.show();
	       		}
		}
		waitPanel.hide();
	}

	var AddUpDelSuccess = function(o) {
		var response = o.responseText;
		YAHOO.log(response,'trac','AddUpDelSuccess');
		waitPanel.hide();
	};
	
	var SaveEventSuccess = function(o) {
		var response = o.responseText;
		YAHOO.log(response,'trac','SaveEventSuccess');
	}	
	
	var RestoreEventSuccess = function(o) {
		var response = o.responseText;
		YAHOO.log(response,'trac','RestoreEventSuccess');
		var form_data = eval( '(' + response + ')' );
		for (var key in form_data) {
		   switch (key) {
			case 'event_description':
			case 'event_url':
			case 'event_start_time':
			case 'event_stop_time':
			case 'event_location':
			case 'event_address':
			case 'event_city':
			case 'event_state':
			case 'event_zip':
			case 'event_caller':
			case 'event_cuer':
			case 'event_name':
				document.getElementById(key).value = form_data[key];
				break;
			case 'event_rd_program':
			case 'event_sd_program':
				var tmpp = form_data[key];
				if (tmpp.length > 0) {
					for (var p=0;p<tmpp.length;p++) 
						if (tmpp[p] != '')
							document.getElementById(key + '_' + tmpp[p]).checked = true;
				}
				break;
			case 'event_org':
			case 'event_type':
				fd = form_data[key].replace(/ /g,'_');
				fd = fd.replace(/&/,'and');
				YAHOO.log(fd,'trac','cbGetEvent, form_data[' + key + ']');
				YAHOO.log('event_' + key + '_' + fd,'trac','cbGetEvent');
				document.getElementById(key + '_' + fd).selected = true;
				break;
			case 'event_contact_name':
			case 'event_contact_email':
			case 'event_contact_phone':
				for (var i=0;i<3;i++)
					document.getElementById(key + '_' + i).value = form_data[key][i];
				break;
		    }
		}
//		event_panel.render();
//		event_panel.show();
	}	
	
	var ClearSavedSuccess = function(o) {
		var response = o.responseText;
		YAHOO.log(response,'trac','ClearSavedSuccess');
	}	
	
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
			YAHOO.log('Current form data (add_up_del_flag): ' + event_panel.getData().add_up_del_flag,'trace','updateEvent');
			YAHOO.log('Current form data (evid): ' + event_panel.getData().evid,'trace','updateEvent');
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
			YAHOO.log('Current form data (up_del_flag): ' + event_panel.getData().add_up_del_flag,'trace','deleteEvent');
			YAHOO.log('Current form data (evid): ' + event_panel.getData().evid,'trace','deleteEvent');
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
			eventSaved = true;
			document.getElementById('add_up_del_flag').value='S';
			this.callback.success = SaveEventSuccess;
			this.callback.failure = handleFailure;
			this.submit();
			this.show();
    }

function restoreEvent(e) {
         pass_str = 'trace=1&loc=restoreEvent';
         gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			if (eventSaved) {
				document.getElementById('add_up_del_flag').value='R';
				this.callback.success = RestoreEventSuccess;
				this.callback.failure = handleFailure;
				this.submit();
				this.show();
			}
    }

function clearEvent(e) {
         pass_str = 'trace=1&loc=clearEvent';
         gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			YAHOO.log('','trace','clearEvent');
			if (eventSaved) {
				document.getElementById('add_up_del_flag').value='C';
				this.callback.success = ClearSavedSuccess;
				this.callback.failure = handleFailure;
				this.submit();
				eventSaved = false;
				this.show();
			}
    }

function addEvent(e) {
         pass_str = 'trace=1&loc=addEvent';
         gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			document.getElementById('add_up_del_flag').value='A';
			YAHOO.log('up_del_flag: ' + document.getElementById('add_up_del_flag').value,'trac','addEvent');
			YAHOO.log('Current form data (up_del_flag): ' + event_panel.getData().add_up_del_flag,'trace','addEvent');
			YAHOO.log('Current form data (evid): ' + event_panel.getData().evid,'trace','addEvent');
			waitPanel.setBody('Adding Event');
			waitPanel.render();
			waitPanel.show();
			this.callback.success = AddSuccess;
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

				
					cal1 = new YAHOO.widget.CalendarGroup("cal1","cal1Container", { pages:12, title:"NNJSDA Calendar", close:false} );
					cal1.addRenderer(array_keys(dts).join(),renderAppt);
					cal1.selectEvent.subscribe(mySelectHandler, cal1, true);
					cal1.render();
					panelYear = new YAHOO.widget.Panel("container0", {draggable:false, close:false, visible: true});
					var yearButton1 = new YAHOO.widget.Button({
					                                        id: "yearbuttonid1", 
					                                        type: "link",
																		 ref:"http://www.nnjsda.org/",
					                                        label: "Return to the NNJSDA Home Page", 
					                                        container: "yearbutton1" 
					                                    });
					var commentButton = new YAHOO.widget.Button({
										id: "commentbutton",
										type: "button",
										label: "Send Comments",
										container: "commentbutton"
									});
					commentButton.addListener("click", displayComment);
					panelYear.render();
					
				   calMonth = new YAHOO.widget.Calendar("calMonth","calMonthContainer", {visible: true, close: false});
					calMonth.addRenderer(array_keys(dts).join(),addText);
					calMonth.renderEvent.subscribe(myRenderHandler, calMonth, true);
					calMonth.selectEvent.subscribe(monthSelectHandler, calMonth, true);
					panel = new YAHOO.widget.Panel("container", {draggable:false, close:false, visible: false, width: "800px"});
					var oButton1 = new YAHOO.widget.Button({
	    				                                        id: "mybuttonid1", 
					                                        type: "button", 
					                                        label: "Return to Year View", 
					                                        container: "button1" 
					                                    });
					var oButton2 = new YAHOO.widget.Button({
					                                        id: "mybuttonid2", 
					                                        type: "link",
										href:"http://www.nnjsda.org/",
					                                        label: "Return to the NNJSDA Home Page", 
					                                        container: "button2" 
					                                    });
					var oButton3 = new YAHOO.widget.Button({
					                                        id: "mybuttonid3", 
					                                        type: "button", 
					                                        label: "Return to Year View", 
					                                        container: "button3" 
					                                    });
					var oButton4 = new YAHOO.widget.Button({
					                                        id: "mybuttonid4", 
					                                        type: "link",
										href:"http://www.nnjsda.org/",
					                                        label: "Return to the NNJSDA Home Page", 
					                                        container: "button4" 
					                                    });
					oButton1.addListener("click", returnToYear);
					oButton3.addListener("click", returnToYear);
					panel.render();
				dialog1 = new YAHOO.widget.Dialog("dialog1", {
						width:"500px",
						fixedcenter: true,
						close: true,
						visible: false,
						constraintoviewport : true,
						buttons: [ { text:"Submit", handler:handleSubmit, isDefault:true },
								{ text:"Cancel", handler:handleCancel } ],
						callback:[{ success: handleSuccess,failure: handleFailure }]
						});
				dialog1.render();
				waitPanel = new YAHOO.widget.Panel("wait",  
						{ width:"240px", 
						  fixedcenter:true, 
						  close:false, 
						  draggable:false, 
						  modal:true,
						  visible:false 
						} 
					);
				event_panel = new YAHOO.widget.Dialog("event_panel_markup", { width:"700x",
						  fixedcenter:true, 
						  close:true, 
						  draggable:false, 
						  modal:true,
						  visible:false,
							buttons: [ { text:"Update", handler:updateEvent, isDefault:true },
								  { text:"Delete", handler:deleteEvent },
										{ text:"Save Event", handler:saveEvent },
										{ text:"Restore Event", handler: restoreEvent },
										{ text:"Clear Saved Event", handler: clearEvent },								  
								  { text:"Cancel", handler:handleCancel } ],
							callback:[{ success: AddUpDelSuccess,failure: handleFailure }]
						  
						  } ); 
				
				
				}
				YAHOO.util.Event.addListener(window, "load", init);
		</script>

	</head>
	<body>
		<div class="x">&nbsp;
			<div id="container0">
				<div class="hd"></div>
				<div class="bd">
				<div id="cal1Container"></div>
					<br style="clear:left" />
				</div>
				<div class="ft">
				<div id="commentbutton" style="font-size:75%;float:right"></div>
				<div id="yearbutton1" style="font-size:75%;float:right"></div>
				<div style="clear:both;line-height:0.01em">&nbsp;</div>
				</div>
			</div>
			<div id="container">
				<div class="hd">
				<div id="button1" style="font-size:75%;float:right"></div>
				<div id="button2" style="font-size:75%;float:right"></div>
				<div style="clear:both;line-height:0.01em">&nbsp;</div>
				</div>
				<div class="bd">
					<div id="calMonthContainer" style="display:block"></div>
					<br style="clear:left" />
	
				</div>
				<div class="ft">
				<div id="button3" style="font-size:75%;float:right"></div>
				<div id="button4" style="font-size:75%;float:right"></div>
				<div style="clear:both;line-height:0.01em">&nbsp;</div>
				</div>
			</div>
		</div>
		<div id="dialog1">
			<div class="hd">Please enter your comment</div>
			<div class="bd">
				<form method="POST" action="<?php echo $_SERVER['PHP_SELF']?>">
					<label for="comment_name">Name:</label><input type="text" name="name" id="comment_name" /><br>
					<label for="comment_email">E-mail:</label><input type="text" name="email" id="comment_email" /><br> 
					<label for="comment_comment">Comment:</label><textarea style="width: 80%" name="comment" id="comment_comment" rows="10"></textarea>
					<div class="clear"></div>
				</form>
			<div class="ft"></div>
			</div>
		</div>
		<div id="wait" style="visibility:hidden">
			<div id="hd" style="text-align:center">Please Wait</div>
			<div id="bd"></div>
			<div id="ft"></div>
		</div>
		<?php $tab_index = 0; ?>
		<div id="event_panel_markup" style="display:block;width:700px;">
			<div class="hd" id="event_header" style="text-align:center"></div>
			<div class="bd">
				 <form name="dlgForm" id="dlgForm" method=post action="<? echo $_SERVER['PHP_SELF'] ?>" style="font-size:80%;">
				 <input type="hidden" name="evid" id="evid">
				 <input type="hidden" name="add_up_del_flag" id="add_up_del_flag">
				 <div class="row">
				 <span class="label2">Club or Organization Name:</span>
				 <span class="formw"><select tabindex="<? echo $tab_index++ ?>" class="txtinp" id="event_org" name="event_org" size="1">
				 <option value="" selected></option>
				 <?php
					$q = "select club from clubs order by club";
					$rs = mysql_query($q);
					while ($rw = mysql_fetch_assoc($rs))
						echo '<option value="' . urldecode($rw['club']) . '" id="event_org_' . str_replace(array(' ','&'),array('_','and'),urldecode($rw['club'])) . '">' . urldecode($rw['club']) . "</option>\n";
				?>
				 <option value="NNJSDA" id="event_org_NNJSDA">NNJSDA</option>
				 <option value="5 Clubs" id="event_org_5_Clubs">5 Clubs</option>
				 <option value="CCNJ">CCNJ</option>
				 </select>
				 </span>
				 </div>
				 <div class="row">
				 <span class="label2">Event Type:</span>
				 <span class="formw"><select tabindex="<? echo $tab_index++ ?>" class="txtinp" id="event_type" name="event_type" size=1>
				 	<option value=""></option>
				 <?php
					$q = "select * from eventtypes order by event_type";
					$rs = mysql_query($q);
					while ($rw = mysql_fetch_assoc($rs))
						echo '<option value="' . $rw['event_type'] . '" id="event_type_' . str_replace(' ','_',$rw['event_type']) . '">' . $rw['event_type'] . "</option>\n";
				?>
				 </select>
				 </span>
				 </div>
				 <div class="row">
				 <span class="label2">Event Name:</span>
				 <span class="formw"><input tabindex="<? echo $tab_index++ ?>" name="event_name" id="event_name" type="text" class="txtinp"></span>
				 </div>
				 <div class="row">
				 <span class="label2">Event Description:</span>
				 <span class="formw"><textarea tabindex="<? echo $tab_index++ ?>" class="txtinp" rows="5" id="event_description" name="event_description"></textarea></span>
				 </div>
				 <div class="row">
				 <span class="label2">Event URL:</span>
				 <span class="formw"><input tabindex="<? echo $tab_index++ ?>" name="event_url" id="event_url" type="text" class="txtinp"></span>
				 </div>
				 <div class="row">
				 <span class="label2">Event Date:</span>
				 <span class="formw"><input tabindex="<? echo $tab_index++ ?>" name="event_date" id="event_date" type="text" class="txtinp"></span>
				 </div>
				 <div class="row">
				 <span class="label2">Start Time:</span>
				 <span class="formw"><input tabindex="<? echo $tab_index++ ?>" name="event_start_time" id="event_start_time" class="txtinp" value="8:00 pm"></span>
				 </div>
				 <div class="row">
				 <span class="label2">End Time:</span>
				 <span class="formw"><input tabindex="<? echo $tab_index++ ?>" name="event_stop_time" id="event_stop_time" class="txtinp" value="10:30 pm"></span>
				 </div>
				 <div class="row">
				 <span class="label2">Location:</span>
				 <span class="formw"><input tabindex="<? echo $tab_index++ ?>" name="event_location" id="event_location"class="txtinp" type="Text"></span>
				 </div>
				 <div class="row">
				 <span class="label2">Address:</span>
				 <span class="formw"><input tabindex="<? echo $tab_index++ ?>" id="event_address" name="event_address" class="txtinp" type="Text"></span>
				 </div>
				 <div class="row">
				 <span class="label2">City:</span>
				 <span class="formw"><input tabindex="<? echo $tab_index++ ?>" id="event_city" name="event_city" class="txtinp" type="Text"></span>
				 </div>
				 <div class="row">
				 <span class="label2">State:</span>
				 <span class="formw"><input tabindex="<? echo $tab_index++ ?>" id="event_state" name="event_state" class="txtinp" type="Text"></span>
				 </div>
				 <div class="row">
				 <span class="label2">Zip:</span>
				 <span class="formw"><input tabindex="<? echo $tab_index++ ?>" id="event_zip" name="event_zip" class="txtinp" type="Text"></span>
				 </div>
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
				 </form> 
			</div> 
			<div class="ft">
			</div>
		</div>
		<div id="myLogger" style="width:500px;font-size:110%"></div>
	</body>
</html>
