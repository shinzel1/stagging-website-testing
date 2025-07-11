<?php
require_once("../config/database_connection.php");
header('Content-Type: application/json');

// Utility function for JSON response
function respond($data) {
    echo json_encode($data);
    exit;
}

// Upload image helper
function uploadImage($fileField, $uploadDir = "../uploads/banners/") {
    if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $ext = pathinfo($_FILES[$fileField]['name'], PATHINFO_EXTENSION);
    $filename = uniqid('banner_', true) . '.' . strtolower($ext);
    $targetPath = $uploadDir . $filename;

    // Create directory if not exists
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (move_uploaded_file($_FILES[$fileField]['tmp_name'], $targetPath)) {
        // Return relative path
        return substr($targetPath, 3); // remove ../ for correct public path
    }
    return null;
}

// Fetch all banners
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'fetch') {
    $stmt = $pdo->query("SELECT * FROM banners ORDER BY id DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    respond(['data' => $data]);
}

// Fetch single banner
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM banners WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        respond(['success' => true, 'data' => $data]);
    } else {
        respond(['success' => false, 'message' => 'Banner not found']);
    }
}

// Add or Edit banner
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $description = $_POST['description'];
    $link = $_POST['link'];
    $bg_class = $_POST['bg_class'];
    $block_class = $_POST['block_class'];
    $existing_background_image = $_POST['existing_background_image'] ?? '';

    // Handle image upload
    $uploadedImage = uploadImage('background_image_file');
    $background_image = $uploadedImage ? $uploadedImage : $existing_background_image;

    if ($id) {
        // Update
        $stmt = $pdo->prepare("UPDATE banners SET title=?, description=?, background_image=?, link=?, bg_class=?, block_class=? WHERE id=?");
        $stmt->execute([$title, $description, $background_image, $link, $bg_class, $block_class, $id]);
        respond(['success' => true, 'message' => 'Banner updated successfully']);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO banners (title, description, background_image, link, bg_class, block_class) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $background_image, $link, $bg_class, $block_class]);
        respond(['success' => true, 'message' => 'Banner added successfully']);
    }
}

// Delete banner
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare("DELETE FROM banners WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    respond(['success' => true, 'message' => 'Banner deleted']);
}

// Default fallback
respond(['success' => false, 'message' => 'Invalid request']);
?>