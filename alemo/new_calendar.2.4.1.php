<?php
session_start();
if (!extension_loaded('json')) {
   $prefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
   dl($prefix . 'json.' . PHP_SHLIB_SUFFIX);
}
$api_key = ($_SERVER['HTTP_HOST'] == 'localhost') ? 'ABQIAAAAIt1KZzXv_lkvn9ag5wjKsxT2yXp_ZAY8_ufC3CFXhHIE1NvwkxRkPoXaNMR2mHO46fAeKRTg3dLQSw': 'ABQIAAAAIt1KZzXv_lkvn9ag5wjKsxSj3dkfyIa3jHxfjiA5GrB0GwZIsRReAwd0UoE8ChdB91cfcowo6nmzEA';

include ('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname);
$subject = $_SESSION['org_name'] .' Calendar';
$ver = "(2.13)";
$pageaddr = 'homepage';
include ('emailtracker.inc.php');
if (isset($_POST['trace'])) exit('ok');
if (isset($_POST['name'])) {
	$body = 'Name: ' . stripslashes($_POST['name']) . "\n";
	$body .= 'Email: ' .stripslashes($_POST['email']) . "\n";
	$body .= stripslashes($_POST['comment']);
	$to = 'kenrbnsn@rbnsn.com';
	$from ='From: Calendar Comments <comments@nnjsda.org>';
	@mail($to,'New Calendar Comments',$body,$from,'-f comments@nnjsda.org');
	exit(ok);
}
if (isset($_POST['evid'])) {
	$q = "select * from new_events where ind = " . $_POST['evid'];
	$rs = mysql_query($q) or die("Error, problem with $q " . mysql_error());
	$rw = mysql_fetch_assoc($rs);
	$tmp = array();
	$map_tmp = array();
	$tmp[] = date('l, F jS, Y',strtotime($rw['event_date']));
	$tmp[] = $rw['event_org'];
	if ($rw['event_name'] != '')
		$tmp[] = $rw['event_name'];
	$event_info = $rw;
	$q = "Select * from new_clubs where club='" . $rw['event_org'] . "'";
	$rs = mysql_query($q) or die(mysql_error());
	$org_info = mysql_fetch_assoc($rs);
	$url = eitheror('url',$event_info,$org_info);
	$header = implode('<br>',$tmp);
	$tmp_body = array();
	if ($url == '')
		$url = '*';
	else {
		$tmp_body[] = '<div class="row">';
		$tmp_body[] = '<span style="text-align:center;width:100%;display:block;"><a href="http://' . $url . '">' . $url . '</a></span>';
		$tmp_body[] = '</div>';
	}
	$tmp_body[] = format_display('location',$event_info,$org_info);
	$map_tmp[] = eitheror('location',$event_info,$org_info);
	$tmp_body[] = format_display('address',$event_info,$org_info);
	$map_tmp[] = eitheror('address',$event_info,$org_info);
	$tmp_body[] = format_display('city',$event_info,$org_info);
	$tmp_body[] = format_display('state',$event_info,$org_info);
	$tmp_body[] = format_display('zip',$event_info,$org_info);
	$map_tmp[] = eitheror('city',$event_info,$org_info);
	$map_tmp[] = eitheror('state',$event_info,$org_info) . ' ' . eitheror('zip',$event_info,$org_info);
	$map_tmp[] = convert_latlong($event_info['event_lat']);
	$map_tmp[] = convert_latlong($event_info['event_long']);
	$tmp_body[] = '<div class="row">';
	$tmp_body[] = '<span class="label2">Event:</span>';
	$tmp_body[] = '<span class="formw">' . $event_info['event_type'] . '</span>';
	$tmp_body[] = '</div>';
	if ($event_info['event_description'] != "") {
		$tmp_body[] = '<div class="row">';
		$tmp_body[] = '<span class="label2">Description:</span>';
		$tmp_body[] = '<span class="formw">' . nl2br($event_info['event_description']) . '</span>';
		$tmp_body[] = '</div>'; }
	$tmp_body[] = '<div class="row">';
	$tmp_body[] = '<span class="label2">Time:</span>';
	$tmp_body[] = '<span class="formw">' . date("g:i a",strtotime($event_info['event_start_time'])) . ' to ' . date("g:i a",strtotime($event_info['event_stop_time'])) . '</span>';
	$tmp_body[] = '</div>';
	$tmp_body[] = '<div class="row">';
	$tmp_body[] = '<span class="label2">Square Dance Program:</span>';
	$tmp_body[] = '<span class="formw">' . strtoupper(implode(", ",explode(" ",eitheror('sd_program',$event_info,$org_info)))) . '</span>';
	$tmp_body[] = '</div>';
	$tmp_body[] = '<div class="row">';
	$tmp_body[] = '<span class="label2">Round Dance Program:</span>';
	$tmp_body[] = '<span class="formw">' . eitheror('rd_program',$event_info,$org_info) . '</span>';
	$tmp_body[] = '</div>';
	$tmp_body[] = format_display('caller',$event_info,$org_info);
	$tmp_body[] = format_display('cuer',$event_info,$org_info);
	$tmp_body[] = '<div class="row">';
	$tmp_body[] = '<span class="label2">Club Contacts:</span>';
	$tmp_body[] = '<span class=thirda><span class="boldcu">Name:</span>';
	$ts = 25;
	$cn=($org_info['contact_name'] == "")?array("","",""):explode(',',$org_info['contact_name']);
	$ce=($org_info['contact_email'] == "")?array("","",""):explode(',',$org_info['contact_email']);
	$cp=($org_info['contact_phone'] == "")?array("","",""):explode(',',$org_info['contact_phone']);
	$ecn=($event_info['event_contact_name'] == "")?array("","",""):explode(',',$event_info['event_contact_name']);
	$ece=($event_info['event_contact_email'] == "")?array("","",""):explode(',',$event_info['event_contact_email']);
	$ecp=($event_info['event_contact_phone'] == "")?array("","",""):explode(',',$event_info['event_contact_phone']);
 for ($i=0;$i<3;$i++)
	 $tmp_body[] = builddisp(eitheror1($ecn[$i],$cn[$i]),eitheror1($ece[$i],$ce[$i]))  . '<br>';
	$tmp_body[] = '</span>';
	$tmp_body[] = '<span class="thirda"><span class="boldcu">Phone:</span>';
	for ($i=0;$i<3;$i++)
		$tmp_body[] =  '<span class="center">' . eitheror1($ecp[$i],$cp[$i]) . '</span>';
	$tmp_body[] = '</span>';
	$tmp_body[] = '<div class="clear">&nbsp;</div>';
	
	echo $header . '~' . $url . '~' . json_encode($map_tmp) . '~' . implode('',$tmp_body);
	exit();
}
$do_direct = 'false';
$direct_evid = -1;
if (isset($_GET['direct'])) {
	$do_direct = 'true';
	$direct_evid = $_GET['evid']; }
$q = "select * from new_events";
if (isset($_SESSION['org_name']))
	$q .= " where event_org = '" . mysql_real_escape_string($_SESSION['org_name']) . "'";
$rs = mysql_query($q) or die("Problem with $q<br>" . mysql_error());
$temp = array();
while ($rw = mysql_fetch_assoc($rs)) {
	$tmp = array();
//	$tmp[] = htmlentities($rw['event_org'],ENT_QUOTES);
	$tmp[] = ($rw['event_name'] != '')?htmlentities($rw['event_name'],ENT_QUOTES):'Regular Dance';
//	$tmp[] = '--------------------'; 
	$dt_key = date('n/j/Y',strtotime($rw['event_date']));
	if (array_key_exists($dt_key,$temp)) {
		$tmpx = explode('`',$temp[$dt_key]);
		$tmpx[] = implode('<br>',$tmp) . '~' . $rw['ind'];
		$temp[$dt_key] = implode('`',$tmpx); }
	else
		$temp[$dt_key] .= implode('<br>',$tmp) . '~' . $rw['ind'];
	}

function convert_latlong($degrees) {
	$d = explode('°',$degrees);
	$neg = ($d[0] < 0)?true:false;
	$m = explode("'",$d[1]);
	$s = explode('"',$m[1]);
	$dn = abs($d[0]) + (($m[0] + $s[0]/60)/60);
	if ($neg) $dn = $dn * -1;
	return($dn);
}

function format_display($w,$event_info,$org_info) {
	$tmp_body = array();
	$tmp_body[] = '<div class=row>';
	$tmp_body[] = '<span class="label2">' . ucwords($w) . ':</span>';
	$tmp_body[] = '<span class="formw">' .  eitheror($w,$event_info,$org_info) . '</span>';
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
	<head>
	<title><?php echo $_SESSION['org_name'] ?> Calendar of Events</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.4.1/build/container/assets/skins/sam/container.css">
		<link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.4.1/build/fonts/fonts-min.css">
		<link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.4.1/build/calendar/assets/calendar.css">
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.4.1/build/button/assets/skins/sam/button.css">
		<link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.4.1/build/logger/assets/logger.css">
		<style media="screen" type="text/css">
		.mask {
                overflow:visible; /* or overflow:hidden */
            }
				
		.x {
			display: block;
			margin-left: auto;
			margin-right: auto;
				margin-top: 50px;
			width: 800px;
		}
		
		.fr {
			float: right;
		}
		
		button a, button a:visited {
			color: black;
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
				background-color: Blue;
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
		
		.clear {
			line-height: 0.01em;
			clear: both;
		}
		
		/* 
		 * Part of workaround for https://bugzilla.mozilla.org/show_bug.cgi?id=167801 
		 */
		.caretfix {
		   overflow:auto;
		}

		</style>
		<style media="print" type="text/css">
		.x {
			display: none;
		}
		.fr {
			float: right;
		}
		button a, button a:visited {
			color: black;
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
		
		.clear {
			line-height: 0.01em;
			clear: both;
		}
		</style>
		<script src="http://maps.google.com/maps?file=api&v=2&key=<?php echo $api_key ?>" type="text/javascript"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.4.1/build/yahoo-dom-event/yahoo-dom-event.js"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.4.1/build/dragdrop/dragdrop-min.js"></script> 
		<script type="text/javascript" src="http://yui.yahooapis.com/2.4.1/build/element/element-beta-min.js"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.4.1/build/connection/connection-min.js"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.4.1/build/container/container-min.js"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.4.1/build/calendar/calendar-min.js"></script>
		<script type="text/javascript" src="http://yui.yahooapis.com/2.4.1/build/button/button-min.js"></script>			

		
		<script>

				var dts = new Array();
				var All_Event_Ids = new Array();
				var Event_Ids = new Array();
				var dialog1, event_panel;
				var evid = 0;
				var show_map = new Boolean;
				var do_direct_evid;
				var do_direct = new Boolean();
				do_direct = <?php echo $do_direct ?>;
				var map_addr, map_lat, map_long, map;
				if (do_direct)
						do_direct_evid = <?php echo $direct_evid; ?>;
				show_map = true;
				<?php
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
				var cal1;
        var cbnull = function(){}

        var cb =
        {
                success:cbnull,
                failure:cbnull
        }

        var cbGetEvent = function(o){
		  		tmp = o.responseText.split('~');
				pass_str = 'trace=1&loc=cbGetEvent&first_part_of_response=' + tmp[0];
				gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
				ev_hdr = document.getElementById("event_header");
				ev_hdr.innerHTML = tmp[0];
				ev_pnl = document.getElementById("event_content");
				ev_pnl.innerHTML = tmp[3];
		  		waitPanel.hide();
				map_tmp = eval('(' + tmp[2] + ')' );
				map_lat = map_tmp[4];
				map_long = map_tmp[5];
				map_addr = map_tmp[1] + ', ' + map_tmp[2] + ', ' + map_tmp[3];
				map_panel.setHeader(map_tmp[0] + '<br>' + map_addr);
				map = new GMap2(document.getElementById("eventMap"));
				map.clearOverlays();
				var geocoder = new GClientGeocoder();
				map.addControl(new GSmallMapControl());
				map.addControl(new GMapTypeControl());
				showAddress(map_tmp[0], map_addr, map_lat, map_long, map, geocoder);
				event_panel.render();
				panel.hide();
				event_panel.show();
		  }

				function handleGetFromAddr() {
		//			this.hide();
					var from_addr = document.getElementById("info_from").value;
					var map_center = map.getCenter();
					var dmap = new GMap2(document.getElementById("directions"));
					pass_str = 'trace=1&loc=handleGetFromAddr&to=' + map_addr + '&from=' + from_addr + '&lat=' + map_lat + '&long=' + map_long;
					gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
					dmap.setCenter(map_center,12);
					dmap.addControl(new GLargeMapControl());
					dmap.addControl(new GMapTypeControl());
					directionsPanel = document.getElementById("directions_sidebar");
					directionsPanel.innerHTML = '';
					directionsMap = new GDirections(dmap, directionsPanel);
					if (map_lat != '')
						directionsMap.load(from_addr + ' to ' + map_lat + ',' + map_long);
					else
						directionsMap.load(from_addr + ' to ' + map_addr);
					directions_map_panel.setHeader('From: ' + from_addr + '<br>To: ' + map_addr);
					map_panel.show();
					event_panel.show();
					directions_map_panel.show();
					directions_map_panel.focus();
				}


				function showAddress(loc, address, lat, long, map, geocoder) {
				  geocoder.getLatLng(
				    address,
				    function(point) {
				      if (!point) {
					if (lat != '' && long != '') {
						var point = new GLatLng(lat,long);
					         map.setCenter(point, 13);
						var marker = new GMarker(point);						
						var html = "<span style='font-weight:bold'>" + loc + '</span><br> ' + address;
						html += '<br><div style="display:block;width:100%"><form id="info_directions" action="javascript:void(0)">';
						html += '<input type="hidden" name="info_address" id="info_address" value="' + address + '">';
						html += '<input type="hidden" name="info_lat" id="info_lat" value="' + lat + '">';
						html += '<input type="hidden" name="info_long" id="info_long" value="' + long + '">';
						html += 'Directions from: <input style="width:55%" type="text" name="info_from" id="info_from">';
						html += '<button id="info_get_directions">Go</button></form>';
						GEvent.addListener(marker, "click", function() {
						    	marker.openInfoWindowHtml(html);
						});
						map.addOverlay(marker);
						marker.openInfoWindowHtml(html);
						YAHOO.util.Event.addListener("info_get_directions", "click", handleGetFromAddr);
						show_map = true;
					}
					else
				        	show_map = false;
				      } else {
				        map.setCenter(point, 13);
				        var marker = new GMarker(point);
						  var html = "<span style='font-weight:bold'>" + loc + '</span><br> ' + address;
							html += '<br><div style="display:block;width:100%"><form id="info_directions" action="javascript:void(0)">';
							html += '<input type="hidden" name="info_address" id="info_address" value="' + address + '">';
							html += '<input type="hidden" name="info_lat" id="info_lat" value="' + lat + '">';
							html += '<input type="hidden" name="info_long" id="info_long" value="' + long + '">';
							html += 'Directions from: <input style="width:55%" type="text" name="info_from" id="info_from">';
							html += '<button id="info_get_directions">Go</button></form>';
						  GEvent.addListener(marker, "click", function() {
						          marker.openInfoWindowHtml(html);
									 });
			 			  map.addOverlay(marker);
				        marker.openInfoWindowHtml(html);
							YAHOO.util.Event.addListener('info_get_directions', "click", handleGetFromAddr);
						  show_map = true;
				      }
				    }
				  );
				}		  
		  
        var cbev =
        {
                success:cbGetEvent,
                failure:cbnull
        }

		function show_url_value()
		{
			pass_str = 'trace=1&loc=show_url_value&evid='+evid[1];
			gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			show_URL_Dialog.setBody('http://<?php echo $_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]?>?direct=1&evid=' + evid[1]);
			show_URL_Dialog.cfg.setProperty("icon",YAHOO.widget.SimpleDialog.ICON_INFO);
			show_URL_Dialog.render();
			show_URL_Dialog.show();
		}
		function hide_event_panel()
		{
			pass_str = 'trace=1&loc=hide_event_panel';
			gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			this.hide();
			map_panel.hide();
			directions_map_panel.hide();
			calMonth.render();
			panel.show();
		}

		function map_panel_show()
		{
			pass_str = 'trace=1&loc=map_panel_show&show_map=';
			if (show_map) pass_str += 'true';
			else pass_str += 'false';
			gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			if (show_map) {
				map_panel.show();
				map_panel.focus();
			}
			else
				mapWarningDialog.show();
		}
		
		function get_directions_dialog_show() {
			pass_str = 'trace=1&loc=get_directions_dialog_show';
			gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			map_panel.hide();
			event_panel.hide();
			panel.hide();
			get_directions_dialog.show();
			get_directions_dialog.focus();
		}
		
		function get_directions_dialog_hide() {
			pass_str = 'trace=1&loc=get_directions_dialog_hide';
			gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			get_directions_dialog.hide();
			map_panel.show();
			event_panel.show();
		}
		

	function fnCallback(e) { 
		evid = this.id.split('_');
		pass_str = 'trace=1&loc=fnCallback&id=' + evid[1];
		gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
		pass_str = 'evid=' + evid[1];
		gc  = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cbev,pass_str);
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
				if (dts[test_date1] == undefined) return false;
         pass_str = 'date=' + test_date1 + '&trace=1&loc=mySelectHandler';
         gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			this.deselect(test_date1);
			this.render();
			panelYear.hide();
			calMonth.cfg.setProperty("pagedate",test_date);
			calMonth.render();
			panel.show();
		}

		var myRenderHandler = function(type,args,obj) {
		      if (this.pages && this.pages.length > 0) {
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
               pass_str = 'trace=1&loc=myRenderHandler&month_year='+test_date+'&events='+Event_Ids.join(',');
               gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
					YAHOO.util.Event.addListener(Event_Ids, "click", fnCallback);
		};
		
		
		var monthSelectHandler = function(type,args,obj) {
			var arrDates = cal1.getSelectedDates();
			var date = arrDates[0];
			test_date =  date.getFullYear();
			test_date1 = date.getMonth()+1 + '/' + date.getDate() + '/' + date.getFullYear();
                        pass_str = 'date=' + test_date1 + '&trace=1&loc=monthSelectHandler';
                        gc = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cb,pass_str);
			cal1.deselect(test_date1);
			cal1.render();
			this.hide();
			cal1.show();
		}

	var handleSubmit = function() {
		this.submit();
	};
	var handleOK = function() {
		this.hide();
	};
	var handleCancel = function() {
		this.cancel();
	};
				var handleSuccess = function(o) {
					var response = o.responseText;
					alert(response);
				};

				var handleFailure = function(o) {
					alert("Submission failed: " + o.status);
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
	
		function gotolink() {
			window.location="<?php echo $_SESSION['return_to'] ?>";
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
//	   		div.innerHTML += "<div id='id_" + ev_id[1] + "'><span style='font-size:75%;text-align:left;color:red;font-weight:bold;width:100%;display:block'><a href='#'>" + ev_id[0] + "</a></span></div>";
				div.innerHTML += "<button style='display:block;font-size:75%;width:100px;margin-left:auto;margin-right:auto' id='id_" + ev_id[1] + "' value='" + ev_id[0] + "'>" + ev_id[0] + "</button>";
		}
	        cell.appendChild(div);
		YAHOO.util.Dom.addClass(cell, "appt");
		return YAHOO.widget.Calendar.STOP_RENDER;
	}

				
					cal1 = new YAHOO.widget.CalendarGroup("cal1","cal1Container", { pages:12, title:"<?php echo $_SESSION['org_name'] ?> Calendar", close:false} );
					cal1.addRenderer(array_keys(dts).join(),renderAppt);
					cal1.selectEvent.subscribe(mySelectHandler, cal1, true);
					cal1.render();
					if (do_direct) {
						panelYear = new YAHOO.widget.Panel("container0", {draggable:false, close:false, visible: false}); }
					else {
						panelYear = new YAHOO.widget.Panel("container0", {draggable:false, close:false, visible: true}); }
					YAHOO.util.Dom.setStyle("yearbutton1","float","right");
					var commentButton = new YAHOO.widget.Button("commentbutton");
					YAHOO.util.Dom.setStyle("commentbutton","float","right");
					commentButton.addListener("click", displayComment);
					panelYear.render();
					
				   calMonth = new YAHOO.widget.Calendar("calMonth","calMonthContainer", {visible: true, close: false});
					calMonth.addRenderer(array_keys(dts).join(),addText);
					calMonth.renderEvent.subscribe(myRenderHandler, calMonth, true);
					panel = new YAHOO.widget.Panel("container", {draggable:false, close:false, visible: false, width: "800px"});
					YAHOO.util.Dom.setStyle("button2","float","right");
					YAHOO.util.Dom.setStyle("button4","float","right");
					var rty_btns = ['button1','button3'];
					var link_btns = ['yearbutton1','button2','button4'];
					YAHOO.util.Event.addListener(link_btns, "click", gotolink);
					YAHOO.util.Event.addListener(rty_btns, "click", returnToYear, panel, true);
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
				vpw = YAHOO.util.Dom.getViewportWidth();
				x = (vpw / 2) - 350;
				if ( x < 50) x = 50;
				event_panel = new YAHOO.widget.Panel("event_panel_markup", { width:"700px",
															  fixedcenter:false, 
															  x: x,
															  close:true, 
															  draggable:true, 
															  underlay: "none",
															  modal:false,
															  iframe:false,
															  visible:false} ); 
				event_panel.hideEvent.subscribe(hide_event_panel);
				map_panel = new YAHOO.widget.Panel("map_panel_markup", { fixedcenter:false,
															  width:"500px",
															  close:true, 
															  draggable:true, 
															  underlay: "none",
															  modal:false,
															  iframe:false,
															  visible:false} ); 
				map_panel.render();
				x = vpw - 810;
				if (x < 0) x = 0;
				directions_map_panel = new YAHOO.widget.Panel("directions_panel", { fixedcenter:false,
															  width:"800px",
															  x: x,
															  close:true, 
															  draggable:true, 
															  underlay: "none",
															  modal:false,
															  iframe:false,
															  visible:false} ); 
				directions_map_panel.render();
				x += 100;
				get_directions_dialog = new YAHOO.widget.Dialog("from_loc", { width: "50em",
																fixedcenter: false,
															  underlay: "none",
																draggable: true,
																modal: false,
																iframe: false,
																visible: false} );
				get_directions_btn = [ {text: "Ok", handler: handleGetFromAddr, isDefault: true },
											  {text: "Cancel", handler:  get_directions_dialog_hide } ]
				get_directions_dialog.cfg.queueProperty("buttons",get_directions_btn);
				get_directions_dialog.render();

            if (YAHOO.env.ua.gecko) {
                /* 
                 * Other part of workaround for https://bugzilla.mozilla.org/show_bug.cgi?id=167801 !!?
                 * I have no explanation for why the new thread (setTimeout) is needed, but it is.
                 */
                YAHOO.util.Dom.addClass(get_directions_dialog.get_from_loc, "caretfix");

                get_directions_dialog.showEvent.subscribe(function() {
                    YAHOO.util.Dom.setStyle(get_directions_dialog.get_from_loc, "display", "none");

                    var fixDisplay = function() {
                        YAHOO.util.Dom.setStyle(get_directions_dialog.get_from_loc, "display", "block");
                        try {
                            get_directions_dialog.firstFormElement.focus();
                        } catch (e) {
                            // Not related to the workaround, I just try/catch focus calls
                            // do avoid testing for the various conditions in which they could
                            // fail.
                        }
                    }
                    setTimeout(fixDisplay, 0);
                });
            }
				
				mapWarningDialog = new YAHOO.widget.SimpleDialog("dlg", { 
					width: "20em", 
					fixedcenter:true,
					modal:true,
				    visible:false,
					draggable:false });
				mapWarningDialog.cfg.setProperty("icon",YAHOO.widget.SimpleDialog.ICON_WARN);
				var myMWButtons = [ { text:"OK", 
									handler:handleOK,
									isDefault:true } ];
				mapWarningDialog.cfg.queueProperty("buttons", myMWButtons);
				mapWarningDialog.render(document.body);
				
				show_URL_Dialog = new YAHOO.widget.SimpleDialog("URLdlg", { 
					width: "50em", 
					fixedcenter:true,
					modal:true,
				    visible:false,
					draggable:false });
				var myMWButtons = [ { text:"OK", 
									handler:handleOK,
									isDefault:true } ];
				show_URL_Dialog.cfg.queueProperty("buttons", myMWButtons);
				show_URL_Dialog.render(document.body);
				
				container_manager = new YAHOO.widget.OverlayManager();
				container_manager.register([event_panel,map_panel,get_directions_dialog,directions_map_panel]);
				
				YAHOO.util.Event.addListener("show_map", "click", map_panel_show, map_panel, true);
				YAHOO.util.Event.addListener("hide_map", "click", map_panel.hide, map_panel, true);
				YAHOO.util.Event.addListener("get_directions", "click", get_directions_dialog_show, get_directions_dialog, true);
				YAHOO.util.Event.addListener("hide_directions", "click", directions_map_panel.hide, directions_map_panel, true);
				YAHOO.util.Event.addListener("show_url", "click", show_url_value, event_panel, true);
				YAHOO.util.Event.addListener("close_event", "click", hide_event_panel, event_panel, true);
				
				if (do_direct) {
					pass_str = 'evid=' + do_direct_evid;
					gc  = YAHOO.util.Connect.asyncRequest('POST','<?php echo $_SERVER['PHP_SELF'] ?>',cbev,pass_str);
				}
		}
				YAHOO.util.Event.addListener(window, "load", init);
		</script>

	</head>
	<body class="yui-skin-sam">
		<div class="x">&nbsp;
			<div id="container0">
				<div class="hd"></div>
				<div class="bd">
					<div id="cal1Container"></div>
					<div class="clear">&nbsp;</div>
				</div>
				<div class="ft">
					<button id="yearbutton1" class="fr"><?php echo $_SESSION['org_name'] ?> Home Page</button>
					<button id="commentbutton" class="fr">Send Comments</button>
					<div class="clear">&nbsp;</div>
				</div>
			</div>
			<div id="container">
				<div class="hd">
				</div>
				<div class="bd">
					<div id="calMonthContainer" style="display:block"></div>
					<div class="clear">&nbsp;</div>
				</div>
				<div class="ft">
				<button id="button4" class="fr"><?php echo $_SESSION['org_name'] ?> Home Page</button>
				<button id="button3" class="fr">Year View</button>
				<div class="clear">&nbsp;</div>
				</div>
			</div>
		</div>
		<div id="dialog1">
			<div class="hd">Please enter your comment</div>
			<div class="bd">
				<form method="POST" action="<?php echo $_SERVER['PHP_SELF']?>">
					<label for="name" style="float:left">Name:</label><input  style="float:right;width: 80%" type="textbox" name="name" id="comment_name" /><br>
					<label for="email"  style="float:left">E-mail:</label><input style="float:right;width: 80%" type="textbox" name="email" id="comment_email" /><br> 
					<label for="comment"  style="float:left">Comment:</label><textarea style="width: 80%;float:right" name="comment" id="comment_comment" rows="10"></textarea>
					<div class="clear"></div>
				</form>
			<div class="ft"></div>
			</div>
		</div>
		<div id="wait" style="visibility:hidden">Please Wait -- Retrieving Event Information</div>
		<div id="event_panel_markup" style="display:block;width:700px;">
			<div class="hd" id="event_header" style="text-align:center;height:auto"></div>
			<div class="bd" id="event_content"></div>
			<div class="ft" id="event_footer">
				<button id="show_map" style="float:left">Show Map</button>
				<button id="hide_map" style="float:left">Hide Map</button>
				<button id="close_event" class="fr">Close Event</button>
				<button id="show_url" class="fr">Show URL</button>
				<div class="clear">&nbsp;</div>
			</div>
		</div>
		<div id="map_panel_markup" style="display:block;width:500px">
			<div class="hd" style="text-align:center;height:auto">Map of Dance Location</div>
			<div class="bd" id="eventMap" style="display:block;height:500px;overflow:auto">&nbsp;</div>
			<div class="ft" id="eventMap_footer">
				<button id="hide_directions">Hide Directions</button>
			</div>
		</div>
		<div id="dlg" style="width:20em">
			<div class="hd" style="text-align:center;color:red">Notice</div>
			<div class="bd" style="text-align:center">The map for this event is not available.</div>
		</div>
		<div id="URLdlg" style="width:50em">
			<div class="hd" style="text-align:center;">Please use the following URL to link to this event</div>
			<div class="bd" style="text-align:center"></div>
		</div>
		<div id="from_loc" style="width:40em">
			<div class="hd">Please enter the starting address</div>
			<div class="bd">
				<form id="get_from_loc" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<label for="from_addr" style="font-weight:bold;float:left">From: </label><input type="text" style="float:right;width:80%" name="from_addr" id="from_addr">
					<div style="clear:both;line-height:0.01em">&nbsp;</div>
				</form>
			</div>
			<div class="ft"></div>
		</div>
		<div id="directions_panel" style="display:block;width:800px">
			<div class="hd"></div>
			<div class="bd">
				<div id="directions" style="display:block;float:left;height:500px;width:500px;border:1px solid black"></div>
				<div id="directions_sidebar" style="display:block;float:right;height:500px;width:250px;overflow:scroll"></div>
				<div style="clear:both;line-height:0.01em">&nbsp;</div>
			</div>
			<div class="ft"></div>
		</div>
	</body>
</html>
