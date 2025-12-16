<?php
include './includes/header.php';

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['order_id'];

// Ambil status pesanan dari database (Tabel orders)
$sql = "SELECT total_amount, status FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // 📢 PERBAIKAN: INISIALISASI VARIABEL $status DI SINI
    $status = 'not_found'; // Memberi nilai default agar tidak undefined
    $status_text = "Pesanan tidak ditemukan.";
    $status_class = "alert-warning";
    $amount = 0;
    $order_details = null;
} else {
    $order = $result->fetch_assoc();
    $status = $order['status'];
    $amount = $order['total_amount'];

    // Tentukan tampilan berdasarkan status
    if ($status == 'delivered') {
        $status_text = "Barang telah diterima customer!";
        $status_class = "alert-success";
        $status_paid = "Berhasil";
    } elseif ($status == 'shipped') {
        $status_text = "Barang Sedang dikirm ke customer!";
        $status_class = "alert-info";
        $status_paid = "Berhasil";
    }elseif($status == 'processing') {
        $status_text = "Barang Sedang dikemas!";
        $status_class = "alert-info";
        $status_paid = "Berhasil";
    } elseif ($status == 'paid' || $status == 'settlement') {
        $status_text = "Pembayaran Berhasil! Pesanan Anda sedang diproses.";
        $status_class = "alert-success";
        $status_paid = "Berhasil";
    } elseif ($status == 'pending') {
        $status_text = "Menunggu Pembayaran. Silakan selesaikan pembayaran di Midtrans.";
        $status_class = "alert-info";
        $status_paid = "Tertunda";
    } else {
        $status_text = "Pembayaran dibatalkan atau gagal.";
        $status_class = "alert-danger";
        $status_paid = "Gagal";
    }

    // LOGIKA TAMBAHAN: Mengambil Detail Item dari order_items
    $items_sql = "SELECT oi.quantity, oi.price_at_order, p.nama_produk, p.satuan_berat
                  FROM order_items oi
                  JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = ?";
    $stmt_items = $conn->prepare($items_sql);
    $stmt_items->bind_param("s", $order_id);
    $stmt_items->execute();
    $order_details = $stmt_items->get_result();
}
?>

<div class="container my-5">
    <h1>Status Pesanan Anda</h1>
    <div class="alert <?= $status_class ?> shadow-sm">
        <h4 class="alert-heading"><?= $status_text ?></h4>
        <p>Nomor Pesanan: #<?= htmlspecialchars($order_id) ?></p>
        <hr>
        <p class="mb-0">Status Pembayaran: <?= strtoupper($status_paid) ?></p>
    </div>

    <?php if ($order_details && $order_details->num_rows > 0): ?>
        <h2 class="mt-4">Rincian Barang Dipesan</h2>
        <div class="card mb-4 shadow-sm">
            <ul class="list-group list-group-flush">

                <?php while ($item = $order_details->fetch_assoc()): ?>
                    <?php $item_total = $item['price_at_order'] * $item['quantity']; ?>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold"><?= htmlspecialchars($item['nama_produk']) ?></span>
                            <br>
                            <small class="text-muted">
                                Rp <?= number_format($item['price_at_order'], 0, ',', '.') ?> x <?= $item['quantity'] ?> <?= htmlspecialchars($item['satuan_berat']) ?>
                            </small>
                        </div>
                        <span class="fw-bold">Rp <?= number_format($item_total, 0, ',', '.') ?></span>
                    </li>
                <?php endwhile; ?>

                <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0">Total Pembayaran</h5>
                    <h5 class="mb-0 text-danger">Rp <?= number_format($amount, 0, ',', '.') ?></h5>
                </li>
            </ul>
        </div>
    <?php endif; ?>
    <a href="index.php" class="btn btn-primary mt-3">Kembali ke Beranda</a>
</div>

<?php include './includes/footer.php'; ?>