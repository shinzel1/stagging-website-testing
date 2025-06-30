<?php
session_start();

if (isset($_POST['discount']) && isset($_POST['points_used'])) {
    $_SESSION['redeemed_discount'] = $_POST['discount'];
    $_SESSION['redeemed_points'] = $_POST['points_used'];
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}
?>
