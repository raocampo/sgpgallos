<?php

require_once __DIR__ . '/../includes/app.php';

require_auth();

$url_base = admin_base_url();
$rutaActual = $_SERVER['REQUEST_URI'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Panel Administrativo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link href="<?php echo e(admin_url('bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(admin_url('bootstrap/css/all.min.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset_url('css/estilo.css')); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo e(asset_url('css/style.css')); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo e(asset_url('css/style-responsive.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset_url('css/monthly.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset_url('css/admin-panel.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset_url('DataTables/datatables.min.css')); ?>" rel="stylesheet">
  <script src="<?php echo e(asset_url('js/jquery.js')); ?>"></script>
  <script src="<?php echo e(asset_url('DataTables/datatables.min.js')); ?>"></script>
</head>
<body class="app-shell">
  <div class="app-frame">
    <aside class="app-sidebar" id="app-sidebar" aria-label="Navegacion principal">
      <div class="app-sidebar__inner">
        <div class="app-sidebar__brand">
          <div class="app-brand d-flex align-items-center gap-3">
            <img src="<?php echo e(asset_url('images/Logo.png')); ?>" alt="Logo" style="height: 68px; width: auto;">
            <div>
              <span class="app-kicker">Panel maestro</span>
              <h1 class="app-title">Sistema de Competencia</h1>
              <div class="app-subtitle">Usuario activo: <?php echo e($_SESSION['nombre'] ?? $_SESSION['apodo']); ?></div>
            </div>
          </div>
          <button type="button" class="app-sidebar__close d-lg-none" data-sidebar-close aria-label="Cerrar menu">
            <span class="app-icon-glyph" aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="app-sidebar__section">
          <div class="app-sidebar__section-label">Configuracion base</div>
          <nav class="nav flex-column app-nav app-sidebar__nav">
            <a class="nav-link <?php echo strpos($rutaActual, '/admin/index.php') !== false || rtrim($rutaActual, '/') === '/SG/admin' || rtrim($rutaActual, '/') === '/SG/admin/' ? 'active' : ''; ?>" href="<?php echo e(admin_url()); ?>">Inicio</a>
            <a class="nav-link <?php echo strpos($rutaActual, '/secciones/torneos') !== false ? 'active' : ''; ?>" href="<?php echo e(admin_url('secciones/torneos/')); ?>">Torneos</a>
            <a class="nav-link <?php echo strpos($rutaActual, '/secciones/representantes') !== false ? 'active' : ''; ?>" href="<?php echo e(admin_url('secciones/representantes/')); ?>">Representantes</a>
            <a class="nav-link <?php echo strpos($rutaActual, '/secciones/familias') !== false ? 'active' : ''; ?>" href="<?php echo e(admin_url('secciones/familias/')); ?>">Criaderos</a>
            <a class="nav-link <?php echo strpos($rutaActual, '/secciones/usuarios') !== false ? 'active' : ''; ?>" href="<?php echo e(admin_url('secciones/usuarios/')); ?>">Usuarios</a>
            <a class="nav-link <?php echo strpos($rutaActual, '/secciones/usuarios/cuenta.php') !== false ? 'active' : ''; ?>" href="<?php echo e(admin_url('secciones/usuarios/cuenta.php')); ?>">Mi clave</a>
            <a class="nav-link" href="<?php echo e(admin_url('cerrar.php')); ?>">Cerrar sesion</a>
          </nav>
        </div>

        <div class="app-sidebar__footer">
          <div class="app-sidebar__meta-label">Sesion activa</div>
          <div class="app-sidebar__meta-value"><?php echo e($_SESSION['nombre'] ?? $_SESSION['apodo']); ?></div>
          <div class="app-sidebar__meta-copy">Panel administrativo general del sistema.</div>
        </div>
      </div>
    </aside>

    <div class="app-sidebar-backdrop" data-sidebar-close></div>

    <div class="app-content-shell">
      <header class="app-shellbar">
        <div class="app-shellbar__group">
          <button type="button" class="app-sidebar-toggle" data-sidebar-toggle aria-controls="app-sidebar" aria-expanded="false" aria-label="Mostrar u ocultar sidebar">
            <span class="app-icon-glyph" aria-hidden="true">&#9776;</span>
          </button>
          <div class="app-shellbar__copy">
            <div class="app-shellbar__eyebrow">Panel maestro</div>
            <div class="app-shellbar__title">Sistema de Competencia</div>
          </div>
        </div>
      </header>

      <main class="container-fluid app-main">
        <?php render_flash(); ?>
