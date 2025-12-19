<?php
include 'config/database.php'; // Sesuaikan koneksi Anda
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapor Kendala IT - RS. Permata Keluarga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center mb-4">
                    <h3 class="fw-bold">HELP DESK IT</h3>
                    <span style="font-style: italic;">Lapor IT RS. Permata Keluarga Lippo Cikarang</span>

                </div>

                <div class="card shadow border-0 p-4">
                    <form action="proses_lapor_publik.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <p class="text-muted">Silakan isi formulir di bawah untuk bantuan teknis</p>
                            <label class="form-label small fw-bold">Pilih Unit Kerja</label>
                            <select name="unit_kerja" class="form-select" required>
                                <option value="">-- Pilih Unit Anda --</option>
                                <option value="Office">Office</option>
                                <option value="Casemix">Casemix</option>
                                <option value="MCU">Medical Check Up</option>
                                <option value="IGD">IGD</option>
                                <option value="Poli">Poli</option>
                                <option value="Farmasi">Farmasi</option>
                                <option value="Laboratorium">Laboratorium</option>
                                <option value="Radiologi">Radiologi</option>
                                <option value="Kasir">Kasir</option>
                                <option value="Adm">ADM</option>
                                <option value="FO">Front Office</option>
                                <option value="Poli">POLI</option>
                                <option value="RWI3">RWI 3</option>
                                <option value="RWI5">RWI 5</option>
                                <option value="MR">Medical Record</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Kategori Kendala</label>
                            <select name="kategori" class="form-select" required>
                                <option value="Hardware">Hardware (Printer, PC, Mouse)</option>
                                <option value="Software">Software (SIMRS, Windows, Email)</option>
                                <option value="Jaringan">Jaringan (Internet Mati, Wifi)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Deskripsi Masalah</label>
                            <textarea name="deskripsi_masalah" class="form-control" rows="4"
                                placeholder="Contoh: Printer di kasir tidak bisa narik kertas..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Foto Kendala (Opsional)</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="kirim" class="btn btn-primary shadow-sm">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>