<?php
require_once("../config/database_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        $title = preg_replace('/\s+/', '-', $_POST['name']);
        ;
        $title = strtolower($title);
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "../assets/images/uploads/";

            // Check if directory exists, create if not
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0775, true);
            }

            // Validate file type and size
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (in_array($_FILES['image']['type'], $allowedTypes) && $_FILES['image']['size'] <= $maxFileSize) {
                $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                $targetFilePath = $targetDir . $fileName;
                // echo $_FILES['image']['tmp_name'];
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
            $imageUrl = "";
        }


        $stmt = $pdo->prepare("INSERT INTO categories (name,title, image, description)
                               VALUES (?,?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $title,
            $imageUrl,
            $_POST['description']
        ]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
