<?php 
// session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
include 'config/database.php'; 

if(isset($_POST['submit'])){
    $petugas = $_SESSION['nama']; 
    $unit = mysqli_real_escape_string($conn, $_POST['unit_kerja']);
    // $petugas = mysqli_real_escape_string($conn, $_POST['petugas_it']);
    // $unit = mysqli_real_escape_string($conn, $_POST['unit_kerja']);
    $tanggal = $_POST['tanggal_kustom'];
    $kategori = $_POST['kategori'];
    $masalah = mysqli_real_escape_string($conn, $_POST['deskripsi_masalah']);
    $tindakan = mysqli_real_escape_string($conn, $_POST['tindakan']);
    $status = $_POST['status'];
    $gambar = ''; // Default tanpa gambar

    // Proses Upload Gambar
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0){
        $target_dir = "uploads/"; // Pastikan folder 'uploads' sudah ada
        $file_name = uniqid() . '_' . basename($_FILES["gambar"]["name"]); // Nama unik
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Validasi ekstensi
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if(in_array($imageFileType, $allowed_ext)){
            if(move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)){
                $gambar = $file_name; // Simpan hanya nama file ke database
            } else {
                echo "<script>alert('Gagal mengupload gambar.');</script>";
            }
        } else {
            echo "<script>alert('Hanya diperbolehkan JPG, JPEG, PNG & GIF.');</script>";
        }
    }
    
// Masukkan variabel $tanggal ke dalam query INSERT
    $query = "INSERT INTO laporan_it (tanggal, petugas_it, unit_kerja, kategori, deskripsi_masalah, tindakan, status, gambar) 
              VALUES ('$tanggal', '$petugas', '$unit', '$kategori', '$masalah', '$tindakan', '$status', '$gambar')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Data Berhasil Disimpan'); window.location='index.php?page=laporan';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data.');</script>";
    }
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="container mt-5 mb-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-success text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-plus-circle"></i> Buat Laporan Pekerjaan IT Baru</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tanggal & Waktu Kejadian</label>
                        <div style="position: relative;">
                            <input type="text" id="tanggal_kustom" name="tanggal_kustom" class="form-control bg-white"
                                placeholder="Pilih Tanggal & Jam" style="padding-right: 35px;" required>
                            <i class="bi bi-calendar-date"
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                        </div>
                        <small class="text-muted"
                            style="font-family: 'Gill Sans', 'Gill Sans MT', Calibri, sans-serif;">
                            Input tanggal & jam harus sesuai tindakan!!!
                        </small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Petugas IT</label>
                        <input type="text" name="petugas_it" class="form-control bg-light"
                            value="<?php echo $_SESSION['nama']; ?>" readonly>
                        <small class="text-muted">Nama terisi otomatis sesuai akun login.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Unit/Ruangan Kerja</label>
                        <input type="text" name="unit_kerja" class="form-control" placeholder="Contoh: Radiologi, IGD"
                            required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori Kendala</label>
                    <select name="kategori" class="form-select">
                        <option value="Hardware">Hardware (Printer, PC, Monitor)</option>
                        <option value="Software">Software (Windows, Office)</option>
                        <option value="Jaringan">Jaringan (Internet, LAN, WiFi)</option>
                        <option value="SIMRS">Aplikasi SIMRS</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi Masalah</label>
                    <textarea name="deskripsi_masalah" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tindakan Perbaikan</label>
                    <textarea name="tindakan" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select w-50">
                        <option value="Selesai">Selesai</option>
                        <option value="Proses">Proses</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Upload Gambar Bukti (Opsional)</label>
                    <input type="file" name="gambar" class="form-control">
                    <small class="text-muted">Max 2MB. Format: JPG, JPEG, PNG, GIF.</small>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="laporan.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                    <button type="submit" name="submit" class="btn btn-primary px-4"><i class="fas fa-save"></i>
                        Simpan Laporan</button>
                </div>
            </form>
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