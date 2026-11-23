<?php
header('Content-Type: application/json');

$cid = isset($_GET['cid']) ? trim($_GET['cid']) : '';

if (empty($cid)) {
    echo json_encode(['status' => 'error', 'message' => 'CID kosong']);
    exit;
}

$url = "https://hd.trikamedia.net.id/customer/table_customer";

$postData = http_build_query([
    'draw' => '1',
    'columns[0][data]' => 'DT_RowIndex',
    'columns[0][name]' => 'DT_RowIndex',
    'columns[0][searchable]' => 'false',
    'columns[0][orderable]' => 'false',
    'columns[0][search][value]' => '',
    'columns[0][search][regex]' => 'false',
    
    'columns[1][data]' => 'customer_id',
    'columns[1][name]' => 'customer_id',
    'columns[1][searchable]' => 'true',
    'columns[1][orderable]' => 'true',
    'columns[1][search][value]' => $cid, 
    'columns[1][search][regex]' => 'false',
    
    'columns[2][data]' => 'name',
    'columns[2][name]' => 'name',
    'columns[2][searchable]' => 'true',
    'columns[2][orderable]' => 'true',
    'columns[2][search][value]' => '',
    'columns[2][search][regex]' => 'false',
    
    'columns[3][data]' => 'address',
    'columns[3][name]' => 'address',
    'columns[3][searchable]' => 'true',
    'columns[3][orderable]' => 'true',
    'columns[3][search][value]' => '',
    'columns[3][search][regex]' => 'false',
    
    'order[0][column]' => '0',
    'order[0][dir]' => 'asc',
    'start' => '0',
    'length' => '10', 
    'search[value]' => $cid, 
    'search[regex]' => 'false',
    'filter' => 'name',
    'parameter' => '',
    'id_status' => '',
    'id_plan' => '',
    'id_merchant' => ''
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$cookie_login = "XSRF-TOKEN=eyJpdiI6IkRvcVhlVjFoNzJmL1V1VGxIU1pmclE9PSIsInZhbHVlIjoic0lqcDJMdHVzMlQzY0xyWE52bzQvWkhvYjAzdU1zOVVwNDRERWtHY2V5Y2ovNGNremdFYXphM3gxNDBoMjh1ZitaY0gzRmRoUzZHM0dJY3BwTXBiYmVWWUdXcEJwSyt5b2NuaFNWWTkzS1F2WklnRTZSS2xqQk5JZ2ZuUEVYWVAiLCJtYWMiOiI3ZmZhZTQ3NTI3ODliMTE5MTQ2MjcxNWU3YjA4YTIyYWI3ZGZiZDI5OGE4NWQ0N2I1YTEwYjlhMWJiYjhmMGJjIiwidGFnIjoiIn0%3D; billing_session=eyJpdiI6InhNaXErNTJRM1pxU1A0VS9kb2pIK1E9PSIsInZhbHVlIjoiTjlwekJlMGpDTnVUTGtDU29NczZhbitMVFlvMlN4MWc0Wkx1UCt3djIvME5ncHZkOTgweGEwQlB4ckdQWWNpdG1mbktXdEZUZFQ1bTl1UVNUNUtUN1QxZFY5eXJybitEaGRwKytFaGRDOG15RTFTVHlzdlU3U1U5NnE0bFZ6OVciLCJtYWMiOiIyMTBjNTY1OTYwNTdiY2VmYzRhNmE0OTNmMWQ4MjEyNWNhMzQ4NDMwNTI0ZTViMzJhZmIxYzAxMDFlYWRhMjI0IiwidGFnIjoiIn0%3D";

preg_match('/XSRF-TOKEN=([^;]+)/', $cookie_login, $matches);
$xsrf_token = isset($matches[1]) ? urldecode($matches[1]) : '';

$headers = [
    'Accept: application/json, text/javascript, */*; q=0.01',
    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
    'X-Requested-With: XMLHttpRequest',
    'Referer: https://hd.trikamedia.net.id/customer',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'X-XSRF-TOKEN: ' . $xsrf_token
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_COOKIE, $cookie_login);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200 && $response) {
    $data = json_decode($response, true);
    
    if (isset($data['data']) && count($data['data']) > 0) {
        // Cari apakah ada CID yang PERSIS SAMA (karena search DataTables bisa ngawur)
        $found = null;
        foreach ($data['data'] as $row) {
            if (isset($row['customer_id']) && $row['customer_id'] == $cid) {
                $found = $row;
                break;
            }
        }
        
        // Jika ketemu yang persis, pakai itu. Jika tidak, ambil hasil pertama.
        $pelanggan = $found ? $found : $data['data'][0];
        
        echo json_encode([
            'status' => 'success',
            'nama'   => $pelanggan['name'] ?? '-',
            'alamat' => $pelanggan['address'] ?? '-'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data CID tidak ditemukan.']);
    }
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Gagal terhubung (HTTP ' . $http_code . ')'
    ]);
}