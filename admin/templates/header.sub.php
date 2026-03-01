<?php

require_once __DIR__ . '/../includes/app.php';

require_auth();
start_secure_session();

$url_base = admin_base_url();
$rutaActual = $_SERVER['REQUEST_URI'] ?? '';
$context = require_tournament_context('Seleccione un torneo antes de continuar.');
$nombreTorneo = $context['nombreTorneo'];
$torneoId = $context['torneoId'];
$estadoTorneoActual = 'abierto';

if (isset($conexion) && $conexion instanceof PDO) {
  $torneoActual = fetch_tournament_record($conexion, $torneoId);
  $estadoTorneoActual = tournament_state_label($torneoActual['estado'] ?? null);
}

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
  <div class="app-frame">
    <aside class="app-sidebar" id="app-sidebar" aria-label="Navegacion del torneo">
      <div class="app-sidebar__inner">
        <div class="app-sidebar__brand">
          <div class="app-brand app-brand--stacked">
            <img src="<?php echo e(asset_url('images/Logo.png')); ?>" alt="Logo">
            <div>
              <h1 class="app-title">Panel del torneo</h1>
              <div class="app-subtitle"><?php echo e($nombreTorneo); ?></div>
            </div>
          </div>
          <button type="button" class="app-sidebar__close d-lg-none" data-sidebar-close aria-label="Cerrar menu">
            <span class="app-icon-glyph" aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="app-sidebar__section">
          <div class="app-sidebar__section-label">Flujo del torneo</div>
          <nav class="nav flex-column app-nav app-sidebar__nav">
            <a class="nav-link <?php echo strpos($rutaActual, '/secciones/torneos/dashboard.php') !== false ? 'active' : ''; ?>" href="<?php echo e(torneo_link('secciones/torneos/dashboard.php')); ?>">Resumen</a>
            <a class="nav-link <?php echo strpos($rutaActual, '/secciones/gallos') !== false ? 'active' : ''; ?>" href="<?php echo e(torneo_link('secciones/gallos/')); ?>">Gallos</a>
            <a class="nav-link <?php echo strpos($rutaActual, '/secciones/exclusiones') !== false ? 'active' : ''; ?>" href="<?php echo e(torneo_link('secciones/exclusiones/')); ?>">Exclusiones</a>
            <a class="nav-link <?php echo strpos($rutaActual, '/secciones/peleas/cotejamiento.php') !== false ? 'active' : ''; ?>" href="<?php echo e(torneo_link('secciones/peleas/cotejamiento.php')); ?>">Cotejas</a>
            <a class="nav-link <?php echo strpos($rutaActual, '/secciones/peleas/peleaGenerada.php') !== false ? 'active' : ''; ?>" href="<?php echo e(torneo_link('secciones/peleas/peleaGenerada.php')); ?>">Peleas</a>
            <a class="nav-link <?php echo strpos($rutaActual, '/secciones/peleas/resultados.php') !== false ? 'active' : ''; ?>" href="<?php echo e(torneo_link('secciones/peleas/resultados.php')); ?>">Resultados</a>
            <a class="nav-link" href="<?php echo e(admin_url('secciones/torneos/')); ?>">Volver a torneos</a>
            <a class="nav-link" href="<?php echo e(admin_url()); ?>">Inicio</a>
            <a class="nav-link" href="<?php echo e(admin_url('cerrar.php')); ?>">Cerrar sesion</a>
          </nav>
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
            <div class="app-shellbar__title"><?php echo e($nombreTorneo); ?></div>
          </div>
        </div>
        <div class="app-shellbar__meta">
          <span class="badge-soft <?php echo $estadoTorneoActual === 'cerrado' ? 'accent' : ''; ?>"><?php echo e($estadoTorneoActual); ?></span>
        </div>
      </header>

      <div class="tournament-note">
        <div class="tournament-note__title">Operacion del torneo</div>
        <div class="tournament-note__copy">Carga de gallos, exclusiones, cotejas, peleas y resultados del torneo seleccionado.</div>
      </div>

      <main class="container-fluid app-main">
        <?php render_flash(); ?>
