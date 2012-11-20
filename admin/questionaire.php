<?php
$ver = '(0.0.1)';
$subject = 'NNJSDA Questionaire';
$pageaddr = 'questionaire';
if (file_exists('../emailtracker.inc.php')) include('../emailtracker.inc.php');
$this_year = date('Y');
$next_year = $this_year + 1;
$dance_programs = array('Mainstream','MS w/PL tip','Alt MS &amp; PL','Plus','PL w/A Tip',
						'Advanced-1','Advanced-2','Challenge','DBD','Rounds','Country/Western');
$dance_day = array('sunday','monday','tuesday','wednesday','thursday','friday','saturday');
$dance_week = array('1st'=>'1<sup>st</sup>',
						  '2nd'=>'2<sup>nd</sup>',
						  '3rd'=>'3<sup>rd</sup>',
						  '4th'=>'4<sup>th</sup>',
						  '5th'=>'5<sup>th</sup>',
						  'varied'=>'varied',
						  'every'=>'every');
$class_programs = array('Basic/MS','Basic/MS to PL','PL','A1',
						'A2','Challenge','Rounds');
$class_day = array('sunday','monday','tuesday','wednesday','thursday','friday','saturday');
$sections = array('','club information',
						'grand square information',
						'class information',
						'other officers');
$indices = array('name','address','city','state','zip','phone','email');
$sec = 1;
function put_form_line($l,$n)
{
	global $tmp;
	$tmp[] = '<div class="row">';
	$tmp[] = "\t".'<span class="label2">' . $l . ':</span>';
	$tmp[] = "\t".'<span class="formw"><input class="textfld" name="'.$n.'"></span>';
	$tmp[] = '</div>';
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>NNJSDA <? echo $this_year.'-'.$next_year.' Questionnaire'?></title>
	<link rel="stylesheet" href="../nnjsda.css" type="text/css" media="screen">
	<link rel="stylesheet" href="../printnnjsda.css" type="text/css" media="print">
</head>

<body>
<div id=hdr><h1><? echo ucwords(strtolower('NORTHERN NEW JERSEY SQUARE DANCERS ASSOCIATION')).'<br>'.$this_year.'-'.$next_year.' Questionnaire';?></h1>
</div>
<div id="restofpage">
<p><span class="center">Please fill by <span class="bold">JUNE 15th</span></span><span class="onlyprint"> to <br><pre>Grand Square Co-Editors:<br>
Norman & Barbara Kanter
3461 Amboy Rd.
Apt. 4A
Staten Island, NY 10306</pre></span><span class="center">The club and class information will be published in the September Issue.</span></p>
<form class=quest method="post" action="">
<?
	echo '<p><span class="bold">' . $sec . '. ' . strtoupper($sections[$sec++]) . "</span></p>\n";
?>
<div class="row">
	<span class="label2">Club Name:</span>
	<span class="formw"><input class="textfld" name="club_name"></span>
</div>
<div class=row>
	<span class="label2">E-mail address :</span>
	<span class="formw"><input class="textfld" name="email"></span>
</div>
<div class=row>
	<span class="label2">Program:</span>
	<span class="formw">
	<?
		foreach ($dance_programs as $p) 
			echo '<input type="checkbox" name="dance_program[]" value="' . $p . '">&nbsp;' . $p . "<br>\n";
	?>
			Other:&nbsp;<input type="text" class="textfld" style="width:80%" name="other_program"><br>
			Workshop at every dance:<br><span style="text-align:right;width:10em;display:block;float:left">Level:</span><input type="text" class="textfld" style="width:30%;float:left" name="wad"><br>
<span style="clear: both;text-align:right;width:10em;display:block;float:left">Time:</span><input type="text" class="textfld" style="width:30%;float:left;" name=tow>
		</span>
</div>
<div class="row">
	<span class="label2">Day:</span>
	<span class="formw">
	<?
		foreach ($dance_day as $d) 
			echo '<input type="checkbox" name="dance_day[]" value="' . $d . '">&nbsp;' . ucwords($d) . "<br>\n";
	?>
		</span>
</div>
<div class=row>
	<span class="label2">Week:</span>
	<span class="formw">
	<?
		foreach ($dance_week as $w=>$l) 
			echo '<input type="checkbox" name="dance_week[]" value="' . $w . '">&nbsp;' . ucwords($l) . "<br>\n";
	?>
		</span>
</div>
<div class="row">
	<span class="label2">Months:</span>
	<span class="formw">
		<span style="text-align:right;width:15em;display:block;float:left">Start of dance season:</span><select size=1 class="textfld" style="width:30%;float:left" name="season_start">
<?
	for ($i=1;$i<13;$i++) {
		$mnth = date('F',strtotime($i.'/1/2005'));
		echo '<option value="' . $mnth . '"';
		if ($mnth == 'September') echo ' selected style="font-weight:bold;color:red"';
		echo '>' . $mnth . "</option>\n"; }
	echo '</select><br>'."\n"; ?>
		<span style="clear:both;text-align:right;width:15em;display:block;float:left">End of Dance Season:</span><select size=1 class="textfld" style="width:30%;float:left" name="season_end">
<?
	for ($i=1;$i<13;$i++) {
		$mnth = date('F',strtotime($i.'/1/2005'));
		echo '<option value="' . $mnth . '"';
		if ($mnth == 'June') echo ' selected style="font-weight:bold;color:red"';
		echo '>' . $mnth . "</option>\n"; }
	echo '</select>'."\n"; ?>
	</span>
</div>
<?
$tmp = array();
$tmp[] = '<hr style="height:2px;color:red">';
foreach ($indices as $i)
	put_form_line('Hall ' . ucwords($i),'hall[' . $i . ']');
put_form_line('Dance Night Cell Phone','hall[cell_phone]');
$tmp[] = '<hr style="color:red">';
for ($j=0;$j<2;$j++) {
	foreach ($indices as $i)
		put_form_line('Club President','club_president[' .$i .'][]');
	$tmp[] = '<hr style="color:red">';
}
$tmp[] = '<p style="clear:both"><span class="bold">' . $sec . '. ' . strtoupper($sections[$sec++]) . '</span></p>';
foreach ($indices as $i)
	put_form_line('Reporter ' . ucwords($i),'reporter[' . $i . ']');
$tmp[] = '<hr style="color:red">';
foreach ($indices as $i)
	put_form_line('Ad Maker ' . ucwords($i),'ad_maker[' . $i . ']');
$tmp[] = '<hr style="color:red">';
$tmp[] = '<p style="clear:both"><span class="bold">' . $sec . '. ' . strtoupper($sections[$sec++]) . '</span></p>';
$tmp[] = '<div class="row">';
$tmp[] = "\t".'<span class="label2">Program:</span>';
$tmp[] = "\t".'<span class="formw">';
		foreach ($class_programs as $p) 
			$tmp[] = "\t\t\t".'<input type="checkbox" name="class_program[]" value="' . $p . '">&nbsp;' . $p . '<br>';
$tmp[] = "\t".'</span>';
$tmp[] = '</div>';
$tmp[] = '<div class="row">';
$tmp[] = "\t".'<span class="label2">Day:</span>';
$tmp[] = "\t".'<span class="formw">';
		foreach ($class_day as $d) 
			$tmp[] = "\t\t\t".'<input type="checkbox" name="class_day[]" value="' . $d . '">&nbsp;' . ucwords($d) . "<br>";
$tmp[] = "\t".'</span>';
put_form_line('Open House Date(s)','open_house[date]');
put_form_line('Open House Time','open_house[time]');
put_form_line('Classes Start Date','classes_start[date]');
put_form_line('Classes Start Time','classes_start[time]');
$tmp[] = '<hr style="color:red">';
foreach ($indices as $i)
	put_form_line('Classes Hall ' . ucwords($i),'classes_hall['.$i.']');
$tmp[] = '<hr style="color:red">';
foreach ($indices as $i)
	put_form_line('Teacher ' . ucwords($i),'teacher['.$i.']');
$tmp[] = '<hr style="color:red">';
for ($j=0;$j<2;$j++) {
	foreach ($indices as $i)
		put_form_line('Class Coordinater','class_coord[' .$i .'][]');
	$tmp[] = '<hr style="color:red">';
}
$tmp[] = '<p style="clear:both"><span class="bold">' . $sec . '. ' . strtoupper($sections[$sec++]) . '</span></p>';
foreach ($indices as $i)
	put_form_line('Delegate ' . ucwords($i),'delegate[' . $i . ']');
$tmp[] = '<hr style="color:red">';
foreach ($indices as $i)
	put_form_line('Alt. Delegate ' . ucwords($i),'alt_delegate[' . $i . ']');

$tmp[] = '</div>';

echo implode("\n",$tmp)."\n";
?>
<div class=row>
	<span class="fullwidth"><input type="submit" value="Check Answers"></span>
</div>
<div class="clearer">&nbsp;</div>
</form>
</div>

</body>
</html>
