<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();
start_secure_session();
$context = require_tournament_context('Seleccione un torneo antes de registrar resultados.');
$torneoId = $context['torneoId'];
$nombreTorneo = $context['nombreTorneo'];

$txtID = isset($_GET['txtID']) ? (int) $_GET['txtID'] : 0;
$error = '';
$peleaActual = null;

if ($txtID > 0) {
    $consulta = $conexion->prepare('
        SELECT p.*, gl.anillo AS anilloL, fl.nombre AS nombre_familiaL, gv.anillo AS anilloV, fv.nombre AS nombre_familiaV
        FROM peleas p
        INNER JOIN gallos gl ON p.galloL = gl.ID
        INNER JOIN gallos gv ON p.galloV = gv.ID
        INNER JOIN familias fl ON gl.familiasId = fl.codigo
        INNER JOIN familias fv ON gv.familiasId = fv.codigo
        WHERE p.ID_Pelea = :id AND p.torneoId = :torneoId
    ');
    $consulta->bindValue(':id', $txtID, PDO::PARAM_INT);
    $consulta->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
    $consulta->execute();
    $peleaActual = $consulta->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_resultado'])) {
    require_csrf();

    $txtID = (int) $_POST['txtID'];
    $estado = post('estado');
    $ganador = post('ganador');
    $observaciones = post('observaciones');

    $consulta = $conexion->prepare('SELECT galloL, galloV FROM peleas WHERE ID_Pelea = :id AND torneoId = :torneoId');
    $consulta->bindValue(':id', $txtID, PDO::PARAM_INT);
    $consulta->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
    $consulta->execute();
    $peleaBase = $consulta->fetch();

    if (!$peleaBase) {
        $error = 'La pelea seleccionada no existe.';
    } elseif (!in_array($estado, ['pendiente', 'finalizada', 'anulada'], true)) {
        $error = 'Estado no valido.';
    } elseif ($estado === 'finalizada' && !in_array($ganador, [(string) $peleaBase['galloL'], (string) $peleaBase['galloV']], true)) {
        $error = 'Seleccione un ganador valido.';
    } else {
        $actualiza = $conexion->prepare('
            UPDATE peleas
            SET estado = :estado,
                ganador = :ganador,
                observaciones = :observaciones,
                fecha_resultado = :fecha_resultado
            WHERE ID_Pelea = :id AND torneoId = :torneoId
        ');
        $actualiza->bindValue(':estado', $estado);
        $actualiza->bindValue(':ganador', $estado === 'finalizada' ? $ganador : null, $estado === 'finalizada' ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $actualiza->bindValue(':observaciones', $observaciones !== '' ? $observaciones : null, $observaciones !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $actualiza->bindValue(':fecha_resultado', $estado !== 'pendiente' ? date('Y-m-d H:i:s') : null, $estado !== 'pendiente' ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $actualiza->bindValue(':id', $txtID, PDO::PARAM_INT);
        $actualiza->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
        $actualiza->execute();

        set_flash('success', 'Resultado guardado correctamente.');
        redirect_to('secciones/peleas/resultados.php?nombreTorneo=' . urlencode($nombreTorneo) . '&torneoId=' . $torneoId);
    }
}

$peleas = $conexion->prepare('
    SELECT p.ID_Pelea, p.estado, p.ganador, p.fecha_resultado,
           gl.anillo AS anilloL, fl.nombre AS nombre_familiaL,
           gv.anillo AS anilloV, fv.nombre AS nombre_familiaV
    FROM peleas p
    INNER JOIN gallos gl ON p.galloL = gl.ID
    INNER JOIN gallos gv ON p.galloV = gv.ID
    INNER JOIN familias fl ON gl.familiasId = fl.codigo
    INNER JOIN familias fv ON gv.familiasId = fv.codigo
    WHERE p.torneoId = :torneoId
    ORDER BY p.ID_Pelea ASC
');
$peleas->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
$peleas->execute();
$listaPeleas = $peleas->fetchAll();

$pendientes = 0;
$finalizadas = 0;
$anuladas = 0;

foreach ($listaPeleas as $pelea) {
    if ($pelea['estado'] === 'finalizada') {
        $finalizadas++;
    } elseif ($pelea['estado'] === 'anulada') {
        $anuladas++;
    } else {
        $pendientes++;
    }
}

include __DIR__ . '/../../templates/header.sub.php';
?>

<div class="page-intro">
    <div>
        <span class="app-kicker">Cierre deportivo</span>
        <h2 class="page-title mb-2">Resultados de peleas</h2>
        <p>Actualice el estado de cada pelea, registre el ganador y deje observaciones para la trazabilidad del torneo.</p>
    </div>
    <div class="badge-soft <?php echo $peleaActual ? 'accent' : ''; ?>">
        <?php echo $peleaActual ? 'Editando pelea #' . e((string) $peleaActual['ID_Pelea']) : 'Seleccione una pelea'; ?>
    </div>
</div>

<div class="stats-grid mb-4">
    <div class="card stat-card">
        <div class="stat-label">Total peleas</div>
        <div class="stat-value"><?php echo e((string) count($listaPeleas)); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Pendientes</div>
        <div class="stat-value"><?php echo e((string) $pendientes); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Finalizadas</div>
        <div class="stat-value"><?php echo e((string) $finalizadas); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Anuladas</div>
        <div class="stat-value"><?php echo e((string) $anuladas); ?></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header panel-toolbar">
                <div>
                    <div class="panel-title">Registrar resultado</div>
                    <div class="panel-note">Actualice el estado deportivo y deje trazabilidad operativa.</div>
                </div>
                <a class="btn btn-outline-secondary btn-sm" href="peleaGenerada.php">Ver tablero</a>
            </div>
            <div class="card-body">
                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger"><?php echo e($error); ?></div>
                <?php endif; ?>

                <?php if ($peleaActual): ?>
                    <form method="post" class="row g-3">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="guardar_resultado" value="1">
                        <input type="hidden" name="txtID" value="<?php echo e((string) $peleaActual['ID_Pelea']); ?>">

                        <div class="col-12">
                            <div class="fight-banner">
                                <strong><?php echo e($peleaActual['anilloL'] . ' - ' . $peleaActual['nombre_familiaL']); ?></strong>
                                <span>VS</span>
                                <strong><?php echo e($peleaActual['anilloV'] . ' - ' . $peleaActual['nombre_familiaV']); ?></strong>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="estado">Estado</label>
                            <select class="form-select" name="estado" id="estado" required>
                                <?php foreach (['pendiente', 'finalizada', 'anulada'] as $estado): ?>
                                    <option value="<?php echo e($estado); ?>" <?php echo ($peleaActual['estado'] ?? 'pendiente') === $estado ? 'selected' : ''; ?>>
                                        <?php echo e(ucfirst($estado)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="ganador">Ganador</label>
                            <select class="form-select" name="ganador" id="ganador">
                                <option value="">Seleccione</option>
                                <option value="<?php echo e((string) $peleaActual['galloL']); ?>" <?php echo (string) ($peleaActual['ganador'] ?? '') === (string) $peleaActual['galloL'] ? 'selected' : ''; ?>>
                                    <?php echo e($peleaActual['anilloL'] . ' - ' . $peleaActual['nombre_familiaL']); ?>
                                </option>
                                <option value="<?php echo e((string) $peleaActual['galloV']); ?>" <?php echo (string) ($peleaActual['ganador'] ?? '') === (string) $peleaActual['galloV'] ? 'selected' : ''; ?>>
                                    <?php echo e($peleaActual['anilloV'] . ' - ' . $peleaActual['nombre_familiaV']); ?>
                                </option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="observaciones">Observaciones</label>
                            <textarea class="form-control" name="observaciones" id="observaciones" rows="4"><?php echo e($peleaActual['observaciones'] ?? ''); ?></textarea>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-success" type="submit">Guardar resultado</button>
                            <a class="btn btn-outline-secondary" href="resultados.php">Cancelar</a>
                        </div>
                    </form>
                <?php else: ?>
                    <p class="mb-0 text-muted">Seleccione una pelea de la tabla para registrar o actualizar su resultado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header panel-toolbar">
                <div>
                    <div class="panel-title">Listado de peleas</div>
                    <div class="panel-note">Seleccione una fila para editar el resultado o revisar el cierre de cada pelea.</div>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($listaPeleas)): ?>
                <div class="table-responsive">
                    <table class="table align-middle" data-datatable="true">
                        <thead class="table-light">
                            <tr>
                                <th>Pelea</th>
                                <th>Lado A</th>
                                <th>Lado B</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listaPeleas as $index => $pelea): ?>
                                <tr>
                                    <td><?php echo e((string) ($index + 1)); ?></td>
                                    <td><?php echo e($pelea['anilloL'] . ' - ' . $pelea['nombre_familiaL']); ?></td>
                                    <td><?php echo e($pelea['anilloV'] . ' - ' . $pelea['nombre_familiaV']); ?></td>
                                    <td>
                                        <span class="badge-soft <?php echo $pelea['estado'] === 'finalizada' ? 'accent' : ''; ?>">
                                            <?php echo e($pelea['estado']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo e((string) $pelea['fecha_resultado']); ?></td>
                                    <td>
                                        <a class="btn btn-outline-primary btn-sm" href="resultados.php?txtID=<?php echo urlencode((string) $pelea['ID_Pelea']); ?>">Editar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="empty-panel">
                        <h3>No hay peleas para cerrar</h3>
                        <p>Primero pacte peleas desde la pantalla de cruces para habilitar el registro de resultados.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var estado = document.getElementById('estado');
        var ganador = document.getElementById('ganador');

        if (!estado || !ganador) {
            return;
        }

        var syncGanador = function () {
            var habilitado = estado.value === 'finalizada';
            ganador.disabled = !habilitado;

            if (!habilitado) {
                ganador.value = '';
            }
        };

        estado.addEventListener('change', syncGanador);
        syncGanador();
    });
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
