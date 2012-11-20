<?php
$subject = 'NNJSDA Grand Square Statistics';
$ver = "(1.0.8.2)";
$pageaddr = 'gs.stats';
include ('../emailtracker.inc.php');
include ('../dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>Grand Square Download Stats</title>
	<style type="text/css">
	body, html {
		padding: 0;
		margin: 0;
		font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
		font-size: 100%;
	}

	.hdr {
		width: 95%;
		margin-left: auto;
		margin-right: auto;
		border-bottom: 1px solid black;
		margin-bottom: 0.5em;
		border-bottom: 1px solid black;
	}

	h1 {
		text-align: center;
		font-size: 300%;
	}

	.rop {
		display: block;
		width: 95%;
		margin-left: auto;
		margin-right: auto;
		margin-top: 1em;
	}
	</style>
</head>

<body>
<div class="hdr">
<h1>Grand Square<br>Download Statistics</h1>
</div>
<div class="rop">
<?php
$q = "select description, times_dl, last_dl, last_dl_ip from grand_square_issues order by order_by";
$rs = mysql_query($q) or die("Problem with the query: $q<br>" . mysql_error());
while ($rw = mysql_fetch_assoc($rs)) {
	if ($rw['times_dl'] > 0) {
		echo '<span style="font-weight:bold">' . $rw['description'] . '</span><br>Number Downloads: ' . $rw['times_dl'] . '<br>Last Download: ' . date('l, F jS, Y \a\t g:i A',strtotime($rw['last_dl'])) . "<br>";
		$host = @gethostbyaddr($rw['last_dl_ip']);
		$disp_host = ($host != '')?$host:$rw['last_dl_ip'];
		if ($rw['last_dl_ip'] != '') echo 'Last download IP: <span style="color:blue;font-weight:bold">' . $disp_host . "</span><br>";
		echo "<br>\n";
	}
}
?>
</div>
<?php include('../ga.inc.php') ?>
</body>
</html>
