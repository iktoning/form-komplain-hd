<?php

require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../form/input.php');
    exit;
}

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

// Whitelist keterangan
$opsiKeterangan = ['TIKET TMT','PANTAU','TIKET','TIKET MD','GANTI PERANGKAT','CLOSE'];
if ($keterangan !== '' && !in_array($keterangan, $opsiKeterangan, true)) {
    $keterangan = 'LAINNYA';
}

// Validasi dasar
if ($nama_client === '' || $cid === '' || $alamat === '' || $waktu_komplain === '' || $gangguan === '') {
    die('Data wajib belum lengkap. <a href="../form/input.php">Kembali</a>');
}

function keMysql($val) {
    if (empty($val)) return null;
    return str_replace('T', ' ', $val) . ':00';
}

$stmt = $conn->prepare("
    INSERT INTO komplain
    (nama_client, cid, alamat, waktu_komplain, gangguan, merk_alat, efad_area,
     waktu_penanganan, waktu_konfirmasi, teknisi, penanganan, keterangan)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$wp = keMysql($waktu_penanganan);
$wk = keMysql($waktu_konfirmasi);
$waktu_komplain_fmt = keMysql($waktu_komplain);

$stmt->bind_param(
    'ssssssssssss',
    $nama_client, $cid, $alamat, $waktu_komplain_fmt, $gangguan, $merk_alat, $efad_area,
    $wp, $wk, $teknisi, $penanganan, $keterangan
);

if ($stmt->execute()) {
    header('Location: ../laporan/index.php?bulan=' . date('n', strtotime($waktu_komplain_fmt)) . '&tahun=' . date('Y', strtotime($waktu_komplain_fmt)) . '&sukses=1');
} else {
    echo 'Gagal menyimpan: ' . $stmt->error;
}
$stmt->close();
$conn->close();