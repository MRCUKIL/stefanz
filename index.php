<?php
require_once 'includes/header.php';

$category_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$query = "SELECT e.*, c.name AS category_name FROM ebooks e JOIN categories c ON e.category_id = c.id WHERE e.is_active = 1";

if ($category_id > 0) {
    $query .= " AND e.category_id = " . $category_id;
}
$query .= " ORDER BY e.id DESC";

$ebooks = $pdo->query($query)->fetchAll();
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<div class="max-w-6xl mx-auto px-4 py-10 space-y-8">
    
    <!-- Hero Banner -->
    <div class="bg-stone-900 text-stone-100 p-8 sm:p-12 rounded-3xl border border-stone-800 shadow-xl flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="space-y-4 max-w-2xl">
            <span class="text-xs font-bold uppercase tracking-widest text-amber-500 bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full">Sastra Klasik Dunia</span>
            <h1 class="font-serif-custom text-3xl sm:text-4xl font-bold tracking-tight text-white leading-tight">Mahakarya Stefan Zweig Dalam Format Digital E-Book</h1>
            <p class="text-stone-300 text-sm leading-relaxed">Nikmati kedalaman psikologis dan keindahan narasi salah satu novelis terhebat abad ke-20. Dapatkan akses langsung dalam format EPUB & PDF.</p>
        </div>
    </div>

    <!-- Filter Kategori -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-stone-200">
        <a href="index.php" class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition <?= $category_id === 0 ? 'bg-stone-900 text-white' : 'bg-white border border-stone-200 text-stone-600 hover:bg-stone-100' ?>">Semua Kategori</a>
        <?php foreach ($categories as $cat): ?>
            <a href="index.php?cat=<?= $cat['id'] ?>" class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition <?= $category_id === $cat['id'] ? 'bg-stone-900 text-white' : 'bg-white border border-stone-200 text-stone-600 hover:bg-stone-100' ?>"><?= htmlspecialchars($cat['name']) ?></a>
        <?php endforeach; ?>
    </div>

    <!-- Grid Ebook -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($ebooks as $eb): ?>
            <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200/60"><?= htmlspecialchars($eb['category_name']) ?></span>
                    <h2 class="font-serif-custom text-xl font-bold text-stone-900 leading-snug"><?= htmlspecialchars($eb['title']) ?></h2>
                    <p class="text-xs text-stone-400 italic"><?= htmlspecialchars($eb['original_title']) ?></p>
                    <p class="text-xs text-stone-600 line-clamp-3 leading-relaxed"><?= htmlspecialchars($eb['synopsis']) ?></p>
                </div>

                <div class="pt-4 border-t border-stone-100 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] text-stone-400 uppercase font-semibold">Harga Ebook</p>
                        <p class="text-base font-bold text-stone-900"><?= format_rupiah($eb['price']) ?></p>
                    </div>
                    <a href="detail.php?id=<?= $eb['id'] ?>" class="bg-stone-900 text-white text-xs font-bold px-4 py-2.5 rounded-xl hover:bg-amber-600 transition flex items-center gap-1.5">
                        <span>Detail Ebook</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>