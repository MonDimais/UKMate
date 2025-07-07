<?php
ob_start();
header('Content-Type: application/json; charset=utf-8');

// Logging untuk debugging
$log_file = 'C:/xampp/htdocs/website_ukm/debug.log';
file_put_contents($log_file, 
    date('Y-m-d H:i:s') . ' - simpan_kehadiran.php diakses: ' . print_r($_POST, true) . "\n", 
    FILE_APPEND
);

session_start();

// Periksa sesi
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id']) || $_SESSION['user']['role'] !== 'anggota') {
    file_put_contents($log_file, 
        date('Y-m-d H:i:s') . ' - Akses ditolak: Sesi tidak valid' . "\n", 
        FILE_APPEND
    );
    echo json_encode(['success' => false, 'message' => 'Akses ditolak: silakan login sebagai anggota']);
    ob_end_flush();
    exit;
}

// Koneksi database
try {
    $database_path = 'C:/xampp/htdocs/website_ukm/config/database.php';
    if (!file_exists($database_path)) {
        throw new Exception('File database.php tidak ditemukan di ' . $database_path);
    }
    require_once $database_path;
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception('Koneksi database gagal: ' . ($conn->connect_error ?? 'Koneksi tidak didefinisikan'));
    }
} catch (Exception $e) {
    file_put_contents($log_file, 
        date('Y-m-d H:i:s') . ' - Database error: ' . $e->getMessage() . "\n", 
        FILE_APPEND
    );
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    ob_end_flush();
    exit;
}

// Proses data POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_jadwal = isset($_POST['id_jadwal']) && is_numeric($_POST['id_jadwal']) ? (int)$_POST['id_jadwal'] : 0;
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $keterangan_key = 'keterangan-' . $id_jadwal;
    $keterangan = isset($_POST[$keterangan_key]) ? trim($_POST[$keterangan_key]) : '';
    $id_user = (int)$_SESSION['user']['id'];

    // Log data
    file_put_contents($log_file, 
        date('Y-m-d H:i:s') . " - Data diterima: id_jadwal=$id_jadwal, status=$status, keterangan=$keterangan, id_user=$id_user\n", 
        FILE_APPEND
    );

    // Validasi
    if ($id_jadwal === 0 || !in_array($status, ['Hadir', 'Izin', 'Tidak Hadir']) || $id_user === 0) {
        file_put_contents($log_file, 
            date('Y-m-d H:i:s') . " - Validasi gagal: id_jadwal=$id_jadwal, status=$status, id_user=$id_user\n", 
            FILE_APPEND
        );
        echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
        ob_end_flush();
        exit;
    }

    // Simpan ke database
    try {
        $query = "INSERT INTO kehadiran (id_user, id_jadwal, status, keterangan) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Prepare statement gagal: ' . $conn->error);
        }
        $stmt->bind_param("iiss", $id_user, $id_jadwal, $status, $keterangan);
        if (!$stmt->execute()) {
            throw new Exception('Eksekusi query gagal: ' . $stmt->error);
        }

        // Ambil data kegiatan untuk riwayat
        $query = "SELECT j.nama_kegiatan, j.tanggal, u.nama as nama_ukm 
                  FROM jadwal_kegiatan j 
                  JOIN ukm u ON j.id_ukm = u.id 
                  WHERE j.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_jadwal);
        $stmt->execute();
        $result = $stmt->get_result();
        $kegiatan = $result->fetch_assoc();

        file_put_contents($log_file, 
            date('Y-m-d H:i:s') . " - Data tersimpan: id_jadwal=$id_jadwal, id_user=$id_user\n", 
            FILE_APPEND
        );

        // Kembalikan respons dengan data kegiatan
        echo json_encode([
            'success' => true,
            'data' => [
                'nama_kegiatan' => $kegiatan['nama_kegiatan'] ?? '',
                'nama_ukm' => $kegiatan['nama_ukm'] ?? '',
                'tanggal' => $kegiatan['tanggal'] ?? '',
                'status' => $status,
                'keterangan' => $keterangan
            ]
        ]);
    } catch (Exception $e) {
        file_put_contents($log_file, 
            date('Y-m-d H:i:s') . ' - Query error: ' . $e->getMessage() . "\n", 
            FILE_APPEND
        );
        echo json_encode(['success' => false, 'message' => 'Gagal mencatat kehadiran: ' . $e->getMessage()]);
    }
} else {
    file_put_contents($log_file, 
        date('Y-m-d H:i:s') . ' - Metode tidak diizinkan: ' . $_SERVER['REQUEST_METHOD'] . "\n", 
        FILE_APPEND
    );
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
}

ob_end_flush();
?>