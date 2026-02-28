<?php

require_once __DIR__ . '/../includes/app.php';

require_auth();
start_secure_session();

$url_base = admin_base_url();
$rutaActual = $_SERVER['REQUEST_URI'] ?? '';
$context = require_tournament_context('Seleccione un torneo antes de continuar.');
$nombreTorneo = $context['nombreTorneo'];
$torneoId = $context['torneoId'];

function torneo_link(string $path): string
{
  global $nombreTorneo, $torneoId;

  return admin_url($path) . '?nombreTorneo=' . urlencode($nombreTorneo) . '&torneoId=' . urlencode((string) $torneoId);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php echo e($nombreTorneo); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link href="<?php echo e(admin_url('bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(admin_url('bootstrap/css/all.min.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset_url('css/estilo.css')); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo e(asset_url('css/style.css')); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo e(asset_url('css/style-responsive.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset_url('css/admin-panel.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset_url('DataTables/datatables.min.css')); ?>" rel="stylesheet">
  <script src="<?php echo e(asset_url('js/jquery.js')); ?>"></script>
  <script src="<?php echo e(asset_url('DataTables/datatables.min.js')); ?>"></script>
</head>
<body class="app-shell">
  <header class="app-topbar">
    <div class="container-fluid py-3">
      <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center gap-3">
        <div class="app-brand">
          <span class="app-kicker">Torneo activo</span>
          <h1 class="app-title"><?php echo e($nombreTorneo); ?></h1>
          <div class="app-subtitle">Gestion operativa del torneo seleccionado</div>
        </div>
        <nav class="nav nav-pills flex-wrap app-nav">
          <a class="nav-link <?php echo strpos($rutaActual, '/secciones/gallos') !== false ? 'active' : ''; ?>" href="<?php echo e(torneo_link('secciones/gallos/')); ?>">Gallos</a>
          <a class="nav-link <?php echo strpos($rutaActual, '/secciones/exclusiones') !== false ? 'active' : ''; ?>" href="<?php echo e(torneo_link('secciones/exclusiones/')); ?>">Exclusiones</a>
          <a class="nav-link <?php echo strpos($rutaActual, '/secciones/peleas/cotejamiento.php') !== false ? 'active' : ''; ?>" href="<?php echo e(torneo_link('secciones/peleas/cotejamiento.php')); ?>">Cotejas</a>
          <a class="nav-link <?php echo strpos($rutaActual, '/secciones/peleas/peleaGenerada.php') !== false ? 'active' : ''; ?>" href="<?php echo e(torneo_link('secciones/peleas/peleaGenerada.php')); ?>">Peleas</a>
          <a class="nav-link <?php echo strpos($rutaActual, '/secciones/peleas/resultados.php') !== false ? 'active' : ''; ?>" href="<?php echo e(torneo_link('secciones/peleas/resultados.php')); ?>">Resultados</a>
          <a class="nav-link" href="<?php echo e(admin_url()); ?>">Inicio</a>
        </nav>
      </div>
    </div>
  </header>
  <main class="container-fluid app-main">
    <?php render_flash(); ?>
