<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();

$representantes = $conexion->query('SELECT ID, nombreCompleto FROM representante ORDER BY nombreCompleto ASC')->fetchAll();

$valores = [
    'nombre' => '',
    'localidad' => '',
    'representanteId' => '',
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $valores['nombre'] = post('nombre');
    $valores['localidad'] = post('localidad');
    $valores['representanteId'] = post('representanteId');

    if ($valores['nombre'] === '' || $valores['representanteId'] === '') {
        $error = 'Complete los campos obligatorios.';
    } else {
        $sentencia = $conexion->prepare('INSERT INTO familias (nombre, localidad, representanteId) VALUES (:nombre, :localidad, :representante)');
        $sentencia->bindValue(':nombre', $valores['nombre']);
        $sentencia->bindValue(':localidad', $valores['localidad']);
        $sentencia->bindValue(':representante', (int) $valores['representanteId'], PDO::PARAM_INT);
        $sentencia->execute();

        set_flash('success', 'Criadero creado correctamente.');
        redirect_to('secciones/familias/');
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header">Crear criadero</div>
    <div class="card-body">
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3">
            <?php echo csrf_input(); ?>

            <div class="col-md-6">
                <label class="form-label" for="nombre">Nombre</label>
                <input class="form-control" type="text" name="nombre" id="nombre" value="<?php echo e($valores['nombre']); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="representanteId">Representante</label>
                <select class="form-select" name="representanteId" id="representanteId" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($representantes as $representante): ?>
                        <option value="<?php echo e((string) $representante['ID']); ?>" <?php echo $valores['representanteId'] === (string) $representante['ID'] ? 'selected' : ''; ?>>
                            <?php echo e($representante['nombreCompleto']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
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

