<?php
$pageTitle = 'Admin';
include './includes/adminHeader.php';
?>

<div class="container marketing">
    <div class="container my-6">
        <h1>Kelola Data Produk</h1>
        <a href="tambahProduk.php">Tambah Produk</a>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Nomor</th>
                    <th scope="col">Gambar</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Jenis</th>
                    <th scope="col">Deskripsi</th>
                    <th scope="col">Berat</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Stok</th>
                    <th scope="col">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $produk = mysqli_query($conn, "SELECT * FROM products");
                if ($produk && mysqli_num_rows($produk) > 0) {
                    while ($row = mysqli_fetch_assoc($produk)) {
                ?>
                        <tr>
                            <th scope="row"><?= $no++; ?></th>
                            <td>
                                <img width="50" height="50" src="images/<?= $row['gambar_url']; ?>" alt="">
                            </td>
                            <td><?= $row['nama_produk']; ?></td>
                            <td><?= $row['jenis_produk']; ?></td>
                            <td><?= $row['deskripsi']; ?></td>
                            <td><?= $row['berat']; ?></td>
                            <td><?= $row['satuan_berat']; ?></td>
                            <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                            <td><?= $row['stok']; ?></td>
                            <td class="d-flex g-5">
                                <a class="btn btn-success" href="editProduk.php?id=<?= $row['id']; ?>">Edit</a>
                                <a class="btn btn-danger" onclick="return confirm('Apa kamu mau hapus produk ini?');" href="hapusProduk.php?id=<?= $row['id']; ?>">Hapus</a>
                            </td>
                        </tr>
                <?php }
                } else {
                    echo "<h1>Tidak ada data tersedia</h1>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include './includes/adminFooter.php';
?>