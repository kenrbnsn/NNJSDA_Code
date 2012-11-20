<?php
$do_debug = false;
$ver = "1.0";
if (!preg_match('/slurp|grub|[Bb]ot|archiver|NetMonitor/',$_SERVER['HTTP_USER_AGENT'])) {
    if ($_SERVER["HTTP_X_FORWARDED_FOR"] != ""){
        $IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
        $proxy = $_SERVER["REMOTE_ADDR"];
        $host = @gethostbyaddr($_SERVER["HTTP_X_FORWARDED_FOR"]);
    }else{
        $IP = $_SERVER["REMOTE_ADDR"];
        $host = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
    }
	$body = "Remote Address:" . $host . "\n";
	$body .=	bdy($_POST,'$_POST');
	$body .=	bdy($_GET,'$_GET');
	$body .=	bdy($_SERVER,'$_SERVER');
	$body .=	bdy($_SESSION,'$_SESSION');
	$body .=	bdy($_COOKIE,'$_COOKIE');
		$from = "From: Visit Tracker <tracker@" . str_replace("www.","",$_SERVER['SERVER_NAME']).">";
 		@mail("kenrbnsn@kis-hosting.com","Yahoo Display Event page Visited (v" . $ver. ")",$body,$from,
		'-f kenrbnsn@nnjsda.org');
}

function bdy($arr,$str)
{
$tmp = array();
if (!empty($arr)) {
	$tmp[] = ' ---- ' . $str . ' ---- ';
	$tmp[] = print_r($arr,TRUE);
 }
 if (!empty($tmp)) return (implode("\n",$tmp)."\n");
}

function write_dbg($fp,$msg,$line,$dbg=true) {
	if ($dbg) fwrite($fp,date('Y-m-d G:i') . ' --- ' . __FILE__ . ' (' . $line . ') -- ' . $msg . "\r\n");
}

include('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname);
$mode = (file_exists('ajax_debug.txt'))?'a':'w';
$fp = ($do_debug)?$fp = fopen('ajax_debug.txt',$mode):false;
write_dbg($fp,'-----------------------------',__LINE__,$do_debug);
write_dbg($fp,str_replace("\n","\r\n",print_r($_POST,true)),__LINE__,$do_debug);
if (isset($_POST['ajaxform'])) {
	switch($_POST['ajaxform']) {
		case 'list':
			$q = "select event_org, event_type, event_date, ind  from new_events where event_date='" . date('Y-m-d',strtotime($_POST['date'])) . "' and event_org != '' order by event_start_time";
			$rs = mysql_query($q) or die("Error, Error in query: $q -->" . mysql_error());
			$events = array();
			while ($rw = mysql_fetch_assoc($rs)) {
				$tmp = array();
				foreach($rw as $k=>$v)
					switch($k) {
						case 'event_date':
							$tmp[$k] = date('n/d/Y',strtotime($v));
							break;
						default:
							$tmp[$k] = $v;
					}
				$events[] = $tmp;
			}
			if (!empty($events)) {
				echo json_encode($events)."\n";
			} else echo "Error, no events retrieved\n";
			exit();
			break;
		case 'display':
			$q = "select * from new_events where ind = '" . $_POST['whichevent'] . "'";
			$rs = mysql_query($q) or die("Error, Error in query: $q -->" . mysql_error());
			$events = array();
			while ($rw = mysql_fetch_assoc($rs)) {
				$tmp = array();
				foreach($rw as $k=>$v)
					switch($k) {
						case 'event_start_time':
						case 'event_stop_time':
							$tmp[$k] = date('g:i a',strtotime($v));
							break;
						case 'ind':
						case 'created_datetime':
						case 'updated_datetime':
							break;
						case 'event_date':
							$tmp[$k] = date('n/d/Y',strtotime($v));
							break;
						case 'event_description':
							$tmp[$k] = nl2br($v);
							break;
						default:
							$tmp[$k] = $v;
					}
				$events[] = $tmp;
			}
			if (!empty($events)) {
				echo json_encode($events)."\n";
			} else echo "Error, no events retrieved\n";
			exit();
			break;
		default:
			write_dbg($fp,'Fell down to the default case???',__LINE__,$do_debug);
			exit('Problems!');
	}
	exit('Something is wrong???');
} else {
	$q = "SELECT  event_date FROM $dbname.`new_events` where event_org != '' order by event_date";
	$rs = mysql_query($q) or die('Problem with query: ' . $q . '<br />' . mysql_error());
	$dates = array();
	$date_org = array();
	while($rw = mysql_fetch_assoc($rs)) {
				$dates[] = date('m/d/Y',strtotime($rw['event_date']));
	}
}
function include_yahoo_js($js,$ver,$dm,$dbg=false) {
	$found = false;
	$yui_ver = '0.11.' . $ver;
	if (file_exists('./yui_' . $yui_ver)) {
		$yui = './yui_' . $yui_ver;
		$found = true; }
	if (!$found && file_exists('../yui_' . $yui_ver)) {
		$yui = '../yui_' . $yui_ver;
		$found = true; }
	if (!$found && file_exists('../../yui_' . $yui_ver)) {
		$yui = '../../yui_' . $yui_ver;
		$found = true; }
	if (!$found) return;
	if (is_array($js) && !empty($js))
		foreach($js as $module) {
			if ($dbg && in_array($module,$dm)) $module .= '-debug';
			echo '<script type="text/javascript" src="' . $yui . '/' . $module . '.js"></script>'."\n"; }
	if (!is_array($js) && $js != '') {
		if ($dbg && in_array($js,$dm)) $js .= '-debug';
		echo '<script type="text/javascript" src="' . $yui . '/' . $js . '.js"></script>'."\n"; }
}

function include_yahoo_css($css,$ver,$dbg=false) {
	$found = false;
	$yui_ver = '0.11.' . $ver;
	if (file_exists('./yui_' . $yui_ver . '/css')) {
		$yui = './yui_' . $yui_ver . '/css';
		$found = true; }
	if (!$found && file_exists('../yui_' . $yui_ver . '/css')) {
		$yui = '../yui_' . $yui_ver . '/css';
		$found = true; }
	if (!$found && file_exists('../../yui_' . $yui_ver . '/css')) {
		$yui = '../../yui_' . $yui_ver . '/css';
		$found = true; }
	if (!$found) return;
	if (is_array($css) && !empty($css))
		foreach($css as $module) {
			echo '<link type="text/css" href="' . $yui . '/' . $module . '.css" media="screen" rel="stylesheet" />'."\n"; }
	if (!is_array($css) && $css != '') {
		echo '<link type="text/css" href="' . $yui . '/' . $css . '.css" media="screen" rel="stylesheet" />'."\n"; }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title>NNJSDA Calendar</title>
	<?php include_yahoo_css(array('calendar','container','logger'),3); ?>	
	<link href="new_nnjsda1.css" type="text/css" rel="STYLESHEET">
	<style>
body, html {
	padding: 0;
	margin: 0;
	font-size: 100%;
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
}

#hdr {
	width: 90%;
	margin-left: auto;
	margin-right: auto;
	border-bottom: 1px solid black;
	display: block;
	text-align: center;
}

#rest {
	width: 90%;
	margin-left: auto;
	margin-right: auto;
	padding-top: 0.5em;
	display: block;	
}


	.yui-calendar td.cellAppt {
		background-color: #AFEEEE;
	}
	
	.yui-calendar td.cellAppt a, .yui-calendar td.cellAppt a:hover {
		color: red;
	}
	
	.yui-calendar td.calcell.today {
	vertical-align:top;
	text-align: right;
	border:1px solid red;
	background-color: #FFF;
}

.yui-calendar td.calcell.today a {
	color: red;
}


.yui-calendar .calcell.oom, .yui-calendar .calcell.oom:hover {
	cursor:default;
	color:#999;
	background-color:#EEE;
	border:1px solid #E0E0E0;
}
.yui-calendar td.calcell.selected {
	color:#003DB8;
	background-color:#FFF;
	border:1px solid #E0E0E0;
}

.yui-calendar td.calcell.calcellhover {
	cursor:pointer;
	color:blue;
	border:1px solid #E0E0E0;
	background-color:#FFF;
}
.yui-calendar td.calcell.calcellhover a {
	color:#003DB8;
}


	
	hr {
		clear: both;
		width: 100%;
	}
	.overlay {
		border:1px solid black;
		background-color:#FFFFFF;
		z-index:10;
		padding:5px;
	}
	#win, #myDialog {
		display: none;
		visibility:hidden;
		width: 90%;
		display: block;
	}
	
	#myDialog {
		height: 80%;
		overflow: scroll;
		margin: 0 auto;
		width: 800px;
	}
	
	.label {
		width: 40%;
		font-weight: bold;
		float: left;
		display: block;
	}

	.input {
		width: 59%;
		float: left;
		display: block;
	}
	
	.txtinp {
				width: 100%;
	}
	</style>
	<?php include_yahoo_js(array('yahoo','event','dom','calendar','dragdrop','connection','container','logger'),3,array('connection'),true); ?>
	<script language="javascript">
		var do_debug = new Boolean;
		do_debug = <?php echo ($do_debug)?"true":"false"; ?>;
		var pass_date = '';
		

	Date.prototype.defaultView=function(){
		var dd=this.getDate();
		if(dd<10)dd='0'+dd;
		var mm=this.getMonth()+1;
		if(mm<10)mm='0'+mm;
		var yyyy=this.getFullYear();
		return String(mm+"\/"+dd+"\/"+yyyy)
	}
	
	Array.prototype.inarray = function(itm) {
		var retv = new Boolean (false);
		for (x in this)
			if (this[x] == itm) retv = true;
		return (retv);
	}
	
	Array.prototype.keyexists = function(key) {
		var retv = new Boolean (false);
		for (x in this) {
			if (x == key) retv = true;
			}
		return(retv)
	}

function dump_props(obj, obj_name, ddbg) {
	if (!ddbg) return;
   var result = ""
   for (var i in obj) {
      result += obj_name + "." + i + " = " + obj[i] + '\n';
   }
	YAHOO.log(result);
}

function dump_props1(obj, obj_name) {
   var result = ""
   for (var i in obj) {
		if (obj[i].constructor == Object)  result += dump_props1(obj[i],obj_name + '.' + i);
		if (i == 'panel') result += dump_props1(obj[i],obj_name + '.' + i);
      result += obj_name + "." + i + " = " + obj[i] + ' (' + obj[i].constructor + ')<br />'
   }
   result += ""
	return(result)
	}

	var selectedDates = "<?php echo implode(',',$dates) ?>";
	var dates_org = new Array();
	var panels = new Array();
	var selectedDates = "<?php echo implode(',',$dates) ?>";
	
	var renderAppointmentDay = function(workingDate, cell) {
      YAHOO.util.Dom.addClass(cell, "cellAppt");
		}
		
	var cbnull = function(){}
	
	var cb =
	{
		success:cbnull,
		failure:cbnull
	}
	
	var handleCancel = function() {
		this.cancel();
	}

	var get_cb_suc1 = function(o) {
		var OKButtons = [ { text:"Ok", 
							handler:handleOK }];
		var returnText = o.responseText;
		if (do_debug == true) YAHOO.log('get_cb_suc1: returnText = ' + returnText);
		var events = eval('(' + returnText + ')');
		var tmp = new Array();
		dump_props(events[0],'events',do_debug);
		bdy = '';
		for (fld in events[0]) {
			if (do_debug == true) YAHOO.log('get_cb_suc1: fld: ' + fld + ' = ' + events[0][fld]);
			switch(fld) {
				case 'event_org':
				case 'event_name':
				case 'event_type':
				case 'event_description':
					bdy += '<div class="row">\n<span class="label">' + fld + ':</span>\n<span class="formw">' + events[0][fld] + '</span></div>';
					break;
			}
		}
		if (do_debug == true) YAHOO.log('get_cb_suc1: bdy = ' + bdy);
		DisplayEvent.setBody(bdy);
		DisplayEvent.cfg.queueProperty("buttons", OKButtons);
		DisplayEvent.setHeader('<span style="text-align:center;display:block;width:100%">' + events[0].event_org + '<br />' + events[0].event_date + '</span>');
		MyWaitPanel.hide();
		DisplayEvent.render();
		DisplayEvent.show();
}	

var handleDisplay = function() {
	document.dlg1.ajaxform.value = "display";
	UpdateMultiple.callback.success = get_cb_suc1;
	MyWaitPanel.setHeader("Retrieving Event for Display, please wait...");
	MyWaitPanel.render(document.body);
	MyWaitPanel.show();
	this.submit();
}

var handleOK = function() {
 this.hide();
}

var handlePrint = function() {
	this.print();
}


	var get_cb_suc = function(o) {
		var rt = o.getAllResponseHeaders;
		var returnText = o.responseText;
		if (do_debug == true) YAHOO.log('get_cb_suc: returnText = ' + returnText);
		var events = eval('(' + returnText + ')');
		if (do_debug == true) YAHOO.log('get_cb_suc: events[0](length) = ' + events.length);
		dump_props(events[0],'events',do_debug);
		MyWaitPanel.hide();
		var myButtons = [ { text:"Display Event", handler:handleDisplay, isDefault:true }, { text: "Cancel", handler:handleCancel }];		
		if (do_debug == true) {
			YAHOO.log('get_db_suc: events.event_date : ' + events[0].event_date);
			YAHOO.log('get_db_suc: Before  setHeader'); }
		DisplayMultiple.setHeader(events[0].event_date);
		pass_date = events[0].event_date;
		if (do_debug == true){
			YAHOO.log('get_db_suc: pass_date = ' + pass_date);
			YAHOO.log('get_db_suc: after setHeader');
		}
		bdy = '<form id="dlg1" name="dlg1" method="post" action="<? echo $_SERVER['PHP_SELF'] ?>">';
		bdy += '<input name="ajax" value="1" type="hidden"><input name="ajaxform" value="" type="hidden">';
		bdy += '<span style="display:none"><input name="whichevent" type="radio" value="-1"></span>';
		if (do_debug == true) YAHOO.log('get_cb_suc: bdy = ' + bdy);
		for(evt=0;evt<events.length;evt++) {
				YAHOO.log('get_db_suc: evt: ' + evt);
				bdy += '<input type="radio" name="whichevent" value="' + events[evt].ind +'"> ' + events[evt].event_org + ' ' + events[evt].event_type + '<br>';
		}
		bdy += '</form>';
		if (do_debug == true) YAHOO.log('get_cb_suc: bdy = ' + bdy);
		DisplayMultiple.setBody(bdy);
		DisplayMultiple.cfg.queueProperty("buttons", myButtons);
		DisplayMultiple.render(document.body);
		if (do_debug == true) YAHOO.log('get_db_suc: just before the show()');
		DisplayMultiple.show();
	}	

	var get_cb_fail = function(o) {
		alert("Your submission failed. Status: " + o.status);
		MyWaitPanel.hide()
}
	
var handleDisplay = function() {
	document.dlg1.ajaxform.value = "display";
	DisplayMultiple.callback.success = get_cb_suc1;
	MyWaitPanel.setHeader("Retrieving Event for Display, please wait...");
	MyWaitPanel.render(document.body);
	MyWaitPanel.show();
	this.submit();
}

	var get_cb =
	{
		success:get_cb_suc,
		failure:get_cb_fail
	}
	
	
	var onSelect = function() {
		dt = this.getSelectedDates();
		dmy = this.deselect(dt);
		if (selectedDates.indexOf(dt[0].defaultView()) > -1)  {
			pass_str = 'date=' + dt[0].defaultView() + '&ajaxform=list&ajax=1';
			gc = YAHOO.util.Connect.asyncRequest('POST','display_event.php',get_cb,pass_str);
			MyWaitPanel.setHeader("Retrieving Events, please wait...");
			MyWaitPanel.render(document.body);
			MyWaitPanel.show();
		}
	}

	var write_calyear = function() {
		var element = document.getElementById('calyear');
		var py = document.getElementById('prevyear');
		var ny = document.getElementById('nextyear');
		var pass_str = 'where=write_calyear&old_year='+element.innerHTML+'&oldprev='+py.innerHTML+'&oldnext='+ny.innerHTML;
		year = this.pageDate.getFullYear();
		pyear = year - 1;
		nyear = year + 1;
		if (element.innerHTML != year) {
			element.innerHTML = year;
			py.innerHTML = pyear;
			ny.innerHTML = nyear;
			<?php if ($_SERVER['SERVER_NAME'] != "localhost") { ?>
			pass_str += '&new_year='+year+'&newprev='+pyear+'&newnext='+nyear;
			YAHOO.util.Connect.asyncRequest('POST',
				'http://www.nnjsda.org/trace_it.php',cb,
				pass_str);
			<?php } ?>	
			}
		this.render();
		return(true);
	}

	function init () { 
				cal1group = new YAHOO.widget.CalendarGroup(12,"cal1group", "cal1Container","1/<?php echo date('Y')?>");
				cal1group.addRenderer(selectedDates, renderAppointmentDay);
				cal1group.setChildFunction("onSelect", onSelect);
				cal1group.setChildFunction("onChangePage", write_calyear);
				cal1group.render();
				if (do_debug == true) {
					var myLogReader = new YAHOO.widget.LogReader();
//					myLogReader.enableBrowserConsole() = true;
				}
				MyWaitPanel = 
						new YAHOO.widget.Panel("wait", 
										{ width:"300px", 
										  fixedcenter:true, 
										  underlay:"shadow", 
										  close:false, 
										  visible:false, 
										  draggable:false, 
										  modal:true
										  } 
										 );
		
				MyWaitPanel.setHeader("Loading, please wait...");
				DisplayMultiple = new YAHOO.widget.Dialog("dlg1", { 
					width: "30em", 
					fixedcenter:true,
					modal:true,
					draggable:false });
				DisplayEvent = new YAHOO.widget.SimpleDialog("myDialog",{
					fixedcenter:true,
					underlay:"shadow",
					visible:false,
					draggable:false,
					modal:true});


			}
YAHOO.util.Event.addListener(window, "load", init,null);

	</script>
</head>
<body>
<div id="hdr">
<h1>NNJSDA Calendar</h1>
</div>
<div id="rest">&nbsp;
	<div style="display:block; width:80%; margin-left: auto; margin-right:auto;">
			<div style="width:100%;clear:both;text-align:center;">
				<a href="javascript:cal1group.nextYear()" class="navLink" style="float:right;font-size:12px;text-decoration:none">Next Year (<span id="nextyear"><?php $y = date('Y') + 1; echo $y; ?></span>)</a>
				<a href="javascript:cal1group.previousYear()" class="navLink" style="float:left;font-size:12px;text-decoration:none">Previous Year (<span id="prevyear"><?php $y = date('Y') - 1; echo $y; ?></span>)</a>
				<span style="text-align:center"><span id="calyear" style="font-weight:bold;font-size:150%;"><?php echo date('Y') ?></span></span>
			</div>
			<div style="clear:both;line-height:0.01em;font-size:1%">&nbsp;</div>
			<div id="cal1Container" style="float:left;width:100%">
<?php
for ($i=0;$i<12;$i++)
	echo '<div id="cal1Container_' . $i . '" style="float:left;width:25%"></div>'."\n";
?>
			</div>
	</div>
	<div style="clear:both;line-height:0.01em;font-size:1%">&nbsp;</div>
	<div id="win">
	  <div class="hd" style="text-align:center"></div> 
	  <div class="bd"></div> 
	  <div class="ft"></div> 
	</div>
</div>
	<?php if ($do_debug) echo '<div id="myLogger"></div>' . "\n"; ?>
	<div id="dlg1">
		<div class="hd"></div>
		<div class="bd"></div>
		<div class="ft"></div>
	</div>
<hr />
<div id="debug" style="display:block;clear:both"></div>
</div>
<div id="myPanel">
  <div class="hd"></div> 
  <div class="bd" id="events">
  </div> 
  <div class="ft"></div> 
</div>
	<div id="myDialog" style="display:hidden;font-size:80%">
		<div class="hd"></div>
		<div class="bd"></div>
		<div class="ft"></div>
	</div>
<div style="display:none">
 			<div id="disp_event_date" style="display:none"></div>
			<div class=row>
                <span class=label>Club or Organization Name:</span>
                <span class=formw id="disp_event_org"></span>
         </div>
         <div class=row>
                <span class=label>Event Type:</span>
                <span class=formw id="disp_event_type"></span>
         </div>
         <div class=row>
                <span class=label>Event Name:</span>
                <span class=formw id="disp_event_name"></span>
         </div>
         <div class=row>
                <span class=label>Event Description:</span>
                <span class=formw id="disp_event_description"></span>
         </div>
         <div class=row>
                <span class=label>Event URL:</span>
                <span class=formw id="disp_event_url></span>
         </div>
          <div class=row>
                <span class=label>Start Time:</span>
                <span class=formw><input name="event_start_time" class="textinp" value="8:00 pm"></span>
                </div>
                <div class=row>
                <span class=label>End Time:</span>
                <span class=formw><input name="event_stop_time" class="textinp" value="10:30 pm"></span>
                </div>
                <div class=row>
                <span class=label>Location:</span>
                <span class=formw><input name="event_location" class="textinp" type="Text"></span>
                </div>
                <div class=row>
                <span class=label>Address:</span>
                <span class=formw><input name="event_address" class="textinp" type="Text"></span>
                </div>
                <div class=row>
                <span class=label>City:</span>
                <span class=formw><input name="event_city" class="textinp" type="Text"></span>
                </div>
                <div class=row>
                <span class=label>State:</span>
                <span class=formw><input name="event_state" class="textinp" type="Text"></span>
                </div>
                <div class=row>
                <span class=label>Zip:</span>
                <span class=formw><input name="event_zip" class="textinp" type="Text"></span>
                </div>
                <div class=row>
                <span class=label>Square Dance Program:</span>
                <span class=formw>
                </span>
                </div>
                <div class=row>
                <span class=label>Round Dance Program:</span>
                <span class=formw>
                </span>
                </div>
                <div class=row>
                <span class=label>Caller(s):</span>
                <span class=formw><input name="event_caller" class="textinp" type=text></span>
                </div>
                <div class=row>
                <span class=label>Cuer(s):</span>
                <span class=formw><input name="event_cuer" class="textinp" type=text></span>
                </div>
                <div class=row>
                <span class=label>Club Contacts:</span>
                <span class=thirda  style="width:20%;"><span class=boldcu>Name:</span><br>
                <? 
                 for ($i=0;$i<3;$i++)
                		echo '<input type=text  class="textinp" name="contact_name[]" id="event_contact_name' . $i . '">' . "\n";
                ?>
                </span>
                <span class=thirda style="width:20%"><span class=boldcu>Email:</span><br>
                <? for ($i=0;$i<3;$i++)
                		echo '<input type=text  class="textinp" name="contact_email[]" id="event_contact_email' . $i . '">' . "\n";
                ?>
                </span>
                <span class=thirda style="width:20%"><span class=boldcu>Phone:</span><br>
                <? for ($i=0;$i<3;$i++)
                		echo '<input type=text  class="textinp" name="contact_phone[]" id="event_contact_phone' . $i . '">' . "\n";
                ?>
                </span>
                </div>
	</div>
</body>
</html>
