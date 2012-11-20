<?php
if ($_SERVER["HTTP_X_FORWARDED_FOR"] != ""){
	$IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
	$proxy = $_SERVER["REMOTE_ADDR"];
	$host = @gethostbyaddr($_SERVER["HTTP_X_FORWARDED_FOR"]);
}else{
	$IP = $_SERVER["REMOTE_ADDR"];
	$host = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
}
$body = "Remote Address:" . $host . "\n";
$body .= bdy($_SERVER,'$_SERVER');
$body .= bdy($_POST,'$_POST');
$body .= bdy($_GET,'$_GET');
$body .= bdy($_SESSION,'$_SESSION');
//@mail("krobinson",
//	"T4 Charts v1.0 Visited",
//	$body,
//	"From: Visit Tracker <krobinson@espeed.com>");

function bdy($arr,$str)
{
$tmp = array();
if (!empty($arr)) {
	$tmp[] = ' ---- ' . $str . ' ---- ';
	$tmp[] = print_r($arr,true);
	}
return(implode("\n",$tmp));
}

	if (isset($_GET['di'])) {
		header("Content-type: image/png");
		readfile($_GET['di']);
		exit();
	}
	$topdir = openvms_cvt_filename(OPENVMS_CVT_VMS_TO_UNIX,'disk$t4_data:[000000]t4_graphs.dir');
	$tmp = array();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<title>T4 Charts</title>
<link rel="stylesheet" type="text/css" href="../yui/yui_0.11.3/css/reset.css">
<link rel="stylesheet" type="text/css" href="../yui/yui_0.11.3/css/fonts.css">

        <!-- CSS for Menu -->
<link rel="stylesheet" type="text/css" href="../yui/yui_0.11.3/css/menu.css">
 
        <!-- Namespace source file -->
<script type="text/javascript" src="../yui/yui_0.11.3/js/yahoo.js"></script>

        <!-- Dependency source files -->
<script type="text/javascript" src="../yui/yui_0.11.3/js/event.js"></script>
<script type="text/javascript" src="../yui/yui_0.11.3/js/dom.js"></script>

        <!-- Container source file -->
<script type="text/javascript" src="../yui/yui_0.11.3/js/container_core.js"></script>

        <!-- Menu source file -->
<script type="text/javascript" src="../yui/yui_0.11.3/js/menu.js"></script>


<style>
body, html {
	padding: 0;
	margin: 0;
	font-size: 100%;
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
}

#hdr {
	width: 90%;
	margin-left: auto;
	margin-right: auto;
	border-bottom: 1px solid black;
	display: block;
	text-align: center;
}

#rop {
	width: 90%;
	margin-left: auto;
	margin-right: auto;
	padding-top: 0.5em;
	display: block;	
}


#rhc {
	display: block;
	float: left;
	width: 84%;
	border-left: 1px solid black;
}

h1, h2 {
	text-align: center;
	border: 0;
	margin: 0;
}

.img {
	margin: 0 auto;
	display: block;
	width: 691px;
}

.clearer {
	clear: both;
	line-height: 0.01em;
}
</style>
<style media="print">
#lhc {
	width: 0;
	display: none;
}

#rhc {
	display: block;
	float: left;
	width: 100%;
	border: 1px solid red;
}

</style>
</head>
        <script type="text/javascript">
            function onWindowLoad(p_oEvent) {
                var oMenuBar = new YAHOO.widget.MenuBar("T4Graphs");
                oMenuBar.render();
                // "click" event handler for each item in the menubar

                function onMenuBarItemClick(p_sType, p_aArgs) {
                
                    var oEvent = p_aArgs[0];
                    var oTarget = YAHOO.util.Event.getTarget(oEvent);

                    if(oTarget != this.submenuIndicator) {

                        var oActiveItem = this.parent.activeItem;
                    
                    
                        // Hide any other submenus that might be visible
                    
                        if(oActiveItem && oActiveItem != this) {
                    
                            this.parent.clearActiveItem();
                    
                        }
                    
                    
                        // Select and focus the current MenuItem instance
                    
                        this.cfg.setProperty("selected", true);
                        this.focus();
                    
                    
                        // Show the submenu for this instance
                    
                        var oSubmenu = this.cfg.getProperty("submenu");
        
                        if(oSubmenu) {
                    
                            if(oSubmenu.cfg.getProperty("visible")) {
                            
                                oSubmenu.hide();
                            
                            }
                            else {
                            
                                oSubmenu.show();                    
                            
                            }
                    
                        }
                    
                    }
    
                }


                // Add a "click" handler to each item in the menubar

                var i = oMenuBar.getItemGroups()[0].length - 1,
                    oMenuBarItem;

                do {

                    oMenuBarItem = oMenuBar.getItem(i);
                    
                    if(oMenuBarItem) {

                        oMenuBarItem.clickEvent.subscribe(
                                onMenuBarItemClick,
                                oMenuBarItem,
                                true
                            );

                    }
                
                }
                while(i--);


                // "click" event handler for the document
    
                function onDocumentClick(p_oEvent) {
                
                    YAHOO.example.OverlayManager.hideAll();
                    
                    if(oMenuBar.activeItem) {

                        oMenuBar.clearActiveItem();
                        oMenuBar.activeItem.blur();
                    
                    }

                }


                // Add a "click" handler for the document

                YAHOO.util.Event.addListener(
                        document, 
                        "click", 
                        onDocumentClick
                    );


                YAHOO.example.OverlayManager = 
                    new YAHOO.widget.OverlayManager();


                // Register the menus with the Overlay mananger

                var oProduction = oMenuBar.getItem(0).cfg.getProperty("submenu"),
                    oBeta = oMenuBar.getItem(1).cfg.getProperty("submenu"),
                    oQa = oMenuBar.getItem(2).cfg.getProperty("submenu"),
                    oDevelopment = oMenuBar.getItem(3).cfg.getProperty("submenu"),
                    oBackOffice = oMenuBar.getItem(4).cfg.getProperty("submenu"),
                    oColts = oMenuBar.getItem(5).cfg.getProperty("submenu"),
                    oColtsDevelopment = oMenuBar.getItem(6).cfg.getProperty("submenu");


                YAHOO.example.OverlayManager.register([oProduction, oBeta, oQa, oDevelopment, oBackOffice, oColts, oColtsDevelopment]);
                
            }

            YAHOO.util.Event.addListener(window, "load", onWindowLoad);
        </script>
<body>
<?php
	$toplevel = array('production'=>array('cfts1','cfts2','cfts3'),
			  'beta'=>array('njbta1','njbta2','njbta3'),
			  'qa'=>array('njdev7','njdev8','njqa1','qaperf'),
			  'development'=>array('njdev4','njdev5','njdev6'),
			  'back_office'=>array('cfred','cfblue'),
			  'colts'=>array('hawks','cosmos'),
			  'colts_development'=>array('nydev1','nydev2'));
	$tmp[] = '<div id="rop">';
	$tmp[] = '<div id="T4Graphs" class="yuimenubar">';
	$tmp[] = '<div class="bd">';
	$tmp[] = '<ul class="first-of-type">';
	foreach($toplevel as $tl=>$w){
		$tmp[] = '<li class="yuimenubaritem">' . ucwords(str_replace('_',' ',$tl));
		$tmp[] = '<div id="' . $tl . '" class="yuimenu">';
		$tmp[] = '<div class="bd">';
		$tmp[] = '<ul>';
		foreach($w as $nl)
			$tmp[] = '<li id="' . $nl . '" class="yuimenuitem">' . strtoupper(str_replace('_',' ',$nl)) . '</li>';
		$tmp[] = '</ul>';
		$tmp[] = '</div>';
		$tmp[] = '</div>';
		$tmp[] = '</li>';
	}
//	if (is_dir(str_replace('.dir','',$topdir))) {
//	   $tmp[] = expand_dir($topdir);
//	}
	$tmp[] = '</ul>';
	$tmp[] = '</div>';
	$tmp[] = '</div>';
//	$tmp[] = '<div id="rhc">';
//	if (isset($_GET['alt'])) $tmp[] = '<h2>' . $_GET['alt'] . '</h2>';
//	if (isset($_GET['img'])) {
//	   list($width, $height, $type, $attr) = getimagesize($_GET['img']);
//	   $tmp[] = '<div class="img">';
//	   $tmp[] = '<img src="' . $_SERVER['PHP_SELF'] . '?di=' . $_GET['img'] . '" ' . $attr . '>';
//	   $tmp[] = '</div>';
//	}
	$tmp[] = '</div>';
	$tmp[] = '<div class="clearer">&nbsp;</div>';
	echo implode("\n",$tmp);

function is_dir_empty($d) {
	$fp = fopen('trace.txt','a');
	fwrite($fp,'In is_dir_empty, checking:' . $d . "\n");
	fclose($fp);
	$ret = true;
	$d1 = str_replace('.dir','',$d);
	if ($handle = opendir($d))
	    while (false !== ($file = readdir($handle)))
		$ret= (!is_dir($d1.'/'.$file))?false:is_dir_empty($d1 . '/' . $file);
	closedir($handle);
	$fp = fopen('trace.txt','a');
	fwrite($fp,'In is_dir_empty, checked:' . var_export($ret,true) . "\n");
	fclose($fp);
	return($ret);
}

function expand_dir($d) {
	$tmp = array();
	$d1 = str_replace('.dir','',$d);
	if ($handle = opendir($d)) {
	    while (false !== ($file = readdir($handle))) {
		    $fp = fopen('trace.txt','a');
		    fwrite($fp,'$file = ' . $d . '/' . $file . "\n");
		    fclose($fp);
		if (!is_dir($d1.'/'.$file)) {
		    $pi = pathinfo($d1 . '/' . $file);
		    $pi['filename'] = str_replace('.html','',$pi['basename']);
		    if ($pi['extension'] == 'html') {
			$tmp[] = '<li class="yuimenuitem"><a>' . strtoupper($pi['filename']) . '</a>';
			$tmp[] = '<ul class="bd">';
			$tmp[] = parse_html($pi['dirname'],$file);
			$tmp[] = '</ul>';
			$tmp[] = '</li>';
		    }
		}
		else {
			if (!is_dir_empty($d1 . '/' . $file)) {
			    $file1 = ($file == 't4$now')?$file:date('F j, Y',strtotime($file));
			    $tmp[] = '<li class="yuimenuitem"><a>' . $file1 . '</a>';
			    $tmp[] = '<ul class="bd">';
			    $tmp[] = expand_dir($d1 . '/' . $file);
			    $tmp[] = '</ul>';
			    $tmp[] = '</li>';
			}
		}
	   }
	}
	closedir($handle);
	return(implode("\n",$tmp));
}

function parse_html($dir,$hf) {
	$tmp = array();
	$tf = file($dir . '/' . $hf);
	for($i=0;$i<count($tf);$i++) {
	    if (strpos($tf[$i],'<img ') !== false) {
		list ($src_value, $alt_value) = explode ("\t", preg_replace ('/.*src="([^"]*)" alt="([^"]*)".*/', "$1\t$2", $tf[$i]));
		$av = explode(' ',$alt_value,2);
		$tmp[] = '<li class="yuimenuitem"><a href="' . $_SERVER['PHP_SELF'] . '?img=' . $dir . '/' . strtolower($src_value) . '&amp;alt=' . $av[1] . '">' . 
			str_replace(']','] ',$av[1]) . '</a>' . "</li>\n";
	    }
	}
	return (implode("\n",$tmp));
}
?>
</body>
</html>
