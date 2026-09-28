<?php
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT e.*, c.name AS category_name FROM ebooks e JOIN categories c ON e.category_id = c.id WHERE e.id = ? AND e.is_active = 1");
$stmt->execute([$id]);
$ebook = $stmt->fetch();

if (!$ebook) {
    header('Location: index.php');
    exit;
}
?>

<div class="max-w-4xl mx-auto px-4 py-12 space-y-8">
    <div class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-10 shadow-sm space-y-8">
        
        <div class="space-y-3">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-800 bg-amber-100 px-3 py-1 rounded-full"><?= htmlspecialchars($ebook['category_name']) ?></span>
            <h1 class="font-serif-custom text-3xl sm:text-4xl font-bold text-stone-900"><?= htmlspecialchars($ebook['title']) ?></h1>
            <p class="text-sm text-stone-400 italic">Judul Asli: <?= htmlspecialchars($ebook['original_title']) ?></p>
        </div>

        <div class="space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-stone-400">Sinopsis Ebook</h3>
            <p class="text-sm text-stone-700 leading-relaxed whitespace-pre-line"><?= htmlspecialchars($ebook['synopsis']) ?></p>
        </div>

        <?php if (!empty($ebook['sample_text'])): ?>
            <div class="bg-stone-50 p-6 rounded-2xl border border-stone-200 space-y-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800">Cuplikan Sampel Gratis</h3>
                <p class="text-xs text-stone-600 italic leading-relaxed">"<?= htmlspecialchars($ebook['sample_text']) ?>"</p>
            </div>
        <?php endif; ?>

        <div class="pt-6 border-t border-stone-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs text-stone-400 font-semibold block">Harga Lisensi Digital</span>
                <span class="text-2xl font-bold text-stone-900"><?= format_rupiah($ebook['price']) ?></span>
            </div>
            
            <form action="cart.php" method="POST">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="ebook_id" value="<?= $ebook['id'] ?>">
                <button type="submit" class="w-full sm:w-auto bg-stone-900 text-white font-bold text-xs px-6 py-3.5 rounded-xl hover:bg-amber-600 transition flex items-center justify-center gap-2">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                    <span>Tambah Ke Keranjang</span>
                </button>
            </form>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>