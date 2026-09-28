<?php
require_once 'layout_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$cat = $stmt->fetch();

if (!$cat) {
    header('Location: categories.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $name = sanitize($_POST['name']);
    $slug = strtolower(str_replace(' ', '-', $name));

    if (!empty($name)) {
        $stmtUpdate = $pdo->prepare("UPDATE categories SET name = ?, slug = ? WHERE id = ?");
        $stmtUpdate->execute([$name, $slug, $id]);
        header('Location: categories.php?status=updated');
        exit;
    }
}
?>

<div class="max-w-md space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-stone-900">Ubah Kategori</h1>
        <p class="text-xs text-stone-500 mt-1">Edit nama genre sastra</p>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
        <form method="POST" class="space-y-4 text-xs">
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Nama Kategori</label>
                <input type="text" name="name" value="<?= htmlspecialchars($cat['name']) ?>" class="w-full px-3 py-2 border rounded-xl" required>
            </div>
            <div class="flex gap-2">
                <a href="categories.php" class="w-1/2 text-center bg-stone-100 text-stone-700 font-bold py-2.5 rounded-xl hover:bg-stone-200 transition">Batal</a>
                <button type="submit" name="update_category" class="w-1/2 bg-stone-900 text-white font-bold py-2.5 rounded-xl hover:bg-stone-800 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>