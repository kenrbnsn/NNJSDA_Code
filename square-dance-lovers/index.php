<?php
	$subject = 'Square Dance Lovers';
	$ver = "(v0.0.4)";
	$pageaddr = 'square.dance.lovers';
	include ('../emailtracker.inc.php');
	if (isset($_POST['trace'])) {
		exit(json_encode(array('ret'=>'ok')));
	}
	include('../dbconfig.php');
	$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
	$db = mysql_select_db($dbname);
	$q = "select `names`, `filename`, `notes` from `lovers` order by `sort_by_lastname`";
	$rs = mysql_query($q) or die("Problem with the query: $q<br>" . mysql_error());
	$tmp = array();
	while ($rw = mysql_fetch_assoc($rs)) {
		$title = $rw['names'];
		if ($rw['notes'] != '') {
			$title .= " {$rw['notes']}";
		}
		$tmp[] = "<a class='click_link' href='{$rw['filename']}' target='_blank'>$title</a></br>";
	}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>NNJSDA Square Dance Lovers</title>
		<style type="text/css">
			body, html {
				font-size:100%;
				font-family:Arial, Helvetica, sans-serif;
				padding:0;
				margin:0;
			}
			a {
				text-decoration: none;
			}

			a:visited {
				color: blue;
			}

			a:hover {
				font-weight: bold;
			}

			h1 {
				text-align: center;
			}

			p {
				display: block;
				width: 60%;
				margin-right: auto;
				margin-left: auto;
			}
		</style>
		<link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/themes/start/jquery-ui.css">
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript">
	    google.load("jquery", "1.7.1", {uncompressed:true});
	  </script>
		<script type="text/javascript">
			$(document).ready(function() {
	  		$('.click_link').click(function() {
	  			_gaq.push(['_trackEvent', 'Click', 'link', $(this).attr('href')]);
	  			$.post("<?php echo $_SERVER['PHP_SELF'] ?>",{trace:1, pdf: $(this).attr('href')});
	  		});
	  	});
	  </script>
	<?php include('../ga.inc.php') ?>
	</head>
	<body>
		<h1><span style="color:red">&hearts;</span><br>Square Dance Lovers<br>
			<span style="color:red">&hearts;</span></h1>
		<p>Twenty-one loving couples responded to the request for people who met and married through square dancing.Summaries of their stories were printed in the January 2012 issue of <span style="font-style:italic">Grand Square</span> (Vol. 54, No. 2).<br><br>
			<?php echo implode("\n",$tmp) . "\n" ?>
		</p>
	</body>
</html>

