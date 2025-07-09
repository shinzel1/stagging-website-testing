<?php
require_once("config/database_connection.php");

// Slugify function
function slugify($string)
{
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9]+/i', '-', $string);
    return trim($string, '-');
}

try {
    $stmt = $pdo->query("SELECT id, name FROM products");

    while ($row = $stmt->fetch()) {
        $id = $row['id'];
        $name = $row['name'];
        $slug = slugify($name);

        $updateStmt = $pdo->prepare("UPDATE products SET slug = ? WHERE id = ?");
        $updateStmt->execute([$slug, $id]);

        echo "Updated slug for ID $id: $slug<br>";
    }

    echo "<strong>✅ All slugs updated successfully.</strong>";

} catch (PDOException $e) {
    echo "<strong>❌ DB Error:</strong> " . $e->getMessage();
}
?>
