<?php
$subject = 'Western Wheelers Home';
$pageaddr = 'Western Wheelers';
$ver = '(1.0.0.0)';
include ('emailtracker.inc.php');
include('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>Western Wheelers</title>
	<style type="text/css">
	body, html {
		font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
		font-size: 100%;
		padding: 0;
		margin: 0;
	}
	
	.table {
		display: block;
		width: 100%;
	}
	.th {
		display: block;
		width: 50%;
		font-weight: bold;
		text-align: center;
		float: left;
	}

	.td {
		display: block;
		width: 49%;
		float: left;
	}
	.tr	{
		display: block;
		width: 100%;
		clear: both;
	}

	h1 {
		font-family: "Comic Sans MS",Verdana, Geneva, Arial, Helvetica, sans-serif;
		text-align: center;
		font-size: 300%;
	}
	
	#hdr {
		display: block;
		width:80%;
		margin-left: auto;
		margin-right: auto;
		margin-top: 1em;
		border-bottom: 1px solid black;
	}

	#tbldiv {
		display: block;
		width: 50%;
		margin-left:auto;
		margin-right:auto;
                border: 1px solid black;
                margin-top: 1em;
                padding: 0.5em;
	}
	.clearer {
		clear: both;
		line-height: 0.01 em;
	}
	</style>
</head>

<body>
<div id="hdr">
<h1>Western Wheelers</h1>
</div>
<div id="tbldiv">

<?php
        $tmp = array();
        $tmp[] = '<div class="table"><span class="th">Date</span><span class="th">Caller</span>';
        $q = "SELECT event_name, event_date, event_caller, ind FROM `new_events` WHERE event_org = 'Western Wheelers' and event_date > NOW() and `event_type` like '%dance%' order by event_date";
        $rs = mysql_query($q);
        if (mysql_num_rows($rs) == 0)
                $tmp[] = '<td style="color:red;font-weight:bold;text-align:center">Sorry no dances scheduled</td>';
        else
                while ($rw = mysql_fetch_assoc($rs)) {
                        $tmp[] = '<div class="tr"><span class="td"><a href="http://www.nnjsda.org/new_calendar.php?direct=1&evid=' . $rw['ind'] . '">' . date('l, F jS, Y',strtotime($rw['event_date'])) . '</a></span><span class="td">' . $rw['event_caller'] . '</span></div>';
                }
	$tmp[] = '<div class="clear">&nbsp;</div>';
        $tmp[] = '</div>';
        echo implode("\n",$tmp);
?>
</div>


</body>
</html>
