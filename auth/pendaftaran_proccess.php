<?php
session_start();
require_once 'db_connect.php';

$nama     = $_POST['nama'];
$npm      = $_POST['npm'];
$email    = $_POST['email'];
$prodi    = $_POST['prodi'];
$angkatan = $_POST['angkatan'];
$bio      = $_POST['bio'];
$foto     = null;

// Pastikan folder uploads/ sudah dibuat dan bisa ditulis
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('foto_') . '.' . $ext;
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $destination)) {
        $foto = $filename;
    }
}

try {
    $stmt = $conn->prepare("INSERT INTO pendaftaran (nama, npm, email, prodi, angkatan, bio, foto)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nama, $npm, $email, $prodi, $angkatan, $bio, $foto]);

    $_SESSION['success'] = "Pendaftaran berhasil!";
    header("Location: ../pendaftaran.php");
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    header("Location: ../pendaftaran.php");
    exit();
}


?>