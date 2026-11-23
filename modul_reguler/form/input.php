<?php

require_once __DIR__ . '/../../config/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Komplain</title>

     <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
<div class="container">
    <h2>Form Input Komplain</h2>
    <!-- <p class="subtitle">Isi data komplain pelanggan dengan lengkap. Kolom bertanda <span style="color:#e74c3c">*</span> wajib diisi.</p> -->

    <form action="../proses/simpan.php" method="POST">
        <div class="form-grid">

            <!-- ==== KOLOM KIRI ==== -->
            <div class="form-group">
                <label>CID <span class="required">*</span></label>
                <input type="text" name="cid" id="input-cid" required onblur="fetchDataPelanggan()" autofocus>
                <small id="loading-text" style="color: blue; display: none;">Mencari data...</small>
            </div>
            
            <div class="form-group">
                <label>Nama Client <span class="required">*</span></label>
                <input type="text" name="nama_client" id="input-nama" required>
            </div>

            <!-- ==== FULL WIDTH ==== -->
            <div class="form-group full-width">
                <label>Alamat <span class="required">*</span></label>
                <textarea name="alamat" id="input-alamat" required></textarea>
            </div>

            <div class="form-group">
                <label>Waktu Komplain <span class="required">*</span></label>
                <div class="input-waktu">
                    <input type="text"
                           name="waktu_komplain"
                           id="waktu_komplain"
                           placeholder="Pilih tanggal & jam..."
                           required
                           readonly>
                    <!-- <span class="icon-cal">📅</span> -->
                </div>
            </div>

            <div class="form-group">
                <label>Gangguan <span class="required">*</span></label>
                <select name="gangguan" required>
                    <option value="">-- Pilih Gangguan --</option>
                    <option>WIFI LOSS</option>
                    <option>Kabel Putus</option>
                    <option>Perangkat Rusak</option>
                    <option>Gangguan Massal</option>
                    <option>Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label>Merk Alat</label>
                <select name="merk_alat">
                    <option value="">-- Pilih Merk --</option>
                    <option>ZTE</option>
                    <option>CDATA</option>
                    <!-- <option>Huawei</option>
                    <option>Tp-Link</option> -->
                </select>
            </div>

            <div class="form-group">
                <label>LEAD Area</label>
                <select name="lead_area">
                    <option value="">-- Pilih Lead Area --</option>
                    <option>Sumariana</option>
                    <option>Sudiartana</option>
                </select>
            </div>

            <!-- ==== KOLOM KANAN ==== -->
            <div class="form-group">
                <label>Waktu Penanganan</label>
                <div class="input-waktu">
                    <input type="text"
                           name="waktu_penanganan"
                           id="waktu_penanganan"
                           placeholder="Pilih tanggal & jam..."
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
                           placeholder="Pilih tanggal & jam..."
                           readonly>
                    <!-- <span class="icon-cal">📅</span> -->
                </div>
            </div>

            <div class="form-group">
                <label>Teknisi/NOC</label>
                <select name="teknisi">
                    <option value="">-- Pilih Teknisi --</option>
                    <option>Dwiya</option>
                    <option>Andi</option>
                </select>
            </div>

            <div class="form-group">
                <label>Penanganan</label>
                <select name="penanganan">
                    <option value="">-- Pilih Penanganan --</option>
                    <option>Lapangan</option>
                    <option>MD</option>
                    <option>Restart</option>
                    <option>Ganti Perangkat</option>
                </select>
            </div>

            <div class="form-group">
                <label>Keterangan</label>
                <select name="keterangan">
                    <option value="">-- Pilih Keterangan --</option>
                    <option>TIKET TMT</option>
                    <option>PANTAU</option>
                    <option>TIKET</option>
                    <option>TIKET MD</option>
                    <option>GANTI PERANGKAT</option>
                    <option>CLOSE</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="../laporan/index.php" class="btn btn-secondary">← Kembali ke Laporan</a>
            <button type="submit" class="btn btn-primary">Simpan Komplain</button>
        </div>
    </form>
</div>

<script>
// Inisialisasi Flatpickr untuk 3 input waktu
flatpickr.localize(flatpickr.l10ns.id); // Bahasa Indonesia

const config = {
    enableTime: true,          // aktifkan pilih jam
    time_24hr: true,           // format 24 jam
    dateFormat: "Y-m-d H:i",   // format yang dikirim ke server: 2026-11-21 09:41
    altInput: true,            // tampilan alternatif
    altFormat: "d F Y H:i",    // tampilan ke user: 21 November 2026 09:41
    allowInput: false,
    defaultHour: 8,
    minuteIncrement: 1,
};

flatpickr("#waktu_komplain", config);
flatpickr("#waktu_penanganan", config);
flatpickr("#waktu_konfirmasi", config);

// GET data pelanggan berdasarkan CID
function fetchDataPelanggan() {
    const cid = document.getElementById('input-cid').value.trim();
    const loadingText = document.getElementById('loading-text');
    const inputNama = document.getElementById('input-nama');
    const inputAlamat = document.getElementById('input-alamat');

    if (cid === '') return;

    loadingText.style.display = 'block';

    // Coba panggil API
    fetch(`../proses/get_pelanggan.php?cid=${encodeURIComponent(cid)}`)
        .then(async response => {
            // Cek jika HTTP status bukan 200 (misal 500, 404)
            if (!response.ok) {
                const textError = await response.text(); // Ambil pesan error aslinya
                throw new Error(`HTTP Error ${response.status}: ${textError.substring(0, 100)}`);
            }
            return response.json(); // Ubah ke JSON
        })
        .then(data => {
            loadingText.style.display = 'none';
            
            if (data.status === 'success') {
                inputNama.value = data.nama;
                inputAlamat.value = data.alamat;
                
                inputNama.style.backgroundColor = '#e8f4ec';
                inputAlamat.style.backgroundColor = '#e8f4ec';
                setTimeout(() => {
                    inputNama.style.backgroundColor = '#fff';
                    inputAlamat.style.backgroundColor = '#fff';
                }, 1500);
            } else {
                alert(data.message || 'Data pelanggan tidak ditemukan.');
                inputNama.value = '';
                inputAlamat.value = '';
            }
        })
        .catch(error => {
            loadingText.style.display = 'none';
            console.error('Detail Error:', error);
            // Tampilkan pesan error aslinya ke layar
            alert('Gagal mengambil data. Cek Console (F12) untuk detail.\n\nPesan: ' + error.message);
        });
}
</script>

</body>
</html>