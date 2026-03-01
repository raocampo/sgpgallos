<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();
start_secure_session();
$context = require_tournament_context('Seleccione un torneo antes de gestionar peleas.');
$torneoId = $context['torneoId'];
$nombreTorneo = $context['nombreTorneo'];
$redirectPeleas = 'secciones/peleas/peleaGenerada.php?nombreTorneo=' . urlencode($nombreTorneo) . '&torneoId=' . $torneoId;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['peleaGenerada'])) {
    require_csrf();
    ensure_open_tournament_or_redirect($conexion, $torneoId, $redirectPeleas);

    $peleasSeleccionadas = $_POST['peleas'] ?? [];

    if (empty($peleasSeleccionadas)) {
        set_flash('warning', 'No se seleccionaron cotejas para pactar.');
    } else {
        $insertadas = 0;
        $omitidasDuplicadas = 0;
        $omitidasDisponibilidad = 0;

        foreach ($peleasSeleccionadas as $pelea) {
            $galloL = null;
            $galloV = null;

            if (strpos($pelea, '-') !== false) {
                [$galloL, $galloV] = array_map('intval', explode('-', $pelea, 2));
            } else {
                $idCoteja = (int) $pelea;
                $sentenciaCoteja = $conexion->prepare('SELECT galloL, galloV FROM coteja WHERE ID_Coteja = :id AND torneoId = :torneoId');
                $sentenciaCoteja->bindValue(':id', $idCoteja, PDO::PARAM_INT);
                $sentenciaCoteja->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
                $sentenciaCoteja->execute();
                $coteja = $sentenciaCoteja->fetch();

                if ($coteja) {
                    $galloL = (int) $coteja['galloL'];
                    $galloV = (int) $coteja['galloV'];
                }
            }

            if (!$galloL || !$galloV || $galloL === $galloV) {
                continue;
            }

            $verifica = $conexion->prepare('
                SELECT COUNT(*)
                FROM peleas
                WHERE torneoId = :torneoId
                  AND (
                        (galloL = :galloL_a AND galloV = :galloV_a)
                     OR (galloL = :galloV_b AND galloV = :galloL_b)
                  )
            ');
            $verifica->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
            $verifica->bindValue(':galloL_a', (string) $galloL);
            $verifica->bindValue(':galloV_a', (string) $galloV);
            $verifica->bindValue(':galloV_b', (string) $galloV);
            $verifica->bindValue(':galloL_b', (string) $galloL);
            $verifica->execute();

            if ((int) $verifica->fetchColumn() > 0) {
                $omitidasDuplicadas++;
                continue;
            }

            $verificaDisponibilidad = $conexion->prepare('
                SELECT COUNT(*)
                FROM peleas
                WHERE torneoId = :torneoId
                  AND estado <> :estadoAnulada
                  AND (
                        galloL = :galloL_a
                     OR galloV = :galloL_b
                     OR galloL = :galloV_a
                     OR galloV = :galloV_b
                  )
            ');
            $verificaDisponibilidad->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
            $verificaDisponibilidad->bindValue(':estadoAnulada', 'anulada');
            $verificaDisponibilidad->bindValue(':galloL_a', (string) $galloL);
            $verificaDisponibilidad->bindValue(':galloL_b', (string) $galloL);
            $verificaDisponibilidad->bindValue(':galloV_a', (string) $galloV);
            $verificaDisponibilidad->bindValue(':galloV_b', (string) $galloV);
            $verificaDisponibilidad->execute();

            if ((int) $verificaDisponibilidad->fetchColumn() > 0) {
                $omitidasDisponibilidad++;
                continue;
            }

            $inserta = $conexion->prepare('INSERT INTO peleas (galloL, galloV, torneoId, estado) VALUES (:galloL, :galloV, :torneoId, :estado)');
            $inserta->bindValue(':galloL', (string) $galloL);
            $inserta->bindValue(':galloV', (string) $galloV);
            $inserta->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
            $inserta->bindValue(':estado', 'pendiente');
            $inserta->execute();
            $insertadas++;
        }

        if ($insertadas > 0) {
            $mensaje = 'Peleas pactadas correctamente.';
            if ($omitidasDuplicadas > 0 || $omitidasDisponibilidad > 0) {
                $mensaje .= ' Omitidas: ' . $omitidasDuplicadas . ' repetidas y ' . $omitidasDisponibilidad . ' por gallos ya comprometidos.';
            }
            set_flash('success', $mensaje);
        } else {
            set_flash('warning', 'No se agregaron peleas nuevas. Revise duplicados o gallos ya comprometidos.');
        }
    }

    redirect_to($redirectPeleas);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_pelea'])) {
    require_csrf();
    ensure_open_tournament_or_redirect($conexion, $torneoId, $redirectPeleas);

    $id = (int) $_POST['eliminar_pelea'];
    $sentencia = $conexion->prepare('DELETE FROM peleas WHERE ID_Pelea = :id AND torneoId = :torneoId');
    $sentencia->bindValue(':id', $id, PDO::PARAM_INT);
    $sentencia->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
    $sentencia->execute();

    set_flash('success', 'Pelea liberada correctamente.');
    redirect_to($redirectPeleas);
}

$sentencia = $conexion->prepare('
    SELECT p.ID_Pelea, p.galloL, p.galloV, p.estado, p.ganador, p.observaciones,
           gl.anillo AS anilloL, gl.frente AS frenteL, gl.pesoReal AS pesoRealL, gl.tamañoReal AS tamañoRealL, fl.nombre AS nombre_familiaL,
           gv.anillo AS anilloV, gv.frente AS frenteV, gv.pesoReal AS pesoRealV, gv.tamañoReal AS tamañoRealV, fv.nombre AS nombre_familiaV
    FROM peleas p
    INNER JOIN gallos gl ON p.galloL = gl.ID
    INNER JOIN gallos gv ON p.galloV = gv.ID
    INNER JOIN familias fl ON gl.familiasId = fl.codigo
    INNER JOIN familias fv ON gv.familiasId = fv.codigo
    WHERE p.torneoId = :torneoId
    ORDER BY p.ID_Pelea ASC
');
$sentencia->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
$sentencia->execute();
$resultado = $sentencia->fetchAll();

$pendientes = 0;
$finalizadas = 0;
$anuladas = 0;

foreach ($resultado as $pelea) {
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
        <span class="app-kicker">Ronda vigente</span>
        <h2 class="page-title mb-2">Peleas pactadas</h2>
        <p>Revise los cruces promovidos desde cotejas, libere peleas si hace falta y avance al registro oficial de resultados.</p>
    </div>
    <div class="badge-soft">Total: <?php echo e((string) count($resultado)); ?> peleas</div>
</div>

<div class="stats-grid mb-4">
    <div class="card stat-card">
        <div class="stat-label">Total pactadas</div>
        <div class="stat-value"><?php echo e((string) count($resultado)); ?></div>
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
            <div class="panel-title">Tablero de peleas</div>
            <div class="panel-note">Solo se listan peleas del torneo activo.</div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary btn-sm" href="resultados.php">Resultados</a>
            <a class="btn btn-outline-secondary btn-sm" href="reportePelea.php" target="_blank">Imprimir</a>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($resultado)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-grid table-compact align-middle" data-datatable="true">
                <thead class="table-light">
                    <tr>
                        <th>Pelea</th>
                        <th>Anillo L</th>
                        <th>Criadero L</th>
                        <th>Peso L</th>
                        <th>Anillo V</th>
                        <th>Criadero V</th>
                        <th>Peso V</th>
                        <th>Estado</th>
                        <th>Ganador</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultado as $index => $pelea): ?>
                        <tr>
                            <td><?php echo e((string) ($index + 1)); ?></td>
                            <td><?php echo e($pelea['anilloL']); ?></td>
                            <td><?php echo e($pelea['nombre_familiaL']); ?></td>
                            <td><?php echo e((string) $pelea['pesoRealL']); ?></td>
                            <td><?php echo e($pelea['anilloV']); ?></td>
                            <td><?php echo e($pelea['nombre_familiaV']); ?></td>
                            <td><?php echo e((string) $pelea['pesoRealV']); ?></td>
                            <td>
                                <span class="badge-soft <?php echo $pelea['estado'] === 'finalizada' ? 'accent' : ''; ?>">
                                    <?php echo e($pelea['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                    $ganador = '';
                                    if ((string) $pelea['ganador'] === (string) $pelea['galloL']) {
                                        $ganador = $pelea['anilloL'] . ' - ' . $pelea['nombre_familiaL'];
                                    } elseif ((string) $pelea['ganador'] === (string) $pelea['galloV']) {
                                        $ganador = $pelea['anilloV'] . ' - ' . $pelea['nombre_familiaV'];
                                    }
                                    echo e($ganador);
                                ?>
                            </td>
                            <td class="d-flex gap-2 flex-wrap">
                                <a class="btn btn-outline-success btn-sm" href="resultados.php?txtID=<?php echo urlencode((string) $pelea['ID_Pelea']); ?>">Registrar resultado</a>
                                <?php echo render_delete_button('eliminar_pelea', (int) $pelea['ID_Pelea'], 'Liberar', 'btn btn-danger btn-sm'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="empty-panel">
                <h3>No hay peleas pactadas</h3>
                <p>Seleccione cotejas desde el motor de cruces para empezar a registrar resultados del torneo.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
