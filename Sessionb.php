<?php
session_start();
include './dbb.php';
//  ini_set('display_errors' ,'1');
//  error_reporting(E_ALL & ~E_NOTICE);
if (!isset($_SESSION['username'])) {
     header("Location: login.php");
    exit();
}
?>
