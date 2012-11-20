<?php
$subject = 'NNJSDA History';
$ver = '(v1.0.6)';
$pageaddr = 'nnjsda.history';
include('../emailtracker.inc.php');
if (isset($_POST['trace'])) {
	exit('ok');
}

if (isset($_GET['pdf']) || isset($_GET['clubs'])) {
	$fn = (isset($_GET['pdf']))?'History of the NNJSDA.pdf':'NNJSDA Membership Through the Years.pdf';
	if (!file_exists($fn)) {
		exit();
	}
	$fs = filesize($fn);
	header("Pragma: public"); // required
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false); // required for certain browsers
	header("Content-Transfer-Encoding: binary");
	header("Content-Type: application/pdf");
	header("Content-Length: " . $fs);
	header('Content-Disposition: inline; filename="$fn";' );

	// Send data
	readfile($fn);
	exit();
}
include('../dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname);
$history = array();
$tabs = array();
$q = "select period,description from history";
$rs = mysql_query($q);
while ($rw = mysql_fetch_assoc($rs)) {
	$history[$rw['period']] = $rw['description'];
	$tabs[] = "<li><a href='#tabs-{$rw['period']}'>" . str_replace('_',' ',$rw['period']) . "</a></li>";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>NNJSDA History</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css" type="text/css">
		<link rel="stylesheet" type="text/css" href="../fancybox/fancybox/jquery.fancybox-1.3.1.css" media="screen" />
		<style type="text/css">
			body, html {
				font-size:100%;
				font-family:Verdana, Geneva, sans-serif;
				padding:0;
				margin:0;
			}

			#hdr {
				display:block;
				width: 95%;
				margin-left:auto;
				margin-right:auto;
				border-bottom: 1px solid black;
			}

			#rop {
				display:block;
				width: 95%;
				margin-left:auto;
				margin-right:auto;
				margin-top:0.5em;
			}

			h1, h3 {
				text-align:center
			}

			h2 {
				text-align: center
			}

			.even {
				background-color:#FFCACA;
			}

			.odd {
				background-color:#FFDFDF;
			}


			a.norm:link, a:active, a:visited {
				background-color: transparent;
				color: #0000FF;
				font-weight: bold;
				text-decoration: none;
			}

			a.norm:hover {
				background-color: #0000FF;
				color: #FFFFFF;
				text-decoration: none;
			}
			/* Vertical Tabs
			----------------------------------*/
			.ui-tabs-vertical { width: 100%; }
			.ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; text-align:center; float: left; width: 10%; }
			.ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
			.ui-tabs-vertical .ui-tabs-nav li a { display:block;font-family:Verdana, Geneva, sans-serif;font-size:90% }
			.ui-tabs-vertical .ui-tabs-nav li.ui-tabs-selected { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
			.ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: left; width: 80%;}
		</style>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.js"></script>
		<script type="text/javascript" src="../fancybox/fancybox/jquery.mousewheel-3.0.2.pack.js"></script>
		<script type="text/javascript" src="../fancybox/fancybox/jquery.fancybox-1.3.1.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
	      $("#tabs").tabs(
					  {select: function(event, ui) {
					  	_gaq.push(['_trackEvent', 'History', 'Tab Clicked', ui.tab.firstChild.data]);
						  $.post("<?php echo $_SERVER['PHP_SELF'] ?>", {trace: 1, tab_clicked: ui.tab.firstChild.data});
						  if (ui.tab.firstChild.data == 'NNJSDA Home Page') {
						  	window.location = 'http://nnjsda.org/';
						  }
						}
					  }
					  ).addClass('ui-tabs-vertical ui-helper-clearfix');
		  	$("#tabs li").removeClass('ui-corner-top').addClass('ui-corner-left');
	      $(".pdf").click(function() {
	      	_gaq.push(['_trackEvent', 'Show', 'History', $(this).attr('id')]);
	        });
  		});
  	</script>
  	<?php include('../ga.inc.php'); ?>
	</head>
	<body>
		<div id="hdr">
			<h1>History of the NNJSDA</h1>
		</div>
		<div id="rop">
			<p>This history of the NNJSDA provides highlights from the beginning in 1958 to the present. A PDF version incorporating all the years into one document <a class="pdf" id="History of the NNJSDA.pdf" href="?pdf=1" target="_blank">can be found here</a>.</p>
			<p>A PDF document containing a history of when clubs entered and left the NNJSDA <a class="pdf" id="NNJSDA Membership Through the Years.pdf" href="?clubs=1" target="_blank">can be found here</a>. This document was compiled for the 35<sup>th</sup> anniversary of the NNJSDA, so it's not up to date. Updates and corrections will be appreciated.</p>
		<div id="tabs">
		   <ul>
<?php
	echo implode("\n",$tabs) . "\n";
?>
				<li><a href="#tabs-home">NNJSDA Home Page</a></li>
			</ul>
<?php
	$tmp = array();
	foreach ($history as $p => $d) {
		$tmp[] = "<div id='tabs-{$p}'>";
		$tmp[] = "<h2>" . str_replace('_',' ',$p) . "</h2>";
		$tmp[] = '<p>';
		$tmp[] = $d;
		$tmp[] = '</p>';
		$tmp[] = '</div>';
	}
	$tmp[] = '<div id="tabs-home"></div>';
	echo implode("\n",$tmp) . "\n";
?>
		</div>
	</body>
</html>
