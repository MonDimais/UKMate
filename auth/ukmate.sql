-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 31 Bulan Mei 2025 pada 04.55
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

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
-- Struktur dari tabel `anggota`
--

CREATE TABLE `anggota` (
  `id_anggota` int(11) NOT NULL,
  `id_pendaftaran` int(11) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `npm` varchar(20) NOT NULL,
  `prodi` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `jabatan` enum('Ketua','Wakil Ketua','Bendahara','Sekretaris','Koordinator','Anggota') NOT NULL,
  `angkatan` int(11) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `dibuat_oleh` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `anggota`
--

INSERT INTO `anggota` (`id_anggota`, `id_pendaftaran`, `nama`, `npm`, `prodi`, `email`, `jabatan`, `angkatan`, `bio`, `dibuat_oleh`, `created_at`) VALUES
(1, NULL, 'Muhammad Ilham Syahputra', '2310631170057', 'Informatika', 'Ilham@gmail.com', 'Ketua', 2021, 'Pemimpin UKM Fasilkom dengan semangat tinggi dan visi yang jelas.', '', '2025-05-12 13:30:40'),
(2, NULL, 'Rolis Liu', '2310631170111', 'Informatika', 'rols@gmail.com', 'Wakil Ketua', 2022, 'Mendampingi ketua dalam berbagai kegiatan dan perencanaan.', '', '2025-05-12 13:30:40'),
(3, NULL, 'Budi Santoso', '120221003', NULL, 'budi.santoso@example.com', 'Sekretaris', 2022, 'Bertugas dalam dokumentasi dan pengelolaan arsip kegiatan UKM.', 'admin', '2025-05-12 13:30:40'),
(4, NULL, 'Rina Aprilia', '120221004', 'Informatika', 'rina.aprilia@example.com', 'Bendahara', 2022, 'Mengatur keuangan UKM secara transparan dan bertanggung jawab.', '', '2025-05-12 13:30:40'),
(5, NULL, 'Dewi Anjani', '120221005', NULL, 'dewi.anjani@example.com', 'Koordinator', 2023, 'Menjadi penghubung antar divisi dan menyusun agenda kegiatan.', 'admin', '2025-05-12 13:30:40'),
(10, 15, 'Zaldy Seno Yudhanto', '2310631170123', 'Informatika', 'seno@gmail.com', 'Koordinator', 2023, 'Koordinator cabang olaharaga voli', '', '2025-05-12 13:30:40'),
(15, 16, 'Dimas Agung Fitriyanto', '231063117075', 'Informatika', 'dimas@gmail.com', 'Koordinator', 2023, 'Aku dimas', '', '2025-05-30 21:01:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id_kegiatan` int(11) NOT NULL,
  `judul_kegiatan` varchar(100) NOT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `waktu_kegiatan` time NOT NULL,
  `lokasi` varchar(150) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `status` enum('Terjadwal','Berlangsung','Selesai','Dibatalkan') DEFAULT 'Terjadwal',
  `dibuat_oleh` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kegiatan`
--

INSERT INTO `kegiatan` (`id_kegiatan`, `judul_kegiatan`, `tanggal_kegiatan`, `waktu_kegiatan`, `lokasi`, `deskripsi`, `status`, `dibuat_oleh`, `created_at`) VALUES
(21, 'Latihan Futsal', '2025-06-02', '18:30:00', 'Lapangan Futsal Seawall', 'Latihan akan diadakan di Seawall, buat teman teman yang ingin hadir dapat langsung menuju seawall pada jam 18.30. Ditungguu kehadirannyaa!', 'Terjadwal', 'Seno', '2025-05-30 20:10:51'),
(22, 'Latihan Voli ', '2025-06-03', '19:30:00', 'Lapangan IREPA (belakang gor adiarsa)', 'Buat temen-temen yang ingin hadir, bisa langsung menuju lapangan irepa pada jam 19.30, ditunggu kehadirannyaa1', 'Terjadwal', 'seno', '2025-05-30 20:13:08'),
(23, 'Sholat Subuh bersama anggota UKM', '2025-05-31', '04:00:00', 'Masjid Unsika', 'Kegiatan Sholat bersama agar meningkatkan rasa persaudaraan sesama anggota', 'Berlangsung', 'seno', '2025-05-30 20:47:54'),
(24, 'Olahraga Bersama', '2025-05-31', '10:00:00', 'Lapangan Unsika', 'Kegiatan olahraga bersama UKM/F', 'Terjadwal', 'seno', '2025-05-30 21:12:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id_pendaftaran` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `npm` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `prodi` varchar(100) NOT NULL,
  `angkatan` year(4) NOT NULL,
  `jabatan` varchar(50) DEFAULT 'Anggota',
  `bio` text DEFAULT NULL,
  `tanggal_daftar` datetime DEFAULT current_timestamp(),
  `status` enum('pending','diterima','ditolak') DEFAULT 'pending',
  `bukti_npm` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendaftaran`
--

INSERT INTO `pendaftaran` (`id_pendaftaran`, `id_user`, `nama`, `npm`, `email`, `prodi`, `angkatan`, `jabatan`, `bio`, `tanggal_daftar`, `status`, `bukti_npm`) VALUES
(15, 10, 'Zaldy Seno Yudhanto', '2310631170123', 'seno@gmail.com', 'Informatika', '2023', 'Koordinator UKM Voli', 'Saya tertarik bergabung karena ingin mengembangkan kemampuan organisasi.', '2025-05-31 10:00:00', 'diterima', 'bukti_npm_2310631170123.jpg'),
(16, 11, 'Dimas Agung Fitriyanto', '231063117075', 'dimas@gmail.com', 'Informatika', '2023', 'Anggota', 'Aku dimas', '2025-05-31 03:53:08', 'diterima', 'foto_11_1748638388.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id_pengumuman` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `konten` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengumuman`
--

INSERT INTO `pengumuman` (`id_pengumuman`, `judul`, `konten`, `created_at`) VALUES
(4, 'Agenda Minggu ini', 'Kegiatan UKM tanggal 1-7 Juni diliburkan', '2025-05-30 20:46:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `presensi`
--

CREATE TABLE `presensi` (
  `id_presensi` int(11) NOT NULL,
  `id_kegiatan` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `presensi` enum('Hadir','Izin','Tidak Hadir') DEFAULT NULL,
  `waktu_presensi` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `presensi`
--

INSERT INTO `presensi` (`id_presensi`, `id_kegiatan`, `id_anggota`, `presensi`, `waktu_presensi`) VALUES
(12, 23, 15, 'Hadir', '2025-05-31 04:05:54'),
(13, 23, 10, 'Hadir', '2025-05-31 04:09:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `role` enum('User','Admin') NOT NULL DEFAULT 'User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`) VALUES
(10, 'seno', '$2y$10$oAs1CIMrlXr35D7KsUr5be/yKghvAlVCWqD/r5mo/Pz1RNyhRJ2fK', 'Admin'),
(11, 'dimas', '$2y$10$ZVuboVHXy.VVUHocxibL1.uHJfwmIy3XIco7dNz.LkuYkCPEE03Uq', 'User');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id_anggota`),
  ADD KEY `fk_id_pendaftaran` (`id_pendaftaran`);

--
-- Indeks untuk tabel `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id_kegiatan`);

--
-- Indeks untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id_pendaftaran`),
  ADD UNIQUE KEY `npm` (`npm`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id_pengumuman`);

--
-- Indeks untuk tabel `presensi`
--
ALTER TABLE `presensi`
  ADD PRIMARY KEY (`id_presensi`),
  ADD KEY `id_kegiatan` (`id_kegiatan`),
  ADD KEY `id_anggota` (`id_anggota`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id_pendaftaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id_pengumuman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id_presensi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD CONSTRAINT `fk_id_pendaftaran` FOREIGN KEY (`id_pendaftaran`) REFERENCES `pendaftaran` (`id_pendaftaran`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `presensi`
--
ALTER TABLE `presensi`
  ADD CONSTRAINT `presensi_ibfk_1` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan` (`id_kegiatan`),
  ADD CONSTRAINT `presensi_ibfk_2` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
