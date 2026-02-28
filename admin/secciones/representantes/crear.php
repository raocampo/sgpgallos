<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();

$valores = [
    'nombreCompleto' => '',
    'localidad' => '',
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $valores['nombreCompleto'] = post('nombreCompleto');
    $valores['localidad'] = post('localidad');

    if ($valores['nombreCompleto'] === '') {
        $error = 'Ingrese el nombre del representante.';
    } else {
        $sentencia = $conexion->prepare('INSERT INTO representante (nombreCompleto, localidad) VALUES (:nombre, :localidad)');
        $sentencia->bindValue(':nombre', $valores['nombreCompleto']);
        $sentencia->bindValue(':localidad', $valores['localidad']);
        $sentencia->execute();

        set_flash('success', 'Representante creado correctamente.');
        redirect_to('secciones/representantes/');
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header">Crear representante</div>
    <div class="card-body">
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3">
            <?php echo csrf_input(); ?>

            <div class="col-md-8">
                <label class="form-label" for="nombreCompleto">Nombres y apellidos</label>
                <input class="form-control" type="text" name="nombreCompleto" id="nombreCompleto" value="<?php echo e($valores['nombreCompleto']); ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label" for="localidad">Localidad</label>
                <input class="form-control" type="text" name="localidad" id="localidad" value="<?php echo e($valores['localidad']); ?>">
            </div>

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-success" type="submit">Guardar</button>
                <a class="btn btn-outline-secondary" href="index.php">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>

