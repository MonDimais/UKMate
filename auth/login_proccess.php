<?php
session_start();
include "db_connect.php";
$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  // Ambil id_user, password dan role dalam satu query
  $stmt = $conn->prepare("SELECT id_user, password, role FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($id_user, $hashed_password, $role);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
      // Simpan info login di session
      $_SESSION['id_user'] = $id_user;
      $_SESSION['username'] = $username;
      $_SESSION['role'] = $role;
      $_SESSION['login'] = true;

      // Jika Admin, langsung ke halaman admin
      if ($role === 'Admin') {
        header("Location: ../admin_choice.php");
        exit;
      }

      // Untuk user biasa → cek pendaftaran
      $stmt2 = $conn->prepare("SELECT status FROM pendaftaran WHERE id_user = ?");
      $stmt2->bind_param("i", $id_user);
      $stmt2->execute();
      $result2 = $stmt2->get_result();

      if ($result2->num_rows === 0) {
        // Belum mendaftar → arahkan ke halaman pendaftaran
        header("Location: ../pendaftaran.php");
        exit;
      } else {
        $row = $result2->fetch_assoc();
        $status = strtolower($row['status']);

        if ($status === 'pending') {
          header("Location: ../pending.php");
        } elseif ($status === 'ditolak') {
          header("Location: ../rejected.php");
        } elseif ($status === 'diterima') {
          $_SESSION['login_success'] = "Berhasil login! Selamat datang kembali.";
          header("Location: ../login.php"); // Ganti ke halaman dashboard user
        } else {
          $_SESSION['login_error'] = "Status tidak dikenali.";
          header("Location: ../login.php");
        }
      }

      $stmt2->close();
      exit;
    } else {
      $_SESSION['login_error'] = "Username atau password salah.";
    }
  } else {
    $_SESSION['login_error'] = "Username tidak ditemukan.";
  }

  $stmt->close();
  header("Location: ../login.php");
  exit;
}
?>
