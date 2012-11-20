<?php
function get_value($fld,$msg,$val=true) {
	if ($msg == '') return('');
	else {
		if ($val) return ('value="' . htmlentities(stripslashes($_POST[$fld])) . '"');
		else return(nl2br(htmlentities(stripslashes($_POST[$fld]))));
	}
}
$subject = 'Upload NNJSDA Presidents Letter';
$ver = '(v1.0.0)';
$pageaddr = 'upload.nnjsda.pres.ltr';
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
		if (strlen(trim(stripslashes($_POST['doc_title']))) == 0)
			$errors[] = 'No Document Title entered';
	if (!empty($errors))
		$msg = 'The following errors were found:<br><span style="color:red">' . implode("<br>\n",$errors) . '</span><br>';
	else {
		if (is_uploaded_file($_FILES['filespec']['tmp_name'])) {
			include('dbconfig.php');
			if (!isset($connect)) {
				$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
				$db = mysql_select_db($dbname);
			}
			$uploaddir = '../presidents_letter/';
			$uploadfile = $uploaddir . basename($_FILES['filespec']['name']);
			$q = "select * from presidents_letter where doc_filename like '%" . mysql_real_escape_string(basename($_FILES['filespec']['name'])) . "%'";
			$rs = mysql_query($q) or die("Problem with the query: <span style='color:red'>$q</span> on line " . __LINE__ . '<br>' . mysql_error());
			if (mysql_num_rows($rs) > 0) {
				$rw = mysql_fetch_assoc($rs);
				$msg = '<span style="color:red">This file was uploaded before on <span style="font-weight:bold">' . date('l, F jS, Y \a\t g:i:s A',strtotime($rw['date_uploaded'])) . '</span> with a title of <span style="font-weight:bold">' . htmlentities($rw['doc_title'],ENT_QUOTES) . '</span> by <span style="font-weight:bold">' . htmlentities($rw['upload_who'],ENT_QUOTES) . '</span></span>' ;
			} else {
				$qtmp = array();
				foreach ($_POST as $fld => $val) {
					$val = stripslashes($val);
					switch ($fld) {
						case 'doc_title':
						case 'doc_long_desc':
						case 'upload_who':
						case 'upload_email':
							$qtmp[] = $fld . " = '" . mysql_real_escape_string(trim($val)) . "'";
							break;
					}
				}
				$qtmp[] = "doc_filename = '" . mysql_real_escape_string(basename($_FILES['filespec']['name'])) . "'";
				$qtmp[] = "date_uploaded = '" . date('Y-m-d H:i:s') . "'";
				$q = "insert into presidents_letter set " . implode(', ',$qtmp);
				$rs = mysql_query($q) or die("Problem with the query: <span style='color:red'>$q</span> on line " . __LINE__ . '<br>' . mysql_error());
				move_uploaded_file($_FILES['filespec']['tmp_name'], $uploadfile);
				$msg = 'The file <span style="font-weight:bold;color:blue">' . $_FILES['filespec']['name'] . '</span> was uploaded successfully<br>';
				mail('kenrbnsn@nnjsda.org','NNSJDA Presidents Letter Uploaded',strip_tags($msg) . "\nby: " . $_POST['upload_who'] . ' (' . $_POST['upload_email'] . ')','From: NNJSDA Presidents Letter Uploaded <' . $pageaddr . '@nnjsda.org>','-f ' . $pageaddr . '@nnjsda.org');
			}
		}
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>Upload NNJSDA Documents</title>
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
<h1>NNJSDA Upload Preidents Letter</h1>
</div>
<?php
	if ($msg != '') echo '<div id="msgdiv">' . $msg . "</div><br>\n";
?>
<form action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="30000000">
<fieldset>
<legend><label for="filespec">File</label></legend><input class="inp" id="filespec" name="filespec" type="file">
</fieldset>
<fieldset>
<legend><label for="doc_title">Document Title</label></legend><input id="doc_title" name="doc_title" type="text" class="inp" <?php echo get_value('doc_title',$msg)?>>
</fieldset>
<fieldset>
<legend><label for="doc_long_desc">Long Description</label></legend><textarea id="doc_long_desc" name="doc_long_desc" type="text" class="inp" rows="10"><?php echo get_value('doc_long_desc',$msg,false)?></textarea>
</fieldset>
<fieldset>
<legend><label for="upload_who">Your Name</label></legend><input id="upload_who" name="upload_who" type="text" class="inp" <?php echo get_value('upload_who',$msg)?>>
</fieldset>
<fieldset>
<legend><label for="upload_email">Your Email</label></legend><input id="upload_email" name="upload_email" type="text" class="inp" <?php echo get_value('upload_email',$msg)?>>
</fieldset>
<div id="subm"><input type="submit" name="submit" value="Upload Document"></div>
</form>


</body>
</html>
