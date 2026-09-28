<?php
require_once 'includes/header.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add' && isset($_POST['ebook_id'])) {
        $ebook_id = (int)$_POST['ebook_id'];
        $stmt = $pdo->prepare("SELECT * FROM ebooks WHERE id = ?");
        $stmt->execute([$ebook_id]);
        $eb = $stmt->fetch();
        if ($eb) {
            $_SESSION['cart'][$eb['id']] = $eb;
        }
        header('Location: cart.php');
        exit;
    }

    if ($_POST['action'] === 'remove' && isset($_POST['ebook_id'])) {
        $ebook_id = (int)$_POST['ebook_id'];
        unset($_SESSION['cart'][$ebook_id]);
        header('Location: cart.php');
        exit;
    }
}

$cart = $_SESSION['cart'];
$total = array_sum(array_column($cart, 'price'));
?>

<div class="max-w-4xl mx-auto px-4 py-12 space-y-8">
    <h1 class="font-serif-custom text-2xl font-bold text-stone-900">Keranjang Belanja Ebook</h1>

    <?php if (empty($cart)): ?>
        <div class="bg-white p-8 rounded-2xl border border-stone-200 text-center space-y-4">
            <p class="text-stone-500 text-sm">Keranjang belanja Anda masih kosong.</p>
            <a href="index.php" class="inline-block bg-stone-900 text-white text-xs font-bold px-5 py-2.5 rounded-xl uppercase tracking-wider">Eksplor Katalog</a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm space-y-6">
            <div class="divide-y divide-stone-100">
                <?php foreach ($cart as $item): ?>
                    <div class="py-4 flex justify-between items-center gap-4">
                        <div>
                            <h3 class="font-serif-custom font-bold text-stone-900 text-sm"><?= htmlspecialchars($item['title']) ?></h3>
                            <p class="text-xs text-stone-400 italic"><?= htmlspecialchars($item['original_title']) ?></p>
                            <p class="text-xs font-bold text-stone-800 mt-1"><?= format_rupiah($item['price']) ?></p>
                        </div>
                        <form action="cart.php" method="POST">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="ebook_id" value="<?= $item['id'] ?>">
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold">Hapus</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="pt-6 border-t border-stone-200 flex items-center justify-between">
                <div>
                    <span class="text-xs text-stone-400 font-semibold block">Total Pembayaran</span>
                    <span class="text-2xl font-bold text-stone-900"><?= format_rupiah($total) ?></span>
                </div>
                <a href="checkout.php" class="bg-amber-500 text-stone-900 font-bold text-xs px-6 py-3.5 rounded-xl hover:bg-amber-400 transition">Lanjut Ke Checkout &rarr;</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>