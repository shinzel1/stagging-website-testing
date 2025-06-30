<?php
require_once("../config/database_connection.php");

if (isset($_POST["product_Id"])) {
    $stmt = $pdo->prepare("SELECT c.id,c.name
                               FROM categories c
                               JOIN product_categories pc ON c.id = pc.category_id
                               WHERE pc.product_id = :product_id");
    $stmt->execute([':product_id' => $_POST["product_Id"]]);
    $productCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare("SELECT c.id,c.name
    FROM categories c
    LEFT JOIN product_categories pc
    ON c.id = pc.category_id AND pc.product_id = :product_id
    WHERE pc.product_id IS NULL");
    $stmt->execute([':product_id' => $_POST["product_Id"]]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(value: ["success" => true, "productCategories" => $productCategories, "categories" => $categories]);

} elseif (isset($_POST["length"])) {
    // DataTables request parameters
    $limit = $_POST['length']; // Records per page
    $offset = $_POST['start']; // Start index
    $search = $_POST['search']['value']; // Search value

    $query = "SELECT id, name, image, description FROM categories";

    // Apply search filter if needed
    if (!empty($search)) {
        $query .= " WHERE name LIKE :search OR description LIKE :search";
    }

    $query .= " LIMIT :offset, :limit";
    $stmt = $pdo->prepare($query);

    // Bind parameters
    if (!empty($search)) {
        $stmt->bindValue(":search", "%$search%", PDO::PARAM_STR);
    }
    $stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);
    $stmt->bindValue(":limit", (int) $limit, PDO::PARAM_INT);

    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total records count
    $totalRecords = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

    // Prepare response for DataTables
    $response = [
        "draw" => intval($_POST['draw']),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecords,
        "data" => $categories
    ];
    echo json_encode($response);
} else {
    $query = "SELECT * FROM categories";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(value: ["success" => true, "categories" => $categories]);
}

?>