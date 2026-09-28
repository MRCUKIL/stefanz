<?php
require_once 'includes/functions.php';

$json_str = file_get_contents('php://input');
$data = json_decode($json_str, true);

if (!$data) {
    http_response_code(400);
    exit('Invalid JSON Payload');
}

$merchant_ref   = $data['merchant_ref'] ?? null;
$status         = $data['status'] ?? null;
$signature_recv = $data['signature'] ?? null;

$signature_calc = hash_hmac('sha256', SAKURUPIAH_API_ID . $merchant_ref . $status, SAKURUPIAH_API_KEY);

if ($signature_recv !== $signature_calc) {
    http_response_code(403);
    exit('Invalid Signature');
}

if ($status === 'paid') {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid', paid_at = NOW() WHERE order_ref = ?");
        $stmt->execute([$merchant_ref]);

        $stmtOrder = $pdo->prepare("SELECT id, user_id FROM orders WHERE order_ref = ?");
        $stmtOrder->execute([$merchant_ref]);
        $order = $stmtOrder->fetch();

        if ($order) {
            $stmtItems = $pdo->prepare("SELECT ebook_id FROM order_items WHERE order_id = ?");
            $stmtItems->execute([$order['id']]);
            $items = $stmtItems->fetchAll();

            $stmtGrant = $pdo->prepare("INSERT INTO user_downloads (user_id, ebook_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE download_count = download_count");
            foreach ($items as $item) {
                $stmtGrant->execute([$order['user_id'], $item['ebook_id']]);
            }
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Callback Processed Successfully']);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'ignored', 'message' => 'Status is not paid']);
}