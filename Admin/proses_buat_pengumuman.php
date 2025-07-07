<?php
session_start(); // Mulai session
// Include koneksi database
include 'koneksi.php';  // Pastikan file koneksi sesuai dengan proyekmu

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $judul = isset($_POST['judul']) ? trim($_POST['judul']) : '';
    $konten = isset($_POST['konten']) ? trim($_POST['konten']) : '';

    // Validasi sederhana
    if (empty($judul) || empty($konten)) {
        // Jika data kosong, kembali ke form dengan pesan error (bisa juga redirect)
        echo "<script>alert('Judul dan konten wajib diisi!'); window.history.back();</script>";
        exit;
    }

    // Escape data agar aman saat disimpan di DB (gunakan prepared statement lebih baik)
    $judul = mysqli_real_escape_string($koneksi, $judul);
    $konten = mysqli_real_escape_string($koneksi, $konten);

    // Insert ke database
    $sql = "INSERT INTO pengumuman (judul, konten) VALUES ('$judul', '$konten')";
    $query = mysqli_query($koneksi, $sql); // <-- eksekusi query

    // Redirect dengan pesan
    if ($query) {
        $message = urlencode("Data berhasil ditambahkan");
        header("Location: dashboard.php?message=$message&type=success");
    } else {
        $message = urlencode("Data gagal ditambahkan: " . mysqli_error($koneksi));
        header("Location: dashboard.php?message=$message&type=error");
    }
    exit;
} else {
    $message = urlencode("Form tidak dikirim dengan benar");
    header("Location: dashboard.php?message=$message&type=error");
    exit;
}
?>
