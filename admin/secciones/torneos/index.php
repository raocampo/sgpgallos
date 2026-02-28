<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_torneo'])) {
    require_csrf();

    $id = (int) $_POST['eliminar_torneo'];
    $sentencia = $conexion->prepare('DELETE FROM torneos WHERE ID = :id');
    $sentencia->bindValue(':id', $id, PDO::PARAM_INT);
    $sentencia->execute();

    if (isset($_SESSION['torneoId']) && (int) $_SESSION['torneoId'] === $id) {
        unset($_SESSION['torneoId'], $_SESSION['nombreTorneo']);
    }

    set_flash('success', 'Torneo eliminado correctamente.');
    redirect_to('secciones/torneos/');
}

$sentencia = $conexion->query('SELECT ID, nombre, fecha_inicio, fecha_fin, tipoTorneo FROM torneos ORDER BY fecha_inicio DESC, ID DESC');
$lista_torn = $sentencia->fetchAll();

$tiposTorneo = array_unique(array_column($lista_torn, 'tipoTorneo'));
$torneoActivo = $_SESSION['nombreTorneo'] ?? '';

include __DIR__ . '/../../templates/header.php';
?>

<div class="page-intro">
    <div>
        <span class="app-kicker">Configuracion base</span>
        <h2 class="page-title mb-2">Torneos</h2>
        <p>Abra, edite y mantenga los torneos listos para cargar gallos, aplicar exclusiones y gestionar la competencia completa.</p>
    </div>
    <a class="btn btn-primary btn-sm" href="crear.php"><i class="fa-solid fa-plus"></i> Agregar torneo</a>
</div>

<div class="stats-grid mb-4">
    <div class="card stat-card">
        <div class="stat-label">Total torneos</div>
        <div class="stat-value"><?php echo e((string) count($lista_torn)); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Tipos</div>
        <div class="stat-value"><?php echo e((string) count($tiposTorneo)); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Torneo activo</div>
        <div class="stat-value stat-value-sm"><?php echo e($torneoActivo !== '' ? $torneoActivo : 'Ninguno'); ?></div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header panel-toolbar">
        <div>
            <div class="panel-title">Listado de torneos</div>
            <div class="panel-note">Abra un torneo para continuar con la carga de datos y el flujo competitivo.</div>
        </div>
        <a class="btn btn-primary btn-sm" href="crear.php"><i class="fa-solid fa-plus"></i> Agregar</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle" data-datatable="true">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha inicio</th>
                        <th>Fecha fin</th>
                        <th>Tipo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_torn as $registro): ?>
                        <tr>
                            <td><?php echo e((string) $registro['ID']); ?></td>
                            <td><?php echo e($registro['nombre']); ?></td>
                            <td><?php echo e($registro['fecha_inicio']); ?></td>
                            <td><?php echo e($registro['fecha_fin']); ?></td>
                            <td><?php echo e($registro['tipoTorneo']); ?></td>
                            <td class="d-flex gap-2 flex-wrap">
                                <a class="btn btn-outline-primary btn-sm" href="editar.php?txtID=<?php echo urlencode((string) $registro['ID']); ?>">Editar</a>
                                <a class="btn btn-outline-success btn-sm" href="<?php echo e(admin_url('secciones/gallos/')); ?>?nombreTorneo=<?php echo urlencode($registro['nombre']); ?>&torneoId=<?php echo urlencode((string) $registro['ID']); ?>">Abrir</a>
                                <?php echo render_delete_button('eliminar_torneo', (int) $registro['ID']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
