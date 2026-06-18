<?php
/**
 * Layout Template — Shared sidebar + navbar for all authenticated pages.
 *
 * Usage in pages:
 *   $page_title   = "Dashboard";
 *   $current_page  = "dashboard";
 *   ob_start();
 *   // ... your page content HTML ...
 *   $page_content = ob_get_clean();
 *   include 'layout.php';
 *
 * Required variables:
 *   $page_title   (string) — Page <title> text
 *   $current_page (string) — Active nav item key
 *   $page_content (string) — HTML content for the main area
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auth guard — redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

require_once 'db.php';

// Flash message
$flash = getFlashMessage();

// Nav items
$navItems = [
    ['key' => 'dashboard',   'label' => 'Dashboard',           'icon' => 'bi-grid-1x2-fill',    'href' => 'dashboard.php'],
    ['key' => 'members',     'label' => 'Members',             'icon' => 'bi-people-fill',       'href' => 'trainer_details.php'],
    ['key' => 'memberships', 'label' => 'Membership Programs', 'icon' => 'bi-card-checklist',    'href' => 'membership.php'],
    ['key' => 'trainers',    'label' => 'Trainers',            'icon' => 'bi-person-badge-fill', 'href' => 'trainer.php'],
    ['key' => 'packages',    'label' => 'Packages',            'icon' => 'bi-box-seam-fill',     'href' => 'package.php'],
    ['key' => 'payments',    'label' => 'Payments',            'icon' => 'bi-credit-card-fill',  'href' => 'payment.php'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FitManager — Gym Management System">
    <title><?php echo h($page_title ?? 'FitManager'); ?> — FitManager</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="app-wrapper">

    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <a href="dashboard.php" class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="bi bi-lightning-charge-fill"></i>
            </div>
            <div class="sidebar-brand-text">Fit<span>Manager</span></div>
        </a>

        <nav class="sidebar-nav">
            <div class="sidebar-label">Main Menu</div>
            <?php foreach ($navItems as $item): ?>
                <a href="<?php echo $item['href']; ?>"
                   class="sidebar-link <?php echo ($current_page === $item['key']) ? 'active' : ''; ?>"
                   id="nav-<?php echo $item['key']; ?>">
                    <i class="bi <?php echo $item['icon']; ?>"></i>
                    <?php echo $item['label']; ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="sidebar-footer">
            <a href="index.php?logout=1" class="sidebar-link" id="nav-logout">
                <i class="bi bi-box-arrow-left"></i>
                Sign Out
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <span class="navbar-title"><?php echo h($page_title ?? 'FitManager'); ?></span>
            </div>
            <div class="navbar-actions">
                <a href="index.php?logout=1" class="navbar-user" id="btn-logout-top">
                    <i class="bi bi-person-circle"></i>
                    Admin
                    <i class="bi bi-chevron-right" style="font-size:0.7rem; opacity:0.5;"></i>
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <main class="page-content">
            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : h($flash['type']); ?> fade-in" role="alert">
                    <i class="bi <?php
                        echo match ($flash['type']) {
                            'success' => 'bi-check-circle-fill',
                            'error'   => 'bi-exclamation-circle-fill',
                            'warning' => 'bi-exclamation-triangle-fill',
                            default   => 'bi-info-circle-fill',
                        };
                    ?>"></i>
                    <?php echo h($flash['message']); ?>
                </div>
            <?php endif; ?>

            <?php echo $page_content; ?>
        </main>
    </div>
</div>

<!-- Bootstrap 5.3 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Sidebar toggle for mobile
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    }

    // Close sidebar when clicking a link on mobile
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                toggleSidebar();
            }
        });
    });

    // Auto-dismiss flash messages
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-8px)';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
</script>
</body>
</html>
