<?php
session_start();
if (empty($_SESSION)) {
        header("location:isnotok.php?e=2");
        exit();
        }
$action = (IsSet($_GET['action']))?$_GET['action']:'';
$stripped_club = (IsSet($clubname))?stripslashes($clubname):"";
$added_club = (IsSet($clubname))?addslashes($clubname):"";
include('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname); 
$teststyle = 'style="font-size: 100%; font-weight: bold;"';
$level = array('Mainstream','Plus','DBD/APD','Advanced');
$club_keys = array('location','address','city','state','zip','county','url');
$headers = array('chgpwd' => "Change Password",
                                 'changepwd' => "Changing Password",
                                 'updcontacts' => "Update Contact",
                                 'delcontacts' => "Delete Contact",
                                 'addcontacts' => "Add Contact",
                                 'dispcontacts' => "Display Contact",
                                 'updevent' => "Events",
                                 'dispevent' => "Events",
                                 'adduser' => "Add New User",
                                 'updclub' => "Update Club",
                                 'addclub' => "Add Club",
                                 'delclub' => "Delete Club",
                                 'check_updclub' => "Update Club",
                                 'dispclub' => "Show Club",
                                 'dispclass' => 'Display Class',
                                 'addclass' => 'Add Class',
                                 'updclass' => 'Update Class',
                                 'delclass' => 'Delete Class',
                                 'logout' => "Logout");
                                 
$uc_submit = array('Update Club' => 'Update Club Information',
                                   'Delete Club' => 'OK To Delete Club',
                                   'Add Club' => 'Add New Club');
if (IsSet($_GET['todo']) && ($_GET['todo'] == "logout")) {
        session_destroy();
        header("Location: index.php");
        exit();
        }
$curr_month = date("F Y",strtotime("today"));
$past_month = date("F Y",strtotime("0 $curr_month"));
$next_month = date("F Y",strtotime("32 $curr_month")); 
/*
$months_days = array();
$curr_year = date("Y",strtotime("today");
for($i=1;$i<13;$i++)
        $months_days[date("F", strtotime("$i/1/$curr_year"))] = implode(",", range(1, date('t', strtotime(date("F 1, Y", strtotime("$i/1/$curr_year"))))));
*/
function get_club_names($cluborg)
{
        $query = "Select club from new_clubs order by club";
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
function disp_club_names($td)
{
        $query = "Select club from new_clubs order by club";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
                echo '<li style="font-size:65%;width:15em"><a href=' . $_SERVER['PHP_SELF'] . "?todo={$td}club&amp;clubname=" .  urlencode(urldecode(stripslashes($row->club))) . ">" . stripslashes(urldecode($row->club)) . "</a></li>\n"; }
}
function disp_class_names($td)
{
        $query = "Select distinct day from classes order by day";
        $result = mysql_query($query);
        echo mysql_error();
        while ($row = mysql_fetch_object($result)) {
                echo '<li style="font-size:65%;width:15em"><a href=' . $_SERVER['PHP_SELF'] . "?todo={$td}class&amp;classday=" .  stripslashes($row->day) . ">" . stripslashes(urldecode($row->day)) . "</a>\n";
                        echo "<ul>\n";
                                disp_class_day_name($td,$row->day);
                        echo "</ul>\n";
                echo "</li>\n"; }
}
function disp_class_day_name($td,$day,$level)
{
        $query = "Select * from classes where day='$day' order by club_name";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result)) {
                echo '<li style="width:15em"><a href=' . $_SERVER['PHP_SELF'] . "?todo={$td}class&amp;ind=" .  $row->ind . ">" . stripslashes(urldecode($row->club_name)) . ' (' . $level[$row->class_level] . ")</a>\n";
                echo "</li>\n"; }
}
function put_day($ed,$n='event_weekday')
{
        $eday = date("l",strtotime($ed));
        $days = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
        for ($i=0;$i<7;$i++)
        {
                $checked = ($eday == $days[$i]) ? "checked": "";
                echo "<input type=Radio name=$n value={$days[$i]} $checked>{$days[$i]}<br>\n";
        }
}
function check_old_pwd($u, $p)
{
$cp = crypt($p,'$1$somesalt');
$mdp = md5($p);
$query = "Select * from validusers WHERE username = '$u'";
$result = mysql_query($query);
$row = mysql_fetch_object($result);
if (($row->pwd == $cp) || ($row->pwd == $mdp)) return true;
else return false;
}
function change_password($u, $op, $np)
{
$cp = crypt($op,'$1$somesalt');
$mdp = md5($op);
$query = "Select * from validusers WHERE username = '$u' AND (pwd = '$cp' or pwd = 'mdp')";
$result = mysql_query($query);
if (mysql_num_rows($result) == 1){
        $row = mysql_fetch_object($result);
        $ncp = md5($np);
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
        echo "<div class=row><div class=halfwidth>\n";
        if ($action == "del") {
                echo '<span class=small><input type="Checkbox" name=delete[] value=yes' . $row->ind . ">&nbsp;</span>\n";
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
        echo '<div class=halfwidth><span class=formw><textarea class=textino rows=7 name=description[]>';
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
function disp_contact_boxes($td)
{
        $query = "Select distinct position from contacts order by listorder";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result))
        {
                echo "<li><a  href={$_SERVER['PHP_SELF']}?todo={$td}contact&amp;position=".urlencode($row->position).">";
                echo ucwords(urldecode($row->position))."</a>\n";
                if ($td != 'add') {
                        echo "<ul>\n";
                        disp_contact_names($td,$row->position);
                        echo "</ul>\n";
                }
                echo "</li>\n";
        }
}
function disp_contact_names($td,$pos)
{
        $query = "Select name from contacts where position='$pos' or position='" . urlencode($pos) . "' order by name";
        $result = mysql_query($query);
        while ($row = mysql_fetch_object($result))
        {
                echo '<li><a  href="' . $_SERVER['PHP_SELF'] . '?todo=' . $td . 'contact&amp;position=' . urlencode(urldecode($pos)) . '&amp;name=' . urlencode(urldecode($row->name)) . '">';
                echo ucwords(urldecode($row->name))."</a>\n";
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
        $query = "Select * from contacts WHERE position = '". urlencode($_REQUEST['position']) . 
                "' and name='" . urlencode($_REQUEST['name']) .  "' or name='" . $_REQUEST['name'] . "'";
        $result = mysql_query($query) or die(mysql_error());
        if (mysql_num_rows($result) == 0) return true;
        else return false;
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
function put_months_days($td)
{
        echo '<ul style="width:5em;">'."\n";
        for ($i=date("Y",strtotime("today"));$i<date("Y",strtotime("today"))+5;$i++) { 
        echo '  <li><a style="width:5em" href=' . $_SERVER['PHP_SELF'] . '?todo=' . $td . '&amp;year=' . $i . '>' . $i ."</a>\n";
        $start_mnt = (date("Y",strtotime("today")) == $i)?date('m',strtotime('today')):1;
        if ($td == 'addevent') {
        echo '          <ul style="width:10em">'."\n";
                        for ($j=$start_mnt;$j<13;$j++) {
        echo '                  <li><a  href=' . $_SERVER['PHP_SELF'] .'?todo=' . $td . '&amp;year=' . $i . '&amp;month=' . $j . '>' . date("F",strtotime("$j/1/$i")) . "</a>\n";
        echo "                          <ul>\n";
                                $start_day = (date("m Y",strtotime("today")) == "$j $i") ? date('j',strtotime('today')) : 1;
                                $numdays = date("t",strtotime(date("F 1, Y",strtotime("$j/1/$i"))));
                                for ($d=$start_day;$d<$numdays+1;$d++) {
        echo '                                  <li style="width:2em">'."\n";
        echo '                                          <a href=' . $_SERVER['PHP_SELF'] . '?todo=' . $td . '&amp;year=' . $i . '&amp;month=' . $j . '&amp;day=' . $d . '>' .  $d . "</a>\n";
        echo "                                  </li>\n";
                                }
        echo "                          </ul>\n";
        echo "                  </li>\n";
                        }
        echo "  </ul>\n";
        }
        else 
        {
                $query = "select distinct event_date from events where YEAR(event_date) = '" . $i . "'  and MONTH(event_date) >= '$start_mnt' order by event_date";
                $result = mysql_query($query);
                if ($result) {
                echo "<ul>\n";
                while ($row = mysql_fetch_assoc($result)) {
                        echo '  <li><a href="' . $_SERVER['PHP_SELF'] .'?todo=' . $td . '&amp;eventdate=' . $row['event_date'] . '">'.date('l, F j, Y',strtotime($row['event_date']))."</a>";
                        $q2 = "select * from events where event_date = '" . $row['event_date'] . "'";
                        $r2 = mysql_query($q2);
                        echo "<ul>\n";
                        while ($row2 = mysql_fetch_assoc($r2))
                                echo '  <li><a style="text-align:center" href="' . $_SERVER['PHP_SELF'] .'?todo=' . $td . '&amp;eventdate=' . $row['event_date'] . '&amp;eventorg=' . urlencode(urldecode($row2['event_org'])) . '">'.urldecode($row2['event_org'])."</a></li>\n";
                        echo "</ul></li>\n\n";
                }       
                echo "</ul>\n"; }
        }
        echo "  </li>\n";
                }
        echo "</ul>\n";
}
function eitheror($e,$o)
{
        if (($e == "") && ($o == "")) return($e);
        if (($e == "") && ($o != "")) return(urldecode($o));
        return(($e != urldecode($o))?$e:urldecode($o));
}
function eitheror1($e, $o)
{
        if (($e == "") && ($o == "")) return($e);
        if (($e == "") && ($o != "")) return($o);
        return(($e != $o)?$e:$o);
}
function put_row($n,$l='')
{
        global $row;
	$val = (substr(0,3,$_GET['todo']) == 'add')?'':urldecode($row[$n]);
        $line = array();
        $loc = ($l != '')?$l:ucwords($n);
        $line[] = '<div class=row>';
        $line[] = '     <span class=label2>' . $loc . ':</span>';
        $line[] = '     <span class=formw><input type="text" name=' . $n . ' class="textinp" value="' . $val . '"></span>';
        $line[] = '</div>';
        echo implode("\n",$line)."\n";
}
function put_row_date($n,$l='')
{
        global $row;
        $line = array();
        $loc = ($l != '')?$l:ucwords($n);
        if (substr(0,3,$_GET['todo']) == 'add') $dt = '';
	else $dt = ($row[$n] != '0000-00-00 00:00:00')?date('F j, Y g:i a',strtotime($row[$n])):'';
        $line[] = '<div class=row>';
        $line[] = '     <span class=label2>' . $loc . ':</span>';
        $line[] = '     <span class=formw><input type="text" name=' . $n . ' class="textinp" value="' . $dt . '"></span>';
        $line[] = '</div>';
        echo implode("\n",$line)."\n";
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
$msg = array();
$err_msg = array();
if (isset($_POST['submit']))
        switch ($_POST['submit']) {
                case 'Update Class':
                        $q = "select * from classes where ind='" . $_POST['ind'] . "'";
                        $r = @mysql_query($q);
                        $row = @mysql_fetch_assoc($r);
                        $ek = array_keys($_POST);
                        $ueq = "update classes set ";
                        $tmp = array();
                        for ($i=0;$i<count($ek);$i++) {
                                switch ($ek[$i]) {
					case 'day':
                                        case 'location':
                                        case 'address':
                                        case 'city':
                                        case 'state':
                                        case 'teacher':
                                        case 'cc_name':
                                        case 'cc_address':
                                        case 'cc_city':
                                        case 'cc_state':
                                        case 'cc_phone':
                                        case 'cc_email':
                                                if (stripslashes($_POST[$ek[$i]]) != urldecode($row[$ek[$i]]))
                                                        $tmp[] = $ek[$i] . "='" . urlencode(stripslashes($_POST[$ek[$i]])) . "'";
                                                break;
                                        case 'cc_zip':
                                        case 'zip':
                                                if ($_POST[$ek[$i]] != $row[$ek[$i]])
                                                        $tmp[] = $ek[$i] . "='" . $_POST[$ek[$i]] . "'";
                                                break;
                                        case 'openhouse_datetime':
                                        case 'class_datetime':
                                                        if ($_POST[$ek[$i]] != '' )
                                                                $tmp_datetime = date('Y-m-d H:i:00',strtotime($_POST[$ek[$i]]));
                                                        else
                                                                $tmp_datetime = '0000-00-00 00:00:00';
                                                        if ($tmp_datetime != $row[$ek[$i]])
                                                                $tmp[] = $ek[$i] . "='" . $tmp_datetime . "'";
                                        break;
                                }
                        }
                        $ueq .= implode(", ",$tmp) . " where ind='" . $_POST['ind'] . "'";
                        $msg[] = '<br>Update Query:' . $ueq;
                        $result = @mysql_query($ueq);
                        if($result) $msg[] = 'Class updated OK';
                        else $err_msg[] =  '<span class=error>Problem updating class:'.mysql_error().'</span>';
                break;
		case 'Add Class':
                        $ek = array_keys($_POST);
                        $ueq = "insert classes set ";
                        $tmp = array();
                        for ($i=0;$i<count($ek);$i++) {
                                switch ($ek[$i]) {
					case 'club_name':
					case 'day':
                                        case 'location':
                                        case 'address':
                                        case 'city':
                                        case 'state':
                                        case 'teacher':
                                        case 'cc_name':
                                        case 'cc_address':
                                        case 'cc_city':
                                        case 'cc_state':
                                        case 'cc_phone':
                                        case 'cc_email':
                                                if ($_POST[$ek[$i]] != '')
                                                        $tmp[] = $ek[$i] . "='" . urlencode(stripslashes($_POST[$ek[$i]])) . "'";
                                                break;
                                        case 'cc_zip':
                                        case 'zip':
					case 'level':
                                                if ($_POST[$ek[$i]] != '')
                                                        $tmp[] = $ek[$i] . "='" . $_POST[$ek[$i]] . "'";
                                                break;
                                        case 'openhouse_datetime':
                                        case 'class_datetime':
                                                if ($_POST[$ek[$i]] != '' )
                                                        $tmp_datetime = date('Y-m-d H:i:00',strtotime($_POST[$ek[$i]]));
                                                else
                                                        $tmp_datetime = '0000-00-00 00:00:00';
                                        	$tmp[] = $ek[$i] . "='" . $tmp_datetime . "'";
                                        break;
                                }
                        }
                        $ueq .= implode(", ",$tmp);
                        $msg[] = '<br>Insert Query:' . $ueq;
                        $result = @mysql_query($ueq);
                        if($result) $msg[] = 'Class added OK';
                        else $err_msg[] =  '<span class=error>Problem updating class:'.mysql_error().'</span>';

		break;
        case "Delete Club":
                $query = "delete from new_clubs where id = '" . $_POST['id']. "'";
                $result = @mysql_query($query);
                if (!$result) {
                        $err_msg[] = "<p><span class=error>Delete of <span class=bold>$cluborg</span> did not  work.</span></p>\n". mysql_error(); }
                else
                        $msg[] = "<p>Delete of <span class=bold>$cluborg</span> succeeded.</p>\n";
                break;
                }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
        <title>NNJSDA Admin Area</title>
        <link href="nnjsda.css" type="text/css" rel="STYLESHEET">
        <script type="text/javascript"><!--//--><![CDATA[//><!--
activateMenu = function(nav) {
    /* currentStyle restricts the Javascript to IE only 
        if (document.all && document.getElementById(nav).currentStyle) {  */
        var navroot = document.getElementById(nav);
        
        /* Get all the list items within the menu */
        var lis=navroot.getElementsByTagName("LI"); 
        for (i=0; i<lis.length; i++) {
        
           /* If the LI has another menu level */
            if(lis[i].lastChild.tagName=="UL"){
                /* assign the function to the LI */
                lis[i].onmouseover=function() { 
                
                   /* display the inner menu */
                   this.lastChild.style.display="block";
                }
                lis[i].onmouseout=function() {                       
                   this.lastChild.style.display="none";
                }
            }
        }
    }
window.onload= function(){
    /* pass the function the id of the top level UL */
    activateMenu('vertnav1');
}
//--><!]]></script>
</head>
<body class=admin>
<div id=hdr>
<h1>NNJSDA Web Site Admin</h1>
</div>
<div id="lhcol">
<div id=vertnav1>
<ul>
<li><a href="#">Club</a>
        <ul style="width:5em">
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=addclub>Add</a></li>
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=updclub>Update</a>
                        <ul>
                                <? disp_club_names('upd') ?>
                        </ul>
                </li>
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=delclub>Delete</a>
                        <ul>
                                <? disp_club_names('del') ?>
                        </ul>           
                </li>
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=dispclub>Display</a>
                        <ul>
                                <? disp_club_names('disp') ?>
                        </ul>
                </li>
        </ul>
</li>
<li><a href="#">Class</a>
        <ul style="width:5em">
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=addclass>Add</a></li>
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=updclass>Update</a>
                        <ul>
                                <? disp_class_names('upd') ?>
                        </ul>
                </li>
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=delclass>Delete</a>
                        <ul>
                                <? disp_class_names('del') ?>
                        </ul>           
                </li>
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=dispclass>Display</a>
                        <ul>
                                <? disp_class_names('disp') ?>
                        </ul>
                </li>
        </ul>
</li>
<li><a <? echo $teststyle ?> href="#">Contacts</a>
        <ul style="width:5em">
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=addcontacts>Add</a>
                        <ul>
                                <? disp_contact_boxes('add'); ?>
                        </ul>
                </li>
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=updcontacts>Update</a>
                        <ul>
                                <? disp_contact_boxes('upd'); ?>
                        </ul>           
                </li>
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=delcontacts>Delete</a>
                        <ul>
                                <? disp_contact_boxes('del'); ?>
                        </ul>           
                </li>
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=dispcontacts>Display</a>
                        <ul>
                                <? disp_contact_boxes('disp'); ?>
                        </ul>           
                </li>
        </ul>
</li>
<li><a <? echo $teststyle ?> href="#">Events</a>
        <ul style="width:5em">
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=addevent>Add</a>
                        <? put_months_days("addevent"); ?>
                </li>
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=updevent>Update</a>
                        <? put_months_days("updevent"); ?>
                </li>
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=delevent>Delete</a>
                        <? put_months_days("delevent"); ?>
                </li>
                <li><a href=<? echo $_SERVER['PHP_SELF'] ?>?todo=dispevent>Display</a>
                        <? put_months_days("dispevent"); ?>
                </li>
        </ul>
</li>
<li><a <? echo $teststyle ?> href=<? echo $_SERVER['PHP_SELF'] ?>?todo=adduser>Add New User</a></li>
<li><a <? echo $teststyle ?> href=<? echo $_SERVER['PHP_SELF'] ?>?todo=chgpwd>Change Password</a></li>
<li><a <? echo $teststyle ?> href="<? echo $_SERVER['PHP_SELF'] ?>">Refresh Screen</a></li>
<li><a <? echo $teststyle ?> href=<? echo $_SERVER['PHP_SELF'] ?>?todo=logout>Logout</a></li>
</ul>
<div class=clearer>&nbsp;</div>
</div>
<div class=clearer>&nbsp;</div>
</div>
<div id=rhcol>
<? if (IsSet($todo)) { 
        switch ($todo) {
        case "chgpwd":  ?>
        <div id=hdr>
<h2>Changing Password</h2>
</div><form class="cp" method="post" action="?todo=changepwd">
<div class=row>
<span class=label style="color:white;">Old Password:</span>
<span class=formw><input class=textinp name=oldpassword type=password></span>
</div>
<div class=row>
<span class=label style="color:white">New Password:<br>(length > 5 chars)</span>
<span class=formw><input class=textinp name=newpassword[] type=password></span>
</div>
<div class=row>
<span class=label style="color:white">Confirm New Password:</span>
<span class=formw><input class=textinp name=newpassword[] type=password></span>
</div>
<div class=rownc>
<span class=fullwidth><input type=submit value="Change Password"></span>
</div>
<div class=clearer>&nbsp;</div>
</form>
<?
        break;
case "changepwd":
        echo "<div id=hdr>\n";
        echo "<h2>Changing Password</h2></div>\n";
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
        break;
case 'addevent':
                $eventdate = $_REQUEST['year'] . '-' . $_REQUEST['month'] . '-' . $_REQUEST['day'];
                echo '<div id=hdr>';
                echo '<h1>Add Event<br>' . date('l, F j, Y',strtotime($eventdate)) . "</h1>\n";
                echo "</div>\n";
                if (!isset($_POST['submit'])) { ?>
                <form method=post action="<? echo $_SERVER['PHP_SELF'] ?>">
                <input type="hidden" name=todo value="<? echo $todo ?>">
                <input type="hidden" name=year value="<? echo $_GET['year'] ?>">
                <input type="hidden" name=month value="<? echo $_GET['month'] ?>">
                <input type="hidden" name=day value="<? echo $_GET['day'] ?>">
                <div class=row>
                        <span class=label>Date:</span>
                        <span class=formw>
                        <select name=event_month size=1>
                        <option value=""></option>
                        <? put_months($eventdate); ?>
                        </select>&nbsp;
                        <select name=event_day size=1>
                        <option value=""></option>
                        <? 
                                $ed = $_GET['day'];
                                for ($i=1;$i<32;$i++) {
                                $selected = ($i == $ed) ? "selected" : "";
                                echo "<option value=$i ".$selected.">$i</option>\n"; }?>
                        </select>&nbsp;
                        <select name=event_year size=1>
                        <option value=""></option>
                        <?
                                $ey = $_GET['year'];
                                $cur_year = date("Y",strtotime("now"));
                                for ($i=$cur_year;$i<$cur_year+10;$i++){
                                        $selected = ($i == $ey) ? "selected" : "";
                                        echo "<option value=$i ".$selected.">$i</option>\n"; }?>
                        </select>
                        </span>
                </div>
                <div class=row>
                <span class=label>Club or Organization Name:</span>
                <span class=formw><select class=textinp name="event_org" size="1">
                <? get_club_names(''); ?>
                <option value="NNJSDA">NNJSDA</option>
                <option value="5 Clubs">5 Clubs</option>
                </select>
                </span>
                </div>
                <div class=row>
                <span class=label>Event Type:</span>
                <span class=formw><select class=textinp name=event_type size=1>
                <? get_event_types(''); ?>
                </select>
                </span>
                </div>
                <div class=row>
                <span class=label>Event Name:</span>
                <span class=formw><input name=event_name class=textinp type="Text"></span>
                </div>
                <div class=row>
                <span class=label>Event Description:</span>
                <span class=formw><textarea class=textinp rows="10" name="event_description"></textarea></span>
                </div>
                <div class=row>
                <span class=label>Event URL:</span>
                <span class=formw><input name="event_url" type="text" class=textinp></span>
                </div>
                <div class=row>
                <span class=label>Day:</span>
                <span class=formw><? put_day($eventdate); ?></span>
                </div>
                <div class=row>
                <span class=label>Start Time:</span>
                <span class=formw><select name="starthour" size="1">
                <? 
                        for ($h=1;$h<13;$h++) {
                                $sel = ($h == 8)?" selected":"";
                                echo "<option value=\"$h\"$sel>$h</option>\n";
                        }
                ?>
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
                <? 
                        for ($h=1;$h<13;$h++) {
                                $sel = ($h == 10)?" selected":"";
                                echo "<option value=\"$h\"$sel>$h</option>\n";
                        }
                ?>
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
                <span class=formw><input name=event_location class=textinp type="Text"></span>
                </div>
                <div class=row>
                <span class=label>Address:</span>
                <span class=formw><input name=event_address class=textinp type="Text"></span>
                </div>
                <div class=row>
                <span class=label>City:</span>
                <span class=formw><input name=event_city class=textinp type="Text"></span>
                </div>
                <div class=row>
                <span class=label>State:</span>
                <span class=formw><input name=event_state class=textinp type="Text"></span>
                </div>
                <div class=row>
                <span class=label>Zip:</span>
                <span class=formw><input name=event_zip class=textinp type="Text"></span>
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
                <span class=formw><input name=caller class=textinp type=text></span>
                </div>
                <div class=row>
                <span class=label>Cuer(s):</span>
                <span class=formw><input name=cuer class=textinp type=text></span>
                </div>
                <div class=row>
                <span class=label>Club Contacts:</span>
                <span class=thirda  style="width:20%;"><span class=boldcu>Name:</span><br>
                <? 
                 for ($i=0;$i<3;$i++) { ?>
                <input type=text  class=textinp name=contact_name[]>
                <? } ?>
                </span>
                <span class=thirda style="width:20%"><span class=boldcu>Email:</span><br>
                <? for ($i=0;$i<3;$i++) { ?>
                <input type=text  class=textinp name=contact_email[]>
                <? } ?>
                </span>
                <span class=thirda style="width:20%"><span class=boldcu>Phone:</span><br>
                <? for ($i=0;$i<3;$i++) { ?>
                <input type=text  class=textinp name=contact_phone[]>
                <? } ?>
                </span>
                </div>
                <div class=row>
                <span class=fullwidth><input type=submit name="submit" value="Add Event"></span>
                </div>
                </form> 
<?              } else {
                        $ek = array_keys($_POST);
                        $ueq = "insert events set ";
                        $event_date = '';
                        $start_time = '';
                        $end_time = '';
                        $tmp = array();
                        for ($i=0;$i<count($ek);$i++) {
                                switch ($ek[$i]) {
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
                                                        $tmp[] = $ek[$i] . "='" . urlencode(stripslashes($_POST[$ek[$i]])) . "'";
                                                break;
                                        case 'event_zip':
                                                        $tmp[] = $ek[$i] . "='" . $_POST[$ek[$i]] . "'";
                                                break;
                                        case 'cuer':
                                        case 'caller':
                                                        $tmp[] = 'event_' . $ek[$i] . "='" . urlencode(stripslashes($_POST[$ek[$i]])) . "'";
                                                break;
                                        case 'contact_name':
                                        case 'contact_email':
                                        case 'contact_phone':
                                                        $tmp[] = 'event_' . $ek[$i] . "='" . serialize($_POST[$ek[$i]]) . "'";
                                                break;
                                        case 'event_month':
                                        case 'event_day':
                                        case 'event_year':
                                                if ($event_date == '') {
                                                        $tmp_str = $_POST['event_month'] . ' ' . $_POST['event_day'] . ',' . $_POST['event_year'];
                                                        echo $tmp_str;
                                                        $event_date = date('Y-m-d',strtotime($tmp_str));
                                                        $tmp[] = "event_date='" . $event_date . "'";
                                                }
                                        break;
                                        case 'starthour':
                                        case 'startmin':
                                        case 'startampm':
                                                if ($start_time == '') {
                                                        $start_time = date('H:i',strtotime($_POST['starthour'] . ':' . $_POST['startmin'] . ' ' . $_POST['startampm']));
                                                        $tmp[] = "event_start_time='" . $start_time . "'";
                                                }
                                        break;
                                        case 'endhour':
                                        case 'endmin':
                                        case 'endampm':
                                                if ($end_time == '') {
                                                        $end_time = date('H:i',strtotime($_POST['endhour'] . ':' . $_POST['endmin'] . ' ' . $_POST['endampm']));
                                                        $tmp[] = "event_stop_time='" . $end_time . "'";                                         
                                                }
                                        break;
                                        case 'sdprogram':
                                                $tmp[] = "event_sd_program='" . implode(' ',$_POST['sdprogram']) . "'";
                                        break;
                                        case 'rdprogram':
                                                $tmp[] = "event_rd_program='" . implode(' ',$_POST['rdprogram']) . "'";
                                        break;
                                }
                        }
                        $ueq .= implode(", ",$tmp);
                        echo '<br>Add Query:';print_r($ueq); echo "</br>\n";
                        $result = @mysql_query($ueq);
                        if($result) echo "Event added OK<br>\n";
                        else echo "<span class=error>Problem adding event:".mysql_error()."</span>\n";
                }
        break;
case 'updevent':
        if (!isset($_REQUEST['eventorg'])) {
                if (!isset($_REQUEST['eventdate'])) {
                        if (isset($_REQUEST['year'])) {
                                echo '<div id=hdr>';
                                echo '<h1>Update an Event</h1><h2>' . $_REQUEST['year'] . "</h2></div>\n";
                                $q = "select * from events where YEAR(event_date) = '" . $_REQUEST['year'] . "' order by event_date";
                                $rs = mysql_query($q);
                                echo "<p>Please choose one of the following events to update:</p>\n";
                                while ($rw = mysql_fetch_assoc($rs)) {
                                        echo '<a href="' . $_SERVER['PHP_SELF'] . '?todo=updevent&amp;eventdate=' . $rw['event_date'] . '&amp;eventorg=' . urlencode(urldecode($rw['event_org'])) . '">';
                                        echo date('l, F j, Y',strtotime($rw['event_date']));
                                        echo ' --> ' . urldecode($rw['event_org']) . "</a><br>\n"; 
                                }
                        }
                } else {
                        echo '<div id=hdr>';
                        echo '<h1>Update an Event</h1><h2>' . date('F j, Y',strtotime($_REQUEST['eventdate'])). "</h2></div>\n";
                        $q = "select * from events where event_date = '" . $_REQUEST['eventdate'] . "' order by event_date";
                        $rs = mysql_query($q);
                        echo "<p>Please choose one of the following events to update:</p>\n";
                        while ($rw = mysql_fetch_assoc($rs)) {
                                echo date('l, F j, Y',strtotime($rw['event_date']));
                                echo ' --> ' . urldecode($rw['event_org']) . "<br>\n"; 
                        }
                }
        } else {
                echo '<div id=hdr>';
                echo '<h1>Update Event<br>' . date('l, F j, Y',strtotime($_REQUEST['eventdate'])) . "</h1>\n";
                echo '<h2>' . ucwords($_REQUEST['eventorg']) . "</h2></div>\n";
                $q = "select * from events where event_date = '" . $_REQUEST['eventdate'] . "' and (event_org = '" . $_REQUEST['eventorg'] . "' or event_org = '" . urlencode($_REQUEST['eventorg']) . "')";
                $rs = mysql_query($q);
                $event = mysql_fetch_object($rs);
                $rs = mysql_query($q);
                $event_ar = mysql_fetch_assoc($rs);
                $query = "Select * from new_clubs where club = '" . urlencode($event->event_org) . "'";
                $result = mysql_query($query);
                $club_info = mysql_fetch_object($result);
                $ek = array_keys($event_ar);
                if (!isset($_POST['submit'])) { ?>
                <form method=post action="<? echo $_SERVER['PHP_SELF'] ?>">
                <input type=hidden name=id value=<? echo $event->ind; ?>>
                <input type="hidden" name=todo value="<? echo $todo ?>">
                <input type="hidden" name=year value="<? echo $_REQUEST['year'] ?>">
                <input type="hidden" name="eventdate" value="<? echo $_REQUEST['eventdate']?>">
                <input type="hidden" name="eventorg" value="<? echo $_REQUEST['eventorg']?>">
                <div class=row>
                        <span class=label>Date:</span>
                        <span class=formw>
                        <select name=event_month size=1>
                        <option value=""></option>
                        <? put_months($_REQUEST['eventdate']); ?>
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
                <span class=formw><select class=textinp name="event_org" size="1">
                <? get_club_names(urldecode($event->event_org)); ?>
                <option value="NNJSDA" <? $s = ($event->event_org == "NNJSDA")?"selected":""; echo $s ?>>NNJSDA</option>
                <option value="5 Clubs" <? $s = ($event->event_org == "5 Clubs")?"selected":""; echo $s ?>>5 Clubs</option>
                </select>
                </span>
                </div>
                <div class=row>
                <span class=label>Event Type:</span>
                <span class=formw><select class=textinp name=event_type size=1>
                <? get_event_types($event->event_type); ?>
                </select>
                </span>
                </div>
                <div class=row>
                <span class=label>Event Name:</span>
                <span class=formw><input name=event_name class=textinp type="Text" value="<? echo urldecode($event->event_name); ?>"></span>
                </div>
                <div class=row>
                <span class=label>Event Description:</span>
                <span class=formw><textarea class=textinp rows="10" name="event_description"><? echo urldecode($event->event_description); ?></textarea></span>
                </div>
                <div class=row>
                <span class=label>Event URL:</span>
                <span class=formw><input name="event_url" type="text" class=textinp value="<? echo eitheror(urldecode($event->event_url),$club->url); ?>"></span>
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
                <span class=formw><input name=event_location class=textinp type="Text" value="<? echo  eitheror(urldecode($event->event_location),$club_info->location); ?>"></span>
                </div>
                <div class=row>
                <span class=label>Address:</span>
                <span class=formw><input name=event_address class=textinp type="Text" value="<? echo  eitheror(urldecode($event->event_address),$club_info->address); ?>"></span>
                </div>
                <div class=row>
                <span class=label>City:</span>
                <span class=formw><input name=event_city class=textinp type="Text" value="<? echo  eitheror(urldecode($event->event_city),$club_info->city); ?>"></span>
                </div>
                <div class=row>
                <span class=label>State:</span>
                <span class=formw><input name=event_state class=textinp type="Text" value="<? echo  eitheror(urldecode($event->event_state),$club_info->state); ?>"></span>
                </div>
                <div class=row>
                <span class=label>Zip:</span>
                <span class=formw><input name=event_zip class=textinp type="Text" value="<? echo  eitheror($event->event_zip,$club_info->zip); ?>"></span>
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
                <span class=formw><input name=caller class=textinp type=text value="<? echo  eitheror(urldecode($event->event_caller),$club_info->caller); ?>"></span>
                </div>
                <div class=row>
                <span class=label>Cuer(s):</span>
                <span class=formw><input name=cuer class=textinp type=text value="<? echo  eitheror(urldecode($event->event_cuer),$club_info->cuer); ?>"></span>
                </div>
                <div class=row>
                <span class=label>Club Contacts:</span>
                <span class=thirda  style="width:20%;"><span class=boldcu>Name:</span><br>
                <? 
                $cn=($club_info->contact_name == "")?array("","",""):unserialize($club_info->contact_name);
                $ce=($club_info->contact_email == "")?array("","",""):unserialize($club_info->contact_email);
                $cp=($club_info->contact_phone == "")?array("","",""):unserialize($club_info->contact_phone);
                $ecn=($event->event_contact_name == "")?array("","",""):unserialize($event->event_contact_name);
                $ece=($event->event_contact_email == "")?array("","",""):unserialize($event->event_contact_email);
                $ecp=($event->event_contact_phone == "")?array("","",""):unserialize($event->event_contact_phone);
                 for ($i=0;$i<3;$i++) { ?>
                <input type=text  class=textinp name=contact_name[] value='<? echo eitheror($ecn[$i],$cn[$i]) ?>'>
                <? } ?>
                </span>
                <span class=thirda style="width:20%"><span class=boldcu>Email:</span><br>
                <? for ($i=0;$i<3;$i++) { ?>
                <input type=text  class=textinp name=contact_email[] value='<? echo eitheror($ece[$i],$ce[$i]) ?>'>
                <? } ?>
                </span>
                <span class=thirda style="width:20%"><span class=boldcu>Phone:</span><br>
                <? for ($i=0;$i<3;$i++) { ?>
                <input type=text  class=textinp name=contact_phone[] value='<? echo eitheror($ecp[$i],$cp[$i]) ?>'>
                <? } ?>
                </span>
                </div>
                <div class=row>
                <span class=fullwidth><input type=submit name="submit" value="Update Event"></span>
                </div>
                </form> 
<?              } else {
                        $ek = array_keys($_POST);
                        $ueq = "update events set ";
                        $event_date = '';
                        $start_time = '';
                        $end_time = '';
                        $tmp = array();
                        for ($i=0;$i<count($ek);$i++) {
                                switch ($ek[$i]) {
                                        case 'event_name':
                                        case 'event_description':
                                        case 'event_org':
                                        case 'event_location':
                                        case 'event_address':
                                        case 'event_city':
                                        case 'event_state':
                                        case 'event_type':
                                        case 'event_caller':
                                        case 'event_url':
                                                if (stripslashes($_POST[$ek[$i]]) != urldecode($event_ar[$ek[$i]]))
                                                        $tmp[] = $ek[$i] . "='" . urlencode(stripslashes($_POST[$ek[$i]])) . "'";
                                                break;
                                        case 'event_zip':
                                                if ($_POST[$ek[$i]] != $event_ar[$ek[$i]])
                                                        $tmp[] = $ek[$i] . "='" . $_POST[$ek[$i]] . "'";
                                                break;
                                        case 'contact_name':
                                        case 'contact_email':
                                        case 'contact_phone':
                                                if (serialize($_POST[$ek[$i]] != $event_ar['event_' . $ek[$i]]))
                                                        $tmp[] = 'event_' . $ek[$i] . "='" . serialize($_POST[$ek[$i]]) . "'";
                                                break;
                                        case 'caller':
                                        case 'cuer':
                                                if (stripslashes($_POST[$ek[$i]]) != urldecode($event_ar['event_' . $ek[$i]]))
                                                        $tmp[] = 'event_' . $ek[$i] . "='" . urlencode(stripslashes($_POST[$ek[$i]])) . "'";                                    
                                        break;                          
                                        case 'event_month':
                                        case 'event_day':
                                        case 'event_year':
                                                if ($event_date == '') {
                                                        $tmp_str = $_POST['event_month'] . ' ' . $_POST['event_day'] . ',' . $_POST['event_year'];
                                                        echo $tmp_str;
                                                        $event_date = date('Y-m-d',strtotime($tmp_str));
                                                        if ($event_date != $event_ar['event_date'])
                                                                $tmp[] = "event_date='" . $event_date . "'";
                                                }
                                        break;
                                        case 'starthour':
                                        case 'startmin':
                                        case 'startampm':
                                                if ($start_time == '') {
                                                        $start_time = date('H:i:00',strtotime($_POST['starthour'] . ':' . $_POST['startmin'] . ' ' . $_POST['startampm']));
                                                        if ($start_time != $event_ar['event_start_time'])
                                                                $tmp[] = "event_start_time='" . $start_time . "'";
                                                }
                                        break;
                                        case 'endhour':
                                        case 'endmin':
                                        case 'endampm':
                                                if ($end_time == '') {
                                                        $end_time = date('H:i:00',strtotime($_POST['endhour'] . ':' . $_POST['endmin'] . ' ' . $_POST['endampm']));
                                                        if ($end_time != $event_ar['event_stop_time'])
                                                                $tmp[] = "event_stop_time='" . $end_time . "'";                                         
                                                }
                                        break;
                                        case 'sdprogram':
                                                if (implode(' ',$_POST['sdprogram']) != $event_ar['event_sd_program'])
                                                        $tmp[] = "event_sd_program='" . implode(' ',$_POST['sdprogram']) . "'";
                                        break;
                                        case 'rdprogram':
                                                if (implode(' ',$_POST['rdprogram']) != $event_ar['event_rd_program'])
                                                        $tmp[] = "event_rd_program='" . implode(' ',$_POST['rdprogram']) . "'";
                                        break;
                                }
                        }
                        $ueq .= implode(", ",$tmp) . " where ind='" . $_POST['id'] . "'";
                        echo '<br>Update Query:';print_r($ueq); echo "<br>\n";
                        $result = @mysql_query($ueq);
                        if($result) echo "Event updated OK<br>\n";
                        else echo "<span class=error>Problem updating event:".mysql_error()."</span>\n";
                }
        }
        break;
case 'dispevent':
        if (!isset($_GET['eventorg'])) {
                if (!isset($_GET['eventdate'])) {
                        if (isset($_GET['year'])) {
                                echo '<div id=hdr>';
                                echo '<h1>Display an Event</h1><h2>' . $_GET['year'] . "</h2></div>\n";
                                $q = "select * from events where YEAR(event_date) = '" . $_GET['year'] . "' order by event_date";
                                $rs = mysql_query($q);
                                echo "<p>Please choose one of the following events to display:</p>\n";
                                while ($rw = mysql_fetch_assoc($rs)) {
                                        echo date('l, F j, Y',strtotime($rw['event_date']));
                                        echo ' --> ' . $rw['event_org'] . "<br>\n"; 
                                }
                        }
                } else {
                        echo '<div id=hdr>';
                        echo '<h1>Display an Event</h1><h2>' . date('F j, Y',strtotime($_GET['eventdate'])). "</h2></div>\n";
                        $q = "select * from events where event_date = '" . $_GET['eventdate'] . "' order by event_date";
                        $rs = mysql_query($q);
                        echo "<p>Please choose one of the following events to display:</p>\n";
                        while ($rw = mysql_fetch_assoc($rs)) {
                                echo date('l, F j, Y',strtotime($rw['event_date']));
                                echo ' --> ' . $rw['event_org'] . "<br>\n"; 
                        }
                }
        } else {
                echo '<div id=hdr>';
                echo '<h1>Display Event<br>' . date('l, F j, Y',strtotime($_GET['eventdate'])) . "</h1>\n";
                echo '<h2>' . ucwords($_GET['eventorg']) . "</h2></div>\n";
                $q = "select * from events where event_date = '" . $_GET['eventdate'] . "' and (event_org = '" . $_GET['eventorg'] . "' or event_org='" . urlencode($_GET['eventorg']) . "')";
                $rs = mysql_query($q);
                $row = mysql_fetch_assoc($rs); 
                ?>
<div class=row>
<span class=label2>Event Type:</span>
<span class=formw><? echo urldecode($row['event_type']) ?></span>
</div>
<div class=row>
<span class=label2>Event Name:</span>
<span class=formw><? echo urldecode($row['event_name']) ?></span>
</div>
<div class=row>
<span class=label2>Description:</span>
<span class=formw><? echo urldecode($row['event_description']) ?></span>
</div>
<div class=row>
<span class=label2>Location:</span>
<span class=formw><? echo urldecode($row['event_location']) ?></span>
</div>
<div class=row>
<span class=label2>Address:</span>
<span class=formw><? echo urldecode($row['event_address']) ?></span>
</div>
<div class=row>
<span class=label2>City:</span>
<span class=formw><? echo urldecode($row['event_city']) ?></span>
</div>
<div class=row>
<span class=label2>State:</span>
<span class=formw><? echo urldecode($row['event_state']) ?></span>
</div>
<div class=row>
<span class=label2>Zip:</span>
<span class=formw><? echo $row['event_zip'] ?></span>
</div>
<div class=row>
<span class=label2>URL:</span>
<span class=formw><? echo urldecode($row['event_url']) ?></span>
</div>
<div class=row>
<span class=label2>Start Time:</span>
<span class=formw><? echo date("g:i a",strtotime($row['event_start_time'])) ?></span>
</div>
<div class=row>
<span class=label2>Stop Time:</span>
<span class=formw><? echo date("g:i a",strtotime($row['event_stop_time'])) ?></span>
</div>
<div class=row>
<span class=label2>Square Dance Program:</span>
<span class=formw><? echo $row['event_sd_program'] ?></span>
</div>
<div class=row>
<span class=label2>Round Dance Program:</span>
<span class=formw><? echo $row['event_rd_program'] ?></span>
</div>
<div class=row>
<span class=label2>Caller:</span>
<span class=formw><? echo urldecode($row['event_caller']) ?></span>
</div>
<div class=row>
<span class=label2>Cuer:</span>
<span class=formw><? echo urldecode($row['event_cuer']) ?></span>
</div>
<div class=row>
<span class=label2>Contacts:</span>
<span class=thirda><span class=boldcu>Name:</span><br>
<? 
$cn=unserialize($row['event_contact_name']);
$ce=unserialize($row['event_contact_email']);
$cp=unserialize($row['event_contact_phone']);
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
                }
        }
        break;
case 'dispclub':
case 'delclub':
if (IsSet($clubname)) { ?>
<div id=hdr>
<h1><? echo ucwords($stripped_club); ?></h1>
</div>
<?      $query = "Select * from new_clubs where club='" . urlencode(stripslashes($_GET['clubname'])) . "' or club = '" . stripslashes($_GET['clubname']) . "'";
        $result = mysql_query ($query);
        $row = mysql_fetch_object($result);
 ?>
<div class=row>
<span class=label2>Location:</span>
<span class=formw><? echo urldecode($row->location) ?></span>
</div>
<div class=row>
<span class=label2>Address:</span>
<span class=formw><? echo urldecode($row->address) ?></span>
</div>
<div class=row>
<span class=label2>City:</span>
<span class=formw><? echo urldecode($row->city) ?></span>
</div>
<div class=row>
<span class=label2>State:</span>
<span class=formw><? echo urldecode($row->state) ?></span>
</div>
<div class=row>
<span class=label2>Zip:</span>
<span class=formw><? echo urldecode($row->zip) ?></span>
</div>
<div class=row>
<span class=label2>County:</span>
<span class=formw><? echo urldecode($row->county) ?></span>
</div>
<div class=row>
<span class=label2>URL:</span>
<span class=formw><? echo urldecode($row->url) ?></span>
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
<span class=formw><? echo urldecode($row->caller) ?></span>
</div>
<div class=row>
<span class=label2>Cuer:</span>
<span class=formw><? echo urldecode($row->cuer) ?></span>
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
			if ($_GET['todo'] == 'delclub') { ?>
			<form method="post" action="<? echo $_SERVER['PHP_SELF'] ?>">
			<input type="hidden" name=id value="<? echo $row->id ?>">
			<input type="hidden" name=cluborg value="<? echo urldecode($row->club) ?>">
			<div class=row>
				<span class=fullwidth><input type=submit name=submit value="Delete Club"></span>
			</div>
			<div classs=clearer>&nbsp;</div>
			</form> <? }
        break;
case 'updclass':
        $ind = (isset($_POST['submit']))?$_POST['ind']:$_GET['ind'];
        $todo = (isset($_POST['submit']))?$_POST['todo']:$_GET['todo'];
        $query = "Select * from classes where ind='" . $ind ."'";
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $line = array();
        $line[] = '<div id=hdr>';
        $line[] = '<h2>' . $headers[$todo] . '<br>' . urldecode($row['club_name']) . '<br>' . $row['day'] . '<br>' . $level[$row['class_level']] . '</h2>';
        $line[] = '</div>';
        echo implode("\n",$line)."\n";
        $line = array();
        if (isset($_POST['submit'])) {
                echo implode("<br>\n",$msg)."\n";
                if (!empty($err_msg)) echo '<br>'.implode("<br>\n",$err_msg);
        } else {
        $line[] = '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
        $line[] = '<input type="hidden" name=todo value="' . $_GET['todo'] . '">';
        $line[] = '<input type="hidden" name=ind value="' . $_GET['ind'] . '">';
        echo implode("\n",$line)."\n";
	put_row('day');
        put_row('location');
        put_row('address');
        put_row('city');
        put_row('state');
        put_row('zip');
        put_row_date('openhouse_datetime','Open House Date');
        put_row_date('class_datetime','Class Starting Date');
        put_row('teacher');
        put_row('cc_name','Class Contact Name');
        put_row('cc_address','Class Contact Address');
        put_row('cc_city','Class Contact City');
        put_row('cc_state','Class Contact State');
        put_row('cc_zip','Class Contact Zip');
        put_row('cc_phone','Class Contact Phone');
        put_row('cc_email','Class Contact Email');
         ?>
        <div class=row>
                <span class=fullwidth><input type="submit" name=submit value="<? echo $headers[$_GET['todo']] ?>"></span>
        </div>
        <div class=clearer>&nbsp;</div>
        </form>
<?}
        break;
case 'addclass':
        $todo = (isset($_POST['submit']))?$_POST['todo']:$_GET['todo'];
        $row = array();
        $line = array();
        $line[] = '<div id=hdr>';
        $line[] = '<h2>' . $headers[$todo]  . '</h2>';
        $line[] = '</div>';
        echo implode("\n",$line)."\n";
        $line = array();
        if (isset($_POST['submit'])) {
                echo implode("<br>\n",$msg)."\n";
                if (!empty($err_msg)) echo '<br>'.implode("<br>\n",$err_msg);
        } else {
        $line[] = '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
        $line[] = '<input type="hidden" name=todo value="' . $_GET['todo'] . '">';
        echo implode("\n",$line)."\n";
	$line = array();
	put_row('club_name');
	put_row('day'); ?>
	<div class=row>
		<span class=label2>Program:</span>
		<span class=formw>
<?	for($i=0;$i<count($level);$i++)
		$line[] = '		<input name=class_level type=radio value=' . $i . '>&nbsp;' . $level[$i];
	echo implode("<br>\n",$line); ?>
		</span>
	</div>
<?
        put_row('location');
	put_row('address');
        put_row('city');
        put_row('state');
        put_row('zip');
        put_row_date('openhouse_datetime','Open House Date');
        put_row_date('class_datetime','Class Starting Date');
        put_row('teacher');
        put_row('cc_name','Class Contact Name');
        put_row('cc_address','Class Contact Address');
        put_row('cc_city','Class Contact City');
        put_row('cc_state','Class Contact State');
        put_row('cc_zip','Class Contact Zip');
        put_row('cc_phone','Class Contact Phone');
        put_row('cc_email','Class Contact Email');
         ?>
        <div class=row>
                <span class=fullwidth><input type="submit" name=submit value="<? echo $headers[$_GET['todo']] ?>"></span>
        </div>
        <div class=clearer>&nbsp;</div>
        </form>
<?}
        break;
case 'check_updclub':
case 'updclub':
case 'addclub': ?>
<div id=hdr>
<h2><? echo $headers[$_GET['todo']] ?><br><? echo stripslashes($_GET['clubname']) ?></h2>
</div>
<form method="post" action="<? echo $PHP_SELF ?>">
<input type="Hidden" name=todo value=<? echo $todo ?>>
<?      if (!isset($_POST['submit'])) {
                if ($_GET['todo'] == 'updclub') { 
                        $query = "Select * from new_clubs where club='" . urlencode(stripslashes($_GET['clubname'])) ."'";
                        $result = mysql_query ($query);
                        $row = @mysql_fetch_array($result,MYSQL_ASSOC);
                        echo '<input type="hidden" name=id value=' . $row['id'] . ">\n";
                        echo '<input type="hidden" name=clubname value="' . $row['club'] . '">' . "\n";
                         }
                else {
?>
<div class=row>
        <span class=label2>Club Name:</span>
        <span class=formw><input type="text" class="textinp" name="clubname"></span>
</div>
<? }
        for ($i=0;$i<count($club_keys);$i++) { ?>
<div class=row>
<span class=label2><? echo ucwords($club_keys[$i])?>:</span>
<span class=formw><input type=text name=<? echo $club_keys[$i] ?> class=textinp value="<? echo stripslashes(urldecode($row[$club_keys[$i]])); ?>"></span>
</div>
<? } ?>
<div class=row>
<span class=label2>Dance Day:</span>
<span class=formw><? put_day($row['dance_day'],'day') ?></span>
</div>
<div class=row>
<span class=label2>Dance Weeks:</span>
<span class=formw><input type=text name=dance_weeks class=textinp value='<? echo $row['dance_weeks']; ?>'></span>
</div>
<div class=row>
<span class=label2>Start Time:</span>
<span class=formw><select name="starthour" size="1">
<? 
        $eh = date("g",strtotime($row['dance_start']));
        for ($h=1;$h<13;$h++) {
                $sel = ($eh == $h)?" selected":"";
                echo "<option value=\"$h\"$sel>$h</option>";
        }
?>
</select>:<select name="startmin" size="1">
<?
        $sela = (date("i",strtotime($row['dance_start'])) == "00")?"selected":"";
        $selp = (date("i",strtotime($row['dance_start'])) == "30")?"selected":"";
 ?>
        <option value="00" <? echo $sela; ?>>00</option>
        <option value="30" <? echo $selp; ?>>30</option>
</select>&nbsp;<select name="startampm" size="1">
<?
        $sela = (date("a",strtotime($row['dance_start'])) == "am")?"selected":"";
        $selp = (date("a",strtotime($row['dance_start'])) == "pm")?"selected":"";
 ?>
        <option value="am" <? echo $sela; ?>>am</option>
        <option value="pm" <? echo $selp; ?>>pm</option>
</select></span>
</div>
<div class=row>
<span class=label2>Stop Time:</span>
<span class=formw><select name="endhour" size="1">
<? 
        $eh = date("g",strtotime($row['dance_stop']));
        for ($h=1;$h<13;$h++) {
                $sel = ($eh == $h)?" selected":"";
                echo "<option value=\"$h\"$sel>$h</option>";
        }
?>
</select>:<select name="endmin" size="1">
<?
        $sela = (date("i",strtotime($row['dance_stop'])) == "00")?"selected":"";
        $selp = (date("i",strtotime($row['dance_stop'])) == "30")?"selected":"";
 ?>
        <option value="00" <? echo $sela; ?>>00</option>
        <option value="30" <? echo $selp; ?>>30</option>
</select>&nbsp;<select name="endampm" size="1">
<?
        $sela = (date("a",strtotime($row['dance_stop'])) == "am")?"selected":"";
        $selp = (date("a",strtotime($row['dance_stop'])) == "pm")?"selected":"";
 ?>
        <option value="am" <? echo $sela; ?>>am</option>
        <option value="pm" <? echo $selp; ?>>pm</option>
</select></span>
</div>
<div class=row>
<span class=label2>Square Dance Program:</span>
<span class=formw>
<?
        put_sd_program(strtolower($row['sd_program']));
 ?>
</span>
</div>
<div class=row>
<span class=label2>Round Dance Program:</span>
<span class=formw>
<?
        put_rd_program(strtoupper($row['rd_program']));
 ?>
</span>
</div>
<div class=row>
<span class=label2>Club Caller(s):</span>
<span class=formw><input type=text name=caller class=textinp value='<?  echo stripslashes(urldecode($row['caller'])); ?>'></span>
</div>
<div class=row>
<span class=label2>Club Cuer(s):</span>
<span class=formw><input type=text name=cuer class=textinp value='<? echo stripslashes(urldecode($row['cuer'])); ?>'></span>
</div>
<div class=row>
<span class=label2>Club Contacts:</span>
<span class=third><span class=boldcu>Name:</span><br>
<? $ts = 25;
$cn=($ac)?array("","",""):unserialize(urldecode($row['contact_name']));
$ce=($ac)?array("","",""):unserialize(urldecode($row['contact_email']));
$cp=($ac)?array("","",""):unserialize($row['contact_phone']);
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
<span class=fullwidth><input type=submit name=submit value='<? echo $uc_submit[$headers[$_GET['todo']]] ?>'></span>
</div>
</div>
</form>
<? } else {
switch ($_POST['submit']) {
        case "Update Club Information":
                $starttime = date("H:i",strtotime("$starthour:$startmin $startampm"));
                $stoptime  = date("H:i",strtotime("$endhour:$endmin $endampm"));
                $rdprog = ($rdprogram != "")?implode(" ",$rdprogram):"";
                $sdprog = ($sdprogram != "")?implode(" ",$sdprogram):"";
                $cn = serialize($contact_name);
                $ce = serialize($contact_email);
                $cp = serialize($contact_phone);
                $query = "update clubs set dance_day='" . $_POST['day'] . "', 
                                                                  dance_start='$starttime', 
                                                                  dance_stop='$stoptime',
                                                                  dance_weeks='$dance_weeks',
                                                                  location='" . urlencode(trim(stripslashes($_POST['location']))) . "',
                                                                  address='" . urlencode(trim(stripslashes($_POST['address']))) . "',
                                                                  city='" . urlencode(trim(stripslashes($_POST['city']))) . "',
                                                                  state='" . urlencode(trim(stripslashes($_POST['state']))) . "',
                                                                  zip='$zip',
                                                                  county='" . urlencode(strtolower(trim(stripslashes($_POST['county'])))) . "',
                                                                  url='" . urlencode(trim(stripslashes($_POST['url']))) . "',
                                                                  sd_program='$sdprog',
                                                                  rd_program='$rdprog',
                                                                  caller='" . urlencode(trim(stripslashes($_POST['caller']))) . "',
                                                                  cuer='" . urlencode(trim(stripslashes($_POST['cuer']))) . "',
                                                                  contact_name = '$cn',
                                                                  contact_email = '$ce',
                                                                  contact_phone = '$cp'
                                                                                where id='" . $_POST['id'] . "'";
                $result = @mysql_query($query);
                if (!$result) {
                        echo "<p><span class=error>The update for <span class=bold>";
                        echo stripslashes(urldecode($_POST['clubname'])) . "</span> did not work</span></p>\n";
                        echo mysql_error(); }
                else {
                        echo '<p>The information for <span class=bold>';
                        echo stripslashes(urldecode($_POST['clubname'])) . "</span> has been updated</p>\n"; }
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
                 VALUES ('" . urlencode(trim(stripslashes($_POST['clubname']))) . "', 
                                 '" . urlencode(trim(stripslashes($_POST['location']))) . "', 
                                 '" . urlencode(strtolower(trim(stripslashes($_POST['county'])))) . "', 
                                 '" . urlencode(trim(stripslashes($_POST['address']))) . "', 
                                 '" . urlencode(trim(stripslashes($_POST['city']))) . "',
                                 '" . urlencode(trim(stripslashes($_POST['state']))) . "',
                                 '$zip',
                                 '" . urlencode(trim(stripslashes($_POST['url']))) . "',
                                 '$day',
                                 '$dance_weeks',
                                 '$starttime',
                                 '$stoptime',
                                 '$sdprog',
                                 '$rdprog',
                                 '" . urlencode(trim(stripslashes($_POST['caller']))) . "',
                                 '" . urlencode(trim(stripslashes($_POST['cuer']))) . "',
                                 '$cn',
                                 '$ce',
                                 '$cp')";
                $result = @mysql_query($query);
                if (!$result) {
                        echo "<p><span class=error>Addition of <span class=bold>" . stripslashes($_POST['clubname']) . "</span> did not  work.</span></p>\n";
                        echo mysql_error(); }
                else
                        echo "<p>Addition of <span class=bold>" . stripslashes($_POST['clubname']) . "</span> succeeded.</p>\n";
                break;
                }
        }
        break;
case 'addcontact':
        if ($action == "") {?>
<div id=hdr>
<h1>Add Contact</h1>
</div>
<form method=post action=<? echo "$PHP_SELF?todo=$todo"; ?>&amp;action=addingcontact>
<div class=row>
        <span class=label2>Position:</span>
        <span class=formw><input type=text class="textinp" name=position <? if (isset($_REQUEST['position'])) echo 'value="'.$_REQUEST['position'].'"';?>></span>
</div>
<div class=row>
        <span class=label2>Officer or Committee:</span>
        <span class=formw>
                <input type="radio" name="officer" value="Officer">&nbsp;Officer<br>
                <input type="radio" name="officer" value="Committee Chair">&nbsp;Committee Chair</span>
</div>  
<div class=row>
        <span class=label2>Name:</span>
        <span class=formw><input type=text class="textinp" name=name></span>
</div>
<div class=row>
        <span class=label2>Address:</span>
        <span class=formw><input type=text class="textinp" name=address></span>
</div>
<div class=row>
        <span class=label2>City:</span>
        <span class=formw><input type=text class="textinp" name=city></span>
</div>
<div class=row>
        <span class=label2>State:</span>
        <span class=formw><input type=text class="textinp" name=state></span>
</div>
<div class=row>
        <span class=label2>Zip:</span>
        <span class=formw><input type=text class="textinp" name=zip></span>
</div>
<div class=row>
        <span class=label2>Phone:</span>
        <span class=formw><input type=text class="textinp" name=phone></span>
</div>
<div class=row>
        <span class=label2>Email:</span>
        <span class=formw><input type=text class="textinp" name=email></span>
</div>
<div class=row>
        <span class=label2>Listing Order:</span>
        <span class=formw><input type=text size=5 name=listorder></span>
</div>
<div class=row>
        <span class=fullwidth><input type=submit value="Add Contact" name=submit></span>
</div>
<div class=clearer>&nbsp;</div>
</form>
<? } else {?>
<div id hdr>
<h1>Adding Contact</h1>
</div>
<?      $error_found = false;
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
                         VALUES ('" . urlencode(trim(stripslashes($_POST['position']))) . "', 
                                         '" . urlencode(trim(stripslashes($_POST['officer']))) . "', 
                                         '" . urlencode(trim(stripslashes($_POST['name']))) . "', 
                                         '" . urlencode(trim(stripslashes($_POST['address']))) . "', 
                                         '" . urlencode(trim(stripslashes($_POST['city']))) . "',
                                         '" . urlencode(trim(stripslashes($_POST['state']))) . "',
                                         '" . urlencode(trim(stripslashes($_POST['zip']))) . "',
                                         '" . urlencode(trim(stripslashes($_POSY['phone']))) . "',
                                         '" . urlencode(trim(stripslashes($_POST['email']))) . "',
                                         '" . $_POST['listorder'] ."')";
                $result = mysql_query($query);
                if ($result)
                        echo "Contact <span class=bold>" . trim(stripslashes($_POST['position'])) . ', ' . trim(stripslashes($_POST['name'])) . "</span> added successfully.\n";
                else {
                        echo "<p class=error>Couldn't add Contact " . trim(stripslashes($_POST['position'])) . ".";
                        echo "<br>$query<br>\n";
                        echo mysql_error();
                        echo "</p>\n"; }
                 }
        }
        break;
case 'dispcontact':
        if (IsSet($_REQUEST['position'])) { ?>
<div id=hdr>
<h1>Display Contact<br><? echo urldecode($_REQUEST['position']); ?></h1>
</div>
<?
        $query = "Select * from contacts WHERE position = '". urlencode(urldecode($_REQUEST['position'])) . 
                "' and name='" . urlencode($_REQUEST['name']) .  "' or name='" . $_REQUEST['name'] . "'";
        $result = mysql_query($query);
        $row = mysql_fetch_object($result); ?>
<div class=row>
        <span class=label2>Position:</span>
        <span class=formw><?echo urldecode($row->position); ?></span>
</div>  
<div class=row>
        <span class=label2>Officer or Committee:</span>
        <span class=formw><?echo urldecode($row->officer); ?></span>
</div>  
<div class=row>
        <span class=label2>Name:</span>
        <span class=formw><?echo urldecode($row->name); ?></span>
</div>  
<div class=row>
        <span class=label2>Address:</span>
        <span class=formw><?echo urldecode($row->address); ?></span>
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
        <span class=formw><?echo urldecode($row->email); ?></span>
</div>  
<div class=row>
        <span class=label2>Listing Order:</span>
        <span class=formw><?echo $row->listorder; ?></span>
</div>  
<? }
        break;
case 'updcontact':
        if (IsSet($position)) { 
        if (!isset($_POST['submit'])) {?>
<div id=hdr>
<h1>Update Contact<br><? echo $position; ?></h1>
</div>
<?
        $query = "Select * from contacts WHERE position = '" . urlencode($position) . "' and name='" . urlencode($name) . "' or name='" . $name . "'";
        $result = mysql_query($query);
        $row = mysql_fetch_object($result); ?>
        <form method=post action=<? echo $_SERFVER['PHP_SELF']; ?>>
        <input type="hidden" name=todo value="<? echo $_GET['todo'] ?>">
        <input type="hidden" name=ind value="<? echo $row->ind ?>">
<div class=row>
        <span class=label2>Position:</span>
        <span class=formw><input name=position type=text class="textinp" value="<?echo urldecode($row->position); ?>"></span>
</div>
<div class=row>
        <span class=label2>Officer or Chair:</span>
        <span class=formw>
                <input type="radio" name="officer" value="Officer" <? if ($row->officer == "Officer") echo "checked"; ?>>&nbsp;Officer<br>
                <input type="radio" name="officer" value="Committee Chair" <? if ($row->officer == "Committee Chair") echo "checked"; ?>>&nbsp;Committee Chair</span>
</div>  
<div class=row>
        <span class=label2>Name:</span>
        <span class=formw><input name=name type=text class="textinp" value="<?echo urldecode($row->name); ?>"></span>
</div>  
<div class=row>
        <span class=label2>Address:</span>
        <span class=formw><input name=address type=text class="textinp" value="<?echo urldecode($row->address); ?>"></span>
</div>  
<div class=row>
        <span class=label2>City:</span>
        <span class=formw><input name=city type=text class="textinp" value="<?echo urldecode($row->city); ?>"></span>
</div>  
<div class=row>
        <span class=label2>State:</span>
        <span class=formw><input name=state type=text class="textinp" value="<?echo $row->state; ?>"></span>
</div>  
<div class=row>
        <span class=label2>Zip:</span>
        <span class=formw><input name=zip type=text class="textinp" value="<?echo $row->zip; ?>"></span>
</div>  
<div class=row>
        <span class=label2>Phone:</span>
        <span class=formw><input name=phone type=text class="textinp" value="<?echo $row->phone; ?>"></span>
</div>
<div class=row>
        <span class=label2>Email:</span>
        <span class=formw><input name=email type=text class="textinp" value="<?echo urldecode($row->email); ?>"></span>
</div>
<div class=row>
        <span class=label2>Listing Order:</span>
        <span class=formw><input name=listorder type=text size=5 value="<?echo $row->listorder; ?>"></span>
</div>
<div class=row>
        <span class=fullwidth><input type=submit name=submit value="Update <? echo $position ?>"></span>
</div>
<div class=clearer>&nbsp;</div>
</form> 
<? } else { ?>
<div id=hdr>
<h1>Updating Contact<br><? echo $position; ?></h1>
</div>
<?      $error_found = false;
        $error_msg = "";
        $tmp = array();
        if ($position == "") {
                $error_found = true;
                $error_msg .= "Position cannot be blank"; }
/*      
        if (check_for_position($position, $name)) {
                $error_found = true;
                $error_msg .= "<br>Position $position for $name has not been added, use 'Add Contact'"; }
*/
        if ($name == "") {
                $error_found = true;
                $error_msg .= "<br>Name cannot be blank"; }
        if ($error_found) {
                echo "<p class=error>Error updating contact:<br>\n";
                echo "$error_msg, try again.</p>\n"; }
        else {
                $q1 = "select * from contacts where ind='" . $_POST['ind'] . "'";
                $rs1 = @mysql_query($q1);
                $rw1 = @mysql_fetch_object($rs1);
                $query = "Update contacts set ";
                if (urlencode(trim(stripslashes($_POST['position']))) != $rw1->position)
                        $tmp[] = "position='" . urlencode(trim(stripslashes($_POST['position']))) . "'";
                if (urlencode(trim(stripslashes($_POST['name']))) != $rw1->name)
                        $tmp[] = "name='" . urlencode(trim(stripslashes($_POST['name']))) . "'";
                if (urlencode(trim(stripslashes($_POST['address']))) != $rw1->address)
                        $tmp[] = "address='" . urlencode(trim(stripslashes($_POST['address']))) . "'";
                if (urlencode(trim(stripslashes($_POST['city']))) != $rw1->city)
                        $tmp[] = "city='" . urlencode(trim(stripslashes($_POST['city']))) . "'";
                if (urlencode(trim(stripslashes($_POST['state']))) != $rw1->state)
                        $tmp[] = "state='" . urlencode(trim(stripslashes($_POST['state']))) . "'";
                if (urlencode(trim(stripslashes($_POST['zip']))) != $rw1->zip)
                        $tmp[] = "zip='" . urlencode(trim(stripslashes($_POST['zip']))) . "'";
                if (urlencode(trim(stripslashes($_POST['officer']))) != $rw1->officer)
                        $tmp[] = "officer='" . urlencode(trim(stripslashes($_POST['officer']))) . "'";
                if (urlencode(trim(stripslashes($_POST['phone']))) != $rw1->phone)
                        $tmp[] = "phone='" . urlencode(trim(stripslashes($_POST['phone']))) . "'";
                if (urlencode(trim(stripslashes($_POST['email']))) != $rw1->email)
                        $tmp[] = "email='" . urlencode(trim(stripslashes($_POST['email']))) . "'";
                if ($_POST['listorder'] != $rw1->listorder)
                        $tmp[] = "listorder='" . $_POST['listorder'] ."'";
                if (!empty($tmp)) {
                        $query .= implode(',',$tmp);
                        $query .= " where ind='" . $_POST['ind'] . "'";
                        print_r ($query);echo "<br>\n";
                        $result = mysql_query($query);
                        if ($result) {
                                echo "Contact <span class=bold>" . 
                                        trim(stripslashes($_POST['position'])) . ', ' . 
                                        trim(stripslashes($_POST['name'])) . "</span> updated successfully."; }
                        else {
                                echo "<p class=error>Couldn't update Contact: " . trim(stripslashes($_POST['position'])) . ".";
                                echo "<br>$query<br>\n";
                                mysql_error();
                                echo "</p>\n"; }
                        }
                }
        }
}       break;
case 'delcontact':
        if (IsSet($position)) { 
        if (!isset($_POST['submit'])) {?>
<div id=hdr>
<h1>Delete Contact<br><? echo $position; ?></h1>
</div>
<?
        $query = "Select * from contacts WHERE position = '" . urlencode($position) . "' and name='" . urlencode($name) . "' or name='" . $name . "'";
        $result = mysql_query($query);
        $row = mysql_fetch_object($result); ?>
        <form method=post action=<? echo $_SERFVER['PHP_SELF']; ?>>
        <input type="hidden" name=todo value="<? echo $_GET['todo'] ?>">
        <input type="hidden" name=ind value="<? echo $row->ind ?>">
        <input type="hidden" name=name value="<?echo urldecode($row->name); ?>">
        <input type="hidden" name=position value="<?echo urldecode($row->position); ?>">
<div class=row>
        <span class=label2>Position:</span>
        <span class=formw><?echo urldecode($row->position); ?></span>
</div>
<div class=row>
        <span class=label2>Officer or Chair:</span>
        <span class=formw><? echo urldecode($row->officer); ?></span>
</div>  
<div class=row>
        <span class=label2>Name:</span>
        <span class=formw><?echo urldecode($row->name); ?></span>
</div>  
<div class=row>
        <span class=label2>Address:</span>
        <span class=formw><?echo urldecode($row->address); ?></span>
</div>  
<div class=row>
        <span class=label2>City:</span>
        <span class=formw><?echo urldecode($row->city); ?></span>
</div>  
<div class=row>
        <span class=label2>State:</span>
        <span class=formw><?echo urldecode($row->state); ?></span>
</div>  
<div class=row>
        <span class=label2>Zip:</span>
        <span class=formw><?echo urldecode($row->zip); ?></span>
</div>  
<div class=row>
        <span class=label2>Phone:</span>
        <span class=formw><?echo urldecode($row->phone); ?></span>
</div>
<div class=row>
        <span class=label2>Email:</span>
        <span class=formw><?echo urldecode($row->email); ?></span>
</div>
<div class=row>
        <span class=fullwidth><input type=submit name=submit value="Delete <? echo $position ?>"></span>
</div>
<div class=clearer>&nbsp;</div>
</form> 
<? } else { ?>
<div id=hdr>
<h1>Deleting Contact<br><? echo $position; ?></h1>
</div>
<?
                $query = "Delete from contacts where ind='" . $_POST['ind'] . "'";
                print_r ($query);echo "<br>\n";
                $result = mysql_query($query);
                if ($result) {
                                echo "Contact <span class=bold>" . 
                                        trim(stripslashes($_POST['position'])) . ', ' . 
                                        trim(stripslashes($_POST['name'])) . "</span> deleted successfully."; }
                        else {
                                echo "<p class=error>Couldn't delete Contact: " . trim(stripslashes($_POST['position'])) . ".";
                                echo "<br>$query<br>\n";
                                mysql_error();
                                echo "</p>\n"; }
        }
}       break;
}} ?>
</div>  
</body>
</html>
