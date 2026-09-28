<?php
require_once 'layout_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM ebooks WHERE id = ?");
$stmt->execute([$id]);
$ebook = $stmt->fetch();

if (!$ebook) {
    header('Location: ebooks.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_ebook'])) {
    $title       = sanitize($_POST['title']);
    $orig_title  = sanitize($_POST['original_title']);
    $cat_id      = (int)$_POST['category_id'];
    $price       = (float)$_POST['price'];
    $synopsis    = sanitize($_POST['synopsis']);
    $sample_text = sanitize($_POST['sample_text']);
    $slug        = strtolower(str_replace(' ', '-', $title));

    $cover_path    = $ebook['cover_image'];
    $file_epub_path = $ebook['file_epub_path'];
    $file_pdf_path  = $ebook['file_pdf_path'];

    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $cover_name = 'cover_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['cover']['tmp_name'], __DIR__ . '/../uploads/covers/' . $cover_name);
        $cover_path = 'covers/' . $cover_name;
    }

    if (isset($_FILES['file_epub']) && $_FILES['file_epub']['error'] === UPLOAD_ERR_OK) {
        $epub_name = 'ebook_' . time() . '.epub';
        move_uploaded_file($_FILES['file_epub']['tmp_name'], __DIR__ . '/../storage/epub/' . $epub_name);
        $file_epub_path = 'storage/epub/' . $epub_name;
    }

    if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === UPLOAD_ERR_OK) {
        $pdf_name = 'ebook_' . time() . '.pdf';
        move_uploaded_file($_FILES['file_pdf']['tmp_name'], __DIR__ . '/../storage/pdf/' . $pdf_name);
        $file_pdf_path = 'storage/pdf/' . $pdf_name;
    }

    $stmtUpdate = $pdo->prepare("UPDATE ebooks SET category_id = ?, title = ?, original_title = ?, slug = ?, price = ?, synopsis = ?, sample_text = ?, cover_image = ?, file_epub_path = ?, file_pdf_path = ? WHERE id = ?");
    $stmtUpdate->execute([$cat_id, $title, $orig_title, $slug, $price, $synopsis, $sample_text, $cover_path, $file_epub_path, $file_pdf_path, $id]);

    header('Location: ebooks.php?status=updated');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-stone-900">Ubah Data Ebook</h1>
            <p class="text-xs text-stone-500 mt-1">Memperbarui informasi karya: <?= htmlspecialchars($ebook['title']) ?></p>
        </div>
        <a href="ebooks.php" class="text-xs font-semibold text-stone-600 hover:underline">&larr; Kembali ke Katalog</a>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm">
        <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Judul Terjemahan (Indonesia)</label>
                <input type="text" name="title" value="<?= htmlspecialchars($ebook['title']) ?>" class="w-full px-3 py-2 border rounded-xl" required>
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Judul Asli (Jerman)</label>
                <input type="text" name="original_title" value="<?= htmlspecialchars($ebook['original_title']) ?>" class="w-full px-3 py-2 border rounded-xl" required>
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Kategori Genre</label>
                <select name="category_id" class="w-full px-3 py-2 border rounded-xl" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $ebook['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Harga (Rp)</label>
                <input type="number" name="price" value="<?= (int)$ebook['price'] ?>" class="w-full px-3 py-2 border rounded-xl" required>
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold text-stone-700 mb-1">Sinopsis Lengkap</label>
                <textarea name="synopsis" rows="4" class="w-full px-3 py-2 border rounded-xl" required><?= htmlspecialchars($ebook['synopsis']) ?></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold text-stone-700 mb-1">Cuplikan Sampel Gratis</label>
                <textarea name="sample_text" rows="2" class="w-full px-3 py-2 border rounded-xl"><?= htmlspecialchars($ebook['sample_text']) ?></textarea>
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Ganti Cover (.jpg/.png)</label>
                <input type="file" name="cover" accept="image/*" class="w-full border rounded-xl p-1.5">
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Ganti File .EPUB Digital</label>
                <input type="file" name="file_epub" accept=".epub" class="w-full border rounded-xl p-1.5">
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Ganti File .PDF Digital</label>
                <input type="file" name="file_pdf" accept=".pdf" class="w-full border rounded-xl p-1.5">
            </div>
            <div class="flex items-end">
                <button type="submit" name="update_ebook" class="w-full bg-stone-900 text-white font-bold py-2.5 rounded-xl hover:bg-stone-800 transition">Simpan Perubahan Ebook</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>