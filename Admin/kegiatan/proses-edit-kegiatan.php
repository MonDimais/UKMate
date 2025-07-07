<?php
session_start(); // Mulai session
include '../koneksi.php'; // pastikan file ini benar

if (isset($_POST['submit'])) {
    // Tangkap dan amankan data dari form
    $id_kegiatan = $_POST['id_kegiatan'];
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

    // Query update data ke tabel kegiatan
    $query = mysqli_query($koneksi, "UPDATE kegiatan SET
        judul_kegiatan = '$judul_kegiatan',
        tanggal_kegiatan = '$tanggal_kegiatan',
        waktu_kegiatan = '$waktu_kegiatan',
        lokasi = '$lokasi',
        deskripsi = '$deskripsi',
        status = '$status',
        dibuat_oleh = '$dibuat_oleh'
        WHERE id_kegiatan = '$id_kegiatan'
    ");


    // Redirect dengan pesan
    if ($query) {
        $message = urlencode("Data berhasil diupdate");
        header("Location: data-kegiatan.php?message=$message&type=info");
    } else {
        $message = urlencode("Data gagal diupdate");
        header("Location: data-kegiatan.php?message=$message&type=error");
    }
    exit;
} else {
    $message = urlencode("Form tidak dikirim dengan benar");
    header("Location: data-kegiatan.php?message=$message&type=error");
    exit;
}
?>
