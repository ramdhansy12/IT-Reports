<?php
session_start();
if (!isset($_SESSION['login'])) {
    exit;
}
include 'config/database.php';

// Mengambil parameter filter agar data yang di-export sesuai dengan yang tampil di layar
$where_clause = " WHERE 1=1 ";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clause .= " AND (deskripsi_masalah LIKE '%$search%' OR petugas_it LIKE '%$search%' OR unit_kerja LIKE '%$search%')";
}
if (isset($_GET['unit_kerja']) && !empty($_GET['unit_kerja'])) {
    $unit = mysqli_real_escape_string($conn, $_GET['unit_kerja']);
    $where_clause .= " AND unit_kerja = '$unit'";
}
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $where_clause .= " AND status = '$status'";
}

// Perintah header untuk mendownload file excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_IT_RS_".date('d-m-Y').".xls");

?>

<center>
    <h3>LAPORAN MAINTENANCE IT RUMAH SAKIT</h3>
</center>

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
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
        $sql = mysqli_query($conn, "SELECT * FROM laporan_it $where_clause ORDER BY tanggal DESC");
        while($data = mysqli_fetch_array($sql)){
            echo "<tr>
                <td>$no</td>
                <td>{$data['tanggal']}</td>
                <td>{$data['petugas_it']}</td>
                <td>{$data['unit_kerja']}</td>
                <td>{$data['kategori']}</td>
                <td>{$data['deskripsi_masalah']}</td>
                <td>{$data['tindakan']}</td>
                <td>{$data['status']}</td>
            </tr>";
            $no++;
        }
        ?>
    </tbody>
</table>