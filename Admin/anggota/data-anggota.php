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


// Query untuk menghitung total anggota
$query_count = "SELECT COUNT(*) AS total FROM anggota";
$count_result = mysqli_query($koneksi, $query_count);
$count_data = mysqli_fetch_assoc($count_result);
$total_anggota = $count_data['total'];

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | UKM Fasilkom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-sans h-full">
    <div class="flex h-screen">

        <!-- Sidebar -->
        <?php include '../sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col sm:ml-0 md:ml-80 lg:ml-80 xl:ml-80">

            <!-- Pesan notifikasi jika ada -->
            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mx-6 my-4">
                    Data berhasil disimpan!
                </div>
            <?php endif; ?>

        <!-- Pesan notifikasi simpan ke database berhasil atau tidak -->
        <?php if (isset($_GET['message']) && isset($_GET['type'])):
            $type = $_GET['type'];
            $message = htmlspecialchars($_GET['message']);

            $styles = [
                'success' => 'bg-green-100 border border-green-400 text-green-700',
                'error'   => 'bg-red-100 border border-red-400 text-red-700',
                'warning' => 'bg-yellow-100 border border-yellow-400 text-yellow-700',
                'info'    => 'bg-blue-100 border border-blue-400 text-blue-700',
            ];

            $class = $styles[$type] ?? $styles['info'];
        ?>
        <div id="alert-box" class="fixed top-4 right-4 z-50 px-4 py-3 rounded shadow-lg <?= $class ?>">
            <?= $message ?>
        </div>
        <?php endif; ?>
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

                        <!-- Tombol Tambah -->
                        <button onclick="openModal()" 
                            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition duration-300 ease-in-out">
                            <!-- Icon SVG (person or users icon) -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 14a4 4 0 100-8 4 4 0 000 8z" />
                            </svg>
                            Lihat Pendaftar
                        </button>

                        
                        <!-- Modal Overlay -->
                        <div id="modalPendaftar" class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden flex items-center justify-center">
                            <!-- Modal Box -->
                            <div class="bg-white rounded-lg w-full max-w-3xl shadow-lg p-6 relative">
                                <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-600 hover:text-red-600 text-xl">&times;</button>
                                <h2 class="text-xl font-bold mb-4">Daftar Calon Anggota</h2>

                                <div class="overflow-y-auto max-h-[400px] space-y-3">
                                <?php
                                    $pendaftarQuery = mysqli_query($koneksi, "SELECT * FROM pendaftaran WHERE status != 'diterima' ORDER BY nama ASC");
                                    if (mysqli_num_rows($pendaftarQuery) > 0):
                                        while ($p = mysqli_fetch_assoc($pendaftarQuery)): ?>
                                            <div class="border p-4 rounded-lg shadow">
                                                <h3 class="font-semibold"><?= htmlspecialchars($p['nama']) ?></h3>
                                                <p class="text-sm text-gray-600">NPM: <?= htmlspecialchars($p['npm']) ?> | Prodi: <?= htmlspecialchars($p['prodi']) ?></p>
                                                <div class="mt-2 space-x-2">
                                                    <button onclick='showDetail(<?= json_encode($p) ?>)' class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">Lihat Detail</button>
                                                    <a href="konfirmasi-anggota.php?id=<?= htmlspecialchars($p['id_pendaftaran']) ?>" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">Konfirmasi</a>
                                                    <a href="tolak-anggota.php?id=<?= htmlspecialchars($p['id_pendaftaran']) ?>" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">Tolak</a>
                                                </div>
                                                <!-- Modal Detail -->
                                                <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden flex items-center justify-center">
                                                    <div class="bg-white rounded-lg w-full max-w-md p-6 relative">
                                                        <button onclick="closeDetailModal()" class="absolute top-2 right-3 text-gray-600 hover:text-red-600 text-xl">&times;</button>
                                                        <h2 class="text-xl font-bold mb-4">Detail Anggota</h2>
                                                        <div id="detailContent" class="space-y-2 text-sm">
                                                            <!-- Akan diisi JS -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile;
                                    else: ?>
                                        <p class="text-gray-500 text-sm">Belum ada pendaftar.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <!-- Anggota -->
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Daftar Anggota</h3>
                            <p class="text-sm text-gray-500">Semua anggota yang terdaftar.</p>
                        </div>
                        <!-- Anggota Count -->
                        <div class="mb-4">
                            <p class="text-lg font-semibold text-primary">Total Anggota : <?php echo $total_anggota; ?></p>

                        </div>
                    </div>
                </div>

                <!-- Daftar anggota -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    <?php 
                    // Pastikan query result valid
                    if ($query_result && mysqli_num_rows($query_result) > 0):
                        while ($row = mysqli_fetch_assoc($query_result)): 
                    ?>
                    <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition-all duration-300 relative border border-gray-200 group">
                        
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
                        <div class="mt-6">
                            <h2 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($row['nama'] ?? '') ?></h2>
                            <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($row['email'] ?? '') ?></p>
                            <ul class="text-sm text-gray-700 space-y-1 mt-2">
                                <li><strong>NPM:</strong> <?= htmlspecialchars($row['npm'] ?? '') ?></li>
                                <li><strong>Prodi:</strong> <?= htmlspecialchars($row['prodi'] ?? '') ?></li>
                                <li><strong>Angkatan:</strong> <?= htmlspecialchars($row['angkatan'] ?? '') ?></li>
                            </ul>

                            <?php if (!empty($row['bio'])): ?>
                                <p class="mt-3 text-sm text-gray-600 italic">"<?= htmlspecialchars($row['bio'] ?? '') ?>"</p>
                            <?php endif; ?>
                        </div>

                        <!-- Tombol Edit & Hapus -->
                        <div class="absolute top-4 right-4 flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="edit-anggota.php?id=<?= $row['id_anggota'] ?>" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm">Edit</a>
                            <a href="hapus-anggota.php?id=<?= $row['id_anggota'] ?>" onclick= "return confirmDelete()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Hapus</a>
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

    <script>
    function confirmDelete() {
        const id = event.target.href.split('=')[1];  // Ambil ID dari link
        event.preventDefault(); // Mencegah link untuk langsung diarahkan

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'hapus-anggota.php?id=' + id;  // Arahkan ke halaman hapus setelah konfirmasi
            }
        });
    }

    function openModal() {
    document.getElementById('modalPendaftar').classList.remove('hidden');
    }

    function closeModal() {
    document.getElementById('modalPendaftar').classList.add('hidden');
    }

    function showDetail(data) {
        const content = `
            <p><strong>Nama:</strong> ${data.nama}</p>
            <p><strong>NPM:</strong> ${data.npm}</p>
            <p><strong>Prodi:</strong> ${data.prodi}</p>
            <p><strong>Bukti NPM:</strong><br><img src="../../uploads/${data.bukti_npm}" alt="${data.bukti_npm}" class="w-full mt-2 rounded shadow"></p>
        `;
        document.getElementById('detailContent').innerHTML = content;
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }
    </script>


</body>
</html>
