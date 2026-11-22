<?php
// laporan/export.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helper.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard\Text;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard\CellIs;

// ==========================================
// 1. TANGKAP PARAMETER FILTER (Sama seperti web)
// ==========================================
$bulan         = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('n');
$tahun         = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');
$filter_keterangan = isset($_GET['keterangan']) && is_array($_GET['keterangan']) ? $_GET['keterangan'] : [];

// ==========================================
// 2. QUERY KE DATABASE
// ==========================================
$sql = "SELECT * FROM komplain WHERE MONTH(waktu_komplain) = ? AND YEAR(waktu_komplain) = ?";
$params = [$bulan, $tahun];
$types  = 'ii';

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

$namaBulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

// ==========================================
// 3. SETUP SPREADSHEET & STYLE DASAR
// ==========================================
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Komplain');

// Set Default Font Times New Roman 12pt untuk SELURUH sheet
$spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(12);

// ==== BARIS 1: JUDUL (Merge Cell) ====
$sheet->setCellValue('A1', 'DATA KOMPLAIN ' . strtoupper($namaBulan[$bulan]) . ' ' . $tahun);
$sheet->mergeCells('A1:M1'); // Sesuaikan jika ada tambahan kolom
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getRowDimension(1)->setRowHeight(25);

// ==== BARIS 2: HEADER KOLOM ====
$headers = [
    'NO', 'NAMA CLIENT', 'CID', 'ALAMAT', 'WAKTU KOMPLAIN', 'GANGGUAN',
    'MERK ALAT', 'LEAD AREA', 'WAKTU PENANGANAN', 'WAKTU KONFIRMASI',
    'TEKNISI', 'PENANGANAN', 'KETERANGAN'
];
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col . '2', $h);
    $col++;
}

// Style Header (Background Kuning, Text Bold)
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1C40F']],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER, 
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true
    ],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];
$sheet->getStyle('A2:M2')->applyFromArray($headerStyle);
$sheet->getRowDimension(2)->setRowHeight(30);

// ==========================================
// 4. MASUKKAN DATA & APPLY STYLE
// ==========================================
$row = 3;
$no = 1;

// Array warna background (Fill) untuk Conditional Formatting Kolom Keterangan (Kolom M)
// Format: 'TeksKeterangan' => 'KodeHexWarna'
$warnaKeterangan = [
    'TIKET TMT'       => 'FFF3CD', // Kuning muda
    'PANTAU'          => 'D1ECF1', // Biru muda
    'TIKET'           => 'D4EDDA', // Hijau muda
    'TIKET MD'        => 'F8D7DA', // Merah muda
    'GANTI PERANGKAT' => 'E2D9F3', // Ungu muda
    'CLOSE'           => 'D6D8DB', // Abu-abu
    'SUDAH NORMAL'    => 'D6D8DB', // Abu-abu
];

while ($d = $result->fetch_assoc()) {
    $sheet->setCellValue("A$row", $no++);
    $sheet->setCellValue("B$row", $d['nama_client']);
    $sheet->setCellValue("C$row", $d['cid']);
    $sheet->setCellValue("D$row", $d['alamat']);
    
    // Format Tanggal Indonesia
    $sheet->setCellValue("E$row", tanggalIndo($d['waktu_komplain']));
    $sheet->setCellValue("F$row", $d['gangguan']);
    $sheet->setCellValue("G$row", $d['merk_alat']);
    $sheet->setCellValue("H$row", $d['efad_area']);
    $sheet->setCellValue("I$row", tanggalIndo($d['waktu_penanganan']));
    $sheet->setCellValue("J$row", tanggalIndo($d['waktu_konfirmasi']));
    $sheet->setCellValue("K$row", $d['teknisi']);
    $sheet->setCellValue("L$row", $d['penanganan']);
    $sheet->setCellValue("M$row", $d['keterangan']);
    
    $row++;
}

$lastRow = $row - 1;

// ==== Style untuk SELURUH DATA TABEL ====
if ($lastRow >= 3) {
    $dataRange = 'A3:M' . $lastRow;
    
    // Border & Alignment
    $sheet->getStyle($dataRange)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_TOP,
            'wrapText' => true // Agar teks panjang turun ke bawah
        ]
    ]);

    // Rata Tengah untuk Kolom Tertentu
    $sheet->getStyle('A3:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // NO
    $sheet->getStyle('C3:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // CID
    $sheet->getStyle('M3:M' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // KETERANGAN

     // ==========================================
    // 5. CONDITIONAL FORMATTING UNTUK WARNA SELURUH BARIS
    // ==========================================
    $conditionalStyles = [];
    
    foreach ($warnaKeterangan as $text => $hexColor) {
        $conditional = new Conditional();
        
        // Gunakan tipe Expression (Formula) yang didukung semua versi
        $conditional->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_EXPRESSION);
        
        // Kunci kolom M ($M3), agar pengecekan selalu merujuk ke kolom Keterangan
        $conditional->setConditions(["=\$M3=\"$text\""]); 
        
        $conditional->getStyle()->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($hexColor);
                    
        $conditionalStyles[] = $conditional;
    }

    // Terapkan ke SELURUH BARIS (Kolom A sampai M)
    $sheet->getStyle('A3:M' . $lastRow)->setConditionalStyles($conditionalStyles);
}

// ==========================================
// 6. PENGATURAN LEBAR KOLOM & AUTOFILTER
// ==========================================
// Auto-filter agar user bisa memfilter di Excel
$sheet->setAutoFilter('A2:M2');

// Atur lebar kolom secara manual agar proporsional
$sheet->getColumnDimension('A')->setWidth(5);   // NO
$sheet->getColumnDimension('B')->setWidth(20);  // NAMA
$sheet->getColumnDimension('C')->setWidth(15);  // CID
$sheet->getColumnDimension('D')->setWidth(25);  // ALAMAT
$sheet->getColumnDimension('E')->setWidth(22);  // WAKTU KOMPLAIN
$sheet->getColumnDimension('F')->setWidth(18);  // GANGGUAN
$sheet->getColumnDimension('G')->setWidth(15);  // MERK
$sheet->getColumnDimension('H')->setWidth(15);  // LEAD AREA
$sheet->getColumnDimension('I')->setWidth(22);  // WKT PENANGANAN
$sheet->getColumnDimension('J')->setWidth(22);  // WKT KONFIRMASI
$sheet->getColumnDimension('K')->setWidth(15);  // TEKNISI
$sheet->getColumnDimension('L')->setWidth(18);  // PENANGANAN
$sheet->getColumnDimension('M')->setWidth(22);  // KETERANGAN

// Freeze pane agar saat di-scroll header tetap terlihat
$sheet->freezePane('A3');

// ==========================================
// 7. PROSES DOWNLOAD
// ==========================================
$writer = new Xlsx($spreadsheet);
$filename = 'DATA KOMPLAIN ' . strtoupper($namaBulan[$bulan]) . ' ' . $tahun . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;