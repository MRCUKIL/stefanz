<?php
require_once 'layout_header.php';

if (isset($_GET['action']) && $_GET['action'] === 'confirm_paid' && isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];
    
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid', paid_at = NOW() WHERE id = ?");
    $stmt->execute([$order_id]);

    $stmtOrder = $pdo->prepare("SELECT user_id FROM orders WHERE id = ?");
    $stmtOrder->execute([$order_id]);
    $user_id = $stmtOrder->fetchColumn();

    $stmtItems = $pdo->prepare("SELECT ebook_id FROM order_items WHERE order_id = ?");
    $stmtItems->execute([$order_id]);
    $items = $stmtItems->fetchAll();

    $stmtGrant = $pdo->prepare("INSERT INTO user_downloads (user_id, ebook_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE download_count = download_count");
    foreach ($items as $it) {
        $stmtGrant->execute([$user_id, $it['ebook_id']]);
    }

    $pdo->commit();
    header('Location: orders.php?status=confirmed');
    exit;
}

$orders = $pdo->query("SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC")->fetchAll();
?>

<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-stone-900">Kelola Semua Transaksi</h1>
        <p class="text-xs text-stone-500 mt-1">Pemantauan arus kas, status pembayaran Sakurupiah, dan verifikasi manual</p>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-stone-50 text-stone-500 font-semibold border-b">
                <tr>
                    <th class="p-3">Ref Transaksi</th>
                    <th class="p-3">Customer</th>
                    <th class="p-3">Metode</th>
                    <th class="p-3">Nominal</th>
                    <th class="p-3">Tanggal Order</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                <?php foreach ($orders as $o): ?>
                    <tr class="hover:bg-stone-50/50 transition">
                        <td class="p-3 font-mono font-bold text-stone-900"><?= $o['order_ref'] ?></td>
                        <td class="p-3">
                            <p class="font-bold text-stone-800"><?= htmlspecialchars($o['full_name']) ?></p>
                            <p class="text-[10px] text-stone-400"><?= htmlspecialchars($o['email']) ?></p>
                        </td>
                        <td class="p-3 font-semibold text-stone-700"><?= htmlspecialchars($o['payment_method'] ?? '-') ?></td>
                        <td class="p-3 font-bold text-stone-900"><?= format_rupiah($o['total_amount']) ?></td>
                        <td class="p-3 text-stone-500"><?= date('d M Y H:i', strtotime($o['created_at'])) ?></td>
                        <td class="p-3">
                            <?php if ($o['payment_status'] === 'paid'): ?>
                                <span class="bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded text-[10px]">PAID</span>
                            <?php elseif ($o['payment_status'] === 'pending'): ?>
                                <span class="bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded text-[10px]">PENDING</span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-800 font-bold px-2 py-0.5 rounded text-[10px]">EXPIRED</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-right">
                            <?php if ($o['payment_status'] === 'pending'): ?>
                                <a href="orders.php?action=confirm_paid&id=<?= $o['id'] ?>" onclick="return confirm('Konfirmasi transaksi ini secara manual?')" class="bg-emerald-600 text-white px-2.5 py-1 rounded text-[10px] font-bold hover:bg-emerald-700">Set Lunas</a>
                            <?php else: ?>
                                <span class="text-stone-400 text-[10px]">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>