<?php

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../laporan/index.php');
    exit;
}

$id               = (int)($_POST['id'] ?? 0);
$nama_client      = trim($_POST['nama_client'] ?? '');
$cid              = trim($_POST['cid'] ?? '');
$alamat           = trim($_POST['alamat'] ?? '');
$waktu_komplain   = $_POST['waktu_komplain'] ?? '';
$gangguan         = trim($_POST['gangguan'] ?? '');
$merk_alat        = trim($_POST['merk_alat'] ?? '');
$efad_area        = trim($_POST['efad_area'] ?? '');
$waktu_penanganan = $_POST['waktu_penanganan'] ?? '';
$waktu_konfirmasi = $_POST['waktu_konfirmasi'] ?? '';
$teknisi          = trim($_POST['teknisi'] ?? '');
$penanganan       = trim($_POST['penanganan'] ?? '');
$keterangan       = trim($_POST['keterangan'] ?? '');

if ($id <= 0 || $nama_client === '' || $cid === '' || $alamat === '' || $waktu_komplain === '' || $gangguan === '') {
    die('Data tidak lengkap. <a href="../laporan/index.php">Kembali</a>');
}

$opsiKeterangan = ['TIKET TMT','PANTAU','TIKET','TIKET MD','GANTI PERANGKAT','CLOSE'];
if ($keterangan !== '' && !in_array($keterangan, $opsiKeterangan, true)) {
    $keterangan = 'LAINNYA';
}

function keMysql($val) {
    if (empty($val)) return null;
    return str_replace('T', ' ', $val) . ':00';
}

$wp = keMysql($waktu_penanganan);
$wk = keMysql($waktu_konfirmasi);
$waktu_komplain_fmt = keMysql($waktu_komplain);

$stmt = $conn->prepare("
    UPDATE komplain SET
        nama_client = ?, cid = ?, alamat = ?, waktu_komplain = ?, gangguan = ?,
        merk_alat = ?, efad_area = ?, waktu_penanganan = ?, waktu_konfirmasi = ?,
        teknisi = ?, penanganan = ?, keterangan = ?
    WHERE id = ?
");

$stmt->bind_param(
    'ssssssssssssi',
    $nama_client, $cid, $alamat, $waktu_komplain_fmt, $gangguan, $merk_alat, $efad_area,
    $wp, $wk, $teknisi, $penanganan, $keterangan, $id
);

if ($stmt->execute()) {
    header('Location: ../laporan/index.php?bulan=' . date('n', strtotime($waktu_komplain_fmt)) . '&tahun=' . date('Y', strtotime($waktu_komplain_fmt)) . '&sukses=2');
} else {
    echo 'Gagal memperbarui: ' . $stmt->error;
}
$stmt->close();
$conn->close();