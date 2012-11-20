<?php
include ('mfinit.inc.php');
$subject = $which1 . ' Mini Festival (' . $when . ') Map';
$pageaddr = 'minifestivalmap';
$ver = "(2.0.3)";
include ('../emailtracker.php.inc');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><? echo $which1 . ' Mini Festival ' . 'Directions'?></title>
		<?php $css = (file_exists('../nnjsda_new.css'))?'../nnjsda_new.css':'nnjsda_new.css'; ?>
		<LINK rel="STYLESHEET" type="text/css" href="<?php echo $css; ?>">
	</head>
	<body>
		<div id="hdr">
			<h1><? echo $which1 . ' Mini Festival ' . 'Directions'?></h1>
		</div>
		<div id="restofpage">
			<div id="lhcol">
				<div id="nav">
					<span style="font-weight:bold; font-size:115%; text-align:center; width:100%; display:block">Traveling</span>
					<ul>
						<li><a href="<? echo $_SERVER['PHP_SELF'] ?>?dir=1">West on Route 22</a></li>
						<li><a href="<? echo $_SERVER['PHP_SELF'] ?>?dir=2">East on Route 22</a></li>
						<li><a href="<? echo $_SERVER['PHP_SELF'] ?>?dir=3">North on Route 287</a></li>
						<li><a href="<? echo $_SERVER['PHP_SELF'] ?>?dir=4">South on Route 287</a></li>
						<li><a href="<? echo $_SERVER['PHP_SELF'] ?>?dir=0">Don't Know</a></li>
					</ul>
				</div>
			</div>
			<div id="rhcol">
				<h2>Directions</h2>
				<?php
				$dir = (isset($_GET['dir']))?$_GET['dir']:0;
				if ($dir == 1 || $dir == 0) { ?>
				<p>Going <span class=bold>west on Route 22</span>, take the Manville turnoff. At the stop sign, turn left on to Foothill Road. Continue approximately ½ mile on Foothill Rd. to the school on your left (Merriwood Road.)</p>
				<?php }
					if ($dir == 2 || $dir == 0) {
				?>
				<p>Going <span class=bold>east on Route 22</span>, take Finderne Avenue exit, turn right on to Foothill Road. Continue on Foothill Road approximately ½ mile to school on your left (Merriwood Road.)</p>
				<?php
					}
						if ($dir == 3 || $dir == 0) {
				?>
				<p>Going <span class=bold>north on Route 287</span>, take exit 13B (Rt. 28 West, Somerville).  Make a right at the light at the end of the ramp.  Make another right at second light (Finderne Avenue), then first left (Foothill Rd.) at traffic light – continue approximately 1 mile to the school on left (Merriwood Road.)</p>
				<?php
						}
						if ($dir == 4 || $dir == 0) {
				?>
				<p>Going <span class=bold>south on Route 287</span> take exit 17 (Somerville, Flemington 202/206 South.) Go ¼ mile and make a right onto Commons Way. Go to light and make a left. Go to third light and make a left onto Prince Rodgers and go approximately 1½ miles to the school. There will be a DO NOT ENTER sign. Make a left – go to a "T" and turn right onto Foothill road.  Go approximately ¼ mile to Merriwood Road on the right.</p>
				<?php
						}
				?>
			</div>
		</div>
	</body>
</html>