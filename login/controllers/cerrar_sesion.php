<?php


error_reporting(E_ALL);
ini_set('display_errors', '1');

include ('../../app/config.php');

session_destroy();
header('Location: '.$URL.'/');

