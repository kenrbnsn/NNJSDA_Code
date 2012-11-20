<?

	$menuar = array('user','contacts','events','club','classes','logout');

	function disp_menu($ar)
	{
	for ($i=0;$i<count($ar);$i++)
		{
		echo "<a class=abox2 href=".$_SERVER['PHP_SELF']."?todo=$ar[$i]>".ucwords($ar[$i])."</a>\n";
		}
	}
?>
<html>
<head><title>Test</title></head>
<body>
<? disp_menu($menuar); ?>
</body>
</html>