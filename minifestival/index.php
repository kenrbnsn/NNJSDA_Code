<?php
include('mfinit.inc.php');
$subject = $which1 . ' Mini Festival (' . $when . ') Home';
$pageaddr = 'index';
$ver = '(v4.0.16)';
include ('emailtracker.inc.php');
if (isset($_POST['trace'])) {
	if ($_POST['trace'] == '1') {
		exit(json_encode(array('ret'=>'ok')));
	}
	if ($_POST['trace'] == '2') {
		exit(json_encode(array('ret'=>'ok',url=>$_POST['addr'])));
	}
	exit(json_encode(array('ret'=>'Bad trace')));
}

if (isset($_GET['reg']) &&
	(($_GET['reg'] == 'pdf') || ($_GET['reg'] == 'rtf'))) {
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0");
		header("Content-type: application/".$_GET['reg']);
//		header("Content-Type: application/force-download");
		header("Content-Disposition: inline; filename=minifestival_registration.".$_GET['reg']);
		header('Content-Length: '.filesize('./minifestival' . $when_year . '.' . $_GET['reg']));
		header("Content-Transfer-Encoding: binary");
		$file = fopen('./minifestival' . $when_year . '.' . $_GET['reg'],'r');
		fpassthru($file);
		@fclose($file);
		exit();
}
$callers = array('Vic Ceder','Don Beck','Butch Adams');
$sched1 = array('1:45 - 2:10' => array('a'=>$callers[0],'b'=>$callers[1],'c'=>$callers[2]),
					'2:15 - 2:40' => array('a'=>$callers[2],'b'=>$callers[0],$callers[1]),
					'2:45 - 3:10' => array('a'=>$callers[1],'b'=>$callers[2],$callers[0]),
					'3:15 - 3:40' => array('a'=>$callers[0],'b'=>$callers[1],$callers[2]),
);
$sched2 = array('4:05 - 4:30' => array('a'=>$callers[2],'b'=>$callers[0],'c'=>$callers[1]),
					'4:35 - 5:00' => array('a'=>$callers[1],'b'=>$callers[2],$callers[0]),
					'5:05 - 5:30' => array('a'=>$callers[0],'b'=>$callers[1],$callers[2])
);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html>
<head>
	<title>NNJSDA -- <?php echo $which1 ?> Annual Mini Festival</title>
	<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/themes/base/jquery-ui.css" type="text/css">
	<link rel="stylesheet" type="text/css" href="http://www.nnjsda.org/nnjsda.css" media="screen">
	<link rel="stylesheet" type="text/css" href="http://www.nnjsda.org/printnnjsda.css" media="print">
	<style type="text/css">
		a#raffle:hover {
			text-decoration: none;
			background-color: white;
			color: white;
		}
		a#prev_pic:hover {
			text-decoration: none;
			background-color: white;
			color: white;
		}
		a#callers_bios:hover, a#mf_directions:hover {
			text-decoration: none;
			background-color: white;
			color: white;
		}

	</style>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("jquery", "1.7", {uncompressed:true});
    google.load("jqueryui", "1.8.0", {uncompressed:true});
    </script>
<script type="text/javascript">
		$(document).ready(function() {
		$('#mf_rest_map').dialog({
													  width:680,
													  height:550,
													  modal:false,
													  bgiframe: true,
													  autoOpen: false,
													  buttons: {
													  	Ok : function() {
													  		$(this).dialog('close');
													  	}
													  }
													});
		$('#schedule').dialog({
													  modal:false,
													  width: 680,
													  bgiframe: true,
													  autoOpen: false,
													  modal: true,
													  buttons: {
													  	Close : function() {
													  		$(this).dialog('close');
													  	}
													  }
													});
$('#schedule_btn').click(function() {
	_gaq.push(['_trackEvent', 'Button', 'Pushed', 'schedule']);
   $.post("<?php echo $_SERVER['PHP_SELF'] ?>",
            {trace:1, button_pushed: 'schedule_btn'});
   $('#schedule').dialog('open');
   });
$('#rest').click(function() {
	_gaq.push(['_trackEvent', 'Button', 'Pushed', 'rest']);
	$.post("<?php echo $_SERVER['PHP_SELF'] ?>",
						{trace:2 , button_pushed: 'rest',
												addr: $(this).attr('url')},
						 function(data) {
						 						window.location = data.url;
						 						},
						 						"json");
   });
$('#mf_directions').click(function() {
	_gaq.push(['_trackEvent', 'Button', 'Pushed', 'mf_directions']);
	$.post("<?php echo $_SERVER['PHP_SELF'] ?>",
						{trace:2 , button_pushed: 'mf_directions',
												addr: $(this).attr('href')},
						 function(data) {
//						 						window.location = data.url;
						 						},
						 						"json");
						 						});
$('#reg_form').click(function() {
	_gaq.push(['_trackEvent', 'Button', 'Pushed', 'reg_form']);
	$.post("<?php echo $_SERVER['PHP_SELF'] ?>",
						{trace:2 , button_pushed: 'reg_form',
												addr: $(this).attr('url')},
						 function(data) {
						 						window.location = data.url;
						 						},
						 						"json");
						 						});
$('#print_sched').click(function() {
	_gaq.push(['_trackEvent', 'Button', 'Pushed', 'print_sched']);
	$.post("<?php echo $_SERVER['PHP_SELF'] ?>",
						{trace:2 , button_pushed: 'print_sched',
												addr: $(this).attr('url')},
						 function(data) {
						 						window.location = data.url;
						 						},
						 						"json");
						 						});
	$('.bio').click(function() {
		_gaq.push(['_trackEvent', 'Button', 'Pushed', $(this).attr('id')]);
		$.post("<?php echo $_SERVER['PHP_SELF'] ?>",
				{ trace: 2, button_pushed: $(this).attr('id') + ' bio', addr: $(this).attr('url')},
				function(data) {
					window.location = data.url;
				}, "json");
	});
	$('#raffle').click(function() {
		_gaq.push(['_trackEvent', 'Button', 'Pushed', $(this).attr('id')]);
		if (navigator.appName == 'Microsoft Internet Explorer') {
			window.location = $(this).attr('href');
			return false;
		} else {
			return true;
		}
	});
	$('#prev_pic').click(function() {
		_gaq.push(['_trackEvent', 'Button', 'Pushed', $(this).attr('id')]);
		if (navigator.appName == 'Microsoft Internet Explorer') {
			window.location = $(this).attr('href');
			return false;
		} else {
			return true;
		}
	});
	$('#callers_bios').click(function() {
		_gaq.push(['_trackEvent', 'Button', 'Pushed', $(this).attr('id')]);
		if (navigator.appName == 'Microsoft Internet Explorer') {
			window.location = $(this).attr('href');
			return false;
		} else {
			return true;
		}
	});
	$('#nnjsda_home').click(function() {
		_gaq.push(['_trackEvent', 'Button', 'Pushed', $(this).attr('id')]);
		$.post("<?php echo $_SERVER['PHP_SELF'] ?>",
				{ trace: 2, button_pushed: $(this).attr('id'), addr: $(this).attr('url')},
				function(data) {
					window.location = data.url;
				}, "json");
	});
});
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-7145849-1']);
  _gaq.push(['_setDomainName', 'nnjsda.org']);
  _gaq.push(['_setAllowHash', false]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>

<body>
<div id="hdr">
<h2><? echo $which ?> Annual</h2>
<h1>Mini-Festival</h1>
<h2><? echo $when ?><br> 1:00 - 6:30 pm</h2>
</div>
<div id="rest-of-page">
<span class=location><?php echo $location ?></span>
<span class=maincaller><br><?php echo $maincaller ?></span><span class=callercuerloc><?php echo $maincallerloc ?></span>
<span class=othercaller><br><?php echo $othercaller1 ?></span><span class=callercuerloc><?php echo $othercallerloc1 ?></span>
<span class=othercaller><br><?php echo $othercaller2 ?></span><span class=callercuerloc><?php echo $othercallerloc2 ?></span>
<div class="spacer"><p>&nbsp;</p></div>
<div class=cuer>
<span class=callercuerloc>Rounds By:</span>
<span class=cuername><? echo $cuer ?></span><span class=callercuerloc><? echo $cuerloc ?></span>
</div>
<div class="spacer"><p>&nbsp;</p></div>
<div class=centerblock>
	<!--
	<p><span class="bold" style="text-decoration:underline;font-size:150%;color:red">New Schedule! All Halls 1:00 PM - 6:00 PM</span></p>
	-->
<p><span class="bold">**** 3 Halls -- Mainstream, Plus, Advanced ****</span><br>
DBD MS/Fast Plus, Challenge, Rounds by Request 5:30 - 6:30</p>
<p><span class=onesquare>ONE SQUARE IS FAIR</span><br>&nbsp;</p>
<p>Info: <a href="mailto:minifestival.info@nnjsda.org?subject=Please%20Send%20Information%20about%20the%20<?echo $which1?>%20Mini-Festival">Ken Robinson</a> 908-963-2447</p>
<p>Registrar: Georgi Flandera 973-427-2889<br><br>
<span style="font-weight:bold">LIGHT</span> Soft Sole Shoes ONLY -- NO Boots Allowed</p></div>
<div class="clearer">&nbsp;</div>
</div>
<div class="bottomarea" style="font-size:80%">
<?
echo "Page Last modified: " . date ("F j, Y", getlastmod()) . " at " . date ("g:i a.", getlastmod()) ; ?></div>
</div>
<?php
 $sched_title = ($sched_year == $when_year)?"$sched_year Mini Festival Schedule":"$sched_year Mini Festival Schedule";
 ?>
<div id="schedule" title="<?php echo $sched_title ?>">
	<div class="bd">
		<table cellspacing="0">
			<tr>
				<td valign="middle" style="padding:0.5em;text-align:center;font-weight:bold;border:1px solid black">Time</td>
				<td valign="top" style="padding:0.5em;text-align:center;font-weight:bold;border:1px solid black">Hall A<br>Plus and Rounds</td>
				<td valign="top" style="padding:0.5em;text-align:center;font-weight:bold;border:1px solid black">Hall B<br>Mainstream</td>
				<td valign="top" style="padding:0.5em;text-align:center;font-weight:bold;border:1px solid black">Hall C<br>Advanced</td>
			</tr>
			<tr>
				<td valign="middle" style="padding:0.5em;text-align:center;font-weight:bold;border:1px solid black">1:00 - 1:10</td>
				<td valign="top" colspan="3" style="padding:0.5em;text-align:left;font-weight:normal;border:1px solid black"><span style="font-weight:bold">Hall A</span> -- Introduction of Callers<br>National Anthem -- Pledge of Allegiance<br>
		Announcements</td>
			</tr>
			<tr>
				<td valign="middle" style="padding:0.5em;text-align:center;font-weight:bold;border:1px solid black">1:15 - 1:40</td>
				<td valign="top" colspan="3" style="padding:0.5em;text-align:left;font-weight:normal;border:1px solid black"><span style="font-weight:bold">Hall A</span> -- Mainstream,<br>
			<?php echo implode(', ',$callers) ?></td>
			</tr>
		<?php
			$tmp = array();
			foreach ($sched1 as $time => $halls) {
				$tmp[] = '<tr>';
				$tmp[] = '<td valign="middle" style="padding:0.5em;text-align:center;font-weight:bold;border:1px solid black">' . $time . '</td>';
				foreach ($halls as $hall => $caller)
					$tmp[] = '<td valign="top" style="padding:0.5em;text-align:left;font-weight:normal;border:1px solid black">' . $caller . '</td>';
				$tmp[] = '</tr>';
			}
			echo implode("\n",$tmp) . "\n";
		?>
			<tr>
				<td valign="middle" style="padding:0.5em;text-align:center;font-weight:bold;border:1px solid black">3:45 - 4:00</td>
				<td valign="top" colspan="3" style="padding:0.5em;text-align:left;font-weight:normal;border:1px solid black"><span style="font-weight:bold">Hall A</span> -- Drawing of 50/50<br>
		(Raffle winners will be posted at 3:30 pm. -- Claim prizes by 5:00 pm.)</td>
			</tr>
		<?php
			$tmp = array();
			foreach ($sched2 as $time => $halls) {
				$tmp[] = '<tr>';
				$tmp[] = '<td valign="middle" style="padding:0.5em;text-align:center;font-weight:bold;border:1px solid black">' . $time . '</td>';
				foreach ($halls as $hall => $caller)
					$tmp[] = '<td valign="top" style="padding:0.5em;text-align:left;font-weight:normal;border:1px solid black">' . $caller . '</td>';
				$tmp[] = '</tr>';
			}
			echo implode("\n",$tmp) . "\n";
		?>
			<tr>
				<td valign="middle" style="padding:0.5em;text-align:center;font-weight:bold;border:1px solid black">5:35 - 6:00</td>
				<td valign="top" colspan="3" style="padding:0.5em;text-align:left;font-weight:normal;border:1px solid black"><span style="font-weight:bold">Hall A</span> -- Mainstream,<br>
		<?php echo implode(', ',$callers) ?></td>
			</tr>
			<tr>
				<td valign="middle" style="padding:0.5em;text-align:center;font-weight:bold;border:1px solid black">5:00 - 6:30</td>
				<td valign="top" colspan="3" style="padding:0.5em;text-align:left;font-weight:normal;border:1px solid black"><span style="font-weight:bold">6:05 - 6:30 Hall A</span> - Rounds by Request -- Mary Pickett<br>
		<span style="font-weight:bold">6:05 - 6:30 Hall B</span> - C1 Vic Ceder
		</td>
			</tr>
		</table>
	</div>
</div>
</body>
</html>
