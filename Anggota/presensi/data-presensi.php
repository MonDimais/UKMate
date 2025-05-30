<?php 
date_default_timezone_set('Asia/Jakarta');

session_start();
include '../koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Waktu sekarang
$current_time = date('Y-m-d H:i:s');

// Fungsi untuk mendapatkan id_anggota dari id_user
function getIdAnggota($koneksi, $id_user) {
    // Ambil id_pendaftaran dari tabel pendaftaran
    $sql_pendaftaran = "SELECT id_pendaftaran FROM pendaftaran WHERE id_user = $id_user LIMIT 1";
    $result_pendaftaran = mysqli_query($koneksi, $sql_pendaftaran);
    
    if ($result_pendaftaran && mysqli_num_rows($result_pendaftaran) > 0) {
        $row_pendaftaran = mysqli_fetch_assoc($result_pendaftaran);
        $id_pendaftaran = $row_pendaftaran['id_pendaftaran'];
        
        // Ambil id_anggota dari tabel anggota
        $sql_anggota = "SELECT id_anggota FROM anggota WHERE id_pendaftaran = $id_pendaftaran LIMIT 1";
        $result_anggota = mysqli_query($koneksi, $sql_anggota);
        
        if ($result_anggota && mysqli_num_rows($result_anggota) > 0) {
            $row_anggota = mysqli_fetch_assoc($result_anggota);
            return $row_anggota['id_anggota'];
        }
    }
    return null;
}

// Mendapatkan id_anggota user yang login
$id_anggota = getIdAnggota($koneksi, $id_user);

// Handle form presensi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_presensi'])) {
    $id_kegiatan = (int)$_POST['id_kegiatan'];
    $status_presensi = mysqli_real_escape_string($koneksi, $_POST['status_presensi']);
    
    if ($id_anggota) {
        // Cek apakah sudah ada presensi
        $sql_cek = "SELECT id_presensi, presensi FROM presensi WHERE id_kegiatan = $id_kegiatan AND id_anggota = $id_anggota";
        $result_cek = mysqli_query($koneksi, $sql_cek);
        
        if (mysqli_num_rows($result_cek) == 0) {
            // Insert presensi baru
            $sql_insert = "INSERT INTO presensi (id_kegiatan, id_anggota, presensi, waktu_presensi) 
                          VALUES ($id_kegiatan, $id_anggota, '$status_presensi', '$current_time')";
            
            if (mysqli_query($koneksi, $sql_insert)) {
                header("Location: ?message=Presensi berhasil disimpan&type=success");
                exit();
            } else {
                header("Location: ?message=Gagal menyimpan presensi&type=error");
                exit();
            }
        } else {
            $row_cek = mysqli_fetch_assoc($result_cek);
            if ($row_cek['presensi'] == NULL) {
                // Update presensi jika masih NULL
                $sql_update = "UPDATE presensi SET presensi = '$status_presensi', waktu_presensi = '$current_time' 
                              WHERE id_kegiatan = $id_kegiatan AND id_anggota = $id_anggota";
                
                if (mysqli_query($koneksi, $sql_update)) {
                    header("Location: ?message=Presensi berhasil disimpan&type=success");
                    exit();
                } else {
                    header("Location: ?message=Gagal menyimpan presensi&type=error");
                    exit();
                }
            } else {
                header("Location: ?message=Anda sudah melakukan presensi untuk kegiatan ini&type=warning");
                exit();
            }
        }
    } else {
        header("Location: ?message=Anda belum terdaftar sebagai anggota&type=error");
        exit();
    }
}

// Filter pencarian dan urut
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'tanggal';
$order = isset($_GET['order']) ? strtoupper($_GET['order']) : 'DESC';

// Validasi order untuk keamanan
$order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';

$orderBy = "tanggal_kegiatan $order";
if ($sort == 'nama') {
    $orderBy = "judul_kegiatan $order";
}

// Query semua kegiatan sesuai filter dengan error handling
$sql_kegiatan = "
    SELECT * FROM kegiatan 
    WHERE judul_kegiatan LIKE '%$search%' 
    ORDER BY $orderBy
";

$query = mysqli_query($koneksi, $sql_kegiatan);

// Cek apakah query berhasil
if (!$query) {
    die("Query Error: " . mysqli_error($koneksi));
}

// Hitung total kegiatan
$total_kegiatan = mysqli_num_rows($query);

// Simpan data ke array
$kegiatan_data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $kegiatan_data[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggota | UKM Fasilkom</title>
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
                    </div>
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
                            <h3 class="text-lg font-semibold">Total Kegiatan: <?= $total_kegiatan ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Kegiatan List -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                    <?php if (count($kegiatan_data) > 0): ?>
                        <?php foreach ($kegiatan_data as $row): ?>
                            <?php
                            // Cek status presensi user untuk kegiatan ini
                            $user_presensi_status = null;
                            if ($id_anggota) {
                                $sql_cek_user = "SELECT presensi FROM presensi WHERE id_kegiatan = {$row['id_kegiatan']} AND id_anggota = $id_anggota";
                                $result_cek_user = mysqli_query($koneksi, $sql_cek_user);
                                if ($result_cek_user && mysqli_num_rows($result_cek_user) > 0) {
                                    $row_user = mysqli_fetch_assoc($result_cek_user);
                                    $user_presensi_status = $row_user['presensi'];
                                }
                            }
                            ?>
                            <div 
                                class="cursor-pointer bg-white rounded-lg shadow p-4 hover:shadow-lg transition"
                                onclick="openModal(<?= $row['id_kegiatan'] ?>)"
                            >
                                <h2 class="text-xl font-semibold text-blue-700"><?= htmlspecialchars($row['judul_kegiatan'] ?? '') ?></h2>
                                <p class="text-gray-600 mt-2"><?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?> | <?= date('H:i', strtotime($row['waktu_kegiatan'])) ?></p>
                                <p class="text-gray-500 mt-1"><?= htmlspecialchars($row['lokasi'] ?? '') ?></p>
                                <p class="text-gray-500 mt-1"><strong>Status:</strong> 
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        <?= $row['status'] == 'Berlangsung' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' ?>">
                                        <?= htmlspecialchars($row['status'] ?? '') ?>
                                    </span>
                                </p>
                                <?php if ($user_presensi_status): ?>
                                    <p class="text-gray-500 mt-2"><strong>Presensi Anda:</strong> 
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                            <?= $user_presensi_status == 'Hadir' ? 'bg-green-100 text-green-800' : 
                                                ($user_presensi_status == 'Izin' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                            <?= htmlspecialchars($user_presensi_status) ?>
                                        </span>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Modal -->
                            <div id="modal-<?= $row['id_kegiatan'] ?>" class="modal fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50" data-modal-id="<?= $row['id_kegiatan'] ?>">
                                <div class="bg-white rounded-lg p-6 w-full max-w-2xl relative max-h-[90vh] overflow-y-auto">
                                    <button onclick="closeModal(<?= $row['id_kegiatan'] ?>)" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
                                    <h2 class="text-2xl font-bold text-blue-700 mb-1"><?= htmlspecialchars($row['judul_kegiatan'] ?? '') ?></h2>
                                    <p class="text-sm text-gray-600"><?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?> | <?= date('H:i', strtotime($row['waktu_kegiatan'])) ?></p>
                                    <p class="text-gray-600 mt-2"><strong>Lokasi:</strong> <?= htmlspecialchars($row['lokasi']) ?></p>
                                    <p class="text-gray-600 mt-2"><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>
                                    <p class="text-gray-600 mt-2"><strong>Dibuat oleh:</strong> <?= htmlspecialchars($row['dibuat_oleh']) ?></p>
                                    
                                    <div class="mt-2 flex items-center justify-between">
                                        <p class="text-sm">
                                            <strong>Status kegiatan:</strong> 
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                <?= $row['status'] == 'Berlangsung' ? 'bg-yellow-100 text-yellow-800' : 
                                                    ($row['status'] == 'Selesai' ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800') ?>">
                                                <?= htmlspecialchars($row['status'] ?? '') ?>
                                            </span>
                                        </p>
                                    </div>

                                    <!-- Form Presensi untuk User -->
                                    <?php if ($id_anggota && $row['status'] == 'Berlangsung'): ?>
                                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                                            <h3 class="text-lg font-semibold mb-2">Presensi Anda</h3>
                                            <?php if ($user_presensi_status == null): ?>
                                                <form method="POST" action="">
                                                    <input type="hidden" name="id_kegiatan" value="<?= $row['id_kegiatan'] ?>">
                                                    <div class="flex gap-2 mb-3">
                                                        <label class="flex items-center cursor-pointer">
                                                            <input type="radio" name="status_presensi" value="Hadir" required class="mr-2">
                                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full">Hadir</span>
                                                        </label>
                                                        <label class="flex items-center cursor-pointer">
                                                            <input type="radio" name="status_presensi" value="Izin" required class="mr-2">
                                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full">Izin</span>
                                                        </label>
                                                        <label class="flex items-center cursor-pointer">
                                                            <input type="radio" name="status_presensi" value="Tidak Hadir" required class="mr-2">
                                                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full">Tidak Hadir</span>
                                                        </label>
                                                    </div>
                                                    <button type="submit" name="submit_presensi" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                                        Kirim Presensi
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <p class="text-gray-600">Anda sudah melakukan presensi: 
                                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                        <?= $user_presensi_status == 'Hadir' ? 'bg-green-100 text-green-800' : 
                                                            ($user_presensi_status == 'Izin' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                                        <?= htmlspecialchars($user_presensi_status) ?>
                                                    </span>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif (!$id_anggota): ?>
                                        <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
                                            <p class="text-yellow-800">Anda belum terdaftar sebagai anggota. Silakan daftar terlebih dahulu untuk melakukan presensi.</p>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Daftar Presensi -->
                                    <?php
                                    $id_kegiatan = $row['id_kegiatan'];
                                    
                                    // Query untuk mendapatkan semua presensi kegiatan ini
                                    $sql_presensi = "
                                        SELECT 
                                            anggota.nama, 
                                            anggota.npm,
                                            presensi.presensi as status_presensi,
                                            presensi.waktu_presensi 
                                        FROM presensi 
                                        JOIN anggota ON presensi.id_anggota = anggota.id_anggota 
                                        WHERE presensi.id_kegiatan = $id_kegiatan
                                        ORDER BY 
                                            CASE presensi.presensi 
                                                WHEN 'Hadir' THEN 1
                                                WHEN 'Izin' THEN 2
                                                WHEN 'Tidak Hadir' THEN 3
                                            END,
                                            anggota.nama ASC
                                    ";
                                    $result_presensi = mysqli_query($koneksi, $sql_presensi);
                                    
                                    // Hitung statistik presensi
                                    $sql_stats = "
                                        SELECT 
                                            presensi,
                                            COUNT(*) as jumlah
                                        FROM presensi
                                        WHERE id_kegiatan = $id_kegiatan AND presensi IS NOT NULL
                                        GROUP BY presensi
                                    ";
                                    $result_stats = mysqli_query($koneksi, $sql_stats);
                                    
                                    $stats = ['Hadir' => 0, 'Izin' => 0, 'Tidak Hadir' => 0];
                                    while ($stat = mysqli_fetch_assoc($result_stats)) {
                                        $stats[$stat['presensi']] = $stat['jumlah'];
                                    }
                                    
                                    $total_peserta = array_sum($stats);
                                    ?>

                                    <div class="mt-6">
                                        <h3 class="text-lg font-semibold mb-2">Daftar Kehadiran</h3>
                                        
                                        <!-- Tabel Presensi -->
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full border text-sm text-left">
                                                <thead>
                                                    <tr class="bg-gray-100">
                                                        <th class="border px-4 py-2">#</th>
                                                        <th class="border px-4 py-2">Nama</th>
                                                        <th class="border px-4 py-2">NPM</th>
                                                        <th class="border px-4 py-2">Status</th>
                                                        <th class="border px-4 py-2">Waktu Presensi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no = 1; 
                                                    if ($result_presensi && mysqli_num_rows($result_presensi) > 0):
                                                        while ($p = mysqli_fetch_assoc($result_presensi)): 
                                                            if ($p['status_presensi'] != null):
                                                    ?>
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="border px-4 py-2"><?= $no++ ?></td>
                                                            <td class="border px-4 py-2"><?= htmlspecialchars($p['nama'] ?? '') ?></td>
                                                            <td class="border px-4 py-2"><?= htmlspecialchars($p['npm'] ?? '') ?></td>
                                                            <td class="border px-4                                                             <td class="border px-4 py-2">
                                                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                                    <?= $p['status_presensi'] == 'Hadir' ? 'bg-green-100 text-green-800' : 
                                                                        ($p['status_presensi'] == 'Izin' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                                                    <?= htmlspecialchars($p['status_presensi'] ?? '') ?>
                                                                </span>
                                                            </td>
                                                            <td class="border px-4 py-2">
                                                                <?= $p['waktu_presensi'] ? date('d M Y H:i', strtotime($p['waktu_presensi'])) : '-' ?>
                                                            </td>
                                                        </tr>
                                                    <?php 
                                                            endif;
                                                        endwhile;
                                                    else: 
                                                    ?>
                                                        <tr>
                                                            <td colspan="5" class="border px-4 py-2 text-center text-gray-500">Belum ada data presensi.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                            
                                            <?php if ($total_peserta > 0): ?>
                                            <div class="mt-4 flex justify-between items-center">
                                                <p class="text-sm text-gray-600">
                                                    Total Peserta: <span class="font-semibold"><?= $total_peserta ?></span> orang
                                                </p>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full text-center py-8">
                            <p class="text-gray-500">Tidak ada data kegiatan yang ditemukan.</p>
                        </div>
                    <?php endif; ?>
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

    // Style radio buttons
    document.querySelectorAll('input[type="radio"][name="status_presensi"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Reset all labels
            document.querySelectorAll('label span').forEach(span => {
                span.classList.remove('ring-2', 'ring-offset-2');
            });
            
            // Add ring to selected label
            if (this.checked) {
                this.nextElementSibling.classList.add('ring-2', 'ring-offset-2');
                if (this.value === 'Hadir') {
                    this.nextElementSibling.classList.add('ring-green-600');
                } else if (this.value === 'Izin') {
                    this.nextElementSibling.classList.add('ring-yellow-600');
                } else {
                    this.nextElementSibling.classList.add('ring-red-600');
                }
            }
        });
    });
    </script>

</body>
</html>