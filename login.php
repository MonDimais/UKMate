<?php
// Mulai session
session_start();
include "db_connect.php";
$conn = db_connect();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  // Query user
  $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows === 1) {
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    if (password_verify($password, $hashed_password)) {
      $_SESSION['username'] = $username;
      $_SESSION['login'] = true;
      header("Location: dashboard.php");
      exit;
    } else {
      $error = "Username atau password salah.";
    }
  }
  
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign in to UKMate</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">
  <div class="bg-gray-800 border border-gray-700 p-8 rounded-lg w-80 shadow-lg">
  <h2 class="text-center text-2xl font-normal text-white mb-6">Sign in to UKMate</h2>
  <?php if ($error): ?>
    <div class="bg-red-500 text-white text-sm rounded px-3 py-2 mb-4 text-center">
    <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>
  <form method="POST" action="login.php">
    <label for="username" class="block text-sm mb-1 mt-3 text-gray-200">Username</label>
    <input type="text" id="username" name="username" required
    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

    <label for="password" class="block text-sm mb-1 mt-4 text-gray-200">Password</label>
    <input type="password" id="password" name="password" required
    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

    <button type="submit"
    class="w-full py-2 mt-6 bg-green-600 hover:bg-green-700 text-white font-bold rounded transition">Sign in</button>
  </form>
  <div class="text-center mt-6 text-sm text-gray-300">
    New to UKMate?
    <a href="register.php" class="text-blue-400 hover:underline">Create an account</a>
  </div>
  </div>
</body>
</html>
