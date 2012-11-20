<?php
$ver='(1.0.1)';
$subject="AJAX Test Case";
$pageaddr = 'ajax.test.case';
include('emailtracker.inc.php');
if (isset($_POST['test'])) {
    $a = range(rand(0,100),rand(101,150));
    mail('kenrbnsn@rbnsn.com','In ajax-test-case',"Remote Address:" . $host . "\n" . print_r($a,true),'from: ajax.test.case@nnjsda.org');
    exit(json_encode(array('ret'=>'ok',$a)));
}
?>
<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Test ajax & Firebug</title>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.load("jquery", "1.7.1", {uncompressed:true});
    </script>
    <script type="text/javascript">
        $(function() {
            $('#push_me').click(function() {
                $.post("<?php echo $_SERVER['PHP_SELF'] ?>",{test:1},
                    function(data) {
                        if (data.ret != 'ok') {
                            alert(data.query + '\n' + data.error);
                        } else {
                            alert('got them');
                        }
                    },'json');
              });
        });
    </script>
    <?php include('../ga.inc.php') ?>
    </head>
    <body>
        <div><button id="push_me">Test AJAX</button></div>
    </body>
</html>
