<?
require_once('../config/database_connection.php');

$action = isset($_POST['action']) ?? '';
echo $action;
if ($action === 'save') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);

    if ($id) {
        $stmt = $pdo->prepare("UPDATE flavours SET name=?, description=? WHERE id=?");
        $stmt->execute([$name, $desc, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO flavours (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $desc]);
    }
    exit;
}

if ($action === 'get') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM flavours WHERE id=?");
    $stmt->execute([$id]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM flavours WHERE id = ?");
        $stmt->execute([$id]);
    }
    exit;
}


if ($action === 'list') {
    $rows = $pdo->query("SELECT * FROM flavours ORDER BY id DESC")->fetchAll();
    foreach ($rows as $r) {
        echo "<tr>
            <td>{$r['id']}</td>
            <td>{$r['name']}</td>
            <td>{$r['description']}</td>
            <td>
              <button class='btn btn-sm btn-warning edit-btn' data-id='{$r['id']}'>Edit</button>
              <button class='btn btn-sm btn-danger delete-btn' data-id='{$r['id']}'>Delete</button>
            </td>
          </tr>";
    }
    exit;
}

?>