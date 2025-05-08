<?php
session_start();
include "db_connect.php";
$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  // Ambil password dan role dalam satu query
  $stmt = $conn->prepare("SELECT password, role FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($hashed_password, $role);
    $stmt->fetch();
    if (password_verify($password, $hashed_password)) {
      $_SESSION['username'] = $username;
      $_SESSION['role'] = $role;
      $_SESSION['login'] = true;

      // Arahkan sesuai role
      if ($role === 'Admin') {
        header("Location: dashboard_admin.php");
      } else {
        header("Location: dashboard.php");
      }
      exit;
    } else {
      $_SESSION['login_error'] = "Username atau password salah.";
    }
  } else {
    $_SESSION['login_error'] = "Username tidak ditemukan.";
  }

  $stmt->close();
  header("Location: login.php");
  exit;
}
?>
