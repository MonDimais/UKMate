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

// Ambil daftar kegiatan yang belum selesai
$kegiatan = [];
try {
    $query = "SELECT j.*, u.nama as nama_ukm 
              FROM jadwal_kegiatan j 
              JOIN ukm u ON j.id_ukm = u.id 
              WHERE j.tanggal >= CURDATE() 
              AND NOT EXISTS (
                  SELECT 1 FROM kehadiran k 
                  WHERE k.id_jadwal = j.id AND k.id_user = ?
              )
              ORDER BY j.tanggal ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $kegiatan = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error_kegiatan = "Gagal memuat kegiatan: " . $e->getMessage();
}

// Ambil riwayat kehadiran
$riwayat = [];
try {
    $query = "SELECT k.*, j.nama_kegiatan, j.tanggal, u.nama as nama_ukm 
              FROM kehadiran k 
              JOIN jadwal_kegiatan j ON k.id_jadwal = j.id 
              JOIN ukm u ON j.id_ukm = u.id 
              WHERE k.id_user = ? 
              ORDER BY j.tanggal DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $riwayat = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error_riwayat = "Gagal memuat riwayat kehadiran: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kehadiran UKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-buttons button { margin-right: 5px; }
        .status-btn.selected { background-color: #007bff; color: white; }
        .kegiatan-item { border-bottom: 1px solid #ddd; padding: 15px 0; }

/* Profile Dropdown */
.profile-dropdown {
    cursor: pointer;
}
.dropdown-menu {
    min-width: 150px;
}

/* Sidebar */
.sidebar {
    min-height: calc(100vh - 56px);
    background-color: #f8f9fa;
    padding: 20px 0;
}
.sidebar .nav-link {
    color: #333;
    padding: 10px 20px;
    border-radius: 5px;
    margin-bottom: 5px;
}
.sidebar .nav-link:hover {
    background-color: #e9ecef;
}
.sidebar .nav-link.active {
    background-color: #0d6efd;
    color: white;
}

/* Main Content */
.main-content {
    padding: 20px;
}

/* UKM Card Styles */
.ukm-card {
    transition: transform 0.3s;
    height: 100%;
}
.ukm-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.ukm-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
}
.badge-ukm {
    position: absolute;
    top: 10px;
    right: 10px;
}

/* Login and Registration Card */
.card-login, .card-register {
    max-width: 500px;
    margin: 50px auto;
}
.form-label {
    font-weight: bold;
}
.alert {
    margin-bottom: 20px;
}

/* Kehadiran Styles */
.kegiatan-item {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    background-color: #fff;
}
.kegiatan-item h5 {
    margin-bottom: 10px;
}
.status-btn {
    margin-right: 10px;
    padding: 5px 15px;
    border-radius: 5px;
    cursor: pointer;
}
.status-btn.hadir {
    background-color: #28a745;
    color: white;
}
.status-btn.izin {
    background-color: #ffc107;
    color: black;
}
.status-btn.tidak-hadir {
    background-color: #dc3545;
    color: white;
}
.status-btn.selected {
    font-weight: bold;
    border: 2px solid #000;
}
.keterangan-input {
    margin-top: 10px;
    width: 100%;
}
.save-btn {
    margin-top: 10px;
}

/* Responsive Adjustments */
@media (max-width: 767.98px) {
    .sidebar {
        position: fixed;
        top: 56px;
        left: 0;
        width: 250px;
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    .sidebar.show {
        transform: translateX(0);
    }
    .main-content {
        padding: 15px;
    }
    .kegiatan-item {
        padding: 10px;
    }
    .status-btn {
        padding: 5px 10px;
        font-size: 0.9rem;
    }
}
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>
            <div class="col-md-9 col-lg-10 main-content">
                <h2 class="mb-4"><i class="fas fa-check-circle me-2"></i>Kehadiran UKM</h2>

                <!-- Pesan Error -->
                <?php if (isset($error_kegiatan)): ?>
                    <div class="alert alert-danger"><?php echo $error_kegiatan; ?></div>
                <?php endif; ?>

                  <!-- Navbar dan Sidebar (sesuaikan dengan struktur Anda) -->
    <div class="container mt-4">
        <!-- Daftar Kegiatan -->
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Isi Kehadiran</h4>
                <?php if (isset($error_kegiatan)): ?>
                    <div class="alert alert-danger"><?php echo $error_kegiatan; ?></div>
                <?php elseif (!empty($kegiatan)): ?>
                    <?php foreach ($kegiatan as $item): ?>
                        <div class="kegiatan-item" id="kegiatan-<?php echo $item['id']; ?>">
                            <h5><?php echo htmlspecialchars($item['nama_kegiatan']); ?> (<?php echo htmlspecialchars($item['nama_ukm']); ?>)</h5>
                            <p class="mb-2">
                                <strong>Tanggal:</strong> <?php echo date('d M Y', strtotime($item['tanggal'])); ?><br>
                                <strong>Waktu:</strong> <?php echo date('H:i', strtotime($item['waktu_mulai'])); ?> - <?php echo date('H:i', strtotime($item['waktu_selesai'])); ?><br>
                                <strong>Tempat:</strong> <?php echo htmlspecialchars($item['tempat']); ?>
                            </p>
                            <div class="status-buttons mb-2">
                                <button class="status-btn hadir btn btn-outline-primary" data-status="Hadir" data-id="<?php echo $item['id']; ?>">Hadir</button>
                                <button class="status-btn izin btn btn-outline-primary" data-status="Izin" data-id="<?php echo $item['id']; ?>">Izin</button>
                                <button class="status-btn tidak-hadir btn btn-outline-primary" data-status="Tidak Hadir" data-id="<?php echo $item['id']; ?>">Tidak Hadir</button>
                            </div>
                            <input type="text" class="form-control keterangan-input" placeholder="Keterangan (opsional)" 
                                   id="keterangan-<?php echo $item['id']; ?>" 
                                   name="keterangan-<?php echo $item['id']; ?>" 
                                   data-id="<?php echo $item['id']; ?>">
                            <button class="save-btn btn btn-primary mt-2" data-id="<?php echo $item['id']; ?>">Simpan Kehadiran</button>
                            <div class="alert alert-success mt-2 d-none" id="success-<?php echo $item['id']; ?>">Kehadiran berhasil disimpan!</div>
                            <div class="alert alert-danger mt-2 d-none" id="error-<?php echo $item['id']; ?>"></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">Tidak ada kegiatan yang tersedia untuk diisi kehadirannya.</div>
                <?php endif; ?>
            </div>
        </div>

         <!-- Riwayat Kehadiran -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Riwayat Kehadiran</h4>
                <?php if (isset($error_riwayat)): ?>
                    <div class="alert alert-danger"><?php echo $error_riwayat; ?></div>
                <?php elseif (!empty($riwayat) || true): // Selalu tampilkan tabel ?>
                    <table class="table table-striped" id="riwayat-table">
                        <thead>
                            <tr>
                                <th>Kegiatan</th>
                                <th>UKM</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayat as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['nama_kegiatan']); ?></td>
                                    <td><?php echo htmlspecialchars($item['nama_ukm']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($item['tanggal'])); ?></td>
                                    <td><?php echo htmlspecialchars($item['status']); ?></td>
                                    <td><?php echo htmlspecialchars($item['keterangan'] ?: '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info" id="riwayat-empty">Belum ada riwayat kehadiran.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
    $(document).ready(function() {
        console.log('jQuery loaded, ready to go');

        // Handler untuk tombol status
        $('.status-btn').click(function() {
            console.log('Status button clicked:', $(this).data('status'));
            var parent = $(this).closest('.kegiatan-item');
            parent.find('.status-btn').removeClass('selected');
            $(this).addClass('selected');
        });

        // Handler untuk tombol simpan
        $('.save-btn').click(function() {
            console.log('Save button clicked');
            var id_jadwal = $(this).data('id');
            var parent = $('#kegiatan-' + id_jadwal);
            var status = parent.find('.status-btn.selected').data('status');
            var keterangan = parent.find('#keterangan-' + id_jadwal).val();

            console.log('Data to send:', { id_jadwal, status, keterangan });

            if (!status) {
                console.log('No status selected');
                parent.find('#error-' + id_jadwal).text('Pilih status kehadiran terlebih dahulu!').removeClass('d-none');
                parent.find('#success-' + id_jadwal).addClass('d-none');
                return;
            }

            $.ajax({
                url: 'simpan_kehadiran.php',
                method: 'POST',
                data: {
                    id_jadwal: id_jadwal,
                    status: status,
                    ['keterangan-' + id_jadwal]: keterangan
                },
                dataType: 'json',
                success: function(response) {
                    console.log('AJAX success, response:', response);
                    if (response.success) {
                        parent.find('#success-' + id_jadwal).removeClass('d-none');
                        parent.find('#error-' + id_jadwal).addClass('d-none');

                        // Tambahkan ke riwayat
                        var riwayatTable = $('#riwayat-table tbody');
                        var emptyMessage = $('#riwayat-empty');
                        if (emptyMessage.length) {
                            emptyMessage.remove();
                            $('#riwayat-table').removeClass('d-none');
                        }

                        var newRow = $('<tr>');
                        newRow.append($('<td>').text(response.data.nama_kegiatan));
                        newRow.append($('<td>').text(response.data.nama_ukm));
                        newRow.append($('<td>').text(new Date(response.data.tanggal).toLocaleDateString('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric'
                        })));
                        newRow.append($('<td>').text(response.data.status));
                        newRow.append($('<td>').text(response.data.keterangan || '-'));

                        riwayatTable.prepend(newRow); // Tambahkan di atas

                        // Hapus kegiatan dari daftar
                        setTimeout(function() {
                            parent.fadeOut();
                        }, 2000);
                    } else {
                        parent.find('#error-' + id_jadwal).text(response.message || 'Gagal menyimpan kehadiran').removeClass('d-none');
                        parent.find('#success-' + id_jadwal).addClass('d-none');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error:', status, error, 'Response:', xhr.responseText);
                    parent.find('#error-' + id_jadwal).text('Gagal menyimpan kehadiran: ' + (xhr.responseText || error)).removeClass('d-none');
                    parent.find('#success-' + id_jadwal).addClass('d-none');
                }
            });
        });
    });
    </script>
</body>
</html>