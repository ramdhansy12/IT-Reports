<?php
session_start();
// Proteksi: Hanya Admin yang boleh edit user
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}
include 'config/database.php';

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $role = $_POST['role'];
    
    // Cek apakah password diisi (artinya mau ganti password)
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username='$username', password='$password', nama_lengkap='$nama', role='$role' WHERE id='$id'";
    } else {
        // Jika password kosong, jangan update kolom password
        $sql = "UPDATE users SET username='$username', nama_lengkap='$nama', role='$role' WHERE id='$id'";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data User Berhasil Diperbarui'); window.location='users.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow border-0 mx-auto" style="max-width: 500px;">
            <div class="card-header bg-warning">
                <h5><i class="fas fa-user-edit"></i> Edit Pengguna</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= $data['username'] ?>"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?= $data['nama_lengkap'] ?>"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru (Kosongkan jika tidak ingin ganti)</label>
                        <input type="password" name="password" class="form-control"
                            placeholder="Isi hanya jika ingin reset password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="Teknisi" <?= $data['role'] == 'Teknisi' ? 'selected' : '' ?>>Teknisi</option>
                            <option value="Admin" <?= $data['role'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="IT" <?= $data['role'] == 'IT' ? 'selected' : '' ?>>IT</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="update" class="btn btn-warning">Perbarui User</button>
                        <a href="users.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>