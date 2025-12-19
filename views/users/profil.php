<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
include 'config/database.php';
// Ambil data user yang sedang login berdasarkan session ID (asumsi Anda menyimpan ID di session saat login)
// Jika belum ada ID di session, kita gunakan username sebagai kunci
$username_session = $_SESSION['nama']; // Atau gunakan $_SESSION['username'] jika ada
$query = mysqli_query($conn, "SELECT * FROM users WHERE nama_lengkap = '$username_session'");
$user_data = mysqli_fetch_assoc($query);
$id_user = $user_data['id'];

if (isset($_POST['update_profil'])) {
    $nama_baru = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $password_baru = $_POST['password_baru'];
    $konfirmasi_pass = $_POST['konfirmasi_password'];

    // Validasi Ganti Password
    if (!empty($password_baru)) {
        if ($password_baru === $konfirmasi_pass) {
            $pass_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET nama_lengkap='$nama_baru', password='$pass_hash' WHERE id='$id_user'";
            $msg = "Profil dan Password berhasil diperbarui!";
        } else {
            $error = "Konfirmasi password tidak cocok!";
        }
    } else {
        // Hanya update nama saja
        $sql = "UPDATE users SET nama_lengkap='$nama_baru' WHERE id='$id_user'";
        $msg = "Nama berhasil diperbarui!";
    }

    if (isset($sql) && mysqli_query($conn, $sql)) {
        $_SESSION['nama'] = $nama_baru; // Update session nama agar di navbar berubah
        $success = $msg;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Profil Saya - IT Report RS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-user-circle"></i> Pengaturan Profil</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if(isset($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username (Tidak dapat diubah)</label>
                                <input type="text" class="form-control bg-light" value="<?= $user_data['username'] ?>"
                                    readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control"
                                    value="<?= $user_data['nama_lengkap'] ?>" required>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password_baru" class="form-control"
                                    placeholder="Kosongkan jika tidak ingin ganti">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="konfirmasi_password" class="form-control"
                                    placeholder="Ulangi password baru">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="update_profil" class="btn btn-primary">Simpan
                                    Perubahan</button>
                                <a href="index.php" class="btn btn-link text-secondary">Kembali ke Dashboard</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>