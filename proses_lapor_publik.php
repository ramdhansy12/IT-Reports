<?php
include 'config/database.php';

if (isset($_POST['kirim'])) {
    $tanggal = date('Y-m-d H:i:s');
    $unit = mysqli_real_escape_string($conn, $_POST['unit_kerja']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $masalah = mysqli_real_escape_string($conn, $_POST['deskripsi_masalah']);
    $status = "Pending";

    // 1. Simpan Gambar
    $gambar = "";
    if (!empty($_FILES['gambar']['name'])) {
        $nama_file = time() . "_" . $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], "uploads/" . $nama_file);
        $gambar = $nama_file;
    }

    // 2. Query Simpan ke Database (Langkah cegah error PHP 8 di screenshot Anda)
    $query = "INSERT INTO laporan_it (tanggal, unit_kerja, kategori, deskripsi_masalah, status, gambar, petugas_it, tindakan) 
              VALUES ('$tanggal', '$unit', '$kategori', '$masalah', '$status', '$gambar', '-', '-')";

    if (mysqli_query(mysql: $conn, $query)) {
        
        // --- FITUR NOTIFIKASI WHATSAPP KE GRUP ---
        $nomor_it = "120363353265954412@g.us"; // GANTI DENGAN ID GRUP ANDA
        $api_token = "uXRfAyFXKUcdFEWBsRQL"; 

        $pesan_wa = "*🚨 LAPORAN PROBLEM UNIT RS. PERMATA KELUARGA LIPPO*\n\n" .
                    "📍 *Unit:* $unit\n" .
                    "📁 *Kategori:* $kategori\n" .
                    "📝 *Masalah:* $masalah\n" .
                    "⏰ *Waktu:* $tanggal\n\n" .
                    "Mohon tim IT yang bertugas segera merespon.";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $nomor_it,
                'message' => $pesan_wa,
                // countryCode tidak wajib untuk ID Grup, tapi aman jika tetap ada
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $api_token"
            ),
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        
        // CURLOPT_SSL_VERIFYPEER => false, // Penting jika running di localhost/Laragon
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        // Jika ingin melihat alasan gagal, hilangkan komentar di bawah ini saat tes:
        //  if($err) { die($err); } else { echo $response; die(); }

        echo "<script>alert('Laporan berhasil terkirim ke IT!'); window.location='lapor_publik.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}