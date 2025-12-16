<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary" style="display: flex; align-items:center; justify-content: center; min-height: 100vh; background-color: #f8f9fa;">
    <div class="container">
        <main class="form-registration m-auto" style="max-width: 400px; width: 100%; padding: 20px; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); border-radius: 0.5rem; background-color: white;">
            <form action="prosesRegistrasi.php" method="POST">
                <div class="text-center mb-4">
                    <h1 class="h3 mb-3 fw-normal">Mohon Registrasi</h1>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" name="nama_lengkap" class="form-control" id="floatingNamaLengkap" placeholder="Nama Lengkap" required>
                    <label for="floatingNamaLengkap">Nama Lengkap</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" name="username" class="form-control" id="floatingUsername" placeholder="Username" required>
                    <label for="floatingUsername">Username</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="floatingEmail" placeholder="Email" required>
                    <label for="floatingEmail">Email</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="tel" name="nomor_hp" class="form-control" id="floatingNomorHp" placeholder="Nomor Ponsel" pattern="[0-9]{10,15}" title="Masukkan nomor ponsel yang valid" required>
                    <label for="floatingNomorHp">Nomor Ponsel</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                    <label for="floatingPassword">Password</label>
                </div>

                <button class="btn btn-primary w-100 py-2 mb-3" name="submit_register" type="submit">
                    Daftar (Sign up)
                </button>

                <a class="btn btn-outline-secondary w-100 py-2 mb-3" href="login.php">
                    Sudah punya akun? Masuk (Sign in)
                </a>

                <p class="mt-4 mb-3 text-body-secondary text-center">© 2025 Murthala, Coffee</p>
            </form>
        </main>
    </div>
</body>
<script src="js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('registrationForm');

        // Ambil elemen input
        const username = document.getElementById('username');
        const email = document.getElementById('email');
        const nomor_hp = document.getElementById('nomor_hp');
        const password = document.getElementById('password');

        form.addEventListener('submit', function (event) {
            // Asumsi validasi dasar HTML (required) sudah dilakukan

            let isValid = true;

            // --- 1. Validasi Username ---
            // Kriteria: Minimal 4 karakter, tanpa spasi
            const usernameValue = username.value.trim();
            if (usernameValue.length < 4 || usernameValue.includes(' ')) {
                displayValidation(username, 'Username minimal 4 karakter dan tidak boleh mengandung spasi.');
                isValid = false;
            } else {
                removeValidation(username);
            }

            // --- 2. Validasi Email ---
            // Kriteria: Menggunakan regex dasar untuk format email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                displayValidation(email, 'Format email tidak valid.', 'emailFeedback');
                isValid = false;
            } else {
                removeValidation(email);
            }

            // --- 3. Validasi Nomor HP ---
            // Kriteria: Hanya angka, panjang 10 sampai 15 (sesuai pattern di HTML)
            const phoneRegex = /^[0-9]{10,15}$/;
            if (!phoneRegex.test(nomor_hp.value)) {
                 displayValidation(nomor_hp, 'Nomor ponsel harus 10-15 digit angka.', 'phoneFeedback');
                 isValid = false;
            } else {
                 removeValidation(nomor_hp);
            }


            // --- 4. Validasi Password ---
            // Kriteria: Minimal 8 karakter
            if (password.value.length < 8) {
                displayValidation(password, 'Password minimal 8 karakter.', 'passwordFeedback');
                isValid = false;
            } else {
                removeValidation(password);
            }

            // Jika ada validasi yang gagal, cegah form dikirim
            if (!isValid) {
                event.preventDefault();
            }
        });

        // --- Fungsi Bantuan untuk Tampilan Bootstrap ---

        function displayValidation(element, message, feedbackId) {
            element.classList.add('is-invalid');
            element.classList.remove('is-valid');
            if (feedbackId) {
                document.getElementById(feedbackId).textContent = message;
            }
        }

        function removeValidation(element) {
            element.classList.remove('is-invalid');
            element.classList.add('is-valid');
        }
    });
</script>

</html>