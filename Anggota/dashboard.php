<?php
include 'koneksi.php';

// Ambil total kegiatan
$total_kegiatan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kegiatan"))['total'];

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

// Grafik kegiatan per bulan
$label_bulan = [];
$jumlah_kegiatan_bulanan = [];
for ($i = 1; $i <= 12; $i++) {
    $query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kegiatan WHERE MONTH(tanggal_kegiatan) = $i");
    $row = mysqli_fetch_assoc($query);
    $label_bulan[] = date("M", mktime(0, 0, 0, $i, 10));
    $jumlah_kegiatan_bulanan[] = $row['total'];
}

// Ambil jadwal kegiatan seminggu kedepan
$tanggal_sekarang = date('Y-m-d');
$tanggal_seminggu = date('Y-m-d', strtotime('+7 days'));
$kegiatan_mendatang = mysqli_query($koneksi, "
    SELECT * FROM kegiatan 
    WHERE tanggal_kegiatan BETWEEN '$tanggal_sekarang' AND '$tanggal_seminggu' 
    AND status != 'Selesai'
    ORDER BY tanggal_kegiatan ASC, waktu_kegiatan ASC
");

// Ambil pengumuman terbaru
$pengumuman = mysqli_query($koneksi, "
    SELECT * FROM pengumuman 
    ORDER BY created_at DESC 
    LIMIT 5
");

// Cek jumlah pendaftar belum dikonfirmasi
$pendaftar_belum_dikonfirmasi = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pendaftaran WHERE status = 'pending'"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggota | UKM Fasilkom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen">
    <?php include 'sidebar.php'; ?>
    <div class="flex-1 flex flex-col overflow-auto sm:ml-0 md:ml-80 lg:ml-80 xl:ml-80">
        <?php include 'navbar.php'; ?>

        <section class="p-6 space-y-6">
            <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

            <!-- Notifikasi Pendaftar -->
            <?php if ($pendaftar_belum_dikonfirmasi > 0): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
                    <p class="font-semibold">⚠️ Ada <?php echo $pendaftar_belum_dikonfirmasi; ?> pendaftar yang belum dikonfirmasi.</p>
                    <a href="anggota/data-anggota.php" class="text-blue-600 underline">Lihat Sekarang</a>
                </div>
            <?php endif; ?>

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

            <!-- Jadwal Kegiatan Terdekat -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-2xl font-bold mb-4">Jadwal Kegiatan Minggu Ini</h2>
                <?php if (mysqli_num_rows($kegiatan_mendatang) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kegiatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php while ($row = mysqli_fetch_assoc($kegiatan_mendatang)): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= date('H:i', strtotime($row['waktu_kegiatan'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($row['judul_kegiatan']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($row['lokasi'] ?? '-') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?= $row['status'] == 'Terjadwal' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' ?>">
                                                <?= $row['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">Tidak ada kegiatan terjadwal untuk minggu ini.</p>
                <?php endif; ?>
            </div>

            <!-- Pengumuman -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-2xl font-bold mb-4">Pengumuman Terbaru</h2>
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
        </section>
    </div>
</div>

<script>
const ctxKegiatan = document.getElementById('kegiatanChart').getContext('2d');

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
</script>
</body>
</html>