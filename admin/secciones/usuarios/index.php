<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_usuario'])) {
    require_csrf();

    $id = (int) $_POST['eliminar_usuario'];

    if (!empty($_SESSION['user_id']) && $id === (int) $_SESSION['user_id']) {
        set_flash('warning', 'No puede eliminar su propio usuario mientras tiene la sesion iniciada.');
    } else {
        $sentencia = $conexion->prepare('DELETE FROM usuarios WHERE ID = :id');
        $sentencia->bindValue(':id', $id, PDO::PARAM_INT);
        $sentencia->execute();
        set_flash('success', 'Usuario eliminado correctamente.');
    }

    redirect_to('secciones/usuarios/');
}

$sentencia = $conexion->query('SELECT ID, nombre, correo, apodo, empresa FROM usuarios ORDER BY nombre ASC');
$lista_usuarios = $sentencia->fetchAll();

include __DIR__ . '/../../templates/header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Usuarios</span>
        <a class="btn btn-primary btn-sm" href="crear.php"><i class="fa-solid fa-plus"></i> Agregar</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle" id="tabla_id">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Usuario</th>
                        <th>Dependencia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_usuarios as $registro): ?>
                        <tr>
                            <td><?php echo e((string) $registro['ID']); ?></td>
                            <td><?php echo e($registro['nombre']); ?></td>
                            <td><?php echo e($registro['correo']); ?></td>
                            <td><?php echo e($registro['apodo']); ?></td>
                            <td><?php echo e($registro['empresa']); ?></td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-outline-primary btn-sm" href="editar.php?txtID=<?php echo urlencode((string) $registro['ID']); ?>">Editar</a>
                                <?php echo render_delete_button('eliminar_usuario', (int) $registro['ID']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>

