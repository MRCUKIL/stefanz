<?php
require_once 'includes/functions.php';
require_login();

header('Content-Type: application/json');

$order_ref = filter_input(INPUT_GET, 'order_ref', FILTER_DEFAULT);

if (!$order_ref) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid order_ref']);
    exit;
}

$stmt = $pdo->prepare("SELECT payment_status FROM orders WHERE order_ref = ? AND user_id = ?");
$stmt->execute([$order_ref, $_SESSION['user_id']]);
$order = $stmt->fetch();

if ($order) {
    echo json_encode(['status' => 'success', 'payment_status' => $order['payment_status']]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Order not found']);
}