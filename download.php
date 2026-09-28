<?php
require_once 'includes/functions.php';
require_login();

$user_id  = $_SESSION['user_id'];
$ebook_id = isset($_GET['ebook_id']) ? (int)$_GET['ebook_id'] : 0;
$format   = isset($_GET['format']) ? strtolower(sanitize($_GET['format'])) : 'pdf';

$stmt = $pdo->prepare("SELECT ebook_id FROM user_downloads WHERE user_id = ? AND ebook_id = ?");
$stmt->execute([$user_id, $ebook_id]);

if (!$stmt->fetch()) {
    die("Akses Ditolak: Anda belum membeli lisensi e-book ini.");
}

$stmtBook = $pdo->prepare("SELECT * FROM ebooks WHERE id = ?");
$stmtBook->execute([$ebook_id]);
$ebook = $stmtBook->fetch();

if (!$ebook) {
    die("File tidak ditemukan.");
}

$file_path = ($format === 'epub') ? $ebook['file_epub_path'] : $ebook['file_pdf_path'];
$full_path = __DIR__ . '/' . $file_path;

if (!file_exists($full_path)) {
    die("File fisik belum diunggah di server.");
}

$pdo->prepare("UPDATE user_downloads SET download_count = download_count + 1 WHERE user_id = ? AND ebook_id = ?")
    ->execute([$user_id, $ebook_id]);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($full_path) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($full_path));
readfile($full_path);
exit;