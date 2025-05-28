<?php
session_start();
$successMsg = $_SESSION['login_success'] ?? '';
unset($_SESSION['login_success']);

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign in to UKMate</title>
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

    /* Shared styles for consistent sizing */
    .card-container {
      height: 600px;
    }
    .form-card {
      height: 100%;
    }
    .form-container {
      min-height: 320px;
    }
  </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="flex max-w-5xl w-full mx-auto shadow-lg rounded-lg overflow-hidden card-container">
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
          <strong>Daftar sekarang dan jadilah bagian dari UKMate!</strong>
        </p>
      </div>
    </div>

    
    <!-- Right card - Login form -->
    <div class="bg-white p-12 w-1/2 relative flex flex-col form-card">
      <h2 class="text-3xl font-bold text-gray-900 mb-8">Log In</h2>
      
      <?php if ($error): ?>
        <div class="bg-red-500 text-white text-sm rounded px-3 py-2 mb-4 text-center">
        <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>
      
      <div class="form-container">
        <form method="POST" action="auth/login_proccess.php">
          <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" id="username" name="username" required
            class="w-full px-3 py-2 rounded border border-gray-300 text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>

          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div class="relative">
              <input type="password" id="password" name="password" required
                class="w-full px-3 py-2 border border-gray-300 rounded text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
              <i class="fa-regular fa-eye absolute right-3 top-2.5 text-gray-500 cursor-pointer"
                onclick="toggleVisibility('password', this)"></i>
            </div>
          </div>

          <button type="submit"
          class="w-full py-3 bg-cyan-400 hover:bg-cyan-500 text-black font-bold rounded transition">Next</button>
        </form>
      </div>
      
      <div class="mt-auto">
        <div class="flex items-center my-6">
          <div class="flex-grow border-t border-gray-300"></div>
          <span class="px-4 text-gray-500">or</span>
          <div class="flex-grow border-t border-gray-300"></div>
        </div>
        
        <div class="text-center">
          <span class="text-gray-700">Become a member</span>
          <a href="register.php" class="text-green-500 hover:underline font-bold ml-2">Join UKMate</a>
        </div>
      </div>
    </div>
  </div>

  <script>
      function toggleVisibility(fieldId, icon) {
        const input = document.getElementById(fieldId);
        if (input.type === "password") {
          input.type = "text";
          icon.classList.remove("fa-eye");
          icon.classList.add("fa-eye-slash");
        } else {
          input.type = "password";
          icon.classList.remove("fa-eye-slash");
          icon.classList.add("fa-eye");
        }
      }
    </script>

    <?php if ($successMsg): ?>
    <div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full text-center">
        <h2 class="text-2xl font-bold mb-4 text-green-600">Sukses!</h2>
        <p class="text-gray-700 mb-6"><?php echo htmlspecialchars($successMsg); ?></p>
        <button onclick="redirectToDashboard()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded transition">
          OK
        </button>
      </div>
    </div>
    <script>
      function redirectToDashboard() {
        window.location.href = 'dashboard.php';
      }

      // Auto-redirect in 5 seconds if user doesn't click
      setTimeout(() => {
        redirectToDashboard();
      }, 5000);
    </script>
    <?php endif; ?>

</body>
</html>