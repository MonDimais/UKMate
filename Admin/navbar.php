<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fungsi untuk mendapatkan path yang benar
if (!function_exists('getCorrectPath')) {
    function getCorrectPath($targetPath) {
        $currentFile = $_SERVER['PHP_SELF'];
        $currentDir = dirname($currentFile);
        
        // Normalize the current directory
        $currentDir = str_replace('\\', '/', $currentDir);
        
        // Check if we're in a subdirectory
        if (strpos($currentDir, '/Admin/kegiatan') !== false || 
            strpos($currentDir, '/Admin/anggota') !== false || 
            strpos($currentDir, '/Admin/presensi') !== false) {
            // We're in a subfolder, need to go up one level
            return '../../' . $targetPath;
        } elseif (strpos($currentDir, '/Admin') !== false) {
            // We're in Admin folder
            return '../' . $targetPath;
        } else {
            // We're in root
            return $targetPath;
        }
    }
}

// Include koneksi database - gunakan path yang benar
$koneksiPath = getCorrectPath('koneksi.php');
if (file_exists($koneksiPath)) {
    require_once $koneksiPath;
} else {
    // Coba path alternatif
    $altPaths = ['../koneksi.php', '../../koneksi.php', 'koneksi.php'];
    foreach ($altPaths as $path) {
        if (file_exists($path)) {
            require_once $path;
            break;
        }
    }
}

// Get current page name
$current_page = basename($_SERVER['PHP_SELF']);

// Inisialisasi variabel default
$user_name = 'Admin';
$user_nim = '-';
$user_prodi = '-';
$user_email = '-';

// Cek apakah user sudah login dan ambil data dari database
if (isset($_SESSION['id_user'])) {
    $id_user = $_SESSION['id_user'];
    
    // Query untuk mengambil data user dari tabel pendaftaran
    $query = "SELECT nama, npm, prodi, email FROM pendaftaran WHERE id_user = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $userData = mysqli_fetch_assoc($result);
            $user_name = $userData['nama'] ?? 'Admin';
            $user_nim = $userData['npm'] ?? '-';
            $user_prodi = $userData['prodi'] ?? '-';
            $user_email = $userData['email'] ?? '-';
        } else {
            // Jika tidak ada di tabel pendaftaran, coba ambil username dari session
            $user_name = $_SESSION['username'] ?? 'Admin';
        }
        
        mysqli_stmt_close($stmt);
    }
} else {
    // Redirect ke login jika tidak ada session - gunakan path yang benar
    $loginPath = getCorrectPath('login.php');
    header("Location: " . $loginPath);
    exit();
}

// Dapatkan path logout yang benar
$logoutPath = getCorrectPath('auth/logout.php');

// Optional: Dapatkan path untuk link lain yang mungkin dibutuhkan
$profilePath = getCorrectPath('profile.php');
$settingsPath = getCorrectPath('settings.php');
?>

<nav class="bg-white shadow-md px-6 py-4 flex items-center justify-between sticky top-0 z-40 mt-16 sm:mt-16 md:mt-0 lg:mt-0">
    <h2 class="text-xl font-semibold text-gray-800">Dashboard Admin</h2>
    
    <div class="relative">
        <button onclick="toggleDropdown()" id="dropdownButton" class="flex items-center space-x-3 focus:outline-none hover:bg-gray-100 px-3 py-2 rounded transition">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($user_name) ?>&background=27548A&color=fff" class="w-9 h-9 rounded-full" alt="User Photo">
            <span class="text-gray-700 font-medium hidden sm:inline"><?= htmlspecialchars($user_name) ?></span>
            <svg id="dropdownIcon" class="w-4 h-4 text-gray-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown -->
        <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-md shadow-xl z-50 overflow-hidden animate-fade">
            <!-- User Info Section -->
            <div class="px-4 py-3 bg-gray-50">
                <div class="flex items-center space-x-3">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user_name) ?>&background=27548A&color=fff" class="w-12 h-12 rounded-full" alt="User Photo">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($user_name) ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($_SESSION['role'] ?? 'Admin') ?></p>
                    </div>
                </div>
            </div>
            
            <!-- User Details -->
            <div class="px-4 py-3 text-sm text-gray-700 space-y-2 border-t border-gray-200">
                <div class="flex items-center">
                    <i class="fas fa-id-card mr-2 w-4 text-gray-400"></i>
                    <span class="text-gray-600">NPM:</span>
                    <span class="ml-auto font-medium"><?= htmlspecialchars($user_nim) ?></span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-graduation-cap mr-2 w-4 text-gray-400"></i>
                    <span class="text-gray-600">Prodi:</span>
                    <span class="ml-auto font-medium"><?= htmlspecialchars($user_prodi) ?></span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-envelope mr-2 w-4 text-gray-400"></i>
                    <span class="text-gray-600">Email:</span>
                    <span class="ml-auto font-medium text-xs"><?= htmlspecialchars($user_email) ?></span>
                </div>
            </div>
            
            <!-- Menu Actions -->
            <div class="border-t border-gray-200"></div>
            <ul class="py-1 text-sm">
                <!-- Tambahkan menu lain jika diperlukan -->
                <!-- <li>
                    <a href="<?= $profilePath ?>" class="flex items-center px-4 py-2 hover:bg-gray-100 text-gray-700">
                        <i class="fas fa-user mr-2 w-4"></i> Profile
                    </a>
                </li>
                <li>
                    <a href="<?= $settingsPath ?>" class="flex items-center px-4 py-2 hover:bg-gray-100 text-gray-700">
                        <i class="fas fa-cog mr-2 w-4"></i> Settings
                    </a>
                </li> -->
                <li>
                    <a href="<?= $logoutPath ?>" class="flex items-center px-4 py-2 hover:bg-gray-100 text-red-600">
                        <i class="fas fa-sign-out-alt mr-2 w-4"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Styles dan Scripts tetap sama -->
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade {
    animation: fadeIn 0.2s ease-out;
}

#dropdownMenu {
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

#dropdownButton:hover {
    background-color: #f3f4f6;
}

#dropdownIcon {
    transition: transform 0.2s ease;
}

#dropdownIcon.rotate-180 {
    transform: rotate(180deg);
}
</style>

<script>
function toggleDropdown() {
    const menu = document.getElementById('dropdownMenu');
    const icon = document.getElementById('dropdownIcon');
    menu.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
    const button = document.getElementById('dropdownButton');
    const dropdown = document.getElementById('dropdownMenu');
    const icon = document.getElementById('dropdownIcon');
    
    if (!button.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
});

// Optional: Close dropdown with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const dropdown = document.getElementById('dropdownMenu');
        const icon = document.getElementById('dropdownIcon');
        dropdown.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
});
</script>