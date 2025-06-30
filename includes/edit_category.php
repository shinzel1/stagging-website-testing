<?php
require_once("../config/database_connection.php");

$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Extract form fields
        $id = $_POST['id'];
        $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');

        // Handle file upload (if a new image is uploaded)
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = __DIR__ . "/../assets/images/uploads/";

            // Check if directory exists, create if not
            if (!is_dir($targetDir)) {
                if (!mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
                    throw new RuntimeException("Failed to create upload directory.");
                }
            }

            // Validate file type and size
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (in_array($_FILES['image']['type'], $allowedTypes) && $_FILES['image']['size'] <= $maxFileSize) {
                $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                $targetFilePath = $targetDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                    $imageUrl = "assets/images/uploads/" . $fileName;
                } else {
                    $response['error'] = "Failed to upload image.";
                    echo json_encode($response);
                    exit;
                }
            } else {
                $response['error'] = "Invalid file type or file is too large.";
                echo json_encode($response);
                exit;
            }
        } else {
            $imageUrl = isset($_POST['editImage']) ? $_POST['editImage'] : '';
        }

        // Update product in the database
        $stmt = $pdo->prepare("
            UPDATE categories 
            SET name = ?, description = ?, title = ?, image = ? 
            WHERE id = ?
        ");
        $stmt->execute([$name, $description, $title, $imageUrl, $id]);

        $response['success'] = true;
    } catch (PDOException $e) {
        $response['error'] = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        $response['error'] = "General error: " . $e->getMessage();
    }
} else {
    $response['error'] = "Invalid request method.";
}

echo json_encode($response);
?>