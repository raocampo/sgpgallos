<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();
start_secure_session();

$context = require_tournament_context('Seleccione un torneo antes de revisar su resumen.');
$torneoId = $context['torneoId'];
$nombreTorneo = $context['nombreTorneo'];
$redirectDashboard = 'secciones/torneos/dashboard.php?nombreTorneo=' . urlencode($nombreTorneo) . '&torneoId=' . $torneoId;
$torneoSoportaEstado = tournaments_support_state($conexion);
$peleasSoportanEstado = table_has_column($conexion, 'peleas', 'estado');
$peleasSoportanFechaResultado = table_has_column($conexion, 'peleas', 'fecha_resultado');

$torneo = fetch_tournament_record($conexion, $torneoId);
if (!$torneo) {
    set_flash('warning', 'El torneo seleccionado ya no existe.');
    redirect_to('secciones/torneos/');
}

$estadoTorneo = tournament_state_label($torneo['estado'] ?? null);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    if (!$torneoSoportaEstado) {
        set_flash('warning', 'La base de datos aun no tiene habilitado el cierre de torneos.');
        redirect_to($redirectDashboard);
    }

    if ($peleasSoportanEstado) {
        $pendientesConsulta = $conexion->prepare("SELECT COUNT(*) FROM peleas WHERE torneoId = :torneoId AND estado = 'pendiente'");
        $pendientesConsulta->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
        $pendientesConsulta->execute();
        $peleasPendientes = (int) $pendientesConsulta->fetchColumn();
    } else {
        $pendientesConsulta = $conexion->prepare("SELECT COUNT(*) FROM peleas WHERE torneoId = :torneoId");
        $pendientesConsulta->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
        $pendientesConsulta->execute();
        $peleasPendientes = (int) $pendientesConsulta->fetchColumn();
    }

    if (isset($_POST['cerrar_torneo'])) {
        if ($peleasPendientes > 0) {
            set_flash('warning', 'No se puede cerrar el torneo mientras existan peleas pendientes.');
        } else {
            $actualiza = $conexion->prepare("UPDATE torneos SET estado = 'cerrado', fecha_cierre_real = :fecha WHERE ID = :id");
            $actualiza->bindValue(':fecha', date('Y-m-d H:i:s'));
            $actualiza->bindValue(':id', $torneoId, PDO::PARAM_INT);
            $actualiza->execute();

            set_flash('success', 'Torneo cerrado correctamente.');
        }

        redirect_to($redirectDashboard);
    }

    if (isset($_POST['reabrir_torneo'])) {
        $actualiza = $conexion->prepare("UPDATE torneos SET estado = 'abierto', fecha_cierre_real = NULL WHERE ID = :id");
        $actualiza->bindValue(':id', $torneoId, PDO::PARAM_INT);
        $actualiza->execute();

        set_flash('success', 'Torneo reabierto correctamente.');
        redirect_to($redirectDashboard);
    }
}

$contadores = [
    'gallos' => 0,
    'cotejas' => 0,
    'peleas' => 0,
    'exclusiones' => 0,
];

$consultas = [
    'gallos' => 'SELECT COUNT(*) FROM gallos WHERE torneoId = :torneoId',
    'cotejas' => 'SELECT COUNT(*) FROM coteja WHERE torneoId = :torneoId',
    'peleas' => 'SELECT COUNT(*) FROM peleas WHERE torneoId = :torneoId',
    'exclusiones' => 'SELECT COUNT(*) FROM exclusiones WHERE torneoId = :torneoId',
];

foreach ($consultas as $clave => $sql) {
    $consulta = $conexion->prepare($sql);
    $consulta->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
    $consulta->execute();
    $contadores[$clave] = (int) $consulta->fetchColumn();
}

if ($peleasSoportanEstado) {
    $consultaPendientes = $conexion->prepare("SELECT COUNT(*) FROM peleas WHERE torneoId = :torneoId AND estado = 'pendiente'");
    $consultaPendientes->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
    $consultaPendientes->execute();
    $peleasPendientes = (int) $consultaPendientes->fetchColumn();

    $consultaFinalizadas = $conexion->prepare("SELECT COUNT(*) FROM peleas WHERE torneoId = :torneoId AND estado = 'finalizada'");
    $consultaFinalizadas->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
    $consultaFinalizadas->execute();
    $peleasFinalizadas = (int) $consultaFinalizadas->fetchColumn();

    $consultaAnuladas = $conexion->prepare("SELECT COUNT(*) FROM peleas WHERE torneoId = :torneoId AND estado = 'anulada'");
    $consultaAnuladas->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
    $consultaAnuladas->execute();
    $peleasAnuladas = (int) $consultaAnuladas->fetchColumn();
} else {
    $peleasPendientes = $contadores['peleas'];
    $peleasFinalizadas = 0;
    $peleasAnuladas = 0;
}

$consultaGallosLibres = $conexion->prepare('
    SELECT COUNT(*)
    FROM gallos g
    WHERE g.torneoId = :torneoId
      AND g.ID NOT IN (
            SELECT galloL FROM coteja WHERE torneoId = :torneoIdA
            UNION
            SELECT galloV FROM coteja WHERE torneoId = :torneoIdB
      )
');
$consultaGallosLibres->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
$consultaGallosLibres->bindValue(':torneoIdA', $torneoId, PDO::PARAM_INT);
$consultaGallosLibres->bindValue(':torneoIdB', $torneoId, PDO::PARAM_INT);
$consultaGallosLibres->execute();
$gallosLibres = (int) $consultaGallosLibres->fetchColumn();

$avance = $contadores['peleas'] > 0
    ? (int) round((($peleasFinalizadas + $peleasAnuladas) / $contadores['peleas']) * 100)
    : 0;

$campoEstadoPelea = $peleasSoportanEstado ? 'p.estado' : "'pendiente' AS estado";
$campoFechaResultado = $peleasSoportanFechaResultado ? 'p.fecha_resultado' : 'NULL AS fecha_resultado';

$consultaRecentes = $conexion->prepare('
    SELECT p.ID_Pelea, ' . $campoEstadoPelea . ', ' . $campoFechaResultado . ',
           gl.anillo AS anilloL, fl.nombre AS nombre_familiaL,
           gv.anillo AS anilloV, fv.nombre AS nombre_familiaV
    FROM peleas p
    INNER JOIN gallos gl ON p.galloL = gl.ID
    INNER JOIN gallos gv ON p.galloV = gv.ID
    INNER JOIN familias fl ON gl.familiasId = fl.codigo
    INNER JOIN familias fv ON gv.familiasId = fv.codigo
    WHERE p.torneoId = :torneoId
    ORDER BY p.ID_Pelea DESC
    LIMIT 8
');
$consultaRecentes->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
$consultaRecentes->execute();
$peleasRecientes = $consultaRecentes->fetchAll();

$consultaCriaderos = $conexion->prepare('
    SELECT f.nombre, COUNT(*) AS total
    FROM gallos g
    INNER JOIN familias f ON g.familiasId = f.codigo
    WHERE g.torneoId = :torneoId
    GROUP BY f.codigo, f.nombre
    ORDER BY total DESC, f.nombre ASC
    LIMIT 5
');
$consultaCriaderos->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
$consultaCriaderos->execute();
$criaderosTop = $consultaCriaderos->fetchAll();

include __DIR__ . '/../../templates/header.sub.php';
?>

<section class="dashboard-hero mb-4">
    <span class="app-kicker" style="background: rgba(255,255,255,.14); color: #fff;">Resumen operativo</span>
    <h2 class="page-title mb-3">Tablero del torneo</h2>
    <p>Revise la salud deportiva del torneo, el avance del cierre y las acciones principales sin entrar modulo por modulo.</p>
    <div class="hero-actions">
        <a class="btn btn-light" href="<?php echo e(admin_url('secciones/gallos/')); ?>?nombreTorneo=<?php echo urlencode($nombreTorneo); ?>&torneoId=<?php echo urlencode((string) $torneoId); ?>">Gallos</a>
        <a class="btn btn-light" href="<?php echo e(admin_url('secciones/peleas/cotejamiento.php')); ?>?nombreTorneo=<?php echo urlencode($nombreTorneo); ?>&torneoId=<?php echo urlencode((string) $torneoId); ?>">Cotejas</a>
        <a class="btn btn-outline-light" href="<?php echo e(admin_url('secciones/peleas/resultados.php')); ?>?nombreTorneo=<?php echo urlencode($nombreTorneo); ?>&torneoId=<?php echo urlencode((string) $torneoId); ?>">Resultados</a>
        <?php if ($torneoSoportaEstado && $estadoTorneo === 'abierto'): ?>
            <form method="post" class="d-inline">
                <?php echo csrf_input(); ?>
                <button type="submit" class="btn btn-outline-light" name="cerrar_torneo" value="1" data-confirm="Esto cerrara el torneo y bloqueara modificaciones. Desea continuar?">Cerrar torneo</button>
            </form>
        <?php elseif ($torneoSoportaEstado): ?>
            <form method="post" class="d-inline">
                <?php echo csrf_input(); ?>
                <button type="submit" class="btn btn-light" name="reabrir_torneo" value="1">Reabrir torneo</button>
            </form>
        <?php else: ?>
            <span class="badge-soft">Cierre no disponible aun</span>
        <?php endif; ?>
    </div>
</section>

<div class="stats-grid mb-4">
    <div class="card stat-card">
        <div class="stat-label">Estado</div>
        <div class="stat-value stat-value-sm"><?php echo e(ucfirst($estadoTorneo)); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Gallos</div>
        <div class="stat-value"><?php echo e((string) $contadores['gallos']); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Cotejas</div>
        <div class="stat-value"><?php echo e((string) $contadores['cotejas']); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Peleas</div>
        <div class="stat-value"><?php echo e((string) $contadores['peleas']); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Avance</div>
        <div class="stat-value"><?php echo e((string) $avance); ?>%</div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header panel-toolbar">
                <div>
                    <div class="panel-title">Estado del torneo</div>
                    <div class="panel-note">Control general del ciclo operativo y del cierre deportivo.</div>
                </div>
                <span class="badge-soft <?php echo $estadoTorneo === 'cerrado' ? 'accent' : ''; ?>">
                    <?php echo e($estadoTorneo === 'cerrado' ? 'Cerrado' : 'Abierto'); ?>
                </span>
            </div>
            <div class="card-body">
                <div class="soft-panel mb-3">
                    <strong>Tipo:</strong> <?php echo e($torneo['tipoTorneo']); ?><br>
                    <strong>Inicio:</strong> <?php echo e($torneo['fecha_inicio']); ?><br>
                    <strong>Fin programado:</strong> <?php echo e($torneo['fecha_fin']); ?><br>
                    <strong>Cierre real:</strong> <?php echo e((string) ($torneo['fecha_cierre_real'] ?? 'Pendiente')); ?>
                </div>

                <div class="stats-grid">
                    <div class="card stat-card">
                        <div class="stat-label">Pendientes</div>
                        <div class="stat-value"><?php echo e((string) $peleasPendientes); ?></div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-label">Finalizadas</div>
                        <div class="stat-value"><?php echo e((string) $peleasFinalizadas); ?></div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-label">Anuladas</div>
                        <div class="stat-value"><?php echo e((string) $peleasAnuladas); ?></div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-label">Exclusiones</div>
                        <div class="stat-value"><?php echo e((string) $contadores['exclusiones']); ?></div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-label">Gallos libres</div>
                        <div class="stat-value"><?php echo e((string) $gallosLibres); ?></div>
                    </div>
                </div>

                <?php if ($estadoTorneo === 'cerrado'): ?>
                    <div class="alert alert-warning mt-3 mb-0">El torneo esta cerrado. Los modulos deportivos quedan en modo consulta hasta reabrirlo.</div>
                <?php elseif ($peleasPendientes > 0): ?>
                    <div class="alert alert-info mt-3 mb-0">Todavia existen peleas pendientes. Para cerrar el torneo primero debe resolverlas o anularlas.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header panel-toolbar">
                <div>
                    <div class="panel-title">Criaderos con mas carga</div>
                    <div class="panel-note">Distribucion actual de gallos registrados por criadero.</div>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($criaderosTop)): ?>
                    <div class="stack-grid">
                        <?php foreach ($criaderosTop as $criadero): ?>
                            <div class="soft-panel d-flex justify-content-between align-items-center">
                                <strong><?php echo e($criadero['nombre']); ?></strong>
                                <span class="badge-soft"><?php echo e((string) $criadero['total']); ?> gallos</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-panel">
                        <h3>Sin datos deportivos</h3>
                        <p>Agregue gallos al torneo para construir el resumen operativo.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mt-4">
    <div class="card-header panel-toolbar">
        <div>
            <div class="panel-title">Peleas recientes</div>
            <div class="panel-note">Ultimos movimientos deportivos registrados en el torneo.</div>
        </div>
        <a class="btn btn-outline-primary btn-sm" href="<?php echo e(admin_url('secciones/peleas/peleaGenerada.php')); ?>?nombreTorneo=<?php echo urlencode($nombreTorneo); ?>&torneoId=<?php echo urlencode((string) $torneoId); ?>">Abrir tablero</a>
    </div>
    <div class="card-body">
        <?php if (!empty($peleasRecientes)): ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Lado A</th>
                            <th>Lado B</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($peleasRecientes as $pelea): ?>
                            <tr>
                                <td><?php echo e((string) $pelea['ID_Pelea']); ?></td>
                                <td><?php echo e($pelea['anilloL'] . ' - ' . $pelea['nombre_familiaL']); ?></td>
                                <td><?php echo e($pelea['anilloV'] . ' - ' . $pelea['nombre_familiaV']); ?></td>
                                <td><span class="badge-soft <?php echo $pelea['estado'] === 'finalizada' ? 'accent' : ''; ?>"><?php echo e($pelea['estado']); ?></span></td>
                                <td><?php echo e((string) ($pelea['fecha_resultado'] ?? '')); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-panel">
                <h3>No hay peleas registradas</h3>
                <p>Genere cotejas y pacte peleas para que el tablero empiece a mostrar actividad.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
