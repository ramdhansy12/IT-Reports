<?php
session_start();
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
include 'config/database.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['nama'] = $row['nama_lengkap'];
            $_SESSION['role'] = $row['role'];
            header("Location: index.php");
            exit;
        }
    }
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IT Report RS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    body {
        background: linear-gradient(135deg, #0d6efd 0%, #00d4ff 100%);
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-card {
        width: 100%;
        max-width: 400px;
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .login-header {
        background: white;
        padding: 30px 30px 10px 30px;
        text-align: center;
    }

    .login-header i {
        font-size: 50px;
        color: #0d6efd;
        margin-bottom: 15px;
    }

    .login-body {
        background: white;
        padding: 20px 40px 40px 40px;
    }

    .form-control {
        border-radius: 10px;
        padding: 12px;
        border: 1px solid #ddd;
    }

    .btn-login {
        border-radius: 10px;
        padding: 12px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: 0.3s;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
    }
    </style>
</head>

<body>

    <div class="login-card shadow">
        <div class="login-header">
            <i class="fas fa-hospital-user"></i>
            <h4 class="fw-bold text-dark">IT REPORT SYSTEM</h4>
            <p class="text-muted small">Silakan login untuk mengakses laporan</p>
        </div>

        <div class="login-body">
            <?php if(isset($error)): ?>
            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div>Username atau Password salah!</div>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i
                                class="fas fa-user text-muted"></i></span>
                        <input type="text" name="username" class="form-control border-start-0 bg-light"
                            placeholder="Masukkan username" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i
                                class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 bg-light"
                            placeholder="Masukkan password" required>
                    </div>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 btn-login">
                    Masuk <i class="fas fa-sign-in-alt ms-2"></i>
                </button>
            </form>
        </div>
        <div class="bg-light p-3 text-center border-top">
            <small class="text-muted">v2.0 &copy; 2025 IT Dept Rumah Sakit</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>