<?php
$ary = parse_ini_file('event.ini',true);
echo '<pre>';
print_r($ary);
print_r(array_keys($ary));
echo json_encode($ary) . "</pre>\n";;
?>
