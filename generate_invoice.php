<?php 
require_once('includes/tcpdf/tcpdf.php'); // Include TCPDF library
require_once('config/database_connection.php'); // Database connection

// Fetch Order ID from the request
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id === 0) {
    die('Invalid Order ID');
}

try {
    // Fetch Order Details
    $stmt = $pdo->prepare("SELECT o.*, u.email, u.loyalty_points FROM orders o 
                           INNER JOIN users u ON o.user_id = u.id 
                           WHERE o.order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Order not found!");
    }

    // Fetch Ordered Products
    $stmt = $pdo->prepare("SELECT p.name, p.price, oi.quantity, (p.price * oi.quantity) AS total
                           FROM order_items oi 
                           JOIN products p ON oi.product_id = p.id
                           WHERE oi.order_id = ?");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize PDF
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Nutrizone');
    $pdf->SetTitle('Invoice - Order #' . $order_id);
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Build Invoice Header
    $html = '
    <h1 style="text-align: center;">INVOICE</h1>
    <h4>Company Name: Nutrizone</h4>
    <p><strong>Address:</strong> Rajouri Garden</p>
    <p><strong>Email:</strong> support@nutrizone.com</p>
    <p><strong>Phone:</strong> (+91)9999999999</p>
    <hr>
    <h4>Invoice Details</h4>
    <p><strong>Invoice No:</strong> INV-' . $order_id . '</p>
    <p><strong>Invoice Date:</strong> ' . date('d M, Y', strtotime($order['created_at'])) . '</p>
    <p><strong>Order No:</strong> #' . $order_id . '</p>
    <hr>
    <h4>Customer Details</h4>
    <p><strong>Email:</strong> ' . htmlspecialchars($order['email']) . '</p>
    <p><strong>Shipping Address:</strong> ' . htmlspecialchars($order['shipping_address']) . '</p>
    <hr>
    <h4>Order Summary</h4>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>No.</th>
                <th>Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>';

    // Initialize variables
    $counter = 1;
    $subtotal = 0;
    
    foreach ($items as $item) {
        $subtotal += $item['total'];
        $html .= '<tr>
                    <td>' . $counter++ . '</td>
                    <td>' . htmlspecialchars($item['name']) . '</td>
                    <td>₹' . number_format($item['price'], 2) . '</td>
                    <td>' . $item['quantity'] . '</td>
                    <td>₹' . number_format($item['total'], 2) . '</td>
                </tr>';
    }

    // Retrieve Promo Discount & Loyalty Discount
    $promo_discount = $order['promo_discount'] ?? 0;
    $loyalty_discount = ($order['loyalty_points'] / 10) ?? 0; // Assuming 10 points = ₹1 discount
    $final_amount = $order['final_amount'];

    // Dynamically Set Tax & Shipping Charges
    $shipping = 20.00; // Update this if stored in the database
    $tax = round(($subtotal - $promo_discount - $loyalty_discount) * 0.05, 2); // Example: 5% Tax

    // Generate Final Summary
    $html .= '
        <tr>
            <th colspan="4" style="text-align:right;">Subtotal</th>
            <td>₹' . number_format($subtotal, 2) . '</td>
        </tr>';

    if ($promo_discount > 0) {
        $html .= '
        <tr>
            <th colspan="4" style="text-align:right;">Promo Discount (' . htmlspecialchars($order['promo_code']) . ')</th>
            <td class="text-success">-₹' . number_format($promo_discount, 2) . '</td>
        </tr>';
    }

    if ($loyalty_discount > 0) {
        $html .= '
        <tr>
            <th colspan="4" style="text-align:right;">Loyalty Points Discount</th>
            <td class="text-success">-₹' . number_format($loyalty_discount, 2) . '</td>
        </tr>';
    }

    $html .= '
        <tr>
            <th colspan="4" style="text-align:right;">Shipping</th>
            <td>₹' . number_format($shipping, 2) . '</td>
        </tr>
        <tr>
            <th colspan="4" style="text-align:right;">Tax (5%)</th>
            <td>₹' . number_format($tax, 2) . '</td>
        </tr>
        <tr>
            <th colspan="4" style="text-align:right;">Final Amount Paid</th>
            <td><strong>₹' . number_format($final_amount, 2) . '</strong></td>
        </tr>
    </tbody>
    </table>
    <br>
    <p style="text-align:center;">Thank you for your business!</p>
    ';

    // Write HTML content to PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Output the PDF
    $pdf->Output('Invoice-' . $order_id . '.pdf', 'D'); // 'D' forces download

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
