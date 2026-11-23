<?php require_once __DIR__ . '/../config/db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Laporan Harian</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
<div class="app-container">
    <?php include __DIR__ . '/../../config/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h2>Input Laporan Harian</h2>
            <p class="subtitle">Catat laporan shift operasional harian Helpdesk.</p>

            <form action="../proses/simpan.php" method="POST">
                <div class="form-grid">
                    
                    <div class="form-group">
                        <label>Tanggal <span class="required">*</span></label>
                        <input type="date" name="tanggal" required>
                    </div>
                    <div class="form-group">
                        <label>Shift</label>
                        <select name="shift">
                            <option value="Pagi">Pagi</option>
                            <option value="Siang">Siang</option>
                            <option value="Malam">Malam</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label>PIC <span class="required">*</span></label>
                        <input type="text" name="pic" placeholder="Contoh: Mingguwati - Tantya - Jaki" required>
                    </div>

                    <div class="form-group full-width">
                        <label>On Duty <span class="required">*</span></label>
                        <input type="text" name="on_duty" placeholder="Contoh: Salman - Samsul *Team 1 Gianyar*" required>
                    </div>

                    <div class="form-group">
                        <label>Kategori Laporan <span class="required">*</span></label>
                        <select name="kategori" required>
                            <option value="TIKET PENDING POP PEJENG">TIKET PENDING POP PEJENG</option>
                            <option value="TIKET MAINTENANCE/TROUBLESHOOT TMT">TIKET MAINTENANCE/TROUBLESHOOT TMT</option>
                            <option value="TIKET TROUBLESHOOT SUDIARTANA">TIKET TROUBLESHOOT SUDIARTANA</option>
                            <option value="TIKET INPROGRES POP PEJENG">TIKET INPROGRES POP PEJENG</option>
                            <option value="TIKET TROUBLESHOOT & MAINTENANCE ES. TELKOM">TIKET TROUBLESHOOT & MAINTENANCE ES. TELKOM</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label>Detail Laporan (Gangguan / Tindakan) <span class="required">*</span></label>
                        <textarea name="detail_laporan" style="min-height: 200px;" placeholder="Tulis detail laporan di sini. (Bisa menggunakan enter untuk baris baru)" required></textarea>
                    </div>

                </div>

                <div class="form-actions">
                    <a href="../laporan/index.php" class="btn btn-secondary">← Kembali</a>
                    <button type="submit" class="btn btn-primary">💾 Simpan Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>