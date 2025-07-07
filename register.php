<?php
session_start();
$successMsg = $_SESSION['register_success'] ?? '';
unset($_SESSION['register_success']);

$errorMsg = $_SESSION['register_error'] ?? '';
unset($_SESSION['register_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register to UKMate</title>
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
        <img src="bg-images/bg-1..jpg" alt="bg-1">
        <img src="bg-images/bg-2.jpg" alt="bg-1">
        <img src="bg-images/bg-3.jpg" alt="bg-1">
        <img src="bg-images/bg-4.jpg" alt="bg-1">
        <img src="bg-images/bg-5.jpg" alt="bg-1">
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
    
    <!-- Right card - Registration form -->
    <div class="bg-white p-12 w-1/2 relative flex flex-col form-card">
      <h2 class="text-3xl font-bold text-gray-900 mb-8">Create Account</h2>
      
      <div class="form-container">
        <form id="registerForm" method="POST" action="auth/register_proccess.php" onsubmit="return validatePasswords()">
          <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <div class="relative">
              <input type="text" id="username" name="username" required oninput="checkUsername()"
                class="w-full px-3 py-2 border border-gray-300 rounded text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
              <span id="checkmark" class="absolute right-3 top-2.5 text-green-500 hidden">✓</span>
            </div>
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

          <div class="mb-4">
            <label for="confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <div class="relative">
              <input type="password" id="confirm" name="confirm" required
                class="w-full px-3 py-2 border border-gray-300 rounded text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
              <i class="fa-regular fa-eye absolute right-3 top-2.5 text-gray-500 cursor-pointer"
                onclick="toggleVisibility('confirm', this)"></i>
            </div>
          </div>

          
          <div class="text-red-500 text-xs mb-4" id="errorMsg"><?php echo htmlspecialchars($errorMsg); ?></div>

          <button type="submit" class="w-full py-3 bg-cyan-400 hover:bg-cyan-500 rounded text-black font-bold transition">
            Create Account
          </button>
        </form>
      </div>

      <div class="mt-auto">
        <div class="flex items-center my-6">
          <div class="flex-grow border-t border-gray-300"></div>
          <span class="px-4 text-gray-500">or</span>
          <div class="flex-grow border-t border-gray-300"></div>
        </div>
        
        <div class="text-center">
          <span class="text-gray-700">Already have an account?</span>
          <a href="login.php" class="text-green-500 hover:underline font-bold ml-2">Sign in</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    function checkUsername() {
      const username = document.getElementById('username').value;
      const checkmark = document.getElementById('checkmark');
      if (username.length >= 3) {
        checkmark.style.display = 'inline';
      } else {
        checkmark.style.display = 'none';
      }
    }

    function validatePasswords() {
      const pass = document.getElementById('password').value;
      const confirm = document.getElementById('confirm').value;
      const error = document.getElementById('errorMsg');
      if (pass !== confirm) {
        error.textContent = "Passwords do not match.";
        return false;
      }
      error.textContent = "";
      return true;
    }
  </script>

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