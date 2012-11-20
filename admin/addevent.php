<?
session_start();
if (empty($_SESSION)) {
	header("location:isnotok.php?e=2");
	exit();
	}

if (!empty($_POST)) {
	extract ($_POST);  }

if (!empty($_GET)) {
	extract ($_POST);  }

if (!isset($submit)) $submit = "";
if ($submit == "Return to the Admin Page") {
	header("location:newadmin.php?todo=updevent&action=showadd");
	exit();
}


include ("dbconfig.php");
$connect = mysql_connect($dbhost, $dbuser, $dbpass);
mysql_select_db($dbname);

function get_club_names()
{
	$query = "Select club from clubs order by club";
	$result = mysql_query($query);
	while ($row = mysql_fetch_object($result))
		echo "<option value=\"$row->club\">". stripslashes($row->club) ."</option>\n";
}

function get_event_types()
{
	$query = "Select event_type from eventtypes order by event_type";
	$result = mysql_query($query);
	while ($row = mysql_fetch_object($result))
		echo "<option value=\"$row->event_type\">$row->event_type</option>\n";
}

function put_months()
{
	for ($i=1;$i<13;$i++){
		$mon = date("F",strtotime("$i/1/2003"));
		$selected = (IsSet($_GET['input_date'])) ? is_selected($mon,$_GET['input_date'],"F") : "";
		echo "<option value=$mon $selected>$mon</option>\n";}
}

function put_day()
{
	$days = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
	for ($i=0;$i<7;$i++)
	{
		$checked = (IsSet($_GET['input_date'])) ? is_selected($days[$i],$_GET['input_date'],"l",false) : "";
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

function add_event($ed, $eo, $et, $en, $edesc, $etimes,
								$event_location,
								$event_address,
								$event_city,
								$event_state,
								$event_zip,
								$esd, $erd, $caller, $cuer,
							  $contact_name,
							  $contact_email,
							  $contact_phone)
{
	$event_date = date("Y-m-d",$ed);
	$event_start_time = date("H:i",$etimes[0]);
	$event_stop_time = date("H:i",$etimes[1]);
	$rdp = "";
	$sdp = "";
	$cn = serialize($contact_name);
	$ce = serialize($contact_email);
	$cp = serialize($contact_phone);
	for($i=0;$i<count($erd);$i++)
		$rdp .= $erd[$i] . " ";
	for($i=0;$i<count($esd);$i++)
		$sdp .= $esd[$i] . " ";

	$query = "insert events (event_date, 
							  event_start_time, 
							  event_stop_time,
							  event_name,
							  event_description,
							  event_org,
							  event_location,
							  event_address,
							  event_city,
							  event_state,
							  event_zip,
							  event_sd_program,
							  event_rd_program,
							  event_type,
							  event_caller,
							  event_cuer,
							  event_contact_name,
							  event_contact_email,
							  event_contact_phone) values 
							  ('$event_date',
							   '$event_start_time',
							   '$event_stop_time',
							   '$en','$edesc','$eo',
							   '$event_location',
							   '$event_address',
							   '$event_city',
							   '$event_state',
							   '$event_zip',
							   '$sdp','$rdp', '$et','$caller','$cuer',
								'$cn','$ce','$cp')";
	$result = mysql_query($query) or die(mysql_error());
	echo "<h2>Added the event:</h2>\n";
	put_results("Organization",$eo);
	put_results("Date",$event_date);
	put_results("Event",$en);
	put_results("Type",$et);
	put_results("Start time",date("g:i a",$etimes[0]));
	put_results("End time",date("g:i a",$etimes[1]));
	put_results("Description",$edesc);
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
							  $contact_phone)
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
							  $contact_phone);

		$mnth = date("F Y",strtotime("32 $mnth"));
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
 mail("kenrbnsn@kis-hosting.com","NNJSDA AddEvent Page Visited",$body,"From: Visit Tracker <nobody@nnjsda.org>");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">


<html>
<head>
	<title>NNJSDA -- Add Event</title>
	<link href="../nnjsda.css" type="text/css" rel="STYLESHEET">
</head>

<body>
<div id=hdr>
<h1>Add Event</h1>
</div>
<div id=rest-of-page>
<?
if ($submit == "Add Event"){
	$event_date = strtotime("$event_month $event_day, $event_year");
	$etimes[0] = strtotime("$starthour:$startmin $startampm");
	$etimes[1] = strtotime("$endhour:$endmin $endampm");
	for($i=0;$i<count($frequency);$i++)
	{
		switch ($frequency[$i]) {
		case "0":
			add_event($event_date,
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
					  $contact_phone								
);
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
							  $contact_phone								
);
			break;
		default:
		}
	}
	echo "<a class=abox2 href={$_SERVER['PHP_SELF']}>Add Another Event</a>\n";
	echo "<a class=abox2 href=newadmin.php>Do Other Admin Functions</a>\n";
} else { ?>
<div class=tryit>
<form method=post action="<? echo $_SERVER['PHP_SELF'] ?>">
<div class=row>
<span class=label>Date:</span>
<span class=formw>
<select name=event_month size=1>
<option value=""></option>
<? put_months(); ?>
</select>&nbsp;
<select name=event_day size=1>
<option value=""></option>
<? for ($i=1;$i<32;$i++) {
	$selected = (IsSet($_GET['input_date'])) ? is_selected($i,$_GET['input_date'],"j") : "";
	echo "<option value=$i ".$selected.">$i</option>\n"; }?>
</select>&nbsp;
<select name=event_year size=1>
<option value=""></option>
<?
	$cur_year = date("Y",strtotime("now"));
	for ($i=$cur_year;$i<$cur_year+10;$i++){
		$selected = (IsSet($_GET['input_date'])) ? is_selected($i,$_GET['input_date'],"Y") : "";
		echo "<option value=$i ".$selected.">$i</option>\n"; }?>
</select>
</span>
</div>
<div class=row>
<span class=label>Club or Organization Name:</span>
<span class=formw><select name="cluborg" size="1">
<? get_club_names(); ?>
<option value="NNJSDA">NNJSDA</option>
<option value="5 Clubs">5 Clubs</option>
</select>
</span>
</div>
<div class=row>
<span class=label>Event Type:</span>
<span class=formw><select name=eventtype size=1>
<? get_event_types(); ?>
</select>
</span>
</div>
<div class=row>
<span class=label>Event Name:</span>
<span class=formw><input name=eventname size=50 type="Text"></span>
</div>
<div class=row>
<span class=label>Event Description:</span>
<span class=formw><textarea cols="50" rows="10" name="event_description" wrap="soft"></textarea></span>
</div>
<div class=row>
<span class=label>Frequency:</span>
<span class=formw><input type="Checkbox" name=frequency[] <? echo ((IsSet($_GET['input_date'])) ? " checked " : ""); ?>value="0">One Time<br>
				  <input type="Checkbox" name=frequency[] value="1st">Every 1st<br>
				  <input type="Checkbox" name=frequency[] value="2nd">Every 2nd<br>
				  <input type="Checkbox" name=frequency[] value="3rd">Every 3rd<br>
				  <input type="Checkbox" name=frequency[] value="4th">Every 4th<br>
				  <input type="Checkbox" name=frequency[] value="5th">Every 5th
				  </span>
</div>
<div class=row>
<span class=label>Day:</span>
<span class=formw><? put_day(); ?></span>
</div>
<div class=row>
<span class=label>Start Time:</span>
<span class=formw><select name="starthour" size="1">
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	<option value="8" selected>8</option>
	<option value="9">9</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
</select>:<select name="startmin" size="1">
	<option value="00" selected>00</option>
	<option value="30">30</option>
</select>&nbsp;<select name="startampm" size="1">
	<option value="am">am</option>
	<option value="pm" selected>pm</option>
</select></span>
</div>
<div class=row>
<span class=label>End Time:</span>
<span class=formw><select name="endhour" size="1">
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	<option value="8">8</option>
	<option value="9">9</option>
	<option value="10" selected>10</option>
	<option value="11">11</option>
	<option value="12">12</option>
</select>:<select name="endmin" size="1">
	<option value="00">00</option>
	<option value="30" selected>30</option>
</select>&nbsp;<select name="endampm" size="1">
	<option value="am">am</option>
	<option value="pm" selected>pm</option>
</select></span>
</div>
<div class=row>
<span class=label>Location:</span>
<span class=formw><input name=event_location size=50 type="Text"></span>
</div>
<div class=row>
<span class=label>Address:</span>
<span class=formw><input name=event_address size=50 type="Text"></span>
</div>
<div class=row>
<span class=label>City:</span>
<span class=formw><input name=event_city size=50 type="Text"></span>
</div>
<div class=row>
<span class=label>State:</span>
<span class=formw><input name=event_state size=50 type="Text"></span>
</div>
<div class=row>
<span class=label>Zip:</span>
<span class=formw><input name=event_zip size=50 type="Text"></span>
</div>
<div class=row>
<span class=label>Square Dance Program:</span>
<span class=formw>
<input type="checkbox" name="sdprogram[]" value="ms">&nbsp;Mainstream<br>
<input type=checkbox value=plus name=sdprogram[]>&nbsp;Plus<br>
<input type=checkbox value=a1 name=sdprogram[]>&nbsp;A1<br>
<input type=checkbox value=a2 name=sdprogram[]>&nbsp;A2<br>
<input type=checkbox value=c1 name=sdprogram[]>&nbsp;C1<br>
<input type=checkbox value=c2 name=sdprogram[]>&nbsp;C2<br>
</span>
</div>
<div class=row>
<span class=label>Round Dance Program:</span>
<span class=formw>
<input type=checkbox value=I name=rdprogram[]>&nbsp;Phase 1<br>
<input type=checkbox value=II name=rdprogram[]>&nbsp;Phase 2<br>
<input type=checkbox value=III name=rdprogram[]>&nbsp;Phase 3<br>
<input type=checkbox value=IV name=rdprogram[]>&nbsp;Phase 4<br>
<input type=checkbox value=V name=rdprogram[]>&nbsp;Phase 5<br>
<input type=checkbox value=VI name=rdprogram[]>&nbsp;Phase 6<br>
</span>
</div>
<div class=row>
<span class=label>Caller(s):</span>
<span class=formw><input name=caller size=50 type=text></span>
</div>
<div class=row>
<span class=label>Cuer(s):</span>
<span class=formw><input name=cuer size=50 type=text></span>
</div>
<div class=row>
<span class=label>Club Contacts:</span>
<span class=thirda><span class=boldcu>Name:</span><br>
<? $ts = 25;
 for ($i=0;$i<3;$i++) { ?>
<input type=text size=<? echo $ts ?> name=contact_name[]><br>
<? } ?>
</span>
<span class=thirda><span class=boldcu>Email:</span><br>
<? for ($i=0;$i<3;$i++) { ?>
<input type=text size=<? echo $ts ?> name=contact_email[]><br>
<? } ?>
</span>
<span class=thirda><span class=boldcu>Phone:</span><br>
<? for ($i=0;$i<3;$i++) { ?>
<input type=text size=<? echo $ts ?> name=contact_phone[]><br>
<? } ?>
</span>
</div>
<div class=row>
<span class=fullwidth><input type=submit name="submit" value="Add Event">&nbsp;<input type=submit name="submit" value="Return to the Admin Page"></span>
</div>
</form>
<? } ?>
</div>
</div>

</body>
</html>
