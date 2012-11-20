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
 mail("tracker@kis-hosting.com","NNJSDA addme Page Visited",$body,"From: Visit Tracker <tracker@nnjsda.org>");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>NNJSDA -- Add New Admin User</title>
	<LINK rel="STYLESHEET" type="text/css" href="../nnjsda.css">
</head>

<body>
<div class=newcenterarea3>
<h1>NNJSDA<br>New User Form</h1>
<hr>
<form method="post" action="new_addme.php">
<div class=row>
<span class=label>Your Name: <em>(Required)</em></span>
<span class=formw><input name="member_name" size=50 type=text<? if (IsSet($member_name)) echo "value=\"$member_name\""; ?>></span>
</div>
<div class=row>
<span class=label>New Username:</span>
<span class=formw><input name="username" size=50 type="Text" <? if (IsSet($username)) echo "value=\"$username\""; ?>></span>
</div>
<div class=row>
<span class=label>Email Address:</span>
<span class=formw><input name="email" size=50 type="text"<? if (IsSet($email)) echo "value=\"$email\""; ?>></span>
</div>
<div class=row>
<span class=fullwidth><input type="submit" name="Add_User" value="Submit">&nbsp;<input type="Reset"></span>
</div>
</form>
<?
	if (IsSet($err))
	{ ?>
<hr>
<div class=error>Error:
<?
switch ($err) {
	case 1:
		echo "Email address can not be blank";
		break;
	case 2:
		echo "Username can not be blank";
		break;
	case 4:
	case 5:
		echo "Username already exists";
		break;
	}
echo ", please try again.<br></div>\n";
 }?>
<hr>
<a href="user_login.php">Return</a> to the login page.
</div>
</body>
</html>