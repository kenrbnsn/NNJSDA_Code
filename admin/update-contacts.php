<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect!");
$db = mysql_select_db($dbname);
$tmp = array();
if (isset($_POST['put'])) {
    list($field,$ind) = explode('-',$_POST['field']);
    $q = "select $field from contacts where ind = $ind";
    $rs = mysql_query($q)  or die(json_encode(array('ret'=>'Mysql Error','query'=>$q,'error'=>mysql_error())));
    $rw = mysql_fetch_assoc($rs);
    $uq = "update contacts set $field = '" . mysql_real_escape_string(trim($_POST['value'])) . "' where ind = $ind";
    $rsu = mysql_query($uq)  or die(json_encode(array('ret'=>'Mysql Error','query'=>$uq,'error'=>mysql_error())));
    exit(json_encode(array('ret'=>'ok','msg'=>"Field $field changed from <{$rw[$field]}> to <{$_POST['value']}>")));
}
if (isset($_POST['get'])) {
	$q = "select * from contacts where display_field = 'on'";
	$rs = mysql_query($q) or die(json_encode(array('ret'=>'Mysql Error','query'=>$q,'error'=>mysql_error())));
	while ($rw = mysql_fetch_assoc($rs)) {
            $tmpx = array();
            $ind = $rw['ind'];
            foreach($rw as $f => $v) {
                if ($f != 'ind') {
                    $tmpx[] = "<div class='row'><label for='{$f}-{$ind}'>" . ucwords(str_replace('_',' ',$f)) . ":</label> <input type='text' name='{$f}[{$ind}]' id='{$f}-{$ind}' value='$v'></div><br>";
                }
            }
            $tmp[] = implode('',$tmpx) . "<br>\n";
        }
	exit(json_encode(array('ret'=>'ok','contacts'=>implode("<br>\n",$tmp))));
}?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style type="text/css">
            .row {
                display: block;
                width: 100%;
                clear: both;
            }
            label {
                font-weight: bold;
                width: 10%;
                display: block;
                float: left;
            }
            
            input {
                display: block;
                width: 65%;
                float: left;
            }
        </style>
    </head>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.load("jquery", "1.7.1", {uncompressed:true});
    </script>
    <script type="text/javascript">
        var self = "<?php echo $_SERVER['PHP_SELF'] ?>";
        $(function() {
            $('#contact-form').change(function(e) {
                var changedFieldset = $(e.target).parents('fieldset');
                $.post(self,{put:1,field:changedFieldset.context.id,value:changedFieldset.context.value},
                   function(data) {
                       if(data.ret != 'ok') {
                           alert(data.query + '\n' + data.error);
                       } else {
                           alert(data.msg);
                       }
                   },'json');
                });
            $('#get_contacts').click(function() {
                $.post(self,{get:1},
                    function(data) {
                        if (data.ret != 'ok') {
                            alert(data.query + '\n' + data.error);
                        } else {
                            $('#contact-form').html(data.contacts);
                        }
                    },'json');
              });
        });
    </script>
    <body>
        <div id="contacts">
            <form id="contact-form"></form>
        </div>
        <div><button id="get_contacts">Get Contacts</button></div>
    </body>
</html>