<?php
// notification.php (Midtrans Webhook Handler)
// --- REVISI FINAL UNTUK STABILITAS ---

// Tidak perlu start session di sini, karena ini diakses oleh server Midtrans
include './database/koneksi.php';
include './includes/midtrans_config.php';

// Atur header
header('Content-Type: application/json');

// Dapatkan JSON notifikasi dari Midtrans
$json_result = file_get_contents('php://input');
$result = json_decode($json_result);

// --- 1. VALIDASI DASAR DAN DATA ---
if (!is_object($result) || !isset($result->order_id)) {
    http_response_code(400); // Bad Request
    exit();
}

$order_id = $result->order_id; // Ini adalah midtrans_order_id (string)

try {
    // Ambil status transaksi dari Midtrans (Disarankan untuk keamanan)
    $status = \Midtrans\Transaction::status($order_id);
} catch (Exception $e) {
    error_log("Midtrans API Status Check Failed: " . $e->getMessage());
    http_response_code(500);
    exit();
}


$server_status = $status->transaction_status;
$fraud_status = $status->fraud_status;
$gross_amount_str = strval($status->gross_amount); // Ambil sebagai string Midtrans


// --- 2. VALIDASI SIGNATURE HASH (KEAMANAN KRITIS) ---

// 📢 PERBAIKAN: Hapus desimal dari gross_amount untuk hashing yang akurat
$amount_for_hash = preg_replace("/\.00$/", "", $gross_amount_str);

$input_signature_key = $result->signature_key;
$calculated_signature_key = hash(
    'sha512',
    $order_id .
        $server_status .
        $amount_for_hash . // Menggunakan nilai yang sudah diparsing
        \Midtrans\Config::$serverKey
);

if ($calculated_signature_key != $input_signature_key) {
    http_response_code(403);
    error_log("Midtrans Security Alert: Invalid signature key for order ID " . $order_id);
    exit();
}


// --- 3. AMBIL DATA PESANAN DARI DATABASE ---
// 📢 PERUBAHAN: Ambil ID INT (id) dari tabel orders, bukan hanya status dan user_id
$check_order_sql = "SELECT id, status, user_id FROM orders WHERE midtrans_order_id = ?";
$stmt_check = $conn->prepare($check_order_sql);
$stmt_check->bind_param("s", $order_id); // $order_id adalah midtrans_order_id (string)
$stmt_check->execute();
$order_data = $stmt_check->get_result()->fetch_assoc();

if (!$order_data) {
    http_response_code(404); // Pesanan tidak ditemukan
    exit();
}

// 📢 Dapatkan ID INT dari tabel orders untuk digunakan di order_items
$db_order_id = $order_data['id'];
$current_db_status = $order_data['status'];
$user_id = $order_data['user_id'];

// Pencegahan: Hanya proses jika status di database masih 'pending'
if ($current_db_status != 'pending') {
    http_response_code(200);
    exit();
}


// --- 4. LOGIKA UPDATE STATUS & TRANSAKSI DATABASE ---

$new_status = $current_db_status;

if (($server_status == 'capture' && $fraud_status == 'accept') || $server_status == 'settlement') {
    $new_status = 'paid';
} else if ($server_status == 'pending') {
    $new_status = 'pending';
} else if ($server_status == 'deny' || $server_status == 'cancel' || $server_status == 'expire') {
    $new_status = 'cancelled';
}

$conn->begin_transaction();
try {
    // A. Update Status di Tabel orders
    $update_order_sql = "UPDATE orders SET status = ? WHERE midtrans_order_id = ?";
    $stmt_update = $conn->prepare($update_order_sql);
    $stmt_update->bind_param("ss", $new_status, $order_id);
    $stmt_update->execute();

    // B. PENGURANGAN STOK & PEMBERSIHAN KERANJANG (HANYA JIKA PAID)
    if ($new_status == 'paid') {

        // 1. Ambil detail item yang dipesan
        // 📢 PERUBAHAN: Gunakan $db_order_id (ID INT) yang sudah kita ambil untuk query order_items
        $items_sql = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
        $stmt_items = $conn->prepare($items_sql);
        $stmt_items->bind_param("i", $db_order_id); // Gunakan 'i' karena order_id di order_items adalah INT
        $stmt_items->execute();
        $order_items = $stmt_items->get_result();

        // 2. Kurangi stok dan hapus dari keranjang
        $stock_sql = "UPDATE products SET stok = stok - ? WHERE id = ?";
        $delete_cart_sql = "DELETE FROM carts WHERE user_id = ?";

        while ($item = $order_items->fetch_assoc()) {
            // Update Stok
            $stmt_stock = $conn->prepare($stock_sql);
            if (!$stmt_stock->bind_param("ii", $item['quantity'], $item['product_id'])) {
                throw new Exception("Stock update binding failed.");
            }
            if (!$stmt_stock->execute()) { // Tambahkan pengecekan execute
                throw new Exception("Stock update execution failed: " . $stmt_stock->error);
            }
        }

        // 3. Bersihkan keranjang user
        if ($user_id) {
            $stmt_delete_cart = $conn->prepare($delete_cart_sql);
            $stmt_delete_cart->bind_param("i", $user_id);
            if (!$stmt_delete_cart->execute()) {
                throw new Exception("Cart deletion failed.");
            }
        }
    }

    // COMMIT transaksi jika semua berhasil
    $conn->commit();
    http_response_code(200);
} catch (Exception $e) {
    // ROLLBACK jika ada error
    $conn->rollback();
    // 📢 PERBAIKAN: Log error yang lebih jelas
    error_log("Midtrans Rollback Error (Order ID: " . $order_id . "): " . $e->getMessage());
    http_response_code(500);
}
