<?php
require_once 'includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email']= $user['email'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header('Location: admin/index.php');
        } else {
            header('Location: dashboard.php');
        }
        exit;
    } else {
        $error = "Email atau password tidak terdaftar.";
    }
}
?>

<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white p-8 rounded-3xl border border-stone-200 shadow-sm space-y-6">
        <div>
            <h1 class="font-serif-custom text-2xl font-bold text-stone-900">Masuk Akun</h1>
            <p class="text-xs text-stone-500 mt-1">Akses perpustakaan digital stefanzweig.eu</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-700 text-xs p-3 rounded-xl border border-red-200"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4 text-xs">
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Email</label>
                <input type="email" name="email" class="w-full px-3.5 py-2.5 border rounded-xl" required>
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full px-3.5 py-2.5 border rounded-xl" required>
            </div>
            <button type="submit" class="w-full bg-stone-900 text-white font-bold py-3 rounded-xl hover:bg-amber-600 transition">Masuk Akun</button>
        </form>

        <p class="text-xs text-center text-stone-500">Belum punya akun? <a href="register.php" class="text-amber-600 font-bold hover:underline">Daftar Sekarang</a></p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>