<?php 
date_default_timezone_set('Asia/Jakarta');

session_start();
include '../koneksi.php';

// Waktu sekarang
$current_time = date('Y-m-d H:i:s');

// 1. Ubah status menjadi 'Berlangsung' jika waktu sekarang >= waktu mulai kegiatan
mysqli_query($koneksi, "
    UPDATE kegiatan 
    SET status = 'Berlangsung'
    WHERE status = 'Terjadwal' 
    AND CONCAT(tanggal_kegiatan, ' ', waktu_kegiatan) <= '$current_time'
");

// 2. Ubah status menjadi 'Selesai' jika waktu sekarang > waktu mulai + 2 jam
mysqli_query($koneksi, "
    UPDATE kegiatan 
    SET status = 'Selesai'
    WHERE status = 'Berlangsung'
    AND DATE_ADD(CONCAT(tanggal_kegiatan, ' ', waktu_kegiatan), INTERVAL 2 HOUR) <= '$current_time'
");

// Query kegiatan terdekat
$query1 = mysqli_query($koneksi, "
    SELECT * FROM kegiatan
    WHERE 
        CONCAT(tanggal_kegiatan, ' ', waktu_kegiatan)
        AND DATE_ADD(CONCAT(tanggal_kegiatan, ' ', waktu_kegiatan), INTERVAL 2 HOUR) > NOW()
    ORDER BY tanggal_kegiatan ASC, waktu_kegiatan ASC
    LIMIT 2;
");

// Filter pencarian dan urut
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'tanggal';
$order = isset($_GET['order']) ? strtoupper($_GET['order']) : 'DESC';

$orderBy = "tanggal_kegiatan $order";
if ($sort == 'nama') {
    $orderBy = "judul_kegiatan $order";
}

// Query semua kegiatan sesuai filter
$query2 = mysqli_query($koneksi, "
    SELECT * FROM kegiatan 
    WHERE judul_kegiatan LIKE '%$search%' 
    ORDER BY $orderBy
");

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
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen">

        <!-- Sidebar -->
        <?php include '../sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col sm:ml-0 md:ml-80 lg:ml-80 xl:ml-80">

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
                        <h2 class="text-2xl font-semibold">Data Kegiatan</h2>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center gap-2 w-full md:w-auto">
                        <form method="GET" class="flex flex-col md:flex-row md:items-center gap-2 w-full md:w-auto">
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-gray-700">Urutkan:</label>
                                
                                <!-- Urut berdasarkan kolom -->
                                <select name="sort" class="border border-gray-300 rounded p-2" onchange="this.form.submit()">
                                    <option value="tanggal" <?= (isset($_GET['sort']) && $_GET['sort'] == 'tanggal') ? 'selected' : '' ?>>Tanggal</option>
                                    <option value="nama" <?= (isset($_GET['sort']) && $_GET['sort'] == 'nama') ? 'selected' : '' ?>>Nama</option>
                                </select>

                                <!-- Arah pengurutan -->
                                <select name="order" class="border border-gray-300 rounded p-2" onchange="this.form.submit()">
                                    <option value="asc" <?= (isset($_GET['order']) && $_GET['order'] == 'asc') ? 'selected' : '' ?>>Terkecil</option>
                                    <option value="desc" <?= (!isset($_GET['order']) || $_GET['order'] == 'desc') ? 'selected' : '' ?>>Terbesar</option>
                                </select>
                            </div>
                            <!-- Pencarian -->
                            <div class="flex items-center gap-2 w-full md:w-80">
                                <input type="text" name="search" placeholder="Cari Kegiatan..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" class="border p-2 rounded w-full">
                                <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">Cari</button>
                            </div>
                        </form>
                        <a href="tambah-kegiatan.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 whitespace-nowrap">Tambah Kegiatan</a>
                    </div>
                </div>
                
                <div class="mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <!-- Kegiatan Terdekat -->
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Kegiatan Terdekat</h3>
                            <p class="text-sm text-gray-500">Kegiatan yang akan datang dalam waktu dekat.</p>
                        </div>
                    </div>
                    <!-- Kegiatan List -->
                    <?php if (mysqli_num_rows($query1) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($query1)): ?>
                            <div 
                                class="cursor-pointer bg-white rounded-lg shadow p-4 hover:shadow-lg transition mb-4"
                                onclick="openModal(<?= $row['id_kegiatan'] ?>)"
                            >
                                <h2 class="text-xl font-semibold text-blue-700"><?= htmlspecialchars($row['judul_kegiatan']) ?></h2>
                                <p class="text-gray-600 mt-2"><?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?> | <?= date('H:i', strtotime($row['waktu_kegiatan'])) ?></p>
                                <p class="text-gray-500 mt-1"><?= htmlspecialchars($row['lokasi']) ?></p>
                                <p class="text-gray-500 mt-1"><strong>Status:</strong> 
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        <?= $row['status'] == 'Berlangsung' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </p>
                            </div>

                            <!-- Modal -->
                            <div id="modal-<?= $row['id_kegiatan'] ?>" class="modal fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50" data-modal-id="<?= $row['id_kegiatan'] ?>">
                                <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
                                    <button onclick="closeModal(<?= $row['id_kegiatan'] ?>)" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">&times;</button>
                                    <h2 class="text-xl font-bold text-blue-700 mb-2"><?= htmlspecialchars($row['judul_kegiatan']) ?></h2>
                                    <p class="text-sm text-gray-600"><?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?> | <?= date('H:i', strtotime($row['waktu_kegiatan'])) ?></p>
                                    <p class="text-gray-600 mt-2"><strong>Lokasi:</strong> <?= htmlspecialchars($row['lokasi']) ?></p>
                                    <p class="text-gray-600 mt-2"><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>
                                    <p class="text-gray-600 mt-2"><strong>Dibuat oleh:</strong> <?= htmlspecialchars($row['dibuat_oleh']) ?></p>

                                    <div class="mt-4 flex items-center gap-2 justify-between">
                                        <p class="mt-4 text-sm">
                                            <strong>Status kegiatan:</strong> 
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                <?= $row['status'] == 'Berlangsung' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' ?>">
                                                <?= htmlspecialchars($row['status']) ?>
                                            </span>
                                        </p>
                                        <div class="mt-4 flex justify-end gap-2">
                                            <a href="edit-kegiatan.php?id=<?= $row['id_kegiatan'] ?>" class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</a>
                                            <a href="hapus-kegiatan.php?id=<?= $row['id_kegiatan'] ?>" onclick="return confirmDelete()" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Hapus</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="bg-white rounded-lg shadow p-4 text-gray-500">Tidak ada kegiatan terdekat.</p>
                    <?php endif; ?>
                </div>


                <div class="mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <!-- Kegiatan List -->
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Daftar Kegiatan</h3>
                            <p class="text-sm text-gray-500">Semua kegiatan yang telah dibuat.</p>
                        </div>
                        <!-- Kegiatan Count -->
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold">Total Kegiatan: <?= mysqli_num_rows($query2) ?></h3>
                        </div>
                    </div>
                <!-- Kegiatan List -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                    <?php while ($row = mysqli_fetch_assoc($query2)): ?>
                        <div 
                            class="cursor-pointer bg-white rounded-lg shadow p-4 hover:shadow-lg transition"
                            onclick="openModal(<?= $row['id_kegiatan'] ?>)"
                        >
                            <h2 class="text-xl font-semibold text-blue-700"><?= htmlspecialchars($row['judul_kegiatan']) ?></h2>
                            <p class="text-gray-600 mt-2"><?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?> | <?= date('H:i', strtotime($row['waktu_kegiatan'])) ?></p>
                            <p class="text-gray-500 mt-1"><?= htmlspecialchars($row['lokasi']) ?></p>
                            <p class="text-gray-500 mt-1"><strong>Status:</strong> 
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    <?= $row['status'] == 'Berlangsung' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </p>
                        </div>

                        <!-- Modal -->
                        <div id="modal-<?= $row['id_kegiatan'] ?>" class="modal fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50" data-modal-id="<?= $row['id_kegiatan'] ?>">
                            <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
                                <button onclick="closeModal(<?= $row['id_kegiatan'] ?>)" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">&times;</button>
                                <h2 class="text-xl font-bold text-blue-700 mb-2"><?= htmlspecialchars($row['judul_kegiatan']) ?></h2>
                                <p class="text-sm text-gray-600"><?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?> | <?= date('H:i', strtotime($row['waktu_kegiatan'])) ?></p>
                                <p class="text-gray-600 mt-2"><strong>Lokasi:</strong> <?= htmlspecialchars($row['lokasi']) ?></p>
                                <p class="text-gray-600 mt-2"><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>
                                <p class="text-gray-600 mt-2"><strong>Dibuat oleh:</strong> <?= htmlspecialchars($row['dibuat_oleh']) ?></p>

                                <div class="mt-4 flex items-center gap-2 justify-between">
                                    <p class="mt-4 text-sm">
                                        <strong>Status kegiatan:</strong> 
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                            <?= $row['status'] == 'Berlangsung' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' ?>">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </span>
                                    </p>
                                    <div class="mt-4 flex justify-end gap-2">
                                        <a href="edit-kegiatan.php?id=<?= $row['id_kegiatan'] ?>" class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</a>
                                        <a href="hapus-kegiatan.php?id=<?= $row['id_kegiatan'] ?>" onclick="return confirmDelete()" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </div>
    </div>

    <script>
    // Auto-hide alert box after 3 seconds
        setTimeout(() => {
        const alertBox = document.getElementById('alert-box');
        if (alertBox) {
            alertBox.style.transition = 'opacity 0.5s ease';
            alertBox.style.opacity = '0';
            setTimeout(() => alertBox.remove(), 500); // remove dari DOM
        }
    }, 3000); // 3000 ms = 3 detik

    function openModal(id) {
        document.getElementById(`modal-${id}`).classList.remove('hidden');
        document.body.classList.add('overflow-hidden'); // optional: lock scroll
    }

    function closeModal(id) {
        document.getElementById(`modal-${id}`).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Tutup modal jika klik di luar isi modal
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(event) {
            // Jika klik terjadi di luar konten (bukan modal box)
            if (event.target === modal) {
                const id = modal.getAttribute('data-modal-id');
                closeModal(id);
            }
        });
    });

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
                window.location.href = 'hapus-kegiatan.php?id=' + id;  // Arahkan ke halaman hapus setelah konfirmasi
            }
        });
    }
    </script>


</body>
</html>
