<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/helper.php';

$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');
$filter_keterangan = isset($_GET['keterangan']) && is_array($_GET['keterangan']) ? $_GET['keterangan'] : [];

// ==== Query dinamis ====
$sql = "SELECT * FROM komplain WHERE MONTH(waktu_komplain) = ? AND YEAR(waktu_komplain) = ?";
$params = [$bulan, $tahun];
$types  = 'ii';

// Jika ada yang dicentang, masukkan ke query
if (!empty($filter_keterangan)) {
    $placeholders = implode(',', array_fill(0, count($filter_keterangan), '?'));
    $sql .= " AND keterangan IN ($placeholders)";
    foreach ($filter_keterangan as $k) {
        $params[] = $k;
        $types .= 's';
    }
}

$sql .= " ORDER BY waktu_komplain ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// ==== Opsi unik untuk Checkbox Keterangan ====
$resKet = $conn->query("SELECT DISTINCT keterangan FROM komplain WHERE keterangan IS NOT NULL AND keterangan <> '' ORDER BY keterangan ASC");
$opsiKeterangan = [];
while ($r = $resKet->fetch_assoc()) $opsiKeterangan[] = $r['keterangan'];

$namaBulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Komplain</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
<div class="container" style="max-width: 1600px;">
    <h2>Laporan Komplain — <?= $namaBulan[$bulan] . ' ' . $tahun ?></h2>
    <p class="subtitle">Total data: <strong><?= $result->num_rows ?></strong> komplain pada bulan ini.</p>

    <?php if (isset($_GET['sukses'])): ?>
        <div class="alert alert-success" id="notif-sukses">
            <?php
                echo match($_GET['sukses']) {
                    '1' => '✅ Data berhasil disimpan.',
                    '2' => '✅ Data berhasil diperbarui.',
                    '3' => '🗑️ Data berhasil dihapus.',
                    default => '✅ Berhasil.',
                };
            ?>
        </div>
    <?php endif; ?>

    <!-- ==== Toolbar Atas (Hanya Bulan, Tahun, dan Aksi) ==== -->
    <form method="GET" id="form-filter">
        <div class="toolbar" style="align-items: center;">
            <div class="tool-item">
                <label>Bulan</label>
                <select name="bulan" onchange="this.form.submit()">
                    <?php foreach ($namaBulan as $i => $nm): ?>
                        <option value="<?= $i ?>" <?= $i === $bulan ? 'selected' : '' ?>><?= $nm ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="tool-item">
                <label>Tahun</label>
                <input type="number" name="tahun" value="<?= $tahun ?>" min="2000" max="2100" style="width:90px;" onchange="this.form.submit()">
            </div>

            <div class="spacer"></div>

            <div class="tool-item">
                <label>&nbsp;</label>
                <a class="btn btn-export" href="export.php?<?= http_build_query([
                    'bulan' => $bulan, 'tahun' => $tahun, 'keterangan' => $filter_keterangan
                ]) ?>">Export Excel</a>
            </div>
            <div class="tool-item">
                <label>&nbsp;</label>
                <a class="btn btn-primary" href="../form/input.php">+ Tambah Komplain</a>
            </div>
        </div>

        <!-- ==== Tabel ==== -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <!-- Baris 1: Judul Kolom -->
                    <tr>
                        <th>NO</th>
                        <th>NAMA CLIENT</th>
                        <th>CID</th>
                        <th>ALAMAT</th>
                        <th>WAKTU KOMPLAIN</th>
                        <th>GANGGUAN</th>
                        <th>MERK ALAT</th>
                        <th>LEAD AREA</th>
                        <th>WAKTU PENANGANAN</th>
                        <th>WAKTU KONFIRMASI</th>
                        <th>TEKNISI</th>
                        <th>PENANGANAN</th>
                        <th>
                            <div class="th-with-filter">
                                <span>KETERANGAN</span>
                                <div class="dropdown-filter">
                                    <button type="button" class="dropdown-btn" onclick="toggleDropdown(event)">▼</button>
                                    <div class="dropdown-content" id="dropdown-keterangan">
                                        <!-- Checkbox akan dimuat di sini via PHP -->
                                        <?php foreach ($opsiKeterangan as $opt): ?>
                                            <?php 
                                                $isChecked = in_array($opt, $filter_keterangan); 
                                            ?>
                                            <label class="dropdown-item">
                                                <input type="checkbox" name="keterangan[]" value="<?= htmlspecialchars($opt) ?>" 
                                                       <?= $isChecked ? 'checked' : '' ?>
                                                       onchange="document.getElementById('form-filter').submit()">
                                                <?= htmlspecialchars($opt) ?>
                                            </label>
                                        <?php endforeach; ?>
                                        <?php if(empty($opsiKeterangan)): ?>
                                            <span style="padding: 5px; font-size:11px; color:#999;">Tidak ada opsi</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows === 0): ?>
                    <tr><td colspan="14" class="empty-row">Tidak ada data yang cocok dengan filter.</td></tr>
                <?php else: ?>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_client']) ?></td>
                            <td><?= htmlspecialchars($row['cid']) ?></td>
                            <td><?= htmlspecialchars($row['alamat']) ?></td>
                            <td><?= tanggalIndo($row['waktu_komplain']) ?> wita</td>
                            <td><?= htmlspecialchars($row['gangguan']) ?></td>
                            <td><?= htmlspecialchars($row['merk_alat'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['efad_area'] ?? '') ?></td>
                            <td><?= tanggalIndo($row['waktu_penanganan']) ?><?= $row['waktu_penanganan'] ? ' wita' : '' ?></td>
                            <td><?= tanggalIndo($row['waktu_konfirmasi']) ?><?= $row['waktu_konfirmasi'] ? ' wita' : '' ?></td>
                            <td><?= htmlspecialchars($row['teknisi'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['penanganan'] ?? '') ?></td>
                            <td>
                                <?php if (!empty($row['keterangan'])): ?>
                                    <span class="badge <?= badgeClass($row['keterangan']) ?>">
                                        <?= htmlspecialchars($row['keterangan']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="../form/edit.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>

<script>
// Notif auto-dismiss
document.addEventListener('DOMContentLoaded', function () {
    const notif = document.getElementById('notif-sukses');
    if (notif) {
        setTimeout(() => {
            notif.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            notif.style.opacity = '0';
            notif.style.transform = 'translateY(-10px)';
            setTimeout(() => notif.remove(), 500);
        }, 3000);
        const url = new URL(window.location.href);
        url.searchParams.delete('sukses');
        window.history.replaceState({}, '', url);
    }
});

// Fungsi Toggle Dropdown
function toggleDropdown(event) {
    event.stopPropagation(); // Mencegah klik menyebar
    const dropdown = document.getElementById('dropdown-keterangan');
    // Toggle visibility
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Tutup dropdown jika user klik di luar area
window.onclick = function(event) {
    if (!event.target.matches('.dropdown-btn')) {
        const dropdowns = document.getElementsByClassName("dropdown-content");
        for (let i = 0; i < dropdowns.length; i++) {
            let openDropdown = dropdowns[i];
            if (openDropdown.style.display === 'block') {
                openDropdown.style.display = 'none';
            }
        }
    }
}
</script>
</body>
</html>