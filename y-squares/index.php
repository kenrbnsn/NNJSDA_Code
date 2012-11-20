<?
session_start();
$subject = 'Y Squares Home';
$ver = "(2.4.3)";
$pageaddr = 'y.squares.index'; 
include ('emailtracker.inc.php');
$user_agent = explode('/', $_SERVER['HTTP_USER_AGENT']);
if ( strtolower($user_agent[0]) == 'libwww-perl') {
		if ($_SERVER["HTTP_X_FORWARDED_FOR"] != "")
		{
			$IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
			$proxy = $_SERVER["REMOTE_ADDR"];
			$host = @gethostbyaddr($_SERVER["HTTP_X_FORWARDED_FOR"]);
		}
		else
		{
			$IP = $_SERVER["REMOTE_ADDR"];
			$host = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
		}
		@mail('ban.ip@kis-hosting.com',$IP,$_SERVER['HTTP_HOST']."\n".$_SERVER['HTTP_USER_AGENT'],'From: index@' . $subdom . '.nnjsda.org','-f index@' . $subdom . '.nnjsda.org');
		exit();
}
if (!IsSet($subdom)) {
	$ar = explode(".",$_SERVER['HTTP_HOST']);
	$subdom = $ar[0];
	if ($subdom == "www") $subdom = $ar[1]; }
if (!empty($_REQUEST)) {
	while (list($key, $val) = each($_REQUEST))
		if (stristr($val,'http://') !== false || stristr($val,'ftp://') !== false) $problem_para[] = $key;
	if (isset($problem_para)) {
		if ($_SERVER["HTTP_X_FORWARDED_FOR"] != "")
		{
			$IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
			$proxy = $_SERVER["REMOTE_ADDR"];
			$host = @gethostbyaddr($_SERVER["HTTP_X_FORWARDED_FOR"]);
		}
		else
		{
			$IP = $_SERVER["REMOTE_ADDR"];
			$host = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
		}
		$body .= "Remote Address:" . $host . "\n";
		$body .= "HTTP_USER_AGENT = $HTTP_USER_AGENT\r\n";
		 $body .= "\n".'$_SERVER array:' . "\n";
			while (list($key, $val) = each($_SERVER))
				$body .= $key . ': ' . htmlentities(urldecode(stripslashes($val))) . "\n";
		if ($_SERVER[argc] > 0) {
			$body .= " ================================\n";
			for ($i=0;$i<$_SERVER[argc];$i++)
				$body .= '$_SERVER[argv]['.$i.'] = ' . $_SERVER['argv'][$i] . "\n"; }
		$body .= " ================================\n";
		 $body .= "\n".'$_REQUEST array:' . "\n";
			while (list($key, $val) = each($_REQUEST))
				$body .= $key . ': ' . stripslashes($val) . "\n";
		$body .= " ================================\n";
		for ($i=0;$i<count($problem_para);$i++)
			$body .= 'Get Parameter <' . $problem_para[$i] . '> = ' . stripslashes($_REQUEST[$problem_para[$i]]) . "\n";
		@mail("kenrbnsn@kis-hosting.com","Error invoking NNJSDA $subdom Home Page v" . $ver,$body,"From: Visit Tracker <tracker@' . $subdom . '.nnjsda.org>");
		@mail('ban.ip@kis-hosting.com',$IP,$_SERVER['HTTP_HOST'],'From: index@' . $subdom . '.nnjsda.org','-f index@' . $subdom . '.nnjsda.org');
		exit();
	}
}
include('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname); 

$link = (isset($_GET['link']))?$_GET['link']:'';
if ($link == '') $link = (isset($_POST['link']))?$_POST['link']:'';
if ($link == '') $link = "who";
if ($subdom == "localhost") $subdom = "ironiareelers";
$h1 = array('who' => 'Club Information',
				'schedule' => 'Schedule of Dances and/or Events',
				'where' => 'Maps and Directions',
				'contacts' => 'Club Contacts',
				'news' => 'Club News',
				'links' => 'Additional Resources');

$query = "Select * from subdomain_information where subdomain_name='$subdom'";
$result = mysql_query($query) or die("Problem with the query: $query<br>" . mysql_error());
$row = mysql_fetch_object($result);
$club_name = ucwords(urldecode($row->club_name));
$_SESSION['only_org'] = $subdom;
$_SESSION['org_name'] = $club_name;
$_SESSION['return_to'] = basename($_SERVER['PHP_SELF']);

function generate_map($r)
{
	if (($r['address'] == "") || ($r['city'] == "") || ($r['state'] == "")) return (false);
	$url = "http://www.mapquest.com/maps/map.adp?address=".$r['address'];
	$url .= "&amp;city=".$r['city']."&amp;state=".$r['state'];
	if ($r['zip'] != "") $url .= "&amp;zip=".$r['zip'];
	echo '<br>Map in (<a href="'.$url.'" target="_blank">a separate window</a>) or (<a href="'.$url.'">the same window</a>)';
	return(true);
}

function put_links($sd)
{
		$query = "select * from subdomain_links where subdomain_name = '$sd' order by title";
		$result = @mysql_query($query);
		if (!$result) echo mysql_error();
		while ($row = @mysql_fetch_array($result, MYSQL_ASSOC)) {
			echo "<p class=centerbold><a href=".urldecode($row['link_url']).">".urldecode($row['title'])."</a></p>\n";
			echo "<p>".urldecode($row['description'])."</p>\n";
			echo "<hr>\n";
		}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html>
<head>
	<title><? echo $club_name ?></title>
	<link rel="stylesheet" type="text/css" media="screen" href="subdom.css">
	<link rel="stylesheet" type="text/css" media="print" href="subdomp.css"
</head>

<body>
<div id=hdr>
<br><br>
<h1><?
	if (file_exists($subdom."logo.gif")) echo "<img src={$subdom}logo.gif><br>";
	if (file_exists($subdom."logo.jpg")) echo "<img src={$subdom}logo.jpg><br>";
 echo $club_name ?></h1>
</div>
<div id="rest-of-page">
<div id=lhcol1>
<div id=vertnav1>
<ul>
<li><a href="index.php?link=who&amp;subdom=<?php echo $subdom?>">Club Info</a></li>
<li><a href="index.php?link=news&amp;subdom=<?php echo $subdom?>">Club News</a></li>
<li><a href="new_calendar.2.4.1.php">Schedule</a></li>
<li><a href="index.php?link=where&amp;subdom=<?php echo $subdom?>">Dance Locations</a></li>
<li><a href="index.php?link=contacts&amp;subdom=<?php echo $subdom?>">Contacts</a></li>
<li><a href="index.php?link=links&amp;subdom=<?php echo $subdom?>">Additional Resources</a></li>
</ul>
<div class=clearer>&nbsp;</div>
</div>
<div class=clearer>&nbsp;</div>
</div>
<div id=rhcol1>
<div id=hdr>
<h1><? echo $h1[$link] ?></h1>
</div>
<div class=textarea>
<? 
switch ($link) {
	case "who":
	 echo strip_tags(urldecode($row->description),'<strong><a><p><b><i><u><br></u></i></b></p></strong></a>');
	 break;
	case "contacts":
		$query = "select * from subdomain_contact where subdomain_name = '$subdom' order by name";
		$result = @mysql_query($query);
		if (!$result) echo mysql_error();
		$first = true;
		while ($row = @mysql_fetch_array($result, MYSQL_ASSOC)) {
           while (list($key,$val) = each($row)) {
			  	if (($val != "") && ($key != "subdomain_name") && ($key != "ind") && ($key != "email")) {
					echo "<div class=row>\n";
						if ($key == "name") {
							if (!$first)echo "<hr>\n";
							else $first = false; }
						echo "<span class=label2>".ucwords($key).":</span>\n";
						if ($key != "name") echo "<span class=formw>".urldecode($val)."</span>\n";
						else if ($row['email'] != "") echo "<span class=formw><a href=mailto:".$row['email'].">".urldecode($val)."</a></span>\n";
							else echo "<span class=formw>".urldecode($val)."</span>\n";
					echo "</div>\n";
					}
			   }
		}
	break;
	case "where":
		$query = "select * from subdomain_location where subdomain_name = '$subdom' order by location_name";
		$result = @mysql_query($query);
		if (!$result) echo mysql_error();
		$first = true;
		while ($row = @mysql_fetch_array($result, MYSQL_ASSOC)) {
           while (list($key,$val) = each($row)) {
			  	if (($val != "") && ($key != "subdomain_name") && ($key != "ind")) {
					echo "<div class=row>\n";
						if ($key == "location_name") {
							if (!$first)echo "<hr>\n";
							else $first = false; }
						$label = ($key == "location_name")?"name of hall":$key;
						if ($key == "location_phone") $label = "phone";
						echo "<span class=label2>".ucwords($label).":</span>\n";
						echo "<span class=formw>".urldecode($val);
						if ($key == "location_name") generate_map($row);
						echo "</span>\n";
					echo "</div>\n";
					}
			   }
		}
	break;
	case 'links':
		put_links('all');
		put_links($subdom);
	break;
	case 'schedule':
		$cur_year = date('Y');
		if (isset($_GET['ind'])) {
			$query = "select * from subdomain_schedule where ind='" . $_GET['ind'] . "'";
			$result = @mysql_query($query);
			$row = @mysql_fetch_array($result);
 ?>
			<div class=row>
				<span class=label2>Date:</span>
				<span class=formw><? echo date("l, F j, Y",strtotime($row['date'])) ?></span>
			</div>
			<div class=row>
				<span class=label2>Time:</span>
				<span class=formw><? echo date("g:i a",strtotime($row['start_time']))?> - <? echo date("g:i a",strtotime($row['end_time']))?> </span>
			</div>
			<div class=row>
				<span class=label2>Name of Hall:</span>
				<span class=formw><? if (urldecode($row['location']) != "other") {
											$lquery = "select * from subdomain_location where subdomain_name='$subdom' and location_name='".$row['location']."'";
											$lresult = @mysql_query($lquery);
											$lrow = @mysql_fetch_array($lresult, MYSQL_ASSOC);
											echo urldecode($row['location']);
											echo "<span class=center>";generate_map($lrow);echo "</span><br>\n";
											echo urldecode($lrow['address'])."<br>\n";
											echo urldecode($lrow['city'])."<br>\n";
											echo urldecode($lrow['state'])."<br>\n";
											if ($lrow['zip'] != "") echo $lrow['zip']."<br>\n";
											}
											else { echo urldecode($row['other_location'])."<br>\n";
													 if ($row['other_address'] != "")echo urldecode($row['other_address'])."<br>\n"; 
													 if ($row['other_city'] != "")echo urldecode($row['other_city'])."<br>\n"; 
													 if ($row['other_state'] != "")echo urldecode($row['other_state'])."<br>\n"; 
													 if ($row['other_zip'] != "")echo urldecode($row['other_zip'])."<br>\n"; }

?></span>
			</div>
			<? if ($row['caller'] != "") { ?>
			<div class=row>
				<span class=label2>Caller(s):</span>
				<span class=formw><? echo urldecode($row['caller']); ?></span>
			</div>
			<? }
			if ($row['cuer'] != "") { ?>
			<div class=row>
				<span class=label2>Cuer(s):</span>
				<span class=formw><? echo urldecode($row['cuer']); ?></span>
			</div>
			<? } 
			$sdprg = unserialize($row['sdprogram']);
			$rdprg = unserialize($row['rdprogram']);
			if (is_array($sdprg)) { ?>
				<div class=row>
					<span class=label2>Square Dance Program:</span>
					<span class=formw><? echo ucwords(implode(",",$sdprg)) ?></span>
				</div>
			<? }
			if (is_array($rdprg)) { ?>
				<div class=row>
					<span class=label2>Round Dance Program:</span>
					<span class=formw><? echo "Phase ".ucwords(implode(", Phase ",$rdprg)) ?></span>
				</div>
			
			<? }
			if ($row['description'] != "" ) {?>
				<div class=row>
					<span class=label2>Additional Information:</span>
					<span class=formw><? echo nl2br(urldecode($row['description'])) ?></span>
				</div>
			<? } ?>
			
<?		}
		else {
		$query = "select * from subdomain_schedule where subdomain_name='$subdom'  and `date` >= NOW() order by date";
		$result = @mysql_query($query);
		while ($row = @mysql_fetch_array($result)) { ?>
			<div class=row>
				<span class=label2>Date:</span>
				<span class=formw><? echo date("l, F j, Y",strtotime($row['date'])) ?></span>
			</div>
			<div class=row>
				<span class=label2>Time:</span>
				<span class=formw><? echo date("g:i a",strtotime($row['start_time']))?> - <? echo date("g:i a",strtotime($row['end_time']))?> </span>
			</div>
			<div class=row>
				<span class=label2>Name of Hall:</span>
				<span class=formw><? if (urldecode($row['location']) != "other") echo urldecode($row['location']);
											else { echo urldecode($row['other_location'])."<br>\n";
													 if ($row['other_address'] != "")echo urldecode($row['other_address'])."<br>\n"; }?></span>
			</div>
			<div class=row>
				<span class=fullwidth><a href=<? echo $PHP_SELF ?>?link=<?echo $link ?>&amp;ind=<?echo $row['ind']?>&amp;subdom=<? echo $subdom ?>>More Detail</a></span>
			</div>
			<hr>
		<?} }
	break;
	case 'news':
			$query = "select * from subdomain_news where subdomain_name='$subdom' order by date";
		$result = @mysql_query($query);
		while ($row = @mysql_fetch_array($result)) { ?>
			<div class=row>
				<span class=label2>Date:</span>
				<span class=formw><? echo date("l, F j, Y",strtotime($row['date'])) ?></span>
			</div>
			<div class=row>
				<span class=label2>Title:</span>
				<span class=formw><? echo urldecode($row['title']) ?></span>
			</div>
			<div class=row>
				<span class=label2>Description:</span>
				<span class=formw><? echo urldecode($row['description'])?></span>
			</div>
			<hr>
<?	} break;
	}
 ?>
<div class=clearer>&nbsp;</div>
</div>
</div>
</div>
</body>
</html>
