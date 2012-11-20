<?php
$subject = 'NNJSDA Grand Square';
$ver = "(v1.0.8.11)";
$pageaddr = 'homepage';
include ('../emailtracker.inc.php');
include ('../dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname);
if (isset($_GET['issue'])) {
	if (file_exists('./' . $_GET['issue'] . '.pdf')) {
		$q = "select pdf_name, id from grand_square_issues where filename = '" . mysql_real_escape_string($_GET['issue']) . "'";
		$rs = @mysql_query($q);
		if (!$rs) $pdf_name = 'unknown1';
		else {
			if (mysql_num_rows($rs) == 0) $pdf_name = 'unknown2';
			else {
				$rw = mysql_fetch_assoc($rs);
				$pdf_name = $rw['pdf_name'];
				if ($IP != 'Bot') {
					$uq = "update grand_square_issues set times_dl = times_dl + 1, last_dl = NOW(), last_dl_ip = '" . $IP . "' where id = '" . $rw['id'] . "'";
					$urs = mysql_query($uq) or @mail('kenrbnsn@rbnsn.com','Problem updating gs_count',$uq . "\n" . mysql_error(),'From: gs_count.error@nnjsda.org', '-f gs_count.error@nnjsda.org');
				}
			}
		}
		header("Pragma: public"); // required
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Transfer-Encoding: binary");
		header("Content-type: application/pdf");
		header("Content-Disposition: inline; filename=NNJSDA_GS_{$pdf_name}.pdf");
		header("Content-Length: ".filesize("./{$_GET['issue']}.pdf"));
		readfile("./{$_GET['issue']}.pdf");
/*
		$file = fopen('./' . $_GET['issue'] . '.pdf','r');
		fpassthru($file);
		@fclose($file);
*/
		exit();
	} else exit();
}

	function my_filesize($file) {
   // Setup some common file size measurements.
		$kb = 1024;         // Kilobyte
		$mb = 1024 * $kb;   // Megabyte
		$gb = 1024 * $mb;   // Gigabyte
		$tb = 1024 * $gb;   // Terabyte
		// Get the file size in bytes.
		$size = filesize($file);
		/* If it's less than a kb we just return the size, otherwise we keep going until
		the size is in the appropriate measurement range. */
		if($size < $kb) return $size." B";
		else if($size < $mb) return round($size/$kb,2)." KB";
		else if($size < $gb) return round($size/$mb,2)." MB";
		else if($size < $tb) return round($size/$gb,2)." GB";
		else return round($size/$tb,2)." TB";
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>Grand Square Magazine</title>
	<style type="text/css">
	body, html {
		padding: 0;
		margin: 0;
		font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
		font-size: 100%;
	}

	.md {
		width: 95%;
		margin-left: auto;
		margin-right: auto;
	}

	.hdr {
		width: 95%;
		margin-left: auto;
		margin-right: auto;
		border-bottom: 1px solid black;
		margin-bottom: 0.5em;
	}

	h1 {
		text-align: center;
		font-size: 300%;
	}

	.gs {
		font-family: "Script MT Bold",Script,  cursive;
		font-style: italic;
	}
	.bottomarea {
		clear: both;
		width: 95%;
		font-size: 80%;
		border-top: 1px solid black;
		margin-left: auto;
		margin-right: auto;
		padding-top:0.5em;
		padding-bottom: 0.5em;
	}

	a:link, a:active, a:visited {
		background-color: transparent;
		color: #0000FF;
		font-weight: bold;
		text-decoration: none;
	}

	a:hover {
		background-color: #0000FF;
		color: #FFFFFF;
		text-decoration: none;
	}

	</style>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript">
    google.load("jquery", "1.6.2", {uncompressed:true});
  </script>
  <script type="text/javascript">
  	$(document).ready(function() {
  		$('.click_link').click(function() {
  			_gaq.push(['_trackEvent', 'Click', 'link', $(this).attr('id')]);
  		});
  	});
  </script>
	<?php include('../ga.inc.php') ?>
</head>

<body>
<div class="hdr">
	<h1 class="gs">Grand Square</h1>
</div>
<div class="md">
The following issues are available here:
<ul>
<li><a class="click_link" id="old_grand_squares" href="../historic-grand-squares/">Historical <span class="gs" style="font-size:130%">Grand Square</span> Magazines (1959 - May, 2007)</a></li>
<?php
$q = "select description, filename from grand_square_issues where display_after < NOW() order by order_by";
$rs = mysql_query($q);
while ($rw = mysql_fetch_assoc($rs)) {
	echo "<li><a class='click_link' id='{$rw['filename']}' href='?issue={$rw['filename']}' target='_blank'>{$rw['description']}</a> (" . my_filesize("{$rw['filename']}.pdf") . ")</li>\n";

}
?>
</ul>
</div>
<div class="bottomarea">
<a href="../index.php">Return</a> to the NNJSDA home page.<br>
<?php echo "Last modified: " . date ("F j, Y g:i a.", getlastmod()); ?>
</div>
</body>
</html>
