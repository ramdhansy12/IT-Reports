<?php
session_start();
if (!isset($_SESSION['login'])) {
    exit;
}

// PERBAIKAN: Keluar 2 tingkat folder (../../) untuk mencapai folder utama
include '../../config/database.php'; 

if (isset($_GET['id'])) {
    // Variabel $conn sekarang sudah dikenali karena include sudah benar
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // 1. Ambil nama gambar sebelum dihapus
    $query_gambar = mysqli_query($conn, "SELECT gambar FROM laporan_it WHERE id = '$id'");
    $data_gambar = mysqli_fetch_assoc($query_gambar);
    
    if ($data_gambar) {
        $nama_gambar = $data_gambar['gambar'];

        // 2. Hapus data dari database
        $delete = mysqli_query($conn, "DELETE FROM laporan_it WHERE id = '$id'");

        if ($delete) {
            // PERBAIKAN: Keluar 2 tingkat folder untuk mencari folder uploads
            if (!empty($nama_gambar) && file_exists('../../uploads/' . $nama_gambar)) {
                unlink('../../uploads/' . $nama_gambar);
            }
            // PERBAIKAN: Keluar 2 tingkat folder untuk kembali ke index.php
            echo "<script>alert('Laporan berhasil dihapus'); window.location='../../index.php?page=laporan';</script>";
        } else {
            echo "<script>alert('Gagal menghapus data.'); window.location='../../index.php?page=laporan';</script>";
        }
    } else {
        echo "<script>alert('Data tidak ditemukan.'); window.location='../../index.php?page=laporan';</script>";
    }
} else {
    echo "<script>window.location='../../index.php?page=laporan';</script>";
}
?>