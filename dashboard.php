<?php
require_once 'includes/header.php';
require_login();

$user_id = $_SESSION['user_id'];
$tab = filter_input(INPUT_GET, 'tab', FILTER_DEFAULT) ?? 'library';

$stmtEbooks = $pdo->prepare("SELECT e.*, ud.created_at AS access_granted_at 
                            FROM user_downloads ud 
                            JOIN ebooks e ON ud.ebook_id = e.id 
                            WHERE ud.user_id = ? 
                            ORDER BY ud.created_at DESC");
$stmtEbooks->execute([$user_id]);
$purchased_ebooks = $stmtEbooks->fetchAll();

$stmtOrders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmtOrders->execute([$user_id]);
$orders_history = $stmtOrders->fetchAll();
?>

<div class="max-w-5xl mx-auto px-4 py-12">
    <div class="flex justify-between items-center mb-6 border-b border-stone-200 pb-4">
        <div>
            <h1 class="font-serif-custom text-3xl font-bold text-stone-900">Dashboard Pembaca</h1>
            <p class="text-xs text-stone-500 mt-1">Selamat datang kembali, <?= htmlspecialchars($_SESSION['user_name']) ?></p>
        </div>
    </div>

    <div class="flex border-b border-stone-200 mb-8 space-x-8 text-sm font-semibold">
        <a href="dashboard.php?tab=library" class="pb-3 border-b-2 <?= $tab === 'library' ? 'border-amber-500 text-amber-800 font-bold' : 'border-transparent text-stone-500 hover:text-stone-900' ?>">Perpustakaan Ebook (<?= count($purchased_ebooks) ?>)</a>
        <a href="dashboard.php?tab=history" class="pb-3 border-b-2 <?= $tab === 'history' ? 'border-amber-500 text-amber-800 font-bold' : 'border-transparent text-stone-500 hover:text-stone-900' ?>">Riwayat Transaksi (<?= count($orders_history) ?>)</a>
    </div>

    <?php if ($tab === 'library'): ?>
        <?php if (empty($purchased_ebooks)): ?>
            <div class="bg-white p-8 rounded-2xl border border-stone-200 text-center">
                <p class="text-stone-500 text-sm mb-4">Anda belum memiliki koleksi Ebook.</p>
                <a href="index.php" class="bg-stone-900 text-white px-4 py-2.5 rounded-xl text-xs font-bold uppercase">Beli Ebook Pertama</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($purchased_ebooks as $ebook): ?>
                    <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm flex justify-between items-center">
                        <div>
                            <h3 class="font-serif-custom font-bold text-stone-900 text-base"><?= htmlspecialchars($ebook['title']) ?></h3>
                            <p class="text-xs text-stone-400 italic mb-2"><?= htmlspecialchars($ebook['original_title']) ?></p>
                            <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded">Tersedia</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <a href="download.php?ebook_id=<?= $ebook['id'] ?>&format=epub" class="bg-stone-900 text-white text-[10px] font-bold uppercase px-3 py-2 rounded-lg hover:bg-amber-600 transition text-center">Unduh EPUB</a>
                            <a href="download.php?ebook_id=<?= $ebook['id'] ?>&format=pdf" class="border border-stone-300 text-stone-800 text-[10px] font-bold uppercase px-3 py-2 rounded-lg hover:bg-stone-100 transition text-center">Unduh PDF</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 border-b border-stone-200 font-semibold text-stone-700">
                    <tr>
                        <th class="p-4">Ref Order</th>
                        <th class="p-4">Metode</th>
                        <th class="p-4">Total</th>
                        <th class="p-4">Tanggal</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php foreach ($orders_history as $o): ?>
                        <tr>
                            <td class="p-4 font-mono font-bold text-stone-900"><?= $o['order_ref'] ?></td>
                            <td class="p-4"><?= htmlspecialchars($o['payment_method'] ?? 'N/A') ?></td>
                            <td class="p-4 font-bold text-stone-800"><?= format_rupiah($o['total_amount']) ?></td>
                            <td class="p-4 text-stone-500"><?= date('d M Y H:i', strtotime($o['created_at'])) ?></td>
                            <td class="p-4">
                                <?php if ($o['payment_status'] === 'paid'): ?>
                                    <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded text-[10px] font-bold">LUNAS</span>
                                <?php elseif ($o['payment_status'] === 'pending'): ?>
                                    <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded text-[10px] font-bold">MENUNGGU</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-[10px] font-bold">KADALUARSA</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-right">
                                <?php if ($o['payment_status'] === 'pending'): ?>
                                    <a href="payment_detail.php?order_ref=<?= $o['order_ref'] ?>" class="bg-amber-500 text-stone-900 px-3 py-1.5 rounded text-[10px] font-bold hover:bg-amber-400">Bayar</a>
                                <?php else: ?>
                                    <span class="text-stone-400 text-[10px]">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>