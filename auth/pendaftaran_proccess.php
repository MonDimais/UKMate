<?php
session_start();
require_once 'db_connect.php';

// Panggil fungsi db_connect() untuk mendapatkan koneksi
$conn = db_connect();

// Validasi apakah user sudah login
if (!isset($_SESSION['login']) || !isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

$id_user  = $_SESSION['id_user'];
$nama     = $_POST['nama'];
$npm      = $_POST['npm'];
$email    = $_POST['email'];
$prodi    = $_POST['prodi'];
$angkatan = $_POST['angkatan'];
$bio      = $_POST['bio'];
$foto     = NULL;

// Pastikan folder uploads/ sudah dibuat
$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Validasi dan upload foto
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Validasi tipe file
    if (!in_array($_FILES['foto']['type'], $allowed_types)) {
        $_SESSION['error'] = "Tipe file tidak diizinkan. Hanya JPG, PNG, dan GIF yang diperbolehkan.";
        header("Location: ../pendaftaran.php");
        exit();
    }
    
    // Validasi ukuran file
    if ($_FILES['foto']['size'] > $max_size) {
        $_SESSION['error'] = "Ukuran file terlalu besar. Maksimal 5MB.";
        header("Location: ../pendaftaran.php");
        exit();
    }
    
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $filename = 'foto_' . $id_user . '_' . time() . '.' . $ext;
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $destination)) {
        $foto = $filename; // Simpan nama file saja
    } else {
        $_SESSION['error'] = "Gagal mengupload foto.";
        header("Location: ../pendaftaran.php");
        exit();
    }
}

try {
    // Cek apakah user sudah pernah mendaftar
    $checkStmt = $conn->prepare("SELECT id_pendaftaran FROM pendaftaran WHERE id_user = ?");
    $checkStmt->bind_param("i", $id_user);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $_SESSION['error'] = "Anda sudah pernah mendaftar sebelumnya.";
        header("Location: ../pendaftaran.php");
        exit();
    }
    $checkStmt->close();

    // Insert data pendaftaran
    $stmt = $conn->prepare("INSERT INTO pendaftaran (id_user, nama, npm, email, prodi, angkatan, bio, bukti_npm, status, tanggal_daftar)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("isssssss", $id_user, $nama, $npm, $email, $prodi, $angkatan, $bio, $foto);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Pendaftaran berhasil! Silakan tunggu konfirmasi dari admin.";
        header("Location: ../pendaftaran.php");
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat menyimpan data.";
        header("Location: ../pendaftaran.php");
    }
    
    $stmt->close();
    exit();
    
} catch (Exception $e) {
    $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    header("Location: ../pendaftaran.php");
    exit();
}

?>