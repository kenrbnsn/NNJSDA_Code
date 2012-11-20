<?

function get_dirs($d)

{

	if ($dir = @opendir($d)) 

		{

		while (($file = readdir($dir)) !== false) 

			{

			if($file != ".." && $file != ".")

				{

				if (is_dir($file))

					$filelist[] = $file;

				}

			} 

		closedir($dir);

		}

	return($filelist);



}



function get_dir_list($d,$fs)

{

	if ($dir = @opendir($d)) 

		{

		while (($file = readdir($dir)) !== false) 

			{

			if($file != ".." && $file != ".")

				{

				if (!(strpos($file,$fs) === false))

					$filelist[] = $file;

				}

			} 

		closedir($dir);

		}

	return($filelist);

}



function show_sizes($dir, $ar,$dl="",$ind=0)

{

	while (list ($key, $val) = each ($ar)) 

	{

		if ($dl == "")

			echo $val."&nbsp;&nbsp;".my_filesize($dir,$val)."<br>\n";

		else {

			$ex = explode($dl,$val);

			echo $ex[$ind]."&nbsp;&nbsp;".my_filesize($ex[$ind])."<br>\n";

		}

	}

}



function my_filesize($dir=".",$file) {

   // First check if the file exists.

  if(!is_file($dir."/".$file))

     {

	if (is_file($dir."/".strtoupper($file))) $file = strtoupper($file);

	else return("File does not exist!");

     }

   // Setup some common file size measurements.

  $kb = 1024;         // Kilobyte

   $mb = 1024 * $kb;   // Megabyte

  $gb = 1024 * $mb;   // Gigabyte

   $tb = 1024 * $gb;   // Terabyte

   // Get the file size in bytes.

   $size = filesize($dir.'/'.$file);

   /* If it's less than a kb we just return the size, otherwise we keep going until

   the size is in the appropriate measurement range. */

   if($size < $kb) {

       return $size." B";

   }

   else if($size < $mb) {

      return round($size/$kb,2)." KB";

   }

   else if($size < $gb) {

       return round($size/$mb,2)." MB";

  }

   else if($size < $tb) {

       return round($size/$gb,2)." GB";

   }

   else {

      return round($size/$tb,2)." TB";

   }

}



function thumbs($dir='.',$ar,$dl="",$ind=0)

{

	global $club, $dance_type;

	while (list ($key, $val) = each ($ar)) 

	{

		$ex = explode($dl,$val);

		$fn = explode(".",$ex[$ind]);

		$src = ImageCreateFromJPEG($dir."/".$val);

		$width = ImageSx($src);

		$height = ImageSy($src);

		echo "<span class=abox1>";

		echo "<a class=abox1a href=\"display_it.php?filename={$ex[$ind]}&amp;dir=$dir&amp;club=$club&amp;dance_type=".htmlentities(urlencode($dance_type))."\"><img src=$dir/$val border=0 width=$width height=$height></a><span class=smaller>$fn[0]</span><br>";

		echo my_filesize($dir,$ex[$ind])."<br>\n";

		echo "<input type=Checkbox name=\"picturecomments[]\" value='$club,$dir,$fn[0],jpg'> Comments</span>\n";

	}

}



function athumbs($dir='.',$ar,$dl="",$ind=0)

{

	global $club;

	while (list ($key, $val) = each ($ar)) 

	{

		$ex = explode($dl,$val);

		$fn = explode(".",$ex[$ind]);

		$src = ImageCreateFromJPEG($dir."/".$val);

		$width = ImageSx($src);

		$height = ImageSy($src);

		echo "<span class=abox1><a class=abox1a href='downloadfile.php?filename={$fn[0]}&amp;dir=$dir&amp;club=$club'><img src=$dir/sml_{$fn[0]}.jpg border=0 width=$width height=$height></a>".ucwords($fn[0])."<br>";

		echo my_filesize($dir,$fn[0].".avi")."<br>\n";

		echo "<input type=Checkbox name=\"picturecomments[]\" value='$club,$dir,$fn[0],avi'> Comments</span>\n";

	}

}



function dump_it($str,$ar)

{

	echo "$str<br>\n";

	echo "<pre>\n";

	var_dump($ar);

	echo "</pre>\n";

}

 ?>