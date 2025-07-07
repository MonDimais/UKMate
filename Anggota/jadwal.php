<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

// Include koneksi database
require_once '../config/database.php';

$user = $_SESSION['user'];

// Ambil semua jadwal kegiatan
$jadwal = [];
try {
    $query = "SELECT j.*, u.nama as nama_ukm 
              FROM jadwal_kegiatan j 
              JOIN ukm u ON j.id_ukm = u.id 
              ORDER BY j.tanggal DESC, j.waktu_mulai ASC";
    $result = $conn->query($query);
    $jadwal = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error_jadwal = "Gagal memuat jadwal: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Kegiatan UKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-dropdown { cursor: pointer; }
        .dropdown-menu { min-width: 200px; }
        .sidebar { min-height: calc(100vh - 56px); background-color: #f8f9fa; padding: 20px 0; }
        .sidebar .nav-link { color: #333; padding: 10px 20px; border-radius: 5px; margin-bottom: 5px; }
        .sidebar .nav-link:hover { background-color: #e9ecef; }
        .sidebar .nav-link.active { background-color: #0d6efd; color: white; }
        .main-content { padding: 20px; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>
            <div class="col-md-9 col-lg-10 main-content">
                <h2 class="mb-4"><i class="fas fa-calendar-alt me-2"></i>Jadwal Kegiatan UKM</h2>

                <!-- Pesan Error -->
                <?php if (isset($error_jadwal)): ?>
                    <div class="alert alert-danger"><?php echo $error_jadwal; ?></div>
                <?php endif; ?>

                <!-- Tabel Jadwal -->
                <div class="card">
                    <div class="card-body">
                        <?php if (!empty($jadwal)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>UKM</th>
                                            <th>Kegiatan</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Tempat</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($jadwal as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['nama_ukm']) ?></td>
                                                <td><?= htmlspecialchars($item['nama_kegiatan']) ?></td>
                                                <td><?= date('d M Y', strtotime($item['tanggal'])) ?></td>
                                                <td><?= date('H:i', strtotime($item['waktu_mulai'])) ?> - <?= date('H:i', strtotime($item['waktu_selesai'])) ?></td>
                                                <td><?= htmlspecialchars($item['tempat']) ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?= strtotime($item['tanggal']) > strtotime('today') ? 'bg-success' : 'bg-secondary' ?>">
                                                        <?= strtotime($item['tanggal']) > strtotime('today') ? 'Akan Datang' : 'Selesai' ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">Tidak ada jadwal kegiatan tersedia.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>