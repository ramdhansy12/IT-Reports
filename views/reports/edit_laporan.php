<?php
// Tidak perlu session_start atau include database jika dipanggil lewat index.php

// Pastikan ID ada di URL
if (!isset($_GET['id'])) {
    echo "<script>window.location='index.php?page=laporan';</script>";
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM laporan_it WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data tidak ditemukan...");
}

if (isset($_POST['update'])) {
    $petugas = mysqli_real_escape_string($conn, $_POST['petugas_it']);
    $tanggal = mysqli_real_escape_string($conn,$_POST['tanggal_kustom']);
    $unit = mysqli_real_escape_string($conn, $_POST['unit_kerja']);
    $kategori = $_POST['kategori'];
    $masalah = mysqli_real_escape_string($conn, $_POST['deskripsi_masalah']);
    $tindakan = mysqli_real_escape_string($conn, $_POST['tindakan']);
    $status = $_POST['status'];
    $gambar_lama = $data['gambar'];

    $gambar_baru = $gambar_lama; 

    // Proses Upload Gambar Baru
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0){
        $target_dir = "uploads/";
        $file_name = uniqid() . '_' . basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if(in_array($imageFileType, $allowed_ext)){
            if(move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)){
                $gambar_baru = $file_name;
                if(!empty($gambar_lama) && file_exists($target_dir . $gambar_lama)){
                    unlink($target_dir . $gambar_lama);
                }
            }
        }
    }

    $sql = "UPDATE laporan_it SET 
            tanggal='$tanggal',
            petugas_it='$petugas', 
            unit_kerja='$unit', 
            kategori='$kategori', 
            deskripsi_masalah='$masalah', 
            tindakan='$tindakan', 
            status='$status',
            gambar='$gambar_baru' 
            WHERE id='$id'";

    if (mysqli_query($conn, $sql)) {
        // PERBAIKAN: Arahkan kembali ke sistem routing index.php
        echo "<script>alert('Data Berhasil Diperbarui'); window.location='index.php?page=laporan';</script>";
    } else {
        echo "Gagal menyimpan perubahan...";
    }
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow border-0">
            <div class="card-header bg-warning py-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-edit"></i> Edit Laporan Maintenance</h5>
            </div>
            <div class="card-body p-4 bg-white">
                <form method="POST" enctype="multipart/form-data">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tanggal & Waktu Kejadian</label>
                        <input type="text" id="tanggal_kustom" name="tanggal_kustom" class="form-control bg-white"
                            placeholder="Pilih Tanggal & Jam" required>
                        <small class="text-muted"
                            style="font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;">Input
                            tanggal & jam harus sesuai
                            tindakan!!!.</small>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Nama Petugas IT</label>
                            <input type="text" name="petugas_it" class="form-control" value="<?= $data['petugas_it'] ?>"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Unit / Ruangan Yang Trouble</label>
                            <input type="text" name="unit_kerja" class="form-control" value="<?= $data['unit_kerja'] ?>"
                                required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori</label>
                        <select name="kategori" class="form-select">
                            <option value="Hardware" <?= $data['kategori'] == 'Hardware' ? 'selected' : '' ?>>Hardware
                                (Printer, PC, Monitor)</option>
                            <option value="Software" <?= $data['kategori'] == 'Software' ? 'selected' : '' ?>>Software
                                (Windows, Office)</option>
                            <option value="Jaringan" <?= $data['kategori'] == 'Jaringan' ? 'selected' : '' ?>>Jaringan
                                (Internet, WiFi)</option>
                            <option value="SIMRS" <?= $data['kategori'] == 'SIMRS' ? 'selected' : '' ?>>Aplikasi SIMRS
                            </option>
                            <option value="Lainnya" <?= $data['kategori'] == 'Lainnya' ? 'selected' : '' ?>>Lainnya
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Masalah</label>
                        <textarea name="deskripsi_masalah" class="form-control" rows="3"
                            required><?= $data['deskripsi_masalah'] ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tindakan Perbaikan</label>
                        <textarea name="tindakan" class="form-control" rows="3"
                            required><?= $data['tindakan'] ?></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status Pekerjaan</label>
                            <select name="status" class="form-select">
                                <option value="Selesai" <?= $data['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai
                                </option>
                                <option value="Proses" <?= $data['status'] == 'Proses' ? 'selected' : '' ?>>Proses
                                </option>
                                <option value="Pending" <?= $data['status'] == 'Pending' ? 'selected' : '' ?>>Pending
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gambar Bukti</label>
                            <?php if (!empty($data['gambar'])): ?>
                            <div class="mb-2">
                                <img src="uploads/<?= $data['gambar'] ?>" class="img-thumbnail" width="100">
                            </div>
                            <?php endif; ?>
                            <input type="file" name="gambar" class="form-control">
                            <small class="text-muted">Abaikan jika tidak ingin mengubah gambar.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php?page=laporan" class="btn btn-secondary px-4"><i
                                class="fas fa-arrow-left"></i> Kembali</a>
                        <button type="submit" name="update" class="btn btn-primary px-4"><i class="fas fa-save"></i>
                            Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
flatpickr("#tanggal_kustom", {
    enableTime: true,
    dateFormat: "Y-m-d H:i", // Format 24 Jam
    time_24hr: true, // Paksa 24 Jam
    defaultDate: new Date()
});
</script>