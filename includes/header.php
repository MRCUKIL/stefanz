<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions.php';
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>stefanzweig.eu — Koleksi E-Book Karya Stefan Zweig</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif-custom { font-family: 'Cinzel', serif; }
    </style>
</head>
<body class="bg-stone-50 text-stone-800 antialiased min-h-screen flex flex-col">

    <header class="bg-stone-900 text-stone-100 sticky top-0 z-50 border-b border-stone-800">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            
            <a href="<?= SITE_URL ?>/index.php" class="font-serif-custom text-xl font-bold tracking-wider hover:text-amber-400 transition">
                STEFAN ZWEIG<span class="text-amber-500">.EU</span>
            </a>

            <nav class="hidden md:flex items-center space-x-6 text-xs font-semibold uppercase tracking-wider text-stone-300">
                <a href="<?= SITE_URL ?>/index.php" class="hover:text-white transition">Katalog Buku</a>
                <a href="<?= SITE_URL ?>/pages/biografi.php" class="hover:text-white transition">Tentang Zweig</a>
                <a href="<?= SITE_URL ?>/pages/faq.php" class="hover:text-white transition">FAQ</a>
            </nav>

            <div class="hidden md:flex items-center space-x-4">
                <a href="<?= SITE_URL ?>/cart.php" class="relative p-2 text-stone-300 hover:text-white transition">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-amber-500 text-stone-900 font-bold text-[10px] w-4 h-4 rounded-full flex items-center justify-center"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>

                <?php if (is_logged_in()): ?>
                    <a href="<?= SITE_URL ?>/dashboard.php" class="bg-stone-800 border border-stone-700 text-stone-200 text-xs font-semibold px-3.5 py-1.5 rounded-lg hover:bg-stone-700 transition">Dashboard</a>
                    <?php if (is_admin()): ?>
                        <a href="<?= SITE_URL ?>/admin/index.php" class="bg-red-900/80 border border-red-700 text-red-200 text-xs font-semibold px-3.5 py-1.5 rounded-lg hover:bg-red-800 transition">Admin</a>
                    <?php endif; ?>
                    <a href="<?= SITE_URL ?>/logout.php" class="text-stone-400 hover:text-red-400 text-xs font-semibold transition">Keluar</a>
                <?php else: ?>
                    <a href="<?= SITE_URL ?>/login.php" class="text-xs font-semibold text-stone-300 hover:text-white transition">Masuk</a>
                    <a href="<?= SITE_URL ?>/register.php" class="bg-amber-500 text-stone-900 text-xs font-bold px-3.5 py-1.5 rounded-lg hover:bg-amber-400 transition">Daftar</a>
                <?php endif; ?>
            </div>

            <div class="flex items-center space-x-3 md:hidden">
                <a href="<?= SITE_URL ?>/cart.php" class="relative p-2 text-stone-300">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-amber-500 text-stone-900 font-bold text-[10px] w-4 h-4 rounded-full flex items-center justify-center"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
                <button id="mobile-menu-btn" class="p-2 text-stone-300 hover:text-white focus:outline-none">
                    <i data-lucide="menu" id="menu-icon-open" class="w-6 h-6"></i>
                    <i data-lucide="x" id="menu-icon-close" class="w-6 h-6 hidden"></i>
                </button>
            </div>

        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-stone-900 border-b border-stone-800 px-4 pt-2 pb-6 space-y-4">
            <nav class="flex flex-col space-y-3 text-xs font-semibold uppercase tracking-wider text-stone-300 border-b border-stone-800 pb-4">
                <a href="<?= SITE_URL ?>/index.php" class="py-1 hover:text-white transition">Katalog Buku</a>
                <a href="<?= SITE_URL ?>/pages/biografi.php" class="py-1 hover:text-white transition">Tentang Zweig</a>
                <a href="<?= SITE_URL ?>/pages/faq.php" class="py-1 hover:text-white transition">FAQ</a>
            </nav>

            <div class="flex flex-col space-y-2 pt-2">
                <?php if (is_logged_in()): ?>
                    <a href="<?= SITE_URL ?>/dashboard.php" class="w-full text-center bg-stone-800 text-stone-200 text-xs font-semibold py-2 rounded-lg">Dashboard Pembaca</a>
                    <?php if (is_admin()): ?>
                        <a href="<?= SITE_URL ?>/admin/index.php" class="w-full text-center bg-red-900/80 text-red-200 text-xs font-semibold py-2 rounded-lg">Panel Admin</a>
                    <?php endif; ?>
                    <a href="<?= SITE_URL ?>/logout.php" class="w-full text-center text-stone-400 text-xs font-semibold py-2">Keluar Akun</a>
                <?php else: ?>
                    <a href="<?= SITE_URL ?>/login.php" class="w-full text-center border border-stone-700 text-stone-300 text-xs font-semibold py-2 rounded-lg">Masuk</a>
                    <a href="<?= SITE_URL ?>/register.php" class="w-full text-center bg-amber-500 text-stone-900 text-xs font-bold py-2 rounded-lg">Daftar Akun Baru</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="flex-grow">

    <script>
        const menuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const iconOpen = document.getElementById('menu-icon-open');
        const iconClose = document.getElementById('menu-icon-close');

        if (menuBtn) {
            menuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                iconOpen.classList.toggle('hidden');
                iconClose.classList.toggle('hidden');
            });
        }
    </script>