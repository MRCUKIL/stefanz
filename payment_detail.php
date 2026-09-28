<?php
require_once 'includes/header.php';
require_login();

$order_ref = filter_input(INPUT_GET, 'order_ref', FILTER_DEFAULT);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_ref = ? AND user_id = ?");
$stmt->execute([$order_ref, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: dashboard.php');
    exit;
}

if ($order['payment_status'] === 'paid') {
    header('Location: dashboard.php?payment=success');
    exit;
}
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<div class="max-w-xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl border border-stone-200 p-6 sm:p-8 shadow-sm space-y-6 text-center">
        
        <div class="border-b border-stone-200 pb-4">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 bg-amber-100 px-3 py-1 rounded-full">Menunggu Pembayaran</span>
            <h1 class="font-serif-custom text-2xl font-bold text-stone-900 mt-3"><?= format_rupiah($order['total_amount']) ?></h1>
            <p class="text-xs text-stone-500 mt-1">Ref: <span id="ref-code" class="font-mono font-bold text-stone-800"><?= $order['order_ref'] ?></span></p>
        </div>

        <div class="bg-amber-50 p-4 rounded-xl border border-amber-200/80">
            <p class="text-xs text-stone-600 mb-1">Selesaikan Pembayaran Dalam:</p>
            <div id="countdown-timer" class="font-mono font-bold text-xl text-amber-700">00:00:00</div>
        </div>

        <?php if ($order['payment_method'] === 'QRIS' && !empty($order['qr_string'])): ?>
            <div class="space-y-3">
                <p class="text-xs font-semibold text-stone-700">Pindai Kode QRIS Di Bawah Ini:</p>
                <div id="qrcode-container" class="flex justify-center p-4 bg-white rounded-xl border border-stone-200 inline-block"></div>
                <p class="text-[11px] text-stone-500">Mendukung GoPay, OVO, DANA, ShopeePay, & Mobile Banking.</p>
            </div>
        <?php else: ?>
            <div class="space-y-3 bg-stone-50 p-4 rounded-xl border border-stone-200">
                <p class="text-xs font-semibold text-stone-700">Nomor Virtual Account (<?= htmlspecialchars($order['payment_method']) ?>):</p>
                <div class="flex items-center justify-between bg-white px-4 py-3 rounded-lg border border-stone-300">
                    <span id="payment-number" class="font-mono text-lg font-bold text-stone-900"><?= htmlspecialchars($order['payment_no'] ?? 'Menunggu Kode...') ?></span>
                    <button onclick="copyToClipboard('payment-number')" class="bg-stone-900 text-white text-xs px-3 py-1.5 rounded-md font-semibold hover:bg-amber-600 transition flex items-center gap-1">
                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        <span>Salin</span>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <div class="pt-4 border-t border-stone-100 flex items-center justify-center gap-2 text-xs text-stone-500">
            <div class="w-2 h-2 bg-amber-500 rounded-full animate-ping"></div>
            <span>Memeriksa status pembayaran secara otomatis...</span>
        </div>

    </div>
</div>

<script>
    <?php if ($order['payment_method'] === 'QRIS' && !empty($order['qr_string'])): ?>
        new QRCode(document.getElementById("qrcode-container"), {
            text: "<?= $order['qr_string'] ?>",
            width: 200,
            height: 200
        });
    <?php endif; ?>

    function copyToClipboard(elementId) {
        const text = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert("Nomor pembayaran berhasil disalin!");
        }).catch(err => {
            alert("Gagal menyalin teks.");
        });
    }

    const expiredTime = new Date("<?= date('Y-m-d\TH:i:s', strtotime($order['expired_at'] ?? '+24 hours')) ?>").getTime();
    
    const timerInterval = setInterval(function() {
        const now = new Date().getTime();
        const distance = expiredTime - now;

        if (distance < 0) {
            clearInterval(timerInterval);
            document.getElementById("countdown-timer").innerHTML = "EXPIRED";
            alert("Waktu pembayaran telah habis.");
            window.location.href = "dashboard.php";
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("countdown-timer").innerHTML = 
            (hours < 10 ? "0" + hours : hours) + ":" + 
            (minutes < 10 ? "0" + minutes : minutes) + ":" + 
            (seconds < 10 ? "0" + seconds : seconds);
    }, 1000);

    const pollInterval = setInterval(function() {
        fetch('check_payment_status.php?order_ref=<?= $order['order_ref'] ?>')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.payment_status === 'paid') {
                    clearInterval(pollInterval);
                    alert("Pembayaran Berhasil Dikonfirmasi!");
                    window.location.href = "dashboard.php?payment=success";
                }
            })
            .catch(err => console.error("Polling Error:", err));
    }, 3000);
</script>

<?php require_once 'includes/footer.php'; ?>