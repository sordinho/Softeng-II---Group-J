<?php

/*This is a PHP Config file */
define('DBName', 'softeng2');
define('DBUser', 'root');
define('DBPassword', '');
define('DBAddr', 'localhost');
define('TITLE', 'Softeng 2 project 1');
define('PLATFORM_PATH', '');
//define('PLATFORM_PATH', 'http://softeng2.my.to/');
//define('FULL_PATH', $_SERVER['DOCUMENT_ROOT'].'/');\
error_reporting(E_ALL & ~E_NOTICE);


// server should keep session data for 4 hours
ini_set('session.gc_maxlifetime', 3600*4);
// each client should remember their session id for no more than 4 hour
session_set_cookie_params(3600*4);
session_start(); // ready to start session

?>
