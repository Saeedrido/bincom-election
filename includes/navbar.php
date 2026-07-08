<?php
$currentPage = $_GET['page'] ?? 'home';
$navItems = [
    ['label' => 'Dashboard',              'icon' => 'bi-speedometer2',    'page' => 'home'],
    ['label' => 'Polling Unit Results',   'icon' => 'bi-bar-chart',      'page' => 'polling-result'],
    ['label' => 'LGA Summary',            'icon' => 'bi-pie-chart',      'page' => 'lga-result'],
    ['label' => 'Add New Polling Unit',   'icon' => 'bi-plus-square',    'page' => 'add-result'],
];
function pageUrl(string $page): string {
    return $page === 'home' ? 'index.php' : 'index.php?page=' . $page;
}
?>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="bi bi-box-seam-fill"></i>
        </div>
        <span class="sidebar-brand-text">Bincom Election</span>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-label">Main Menu</div>
        <?php foreach ($navItems as $item): ?>
            <a href="<?= pageUrl($item['page']) ?>"
               class="sidebar-link <?= $currentPage === $item['page'] ? 'active' : '' ?>">
                <i class="bi <?= $item['icon'] ?>"></i>
                <span><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="#" class="sidebar-link" id="sidebarCollapseBtn">
            <i class="bi bi-chevron-left"></i>
            <span>Collapse</span>
        </a>
    </div>
</aside>

<!-- Topbar -->
<header class="topbar" id="topbar">
    <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>

    <div class="topbar-search">
        <i class="bi bi-search"></i>
        <input type="text" id="searchPollingUnit" placeholder="Search polling units..." autocomplete="off">
        <div class="search-results-dropdown" id="searchResults"></div>
    </div>

    <div class="topbar-right">
        <span class="topbar-btn" title="Notifications">
            <i class="bi bi-bell"></i>
            <span class="dot"></span>
        </span>
    </div>
</header>

<!-- Main Content -->
<main class="main-content" id="mainContent">