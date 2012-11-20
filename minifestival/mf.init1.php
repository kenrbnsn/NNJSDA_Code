<?
	$which = "34<sup>th</sup>";
	$which1 = "34th";
	$mini_date = '11/9/2008';
	$when = date("l, F j, Y",strtotime($mini_date));
	$when_year = date('Y',strtotime($mini_date));
	$tmp = array();
	$tmp[] = '<span style="text-decoration:underline">Location</span>: Bridgewater Middle School<br>Bridgewater, NJ<br>';
	$tmp[] = '<span style="display:block;padding-top:0.5em">';
	$tmp[] = '<button id="schedule_btn">2008<br>Schedule</button>';
//	$tmp[] = '<button id="print_sched" value="Mini_Festival_Dance_Schedule_' . $when_year . '.pdf">Printable<br>Schedule</button>';
	$tmp[] = '<button id="rest">Map with<br>Nearby Restaurants</button>';
	$tmp[] = '<button id="mf_directions" value="minifestivalmap.php?dir=0">Directions<br>&nbsp;</button>';
	$tmp[] = '<button id="ol_reg_form" value="registration.php">Online<br>Registration</button>';
	$tmp[] = '<button id="reg_form" value="minifestival' . $when_year . '.pdf">Printable<br>Registration Form</button>';
	$tmp[] = '</span>';
	$location = implode("\n",$tmp);
	$maincaller = "Bob Baier";
	$maincallerloc = "Texas";
	$othercaller1 = "Betsy Gotta";
	$othercallerloc1 = "New Jersey";
	$othercaller2 = "Tom Miller";
	$othercallerloc2 = "Pennsylvania";
	$cuer = "Mary McGee";
	$cuerloc = "Pennsylvania";
?>
