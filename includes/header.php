<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

session_start();
// Example: Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
require_once('config/config.php');
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <base href="https://staging.crowndevour.com/">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="format-detection" content="telephone=no">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="robots" content="noindex">
  <meta name="googlebot" content="noindex">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <link href="assets/css/poppinscss2.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="assets/css/vendor.css">
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="assets/css/all.min.css">
</head>

<body>
  <?php
  require_once 'includes/common/svg_files.php';
  ?>
  <?php
  $currentPage = basename($_SERVER['PHP_SELF']); // Get current file name
  ?>
  <?php
  require_once 'includes/common/navbar.php';
  ?>