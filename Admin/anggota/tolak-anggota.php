    <?php

    if (isset($_GET['id'])) {
        include "../koneksi.php";
        $id = $_GET['id'];
        $query = mysqli_query($koneksi, "DELETE FROM pendaftaran WHERE id_pendaftaran='$id'");

        if ($query) {
            $message = urlencode("Data berhasil dihapus");
            header("Location:data-anggota.php?message={$message}&type=warning");

        } else {
            $message = urlencode("Data gagal dihapus");
            header("Location:data-anggota.php?message={$message}&type=error");
        }
    }
    ?>