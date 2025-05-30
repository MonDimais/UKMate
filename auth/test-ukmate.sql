-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 30, 2025 at 06:01 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test-ukmate`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id_anggota` int NOT NULL,
  `id_pendaftaran` int DEFAULT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `npm` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `prodi` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jabatan` enum('Ketua','Wakil Ketua','Bendahara','Sekretaris','Koordinator','Anggota') COLLATE utf8mb4_general_ci NOT NULL,
  `angkatan` int DEFAULT NULL,
  `bio` text COLLATE utf8mb4_general_ci,
  `dibuat_oleh` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id_anggota`, `id_pendaftaran`, `nama`, `npm`, `prodi`, `email`, `jabatan`, `angkatan`, `bio`, `dibuat_oleh`, `created_at`) VALUES
(1, NULL, 'Ahmad Fauzi', '120221001', 'Informatika', 'ahmad.fauzi@example.com', 'Ketua', 2021, 'Pemimpin UKM Fasilkom dengan semangat tinggi dan visi yang jelas.', '', '2025-05-12 13:30:40'),
(2, NULL, 'Siti Nurhaliza', '120221002', NULL, 'siti.nurhaliza@example.com', 'Wakil Ketua', 2021, 'Mendampingi ketua dalam berbagai kegiatan dan perencanaan.', 'admin', '2025-05-12 13:30:40'),
(3, NULL, 'Budi Santoso', '120221003', NULL, 'budi.santoso@example.com', 'Sekretaris', 2022, 'Bertugas dalam dokumentasi dan pengelolaan arsip kegiatan UKM.', 'admin', '2025-05-12 13:30:40'),
(4, NULL, 'Rina Aprilia', '120221004', 'Informatika', 'rina.aprilia@example.com', 'Bendahara', 2022, 'Mengatur keuangan UKM secara transparan dan bertanggung jawab.', '', '2025-05-12 13:30:40'),
(5, NULL, 'Dewi Anjani', '120221005', NULL, 'dewi.anjani@example.com', 'Koordinator', 2023, 'Menjadi penghubung antar divisi dan menyusun agenda kegiatan.', 'admin', '2025-05-12 13:30:40'),
(6, NULL, 'Zaldy Seno Yudhanto', '2310631170123', 'Informatika', 'seno@gmail.com', 'Koordinator', 2023, 'Koordinator cabang olaharaga voli', '', '2025-05-12 13:30:40'),
(9, NULL, 'Andi Saputra', '123456789', 'Teknik Informatika', 'andi@example.com', 'Anggota', 2023, '', NULL, '2025-05-25 04:41:03'),
(12, 7, 'Dimas Agung F', '2301631170075', 'Informatika', 'dimasea@gmail.com', 'Bendahara', 2023, 'aku suka ngoding\r\n', '', '2025-05-30 13:26:59'),
(13, 8, 'bilek', '12345', 'madang', 'gg@gmail.com', 'Anggota', 2012, '#ffff', NULL, '2025-05-30 13:49:47'),
(14, 9, 'Ilham programer handal', '230744', 'Informatika', 'naruto@gmail.com', 'Anggota', 2023, 'ngoding gampang', NULL, '2025-05-30 17:58:42');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id_kegiatan` int NOT NULL,
  `judul_kegiatan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `waktu_kegiatan` time NOT NULL,
  `lokasi` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `status` enum('Terjadwal','Berlangsung','Selesai','Dibatalkan') COLLATE utf8mb4_general_ci DEFAULT 'Terjadwal',
  `dibuat_oleh` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kegiatan`
--

INSERT INTO `kegiatan` (`id_kegiatan`, `judul_kegiatan`, `tanggal_kegiatan`, `waktu_kegiatan`, `lokasi`, `deskripsi`, `status`, `dibuat_oleh`, `created_at`) VALUES
(12, 'Percobaan status', '2025-05-20', '23:15:00', 'Lapangan Rusun Adiarsa', 'Rapat', 'Selesai', 'Seno', '2025-05-20 12:29:05'),
(13, 'Latihan Volly X PMTK', '2025-05-20', '23:17:00', '112ead', 'Sdasdsadasd', 'Selesai', 'Seno', '2025-05-20 16:15:21'),
(16, 'makan nasi', '2025-06-07', '12:00:00', 'di rumah', 'makan makan', 'Terjadwal', 'Seno', '2025-05-30 14:59:11'),
(17, 'test', '2025-05-31', '10:00:00', 'wc', 'dsa', 'Dibatalkan', 'Seno', '2025-05-30 15:28:21'),
(18, 'makan nasi uduk', '2025-05-30', '22:35:00', 'di rumah', 'nut', 'Selesai', 'Seno', '2025-05-30 15:35:28'),
(19, 'tidur', '2025-05-30', '23:50:00', 'di rumah', 'turu', 'Berlangsung', 'dimas', '2025-05-30 16:48:44'),
(20, 'angkringan', '2025-05-31', '00:58:00', 'di jalanan', 'nasi kucing', 'Berlangsung', 'Seno', '2025-05-30 17:54:57');

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id_pendaftaran` int NOT NULL,
  `id_user` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `npm` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `prodi` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `angkatan` year NOT NULL,
  `jabatan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'Anggota',
  `bio` text COLLATE utf8mb4_general_ci,
  `tanggal_daftar` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','diterima','ditolak') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `bukti_npm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pendaftaran`
--

INSERT INTO `pendaftaran` (`id_pendaftaran`, `id_user`, `nama`, `npm`, `email`, `prodi`, `angkatan`, `jabatan`, `bio`, `tanggal_daftar`, `status`, `bukti_npm`) VALUES
(7, 1, 'Dimas Agung F', '2301631170075', 'dimasea@gmail.com', 'Informatika', '2023', 'Anggota', 'aku suka ngoding\r\n', '2025-05-30 15:56:19', 'diterima', 'foto_1_1748595379.png'),
(8, 5, 'bilek', '12345', 'gg@gmail.com', 'madang', '2012', 'Anggota', '#ffff', '2025-05-30 20:49:32', 'diterima', 'foto_5_1748612972.png'),
(9, 8, 'Ilham programer handal', '230744', 'naruto@gmail.com', 'Informatika', '2023', 'Anggota', 'ngoding gampang', '2025-05-31 00:58:07', 'diterima', 'foto_8_1748627887.png');

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id_pengumuman` int NOT NULL,
  `judul` varchar(255) NOT NULL,
  `konten` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengumuman`
--

INSERT INTO `pengumuman` (`id_pengumuman`, `judul`, `konten`, `created_at`) VALUES
(1, 'Pendaftaran UKM 2025', 'Pendaftaran UKM periode 2025 dibuka hingga 30 Juni 2025. Silakan daftar di sekretariat UKM.', '2025-05-27 01:07:14'),
(2, 'Jadwal Latihan Baru', 'Jadwal latihan UKM Voli dan Basket diperbarui mulai 1 Juni 2025.', '2025-05-27 01:07:14'),
(3, 'Pemeliharaan Lapangan', 'Lapangan badminton akan ditutup untuk pemeliharaan pada 10-12 Juni 2025.', '2025-05-27 01:07:14');

-- --------------------------------------------------------

--
-- Table structure for table `presensi`
--

CREATE TABLE `presensi` (
  `id_presensi` int NOT NULL,
  `id_kegiatan` int NOT NULL,
  `id_anggota` int NOT NULL,
  `presensi` enum('Hadir','Izin','Tidak Hadir') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `waktu_presensi` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `presensi`
--

INSERT INTO `presensi` (`id_presensi`, `id_kegiatan`, `id_anggota`, `presensi`, `waktu_presensi`) VALUES
(1, 12, 1, 'Hadir', '2025-05-20 23:00:00'),
(2, 12, 2, 'Hadir', '2025-05-20 23:02:00'),
(3, 12, 3, 'Izin', NULL),
(4, 13, 5, 'Hadir', '2025-05-20 23:18:00'),
(5, 13, 6, 'Hadir', '2025-05-20 23:20:00'),
(6, 13, 9, 'Tidak Hadir', NULL),
(7, 19, 13, 'Hadir', '2025-05-31 00:34:39'),
(8, 18, 13, 'Izin', '2025-05-31 00:34:47'),
(9, 19, 12, 'Izin', '2025-05-31 00:36:47'),
(10, 19, 14, 'Hadir', '2025-05-31 00:59:35'),
(11, 20, 14, 'Tidak Hadir', '2025-05-31 01:00:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `username` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `role` enum('User','Admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`) VALUES
(1, 'dimas', '$2y$10$g46TM9h2wiOKgvp15wUqOO3vIlRFXbj2kYzAL/ty9qtjzoJtEn7UK', 'Admin'),
(3, 'Mondimas', '$2y$10$M083jfjrncNraMmeCdsry.FAiYaeDg.2SdCuEFDpazJtmBIWdkbOC', 'Admin'),
(4, 'seno', '$2y$10$aF8N.gqnLGU4A6jCgw5Lc.5l.RBUMnruhmcbF3NK0h6P.svarHuvW', 'User'),
(5, 'bilek', '$2y$10$VP572xHCt77u7CBpJ2iFSumAD4SbvN9I3FO.c7qsuOZdMVC8NunHu', 'User'),
(6, 'bilek1', '$2y$10$R6./NWv8Y8Rk/7g8MdZ3.e9DGycgR65Gut08iBLHnO34ey8UqqY06', 'User'),
(7, 'mondi', '$2y$10$8A2RthIwIPDBEHVTzV5TXuJK55b.FIDwS1RJfzKC/hHH5jwnb611G', 'User'),
(8, 'ilham', '$2y$10$6yri44XAeeyc7VvkpLti6ezJrnwgJ0.YOjGOX6A5LtDBT4AanOJz6', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id_anggota`),
  ADD KEY `fk_id_pendaftaran` (`id_pendaftaran`);

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id_kegiatan`);

--
-- Indexes for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id_pendaftaran`),
  ADD UNIQUE KEY `npm` (`npm`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id_pengumuman`);

--
-- Indexes for table `presensi`
--
ALTER TABLE `presensi`
  ADD PRIMARY KEY (`id_presensi`),
  ADD KEY `id_kegiatan` (`id_kegiatan`),
  ADD KEY `id_anggota` (`id_anggota`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id_anggota` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id_kegiatan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id_pendaftaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id_pengumuman` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id_presensi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anggota`
--
ALTER TABLE `anggota`
  ADD CONSTRAINT `fk_id_pendaftaran` FOREIGN KEY (`id_pendaftaran`) REFERENCES `pendaftaran` (`id_pendaftaran`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `presensi`
--
ALTER TABLE `presensi`
  ADD CONSTRAINT `presensi_ibfk_1` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan` (`id_kegiatan`),
  ADD CONSTRAINT `presensi_ibfk_2` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
