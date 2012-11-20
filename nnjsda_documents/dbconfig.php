<? 
$dbhost = "localhost";
$dbname = 'nnjsda_info';

 if ($_SERVER['SERVER_NAME'] != "localhost") {
		$dbuser = "nnjsda_kenrbnsn";
		$dbpass = "c0nn1e";
		}
	else {
		$dbuser = "root";
		$dbpass = "c0nn1e";
		}
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect! at line " . __LINE__ . "<br>" . mysql_error());
$db = mysql_select_db($dbname);
?>
