<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();

$valores = [
    'nombre' => '',
    'correo' => '',
    'usuario' => '',
    'dependencia' => '',
];

$error = '';
$clave = '';
$claveConfirmacion = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $valores['nombre'] = post('nombre');
    $valores['correo'] = post('correo');
    $valores['usuario'] = post('usuario');
    $valores['dependencia'] = post('dependencia');
    $clave = post('clave');
    $claveConfirmacion = post('clave_confirmacion');

    if (in_array('', [$valores['nombre'], $valores['correo'], $valores['usuario'], $clave], true)) {
        $error = 'Complete los campos obligatorios.';
    } elseif (!filter_var($valores['correo'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Ingrese un correo valido.';
    } elseif (strlen($clave) < 8) {
        $error = 'La contrasena debe tener al menos 8 caracteres.';
    } elseif ($clave !== $claveConfirmacion) {
        $error = 'La confirmacion de contrasena no coincide.';
    } else {
        $verifica = $conexion->prepare('SELECT COUNT(*) FROM usuarios WHERE apodo = :usuario');
        $verifica->bindValue(':usuario', $valores['usuario']);
        $verifica->execute();

        if ((int) $verifica->fetchColumn() > 0) {
            $error = 'El usuario ya existe.';
        } else {
            $sentencia = $conexion->prepare('INSERT INTO usuarios (nombre, correo, apodo, clave, empresa) VALUES (:nombre, :correo, :usuario, :clave, :empresa)');
            $sentencia->bindValue(':nombre', $valores['nombre']);
            $sentencia->bindValue(':correo', $valores['correo']);
            $sentencia->bindValue(':usuario', $valores['usuario']);
            $sentencia->bindValue(':clave', hash_user_password($clave));
            $sentencia->bindValue(':empresa', $valores['dependencia']);
            $sentencia->execute();

            set_flash('success', 'Usuario creado correctamente.');
            redirect_to('secciones/usuarios/');
        }
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header">Crear usuario</div>
    <div class="card-body">
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3">
            <?php echo csrf_input(); ?>

            <div class="col-md-6">
                <label class="form-label" for="nombre">Nombres y apellidos</label>
                <input class="form-control" type="text" name="nombre" id="nombre" value="<?php echo e($valores['nombre']); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="correo">Correo</label>
                <input class="form-control" type="email" name="correo" id="correo" value="<?php echo e($valores['correo']); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="usuario">Usuario</label>
                <input class="form-control" type="text" name="usuario" id="usuario" value="<?php echo e($valores['usuario']); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="clave">Contrasena</label>
                <input class="form-control" type="password" name="clave" id="clave" required>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="clave_confirmacion">Confirmar contrasena</label>
                <input class="form-control" type="password" name="clave_confirmacion" id="clave_confirmacion" required>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="dependencia">Dependencia</label>
                <input class="form-control" type="text" name="dependencia" id="dependencia" value="<?php echo e($valores['dependencia']); ?>">
            </div>

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-success" type="submit">Guardar</button>
                <a class="btn btn-outline-secondary" href="index.php">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>

