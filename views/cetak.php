<?php
include 'config/database.php';
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
// Opsional: Filter berdasarkan bulan/tahun jika diinginkan
?>
<!DOCTYPE html>
<html>

<head>
    <title>Cetak Laporan IT</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 40px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }

    .header {
        text-align: center;
        border-bottom: 3px double #000;
        padding-bottom: 10px;
    }
    </style>
</head>

<body onload="window.print()">
    <div class="header">
        <h2>LAPORAN PEMELIHARAAN TI (MAINTENANCE)</h2>
        <h3>RS. Permata Keluarga Lippo Cikarang
            <p>Jl. MH. Thamrin No.129, Cibatu, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17550
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Unit</th>
                <th>Masalah</th>
                <th>Tindakan</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $res = mysqli_query($conn, "SELECT * FROM laporan_it ORDER BY tanggal DESC");
            while($row = mysqli_fetch_assoc($res)) {
                echo "<tr>
                    <td>{$no}</td>
                    <td>".date('d/m/Y', strtotime($row['tanggal']))."</td>
                    <td>{$row['unit_kerja']}</td>
                    <td>{$row['deskripsi_masalah']}</td>
                    <td>{$row['tindakan']}</td>
                    <td>{$row['petugas_it']}</td>
                </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>

    <div style="margin-top: 50px; float: right; text-align: center;">
        <p>Dicetak pada: <?php echo date('d/m/Y'); ?></p>
        <br><br><br>
        <p><b>( Kepala Instalasi IT )</b></p>
    </div>
</body>

</html>