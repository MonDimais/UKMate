<?php

if (isset($_GET['id']) || isset($_GET['id_anggota'])) {
    include "../koneksi.php";
    $id_anggota = isset($_GET['id_anggota']) ? $_GET['id_anggota'] : $_GET['id'];

    $query = mysqli_query($koneksi, "SELECT * FROM anggota WHERE
        id_anggota='$id_anggota'");
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
                    <h2 class="text-2xl font-semibold">Edit anggota</h2>
                    <a href="data-anggota.php" class="bg-blue-500 text-white px-4 py-2 rounded">Kembali ke data anggota</a>
                </div>

                <form id="formEditAnggota" action="proses-edit-anggota.php" method="POST" class="bg-white p-6 rounded shadow">
                    <input type="hidden" name="id_anggota" value="<?= $row['id_anggota'] ?>">

                    <div class="mb-4">
                        <label for="nama" class="block text-gray-700">Nama</label>
                        <input type="text" name="nama" id="nama" required 
                            value="<?= htmlspecialchars($row['nama']) ?>"
                            class="border p-2 w-full rounded">
                    </div>

                    <div class="mb-4">
                        <label for="npm" class="block text-gray-700">NPM</label>
                        <input type="number" name="npm" id="npm" required 
                            value="<?= $row['npm'] ?>"
                            class="border p-2 w-full rounded">
                    </div>

                    <div class="mb-4">
                        <label for="prodi" class="block text-gray-700">Program Studi</label>
                        <input type="text" name="prodi" id="prodi" required 
                            value="<?= $row['prodi'] ?>"
                            class="border p-2 w-full rounded">
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-gray-700">Email</label>
                        <input type="email" name="email" id="email" required 
                            value="<?= htmlspecialchars($row['email']) ?>"
                            class="border p-2 w-full rounded">
                    </div>

                    <div class="mb-4">
                        <label for="jabatan" class="block text-gray-700">Jabatan</label>
                        <select name="jabatan" id="jabatan" rows="4" required
                            class="border p-2 w-full rounded"><?= htmlspecialchars($row['jabatan']) ?>
                            <option value="Ketua" <?= $row['jabatan'] == 'Ketua' ? 'selected' : '' ?>>Ketua</option>
                            <option value="Wakil Ketua" <?= $row['jabatan'] == 'Wakil Ketua' ? 'selected' : '' ?>>Wakil Ketua</option>
                            <option value="Bendahara" <?= $row['jabatan'] == 'Bendahara' ? 'selected' : '' ?>>Bendahara</option>
                            <option value="Sekretaris" <?= $row['jabatan'] == 'Sekretaris' ? 'selected' : '' ?>>Sekretaris</option>
                            <option value="Koordinator" <?= $row['jabatan'] == 'Koordinator' ? 'selected' : '' ?>>Koordinator</option>
                            <option value="Anggota" <?= $row['jabatan'] == 'Anggota' ? 'selected' : '' ?>>Anggota</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="angkatan" class="block text-gray-700">Angkatan</label>
                        <select name="angkatan" id="angkatan" class="border p-2 w-full rounded">
                            <option value="2021" <?= $row['angkatan'] == '2021' ? 'selected' : '' ?>>2021</option>
                            <option value="2022" <?= $row['angkatan'] == '2022' ? 'selected' : '' ?>>2022</option>
                            <option value="2023" <?= $row['angkatan'] == '2023' ? 'selected' : '' ?>>2023</option>
                            <option value="2024" <?= $row['angkatan'] == '2024' ? 'selected' : '' ?>>2024</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="bio" class="block text-gray-700">Bio</label>
                        <textarea name="bio" id="bio" rows="4" required
                            class="border p-2 w-full rounded"><?= htmlspecialchars($row['bio']) ?></textarea>
                    </div>

                    <div class="flex space-x-2">
                        <button type="button" onclick="clearForm()" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Clear</button>
                        <button type="submit" name="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan anggota</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</body>
<script>
function clearForm() {
    const form = document.getElementById('formEditAnggota');
    form.querySelectorAll('input:not([type=hidden]), textarea, select').forEach(el => {
        if (el.tagName === 'SELECT') {
            el.selectedIndex = 0;
        } else {
            el.value = '';
        }
    });
}
</script>
</html>

<?php
}
?>