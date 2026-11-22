<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helper.php';

$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');

$stmt = $conn->prepare("
    SELECT * FROM komplain
    WHERE MONTH(waktu_komplain) = ? AND YEAR(waktu_komplain) = ?
    ORDER BY waktu_komplain ASC
");
$stmt->bind_param('ii', $bulan, $tahun);
$stmt->execute();
$result = $stmt->get_result();

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
    <link rel="stylesheet" href="../assets/style.css">
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

    <form class="toolbar" method="GET">
        <div class="tool-item">
            <label>Bulan</label>
            <select name="bulan">
                <?php foreach ($namaBulan as $i => $nm): ?>
                    <option value="<?= $i ?>" <?= $i === $bulan ? 'selected' : '' ?>><?= $nm ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="tool-item">
            <label>Tahun</label>
            <input type="number" name="tahun" value="<?= $tahun ?>" min="2000" max="2100" style="width:90px;">
        </div>
        <div class="tool-item">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-info" style="padding:8px 18px;">Filter</button>
        </div>

        <div class="spacer"></div>

        <div class="tool-item">
            <label>&nbsp;</label>
            <a class="btn btn-export" href="export.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>">Export Excel</a>
        </div>
        <div class="tool-item">
            <label>&nbsp;</label>
            <a class="btn btn-primary" href="../form/input.php">+ Tambah Komplain</a>
        </div>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
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
                    <th>TEKNISI/NOC</th>
                    <th>PENANGANAN</th>
                    <th>KETERANGAN</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows === 0): ?>
                <tr><td colspan="14" class="empty-row">Belum ada data komplain pada bulan ini.</td></tr>
            <?php else: ?>
                <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_client']) ?></td>
                        <td><?= htmlspecialchars($row['cid']) ?></td>
                        <td><?= htmlspecialchars($row['alamat']) ?></td>
                        <td><?= tanggalIndo($row['waktu_komplain']) ?> WITA</td>
                        <td><?= htmlspecialchars($row['gangguan']) ?></td>
                        <td><?= htmlspecialchars($row['merk_alat'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['efad_area'] ?? '') ?></td>
                        <td><?= tanggalIndo($row['waktu_penanganan']) ?><?= $row['waktu_penanganan'] ? ' WITA' : '' ?></td>
                        <td><?= tanggalIndo($row['waktu_konfirmasi']) ?><?= $row['waktu_konfirmasi'] ? ' WITA' : '' ?></td>
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const notif = document.getElementById('notif-sukses');
    if (notif) {
        // Hilangkan otomatis setelah 3 detik dengan animasi fade
        setTimeout(() => {
            notif.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            notif.style.opacity = '0';
            notif.style.transform = 'translateY(-10px)';
            setTimeout(() => notif.remove(), 500);
        }, 3000);

        // Hapus parameter ?sukses= dari URL tanpa reload
        const url = new URL(window.location.href);
        url.searchParams.delete('sukses');
        window.history.replaceState({}, '', url);
    }
});
</script>

</body>
</html>