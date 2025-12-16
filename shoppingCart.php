<?php
$pageTitle = 'Shopping Cart';
include './includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$grand_total = 0;

$sql = "SELECT
            c.id AS cart_id,
            c.product_id,
            c.quantity,
            p.nama_produk,
            p.harga,
            p.satuan_berat
        FROM carts c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $subtotal = $row['quantity'] * $row['harga'];
    $grand_total += $subtotal;
    $row['subtotal'] = $subtotal;
    $cart_items[] = $row;
}
?>

<div class="container my-5">
    <h1 class="mb-4">Keranjang Belanja</h1>

    <?php if (isset($_GET['status'])): ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($_GET['status']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info">
            Keranjang belanja Anda masih kosong. Yuk, <a href="./index.php">mulai belanja kopi!</a>
        </div>
    <?php else: ?>

        <form action="checkout.php" method="POST">

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-light fw-bold">Daftar Item</div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($cart_items as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1"><?= htmlspecialchars($item['nama_produk']) ?></h5>
                                <small class="text-muted">Harga: Rp <?= number_format($item['harga'], 0, ',', '.') ?> / <?= htmlspecialchars($item['satuan_berat']) ?></small>
                            </div>
                            <div class="d-flex align-items-center">
                                <form action="update_cart.php" method="POST" class="me-3">
                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1"
                                        class="form-control form-control-sm text-center" style="width: 70px;"
                                        onchange="this.form.submit()">
                                </form>

                                <strong class="text-nowrap me-3">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></strong>

                                <a href="remove_cart.php?cart_id=<?= $item['cart_id'] ?>" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Yakin ingin menghapus item ini?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="col-md-4">
                <div style="z-index:9;" class="card shadow-sm sticky-top" style="top: 20px;">

                    <div class="card-header bg-secondary text-white fw-bold">
                        <i class="bi bi-geo-alt-fill me-2"></i> Alamat Pengiriman
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label fw-bold">Alamat Lengkap:</label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="4"
                                placeholder="Contoh: Nama Jalan, Nomor, RT/RW, Kecamatan, Kota, Kode Pos."
                                required></textarea>
                            <div class="form-text">Alamat ini akan digunakan untuk pengiriman.</div>
                        </div>
                    </div>

                    <div class="card-header bg-primary text-white fw-bold">Ringkasan Pesanan</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal (<?= count($cart_items) ?> Item)</span>
                            <span class="fw-bold">Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 fw-bolder">Total Pembayaran</span>
                            <span class="h5 fw-bolder text-danger">Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">
                            Lanjutkan ke Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>

        </form>

    <?php endif; ?>
</div>

<?php include './includes/footer.php'; ?>