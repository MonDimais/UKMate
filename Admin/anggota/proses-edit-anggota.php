<?php
session_start(); // Mulai session
include '../koneksi.php'; // pastikan file ini benar

if (isset($_POST['submit'])) {
    // Tangkap dan amankan data dari form
    $id_anggota = $_POST['id_anggota'];
    $nama = $_POST['nama'];
    $npm =  $_POST ['npm'];
    $prodi =  $_POST['prodi'];
    $email =  $_POST['email'];
    $jabatan =  $_POST['jabatan'];
    $angkatan = $_POST['angkatan'];
    $bio = $_POST['bio'];

        // Ambil nama akun dari session
    // if (isset($_SESSION['nama'])) {
    //     $dibuat_oleh = mysqli_real_escape_string($koneksi, $_SESSION['nama']);
    // } else {
    //     $message = urlencode("Session tidak ditemukan. Silakan login.");
    //     header("Location: login.php?message=$message&type=error");
    //     exit;
    // }

    // Query update data ke tabel anggota
    $query = mysqli_query($koneksi, "UPDATE anggota SET
        nama = '$nama',
        npm = '$npm',
        prodi = '$prodi',
        email = '$email',
        jabatan = '$jabatan',
        angkatan = '$angkatan',
        bio = '$bio',
        dibuat_oleh = '$dibuat_oleh'
        WHERE id_anggota = '$id_anggota'
    ");


    // Redirect dengan pesan
    if ($query) {
        $message = urlencode("Data berhasil diupdate");
        header("Location: data-anggota.php?message=$message&type=info");
    } else {
        $message = urlencode("Data gagal diupdate");
        header("Location: data-anggota.php?message=$message&type=error");
    }
    exit;
} else {
    $message = urlencode("Form tidak dikirim dengan benar");
    header("Location: data-anggota.php?message=$message&type=error");
    exit;
}
?>
