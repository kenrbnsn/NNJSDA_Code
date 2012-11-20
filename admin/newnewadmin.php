<?
session_start();
if (empty($_SESSION)) {
	header("location:isnotok.php?e=2");
	exit();
	}
if (!empty($_GET)) {
extract($_GET);
} else if (!empty($HTTP_GET_VARS)) {
extract($HTTP_GET_VARS);
}
if (!empty($_POST)) {
extract($_POST);
} else if (!empty($HTTP_POST_VARS)) {
extract($HTTP_POST_VARS);
}
if (!IsSet($action)) $action = "";

$stripped_club = (IsSet($cluborg))?stripslashes($cluborg):"";
$added_club = (IsSet($cluborg))?addslashes($cluborg):"";

extract ($_SERVER);
if (!empty($_SESSION)) extract($_SESSION);

include('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname); 

$headers = array('chgpwd' => "Change Password",
				 'changepwd' => "Changing Password",
				 'updcontacts' => "Update Contact",
				 'addcontacts' => "Add Contact",
				 'dispcontacts' => "Display Contact",
				 'updevent' => "Events",
				 'dispevent' => "Events",
				 'adduser' => "Add New User",
				 'updclub' => "Update Club",
				 'check_updclub' => "Update Club",
				 'dispclub' => "Show Club",
				 'classes' => "Classes",
				 'logout' => "Logout");
				 
$uc_submit = array('Update Information' => 'Update Club Information',
				   'Delete Club' => 'OK To Delete Club',
				   'Add Club' => 'Add New Club');

if (IsSet($todo) && ($todo == "logout")) {
	session_destroy();
	header("Location: index.php");
	exit();
	}
	
$menuar = array('user','contacts','events','club','classes','logout');

$curr_month = date("F Y",strtotime("today"));
$past_month = date("F Y",strtotime("0 $curr_month"));
$next_month = date("F Y",strtotime("32 $curr_month")); 

function disp_menu($ar)
{
for ($i=0;$i<count($ar);$i++)
	{
	echo "<a class=abox2 href=".$_SERVER['PHP_SELF']."?todo=$ar[$i]>".ucwords($ar[$i])."</a>\n";
	}
}

function get_club_names()
{
	global $cluborg;
	$query = "Select club from clubs order by club";
	$result = mysql_query($query);
	while ($row = mysql_fetch_object($result)) {
		$sel = ($row->club == $cluborg)?"selected":"";
		echo "<option value=\"".stripslashes($row->club)."\" $sel>".stripslashes($row->club)."</option>\n"; }
}

function put_day($ed="September 7, 2003")
{
	$eday = date("l",strtotime($ed));
	$days = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
	for ($i=0;$i<7;$i++)
	{
		$checked = ($eday == $days[$i]) ? "checked": "";
		echo "<input type=Radio name=day value={$days[$i]} $checked>{$days[$i]}<br>\n";
	}
}

function check_old_pwd($u, $p)
{
$cp = crypt($p,'$1$somesalt');
$query = "Select * from validusers WHERE username = '$u'";
$result = mysql_query($query);
$row = mysql_fetch_object($result);
if ($row->pwd == $cp) return true;
else return false;
}

function change_password($u, $op, $np)
{
$cp = crypt($op,'$1$somesalt');
$query = "Select * from validusers WHERE username = '$u' AND pwd = '$cp'";
$result = mysql_query($query);
if (mysql_num_rows($result) == 1){
	$row = mysql_fetch_object($result);
	$ncp = crypt($np,'$1$somesalt');
	$query = "Update validUsers set pwd='$ncp' where ind='$row->ind'";
	$result = mysql_query($query);
 	return true; }
else return false;
}

	function display_events($action)
	{
	$query = "Select * from events";
	$result = mysql_query ($query);
	$i = 0;
	while ($row = mysql_fetch_object($result))
	{
	echo "<div class=rownc><div class=halfwidth>\n";
	if ($action == "del") {
		echo "<span class=small><input type=\"Checkbox\" name=delete[] value=yes$i>&nbsp;</span>\n";
		}
	echo "<span class=formw>\n";
	echo "<span class=boldfl>Name:</span> <input type=text size=35 maxlength=60 name=event_name[]";
	if ($action != "add") echo " value=\"" . $row->event . "\"";
	echo ">&nbsp;<br>\n";
	echo "<span class=boldfl>Date:</span> <input type=text size=10 name=event_date[]";
	if ($action != "add") echo " value=\"" . $row->date . "\"";
	echo "><br>\n";
	echo "<span class=boldfl>Time:</span> <input type=text size=10 name=start_time[]";
	if ($action != "add") echo " value=\"" . $row->start_time . "\"";
	echo "></span></div>\n";
	echo "<div class=halfwidth><span class=formw><textarea cols=35 rows=7 name=description[] wrap=\"soft\">";
	if ($action != "add") echo $row->description;
	echo "</textarea></span></div>\n";
		

	echo "</div>\n";
	$i++;
	if ($action != "add") echo "<input type=hidden name=id[] value=".$row->ind.">\n";
	}
	echo "<div class=rownc>\n";
	echo "<span class=fullwidth>\n";
	echo "<input type=submit name=submit value=\"Submit Event Changes\"</span></div>\n";

	return($i);
	}
	
function view_events($when)
{
	if ($when == "") return;
	$query = "Select * from events where (to_days(date) - to_days(now()) >= 0) order by date";
	if ($when == "past") $query = "Select * from events where (to_days(date) - to_days(now()) < 0) order by date";
	$result = @mysql_query ($query);
	$max_row = 0;
	$max_id = 0;
	while ($row = mysql_fetch_object($result))
	{
		echo "<h2>".ucwords($row->event) . "</h2>";
		echo "<hr>\n";
		echo "<div class=rownc><span class=label2>Date:</span>\n";
		echo "<span class=formw2>".date("l, F jS, Y",strtotime($row->date))."</span></div>\n";
		echo "<div class=rownc><span class=label2>Time:</span>\n";
		echo "<span class=formw2>".date("g:i a",strtotime($row->date." ".$row->start_time."00"))."</span></div>\n";
		echo "<div class=rownc>\n";
		echo "<span class=label2>&nbsp;</span>\n";
		echo "<span class=formw2>".$row->description."</span></div>\n";
		echo "<div class=rownc><span class=fullwidth><hr></span></div>\n";
	}
}

function disp_contact_boxes($t)
{
	$query = "Select position, name from contacts order by listorder";
	$result = mysql_query($query);
	while ($row = mysql_fetch_object($result))
	{
		echo "<a class=abox1a
		 href={$_SERVER['PHP_SELF']}?todo=$t&amp;position=".urlencode($row->position)."&amp;name=".urlencode($row->name).">";
		echo ucwords($row->position)."</a>\n";
	}
}

function display_month($d,$a,$ac="")
{
	$timestamp = strtotime("1 $d");
	$today = date('j M Y',strtotime("today"));;
	$start_day = date('w',$timestamp);
	$days = date('t',$timestamp);
	if ($a && (($ac == "showdel") || ($ac == "showedi"))) $a = false;
	$last_timestamp = strtotime("$days $d");
	for ($i=0;$i<($days+$start_day+1);$i++){
			$j = $i - $start_day;
			if (($i <= $start_day) && ($i > 0)) echo "<span class=largenoday>00</span>\n";
			else { if ($j > 0) {
			$cur = "";
			$cd = "$j $d";
			$notthere = "";
			$sta = $a ? "<a class=eventmonth href='addevent.php?input_date=$j $d'>" : "";
			$enda = $a ? "</a>":"";
			if ($j < 10) $notthere = "<span class=notthere>0</span>";
			if (date('j M Y',strtotime($cd)) == $today) $cur="curr";
			echo "<span class=large{$cur}day>$notthere$sta$j$enda\n";
			check_for_events(date("Y-m-d",strtotime($cd)),$ac);
			echo "</span>\n";
			if ($i%7 == 0) echo "\n";}}
	}
	echo "</span>\n";
}

function check_for_events($d,$ac="")
{
	$query = "Select * from events where event_date = '$d' order by event_start_time";
	$result = mysql_query($query) or die(mysql_error());
	$sta = "";
	$stopa = "";
	while ($row = mysql_fetch_object($result))
	{
		if ($ac == "showdel") {
			$sta = "<a class=event href=modievent.php?action=delete&amp;id=$row->ind>";
			$stopa = "</a>"; }
		if ($ac == "showedi") {
			$sta = "<a class=event href=modievent.php?action=update&amp;id=$row->ind>";
			$stopa = "</a>"; }
		echo "<span class=eventlist>$sta$row->event_org$stopa<br></span>\n";
	}
}

function check_for_position($p, $n)
{
	$query = "Select * from contacts WHERE position = '$p' and name='$n'";
	$result = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($result) == 0) return true;
	else return false;
}

function put_months()
{
	for ($i=1;$i<13;$i++){
		$mon = date("F",strtotime("$i/1/2003"));
		$selected = (IsSet($_GET['input_date'])) ? is_selected($mon,$_GET['input_date'],"F") : "";
		echo "<option value=$mon $selected>$mon</option>\n";}
}

function put_months2()
{
	$nw = date("F",strtotime('today'));
	for ($i=1;$i<13;$i++){
		$mon = date("F",strtotime("$i/1/2003"));
		$selected = ($nw == $mon)?"selected":"";
		echo "<option value=$mon $selected>$mon</option>\n";}
}

function is_selected($t,$i,$w,$o=true)
{
	$sel = ($o) ? "selected" : "checked";
	if ($t == date($w,strtotime($i))) return($sel);
	else return("");
}

function put_sd_program($csd)
{
	$sdp = array(' ms ',' plus ',' a1 ',' a2 ',' c1 ',' c2 ');
	
	for ($i=0;$i<count($sdp);$i++) {
		$checked = (!(strpos(" $csd ",$sdp[$i]) === false))?"checked":"";
		$disp = ($i == 0)?"Mainstream":ucwords($sdp[$i]);
		echo "<input type=checkbox name=sdprogram[] value={$sdp[$i]} $checked>&nbsp;$disp<br>\n";
	}
}

function put_rd_program($crd)
{
	$rdp = array(' I ',' II ',' III ',' IV ',' V ',' VI ');
	
	for ($i=0;$i<count($rdp);$i++) {
		$checked = (!(strpos(" $crd ",$rdp[$i]) === false))?"checked":"";
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
 if (IsSet($todo)) $body .= "ToDo: $todo\n";
 if (IsSet($action)) $body .= "Action: $action\n";
 if (IsSet($position)) $body .= "Position: $position\n";
 mail("kenrbnsn@kis-hosting.com","NNJSDA NewAdmin Page Visited",$body,"From: Visit Tracker <nobody@nnjsda.org>");
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">



<html>
<head>
	<title>NNJSDA Admin Area</title>
	<link href="../new_nnjsda.css" type="text/css" rel="STYLESHEET">
</head>

<body class=admin>
<div id=hdr>
<h1><br>NNJSDA Web Site Admin</h1>
</div>
<div id="restofpage">
<div class=primarymenu>
<? disp_menu($menuar); ?>
</div>
<hr>
<div id=lh-col>
<? if (IsSet($todo)) { ?>
<div class=hdr1><h2><? echo $headers[$todo]; ?></h2></div>
<? if ($todo == "classes") { ?>
&nbsp;<br>
<a class="abox1a" href=<? echo $PHP_SELF; ?>?todo=<? echo $todo ?>&amp;action=show>Show Class</a><br>
<a class="abox1a" href=<? echo $PHP_SELF; ?>?todo=<? echo $todo ?>&amp;action=add>Add Class</a><br>
<a class="abox1a" href=<? echo $PHP_SELF; ?>?todo=<? echo $todo ?>&amp;action=update>Update Class</a><br>
<a class="abox1a" href=<? echo $PHP_SELF; ?>?todo=<? echo $todo ?>&amp;action=delete>Delete Class</a><br>

<? } if ($todo == "updevent") { ?>
<a class=abox1a href=<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showadd>Add New Events</a>
<? if ($action == "showadd") { ?>
<br><a class=eventlink href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showadd&amp;eventmonth=<? echo $curr_month; ?>'><? echo $curr_month; ?></a>
<a class=eventlink href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showadd&amp;eventmonth=<? echo $next_month; ?>'><? echo $next_month; ?></a>
<span class=blue>Select Date:</span>
<form class=select method="post" action="<? echo $PHP_SELF; ?>">
<input name=todo type=hidden value=<? echo $todo ?>>
<input name=action type=hidden value="showadd">
Month:<select name=event_month size=1>
<option value=""></option>
<? put_months(); ?>
</select><br>
Year:<select name=event_year size=1>
<option value=""></option>
<?
	$cur_year = date("Y",strtotime("now"));
	for ($i=$cur_year;$i<$cur_year+10;$i++){
		$selected = (IsSet($_GET['input_date'])) ? is_selected($i,$_GET['input_date'],"Y") : "";
		echo "<option value=$i ".$selected.">$i</option>\n"; }?>
</select>
<input type=submit value="Show Date">
</form>
<!-- <a class=eventlink href='addevent.php'>Select Date</a> -->
<? } ?>
<br>
<a class=abox1a href=<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showdel>Delete Events</a>
<? if ($action == "showdel") { ?>
<br><a class=eventlink href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showdel&amp;eventmonth=<? echo $curr_month; ?>'><? echo $curr_month; ?></a>
<a class=eventlink href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showdel&amp;eventmonth=<? echo $next_month; ?>'><? echo $next_month; ?></a>
<span class=blue>Select Date:</span>
<form class=select method="post" action="<? echo $PHP_SELF; ?>">
<input name=todo type=hidden value=<? echo $todo ?>>
<input name=action type=hidden value="showdel">
Month:<select name=event_month size=1>
<option value=""></option>
<? put_months(); ?>
</select><br>
Year:<select name=event_year size=1>
<option value=""></option>
<?
	$cur_year = date("Y",strtotime("now"));
	for ($i=$cur_year;$i<$cur_year+10;$i++){
		$selected = (IsSet($_GET['input_date'])) ? is_selected($i,$_GET['input_date'],"Y") : "";
		echo "<option value=$i ".$selected.">$i</option>\n"; }?>
</select>
<input type=submit value="Show Date">
</form>
<!-- <a class=eventlink href='modievent.php?action=delete'>Select Date</a> -->
<? } ?>
<br>
<a class=abox1a href=<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showedi>Edit Events</a>
<? if ($action == "showedi") { ?>
<br><a class=eventlink href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showedi&amp;eventmonth=<? echo $curr_month; ?>'><? echo $curr_month; ?></a>
<a class=eventlink href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showedi&amp;eventmonth=<? echo $next_month; ?>'><? echo $next_month; ?></a>
<span class=blue>Select Date:</span>
<form class=select method="post" action="<? echo $PHP_SELF; ?>">
<input name=todo type=hidden value=<? echo $todo ?>>
<input name=action type=hidden value="showedi">
Month:<select name=event_month size=1>
<option value=""></option>
<? put_months(); ?>
</select><br>
Year:<select name=event_year size=1>
<option value=""></option>
<?
	$cur_year = date("Y",strtotime("now"));
	for ($i=$cur_year;$i<$cur_year+10;$i++){
		$selected = (IsSet($_GET['input_date'])) ? is_selected($i,$_GET['input_date'],"Y") : "";
		echo "<option value=$i ".$selected.">$i</option>\n"; }?>
</select>
<input type=submit value="Show Date">
</form>
<!-- <a class=eventlink href='modievent.php?action=update'>Select Date</a> -->
<? } ?>
<br>
<? }
if ($todo == "dispclub") { ?>
<form method="post" action=<? echo $PHP_SELF ?>>
<input type=hidden name=todo value=<? echo $todo ?>>
<select name="cluborg" size="1">
<? get_club_names(); ?>
</select>
<input type="submit" name=submit value="Show Information">
<? }
if (($todo == "updclub") || ($todo == "check_updclub")) { ?>
<form method="post" action=<? echo $PHP_SELF ?>>
<input type=hidden name=todo value=updclub>
<select name="cluborg" size="1">
<option value=" "> </option>
<? get_club_names(); ?>
</select>
<input type="submit" name=submit value="Update Information">
<input type="submit" name=submit value="Delete Club">
<input type="submit" name=submit value="Add Club">
<? }
if ($todo == "dispevent") {
?>
<!-- <a class=abox1a href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&action=<? echo $past_month; ?>'><? echo $past_month; ?></a><br> -->
<a class=abox1a href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&action=<? echo $curr_month; ?>'><? echo $curr_month; ?></a><br>
<a class=abox1a href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&action=<? echo $next_month; ?>'><? echo $next_month; ?></a><br>
<span class=blue>Select Date:</span>
<form class=select method="post" action="<? echo $PHP_SELF; ?>">
<input name=todo type=hidden value=<? echo $todo ?>>
<input name=action type=hidden value="select">
Month:<select name=event_month size=1>
<option value=""></option>
<? put_months(); ?>
</select><br>
Year:<select name=event_year size=1>
<option value=""></option>
<?
	$cur_year = date("Y",strtotime("now"));
	for ($i=$cur_year;$i<$cur_year+10;$i++){
		$selected = (IsSet($_GET['input_date'])) ? is_selected($i,$_GET['input_date'],"Y") : "";
		echo "<option value=$i ".$selected.">$i</option>\n"; }?>
</select>
<input type=submit value="Show Date">
</form>
<!-- <a class=abox1a href=<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&action=select>Select  Month</a><br>-->

<? }
if (($todo == "updcontacts") || ($todo == "dispcontacts")) {
disp_contact_boxes($todo); }
}?>
</div>
<div id=rh-col>
<? if (IsSet($todo)) { 
	if ($todo == "chgpwd") { ?>
<div class=tryit>
<h2>Changing Password</h2><hr>
<form method=post action="<? echo $PHP_SELF; ?>?todo=changepwd">
<div class=rownc>
<span class=label>Old Password:</span>
<span class=formw><input name=oldpassword type=password></span>
</div>
<div class=rownc>
<span class=label>New Password:(length > 5 chars)</span>
<span class=formw><input name=newpassword[] type=password></span>
</div>
<div class=rownc>
<span class=label>Confirm New Password:</span>
<span class=formw><input name=newpassword[] type=password></span>
</div>
<div class=rownc>
<span class=fullwidth><input type=submit value="Change Password"></span>
</div>
</form>
</div>
<? }
if ($todo == "changepwd") {
	echo "<h2>Changing Password</h2><hr>\n";
	$error_found = false;
	if (!check_old_pwd($logged_in_username, $oldpassword)) {
		$error_found = true;
		$error_msg = "Old Password is incorrect"; }
	if ($newpassword[0] == "") {
		$error_found = true;
		$error_msg = "New Password cannot be blank"; }
	if ($newpassword[0] != $newpassword[1]) {
		$error_found = true;
		$error_msg = "New Passwords do not match"; }
	if ($newpassword[0] == $logged_in_username) {
		$error_found = true;
		$error_msg = "Password can not be the same as your username"; }
	if (strlen($newpassword[0]) < 6) {
		$error_found = true;
		$error_msg = "New password not long enough"; }
	if ($error_found) {
		echo "<p class=error>Error changing password:<br>\n";
		echo "$error_msg, try again.</p>\n"; }
	else {
		change_password($logged_in_username, $oldpassword, $newpassword[0]);
		echo "<p>Password changed successfully</p>\n"; }
}
if ($todo == "updevent") {
	switch ($action) {
	case "showedi":
		$str = "Edit";
		break;
	case "showdel":
		$str = "Delete";
		break;
	case "showadd":
		$str = "Add";
		break;
	default:
		$str = "";
}
if (IsSet($event_month) && IsSet($event_year)) $eventmonth=$event_month . " " . $event_year;
if (IsSet($eventmonth)) {?>
<div class=tryit>
<h1><? echo $str ?> Events</h1><? echo "<h2>$eventmonth</h2>"; ?><hr> 
<?
	echo "<div class=largemonth>\n";
	display_month($eventmonth,true,$action);
	echo "</div>\n";  ?>
</div>
<? } }
if ($todo == "dispevent") {
if (IsSet($event_month) && IsSet($event_year)) $action=$event_month . " " . $event_year;
if ($action != "") { ?>
<div class=tryit>
<h1><? echo ucwords($action) ?></h1><hr> 
<?  if($action != "select") {
		echo "<div class=largemonth>\n";
		display_month($action,false);
		echo "</div>\n"; }
	echo "</div>\n";
} }
if (($todo == "dispclub") && (IsSet($cluborg))) { ?>
<div class=tryit>
<h1><? echo ucwords($stripped_club); ?></h1>
<hr>
<?	$query = "Select * from clubs where club='$added_club'";
	$result = mysql_query ($query);
	$row = mysql_fetch_object($result);
 ?>
<div class=row>
<span class=label2>Location:</span>
<span class=formw><? echo $row->location ?></span>
</div>
<div class=row>
<span class=label2>Address:</span>
<span class=formw><? echo $row->address ?></span>
</div>
<div class=row>
<span class=label2>City:</span>
<span class=formw><? echo $row->city ?></span>
</div>
<div class=row>
<span class=label2>State:</span>
<span class=formw><? echo $row->state ?></span>
</div>
<div class=row>
<span class=label2>Zip:</span>
<span class=formw><? echo $row->zip ?></span>
</div>
<div class=row>
<span class=label2>County:</span>
<span class=formw><? echo $row->county ?></span>
</div>
<div class=row>
<span class=label2>URL:</span>
<span class=formw><? echo $row->url ?></span>
</div>
<div class=row>
<span class=label2>Day:</span>
<span class=formw>
<?
	if (($row->dance_day == "0000-00-00") || ($row->dance_day == ""))
		echo " ";
	else
		echo date("l",strtotime($row->dance_day)) ?>
</span>
</div>
<div class=row>
<span class=label2>Weeks:</span>
<span class=formw><? echo $row-> dance_weeks ?></span>
</div>
<div class=row>
<span class=label2>Start Time:</span>
<span class=formw><? echo date("g:i a",strtotime($row->dance_start)) ?></span>
</div>
<div class=row>
<span class=label2>Stop Time:</span>
<span class=formw><? echo date("g:i a",strtotime($row->dance_stop)) ?></span>
</div>
<div class=row>
<span class=label2>Square Dance Program:</span>
<span class=formw><? echo $row->sd_program ?></span>
</div>
<div class=row>
<span class=label2>Round Dance Program:</span>
<span class=formw><? echo $row->rd_program ?></span>
</div>
<div class=row>
<span class=label2>Caller:</span>
<span class=formw><? echo $row->caller ?></span>
</div>
<div class=row>
<span class=label2>Cuer:</span>
<span class=formw><? echo $row->cuer ?></span>
</div>
<div class=row>
<span class=label2>Contacts:</span>
<span class=thirda><span class=boldcu>Name:</span><br>
<? 
$cn=unserialize($row->contact_name);
$ce=unserialize($row->contact_email);
$cp=unserialize($row->contact_phone);
for ($i=0;$i<3;$i++) { 
 		echo "$cn[$i]<br>\n";
		} ?>
</span>
<span class=thirda><span class=boldcu>Email:</span><br>
<? for ($i=0;$i<3;$i++) { 
 		echo "$ce[$i]<br>\n";
		} ?>
</span>
<span class=thirda><span class=boldcu>Phone:</span><br>
<? for ($i=0;$i<3;$i++) { 
 		echo "$cp[$i]<br>\n";
		} ?>
</span>
</div>
</div>

</div>
<? }
if ((IsSet($cluborg)) && (array_key_exists($submit,$uc_submit))) { ?>
<div class=tryit>
<? $ac = ($submit == "Add Club")?true:false; ?>
<h1><? if ($ac) echo "$submit"; else echo ucwords($stripped_club); ?></h1>
<hr>
<form method="post" action="<? echo $PHP_SELF ?>">
<input type="Hidden" name=todo value=<? echo $todo ?>>
<?	if (!$ac) {
		$query = "Select * from clubs where club='$added_club'";
		$result = mysql_query ($query);
		$row = mysql_fetch_object($result); }
	else {
 ?>
<div class=row>
<span class=label2>Club:</span>
<span class=formw><input type=text size=50 name=cluborg></span>
</div>
<? } ?>
<div class=row>
<span class=label2>Location:</span>
<span class=formw><input type=text name=location size=50 value='<? $v=($ac)?"":$row->location; echo $v; ?>'></span>
</div>
<div class=row>
<span class=label2>Address:</span>
<span class=formw><input type=text name=address size=50 value='<? $v=($ac)?"":$row->address; echo $v; ?>'></span>
</div>
<div class=row>
<span class=label2>City:</span>
<span class=formw><input type=text name=city size=50 value='<? $v=($ac)?"":$row->city; echo $v; ?>'></span>
</div>
<div class=row>
<span class=label2>State:</span>
<span class=formw><input type=text name=state size=50 value='<? $v=($ac)?"":$row->state; echo $v; ?>'></span>
</div>
<div class=row>
<span class=label2>Zip:</span>
<span class=formw><input type=text name=zip size=50 value='<? $v=($ac)?"":$row->zip; echo $v; ?>'></span>
</div>
<div class=row>
<span class=label2>County:</span>
<span class=formw><input type=text name=county size=50 value='<? $v=($ac)?"":$row->county; echo $v; ?>'></span>
</div>
<div class=row>
<span class=label2>URL:</span>
<span class=formw><input type=text name=url size=50 value='<? $v=($ac)?"":$row->url; echo $v; ?>'></span>
</div>
<div class=row>
<span class=label2>Dance Day:</span>
<span class=formw><? put_day($row->dance_day) ?></span>
</div>
<div class=row>
<span class=label2>Dance Weeks:</span>
<span class=formw><input type=text name=dance_weeks size=50 value='<? $v=($ac)?"":$row->dance_weeks; echo $v; ?>'></span>
</div>
<div class=row>
<span class=label2>Start Time:</span>
<span class=formw><select name="starthour" size="1">
<? 
	$eh = date("g",strtotime($row->dance_start));
	for ($h=1;$h<13;$h++) {
		$sel = ($eh == $h)?" selected":"";
		echo "<option value=\"$h\"$sel>$h</option>";
	}
?>
</select>:<select name="startmin" size="1">
<?
	$sela = (date("i",strtotime($row->dance_start)) == "00")?"selected":"";
	$selp = (date("i",strtotime($row->dance_start)) == "30")?"selected":"";
 ?>
	<option value="00" <? echo $sela; ?>>00</option>
	<option value="30" <? echo $selp; ?>>30</option>
</select>&nbsp;<select name="startampm" size="1">
<?
	$sela = (date("a",strtotime($row->dance_start)) == "am")?"selected":"";
	$selp = (date("a",strtotime($row->dance_start)) == "pm")?"selected":"";
 ?>
	<option value="am" <? echo $sela; ?>>am</option>
	<option value="pm" <? echo $selp; ?>>pm</option>
</select></span>
</div>
<div class=row>
<span class=label2>Stop Time:</span>
<span class=formw><select name="endhour" size="1">
<? 
	$eh = date("g",strtotime($row->dance_stop));
	for ($h=1;$h<13;$h++) {
		$sel = ($eh == $h)?" selected":"";
		echo "<option value=\"$h\"$sel>$h</option>";
	}
?>
</select>:<select name="endmin" size="1">
<?
	$sela = (date("i",strtotime($row->dance_stop)) == "00")?"selected":"";
	$selp = (date("i",strtotime($row->dance_stop)) == "30")?"selected":"";
 ?>
	<option value="00" <? echo $sela; ?>>00</option>
	<option value="30" <? echo $selp; ?>>30</option>
</select>&nbsp;<select name="endampm" size="1">
<?
	$sela = (date("a",strtotime($row->dance_stop)) == "am")?"selected":"";
	$selp = (date("a",strtotime($row->dance_stop)) == "pm")?"selected":"";
 ?>
	<option value="am" <? echo $sela; ?>>am</option>
	<option value="pm" <? echo $selp; ?>>pm</option>
</select></span>
</div>
<div class=row>
<span class=label2>Square Dance Program:</span>
<span class=formw>
<?
	put_sd_program(strtolower($row->sd_program));
 ?>
</span>
</div>
<div class=row>
<span class=label2>Round Dance Program:</span>
<span class=formw>
<?
	put_rd_program(strtoupper($row->rd_program));
 ?>
</span>
</div>
<div class=row>
<span class=label2>Club Caller(s):</span>
<span class=formw><input type=text name=caller size=50 value='<? $v=($ac)?"":$row->caller; echo $v; ?>'></span>
</div>
<div class=row>
<span class=label2>Club Cuer(s):</span>
<span class=formw><input type=text name=cuer size=50 value='<? $v=($ac)?"":$row->cuer; echo $v; ?>'></span>
</div>
<div class=row>
<span class=label2>Club Contacts:</span>
<span class=third><span class=boldcu>Name:</span><br>
<? $ts = 25;
$cn=($ac)?array("","",""):unserialize($row->contact_name);
$ce=($ac)?array("","",""):unserialize($row->contact_email);
$cp=($ac)?array("","",""):unserialize($row->contact_phone);
 for ($i=0;$i<3;$i++) { ?>
<input type=text size=<? echo $ts ?> name=contact_name[] value='<? echo $cn[$i] ?>'><br>
<? } ?>
</span>
<span class=third><span class=boldcu>Email:</span><br>
<? for ($i=0;$i<3;$i++) { ?>
<input type=text size=<? echo $ts ?> name=contact_email[] value='<? echo $ce[$i] ?>'><br>
<? } ?>
</span>
<span class=third><span class=boldcu>Phone:</span><br>
<? for ($i=0;$i<3;$i++) { ?>
<input type=text size=<? echo $ts ?> name=contact_phone[] value='<? echo $cp[$i] ?>'><br>
<? } ?>
</span>
</div>
<div class=row>
<span class=fullwidth><input type=submit name=submit value='<? echo $uc_submit[$submit] ?>'></span>
</div>
</div>
<? }
else if ((($todo == "check_updclub") || ($todo == "updclub")) && (in_array($submit,$uc_submit))) { ?>
<div class=tryit>
<h1><? echo $submit ?></h1>
<hr>
<?
switch ($submit) {
	case "Update Club Information":
		$query = "Select id from clubs where club = '$added_club'";
		$result = mysql_query($query);
		$row = mysql_fetch_object($result);
		$clubInd = $row->id;
		$starttime = date("H:i",strtotime("$starthour:$startmin $startampm"));
		$stoptime  = date("H:i",strtotime("$endhour:$endmin $endampm"));
		$rdprog = ($rdprogram != "")?implode(" ",$rdprogram):"";
		$sdprog = ($sdprogram != "")?implode(" ",$sdprogram):"";
		$cn = serialize($contact_name);
		$ce = serialize($contact_email);
		$cp = serialize($contact_phone);
		$query = "update clubs set dance_day='$day', 
								  dance_start='$starttime', 
								  dance_stop='$stoptime',
								  dance_weeks='$dance_weeks',
								  location='$location',
								  address='$address',
								  city='$city',
								  state='$state',
								  zip='$zip',
								  county='$county',
								  url='$url',
								  sd_program='$sdprog',
								  rd_program='$rdprog',
								  caller='$caller',
								  cuer='$cuer',
								  contact_name = '$cn',
								  contact_email = '$ce',
								  contact_phone = '$cp'
								  		where id='$clubInd'";
		$result = @mysql_query($query);
		if (!$result) {
			echo "<p><span class=error>The update for <span class=bold>$cluborg</span> did not work</span></p>\n";
			echo mysql_error(); }
		else
			echo "<p>The information for <span class=bold>$cluborg</span> has been updated</p>\n";
		break;
	case "OK To Delete Club":
		$query = "delete from clubs where club = '$stripped_club'";
		$result = @mysql_query($query);
		if (!$result) {
			echo "<p><span class=error>Delete of <span class=bold>$cluborg</span> did not  work.</span></p>\n";
			echo mysql_error(); }
		else
			echo "<p>Delete of <span class=bold>$cluborg</span> succeeded.</p>\n";
		break;
	case "Add New Club";
		$starttime = date("H:i",strtotime("$starthour:$startmin $startampm"));
		$stoptime  = date("H:i",strtotime("$endhour:$endmin $endampm"));
		$rdprog = ($rdprogram != "")?implode(" ",$rdprogram):"";
		$sdprog = ($sdprogram != "")?implode(" ",$sdprogram):"";
		$cn = serialize($contact_name);
		$ce = serialize($contact_email);
		$cp = serialize($contact_phone);
		$query = "Insert into clubs (club,
									location,
									county, 
									address, 
									city, 
									state, 
									zip, 
									url,
									dance_day,
									dance_weeks,
									dance_start,
									dance_stop,
									sd_program,
									rd_program,
									caller,
									cuer,
									contact_name,
									contact_email,
									contact_phone)
		 VALUES ('$added_club', 
		 		 '$location', 
				 '$county', 
				 '$address', 
				 '$city',
				 '$state',
				 '$zip',
				 '$url',
				 '$day',
				 '$dance_weeks',
				 '$starttime',
				 '$stoptime',
				 '$sdprog',
				 '$rdprog',
				 '$caller',
				 '$cuer',
				 '$cn',
				 '$ce',
				 '$cp')";
		$result = @mysql_query($query);
		if (!$result) {
			echo "<p><span class=error>Addition of <span class=bold>$cluborg</span> did not  work.</span></p>\n";
			echo mysql_error(); }
		else
			echo "<p>Addition of <span class=bold>$cluborg</span> succeeded.</p>\n";
		break;
}
 ?>
</div>
<? }

if ($todo == "addcontacts") {
	if ($action == "") {?>
<div class=tryit>
<h1>Add Contact</h1>
<hr>
<form method=post action=<? echo "$PHP_SELF?todo=$todo"; ?>&amp;action=addingcontact>
<div class=rownc>
	<span class=label2>Position:</span>
	<span class=formw><input type=text size=40 name=position></span>
</div>
<div class=rownc>
	<span class=label2>Officer or Committee:</span>
	<span class=formw>
		<input type="radio" name="officer" value="Officer">&nbsp;Officer<br>
		<input type="radio" name="officer" value="Committee Chair">&nbsp;Committee Chair</span>
</div>	
<div class=rownc>
	<span class=label2>Name:</span>
	<span class=formw><input type=text size=40 name=name></span>
</div>
<div class=rownc>
	<span class=label2>Address:</span>
	<span class=formw><input type=text size=40 name=address></span>
</div>
<div class=rownc>
	<span class=label2>City:</span>
	<span class=formw><input type=text size=40 name=city></span>
</div>
<div class=rownc>
	<span class=label2>State:</span>
	<span class=formw><input type=text size=40 name=state></span>
</div>
<div class=rownc>
	<span class=label2>Zip:</span>
	<span class=formw><input type=text size=40 name=zip></span>
</div>
<div class=rownc>
	<span class=label2>Phone:</span>
	<span class=formw><input type=text size=40 name=phone></span>
</div>
<div class=rownc>
	<span class=label2>Email:</span>
	<span class=formw><input type=text size=40 name=email></span>
</div>
<div class=rownc>
	<span class=label2>Listing Order:</span>
	<span class=formw><input type=text size=5 name=listorder></span>
</div>
<div class=rownc>
	<span class=fullwidth><input type=submit value="Add Contact" name=submit></span>
</div>
</form>
</div>
<? } else {?>
<div class=tryit>
<h1>Adding Contact</h1>
<hr>
<? 	$error_found = false;
	$error_msg = "";
	if ($position == "") {
		$error_found = true;
		$error_msg .= "Position cannot be blank"; }
	if (!check_for_position($position, $name)) {
		$error_found = true;
		$error_msg .= "<br>Position $position using $name has already been added, use 'Update Contact'"; }
	if ($name == "") {
		$error_found = true;
		$error_msg .= "<br>Name cannot be blank"; }
	if ($error_found) {
		echo "<p class=error>Error adding contact:<br>\n";
		echo "$error_msg, try again.</p>\n"; }
	else {
		$query = "Insert into contacts (position,
										officer,
										name, 
										address, 
										city, 
										state, 
										zip, 
										phone, email, listorder)
			 VALUES ('$position', 
			 		 '$officer', 
					 '$name', 
					 '$address', 
					 '$city',
					 '$state',
					 '$zip',
					 '$phone', '$email', '$listorder')";
		$result = mysql_query($query);
		if ($result)
			echo "Contact <span class=bold>$position, $name</span> added successfully.";
		else {
			echo "<p class=error>Couldn't add Contact $position.";
			mysql_error();
			echo "</p>\n"; }
		 }
?>
</div>
<? } } if (($todo == "dispcontacts") && (IsSet($position))) { ?>
<div class=tryit>
<div id=hdr>
<h1>Display Contact<br><? echo $position; ?></h1>
</div>
<?
	$query = "Select * from contacts WHERE position = '$position' and name='$name'";
	$result = mysql_query($query);
	$row = mysql_fetch_object($result); ?>
<div class=row>
	<span class=label2>Position:</span>
	<span class=formw><?echo $row->position; ?></span>
</div>	
<div class=row>
	<span class=label2>Officer or Committee:</span>
	<span class=formw><?echo $row->officer; ?></span>
</div>	
<div class=row>
	<span class=label2>Name:</span>
	<span class=formw><?echo $row->name; ?></span>
</div>	
<div class=row>
	<span class=label2>Address:</span>
	<span class=formw><?echo $row->address; ?></span>
</div>	
<div class=row>
	<span class=label2>City:</span>
	<span class=formw><?echo $row->city; ?></span>
</div>	
<div class=row>
	<span class=label2>State:</span>
	<span class=formw><?echo $row->state; ?></span>
</div>	
<div class=row>
	<span class=label2>Zip:</span>
	<span class=formw><?echo $row->zip; ?></span>
</div>	
<div class=row>
	<span class=label2>Phone:</span>
	<span class=formw><?echo $row->phone; ?></span>
</div>	
<div class=row>
	<span class=label2>Email:</span>
	<span class=formw><?echo $row->email; ?></span>
</div>	
<div class=row>
	<span class=label2>Listing Order:</span>
	<span class=formw><?echo $row->listorder; ?></span>
</div>	
</div>
<? } if (($todo == "updcontacts") && (IsSet($position))) { 
	if ($action != "updating") {?>
<div class=tryit>
<div id=hdr>
<h1>Update Contact<br><? echo $position; ?></h1>
</div>
<?
	$query = "Select * from contacts WHERE position = '$position' and name='$name'";
	$result = mysql_query($query);
	$row = mysql_fetch_object($result); ?>
	<form method=post action=<? echo "$PHP_SELF?todo=$todo&amp;action=updating"; ?>>
<div class=row>
	<span class=label2>Position:</span>
	<span class=formw><input name=position type=text size=40 value="<?echo $row->position; ?>"></span>
</div>
<div class=row>
	<span class=label2>Officer or Chair:</span>
	<span class=formw>
		<input type="radio" name="officer" value="Officer" <? if ($row->officer == "Officer") echo "checked"; ?>>&nbsp;Officer<br>
		<input type="radio" name="officer" value="Committee Chair" <? if ($row->officer == "Committee Chair") echo "checked"; ?>>&nbsp;Committee Chair</span>
</div>	
<div class=row>
	<span class=label2>Name:</span>
	<span class=formw><input name=name type=text size=40 value="<?echo $row->name; ?>"></span>
</div>	
<div class=row>
	<span class=label2>Address:</span>
	<span class=formw><input name=address type=text size=40 value="<?echo $row->address; ?>"></span>
</div>	
<div class=row>
	<span class=label2>City:</span>
	<span class=formw><input name=city type=text size=40 value="<?echo $row->city; ?>"></span>
</div>	
<div class=row>
	<span class=label2>State:</span>
	<span class=formw><input name=state type=text size=40 value="<?echo $row->state; ?>"></span>
</div>	
<div class=row>
	<span class=label2>Zip:</span>
	<span class=formw><input name=zip type=text size=40 value="<?echo $row->zip; ?>"></span>
</div>	
<div class=row>
	<span class=label2>Phone:</span>
	<span class=formw><input name=phone type=text size=40 value="<?echo $row->phone; ?>"></span>
</div>
<div class=row>
	<span class=label2>Email:</span>
	<span class=formw><input name=email type=text size=40 value="<?echo $row->email; ?>"></span>
</div>
<div class=row>
	<span class=label2>Listing Order:</span>
	<span class=formw><input name=listorder type=text size=5 value="<?echo $row->listorder; ?>"></span>
</div>
<div class=row>
	<span class=fullwidth><input type=submit name=submit value="Update <? echo $position ?>"></span>
</div>
</form>	
</div>
<? } else { ?>
<div class=tryit>
<div id=hdr>
<h1>Updating Contact<br><? echo $position; ?></h1>
</div>
<? 	$error_found = false;
	$error_msg = "";
	if ($position == "") {
		$error_found = true;
		$error_msg .= "Position cannot be blank"; }
	if (check_for_position($position, $name)) {
		$error_found = true;
		$error_msg .= "<br>Position $position for $name has not been added, use 'Add Contact'"; }
	if ($name == "") {
		$error_found = true;
		$error_msg .= "<br>Name cannot be blank"; }
	if ($error_found) {
		echo "<p class=error>Error updating contact:<br>\n";
		echo "$error_msg, try again.</p>\n"; }
	else {
		$query = "Select ind from contacts WHERE position = '$position'";
		$result = mysql_query($query);
		$row = mysql_fetch_object($result);
		$query = "Update contacts set position='$position',
					name='$name',
					address='$address',
					city='$city',
					state='$state',
					zip='$zip',
					officer='$officer',
					phone='$phone', email='$email', listorder='$listorder' where ind='$row->ind'";
		$result = mysql_query($query);
		if ($result)
			echo "Contact <span class=bold>$position, $name</span> updated successfully.";
		else {
			echo "<p class=error>Couldn't update Contact: $position.";
			mysql_error();
			echo "</p>\n"; }
		}
	}
	}
	}
?>

<?
if ($todo == "classes") {
	switch ($action) {
		case "add":  
		if ($submit != "Add Class") {?>
		<div class=hdr1><h2>Add Class</h2></div>
		&nbsp;<br>
		<form method="post" action=<? echo $PHP_SELF; ?>>
		<input type="hidden" name=action value="<? echo $action ?>">
		<input type="hidden" name=todo value="<? echo $todo ?>">
		<div class=row>
			<span class=label2>Day:</span>
			<span class=formw><? put_day() ?></span>
		</div>
		<div class=row>
			<span class=label2>Club:</span>
			<span class=formw><select name="cluborg" size="1">
				<? get_club_names(); ?></select>
			</span>
		</div>
		<div class=row>
			<span class=label2>Level/Program:</span>
			<span class=formw><input type=radio name=class_level value=0>Mainstream<br>
									<input type=radio name=class_level value=1>Plus<br>
									<input type=radio name=class_level value=2>DBD/APD<br>
									<input type=radio name=class_level value=3>Advanced
			</span>
		</div>
		<div class=row>
			<span class=label2>Location:</span>
			<span class=formw><input type="text" size=50 name=class_location></span>
		</div>
		<div class=row>
			<span class=label2>Address:</span>
			<span class=formw><input type="text" size=50 name=class_address></span>
		</div>
		<div class=row>
			<span class=label2>City:</span>
			<span class=formw><input type="text" size=50 name=class_city></span>
		</div>
		<div class=row>
			<span class=label2>State:</span>
			<span class=formw><input type="text" size=50 name=class_state></span>
		</div>
		<div class=row>
			<span class=label2>Zip:</span>
			<span class=formw><input type="text" size=50 name=class_zip></span>
		</div>
		<div class=row>
			<span class=label2>Open House<br>Date/Time:</span>
			<span class=formw><select name=oh_month size=1><? put_months2(); ?></select>
								   <select name=oh_day size=1>
										<? $dy = date("d",strtotime('today'));
											for ($i=1;$i<32;$i++) {
												$selected = ($dy == $i)?"selected":"";
											echo "<option $selected>$i</option>";} ?></select>
									<select name=oh_year size=1>
										<option>2003</option>
										<option>2004</option>
										<option>2005</option>
									</select>
									&nbsp;
									<select name=oh_hour size=1>
										<? for ($h=1;$h<13;$h++) {
												$sel = ($h == 8)?"selected":"";
												echo "<option value=$h $sel>$h</option>\n"; }?>
									</select>
									:
									<select name=oh_min size=1>
										<option>00</option>			
										<option>30</option>
									</select>
									<select name=oh_ampm size=1>
										<option>am</option>
										<option selected>pm</option>
									</select>
			</span>
		</div>
		<div class=row>
			<span class=label2>Classes Start<br>Date/Time:</span>
			<span class=formw><select name=cl_month size=1><? put_months2(); ?></select>
								   <select name=cl_day size=1>
										<? $dy = date("d",strtotime('today'));
											for ($i=1;$i<32;$i++) {
												$selected = ($dy == $i)?"selected":"";
											echo "<option $selected>$i</option>";} ?></select>
									<select name=cl_year size=1>
										<option>2003</option>
										<option>2004</option>
										<option>2005</option>
									</select>
									&nbsp;
									<select name=cl_hour size=1>
										<? for ($h=1;$h<13;$h++) {
												$sel = ($h == 8)?"selected":"";
												echo "<option value=$h $sel>$h</option>\n"; }?>
									</select>
									:
									<select name=cl_min size=1>
										<option>00</option>			
										<option>30</option>
									</select>
									<select name=cl_ampm size=1>
										<option>am</option>
										<option selected>pm</option>
									</select>
			</span>
		</div>
		<div class=row>
		<span class=label2>Teacher:</span>
		<span class=formw><input type=text size=50 name=teacher></span>
		</div>
		<div class=row>
			<span class=label2>Class Coordinator:</span>
			<span class=formw><input type=text size=50 name=class_corrd></span>
		</div>
		<div class=row>
			<span class=label2>Address:</span>
			<span class=formw><input type="text" size=50 name=cc_address></span>
		</div>
		<div class=row>
			<span class=label2>City:</span>
			<span class=formw><input type="text" size=50 name=cc_city></span>
		</div>
		<div class=row>
			<span class=label2>State:</span>
			<span class=formw><input type="text" size=50 name=cc_state></span>
		</div>
		<div class=row>
			<span class=label2>Zip:</span>
			<span class=formw><input type="text" size=50 name=cc_zip></span>
		</div>
		<div class=row>
			<span class=label2>Phone:</span>
			<span class=formw><input type="text" size=50 name=cc_phone></span>
		</div>
		<div class=row>
			<span class=label2>Email:</span>
			<span class=formw><input type="text" size=50 name=cc_email></span>
		</div>
		<div class=row>
			<span class=fullwidth><input type="submit" name=submit value="Add Class"></span>
		</div>
		</form>
<?			}
		else {
				$oh_datetime = date("Y-m-d H:i",strtotime("$oh_month $oh_day, $oh_year $oh_hour:$oh_min $oh_ampm"));
				$cl_datetime = date("Y-m-d H:i",strtotime("$cl_month $cl_day, $cl_year $cl_hour:$cl_min $cl_ampm"));
				$club = urlencode(stripslashes($cluborg));
				$email = urlencode(stripslashes($cc_email));
				$cc_name = urlencode(stripslashes($class_corrd));
				$teach = urlencode(stripslashes($teacher));
				$addr = urlencode(stripslashes($class_address));
				$cc_addr = urlencode(stripslashes($cc_address));
				$query = "insert into classes (
							club_name,
							day,
							location,
							address,
							city,
							state,
							zip,
							openhouse_datetime,
							class_datetime,
							class_level,
							teacher,
							cc_name,
							cc_address,
							cc_city,
							cc_state,
							cc_zip,
							cc_phone,
							cc_email) values (
								'$club',
								'$day',
								'$class_location',
								'$addr',
								'$class_city',
								'$class_state',
								'$class_zip',
								'$oh_datetime',
								'$cl_datetime',
								'$class_level',
								'$teach',
								'$cc_name',
								'$cc_addr',
								'$cc_city',
								'$cc_state',
								'$cc_zip',
								'$cc_phone',
								'$email')";
					$result = @mysql_query($query);
					if (!$result) echo mysql_error();
					else { ?>
					<h2>The class for <? echo urldecode($club) ?> was<br>Added successfully</h2>
				<?}}
			break;
		case "show": ?>
		<div class=hdr1><h2>Show Classes</h2>
<?			break;
		case "update": ?>
		<div class=hdr1><h2>Update Class</h2>
<?			break;
		case "delete": ?>
		<div class=hdr1><h2>Delete Class</h2>
<?		break; ?>
<div class=clearer>&nbsp;</div></div>
<? } } ?>
</div>
</div>	
</body>
</html>
