<?php
session_start();
include './database/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['cart_id'])) {
    $cart_id = intval($_GET['cart_id']);
    $user_id = $_SESSION['user_id'];

    $sql = "DELETE FROM carts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();

    header("Location: shoppingCart.php?status=Item%20berhasil%20dihapus%20dari%20keranjang.");
    exit();
}
header("Location: shoppingCart.php");
exit();
?>