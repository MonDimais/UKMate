<?php
include '../koneksi.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Ambil data pendaftar
    $query = mysqli_query($koneksi, "SELECT * FROM pendaftaran WHERE id_pendaftaran = $id");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        // Simpan ke tabel anggota
        $nama = mysqli_real_escape_string($koneksi, $data['nama']);
        $npm = mysqli_real_escape_string($koneksi, $data['npm']);
        $email = mysqli_real_escape_string($koneksi, $data['email']);
        $prodi = mysqli_real_escape_string($koneksi, $data['prodi']);
        $angkatan = (int)$data['angkatan'];
        $jabatan = mysqli_real_escape_string($koneksi, $data['jabatan']);
        $bio = mysqli_real_escape_string($koneksi, $data['bio']);
        $id_pendaftaran = $data['id_pendaftaran'];

        $insert = mysqli_query($koneksi, "INSERT INTO anggota (id_pendaftaran, nama, npm, email, prodi, angkatan, jabatan, bio) 
            VALUES ($id_pendaftaran, '$nama', '$npm', '$email', '$prodi', $angkatan, '$jabatan', '$bio')");

        if ($insert) {
            // Update status di pendaftaran jadi diterima
            mysqli_query($koneksi, "UPDATE pendaftaran SET status = 'diterima' WHERE id_pendaftaran = $id");
            header("Location: data-anggota.php?success=1");
            
        } else {
            echo "Gagal menyimpan ke tabel anggota.";
        }
    } else {
        echo "Pendaftar tidak ditemukan.";
    }
} else {
    echo "ID tidak valid.";
}
?>
