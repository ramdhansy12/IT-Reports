<?php
date_default_timezone_set('Asia/Jakarta');
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_it_rs";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>