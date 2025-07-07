<?php 
date_default_timezone_set('Asia/Jakarta');

session_start();
include '../koneksi.php';

// Inisialisasi variabel filter
$where = [];
$order = "";

// Search nama
if (!empty($_GET['cari']) || !empty($_GET['search'])) {
    $cari = mysqli_real_escape_string($koneksi, $_GET['cari'] ?? $_GET['search']);
    $where[] = "nama LIKE '%$cari%'";
}

// Filter Jabatan
if (!empty($_GET['jabatan'])) {
    $jabatan = mysqli_real_escape_string($koneksi, $_GET['jabatan']);
    $where[] = "jabatan = '$jabatan'";
}

// Filter Angkatan
if (!empty($_GET['angkatan'])) {
    $angkatan = mysqli_real_escape_string($koneksi, $_GET['angkatan']);
    $where[] = "angkatan = '$angkatan'";
}

// Sort
switch ($_GET['sort'] ?? '') {
    case 'nama_asc': $order = "ORDER BY nama ASC"; break;
    case 'nama_desc': $order = "ORDER BY nama DESC"; break;
    case 'angkatan_asc': $order = "ORDER BY angkatan ASC"; break;
    case 'angkatan_desc': $order = "ORDER BY angkatan DESC"; break;
    default: $order = "ORDER BY jabatan ASC"; break;
}

// Build query dengan filter
$whereClause = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';
$sql = "SELECT * FROM anggota $whereClause $order";

// PENTING: Execute query dan simpan hasilnya
$query_result = mysqli_query($koneksi, $sql);

// Cek apakah query berhasil
if (!$query_result) {
    die("Database Error: " . mysqli_error($koneksi) . "<br>Query: " . $sql);
}

// Hitung total untuk ditampilkan
$total_anggota = mysqli_num_rows($query_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota | UKM Fasilkom</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans h-full">
    <div class="flex h-screen">

        <!-- Sidebar -->
        <?php include '../sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col sm:ml-0 md:ml-80 lg:ml-80 xl:ml-80">

            <!-- Navbar -->
            <?php include '../navbar.php'; ?>

            <!-- Main Section -->
            <section class="p-6">
                <!-- Filtering & Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-2xl font-semibold">Data Anggota</h2>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center gap-2 w-full md:w-auto">
                        <form method="GET" class="flex flex-wrap md:flex-nowrap gap-3 w-full">
                            <!-- Search Nama -->
                            <div class="flex items-center gap-2 w-full md:w-80">
                                <input type="text" name="cari" placeholder="Cari Anggota..." value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>" class="border p-2 rounded w-full">
                                <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">Cari</button>
                            </div>

                            <!-- Filter Jabatan -->
                            <select name="jabatan" onchange="this.form.submit()" class="border rounded px-3 py-2">
                                <option value="">Semua Jabatan</option>
                                <?php
                                $jabatans = ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara', 'Koordinator', 'Anggota'];
                                foreach ($jabatans as $j) {
                                    $selected = (($_GET['jabatan'] ?? '') === $j) ? 'selected' : '';
                                    echo "<option value=\"$j\" $selected>$j</option>";
                                }
                                ?>
                            </select>

                            <!-- Filter Angkatan -->
                            <select name="angkatan" onchange="this.form.submit()" class="border rounded px-3 py-2">
                                <option value="">Semua Angkatan</option>
                                <?php
                                for ($i = date('Y'); $i >= 2018; $i--) {
                                    $selected = (($_GET['angkatan'] ?? '') == $i) ? 'selected' : '';
                                    echo "<option value=\"$i\" $selected>$i</option>";
                                }
                                ?>
                            </select>

                            <!-- Urutkan -->
                            <select name="sort" onchange="this.form.submit()" class="border rounded px-3 py-2">
                                <option value="">Urutkan</option>
                                <option value="nama_asc" <?= ($_GET['sort'] ?? '') === 'nama_asc' ? 'selected' : '' ?>>Nama (A-Z)</option>
                                <option value="nama_desc" <?= ($_GET['sort'] ?? '') === 'nama_desc' ? 'selected' : '' ?>>Nama (Z-A)</option>
                                <option value="angkatan_asc" <?= ($_GET['sort'] ?? '') === 'angkatan_asc' ? 'selected' : '' ?>>Angkatan (Tertua)</option>
                                <option value="angkatan_desc" <?= ($_GET['sort'] ?? '') === 'angkatan_desc' ? 'selected' : '' ?>>Angkatan (Terbaru)</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <!-- Anggota -->
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Daftar Anggota</h3>
                            <p class="text-sm text-gray-500">Semua anggota yang terdaftar.</p>
                        </div>
                        <!-- Anggota Count -->
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Total Anggota: <?= $total_anggota ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Daftar anggota -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php 
                    // Pastikan query result valid
                    if ($query_result && mysqli_num_rows($query_result) > 0):
                        while ($row = mysqli_fetch_assoc($query_result)): 
                    ?>
                    <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition-all duration-300 relative border border-gray-200">
                        
                        <!-- Jabatan Badge -->
                        <div class="absolute top-3 left-3">
                            <span class="text-xs font-semibold px-3 py-1 rounded-full
                                <?php
                                    $jabatanColor = [
                                        'Ketua' => 'bg-red-100 text-red-700',
                                        'Wakil Ketua' => 'bg-orange-100 text-orange-700',
                                        'Bendahara' => 'bg-yellow-100 text-yellow-700',
                                        'Sekretaris' => 'bg-blue-100 text-blue-700',
                                        'Koordinator' => 'bg-purple-100 text-purple-700',
                                        'Anggota' => 'bg-green-100 text-green-700',
                                    ];
                                    echo $jabatanColor[$row['jabatan']] ?? 'bg-gray-100 text-gray-700';
                                ?>">
                                <?= htmlspecialchars($row['jabatan']) ?>
                            </span>
                        </div>

                        <!-- Informasi Anggota -->
                        <div class="mt-8">
                            <h2 class="text-lg font-bold text-gray-800 mb-1"><?= htmlspecialchars($row['nama'] ?? '') ?></h2>
                            <p class="text-sm text-gray-600 mb-3"><?= htmlspecialchars($row['email'] ?? '') ?></p>
                            
                            <div class="space-y-1 text-sm text-gray-700">
                                <div class="flex justify-between">
                                    <span class="font-medium">NPM:</span>
                                    <span><?= htmlspecialchars($row['npm'] ?? '') ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Prodi:</span>
                                    <span><?= htmlspecialchars($row['prodi'] ?? '') ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Angkatan:</span>
                                    <span><?= htmlspecialchars($row['angkatan'] ?? '') ?></span>
                                </div>
                            </div>

                            <?php if (!empty($row['bio'])): ?>
                                <p class="mt-4 text-sm text-gray-600 italic border-t pt-3">"<?= htmlspecialchars($row['bio'] ?? '') ?>"</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <div class="col-span-full text-center py-8">
                            <p class="text-gray-500">Tidak ada data anggota yang ditemukan.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</body>
</html>