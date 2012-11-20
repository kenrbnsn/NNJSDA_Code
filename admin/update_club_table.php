<?php
include('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname); 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title></title>
</head>

<body>
<?php
$gq = "select * from new_clubs";
$rsg = mysql_query($gq) or die("Problem with query:$gq<br>" . mysql_error());
while ($rwg = mysql_fetch_assoc($rsg)) {
	echo 'Updating ... ' . urldecode($rwg['club']) . ' ... ';
	$uq = 'update new_clubs set ';
	$tmp = array();
	foreach ($rwg as $fld => $val)
		switch ($fld) {
			case 'club':
			case 'location':
			case 'address':
			case 'city':
			case 'county':
			case 'url':
			case 'caller':
			case 'cuer':
				$tmp[] = $fld . " = '" . mysql_real_escape_string(urldecode($val)) . "'";
				break;
			case 'contact_phone':
			case 'contact_email':
			case 'contact_name':
				$tmp[] = $fld . " = '" . mysql_real_escape_string(implode(',',unserialize($val))) . "'";
				break;
			case 'id':
				break;
			default:
				$tmp[] = $fld . " = '" . $val . "'";
		}
	$uq .= implode(', ',$tmp) . ' where id = ' . $rwg['id'];
	$urs = mysql_query($uq) or die("Problem with update query: $uq<br>" . mysql_error());
	echo "OK<br>\n";
}
?>
</body>
</html>
