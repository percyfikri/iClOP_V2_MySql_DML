-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2025 at 01:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `iclop_v2_testing`
--

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `kode_dosen` varchar(4) NOT NULL,
  `nama_dosen` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hari`
--

CREATE TABLE `hari` (
  `kode_hari` varchar(3) NOT NULL,
  `nama_hari` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `kode_jadwal` int(10) UNSIGNED NOT NULL,
  `kode_kelas` varchar(10) NOT NULL,
  `kode_dosen` varchar(4) DEFAULT NULL,
  `kode_mk` varchar(5) NOT NULL,
  `kode_ruang` varchar(5) NOT NULL,
  `kode_hari` varchar(3) NOT NULL,
  `jp_mulai` int(11) NOT NULL,
  `jp_selesai` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jp`
--

CREATE TABLE `jp` (
  `kode_jp` int(11) NOT NULL,
  `jp_mulai` time NOT NULL,
  `jp_selesai` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `kode_kelas` varchar(10) NOT NULL,
  `kode_prodi` varchar(3) NOT NULL,
  `nama_kelas` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_05_24_130620_prodi', 1),
(2, '2025_05_24_130621_kelas', 1),
(3, '2025_05_24_130622_dosen', 1),
(4, '2025_05_24_130623_hari', 1),
(5, '2025_05_24_130624_mk', 1),
(6, '2025_05_24_130625_ruang', 1),
(7, '2025_05_24_130626_jp', 1),
(8, '2025_05_24_130627_jadwal', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mk`
--

CREATE TABLE `mk` (
  `kode_mk` varchar(5) NOT NULL,
  `nama_mk` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prodi`
--

CREATE TABLE `prodi` (
  `kode_prodi` varchar(3) NOT NULL,
  `nama_prodi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ruang`
--

CREATE TABLE `ruang` (
  `kode_ruang` varchar(5) NOT NULL,
  `nama_ruang` varchar(20) NOT NULL,
  `deskripsi_ruang` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`kode_dosen`);

--
-- Indexes for table `hari`
--
ALTER TABLE `hari`
  ADD PRIMARY KEY (`kode_hari`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`kode_jadwal`),
  ADD KEY `jadwal_kode_kelas_foreign` (`kode_kelas`),
  ADD KEY `jadwal_kode_dosen_foreign` (`kode_dosen`),
  ADD KEY `jadwal_kode_mk_foreign` (`kode_mk`),
  ADD KEY `jadwal_kode_ruang_foreign` (`kode_ruang`),
  ADD KEY `jadwal_kode_hari_foreign` (`kode_hari`),
  ADD KEY `jadwal_jp_mulai_foreign` (`jp_mulai`),
  ADD KEY `jadwal_jp_selesai_foreign` (`jp_selesai`);

--
-- Indexes for table `jp`
--
ALTER TABLE `jp`
  ADD PRIMARY KEY (`kode_jp`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`kode_kelas`),
  ADD KEY `kelas_kode_prodi_foreign` (`kode_prodi`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mk`
--
ALTER TABLE `mk`
  ADD PRIMARY KEY (`kode_mk`);

--
-- Indexes for table `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`kode_prodi`);

--
-- Indexes for table `ruang`
--
ALTER TABLE `ruang`
  ADD PRIMARY KEY (`kode_ruang`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `kode_jadwal` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=371;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_jp_mulai_foreign` FOREIGN KEY (`jp_mulai`) REFERENCES `jp` (`kode_jp`),
  ADD CONSTRAINT `jadwal_jp_selesai_foreign` FOREIGN KEY (`jp_selesai`) REFERENCES `jp` (`kode_jp`),
  ADD CONSTRAINT `jadwal_kode_dosen_foreign` FOREIGN KEY (`kode_dosen`) REFERENCES `dosen` (`kode_dosen`),
  ADD CONSTRAINT `jadwal_kode_hari_foreign` FOREIGN KEY (`kode_hari`) REFERENCES `hari` (`kode_hari`),
  ADD CONSTRAINT `jadwal_kode_kelas_foreign` FOREIGN KEY (`kode_kelas`) REFERENCES `kelas` (`kode_kelas`),
  ADD CONSTRAINT `jadwal_kode_mk_foreign` FOREIGN KEY (`kode_mk`) REFERENCES `mk` (`kode_mk`),
  ADD CONSTRAINT `jadwal_kode_ruang_foreign` FOREIGN KEY (`kode_ruang`) REFERENCES `ruang` (`kode_ruang`);

--
-- Constraints for table `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `kelas_kode_prodi_foreign` FOREIGN KEY (`kode_prodi`) REFERENCES `prodi` (`kode_prodi`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
