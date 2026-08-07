<?php
$admin_active_menu = isset($admin_active_menu) ? $admin_active_menu : '';
$admin_name = $this->session->userdata('nama') ?: 'Administrator';
$admin_initial = strtoupper(substr(trim($admin_name), 0, 1));
?>
<nav class="navbar navbar-expand-lg navbar-light sticky-top admin-navbar">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('admin/dashboard'); ?>">
            <img src="<?= base_url('assets/logo/logo.png'); ?>" alt="Logo Digital Pencak Silat">
            <span>ADMIN PANEL</span>
        </a>

        <button class="navbar-toggler admin-navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Buka navigasi admin">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav admin-main-nav ms-lg-5 me-auto">
                <li class="nav-item">
                    <a class="admin-nav-link <?= $admin_active_menu === 'dashboard' ? 'active' : ''; ?>" href="<?= base_url('admin/dashboard'); ?>">
                        <i class="fas fa-th-large"></i><span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="admin-nav-link <?= $admin_active_menu === 'event' ? 'active' : ''; ?>" href="<?= base_url('admin/events#event-management'); ?>">
                        <i class="far fa-calendar-alt"></i><span>Event</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="admin-nav-link <?= $admin_active_menu === 'news' ? 'active' : ''; ?>" href="<?= base_url('admin/news'); ?>">
                        <i class="far fa-newspaper"></i><span>News</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="admin-nav-link <?= $admin_active_menu === 'api' ? 'active' : ''; ?>" href="<?= base_url('admin/api_management'); ?>">
                        <i class="fas fa-plug"></i><span>Integrasi API</span>
                    </a>
                </li>
            </ul>

            <div class="dropdown admin-account-dropdown">
                <button class="btn admin-account-button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="admin-avatar"><?= html_escape($admin_initial); ?></span>
                    <span class="admin-account-copy"><small>Administrator</small><strong><?= html_escape($admin_name); ?></strong></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end admin-account-menu">
                    <li class="admin-account-summary"><span class="admin-avatar large"><?= html_escape($admin_initial); ?></span><div><strong><?= html_escape($admin_name); ?></strong><small>Pengelola Portal</small></div></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item <?= $admin_active_menu === 'settings' ? 'active' : ''; ?>" href="<?= base_url('admin/pengaturan'); ?>"><i class="fas fa-cog"></i>Pengaturan Kontak</a></li>
                    <li><a class="dropdown-item" href="<?= base_url(); ?>" target="_blank" rel="noopener"><i class="fas fa-external-link-alt"></i>Lihat Website</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= base_url('admin/logout'); ?>" id="btn-logout"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
