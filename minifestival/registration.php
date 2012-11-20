<?
include('mfinit.inc.php');	
$subject = $which1 . ' Mini Festival (' . $when . ') Registration';
$pageaddr = 'registration'; 
$ver = '(2.0.4)';
if (file_exists('emailtraker.php.inc')) include ('emailtracker.php.inc');
foreach ($_POST as $k=>$v) 
 if (strpos($v,'Content-Type:') !== false)  {
	@mail('kenrbnsn@kis-hosting.com',$subject .' Incorrectly Invoked','content-type found in ' . $k,
			'From: Visit Tracker <nobody@nnjsda.org>','-f contenttype@nnjsda.org'); 
	header("HTTP/1.0 404 Not Found");
	exit();
}

if (isset($_POST['submit']) && ($_POST['submit'] != 'Process Registration')) {
	@mail('kenrbnsn@kis-hosting.com',$subject .' Incorrectly Invoked','Submit is wrong value ' . $_POST['submit'],
			'From: Visit Tracker <nobody@nnjsda.org>','-f submitwrong@nnjsda.org'); 
	header("HTTP/1.0 404 Not Found");
	exit();
}
	$registrar_addr = "Kay Davis\n23 Woodland Drive\nOak Ridge, NJ 07438";
	$regist_email = "kaydavis@optonline.net";

	$ersp = "";
	$erspend = "";

	$fields = array('name','club','address','city','state','zip','phone','email');
	$errors = array(false,false,false,false,false,false,false,false);
	$error_found = false;
	$ticket_label = "rightlabel";
	$ticket_fam_label = "rightlabel";
	
if (IsSet($_POST['submit'])) {
	for ($i=0;$i<count($fields);$i++)
		if ($_POST['regfield'][$fields[$i]] == "") {
			$errors[$i] = true;
			$error_found = true; }
	if (($_POST['regfield']['name'] != "") && ($_POST['num_tickets'] < 1 && $_POST['num_fam_tickets'] < 1)) {
		$error_found = true;
		if ($_POST['num_tickets'] < 1) $ticket_label .= "e";
		if ($_POST['num_fam_tickets'] < 1) $ticket_fam_label .= "e";
		}
	if (!$error_found) {
		$tot_amount = 0;
		if ($_POST['num_tickets'] < 1) $tot_amount += $_POST['num_tickets']*17;
		if ($_POST['num_fam_tickets'] < 1) $tot_amount += $_POST['num_fam_tickets']*51;
		if ($_POST['num_badges'] > 0) $tot_amount += $_POST['num_badges']*2.75;
		if ($_POST['num_bars'] > 0) $tot_amount += $_POST['num_bars']*1.00;
	}
}
	
function dispval($i,$dv=" ",$o="")
{
	global $fields;
	if (!Isset($_POST['submit'])) return("value=".$dv);
	$ret = ($i < 0)?'value='.$o:'value="'.$_POST['regfield'][$fields[$i]].'"';
	return($ret);
}

function dispunderval($i,$dv=" ",$o="")
{
	global $fields;
	$ret = ($i < 0)?$o:"<span class=underline>".str_replace("~","&nbsp;",str_pad(stripslashes($_POST['regfield'][$fields[$i]]),50,"~"))."</span>";
	return($ret);
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html>
<head>
	<title>NNJSDA -- <? echo $which1 ?> Annual Mini Festival Registration Form</title>
	<link rel="stylesheet" type="text/css" href="http://www.nnjsda.org/nnjsda.css" media="screen">
	<? if (IsSet($_POST['submit'])) { ?> 
		<link rel="stylesheet" media="print" type="text/css" href="../printnnjsda.css"> <? } ?>
</head>

<body>
<div id=hdr>
<h2><? echo $which ?> Annual</h2>
<h1>Mini-Festival</h1>
<h2>Registration Form</h2>
</div>
<div id="restofpage">
<? if (($error_found) || (!Isset($_POST['submit']))) { ?>
<p>Online payment is not available yet. Please complete this form and press the&nbsp;<button>Process Registration</button> button. Please note that the <span class=bold>first 8 fields</span> are required and that the <span class=bold>Number of Advanced Tickets</span> orders must be greater than zero. Print the resulting page and mail it with a check payable to <span class="bold">NNJSDA MINI Festival</span> to:
<pre><?php echo $registrar_addr ?></pre></p>
<? if ($error_found) { ?>
<p><span class=bold>Errors were found during the processing of the form. Please check all fields where the labels is printed in <span class=errori>Red Italic</span></span></p>
<? } ?>
<form class="reg" method="post" action=<? echo $PHP_SELF ?>>
<? for ($i=0;$i<count($fields);$i++) { 
	$label = ($errors[$i])?"label2e":"label2";?>
<div class=row>
	<span class="<? echo $label ?>"><? echo ucwords($fields[$i]) ?>:</span>
	<span class="formw"><input class="textfld" name=regfield[<? echo $fields[$i] ?>] type="text" <? echo dispval($i) ?>></span>
</div>
<? } ?>
<div class=row>
	<span class="label2">Program Level:</span>
	<span class=thirdnc><span class=bold>Square Dance</span><br>
							<input type="checkbox" name="level[]" value=0>&nbsp;Mainstream<br>
							<input type="checkbox" name="level[]" value=1>&nbsp;Plus<br>
							<input type="checkbox" name="level[]" value=2>&nbsp;Advanced</span>
	<span class=thirdnc><span class=bold>Round Dance</span><br>
							<input type="checkbox" name="phase[]" value=0>&nbsp;Phase II<br>
							<input type="checkbox" name="phase[]" value=1>&nbsp;Phase III<br>
							<input type="checkbox" name="phase[]" value=2>&nbsp;Phase IV<br>
							<input type="checkbox" name="phase[]" value=3>&nbsp;Phase V</span>						
</div>
<div class=row>
	<span class=fullwidth><input type="checkbox" name=single value="single">&nbsp;Single -- Would like a dance partner (prefer to dance <input type="radio" name="single_partner" value="MS">MS, <input type="radio" name="single_partner" value="Plus">Plus or <input type="radio" name="single_partner" value="Adv">Adv for the day)</span>
</div>
<span class=row>
	<span class=fullwidth><hr style="color:red"></span>
</span>
<div class=row>
	<span class=<? echo $ticket_label ?>>Number of Advanced Tickets at $17/person:</span>
	<span class=formw style="width:29%"><input name=num_tickets type="text" size=5 maxlength=5 <? echo dispval(-1,"0",$_POST['num_tickets']) ?>></span>
</div>
<div class=row>
	<span class=<? echo $ticket_fam_label ?>>Number of Advanced Family Tickets at $51/family:</span>
	<span class=formw style="width:29%"><input name=num_tickets type="text" size=5 maxlength=5 <? echo dispval(-1,"0",$_POST['num_fam_tickets']) ?>></span>
</div><div class=row>
	<span class=rightlabel>Number 1<sup>st</sup> Time Badges at $2.75 each:</span>
	<span class=formw style="width:29%"><input name=num_badges type="text" size=5 maxlength=5 <? echo dispval(-1,"0",$_POST['num_badges']) ?>></span>
</div>
<div class=row>
	<span class=rightlabel>Number <?php echo $when_year ?> Bars at $1.00 each:</span>
	<span class=formw style="width:29%"><input name=num_bars type="text" size=5 maxlength=5 <? echo dispval(-1,"0",$_POST['num_bars']) ?>></span>
</div>
<div class=row>
	<span class=fullwidth><input type=submit name=submit value="Process Registration"</span>
</div>
<div class=clearer>&nbsp;</div>
</form>
<? }  else {?>
<p><span class="onweb">Print this page and mail it</span><span class="forprint">Mail this page</span> with a check in the amount of <span class="bold">$<? printf("%01.2f",$tot_amount) ?></span> payable to <span class="bold">NNJSDA MINI Festival</span> to:
<pre><?php echo $registrar_addr ?></pre></p>
<div class=printregform>
<? 
	for($i=0;$i<count($fields);$i++)
	{ ?>
	<div class=row>
		<span class=label2><? echo ucwords($fields[$i]) ?>:</span>
		<span class=formw><? echo dispunderval($i) ?></span>
	</div>
<?
	} ?>
	<div class=row>
		<span class=rightlabel>Number of Advanced Tickets at $17/person:</span>
		<span class=formw style="width:25%"><span class=underline><? echo str_replace(" ","&nbsp;",str_pad($_POST['num_tickets'],7," ",PAD_LEFT))?></span></span>
	</div>		
	<div class=row>
		<span class=rightlabel>Number 1<sup>st</sup> Time Badges at $2.75 each:</span>
		<span class=formw style="width:25%"><span class=underline><? echo str_replace(" ","&nbsp;",str_pad($_POST['num_badges'],7," ",PAD_LEFT)) ?></span></span>
</div>
	<div class=row>
		<span class=rightlabel>Number <?php echo $when_year ?> Bars at $1.00 each:</span>
		<span class=formw style="width:25%"><span class=underline><? echo str_replace(" ","&nbsp;",str_pad($_POST['num_bars'],7," ",PAD_LEFT)) ?></span></span>
	</div>
	<div class=row>
		<span class=rightlabel>Total Amount Enclosed:</span>
		<span class=formw style="width:25%"><span class=underline><? echo str_replace(" ","&nbsp;",str_pad("$".sprintf("%01.2f",$tot_amount),7," ",PAD_LEFT))?></span></span>
	</div>
	<div class=clearer>&nbsp;</div>
</div>
</div>
<? 
if ($_SERVER['SERVER_NAME'] != 'localhost') {
	$body = "A NNJSDA Mini Festival Registration has been filled out with the following information:\n";
	$body .= " ====================================================\n";
	foreach($fields as $v)
		$body .= ucwords($v) .': '. $_POST['regfield'][$v] ."\n";
	$body .= "Number of Advanced Tickets: " . $_POST['num_tickets'] ."\n";
	$body .= "Number of Advanced Family Tickets: " . $_POST['num_fam_tickets'] ."\n";
	$body .= "Number of 1st Time Badges: " . $_POST['num_badges'] ."\n";
	$body .= "Number of " . $when_year . " Bars: " . $_POST['num_bars'] ."\n";
	$body .= "Total amount: $".sprintf("%01.2f",$tot_amount)."\n";
	@mail("kenrbnsn@kis-hosting.com","NNJSDA $which1 Mini Festival Registration Filled In (copy)",$body,"From: Visit Tracker <nobody@nnjsda.org>",'-f minifestival@nnjsda.org');
	@mail($regist_email,"NNJSDA $which1 Mini Festival Registration Filled In",$body,"From: Mini Festival Registration <minifestival@nnjsda.org>",'-f minifestival@nnjsda.org');
}

} ?>
</div>
<div class="footer" style="padding-left:0.5em">
<hr>
<? if (IsSet($_POST['submit'])) { ?> Return to the <a href=registration.php>Registration</a> page.<br> <? } ?>
<a href="../index.php">Return</a> to the NNJSDA home page.

<div style="font-size:80%;padding-left:0.5em">
<hr>
<?
echo "Page Last modified: " . date ("F j, Y", getlastmod()) . " at " . date ("g:i a.", getlastmod()) ; ?></div>
</div>
</div>
</body>