<?php

function tanggalIndo($datetime, $withWaktu = true) {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00' || trim($datetime) === '') {
        return '';
    }

    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    $ts = strtotime($datetime);
    if ($ts === false) {
        return $datetime; 
    }

    $hari = date('d', $ts);
    $bln  = $bulan[(int)date('n', $ts)];
    $thn  = date('Y', $ts);

    if (!$withWaktu) return "$hari $bln $thn";

    $jam   = date('H', $ts);
    $menit = date('i', $ts);
    return "$hari $bln $thn $jam:$menit";
}

/**
 * Mapping keterangan ke class badge
 */
function badgeClass($keterangan) {
    $map = [
        'TIKET TMT'       => 'badge-tmt',
        'PANTAU'          => 'badge-pantau',
        'TIKET'           => 'badge-tiket',
        'TIKET MD'        => 'badge-tiket-md',
        'GANTI PERANGKAT' => 'badge-ganti',
        'CLOSE'           => 'badge-close',
    ];
    return $map[$keterangan] ?? 'badge-lainnya';
}
