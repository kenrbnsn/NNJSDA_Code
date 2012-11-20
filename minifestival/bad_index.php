<?
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
 if ($_SERVER['SERVER_NAME'] != "localhost") mail("kenrbnsn@kis-hosting.com","NNJSDA Mini Festival Page Visited",$body,"From: Visit Tracker <tracker@nnjsda.com>");
}
if (IsSet($form_submitted))
{
	$error_found = false;
	$error_num = false;
	$v = 0;
	$n = 0;
	if (check_value($name)) {$field_errors[$v++] = 'name'; $error_found = true;}
	if (check_value($club)) {$field_errors[$v++] = 'club'; $error_found = true;}
	if (check_value($address)) {$field_errors[$v++] = 'address'; $error_found = true;}
	if (check_value($city)) {$field_errors[$v++] = 'city'; $error_found = true;}
	if (check_value($email)) {$field_errors[$v++] = 'email'; $error_found = true;}
	if (check_value($numribbons)) {$field_errors[$v++] = 'Number of Ribbons'; $error_found = true;}
	if (check_numeric($numribbons)) {$num_errors[$n++] = 'Number of Ribbons'; $error_num = true;}
	if (check_numeric($badge)) {$num_errors[$n++] = 'Number of Badges'; $error_num = true;}
	if (check_numeric($bars)) {$num_errors[$n++] = 'Number of Bars'; $error_num = true;}
}

function check_value($v)
{
	if ($v == "") return true;
	return false;
}

function check_numeric($n)
{
	if (($n != "") && (!is_numeric($n))) return true;
	return false;
}

function padspaces($v)
{
	if (strlen($v) >= 40) return $v;
	$v = str_pad($v, 40, "\\");
	$v = str_replace("\\", " ", $v);

	return ($v);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>28th Annual Mini-Festival</title>
	<link rel="STYLESHEET" type="text/css" href="mini.css">
</head>

<body>
<? if (IsSet($submit)) {
	if ($error_found)
	   {echo "Some field not filled in<br>"; var_dump ($field_errors); echo "<br>";}
	if ($error_num)
	   {echo "Problem in number field<br>"; var_dump ($num_errors);}
	if ((!$error_found) && (!$error_num))
	{ ?>
<div align=center><h1 class=caps>thanks for signing up</h1>
<hr>
</div>
<div>
<table width=100% align=center>
	<tr>
		<td valign="top" class=prompt><b>Name(s):</b></td><td class=fillin><?echo padspaces($name); ?></td>
		<td valign=top class=prompt><b>Club:</b></td><td class=fillin><?echo padspaces($club); ?></td>
	</tr>
	<tr>
		<td valign=top class=prompt><b>Address:</b></td><td class=fillin><?echo padspaces($address); ?></td>
		<td valign=top class=prompt><b>City:</b></td><td class=fillin><?echo padspaces($city); ?></td>
	</tr>
	<tr>
		<td valign=top class=prompt><b>State:</b></td><td class=fillin><?echo padspaces($state); ?></td>
		<td valign=top class=prompt><b>Zip:</b></td><td class=fillin><?echo padspaces($zip); ?></td>
	</tr>
	<tr>
		<td valign=top class=prompt><b>Phone:</b></td><td class=fillin><?echo padspaces($phone); ?></td>
		<td valign=top class=prompt><b>Email:</b></td><td class=fillin><?echo padspaces($email); ?></td>
	</tr>
</table>
<? $total = ($numribbons*12) + ($badge*2.25) + ($bars * .75); ?>
<table>
<tr>
<td width=30% class=numprompt>Advanced Remittance $12/person</td>
<td class=nums>$<? printf("\..2f",$numribbons*12); ?></td>
<td width=60%> </td>
</tr>
<tr>
<td width=10% class=numprompt>1st Time Badge Only 2002 $2.25 each</td>
<td class=nums>$<? printf("\..2f",$badge*2.25); ?></td>
</tr>
<tr>
<td  class=numprompt>2002 Bar $0.75 each</td>
<td class=nums>$<? printf("\..2f",$bars*0.75); ?></td>
</tr>
<tr>
<td  class=numprompt>Total</td>
<td class=nums>$<? printf("\..2f",$total); ?></td>
</tr>
</table>
<? if ($submit == "Pay by Check or Cash") { ?>
<table width=100%>
<tr><td><hr></td></tr>
<tr>
<td>
Please print this page in <strong>LANDSCAPE</strong> and mail with your check to
<pre>
Norman & Audrey Bolin
585 West Shore Trail
Sparta, NJ 07871-1338
</pre>
</td></tr></table>
<? } else { ?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="kenrbnsn@kis-hosting.com">
<input type="hidden" name="item_name" value="minifestival">
<input type="hidden" name="item_number" value="01">
<input type="hidden" name="amount" value="$<? printf('\..2f',$total); ?>">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="return" value="http://www.nnjsda.org/minifestival/thankyou.php">
<input type="hidden" name="no_note" value="1">
<table width=100%>
<tr><td><hr></td></tr>
<tr>
<td>
Please click on the following button to pay vai PayPal<br>
<input type="image" src="https://www.paypal.com/images/x-click-but9.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
</td></tr></table>
</form>
<? } ?>
</div>
	<?}
} else { ?>
<div align="center"><img src="../mini_festival_wanted2.jpg" width="576" height="455" alt="" border="0"></div>
<hr>
<div align=center>
<form action="<? echo $_SERVER['PHP_SELF']; ?>" method=post>
<input type=hidden name=form_submitted value=1>
<table width=50% align=center>
<tr>
<td><b>Name(s):</b></td><td><input type=text name=name size=50></td>
</tr><tr>
<td><b>Club:</b></td><td><input type=text name=club size=50></td>
</tr><tr>
<td><b>Address:</b></td><td><input type=text name=address size=50></td>
</tr><tr>
<td><b>City:</b></td><td><input type=text name=city size=50></td>
</tr><tr>
<td><b>State:</b></td><td><input type=text name=state size=50></td>
</tr><tr>
<td><b>Zip:</b></td><td><input type=text name=zip size=50></td>
</tr><tr>
<td><b>Phone:</b></td><td><input type=text name=phone size=50></td>
</tr><tr>
<td><b>Email:</b></td><td><input type=text name=email size=50></td>
</tr>
</table>
<hr>
<table align=center width=30%>
<tr>
<th> </th><th>Number Coming<br>or Needed</th>
</tr>
<tr>
<td valign=top><b>Number of Ribbons Needed:<br>$12/person</b></td><td><input type=text name=numribbons size=10></td>
</tr><tr>
<td valign=top><b>1st Time Badge Only 2002:<br>$2.25 each</b></td><td><input type=text name=badge size=10></td>
</tr><tr>
<td valign=top><b>2002 Bar:<br>$0.75 each</b></td><td><input type=text name=bars size=10></td>
</tr>
<tr>
<td colspan=2 align=center><input type="submit" name=submit value="Pay by Check or Cash"><br><input type="submit" name=submit value="Pay by Credit Card Using PayPal"><br><input type="reset"></td>
</tr>
</table>
</form>
</div>
<? } ?>
</body>
</html>




