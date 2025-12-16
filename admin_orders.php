<?php
// admin_orders.php - Dashboard Admin: Daftar Pesanan
$pageTitle = 'Kelola Pesanan';

// 🛑 Asumsi: header_admin.php sudah memuat koneksi DB ($conn)
// dan mengecek otorisasi role 'admin'
include 'includes/adminHeader.php';

// --- 1. Query Pengambilan Data ---
// Mengambil data pesanan dan menggabungkannya dengan nama customer.
$sql = "SELECT
            o.id,
            o.midtrans_order_id,
            o.total_amount,
            o.status,
            o.order_date,
            u.nama_lengkap,
            u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$orders = $stmt->get_result();
?>

<div class="container my-5">
    <h1><i class="bi bi-cart-check-fill me-2"></i> Kelola Pesanan Customer</h1>
    <p class="lead">Gunakan tombol 'Kelola Status' untuk mengubah status dari Pending ke Paid/Processing.</p>

    <?php
    // Menampilkan pesan sukses setelah update status (jika ada)
    if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($orders->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>#ID DB</th>
                        <th>No. Midtrans</th>
                        <th>Customer</th>
                        <th>Email Customer</th>
                        <th>Tanggal Pesan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                        <?php
                            // Menentukan Badge Class berdasarkan status
                            $status_class = match ($order['status']) {
                                'settlement', 'paid' => 'bg-success',
                                'pending' => 'bg-warning text-dark',
                                'processing', 'shipped' => 'bg-primary',
                                'delivered' => 'bg-info',
                                default => 'bg-danger',
                            };
                            $db_order_id = $order['id']; // ID INT dari tabel orders
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($db_order_id) ?></td>
                            <td><?= htmlspecialchars($order['midtrans_order_id']) ?></td>
                            <td><?= htmlspecialchars($order['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($order['email']) ?></td>
                            <td><?= date('d M Y H:i', strtotime($order['order_date'])) ?></td>
                            <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge <?= $status_class ?> p-2">
                                    <?= strtoupper($order['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="admin_order_edit.php?id=<?= $db_order_id ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    Kelola Status
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4">
            Belum ada pesanan yang masuk.
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/adminFooter.php'; ?>