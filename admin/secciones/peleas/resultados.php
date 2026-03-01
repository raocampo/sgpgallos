<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();
start_secure_session();
$context = require_tournament_context('Seleccione un torneo antes de registrar resultados.');
$torneoId = $context['torneoId'];
$nombreTorneo = $context['nombreTorneo'];
$redirectResultados = 'secciones/peleas/resultados.php?nombreTorneo=' . urlencode($nombreTorneo) . '&torneoId=' . $torneoId;

$txtID = isset($_GET['txtID']) ? (int) $_GET['txtID'] : 0;
$error = '';
$peleaActual = null;

$consultaPeleaActual = $conexion->prepare('
    SELECT p.*, gl.anillo AS anilloL, fl.nombre AS nombre_familiaL, gv.anillo AS anilloV, fv.nombre AS nombre_familiaV
    FROM peleas p
    INNER JOIN gallos gl ON p.galloL = gl.ID
    INNER JOIN gallos gv ON p.galloV = gv.ID
    INNER JOIN familias fl ON gl.familiasId = fl.codigo
    INNER JOIN familias fv ON gv.familiasId = fv.codigo
    WHERE p.ID_Pelea = :id AND p.torneoId = :torneoId
');

if ($txtID > 0) {
    $consultaPeleaActual->bindValue(':id', $txtID, PDO::PARAM_INT);
    $consultaPeleaActual->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
    $consultaPeleaActual->execute();
    $peleaActual = $consultaPeleaActual->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_resultado'])) {
    require_csrf();
    ensure_open_tournament_or_redirect($conexion, $torneoId, $redirectResultados);

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
        redirect_to($redirectResultados);
    }

    if ($error !== '') {
        $consultaPeleaActual->bindValue(':id', $txtID, PDO::PARAM_INT);
        $consultaPeleaActual->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
        $consultaPeleaActual->execute();
        $peleaActual = $consultaPeleaActual->fetch();
    }
}

$peleas = $conexion->prepare('
    SELECT p.ID_Pelea, p.galloL, p.galloV, p.estado, p.ganador, p.observaciones, p.fecha_resultado,
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
        <p>Revise la ronda completa y registre resultados desde un modal por pelea, sin salir del tablero.</p>
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

<div class="card shadow-sm border-0">
    <div class="card-header panel-toolbar">
        <div>
            <div class="panel-title">Listado de peleas</div>
            <div class="panel-note">Use el boton Resultado para abrir el modal de cierre de cada pelea.</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a class="btn btn-outline-secondary btn-sm" href="peleaGenerada.php">Ver tablero</a>
        </div>
    </div>
    <div class="card-body">
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($listaPeleas)): ?>
        <div class="table-responsive">
            <table class="table table-grid table-compact align-middle" data-datatable="true">
                <thead class="table-light">
                    <tr>
                        <th style="width: 6%;">Pelea</th>
                        <th style="width: 19%;">Lado A</th>
                        <th style="width: 19%;">Lado B</th>
                        <th style="width: 12%;">Estado</th>
                        <th style="width: 16%;">Ganador</th>
                        <th style="width: 14%;">Fecha</th>
                        <th style="width: 14%;">Accion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaPeleas as $index => $pelea): ?>
                        <?php
                            $ganadorLabel = 'Pendiente';
                            if ((string) $pelea['ganador'] === (string) $pelea['galloL']) {
                                $ganadorLabel = $pelea['anilloL'] . ' - ' . $pelea['nombre_familiaL'];
                            } elseif ((string) $pelea['ganador'] === (string) $pelea['galloV']) {
                                $ganadorLabel = $pelea['anilloV'] . ' - ' . $pelea['nombre_familiaV'];
                            } elseif ($pelea['estado'] === 'anulada') {
                                $ganadorLabel = 'Sin ganador';
                            }
                        ?>
                        <tr>
                            <td><?php echo e((string) ($index + 1)); ?></td>
                            <td><?php echo e($pelea['anilloL'] . ' - ' . $pelea['nombre_familiaL']); ?></td>
                            <td><?php echo e($pelea['anilloV'] . ' - ' . $pelea['nombre_familiaV']); ?></td>
                            <td>
                                <span class="badge-soft result-pill <?php echo $pelea['estado'] === 'finalizada' ? 'accent' : ''; ?>">
                                    <?php echo e($pelea['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="result-cell">
                                    <strong><?php echo e($ganadorLabel); ?></strong>
                                    <?php if (!empty($pelea['observaciones'])): ?>
                                        <small><?php echo e(strlen((string) $pelea['observaciones']) > 56 ? substr((string) $pelea['observaciones'], 0, 56) . '...' : (string) $pelea['observaciones']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo e((string) ($pelea['fecha_resultado'] ?? '')); ?></td>
                            <td>
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm js-result-modal"
                                    data-pelea-id="<?php echo e((string) $pelea['ID_Pelea']); ?>"
                                    data-estado="<?php echo e($pelea['estado']); ?>"
                                    data-ganador="<?php echo e((string) ($pelea['ganador'] ?? '')); ?>"
                                    data-observaciones="<?php echo e((string) ($pelea['observaciones'] ?? '')); ?>"
                                    data-gallo-l="<?php echo e((string) $pelea['galloL']); ?>"
                                    data-gallo-v="<?php echo e((string) $pelea['galloV']); ?>"
                                    data-label-l="<?php echo e($pelea['anilloL'] . ' - ' . $pelea['nombre_familiaL']); ?>"
                                    data-label-v="<?php echo e($pelea['anilloV'] . ' - ' . $pelea['nombre_familiaV']); ?>"
                                >
                                    Resultado
                                </button>
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

<div
    class="app-modal<?php echo $peleaActual ? ' is-open' : ''; ?>"
    id="resultadoModal"
    aria-hidden="<?php echo $peleaActual ? 'false' : 'true'; ?>"
    data-open-on-load="<?php echo $peleaActual ? '1' : '0'; ?>"
>
    <div class="app-modal__backdrop" data-modal-close></div>
    <div class="app-modal__dialog">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <div class="app-kicker mb-2">Registro de resultado</div>
                    <h3 class="panel-title h4 mb-0" id="resultadoModalTitle">Actualizar pelea</h3>
                </div>
                <button type="button" class="app-modal__close" data-modal-close aria-label="Cerrar">
                    <span class="app-icon-glyph" aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" id="resultadoModalForm">
                <div class="modal-body">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="guardar_resultado" value="1">
                    <input type="hidden" name="txtID" id="modal_txtID" value="<?php echo e((string) ($peleaActual['ID_Pelea'] ?? 0)); ?>">

                    <div class="result-modal-meta">
                        <div class="fight-banner" id="modal_fight_banner">
                            <strong><?php echo e($peleaActual ? $peleaActual['anilloL'] . ' - ' . $peleaActual['nombre_familiaL'] : 'Seleccione una pelea'); ?></strong>
                            <span>VS</span>
                            <strong><?php echo e($peleaActual ? $peleaActual['anilloV'] . ' - ' . $peleaActual['nombre_familiaV'] : ''); ?></strong>
                        </div>

                        <div class="result-modal-grid">
                            <div>
                                <label class="form-label" for="modal_estado">Estado</label>
                                <select class="form-select" name="estado" id="modal_estado" required>
                                    <?php foreach (['pendiente', 'finalizada', 'anulada'] as $estado): ?>
                                        <option value="<?php echo e($estado); ?>" <?php echo ($peleaActual['estado'] ?? 'pendiente') === $estado ? 'selected' : ''; ?>>
                                            <?php echo e(ucfirst($estado)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="form-label" for="modal_ganador">Ganador</label>
                                <select class="form-select" name="ganador" id="modal_ganador">
                                    <option value="">Seleccione</option>
                                    <?php if ($peleaActual): ?>
                                        <option value="<?php echo e((string) $peleaActual['galloL']); ?>" <?php echo (string) ($peleaActual['ganador'] ?? '') === (string) $peleaActual['galloL'] ? 'selected' : ''; ?>>
                                            <?php echo e($peleaActual['anilloL'] . ' - ' . $peleaActual['nombre_familiaL']); ?>
                                        </option>
                                        <option value="<?php echo e((string) $peleaActual['galloV']); ?>" <?php echo (string) ($peleaActual['ganador'] ?? '') === (string) $peleaActual['galloV'] ? 'selected' : ''; ?>>
                                            <?php echo e($peleaActual['anilloV'] . ' - ' . $peleaActual['nombre_familiaV']); ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="form-label" for="modal_observaciones">Observaciones</label>
                            <textarea class="form-control" name="observaciones" id="modal_observaciones" rows="4"><?php echo e((string) ($peleaActual['observaciones'] ?? '')); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Guardar resultado</button>
                    <button class="btn btn-outline-secondary" type="button" data-modal-close>Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var estado = document.getElementById('modal_estado');
        var ganador = document.getElementById('modal_ganador');
        var txtID = document.getElementById('modal_txtID');
        var observaciones = document.getElementById('modal_observaciones');
        var banner = document.getElementById('modal_fight_banner');
        var modalElement = document.getElementById('resultadoModal');
        var triggerButtons = document.querySelectorAll('.js-result-modal');
        var closeButtons = document.querySelectorAll('[data-modal-close]');

        if (!estado || !ganador || !txtID || !observaciones || !banner || !modalElement) {
            return;
        }

        var openModal = function () {
            modalElement.classList.add('is-open');
            modalElement.setAttribute('aria-hidden', 'false');
            document.body.classList.add('app-modal-open');
        };

        var closeModal = function () {
            modalElement.classList.remove('is-open');
            modalElement.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('app-modal-open');
        };

        var syncGanador = function () {
            var habilitado = estado.value === 'finalizada';
            ganador.disabled = !habilitado;

            if (!habilitado) {
                ganador.value = '';
            }
        };

        estado.addEventListener('change', syncGanador);
        syncGanador();

        triggerButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var labelL = this.getAttribute('data-label-l') || '';
                var labelV = this.getAttribute('data-label-v') || '';
                var galloL = this.getAttribute('data-gallo-l') || '';
                var galloV = this.getAttribute('data-gallo-v') || '';

                txtID.value = this.getAttribute('data-pelea-id') || '';
                estado.value = this.getAttribute('data-estado') || 'pendiente';
                observaciones.value = this.getAttribute('data-observaciones') || '';

                ganador.innerHTML = '<option value="">Seleccione</option>'
                    + '<option value="' + galloL + '">' + labelL + '</option>'
                    + '<option value="' + galloV + '">' + labelV + '</option>';
                ganador.value = this.getAttribute('data-ganador') || '';

                banner.innerHTML = '<strong>' + labelL + '</strong><span>VS</span><strong>' + labelV + '</strong>';
                syncGanador();
                openModal();
            });
        });

        closeButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                closeModal();
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modalElement.classList.contains('is-open')) {
                closeModal();
            }
        });

        if (modalElement.getAttribute('data-open-on-load') === '1') {
            openModal();
        }
    });
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
