<?php
if (!extension_loaded('json')) {
   $prefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
   dl($prefix . 'json.' . PHP_SHLIB_SUFFIX);
}
$ver = "1.4";
$do_debug = (isset($_GET['d']))?true:false;
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
 		@mail("kenrbnsn@kis-hosting.com","NNJSDA Admin New Update Events Visited (v" . $ver. ")",$body,$from,
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

include('../dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname);
$mode = (file_exists('ajax_debug.txt'))?'a':'w';
$fp = ($do_debug)?$fp = fopen('ajax_debug.txt',$mode):false;
write_dbg($fp,'-----------------------------',__LINE__,$do_debug);
write_dbg($fp,str_replace("\n","\r\n",print_r($_POST,true)),__LINE__,$do_debug);
if (isset($_POST['ajax'])) {
	write_dbg($fp,'$_POST[' . "'ajax'" . '] is set',__LINE__,$do_debug);
	write_dbg($fp,'$_POST[' . "'ajaxform'" . '] = ' . $_POST['ajaxform'],__LINE__,$do_debug);
	write_dbg($fp,'$_POST[' . "'Event_org'" . '] = ' . $_POST['event_org'],__LINE__,$do_debug);
	write_dbg($fp,'$_POST[' . "'whichevent'" . '] = ' . $_POST['whichevent'],__LINE__,$do_debug);
	$tmp = array();
	write_dbg($fp,'Just before the switch statement',__LINE__,$do_debug);
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
		case 'get':
			$q = "select * from new_events ind = '" . $_POST['ind'] . "'";
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
		case 'Add':
			if (!isset($_POST['event_org']) || (isset($_POST['event_org']) && $_POST['event_org'] == '[none]'))
				exit('Error,' . $_POST['event_date'] . ',You did not select an organization');
			foreach ($_POST as $fld => $val)
				        switch ($fld) {
				                case 'event_name':
				                case 'event_description':
				                case 'event_org':
				                case 'event_location':
				                case 'event_address':
				                case 'event_city':
				                case 'event_type':
				                case 'event_state':
				                case 'event_cuer':
				                case 'event_caller':
				                case 'event_url':
										$tmp[] = $fld . "='" . mysql_real_escape_string(trim(stripslashes($val))) . "'";
										break;
				                case 'event_zip':
										$tmp[] = $fld . "='" . $val . "'";
										break;
				                case 'contact_name':
				                case 'contact_email':
				                case 'contact_phone':
										$tmp[] = 'event_' . $fld . "='" . mysql_real_escape_string(implode(',',$val)) . "'";
										break;
				                case 'event_date':
										$tmp[] = $fld . " = '" . date('Y-m-d',strtotime($val)) . "'";
										break;
				                case 'event_start_time':
						case 'event_stop_time':
									 	if (strlen(trim($val)) == 0) {
											if ($fld == 'event_start_time') $val = '8:00 PM';
											if ($fld == 'event_stop_time') $val = '10:30 PM';
										}
										$tmp[] = $fld . "='" . date('H:i',strtotime($val)) . "'";
										break;
				                case 'event_sd_program':
						case 'event_rd_program':
				                        $tmp[] = $fld . "='" . implode(' ',$val) . "'";
				                break;
				        }
				$tmp[] = "created_datetime = '" . date('Y-m-d H:i') ."'";
				$q = "insert new_events set " . implode(", ",$tmp);
				write_dbg($fp,$q,__LINE__,$do_debug);
				$result = mysql_query($q) or die(write_dbg($fp,"Problem with the query: $q -- " . mysql_error(),__LINE__,$do_debug));
				echo "Added," . $_POST['event_date'] . ',' . stripslashes($_POST['event_org']);
				write_dbg($fp, "Event added OK," . $_POST['event_date'] . ',' . stripslashes($_POST['event_org']),__LINE__,$do_debug);
				if ($do_debug) fclose($fp);
				exit();
				break;
		case 'Update':
			if (!isset($_POST['event_org']) || (isset($_POST['event_org']) && $_POST['event_org'] == '[none]'))
				exit('Error,' . $_POST['event_date'] . ',You did not select an organization');
			$q = "select * from new_events where event_date='" . date('Y-m-d',strtotime($_POST['event_date'])) . "' and event_org = '" . mysql_real_escape_string(stripslashes($_POST['event_org'])) . "'";
			write_dbg($fp,$q,__LINE__,$do_debug);
			$rs = mysql_query($q) or die(write_dbg($fp,"Error with update query: $q\n" . mysql_error()));
			$rw = mysql_fetch_assoc($rs);
			write_dbg($fp,str_replace("\n","\r\n",print_r($rw,true)),__LINE__,$do_debug);
			foreach ($_POST as $fld => $val) {
				if (is_array($val)) write_dbg($fp,$fld . '=>' . print_r($val,true),__LINE__,$do_debug);
								else	  write_dbg($fp,$fld . '=>' . $val,__LINE__,$do_debug);
				        switch ($fld) {
				                case 'event_name':
				                case 'event_description':
				                case 'event_org':
				                case 'event_location':
				                case 'event_address':
				                case 'event_city':
				                case 'event_type':
				                case 'event_state':
				                case 'event_url':
				                case 'event_caller':
						case 'event_cuer':
									 	if ($rw[$fld] != trim(stripslashes($val))) 
												$tmp[] = $fld . "='" . mysql_real_escape_string(trim(stripslashes($val))) . "'";
										break;
				                case 'event_zip':
									 	if ($rw[$fld] != $val)
												$tmp[] = $fld . "='" . $val . "'";
										break;
				                case 'contact_name':
				                case 'contact_email':
				                case 'contact_phone':
											if ($rw[$fld] != implode(',',$val))
												$tmp[] = 'event_' . $fld . "='" . mysql_real_escape_string(implode(',',$val)) . "'";
										break;
				                case 'event_start_time':
									 case 'event_stop_time':
											if($rw[$fld] != date('H:i:00',strtotime($val)))
												$tmp[] = $fld . "='" . date('H:i:00',strtotime($val)) . "'";
										break;
				                case 'event_sd_program':
									 case 'event_rd_program':
											if($rw[$fld] != implode(' ',$val))
				                        $tmp[] = $fld . "='" . implode(' ',$val) . "'";
				                break;
				        }
					}
				write_dbg($fp,str_replace("\n","\r\n",print_r($_tmp,true)),__LINE__,$do_debug);
				if (!empty($tmp)) {
					$tmp[] = "updated_datetime = '" . date('Y-m-d H:i') ."'";
					$q = "update new_events set " . implode(", ",$tmp) . " where event_date='" . date('Y-m-d',strtotime($_POST['event_date'])) . "' and event_org = '" . $_POST['event_org'] . "'";
					write_dbg($fp,$q,__LINE__,$do_debug);
					$result = mysql_query($q) or die(write_dbg($fp,"Problem with the query: $q -- " . mysql_error(),__LINE__,$do_debug));
					echo "Updated," . $_POST['event_date'] . ',' . stripslashes($_POST['event_org']); }
				write_dbg($fp, "Updated," . $_POST['event_date'] . ',' . stripslashes($_POST['event_org']),__LINE__,$do_debug); 
				if ($do_debug) fclose($fp);
				exit();
				break;
		case 'Get_Update':
			$q = "select * from new_events where ind='" . $_POST['whichevent'] . "'";
			write_dbg($fp,$q,__LINE__,$do_debug);
			$rs = mysql_query($q) or die(write_dbg($fp,"Error getting info for update: $q\n" . mysql_error()));
			$events = array();
			$tmp = array();
			$rw = mysql_fetch_assoc($rs);
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
					default:
						$tmp[$k] = $v;
				}
			$events[] = $tmp;
			echo json_encode($events)."\n";
			exit();
			break;
		case 'Delete';
			$q = "delete from new_events where event_date='" . date('Y-m-d',strtotime($_POST['event_date'])) . "' and event_org = '" . mysql_real_escape_string(stripslashes($_POST['event_org'])) . "'";
			write_dbg($fp,$q,__LINE__,$do_debug);
			$rs = mysql_query($q) or die(write_dbg($fp,"Error with update query: $q\n" . mysql_error()));
			list($dum1,$dum2,$num_left) = get_num_left($_POST['event_date'],'d');
			echo "Deleted,". $_POST['event_date'] . ',' . stripslashes($_POST['event_org']) . ',' . $num_left;
			write_dbg($fp, "Deleted," . $_POST['event_date'] . ',' . stripslashes($_POST['event_org']),__LINE__,$do_debug); 
			exit();
			break;
		case 'Delete_via_ind':
			list($event_date,$event_org,$num_left) = get_num_left($_POST['whichevent'],'i');
			$q = "delete from new_events where ind = '" . $_POST['whichevent'] . "'";
			write_dbg($fp,$q,__LINE__,$do_debug);
			$rs = mysql_query($q) or die(write_dbg($fp,"Error with update query: $q\n" . mysql_error()));
			$num_left--;
			echo "Deleted,". date('m/d/Y',strtotime($event_date)) . ',' . $event_org . ',' . $num_left;
			write_dbg($fp, "Deleted," . date('m/d/Y',strtotime($event_date)) . ',' . $event_org,__LINE__,$do_debug); 
			exit();
			break;

		default:
			write_dbg($fp,'Fell down to the default case???',__LINE__,$do_debug);
			exit('Problems!');
	}
	exit('Something is wrong???');
}
$q = "SELECT  event_date FROM $dbname.`new_events` where event_org != '' order by event_date";
$rs = mysql_query($q) or die('Problem with query: ' . $q . '<br />' . mysql_error());
$dates = array();
$date_org = array();
while($rw = mysql_fetch_assoc($rs)) {
			$dates[] = date('m/d/Y',strtotime($rw['event_date']));
}
function  get_num_left($k,$w)
{
	switch ($w) {
		case 'd':
			$whr = "event_date = '" . date('Y-m-d',strtotime($k)) . "'";
			break;
		case 'i':
			$whr = "ind = '" . $k . "'";
			break;
	}
	$q = "select event_date, event_org from new_events where $whr";
	$rs = mysql_query($q);
	$num = mysql_num_rows($rs);
	$ret_ary = array('','',$num);
	if ($num > 0 && $w == 'i') {
		$rw = mysql_fetch_assoc($rs);
		$ret_ary[0] = $rw['event_date'];
		$ret_ary[1] = $rw['event_org'];
		list($dmy1,$dmy2,$ret_ary[2]) = get_num_left($rw['event_date'],'d');
	}
	return($ret_ary);
}

function get_club_names($cluborg)
{
        $query = "Select club from clubs order by club";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
                $sel = (urldecode($row->club) == $cluborg)?" selected":"";
                echo '<option value="' . stripslashes(urldecode($row->club)) . '"' . $sel . '>' . stripslashes(urldecode($row->club)) . "</option>\n"; }
}
function get_event_types($et)
{
        $query = "Select event_type from eventtypes order by event_type";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
                $sel = ($et == $row->event_type)?' selected':'';
                echo '<option value="' . $row->event_type . '"' . $sel .'>' . $row->event_type . "</option>\n";
        }
}
function put_months($d)
{
        $cy = date('Y',strtotime('today'));
        for ($i=1;$i<13;$i++){
                $mon = date("F",strtotime("$i/1/$cy"));
                $selected = (IsSet($d)) ? ' ' . is_selected($mon,$d,"F") : '';
                echo '<option value="' . $mon .'"'. $selected . '>' . $mon . "</option>\n";}
}
function is_selected($t,$i,$w,$o=true)
{
        $sel = ($o) ? " selected" : " checked";
        if ($t == date($w,strtotime($i))) return($sel);
        else return("");
}
function put_sd_program($csd)
{
        $sdp = array('ms','plus','a1','a2','c1','c2');
        $tmp = array();
        for ($i=0;$i<count($sdp);$i++) {
                $checked = (!(strpos(" $csd ",$sdp[$i]) === false))?"checked":"";
                $disp = ($i == 0)?"Mainstream":ucwords($sdp[$i]);
                $tmp[] = "<input type='checkbox' name='event_sd_program[]' id='event_sd_program" . $i . "' value='{$sdp[$i]}' $checked>&nbsp;$disp";
        }
		  echo implode(' | ',$tmp) . "\n";
}
function put_rd_program($crd)
{
        $rdp = array('I','II','III','IV','V','VI');
        $tmp = array();
        for ($i=0;$i<count($rdp);$i++) {
                $checked = (!(strpos(" $crd ",$rdp[$i]) === false))?"checked":"";
                $d = $i + 1;
                $tmp[] = "<input type='checkbox' name='event_rd_program[]' id='event_rd_program" . $i . "' value='{$rdp[$i]}' $checked>&nbsp;Phase $d";
        }
		  echo implode(' | ',$tmp) . "\n";
}
function put_day($n='event_weekday')
{
        $days = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
        for ($i=0;$i<7;$i++)
        {
                echo '<input type="Radio" name="' . $n . '" value="' . $days[$i] . '">' . $days[$i] . "|\n";
        }
}

function include_yahoo_js($js,$majv,$ver,$dm,$dbg=false) {
	$found = false;
	$yui_ver = '0.' . $majv . '.' . $ver;
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

function include_yahoo_css($css,$majv,$ver,$dbg=false) {
	$found = false;
	$yui_ver = '0.' . $majv . '.' . $ver;
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
	<title>NNJSDA Calendar</title>
	<?php include_yahoo_css(array('calendar','container','logger'),12,2); ?>	
	<link href="new_nnjsda.css" type="text/css" rel="STYLESHEET">
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
	.ttt {
	overflow: scroll;
}
	</style>
	<?php include_yahoo_js(array('yahoo','event','dom','calendar','dragdrop','connection','container','logger'),12,2,array(),$do_debug); ?>
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

	function dt_struct(org,panel) {
		this.org = org,
		this.panel = panel
	}
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
	
	var get_cb_suc = function(o) {
		var rt = o.getAllResponseHeaders;
		var returnText = o.responseText;
		if (do_debug == true) YAHOO.log('get_cb_suc: returnText = ' + returnText);
		var events = eval('(' + returnText + ')');
		if (do_debug == true) YAHOO.log('get_cb_suc: events[0](length) = ' + events.length);
		dump_props(events[0],'events',do_debug);
		MyWaitPanel.hide()
		var myButtons = [ { text:"Add New Event",handler:handleNew},{ text:"Update Event", handler:handleUpdate, isDefault:true },
								{ text:"Delete Event", handler:handleDelete }, { text: "Cancel", handler:handleCancel }];
			
		if (do_debug == true) {
				YAHOO.log('get_db_suc: UpdateMultiple: ' + UpdateMultiple);
				YAHOO.log('get_db_suc: events.event_date : ' + events[0].event_date);
		}
		UpdateMultiple.setHeader(events[0].event_date);
		pass_date = events[0].event_date;
		YAHOO.log('get_db_suc: after setHeader');

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
		UpdateMultiple.setBody(bdy);
		UpdateMultiple.cfg.queueProperty("buttons", myButtons);
		UpdateMultiple.render(document.body);
		if (do_debug == true) YAHOO.log('get_db_suc: just before the show()');
		UpdateMultiple.show();
	}	
	
	var get_cb_suc1 = function(o) {
		var rt = o.getAllResponseHeaders;
		var returnText = o.responseText;
		if (do_debug == true) YAHOO.log('get_cb_suc1: returnText = ' + returnText);
		if (returnText.substr(3) == 'Err') {
			var OKButtons = [ { text:"Ok", 
								handler:handleOK }];
			tmp = returnText.split(",");
			mySimpleDialog = new YAHOO.widget.SimpleDialog("dlg", { 
				width: "20em", 
				fixedcenter:true,
				modal:true,
				draggable:false });
			mySimpleDialog.setHeader(tmp[1]);
			bdy = (tmp.length == 3)?tmp[0] + '<br>Organization: ' + tmp[2]:tmp[0];
			if (tmp[0] == 'Not Added' || tmp[0] == 'Error') {
				bdy = tmp[0] + '<br><span style="font-weight:bold;color=red">' + tmp[2] + '</span>'; }
			mySimpleDialog.setBody(bdy);
			mySimpleDialog.cfg.queueProperty("buttons", OKButtons);
			mySimpleDialog.render(document.body);
			mySimpleDialog.show();
		} else {
		var events = eval('(' + returnText + ')');
		var frm = document.forms["dlgForm"];
		var dbg = document.getElementById('debug');
		var tmp = new Array();
		cur = dbg.innerHTML;
		dump_props(events[0],'events',do_debug);
		for (i=0;i<frm.length;i++) {
			if (do_debug == true) YAHOO.log(frm.elements[i].type);
			if (frm.elements[i].type == "text" || frm.elements[i].type == "textarea") {
				if(frm.elements[i].name.substr(0,8) != 'contact_') {
					frm.elements[i].value = events[0][frm.elements[i].name]; }
				else {
					tmp_contact = events[0]['event_' + frm.elements[i].name.replace(/\[\]/,'')].split(',');
					for(j=0;j<3;j++) {
						if (do_debug == true) YAHOO.log('event_' + frm.elements[i].name.replace(/\[\]/,'') + j);
						frm['event_' + frm.elements[i].name.replace(/\[\]/,'') + j].value = tmp_contact[j]; }
				}
			}
			if (frm.elements[i].type == "checkbox") {
				prgtmp = events[0][frm.elements[i].name.replace(/\[\]/,'')].split(' ');
				tmpp = frm.elements[i].name.split('_');
				maxcb = (tmpp[1] == 'sd')?5:4;
				for(j=0;j<maxcb;j++) {
					if (prgtmp[j] == frm.elements[i].value) {
						frm.elements[i].checked = true; 
					}
				}
			}
			if (frm.elements[i].type == "select-one") {
				if (do_debug == true) YAHOO.log(frm.elements[i].name + '=>' + frm.elements[i].type + '=>' + frm.elements[i].length);
				for (j=0;j<frm.elements[i].length;j++) {
					if (do_debug == true) YAHOO.log(frm.elements[i].options[j].value);
					if (frm.elements[i].options[j].value == events[0][frm.elements[i].name]) {
						frm.elements[i].options[j].selected = true;
					}
				}
			}
			tmp[i] = "frm.elements[" + i + "] = " + frm.elements[i].name + ',' + frm.elements[i].type + ' => ' + frm.elements[i].value; }
		if (do_debug == true) dbg.innerHTML += '<hr>' + tmp.join('<br>');
		MyWaitPanel.hide()
			var myButtons = [ { text:"Update", handler:handleSubmit, isDefault:true },{ text:"Delete", handler:handleSubmitDelete},
				  { text:"Cancel", handler:handleCancel } ];
			myDialog.cfg.queueProperty("buttons", myButtons);
		myDialog.render(); 
		myDialog.show();
	}
}	
	var get_cb_fail = function(o) {
		alert("Your submission failed. Status: " + o.status);
		MyWaitPanel.hide()
}

	var get_cb =
	{
		success:get_cb_suc,
		failure:get_cb_fail
	}
	
	var get_cb1 =
	{
		success:get_cb_suc1,
		failure:get_cb_fail
	}
	
	var handleCancel = function() {
		this.cancel();
	}
	var handleSubmitAdd = function() {
		MyWaitPanel.setHeader("Adding Event, please wait...");
		MyWaitPanel.render(document.body);
		MyWaitPanel.show();
		this.submit();
	}

	var handleSubmit = function() {
		MyWaitPanel.setHeader("Updating Event, please wait...");
		MyWaitPanel.render(document.body);
		MyWaitPanel.show();
		this.submit();
	}

	var handleSubmitDelete = function() {
		ajxf = document.getElementById('ajaxform');
		ajxf.value = "Delete";
		MyWaitPanel.setHeader("Deleting Event, please wait...");		
		MyWaitPanel.render(document.body);
		MyWaitPanel.show();
		this.submit();
	}

var handleOK = function() {
 MyWaitPanel.hide()
 this.hide();
}

var handleNew = function() {
	var myButtons = [ { text:"Add", handler:handleSubmitAdd, isDefault:true },
		  { text:"Cancel", handler:handleCancel } ];
	dt = pass_date;
	if (do_debug == true) YAHOO.log('In handleNew, dt:' + dt);
	myDialog.cfg.queueProperty("buttons", myButtons);
	myDialog.render(); 
	frm.reset();
	ajxf.value = "Add";
	eventDate.value = dt;
	if(do_debug == true) YAHOO.log('Event date = ' + eventDate.value);
	UpdateMultiple.hide();
	myDialog.show();

}

var handleUpdate = function() {
	document.dlg1.ajaxform.value = "Get_Update";
	UpdateMultiple.callback.success = get_cb_suc1;
	MyWaitPanel.setHeader("Retrieving Event for Update, please wait...");
	MyWaitPanel.render(document.body);
	MyWaitPanel.show();
	this.submit();
}

var handleDelete = function() {
	document.dlg1.ajaxform.value = "Delete_via_ind";
	UpdateMultiple.callback.success = onSuccess;
	MyWaitPanel.setHeader("Deleting Event, please wait...");		
	MyWaitPanel.render(document.body);
	MyWaitPanel.show();
	this.submit();	
}

	var myButtons = [ { text:"Submit", handler:handleSubmit, isDefault:true },
					  { text:"Cancel", handler:handleCancel } ];
					  
	var onSuccess = function(o) {
		var OKButtons = [ { text:"Ok", 
							handler:handleOK }];
		var response = o.responseText;
		if (do_debug == true) YAHOO.log('onSuccess: response = ' + response);
		tmp = response.split(",");
		mySimpleDialog = new YAHOO.widget.SimpleDialog("dlg", { 
			width: "20em", 
			fixedcenter:true,
			modal:true,
			draggable:false });
		mySimpleDialog.setHeader(tmp[1]);
		bdy = (tmp.length >= 3)?tmp[0] + '<br>Organization: ' + tmp[2]:tmp[0];
		if (tmp[0] == 'Not Added') {
			bdy = tmp[0] + '<br><span style="font-weight:bold;color=red">' + tmp[2] + '</span>'; }
		mySimpleDialog.setBody(bdy);
		mySimpleDialog.cfg.queueProperty("buttons", OKButtons);
		mySimpleDialog.render(document.body);
		mySimpleDialog.show();
		if (tmp[0] == 'Added') {
			selectedDates = selectedDates + ',' + tmp[1]; 
			cal1group.addRenderer(selectedDates, renderAppointmentDay);
			cal1group.render();
		}
		if (tmp[0] == 'Deleted' && tmp[3] > 0) {
			if (do_debug == true) YAHOO.log('Deleted OK, tmp[1] before: ' + tmp[1]);
			if (do_debug == true) YAHOO.log('selectedDates: ' + selectedDates);
			var x = '|,' + selectedDates + ',|';
			rep_str = ',' + tmp[1] + ',';
			st_str = '|,';
			end_str = ',|';
			if (do_debug == true) YAHOO.log('x: ' + x);
			x = x.replace(rep_str,',');
			if (do_debug == true) YAHOO.log('x: ' + x);
			x = x.replace(st_str,'');
			if (do_debug == true) YAHOO.log('x: ' + x);
			x = x.replace(end_str,'');
			if (do_debug == true) YAHOO.log('x: ' + x);
			selectedDates = x;
			if (do_debug == true) YAHOO.log('selectedDates: ' + selectedDates);
			cal1group.addRenderer(selectedDates, renderAppointmentDay);
			cal1group.render();
		}
	}
	
var onFailure = function(o) {
	alert("Your submission failed. Status: " + o.status);
		MyWaitPanel.hide()
}

	function submitCallback(obj) {
		var response = obj.responseText;
	}

	function submitFailure(obj) {
		alert("Submission failed: " + obj.status);
		MyWaitPanel.hide()
	}
	
	var onSelect = function() {
		YAHOO.log('in onSelect','trace');
		YAHOO.log(this,'trace','this');

		dt = this.getSelectedDates();
		dmy = this.deselect(dt);
		ajxf = document.getElementById('ajaxform');
		eventDate = document.getElementById('event_date');
		frm = document.forms["dlgForm"];
		eventDate.value = dt[0].defaultView();
		if (selectedDates.indexOf(dt[0].defaultView()) > -1)  {
			pass_str = 'date=' + dt[0].defaultView() + '&ajaxform=list&ajax=1';
			gc = YAHOO.util.Connect.asyncRequest('POST','new_update_events.php',get_cb,pass_str);
			ajxf.value = "Update";
			dmy = this.deselect(dt);
			MyWaitPanel.setHeader("Retrieving Events, please wait...");
			MyWaitPanel.render(document.body);
			MyWaitPanel.show();
		} else {
			var myButtons = [ { text:"Add", handler:handleSubmitAdd, isDefault:true },
				  { text:"Cancel", handler:handleCancel } ];
			myDialog.cfg.queueProperty("buttons", myButtons);
//			YAHOO.log(myDialog.cfg.getProperty('height'),'trace','myDialogHeight (before set)','onSelect');
//			myDialog.cfg.setProperty('height', ViewportHeight+"px");
//			YAHOO.log(myDialog.cfg.getProperty('height'),'trace','myDialogHeight (after set)','onSelect');
			YAHOO.log('Before myDialog.render','trace');
			myDialog.render(); 
			YAHOO.log('After myDialog.render','trace');
			frm.reset();
			ajxf.value = "Add";
			eventDate.value = dt[0].defaultView();
			if(do_debug == true) YAHOO.log('Event date = ' + eventDate.value,'trace');
			YAHOO.log('Before myDialog.show','trace');
			myDialog.show();
			YAHOO.log('After myDialog.show','trace');
			dmy = this.deselect(dt);
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
			}
		this.render();
		return(true);
	}

	function init () { 
				cal1group = new YAHOO.widget.CalendarGroup("cal1group", "cal1Container",{pages:12,HIDE_BLANK_WEEKS:true,NAV_ARROW_LEFT:'',
																			NAV_ARROW_RIGHT:''});
				cal1group.addRenderer(selectedDates, renderAppointmentDay);
				cal1group.selectEvent.subscribe(onSelect,cal1group,true);
				cal1group.setChildFunction("onChangePage", write_calyear);
				cal1group.render();
				myDialog = new YAHOO.widget.Dialog("myDialog", 
						{ modal:true, visible:false, width:"810px", 
						   fixedcenter:true, constraintoviewport:true, draggable:true, close:true });
				myDialog.cfg.setProperty('postmethod','async');
				myDialog.cfg.setProperty('height',ViewportHeight+'px');
				myDialog.callback.success = onSuccess;
				myDialog.callback.failure = onFailure;
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
				UpdateMultiple = new YAHOO.widget.Dialog("dlg1", { 
					width: "30em", 
					fixedcenter:true,
					modal:true,
					draggable:false });

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
	<hr />
	<div style="clear:both;line-height:0.01em;font-size:1%">&nbsp;</div>
	<div id="debug" style="display:block;clear:both"></div>
	<div id="win">
	  <div class="hd" style="text-align:center"></div> 
	  <div class="bd"></div> 
	  <div class="ft"></div> 
	</div>
	<div id="myPanel">
	  <div class="hd"></div> 
	  <div class="bd" id="events">
	  </div> 
	  <div class="ft"></div> 
	</div>
	<?php if ($do_debug) echo '<div id="myLogger"></div>' . "\n"; ?>
	<div id="dlg1">
		<div class="hd"></div>
		<div class="bd"></div>
		<div class="ft"></div>
	</div>
	<div id="myDialog" style="font-size:80%;display:block;overflow:scroll">
 		<div class="hd">Please Enter/Update the Event</div>
		<div class="bd">
                <form name="dlgForm" id="dlgForm" method=post action="<? echo $_SERVER['PHP_SELF'] ?>">
					 <input name="event_date" id="event_date" type="hidden">
					 <input name="event_weekday" id="event_weekday" type="hidden">
                <div class=row>
                <span class=label>Club or Organization Name:</span>
                <span class=formw><select class="textinp" name="event_org" size="1">
					 <option value="[none]" selected>Please select an Organization</option>
                <? get_club_names(''); ?>
                <option value="NNJSDA">NNJSDA</option>
                <option value="5 Clubs">5 Clubs</option>
                </select>
                </span>
                </div>
                <div class=row>
                <span class=label>Event Type:</span>
                <span class=formw><select class="textinp" name=event_type size=1>
                <? get_event_types(''); ?>
                </select>
                </span>
                </div>
                <div class=row>
                <span class=label>Event Name:</span>
                <span class=formw><input name="event_name" class="textinp" type="Text"></span>
                </div>
                <div class=row>
                <span class=label>Event Description:</span>
                <span class=formw><textarea class="textinp" rows="5" name="event_description"></textarea></span>
                </div>
                <div class=row>
                <span class=label>Event URL:</span>
                <span class=formw><input name="event_url" type="text" class="textinp"></span>
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
                <?
                        put_sd_program('', '');
                 ?>
                </span>
                </div>
                <div class=row>
                <span class=label>Round Dance Program:</span>
                <span class=formw>
                <?
                        put_rd_program('', '');
                 ?>
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
					 <input type="hidden" name="ajaxform" id="ajaxform" value="add">
					 <input type="hidden" name="ajax" id="ajax">
                </form> 
		</div>
	</div>
</body>
<script type="text/javascript">
				var ViewportHeight = YAHOO.util.Dom.getViewportHeight();
				if (do_debug == true) {
					YAHOO.widget.Logger.enableBrowserConsole();
					var myLogReader = new YAHOO.widget.LogReader();
				}
				YAHOO.log(ViewportHeight,'trace','ViewportHeight');
</script>
</html>
