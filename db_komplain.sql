-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 22 Nov 2026 pada 17.37
-- Versi server: 8.4.3
-- Versi PHP: 8.3.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `db_komplain`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `komplain`
--

CREATE TABLE `komplain` (
  `id` int NOT NULL,
  `nama_client` varchar(150) NOT NULL,
  `cid` varchar(50) NOT NULL,
  `alamat` text NOT NULL,
  `waktu_komplain` datetime NOT NULL,
  `gangguan` varchar(100) NOT NULL,
  `merk_alat` varchar(50) DEFAULT NULL,
  `efad_area` varchar(50) DEFAULT NULL,
  `waktu_penanganan` datetime DEFAULT NULL,
  `waktu_konfirmasi` datetime DEFAULT NULL,
  `teknisi` varchar(100) DEFAULT NULL,
  `penanganan` varchar(50) DEFAULT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `komplain`
--

INSERT INTO `komplain` (`id`, `nama_client`, `cid`, `alamat`, `waktu_komplain`, `gangguan`, `merk_alat`, `efad_area`, `waktu_penanganan`, `waktu_konfirmasi`, `teknisi`, `penanganan`, `keterangan`, `created_at`) VALUES
(1, 'agus', '5739720', 'npafpnc', '2026-11-21 03:12:00', 'WIFI LOSS', 'ZTE', 'anak', '2026-11-22 08:12:00', '2026-11-21 09:09:00', '', 'Lapangan', 'PANTAU', '2026-11-22 13:08:11'),
(2, 'Jakli', '42040274', 'Makanan yg bergizi', '2026-11-21 10:24:00', 'Gangguan Massal', 'ZTE', '', '2026-11-22 16:07:00', NULL, 'Dwiya', 'Lapangan', 'TIKET', '2026-11-22 14:43:38'),
(3, 'Arya', '38496027', 'Kost LC', '2026-11-21 22:17:00', 'WIFI LOSS', 'CDATA', '', NULL, NULL, '', 'Lapangan', 'TIKET', '2026-11-22 14:58:58');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `komplain`
--
ALTER TABLE `komplain`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_waktu_komplain` (`waktu_komplain`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `komplain`
--
ALTER TABLE `komplain`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
