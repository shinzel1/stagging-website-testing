<?php
require_once __DIR__ . '/v2/config.php';
require_once __DIR__ . '/v2/package/PhonePeHelper.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $data = $_POST;

        // You can store the order in DB before proceeding to payment
        // Example:
        // $stmt = $pdo->prepare("INSERT INTO orders (order_id, user_id, amount, status) VALUES (?, ?, ?, ?)");
        // $stmt->execute([$data['order_id'], $_SESSION['user_id'], $data['order_amount'], 'initiated']);

        $response_path = RESPONSE_PATH . "?order_id=" . $data['order_id'];
        $data['order_amount'] = $data['order_amount'] * 100;

        $phonePeHelper = new PhonePeHelper(CLIENT_ID, CLIENT_SECRET, CLIENT_VERSION, ENV);
        $response = $phonePeHelper->createPayment($data['order_id'], $data['order_amount'], $response_path);

        // Redirect to PhonePe payment page
        echo "<script>location.href='" . $response['redirectUrl'] . "';</script>";
        exit;
    } catch (Throwable $e) {
        echo "<pre>Error: " . $e->getMessage() . "</pre>";
        exit;
    }
} else {
    echo "<script>location.href='index.php';</script>";
}
