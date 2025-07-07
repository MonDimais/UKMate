<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<?php
// Pastikan session dimulai jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Jakarta');

// Fungsi untuk mendapatkan path yang benar
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

// Get current page name
$current_page = basename($_SERVER['PHP_SELF']);

// Menu items array
$menuItems = [
    [
        'title' => 'Dashboard',
        'icon' => 'fas fa-chart-line',
        'path' => 'Admin/dashboard.php',
        'file' => 'dashboard.php'
    ],
    [
        'title' => 'Data Kegiatan',
        'icon' => 'fas fa-calendar-alt',
        'path' => 'Admin/kegiatan/data-kegiatan.php',
        'file' => 'data-kegiatan.php'
    ],
    [
        'title' => 'Data Anggota',
        'icon' => 'fas fa-users',
        'path' => 'Admin/anggota/data-anggota.php',
        'file' => 'data-anggota.php'
    ],
    [
        'title' => 'Presensi',
        'icon' => 'fas fa-check-circle',
        'path' => 'Admin/presensi/data-presensi.php',
        'file' => 'data-presensi.php'
    ]
];
?>

<!-- Navbar (Mobile Only) -->
<div class="md:hidden flex justify-between items-center bg-[#27548A] text-white px-4 py-3 shadow-md fixed top-0 left-0 w-full z-50">
    <h1 class="text-lg font-bold">UKM Fasilkom</h1>
    <button id="menu-toggle" class="text-2xl focus:outline-none">☰</button>
</div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed inset-y-0 left-0 w-80 bg-[#27548A] text-white transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-50 shadow-lg md:shadow-none overflow-y-auto">

    <div class="p-4 space-y-4 flex flex-col justify-between h-full">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold mb-6 hidden md:block">UKM Fasilkom</h1>
            <nav class="space-y-2">
                <?php foreach ($menuItems as $item): ?>
                    <?php 
                    $isActive = ($current_page === $item['file']);
                    $linkPath = getCorrectPath($item['path']);
                    ?>
                    <a href="<?= htmlspecialchars($linkPath) ?>"
                       class="block p-3 rounded-lg transition-all duration-200 <?= $isActive ? 'text-[#DDA853] font-semibold bg-[#183B4E] shadow-md' : 'hover:bg-[#183B4E] hover:text-[#DDA853]' ?>">
                        <i class="<?= $item['icon'] ?> mr-3 w-5 text-center"></i>
                        <span><?= $item['title'] ?></span>
                        <?php if ($isActive): ?>
                            <span class="float-right">
                                <i class="fas fa-chevron-right text-sm"></i>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- Footer -->
        <div>
            
            <!-- Debug info (hapus ini setelah masalah teratasi) -->
            <?php if (!isset($_SESSION['username'])): ?>
                <div class="mb-2 p-2 bg-yellow-600 rounded text-xs">
                    <p>Debug: Session tidak tersedia</p>
                    <p>Session ID: <?= session_id() ?></p>
                </div>
            <?php endif; ?>
            
            <!-- Clock -->
            <div class="text-sm text-gray-300 mb-4">
                <div class="flex items-center justify-center p-2 bg-[#183B4E] rounded-lg">
                    <i class="far fa-clock mr-2"></i>
                    <span id="live-clock"></span>
                </div>
            </div>
            
            <!-- Logout Button -->
            <a href="<?= htmlspecialchars(getCorrectPath('auth/logout.php')) ?>" 
               class="block w-full text-center bg-red-600 hover:bg-red-700 py-3 rounded-lg transition-colors duration-200 font-semibold">
                <i class="fas fa-sign-out-alt mr-2"></i>
                Logout
            </a>
        </div>
    </div>
</aside>

<!-- Overlay for Mobile -->
<div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

<!-- Style untuk efek tambahan -->
<style>
    /* Active indicator animation */
    nav a {
        position: relative;
        overflow: hidden;
    }
    
    nav a::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background-color: #DDA853;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    nav a:hover::before {
        transform: translateX(0);
    }
    
    nav a.active::before {
        transform: translateX(0);
    }
    
    /* Smooth scrollbar */
    #sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    #sidebar::-webkit-scrollbar-track {
        background: #183B4E;
    }
    
    #sidebar::-webkit-scrollbar-thumb {
        background: #DDA853;
        border-radius: 3px;
    }
    
    #sidebar::-webkit-scrollbar-thumb:hover {
        background: #c4964a;
    }
</style>

<!-- Script -->
<script>
    const toggleBtn = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    }

    // Live Clock with better formatting
    function updateClock() {
        const now = new Date();
        const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][now.getDay()];
        const tanggal = now.getDate();
        const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][now.getMonth()];
        const tahun = now.getFullYear();
        const jam = String(now.getHours()).padStart(2, '0');
        const menit = String(now.getMinutes()).padStart(2, '0');
        const detik = String(now.getSeconds()).padStart(2, '0');
        
        const formattedDate = `${hari}, ${tanggal} ${bulan} ${tahun} pukul ${jam}.${menit}.${detik}`;
        
        const clockElement = document.getElementById('live-clock');
        if (clockElement) {
            clockElement.textContent = formattedDate;
        }
    }

    setInterval(updateClock, 1000);
    updateClock();
    
    // Add active class for current page (backup for PHP detection)
    document.addEventListener('DOMContentLoaded', function() {
        const currentPage = window.location.pathname.split('/').pop();
        const navLinks = document.querySelectorAll('nav a');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href').includes(currentPage)) {
                link.classList.add('active');
            }
        });
    });
</script>