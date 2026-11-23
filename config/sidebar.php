<?php

$current_dir = basename(dirname($_SERVER['PHP_SELF'])); 
?>
<div class="sidebar">
    <a href="/" class="sidebar-brand">🛠️ Komplain HD v2.0</a>
    <ul class="sidebar-menu">
        <li>
            <a href="/modul_reguler/laporan/index.php" class="<?= $current_dir == 'modul_reguler' ? 'active' : '' ?>">
                Komplain Reguler
            </a>
        </li>
        <li>
            <a href="/modul_reseller/laporan/index.php" class="<?= $current_dir == 'modul_reseller' ? 'active' : '' ?>">
                Reseller & SOHO
            </a>
        </li>
        <li>
            <a href="/modul_harian/laporan/index.php" class="<?= $current_dir == 'modul_harian' ? 'active' : '' ?>">
                Laporan Harian
            </a>
        </li>
    </ul>
</div>