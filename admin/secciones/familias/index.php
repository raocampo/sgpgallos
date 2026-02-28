<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_familia'])) {
    require_csrf();

    $codigo = (int) $_POST['eliminar_familia'];
    $sentencia = $conexion->prepare('DELETE FROM familias WHERE codigo = :codigo');
    $sentencia->bindValue(':codigo', $codigo, PDO::PARAM_INT);
    $sentencia->execute();

    set_flash('success', 'Criadero eliminado correctamente.');
    redirect_to('secciones/familias/');
}

$consulta = $conexion->query('
    SELECT familias.codigo, familias.nombre, familias.localidad, representante.nombreCompleto
    FROM familias
    INNER JOIN representante ON familias.representanteId = representante.ID
    ORDER BY familias.nombre ASC
');
$lista_fam = $consulta->fetchAll();

include __DIR__ . '/../../templates/header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Criaderos</span>
        <a class="btn btn-primary btn-sm" href="crear.php"><i class="fa-solid fa-plus"></i> Agregar</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle" id="tabla_id">
                <thead class="table-light">
                    <tr>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Representante</th>
                        <th>Localidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_fam as $registro): ?>
                        <tr>
                            <td><?php echo e((string) $registro['codigo']); ?></td>
                            <td><?php echo e($registro['nombre']); ?></td>
                            <td><?php echo e($registro['nombreCompleto']); ?></td>
                            <td><?php echo e($registro['localidad']); ?></td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-outline-primary btn-sm" href="modificar.php?txtID=<?php echo urlencode((string) $registro['codigo']); ?>">Editar</a>
                                <?php echo render_delete_button('eliminar_familia', (int) $registro['codigo']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>

