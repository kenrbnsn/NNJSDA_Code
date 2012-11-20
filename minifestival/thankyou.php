<?
session_start();
if (!empty($_SESSION)) extract($_SESSION);
if (!empty($_GET)) {
extract($_GET);
} else if (!empty($HTTP_GET_VARS)) {
extract($HTTP_GET_VARS);
}

if (!empty($_POST)) {
extract($_POST);
} else if (!empty($HTTP_POST_VARS)) {
extract($HTTP_POST_VARS);
}
if ($_SERVER['SERVER_NAME'] != "localhost")
{
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
 if (IsSet($form_submitted)) $body .= "Submit: $submit\n";
 if ($_SERVER['SERVER_NAME'] != "localhost") mail("kenrbnsn@kis-hosting.com","NNJSDA Mini Festival Thank You Page Visited",$body,"From: Visit Tracker <tracker@nnjsda.com>");
}?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>NNJSDA Thank You for Paying</title>
</head>

<body>
<div align=center><h1>Thanks for buy Mini Festival Ribbons Via PayPal</h1>
<hr>
</div>
<div align=left>

<table width=60% align=center>
<tr>
<td><img src="minifestival_ribbon.jpg" alt="" width="100" height="379" border="0"></td>
<td>You (<strong><? echo $Name; ?></strong>) have purchased the following:<br>
<ul>
<li>Number of Ribbons: <? echo "$Numribbons ($$Remittance)"; ?></li>
<li>Number of Badges: <? echo "$Badge ($$Badge_amount)"; ?></li>
<li>Number of Bars: <? echo "$Bars ($$Bar_amount)"; ?></li>
</ul>
For a total of <strong>$<? echo $Total_paid; ?></strong> charged to your credit card.
</td>
<td><img src="minifestival_ribbon.jpg" alt="" width="100" height="379" border="0"></td>
</tr></table>
<hr>
</div>
<div align=left>
<a  href="index.php">Return</a> to the NNJSDA Home Page.
</div>
</body>
</html>
