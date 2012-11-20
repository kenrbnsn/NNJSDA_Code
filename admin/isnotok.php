<?
	session_start();
	$e = $_GET["e"];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>NNJSDA Web Site Administration -- Error</title>
	<link href="../nnjsda.css" type="text/css" rel="STYLESHEET">
</head>

<body class=admin>
<?
switch ($e) {
	case -1:
		$message = "No Such User.";
		break;
	case 0;
		$message = "Invalid username and/or password.";
		break;
	case 2:
		$message = "Unauthorized access.";
		break;
	case 5:
		$message = "Only Administrators can add new users.";
		break;
    case 6:
        $message = "Username <"  . $SESSION_NEWUSERNAME . "> already exists.";
        break;
	default:
		$message = "Something else is wrong!";
		break;
	}
?>
<div id=hdr>
<h1>Error Detected</h1>
</div>
<div id=rest-of-page>
<div class=newcenterarea3>
<p class=centerp><? echo $message; ?>
<br>
Please <a class=admina href="index.php"><span class=bold>log in</span></a> again.</p>
</div>
</div>
</body>
</html>

