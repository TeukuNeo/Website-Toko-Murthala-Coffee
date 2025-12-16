<?php

include 'database/koneksi.php';

function upload(){
    if(!isset($_FILES['gambar_url'])){
      return false;
    }

    $filename = $_FILES['gambar_url']['name'];
    $filesize = $_FILES['gambar_url']['size'];
    $filetmp = $_FILES['gambar_url']['tmp_name'];

    $ekstensiGambarValid = ['jpg','jpeg','png'];
    $ekstensiGambar = explode('.',$filename);
    $ekstensiGambar = strtolower(end($ekstensiGambar));

    if(!in_array($ekstensiGambar,$ekstensiGambarValid)) {
      echo "
        <script>
        alert('Gambar tidak valid');
        </script>
      ";
      return false;
    }

    if($filesize > 1000000) {
        echo "
        <script>
          alert('Ukuran gambar terlalu besar');
        </script>
        ";
        return false;
    }

    $newFileName = uniqid();
    $newFileName .= '.';
    $newFileName .= $ekstensiGambar;

    move_uploaded_file($filetmp , 'images/' . $newFileName);
    return $newFileName;
}

$namaProduk = $_POST['nama_produk'];
$deskripsi = $_POST['deskripsi'];
$jenisProduk = $_POST['jenis_produk'];
$beratProduk = $_POST['berat'];
$satuanBerat = $_POST['satuan_berat'];
$hargaProduk = $_POST['harga'];
$stokBarang = $_POST['stok'];
$gambarProduk = upload();
$sqlQuery = "INSERT INTO products (gambar_url, nama_produk, deskripsi, jenis_produk, berat, satuan_berat, harga, stok)
             VALUES('$gambarProduk','$namaProduk','$deskripsi','$jenisProduk','$beratProduk','$satuanBerat','$hargaProduk','$stokBarang')";
mysqli_query($conn, $sqlQuery);
header('location:admin.php');

?>