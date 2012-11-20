<?
// login.php -- perform validation
GLOBAL $isAdmin, $row;
	session_start();
$_SESSION["SESSION"] = "New";
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
$db = mysql_select_db($dbname); 

$isAdmin = 0;
$status = authenticate($username, $password);
if ($status == 1)
{
	$_SESSION["SESSION_USERNAME"] = $username;
	$_SESSION["SESSION_ADMIN"] = $isAdmin;
	header("Location: newadmin.php");
	exit();
}
else
{
	header("Location: isnotok.php?e=$status");
	exit();
}

function authenticate($u, $p)
{
GLOBAL  $isAdmin, $row, $candoFiles, $db, $connect;
$cp = crypt($p,'$1$somesalt');
$query = "Select ind from validusers WHERE username = '$u' AND pwd = '$cp'";
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
if (mysql_num_rows($result) == 1)
{
	$query = "Select * from validusers WHERE username = '$u' AND pwd = '$cp'";
	$result = mysql_query($query, $connect) or die ("Error in query: $query. " . mysql_error());
	$row = mysql_fetch_array($result) or die ("Error getting row!");
	$isAdmin = $row['adminprivs'];
	return 1;
}
else
{
	return 0;
}
}
?>