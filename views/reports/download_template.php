<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') die("Akses ditolak");

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=template_laporan.csv');

$output = fopen('php://output', 'w');

// 1. Tambahkan BOM agar karakter terbaca dengan benar
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// 2. PAKSA EXCEL MENGGUNAKAN PEMISAH KOMA
fputs($output, "sep=,\n"); 

// 3. Header Kolom
fputcsv($output, array('Tanggal (YYYY-MM-DD HH:MM)', 'Unit Kerja', 'Kategori', 'Deskripsi Masalah', 'Tindakan', 'Petugas IT', 'Status'), ',');

// 4. Contoh Baris (Gunakan format YYYY-MM-DD)
fputcsv($output, array('2025-12-19 08:30', 'IGD', 'Hardware', 'Printer mati total', 'Ganti kabel power', 'Admin-IT', 'Selesai'), ',');

fclose($output);
exit;