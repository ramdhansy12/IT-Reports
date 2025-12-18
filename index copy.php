<?php
session_start();

// Cek apakah user sudah login, jika belum arahkan ke login.php
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/database.php';

// Query untuk menghitung jumlah laporan per kategori
$query_chart = mysqli_query($conn, "SELECT kategori, COUNT(*) as total FROM laporan_it GROUP BY kategori");

$labels = [];
$data_values = [];

while ($row = mysqli_fetch_assoc($query_chart)) {
    $labels[] = $row['kategori'];
    $data_values[] = $row['total'];
}

// Ambil filter dari URL (default adalah 'bulan_ini')
$view = isset($_GET['view']) ? $_GET['view'] : 'bulan_ini';
$labels = [];
$data_values = [];
$title = "";

if ($view == 'hari_ini') {
    $title = "Statistik Per Jam (Hari Ini)";
    $query = "SELECT HOUR(tanggal) as label, COUNT(*) as total 
              FROM laporan_it WHERE DATE(tanggal) = CURDATE() 
              GROUP BY HOUR(tanggal)";
} elseif ($view == 'minggu_ini') {
    $title = "Statistik Per Hari (Minggu Ini)";
    $query = "SELECT DAYNAME(tanggal) as label, COUNT(*) as total 
              FROM laporan_it WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
              GROUP BY DATE(tanggal)";
} else { // Default: Bulan Ini
    $title = "Statistik Per Tanggal (Bulan Ini)";
    $query = "SELECT DATE_FORMAT(tanggal, '%d %b') as label, COUNT(*) as total 
              FROM laporan_it WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()) 
              GROUP BY DATE(tanggal)";
}

$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['label'];
    $data_values[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard IT Support - RS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-light">


    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-hospital-user"></i> IT Report RS</a>
            <div class="navbar-nav me-auto">
                <a class="nav-link active" href="index.php">Dashboard</a>
                <a class="nav-link" href="laporan.php">Data Laporan</a>
                <?php if($_SESSION['role'] == 'Admin'): ?>
                <a class="nav-link" href="users.php">Manajemen User</a>
                <?php endif; ?>
            </div>
            <div class="navbar-nav">
                <span class="nav-link text-white me-3">Halo, <strong><?php echo $_SESSION['nama']; ?></strong>
                    (<?php echo $_SESSION['role']; ?>)</span>
                <a class="btn btn-danger btn-sm mt-1" href="logout.php"
                    onclick="return confirm('Yakin ingin logout?')">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Dashboard IT Support</h2>

        <div class="row">
            <?php
            // Mengambil statistik sederhana
            $total = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM laporan_it"));
            $pending = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM laporan_it WHERE status='Pending'"));
            $selesai = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM laporan_it WHERE status='Selesai'"));
            ?>
            <div class="col-md-4">
                <div class="card bg-white border-0 shadow-sm p-3 mb-3 border-start border-primary border-5">
                    <h5>Total Laporan</h5>
                    <h2 class="text-primary"><?php echo $total; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white border-0 shadow-sm p-3 mb-3 border-start border-danger border-5">
                    <h5>Status Pending</h5>
                    <h2 class="text-danger"><?php echo $pending; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white border-0 shadow-sm p-3 mb-3 border-start border-success border-5">
                    <h5>Status Selesai</h5>
                    <h2 class="text-success"><?php echo $selesai; ?></h2>
                </div>
            </div>
        </div>

        <div class="mt-4 p-4 bg-white shadow-sm rounded">
            <h5>Aksi Cepat</h5>
            <hr>
            <a href="tambah_laporan.php" class="btn btn-success me-2"><i class="fas fa-plus"></i> Buat Laporan Baru</a>
            <a href="laporan.php" class="btn btn-outline-primary me-2"><i class="fas fa-table"></i> Lihat Semua Data</a>
            <a href="cetak.php" target="_blank" class="btn btn-outline-secondary"><i class="fas fa-print"></i> Cetak
                Laporan</a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow border-0">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="fas fa-chart-pie me-2"></i>Sebaran Masalah per Kategori</h5>
                    <hr>
                    <div style="height: 350px;">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Mengambil data dari PHP ke JavaScript
    const labels = <?php echo json_encode($labels); ?>;
    const dataValues = <?php echo json_encode($data_values); ?>;

    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar', // Anda bisa ganti jadi 'pie' atau 'line'
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Laporan',
                data: dataValues,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1 // Agar angka di sumbu Y bulat
                    }
                }
            }
        }
    });
    </script>

    <div class="card shadow border-0 mt-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-chart-line"></i> <?php echo $title; ?></h5>
            <div class="btn-group shadow-sm">
                <a href="index.php?view=hari_ini"
                    class="btn btn-sm btn-outline-primary <?= $view == 'hari_ini' ? 'active' : '' ?>">Hari</a>
                <a href="index.php?view=minggu_ini"
                    class="btn btn-sm btn-outline-primary <?= $view == 'minggu_ini' ? 'active' : '' ?>">Minggu</a>
                <a href="index.php?view=bulan_ini"
                    class="btn btn-sm btn-outline-primary <?= $view == 'bulan_ini' ? 'active' : '' ?>">Bulan</a>
            </div>
        </div>
        <div class="card-body">
            <canvas id="timeChart" style="width: 100%; height: 300px;"></canvas>
        </div>
    </div>

    <script>
    const ctxTime = document.getElementById('timeChart').getContext('2d');
    new Chart(ctxTime, {
        type: 'line', // Menggunakan Line Chart agar tren kenaikan/penurunan terlihat jelas
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Jumlah Laporan',
                data: <?php echo json_encode($data_values); ?>,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.3, // Membuat garis lebih melengkung/halus
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    </script>
</body>

</html>