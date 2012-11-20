<?
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
 $body .= "Email: $email\n";
 mail("tracker@kis-hosting.com","NNJSDA new_addme.php Visited",$body,"From: Visit Tracker <tracker@nnjsda.org>");
}

if ($email == "")
{
	header("Location: addme.php?err=1&member_name=$member_name&username=$username");
	exit();
}
if ($member_name == "")
{
	header("Location: addme.php?err=2&username=$username&email=$email");
	exit();
}
if (check_username($username))
{
	header(
	"Location: addme.php?err=4&member_name=$member_name&username=$username&email=$email");
	exit();
}
$password = random_password(12);
$status = authenticate($username, $password, $member_name);
$body = "Your password for the NNJSDA Admin Page is < $password >";
if ($status) {
?>
<head>
	<title>NNJSDA  -- New User Accepted</title>
	<LINK rel="STYLESHEET" type="text/css" href="../nnjsda.css">
</head>

<body>
	<div class=newcenterarea>
	<h1>New Username Accepted</h1>
	<hr>
	<p class=left>A password has been generated for you and sent to the email address you
	entered. Please login with that password. 
	<? if ($_SERVER['SERVER_NAME'] == "localhost") {?>Your password for the NNJSDA Administration Page is [<span class=bold><? echo $password; ?></span>]<? } ?></p>
	<hr>
	<p class=left>Go to the <a href=user_login.php>Login</a> page</p>
	</div>
	</body>
<?
	$to = $email;
	if ($_SERVER['SERVER_NAME'] != "localhost")
	{
	mail($to,"Validation for the NNJSDA Admin Web Page",$body,"NNJSDA Validation <validation@nnjsda.org>");
	mail("tracker@kis-hosting.com","Validation Sent to $email ($username)",$body,"NNJSDA Validation <validation@nnjsda.org>");
	}
	$_SESSION["waiting_username"] = $username;
	$_SESSION["uservalue"] = $username;
	$_SESSION['waiting_for_validation'] = true;
	$_SESSION['member_name'] = $member_name;
}
else
{
	header
	("Location: addme.php?err=5&member_name=$member_name&username=$username&email=$email");
	exit();
}

function authenticate($u, $p, $mn)
{
include('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass);
$cp = crypt($p,'$1$somesalt');
$query = "Select * from validusers WHERE username = '$u'";
mysql_select_db($dbname);
$result = mysql_query($query, $connect) or die(mysql_error());
if (mysql_num_rows($result) == 0)
{
	$query = "Insert into validusers (membername, username, pwd, pwdsent, userconfirmed) VALUES ('$mn', '$u', '$cp', '1','0')";
	$result = mysql_query($query, $connect) or die(mysql_error());
	return $result;
}
else
{
	return false;
}
}

function random_password($pass_len=12) 
{ 
        $allchar = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789,.?;:{}-_"; 
        $str = ""; 


        mt_srand ((double) microtime() * 1000000); 
        for ($i = 0; $i < $pass_len; $i++) { 
            $str .= substr($allchar, mt_rand (0,strlen($allchar)-1), 1);
        } 
        return $str;                  

} 

function check_username($u)
{
	include('dbconfig.php');
	$connect = mysql_connect($dbhost, $dbuser, $dbpass);
	$query = "Select ind from validusers WHERE username = '$u'";
	mysql_select_db($dbname);
	$result = mysql_query($query, $connect);
	if (mysql_num_rows($result)>0) return true;
	else return false;
}

?>
