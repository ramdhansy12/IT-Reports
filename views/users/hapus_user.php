<?php
session_start();
// Proteksi Admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'Admin') {
    exit;
}

include '../../config/database.php'; // Keluar 2 tingkat folder

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Ambil data untuk cek username (jangan hapus admin utama)
    $cek = mysqli_query($conn, "SELECT username FROM users WHERE id = '$id'");
    $user = mysqli_fetch_assoc($cek);

    if ($user['username'] !== 'admin') {
        $delete = mysqli_query($conn, "DELETE FROM users WHERE id = '$id'");
        if ($delete) {
            echo "<script>alert('User berhasil dihapus'); window.location='../../index.php?page=users';</script>";
        } else {
            echo "<script>alert('Gagal menghapus user'); window.location='../../index.php?page=users';</script>";
        }
    } else {
        echo "<script>alert('User admin utama tidak boleh dihapus!'); window.location='../../index.php?page=users';</script>";
    }
}
?>