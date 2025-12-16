<?php
$pageTitle = "Berlangganan";
include 'includes/header.php';

$id = $_GET['id'];
$sqlQuery = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
$rows = mysqli_fetch_array($sqlQuery);
$item_in_cart = 0;

if ($isLoggedIn && $_SESSION['role'] === 'customer') {
    $user_id = $_SESSION['user_id'];
    $product_id = $rows['id'];

    $cart_sql = "SELECT quantity FROM carts WHERE user_id = ? AND product_id = ?";

    if ($stmt = $conn->prepare($cart_sql)) {
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $cart_row = $result->fetch_assoc();
            $item_in_cart = $cart_row['quantity'];
        }
        $stmt->close();
    }
}
?>

<div class="container marketing">
    <div class="container my-5">
        <h1 class="mb-4">Beli Produk</h1>
        <div class="row d-flex justify-content-center">
            <div class="col-lg-4 p-0 d-flex">
                <div class="bg-dark rounded-start shadow-sm h-100" style="min-height: 350px; overflow: hidden; width:100%;">
                    <img class="h-100 w-100" src="./images/<?php echo $rows['gambar_url']; ?>"
                        style="object-fit: cover;"
                        alt="Gambar Paket Data">
                </div>
            </div>

            <div class="col-lg-4 p-0 d-flex">
                <div class="bg-white p-4 rounded-end shadow-sm border border-start-0 h-100">
                    <h3 class="h5 fw-bold mb-3">Rincian Produk</h3>

                    <div class="row border-bottom py-2">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-globe me-2 text-muted"></i>
                                <span class="fw-medium">Jenis</span>
                            </div>
                            <div class="small text-muted">
                                <?php echo $rows['jenis_produk']; ?>
                            </div>
                        </div>

                        <div class="col-sm-6 text-end">
                            <div class="mb-1">
                                <span class="fw-bold">Deskripsi</span>
                            </div>
                            <div class="small text-muted">
                                <?php echo $rows['deskripsi']; ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom mb-3">
                        <span class="fw-medium">Stok</span>
                        <span class="fw-bold"><?php echo $rows['stok']; ?></span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom mb-3">
                        <span class="fw-medium">Berat</span>
                        <span class="fw-bold"><?php echo $rows['berat']; ?> | <?php echo $rows['satuan_berat'] ?></span>
                    </div>

                    <div class="mt-4">
                        <h4 class="text-danger fw-bolder mb-0">Rp <?= number_format($rows['harga'], 0, ',', '.') ?></h4>
                        <p class="small text-muted mb-4"><?php echo $rows['nama_produk']; ?></p>
                    </div>

                    <div class="mt-4">
                        <?php if ($isLoggedIn && $_SESSION['role'] === 'customer'): ?>
                            <form action="add_to_cart.php" method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="product_id" value="<?php echo $rows['id']; ?>">

                                <label for="quantityInput" class="form-label fw-medium">Jumlah Beli:</label>
                                <div class="input-group mb-3">
                                    <input type="number" name="quantity" id="quantityInput" class="form-control text-center" value="1" min="1" max="<?php echo $rows['stok']; ?>" required>
                                </div>

                                <?php if ($item_in_cart > 0): ?>
                                    <div class="alert alert-success py-2 px-3 small d-flex justify-content-between align-items-center">
                                        <span>🛒 Sudah ada <?php echo $item_in_cart; ?> item di keranjang Anda.</span>
                                        <a href="shoppingCart.php" class="btn btn-sm btn-success">Lihat</a>
                                    </div>
                                <?php endif; ?>
                                <?php if ($rows['stok'] > 0): ?>
                                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                                        <i class="bi bi-cart-plus"></i> Tambah <?php echo ($item_in_cart > 0) ? 'Lagi' : 'ke Keranjang'; ?>
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-secondary btn-lg w-100 fw-bold" disabled>Stok Habis</button>
                                <?php endif; ?>
                            </form>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-danger btn-lg w-100 fw-bold" style="background-color: #ff5722; border-color: #ff5722;">Login untuk Beli</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
include './includes/footer.php';
?>