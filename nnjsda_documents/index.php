<?php
session_start();
$ver = '(v2.0.6)';
$subject = 'NNJSDA Documents';
$pageaddr = 'nnjsda.documents';
if (file_exists('../emailtracker.inc.php'))
	include('../emailtracker.inc.php');
include('dbconfig.php');
if (isset($_GET['dl'])) {
	$mime_types = array('pdf' => 'application/pdf',
								  'doc' => 'application/msword',
								  'txt' => 'text/plain',
								  'rtf' => 'application/msword',
								  'xls' => 'application/vnd.ms-excel',
								  'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
								  'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	$q = "select * from documents where id = '" . mysql_real_escape_string($_GET['dl']) . "'";
	$rs = mysql_query($q) or die ("Problem with the query: <span style='color:red'>$q</span> on line " . __LINE__ . '<br>' . mysql_error());
	$rw = mysql_fetch_assoc($rs);
	if ($IP != 'Bot') {
		$qu = "update documents set download_num = download_num + 1, last_download_date = NOW(), last_download_ip = '" . $IP . "' where id = '" . mysql_real_escape_string($_GET['dl']) . "'";
		$ru = mysql_query($qu) or die ("Problem with the query: <span style='color:red'>$qu</span> on line " . __LINE__ . '<br>' . mysql_error());
	}
	$ext = pathinfo($rw['doc_filename'],PATHINFO_EXTENSION);
	$mt = $mime_types[$ext];
	$fs = filesize($rw['doc_filename']);
	header("Pragma: public"); // required
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false); // required for certain browsers
	header("Content-Transfer-Encoding: binary");
	header("Content-Type: " . $mt);
	header("Content-Length: " . $fs);
	header('Content-Disposition: inline; filename="' . $rw['doc_filename'] . '";' );

	// Send data
	readfile($rw['doc_filename']);
	exit();
}
$q = "select * from documents where show_doc = 'yes' group by order_by, date_uploaded desc";
$rs = mysql_query($q) or die ("Problem with the query: <span style='color:red'>$q</span> on line " . __LINE__ . '<br>' . mysql_error());
$num_docs = mysql_num_rows($rs);
$lead_line = ($num_docs > 0)?'The following NNJSDA Documents are available for downloading/printing:':'Sorry, there is nothing to see here right now, try again another time';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>NNJSDA Documents</title>
	<style>
		body, html {
			font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
			padding:0;
			margin: 0;
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

		#ftr {
			display: block;
			width: 90%;
			margin-left: auto;
			margin-right: auto;
			border-top: 1px solid black;
			padding-top:0.5em;
			padding-bottom: 0.5em;
			margin-top: 0.5em;
			clear: both;
		}

		h1, h2 {
			text-align: center;
			padding: 0;
			margin: 0;
		}

		#rop {
			display: block;
			width:90%;
			margin-left: auto;
			margin-right: auto;
			margin-top:0.25em;
		}

		a,  a:visited,  a:link {
			color: blue;
			background-color: white;
			text-decoration: none;
			font-weight: bold;
		}

		a:hover {
			color: white;
			background-color: blue;
		}

		.title {
			display: block;
			width: 30%;
			float: left;
			text-align: left;
			padding-top: 0.25em;
			padding-bottom: 0.25em;
		}

		.descr {
			display: block;
			width: 68%;
			float:left;
			text-align: left;
			padding-left: 0.25em;
			padding-top: 0.25em;
			padding-bottom: 0.25em;
		}

		.clearer {
			clear:both;
			line-height:0.01em;
		}
	</style>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("jquery", "1.4.2", {uncompressed:true});
    </script>
	<script type="text/javascript" src="../fancybox/fancybox/jquery.mousewheel-3.0.2.pack.js"></script>
	<script type="text/javascript" src="../fancybox/fancybox/jquery.fancybox-1.3.1.js"></script>
	<link rel="stylesheet" type="text/css" href="../fancybox/fancybox/jquery.fancybox-1.3.1.css" media="screen" />
<script type="text/javascript">
	$(document).ready(function() {
				$('.click_link').click(function() {
					_gaq.push(['_trackEvent', 'Click', 'Link', $(this).attr('id')]);
					return (true);
				});
        $(".pdf").click(function() {
          $.fancybox({
            'width': '95%', // or whatever
            'height': '90%',
            'autoDimensions': false,
            'content': '<embed src="'+this.href+'#nameddest=self&page=1&view=FitH,0&zoom=80,0,0" type="application/pdf" height="100%" width="100%" />',
            'scrolling': 'no',
            'onClosed': function() {
  	          $("#fancybox-inner").empty();
            }
            });
          return false;
        }); // pdf
   });
</script>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-7145849-1']);
		  _gaq.push(['_trackPageview']);

		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
</head>

<body>
<div id="hdr">
<h1>NNJSDA Documents</h1>
</div>
<div id="rop">
<p><?php echo $lead_line; ?></p>
<?php
	$tmp = array();
	while ($rw = mysql_fetch_assoc($rs)) {
		$ext = pathinfo($rw['doc_filename'],PATHINFO_EXTENSION);
		$new_span = '';
//		$class = ($ext == 'pdf')?' class="pdf"':'';
		$class = "class='click_link' id='{$rw['doc_filename']}'";
		if (strtotime($rw['date_uploaded']) > strtotime('-5 days')) $new_span = '<span style="color:red;font-weight:bold">** New **</span><br>';
		if (strtotime($rw['date_updated']) > strtotime('-5 days')) $new_span = '<span style="color:red;font-weight:bold">** Updated **</span><br>';
		echo '<div style="clear:both;margin-bottom:0.5em;border-bottom:1px solid grey;margin-top:0.5em"><span class="title">' . $new_span . '<a ' . $class . ' href="?dl=' . $rw['id'] . '" target="_blank">' . $rw['doc_title'] . '</a></span>';
		if ($rw['doc_long_desc'] != '') echo '<span class="descr">' . nl2br(htmlentities($rw['doc_long_desc'],ENT_QUOTES)) . '</span></div>';
		else echo '</div>';
		$tmp[$rw['id']] = array('file'=>$rw['doc_filename'],'name'=>$rw['doc_name'],'title' => $rw['doc_title'],'long_desc' => $rw['doc_long_desc']);
	}
	if (!empty($tmp)) $_SESSION['nnjsda_documents'] = $tmp;
?>
</div>
<?php if (isset($_SESSION['return_to'])) { ?>
<div id="ftr"><a href="<?php echo $_SESSION['return_to'] ?>">Return</a> to the previous page</div>
<?php } ?>
</html>
