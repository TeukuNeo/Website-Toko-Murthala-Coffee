<?php

include "database/koneksi.php";

$id = $_GET['id'];

$sqlQuery = "DELETE FROM products WHERE id = $id";
mysqli_query($conn, $sqlQuery);
header("location:admin.php");
