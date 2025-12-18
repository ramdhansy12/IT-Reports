<?php
// Tidak perlu session_start() atau include database lagi karena sudah di index.php utama

// 1. Data untuk Chart Kategori
$query_chart = mysqli_query($conn, "SELECT kategori, COUNT(*) as total FROM laporan_it GROUP BY kategori");
$cat_labels = [];
$cat_values = [];
while ($row = mysqli_fetch_assoc($query_chart)) {
    $cat_labels[] = $row['kategori'];
    $cat_values[] = $row['total'];
}

// 2. Data untuk Chart Tren Waktu
$view = isset($_GET['view']) ? $_GET['view'] : 'bulan_ini';
$time_labels = [];
$time_values = [];
$title = "";

if ($view == 'hari_ini') {
    $title = "Statistik Per Jam (Hari Ini)";
    $query = "SELECT HOUR(tanggal) as label, COUNT(*) as total FROM laporan_it WHERE DATE(tanggal) = CURDATE() GROUP BY HOUR(tanggal)";
} elseif ($view == 'minggu_ini') {
    $title = "Statistik Per Hari (Minggu Ini)";
    $query = "SELECT DAYNAME(tanggal) as label, COUNT(*) as total FROM laporan_it WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(tanggal)";
} else {
    $title = "Statistik Per Tanggal (Bulan Ini)";
    $query = "SELECT DATE_FORMAT(tanggal, '%d %b') as label, COUNT(*) as total FROM laporan_it WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()) GROUP BY DATE(tanggal)";
}

$result_time = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result_time)) {
    $time_labels[] = $row['label'];
    $time_values[] = $row['total'];
}

// 3. Statistik Card
$total = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM laporan_it"));
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM laporan_it WHERE status='Pending'"));
$selesai = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM laporan_it WHERE status='Selesai'"));
?>

<h2 class="mb-4">Dashboard IT Support</h2>

<div class="row">
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

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow border-0 h-100">
            <div class="card-body">
                <h5 class="fw-bold"><i class="fas fa-chart-pie me-2"></i>Sebaran Kategori</h5>
                <hr>
                <canvas id="catChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow border-0 h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><?php echo $title; ?></h5>
                <div class="btn-group">
                    <a href="index.php?page=dashboard&view=hari_ini" class="btn btn-sm btn-outline-primary">Hari</a>
                    <a href="index.php?page=dashboard&view=minggu_ini" class="btn btn-sm btn-outline-primary">Minggu</a>
                    <a href="index.php?page=dashboard&view=bulan_ini" class="btn btn-sm btn-outline-primary">Bulan</a>
                </div>
            </div>
            <div class="card-body">
                <canvas id="timeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// Chart Kategori
new Chart(document.getElementById('catChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($cat_labels); ?>,
        datasets: [{
            label: 'Total',
            data: <?php echo json_encode($cat_values); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)'
        }]
    },
    options: {
        responsive: true
    }
});

// Chart Waktu
new Chart(document.getElementById('timeChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($time_labels); ?>,
        datasets: [{
            label: 'Laporan',
            data: <?php echo json_encode($time_values); ?>,
            borderColor: '#0d6efd',
            fill: true,
            tension: 0.3
        }]
    }
});
</script>