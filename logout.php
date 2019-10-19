<?php
require_once('config.php');
session_unset();
session_destroy();
setcookie(session_name(), '', time()-42000, '/');
header("location: ".PLATFORM_PATH);
?>
