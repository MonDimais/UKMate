<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['id_user']) || !isset($_POST['id_kegiatan']) || !isset($_POST['status_presensi'])) {
    header("Location: data-presensi.php?message=Data tidak lengkap&type=error");
    exit;
}

$id_anggota = $_SESSION['id_user']; // Asumsi session menyimpan id_user
$id_kegiatan = mysqli_real_escape_string($koneksi, $_POST['id_kegiatan']);
$status_presensi = mysqli_real_escape_string($koneksi, $_POST['status_presensi']);
$waktu_presensi = date('Y-m-d H:i:s');

// Cek apakah sudah pernah absen
$check_sql = "SELECT * FROM presensi WHERE id_kegiatan = '$id_kegiatan' AND id_anggota = '$id_anggota'";
$check_result = mysqli_query($koneksi, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    // Update presensi yang sudah ada
    $update_sql = "UPDATE presensi SET 
                   presensi = '$status_presensi', 
                   waktu_presensi = '$waktu_presensi' 
                   WHERE id_kegiatan = '$id_kegiatan' AND id_anggota = '$id_anggota'";
    
    if (mysqli_query($koneksi, $update_sql)) {
        header("Location: data-presensi.php?message=Presensi berhasil diperbarui&type=success");
    } else {
        header("Location: data-presensi.php?message=Gagal memperbarui presensi&type=error");
    }
} else {
    // Insert presensi baru
    $insert_sql = "INSERT INTO presensi (id_kegiatan, id_anggota, presensi, waktu_presensi) 
                   VALUES ('$id_kegiatan', '$id_anggota', '$status_presensi', '$waktu_presensi')";
    
    if (mysqli_query($koneksi, $insert_sql)) {
        header("Location: data-presensi.php?message=Presensi berhasil disimpan&type=success");
    } else {
        header("Location: data-presensi.php?message=Gagal menyimpan presensi&type=error");
    }
}
?>