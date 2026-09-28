<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — stefanzweig.eu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-stone-100 text-stone-800 antialiased min-h-screen flex flex-col md:flex-row">

    <aside class="w-full md:w-64 bg-stone-900 text-stone-300 flex-shrink-0 flex flex-col justify-between p-5 border-r border-stone-800">
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <a href="index.php" class="font-serif text-xl font-bold text-white tracking-tight">
                    stefanzweig<span class="text-red-500">.admin</span>
                </a>
                <span class="text-[10px] bg-red-900/60 text-red-300 border border-red-700/50 px-2 py-0.5 rounded font-mono font-bold">PRO</span>
            </div>

            <nav class="space-y-1 text-xs font-medium">
                <a href="index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= $current_page === 'index.php' ? 'bg-stone-800 text-white font-bold' : 'text-stone-400 hover:bg-stone-800/60 hover:text-stone-200' ?>">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard Utama</span>
                </a>
                <a href="ebooks.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($current_page === 'ebooks.php' || $current_page === 'ebook_edit.php') ? 'bg-stone-800 text-white font-bold' : 'text-stone-400 hover:bg-stone-800/60 hover:text-stone-200' ?>">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                    <span>Katalog Ebook</span>
                </a>
                <a href="categories.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($current_page === 'categories.php' || $current_page === 'category_edit.php') ? 'bg-stone-800 text-white font-bold' : 'text-stone-400 hover:bg-stone-800/60 hover:text-stone-200' ?>">
                    <i data-lucide="folder-tree" class="w-4 h-4"></i>
                    <span>Kategori Buku</span>
                </a>
                <a href="orders.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= $current_page === 'orders.php' ? 'bg-stone-800 text-white font-bold' : 'text-stone-400 hover:bg-stone-800/60 hover:text-stone-200' ?>">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    <span>Kelola Transaksi</span>
                </a>
                <a href="users.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= $current_page === 'users.php' ? 'bg-stone-800 text-white font-bold' : 'text-stone-400 hover:bg-stone-800/60 hover:text-stone-200' ?>">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span>Manajemen User</span>
                </a>
            </nav>
        </div>

        <div class="pt-6 border-t border-stone-800 space-y-2">
            <a href="../index.php" target="_blank" class="flex items-center justify-between px-3.5 py-2 rounded-lg text-xs text-stone-400 hover:text-white hover:bg-stone-800 transition">
                <span>Lihat Storefront</span>
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
            </a>
            <a href="../logout.php" class="flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs text-red-400 hover:bg-red-950/30 transition">
                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                <span>Keluar Akun</span>
            </a>
        </div>
    </aside>

    <main class="flex-grow p-6 md:p-10 space-y-8 overflow-x-hidden">