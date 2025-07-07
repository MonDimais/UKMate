<?php 
date_default_timezone_set('Asia/Jakarta');

session_start();
include '../koneksi.php';

// Waktu sekarang
$current_time = date('Y-m-d H:i:s');

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

// Simpan data ke array (TIDAK PERLU mysqli_data_seek)
$kegiatan_data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $kegiatan_data[] = $row;
}

// Presensi handling
$id_kegiatan = isset($_GET['id_kegiatan']) ? (int)$_GET['id_kegiatan'] : 0;

if ($id_kegiatan > 0) {
    $sql_presensi = "
        SELECT anggota.nama, presensi.waktu_presensi 
        FROM presensi 
        JOIN anggota ON presensi.id_anggota = anggota.id_anggota 
        WHERE presensi.id_kegiatan = $id_kegiatan AND presensi.hadir = 1
    ";
    
    $presensi = mysqli_query($koneksi, $sql_presensi);
    
    if (!$presensi) {
        die("Presensi Query Error: " . mysqli_error($koneksi));
    }
} else {
    $presensi = false;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | UKM Fasilkom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

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
                </div> <!-- Pastikan div ini ditutup dengan benar -->

                <!-- Kegiatan List -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                    <?php if (count($kegiatan_data) > 0): ?>
                        <?php foreach ($kegiatan_data as $row): ?>
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
                                        WHERE id_kegiatan = $id_kegiatan
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
                                        
                                        <!-- Statistik Presensi -->
                                        <div class="grid grid-cols-3 gap-2 mb-4">
                                            <div class="bg-green-50 border border-green-200 rounded p-2 text-center">
                                                <p class="text-sm text-green-800 font-semibold">Hadir</p>
                                                <p class="text-xl font-bold text-green-600"><?= $stats['Hadir'] ?></p>
                                            </div>
                                            <div class="bg-yellow-50 border border-yellow-200 rounded p-2 text-center">
                                                <p class="text-sm text-yellow-800 font-semibold">Izin</p>
                                                <p class="text-xl font-bold text-yellow-600"><?= $stats['Izin'] ?></p>
                                            </div>
                                            <div class="bg-red-50 border border-red-200 rounded p-2 text-center">
                                                <p class="text-sm text-red-800 font-semibold">Tidak Hadir</p>
                                                <p class="text-xl font-bold text-red-600"><?= $stats['Tidak Hadir'] ?></p>
                                            </div>
                                        </div>
                                        
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
                                                    ?>
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="border px-4 py-2"><?= $no++ ?></td>
                                                            <td class="border px-4 py-2"><?= htmlspecialchars($p['nama'] ?? '') ?></td>
                                                            <td class="border px-4 py-2"><?= htmlspecialchars($p['npm'] ?? '') ?></td>
                                                            <td class="border px-4 py-2">
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
                                                <button onclick="downloadPDF(<?= $row['id_kegiatan'] ?>)" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Download PDF
                                                </button>
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


    async function downloadPDF(id) {
        const doc = new jspdf.jsPDF();

        const modal = document.querySelector(`#modal-${id}`);
        const title = modal.querySelector('h2').innerText;
        const dateTime = modal.querySelector('p.text-sm').innerText;

        const table = modal.querySelector("table");
        const rows = [...table.querySelectorAll("tbody tr")].map(tr => {
            return [...tr.querySelectorAll("td")].map(td => td.innerText);
        }).filter(row => row.length > 0 && row[1] !== 'Belum ada yang presensi.');

        doc.setFontSize(16);
        doc.text(title, 14, 15);
        doc.setFontSize(12);
        doc.text(dateTime, 14, 23);

        doc.autoTable({
            head: [['No', 'Nama', 'Waktu Presensi']],
            body: rows,
            startY: 30,
        });

        doc.save(`${title.replace(/\s+/g, '_')}_Presensi.pdf`);
    }

    </script>


</body>
</html>
