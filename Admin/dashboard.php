<?php
include 'koneksi.php';

// Ambil total kegiatan
$total_kegiatan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kegiatan"))['total'];

// Ambil total anggota
$total_anggota = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM anggota"))['total'];

// Ambil total presensi hari ini (yang hadir)
$tanggal_hari_ini = date('Y-m-d');
$total_presensi_hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) as total FROM presensi 
    WHERE DATE(waktu_presensi) = '$tanggal_hari_ini' AND presensi = 'Hadir'
"))['total'];

// Grafik kegiatan per bulan
$label_bulan = [];
$jumlah_kegiatan_bulanan = [];
for ($i = 1; $i <= 12; $i++) {
    $query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kegiatan WHERE MONTH(tanggal_kegiatan) = $i");
    $row = mysqli_fetch_assoc($query);
    $label_bulan[] = date("M", mktime(0, 0, 0, $i, 10));
    $jumlah_kegiatan_bulanan[] = $row['total'];
}

// Grafik presensi 7 hari terakhir
$label_hari = [];
$jumlah_presensi_harian = [];
for ($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i days"));
    $query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM presensi WHERE DATE(waktu_presensi) = '$tanggal' AND presensi = 'Hadir'");
    $label_hari[] = date('D', strtotime($tanggal));
    $jumlah_presensi_harian[] = mysqli_fetch_assoc($query)['total'];
}

// Ambil data kegiatan untuk kalender
$query = mysqli_query($koneksi, "SELECT judul_kegiatan, tanggal_kegiatan FROM kegiatan");
$events = [];
while ($row = mysqli_fetch_assoc($query)) {
    $events[] = [
        'title' => $row['judul_kegiatan'],
        'start' => $row['tanggal_kegiatan'],
    ];
}

// Ambil bulan dan tahun saat ini
$bulan_ini = date('m');
$tahun_ini = date('Y');

// Query untuk menghitung kegiatan pada bulan ini
$query_bulan_ini = "SELECT COUNT(*) as jumlah FROM kegiatan 
                    WHERE MONTH(tanggal_kegiatan) = '$bulan_ini' 
                    AND YEAR(tanggal_kegiatan) = '$tahun_ini'";
$result_bulan_ini = mysqli_query($koneksi, $query_bulan_ini);
$data_bulan_ini = mysqli_fetch_assoc($result_bulan_ini);
$kegiatan_bulan_ini = $data_bulan_ini['jumlah'];

// Cek jumlah pendaftar belum dikonfirmasi
$pendaftar_belum_dikonfirmasi = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pendaftaran WHERE status = 'pending'"));

// Ambil 5 kegiatan terbaru
$kegiatan_terbaru = mysqli_query($koneksi, "SELECT * FROM kegiatan ORDER BY tanggal_kegiatan DESC LIMIT 5");

// Ambil pengumuman terbaru
$pengumuman = mysqli_query($koneksi, "
    SELECT * FROM pengumuman 
    ORDER BY created_at DESC 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | UKM Fasilkom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen">
    <?php include 'sidebar.php'; ?>
    <div class="flex-1 flex flex-col overflow-auto sm:ml-0 md:ml-80 lg:ml-80 xl:ml-80">
        <?php include 'navbar.php'; ?>

        <section class="p-6 space-y-6">
            <h1 class="text-2xl font-bold mb-4">Dashboard</h1>


            <!-- Pengumuman -->
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">Pengumuman</h2>
                    <button 
                        id="openModalBtn"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        Buat Pengumuman
                    </button>
                </div>

                <?php if (mysqli_num_rows($pengumuman) > 0): ?>
                    <div class="space-y-4">
                        <?php while ($row = mysqli_fetch_assoc($pengumuman)): ?>
                            <div class="border-l-4 border-blue-500 pl-4 py-2">
                                <h3 class="font-semibold text-lg"><?= htmlspecialchars($row['judul']) ?></h3>
                                <p class="text-gray-600 mt-1"><?= nl2br(htmlspecialchars($row['konten'])) ?></p>
                                <p class="text-sm text-gray-400 mt-2"><?= date('d M Y H:i', strtotime($row['created_at'])) ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">Belum ada pengumuman.</p>
                <?php endif; ?>
            </div>

            <!-- Modal Popup -->
            <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
                    <button id="closeModalBtn" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
                    <h3 class="text-xl font-bold mb-4">Buat Pengumuman Baru</h3>
                    <form action="proses_buat_pengumuman.php" method="post">
                        <label for="judul" class="block mb-1 font-semibold">Judul</label>
                        <input type="text" name="judul" id="judul" required class="w-full border rounded px-3 py-2 mb-4">

                        <label for="konten" class="block mb-1 font-semibold">Konten</label>
                        <textarea name="konten" id="konten" rows="4" required class="w-full border rounded px-3 py-2 mb-4"></textarea>

                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            Simpan Pengumuman
                        </button>
                    </form>
                </div>
            </div>

            <!-- Info Kegiatan -->
            <div class="grid grid-cols-1 gap-6">
                <h2 class="text-2xl font-bold mb-2">Info Kegiatan</h2>

                <div class="grid md:grid-cols-[30%_70%] gap-6">
                    <!-- Kolom kiri (30%): total & bulan ini -->
                    <div class="grid grid-rows-2 gap-6">
                        <!-- Total Kegiatan -->
                        <div class="bg-white p-6 rounded-lg shadow text-center">
                            <h3 class="text-lg font-semibold mb-2">Total Kegiatan</h3>
                            <p class="text-3xl text-blue-600 font-bold"><?= $total_kegiatan ?></p>
                        </div>

                        <!-- Kegiatan Bulan Ini -->
                        <div class="bg-white p-6 rounded-lg shadow text-center">
                            <h3 class="text-lg font-semibold mb-2">Kegiatan Bulan Ini</h3>
                            <p class="text-3xl text-green-600 font-bold"><?= $kegiatan_bulan_ini ?></p>
                        </div>
                    </div>

                    <!-- Kolom kanan (70%): grafik -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4">Grafik Kegiatan Per Bulan</h3>
                        <canvas id="kegiatanChart" height="100"></canvas>
                    </div>
                </div>
            </div>


            <!-- Info Anggota -->
            <div class="grid md:grid-cols-5 gap-4 mb-6">
                <h2 class="col-span-5 text-2x1 font-bold mb-2">Info Anggota</h2>
                <div class="col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow text-center h-full">
                        <h3 class="text-lg font-semibold mb-2">Total Anggota</h3>
                        <p class="text-3xl text-green-600 font-bold"><?php echo $total_anggota; ?></p>
                    </div>
                </div>
                <?php if ($pendaftar_belum_dikonfirmasi > 0): ?>
                    <div class="col-span-4">
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded h-full flex items-center justify-between">
                            <p class="font-semibold">⚠️ Ada <?php echo $pendaftar_belum_dikonfirmasi; ?> pendaftar yang belum dikonfirmasi.</p>
                            <a href="anggota/data-anggota.php" class="text-blue-600 underline">Lihat Sekarang</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="col-span-4">
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded h-full flex items-center justify-between">
                            <p class="font-semibold">✅ Tidak ada pendaftar yang belum dikonfirmasi.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Info Presensi -->
            <div class="grid md:grid-cols-2 gap-4">
                <h2 class="col-span-2 text-2x1 font-bold mb-2">Info Presensi</h2>
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <h3 class="text-lg font-semibold mb-2">Presensi Hari Ini</h3>
                    <p class="text-3xl text-yellow-600 font-bold"><?php echo $total_presensi_hari_ini; ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-bold mb-4">Grafik Presensi 7 Hari Terakhir</h3>
                    <canvas id="presensiChart"></canvas>
                </div>
            </div>
        </section>
    </div>
</div>

<script>

// Buka modal
document.getElementById('openModalBtn').addEventListener('click', function() {
    document.getElementById('modal').classList.remove('hidden');
});

// Tutup modal
document.getElementById('closeModalBtn').addEventListener('click', function() {
    document.getElementById('modal').classList.add('hidden');
});

// Tutup modal ketika klik di luar konten modal
window.addEventListener('click', function(e) {
    const modal = document.getElementById('modal');
    if (e.target === modal) {
        modal.classList.add('hidden');
    }
});

const ctxKegiatan = document.getElementById('kegiatanChart').getContext('2d');
const ctxPresensi = document.getElementById('presensiChart').getContext('2d');

new Chart(ctxKegiatan, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($label_bulan); ?>,
        datasets: [{
            label: 'Jumlah Kegiatan',
            data: <?php echo json_encode($jumlah_kegiatan_bulanan); ?>,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            tension: 0.3,
            fill: true,
            pointRadius: 5,
            pointHoverRadius: 7,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: true }, tooltip: { mode: 'index', intersect: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        if (Number.isInteger(value)) return value;
                    }
                }
            }
        }
    }
});

new Chart(ctxPresensi, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($label_hari); ?>,
        datasets: [{
            label: 'Jumlah Hadir',
            data: <?php echo json_encode($jumlah_presensi_harian); ?>,
            backgroundColor: 'rgb(234, 179, 8)'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: true }, tooltip: { mode: 'index', intersect: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        if (Number.isInteger(value)) return value;
                    }
                }
            }
        }
    }
});
</script>
</body>
</html>
