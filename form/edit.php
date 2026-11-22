<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helper.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ../laporan/index.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM komplain WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die('Data tidak ditemukan. <a href="../laporan/index.php">Kembali</a>');
}

// Konversi untuk value input (Y-m-d H:i)
function toInputVal($dt) {
    if (empty($dt)) return '';
    return date('Y-m-d H:i', strtotime($dt));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Komplain</title>

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
    <h2>Edit Data Komplain</h2>
    <p class="subtitle">Perbarui data komplain untuk <strong><?= htmlspecialchars($data['nama_client']) ?></strong> (CID: <?= htmlspecialchars($data['cid']) ?>).</p>

    <form action="../proses/update.php" method="POST">
        <input type="hidden" name="id" value="<?= $data['id'] ?>">

        <div class="form-grid">

            <!-- ==== KOLOM KIRI ==== -->
            <div class="form-group">
                <label>Nama Client <span class="required">*</span></label>
                <input type="text" name="nama_client" value="<?= htmlspecialchars($data['nama_client']) ?>" required>
            </div>

            <div class="form-group">
                <label>CID <span class="required">*</span></label>
                <input type="text" name="cid" value="<?= htmlspecialchars($data['cid']) ?>" required>
            </div>

            <!-- ==== FULL WIDTH ==== -->
            <div class="form-group full-width">
                <label>Alamat <span class="required">*</span></label>
                <textarea name="alamat" required><?= htmlspecialchars($data['alamat']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Waktu Komplain <span class="required">*</span></label>
                <div class="input-waktu">
                    <input type="text"
                           name="waktu_komplain"
                           id="waktu_komplain"
                           value="<?= toInputVal($data['waktu_komplain']) ?>"
                           placeholder="Pilih tanggal & jam..."
                           required
                           readonly>
                    <!-- <span class="icon-cal">📅</span> -->
                </div>
            </div>

            <div class="form-group">
                <label>Gangguan <span class="required">*</span></label>
                <select name="gangguan" required>
                    <?php
                    $opsiGangguan = ['WIFI LOSS','Kabel Putus','Perangkat Rusak','Gangguan Massal','Lainnya'];
                    foreach ($opsiGangguan as $opt):
                        $sel = ($data['gangguan'] === $opt) ? 'selected' : '';
                    ?>
                        <option <?= $sel ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Merk Alat</label>
                <select name="merk_alat">
                    <option value="">-- Pilih Merk --</option>
                    <?php
                    $opsiMerk = ['ZTE','CDATA'];
                    foreach ($opsiMerk as $opt):
                        $sel = ($data['merk_alat'] === $opt) ? 'selected' : '';
                    ?>
                        <option <?= $sel ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>LEAD Area</label>
                <input type="text" name="efad_area" value="<?= htmlspecialchars($data['efad_area'] ?? '') ?>">
            </div>

            <!-- ==== KOLOM KANAN ==== -->
            <div class="form-group">
                <label>Waktu Penanganan</label>
                <div class="input-waktu">
                    <input type="text"
                           name="waktu_penanganan"
                           id="waktu_penanganan"
                           value="<?= toInputVal($data['waktu_penanganan']) ?>"
                           placeholder="Pilih tanggal & jam..."
                           required
                           readonly>
                    <!-- <span class="icon-cal">📅</span> -->
                </div>
            </div>

            <div class="form-group">
                <label>Waktu Konfirmasi</label>
                <div class="input-waktu">
                    <input type="text"
                           name="waktu_konfirmasi"
                           id="waktu_konfirmasi"
                           value="<?= toInputVal($data['waktu_konfirmasi']) ?>"
                           placeholder="Pilih tanggal & jam..."
                           required
                           readonly>
                    <!-- <span class="icon-cal">📅</span> -->
                </div>
            </div>

            <div class="form-group">
                <label>Teknisi/NOC</label>
                <select name="teknisi">
                    <option value="">-- Pilih Teknisi/NOC --</option>
                    <?php
                    $opsiTeknisi = ['ZTE','CDATA', 'apaya'];
                    foreach ($opsiTeknisi as $opt):
                        $sel = ($data['teknisi'] === $opt) ? 'selected' : '';
                    ?>
                        <option <?= $sel ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Penanganan</label>
                <select name="penanganan">
                    <option value="">-- Pilih Penanganan --</option>
                    <?php
                    $opsiPenanganan = ['Lapangan','MD','Restart','Ganti Perangkat'];
                    foreach ($opsiPenanganan as $opt):
                        $sel = ($data['penanganan'] === $opt) ? 'selected' : '';
                    ?>
                        <option <?= $sel ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Keterangan</label>
                <select name="keterangan">
                    <option value="">-- Pilih Keterangan --</option>
                    <?php
                    $opsiKeterangan = ['TIKET TMT','PANTAU','TIKET','TIKET MD','GANTI PERANGKAT','CLOSE'];
                    foreach ($opsiKeterangan as $opt):
                        $sel = ($data['keterangan'] === $opt) ? 'selected' : '';
                    ?>
                        <option <?= $sel ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="../laporan/index.php" class="btn btn-secondary">← Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
flatpickr.localize(flatpickr.l10ns.id);

const config = {
    enableTime: true,
    time_24hr: true,
    dateFormat: "Y-m-d H:i",
    altInput: true,
    altFormat: "d F Y H:i",
    allowInput: false,
    defaultHour: 8,
    minuteIncrement: 1,
};

flatpickr("#waktu_komplain", config);
flatpickr("#waktu_penanganan", config);
flatpickr("#waktu_konfirmasi", config);
</script>

</body>
</html>