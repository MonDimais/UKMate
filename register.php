<?php
include 'db_connect.php';
$conn = db_connect();

function createUser($username, $password, $conn) {
  $username = $conn->real_escape_string($username);

  // Cek apakah username sudah ada
  $check = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
  $check->bind_param("s", $username);
  $check->execute();
  $check->store_result();
  if ($check->num_rows > 0) {
      return false; // Username sudah ada
  }

  // Insert user baru jika belum ada
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
  $insert->bind_param("ss", $username, $hash);
  return $insert->execute();
}


$errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    if ($password !== $confirm) {
        $errorMsg = "Passwords do not match.";
    } else {
        if (createUser($username, $password, $conn)) {
            header("Location: login.php");
            exit;
        } else {
            $errorMsg = "Username already exists or error occurred.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register to UKMate</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
  <div class="bg-gray-800 border border-gray-700 p-8 rounded-lg w-full max-w-sm">
    <h2 class="mb-6 text-center text-2xl font-normal">Create your UKMate account</h2>
    <form id="registerForm" method="POST" action="" onsubmit="return validatePasswords()">
      <label for="username" class="block text-sm mb-1 mt-3">Username</label>
      <div class="relative">
        <input type="text" id="username" name="username" required oninput="checkUsername()"
          class="w-full px-3 py-2 border border-gray-700 rounded bg-gray-900 text-white text-sm focus:outline-none focus:border-blue-500" />
      </div>

      <label for="password" class="block text-sm mb-1 mt-3">Password</label>
      <input type="password" id="password" name="password" required
        class="w-full px-3 py-2 border border-gray-700 rounded bg-gray-900 text-white text-sm focus:outline-none focus:border-blue-500" />

      <label for="confirm" class="block text-sm mb-1 mt-3">Confirm Password</label>
      <input type="password" id="confirm" name="confirm" required
        class="w-full px-3 py-2 border border-gray-700 rounded bg-gray-900 text-white text-sm focus:outline-none focus:border-blue-500" />
      <div class="text-red-500 text-xs mt-1" id="errorMsg"><?php echo htmlspecialchars($errorMsg); ?></div>

      <button type="submit" class="w-full py-2 mt-6 bg-green-600 hover:bg-green-700 rounded text-white font-bold text-sm transition">Create account</button>
    </form>

    <div class="text-center mt-6 text-sm">
      Already have an account? <a href="login.php" class="text-blue-400 hover:underline">Sign in</a>
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
