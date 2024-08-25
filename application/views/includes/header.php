<!DOCTYPE html>

<html lang="en" class="material-style layout-fixed">

<head>
    <title>Dashboard | Chat bot Ws</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Empire Bootstrap admin template made using Bootstrap 4, it has tons of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
    <meta name="keywords" content="Empire, bootstrap admin template, bootstrap admin panel, bootstrap 4 admin template, admin template">
    <meta name="author" content="Srthemesvilla" />
    <link rel="icon" type="image/x-icon" href="<?=base_url()?>assets/img/favicon.ico">

    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

    <!-- Icon fonts -->
    <link rel="stylesheet" href="<?=base_url()?>assets/fonts/fontawesome.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/fonts/ionicons.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/fonts/linearicons.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/fonts/open-iconic.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/fonts/pe-icon-7-stroke.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/fonts/feather.css">

    <!-- Core stylesheets -->
    <link rel="stylesheet" href="<?=base_url()?>assets/css/bootstrap-material.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/css/shreerang-material.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/css/uikit.css">

    <!-- Libs -->
    <link rel="stylesheet" href="<?=base_url()?>assets/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/libs/flot/flot.css">
	<link rel="stylesheet" href="<?=base_url()?>css/general.css">

</head>

<body>
	<div class="overlay loading" style="display: none;">
        <div class="icon"><img class="rotate-img" src="<?=base_url()?>assets/img/logo_128.png" alt=""></div> <!-- Puedes cambiar este icono por el que prefieras -->
    </div>
    <!-- [ Preloader ] Start -->
    <div class="page-loader">
        <div class="bg-primary"></div>
    </div>
    <!-- [ Preloader ] End -->

    <!-- [ Layout wrapper ] Start -->
    <div class="layout-wrapper layout-2">
        <div class="layout-inner">
            <!-- [ Layout sidenav ] Start -->
            <div id="layout-sidenav" class="layout-sidenav sidenav sidenav-vertical bg-white logo-dark">
                <!-- Brand demo (see assets/css/demo/demo.css) -->
                <div class="app-brand demo">
                    <span class="app-brand-logo demo">
                        <img src="<?=base_url()?>assets/img/logo.png" alt="Brand Logo" class="img-fluid">
                    </span>
                    <a href="<?=base_url()?>Home" class="app-brand-text demo sidenav-text font-weight-normal ml-2" style="font-size: 15px;">Chat Bot Whatsapp</a>
                </div>
                <div class="sidenav-divider mt-0"></div>

                <!-- Links -->
                <ul class="sidenav-inner py-1">

                    <!-- Dashboards -->
                    <li class="sidenav-item active">
                        <a href="<?=base_url()?>Home" class="sidenav-link">
                            <i class="sidenav-icon feather icon-home"></i>
                            <div>Dashboards</div>
                        </a>
                    </li>

                    <!-- Layouts -->
                    <li class="sidenav-divider mb-1"></li>
                    <li class="sidenav-header small font-weight-semibold">Main Menu</li>
					<?php
						foreach ($menus as $menu) {
					?>
                    <li class="sidenav-item">
                        <a href="<?=base_url()?><?=$menu->men_url?>" class="sidenav-link">
                            <i class="<?=$menu->men_icon?>"></i>
                            &nbsp;<div><?=$menu->men_description;?></div>
                        </a>
                    </li>
					<?php } ?>
                </ul>
            </div>
            <!-- [ Layout sidenav ] End -->
            <!-- [ Layout container ] Start -->
            <div class="layout-container">
                <!-- [ Layout navbar ( Header ) ] Start -->
                <nav class="layout-navbar navbar navbar-expand-lg align-items-lg-center bg-dark container-p-x" id="layout-navbar">

                    <!-- Brand demo (see assets/css/demo/demo.css) -->
                    <a href="<?=base_url()?>Home" class="navbar-brand app-brand demo d-lg-none py-0 mr-4">
                        <span class="">
                            <img src="<?=base_url()?>assets/img/logo.png" alt="Brand Logo" class="img-fluid">
                        </span>
                        <span class="app-brand-text demo font-weight-normal ml-2" style="font-size: 15px;">Chat Bot Whatsapp</span>
                    </a>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#layout-navbar-collapse">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="navbar-collapse collapse" id="layout-navbar-collapse">
                        <!-- Divider -->
                        <hr class="d-lg-none w-100 my-2">

                        <div class="navbar-nav align-items-lg-center ml-auto">
						<?php
							foreach ($menus as $menu) {
						?>
                            <div class="demo-navbar-notifications nav-item dropdown mr-lg-3">
                                <a class="nav-link" href="<?=base_url()?><?=$menu->men_url?>">
                                    <i class="<?=$menu->men_icon?>"></i>
                                    <span class="badge badge-danger badge-dot indicator"></span>
                                    <span class="d-lg-none align-middle">&nbsp; <?=$menu->men_description?></span>
                                </a>
                            </div>
						<?php }?>
                            <!-- Divider -->
                            <div class="nav-item d-none d-lg-block text-big font-weight-light line-height-1 opacity-25 mr-3 ml-1">|</div>
                            <div class="demo-navbar-user nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                                    <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
                                        <img src="<?=base_url()?>assets/img/avatars/1.png" alt class="d-block ui-w-30 rounded-circle">
                                        <span class="px-1 mr-lg-2 ml-2 ml-lg-0"><?=$user_data->us_name; ?></span>
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="javascript:" class="dropdown-item">
                                        <i class="feather icon-user text-muted"></i> &nbsp; My profile</a>
                                    <a href="javascript:" class="dropdown-item">
                                        <i class="feather icon-mail text-muted"></i> &nbsp; Messages</a>
                                    <a href="javascript:" class="dropdown-item">
                                        <i class="feather icon-settings text-muted"></i> &nbsp; Account settings</a>
                                    <div class="dropdown-divider"></div>
                                    <a href="<?=base_url()?>Login/logout" class="dropdown-item">
                                        <i class="feather icon-power text-danger"></i> &nbsp; Log Out</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
                <!-- [ Layout navbar ( Header ) ] End -->
