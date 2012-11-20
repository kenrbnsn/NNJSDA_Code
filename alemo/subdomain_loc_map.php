<?php
session_start();
$yui_version = '2.5.2';
$ver = '(v0.0.4)';
$pageaddr = 'subdomain.location.map';
$subject = 'Subdomain Locations Map';
if (file_exists('emailtracker.inc.php')) include('emailtracker.inc.php');
$api_key = ($_SERVER['HTTP_HOST'] == 'localhost') ? 'ABQIAAAAIt1KZzXv_lkvn9ag5wjKsxT2yXp_ZAY8_ufC3CFXhHIE1NvwkxRkPoXaNMR2mHO46fAeKRTg3dLQSw': 'ABQIAAAAIt1KZzXv_lkvn9ag5wjKsxTNvh1hoT_4KGiqPlR1KLhv10Ei0hQqLKT5V7YWcMUm1VdPEhHG62yeow';
$subdom = isset($_SESSION['subdom'])?$_SESSION['subdom']:'y-squares';
include('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect! $dbhost");
$db = mysql_select_db($dbname);
$q = "select * from subdomain_location where subdomain_name = '" . $subdom . "'";
$rs = mysql_query($q) or die("Problem with the query: $q on line " . __LINE__ . '<br>' . mysql_error());
$locs = array();
while ($rw = mysql_fetch_assoc($rs)) {
	$locs[$rw['location_name']] = $rw['address'] . ', ' . $rw['city'] . ', ' . $rw['state'] . ' ' . $rw['zip'];
}
$json_locs = json_encode(array_map('trim',$locs));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
	<title></title>
	<style type="text/css">
	body, html {
		font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
		font-size: 100%;
		padding: 0;
		margin: 0;
	}
	</style>
	<script type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo $api_key ?>" type="text/javascript"></script>
	<script type="text/javascript">
		var locs = eval('(<?php echo $json_locs; ?>)');
		google.load("maps", "2");

  function initialize() {
		var map = new google.maps.Map2(document.getElementById("eventMap"));
		map.clearOverlays();
		var geocoder = new GClientGeocoder();
		map.addControl(new GSmallMapControl());
		map.addControl(new GMapTypeControl());
		for (place in locs) {
			showLocation(place,locs[place]);
//			showAddress(place,locs[place]);
		}


    function showLocation(loc,address) {
      geocoder.getLocations(address,     function addAddressToMap(response) {
      if (!response || response.Status.code != 200) {
        alert("Sorry, we were unable to geocode that address");
      } else {
        place = response.Placemark[0];
        point = new GLatLng(place.Point.coordinates[1],
                            place.Point.coordinates[0]);
        var marker = new GMarker(point);
			map.setCenter(point, 11);
		  var html = "<span style='font-weight:bold'>" + loc + '</span><br />' + address;
			html += '<br /><form id="info_directions" action="javascript:void(0)">';
			html += '<input type="hidden" name="info_address" id="info_address" value="' + address + '" />';
			html += '<input type="hidden" name="info_lat" id="info_lat" value="' + place.Point.coordinates[1] + '" />';
			html += '<input type="hidden" name="info_long" id="info_long" value="' + place.Point.coordinates[0] + '" />';
			html += 'Directions from: <input size="40" type="text" name="info_from" id="info_from" />';
			html += '<br /><button id="info_get_directions">Go</button></form>';
        map.addOverlay(marker);
        GEvent.addListener(marker, "click", function() {
						          marker.openInfoWindowHtml(html);
									 });
      }
    });
    }

		function showAddress(loc,address) {
			if (geocoder) {
				geocoder.getLatLng(
				address,
				function(point) {
					if (!point) {
						alert(address + " not found");
					} else {
						alert(point);
						map.setCenter(point, 11);
						var marker = new GMarker(point);
						  var html = "<span style='font-weight:bold'>" + loc + '</span><br />' + address;
							html += '<br /><form id="info_directions" action="javascript:void(0)">';
							html += '<input type="hidden" name="info_address" id="info_address" value="' + address + '" />';
//							html += '<input type="hidden" name="info_lat" id="info_lat" value="' + lat + '" />';
//							html += '<input type="hidden" name="info_long" id="info_long" value="' + long + '" />';
							html += 'Directions from: <input size="40" type="text" name="info_from" id="info_from" />';
							html += '<br /><button id="info_get_directions">Go</button></form>';
						map.addOverlay(marker);
						GEvent.addListener(marker, "click", function() {
						          marker.openInfoWindowHtml(html);;
									 });
					}
					}
				);
			}
		}

  }
  google.setOnLoadCallback(initialize);
	</script>
</head>

<body>
		<div id="map_panel_markup" style="display:block;width:500px">
			<div class="hd" style="text-align:center;height:auto">Map of Dance Locations</div>
			<div class="bd" id="eventMap" style="display:block;height:500px;overflow:auto">&nbsp;</div>
			<div class="ft" id="eventMap_footer">
				<button id="hide_map">Hide Map</button>
			</div>
		</div>



</body>
</html>
