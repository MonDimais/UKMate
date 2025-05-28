-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 28, 2025 at 04:08 AM
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
-- Database: `ukmate`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id_anggota` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `npm` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `fakultas` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
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

INSERT INTO `anggota` (`id_anggota`, `nama`, `npm`, `fakultas`, `prodi`, `email`, `jabatan`, `angkatan`, `bio`, `dibuat_oleh`, `created_at`) VALUES
(1, 'Ahmad Fauzi', '120221001', NULL, NULL, 'ahmad.fauzi@example.com', 'Ketua', 2021, 'Pemimpin UKM Fasilkom dengan semangat tinggi dan visi yang jelas.', 'admin', '2025-05-12 06:30:40'),
(2, 'Siti Nurhaliza', '120221002', NULL, NULL, 'siti.nurhaliza@example.com', 'Wakil Ketua', 2021, 'Mendampingi ketua dalam berbagai kegiatan dan perencanaan.', 'admin', '2025-05-12 06:30:40'),
(3, 'Budi Santoso', '120221003', NULL, NULL, 'budi.santoso@example.com', 'Sekretaris', 2022, 'Bertugas dalam dokumentasi dan pengelolaan arsip kegiatan UKM.', 'admin', '2025-05-12 06:30:40'),
(4, 'Rina Aprilia', '120221004', NULL, NULL, 'rina.aprilia@example.com', 'Bendahara', 2022, 'Mengatur keuangan UKM secara transparan dan bertanggung jawab.', 'admin', '2025-05-12 06:30:40'),
(5, 'Dewi Anjani', '120221005', NULL, NULL, 'dewi.anjani@example.com', 'Koordinator', 2023, 'Menjadi penghubung antar divisi dan menyusun agenda kegiatan.', 'admin', '2025-05-12 06:30:40'),
(6, 'Andi Kurniawan', '120221006', NULL, NULL, 'andi.kurniawan@example.com', 'Anggota', 2023, 'Anggota aktif yang sering terlibat dalam kegiatan sosial UKM.', 'admin', '2025-05-12 06:30:40'),
(7, 'Lia Marlina', '120221007', NULL, NULL, 'lia.marlina@example.com', 'Anggota', 2023, 'Berperan aktif dalam divisi kreatif dan publikasi.', 'admin', '2025-05-12 06:30:40');

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
(12, 'Percobaan status', '2025-05-20', '23:15:00', 'Lapangan Rusun Adiarsa', 'Rapat', 'Selesai', 'Seno', '2025-05-20 05:29:05'),
(13, 'Latihan Volly X PMTK', '2025-05-20', '23:17:00', '112ead', 'Sdasdsadasd', 'Selesai', 'Seno', '2025-05-20 09:15:21');

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
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'dimas', '$2y$10$g46TM9h2wiOKgvp15wUqOO3vIlRFXbj2kYzAL/ty9qtjzoJtEn7UK', 'User'),
(3, 'Mondimas', '$2y$10$M083jfjrncNraMmeCdsry.FAiYaeDg.2SdCuEFDpazJtmBIWdkbOC', 'Admin'),
(4, 'seno', '$2y$10$aF8N.gqnLGU4A6jCgw5Lc.5l.RBUMnruhmcbF3NK0h6P.svarHuvW', 'User'),
(5, 'bilek', '$2y$10$VP572xHCt77u7CBpJ2iFSumAD4SbvN9I3FO.c7qsuOZdMVC8NunHu', 'User'),
(6, 'bilek1', '$2y$10$R6./NWv8Y8Rk/7g8MdZ3.e9DGycgR65Gut08iBLHnO34ey8UqqY06', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id_anggota`);

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
  MODIFY `id_anggota` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id_kegiatan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id_pendaftaran` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `pendaftaran_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
