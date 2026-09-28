<?php
require_once 'layout_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = sanitize($_POST['name']);
    $slug = strtolower(str_replace(' ', '-', $name));

    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        $stmt->execute([$name, $slug]);
        header('Location: categories.php?status=created');
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $cat_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$cat_id]);
    header('Location: categories.php?status=deleted');
    exit;
}

$categories = $pdo->query("SELECT c.*, COUNT(e.id) AS total_ebooks FROM categories c LEFT JOIN ebooks e ON c.id = e.category_id GROUP BY c.id ORDER BY c.name ASC")->fetchAll();
?>

<div class="max-w-4xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-stone-900">Manajemen Kategori Ebook</h1>
        <p class="text-xs text-stone-500 mt-1">Kelola pengelompokan genre sastra karya Stefan Zweig</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4 h-fit">
            <h3 class="font-bold text-stone-900 text-sm">Tambah Kategori Baru</h3>
            <form method="POST" class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Kategori</label>
                    <input type="text" name="name" placeholder="Misal: Biografi Sejarah" class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs focus:ring-2 focus:ring-red-500 focus:outline-none" required>
                </div>
                <button type="submit" name="add_category" class="w-full bg-stone-900 text-white text-xs font-bold py-2.5 rounded-xl hover:bg-stone-800 transition">Simpan Kategori</button>
            </form>
        </div>

        <div class="md:col-span-2 bg-white rounded-2xl border border-stone-200 p-6 shadow-sm">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-500 border-b border-stone-200">
                    <tr>
                        <th class="p-3">Nama Kategori</th>
                        <th class="p-3">Slug</th>
                        <th class="p-3">Jumlah Ebook</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td class="p-3 font-bold text-stone-900"><?= htmlspecialchars($cat['name']) ?></td>
                            <td class="p-3 text-stone-500 font-mono"><?= htmlspecialchars($cat['slug']) ?></td>
                            <td class="p-3 font-semibold text-stone-700"><?= $cat['total_ebooks'] ?> Ebook</td>
                            <td class="p-3 text-right space-x-2">
                                <a href="category_edit.php?id=<?= $cat['id'] ?>" class="text-indigo-600 hover:underline font-semibold">Edit</a>
                                <a href="categories.php?action=delete&id=<?= $cat['id'] ?>" onclick="return confirm('Yakin ingin menghapus kategori ini?')" class="text-red-500 hover:underline font-semibold">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>