<?php
$pageTitle = "Tambah Produk";
include "includes/adminHeader.php"
?>

<div class="container marketing">
    <h1>Tambah Produk</h1>
    <div class="row">
        <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1">
            <form action="simpanProduk.php" method="post" enctype="multipart/form-data">

                <div class="form-group mb-3">
                    <label for="nama_produk">Nama Produk</label>
                    <input name="nama_produk" id="nama_produk" type="text" class="form-control" placeholder="Masukkan nama produk" required>
                </div>

                <div class="form-group mb-3">
                    <label for="jenis_produk">Jenis Produk</label>
                    <input name="jenis_produk" id="jenis_produk" type="text" class="form-control" placeholder="Contoh: Makanan, Pakaian, Elektronik" required>
                </div>

                <div class="form-group mb-3">
                    <label for="deskripsi">Deskripsi Produk</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" placeholder="Jelaskan detail produk"></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="berat">Berat Produk</label>
                            <input name="berat" id="berat" type="number" step="0.01" class="form-control" placeholder="Contoh: 0.5 (hanya angka)" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="satuan_berat">Satuan Produk</label>
                            <input name="satuan_berat" id="satuan_berat" type="text" class="form-control" placeholder="Contoh: kg, gr, liter" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="harga">Harga Produk (Rp)</label>
                            <input name="harga" id="harga" type="number" class="form-control" placeholder="Hanya angka, contoh: 50000" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="stok">Stok Produk</label>
                            <input name="stok" id="stok" type="number" class="form-control" placeholder="Hanya angka" required>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="gambar_produk">Gambar Produk</label>
                    <input name="gambar_url" id="gambar_url" type="file" class="form-control" required>
                    <small class="form-text text-muted">Pilih file gambar (JPG, PNG, GIF) untuk produk Anda.</small>
                </div>

                <button class="btn btn-primary btn-block" type="submit">Tambah Produk</button>
            </form>
        </div>
    </div>
</div>

<?php
include "includes/adminFooter.php"
?>