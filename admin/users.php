<?php
require_once 'layout_header.php';

if (isset($_GET['action']) && $_GET['action'] === 'toggle_role' && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE users SET role = IF(role = 'customer', 'admin', 'customer') WHERE id = ?");
    $stmt->execute([$user_id]);
    header('Location: users.php');
    exit;
}

$users = $pdo->query("SELECT u.*, COUNT(ud.ebook_id) AS ebooks_owned 
                      FROM users u 
                      LEFT JOIN user_downloads ud ON u.id = ud.user_id 
                      GROUP BY u.id 
                      ORDER BY u.id DESC")->fetchAll();
?>

<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-stone-900">Manajemen Pengguna (User)</h1>
        <p class="text-xs text-stone-500 mt-1">Daftar pembaca terdaftar dan kontrol hak akses administrator</p>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-stone-50 text-stone-500 font-semibold border-b">
                <tr>
                    <th class="p-3">Nama Lengkap</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Role Hak Akses</th>
                    <th class="p-3">Koleksi Ebook</th>
                    <th class="p-3">Tanggal Daftar</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-stone-50/50 transition">
                        <td class="p-3 font-bold text-stone-900"><?= htmlspecialchars($u['full_name']) ?></td>
                        <td class="p-3 font-mono text-stone-600"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $u['role'] === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' ?>">
                                <?= strtoupper($u['role']) ?>
                            </span>
                        </td>
                        <td class="p-3 font-semibold text-stone-800"><?= $u['ebooks_owned'] ?> Ebook</td>
                        <td class="p-3 text-stone-500"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                        <td class="p-3 text-right">
                            <a href="users.php?action=toggle_role&id=<?= $u['id'] ?>" onclick="return confirm('Ubah role pengguna ini?')" class="text-stone-700 hover:underline font-semibold">Ubah Role</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>