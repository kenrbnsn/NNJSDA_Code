<?php
 ini_set("session.cookie_domain", ".nnjsda.org");
session_start();
echo '<pre>' . print_r($_SESSION,true) . '</pre>';
?>
