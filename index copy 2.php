<?php
session_start();
include 'config/database.php';


// Proteksi Login Global
if (!isset($_SESSION['login']) && !isset($_GET['page']) == 'login') {
    header("Location: login.php");
    exit;
}

// Mengambil parameter halaman dari URL (contoh: index.php?page=laporan)
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Layout Header
include 'layouts/header.php';

// Navigasi (Hanya muncul jika sudah login)
if (isset($_SESSION['login'])) {
    include 'layouts/navbar.php';
    include 'layouts/header.php';
}

// Router: Menentukan konten mana yang akan ditampilkan
echo '<div class="container mt-4">';
switch ($page) {
    case 'dashboard':
        include 'views/dashboard.php';
        break;
    case 'laporan':
        include 'views/laporan.php';
        break;
    case 'tambah_laporan':
        include 'views/reports/tambah_laporan.php';
        break;
    case 'hapus_laporan':
        include 'views/reports/hapus_laporan.php';
        break;
    case 'edit_laporan':
        include 'views/reports/edit_laporan.php';
        break;
    case 'users':
        include 'views/users/users.php';
        break;
    case 'edit_user':
        include 'views/users/edit_user.php';
        break;
    case 'hapus_user':
        include 'views/users/hapus_user.php';
        break;
    case 'profil':
        include 'views/users/profil.php';
        break;
    default:
        include 'views/dashboard.php';
}
echo '</div>';

// Layout Footer
include 'layouts/footer.php';
?>