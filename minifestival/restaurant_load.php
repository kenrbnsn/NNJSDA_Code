<?php
$api_key = ($_SERVER['HTTP_HOST'] == 'localhost') ? 'ABQIAAAAIt1KZzXv_lkvn9ag5wjKsxT2yXp_ZAY8_ufC3CFXhHIE1NvwkxRkPoXaNMR2mHO46fAeKRTg3dLQSw': 'ABQIAAAAIt1KZzXv_lkvn9ag5wjKsxT9FZPKEn4Xa9BJXr08E39Wx9xIrRT4JmjJiI5kDfrmDFDTDcXIbMbVsQ';
include('dbconfig.php');
if (isset($_POST['submit'])) {
	$qtmp = array();
	$err = array();
	foreach ($_POST as $f => $v)
		switch ($f) {
			case 'restaurant_name':
			case 'restaurant_phone':
			case 'restaurant_website':
			case 'restaurant_type':
				if (strlen(trim(stripslashes($v))) != 0)
					$qtmp[] = $f . " = '" . mysql_real_escape_string(trim(stripslashes($v))) . "'";
				break;
			case 'restaurant_address':
				if (strlen(trim(stripslashes($v))) != 0) {
					$qtmp[] = $f . " = '" . mysql_real_escape_string(trim(stripslashes($v))) . "'";
					$url = "http://maps.google.com/maps/geo?q=" . urlencode(trim(stripslashes($v))) . "&output=xml&key=$api_key";
					$page = file_get_contents($url);
					$xml = new SimpleXMLElement($page);
					list($long,$lat) = explode(',',$xml->Response->Placemark->Point->coordinates);
					if ($lat == '' || $long == '') {
						$url = "http://local.yahooapis.com/MapsService/V1/geocode?appid=BW3csYbV34FLXr1vTB2oOWKWA7L0Ddb8ujg31FJ2seKvqeI0tnxHpeHE7L3oKVg-&location=" . urlencode(trim(stripslashes($v))) .  "&output=php";
						$page = file_get_contents($url);
						$xml = unserialize($page);
						$lat = $xml['ResultSet']['Result']['Latitude'];
						$long = $xml['ResultSet']['Result']['Longitude'];
					}					
					$qtmp[] = "latitude = '" . $lat . "'";
					$qtmp[] = "longitude = '" . $long . "'";
				}
				break;
		}
	if (!empty($qtmp)) {
		$qtmp = "event = 'minifestival'";
		$q = "insert into gathering_restaurants set " . implode(', ',$qtmp);
		$rs = mysql_query($q) or die("Problem with the query: $q at line " . __LINE__ . '<br>' . mysql_error());
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title></title>
	<style>
	body, html {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 100%;
		padding: 0;
		margin: 0;
	}
	
	form {
		display: block;
		width: 95%;
		margin-left: auto;
		margin-right: auto;
		margin-top: 1em;
	}
	
	fieldset {
		border: 1px solid gray;
	}
	
	legend {
		border: 1px solid gray;	
		font-weight: bold;
	}
	
	label {
		font-weight: bold;
	}
	.txt {
		width: 100%;
		margin-bottom: 0.5em;
	}
	
	.space {
		margin-bottom: 0.5em;
		line-height: 0.01em;
	}
	
	.sub {
		width: 100%;
		text-align: center;
	}
	</style>
</head>

<body>
<form method="post" action="">
<fieldset>
	<legend>Restaurant Information</legend>
	<label for="restaurant_name">Name:</label><br>
	<input class="txt"  name="restaurant_name" id="restaurant_name" type="text">	
	<label for="restaurant_address">Address:</label><br>
	<input class="txt"  name="restaurant_address" id="restaurant_address" type="text">	
	<label for="restaurant_phone">Phone:</label><br>
	<input class="txt"  name="restaurant_phone" id="restaurant_phone" type="text">
	<label for="restaurant_type">Type:</label><br>
	Sit Down: <input name="restaurant_type" id="restaurant_type_sd" type="radio" value="sit down" checked><br>	
	Fast Food: <input name="restaurant_type" id="restaurant_type_ff" type="radio" value="fast food"><br>
	<div class="space">&nbsp;</div>
	<label for="restaurant_website">Web Site:</label><br>
	<input class="txt"  name="restaurant_website" id="restaurant_website" type="text">
	<label for="sponsor">Sponsor:</label><br>
	No: <input name="sponsor" id="sponsor_no" type="radio" value="no" checked><br>	
	Yes: <input name="sponsor" id="sponsor_yes" type="radio" value="yes"><br>
	<div class="sub"><input type="submit" value="Add Restaurant" name="submit"></div>
</fieldset>
</form>


</body>
</html>
