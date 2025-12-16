<?php
session_start();
include './database/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $new_quantity = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'];

    if ($new_quantity < 1) {
        // Jika kuantitas 0 atau kurang, arahkan untuk menghapus
        header("Location: remove_cart.php?cart_id=" . $cart_id);
        exit();
    }

    $sql = "UPDATE carts SET quantity = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $new_quantity, $cart_id, $user_id);
    $stmt->execute();

    header("Location: shoppingCart.php?status=Jumlah%20berhasil%20diperbarui.");
    exit();
}
header("Location: shoppingCart.php");
exit();
?>