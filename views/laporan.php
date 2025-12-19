<?php
// Variabel untuk menyimpan kondisi filter/search
$where_clause = "";
$search_query = "";
$filter_unit = "";
$filter_kategori = "";
$filter_status = "";

// --- FITUR PAGINATION (LOGIKA AWAL) ---
$batas = 10; // Jumlah data per halaman
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;
// --------------------------------------

// Proses Pencarian & Filter (Logika tetap sama)
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clause .= " AND (deskripsi_masalah LIKE '%$search_query%' OR tindakan LIKE '%$search_query%' OR petugas_it LIKE '%$search_query%' OR unit_kerja LIKE '%$search_query%')";
}
if (isset($_GET['unit_kerja']) && !empty($_GET['unit_kerja'])) {
    $filter_unit = mysqli_real_escape_string($conn, $_GET['unit_kerja']);
    $where_clause .= " AND unit_kerja = '$filter_unit'";
}
if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $filter_kategori = mysqli_real_escape_string($conn, $_GET['kategori']);
    $where_clause .= " AND kategori = '$filter_kategori'";
}
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $filter_status = mysqli_real_escape_string($conn, $_GET['status']);
    $where_clause .= " AND status = '$filter_status'";
}

// --- HITUNG TOTAL DATA UNTUK PAGINATION ---
$query_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan_it WHERE 1=1 $where_clause");
$row_total = mysqli_fetch_assoc($query_total);
$total_data = $row_total['total'];
$total_halaman = ceil($total_data / $batas);
// ------------------------------------------

$units_result = mysqli_query($conn, "SELECT DISTINCT unit_kerja FROM laporan_it ORDER BY unit_kerja ASC");
$categories_result = mysqli_query($conn, "SELECT DISTINCT kategori FROM laporan_it ORDER BY kategori ASC");
?>

<div class="d-flex flex-column mb-3">
    <?php if (isset($_GET['msg'])): ?>
    <div id="auto-alert" class="alert alert-info alert-dismissible fade show small py-2 mb-3" role="alert">
        <i class="fas fa-info-circle me-1"></i> <?= htmlspecialchars($_GET['msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
            style="padding: 0.5rem;"></button>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center">
        <h3 class="fs-4 mb-0"><i class="fas fa-table text-primary"></i> Riwayat Perbaikan IT RS. Permata Keluarga</h3>
        <div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
            <button type="button" class="btn btn-outline-success me-2" data-bs-toggle="modal"
                data-bs-target="#importModal">
                <i class="fas fa-file-import"></i> Import CSV
            </button>
            <?php endif; ?>

            <a href="views/reports/export_excel.php" class="btn btn-outline-primary me-2">
                <i class="fas fa-file-export"></i> Export Excel
            </a>
            <a href="index.php?page=tambah_laporan" class="btn btn-success"><i class="fas fa-plus"></i> Tambah
                Laporan</a>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4 p-3 bg-white border-0">
    <form method="GET" action="index.php" class="row g-3 align-items-end">
        <input type="hidden" name="page" value="laporan">
        <div class="col-md-4">
            <label class="form-label small fw-bold">Pencarian</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Kata kunci..."
                value="<?= htmlspecialchars($search_query) ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold">Unit</label>
            <select name="unit_kerja" class="form-select form-select-sm">
                <option value="">Semua Unit</option>
                <?php while($u = mysqli_fetch_assoc($units_result)): ?>
                <option value="<?= $u['unit_kerja'] ?>" <?= $filter_unit == $u['unit_kerja'] ? 'selected' : '' ?>>
                    <?= $u['unit_kerja'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold">Kategori</label>
            <select name="kategori" class="form-select form-select-sm">
                <option value="">Semua</option>
                <?php while($k = mysqli_fetch_assoc($categories_result)): ?>
                <option value="<?= $k['kategori'] ?>" <?= $filter_kategori == $k['kategori'] ? 'selected' : '' ?>>
                    <?= $k['kategori'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua</option>
                <option value="Selesai" <?= $filter_status == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                <option value="Proses" <?= $filter_status == 'Proses' ? 'selected' : '' ?>>Proses</option>
                <option value="Pending" <?= $filter_status == 'Pending' ? 'selected' : '' ?>>Pending</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i></button>
            <a href="index.php?page=laporan" class="btn btn-secondary btn-sm"><i class="fas fa-sync-alt"></i></a>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-hover bg-white shadow-sm rounded">
        <thead class="table-dark">
            <tr>
                <th>Tgl/Waktu</th>
                <th>Unit</th>
                <th>Kategori</th>
                <th>Masalah</th>
                <th>Status</th>
                <th>Gambar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query ditambahkan LIMIT untuk Pagination
            $sql = "SELECT * FROM laporan_it WHERE 1=1 $where_clause ORDER BY tanggal DESC LIMIT $halaman_awal, $batas";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0):
                while($data = mysqli_fetch_array($result)):
                    $badge = ($data['status'] == 'Selesai') ? 'bg-success' : (($data['status'] == 'Proses') ? 'bg-warning text-dark' : 'bg-danger');
            ?>
            <tr>
                <td class="small"><?= date('d/m/y H:i', strtotime($data['tanggal'])) ?></td>
                <td><?= htmlspecialchars($data['unit_kerja']) ?></td>
                <td><span class="badge bg-info text-light"><?= htmlspecialchars($data['kategori']) ?></span></td>
                <td class="small"><?= htmlspecialchars(substr($data['deskripsi_masalah'], 0, 30)) ?>...</td>
                <td><span class="badge <?= $badge ?>"><?= $data['status'] ?></span></td>
                <td>
                    <?php if (!empty($data['gambar'])): ?>
                    <img src="uploads/<?= $data['gambar'] ?>" class="rounded" width="40">
                    <?php else: ?> - <?php endif; ?>
                </td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info text-white me-2" data-bs-toggle="modal"
                            data-bs-target="#viewModal"
                            data-tanggal="<?= date('d/m/Y H:i', strtotime($data['tanggal'])) ?>"
                            data-petugas="<?= htmlspecialchars($data['petugas_it']) ?>"
                            data-unit="<?= htmlspecialchars($data['unit_kerja']) ?>"
                            data-kategori="<?= htmlspecialchars($data['kategori']) ?>"
                            data-masalah="<?= htmlspecialchars($data['deskripsi_masalah']) ?>"
                            data-tindakan="<?= htmlspecialchars($data['tindakan']) ?>"
                            data-status="<?= $data['status'] ?>" data-gambar="<?= $data['gambar'] ?>">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a href="index.php?page=edit_laporan&id=<?= $data['id'] ?>" class="btn btn-sm btn-warning me-2">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger me-2" data-bs-toggle="modal"
                            data-bs-target="#deleteModal" data-id="<?= $data['id'] ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr>
                <td colspan="7" class="text-center">Data tidak ditemukan.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<nav>
    <ul class="pagination pagination-sm justify-content-center">
        <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link"
                href="?page=laporan&halaman=<?= $halaman - 1; ?>&search=<?= $search_query ?>&unit_kerja=<?= $filter_unit ?>&kategori=<?= $filter_kategori ?>&status=<?= $filter_status ?>">Previous</a>
        </li>
        <?php for($x=1; $x<=$total_halaman; $x++): ?>
        <li class="page-item <?= ($halaman == $x) ? 'active' : ''; ?>">
            <a class="page-link"
                href="?page=laporan&halaman=<?= $x; ?>&search=<?= $search_query ?>&unit_kerja=<?= $filter_unit ?>&kategori=<?= $filter_kategori ?>&status=<?= $filter_status ?>"><?= $x; ?></a>
        </li>
        <?php endfor; ?>
        <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
            <a class="page-link"
                href="?page=laporan&halaman=<?= $halaman + 1; ?>&search=<?= $search_query ?>&unit_kerja=<?= $filter_unit ?>&kategori=<?= $filter_kategori ?>&status=<?= $filter_status ?>">Next</a>
        </li>
    </ul>
</nav>

<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-file-alt"></i> Detail Laporan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="120">Waktu</th>
                                <td>: <span id="v-tanggal"></span></td>
                            </tr>
                            <tr>
                                <th>Petugas</th>
                                <td>: <span id="v-petugas"></span></td>
                            </tr>
                            <tr>
                                <th>Unit</th>
                                <td>: <span id="v-unit"></span></td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>: <span id="v-kategori" class="badge bg-secondary"></span></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>: <span id="v-status" class="badge"></span></td>
                            </tr>
                        </table>
                        <hr>
                        <h6><b>Masalah:</b></h6>
                        <p id="v-masalah" class="bg-light p-2 rounded small"></p>
                        <h6><b>Tindakan:</b></h6>
                        <p id="v-tindakan" class="bg-light p-2 rounded small border-start border-4 border-info"></p>
                    </div>
                    <div class="col-md-5 text-center">
                        <h6><b>Foto Bukti</b></h6>
                        <img id="v-gambar" src="" class="img-fluid rounded border shadow-sm"
                            style="max-height: 250px; display:none;">
                        <div id="v-no-img" class="text-muted small py-5 bg-light rounded">Tidak ada gambar</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p>Yakin ingin menghapus laporan ini?<br><b>Tindakan ini permanen.</b></p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a id="btn-confirm-delete" href="#" class="btn btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title small"><i class="fas fa-file-import me-2"></i> Import Data Laporan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="views/reports/import_csv.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-warning py-2 small mb-3">
                        <i class="fas fa-exclamation-circle me-1"></i> Gunakan template resmi agar data masuk dengan
                        benar.
                        <br>
                        <a href="views/reports/download_template.php" class="fw-bold text-decoration-none">
                            <i class="fas fa-download me-1"></i> Download Template CSV
                        </a>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih File CSV</label>
                        <input type="file" name="file" class="form-control form-control-sm" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="import" class="btn btn-sm btn-success">Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Logic View Modal
const vModal = document.getElementById('viewModal');
vModal.addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    document.getElementById('v-tanggal').innerText = b.getAttribute('data-tanggal');
    document.getElementById('v-petugas').innerText = b.getAttribute(
        'data - petugas = "<?= htmlspecialchars($data['petugas_it'] ?? '-') ?>"');
    document.getElementById('v-unit').innerText = b.getAttribute('data-unit');
    document.getElementById('v-kategori').innerText = b.getAttribute('data-kategori');
    document.getElementById('v-masalah').innerText = b.getAttribute('data-masalah');
    document.getElementById('v-tindakan').innerText = b.getAttribute(
        'data - tindakan = "<?= htmlspecialchars($data['tindakan'] ?? 'Belum ada tindakan') ?>"');



    // Status Badge
    const st = b.getAttribute('data-status');
    const stB = document.getElementById('v-status');
    stB.innerText = st;
    stB.className = 'badge ' + (st === 'Selesai' ? 'bg-success' : (st === 'Proses' ? 'bg-warning text-dark' :
        'bg-danger'));

    // Image Logic
    const img = b.getAttribute('data-gambar');
    const imgTag = document.getElementById('v-gambar');
    const noImg = document.getElementById('v-no-img');
    if (img) {
        imgTag.src = 'uploads/' + img;
        imgTag.style.display = 'inline-block';
        noImg.style.display = 'none';
    } else {
        imgTag.style.display = 'none';
        noImg.style.display = 'block';
    }
});

// Logic Delete Modal
const dModal = document.getElementById('deleteModal');
dModal.addEventListener('show.bs.modal', function(e) {
    const id = e.relatedTarget.getAttribute('data-id');
    document.getElementById('btn-confirm-delete').href = 'views/reports/hapus_laporan.php?id=' + id;
});
</script>