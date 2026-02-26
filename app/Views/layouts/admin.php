<!DOCTYPE html>
<?php
$currentLocale = service('request')->getLocale();
$switchLocale = $currentLocale === 'es-MX' ? 'en' : 'es-MX';
$switchLabel = $currentLocale === 'es-MX' ? 'EN' : 'ES-MX';
$modules = app_modules();
$visibleModules = current_user_visible_modules();
$firstSegment = service('request')->getUri()->getSegment(1);
?>
<html lang="<?= esc($currentLocale) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) : lang('App.dashboard') ?></title>
    <!-- AdminLTE CSS via CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css" crossorigin="anonymous" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" crossorigin="anonymous" />
    <style>
        :root {
            --cobalt-950: #03122b;
            --cobalt-900: #08214a;
            --cobalt-850: #0d2e62;
            --cobalt-800: #114289;
            --cobalt-700: #1b57ac;
            --cobalt-600: #2f6fcb;
            --metal-100: #eaf1fb;
            --metal-200: #c8d8ee;
            --metal-300: #9db5d7;
            --metal-500: #6f8cb4;
            --text-dark: #10233f;
            --content-bg: #e6edf8;
        }

        body {
            background:
                radial-gradient(circle at 88% 10%, rgba(183, 213, 255, 0.2), transparent 36%),
                linear-gradient(150deg, var(--cobalt-950), var(--cobalt-900) 35%, var(--cobalt-800));
            color: var(--metal-100);
        }

        .main-header.navbar {
            background: linear-gradient(135deg, rgba(13, 46, 98, 0.95), rgba(11, 37, 79, 0.95)) !important;
            border-bottom: 1px solid rgba(178, 202, 236, 0.3);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 3px 16px rgba(5, 14, 32, 0.45);
        }

        .main-header .nav-link,
        .main-header .navbar-nav .nav-link {
            color: var(--metal-100) !important;
        }

        .main-sidebar {
            background: linear-gradient(180deg, #061a3a 0%, #09295b 48%, #0d3573 100%) !important;
            box-shadow: inset -1px 0 0 rgba(200, 218, 243, 0.14);
        }

        .brand-link {
            border-bottom: 1px solid rgba(181, 203, 236, 0.24) !important;
            color: #eaf3ff !important;
            background: linear-gradient(120deg, rgba(220, 234, 255, 0.08), rgba(255, 255, 255, 0));
        }

        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
            background: linear-gradient(135deg, #2f6fcb, #1a4f9f) !important;
            color: #ffffff !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link {
            color: #d3e4ff;
        }

        .content-wrapper {
            background: var(--content-bg);
            color: var(--text-dark);
        }

        .content-header h1 {
            color: #15366a;
            font-weight: 700;
        }

        .card {
            border: 1px solid #b4c8e3;
            box-shadow: 0 6px 18px rgba(12, 42, 88, 0.18);
        }

        .card-header {
            background: linear-gradient(125deg, #dfe9f8, #f8fbff);
            border-bottom: 1px solid #bfd0e7;
        }

        .small-box {
            border-radius: 0.4rem;
            border: 1px solid rgba(194, 211, 236, 0.45);
            box-shadow: 0 8px 18px rgba(14, 38, 79, 0.24);
            color: #f5f9ff !important;
        }

        .small-box.bg-info { background: linear-gradient(135deg, #1f5db9, #184a99) !important; }
        .small-box.bg-success { background: linear-gradient(135deg, #256ebf, #1b579d) !important; }
        .small-box.bg-warning { background: linear-gradient(135deg, #3f7fd2, #245cae) !important; }
        .small-box.bg-danger { background: linear-gradient(135deg, #5b8fdb, #2f6cc2) !important; }

        .btn-primary {
            border-color: #1f58ac;
            background: linear-gradient(145deg, #3f79d1, #1f58ac);
        }

        .btn-info {
            border-color: #2a69be;
            background: linear-gradient(145deg, #4f8bde, #2a69be);
        }

        .btn-warning {
            border-color: #2c61ad;
            color: #f4f8ff;
            background: linear-gradient(145deg, #4a7ec8, #2c61ad);
        }

        .btn-danger {
            border-color: #28589d;
            background: linear-gradient(145deg, #4573b7, #28589d);
        }

        .table thead th {
            background: #ebf2fd;
            color: #1a3f76;
            border-bottom-color: #c4d4ea;
        }

        .badge-success {
            background-color: #2f6fcb !important;
        }

        .badge-warning {
            background-color: #5b8fdb !important;
            color: #fff;
        }

        .badge-danger {
            background-color: #245cae !important;
        }

        .main-footer {
            background: #f2f6fd;
            border-top: 1px solid #c5d4e7;
            color: #23426e;
        }

        .lang-switch-link {
            border: 1px solid rgba(178, 202, 236, 0.38);
            border-radius: 999px;
            padding: 0.25rem 0.75rem !important;
            margin-right: 0.5rem;
            background: linear-gradient(145deg, rgba(209, 226, 250, 0.16), rgba(112, 149, 204, 0.22));
            font-weight: 600;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= site_url('/') ?>" class="nav-link"><?= esc(lang('App.home')) ?></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a href="<?= site_url('lang/' . $switchLocale) ?>" class="nav-link lang-switch-link" title="<?= esc(lang('App.change_language')) ?>">
                    <i class="fas fa-globe-americas mr-1"></i><?= esc($switchLabel) ?>
                </a>
            </li>
            <?php if(session()->get('user_id')): ?>
                <li class="nav-item">
                    <span class="nav-link"><?= esc(lang('App.hello_user', [session()->get('user_name')])) ?></span>
                </li>
                <li class="nav-item">
                    <span class="nav-link">
                        <span class="badge badge-info"><?= esc(strtoupper((string) session()->get('user_role'))) ?></span>
                    </span>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('logout') ?>" class="nav-link"><?= esc(lang('App.logout')) ?></a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a href="<?= site_url('login') ?>" class="nav-link"><?= esc(lang('App.login')) ?></a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('register') ?>" class="nav-link"><?= esc(lang('App.register')) ?></a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="<?= site_url('/') ?>" class="brand-link">
            <span class="brand-text font-weight-light"><?= esc(lang('App.admin_panel')) ?></span>
        </a>
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <?php foreach ($modules as $moduleKey => $module): ?>
                        <?php if (! in_array($moduleKey, $visibleModules, true)): ?>
                            <?php continue; ?>
                        <?php endif; ?>
                        <?php
                            $moduleRoute = (string) $module['route'];
                            $moduleSegment = explode('/', $moduleRoute)[0];
                            $isActive = $firstSegment === $moduleSegment || ($moduleSegment === 'dashboard' && ($firstSegment === '' || $firstSegment === null));
                        ?>
                        <li class="nav-item">
                            <a href="<?= site_url($moduleRoute) ?>" class="nav-link <?= $isActive ? 'active' : '' ?>">
                                <i class="nav-icon <?= esc((string) $module['icon']) ?>"></i>
                                <p><?= esc(lang((string) $module['label'])) ?></p>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?= isset($title) ? esc($title) : lang('App.dashboard') ?></h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= esc(session()->getFlashdata('success')) ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= esc(session()->getFlashdata('error')) ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>
                <?= $this->renderSection('content') ?>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <strong><?= esc(lang('App.copyright')) ?> &copy; <?= date('Y') ?> <a href="#"><?= esc(lang('App.your_company')) ?></a>.</strong> <?= esc(lang('App.all_rights_reserved')) ?>
    </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery and Bootstrap via CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>

