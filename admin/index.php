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
extract ($_SERVER);

 ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">


<html>
<head>
	<title>NNJSDA Web Site Administration -- Login</title>
	<link href="nnjsda.css" type="text/css" rel="STYLESHEET">
	<META NAME="ROBOTS" CONTENT="NOINDEX">
</head>

<body class=admin>
<div id=hdr>
<h1>NNJSDA<br>Web Site Administration<br>Please Login</h1>
</div>
<div id=rest-of-page>
<form class=cp method="post" action="login.php">
<div class=row>
<span class=label2 style="color:white">Username:</span>
<span class=formw style="width:77%"><input class=textinp name="username" type="Text"></span>
</div>
<div class=row>
<span class=label2 style="color:white">Password:</span>
<span class=formw style="width:77%"><input class=textinp name="password" type="password"></span>
</div>
<div class=row>
<span class=fullwidth><input type="submit" name="Login" value="Login"></span>
</div>
<div class=clearer>&nbsp;</div>
</form>
</div>
</body>
</html>
