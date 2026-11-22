<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');

$namaBulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$stmt = $conn->prepare("
    SELECT * FROM komplain
    WHERE MONTH(waktu_komplain) = ? AND YEAR(waktu_komplain) = ?
    ORDER BY waktu_komplain ASC
");
$stmt->bind_param('ii', $bulan, $tahun);
$stmt->execute();
$result = $stmt->get_result();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Komplain');

// === Judul bulan (baris 1) ===
$sheet->setCellValue('A1', 'DATA KOMPLAIN ' . $namaBulan[$bulan] . ' ' . $tahun);
$sheet->mergeCells('A1:M1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// === Header (baris 2) ===
$headers = [
    'NO','NAMA CLIENT','CID','ALAMAT','WAKTU KOMPLAIN','GANGGUAN',
    'MERK ALAT','EFAD AREA','WAKTU PENANGANAN','WAKTU KONFIRMASI',
    'TEKNISI','PENANGANAN','KETERANGAN'
];
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col . '2', $h);
    $col++;
}

// Style header
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => '2C3E50']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1C40F']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];
$sheet->getStyle('A2:M2')->applyFromArray($headerStyle);

// === Isi data (mulai baris 3) ===
$row = 3;
$no = 1;
while ($d = $result->fetch_assoc()) {
    $sheet->setCellValue("A$row", $no++);
    $sheet->setCellValue("B$row", $d['nama_client']);
    $sheet->setCellValue("C$row", $d['cid']);
    $sheet->setCellValue("D$row", $d['alamat']);
    $sheet->setCellValue("E$row", $d['waktu_komplain'] ? date('d/m/Y H:i', strtotime($d['waktu_komplain'])) . ' wita' : '');
    $sheet->setCellValue("F$row", $d['gangguan']);
    $sheet->setCellValue("G$row", $d['merk_alat']);
    $sheet->setCellValue("H$row", $d['efad_area']);
    $sheet->setCellValue("I$row", $d['waktu_penanganan'] ? date('d/m/Y H:i', strtotime($d['waktu_penanganan'])) . ' wita' : '');
    $sheet->setCellValue("J$row", $d['waktu_konfirmasi'] ? date('d/m/Y H:i', strtotime($d['waktu_konfirmasi'])) . ' wita' : '');
    $sheet->setCellValue("K$row", $d['teknisi']);
    $sheet->setCellValue("L$row", $d['penanganan']);
    $sheet->setCellValue("M$row", $d['keterangan']);
    $row++;
}

// Border untuk seluruh data
if ($row > 3) {
    $sheet->getStyle('A3:M' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
}

// Auto-size kolom
foreach (range('A','M') as $c) {
    $sheet->getColumnDimension($c)->setAutoSize(true);
}

// Freeze pane biar header tetap terlihat
$sheet->freezePane('A3');

// === Output ===
$writer = new Xlsx($spreadsheet);
$filename = 'Komplain_' . $namaBulan[$bulan] . '_' . $tahun . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;