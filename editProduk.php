<?php
$pageTitle = "Edit Produk";
include "includes/adminHeader.php";

$id = $_GET['id'];
$sqlQuery = "SELECT * FROM products WHERE id = $id";
$produk = mysqli_query($conn, $sqlQuery);
$row = mysqli_fetch_array($produk);

?>

<div class="container marketing">
    <h1>Edit Produk</h1>
    <div class="row">
        <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1">
            <form action="ubahProduk.php" method="post" enctype="multipart/form-data">

                <div class="form-group mb-3">
                    <label for="nama_produk">Nama Produk</label>
                    <input name="id" id="id" type="hidden" class="form-control" placeholder="Masukkan nama produk" value="<?php echo $row['id']; ?>" required>
                    <input name="nama_produk" id="nama_produk" type="text" class="form-control" placeholder="Masukkan nama produk" value="<?php echo $row['nama_produk']; ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="jenis_produk">Jenis Produk</label>
                    <input name="jenis_produk" id="jenis_produk" type="text" class="form-control" placeholder="Contoh: Makanan, Pakaian, Elektronik" value="<?php echo $row['jenis_produk']; ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="deskripsi">Deskripsi Produk</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" placeholder="Jelaskan detail produk"><?php echo $row['deskripsi']; ?></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="berat">Berat Produk</label>
                            <input name="berat" id="berat" type="number" step="0.01" class="form-control" placeholder="Contoh: 0.5 (hanya angka)" value="<?php echo $row['berat']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="satuan_berat">Satuan Produk</label>
                            <input name="satuan_berat" id="satuan_berat" type="text" class="form-control" placeholder="Contoh: kg, gr, liter" value="<?php echo $row['satuan_berat']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="harga">Harga Produk (Rp)</label>
                            <input name="harga" id="harga" type="number" class="form-control" placeholder="Hanya angka, contoh: 50000" value="<?php echo $row['harga']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="stok">Stok Produk</label>
                            <input name="stok" id="stok" type="number" class="form-control" placeholder="Hanya angka" value="<?php echo $row['stok']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="gambar_url" class="form-label mt-3">Ganti Gambar Produk (Upload Baru)</label>
                    <img width="100" height="100" src="images/<?php echo $row['gambar_url']; ?>" alt="">
                    <input name="gambar_url" id="gambar_url" type="file" class="form-control">
                    <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti gambar. Pilih file gambar (JPG, PNG, GIF).</small>
                </div>

                <button class="btn btn-primary btn-block" type="submit">Ubah Produk</button>
            </form>
        </div>
    </div>
</div>

<?php
include "includes/adminFooter.php"
?>