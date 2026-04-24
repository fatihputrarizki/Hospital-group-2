<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

$menuItems = [
    ['key' => 'dashboard',     'icon' => 'bx bxs-dashboard',      'label' => 'Dashboard'],
    ['key' => 'patients',      'icon' => 'bx bxs-user-detail',    'label' => 'Pasien'],
    ['key' => 'doctors',       'icon' => 'bx bxs-first-aid',      'label' => 'Dokter'],
    ['key' => 'appointments',  'icon' => 'bx bxs-calendar-check', 'label' => 'Jadwal'],
    ['key' => 'records',       'icon' => 'bx bxs-file-doc',       'label' => 'Rekam Medis'],
    ['key' => 'medications',   'icon' => 'bx bxs-capsule',        'label' => 'Obat'],
    ['key' => 'prescriptions', 'icon' => 'bx bxs-notepad',        'label' => 'Resep'],
];

$pageTitles = [
    'dashboard'     => 'Dashboard',
    'patients'      => 'Manajemen Pasien',
    'doctors'       => 'Manajemen Dokter',
    'appointments'  => 'Jadwal Perjanjian',
    'records'       => 'Rekam Medis',
    'medications'   => 'Manajemen Obat',
    'prescriptions' => 'Resep Obat',
];
?>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class='bx bxs-plus-square'></i>
        </div>
        <div class="sidebar-brand">
            <span class="brand-name">RS Medika</span>
            <span class="brand-sub">Nusantara</span>
        </div>
    </div>

    <div class="sidebar-menu">
        <p class="menu-label">MENU UTAMA</p>
        <ul>
            <?php foreach ($menuItems as $item): ?>
            <li>
                <a class="<?= $page === $item['key'] ? 'active' : '' ?>"
                   href="<?= $item['key'] === 'dashboard' ? 'index.php' : 'index.php?page='.$item['key'] ?>">
                    <i class="<?= $item['icon'] ?>"></i>
                    <span><?= $item['label'] ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-footer-content">
            <i class='bx bxs-hospital'></i>
            <div>
                <p class="footer-title">Hospital Management</p>
                <p class="footer-sub">v2.0 &mdash; 2026</p>
            </div>
        </div>
    </div>
</aside>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- ===== TOPBAR ===== -->
<header class="topbar">
    <div class="topbar-left">
        <button class="topbar-toggle" onclick="toggleSidebar()">
            <i class='bx bx-menu'></i>
        </button>
        <div class="topbar-breadcrumb">
            <i class='bx bxs-home'></i>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current"><?= $pageTitles[$page] ?? 'Dashboard' ?></span>
        </div>
    </div>
    <div class="topbar-right">
        <div class="topbar-info">
            <i class='bx bx-calendar'></i>
            <span><?= date('l, d F Y') ?></span>
        </div>
        <div class="topbar-info" id="liveClock">
            <i class='bx bx-time-five'></i>
            <span><?= date('H:i:s') ?></span>
        </div>
    </div>
</header>
