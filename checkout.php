<?php
require_once 'includes/header.php';
require_login();

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$total = array_sum(array_column($cart, 'price'));
?>

<div class="max-w-xl mx-auto px-4 py-12 space-y-6">
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200 shadow-sm space-y-6">
        <div>
            <h1 class="font-serif-custom text-2xl font-bold text-stone-900">Konfirmasi Pemesanan</h1>
            <p class="text-xs text-stone-500 mt-1">Lengkapi informasi untuk memproses invoice SakuRupiah</p>
        </div>

        <form action="process_checkout.php" method="POST" class="space-y-4 text-xs">
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Nama Pembeli</label>
                <input type="text" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" class="w-full px-3.5 py-2.5 border border-stone-300 rounded-xl bg-stone-50" readonly>
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Email Pembeli</label>
                <input type="email" value="<?= htmlspecialchars($_SESSION['user_email']) ?>" class="w-full px-3.5 py-2.5 border border-stone-300 rounded-xl bg-stone-50" readonly>
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Nomor WhatsApp / HP</label>
                <input type="text" name="phone" placeholder="081234567890" class="w-full px-3.5 py-2.5 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Metode Pembayaran SakuRupiah</label>
                <select name="payment_method" class="w-full px-3.5 py-2.5 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none" required>
                    <option value="QRIS">QRIS (OVO, DANA, GoPay, ShopeePay, All Bank)</option>
                    <option value="BNIVA">Virtual Account Bank BNI</option>
                    <option value="BRIVA">Virtual Account Bank BRI</option>
                    <option value="MANDIRIVA">Virtual Account Bank Mandiri</option>
                </select>
            </div>

            <div class="pt-4 border-t border-stone-200 space-y-2">
                <div class="flex justify-between font-bold text-sm text-stone-900">
                    <span>Total Tagihan:</span>
                    <span><?= format_rupiah($total) ?></span>
                </div>
            </div>

            <button type="submit" class="w-full bg-stone-900 text-white font-bold py-3 rounded-xl hover:bg-amber-600 transition">Proses Pembayaran Direct</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>