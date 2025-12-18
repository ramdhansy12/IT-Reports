<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'Admin') { exit; }
include 'config/database.php';

$id = $_GET['id'];
// Mencegah admin menghapus dirinya sendiri (asumsi id 1 adalah admin utama)
if ($id != 1) {
    mysqli_query($conn, "DELETE FROM users WHERE id = '$id'");
}
header("Location: users.php");
?>