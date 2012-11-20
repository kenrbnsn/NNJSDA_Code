<?php
$subject = 'NNJSDA Winning Poster';
$ver = "(2012.6)";
$pageaddr = 'winningposter';
$winning_year = '2012';
$winning_poster = "nnjsda_{$winning_year}_poster";
$winning_author = 'Connie Pyne';
$other_posters = array('nnjsda_2011_poster'=>array('pdf'=>true,'doc'=>true,'jpg'=>true,'author'=>'Vicky Proskey'),
											 'nnjsda_2010_poster'=>array('pdf'=>true,'doc'=>true,'jpg'=>true,'author'=>'Jan Thompson'),
											 'nnjsda_2009_poster'=>array('pdf'=>true,'doc'=>true,'jpg'=>true,'author'=>'Bill Smythe'),
											 'nnjsda_2008_poster'=>array('pdf'=>true,'doc'=>true,'jpg'=>true,'author'=>'Margaret Kenter'),
											 'nnjsda_2007_poster'=>array('pdf'=>true,'doc'=>true,'jpg'=>true,'author'=>'Lise Greene'),
											 'nnjsda_2004_poster'=>array('pdf'=>true,'doc'=>true,'jpg'=>true,'author'=>'Jan Thompson'),
											 'pallet_poster'=>array('pdf'=>true,'doc'=>false,'jpg'=>true,'author'=>''),
											 'americas_heritage_poster'=>array('pdf'=>false,'doc'=>false,'jpg'=>true,'author'=>''),
											 'step_in_time_poster'=>array('pdf'=>false,'doc'=>false,'jpg'=>true,'author'=>''),
											 'puzzle_poster'=>array('pdf'=>true,'doc'=>false,'jpg'=>true,'author'=>''),
											 'magic_poster'=>array('pdf'=>true,'doc'=>false,'jpg'=>true,'author'=>'')
											 );
if (isset($_GET['mt'])) {
	makethumb($_GET['fn'],false);
	exit();
}
include ('../emailtracker.inc.php');

	function makethumb($fn,$rt=true)
	{
		list($ow, $oh, $type, $attr) = getimagesize($fn);
		$tw = 279;
		$th = $oh * (279/$ow);
//		$tw = $ow * ($pct/100);
		$w = round($tw);
		$h = round($th);
		$tn = imagecreatetruecolor($w, $h);
		$img = imagecreatefromjpeg($fn);
		imagecopyresampled($tn, $img, 0, 0, 0, 0, $w, $h, $ow, $oh);
		if ($rt)
			return (array($tn, $type, $w, $h));
		else {
   		header('Content-type: ' .image_type_to_mime_type($type));
			imagejpeg($tn);
			exit();
		}
	}

if (isset($_GET['type'])) {
	$mime_types = array('pdf' => 'application/pdf',
								  'doc' => 'application/msword',
								  'txt' => 'text/plain',
								  'rtf' => 'application/msword');
	if (!array_key_exists($_GET['type'],$mime_types)) {
		exit();
	}
	$fn = "{$_GET['fn']}.{$_GET['type']}";
	if (!file_exists($fn)) {
		exit();
	}
	$mt = $mime_types[$_GET['type']];
	$fs = filesize($fn);
	header("Pragma: public"); // required
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false); // required for certain browsers
	header("Content-Transfer-Encoding: binary");
	header("Content-Type: " . $mt);
	header("Content-Length: " . $fs);
	header('Content-Disposition: inline; filename="' . $fn . '";' );
	// Send data
	readfile($fn);
	exit();
}
$download_type = array('pdf','rtf','doc');

$which = (isset($_GET['which']))?$_GET['which']:0;
$which = (isset($_POST['which']))?$_POST['which']:$which;
$error = '';
if (isset($errorflds)) unset($errorflds);
switch ($which) {
case '0':
	$other = "?which=1";
	$link = 'See a larger picture of the poster';
	break;
case '1':
	$other = '?which=0';
	$link = 'See a smaller picture of the poster';
	break;
case '2':
	break;
case '3':
	header("Content-type: application/".$download_type[$_POST['format']]);
	header("Content-Type: application/force-download");
	header("Content-Disposition: attachment; filename=" . $winning_poster . '.' . $download_type[$_POST['format']]);
	header("Content-Length: ".filesize('./' . $winning_poster . '.' . $download_type[$_POST['format']]));
	header("Content-Transfer-Encoding: binary");
	$file = fopen('./' . $winning_poster . '.' .$download_type[$_POST['format']],'r');
	fpassthru($file);
	@fclose($file);
	break;
default:
	$error = "This page was invoked incorrectly.<br>Sorry.";
}

function my_filesize($type,$wp) {
   // First check if the file exists.
  if(!file_exists('./' . $wp . '.'.$type)) return(false);
   // Setup some common file size measurements.
  $kb = 1024;         // Kilobyte
   $mb = 1024 * $kb;   // Megabyte
  $gb = 1024 * $mb;   // Gigabyte
   $tb = 1024 * $gb;   // Terabyte
   // Get the file size in bytes.
   $size = filesize('./' . $wp . '.'.$type);
   /* If it's less than a kb we just return the size, otherwise we keep going until
   the size is in the appropriate measurement range. */
   if($size < $kb) {
       return $size." B";
   }
   else if($size < $mb) {
      return round($size/$kb,2)." KB";
   }
   else if($size < $gb) {
       return round($size/$mb,2)." MB";
  }
   else if($size < $tb) {
       return round($size/$gb,2)." GB";
   }
   else {
      return round($size/$tb,2)." TB";
   }
}

function disp_val($n)
{
	if (isset($_REQUEST[$n])) echo 'value="'.htmlentities(stripslashes($_REQUEST[$n])).'"';
}

function display_other_posters($other_posters) {
	$tmp[] = '<h2>Previous Winning Posters</h2>';
	$tmp[] = '<div style="display:block;width:100%;margin-right:auto;margin-left:auto;padding-top:2em;border-top:1px solid black;">';
	foreach($other_posters as $poster => $avail_types) {
		list($dmy1,$dmy2,$width,$height) = makethumb($poster . '.jpg');
		$spw = $width + 10;
		$sph = $height + 2;
		$tmp[] = '<div style="display:block;float:left;width:' . $spw . 'px;height:435px;"><img style="padding-left:5px;margin-left:auto;margin-right:auto;width:' . $width . 'px" src="' .  $_SERVER['php_self'] . '?mt=1&fn=' . $poster . '.jpg" width="' .  $width . '" height="' .  $height . '">';
		$tmp1 = array();
		if ($avail_types['author'] != '') {
			$tmp[] = "Poster Winner: {$avail_types['author']}";
		}
		if ($avail_types['pdf'] && file_exists("{$poster}.pdf")) {
			$tmp1[] = 'Download <a href="?type=pdf&fn=' . $poster . '">PDF Copy</a>';
		}
		if ($avail_types['doc'] && file_exists("{$poster}.doc")) {
			$tmp1[] = 'Download <a href="?type=doc&fn=' . $poster . '">Word Copy</a>';
		}
		$tmp[] = implode("\n<br>",$tmp1) . "\n";
		$tmp[] = '</div>';
	}
	$tmp[] = '<div class="clearer">&nbsp</div>';
	$tmp[] = '</div>';
	$tmp[] = '</div>';
	return (implode("\n",$tmp));
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html>
<head>
	<title>NNJSDA Poster Contest -- Winning Poster</title>
	<link rel="stylesheet" href="../nnjsda.css" type="text/css" media="screen">
	<link rel="stylesheet" href="../printnnjsda.css" type="text/css" media="print">
	<style>
		.aboxcenter {
			display: block;
			height: <?php echo $height ?>px;
			margin-bottom: 5px;
			margin-left: auto;
			margin-right: auto;
			width: <?php echo $width ?>px;
			padding-bottom: 4px;
			padding-left: 2px;
			padding-right: 2px;
			padding-top: 2px;
			text-align: center;
		}
		.textfld {
			width: 98%;
			background-color: #D0D0D0;
			color: Black;
			padding-left: 0.5em;
		}
	</style>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript">
		google.load("jquery", "1.7.2", {uncompressed:true});
		google.load("jqueryui", "1.8.19", {uncompressed:true});
	</script>
	<script type="text/javascript">
	$(document).ready(function() {
		$('a').click(function() {
			_gaq.push(['_trackEvent', 'Click', 'Address', $(this).attr('href')]);
			return (true);
		});
	});
	</script>
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-7145849-1']);
	  _gaq.push(['_setDomainName', 'nnjsda.org']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
</head>
<body>
<div id=hdr>
<h1>NNJSDA Poster Contest<br>Winning Posters</h1>
</div>
<div id=restofpage>
<? if ($error != '') {?>
<p><span class=center style="color:red;font-size:200%"><? echo $error ?></span></p>
<? } else {
switch ($which) {
case '1':
	$tmp = array();
	list($dmy1,$dmy2,$dmy3, $wh) = getimagesize($winning_poster . '.jpg');
	$tmp[] = '<div class="aboxcenter"><img src="' .  $winning_poster . '.jpg" ' .  $wh . "><br>$winning_year Winning Poster<br>Poster Winner: $winning_author</div>";
	$tmp[] = '<div class=onweb>';
	$tmp[] = '<p><span class=center><a href="' .$other . '">' .  $link  . '</a><br><a href="?which=2">Download a Printable Version</a></span></p>';
	$tmp[] = '<hr>';
	$tmp[] = display_other_posters($other_posters);
	echo implode("\n",$tmp) . "\n";
	break;
case '0':
	$tmp = array();
	list($dmy1,$dmy2,$width,$height) = makethumb($winning_poster . '.jpg');
	$tmp[] = '<div class="aboxcenter"><img src="' .  $_SERVER['php_self'] . '?mt=1&fn=' . $winning_poster . '.jpg" width="' .  $width . '" height="' .  $height . '"><br>' . "$winning_year Winning Poster<br>Poater Winner: $winning_author</div>";
	$tmp[] = '<div class=onweb>';
	$tmp[] = '<p><span class=center><a href="' .$other . '">' .  $link  . '</a><br><a href="?which=2">Download a Printable Version</a></span></p>';
	$tmp[] = '<hr>';
	$tmp[] = display_other_posters($other_posters);
	echo implode("\n",$tmp) . "\n";
	break;
case '2': ?>
	<p>Please specify which format you wish to download:</p>
	<form class=quest style="width:60%" method="post" action="<? echo $_SERVER['PHP_SELF'] ?>">
	<input type="hidden" name="which" value=3>
	<div class="row">
		<span class=label2>Download Format:</span>
		<span class=formw>
<?php
		$tmpx = array();
		if (my_filesize('pdf',$winning_poster)) $tmpx[] = '<input type="radio" name="format" value=0 checked>&nbsp;Adobe Acrobat (PDF,  ' . my_filesize('pdf',$winning_poster) . ')<br>';
		if (my_filesize('rtf',$winning_poster)) $tmpx[] = '<input type="radio" name="format" value=1>&nbsp;Rich Text Format (RTF, ' . my_filesize('rtf',$winning_poster) . ')<br>';
		if (my_filesize('doc',$winning_poster)) $tmpx[] = '<input type="radio" name="format" value=2>&nbsp;Microsoft Word (DOC, ' . my_filesize('doc',$winning_poster) . ')';
		echo implode("\n",$tmpx) . "\n";
?>
		</span>
	</div>
	<div class=row>
		<span class=fullwidth><input type=submit name="submit" value="Download File"></span>
	</div>
	<div class=clearer>&nbsp;</div>
	</form>
<?	break;
	}
	} ?>
<div class="footer">
<hr>
<a href="../index.php">Return</a> to the NNJSDA Home Page
</div>
</div>

</body>
</html>
