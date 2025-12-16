<?php
// order_history.php
$pageTitle = 'Riwayat Pesanan';
include './includes/header.php'; // Memuat sesi, koneksi $conn, dan cek login

// Pastikan hanya customer yang login yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$orders = [];

// Query untuk mengambil semua pesanan customer, diurutkan dari yang terbaru
$sql = "SELECT id, order_date, total_amount, status
        FROM orders
        WHERE user_id = ?
        ORDER BY order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
?>

<div class="container my-5">
    <h1><i class="bi bi-box-seam me-2"></i> Riwayat Pesanan Anda</h1>
    <p class="lead text-muted">Daftar pesanan yang pernah Anda buat di Toko Murthala Coffee</p>

    <?php if (empty($orders)): ?>
        <div class="alert alert-warning mt-4" role="alert">
            Anda belum memiliki riwayat pesanan. Mari <a href="index.php" class="alert-link">mulai belanja</a>!
        </div>
    <?php else: ?>
        <div class="list-group mt-4">
            <?php foreach ($orders as $order): ?>
                <?php
                    // Tentukan warna status berdasarkan status pesanan
                    $status_text = strtoupper($order['status']);
                    $status_class = match ($order['status']) {
                        'paid', 'settlement', 'delivered' => 'bg-success text-white',
                        'shipped', 'processing' => 'bg-info text-white',
                        'pending' => 'bg-warning text-dark',
                        'cancelled', 'expire', 'failed' => 'bg-danger text-white',
                        default => 'bg-secondary text-white',
                    };
                ?>

                <a href="order_status.php?order_id=<?= htmlspecialchars($order['id']) ?>"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">

                    <div class="col-md-7">
                        <h5 class="mb-1">Pesanan #<?= htmlspecialchars($order['id']) ?></h5>
                        <small class="text-muted">
                            Tanggal: <?= date('d M Y H:i', strtotime($order['order_date'])) ?>
                        </small>
                    </div>

                    <div class="col-md-3 text-end">
                        <span class="d-block fw-bold">Total: Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                    </div>

                    <div class="col-md-2 text-end">
                        <span class="badge <?= $status_class ?> p-2">
                            <?= $status_text ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include './includes/footer.php'; ?>