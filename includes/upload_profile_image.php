<?php
require_once("../config/database_connection.php");

$response = ['success' => false];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["profile_image"])) {
    $user_id = $_POST['user_id'];
    $upload_dir = realpath(path: dirname(__FILE__)) . "/../assets/images/uploads/";;
    $file_name = uniqid() . "_" . basename($_FILES["profile_image"]["name"]);
    $file_path = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $file_path)) {
        $image_url = "assets/images/uploads/" . $file_name;
        $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?")->execute([$image_url, $user_id]);
        $response['success'] = true;
        $response['image_url'] = $image_url;
    } else {
        $response['error'] = "Failed to upload image.";
    }
}

echo json_encode($response);
?>
