<?php
session_start();

// Cek apakah user adalah anggota
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'anggota') {
    header("Location: ../../login.php");
    exit();
}

// Include koneksi database
try {
    require_once '../config/database.php';
} catch (Exception $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

$user_id = $_SESSION['user']['id'];

// Ambil daftar anggota UKM lain
$anggota = [];
try {
    $query = "SELECT nama, fakultas 
              FROM users 
              WHERE role = 'anggota' AND id != ?
              ORDER BY nama ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $anggota = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "Gagal memuat daftar anggota: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anggota UKM - UKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container { margin-top: 20px; }
        .card { margin-bottom: 20px; }
        .profile-dropdown { cursor: pointer; }
        .dropdown-menu { min-width: 200px; }
        .sidebar { min-height: calc(100vh - 56px); background-color: #f8f9fa; padding: 20px 0; }
        .sidebar .nav-link { color: #333; padding: 10px 20px; border-radius: 5px; margin-bottom: 5px; }
        .sidebar .nav-link:hover { background-color: #e9ecef; }
        .sidebar .nav-link.active { background-color: #0d6efd; color: white; }
        .main-content { padding: 20px; }
    </style>
    <script src="../includes/jquery-3.7.1.min.js"></script>
</head>
<body>
      <!-- Navbar dan Sidebar -->
    <?php include '../includes/navbar.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>
            <div class="col-md-9 col-lg-10 main-content">
                <h2 class="mb-4"><i class="fas fa-users me-2"></i></i>Anggota UKM</h2>
    <!-- Navbar dan Sidebar (sesuaikan dengan struktur Anda) -->
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Anggota UKM</h4>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php elseif (!empty($anggota)): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Fakultas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($anggota as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($item['fakultas'] ?: '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">Tidak ada anggota UKM lain yang ditemukan.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>