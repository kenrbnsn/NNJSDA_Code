<?php
$ver = '(v0.0.1)';
$pageaddr = 'update.freeloaders';
$subject = 'Update Freeloaders';
include_once('../emailtracker.inc.php');
include('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname);

if (isset($_POST['u'])) {
	parse_str($_POST['vals'],$form_vals);
	if (strlen(trim($form_vals['year'])) < 4 || intval(trim($form_vals['year'])) < 2010) {
		exit (json_encode(array('ret'=>'not ok','error'=>'Bad Year','value'=>$form_vals['year'])));
	}
	if (strlen(trim($form_vals['name'])) < 1) {
		exit (json_encode(array('ret'=>'not ok','error'=>'Bad Name','value'=>$form_vals['name'])));
	}
	$name = mysql_real_escape_string(trim($form_vals['name']));
	$date = intval($form_vals['year']) . '-01-01';
	$q = "insert into freeloaders set `year` = '" . $date . "', `name` = '" . $name . "'";
	$rs = mysql_query($q) or die(json_encode(array('ret'=>'not ok','error'=>mysql_error(),'value'=>$q)));
	exit(json_encode(array('ret'=>'ok','message'=>'Added ' . stripslashes($name))));
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
						'Add Freeloader' : function() {
							$.post("<?php echo $_SERVER['PHP_SELF']?>",{u: 1,vals: $('#fl_form').serialize()}, function(data) {
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
							$('#year').val('<?php echo date('Y');?>');
							$('#fl_form').dialog('open');
							break;
					}
				});
			});
		</script>
	</head>
	<body>
		<button class="action" id="add">Add Freeloader</button>&nbsp<button class="action" id="update">Update Freeloader</button>&nbsp<button class="action" id="delete">Delete Freeloader</button>
		<form action="" method="post" display="none" id="fl_form">
			<p>
				<label for="name">Name:</label><input class="inp" type="text" id="name" name="name">
				<label for="year">Year:</label><input class="inp" type="text" id="year" name="year" value="<?php echo date('Y');?>">
			</p>
		</form>
	</body>
</html>


