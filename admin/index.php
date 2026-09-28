<?php
require_once 'layout_header.php';

$total_sales = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE payment_status = 'paid'")->fetchColumn() ?? 0;
$total_paid_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
$total_pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'pending'")->fetchColumn();
$total_customers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$total_ebooks = $pdo->query("SELECT COUNT(*) FROM ebooks")->fetchColumn();
$total_downloads = $pdo->query("SELECT SUM(download_count) FROM user_downloads")->fetchColumn() ?? 0;

$recent_orders = $pdo->query("SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC LIMIT 6")->fetchAll();
$top_ebooks = $pdo->query("SELECT e.title, SUM(ud.download_count) as total_dl FROM user_downloads ud JOIN ebooks e ON ud.ebook_id = e.id GROUP BY e.id ORDER BY total_dl DESC LIMIT 4")->fetchAll();
?>

<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-stone-900">Dashboard Panel Administrator</h1>
            <p class="text-xs text-stone-500 mt-1">Ringkasan performa finansial dan operasional platform stefanzweig.eu</p>
        </div>
        <div class="flex gap-2">
            <a href="ebooks.php" class="bg-stone-900 text-white text-xs font-semibold px-4 py-2.5 rounded-xl hover:bg-stone-800 transition flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Ebook</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm space-y-3">
            <div class="flex items-center justify-between text-stone-500">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Omzet Lunas</span>
                <i data-lucide="wallet" class="w-5 h-5 text-emerald-600"></i>
            </div>
            <p class="text-2xl font-bold text-emerald-600"><?= format_rupiah($total_sales) ?></p>
            <p class="text-[11px] text-stone-400">Dari <?= $total_paid_orders ?> transaksi selesai</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm space-y-3">
            <div class="flex items-center justify-between text-stone-500">
                <span class="text-xs font-semibold uppercase tracking-wider">Menunggu Bayar</span>
                <i data-lucide="clock" class="w-5 h-5 text-amber-500"></i>
            </div>
            <p class="text-2xl font-bold text-amber-600"><?= $total_pending_orders ?></p>
            <p class="text-[11px] text-stone-400">Pesanan pending hari ini</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm space-y-3">
            <div class="flex items-center justify-between text-stone-500">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Customer</span>
                <i data-lucide="users" class="w-5 h-5 text-indigo-500"></i>
            </div>
            <p class="text-2xl font-bold text-stone-900"><?= $total_customers ?></p>
            <p class="text-[11px] text-stone-400">Akun pembaca terdaftar</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm space-y-3">
            <div class="flex items-center justify-between text-stone-500">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Unduhan Ebook</span>
                <i data-lucide="download" class="w-5 h-5 text-blue-500"></i>
            </div>
            <p class="text-2xl font-bold text-stone-900"><?= number_format($total_downloads) ?></p>
            <p class="text-[11px] text-stone-400">Katalog aktif: <?= $total_ebooks ?> judul</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-stone-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                <h3 class="font-bold text-stone-900 text-sm">Transaksi Terbaru</h3>
                <a href="orders.php" class="text-xs font-semibold text-red-700 hover:underline">Lihat Semua</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 text-stone-500 font-semibold border-b border-stone-200">
                        <tr>
                            <th class="p-3">Ref Order</th>
                            <th class="p-3">Pembeli</th>
                            <th class="p-3">Metode</th>
                            <th class="p-3">Total</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <?php foreach ($recent_orders as $o): ?>
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="p-3 font-mono font-bold text-stone-900"><?= $o['order_ref'] ?></td>
                                <td class="p-3"><?= htmlspecialchars($o['full_name']) ?></td>
                                <td class="p-3 font-semibold text-stone-600"><?= htmlspecialchars($o['payment_method'] ?? '-') ?></td>
                                <td class="p-3 font-bold text-stone-900"><?= format_rupiah($o['total_amount']) ?></td>
                                <td class="p-3">
                                    <?php if ($o['payment_status'] === 'paid'): ?>
                                        <span class="bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded text-[10px]">PAID</span>
                                    <?php elseif ($o['payment_status'] === 'pending'): ?>
                                        <span class="bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded text-[10px]">PENDING</span>
                                    <?php else: ?>
                                        <span class="bg-red-100 text-red-800 font-bold px-2 py-0.5 rounded text-[10px]">EXPIRED</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-stone-200/80 shadow-sm space-y-4 h-fit">
            <h3 class="font-bold text-stone-900 text-sm border-b border-stone-100 pb-4">Ebook Terpopuler (Unduhan)</h3>
            <?php if (empty($top_ebooks)): ?>
                <p class="text-xs text-stone-400 text-center py-6">Belum ada aktivitas unduhan.</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($top_ebooks as $tb): ?>
                        <div class="flex justify-between items-center p-3 bg-stone-50 rounded-xl border border-stone-200/60">
                            <span class="text-xs font-semibold text-stone-800 truncate max-w-[180px]"><?= htmlspecialchars($tb['title']) ?></span>
                            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md border border-indigo-100"><?= number_format($tb['total_dl']) ?>x unduh</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>