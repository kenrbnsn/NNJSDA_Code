<?
session_start();
if (empty($_SESSION)) {
	header("location:isnotok.php?e=2");
	exit();
	}

if (!empty($_POST)) {
	extract ($_POST);  }

if (!empty($_GET)) {
	extract ($_GET);  }

if (!isset($submit)) $submit = "";
$return_action = ($action == "delete")?"del":"edi";
if ($submit == "Return to the Admin Page") {
	header("location:newadmin.php?todo=updevent&action=show$return_action");
	exit();
}
include ("dbconfig.php");
$connect = mysql_connect($dbhost, $dbuser, $dbpass);
mysql_select_db($dbname);

$query = "Select * from events where ind='$id'";
$result = mysql_query($query);
$event = mysql_fetch_object($result);

$query = "Select * from clubs where club = '$event->event_org'";
$result = mysql_query($query);
$club_info = mysql_fetch_object($result);


function get_club_names($eo)
{
	$query = "Select club from clubs order by club";
	$result = mysql_query($query);
	while ($row = mysql_fetch_object($result)) {
		$selected = ($eo == $row->club)?"selected":"";
		echo "<option value=\"$row->club\" $selected>". stripslashes($row->club)."</option>\n";}
}

function get_event_types($et)
{
	$query = "Select event_type from eventtypes order by event_type";
	$result = mysql_query($query);
	while ($row = mysql_fetch_object($result)){
		$selected = ($et == $row->event_type)?"selected":"";
		echo "<option value=\"$row->event_type\" $selected>$row->event_type</option>\n";}
}

function put_months($dt)
{
	$rm = date("F",strtotime($dt));
	for ($i=1;$i<13;$i++){
		$mon = date("F",strtotime("$i/1/2003"));
		$selected = ($rm == $mon)?"selected" : "";
		echo "<option value=$mon $selected>$mon</option>\n";}
}

function put_day($ed)
{
	$eday = date("l",strtotime($ed));
	$days = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
	for ($i=0;$i<7;$i++)
	{
		$checked = ($eday == $days[$i]) ? "checked": "";
		echo "<input type=Radio name=day value={$days[$i]} $checked>{$days[$i]}<br>\n";
	}
}

function is_selected($t,$i,$w,$o=true)
{
	$sel = ($o) ? "selected" : "checked";
	if ($t == date($w,strtotime($i))) return($sel);
	else return("");
}

function put_results($l,$c)
{
	echo "<div class=row>\n";
	echo "<span class=label>$l</span>\n";
	echo "<span class=formw>$c</span>\n";
	echo "</div>\n";
}

function put_results_ar($l,$a1,$a2,$a3)
{
	$ar1 = unserialize($a1);
	$ar2 = unserialize($a2);
	$ar3 = unserialize($a3);
	echo "<div class=row>\n";
	echo "<span class=label>$l</span>\n";
	echo "<span class=thirda><span class=boldcu>Name:</span><br>\n";
	for ($i=0;$i<3;$i++)
		echo "$ar1[$i]<br>\n";
	echo "</span>\n";
	echo "<span class=thirda><span class=boldcu>Email:</span><br>\n";
	for ($i=0;$i<3;$i++)
		echo "$ar2[$i]<br>\n";
	echo "</span>\n";
	echo "<span class=thirda><span class=boldcu>Phone:</span><br>\n";
	for ($i=0;$i<3;$i++)
		echo "$ar3[$i]<br>\n";
	echo "</span>\n";
	echo "</div>\n";
}

function update_event($ed, $eo, $et, $en, $edesc, $etimes,
								$event_location,
								$event_address,
								$event_city,
								$event_state,
								$event_zip,
								$esd, $erd, $caller, $cuer,
					  $contact_name,
					  $contact_email,
					  $contact_phone, $event_url,						
								$id)
{
	$event_date = date("Y-m-d",$ed);
	$event_start_time = date("H:i",$etimes[0]);
	$event_stop_time = date("H:i",$etimes[1]);
	$rdp = "";
	$sdp = "";
	for($i=0;$i<count($erd);$i++)
		$rdp .= $erd[$i] . " ";
	for($i=0;$i<count($esd);$i++)
		$sdp .= $esd[$i] . " ";
	$cn = serialize($contact_name);
	$ce = serialize($contact_email);
	$cp = serialize($contact_phone);
	$query = "update events set event_date='$event_date', 
							  event_start_time='$event_start_time', 
							  event_stop_time='$event_stop_time',
							  event_name='$en',
							  event_description='$edesc',
							  event_org='$eo',
							  event_location='$event_location',
							  event_address='$event_address',
							  event_city='$event_city',
							  event_state='$event_state',
							  event_zip='$event_zip',
							  event_sd_program='$sdp',
							  event_rd_program='$rdp',
							  event_type='$et',
							  event_caller='$caller',
							  event_cuer='$cuer',
							  event_contact_name='$cn',
							  event_contact_email='$ce',
							  event_contact_phone='$cp',
							  event_url='$event_url'
							   where ind='$id'";
	$result = mysql_query($query) or die(mysql_error());
	echo "<h2>Updated the event:</h2>\n";
	put_results("Organization",$eo);
	put_results("Date",$event_date);
	put_results("Event",$en);
	put_results("Type",$et);
	put_results("Start time",date("g:i a",$etimes[0]));
	put_results("End time",date("g:i a",$etimes[1]));
	put_results("Description",stripslashes($edesc));
	put_results("Caller(s)",$caller);
	put_results("Cuer(s)",$cuer);
	put_results_ar("Event Contact(s)",$cn,$ce,$cp);
	echo "<hr>\n";
} 

function add_repeating_days($str, $eo, $et, $en, $edesc, $etimes,
 								$event_location,
								$event_address,
								$event_city,
								$event_state,
								$event_zip,
								$esd, $erd, $caller, $cuer,
								$contact_name,
					  $contact_email,
					  $contact_phone, $event_url)
{
	$this_year = date("Y",strtotime("today"));
	echo "<br>Every $str<br>\n";
	$mnth = date("F Y",strtotime("Sept 1, $this_year"));
	for ($i=0;$i<10;$i++)
		{
		$event_date = strtotime($str,strtotime("1 $mnth"));
		add_event($event_date,
				  $eo,
				  $et,
				  $en,
				  $edesc,
				  $etimes,
				  $event_location,
				  $event_address,
				  $event_city,
				  $event_state,
				  $event_zip,
				  $esd,
				  $erd,
				  $caller,
				  $cuer,
					  $contact_name,
					  $contact_email,
					  $contact_phone, $event_url);

		$mnth = date("F Y",strtotime("32 $mnth"));
		}
}

function eitheror($e, $o)
{
	if (($e == "") && ($o == "")) return($e);
	if (($e == "") && ($o != "")) return($o);
	return(($e != $o)?$e:$o);
}

function put_sd_program($esd,$csd)
{
	$sdp = array(' ms ',' plus ',' a1 ',' a2 ',' c1 ',' c2 ');
	
	for ($i=0;$i<count($sdp);$i++) {
		$checked1 = (!(strpos(" $esd ",$sdp[$i]) === false));
		$checked2 = (!(strpos(" $csd ",$sdp[$i]) === false));
		$checked = ($checked1 || $checked2)?"checked":"";
		$disp = ($i == 0)?"Mainstream":ucwords($sdp[$i]);
		echo "<input type=checkbox name=sdprogram[] value={$sdp[$i]} $checked>&nbsp;$disp<br>\n";
	}
}

function put_rd_program($erd,$crd)
{
	$rdp = array(' I ',' II ',' III ',' IV ',' V ',' VI ');
	
	for ($i=0;$i<count($rdp);$i++) {
		$checked1 = (!(strpos(" $erd ",$rdp[$i]) === false));
		$checked2 = (!(strpos(" $crd ",$rdp[$i]) === false));
		$checked = ($checked1 || $checked2)?"checked":"";
		$d = $i + 1;
		echo "<input type=checkbox name=rdprogram[] value={$rdp[$i]} $checked>&nbsp;Phase $d<br>\n";
	}
}

if ($_SERVER['SERVER_NAME'] != "localhost")
{
    if ($_SERVER["HTTP_X_FORWARDED_FOR"] != ""){
        $IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
        $proxy = $_SERVER["REMOTE_ADDR"];
        $host = @gethostbyaddr($_SERVER["HTTP_X_FORWARDED_FOR"]);
    }else{
        $IP = $_SERVER["REMOTE_ADDR"];
        $host = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
    }
 $body = "Referer: " . $_SERVER['HTTP_REFERER'] . "\n";
 $body .= "Remote Address:" . $host . "\n";
 $body .= "Http_user_agent: $HTTP_USER_AGENT\n";
 $body .= "Logged In User: $logged_in_username\n";
 $body .= "Action: $action\n";
 mail("kenrbnsn@kis-hosting.com","NNJSDA modievent Page Visited",$body,"From: Visit Tracker <nobody@nnjsda.org>");
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">


<html>
<head>
	<title>NNJSDA -- <? echo ucwords($action); ?> Event</title>
	<link href="../nnjsda.css" type="text/css" rel="STYLESHEET">
</head>

<body>
<div id=hdr>
<h1><? echo ucwords($action); ?> Event</h1>
</div>
<div id=rest-of-page>
<?
if ($submit == "Update Event"){
	$event_date = strtotime("$event_month $event_day, $event_year");
	$etimes[0] = strtotime("$starthour:$startmin $startampm");
	$etimes[1] = strtotime("$endhour:$endmin $endampm");
	for($i=0;$i<count($frequency);$i++)
	{
		switch ($frequency[$i]) {
		case "0":
			update_event($event_date,
					  $cluborg,
					  $eventtype,
					  $eventname,
					  $event_description,
					  $etimes,
					  $event_location,
					  $event_address,
					  $event_city,
					  $event_state,
					  $event_zip,
					  $sdprogram,
					  $rdprogram,
					  $caller,
					  $cuer,
					  $contact_name,
					  $contact_email,
					  $contact_phone, $event_url,
					  $id);
			break;
		case "1st":
		case "2nd":
		case "3rd":
		case "4th":
		case "5th":
			$etimes[0] = strtotime("$starthour:$startmin $startampm");
			$etimes[1] = strtotime("$endhour:$endmin $endampm");
			add_repeating_days("$frequency[$i] $day",
								$cluborg,
							    $eventtype,
							    $eventname,
							    $event_description,
							    $etimes,
								$event_location,
								$event_address,
								$event_city,
								$event_state,
								$event_zip,
							    $sdprogram,
							    $rdprogram,
								$caller,
								$cuer,
							  $contact_name,
							  $contact_email,
							  $contact_phone, $event_url							
								);
			break;
		default:
		}
	}
	echo "<a class=abox2 href=newadmin.php>Do Other Admin Functions</a>\n";
} else 
if ($submit == "Delete Event"){
$query = "delete from events where ind='$id'";
$result = mysql_query($query);
if (!$result) echo "<p>Could not delete event (id = $id)\n".mysql_error()."</p>\n";
else echo "<p>Event ($id) deleted successfully</p>\n";
	echo "<a class=abox2 href=newadmin.php>Do Other Admin Functions</a>\n";
} else {?>
<form method=post action="<? echo $_SERVER['PHP_SELF'] ?>">
<input type=hidden name=id value=<? echo $id; ?>>
<input type=hidden name=action value=<? echo $action; ?>>
<div class=row>
<span class=label>Date:</span>
<span class=formw>
<select name=event_month size=1>
<option value=""></option>
<? put_months($event->event_date); ?>
</select>&nbsp;
<select name=event_day size=1>
<option value=""></option>
<? 
	$ed = date("j",strtotime($event->event_date));
	for ($i=1;$i<32;$i++) {
	$selected = ($i == $ed) ? "selected" : "";
	echo "<option value=$i ".$selected.">$i</option>\n"; }?>
</select>&nbsp;
<select name=event_year size=1>
<option value=""></option>
<?
	$ey = date("Y",strtotime($event->event_date));
	$cur_year = date("Y",strtotime("now"));
	for ($i=$cur_year;$i<$cur_year+10;$i++){
		$selected = ($i == $ey) ? "selected" : "";
		echo "<option value=$i ".$selected.">$i</option>\n"; }?>
</select>
</span>
</div>
<div class=row>
<span class=label>Club or Organization Name:</span>
<span class=formw><select name="cluborg" size="1">
<? get_club_names($event->event_org); ?>
<option value="NNJSDA" <? $s = ($event->event_org == "NNJSDA")?"selected":""; echo $s ?>>NNJSDA</option>
<option value="5 Clubs" <? $s = ($event->event_org == "5 Clubs")?"selected":""; echo $s ?>>5 Clubs</option>
</select>
</span>
</div>
<div class=row>
<span class=label>Event Type:</span>
<span class=formw><select name=eventtype size=1>
<? get_event_types($event->event_type); ?>
</select>
</span>
</div>
<div class=row>
<span class=label>Event Name:</span>
<span class=formw><input name=eventname size=50 type="Text" value="<? echo $event->event_name; ?>"></span>
</div>
<div class=row>
<span class=label>Event Description:</span>
<span class=formw><textarea cols="50" rows="10" name="event_description" wrap="soft"><? echo $event->event_description; ?></textarea></span>
</div>
<input type=hidden name=frequency[] value=0>
<div class=row>
<span class=label>Event URL:</span>
<span class=formw><input name="event_url" type="text" size=50 value="<? echo $event->event_url; ?>"</span>
</div>
<div class=row>
<span class=label>Day:</span>
<span class=formw><? put_day($event->event_date); ?></span>
</div>
<div class=row>
<span class=label>Start Time:</span>
<span class=formw><select name="starthour" size="1">
<? 
	$eh = date("g",strtotime($event->event_start_time));
	for ($h=1;$h<13;$h++) {
		$sel = ($eh == $h)?" selected":"";
		echo "<option value=\"$h\"$sel>$h</option>";
	}
?>
</select>:<select name="startmin" size="1">
<?
	$sela = (date("i",strtotime($event->event_start_time)) == "00")?"selected":"";
	$selp = (date("i",strtotime($event->event_start_time)) == "30")?"selected":"";
 ?>
	<option value="00" <? echo $sela; ?>>00</option>
	<option value="30" <? echo $selp; ?>>30</option>
</select>&nbsp;<select name="startampm" size="1">
<?
	$sela = (date("a",strtotime($event->event_start_time)) == "am")?"selected":"";
	$selp = (date("a",strtotime($event->event_start_time)) == "pm")?"selected":"";
 ?>
	<option value="am" <? echo $sela; ?>>am</option>
	<option value="pm" <? echo $selp; ?>>pm</option>
</select></span>
</div>
<div class=row>
<span class=label>End Time:</span>
<span class=formw><select name="endhour" size="1">
<? 
	$eh = date("g",strtotime($event->event_stop_time));
	for ($h=1;$h<13;$h++) {
		$sel = ($eh == $h)?" selected":"";
		echo "<option value=\"$h\"$sel>$h</option>";
	}
?>
</select>:<select name="endmin" size="1">
<?
	$sela = (date("i",strtotime($event->event_stop_time)) == "00")?"selected":"";
	$selp = (date("i",strtotime($event->event_stop_time)) == "30")?"selected":"";
 ?>
	<option value="00" <? echo $sela; ?>>00</option>
	<option value="30" <? echo $selp; ?>>30</option>
</select>&nbsp;<select name="endampm" size="1">
<?
	$sela = (date("a",strtotime($event->event_stop_time)) == "am")?"selected":"";
	$selp = (date("a",strtotime($event->event_stop_time)) == "pm")?"selected":"";
 ?>
	<option value="am" <? echo $sela; ?>>am</option>
	<option value="pm" <? echo $selp; ?>>pm</option>
</select></span>
</div>
<div class=row>
<span class=label>Location:</span>
<span class=formw><input name=event_location size=50 type="Text" value="<? echo  eitheror($event->event_location,$club_info->location); ?>"></span>
</div>
<div class=row>
<span class=label>Address:</span>
<span class=formw><input name=event_address size=50 type="Text" value="<? echo  eitheror($event->event_address,$club_info->address); ?>"></span>
</div>
<div class=row>
<span class=label>City:</span>
<span class=formw><input name=event_city size=50 type="Text" value="<? echo  eitheror($event->event_city,$club_info->city); ?>"></span>
</div>
<div class=row>
<span class=label>State:</span>
<span class=formw><input name=event_state size=50 type="Text" value="<? echo  eitheror($event->event_state,$club_info->state); ?>"></span>
</div>
<div class=row>
<span class=label>Zip:</span>
<span class=formw><input name=event_zip size=50 type="Text" value="<? echo  eitheror($event->event_zip,$club_info->zip); ?>"></span>
</div>
<div class=row>
<span class=label>Square Dance Program:</span>
<span class=formw>
<?
	put_sd_program(strtolower($event->event_sd_program), strtolower($club_info->sd_program));
 ?>
</span>
</div>
<div class=row>
<span class=label>Round Dance Program:</span>
<span class=formw>
<?
	put_rd_program(strtoupper($event->event_rd_program), strtoupper($club_info->rd_program));
 ?>
</span>
</div>
<div class=row>
<span class=label>Caller(s):</span>
<span class=formw><input name=caller size=50 type=text value="<? echo  eitheror($event->event_caller,$club_info->caller); ?>"></span>
</div>
<div class=row>
<span class=label>Cuer(s):</span>
<span class=formw><input name=cuer size=50 type=text value="<? echo  eitheror($event->event_cuer,$club_info->cuer); ?>"></span>
</div>
<div class=row>
<span class=label>Club Contacts:</span>
<span class=thirda><span class=boldcu>Name:</span><br>
<? $ts = 25;
$cn=($club_info->contact_name == "")?array("","",""):unserialize($club_info->contact_name);
$ce=($club_info->contact_email == "")?array("","",""):unserialize($club_info->contact_email);
$cp=($club_info->contact_phone == "")?array("","",""):unserialize($club_info->contact_phone);
$ecn=($event->event_contact_name == "")?array("","",""):unserialize($event->event_contact_name);
$ece=($event->event_contact_email == "")?array("","",""):unserialize($event->event_contact_email);
$ecp=($event->event_contact_phone == "")?array("","",""):unserialize($event->event_contact_phone);
 for ($i=0;$i<3;$i++) { ?>
<input type=text size=<? echo $ts ?> name=contact_name[] value='<? echo eitheror($ecn[$i],$cn[$i]) ?>'><br>
<? } ?>
</span>
<span class=thirda><span class=boldcu>Email:</span><br>
<? for ($i=0;$i<3;$i++) { ?>
<input type=text size=<? echo $ts ?> name=contact_email[] value='<? echo eitheror($ece[$i],$ce[$i]) ?>'><br>
<? } ?>
</span>
<span class=thirda><span class=boldcu>Phone:</span><br>
<? for ($i=0;$i<3;$i++) { ?>
<input type=text size=<? echo $ts ?> name=contact_phone[] value='<? echo eitheror($ecp[$i],$cp[$i]) ?>'><br>
<? } ?>
</span>
</div>
<div class=row>
<span class=fullwidth><input type=submit name="submit" value="<? echo ucwords($action); ?> Event">&nbsp;<input type=submit name="submit" value="Return to the Admin Page"></span>
</div>
</form>
<? } ?>
</div>

</body>
</html>
