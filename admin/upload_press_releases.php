<?php
$subject = 'Upload Press Releases';
$ver = '(v1.0.4)';
$pageaddr = 'upload.press.releases';
$show_files = true;
$errors = array();
$msg = '';
$max_upload_size = ini_get('upload_max_filesize');
if (file_exists('emailtracker.inc.php')) include('emailtracker.inc.php');
$valid_uploads = array('pdf' => array('application/force-download','application/pdf'),
							  'doc' => array('application/msword'),
							  'txt' => array('text/plain'),
							  'rtf' => array('application/msword'));
	if (isset($_POST['submit'])) {
		$upload_errors = array(
							UPLOAD_ERR_OK => 'There is no error, the file uploaded with success.',
							UPLOAD_ERR_INI_SIZE => 'The uploaded file is larger than ' . $max_upload_size . ' bytes',
							UPLOAD_ERR_FORM_SIZE => 'The uploaded file is larger than ' . $_POST['MAX_FILE_SIZE'] . ' bytes',
							UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.', 
							UPLOAD_ERR_NO_FILE => 'No file was uploaded.', 
							UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.', 
							UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.', 
							UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.');
		if ($_FILES['filespec']['errors'] > 0)
			$errors[] = $upload_errors[$_FILES['filespec']['errors']];
		if (strlen($_FILES['filespec']['name']) == 0)
			$errors[] = 'No file to upload';
		elseif (!in_array(pathinfo($_FILES['filespec']['name'],PATHINFO_EXTENSION),array_keys($valid_uploads)))
			$errors[] = 'Illegal file upload';
		if ($_POST['display_until'] != '')
			if (strtotime($_POST['display_until']) < time())
				$errors[] = 'Invalid Display Until date/time entered';
		if (strlen(trim(stripslashes($_POST['description']))) == 0)
			$errors[] = 'No description entered';
	if (!empty($errors))
		$msg = 'The following errors were found:<br><span style="color:red">' . implode("<br>\n",$errors) . '</span><br>';
	else {
		if (is_uploaded_file($_FILES['filespec']['tmp_name'])) {
			include('dbconfig.php');
			if (!isset($connect)) {
				$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
				$db = mysql_select_db($dbname);
			}
			$uploaddir = '../press_releases/';
			$uploadfile = $uploaddir . basename($_FILES['filespec']['name']);
			$q = "select * from press_releases where filename like '%" . mysql_real_escape_string(basename($_FILES['filespec']['name'])) . "%'";
			$rs = mysql_query($q) or die("Problem with the query: <span style='color:red'>$q</span> on line " . __LINE__ . '<br>' . mysql_error());
			if (mysql_num_rows($rs) > 0) {
				$rw = mysql_fetch_assoc($rs);
				$msg = '<span style="color:red">This file was uploaded before on <span style="font-weight:bold">' . date('l, F jS, Y \a\t g:i:s A',strtotime($rw['uploaded'])) . '</span> with a description of <span style="font-weight:bold">' . htmlentities($rw['description'],ENT_QUOTES) . '</span></span>';
			} else {
				$qtmp = array();
				foreach ($_POST as $fld => $val) {
					$val = stripslashes($val);
					switch ($fld) {
						case 'description':
							$qtmp[] = $fld . " = '" . mysql_real_escape_string(trim($val)) . "'";
							break;
						case 'display_until':
							if ($val != '')
								$qtmp[] = $fld . " = '" . date('Y-m-d H:i:s',strtotime($val)) . "'";
							break;
					}
				}
				$qtmp[] = "filename = '" . mysql_real_escape_string(basename($_FILES['filespec']['name'])) . "'";
				$qtmp[] = "uploaded = '" . date('Y-m-d H:i:s') . "'";
				$q = "insert into press_releases set " . implode(', ',$qtmp);
				$rs = mysql_query($q) or die("Problem with the query: <span style='color:red'>$q</span> on line " . __LINE__ . '<br>' . mysql_error());
				move_uploaded_file($_FILES['filespec']['tmp_name'], $uploadfile);
				$msg = 'The file <span style="font-weight:bold;color:blue">' . $_FILES['filespec']['name'] . '</span> was uploaded successfully<br>';
			}
		}
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>Upload NNJSDA Press Release</title>
	<style>
		body, html {
			font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
			padding:0;
			margin: 0;
		}
		fieldset {
			display: block;
			border:1px solid gray;
			width:90%;
			margin-top:1em;
			margin-bottom: 0.5em;
			margin-left: auto;
			margin-right: auto;
		}
		
		legend {
			border: 1px solid gray;
			font-weight: bold;
			text-align: center;
			padding-left: 1em;
			padding-right: 1em;
			background-color: pink;
		}
		
		form {
			display: block;
			width:90%;
			margin-left: auto;
			margin-right: auto;
		}
		
		.inp {
			display: block;
			width: 96%;
			margin-left: auto;
			margin-right: auto;
		}
		
		#hdr {
			display: block;
			width: 90%;
			margin-left: auto;
			margin-right: auto;
			border-bottom: 1px solid black;
			padding-top:0.5em;
			padding-bottom: 0.5em;
		}
		
		h1, h2 {
			text-align: center;
			padding: 0;
			margin: 0;
		}
		
		#subm {
			width: 100%;
			text-align: center;
		}
		
		#msgdiv {
			display: block;
			width: 50%;
			padding:1em;
			text-align: center;
			border: 1px solid black;
			margin-left: auto;
			margin-right: auto;
			margin-top: 0.5em;
			margin-bottom: 0.5em;
		}
	</style>
</head>

<body>
<div id="hdr">
<h1>NNJSDA Upload Press Release</h1>
</div>
<?php
	if ($msg != '') echo '<div id="msgdiv">' . $msg . "</div><br>\n";
?>
<form action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="3000000">
<fieldset>
<legend><label for="filespec">File</label></legend><input class="inp" id="filespec" name="filespec" type="file">
</fieldset>
<fieldset>
<legend><label for="description">Description</label></legend><input id="description" name="description" type="text" class="inp">
</fieldset>
<fieldset>
<legend><label for="display_until">Display Until</label></legend><input id="display_until" name="display_until" type="text" class="inp">
</fieldset>
<div id="subm"><input type="submit" name="submit" value="Upload Press Release"></div>
</form>


</body>
</html>
