<?php
session_start();
$ver='(0.0.9)';
$pageaddr = 'select.pictures';
$subject = 'Select Pictures';
if (isset($_GET['p'])) {
	$tmp = makethumb($_GET['p']);		
	exit();
}
$photo_selected = (isset($_POST['submit']))?true:false;
if ($photo_selected) {
	mail('kenrbnsn@nnjsda.org','Picture Selected by ' . $_SESSION['who'],print_r($_POST['select_this'],true),'From: Picture Selected <picture.selected@nnjsda.org>','-f picture.selected@nnjsda.org');
	unset($_SESSION['who']);
}
if (isset($_POST['who'])) {
	$_SESSION['who'] = ucwords(str_replace(' and ',' & ',str_replace('_',' ',basename($_POST['who']))));
}
	
	function makethumb($fn)
	{
		list($ow, $oh, $type, $attr) = getimagesize($fn);
		if ($oh > $ow) {
			$th = 160;
			$tw = ($th / $oh) * $ow;
		}
		if ($ow >= $oh) {
			$tw = 160;
			$th = ($tw / $ow) * $oh;
		}
		$w = round($tw);
		$h = round($th);
		$tn = imagecreatetruecolor($w, $h);
		$img = imagecreatefromjpeg($fn);
		imagecopyresampled($tn, $img, 0, 0, 0, 0, $w, $h, $ow, $oh);
   		header('Content-type: ' .image_type_to_mime_type($type));
		imagejpeg($tn);
	}
include('../emailtracker.inc.php');
?>
<html>
<head>
<title>Select Exec Board Picture</title>
	<style type="text/css">
	body, html {
		padding: 0;
		margin: 0;
		font-size: 100%;
		font-family: sans-serif, ariel;
	}
	
	form {
		display: block;
		width: 90%;
		margin-left: auto;
		margin-right: auto;
	}
	.disp {
		display:block;
		float:left;
		min-height:100px;
		min-width:200px;
		text-align: center;
	}
	
	.intro {
		display: block;
		width: 90%;
		margin-left: auto;
		margin-right: auto;
		border: 1px solid black;
		padding: 0.5em;
		margin-top: 0.5em;
	}
	
	.box {
		display: block;
		width: 70%;
		margin-left: auto;
		margin-right: auto;
		border: 1px solid red;
		padding: 0.5em;
		margin-top:0.5em;
	}	
	
	fieldset {
		display: block;
		min-width: 230px;
		float: left;
		clear: both;
		min-height: 100px;
		margin-top:0.5em;
	}
	
	legend {
		font-weight: bold;
		border: 1px solid grey;
	}
	
	.sel_img {
		border: 1px solid black;
		margin-right: 0.25em;
		margin-top: 0.25em;
		}
	</style>
</head>
<body>
<?php 
if ($photo_selected) {
	echo '<p class="box">Your selection has been noted and the picture you selected will be shown on the NNJSDA web page soon.';
	echo ' If you wish to change your selection, just reinvoke this page.<br>';
	foreach($_POST['select_this'] as $d => $fn) {
		echo '<img class="sel_img" src="?p=' . $fn . '">';
	}
} else {
$dirs = glob('../upload_pictures/*',GLOB_ONLYDIR);
echo '<p class="intro">If your photo was featured in one of the Grand Square issues, you may request that we use that one or submit any other photo you prefer instead of selecting one of these. Couples may choose individual shots or a photo showing both together. Please submit alternate photos (digital preferred) to Lise Greene. Your selection is needed ASAP. <br><br><span style="text-align:center;font-size:150%;font-weight:bold">Please select your pictures only</span><br><br>Thank you!</p>';
echo '<form method="post" action="">';
if (!isset($_POST['who'])) {
echo '<p>';
echo '<label for="enter_name">Who are you?</label><select name="who" id="enter_name">';
echo '<option value=""></option>';
foreach ($dirs as $dir) {
	if (basename($dir) != 'no_pic')
		echo '<option value="' . $dir . '">' . ucwords(str_replace(' and ',' & ',str_replace('_',' ',basename($dir)))) . '</option>';
}
echo '</select>';
echo '<input type="submit" name="name_submit" value="Enter Name">';
} else {
foreach ($dirs as $dir) {
	if (basename($dir) != 'no_pic' && ($dir == $_POST['who'] || in_array($_POST['who'],array('../upload_pictures/lise_greene','../upload_pictures/ken_robinson')))) {
	$x = glob($dir . '/*.jpg');
	echo '<fieldset><legend>' . ucwords(str_replace(' and ',' & ',str_replace('_',' ',basename($dir)))) . '</legend>';
	$n = 1;
	foreach($x as $fn) {
		echo '<span class="disp"><img src="?p=' . $fn . '"><br><input type="radio" name="select_this[' . dirname($fn) . ']" value="' . $fn . '">Photo # ' . $n . "</span>\n";
		$n++;
	}
	echo '</fieldset>';
	}
}
echo '</p><p style="clear:both"><input type="submit" name="submit" value="Select Photo">';
}
echo '</form>';
}
?>
</body>