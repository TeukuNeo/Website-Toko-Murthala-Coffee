<?php

include 'database/koneksi.php';

if (isset($_POST['submit_register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $fullname = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nomorHp = mysqli_real_escape_string($conn, $_POST['nomor_hp']);
    $password_mentah = $_POST['password'];
    $default_role = 'customer';

    if (empty($fullname) || empty($username) || empty($email) || empty($nomorHp) || empty($password_mentah)) {
        header("Location: registrasi.php?status=kosong");
        exit();
    }

    $check_user = mysqli_query($conn, "SELECT username FROM users WHERE username='$username'");
    if (mysqli_num_rows($check_user) > 0) {
        header("Location: registrasi.php?status=duplikat");
        exit();
    }

    $check_email = mysqli_query($conn, "SELECT email FROM users WHERE email='$email'");
    if (mysqli_num_rows($check_email) > 0) {
        header("Location: registrasi.php?status=duplikat");
        exit();
    }

    $password_hash = password_hash($password_mentah, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (nama_lengkap, username, email, nomor_hp, password, role)
            VALUES ('$fullname', '$username', '$email', '$nomorHp', '$password_hash', '$default_role')";

    if (mysqli_query($conn, $sql)) {
        header("Location: login.php?status=sukses_register");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

} else {
    header("Location: registrasi.php");
    exit();
}
?>