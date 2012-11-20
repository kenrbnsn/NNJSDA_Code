<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Test Strtotime</title>
</head>

<body>
<h1><?echo $_GET['x']; if (IsSet($_GET['y'])) echo "<br>{$_GET['y']}"; ?></h1><hr>
<?
$now = strtotime("now");
if (IsSet($_GET['y'])) $now = strtotime($_GET['y']);
echo date('j M Y',strtotime($_GET['x'],$now));
 ?>



</body>
</html>
