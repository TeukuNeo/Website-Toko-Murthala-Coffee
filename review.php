<?php

$pageTitle = 'Review Pelanggan';
include 'includes/header.php';

?>

<section class="contact-hero-section">
        <div class="container">
            <h1>Review Pelanggan</h1>
            <p>Kami senang bisa melayani anda, Silahkan berikan ulasan anda mengenai produk kami!</p>
        </div>
    </section>
<div class="container my-4">
    <h1>Review</h1>
    <div class="row">
        <?php
        // Asumsi $conn sudah terdefinisi
        $review = mysqli_query($conn, "SELECT * FROM review ORDER BY tanggal_komentar DESC");
        foreach ($review as $row) {
        ?>
            <div class="col-lg-4 mb-4">
                <div class="card text-center h-100 shadow-sm">
                    <div class="card-body">

                        <div class="mb-3">
                            <?php if (!empty($row['gambar_url'])) { ?>
                                <img src="images/<?= $row['gambar_url']; ?>" alt="<?= $row['nama']; ?>" class="bd-placeholder-img rounded-circle" width="140" height="140" style="object-fit: cover;">
                            <?php } else { ?>
                                <svg class="bd-placeholder-img rounded-circle" width="140" height="140" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder" preserveAspectRatio="xMidYMid slice" focusable="false">
                                    <title><?= $row['nama']; ?></title>
                                    <rect width="100%" height="100%" fill="#777"></rect>
                                    <text x="50%" y="50%" fill="#fff" dy=".3em" text-anchor="middle">Foto</text>
                                </svg>
                            <?php } ?>
                        </div>

                        <h5 class="card-title fw-bold mt-2 mb-3"><?= $row['nama']; ?></h5>

                        <p class="card-text">"<?= $row['komentar']; ?>"</p>

                    </div>

                    <div class="card-footer text-muted small">
                        <?= date('d M Y, H:i', strtotime($row['tanggal_komentar'])); ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php

include 'includes/footer.php';

?>