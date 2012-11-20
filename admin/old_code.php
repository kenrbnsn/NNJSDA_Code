<? if (IsSet($todo)) { ?>
<h2><? echo $headers[$todo]; ?></h2>
<hr> 
<? if ($todo == "updevent") { ?>
<a class=abox1a href=<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showadd>Add New Events</a>
<? if ($action == "showadd") { ?>
<br><a class=eventlink href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showadd&amp;eventmonth=<? echo $curr_month; ?>'><? echo $curr_month; ?></a>
<a class=eventlink href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showadd&amp;eventmonth=<? echo $next_month; ?>'><? echo $next_month; ?></a>
<span class=blue>Select Date:</span>
<form class=select method="post" action="<? echo $PHP_SELF; ?>">
<input name=todo type=hidden value=<? echo $todo ?>>
<input name=action type=hidden value="showadd">
Month:<select name=event_month size=1>
<option value=""></option>
<? put_months(); ?>
</select><br>
Year:<select name=event_year size=1>
<option value=""></option>
<?
	$cur_year = date("Y",strtotime("now"));
	for ($i=$cur_year;$i<$cur_year+10;$i++){
		$selected = (IsSet($_GET['input_date'])) ? is_selected($i,$_GET['input_date'],"Y") : "";
		echo "<option value=$i ".$selected.">$i</option>\n"; }?>
</select>
<input type=submit value="Show Date">
</form>
<!-- <a class=eventlink href='addevent.php'>Select Date</a> -->
<? } ?>
<br>
<a class=abox1a href=<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showdel>Delete Events</a>
<? if ($action == "showdel") { ?>
<br><a class=eventlink href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showdel&amp;eventmonth=<? echo $curr_month; ?>'><? echo $curr_month; ?></a>
<a class=eventlink href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showdel&amp;eventmonth=<? echo $next_month; ?>'><? echo $next_month; ?></a>
<span class=blue>Select Date:</span>
<form class=select method="post" action="<? echo $PHP_SELF; ?>">
<input name=todo type=hidden value=<? echo $todo ?>>
<input name=action type=hidden value="showdel">
Month:<select name=event_month size=1>
<option value=""></option>
<? put_months(); ?>
</select><br>
Year:<select name=event_year size=1>
<option value=""></option>
<?
	$cur_year = date("Y",strtotime("now"));
	for ($i=$cur_year;$i<$cur_year+10;$i++){
		$selected = (IsSet($_GET['input_date'])) ? is_selected($i,$_GET['input_date'],"Y") : "";
		echo "<option value=$i ".$selected.">$i</option>\n"; }?>
</select>
<input type=submit value="Show Date">
</form>
<!-- <a class=eventlink href='modievent.php?action=delete'>Select Date</a> -->
<? } ?>
<br>
<a class=abox1a href=<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showedi>Edit Events</a>
<? if ($action == "showedi") { ?>
<br><a class=eventlink href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showedi&amp;eventmonth=<? echo $curr_month; ?>'><? echo $curr_month; ?></a>
<a class=eventlink href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&amp;action=showedi&amp;eventmonth=<? echo $next_month; ?>'><? echo $next_month; ?></a>
<span class=blue>Select Date:</span>
<form class=select method="post" action="<? echo $PHP_SELF; ?>">
<input name=todo type=hidden value=<? echo $todo ?>>
<input name=action type=hidden value="showedi">
Month:<select name=event_month size=1>
<option value=""></option>
<? put_months(); ?>
</select><br>
Year:<select name=event_year size=1>
<option value=""></option>
<?
	$cur_year = date("Y",strtotime("now"));
	for ($i=$cur_year;$i<$cur_year+10;$i++){
		$selected = (IsSet($_GET['input_date'])) ? is_selected($i,$_GET['input_date'],"Y") : "";
		echo "<option value=$i ".$selected.">$i</option>\n"; }?>
</select>
<input type=submit value="Show Date">
</form>
<!-- <a class=eventlink href='modievent.php?action=update'>Select Date</a> -->
<? } ?>
<br>
<? }
if ($todo == "dispclub") { ?>
<form method="post" action=<? echo $PHP_SELF ?>>
<input type=hidden name=todo value=<? echo $todo ?>>
<select name="cluborg" size="1">
<? get_club_names(); ?>
</select>
<input type="submit" name=submit value="Show Information">
<? }
if (($todo == "updclub") || ($todo == "check_updclub")) { ?>
<form method="post" action=<? echo $PHP_SELF ?>>
<input type=hidden name=todo value=updclub>
<select name="cluborg" size="1">
<option value=" "> </option>
<? get_club_names(); ?>
</select>
<input type="submit" name=submit value="Update Information">
<input type="submit" name=submit value="Delete Club">
<input type="submit" name=submit value="Add Club">
<? }
if ($todo == "dispevent") {
?>
<!-- <a class=abox1a href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&action=<? echo $past_month; ?>'><? echo $past_month; ?></a><br> -->
<a class=abox1a href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&action=<? echo $curr_month; ?>'><? echo $curr_month; ?></a><br>
<a class=abox1a href='<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&action=<? echo $next_month; ?>'><? echo $next_month; ?></a><br>
<span class=blue>Select Date:</span>
<form class=select method="post" action="<? echo $PHP_SELF; ?>">
<input name=todo type=hidden value=<? echo $todo ?>>
<input name=action type=hidden value="select">
Month:<select name=event_month size=1>
<option value=""></option>
<? put_months(); ?>
</select><br>
Year:<select name=event_year size=1>
<option value=""></option>
<?
	$cur_year = date("Y",strtotime("now"));
	for ($i=$cur_year;$i<$cur_year+10;$i++){
		$selected = (IsSet($_GET['input_date'])) ? is_selected($i,$_GET['input_date'],"Y") : "";
		echo "<option value=$i ".$selected.">$i</option>\n"; }?>
</select>
<input type=submit value="Show Date">
</form>
<!-- <a class=abox1a href=<? echo $PHP_SELF; ?>?todo=<? echo $todo; ?>&action=select>Select  Month</a><br>-->

<? }
if (($todo == "updcontacts") || ($todo == "dispcontacts")) {
disp_contact_boxes($todo); }
}?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title></title>
</head>

<body>



</body>
</html>
