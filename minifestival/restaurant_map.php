<?php
$subject = "The Minifestival Restaurants";
$ver = '(1.0.0)';
$pageaddr = 'minifestival.restaurants';
if (file_exists('emailtracker.inc.php')) include('emailtracker.inc.php');
if (isset($_POST['trace'])) exit('ok');
$api_key = ($_SERVER['HTTP_HOST'] == 'localhost') ? 'ABQIAAAAIt1KZzXv_lkvn9ag5wjKsxT2yXp_ZAY8_ufC3CFXhHIE1NvwkxRkPoXaNMR2mHO46fAeKRTg3dLQSw': 'ABQIAAAAIt1KZzXv_lkvn9ag5wjKsxT9FZPKEn4Xa9BJXr08E39Wx9xIrRT4JmjJiI5kDfrmDFDTDcXIbMbVsQ';
include('dbconfig.php');
$minifestival_addr = urlencode('Foothill Rd and Merriwood Rd, Bridgewater, NJ 08807');
$url = "http://local.yahooapis.com/MapsService/V1/geocode?appid=BW3csYbV34FLXr1vTB2oOWKWA7L0Ddb8ujg31FJ2seKvqeI0tnxHpeHE7L3oKVg-&street=Foothill%20Rd%20and%20Merriwood%20Rd&city=Bridgewater&state=NJ&zip=08807&output=php";
$page = file_get_contents($url);
$xml = unserialize($page);
//$xml = new SimpleXMLElement($page);
$minifestival_lat = $xml['ResultSet']['Result']['Latitude'];
$minifestival_long = $xml['ResultSet']['Result']['Longitude'];
$tmp = array(urldecode($minifestival_addr),$xml['ResultSet']['Result']['Latitude'],$xml['ResultSet']['Result']['Longitude']);
$minifestival_place = json_encode($tmp);
$q = "select * from nnjsda_restaurants where event = 'minifestival'";
$rs = mysql_query($q) or die("Problem with the query:<span style='color:red;font-weight:bold'>$q</span><br />" . mysql_error());
$rests = array();
$i = 0;
while ($rw = mysql_fetch_assoc($rs)) {
	$rests[$i] = array();
	$rests[$i]['name'] = htmlentities($rw['restaurant_name'],ENT_QUOTES);
	$rests[$i]['address'] = $rw['restaurant_address'];
	$rests[$i]['website'] = $rw['restaurant_website'];
	$rests[$i]['lat'] = $rw['latitude'];
	$rests[$i]['long'] = $rw['longitude'];
	$rests[$i]['phone'] = $rw['restaurant_phone'];
	$rests[$i]['type'] = $rw['restaurant_type'];
	$i++;
}
$json_rests = json_encode($rests);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
	<title>The Minifestival Restaurants</title>
	<style type="text/css">
	body, html {
		font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
		font-size: 100%;
		padding: 0;
		margin: 0;
	}
	
	#map {
		display: block;
		width: 80%;
		height: 600px;
		margin-left: auto;
		margin-right: auto;
		margin-top: 0.5em;
	}
	
	#buttons {
		display: block;
		width: 80%;
		margin-left: auto;
		margin-right: auto;
		margin-top: 0.5em;		
	}
	
	#hdr {
		display: block;
		width: 80%;
		margin-left: auto;
		margin-right: auto;
		margin-top: 0.5em;
		border-bottom: 1px solid black;
		padding-bottom:0.5em;
		font-weight: bold;
		text-align: center;
	}
	.map_canvas {
		display: block;
		width: 80%;
		height: 480px;
		border: 1px solid black;
		margin-left: auto;
		margin-right: auto;
	}
	
	#directionsFooter {
		display: block;
		width: 80%;
		margin-left: auto;
		margin-right: auto;		
	}
	.dpanel {
		display:block;
		width:100%;
	}
	
	.route {
		display: block;
		width: 80%;
		border: 1px solid black;
		margin-left: auto;
		margin-right: auto;		
	}
	
	</style>
	<script src="jquery-1.2.6.js" type="text/javascript"></script>
	<script src="http://www.google.com/jsapi?key=<?php echo $api_key ?>" type="text/javascript"></script>
	<script src="mapiconmaker.js" type="text/javascript"></script>
	<script type="text/javascript">
			var rests = eval('(<?php echo $json_rests; ?>)');
			var minifestival_place = eval('(<?php echo $minifestival_place; ?>)');
			google.load("maps", "2");


	function init() {
		$('#directions_panel').hide();
		var dbh = $(document.body).height() - 200;
		$('#map').height(dbh + 'px');
		$('#map_canvas').height(dbh + 'px');
		var dmap = new google.maps.Map2(document.getElementById("map_canvas"));
		dmap.addControl(new GSmallMapControl());
		var directionsPanel;
		var directions;
		directionsPanel = document.getElementById("route");
		directions = new GDirections(dmap, directionsPanel);
      var map = new google.maps.Map2(document.getElementById("map"));
		var markers = new Array();
		var gmarkers = [];
		var start_end = []
		map.clearOverlays();
      map.addControl(new GLargeMapControl());
      map.addControl(new GMapTypeControl());
		map.setCenter(new GLatLng(0,0),0);
      var reasons=[];
      reasons[G_GEO_SUCCESS]            = "Success";
      reasons[G_GEO_MISSING_ADDRESS]    = "Missing Address: The address was either missing or had no value.";
      reasons[G_GEO_UNKNOWN_ADDRESS]    = "Unknown Address:  No corresponding geographic location could be found for the specified address.";
      reasons[G_GEO_UNAVAILABLE_ADDRESS]= "Unavailable Address:  The geocode for the given address cannot be returned due to legal or contractual reasons.";
      reasons[G_GEO_BAD_KEY]            = "Bad Key: The API key is either invalid or does not match the domain for which it was given";
      reasons[G_GEO_TOO_MANY_QUERIES]   = "Too Many Queries: The daily geocoding quota for this site has been exceeded.";
      reasons[G_GEO_SERVER_ERROR]       = "Server error: The geocoding request could not be successfully processed.";
      reasons[G_GEO_BAD_REQUEST]        = "A directions request could not be successfully parsed.";
      reasons[G_GEO_MISSING_QUERY]      = "No query was specified in the input.";
      reasons[G_GEO_UNKNOWN_DIRECTIONS] = "The GDirections object could not compute directions between the points.";

      // === catch Directions errors ===
      GEvent.addListener(dmap, "error", function() {
        var code = directions.getStatus().code;
        var reason="Code "+code;
        if (reasons[code]) {
          reason = reasons[code]
        } 

        alert("Failed to obtain directions, "+reason);
      });

	
		var bounds = new GLatLngBounds();
		for (i in rests) {
			showLocation(rests[i],rests[i].lat,rests[i].long);
		}
	
		
		showGatheringLocation(minifestival_place);
		map.setZoom(map.getBoundsZoomLevel(bounds));
		map.setCenter(bounds.getCenter());

		$('#show_hide_ff').click(function(){
				$.post("<?php echo $_SERVER['PHP_SELF'] ?>", { trace: true, show_hide_ff: $('#show_hide_ff').text() })
				if ($('#show_hide_ff').text() == 'Hide Fast Food') {
					hide('fast food');
					$('#show_hide_ff').text('Show Fast Food');
				} else {
					show('fast food');
					$('#show_hide_ff').text('Hide Fast Food');
				}
				$('#show_all').show();
			});
			
		$('#show_hide_sd').click(function(){
				$.post("<?php echo $_SERVER['PHP_SELF'] ?>", { trace: true, show_hide_sd: $('#show_hide_sd').text() })
				if ($('#show_hide_sd').text() == 'Hide Sit Down') {
					hide('sit down');
					$('#show_hide_sd').text('Show Sit Down');
				} else {
					show('sit down');
					$('#show_hide_sd').text('Hide Sit Down');
				}
				$('#show_all').show();
			});
			
		$('#show_all').click(function() {
				$.post("<?php echo $_SERVER['PHP_SELF'] ?>", { trace: true, show_all_clicked: true })
				show('fast food');
				show('sit down');
				$('#show_all').hide();
				$('#show_hide_ff').text('Hide Fast Food');
				$('#show_hide_sd').text('Hide Sit Down');
			});
			
		$('#hide_map').click(function(event) {
				var action = $('#hide_map').text();
				if (action == 'Hide Map') {
					$('#map').hide();
					$('#hide_map').text('Show Map');
					$('#show_hide_ff').hide();
					$('#show_hide_sd').hide();
				} else {
					$('#map').show();
					$('#hide_map').text('Hide Map');
					$('#show_hide_ff').show();
					$('#show_hide_sd').show();
				}
		});
		$('#hideDirections').click(function(event) {
		 	$('#directions_panel').hide();
			$('#map').show();
			$('#hide_map').text('Hide Map');
			$('#show_hide_ff').show();
			$('#show_hide_sd').show();
			$('#hideDirections').hide();
		});
	
      function show(category) {
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].mycategory == category) {
            gmarkers[i].show();
          }
        }
		 }

      function hide(category) {
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].mycategory == category) {
            gmarkers[i].hide();
          }
        }
		 }
					
	function showLocation(wc,lat,long) {
			var html = '<span style="font-weight:bold">' + wc.name + '</span><br />';
			html += 'Address: ' + wc.address + '<br />';
			if (wc.phone != '') html += 'Phone: ' + wc.phone + '<br />';
			if (wc.website != '') html += '<a href="http://' + wc.website + '">' + wc.website + '</a><br />';
			html += 'Type: ' + wc.type + '<br />';
			html += 'Directions: <button id="to_dcc">To</button>/<button id="from_dcc">From</button> the Bridgewater Raritan Middle School<br />';
			html += 'Directions in General: <a id="gen_to" href="#">To</a>, <a id="gen_from" href="#">From</a><br />';
			html += '<span id="gen_to_addr" style="display:none"><input id="gen_to_value" type="text" /></span>';
			html += '<span id="gen_from_addr" style="display:none"><input id="gen_from_value" type="text" /></span>';
			var rn = wc.name;
			var point = new GLatLng(lat,long);
			iColor = (wc.type == 'sit down')?'#0000ff':'#00ff00';
			var newIcon = MapIconMaker.createMarkerIcon({width: 32, height: 32, primaryColor: iColor});
         var marker = new GMarker(point, {icon: newIcon});
		   marker.myname = rn;
			marker.mycategory = wc.type;
			bounds.extend(point);
			map.addOverlay(marker);
        GEvent.addListener(marker, "click", function() {
          marker.openInfoWindowHtml(html);
			 $.post("<?php echo $_SERVER['PHP_SELF'] ?>", { trace: true, marker_clicked: wc.name });
			 $('#gen_to').click(function(event){
			 	$('#gen_to_addr').show();
			 
			 });
			 $('#to_dcc').click(function(event){
			 				var whereTo = wc.address + ' to ' + minifestival_place[0];
							$.post("<?php echo $_SERVER['PHP_SELF'] ?>", { trace: true, whereTo: whereTo });
  							$('#directions_panel').hide();
							marker.closeInfoWindow();
							dmap.clearOverlays();
							dmap.setCenter(point, 15);
							$('#route').html('');
							$('#map').hide();
							$('#directions_panel').show();
							$('#hideDirections').show();
							dmap.checkResize();
							$('#hide_map').text('Show Map');
							$('#show_hide_ff').hide();
							$('#show_hide_sd').hide();
							directionsPanel = document.getElementById("route");
							directions = new GDirections(dmap, directionsPanel);
							directions.load(wc.address + " to " + minifestival_place[0]);
					});
			 $('#from_dcc').click(function(event){
			 				var whereTo = 'from: ' + minifestival_place[0] + ' to: ' + wc.address;
							$.post("<?php echo $_SERVER['PHP_SELF'] ?>", { trace: true, whereTo: whereTo });
  							$('#directions_panel').hide();
							marker.closeInfoWindow();
							dmap.clearOverlays();
							dmap.setCenter(point, 15);
							$('#route').html('');
							$('#map').hide();
							$('#directions_panel').show();
							$('#hideDirections').show();
							dmap.checkResize();
							$('#hide_map').text('Show Map');
							$('#show_hide_ff').hide();
							$('#show_hide_sd').hide();
							directionsPanel = document.getElementById("route");
							directions = new GDirections(dmap, directionsPanel);
							directions.load(minifestival_place[0] + " to " + wc.address);
					});
        });
		   gmarkers.push(marker);
		}		

	function showGatheringLocation(pl) {
			var html = '<span style="font-weight:bold">Bridgewater-Raritan Middle School</span><br />';
			html += pl[0] + '<br />';
			html += 'GPS Coordinates: ' + pl[1] + ', ' + pl[2];
			var point = new GLatLng(pl[1],pl[2]);
			var newIcon = MapIconMaker.createMarkerIcon({width: 32, height: 32, primaryColor: "#ff0000"});
         var marker = new GMarker(point, {icon: newIcon});
		   marker.myname = 'Minifestival Location';
			bounds.extend(point);
			map.addOverlay(marker);
        GEvent.addListener(marker, "click", function() {
		    $.post("<?php echo $_SERVER['PHP_SELF'] ?>", { trace: true, marker_clicked: marker.myname });
          marker.openInfoWindowHtml(html);
        });
		}		
	}
  google.setOnLoadCallback(init);
	</script>
</head>

<body>
<div id="hdr">Minifestival Restaurants</div>
<div id="map"></div>
<div id="buttons">
<button id="hide_map">Hide Map</button>
<button id="show_hide_ff">Hide Fast Food</button>
<button id="show_hide_sd">Hide Sit Down</button>
<button id="show_all" style="display:none">Show All</button>
<button id="hideDirections" style="display:none">Hide Directions</button></div>
		<div id="directions_panel" class="dpanel">
			<div id="map_canvas" class="map_canvas"></div>
			<div id="route" class="route"></div>
		</div>
</body>
</html>