<?php
session_start();
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
  <style>
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
      <div class="relative z-10">
        <h1 class="text-5xl font-bold mb-6">JOIN UKMATE</h1>
        
        <p class="text-xl mb-8">
          Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
          Voluptatum dolor accusamus cumque, quo quaerat dolorem placeat pariatur, labore maxime natus quis vitae architecto praesentium quam. 
          Hic, blanditiis aspernatur? Natus, ipsam.
        </p>
      </div>
    </div>
    
    <!-- Right card - Registration form -->
    <div class="bg-white p-12 w-1/2 relative flex flex-col form-card">
      <h2 class="text-3xl font-bold text-gray-900 mb-8">Create Account</h2>
      
      <div class="form-container">
        <form id="registerForm" method="POST" action="register_proccess.php" onsubmit="return validatePasswords()">
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
            <input type="password" id="password" name="password" required
              class="w-full px-3 py-2 border border-gray-300 rounded text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>

          <div class="mb-4">
            <label for="confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input type="password" id="confirm" name="confirm" required
              class="w-full px-3 py-2 border border-gray-300 rounded text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
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
</body>
</html>