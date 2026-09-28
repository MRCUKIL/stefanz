<?php
require_once 'layout_header.php';

if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE ebooks SET is_active = NOT is_active WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ebooks.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM ebooks WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ebooks.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ebook'])) {
    $title       = sanitize($_POST['title']);
    $orig_title  = sanitize($_POST['original_title']);
    $cat_id      = (int)$_POST['category_id'];
    $price       = (float)$_POST['price'];
    $synopsis    = sanitize($_POST['synopsis']);
    $sample_text = sanitize($_POST['sample_text']);
    $slug        = strtolower(str_replace(' ', '-', $title));

    $cover_path    = 'covers/default.jpg';
    $file_epub_path = 'storage/epub/sample.epub';
    $file_pdf_path  = 'storage/pdf/sample.pdf';

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

    $stmtInsert = $pdo->prepare("INSERT INTO ebooks (category_id, title, original_title, slug, price, synopsis, sample_text, cover_image, file_epub_path, file_pdf_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtInsert->execute([$cat_id, $title, $orig_title, $slug, $price, $synopsis, $sample_text, $cover_path, $file_epub_path, $file_pdf_path]);
    
    header('Location: ebooks.php?status=created');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$ebooks = $pdo->query("SELECT e.*, c.name AS category_name FROM ebooks e JOIN categories c ON e.category_id = c.id ORDER BY e.id DESC")->fetchAll();
?>

<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-stone-900">Kelola Katalog Ebook</h1>
            <p class="text-xs text-stone-500 mt-1">Kelola judul, harga, dan file digital EPUB/PDF</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm space-y-4">
        <h3 class="font-bold text-stone-900 text-sm border-b pb-3">Tambah Ebook Baru Ke Katalog</h3>
        
        <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Judul Terjemahan (Indonesia)</label>
                <input type="text" name="title" placeholder="Contoh: Novel Catur" class="w-full px-3 py-2 border rounded-xl" required>
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Judul Asli (Jerman)</label>
                <input type="text" name="original_title" placeholder="Contoh: Schachnovelle (1942)" class="w-full px-3 py-2 border rounded-xl" required>
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Kategori Genre</label>
                <select name="category_id" class="w-full px-3 py-2 border rounded-xl" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Harga (Rp)</label>
                <input type="number" name="price" placeholder="45000" class="w-full px-3 py-2 border rounded-xl" required>
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold text-stone-700 mb-1">Sinopsis Lengkap</label>
                <textarea name="synopsis" rows="3" class="w-full px-3 py-2 border rounded-xl" required></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold text-stone-700 mb-1">Cuplikan Sampel Gratis</label>
                <textarea name="sample_text" rows="2" class="w-full px-3 py-2 border rounded-xl"></textarea>
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Upload Gambar Cover (.jpg/.png)</label>
                <input type="file" name="cover" accept="image/*" class="w-full border rounded-xl p-1.5">
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Upload File .EPUB Digital</label>
                <input type="file" name="file_epub" accept=".epub" class="w-full border rounded-xl p-1.5">
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Upload File .PDF Digital</label>
                <input type="file" name="file_pdf" accept=".pdf" class="w-full border rounded-xl p-1.5">
            </div>
            <div class="flex items-end">
                <button type="submit" name="add_ebook" class="w-full bg-stone-900 text-white font-bold py-2.5 rounded-xl hover:bg-stone-800 transition">Upload & Simpan Ebook</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm space-y-4">
        <h3 class="font-bold text-stone-900 text-sm">Daftar Katalog Ebook Aktif</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-500 border-b">
                    <tr>
                        <th class="p-3">Judul Ebook</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Harga</th>
                        <th class="p-3">Status Katalog</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php foreach ($ebooks as $eb): ?>
                        <tr>
                            <td class="p-3">
                                <p class="font-bold text-stone-900"><?= htmlspecialchars($eb['title']) ?></p>
                                <p class="text-[10px] text-stone-400 italic"><?= htmlspecialchars($eb['original_title']) ?></p>
                            </td>
                            <td class="p-3 text-stone-600 font-semibold"><?= htmlspecialchars($eb['category_name']) ?></td>
                            <td class="p-3 font-bold text-stone-900"><?= format_rupiah($eb['price']) ?></td>
                            <td class="p-3">
                                <a href="ebooks.php?action=toggle&id=<?= $eb['id'] ?>" class="px-2 py-0.5 rounded text-[10px] font-bold <?= $eb['is_active'] ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-200 text-stone-600' ?>">
                                    <?= $eb['is_active'] ? 'AKTIF' : 'NONAKTIF' ?>
                                </a>
                            </td>
                            <td class="p-3 text-right space-x-2">
                                <a href="ebook_edit.php?id=<?= $eb['id'] ?>" class="text-indigo-600 hover:underline font-semibold">Edit</a>
                                <a href="ebooks.php?action=delete&id=<?= $eb['id'] ?>" onclick="return confirm('Hapus ebook ini?')" class="text-red-500 hover:underline font-semibold">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>