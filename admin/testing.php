	<?
	$str = "3rd tuesday";
	$this_year = date("Y",strtotime("today"));
	echo "<br>Every $str<br>\n";
	$mnth = date("F Y",strtotime("1 oct $this_year"));
	$enddate = strtotime("21 oct 2004");
	$checkdate = strtotime("1 $mnth");
	echo "\$mnth = $mnth<br>\n";
	
/*	for ($i=0;$i<10;$i++)
*/
	while ($checkdate < $enddate)
		{
		$event_date = strtotime($str,strtotime("1 $mnth"));
		$mnth = date("F Y",strtotime("32 $mnth"));
		echo "\$event_date = ".date("F j, Y",$event_date)."  \$mnth = $mnth<br>\n";
		$checkdate = strtotime("1 $mnth");
		echo "\$checkdate = $checkdate ... \$enddate = $enddate<br><hr>\n";
		}
		
?>