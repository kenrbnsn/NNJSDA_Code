<?php
session_start();
include ('../yui_version.php');
	if (isset($_GET['i'])) {
		$image = exif_thumbnail($_GET['i'], $width, $height, $type);
		if ($image === false) {
		   	$tmp = makethumb($_GET['i'],false);
		} else {
				if (image_type_to_mime_type($type) != 'image/jpeg') 
					$tmp = makethumb($_GET['i'],false);
				else {
	   			header('Content-type: ' .image_type_to_mime_type($type));
	   			echo $image;
				}
		}
		exit();
	}
$do_debug = (isset($_GET['d']))?true:false;
if (isset($_POST['ajax']) && isset($_SESSION['do_debug'])) $do_debug = $_SESSION['do_debug'];
if (!isset($_SESSION['do_debug'])) $_SESSION['do_debug'] = $do_debug;
$ver = "(2.0.0)";
$subject = 'NNJSDA State Outfit';
$pageaddr = 'state.outfit';
include('../emailtracker.inc.php');

list($sa,$sv) = explode('/',$_SERVER['HTTP_USER_AGENT']);
if (strtolower($sa) == 'libwww-perl') {
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
		@mail('ban.ip@kis-hosting.com',$IP,$_SERVER['HTTP_HOST'] . "\n" . $_SERVER['HTTP_USER_AGENT'],'From: ' . $pageaddr . '@nnjsda.org','-f ' . $pageaddr . '@nnjsda.org');
		exit();
}
if (!empty($_POST)) {
	while (list($key, $val) = each($_POST))
		if (stristr($val,'http://') !== false || stristr($val,'ftp://') !== false) $problem_para[] = $key;
	if (isset($problem_para)) {
		@mail('ban.ip@kis-hosting.com',$IP,$_SERVER['HTTP_HOST'],'From: ' . $pageaddr . '_post@nnjsda.org','-f ' . $pageaddr . '_post@nnjsda.org');
		exit();
	}
}

if (!empty($_GET)) {
	while (list($key, $val) = each($_GET))
		if (stristr($val,'http://') !== false || stristr($val,'ftp://') !== false) $problem_para[] = $key;
	if (isset($problem_para)) {
		@mail('ban.ip@kis-hosting.com',$IP,$_SERVER['HTTP_HOST'],'From: ' . $pageaddr . '_get@nnjsda.org','-f ' . $pageaddr . '_get@nnjsda.org');
		exit();
	}
}

if (isset($_POST['submit'])) {
	$tmp = array();
	$tmp[] = "The following request for State Outfit Information was filled in today:";
	foreach($_POST as $k=>$v)
		if ($k != 'submit')
			$tmp[] = ucwords($k) . ': ' . stripslashes($v);
	@mail('NJSquareDancers@Verizon.net','NJ State Outfit Information Request',implode("\n",$tmp)."\n",
			'From: NJ Outfit Request <njoutfit@nnjsda.org>','-f njoutfit@nnjsda.org');
	@mail('kenrbnsn@rbnsn.com','NJ State Outfit Information Request (copy)',implode("\n",$tmp)."\n",
			'From: NJ Outfit Request <njoutfit@nnjsda.org>','-f njoutfit@nnjsda.org');
}

function write_dbg($fp,$msg,$line,$dbg=true) {
	if ($dbg) fwrite($fp,date('Y-m-d G:i') . ' --- ' . __FILE__ . ' (' . $line . ') -- ' . $msg . "\r\n");
}

$mode = (file_exists('ajax_debug.txt'))?'a':'w';
$fp = ($do_debug)?$fp = fopen('/tmp/ajax_debug.txt',$mode):false;
write_dbg($fp,'-----------------------------',__LINE__,$do_debug);
write_dbg($fp,str_replace("\n","\r\n",print_r($_POST,true)),__LINE__,$do_debug);

if (isset($_POST['ajax'])) {

}
function disp_val($k,$ta=false) {
	if(!isset($_POST[$k])) return('');
	$pval = nl2br(htmlentities(stripslashes(trim($_POST[$k]))));
	$ret = ($ta)?$pval:'value="' . $pval . '"';
	return($ret);
}

function check_selected($k,$val,$def=true) {
	if(!isset($_POST[$k]) && !$def) return('');
	if(!isset($_POST[$k]) && $def) return ('checked');
	if($_POST[$k] == $val) return('checked');
	return('');
} 
	
	function makethumb($fn,$rt=true)
	{
		$ok = false;
		list($ow, $oh, $type, $attr) = getimagesize($fn);
		$pct = 75;
		while (!$ok) {
			$th = $oh * ($pct/100);
			$tw = $ow * ($pct/100);
			if (($oh > $ow) && ($th <= 160 && $w <= 120)) $ok = true;
			if (($ow >= $oh) && ($tw <= 160 && $th <= 120)) $ok = true;
			$pct -= 1;
			if ($pct < 5) {
				$ok = true;
				$th = 120;
				$tw = 160;
			}
		}
		$w = $tw;
		$h = $th;
		$tn = imagecreatetruecolor($w, $h);
		$img = imagecreatefromjpeg($fn);
		imagecopyresampled($tn, $img, 0, 0, 0, 0, $w, $h, $ow, $oh);
		if ($rt) {
			return (array($tn, $type, $w, $h));
		} else {
   			header('Content-type: ' .image_type_to_mime_type($type));
			imagejpeg($tn);
		}
	}

	function shrinkpic($fn,$maxw)
	{
		$ok = false;
		list($ow, $oh, $type, $attr) = getimagesize($fn);
		$nf = str_replace(basename($fn),'smaller_'.basename($fn),$fn);
		if (file_exists($nf)) {
			list($ow, $oh, $type, $attr) = getimagesize($nf);
			return(array($nf,$ow,$oh));
		}
		$pct = 90;
		while (!$ok) {
			$th = $oh * ($pct/100);
			$tw = $ow * ($pct/100);
			if  ($tw <= $maxw) $ok = true;
			$pct -= 1;
		}
		$w = $tw;
		$h = $th;
		$tn = imagecreatetruecolor($w, $h);
		$img = imagecreatefromjpeg($fn);
		imagecopyresampled($tn, $img, 0, 0, 0, 0, $w, $h, $ow, $oh);
		$nf = str_replace(basename($fn),'smaller_'.basename($fn),$fn);
		imagejpeg($tn,$nf);
		return(array($nf,$w,$h));
	}
		
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>NNJSDA State Outfit</title>
	<link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/<?php echo $yui_version; ?>/build/container/assets/skins/sam/container.css">
	<link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/<?php echo $yui_version; ?>/build/container/assets/skins/sam/logger.css">
	<style type="text/css">
	body, html {
		padding: 0;
		margin: 0;
		font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
		font-size: 100%;
	}
	
	.so {
		display: block;
		width: 656px;
		margin-left: auto;
		margin-right: auto;
		padding-bottom: 0.5em;
		padding-top: 0.5em;
	}
	
	#content {
		width: 90%;
		min-width: 656px;
		margin-left: auto;
		margin-right: auto;
		display: block;
	}
	
	.bold {
		font-weight: bold;
	}
	
	.italic {
		font-style: italic;
	}
	
	h1 {
		display: block;
		width: 55%;
		text-align: center;
		margin-left: auto;
		margin-right: auto;
		font-size: 300%;
	}
	
	.frm {
		display: block;
		width: 80%;
		margin-left: auto;
		margin-right: auto;
		border: 1px solid black;
		padding-top: .5em;
		padding-bottom: 0.5em;
		background-color: #018754;
	}
	
	.row {
		display: block;
		width: 98%;
		margin-left: auto;
		margin-right: auto;
		margin-top: 0.25em;
		clear: both;
	}
	
	.label {
		width: 20%;
		float: left;
		font-weight: bold;
	}
	
	.formw {
		width: 79%;
		float: left 
	}
	
	.inptxt {
		width: 100%;
	}
	
	.fullwidth {
		display: block;
		width: 100%;
		text-align: center;
	}
	.clearer {
		clear: both;
		line-height: 0.01em;
	}
	
	.kellygreen {
		background-color: #018754;
		color: black;
		font-weight: bold;
		padding-left: 0.25em;
		padding-right: 0.25em;
	}
	
	.notprint {
		display: inline;
	}
	
	.notonscreen {
		display: none;
	}
	.pic {
		border: 1px solid white;
	}
	
	a.pic {
	color: white;
	display: block;
	float: left;
	font-size: 80%;
	font-weight: normal;
	padding-top: 1em;
	text-align: center;
	text-decoration: none;
	border: 1px solid white;
	padding-right: 1em;
}

a:hover.pic {
	color:white;
	background-color: White;
}
.imgcenter {
	display: block;
	margin-left: auto;
	margin-right: auto;
}

a, a:link, a:active, a:visited {
	text-decoration: none;
	color: black;
	font-weight: bold;
}

a:hover {
	color: White;
	background-color: black;
}
	</style>
	<style type="text/css" media="print">
	.frm {
		display: none;
	}
	
	.notprint {
		display: none;
	}
	
	.notonscreen {
		display: inline;
	}
	
	</style>
	<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yui_version; ?>/build/yahoo-dom-event/yahoo-dom-event.js"></script>
	<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yui_version; ?>/build/dragdrop/dragdrop-min.js"></script> 
	<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yui_version; ?>/build/connection/connection-min.js"></script>
	<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yui_version; ?>/build/container/container-min.js"></script>
	<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yui_version; ?>/build/logger/logger-min.js"></script>
	<script type="text/javascript">
		var do_debug = new Boolean;
		do_debug = <?php echo ($do_debug)?"true":"false"; ?>;
	doSubmit = function(e, obj) {
		myTarget = YAHOO.util.Event.getTarget(e, 1);		alert(myTarget.value);
		}
	</script>
</head>

<body>
<div id="content">
<h1><span style="float:left"><img src="state_outfit_patch.jpg"></span>
<span style="float:left;padding-left:3em;padding-top:1.5em;">The<br>New Jersey<br>State Outfit</span></h1>
<p class="bold italic" style="clear:both;font-size:150%;text-align:center">Plan to wear it at the National Convention in June!</p>
<p class="bold">Square dance conventions and other national gatherings hold a Parade of States or a Grand March, during which the dancers from each state march together.  Usually the dancers from each state will wear the same outfit or the same color combination.</p>
<p>The New Jersey State Outfit colors are <span class="kellygreen">Kelly Green</span> and White, based on the concept of the "Garden State". (Examples of Kelly green are the labels on Schweppes and Seagram's Ginger Ale bottles.) The state emblem is visible in a prominent (but tasteful) location; the emblem set consists of a 9" Kelly green and white embroidered outline of New Jersey, with an embroidered gold star at the location of his/her home club, and an embroidered "NJ". Emblems are $7.50 each.</p>
<p>Some alternatives, which you may already have:
<ul>
<li>The original <span class="kellygreen">Kelly green</span> vest with white top-stitching, with the state emblem on the back, worn over a white shirt/blouse.</li>
<li>Sew/pin the state emblem directly on the back of a green or white shirt/blouse.</li>
<li>Put the state emblem on the side front of a green or white skirt. </li>
<li>Wear the state emblem on a green overskirt/apron trimmed with white eyelet.</li>
<li>Wear an all white dress worn with the green vest, with the state emblem on the skirt side front.</li>
<li>Either white or green slacks are equally appropriate.</li>
</ul>
</p>
<?php
	  if (isset($_GET['f'])) {
		$v = $_GET['f'];
		list($ow, $oh, $type, $attr) = getimagesize($v);
//		if ($ow > 525) {
//			list($v, $ow, $oh) = shrinkpic($v,525);
//		}
		echo '<img class="imgcenter" style="width:' . $ow . 'px;height:' . $oh .'px" src="' . $v . '">' . "\n";
	} else {
?>
<p>Here are some pictures of the state outfit taken over the years. Click on the small picture to see the fullsize picture:<br>
<div style="display:block;background-color:white;border: 1px solid white;">
<?php
		foreach (glob("../../state_outfit_pics/*.jpg") as $filename) {
			if (filesize($filename) > 0 && substr(basename($filename),0,8) != 'smaller_' && substr(basename($filename),0,5) != 'test_'){
				if (($check_grp_mbr && in_array(basename($filename),$pics_in_grp)) || !$check_grp_mbr) {
					$image = exif_thumbnail($filename, $width, $height, $type);
					if ($image === false) 
						list($image, $type, $width, $height) = makethumb($filename);
		   			$dwidth = 160;
					$dheight = 160;
		   			echo '<a class=pic href="' . $_SERVER['PHP_SELF'] . '?f=' . $filename . 
						'"><img style="width:' . $width . '" src="' . $_SERVER['php_self'] . '?i=' . $filename . '" width="' . 
						$width . '" height="' . $height . ' border="0""></a>'."\n";
				}
			}
		}
		?>
<div class="clearer">&nbsp;</div>
</div>
</p>
<?php } 
if (isset($_POST['submit'])) echo '<p style="font-weight:bold;font-size:200%">Thank you for your request or question, some one will be contacting soon.</p>';
else {
?>
<p>Need Emblem(s) or have other questions? <span class="notprint">Please fill out this form:</span><span class="notonscreen"> Please contact the NNJSDA Second Vice-President.</span></p>
<form class="frm" method="post">
<div class="row">
<span class="label">Your Name:</span>
<span class="formw"><input name="your_name" class="inptxt" type="text" <?php echo disp_val('your_name') ?>></span>
</div>
<div class="row">
<span class="label">Club Name:</span>
<span class="formw"><input name="club_name" class="inptxt" type="text" <?php echo disp_val('club_name') ?>></span>
</div>
<div class="row">
<span class="label">Your Address:</span>
<span class="formw"><input name="your_address" class="inptxt" type="text" <?php echo disp_val('your_address') ?>></span>
</div>
<div class="row">
<span class="label">Your City:</span>
<span class="formw"><input name="your_city" class="inptxt" type="text" <?php echo disp_val('your_city') ?>></span>
</div>
<div class="row">
<span class="label">Your State:</span>
<span class="formw"><input name="your_state" class="inptxt" type="text" <?php echo disp_val('your_state') ?>></span>
</div>
<div class="row">
<span class="label">Your ZIP:</span>
<span class="formw"><input name="your_zip" class="inptxt" type="text" <?php echo disp_val('your_zip') ?>></span>
</div>
<div class="row">
<span class="label">Your Email Address:</span>
<span class="formw"><input name="your_email_address" class="inptxt" type="text" <?php echo disp_val('your_email_address') ?>></span>
</div>
<div class="row">
<span class="label">Send Emblems:</span>
<span class="formw"><input name="emblems" type="radio" value="yes" <?php echo check_selected('emblems','yes') ?>>&nbsp;Yes<br><input name="emblems" type="radio" value="no" <?php check_selected('emblems','no',false) ?>>&nbsp;No</span>
</div>
<div class="row">
<span class="label">How Many:</span>
<span class="formw">
<select name="num_emblems" size=1><option value="" <?php echo check_selected('num_emblems','') ?>></option>
<?php
	for($i=1;$i<11;$i++)
		echo '<option value="' . $i . '" ' . check_selected('num_emblems',$i,false) . '>' . $i . '</option>' . "\n";
?>
</select>
</span>
</div>
<div class="row">
<span class="label">Comments or Questions:</span>
<span class="formw"><textarea class="inptxt" name="comments" rows="5"><?php echo disp_val('comments',true) ?></textarea></span>
</div>
<div class="row">
<span class="fullwidth"><input type="submit" id="submit" name="submit" value="Send Request"></span>
</div>
<div class="clearer">&nbsp;</div>
</form>
<? } ?>
<span class="notprint"><p style="text-align:center"><a href="http://www.nnjsda.org/">Return</a> to the NNJSDA Home Page</p></span>
</div>
</body>
</html>
