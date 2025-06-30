<?php
require_once("../config/database_connection.php");

$stmt = $pdo->query("SELECT id, name,description FROM flavours ORDER BY id DESC");
$flavours = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["data" => $flavours]);
?>