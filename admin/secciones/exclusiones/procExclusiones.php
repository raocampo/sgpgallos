<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();
start_secure_session();
$context = require_tournament_context('Seleccione un torneo antes de continuar.');
$torneoId = $context['torneoId'];
$nombreTorneo = $context['nombreTorneo'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_exclusion'])) {
    require_csrf();

    $id = (int) $_POST['eliminar_exclusion'];
    $elimina = $conexion->prepare('DELETE FROM exclusiones WHERE IdExclusion = :id AND torneoId = :torneoId');
    $elimina->bindValue(':id', $id, PDO::PARAM_INT);
    $elimina->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
    $elimina->execute();

    set_flash('success', 'Exclusion eliminada correctamente.');
    redirect_to('secciones/exclusiones/procExclusiones.php?nombreTorneo=' . urlencode($nombreTorneo) . '&torneoId=' . $torneoId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['familia1'], $_POST['familia2'])) {
    require_csrf();

    $familia1 = (int) $_POST['familia1'];
    $familia2 = (int) $_POST['familia2'];

    if ($familia1 <= 0 || $familia2 <= 0) {
        set_flash('danger', 'Seleccione dos criaderos validos.');
    } elseif ($familia1 === $familia2) {
        set_flash('danger', 'No puede excluir un criadero contra si mismo.');
    } else {
        $verifica = $conexion->prepare('
            SELECT COUNT(*)
            FROM exclusiones
            WHERE torneoId = :torneoId
              AND (
                    (nombreFamiliaUno = :familia1_a AND nombreFamiliaDos = :familia2_a)
                 OR (nombreFamiliaUno = :familia2_b AND nombreFamiliaDos = :familia1_b)
              )
        ');
        $verifica->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
        $verifica->bindValue(':familia1_a', $familia1, PDO::PARAM_INT);
        $verifica->bindValue(':familia2_a', $familia2, PDO::PARAM_INT);
        $verifica->bindValue(':familia2_b', $familia2, PDO::PARAM_INT);
        $verifica->bindValue(':familia1_b', $familia1, PDO::PARAM_INT);
        $verifica->execute();

        if ((int) $verifica->fetchColumn() > 0) {
            set_flash('warning', 'Esa exclusion ya existe para el torneo actual.');
        } else {
            $inserta = $conexion->prepare('INSERT INTO exclusiones (nombreFamiliaUno, nombreFamiliaDos, torneoId) VALUES (:familia1, :familia2, :torneoId)');
            $inserta->bindValue(':familia1', $familia1, PDO::PARAM_INT);
            $inserta->bindValue(':familia2', $familia2, PDO::PARAM_INT);
            $inserta->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
            $inserta->execute();

            set_flash('success', 'Exclusion registrada correctamente.');
        }
    }

    redirect_to('secciones/exclusiones/procExclusiones.php?nombreTorneo=' . urlencode($nombreTorneo) . '&torneoId=' . $torneoId);
}

$sentenciaExclusiones = $conexion->prepare('
    SELECT exclusiones.IdExclusion, familia1.nombre AS nombreFamiliaUno, familia2.nombre AS nombreFamiliaDos
    FROM exclusiones
    INNER JOIN familias AS familia1 ON exclusiones.nombreFamiliaUno = familia1.codigo
    INNER JOIN familias AS familia2 ON exclusiones.nombreFamiliaDos = familia2.codigo
    WHERE exclusiones.torneoId = :torneoId
    ORDER BY familia1.nombre ASC, familia2.nombre ASC
');
$sentenciaExclusiones->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
$sentenciaExclusiones->execute();
$listaExclusiones = $sentenciaExclusiones->fetchAll();

include __DIR__ . '/../../templates/header.sub.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Exclusiones activas</span>
        <a class="btn btn-outline-primary btn-sm" href="index.php">Nueva exclusion</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle" id="tabla_id">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Criadero 1</th>
                        <th></th>
                        <th>Criadero 2</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaExclusiones as $index => $exclusion): ?>
                        <tr>
                            <td><?php echo e((string) ($index + 1)); ?></td>
                            <td><?php echo e($exclusion['nombreFamiliaUno']); ?></td>
                            <td>excluido con</td>
                            <td><?php echo e($exclusion['nombreFamiliaDos']); ?></td>
                            <td><?php echo render_delete_button('eliminar_exclusion', (int) $exclusion['IdExclusion']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
