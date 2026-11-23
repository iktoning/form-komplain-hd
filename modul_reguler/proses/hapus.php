<?php

require_once __DIR__ . '/../../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ../laporan/index.php');
    exit;
}

// Ambil bulan-tahun dulu untuk redirect
$stmt = $conn->prepare("SELECT waktu_komplain FROM komplain WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$bulan = $row ? (int)date('n', strtotime($row['waktu_komplain'])) : (int)date('n');
$tahun = $row ? (int)date('Y', strtotime($row['waktu_komplain'])) : (int)date('Y');

$del = $conn->prepare("DELETE FROM komplain WHERE id = ?");
$del->bind_param('i', $id);
$del->execute();

header("Location: ../laporan/index.php?bulan=$bulan&tahun=$tahun&sukses=3");
exit;