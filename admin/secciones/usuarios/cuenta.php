<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();

$usuarioId = current_user_id();
$consulta = $conexion->prepare('SELECT ID, nombre, correo, apodo, empresa, clave FROM usuarios WHERE ID = :id LIMIT 1');
$consulta->bindValue(':id', $usuarioId, PDO::PARAM_INT);
$consulta->execute();
$usuario = $consulta->fetch();

if (!$usuario) {
    logout_user();
    set_flash('danger', 'No fue posible cargar la cuenta actual.');
    redirect_to('login.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $claveActual = post('clave_actual');
    $claveNueva = post('clave_nueva');
    $claveConfirmacion = post('clave_confirmacion');

    if ($claveActual === '' || $claveNueva === '' || $claveConfirmacion === '') {
        $error = 'Complete todos los campos de seguridad.';
    } elseif (!verify_stored_password($claveActual, (string) $usuario['clave'])) {
        $error = 'La contrasena actual no coincide.';
    } elseif (strlen($claveNueva) < 8) {
        $error = 'La nueva contrasena debe tener al menos 8 caracteres.';
    } elseif ($claveNueva !== $claveConfirmacion) {
        $error = 'La confirmacion no coincide con la nueva contrasena.';
    } elseif (hash_equals($claveActual, $claveNueva)) {
        $error = 'La nueva contrasena debe ser diferente a la actual.';
    } else {
        $actualiza = $conexion->prepare('UPDATE usuarios SET clave = :clave WHERE ID = :id');
        $actualiza->bindValue(':clave', hash_user_password($claveNueva));
        $actualiza->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $actualiza->execute();

        set_flash('success', 'Contrasena actualizada correctamente.');
        redirect_to('secciones/usuarios/cuenta.php');
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header">Cuenta actual</div>
            <div class="card-body">
                <div class="soft-panel">
                    <strong>Nombre:</strong> <?php echo e($usuario['nombre']); ?><br>
                    <strong>Usuario:</strong> <?php echo e($usuario['apodo']); ?><br>
                    <strong>Correo:</strong> <?php echo e($usuario['correo']); ?><br>
                    <strong>Dependencia:</strong> <?php echo e($usuario['empresa']); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header panel-toolbar">
                <div>
                    <div class="panel-title">Cambiar contrasena</div>
                    <div class="panel-note">Use una clave nueva de al menos 8 caracteres para proteger el acceso administrativo.</div>
                </div>
            </div>
            <div class="card-body">
                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger"><?php echo e($error); ?></div>
                <?php endif; ?>

                <form method="post" class="row g-3">
                    <?php echo csrf_input(); ?>

                    <div class="col-md-12">
                        <label class="form-label" for="clave_actual">Contrasena actual</label>
                        <input class="form-control" type="password" name="clave_actual" id="clave_actual" required autocomplete="current-password">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="clave_nueva">Nueva contrasena</label>
                        <input class="form-control" type="password" name="clave_nueva" id="clave_nueva" required autocomplete="new-password">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="clave_confirmacion">Confirmar contrasena</label>
                        <input class="form-control" type="password" name="clave_confirmacion" id="clave_confirmacion" required autocomplete="new-password">
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-success" type="submit">Actualizar clave</button>
                        <a class="btn btn-outline-secondary" href="<?php echo e(admin_url('secciones/usuarios/')); ?>">Volver a usuarios</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
