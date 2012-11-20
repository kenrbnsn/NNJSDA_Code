<?php
	include('dbconfig.php');
	$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
	$db = mysql_select_db($dbname);
	$subject = "Add Calls";
	$ver = '(v0.0.1)';
	$pageaddr = 'add.calls';
	include('emailtracker.inc.php');
	if (isset($_POST['uc'])) {
		parse_str($_POST['vals'],$form_vals);
		$qtmp = array();
		$errors = array();
		foreach ($form_vals as $k=>$v) {
			switch($k) {
				case 'callerlab_num':
					if (strlen(trim($v)) > 0) {
						$qtmp[] = $k . ' = ' . (int)$v;
					} else {
						$errors[] = 'Callerlab Number is required';
					}
					break;
				case 'call':
				case 'description':
					if (strlen(trim($v)) > 0) {
						$qtmp[] = $k . " = '" . mysql_real_escape_string($v) . "'";
					} else {
						$errors[] = ucwords($k) . ' is required';
					}
					break;
				default:
					$errors[] = "Invalid form entry found: $k => $v";
			}
		}
		if (!empty($errors)) {
			exit(json_encode(array('ret'=>'Not OK','errors'=>implode("<br>\n",$errors))));
		} else {
			$q = 'insert into calls set ' . implode(', ', $qtmp);
			$rs = mysql_query($q);
			if (!$rs) {
				exit(json_encode(array('ret'=>'Not OK','errors'=>"Problem with the query: $q<br>\n" . mysql_error())));
			}
			exit(json_encode(array('ret'>'OK','msg'=>"Call {$form_vals['call']} inserted OK")));
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
	<head>
		<title>Store Callerlab Calls</title>
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

			#call_form {
				display: block;
				width: 80%;
				margin-right: auto;
				margin-left: auto;
				margin-top: 1em;
			}
		</style>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#call_form')[0].reset();
				$('#submit_form').click(function() {
					$.post("<?php echo $_SERVER['PHP_SELF'] ?>",{uc:1,vals:$("#call_form").serialize()},
						function(data) {
							if (data.ret != 'OK') {
								alert(data.errors);
								return false;
							} else {
								alert(data.msg);
								$('#call_form')[0].reset();
								return false;
							}
						},"json");
					return false;
				});
			});
		</script>
	</head>
	<body>
		<form id="call_form">
			<p>
				<div class="formrow">
					<label for="call">Call Name:</label><input class="inptxt" type="text" name="call" id="call">
				</div>
				<div class="formrow">
					<label for="description">Definition:</label><textarea class="inptxt" rows="10" name="description" id="description"></textarea>
				</div>
				<div class="formrow">
					<label for="callerlab_num">Callerlab Number:</label><input class="inptxt" type="text" name="callerlab_num" id="callerlab_num">
				</div>
				<div class="formrow">
					<button id="submit_form">Add Call</button>
				</div>
			</p>
		</form>
	</body>
</html>




