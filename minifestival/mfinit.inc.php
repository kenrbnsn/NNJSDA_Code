<?php
	$this_years_sched = true;
	$which = "39<sup>th</sup>";
	$which1 = "39th";
	$mini_date = '11/10/2013';
	$when = date("l, F j, Y",strtotime($mini_date));
	$when_year = date('Y',strtotime($mini_date));
	$sched_year = ($this_years_sched)?$when_year:$when_year - 1;
	$tmp = array();
	$tmp[] = '<span style="text-decoration:underline">Tentative Location</span>: Morris Knolls High School<br>50 Knoll Drive<br>Rockaway, NJ 07866<br>';
	$tmp[] = '<span style="display:block;padding-top:0.5em">';
	//$tmp[] = '<a id="raffle" href="http://minifestival.nnjsda.org/Raffle prizes.pdf" target="_blank"><button>Raffle<br>Prizes</button></a>';
	//$tmp[] = '<button id="schedule_btn">'. $sched_year . '<br>Schedule</button>';
/*
	if ($this_years_sched) {
		$tmp[] = '<button id="print_sched" url="Mini_Festival_Dance_Schedule_' . $when_year . '.pdf" target="_blank">Schedule<br>&nbsp;</button>';
	}
*/
//	$tmp[] = '<button class="bio" id="vic" url="Bio - Vic Ceder.pdf" target="_blank">Vic Ceder\'s<br>Bio</button>';
//	$tmp[] = '<button class="bio" id="butch" url="Bio - Butch Adams.pdf" target="_blank">Butch Adams\'<br>Bio</button>';
//	$tmp[] = '<button class="bio" id="don" url="Bio - Don Beck.pdf" target="_blank">Don Beck\'s<br>Bio</button>';
//	$tmp[] = '<a id="callers_bios" href="http://minifestival.nnjsda.org/2012 Mini-Festival caller bios.pdf" target="_blank"><button>Callers<br>Bios</button></a>';
	$tmp[] = '<button id="rest" url="Restaurants.pdf" target="blank">Nearby<br>Restaurants</button>';
	$tmp[] = '<a id="mf_directions" href="http://minifestival.nnjsda.org/Directions2012.pdf" target="_blank"><button>Directions<br>(revised)</button></a>';
//	$tmp[] = '<button id="ol_reg_form" value="registration.php">Online<br>Registration</button>';
//	$tmp[] = '<button id="reg_form" url="minifestival' . $when_year . '.pdf">Printable<br>Registration Form</button>';
	$tmp[] = '<a id="prev_pic" href="http://www.nnjsda.org/minifestival/pictures/" target="_blank"><button>Pictures<br>&nbsp;</button></a>';
	$tmp[] = '<button id="nnjsda_home" url="http://nnjsda.org">NNJSDA<br>Home Page</button>';
	$tmp[] = '</span>';
	$location = implode("\n",$tmp);
	$maincaller = "Dan Preedy";
	$maincallerloc = "Washington";
	$othercaller1 = "Tom Miller";
	$othercallerloc1 = "Pennsylvania";
	$othercaller2 = "Randy Page";
	$othercallerloc2 = "North Carolina";
	$cuer = "TBD";
	$cuerloc = "tbd";
?>
