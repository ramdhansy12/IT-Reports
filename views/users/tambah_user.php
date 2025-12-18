<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'Admin') { exit; }
include 'config/database.php';


if (isset($_POST['simpan'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password', '$nama', '$role')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('User Berhasil Ditambahkan'); window.location='users.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow border-0 mx-auto" style="max-width: 500px;">
            <div class="card-header bg-primary text-white">
                <h5>Tambah Pengguna Baru</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-select">
                            <option value="Teknisi">Teknisi</option>
                            <option value="Admin">Admin</option>
                            <option value="IT">IT</option>
                        </select>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-primary w-100">Simpan User</button>
                    <a href="users.php" class="btn btn-link w-100 mt-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>