<?php
$subject = 'Motiv8ors Home';
$pageaddr = 'motiv8ors';
$ver = '(1.0.0.11)';
if (!empty($_GET)) {
	while (list($key, $val) = each($_GET))
	if (stristr($val,'http://') !== false || stristr($val,'ftp://') !== false || stristr($val,'[url') !== false || stristr($val,'jatest') !== false || $key == 'option' || $key == 'controller' || stristr($val,'../../')) $problem_para[] = $key;
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
		$body = "Referer: " . $_SERVER['HTTP_REFERER'] . "\n";
		$body .= "Remote Address:" . $host . "\n";
		$body .= "HTTP_USER_AGENT = " . $_SERVER['HTTP_USER_AGENT'] . "\r\n";
		for ($i=0;$i<count($problem_para);$i++)
		$body .= 'Get Parameter <' . $problem_para[$i] . '> = ' . stripslashes($_GET[$problem_para[$i]]) . "\n";
		@mail("kenrbnsn@kis-hosting.com","Error invoking Motiv8ors Home Page",$body,"From: Visit Tracker <tracker@nnjsda.org>",'-f problems@nnjsda.org');
		@mail('ban.ip@kis-hosting.com',$IP,$_SERVER['HTTP_HOST'],'From: index@motiv8ors.nnjsda.org','-f index@motiv8ors.nnjsda.org');
		exit();
	}
}
include ('emailtracker.inc.php');
include('dbconfig.php');
$sp_dates = array(strtotime('2009-02-14'),strtotime('2009-03-16'));
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>Motiv8ors</title>
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
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript">
	    google.load("jquery", "1.7", {uncompressed:true});
	    </script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('a').click(function() {
				_gaq.push(['_trackEvent', 'Motiv8ors Link', 'Clicked', $(this).text()]);
				return(true);
			});
		});
	</script>
	<?php include('ga.inc.php'); ?>
</head>

<body>
<div id="hdr">
<h1>Motiv8ors</h1>
</div>
<div id="tbldiv">
<p style="text-align:center;border-bottom: 1px solid black;padding-bottom:0.5em"><span style="font-weight:bold">NEW JERSEY'S PREMIER ADVANCED & CHALLENGE CLUB</span><br>
Sundays at the Borough Hall of Oradell<br>
355 Kinderkamack Road, Oradell, NJ 07649<br>
C1 Program &mdash; 6:30-7:30 pm, A2 Program &mdash; 7:30-9:30 pm<br>
<a href="http://maps.google.com/maps?q=355%20Kinderkamack%20Road,%20Oradell,%20NJ%2007649">Get directions via Google Maps</a></p>
<?php
        $tmp = array();
        $tmp[] = '<div class="table"><span class="th">Date</span><span class="th">Caller</span>';
        $q = "SELECT event_name, event_date, event_caller, ind FROM `new_events` WHERE event_org = 'Motiv8ors' and event_date >= NOW() and `event_type` like '%dance%'  and `canceled` = 'no' order by event_date";
        $rs = mysql_query($q);
        if (mysql_num_rows($rs) == 0)
                $tmp[] = '<td style="color:red;font-weight:bold;text-align:center">Sorry no dances scheduled</td>';
        else
                while ($rw = mysql_fetch_assoc($rs)) {
                				if ($rw['event_date'] == '2012-04-22') {
                					$tmp[] = '<div style="clear:both;border:2px solid red;padding:0.5em;margin-top:0.5em;margin-bottom:0.5em;">';
                				}
                        $tmp[] = '<div class="tr"><span class="td"><a href="http://www.nnjsda.org/new_calendar.php?direct=1&evid=' . $rw['ind'] . '">' . date('l, F jS, Y',strtotime($rw['event_date'])) . '</a></span><span class="td">' . $rw['event_caller'] . '</span></div>';
                        if ($rw['event_date'] == '2012-04-22') {
                        	$tmp[] = '<div class="tr"><span style="text-align:center"><span style="font-weight:bold"><span style="color:red">*** Special Schedule</span>&nbsp;&nbsp;&nbsp;C1</span> 3:00-5:00 pm&nbsp;&nbsp;&nbsp;<span style="font-weight:bold">A2</span> 7:00-9:30 pm</span></div>';
                        	$tmp[] = '</div>';
                        }
                }
	$tmp[] = '<div class="clear">&nbsp;</div>';
        $tmp[] = '</div>';
        echo implode("\n",$tmp);
?>
<p style="text-align:center;border-top: 1px solid black;">Singles Welcome<br>Casual Attire<br>Computer Squares<br>
<a href="mailto:charartist@aol.com">Chariss & Carl</a>: 201-489-2498<br><a href="mailto:LTPT1@yahoo.com">Jan & Louis</a>: 732-577-9413
</p>
</div>


</body>
</html>
