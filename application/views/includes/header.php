<!DOCTYPE html>

<html lang="es" class="material-style layout-fixed">

<head>
    <title>Wapi Admin | <?=$title ?? 'Dashboard'?></title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Wapi - Plataforma WhatsApp para negocios" />
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

            <!-- ============================================
                 SIDEBAR
            ============================================ -->
            <div id="layout-sidenav" class="layout-sidenav sidenav sidenav-vertical bg-white logo-dark">
                
                <!-- Brand -->
                <div class="app-brand">
                    <span class="app-brand-logo">
                        <img src="<?=base_url()?>assets/img/logo.png" alt="Wapi" class="img-fluid" style="height: 32px;">
                    </span>
                    <span class="app-brand-text font-weight-semibold ml-2">Wapi</span>
                </div>

                <!-- Navigation -->
                <ul class="sidenav-inner py-1">

                    <li class="sidenav-header">Navegación</li>

                    <!-- Dashboard -->
                    <li class="sidenav-item <?= $active_menu === 'dashboard' ? 'active' : '' ?>">
                        <a href="<?=base_url()?>Home" class="sidenav-link">
                            <i class="sidenav-icon feather icon-home"></i>
                            <div>Dashboard</div>
                        </a>
                    </li>

                    <!-- WhatsApp QR -->
                    <li class="sidenav-item <?= $active_menu === 'whatsapp' ? 'active' : '' ?>">
                        <a href="<?=base_url()?>Whatsapp" class="sidenav-link">
                            <i class="sidenav-icon fab fa-whatsapp"></i>
                            <div>WhatsApp QR</div>
                        </a>
                    </li>

                    <li class="sidenav-divider"></li>
                    <li class="sidenav-header">Configuración</li>

                    <!-- Business Config -->
                    <li class="sidenav-item <?= $active_menu === 'negocio' ? 'active' : '' ?>">
                        <a href="<?=base_url()?>Business" class="sidenav-link">
                            <i class="sidenav-icon feather icon-briefcase"></i>
                            <div>Mi Negocio</div>
                        </a>
                    </li>

                    <!-- Bot Config -->
                    <li class="sidenav-item <?= $active_menu === 'bot' ? 'active' : '' ?>">
                        <a href="<?=base_url()?>BotConfig" class="sidenav-link">
                            <i class="sidenav-icon feather icon-message-square"></i>
                            <div>Bot Mensajes</div>
                        </a>
                    </li>

                    <!-- Products -->
                    <li class="sidenav-item <?= $active_menu === 'productos' ? 'active' : '' ?>">
                        <a href="<?=base_url()?>Products" class="sidenav-link">
                            <i class="sidenav-icon feather icon-package"></i>
                            <div>Productos</div>
                        </a>
                    </li>

                    <!-- Existing dynamic menus -->
                    <?php if(!empty($menus)): ?>
                    <li class="sidenav-divider"></li>
                    <li class="sidenav-header">Gestión</li>
                    <?php foreach ($menus as $menu): ?>
                    <li class="sidenav-item">
                        <a href="<?=base_url()?><?=$menu->men_url?>" class="sidenav-link">
                            <i class="sidenav-icon <?=$menu->men_icon?>"></i>
                            <div><?=$menu->men_description?></div>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <li class="sidenav-divider"></li>

                    <!-- Public Preview -->
                    <li class="sidenav-item <?= $active_menu === 'preview' ? 'active' : '' ?>">
                        <a href="<?=base_url()?>Preview" class="sidenav-link" target="_blank">
                            <i class="sidenav-icon feather icon-external-link"></i>
                            <div>Ver Página Pública</div>
                        </a>
                    </li>

                    <!-- Technical -->
                    <li class="sidenav-item <?= $active_menu === 'tecnico' ? 'active' : '' ?>">
                        <a href="<?=base_url()?>TechConfig" class="sidenav-link">
                            <i class="sidenav-icon feather icon-server"></i>
                            <div>Config. Técnica</div>
                        </a>
                    </li>

                </ul>
            </div>
            <!-- / Sidebar -->

            <!-- ============================================
                 MAIN CONTENT
            ============================================ -->
            <div class="layout-container">

                <!-- Top Navbar -->
                <nav class="layout-navbar navbar navbar-expand-lg align-items-lg-center" id="layout-navbar">

                    <a href="<?=base_url()?>Home" class="navbar-brand app-brand demo d-lg-none py-0 mr-4">
                        <span><img src="<?=base_url()?>assets/img/logo.png" alt="Wapi" class="img-fluid" style="height: 28px;"></span>
                        <span class="app-brand-text font-weight-semibold ml-2">Wapi</span>
                    </a>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#layout-navbar-collapse">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="navbar-collapse collapse" id="layout-navbar-collapse">
                        <hr class="d-lg-none w-100 my-2">

                        <div class="navbar-nav align-items-lg-center ml-auto">

                            <!-- User dropdown -->
                            <div class="demo-navbar-user nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-toggle="dropdown">
                                    <div class="d-flex align-items-center gap-2" style="gap: 0.5rem;">
                                        <div style="width: 32px; height: 32px; background: var(--saas-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.8125rem; font-weight: 600;">
                                            <?= strtoupper(substr($user_data->us_name ?? 'U', 0, 1)) ?>
                                        </div>
                                        <span class="d-none d-lg-inline font-weight-medium" style="font-size: 0.875rem; color: var(--saas-gray-700);"><?=$user_data->us_name ?? 'Usuario'?></span>
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
                    </div>
                </nav>
                <!-- / Top Navbar -->