</main>

    <footer class="bg-stone-900 text-stone-400 text-xs border-t border-stone-800 mt-16">
        <div class="max-w-6xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-3">
                <h3 class="font-serif-custom text-white font-bold text-base tracking-wider">STEFAN ZWEIG.EU</h3>
                <p class="text-stone-400 leading-relaxed">Platform penerbitan dan distribusi e-book digital resmi terjemahan karya-karya sastra klasik Stefan Zweig dalam bahasa Indonesia.</p>
            </div>
            <div class="space-y-2">
                <h4 class="text-stone-200 font-semibold uppercase tracking-wider">Navigasi Halaman</h4>
                <ul class="space-y-1">
                    <li><a href="<?= SITE_URL ?>/index.php" class="hover:text-white transition">Katalog Ebook</a></li>
                    <li><a href="<?= SITE_URL ?>/pages/biografi.php" class="hover:text-white transition">Biografi Penulis</a></li>
                    <li><a href="<?= SITE_URL ?>/pages/syarat-ketentuan.php" class="hover:text-white transition">Syarat & Ketentuan</a></li>
                    <li><a href="<?= SITE_URL ?>/pages/kebijakan-privasi.php" class="hover:text-white transition">Kebijakan Privasi</a></li>
                </ul>
            </div>
            <div class="space-y-2">
                <h4 class="text-stone-200 font-semibold uppercase tracking-wider">Metode Pembayaran</h4>
                <p class="text-stone-400">Mendukung pembayaran otomatis via SakuRupiah: QRIS, OVO, DANA, GoPay, ShopeePay, & Virtual Account Bank.</p>
            </div>
        </div>
        <div class="border-t border-stone-800 py-4 text-center text-stone-500 text-[11px]">
            &copy; <?= date('Y') ?> stefanzweig.eu. All Rights Reserved.
        </div>
    </footer>

    <script>lucide.createIcons();</script>
</body>
</html>