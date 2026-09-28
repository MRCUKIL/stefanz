<?php
require_once 'includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email     = sanitize($_POST['email']);
    $password  = $_POST['password'];

    $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmtCheck->execute([$email]);

    if ($stmtCheck->fetch()) {
        $error = "Email sudah terdaftar. Silakan masuk.";
    } else {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmtInsert = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'customer')");
        $stmtInsert->execute([$full_name, $email, $hashed]);

        header('Location: login.php?registered=success');
        exit;
    }
}
?>

<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white p-8 rounded-3xl border border-stone-200 shadow-sm space-y-6">
        <div>
            <h1 class="font-serif-custom text-2xl font-bold text-stone-900">Daftar Akun Baru</h1>
            <p class="text-xs text-stone-500 mt-1">Buat akun untuk mengoleksi e-book Stefan Zweig</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-700 text-xs p-3 rounded-xl border border-red-200"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4 text-xs">
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Nama Lengkap</label>
                <input type="text" name="full_name" class="w-full px-3.5 py-2.5 border rounded-xl" required>
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Email</label>
                <input type="email" name="email" class="w-full px-3.5 py-2.5 border rounded-xl" required>
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full px-3.5 py-2.5 border rounded-xl" required>
            </div>
            <button type="submit" class="w-full bg-stone-900 text-white font-bold py-3 rounded-xl hover:bg-amber-600 transition">Daftar Akun</button>
        </form>

        <p class="text-xs text-center text-stone-500">Sudah punya akun? <a href="login.php" class="text-amber-600 font-bold hover:underline">Masuk Di Sini</a></p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>