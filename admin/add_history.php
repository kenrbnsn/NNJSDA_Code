<?php
$subject = 'Add NNJSDA History';
$ver = '(v1.0.3)';
$pageaddr = 'add.nnjsda.history';
include('emailtracker.inc.php');
if (isset($_POST['trace'])) {
	exit(json_encode(array('ret'=>'ok')));
}
if (isset($_POST['ah'])) {
	include('../dbconfig.php');
	$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
	$db = mysql_select_db($dbname);
	parse_str($_POST['vals'],$form_vals);
		$qtmp = array();
		$errors = array();
		foreach ($form_vals as $k=>$v) {
			switch($k) {
				case 'period':
				case 'description':
					if ($k == 'period') {
						$k = str_replace(' ','_',$k);
					}
					if (strlen(trim(stripslashes(stripslashes($v)))) > 0) {
						$qtmp[] = $k . " = '" . mysql_real_escape_string(trim(stripslashes(stripslashes($v)))) . "'";
					} else {
						$errors[] = '<span style="color:red">' . ucwords($k) . '</span>';
					}
					break;
				default:
					$errors[] = "Invalid form entry found: $k => " . trim(stripslashes(stripslashes($v)));
			}
		}
		if (!empty($errors)) {
			exit(json_encode(array('ret'=>'Not OK','title'=>'Required Fields Missing','errors'=>implode("<br>\n",$errors))));
		} else {
			$q = 'insert into history set ' . implode(', ', $qtmp);
			$rs = mysql_query($q);
			if (!$rs) {
				exit(json_encode(array('ret'=>'Not OK','title'=>'Errors Found','errors'=>"Problem with the query: $q<br>\n" . mysql_error())));
			}
			exit(json_encode(array('ret'=>'OK','title'=>"History for {$form_vals['period']} Inserted OK",'msg'=>"<span style='color:red;font-weight:bold'>" . trim(stripslashes(stripslashes($form_vals['description']))) . "</span>")));
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
	<head>
		<title>Store NNJSDA History</title>
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css" type="text/css">
		<style type="text/css">
			html, body {
				margin: 0;
				border: 0;
				font-size:100%;
				font-family: sans-serif;
			}

			label {
				font-weight: bold;
				width: 15%;
				float: left;
				display: block;
			}

			.inptxt {
				width: 84%;
				float: left;
				display: block;
			}

			.formrow {
				clear:both;
				width: 100%;
				display: block;
			}

			#history_form {
				display: block;
				width: 80%;
				margin-right: auto;
				margin-left: auto;
				margin-top: 1em;
			}
		</style>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#history_form')[0].reset();
				$("#message").dialog({
						bgiframe: true,
						autoOpen: false,
						width: 700,
						modal: true,
						buttons: {
							'Ok': function() {
								$(this).dialog('close');
							}
						}
				});

				$('#submit_form').click(function() {
					$.post("<?php echo $_SERVER['PHP_SELF'] ?>",{ah:1,vals:$("#history_form").serialize()},
						function(data) {
							if (data.ret != 'OK') {
								$("#message" ).dialog( "option", "title", data.title );
								$('#message').html('<p>' + data.errors + '</p>');
								$('#message').dialog('open');
								return false;
							} else {
								$("#message" ).dialog( "option", "title", data.title );
								$('#message').html('<p>' + data.msg + '</p>');
								$('#message').dialog('open');
								$('#history_form')[0].reset();
								return false;
							}
						},"json");
					return false;
				});
			});
		</script>
	</head>
	<body>
		<form id="history_form">
			<p>
				<div class="formrow">
					<label for="period">Period:</label><input class="inptxt" type="text" name="period" id="period">
				</div>
				<div class="formrow">
					<label for="description">Description:</label><textarea class="inptxt" rows="20" name="description" id="description"></textarea>
				</div>
				<div class="formrow">
					<button id="submit_form">Add History</button>
				</div>
			</p>
		</form>
		<div id="message"></div>
	</body>
</html>