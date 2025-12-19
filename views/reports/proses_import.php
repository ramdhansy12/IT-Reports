<?php
session_start();
include '../../config/database.php';

if (isset($_POST['import'])) {
    $file = $_FILES['file_csv']['tmp_name'];
    $petugas = $_SESSION['nama']; // Petugas yang mengimport

    if (($handle = fopen($file, "r")) !== FALSE) {
        // Lewati baris pertama jika file CSV Anda memiliki header/judul kolom
        fgetcsv($handle, 1000, ","); 

        $berhasil = 0;
        $gagal = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Mapping kolom CSV (Sesuaikan dengan urutan di Excel Anda)
            $tanggal   = mysqli_real_escape_string($conn, $data[0]);
            $unit      = mysqli_real_escape_string($conn, $data[1]);
            $kategori  = mysqli_real_escape_string($conn, $data[2]);
            $masalah   = mysqli_real_escape_string($conn, $data[3]);
            $tindakan  = mysqli_real_escape_string($conn, $data[4]);
            $status    = mysqli_real_escape_string($conn, $data[5]);

            $query = "INSERT INTO laporan_it (tanggal, petugas_it, unit_kerja, kategori, deskripsi_masalah, tindakan, status) 
                      VALUES ('$tanggal', '$petugas', '$unit', '$kategori', '$masalah', '$tindakan', '$status')";
            
            if (mysqli_query($conn, $query)) {
                $berhasil++;
            } else {
                $gagal++;
            }
        }
        fclose($handle);
        echo "<script>alert('Import Selesai! Berhasil: $berhasil, Gagal: $gagal'); window.location='../../index.php?page=laporan';</script>";
    } else {
        echo "<script>alert('Gagal membuka file.'); window.location='../../index.php?page=laporan';</script>";
    }
}
?>