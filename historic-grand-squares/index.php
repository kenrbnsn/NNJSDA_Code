<?php
$ver = '(v0.1.03)';
$subject = 'Historical Grand Squares';
$pageaddr = 'historical.grand.squares';
include('../emailtracker.inc.php');
include ('../dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname);
if (isset($_POST['search'])) {
	$get_tot = "http://www.google.com/cse?cx=012064095967704141114:kgir0nuj_j8&num=1&start=0&output=xml&q=" . rawurlencode($_POST['search']);
	$get_tot_xml = new SimpleXMLElement(file_get_contents($get_tot));
	$tot = $get_tot_xml->RES->M;
	$url = "http://www.google.com/cse?cx=012064095967704141114:kgir0nuj_j8&num=20&output=xml&q=" . rawurlencode($_POST['search']);
	$res_array = array();
	$deb_array = array();
	for ($i=0;$i<$tot;$i += 20) {
		$x = file_get_contents($url . "&start=$i");
		$search_results = new SimpleXMLElement($x);
//		mail('kenrbnsn@rbnsn.com','$search_results',print_r($search_results,true),'From: new.search(xml) <new.search@nnjsda.org>');
		foreach ($search_results->RES->R as $results) {
			if (!in_array("{$results->T}",$res_array)) {
				$deb_array[] = "{$results->U}";
				if (stristr("{$results->U}",'historic-grand-squares') !== false || stristr("{$results->U}",'old_grand_squares') !== false) {
					$res_array["{$results->T}"] = array('url'=>"{$results->U}",'teaser'=>str_replace(array('<p>','</p>','<br>'),'',"{$results->S}"));
				}
			}
		}
	}
	ksort($res_array);
	$tmp = array();
	$real_tot = 0;
	foreach ($res_array as $k => $u) {
		$tmp[] = "<a href='{$u['url']}' target='_blank'>$k</a><br>{$u['teaser']}";
		$real_tot++;
	}
//	mail('kenrbnsn@rbnsn.com','$deb_array',print_r($deb_array,true),'From: new.search(deb) <new.search@nnjsda.org>');
//	mail('kenrbnsn@rbnsn.com','$res_array',print_r($res_array,true),'From: new.search(res) <new.search@nnjsda.org>');
//	mail('kenrbnsn@rbnsn.com','$tmp',print_r($tmp,true),'From: new.search(tmp) <new.search@nnjsda.org>');
	exit(json_encode(array('ret'=>'Ok','total'=>"Total: $real_tot<hr>\n",'results'=>implode("<hr>\n",$tmp))));
//	echo "Total: $real_tot<hr>\n";
//	echo implode("<hr>\n",$tmp);
}
if (isset($_GET['pdf']) && $IP != 'Bot') {
	if (!file_exists("files/{$_GET['pdf']}.pdf")) {
		exit();
	}
	$q = "select ind from old_gs_stats where issue = '{$_GET['pdf']}' and dl_ip = '$IP'";
	$rs = mysql_query($q);
	if (!$rs) {
		mail('kenrbnsn@rbnsn.com','Problem with query','At line ' . __LINE__ . ", $q\n" . mysql_error(),'From: Old GS db Error <old.gs.dberror@nnjsda.org>');
		exit();
	}
	if ($IP != 'Bot') {
		if (mysql_num_rows($rs) == 0) {
			$q = "insert into old_gs_stats set issue = '{$_GET['pdf']}', cnt=1, dl_date=NOW(), dl_ip = '$IP'";
		} else {
			$rw = mysql_fetch_assoc($rs);
			$q = "update old_gs_stats set cnt=cnt+1, dl_date=NOW() where ind = {$rw['ind']}";
		}
		$rs = mysql_query($q);
		if (!$rs) {
			mail('kenrbnsn@rbnsn.com','Problem with query','At line ' . __LINE__ . ", $q\n" . mysql_error(),'From: Old GS db Error <old.gs.dberror@nnjsda.org>');
			exit();
		}
	}
	$fn = "files/{$_GET['pdf']}.pdf";
	header("Pragma: no-cache");
	header("Expires: 0");
	header("Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Content-type: application/pdf");
//	header("Content-Type: application/force-download");
	header("Content-Disposition: inline; filename={$_GET['pdf']}.pdf");
	header("Content-Length: ".filesize($fn));
	header("Content-Transfer-Encoding: binary");
	readfile($fn);
}

$ary = array();
$gs = glob('files/*.pdf');
foreach ($gs as $pdf) {
	list($gs_yr,$gs_month) = explode('_',basename($pdf));
	$decade = sprintf("%02d",floor(($gs_yr % 100)/10)*10);
	if (!array_key_exists($decade,$ary)) {
		$ary[$decade] = array();
	}
	if (!array_key_exists($gs_yr,$ary[$decade])) {
		$ary[$decade][$gs_yr] = array();
	}
	$ary[$decade][$gs_yr][$gs_month] = $pdf;
}
$tmp = array();
//$tmp[] = '<ul class="sf-menu sf-js-enabled sf-shadow sf-vertical">';
$tmp[] = '<ul class="sf-menu sf-js-enabled sf-shadow">';
foreach ($ary as $decade => $years) {
	$tmp[] = ($decade != '00')?"<li><a href='#'>The 19{$decade}s</a>":"<li><a href='#'>The 2000s</a>";
	$tmp[] = "<ul>";
	foreach ($years as $year => $issues) {
		$tmp[] = "<li><a href='#'>$year</a>";
		$tmp[] = "<ul>";
		foreach ($issues as $month => $file) {
			$tmp[] = "<li><a class='click_pdf' id='" . basename($file) . "' href='?pdf=" . basename($file,'.pdf') . "' target='_BLANK'>$month</a></li>";
		}
		$tmp[] = "</ul>";
		$tmp[] = "</li>";
	}
	$tmp[] = "</ul>";
	$tmp[] = "</li>";
}
$tmp[] = "</ul>";
?>
<!DOCTYPE html>
<html>
	<head>
		<title>NNJSDA Old Grand Squarea</title>
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/base/jquery-ui.css" type="text/css">
		<link rel="stylesheet" href="//www.google.com/cse/style/look/default.css" type="text/css" />
		<link rel="stylesheet" type="text/css" media="screen" href="../superfish/css/superfish.css">
		<link rel="stylesheet" type="text/css" media="screen" href="../superfish/css/superfish-vertical.css">
		<style type="text/css">
			body, html {
				padding: 0;
				margin: 0;
				font-size: 100%;
				font-family: 	'lucida grande',helvetica,verdana,arial,sans-serif;
			}

			#hdr {
				display: block;
				margin-top: 0.5em;
				margin-right: auto;
				margin-left: auto;
				width: 80%;
				border-bottom: 1px solid black;
				margin-bottom: 0.5em;
			}

			.hdr2 {
				display: block;
				margin-right: auto;
				margin-left: auto;
				width: 100%;
				border-bottom: 1px solid black;
			}


			h1,h2, h3 {
				text-align: center;
			}

			h3 {
				font-weight: normal;
				font-size: 100%;
			}

			#rop {
				display: block;
				margin-right: auto;
				margin-left: auto;
				width: 80%;
			}

			#lhc {
				display: block;
				width: 60%;
				margin-left: auto;
				margin-right: auto;
			}

			#rhc {
				display: block;
				width: 100%;
				clear:both;
			}

			.box {
				display: block;
				margin-right: auto;
				margin-left: auto;
				width: 50%;
				border: 1px solid black;
				padding: 0.5em;
				text-align: center;
			}

			.kc {
				font-weight:bold;
			}

			.list-pad {
				padding-bottom: 0.5em;
			}

			form {
				padding-top: 0.5em;
				display: block;
				width: 90%;
				margin-left: auto;
				margin-right: auto;
			}

			label {
				font-weight: bold;
				float: left;
				clear: both;
				width: 20%;
				display: block;
			}

			.inptxt {
				display: block;
				float: left;
				width: 79%;
			}

			#fs {
				display: block;
				width: 100%;
				text-align:center;
				clear: both;
			}

			.clearer {
				clear:both;
				line-height: 0.01em;
				color:white;
			}
			ul.di {
				list-style-type:disc;
			}

			ul.ci {
				list-style-type:circle;
			}

			ul.sq {
				list-style-type:square;
			}
	.hist-gs {
		font-family: "Script MT Bold",Script,  cursive;
		font-style: italic;
	}
  .gsc-control-cse {
    font-family: Arial, sans-serif;
    border-color: #FFFFFF;
    background-color: #FFFFFF;
  }
  input.gsc-input {
    border-color: #BCCDF0;
  }
  input.gsc-search-button {
    border-color: #666666;
    background-color: #CECECE;
  }
  .gsc-tabHeader.gsc-tabhInactive {
    border-color: #E9E9E9;
    background-color: #E9E9E9;
  }
  .gsc-tabHeader.gsc-tabhActive {
    border-top-color: #FF9900;
    border-left-color: #E9E9E9;
    border-right-color: #E9E9E9;
    background-color: #FFFFFF;
  }
  .gsc-tabsArea {
    border-color: #E9E9E9;
  }
  .gsc-webResult.gsc-result,
  .gsc-results .gsc-imageResult {
    border-color: #FFFFFF;
    background-color: #FFFFFF;
  }
  .gsc-webResult.gsc-result:hover,
  .gsc-imageResult:hover {
    border-color: #FFFFFF;
    background-color: #FFFFFF;
  }
  .gs-webResult.gs-result a.gs-title:link,
  .gs-webResult.gs-result a.gs-title:link b,
  .gs-imageResult a.gs-title:link,
  .gs-imageResult a.gs-title:link b {
    color: #0000CC;
  }
  .gs-webResult.gs-result a.gs-title:visited,
  .gs-webResult.gs-result a.gs-title:visited b,
  .gs-imageResult a.gs-title:visited,
  .gs-imageResult a.gs-title:visited b {
    color: #0000CC;
  }
  .gs-webResult.gs-result a.gs-title:hover,
  .gs-webResult.gs-result a.gs-title:hover b,
  .gs-imageResult a.gs-title:hover,
  .gs-imageResult a.gs-title:hover b {
    color: #0000CC;
  }
  .gs-webResult.gs-result a.gs-title:active,
  .gs-webResult.gs-result a.gs-title:active b,
  .gs-imageResult a.gs-title:active,
  .gs-imageResult a.gs-title:active b {
    color: #0000CC;
  }
  .gsc-cursor-page {
    color: #0000CC;
  }
  a.gsc-trailing-more-results:link {
    color: #0000CC;
  }
  .gs-webResult .gs-snippet,
  .gs-imageResult .gs-snippet,
  .gs-fileFormatType {
    color: #000000;
  }
  .gs-webResult div.gs-visibleUrl,
  .gs-imageResult div.gs-visibleUrl {
    color: #008000;
  }
  .gs-webResult div.gs-visibleUrl-short {
    color: #008000;
  }
  .gs-webResult div.gs-visibleUrl-short {
    display: none;
  }
  .gs-webResult div.gs-visibleUrl-long {
    display: block;
  }
  .gsc-cursor-box {
    border-color: #FFFFFF;
  }
  .gsc-results .gsc-cursor-box .gsc-cursor-page {
    border-color: #E9E9E9;
    background-color: #FFFFFF;
    color: #0000CC;
  }
  .gsc-results .gsc-cursor-box .gsc-cursor-current-page {
    border-color: #FF9900;
    background-color: #FFFFFF;
    color: #0000CC;
  }
  .gs-promotion {
    border-color: #336699;
    background-color: #FFFFFF;
  }
  .gs-promotion a.gs-title:link,
  .gs-promotion a.gs-title:link *,
  .gs-promotion .gs-snippet a:link {
    color: #0000CC;
  }
  .gs-promotion a.gs-title:visited,
  .gs-promotion a.gs-title:visited *,
  .gs-promotion .gs-snippet a:visited {
    color: #0000CC;
  }
  .gs-promotion a.gs-title:hover,
  .gs-promotion a.gs-title:hover *,
  .gs-promotion .gs-snippet a:hover {
    color: #0000CC;
  }
  .gs-promotion a.gs-title:active,
  .gs-promotion a.gs-title:active *,
  .gs-promotion .gs-snippet a:active {
    color: #0000CC;
  }
  .gs-promotion .gs-snippet,
  .gs-promotion .gs-title .gs-promotion-title-right,
  .gs-promotion .gs-title .gs-promotion-title-right *  {
    color: #000000;
  }
  .gs-promotion .gs-visibleUrl,
  .gs-promotion .gs-visibleUrl-short {
    color: #008000;
  }
  .gsc-input input.gsc-input {
    background: none repeat scroll 0% 0% white !important;
  }
		</style>
<!--
	<link rel="stylesheet" type="text/css" media="screen" href="../fancybox/fancybox/jquery.fancybox-1.3.1.css">
-->
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript">
    google.load("jquery", "1.7.1", {uncompressed:true});
//    google.load('search', '1', {language : 'en'});
//    google.load("jqueryui", "1.8.11", {uncompressed:true});
  </script>
	<script type="text/javascript" src="jquery.media.js"></script>
	<script type="text/javascript" src="jquery.metadata.2.1/jquery.metadata.js"></script>
  <script type="text/javascript" src="../superfish/js/hoverIntent.js"></script>
	<script type="text/javascript" src="../superfish/js/superfish.js"></script>
<!--
	<script type="text/javascript" src="../fancybox/fancybox/jquery.mousewheel-3.0.2.pack.js"></script>
	<script type="text/javascript" src="../fancybox/fancybox/jquery.fancybox-1.3.1.js"></script>
-->
	<script type="text/javascript">
		$(document).ready(function() {
	    $('#my-search-submit').click(function() {
	    	if ($('#my-search-text').val() != '') {
	    		_gaq.push(['_trackEvent','Search','Search Term',$('#my-search-text').val()]);
	    		$('#mycse').html('<p style="text-align:center">Working...</p>');
	    		$.post("<?php echo $_SERVER['PHP_SELF'] ?>",{search: $('#my-search-text').val()}, function(data) {
	    			if (data.ret == 'Ok') {
	    				$('#mycse').hide()
	    				$('#mycse').html(data.total);
	    				$('#mycse').append(data.results);
	    				$('#mycse').show();
	    			}
	    		},'json');
	    	};
	    	return false;
	    });
/*		    var customSearchControl = new google.search.CustomSearchControl(
		      '012064095967704141114:kgir0nuj_j8');

		    customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
		    customSearchControl.setSearchStartingCallback(null, _trackQuery);
		    var options = new google.search.DrawOptions();
		    options.setSearchFormRoot('cse-search-form');
		    customSearchControl.draw('cse', options);
*/
     $("ul.sf-menu").superfish({
          animation: {height:'show'},   // slide-down effect without fade-in
          delay:     1000               // 1 second delay on mouseout
      });
  		$('.click_pdf').click(function() {
  			_gaq.push(['_trackEvent', 'Download', 'Old Grand Square', $(this).attr('id')]);
  		});
    });

	  var _gaq = _gaq || [];
	  _gaq.push(["_setAccount", "UA-7145849-1"]);
	  function _trackQuery(control, searcher, query) {
	    var loc = document.location;
	    var url = [
	      loc.pathname,
	      loc.search,
	      loc.search ? '&' : '?',
	      encodeURIComponent('q'),
	      '=',
	      encodeURIComponent(query)
	    ];
	    _gaq.push(["_trackPageview", url.join('')]);
	  }
	</script>
	</head>
	<body>
		<div id="hdr">
			<h1>Historical <span class="hist-gs" style="font-size:130%">Grand Square</span> Magazines</h1>
		</div>
		<div id="rop">
			<div style="width:60%;display:block;margin-left:auto;margin-right:auto;height:1.85em">
				<?php echo implode("\n",$tmp) . "\n"; ?>
			</div>

			<div id="my-search-form" style="width: 60%;display:block;margin-left:auto;margin-right:auto;">
				<input type="text" name="my-search-text" id="my-search-text" style="width:80%" />
				<button name="my-search-submit" id="my-search-submit">Search</button><br>
			</div>
			<div id="mycse" style="width:100%;"></div>

		</div>
	</body>
