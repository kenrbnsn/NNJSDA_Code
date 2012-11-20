<? 
$dbhost = "localhost";
$dbname = (basename($_SERVER['PHP_SELF'],'.php') == 'new_calendar.2.4.1')?'nnjsda_info':'nnjsda_subdoms';

 if ($_SERVER['SERVER_NAME'] != "localhost") {
		$dbuser = "nnjsda_kenrbnsn";
		$dbpass = "c0nn1e";
		}
	else {
		$dbuser = "root";
		$dbpass = "";
		}
?>
