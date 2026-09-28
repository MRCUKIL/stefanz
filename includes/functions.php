<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
}

function require_admin() {
    if (!is_admin()) {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

function generate_order_ref() {
    return 'SZ-' . date('YmdHis') . '-' . rand(100, 999);
}

function create_sakurupiah_invoice($order_ref, $amount, $method, $customer_name, $customer_email, $customer_phone, $items) {
    $endpoint = SAKURUPIAH_BASE_URL . '/create.php';
    $signature = hash_hmac('sha256', SAKURUPIAH_API_ID . $method . $order_ref . $amount, SAKURUPIAH_API_KEY);

    $produk_names = [];
    $qtys = [];
    $hargas = [];

    foreach ($items as $item) {
        $produk_names[] = $item['title'];
        $qtys[] = 1;
        $hargas[] = (int)$item['price'];
    }

    $payload = [
        'api_id'       => SAKURUPIAH_API_ID,
        'method'       => $method,
        'name'         => $customer_name,
        'email'        => $customer_email,
        'phone'        => $customer_phone,
        'amount'       => (int)$amount,
        'merchant_fee' => 1,
        'merchant_ref' => $order_ref,
        'expired'      => 24,
        'produk'       => $produk_names,
        'qty'          => $qtys,
        'harga'        => $hargas,
        'callback_url' => SITE_URL . '/webhook.php',
        'return_url'   => SITE_URL . '/payment_detail.php?order_ref=' . $order_ref,
        'signature'    => $signature
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($payload),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . SAKURUPIAH_API_KEY
        ]
    ]);

    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        return ['status' => '400', 'message' => 'cURL Error: ' . $curl_error];
    }

    return json_decode($response, true);
}