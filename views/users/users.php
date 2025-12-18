<?php
session_start();
// Proteksi: Hanya Admin yang boleh akses manajemen user
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Akses Ditolak! Hanya Admin yang boleh mengakses halaman ini.'); window.location='index.php';</script>";
    exit;
}
include 'config/database.php';

?>

<!DOCTYPE html>
<html lang="id">


<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="fas fa-users"></i> Daftar Pengguna Sistem</h3>
            <a href="tambah_user.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Tambah User Baru</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover bg-white shadow-sm rounded">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $sql = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC");
                    while($row = mysqli_fetch_assoc($sql)){
                        echo "<tr>
                            <td>$no</td>
                            <td>{$row['username']}</td>
                            <td>{$row['nama_lengkap']}</td>
                            <td><span class='badge ".($row['role'] == 'Admin' ? 'bg-primary' : 'bg-secondary')."'>{$row['role']}</span></td>
                            <td>
                                <a href='edit_user.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                                ".($row['username'] !== 'admin' ? "<a href='hapus_user.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Hapus user ini?\")'>Hapus</a>" : "")."
                            </td>
                        </tr>";
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>