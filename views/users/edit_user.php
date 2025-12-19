<?php 
// 1. HAPUS ATAU KOMENTAR session_start() karena sudah ada di index.php
// session_start(); 

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
    exit;
}

include 'config/database.php'; // Pastikan path ini benar sesuai lokasi file

// 2. Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: index.php?page=users");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan
if (!$data) {
    echo "<script>alert('Data user tidak ditemukan!'); window.location='index.php?page=users';</script>";
    exit;
}

// 3. Proses Update Data
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $role = $_POST['role'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    
    // Logika Password: Jika diisi maka ganti, jika kosong tetap yang lama
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username='$username', nama_lengkap='$nama', role='$role', password='$password' WHERE id='$id'";
    } else {
        $sql = "UPDATE users SET username='$username', nama_lengkap='$nama', role='$role' WHERE id='$id'";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('User berhasil diupdate!'); window.location='index.php?page=users';</script>";
    } else {
        echo "<script>alert('Gagal update data.');</script>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-dark py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-user-edit"></i> Edit Pengguna</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= $data['username'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" value="<?= $data['nama_lengkap'] ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="Admin" <?= $data['role'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="Teknisi" <?= $data['role'] == 'Teknisi' ? 'selected' : '' ?>>Teknisi
                        </option>
                        <option value="IT" <?= $data['role'] == 'IT' ? 'selected' : '' ?>>IT</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password Baru (Kosongkan jika tidak ingin ganti)</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password baru">
                </div>
                <div class="d-flex justify-content-between">
                    <a href="index.php?page=users" class="btn btn-secondary">Batal</a>
                    <button type="submit" name="update" class="btn btn-primary px-4">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>