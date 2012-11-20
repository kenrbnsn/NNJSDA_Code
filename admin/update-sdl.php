<?php
	session_start();
	include('dbconfig.php');
	$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
	$db = mysql_select_db($dbname);
	if (!isset($_SESSION['files'])) {
		$_SESSION['files'] = array_reverse(array_map('basename',glob('../square-dance-lovers/*.pdf')));
	}
	if (isset($_POST['sess'])) {
		unset($_SESSION['files']);
		exit(json_encode($_SESSION));
	}
	if (isset($_POST['do'])) {
		if(!empty($_SESSION['files'])) {
			$nf = array_pop($_SESSION['files']);
			$name = basename($nf,'.pdf');
			$sort_name = array_pop(explode(' ',$name));
			exit(json_encode(array('ret'=>'Ok','filename'=>$nf,'names'=>$name,'sort_by_lastname'=>$sort_name)));
		} else {
			exit(json_encode(array('ret'=>'Not OK','msg'=>'No more files')));
		}
	}
	if (isset($_POST['up'])) {
		$qtmp = array();
		parse_str($_POST['vals'],$form_vals);
		foreach ($form_vals as $f => $v) {
			switch ($f) {
				case 'filename':
				case 'sort_by_lastname':
				case 'names':
				case 'notes':
					if (strlen(trim($v)) > 0) {
						$qtmp[] = "`$f` = '" . mysql_real_escape_string(trim($v)) . "'";
					}
					break;
			}
		}
		if (!empty($qtmp)) {
			$q = 'insert into lovers set ' . implode(', ',$qtmp);
			$rs = mysql_query($q);
			if (!$rs) {
				exit(json_encode(array('ret'=>'Not OK','query'=>$q,'error'=>mysql_error())));
			} else {
				exit(json_encode(array('ret'=>'Ok','msg'=>"{$form_vals['names']} inserted OK")));
			}
		}
		exit(json_encode(array('ret'=>'Not OK','query'=>'','error'=>'Oops, something went wrong, $qtmp is empty!')));
	}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Update Square Dance Lovers</title>
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

			.inptxt {
				display: block;
				width: 84%;
				float: left;
			}

		</style>
		<link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/themes/start/jquery-ui.css">
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript">
	    google.load("jquery", "1.7.1", {uncompressed:true});
	    google.load("jqueryui", "1.8.17", {uncompressed:true});
	  </script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#lovers').dialog({
					bgiframe: true,
					autoOpen: true,
					width: 700,
					modal: true,
					zIndex: 5,
					buttons: {
						'Get Next' : function() {
							$.post("<?php echo $_SERVER['PHP_SELF']?>",{do: 1}, function(data) {
								if (data.ret == 'Not OK') {
									alert(data.msg);
								} else {
									$('#names').val(data.names);
									$('#filename').val(data.filename);
									$('#sort_by_lastname').val(data.sort_by_lastname);
									$('#notes').val('');
								}
							},"json");
						},
						'Insert Record' : function() {
							$.post("<?php echo $_SERVER['PHP_SELF']?>",{up:1 , vals: $('#lovers').serialize()}, function(data) {
								if (data.ret == 'Not OK') {
									alert(data.query + ', ' + data.error);
								} else {
									alert(data.msg);
								}
							},"json");
						},
						'Reset Session': function() {
							$.post("<?php echo $_SERVER['PHP_SELF']?>",{sess:1}, function(data) {
									$('#names').val('');
									$('#filename').val('');
									$('#sort_by_lastname').val('');
									$('#notes').val('');
							},"json");
						},
						'Cancel' : function() {
							$(this).dialog('close');
						}
					}
				});

			});
		</script>
	</head>
	<body>
		<form id="lovers" action="" method="post">
			<p>
				<label for="names">Names:</label><input class="inptxt" name="names" id="names" value=""><br>
				<label for="notes">Notes:</label><input class="inptxt" name="notes" id="notes" value=""><br>
				<label for="sort_by_lastname">Sort by Name:</label><input class="inptxt" name="sort_by_lastname" id="sort_by_lastname" value=""><br>
				<label for="filename">Filename:</label><input class="inptxt" name="filename" id="filename" value="">
			</p>
		</form>
	</body>
</html>
