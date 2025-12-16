<?php

$pageTitle = 'Beranda';
include 'includes/header.php';

?>

<section id="hero-section">
    <div id="hero-content">
            <h1 id="hero-title">Aroma Khas Benermeriah, Menemani Setiap Hari Anda</h1>
            <p id="hero-subtitle">Nikmati biji kopi Arabica murni yang kini terjual dimedan. Kualitas premium, cita rasa autentik, dan aroma yang tak terlupakan.</p>
            <a href="about.php" class="cta-button">Lihat Sejarah Kami</a>
        </div>
</section>
<section class="home-intro-section">
        <div class="container">
            <div class="intro-grid">
                <div class="intro-text">
                    <h2>Tentang Murthala Coffee</h2>
                    <p>Kami adalah brand lokal yang berkomitmen menyajikan keaslian kopi Arabica Gayo. Didirikan dengan cinta dan dedikasi, kami mengolah setiap biji kopi secara teliti, dari proses penanaman hingga penyajian, untuk memastikan setiap cangkir kopi Anda adalah pengalaman yang istimewa.</p>
                    <a href="about.php" class="learn-more-link">Pelajari Lebih Lanjut →</a>
                </div>
                <div class="intro-image">
                    <img src="images/about-home.jpg" alt="Kisah Kami" class="responsive-image">
                </div>
            </div>
        </div>
    </section>
<div class="container marketing" style="padding-bottom: 25px; padding-top: 25px;">
    <h1>Produk Kami</h1>
    <div class="d-flex justify-content-center">

        <div class="col-md-9">
            <div class="row g-4">
                <?php
                $result_produk = mysqli_query($conn, "SELECT * FROM products");

                if ($result_produk && mysqli_num_rows($result_produk) > 0) {
                    while ($row = mysqli_fetch_assoc($result_produk)) {
                ?>
                        <div class="col-12 col-md-6 col-lg-3">

                            <div class="card h-100">
                                <img height="180" src="images/<?= strtolower($row['gambar_url']) ?>" class="card-img-top" alt="Logo Operator">

                                <div class="card-body bg-light">
                                    <h5 class="card-title text-truncate" title="<?= $row['nama_produk'] ?>">
                                        <?= $row['nama_produk'] ?>
                                    </h5>
                                    <p class="card-text text-muted small mb-0"><?= $row['jenis_produk'] ?></p>

                                    <h4 class="card-text text-danger">
                                        Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                                    </h4>

                                    <p class="card-text small">Stok: <?= $row['stok'] ?></p>

                                    <a href="viewProduk.php?id=<?= $row['id'] ?>" style="background-color: #ff5722; border-color: #ff5722;" class="btn btn-primary w-100">
                                        Beli Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo '<div class="col-12">
                         <p class="alert alert-info">Tidak ada produk pulsa yang ditemukan saat ini.</p>
                    </div>';
                }
                ?>

            </div>
        </div>
    </div>
</div>


<?php

include 'includes/footer.php';

?>