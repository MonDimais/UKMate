<?php

if (isset($_GET['id']) || isset($_GET['id_kegiatan'])) {
    include "../koneksi.php";
    $id_kegiatan = isset($_GET['id_kegiatan']) ? $_GET['id_kegiatan'] : $_GET['id'];

    $query = mysqli_query($koneksi, "SELECT * FROM kegiatan WHERE
        id_kegiatan='$id_kegiatan'");
    $row = mysqli_fetch_array($query);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | UKM Fasilkom</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <?php include '../sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col sm:ml-0 md:ml-80 lg:ml-80 xl:ml-80">

            <!-- Navbar -->
            <?php include '../navbar.php'; ?>

            <!-- Message Alert -->
            <?php if (isset($_GET['message'])): ?>
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mx-6 my-4">
                    <?= htmlspecialchars($_GET['message']) ?>
                </div>
            <?php endif; ?>

            <!-- Main Section -->
            <section class="p-6">
                <div class="mb-4 flex justify-between items-center">
                    <h2 class="text-2xl font-semibold">Edit Kegiatan</h2>
                    <a href="data-kegiatan.php" class="bg-blue-500 text-white px-4 py-2 rounded">Kembali ke data Kegiatan</a>
                </div>

                <form action="proses-edit-kegiatan.php" method="POST" class="bg-white p-6 rounded shadow">
                    <input type="hidden" name="id_kegiatan" value="<?= $row['id_kegiatan'] ?>">

                    <div class="mb-4">
                        <label for="judul_kegiatan" class="block text-gray-700">Judul Kegiatan</label>
                        <input type="text" name="judul_kegiatan" id="judul_kegiatan" required 
                            value="<?= htmlspecialchars($row['judul_kegiatan']) ?>"
                            class="border p-2 w-full rounded">
                    </div>

                    <div class="mb-4">
                        <label for="tanggal_kegiatan" class="block text-gray-700">Tanggal Kegiatan</label>
                        <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" required 
                            value="<?= $row['tanggal_kegiatan'] ?>"
                            class="border p-2 w-full rounded">
                    </div>

                    <div class="mb-4">
                        <label for="waktu_kegiatan" class="block text-gray-700">Waktu Kegiatan</label>
                        <input type="time" name="waktu_kegiatan" id="waktu_kegiatan" required 
                            value="<?= $row['waktu_kegiatan'] ?>"
                            class="border p-2 w-full rounded">
                    </div>

                    <div class="mb-4">
                        <label for="lokasi" class="block text-gray-700">Tempat Kegiatan</label>
                        <input type="text" name="lokasi" id="lokasi" required 
                            value="<?= htmlspecialchars($row['lokasi']) ?>"
                            class="border p-2 w-full rounded">
                    </div>

                    <div class="mb-4">
                        <label for="deskripsi" class="block text-gray-700">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4" required
                            class="border p-2 w-full rounded"><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-gray-700">Status</label>
                        <select name="status" id="status" class="border p-2 w-full rounded">
                            <option value="Terjadwal" <?= $row['status'] == 'Terjadwal' ? 'selected' : '' ?>>Terjadwal</option>
                            <option value="Dibatalkan" <?= $row['status'] == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                            <option value="Selesai" <?= $row['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>

                    <div class="flex space-x-2">
                        <button type="reset" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Clear</button>
                        <button type="submit" name="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan Kegiatan</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</body>
</html>

<?php
}
?>