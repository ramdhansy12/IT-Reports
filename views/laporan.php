<?php
// Variabel untuk menyimpan kondisi filter/search
$where_clause = "";
$search_query = "";
$filter_unit = "";
$filter_kategori = "";
$filter_status = "";

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

$units_result = mysqli_query($conn, "SELECT DISTINCT unit_kerja FROM laporan_it ORDER BY unit_kerja ASC");
$categories_result = mysqli_query($conn, "SELECT DISTINCT kategori FROM laporan_it ORDER BY kategori ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><i class="fas fa-table"></i> Riwayat Maintenance IT</h3>
    <a href="index.php?page=tambah_laporan" class="btn btn-success"><i class="fas fa-plus"></i> Tambah Laporan</a>
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
            $sql = "SELECT * FROM laporan_it WHERE 1=1 $where_clause ORDER BY tanggal DESC";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0):
                while($data = mysqli_fetch_array($result)):
                    $badge = ($data['status'] == 'Selesai') ? 'bg-success' : (($data['status'] == 'Proses') ? 'bg-warning text-dark' : 'bg-danger');
            ?>
            <tr>
                <td class="small"><?= date('d/m/y H:i', strtotime($data['tanggal'])) ?></td>
                <td><?= htmlspecialchars($data['unit_kerja']) ?></td>
                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($data['kategori']) ?></span></td>
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

<script>
// Logic View Modal
const vModal = document.getElementById('viewModal');
vModal.addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    document.getElementById('v-tanggal').innerText = b.getAttribute('data-tanggal');
    document.getElementById('v-petugas').innerText = b.getAttribute('data-petugas');
    document.getElementById('v-unit').innerText = b.getAttribute('data-unit');
    document.getElementById('v-kategori').innerText = b.getAttribute('data-kategori');
    document.getElementById('v-masalah').innerText = b.getAttribute('data-masalah');
    document.getElementById('v-tindakan').innerText = b.getAttribute('data-tindakan');

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