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

}
date_default_timezone_set('Asia/Jakarta');
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
include 'config/database.php';
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Laporan - System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
    body {
        overflow-x: hidden;
        background-color: #f8f9fa;
    }

    #sidebar-wrapper {
        min-height: 100vh;
        width:
            250px;
        transition: all 0.3s;
        background: #212529;
    }

    .sidebar-heading {
        padding: 1.5rem 1.25rem;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
        border-bottom: 1px solid #343a40;
    }

    .list-group-item {
        border: none;
        padding: 1rem 1.5rem;
        background: transparent;
        color: #adb5bd;
    }

    .list-group-item:hover {
        background: #eaf2faff;
        color: white;
    }

    .list-group-item.active {
        background: #0a0ae1ff;
        color: white;
    }

    #page-content-wrapper {
        width:
            100%;
    }

    .navbar {
        background: white;
        border-bottom: 1px solid #dee2e6;
    }

    /* ... kode CSS sebelumnya ... */
    /*
    Default: Sidebar tampil di kiri */
    #sidebar-wrapper {
        min-height: 100vh;
        width: 250px;
        background: #029ef8ff;
        transition: all 0.3s;
    }

    /* Di layar HP (Max 992px / Large) */
    @media (max-width: 991.98px) {
        #sidebar-wrapper {
            margin-left: -250px;
            /* Sembunyikan ke kiri */
        }

        /* Jika class toggled ada, maka tampilkan (saat klik bi-list)
    */
        #wrapper.toggled #sidebar-wrapper {
            margin-left: 0;
        }
    }

    /* Memperhalus transisi */
    #wrapper {
        display: flex;
        width: 100%;
        align-items: stretch;
        transition: all 0.3s ease;
    }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <div id="sidebar-wrapper">
            <div class="sidebar-heading"><i class="bi bi-pc-display-horizontal me-2"></i>IT LAPORAN</div>
            <div class="list-group list-group-flush"><a href="index.php?page=dashboard"
                    class="list-group-item list-group-item-action <?= $page == 'dashboard' ? 'active' : '' ?>"><i
                        class="bi bi-speedometer2 me-2"></i>Dashboard </a><a href="index.php?page=laporan"
                    class="list-group-item list-group-item-action <?= $page == 'laporan' ? 'active' : '' ?>"><i
                        class="bi bi-file-earmark-text me-2"></i>Data Laporan
                </a><?php if ($_SESSION['role']=='Admin'): ?><a href="index.php?page=users"
                    class="list-group-item list-group-item-action <?= $page == 'users' ? 'active' : '' ?>"><i
                        class="bi bi-people me-2"></i>Manajemen User </a><?php endif;
    ?><a href="logout.php" class="list-group-item list-group-item-action text-dark mt-5"
                    onclick="return confirm('Yakin ingin keluar?')"><i class="bi bi-box-arrow-right me-2"></i>Logout
                </a></div>
        </div>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg py-3 px-4 shadow-sm">
                <div class="container-fluid"><button class="btn btn-primary d-lg-none" id="menu-toggle"><i
                            class="bi bi-list"></i></button><span class="navbar-text fw-bold text-dark ms-2"><i
                            class="bi bi-person-circle me-1"></i><span class="d-none d-sm-inline"><?=$_SESSION['nama'];
    ?></span></span>
                    <div class="ms-auto"><span class="badge bg-secondary"><?=$_SESSION['role'];

    ?></span></div>
                </div>
            </nav>
            <div class="container-fluid p-4"><?php switch ($page) {
        case 'dashboard': include "views/dashboard.php";
        break;
        case 'laporan': include "views/laporan.php";
        break;
        case 'tambah_laporan': include "views/reports/tambah_laporan.php";
        break;
        case 'edit_laporan': include "views/reports/edit_laporan.php";
        break;
        case 'hapus_laporan': include "views/reports/hapus_laporan.php";
        break;
        case 'users': include "views/users/users.php";
        break;
        case 'tambah_user': include "views/users/tambah_user.php";
        break;
        case 'edit_user': include "views/users/edit_user.php";
        break;
        case 'hapus_user': include "views/users/hapus_user.php";
        break;
        default: echo "<h3>Halaman Tidak Ditemukan</h3>";
        break;
    }

    ?></div>
        </div>
    </div>
    <script>
    const menuToggle = document.querySelector("#menu-toggle");
    const wrapper = document.querySelector("#wrapper");

    menuToggle.addEventListener("click", e => {
            e.preventDefault();
            wrapper.classList.toggle("toggled");
        }

    );
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>

</html>