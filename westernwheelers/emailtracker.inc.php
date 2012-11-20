<?
if (!isset($ver)) $ver = '';
if (!preg_match('/[Ss]lurp|[Cc]rawler|[Cc]rawl|slurpi|ScoutJet|[Ss]pider|[Gg]rub|[Bb]ot|archiver|NetMonitor|[Ss]qworm|SiteUptime|NG|[Aa]sk|Pingdom|[Cc]rawler|lwp-request/', $_SERVER['HTTP_USER_AGENT'])) {
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
 $body .= bdy($_SESSION,'$_SESSION');
 $body .= bdy($_SERVER,'$_SERVER');
 @mail("kenrbnsn@kis-hosting.com",$subject . ' Page ' . $ver . ' Visited',$body,'From: Visit Tracker <nobody@nnjsda.org>',
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
