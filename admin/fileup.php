<?php

	echo "<pre>";
	var_dump($_POST);


if (IsSet($_FILES)) {
	echo "<br>";
	var_dump($_FILES); }
echo "</pre>\n";

 ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>File Up Load Test</title>
</head>

<body>
<form enctype="multipart/form-data" method="post" action=<? echo $_SERVER['PHP_SELF'] ?>>
File: <input type=file name=filelist size=80><br>
<input type=hidden value="This is a test" name=hidden>
<input type=submit name=submit>
</form>


</body>
</html>
