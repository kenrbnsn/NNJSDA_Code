<?
session_start();
if (!empty($_GET)) {
extract($_GET);
} else if (!empty($HTTP_GET_VARS)) {
extract($HTTP_GET_VARS);
}
if (!empty($_SESSION)) extract($_SESSION);
if (!empty($_POST)) {
extract($_POST);
} else if (!empty($HTTP_POST_VARS)) {
extract($HTTP_POST_VARS);
}
extract ($_SERVER);

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
 if ($_SERVER['SERVER_NAME'] != "localhost") mail("kenrbnsn@kis-hosting.com","NNJSDA Admin User Login Page Visited",$body,"From: Visit Tracker <tracker@nnjsda.org>");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html>
<head>
	<title>NNJSDA  -- Admin User Login</title>
	<META NAME="ROBOTS" CONTENT="NOINDEX">
	<LINK rel="STYLESHEET" type="text/css" href="nnjsda.css">
</head>

<body>
<div id=hdr>
<h1>NNJSDA<br>Web Site Administration<br>Please Login</h1>
</div>
<div id=rest-of-page>
<form class=cp method="post" action="login.php">
<div class=row>
<span class=label2 style="color:white">Username:</span>
<span class=formw style="width:77%"><input name="username" class=textinp type="Text" <? if (IsSet($uservalue)) echo 'value="'.$uservalue.'"'; ?>></span>
</div>
<div class=row>
<span class=label2 style="color:white">Password:</span>
<span class=formw style="width:77%"><input name="password" class=textinp type="password"></span>
</div>
<div class=row>
<span class=fullwidth><input type="submit" name="submit" value="Login">&nbsp;<input type=submit value="Get New Username" name="submit"></span>
</div>
<div class=clearer>&nbsp;</div>
</form>
<?
	if (IsSet($err))
	{ ?>
<div class=error>
<p>Error: Invalid Password or Username, try again<br></p>
</div>
<? } ?>
<a href="index.php">Return</a> to the home page.
</div>
</body>
</html>


