<?php
session_start();
$_SESSION['user_id'] = 16;
$_SESSION['role'] = 'employer';
$_SESSION['lang'] = 'en';
$_GET['application_id'] = 1;

include('candidate_biodata.php');
?>
