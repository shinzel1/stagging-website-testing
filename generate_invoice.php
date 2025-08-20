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
    <div style="width: 100%; font-family: Arial, sans-serif; line-height: 1.5;">

    <!-- Title -->
    <h1 style="text-align: center; margin-bottom: 10px;">INVOICE</h1>
    <hr style="margin: 10px 0;">

    <!-- Invoice Details Table -->
    <table width="100%" cellpadding="4" cellspacing="0" border="0">
        <tr>
            <td align="left">
                <strong>Company:</strong> Nutrizone: A Unit of Bio Active Nutrition<br>
                <strong>Address:</strong> Shop No4, Ground Floor, J13/1, Rajouri Garden,<br>
                New Delhi, Delhi 110027<br>
                <strong>Email:</strong> support@nutrizone.in<br>
                <strong>Phone:</strong> (+91)9891289789
            </td>
            <td align="right">
                <strong>Invoice No:</strong> ' . $order_id . '<br>
                <strong>Invoice Date:</strong> ' . date('d M, Y', strtotime($order['created_at'])) . '<br>
                <strong>Order No:</strong> #' . $order_id . '
            </td>
        </tr>
    </table>
    <hr style="margin: 10px 0;">

    <!-- Customer & Shipping Details -->
    <table width="100%" cellpadding="4" cellspacing="0" border="0">
        <tr>
            <td align="left" width="50%">
                <h4>Bill To:</h4>
                <strong>Email:</strong> ' . htmlspecialchars($order['email']) . '<br>
                <strong>Shipping Address:</strong> ' . htmlspecialchars($order['shipping_address']) . '
            </td>
            <td align="right" width="50%">
                <h4>From:</h4>
                Nutrizone<br>
                support@nutrizone.in<br>
                (+91)9891289789
            </td>
        </tr>
    </table>
    <hr style="margin: 10px 0;">

    <!-- Order Summary -->
    <h4 style="margin-bottom: 5px;">Order Summary</h4>
    <table border="1" cellpadding="8" cellspacing="0" width="100%" style="border-collapse: collapse; text-align: center; font-size: 9px;">
        <thead style="background: #f2f2f2;">
            <tr>
                <th width="5%">No.</th>
                <th width="55%">Item</th>
                <th width="15%">Price</th>
                <th width="10%">Quantity</th>
                <th width="15%">Total</th>
            </tr>
        </thead>
        <tbody>
';

    // Initialize variables
    $counter = 1;
    $subtotal = 0;

    foreach ($items as $item) {
        $subtotal += $item['total'];
        $html .= '<tr>
                    <td width="5%">' . $counter++ . '</td>
                    <td width="55%">' . htmlspecialchars($item['name']) . '</td>
                    <td width="15%">₹' . number_format($item['price'], 2) . '</td>
                    <td width="10%">' . $item['quantity'] . '</td>
                    <td width="15%">₹' . number_format($item['total'], 2) . '</td>
                </tr>';
    }

    // Retrieve Promo Discount & Loyalty Discount
    $promo_discount = $order['promo_discount'] ?? 0;
    $loyalty_discount = ($order['loyalty_points'] / 10) ?? 0; // Assuming 10 points = ₹1 discount
    $final_amount = $order['final_amount'];

    // Dynamically Set Tax & Shipping Charges
    $shipping = 0.01; // Update this if stored in the database
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
            <th colspan="4" style="text-align:right;">Final Amount Paid</th>
            <td><strong>₹' . number_format($final_amount, 2) . '</strong></td>
        </tr>
    </tbody>
    </table>
    <br>
    <p style="text-align:center;">Thank you for your business!</p>
    <p style="text-align:center;"><b>instagram:</b>@nutrizone.delhi<b>&nbsp;&nbsp;&nbsp;&nbsp; facebook:</b>nutrizonecompletenutritionstore</p>
    ';

    // Write HTML content to PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Output the PDF
    $pdf->Output('Invoice-' . $order_id .'_'. rand() . '.pdf', 'D'); // 'D' forces download

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>