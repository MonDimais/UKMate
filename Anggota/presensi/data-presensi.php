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


$sql_riwayat = "
    SELECT 
        k.judul_kegiatan, 
        k.tanggal_kegiatan,
        k.waktu_kegiatan,
        k.lokasi,
        p.presensi,
        p.waktu_presensi
    FROM presensi p
    JOIN kegiatan k ON p.id_kegiatan = k.id_kegiatan
    WHERE p.id_anggota = $id_anggota
    ORDER BY p.waktu_presensi DESC
";

$result_riwayat = mysqli_query($koneksi, $sql_riwayat);

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
                <div class="p-6 bg-white rounded-2xl shadow">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Riwayat Presensi</h2>
    
                    <?php if (mysqli_num_rows($result_riwayat) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-gray-700 text-left font-semibold">
                                <tr>
                                    <th class="px-4 py-2">#</th>
                                    <th class="px-4 py-2">Kegiatan</th>
                                    <th class="px-4 py-2">Tanggal</th>
                                    <th class="px-4 py-2">Waktu</th>
                                    <th class="px-4 py-2">Lokasi</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Waktu Presensi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php $no = 1; ?>
                                <?php while ($row = mysqli_fetch_assoc($result_riwayat)): ?>
                                    <tr class="hover:bg-gray-50 transition-all duration-200">
                                        <td class="px-4 py-2"><?= $no++ ?></td>
                                        <td class="px-4 py-2 font-medium text-gray-800"><?= htmlspecialchars($row['judul_kegiatan']) ?></td>
                                        <td class="px-4 py-2"><?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?></td>
                                        <td class="px-4 py-2"><?= date('H:i', strtotime($row['waktu_kegiatan'])) ?></td>
                                        <td class="px-4 py-2"><?= htmlspecialchars($row['lokasi']) ?></td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 rounded-full text-xs font-bold tracking-wide 
                                                <?= $row['presensi'] == 'Hadir' ? 'bg-green-100 text-green-700' : 
                                                    ($row['presensi'] == 'Izin' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') ?>">
                                                <?= htmlspecialchars($row['presensi']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-2"><?= date('d M Y H:i', strtotime($row['waktu_presensi'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="bg-yellow-50 text-yellow-800 p-4 rounded mt-4 border border-yellow-200">
                            <p class="text-sm">Belum ada riwayat presensi yang tercatat.</p>
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