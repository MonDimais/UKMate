<?php
session_start();

// Cek apakah user sudah login dan rolenya admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Dashboard - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 90%;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .welcome-text {
            color: #666;
            margin-bottom: 30px;
            font-size: 18px;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 30px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            background-color: #e9ecef;
        }

        .card-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .card-description {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }

        .user-card {
            border-color: #4CAF50;
        }

        .user-card:hover {
            background-color: #e8f5e9;
            border-color: #2e7d32;
        }

        .user-card .card-icon {
            color: #4CAF50;
        }

        .admin-card {
            border-color: #2196F3;
        }

        .admin-card:hover {
            background-color: #e3f2fd;
            border-color: #1565c0;
        }

        .admin-card .card-icon {
            color: #2196F3;
        }

        .logout-link {
            margin-top: 30px;
            display: inline-block;
            color: #dc3545;
            text-decoration: none;
            font-size: 14px;
        }

        .logout-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p class="welcome-text">Pilih dashboard yang ingin Anda akses:</p>
        
        <div class="cards-container">
            <a href="Anggota/dashboard.php" class="card user-card">
                <div class="card-icon">👤</div>
                <h2 class="card-title">Dashboard Anggota</h2>
                <p class="card-description">
                    Akses sebagai anggota biasa untuk melihat informasi keanggotaan, 
                    kegiatan, dan fitur-fitur untuk anggota.
                </p>
            </a>
            
            <a href="Admin/dashboard.php" class="card admin-card">
                <div class="card-icon">👨‍💼</div>
                <h2 class="card-title">Dashboard Admin</h2>
                <p class="card-description">
                    Akses sebagai administrator untuk mengelola anggota, 
                    menyetujui pendaftaran, dan mengatur sistem.
                </p>
            </a>
        </div>
        
        <a href="auth/logout.php" class="logout-link">Logout</a>
    </div>
</body>
</html>