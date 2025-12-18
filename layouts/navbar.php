<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-hospital-user"></i> IT Report RS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="index.php?page=dashboard">Dashboard</a>
                <a class="nav-link" href="index.php?page=laporan">Data Laporan</a>
                <?php if($_SESSION['role'] == 'Admin'): ?>
                <a class="nav-link" href="index.php?page=users">Manajemen User</a>
                <?php endif; ?>
            </div>
            <div class="navbar-nav">
                <span class="nav-link text-white me-3">Halo, <strong><?= $_SESSION['nama'] ?></strong>
                    (<?= $_SESSION['role'] ?>)</span>
                <a class="btn btn-danger btn-sm mt-1" href="logout.php"
                    onclick="return confirm('Yakin ingin logout?')">Logout</a>
            </div>
        </div>
    </div>
</nav>