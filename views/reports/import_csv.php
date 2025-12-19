<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php?page=laporan&status=error&msg=AksesDitolak");
    exit;
}

if (isset($_POST['import'])) {
    $filename = $_FILES['file']['tmp_name'];

    if ($_FILES['file']['size'] > 0) {
        $file = fopen($filename, "r");
        
        // 1. Ambil baris pertama untuk deteksi delimiter (koma atau titik koma)
        $firstLine = fgets($file);
        $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';
        
        // Kembalikan ke awal file
        rewind($file);

        // 2. Lewati instruksi 'sep=' jika ada
        $lineCheck = fgets($file);
        if (strpos($lineCheck, 'sep=') === false) {
            rewind($file);
        }

        // 3. Lewati Header
        fgetcsv($file, 10000, $delimiter);

        $success_count = 0;
        $error_count = 0;

        while (($column = fgetcsv($file, 10000, $delimiter)) !== FALSE) {
            // Jika kolom pertama (tanggal) kosong, lewati baris ini
            if (empty($column[0])) continue;

            // 4. Bersihkan karakter sampah/BOM dari setiap kolom
            $clean_data = [];
            foreach ($column as $val) {
                // Hapus karakter non-printable dan trim spasi
                $val = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $val);
                $clean_data[] = trim($val);
            }

            // Pastikan minimal ada data tanggal dan unit
            if (count($clean_data) < 2) continue;

            // Mapping Data
            $raw_date  = $clean_data[0];
            $unit      = mysqli_real_escape_string($conn, $clean_data[1] ?? '');
            $kategori  = mysqli_real_escape_string($conn, $clean_data[2] ?? '');
            $masalah   = mysqli_real_escape_string($conn, $clean_data[3] ?? '');
            $tindakan  = mysqli_real_escape_string($conn, $clean_data[4] ?? '');
            $petugas   = mysqli_real_escape_string($conn, $clean_data[5] ?? '');
            $status    = mysqli_real_escape_string($conn, $clean_data[6] ?? '');

            // 5. Konversi Tanggal yang lebih kuat
            $timestamp = strtotime(str_replace('/', '-', $raw_date));
            $tanggal = ($timestamp) ? date('Y-m-d H:i:s', $timestamp) : date('Y-m-d H:i:s');

            $sql = "INSERT INTO laporan_it (tanggal, unit_kerja, kategori, deskripsi_masalah, tindakan, petugas_it, status) 
                    VALUES ('$tanggal', '$unit', '$kategori', '$masalah', '$tindakan', '$petugas', '$status')";
            
            if (mysqli_query($conn, $sql)) {
                $success_count++;
            } else {
                $error_count++;
            }
        }
        fclose($file);

        header("Location: ../../index.php?page=laporan&status=success&msg=Import Selesai. Berhasil: $success_count, Gagal: $error_count");
    } else {
        header("Location: ../../index.php?page=laporan&status=error&msg=FileKosong");
    }
}