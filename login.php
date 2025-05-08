<?php
session_start();
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
    
    <!-- Right card - Login form -->
    <div class="bg-white p-12 w-1/2 relative flex flex-col form-card">
      <h2 class="text-3xl font-bold text-gray-900 mb-8">Log In</h2>
      
      <?php if ($error): ?>
        <div class="bg-red-500 text-white text-sm rounded px-3 py-2 mb-4 text-center">
        <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>
      
      <div class="form-container">
        <form method="POST" action="login_proccess.php">
          <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" id="username" name="username" required
            class="w-full px-3 py-2 rounded border border-gray-300 text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>

          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" id="password" name="password" required
            class="w-full px-3 py-2 rounded border border-gray-300 text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
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
</body>
</html>