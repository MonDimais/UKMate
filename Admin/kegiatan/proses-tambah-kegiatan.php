<?php
session_start(); // Mulai session
include '../koneksi.php'; // pastikan file ini benar

if (isset($_POST['submit'])) {
    // Tangkap dan amankan data dari form
    $judul_kegiatan = $_POST['judul_kegiatan'];
    $tanggal_kegiatan =  $_POST ['tanggal_kegiatan'];
    $waktu_kegiatan =  $_POST['waktu_kegiatan'];
    $lokasi =  $_POST['lokasi'];
    $deskripsi =  $_POST['deskripsi'];
    $status = $_POST['status'];

        // Ambil nama akun dari session
    if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        $dibuat_oleh = $_SESSION['username'];
    } else {
        $dibuat_oleh = 'Admin'; // Default jika session tidak ada
    }

    // Query insert data ke tabel kegiatan
    $query = mysqli_query($koneksi, "INSERT INTO kegiatan (
        judul_kegiatan, tanggal_kegiatan, waktu_kegiatan, lokasi, deskripsi, status, dibuat_oleh
    ) VALUES (
        '$judul_kegiatan', '$tanggal_kegiatan', '$waktu_kegiatan', '$lokasi', '$deskripsi', '$status', '$dibuat_oleh'
    )");

    // Redirect dengan pesan
    if ($query) {
        $message = urlencode("Data berhasil ditambahkan");
        header("Location: data-kegiatan.php?message=$message&type=success");
    } else {
        $message = urlencode("Data gagal ditambahkan: " . mysqli_error($koneksi));
        header("Location: data-kegiatan.php?message=$message&type=error");
    }
    exit;
} else {
    $message = urlencode("Form tidak dikirim dengan benar");
    header("Location: data-kegiatan.php?message=$message&type=error");
    exit;
}
?>
