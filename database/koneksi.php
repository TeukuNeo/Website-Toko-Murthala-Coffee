<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'murthala_coffee';

$conn = mysqli_connect($host, $user, $pass, $dbName);

if(!$conn) {
    die('Database tidak terkonfigurasi!');
}

?>