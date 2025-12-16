<?php
session_start();
include './database/koneksi.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {

    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity < 1) {
        header("Location: viewProduct.php?id=" . $product_id . "&error=Kuantitas tidak valid");
        exit();
    }

    $check_sql = "SELECT id, quantity FROM carts WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cart_item = $result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + $quantity;

        $update_sql = "UPDATE carts SET quantity = ? WHERE id = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("ii", $new_quantity, $cart_item['id']);
        $stmt_update->execute();

        $message = "Jumlah produk berhasil diperbarui.";
    } else {
        $insert_sql = "INSERT INTO carts (user_id, product_id, quantity, added_at) VALUES (?, ?, ?, NOW())";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt_insert->execute();

        $message = "Produk berhasil ditambahkan ke keranjang.";
    }

    header("Location: shoppingCart.php?status=" . urlencode($message));
    exit();
}
header("Location: index.php");
exit();
