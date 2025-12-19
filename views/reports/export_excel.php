<?php
session_start();
include '../../config/database.php';

// Pastikan hanya user yang login bisa export
if (!isset($_SESSION['login'])) {
    exit;
}

// Memberitahu browser bahwa ini adalah file Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Maintenance_IT_" . date('Y-m-d') . ".xls");
?>

<center>
    <h2>LAPORAN MAINTENANCE IT</h2>
</center>

<table border="1">
    <thead>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <th>No</th>
            <th>Tanggal/Waktu</th>
            <th>Petugas IT</th>
            <th>Unit Kerja</th>
            <th>Kategori</th>
            <th>Deskripsi Masalah</th>
            <th>Tindakan Perbaikan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        // Ambil semua data
        $sql = mysqli_query($conn, "SELECT * FROM laporan_it ORDER BY tanggal DESC");
        while ($data = mysqli_fetch_array($sql)) {
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= date('d/m/Y H:i', strtotime($data['tanggal'])) ?></td>
            <td><?= $data['petugas_it']; ?></td>
            <td><?= $data['unit_kerja']; ?></td>
            <td><?= $data['kategori']; ?></td>
            <td><?= $data['deskripsi_masalah']; ?></td>
            <td><?= $data['tindakan']; ?></td>
            <td><?= $data['status']; ?></td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>