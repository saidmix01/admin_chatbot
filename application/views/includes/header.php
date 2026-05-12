<!DOCTYPE html>

<html lang="es" class="material-style layout-fixed">

<head>
    <title>Wapi Admin | <?=$title ?? 'Dashboard'?></title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Wapi - Plataforma WhatsApp para negocios" />
    <link rel="icon" type="image/svg+xml" href="<?=base_url()?>assets/img/favicon.svg">
    <link rel="icon" type="image/x-icon" href="<?=base_url()?>assets/img/favicon.ico">

    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon fonts -->
    <link rel="stylesheet" href="<?=base_url()?>assets/fonts/fontawesome.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/fonts/feather.css">

    <!-- Core stylesheets -->
    <link rel="stylesheet" href="<?=base_url()?>assets/css/bootstrap-material.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/css/shreerang-material.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/css/uikit.css">

    <!-- Libs -->
    <link rel="stylesheet" href="<?=base_url()?>assets/libs/perfect-scrollbar/perfect-scrollbar.css">

    <!-- WAPI SaaS Custom CSS -->
    <link rel="stylesheet" href="<?=base_url()?>css/saas.css?v=1">
    <link rel="stylesheet" href="<?=base_url()?>css/general.css">

    <style>
        /* Mobile nav dropdown */
        .mobile-nav-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border-bottom: 1px solid var(--saas-gray-200);
            box-shadow: var(--saas-shadow-lg);
            z-index: 999;
            max-height: 70vh;
            overflow-y: auto;
            padding: 0.5rem 0;
        }
        .mobile-nav-dropdown.open {
            display: block;
        }
        .mobile-nav-dropdown a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            color: var(--saas-gray-700);
            font-size: 0.9375rem;
            text-decoration: none;
            transition: background 0.15s;
        }
        .mobile-nav-dropdown a:hover,
        .mobile-nav-dropdown a:active {
            background: var(--saas-gray-50);
        }
        .mobile-nav-dropdown a.active {
            color: var(--saas-primary);
            background: var(--saas-primary-light);
            font-weight: 500;
        }
        .mobile-nav-dropdown i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        .mobile-nav-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.3);
            z-index: 998;
        }
        .mobile-nav-overlay.open {
            display: block;
        }
        @media (min-width: 992px) {
            .mobile-nav-dropdown,
            .mobile-nav-overlay {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="overlay loading" style="display: none;">
        <div class="icon"><img class="rotate-img" src="<?=base_url()?>assets/img/logo_128.png" alt=""></div>
    </div>

    <div class="page-loader">
        <div class="bg-primary"></div>
    </div>

    <div class="layout-wrapper layout-2">
        <div class="layout-inner">

            <!-- DESKTOP SIDEBAR (solo > 992px) -->
            <div id="layout-sidenav" class="layout-sidenav sidenav sidenav-vertical bg-white logo-dark d-none d-lg-block">
                
                <div class="app-brand" style="padding: 1rem 1.25rem;">
                    <img src="<?=base_url()?>assets/img/logo-wapi.svg" alt="Wapi" style="height: 32px;">
                </div>

                <ul class="sidenav-inner py-1">
                    <?php if(!empty($menus)): ?>
                    <?php 
                    $current_url = trim($this->uri->segment(1));
                    foreach ($menus as $menu):
                        $is_active = ($current_url === $menu->men_url || 
                                     ($current_url === '' && $menu->men_url === 'home'));
                    ?>
                    <li class="sidenav-item <?= $is_active ? 'active' : '' ?>">
                        <a href="<?=base_url()?><?=$menu->men_url?>" class="sidenav-link" <?= $menu->men_url === 'preview' ? 'target="_blank"' : '' ?>>
                            <i class="sidenav-icon <?=$menu->men_icon?>"></i>
                            <div><?=$menu->men_description?></div>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- / Desktop Sidebar -->

            <!-- MOBILE MENU OVERLAY -->
            <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>

            <!-- MOBILE DROPDOWN NAV -->
            <div class="mobile-nav-dropdown" id="mobileNavDropdown">
                <?php if(!empty($menus)): ?>
                <?php foreach ($menus as $menu):
                    $is_active = ($current_url ?? '') === $menu->men_url || 
                                 (($current_url ?? '') === '' && $menu->men_url === 'home');
                ?>
                <a href="<?=base_url()?><?=$menu->men_url?>" class="<?= $is_active ? 'active' : '' ?>" <?= $menu->men_url === 'preview' ? 'target="_blank"' : '' ?>>
                    <i class="<?=$menu->men_icon?>"></i>
                    <?=$menu->men_description?>
                </a>
                <?php endforeach; ?>
                <hr style="margin: 0.5rem 1rem; border-color: var(--saas-gray-100);">
                <a href="<?=base_url()?>Login/logout">
                    <i class="feather icon-power" style="color: var(--saas-danger);"></i>
                    Cerrar sesión
                </a>
                <?php endif; ?>
            </div>

            <!-- MAIN CONTENT -->
            <div class="layout-container">

                <!-- Top Navbar -->
                <nav class="layout-navbar navbar navbar-expand-lg align-items-lg-center" id="layout-navbar" style="background: #fff !important; position: relative;">

                    <!-- Hamburger for mobile dropdown -->
                    <button class="navbar-toggler d-lg-none" type="button" id="mobileMenuToggle" style="border: none; outline: none; padding: 0.25rem 0.5rem; margin-right: 0.5rem; color: var(--saas-gray-600); font-size: 1.25rem; cursor: pointer;">
                        <i class="feather icon-menu"></i>
                    </button>

                    <a href="<?=base_url()?>Home" class="navbar-brand d-lg-none py-0 mr-4" style="padding: 0 !important; border: none !important;">
                        <img src="<?=base_url()?>assets/img/logo-wapi.svg" alt="Wapi" style="height: 24px;">
                    </a>

                    <div style="flex: 1;"></div>

                    <!-- Desktop user dropdown -->
                    <div class="d-none d-lg-block">
                        <div class="demo-navbar-user nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-toggle="dropdown" style="padding: 0;">
                                <div class="d-flex align-items-center" style="gap: 0.5rem;">
                                    <div style="width: 32px; height: 32px; background: var(--saas-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.8125rem; font-weight: 600;">
                                        <?= strtoupper(substr($user_data->us_name ?? 'U', 0, 1)) ?>
                                    </div>
                                    <span class="font-weight-medium" style="font-size: 0.875rem; color: var(--saas-gray-700);"><?=$user_data->us_name ?? 'Usuario'?></span>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" style="border-radius: var(--saas-radius-sm); border: 1px solid var(--saas-gray-200); box-shadow: var(--saas-shadow-lg);">
                                <div class="dropdown-item" style="font-size: 0.8125rem; color: var(--saas-gray-500); padding: 0.5rem 1rem;">
                                    <?=$user_data->us_email ?? ''?>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a href="<?=base_url()?>Login/logout" class="dropdown-item">
                                    <i class="feather icon-power" style="color: var(--saas-danger);"></i> &nbsp; Cerrar sesión
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile user icon -->
                    <div class="d-lg-none" style="color: var(--saas-gray-600); font-size: 1.1rem; display: flex; align-items: center;">
                        <div style="width: 30px; height: 30px; background: var(--saas-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.75rem; font-weight: 600;">
                            <?= strtoupper(substr($user_data->us_name ?? 'U', 0, 1)) ?>
                        </div>
                    </div>

                </nav>
                <!-- / Top Navbar -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.getElementById('mobileMenuToggle');
    var dropdown = document.getElementById('mobileNavDropdown');
    var overlay = document.getElementById('mobileNavOverlay');
    
    function closeMenu() {
        dropdown.classList.remove('open');
        overlay.classList.remove('open');
    }
    
    if (toggle && dropdown) {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('open');
            overlay.classList.toggle('open');
        });
    }
    
    if (overlay) {
        overlay.addEventListener('click', closeMenu);
    }
    
    // Close on link click
    if (dropdown) {
        dropdown.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', closeMenu);
        });
    }
});
</script>