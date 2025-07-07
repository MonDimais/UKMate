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

// Ambil data UKM berdasarkan id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id_ukm = $_GET['id'];
$ukm = null;
try {
    $query = "SELECT * FROM ukm WHERE id = ? AND status != 'Nonaktif'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_ukm);
    $stmt->execute();
    $result = $stmt->get_result();
    $ukm = $result->fetch_assoc();
    if (!$ukm) {
        header("Location: dashboard.php");
        exit();
    }
} catch (Exception $e) {
    $error_ukm = "Gagal memuat data UKM: " . $e->getMessage();
}

// Ambil jadwal kegiatan UKM
$jadwal = [];
try {
    $query = "SELECT * FROM jadwal_kegiatan WHERE id_ukm = ? ORDER BY tanggal DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_ukm);
    $stmt->execute();
    $result = $stmt->get_result();
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
    <title>Detail UKM - <?php echo htmlspecialchars($ukm['nama']); ?></title>
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
        .ukm-icon { font-size: 3rem; margin-bottom: 15px; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>
            <div class="col-md-9 col-lg-10 main-content">
                <h2 class="mb-4"><i class="fas fa-info-circle me-2"></i>Detail UKM</h2>

                <!-- Pesan Error -->
                <?php if (isset($error_ukm)): ?>
                    <div class="alert alert-danger"><?php echo $error_ukm; ?></div>
                <?php endif; ?>

                <!-- Detail UKM -->
                <?php if ($ukm): ?>
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <i class="fas <?= htmlspecialchars($ukm['icon']) ?> ukm-icon 
                                <?php 
                                $ukm_lower = strtolower($ukm['nama']);
                                if (strpos($ukm_lower, 'voli') !== false) echo 'text-primary';
                                elseif (strpos($ukm_lower, 'basket') !== false) echo 'text-danger';
                                elseif (strpos($ukm_lower, 'badminton') !== false) echo 'text-success';
                                elseif (strpos($ukm_lower, 'futsal') !== false) echo 'text-warning';
                                elseif (strpos($ukm_lower, 'tenis') !== false) echo 'text-info';
                                else echo 'text-secondary';
                                ?>">
                            </i>
                            <h3 class="card-title"><?= htmlspecialchars($ukm['nama']) ?></h3>
                            <p class="card-text"><?= htmlspecialchars($ukm['deskripsi']) ?></p>
                            <div class="d-flex justify-content-center gap-4 small text-muted">
                                <span><i class="fas fa-users me-1"></i> <?= $ukm['jumlah_anggota'] ?> Anggota</span>
                                <span><i class="fas fa-star me-1"></i> <?= number_format($ukm['rating'], 1) ?>/5</span>
                                <span><i class="fas fa-info-circle me-1"></i> Status: 
                                    <span class="badge 
                                        <?php 
                                        switch($ukm['status']) {
                                            case 'Aktif': echo 'bg-primary'; break;
                                            case 'Coming Soon': echo 'bg-warning'; break;
                                            case 'Libur': echo 'bg-secondary'; break;
                                            default: echo 'bg-light text-dark';
                                        }
                                        ?>">
                                        <?= htmlspecialchars($ukm['status']) ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal Kegiatan UKM -->
                    <h4 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Jadwal Kegiatan</h4>
                    <div class="card">
                        <div class="card-body">
                            <?php if (!empty($jadwal)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Kegiatan</th>
                                                <th>Waktu</th>
                                                <th>Tempat</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($jadwal as $item): ?>
                                                <tr>
                                                    <td><?= date('d M Y', strtotime($item['tanggal'])) ?></td>
                                                    <td><?= htmlspecialchars($item['nama_kegiatan']) ?></td>
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
                                <div class="alert alert-info">Tidak ada jadwal kegiatan untuk UKM ini.</div>
                            <?php endif; ?>
                            <?php if (isset($error_jadwal)): ?>
                                <div class="alert alert-danger"><?php echo $error_jadwal; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">UKM tidak ditemukan atau tidak aktif.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>