<?php
require_once("../config/database_connection.php");
if(isset( $_POST['length'])){
    
// DataTables request parameters
$limit = $_POST['length']; // Records per page
$offset = $_POST['start']; // Start index
$search = $_POST['search']['value']; // Search value

$query = "SELECT id, name, image_url, description, price, quantity, categories, rating FROM products";

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
$stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
$stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total records count
$totalRecords = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// Prepare response for DataTables
$response = [
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $products
];

echo json_encode($response);
}else{
    $query = "SELECT * FROM products";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(value: ["success" => true, "products" => $products]);
}
?>
