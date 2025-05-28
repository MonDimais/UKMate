<?php
session_start();
$successMsg = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pendaftaran UKMate</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />

  <style>
    .background-slideshow {
      position: absolute;
      inset: 0;
      z-index: 0;
      overflow: hidden;
      opacity: 0.10; /* transparan untuk efek samar */
    }

    .background-slideshow img {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: 0;
      animation: fadeImages 16s infinite;
    }

    .background-slideshow img:nth-child(1) {
      animation-delay: 0s;
    }
    .background-slideshow img:nth-child(2) {
      animation-delay: 4s;
    }
    .background-slideshow img:nth-child(3) {
      animation-delay: 8s;
    }
    .background-slideshow img:nth-child(4) {
      animation-delay: 12s;
    }

    @keyframes fadeImages {
      0% { opacity: 0; }
      8% { opacity: 1; }
      25% { opacity: 1; }
      33% { opacity: 0; }
      100% { opacity: 0; }
    }
  </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="flex max-w-5xl w-full mx-auto shadow-lg rounded-lg overflow-hidden">
    <!-- Left card - Greeting/Introduction -->
    <div class="bg-gradient-to-b from-gray-800 to-gray-900 text-white p-12 w-1/2 relative overflow-hidden">
      
      <!-- Background image slideshow -->
      <div class="background-slideshow">
        <img src="bg-images/bg-1.jpg" alt="bg1">
        <img src="bg-images/bg-2.jpg" alt="bg2">
        <img src="bg-images/bg-3.jpg" alt="bg3">
        <img src="bg-images/bg-4.jpg" alt="bg4">
        <img src="bg-images/bg-5.jpg" alt="bg5">
      </div>

      <!-- Foreground content -->
      <div class="relative z-10">
        <h1 class="text-5xl font-bold mb-6">JOIN UKMATE</h1>
        <p class="text-xl mb-8">
          Selamat datang di UKMate! Bergabunglah dengan komunitas mahasiswa yang aktif dan bersemangat. 
          Daftarkan dirimu sekarang untuk mendapatkan akses ke berbagai kegiatan, informasi, dan peluang menarik.
          <br><br>
          Dengan UKMate, kamu akan terhubung dengan teman-teman seangkatan, mendapatkan informasi terkini tentang kegiatan kampus,
          dan memiliki kesempatan untuk berpartisipasi dalam berbagai acara seru. Jangan lewatkan kesempatan ini untuk menjadi bagian dari komunitas yang mendukung dan menginspirasi!
          <br><br>
          <strong>Daftar sekarang dan jadilah bagian dari UKMate!</strong>
        </p>
      </div>
    </div>

    <!-- Right Card -->
    <div class="bg-white p-12 w-1/2">
      <h2 class="text-3xl font-bold text-gray-900 mb-6">Formulir Pendaftaran</h2>

      <?php if ($successMsg): ?>
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4"><?= htmlspecialchars($success) ?></div>
      <?php elseif ($error): ?>
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="auth/pendaftaran_proccess.php">
        <div class="mb-4">
          <label for="nama" class="block text-sm font-medium">Nama</label>
          <input type="text" id="nama" name="nama" required class="w-full px-3 py-2 border rounded" />
        </div>

        <div class="mb-4">
          <label for="npm" class="block text-sm font-medium">NPM</label>
          <input type="text" id="npm" name="npm" required class="w-full px-3 py-2 border rounded" />
        </div>

        <div class="mb-4">
          <label for="email" class="block text-sm font-medium">Email</label>
          <input type="email" id="email" name="email" required class="w-full px-3 py-2 border rounded" />
        </div>

        <div class="mb-4">
          <label for="prodi" class="block text-sm font-medium">Program Studi</label>
          <input type="text" id="prodi" name="prodi" required class="w-full px-3 py-2 border rounded" />
        </div>

        <div class="mb-4">
          <label for="angkatan" class="block text-sm font-medium">Angkatan</label>
          <input type="number" id="angkatan" name="angkatan" required class="w-full px-3 py-2 border rounded" min="2000" max="2099" />
        </div>

        <div class="mb-4">
          <label for="bio" class="block text-sm font-medium">Bio Singkat</label>
          <textarea id="bio" name="bio" class="w-full px-3 py-2 border rounded" rows="3"></textarea>
        </div>

        <div class="mb-4">
            <label for="foto" class="block text-sm font-medium">Foto Bukti NPM</label>
            <input type="file" id="foto" name="foto"
            class="w-full px-3 py-2 border rounded bg-white text-gray-700" accept="image/*" />
        </div>

        <button type="submit" class="w-full py-3 bg-blue-500 text-white font-bold rounded hover:bg-blue-600">Daftar</button>
      </form>
    </div>
  </div>

  <?php if ($successMsg): ?>
    <div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full text-center">
        <h2 class="text-2xl font-bold mb-4 text-green-600">Sukses!</h2>
        <p class="text-gray-700 mb-6"><?php echo htmlspecialchars($successMsg); ?></p>
        <button onclick="redirectToLogin()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded transition">
          OK
        </button>
      </div>
    </div>
    <script>
      function redirectToLogin() {
        window.location.href = 'login.php';
      }

      // Auto-redirect in 5 seconds if user doesn't click
      setTimeout(() => {
        redirectToLogin();
      }, 5000);
    </script>
  <?php endif; ?>

</body>
</html>
