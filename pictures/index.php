<?php
session_start();
$dbg = false;

if (isset($_GET['dbg'])) {
	$dbg = true;
	if (file_exists('trace/debug_thumb.txt'))
		$fp = fopen('trace/debug_thumb.txt','a');
	else
		$fp = fopen('trace/debug_thumb.txt','w');
	fwrite($fp, ' -------------------------------------------------' . "\r\n");
}
if (isset($_GET['i'])) {
		if ($dbg){
			fwrite($fp,date('m/d/Y G:i a') . ' Getting thumbnail for ' . $_GET['i'] . "\r\n");
//			$exif = exif_read_data($_GET['i']);
//			$exifp = print_r($exif,true);
//			fwrite($fp,date('m/d/Y G:i a') . ' EXIF Meta data: ' . $exifp . "\r\n");
		}
		$image = @exif_thumbnail($_GET['i'], $width, $height, $type);
		if ($image === false || image_type_to_mime_type($type) == 'image/tiff') {
				if ($dbg) fwrite($fp,date('m/d/Y G:i a') . ' exif_thumbnail returned false'."\r\n");
		   	$tmp = makethumb($_GET['i'],false,$dbg);
		} else {
				if ($dbg) fwrite($fp,date('m/d/Y G:i a') . ' exif_thumbnail returned true, width: ' . $width . ' height: ' . $height . ' $type: ' . $type .
							'Mime type: ' . image_type_to_mime_type($type) . "\r\n");
				if (image_type_to_mime_type($type) != 'image/jpeg')
					$tmp = makethumb($_GET['i'],false,$dbg);
				else {
	   			header('Content-type: ' .image_type_to_mime_type($type));
	   			echo $image;
				}
		}
	if ($dbg) fclose($fp);
	exit();
	}

	if (isset($_GET['mt'])) {
		if (file_exists($_GET['mt'])) {
			$movie = new ffmpeg_movie($_GET['mt']);
			$fc = $movie->getFrameCount();
			if ($fc > 99) {
				$ff = $movie->getFrame(100);
			} else {
				$ff = $move->getFrame(int($fc/2));
			}
//    	$ff = ($fc > 99)? $movie->getFrame(100): $movie->getFrame($fc/2);
    	if ($ff !== false) {
    		$pic = $ff->toGDImage();
    		$fh = $ff->getHeight();
    		$fw = $ff->getWidth();
    		if ($fh > $fw) {
    			$th = 160;
    			$tw = ($th / $fh) * $fw;
    		}
    		if ($fw >= $fh) {
    			$tw = 160;
    			$th = ($tw / $fw) * $fh;
    		}
    		$w = round($tw);
    		$h = round($th);
    		$tn = imagecreatetruecolor($w, $h);
    		imagecopyresampled($tn, $pic, 0, 0, 0, 0, $w, $h, $fw, $fh);
    		header('Content-type: image/jpeg');
    		imagejpeg($tn,'',100);
    	}
    }
  	exit();
	}

	$ver = '(V4.0.1)';
	$subject = 'NNJSDA Picture Gallery';
	$pageaddr = 'nnjsda.pictures';
	include('../emailtracker.inc.php');

if (isset($_POST['vph'])) {
	if ($_POST['vph'] > 0) $_SESSION['vph'] = $_POST['vph'];
	if ($_POST['vph'] == 0 && !isset($_SESSION['vph']))$_SESSION['vph'] = 520;
	exit(json_encode(array('ret'=>'ok','vph'=>$_SESSION['vph'])));
}
if (isset($_POST['get_mt'])) {
	$tmp = array();
	$movie = new ffmpeg_movie($_POST['movie']);
	$width = $movie->getFrameWidth();
	$height = $movie->getFrameHeight();
	$tmp['movie'] = $_POST['movie'];
	$tmp['title'] = basename($_POST['movie'],'.flv');
	$tmp['width'] = $width;
	$tmp['height'] = $height;
	exit(json_encode($tmp));
}
if (isset($_GET['s'])) {
        $maxh = $_SESSION['vph'] - 148;
        list($ow, $oh, $type, $attr) = getimagesize($_GET['s']);
        $pct = ($maxh / $oh) * 100;
        $tf = shrinkpic($_GET['s'],$pct,$dbg);
        list($ow, $oh, $type, $attr) = getimagesize($tf);
        header('Content-type: ' .image_type_to_mime_type($type));
        readfile($tf);
        exit();
}
	function write_dbg($fp,$msg)
	{
		fwrite($fp,date('m/d/Y G:i a') . ' --- ' . $msg . "\r\n");
	}

	function debug_it($arr,$str)
	{
		if(!empty($arr)) {
			echo '<pre> ----- ' . $str . ' ----- ' . "\n". print_r($arr,true) . '</pre>';
		}
	}

	function makethumb($fn,$rt=true,$dbg=false)
	{
		if ($dbg) global $fp;
		$ok = false;
		if ($dbg) fwrite($fp,date('m/d/Y G:i a') . ' Getting thumbnail for ' . $fn . ' $rt: ' . var_export($rt,true) . "\r\n");
		list($ow, $oh, $type, $attr) = getimagesize($fn);
		if ($dbg) fwrite($fp,date('m/d/Y G:i a') . ' Original width: ' . $ow . ' original height: ' . $oh . "\r\n");
//		$pct = 75;
//		while (!$ok) {
//			$th = $oh * ($pct/100);
//			$tw = $ow * ($pct/100);
		if ($oh > $ow) {
			$th = 160;
			$tw = ($th / $oh) * $ow;
		}
		if ($ow >= $oh) {
			$tw = 160;
			$th = ($tw / $ow) * $oh;
		}
//			if (($oh > $ow) && ($th <= 160 && $w <= 120)) $ok = true;
//			if (($ow >= $oh) && ($tw <= 160 && $th <= 120)) $ok = true;
		if ($dbg) fwrite($fp,date('m/d/Y G:i a') . ' Percent: ' . $pct . ' Ok: ' . var_export($ok,true) . ' Computed width: ' . $tw . ' computed height: ' . $th . "\r\n");
//			$pct -= 1;
//			if ($pct < 5) {
//				$ok = true;
//				$th = 120;
//				$tw = 160;
//			}
//		}
		$w = round($tw);
		$h = round($th);
		$tn = imagecreatetruecolor($w, $h);
		$img = imagecreatefromjpeg($fn);
		imagecopyresampled($tn, $img, 0, 0, 0, 0, $w, $h, $ow, $oh);
		if ($rt) {
			if ($dbg) fwrite($fp, date('m/d/Y G:i a') . ' Returning type: ' . $type . ' width: ' . $w . ' height: ' . $h . "\r\n");
			return (array($tn, $type, $w, $h));
		} else {
			if ($dbg) fwrite($fp, date('m/d/Y G:i a') . ' [' . $fn . '] Writing mime type: ' . image_type_to_mime_type($type) . "\r\n");
   		header('Content-type: ' .image_type_to_mime_type($type));
			imagejpeg($tn);
//			readfile('tmp.tmp');
		}
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

	function shrinkpic($fn,$pct,$dbg=false)
	{
		if ($dbg) global $fp;
		if ($dbg) fwrite($fp,date('m/d/Y G:i a') . ' In shrinkpics, $fn: ' . $fn . ' $pct: ' . $pct . "\r\n");
		$tf = str_replace(basename($fn),'smaller_'.basename($fn),$fn);
		list($ow, $oh, $type, $attr) = getimagesize($fn);
		if ($dbg) fwrite($fp,date('m/d/Y G:i a') . ' In shrinkpics, $ow: ' . $ow . ' $oh: ' . $oh . "\r\n");
		$w = $ow * ($pct/100);
		$h = $oh * ($pct/100);
		if (file_exists($tf)) {
			list($tw, $th, $type, $attr) = getimagesize($tf);
			if ($h == $th)
				 return($tf);
		}
		if ($dbg) fwrite($fp,date('m/d/Y G:i a') . ' In shrinkpics, $w: ' . $w . ' $h: ' . $h . "\r\n");
		$tn = imagecreatetruecolor($w, $h);
		$img = imagecreatefromjpeg($fn);
		imagecopyresampled($tn, $img, 0, 0, 0, 0, $w, $h, $ow, $oh);
		imagejpeg($tn,$tf);
		$_SESSION['tf'] = $tf;
		return($tf);
	}

function contains_pics($d,$dbg=false) {
	if ($dbg) global $fp;
	$ret = false;
	$x = glob($d . '/*');
//	if ($dbg) fwrite($fp,date('m/d/Y G:i a') . ' In contains_pics, $d: ' . $d . ' glob($d . "/*"): ' . print_r($x,true) . "\r\n");
	if (is_array($x)) foreach ($x as $nxt) {
		if (!is_dir($nxt)){
			$tmp = pathinfo($nxt);
//			if ($dbg) fwrite($fp,date('m/d/Y G:i a') . ' $nxt: ' . $nxt . ' $tmp: ' . print_r($tmp,true) . ' basename($nxt): ' . basename($nxt) . "\r\n");
			if (strtolower($tmp['extension']) == 'jpg'
				&& (substr(basename($nxt),0,8) != 'smaller_')
				&& (substr(basename($nxt),0,6) != 'small_')
				&& (substr(basename($nxt),0,4) != 'rup_'))
					$ret = true;
//			if ($dbg) fwrite($fp,date('m/d/Y G:i a') . ' $ret: ' . var_export($ret,true) . "\r\n");
		}
	}
//	if ($dbg) fwrite($fp,date('m/d/Y G:i a') . ' Returning, $ret: ' . var_export($ret,true) . "\r\n");
	return($ret);
}

function contains_dirs($d,$dbg=false) {
	if ($dbg) global $fp;
	$ret = false;
	$x = glob($d . '/*');
	if (is_array($x)) foreach ($x as $nxt) {
		if (is_dir($nxt) && basename($nxt) != 'thms'){
			$ret = true;
		}
	}
	return($ret);
}

function dir_list($d='.',$dbg=false) {
	if ($dbg) global $fp;
	$tmp = array();
	$x = glob($d . '/*');
	if ($dbg) write_dbg($fp,"Will be searching $d/*");
	if (is_array($x)) foreach ($x as $nxt) {
		if (is_dir($nxt) && basename($nxt) != 'processed' && basename($nxt) != 'donotuse' && basename($nxt) != 'thms' && basename($nxt) != 'avis' && basename($nxt) != 'trace') {
			$tmpl = '<li><a href="';
			if (contains_pics($nxt,$dbg)) {
				$tmpl .= $_SERVER['PHP_SELF'] . '?d=' . $nxt . '">';
				$pi = pathinfo($nxt);
				$pi2 = explode('/',$nxt);
//				$pi2 = pathinfo($pi['dirname']);
//				$tmpd = array();
//				$tmpd[] = $pi2['basename'];
//				$tmpd[] = substr($pi['basename'],0,2);
//				$tmpd[] = substr($pi['basename'],2,2);
				$tmpl .= @date('F j, Y',strtotime($pi2[2]));
			}
			else {
				$tmpl .= '#">';
				$tmpl .= basename($nxt);
			}
			$tmpl .= '</a>';
			$tmp[] = $tmpl;
			if (contains_dirs($nxt,$dbg)) {
	         $tmp[] = '<div id="lvl_' . $lvls[$lvl] . '_' . basename($nxt) . '" class="yuimenu">';
				$lvl++;
	         $tmp[] = '<div class="bd">';
				$tmp[] = '<ul>';
				$tmp[] = dir_list($nxt,$dbg);
				$tmp[] = '</ul>';
				$tmp[] = '</div>';
				$tmp[] = '</div>';
			}
			$tmp[] = '</li>'; }
	}
	return(implode("\n",$tmp));
}

function prev_next($img) {
	$imgs = $_SESSION['save_pics'];
	for ($i = 0; $i < count($imgs); $i++)
		if ($imgs[$i] == $img) {
			switch (true) {
				case ($i == 0):
					$prv_nxt = array($imgs[count($imgs) - 1],$imgs[$i + 1]);
					break;
				case ($i == count($imgs) - 1):
					$prv_nxt = array($imgs[$i - 1], $imgs[0]);
					break;
				default:
					$prv_nxt = array($imgs[$i - 1], $imgs[$i + 1]);
			}
		}
	return($prv_nxt);
}

function mmss($secs) {
  $hh = intval($secs / 3600);
  $ss_remaining = ($secs - ($hh * 3600));
  $mm = intval($ss_remaining / 60);
  $ss = sprintf('%02d',intval($ss_remaining - ($mm * 60)));
  $hr = ($hh > 0)?$hh . ':':'';
  return($hr . $mm . ':' . $ss);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>NNJSDA Picture Gallery</title>

	<link rel="stylesheet" type="text/css" href="../jquery-ui-1.8.custom.css">
	<link rel="stylesheet" type="text/css" media="screen" href="../superfish/css/superfish.css">
	<link rel="stylesheet" type="text/css" media="screen" href="../superfish/css/superfish-vertical.css">
	<link rel="stylesheet" type="text/css" href="lightbox.css">
	<link rel="stylesheet" type="text/css" href="yui_pictures.css" media="screen">
	<link rel="stylesheet" type="text/css" href="pictures_print.css" media="print">
	<style type="text/css">
		#player {
			display:block;
			width: 640px;
			height: 480px;
		}
	</style>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript">
    google.load("jquery", "1.4.2", {uncompressed:true});
    google.load("jqueryui", "1.8.0", {uncompressed:true});
    </script>
    <script type="text/javascript" src="lightbox.js"></script>
    <script type="text/javascript" src="../flowplayer/flowplayer-3.2.0.min.js"></script>
	<script type="text/javascript" src="../superfish/js/hoverIntent.js"></script>
	<script type="text/javascript" src="../superfish/js/superfish.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		var vph = $(window).height();
		$.post("<?php echo $_SERVER['PHP_SELF'] ?>", {vph: vph});
    $(".lightbox").lightbox();
    $('#show_movie').dialog({
    	bgiframe: true,
    	autoOpen: false,
    	width: 640,
    	height: 480,
    	modal: true,
    	buttons: {
    		Ok: function() {
    			$(this).dialog('close');
    		}
    	}
    });
    $('.display_movie').click(function() {
    	movie = $(this).attr('id');
//    	$.post("<?php echo $_SERVER['PHP_SELF'] ?>", {trace:1, movie: movie});
			$.post("<?php echo $_SERVER['PHP_SELF'] ?>", {get_mt:1, movie: movie }, function (data) {
				width = data.width + 52;
				height = data.height + 154;
				$("#show_movie").dialog( "option", "height", height );
				$("#show_movie").dialog( "option", "width", width );
				$("#show_movie").dialog("option", "title", data.title);
				$('#player_h').html('<a id="player" href="' + data.movie + '"></a>');
				$('#player').css('height',data.height);
				$('#player').css('width',data.width);
				flowplayer("player", "../flowplayer/flowplayer-3.2.1.swf");
				$('#show_movie').dialog('open');
			},"json");
			return(false);
    });
		$(window).resize(function() {
			$.post("<?php echo $_SERVER['PHP_SELF'] ?>", {vph: vph});
		});
        $("ul.sf-menu").superfish({
            animation: {height:'show'},   // slide-down effect without fade-in
            delay:     1200               // 1.2 second delay on mouseout
        });

	});
</script>
</head>

<body>
<div id="hdr">
<h1>NNJSDA Picture Gallery</h1>
</div>
<div id="rest">
<?php
	$tmp = array();
	$tmp[] = '<ul id="lhc_menu" class="sf-menu sf-vertical">';
	$tmp[] = dir_list('.');
	$tmp[] = '</ul>';
	$tmp[] =  '<div id="rhc">';
	if (isset($_GET) && count($_GET)>0){
	foreach($_GET as $k=>$v)  {
		$thumbs = substr($v,0,(strlen(basename($v)) + 1) * -1);
		switch($k) {
		case 'i':
			break;
		case 's':
			$maxh = $_SESSION['vph'] - 120;
			list($ow, $oh, $type, $attr) = getimagesize($v);
			list($prev_pic, $next_pic) = prev_next($v);
//			$pct = ($ow > 1024 || $oh > 1024)?50:75;
			$pct = ($maxh / $oh) * 100;
			$tf = shrinkpic($v,$pct,$dbg);
			list($ow, $oh, $type, $attr) = getimagesize($tf);
			$tmp[] = '<div id="hdr">';
			$tmpx = explode('/',$v);
			$tmp[] = '<h2>' . date('l, F j, Y', strtotime($tmpx[2])) . '</h2>';
			$tmp[] = '</div>';
			$tmp[] = '<div id="rest">';
			$tmp[] = '<img style="width:' . $ow . 'px" src="' . $tf . '">';
			$tmp[] = '<hr>';
			$tmp[] = '<div class="footer">';
			$tmp[] = '<a href="' . $_SERVER['PHP_SELF'] . '?s=' . $prev_pic . '">Previous picture</a>&nbsp;';
			$tmp[] = '<a href="' . $_SERVER['PHP_SELF'] . '?f=' . $v . '">See fullsize picture</a>&nbsp;';
			$tmp[] = '<a href="' . $_SERVER['PHP_SELF'] . '?s=' . $next_pic . '">Next picture</a><br>';
			$tmp[] = '<a href="' . $_SERVER['PHP_SELF'] . '?d=' . $thumbs . '">Back</a> to the thumbnails</div></div>';
			break;
		case 'f':
			list($ow, $oh, $type, $attr) = getimagesize($v);
			$tmp[] = '<div id="hdr">';
			$tmpx = explode('/',$v);
			$tmp[] = '<h2>' . date('l, F j, Y', strtotime($tmpx[2])) . '</h2>';
			$tmp[] = '</div>';
			$tmp[] = '<div id="rest">';
			$tmp[] = '<img style="width:' . $ow . 'px" src="' . $v . '">';
			$tmp[] = '<hr>';
			$tmp[] = '<div class="footer"><a href="' . $_SERVER['PHP_SELF'] . '?d=' . $thumbs . '">Back</a> to the thumbnails</div></div>';
			break;
		case 'a':
			$exif = exif_read_data($_GET['thm']);
			$avi_wid = $exif['RelatedImageWidth'];
			$avi_hgt = $exif['RelatedImageHeight'] + 45;
			$tmp[] = '<div id="hdr">';
			$tmpx = explode('/',$v);
			$tmp[] = '<h2>' . date('l, F j, Y', strtotime($tmpx[2])) . '</h2>';
			$tmp[] = '</div>';
			$tmp[] = '<div id="rest">';
			$tmp[] = '<a href="' . $_GET['a'] . '" id="player"></a>';
			$tmp[] = '</div>';
			$tmp[] = '<hr>';
			$tmp[] = '<div class="footer"><a href="' . $_SERVER['PHP_SELF'] . '?d=' . $thumbs . '">Back</a> to the thumbnails</div></div>';
			break;

		case 'd':
			$tmpx = explode('/',$_GET[$k]);
			$tmp[] = '<div id="hdr">';
			$tmpx = explode('/',$v);
			$tmp[] = '<h2>' . date('l, F j, Y', strtotime($tmpx[2])) . '</h2>';
			$tmp[] = '</div>';
			$dbgx = ($dbg)?'&dbg=1':'';
			$save_pics = array();
			foreach (glob($_GET['d'] . '/{*.jpg,*.JPG}',GLOB_BRACE) as $filename) {
				$filp = pathinfo($filename);
				if (filesize($filename) > 0
					&& substr(basename($filename),0,8) != 'smaller_'
					&& substr(basename($filename),0,6) != 'small_'
					&& substr(basename($filename),0,4) != 'rup_'
					&& strtolower($filp['extension']) == 'jpg') {
					$image = @exif_thumbnail($filename, $width, $height, $type);
					if ($image === false || image_type_to_mime_type($type) == 'image/tiff')
						list($image, $type, $width, $height) = makethumb($filename,true,$dbg);
					if ($dbg) fwrite($fp, date('m/d/Y G:i a') . ' Returned type: ' . $type . ' width: ' . $width . ' height: ' . $height . "\r\n");
		   		$dwidth = 160;
					$dheight = 160;
		   		$tmp[] = '<a class="pic lightbox" href="?s=' . $filename . '" rel="test_gallery" title="' . basename($filename,'.jpg') . '"><img style="width:' . $width . 'px " src="?i=' . $filename . $dbgx .'" width="' . $width . '" height="' . $height . '"><br>' . basename($filename) . '</a>';
					$save_pics[] = $filename;
				}
			}
			$_SESSION['save_pics'] = $save_pics;
			$avis = glob($_GET['d'] . '/*.{flv,FLV}',GLOB_BRACE);

			if (count($avis) > 0) {
				$tmp[] = '<div class="clearer">&nbsp;</div><hr>';
				$tmp[] = '<p>The following movies are available.</p>';

				foreach ($avis as $filename) {
					$filp = pathinfo($filename);
					if (filesize($filename) > 0)
					{
						list($width, $height, $type, $attr) = getimagesize($filename);
			   		$dwidth = 160;
						$dheight = 120;
    				$movie = new ffmpeg_movie($filename);
    				$dur = mmss($movie->getDuration());
			   		$tmp[] = '<a class="pic display_movie" href="#" id="' . $filename .
									 '"><img style="width:' . $dwidth . 'px " src="?mt=' . $filename . '" width="' . $dwidth . '" height="' . $dheight . '"><br>' . basename($filename) . '<br>Length: ' . $dur . '</a>';
					}
				}
			}
		}
	}
}
	$tmp[] = '</div>';
	echo implode("\n",$tmp)."\n";
	if ($dbg) @	fclose($fp);
?>
</div>
<div id="show_movie"><span id="player_h"></span></div>
<?php include('../ga.inc.php') ?>
</body>
</html>
