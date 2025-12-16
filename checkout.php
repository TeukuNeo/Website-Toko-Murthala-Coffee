<?php
session_start();
// 1. INCLUDE FILE PENTING
include './includes/header.php';
include './includes/midtrans_config.php';

// 2. Cek Login dan Role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['shipping_address']) || empty(trim($_POST['shipping_address']))) {
    header("Location: shoppingCart.php?error=Alamat%20pengiriman%20belum%20diisi.");
    exit();
}

$shipping_address = trim($_POST['shipping_address']);

// Jika Anda ingin membatasi panjang alamat (disarankan)
if (strlen($shipping_address) > 500) {
    $shipping_address = substr($shipping_address, 0, 500);
}

$user_id = $_SESSION['user_id'];
// INISIALISASI VARIABEL KRITIS DI LUAR TRY
$customer_details = [];
$transaction_items = [];
$total_amount = 0;
$cart_items_data = [];
$status = 'pending';

// Midtrans ID harus tetap unik string. Kita gunakan ini untuk komunikasi API.
$midtrans_order_id = "MID-" . time() . "-" . $user_id;

// --- 3. AMBIL DATA USER DAN KERANJANG ---
try {
    // 📢 LOGIKA AMBIL DATA CUSTOMER
    $user_sql = "SELECT nama_lengkap, email, nomor_hp FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($user_sql);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $user_data = $stmt_user->get_result()->fetch_assoc();

    if (!$user_data) {
        throw new Exception("Data pengguna tidak ditemukan. Harap login ulang.");
    }
    $customer_details = [
        'first_name' => $user_data['nama_lengkap'],
        'email' => $user_data['email'],
        'phone' => $user_data['nomor_hp'],
    ];

    // 📢 LOGIKA AMBIL DATA KERANJANG
    $cart_sql = "SELECT c.product_id, c.quantity, p.nama_produk, p.harga, p.stok
                 FROM carts c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
    $stmt_cart = $conn->prepare($cart_sql);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $cart_result = $stmt_cart->get_result();

    if ($cart_result->num_rows === 0) {
        header("Location: shoppingCart.php?error=Keranjang%20kosong.");
        exit();
    }

    while ($item = $cart_result->fetch_assoc()) {
        // 🛑 VALIDASI STOK
        if ($item['stok'] < $item['quantity']) {
            throw new Exception("Stok produk '" . $item['nama_produk'] . "' tidak cukup. Sisa stok: " . $item['stok']);
        }

        $subtotal = $item['quantity'] * $item['harga'];
        $total_amount += $subtotal;
        $cart_items_data[] = $item;

        // Format data untuk Midtrans
        $transaction_items[] = [
            'id' => $item['product_id'],
            'price' => round($item['harga']),
            'quantity' => $item['quantity'],
            'name' => $item['nama_produk']
        ];
    }
} catch (Exception $e) {
    die("<h1>Gagal Memuat Keranjang!</h1><p>Error: " . htmlspecialchars($e->getMessage()) . "</p>");
}


// --- 4. CATAT PESANAN KE DATABASE (Transaksi Atomik) ---
$conn->begin_transaction();

try {
    // A. INSERT KE TABEL orders (midtrans_order_id digunakan untuk kolom VARCHAR)
    $order_sql = "INSERT INTO orders (user_id, midtrans_order_id, order_date, total_amount, status, shipping_address)
                  VALUES (?, ?, NOW(), ?, ?, ?)";
    $stmt_insert_order = $conn->prepare($order_sql);

    // Binding: user_id(i), midtrans_order_id(s), total_amount(d), status(s), address(s)
    if (!$stmt_insert_order->bind_param("isdss", $user_id, $midtrans_order_id, $total_amount, $status, $shipping_address)) {
         throw new Exception("Binding parameters failed for orders INSERT.");
    }

    if (!$stmt_insert_order->execute()) {
        throw new Exception("Execution failed for orders INSERT. SQL Error: " . $stmt_insert_order->error);
    }

    // 📢 AMBIL ID PESANAN INT YANG BARU DIBUAT OLEH MYSQL
    $db_order_id = $conn->insert_id; // ID INT untuk Foreign Key

    // B. INSERT KE TABEL order_items (Detail Permanen)
    $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price_at_order) VALUES (?, ?, ?, ?)";

    foreach ($cart_items_data as $item) {
        $price_at_order_str = strval($item['harga']);

        $stmt_item = $conn->prepare($item_sql);

        // Binding: order_id(i), product_id(i), quantity(i), price_at_order(s)
        if (!$stmt_item->bind_param("iiis", $db_order_id, $item['product_id'], $item['quantity'], $price_at_order_str)) {
             throw new Exception("Binding parameters failed for order_items.");
        }

        if (!$stmt_item->execute()) {
             throw new Exception("Execution failed for order_items INSERT: " . $stmt_item->error);
        }
    }

    // COMMIT TRANSAKSI jika semua INSERT berhasil
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    die("<h1>Gagal Menyimpan Pesanan!</h1><p>Mohon ulangi dari keranjang. Error: " . htmlspecialchars($e->getMessage()) . "</p>");
}
// --- END TRANSAKSI DATABASE ---

// --- 5. PREPARE PARAMETER TRANSAKSI MIDTRANS ---
$BASE_URL = "https://localhost/Toko_Murthala_Coffee/notification.php"; // Ganti dengan ngrok/domain publik Anda

$params = [
    'transaction_details' => [
        'order_id' => $midtrans_order_id, // Gunakan string ID untuk Midtrans
        'gross_amount' => round($total_amount),
    ],
    'item_details' => $transaction_items,
    'customer_details' => $customer_details,
    'callbacks' => [
        'finish' => $BASE_URL . '/order_status.php?order_id=' . $midtrans_order_id,
        'notification' => $BASE_URL . '/notification.php',
    ],
];

try {
    // 6. DAPATKAN SNAP TOKEN
    $snapToken = \Midtrans\Snap::getSnapToken($params);
} catch (Exception $e) {
    die("<h1>Error Midtrans API!</h1><p>Silakan cek Server Key Anda. Error: " . htmlspecialchars($e->getMessage()) . "</p>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checkout Pembayaran</title>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo \Midtrans\Config::$clientKey; ?>"></script>
</head>
<body>

    <div class="container my-5">
        <h1>Konfirmasi Pembayaran</h1>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="card-title">Nomor Pesanan: #<?php echo $midtrans_order_id; ?></h4>
                <p class="card-text">Total yang harus dibayar:</p>
                <h1 class="text-danger fw-bolder">Rp <?= number_format($total_amount, 0, ',', '.') ?></h1>
            </div>
        </div>

        <button id="pay-button" class="btn btn-primary btn-lg w-100">Bayar Sekarang</button>
    </div>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function() {
            snap.pay('<?php echo $snapToken; ?>', {
                onSuccess: function(result) {
                    alert("Pembayaran Berhasil! Cek status pesanan Anda.");
                    // Menggunakan ID Midtrans string untuk URL status
                    window.location.href = 'order_status.php?order_id=<?php echo $midtrans_order_id; ?>';
                },
                onPending: function(result) {
                    alert("Pesanan pending, menunggu pembayaran.");
                    window.location.href = 'order_status.php?order_id=<?php echo $midtrans_order_id; ?>';
                },
                onError: function(result) {
                    alert("Pembayaran Gagal. Silakan coba lagi.");
                    window.location.href = 'order_status.php?order_id=<?php echo $midtrans_order_id; ?>';
                }
            });
        };
    </script>

    <?php include './includes/footer.php'; ?>
</body>
</html>