<?php
// Konfigurasi Database MySQL
define('DB_HOST', 'localhost');
define('DB_USER', 'user_api');
define('DB_PASS', 'PasswordRumit123!');
define('DB_NAME', 'stefanzweig_db');

// Konfigurasi Mode Sakurupiah ('sandbox' atau 'production')
define('SAKURUPIAH_MODE', 'sandbox');

if (SAKURUPIAH_MODE === 'production') {
    define('SAKURUPIAH_API_ID', 'PROD-XXXXXXXX');
    define('SAKURUPIAH_API_KEY', 'PROD-XXXXXXXXXXXXXXXXXXXXXXXXXXXX');
    define('SAKURUPIAH_BASE_URL', 'https://sakurupiah.id/api');
} else {
    define('SAKURUPIAH_API_ID', 'SANBOX-05378343');
    define('SAKURUPIAH_API_KEY', 'SANBOX-dUnZigMPk3ZqERN4sv90Z1NRS');
    define('SAKURUPIAH_BASE_URL', 'https://sakurupiah.id/api-sanbox');
}

define('SITE_URL', 'https://stefanzweig.eu');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}