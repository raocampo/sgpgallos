<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();

$txtID = isset($_GET['txtID']) ? (int) $_GET['txtID'] : 0;

if ($txtID <= 0) {
    set_flash('warning', 'Usuario no valido.');
    redirect_to('secciones/usuarios/');
}

$sentencia = $conexion->prepare('SELECT ID, nombre, correo, apodo, empresa FROM usuarios WHERE ID = :id');
$sentencia->bindValue(':id', $txtID, PDO::PARAM_INT);
$sentencia->execute();
$registro = $sentencia->fetch();

if (!$registro) {
    set_flash('warning', 'Usuario no encontrado.');
    redirect_to('secciones/usuarios/');
}

$valores = [
    'nombre' => $registro['nombre'],
    'correo' => $registro['correo'],
    'usuario' => $registro['apodo'],
    'dependencia' => $registro['empresa'],
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $valores['nombre'] = post('nombre');
    $valores['correo'] = post('correo');
    $valores['usuario'] = post('usuario');
    $valores['dependencia'] = post('dependencia');
    $clave = post('clave');

    if (in_array('', [$valores['nombre'], $valores['correo'], $valores['usuario']], true)) {
        $error = 'Complete los campos obligatorios.';
    } elseif (!filter_var($valores['correo'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Ingrese un correo valido.';
    } else {
        $verifica = $conexion->prepare('SELECT COUNT(*) FROM usuarios WHERE apodo = :usuario AND ID <> :id');
        $verifica->bindValue(':usuario', $valores['usuario']);
        $verifica->bindValue(':id', $txtID, PDO::PARAM_INT);
        $verifica->execute();

        if ((int) $verifica->fetchColumn() > 0) {
            $error = 'El nombre de usuario ya esta en uso.';
        } else {
            $sql = 'UPDATE usuarios SET nombre = :nombre, correo = :correo, apodo = :usuario, empresa = :empresa';

            if ($clave !== '') {
                $sql .= ', clave = :clave';
            }

            $sql .= ' WHERE ID = :id';

            $actualiza = $conexion->prepare($sql);
            $actualiza->bindValue(':nombre', $valores['nombre']);
            $actualiza->bindValue(':correo', $valores['correo']);
            $actualiza->bindValue(':usuario', $valores['usuario']);
            $actualiza->bindValue(':empresa', $valores['dependencia']);
            $actualiza->bindValue(':id', $txtID, PDO::PARAM_INT);

            if ($clave !== '') {
                $actualiza->bindValue(':clave', password_hash($clave, PASSWORD_DEFAULT));
            }

            $actualiza->execute();

            set_flash('success', 'Usuario actualizado correctamente.');
            redirect_to('secciones/usuarios/');
        }
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header">Editar usuario</div>
    <div class="card-body">
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3">
            <?php echo csrf_input(); ?>

            <div class="col-md-2">
                <label class="form-label" for="txtID">ID</label>
                <input class="form-control" type="text" id="txtID" value="<?php echo e((string) $txtID); ?>" readonly>
            </div>

            <div class="col-md-5">
                <label class="form-label" for="nombre">Nombres y apellidos</label>
                <input class="form-control" type="text" name="nombre" id="nombre" value="<?php echo e($valores['nombre']); ?>" required>
            </div>

            <div class="col-md-5">
                <label class="form-label" for="correo">Correo</label>
                <input class="form-control" type="email" name="correo" id="correo" value="<?php echo e($valores['correo']); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="usuario">Usuario</label>
                <input class="form-control" type="text" name="usuario" id="usuario" value="<?php echo e($valores['usuario']); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="clave">Nueva contrasena</label>
                <input class="form-control" type="password" name="clave" id="clave" placeholder="Dejar vacio para conservar la actual">
            </div>

            <div class="col-md-6">
                <label class="form-label" for="dependencia">Dependencia</label>
                <input class="form-control" type="text" name="dependencia" id="dependencia" value="<?php echo e($valores['dependencia']); ?>">
            </div>

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-success" type="submit">Actualizar</button>
                <a class="btn btn-outline-secondary" href="index.php">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>

