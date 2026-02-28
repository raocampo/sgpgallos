<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_representante'])) {
    require_csrf();

    $id = (int) $_POST['eliminar_representante'];
    $sentencia = $conexion->prepare('DELETE FROM representante WHERE ID = :id');
    $sentencia->bindValue(':id', $id, PDO::PARAM_INT);
    $sentencia->execute();

    set_flash('success', 'Representante eliminado correctamente.');
    redirect_to('secciones/representantes/');
}

$sentencia = $conexion->query('SELECT ID, nombreCompleto, localidad FROM representante ORDER BY nombreCompleto ASC');
$lista_rep = $sentencia->fetchAll();

include __DIR__ . '/../../templates/header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Representantes</span>
        <a class="btn btn-primary btn-sm" href="crear.php"><i class="fa-solid fa-plus"></i> Agregar</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle" id="tabla_id">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre completo</th>
                        <th>Localidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_rep as $registro): ?>
                        <tr>
                            <td><?php echo e((string) $registro['ID']); ?></td>
                            <td><?php echo e($registro['nombreCompleto']); ?></td>
                            <td><?php echo e($registro['localidad']); ?></td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-outline-primary btn-sm" href="editar.php?txtID=<?php echo urlencode((string) $registro['ID']); ?>">Editar</a>
                                <?php echo render_delete_button('eliminar_representante', (int) $registro['ID']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>

