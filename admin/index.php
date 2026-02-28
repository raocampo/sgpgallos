<?php

require_once __DIR__ . '/includes/app.php';
require_once __DIR__ . '/bd.php';

require_auth();

$totales = [
    'torneos' => 0,
    'representantes' => 0,
    'criaderos' => 0,
    'usuarios' => 0,
];

foreach ($totales as $tabla => $valor) {
    $mapa = [
        'torneos' => 'torneos',
        'representantes' => 'representante',
        'criaderos' => 'familias',
        'usuarios' => 'usuarios',
    ];

    $consulta = $conexion->query('SELECT COUNT(*) FROM ' . $mapa[$tabla]);
    $totales[$tabla] = (int) $consulta->fetchColumn();
}

$torneos = $conexion->query('SELECT ID, nombre, fecha_inicio, fecha_fin, tipoTorneo FROM torneos ORDER BY fecha_inicio DESC, ID DESC LIMIT 6')->fetchAll();

include __DIR__ . '/templates/header.php';
?>

<section class="dashboard-hero mb-4">
    <span class="app-kicker" style="background: rgba(255,255,255,.14); color: #fff;">Centro de control</span>
    <h2 class="page-title mb-3">Operacion general del sistema</h2>
    <p>Este panel concentra la administracion de torneos, criaderos, representantes, usuarios y el acceso rapido al flujo operativo de cada competencia.</p>
    <div class="hero-actions">
        <a class="btn btn-light" href="<?php echo e(admin_url('secciones/torneos/crear.php')); ?>">Crear torneo</a>
        <a class="btn btn-outline-light" href="<?php echo e(admin_url('secciones/usuarios/')); ?>">Gestionar usuarios</a>
    </div>
</section>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="stat-label">Torneos</div>
                <div class="stat-value"><?php echo e((string) $totales['torneos']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="stat-label">Representantes</div>
                <div class="stat-value"><?php echo e((string) $totales['representantes']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="stat-label">Criaderos</div>
                <div class="stat-value"><?php echo e((string) $totales['criaderos']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="stat-label">Usuarios</div>
                <div class="stat-value"><?php echo e((string) $totales['usuarios']); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header panel-toolbar">
        <div>
            <div class="panel-title">Torneos recientes</div>
            <div class="panel-note">Acceso rapido a las competiciones mas recientes registradas en el sistema.</div>
        </div>
        <a class="btn btn-primary btn-sm" href="<?php echo e(admin_url('secciones/torneos/crear.php')); ?>">Nuevo torneo</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle" id="tabla_id">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha inicio</th>
                        <th>Fecha fin</th>
                        <th>Tipo</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($torneos as $torneo): ?>
                        <tr>
                            <td><?php echo e($torneo['nombre']); ?></td>
                            <td><?php echo e($torneo['fecha_inicio']); ?></td>
                            <td><?php echo e($torneo['fecha_fin']); ?></td>
                            <td><?php echo e($torneo['tipoTorneo']); ?></td>
                            <td>
                                <a class="btn btn-outline-primary btn-sm" href="<?php echo e(admin_url('secciones/gallos/')); ?>?nombreTorneo=<?php echo urlencode($torneo['nombre']); ?>&torneoId=<?php echo urlencode((string) $torneo['ID']); ?>">Abrir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
