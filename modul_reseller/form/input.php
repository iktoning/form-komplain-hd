<?php require_once __DIR__ . '/../../config/db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Komplain Reseller & SOHO</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
<div class="app-container">
    <?php include __DIR__ . '/../../config/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h2>Input Komplain Reseller & SOHO</h2>
            <p class="subtitle">Catat laporan gangguan khusus pelanggan Reseller dan SOHO.</p>

            <form action="../proses/simpan.php" method="POST">
                <div class="form-grid">
                    
                    <div class="form-group">
                        <label>CID <span class="required">*</span></label>
                        <input type="text" name="cid" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Client <span class="required">*</span></label>
                        <input type="text" name="nama_client" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Paket</label>
                        <select name="paket">
                            <option value="DEDICATED">DEDICATED</option>
                            <option value="RESELLER">RESELLER</option>
                            <option value="SOHO">SOHO</option>
                            <option value="LOCAL LOOP">LOCAL LOOP</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Komplain <span class="required">*</span></label>
                        <input type="datetime-local" name="tgl_komplain" required>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Penanganan</label>
                        <input type="datetime-local" name="tgl_penanganan">
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi User</label>
                        <input type="datetime-local" name="konfirmasi_user">
                    </div>

                    <div class="form-group">
                        <label>Impact</label>
                        <select name="impact">
                            <option value="DOWN SLA">DOWN SLA</option>
                            <option value="DOWN NON SLA">DOWN NON SLA</option>
                            <option value="LAMBAT">LAMBAT</option>
                            <option value="INFORMATION">INFORMATION</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Total Problem</label>
                        <input type="text" name="total_problem" placeholder="Contoh: Terjantur Dying Gasp">
                    </div>

                    <div class="form-group full-width">
                        <label>Action</label>
                        <textarea name="action" placeholder="Tindakan yang diambil..."></textarea>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Keterangan</label>
                        <select name="keterangan">
                            <option value="BACK TO NORMAL AND SOLVED">BACK TO NORMAL AND SOLVED</option>
                            <option value="PENDING">PENDING</option>
                            <option value="PROGRESS">PROGRESS</option>
                        </select>
                    </div>

                </div>

                <div class="form-actions">
                    <a href="../laporan/index.php" class="btn btn-secondary">← Kembali</a>
                    <button type="submit" class="btn btn-primary">💾 Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>