<?php
header('Content-Type: application/json');

$cid = isset($_GET['cid']) ? trim($_GET['cid']) : '';

if (empty($cid)) {
    echo json_encode(['status' => 'error', 'message' => 'CID kosong']);
    exit;
}

// URL Endpoint
$url = "https://hd.trikamedia.net.id/customer/table_customer";

// Menyusun body POST (x-www-form-urlencoded) meniru persis request DataTables
// Kita ubah 'search[value]' menjadi CID yang dicari.
$postData = http_build_query([
    'draw' => '1',
    'columns[0][data]' => 'DT_RowIndex',
    'columns[1][data]' => 'customer_id',
    'columns[1][search][value]' => $cid, // Kita coba cari berdasarkan customer_id (CID)
    'columns[2][data]' => 'name',
    'columns[2][search][value]' => $cid, // Cadangan jika CID masuk ke kolom name
    'columns[3][data]' => 'address',
    'order[0][column]' => '0',
    'order[0][dir]' => 'asc',
    'start' => '0',
    'length' => '10', // Cukup ambil 10 data
    'search[value]' => $cid, // Parameter pencarian global
    'search[regex]' => 'false'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Bypass SSL lokal

// Menyusun Header persis seperti request browser Anda
$headers = [
    'Accept: application/json, text/javascript, */*; q=0.01',
    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
    'X-Requested-With: XMLHttpRequest',
    'Referer: https://hd.trikamedia.net.id/customer'
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Karena ada indikasi "credentials: include", kita set cookie jar (opsional)
// Jika web utama butuh login, Anda mungkin perlu memasukkan cookie session di sini.
// curl_setopt($ch, CURLOPT_COOKIE, "nama_cookie=value_cookie");

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200 && $response) {
    $data = json_decode($response, true);
    
    // DataTables biasanya mengembalikan objek dengan key 'data'
    if (isset($data['data']) && count($data['data']) > 0) {
        // Ambil data pertama yang paling cocok
        $pelanggan = $data['data'][0];
        
        echo json_encode([
            'status' => 'success',
            'nama'   => $pelanggan['name'] ?? '-',
            'alamat' => $pelanggan['address'] ?? '-'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data CID tidak ditemukan di server utama.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal terhubung ke server utama (HTTP ' . $http_code . ')']);
}