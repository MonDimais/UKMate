<?php
session_start();
include 'db_connect.php';
$conn = db_connect();

function createUser($username, $password, $conn) {
  $username = $conn->real_escape_string($username);
  $check = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
  $check->bind_param("s", $username);
  $check->execute();
  $check->store_result();
  if ($check->num_rows > 0) {
    return false;
  }

  $hash = password_hash($password, PASSWORD_DEFAULT);
  $insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
  $insert->bind_param("ss", $username, $hash);
  return $insert->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $confirm  = $_POST['confirm'] ?? '';

  if ($password !== $confirm) {
    $_SESSION['register_error'] = "Passwords do not match.";
    header("Location: register.php");
    exit;
  }

  if (createUser($username, $password, $conn)) {
    header("Location: login.php");
    exit;
  } else {
    $_SESSION['register_error'] = "Username already exists or error occurred.";
    header("Location: register.php");
    exit;
  }
}
?>
