<?
$subject = 'NNJSDA Poster Contest Page';
$ver = "(2008.1)";
$pageaddr = 'postercontest'; 
include ('../emailtracker.inc.php');

$deadline = date('F j, Y',strtotime('3/2/2008'));
$vpname = 'Lise Greene';
$snail_mail = array('3<sup>rd</sup> Vice President','133 New Jersey Avenue','Lake Hopatcong, NJ 07849');
 ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>NNJSDA Poster Contest</title>
	<LINK rel="STYLESHEET" type="text/css" href="../nnjsda.css">

</head>

<body>
<div id=hdr1>
<h1>NNJSDA<br>Poster Contest Rules</h1>
</div>
<div id=restofpage>
<p class=center><span class=bold>Object of the Contest:<br>
To Stimulate Interest in <span class=italic>SQUARE DANCING</span></span>
</p>
<ol>
<li><span class=bold>Anyone is eligible to enter whether or not they are square dancers.</span>  Any number of entries may be submitted.</li>
<li><span class=bold>All posters must be 8-1/2" x 11" done in black ink on white paper or poster     
board, or as an e-mail attachment.</span> If sending via email, please send your design <a href="mailto:postercontest%40nnjsda.com?subject=Poster Contest Entry">here</a>. The following formats will be acceptable via email attachments:
<ul>
<li>gif</li>
<li>jpeg</li>
<li>pdf</li>
<li>doc (Word)</li>
<li>bmp</li>
<li>tiff</li>
<li>psd (Photoshop)</li>
</ul>
</li>
<li><span class=bold>Subject may be any square dance theme, but must include:</span>
<ul>
<li>National Square Dance Month - September</li>
<li>Sufficient space for each club to fill in its own class or open house information.</li>
</ul>
</li>
<li><span class=bold>The subject matter must not be localized to any one geographical area.</span></li>
<li><span class=bold>Judging will be the responsibility and honor of the NNJSDA  Executive Board at its March 2004 meeting.</span></li>
<li><span class=bold>All entries, including the winning poster, will become the property of the NNJSDA</span>, and at its discretion, may be reprinted to promote square dancing in New Jersey, New York, and Pennsylvania.  Entries may also be displayed at National and State Square Dance Conventions.</li>
<li><span class=bold>The winning poster will be featured on the cover of the Fall Issue of <span class=italic>Grand Square</span>.</span></li>
<li><span class=bold>Deadline for all entries is <?php echo $deadline ?></span>.  Mail or deliver entries to the 3rd VPs,<br>
<pre><?php echo $vpname?> (Emaii: <a href="mailto:postercontest%40nnjsda.org?Subject=Poster Contest Submission">postercontest@nnjsda.org</a>)
<?php 
echo implode("\n",$snail_mail);
?>
</pre>
</li>
</ol>
<hr>
<a href="../index.php">Return</a> to the NNJSDA home page.
</div>


</body>
</html>
