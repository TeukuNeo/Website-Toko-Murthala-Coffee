<?php
session_start();

$pageTitle = 'Kelola Status Pesanan';
$message = '';
$status_options = ['pending', 'paid', 'settlement', 'processing', 'shipped', 'delivered', 'cancelled'];

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_orders.php");
    exit;
}

$order_id = intval($_GET['id']);


// =============================
// 1. PROSES UPDATE STATUS (POST)
// =============================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_status'])) {

    $new_status = $_POST['new_status'];

    if (!in_array($new_status, $status_options)) {
        header("Location: admin_order_edit.php?id=$order_id");
        exit;
    }

    include './database/koneksi.php';

    $update = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $update->bind_param("si", $new_status, $order_id);

    if ($update->execute()) {
        // 100% memastikan data baru diambil setelah update
        header("Location: admin_order_edit.php?id=$order_id&msg=success&ts=" . time());
        exit;
    } else {
        $_SESSION['error_message'] = "<div class='alert alert-danger'>Gagal update status.</div>";
        header("Location: admin_order_edit.php?id=$order_id");
        exit;
    }
}



// =============================
// 2. TAMPILAN HALAMAN (GET)
// =============================
include './database/koneksi.php';
include "includes/adminHeader.php";

if (isset($_SESSION['error_message'])) {
    $message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if (isset($_GET['msg']) && $_GET['msg'] == 'success') {
    $message = "<div class='alert alert-success'>Status pesanan berhasil diperbarui.</div>";
}


// =============================
// 3. AMBIL DATA ORDER TERBARU
// =============================
$detail = $conn->prepare("
    SELECT o.*, u.nama_lengkap, u.email, u.nomor_hp
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");
$detail->bind_param("i", $order_id);
$detail->execute();
$order_data = $detail->get_result()->fetch_assoc();

if (!$order_data) {
    die("Pesanan tidak ditemukan.");
}

$current_status = $order_data['status'];


// =============================
// 4. AMBIL ITEM PESANAN
// =============================
$items = $conn->prepare("
    SELECT oi.quantity, oi.price_at_order, p.nama_produk
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$items->bind_param("i", $order_id);
$items->execute();
$order_items = $items->get_result();
?>

<div class="container my-5">
    <h2>Kelola Pesanan #<?= htmlspecialchars($order_id) ?></h2>

    <?= $message ?>

    <div class="row">

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">Status Saat Ini</div>
                <div class="card-body">
                    <h1 class="display-6">
                        <span class="badge bg-danger p-3 mb-3">
                            <?= strtoupper($current_status) ?>
                        </span>
                    </h1>

                    <form method="POST">
                        <label class="form-label">Ubah Status:</label>
                        <select name="new_status" class="form-select mb-3">
                            <?php foreach ($status_options as $opt): ?>
                                <option value="<?= $opt ?>" <?= ($opt == $current_status ? "selected" : "") ?>>
                                    <?= strtoupper($opt) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button class="btn btn-primary w-100">Simpan</button>
                        <a href="admin_orders.php" class="btn btn-outline-secondary mt-2 w-100">Kembali</a>
                    </form>

                </div>
            </div>
        </div>

        <div class="col-md-8">

            <!-- DETAIL CUSTOMER -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">Detail Customer</div>
                <div class="card-body">
                    <p><strong>Nama:</strong> <?= htmlspecialchars($order_data['nama_lengkap']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order_data['email']) ?></p>
                    <p><strong>Telepon:</strong> <?= htmlspecialchars($order_data['nomor_hp']) ?></p>
                    <p><strong>Alamat Kirim:</strong> <?= nl2br(htmlspecialchars($order_data['shipping_address'])) ?></p>
                </div>
            </div>

            <!-- DETAIL ITEM -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">Detail Item Pesanan</div>
                <ul class="list-group list-group-flush">
                    <?php while ($row = $order_items->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <div>
                                <strong><?= htmlspecialchars($row['nama_produk']) ?></strong>
                                (<?= $row['quantity'] ?> × Rp <?= number_format($row['price_at_order'], 0, ',', '.') ?>)
                            </div>
                            <span class="fw-bold">
                                Rp <?= number_format($row['price_at_order'] * $row['quantity'], 0, ',', '.') ?>
                            </span>
                        </li>
                    <?php endwhile; ?>

                    <li class="list-group-item d-flex justify-content-between bg-light">
                        <h5>Total</h5>
                        <h5 class="text-danger">Rp <?= number_format($order_data['total_amount'], 0, ',', '.') ?></h5>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>

<?php include "includes/adminFooter.php"; ?>