<?php
require_once 'includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$payment_method = sanitize($_POST['payment_method']);
$phone          = sanitize($_POST['phone']);
$user_id        = $_SESSION['user_id'];
$customer_name  = $_SESSION['user_name'];
$customer_email = $_SESSION['user_email'];
$total_amount   = array_sum(array_column($cart, 'price'));
$order_ref      = generate_order_ref();

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO orders (order_ref, user_id, total_amount, payment_status, payment_method) VALUES (?, ?, ?, 'pending', ?)");
    $stmt->execute([$order_ref, $user_id, $total_amount, $payment_method]);
    $order_id = $pdo->lastInsertId();

    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, ebook_id, price) VALUES (?, ?, ?)");
    foreach ($cart as $item) {
        $stmtItem->execute([$order_id, $item['id'], $item['price']]);
    }

    $sakurupiahRes = create_sakurupiah_invoice($order_ref, $total_amount, $payment_method, $customer_name, $customer_email, $phone, $cart);

    if (isset($sakurupiahRes['status']) && $sakurupiahRes['status'] == "200") {
        $dataTrx = $sakurupiahRes['data'][0];
        $trx_id     = $dataTrx['trx_id'] ?? null;
        $payment_no = $dataTrx['payment_no'] ?? null;
        $qr_string  = $dataTrx['qr'] ?? null;
        $expired_at = $dataTrx['expired'] ?? date('Y-m-d H:i:s', strtotime('+24 hours'));
        $pay_url    = $dataTrx['checkout_url'] ?? null;

        $updateStmt = $pdo->prepare("UPDATE orders SET sakurupiah_trx_id = ?, payment_no = ?, qr_string = ?, expired_at = ?, payment_url = ? WHERE id = ?");
        $updateStmt->execute([$trx_id, $payment_no, $qr_string, $expired_at, $pay_url, $order_id]);

        $pdo->commit();
        unset($_SESSION['cart']);

        header('Location: payment_detail.php?order_ref=' . $order_ref);
        exit;
    } else {
        $pdo->rollBack();
        die("Gagal membuat transaksi: " . ($sakurupiahRes['message'] ?? 'Unknown Error'));
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die("Error: " . $e->getMessage());
}