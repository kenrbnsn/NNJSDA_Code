<?php
$ver = '(v0.0.5)';
$pageaddr = 'update.patrons';
$subject = 'Update Patrons';
include_once('../emailtracker.inc.php');
include('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname);

if (isset($_POST['a'])) {
	parse_str($_POST['vals'],$form_vals);
	if (strtotime($form_vals['date']) == 0) {
		exit (json_encode(array('ret'=>'not ok','error'=>'Bad Year','value'=>$form_vals['year'])));
	}
	if (strlen(trim($form_vals['name'])) < 1) {
		exit (json_encode(array('ret'=>'not ok','error'=>'Bad Name','value'=>$form_vals['name'])));
	}
	$name = mysql_real_escape_string(trim($form_vals['name']));
	$notes = mysql_real_escape_string(trim($form_vals['notes']));
	$date = mysql_real_escape_string(trim($form_vals['date']));
	$q = "insert into patrons set `year` = '$date', `name` = '$name', `notes`='$notes', `date_added` = NOW()";
	$rs = mysql_query($q) or die(json_encode(array('ret'=>'not ok','error'=>mysql_error(),'value'=>$q)));
	exit(json_encode(array('ret'=>'ok','message'=>'Added ' . stripslashes($name))));
}
if (isset($_POST['g'])) {
	parse_str($_POST['vals'],$form_vals);
	if (strtotime($form_vals['gyear']) == 0) {
		exit (json_encode(array('ret'=>'not ok','error'=>'Bad Year','value'=>$form_vals['gyear'])));
	}
	if (strlen(trim($form_vals['gname'])) < 1) {
		exit (json_encode(array('ret'=>'not ok','error'=>'Bad Name','value'=>$form_vals['gname'])));
	}
	$name = mysql_real_escape_string(trim($form_vals['gname']));
	$date = mysql_real_escape_string(trim($form_vals['gyear']));
	$q = "select `year`, notes, id as ind from patrons where name = '$name' and YEAR(`year`) = '$date'";
	$rs = mysql_query($q)  or die(json_encode(array('ret'=>'not ok','error'=>mysql_error(),'value'=>$q)));
	$html = array();
	$str = "<label>&nbsp;</label><span class='inp' style='line-height:2px;border-bottom: 1px solid black'><br></span>\n";
	$disp_name = htmlentities($form_vals['gname'],ENT_QUOTES);
	while($rw = mysql_fetch_assoc($rs)) {
		$tmp = array();
		$tmp[] = "<input type='hidden' name='{$rw['ind']}[up_name]' value='$disp_name'>";
		$tmp[] = "<label for='up_date_{$rw['ind']}'>Date:</label><input class='inp' type='text' value='{$rw['year']}' name='{$rw['ind']}[up_date]' id='up_date_{$rw['ind']}' readonly>";
		$tmp[] = "<label for='up_notes_{$rw['ind']}'>Notes:</label><input class='inp' type='text' value='{$rw['notes']}' id='up_notes_{$rw['ind']}' name='{$rw['ind']}[up_notes]'>";
		$html[]= implode("\n",$tmp);
	}
	exit(json_encode(array('ret'=>'ok',html=>"<h3>$name</h3>\n" . implode($str,$html))));
}

if (isset($_POST['u'])) {
	parse_str($_POST['vals'],$form_vals);
	foreach ($form_vals as $id => $info) {
		$disp_name = htmlentities($info['up_name'],ENT_QUOTES);
		$notes = mysql_real_escape_string($info['up_notes']);
		$q = "update patrons set notes = '$notes', `date_updated` = NOW() where id = $id";
		$rs = mysql_query($q) or die(json_encode(array('ret'=>'not ok','error'=>mysql_error(),'value'=>$q)));
	}
	exit(json_encode(array('ret'=>'ok',message=>"{$disp_name}'s patron information has been updated")));
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Update Freeloaders</title>
		<style type="text/css">
			body, html {
				font-size:100%;
				font-family:Arial, Helvetica, sans-serif;
				padding:0;
				margin:0;
			}

			form {
				font-size: 80%;
				width: 80%;
				}

			label {
				font-weight: bold;
				display:block;
				width: 15%;
				float: left;
				clear: both;
			}

			.inp {
				display: block;
				width: 84%;
				float: left;
			}
		</style>
		<link type="text/css" href="../jquery-ui-1.8.custom.css" rel="stylesheet">
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript">
			google.load("jquery", "1.4.2", {uncompressed:true});
			google.load("jqueryui", "1.8.0", {uncompressed:true});
		</script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#fl_form').dialog({
					bgiframe: true,
					autoOpen: false,
					width: 700,
					modal: true,
					zIndex: 5,
					buttons: {
						'Add Patron' : function() {
							$.post("<?php echo $_SERVER['PHP_SELF']?>",{a: 1,vals: $('#fl_form').serialize()}, function(data) {
								if (data.ret == 'not ok') {
									alert(data.error + ' -- ' + data.value);
								} else {
									alert(data.message);
								}
							},"json");
							$(this).dialog('close');
						},
						'Cancel' : function() {
							$(this).dialog('close');
						}
					}
				});
				$('#g_form').dialog({
					bgiframe: true,
					autoOpen: false,
					width: 700,
					modal: true,
					zIndex: 5,
					buttons: {
						'Get Patron Information' : function() {
							$.post("<?php echo $_SERVER['PHP_SELF']?>",{g: 1,vals: $('#g_form').serialize()}, function(data) {
								if (data.ret == 'not ok') {
									alert(data.error + ' -- ' + data.value);
								} else {
									$('#up_form_contents').html(data.html);
									$('#g_form').dialog('close');
									$('#up_form').dialog('open');
								}
							},"json");
//							$(this).dialog('close');
						},
						'Cancel' : function() {
							$(this).dialog('close');
						}
					}
				});
				$('#up_form').dialog({
					bgiframe: true,
					autoOpen: false,
					width: 700,
					modal: true,
					zIndex: 5,
					buttons: {
						'Update Patron' : function() {
							$.post("<?php echo $_SERVER['PHP_SELF']?>",{u: 1,vals: $('#up_form').serialize()}, function(data) {
								if (data.ret == 'not ok') {
									alert(data.error + ' -- ' + data.value);
								} else {
									alert(data.message);
								}
							},"json");
							$(this).dialog('close');
						},
						'Cancel' : function() {
							$(this).dialog('close');
						}
					}
				});
				$('.action').click(function() {
					switch($(this).attr('id')) {
						case 'add':
							$('#name').val('');
							$('#year').val('<?php echo date('Y-m-d');?>');
							$('#notes').val('');
							$('#fl_form').dialog('open');
							break;
						case 'update':
							$('#gname').val('');
							$('#gyear').val('<?php echo date('Y');?>');
							$('#g_form').dialog('open');
							break;
					}
				});
			});
		</script>
	</head>
	<body>
		<button class="action" id="add">Add Patron</button>&nbsp<button class="action" id="update">Update Patron</button>&nbsp<button class="action" id="delete">Delete Patron</button>
		<form action="" method="post" style="display:none" id="fl_form">
			<p>
				<label for="name">Name:</label><input class="inp" type="text" id="name" name="name">
				<label for="date">Year:</label><input class="inp" type="text" id="date" name="date" value="<?php echo date('Y-m-d');?>">
				<label for="notes">Notes:</label><input class="inp" type="text" id="notes" name="notes">
			</p>
		</form>
		<form action="" method="post" style="display:none" id="g_form">
			<p>
				<label for="gname">Name:</label><input class="inp" type="text" id="gname" name="gname">
				<label for="gyear">Year:</label><input class="inp" type="text" id="gyear" name="gyear">
			</p>
		</form>
		<form action="" method="post" style="display:none" id="up_form">
			<p>
				<span id="up_form_contents"></span>
			</p>
		</form>
	</body>
</html>


