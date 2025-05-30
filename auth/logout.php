<?php
// Start session untuk bisa menghapusnya
session_start();

// Simpan pesan logout (opsional)
$logout_message = "Anda telah berhasil logout.";

// Hapus semua variabel session
$_SESSION = array();

// Jika menggunakan session cookies, hapus juga cookie-nya
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Mulai session baru untuk pesan logout (opsional)
session_start();
$_SESSION['logout_message'] = $logout_message;

// Redirect ke halaman login
header("Location: ../login.php");
exit();
?>