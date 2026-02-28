<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();

$valores = [
    'nombre' => '',
    'fechaInicio' => '',
    'fechaFin' => '',
    'tipoTorneo' => '',
];

$error = '';
$tipos = ['Nacional', 'Provincial', 'Local', 'Abierto', 'Prueba'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $valores['nombre'] = post('nombre');
    $valores['fechaInicio'] = post('fechaInicio');
    $valores['fechaFin'] = post('fechaFin');
    $valores['tipoTorneo'] = post('tipoTorneo');

    if ($valores['nombre'] === '' || $valores['fechaInicio'] === '' || $valores['tipoTorneo'] === '') {
        $error = 'Complete los campos obligatorios.';
    } elseif ($valores['fechaFin'] !== '' && $valores['fechaFin'] < $valores['fechaInicio']) {
        $error = 'La fecha de cierre no puede ser menor a la fecha de inicio.';
    } else {
        $sentencia = $conexion->prepare('INSERT INTO torneos (nombre, fecha_inicio, fecha_fin, tipoTorneo) VALUES (:nombre, :fecha_inicio, :fecha_fin, :tipo)');
        $sentencia->bindValue(':nombre', $valores['nombre']);
        $sentencia->bindValue(':fecha_inicio', $valores['fechaInicio']);
        $sentencia->bindValue(':fecha_fin', $valores['fechaFin'] !== '' ? $valores['fechaFin'] : $valores['fechaInicio']);
        $sentencia->bindValue(':tipo', $valores['tipoTorneo']);
        $sentencia->execute();

        set_flash('success', 'Torneo creado correctamente.');
        redirect_to('secciones/torneos/');
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header">Crear torneo</div>
    <div class="card-body">
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3">
            <?php echo csrf_input(); ?>

            <div class="col-md-8">
                <label class="form-label" for="nombre">Nombre</label>
                <input class="form-control" type="text" name="nombre" id="nombre" value="<?php echo e($valores['nombre']); ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label" for="tipoTorneo">Tipo de torneo</label>
                <select class="form-select" name="tipoTorneo" id="tipoTorneo" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($tipos as $tipo): ?>
                        <option value="<?php echo e($tipo); ?>" <?php echo $valores['tipoTorneo'] === $tipo ? 'selected' : ''; ?>><?php echo e($tipo); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="fechaInicio">Fecha inicio</label>
                <input class="form-control" type="date" name="fechaInicio" id="fechaInicio" value="<?php echo e($valores['fechaInicio']); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="fechaFin">Fecha fin</label>
                <input class="form-control" type="date" name="fechaFin" id="fechaFin" value="<?php echo e($valores['fechaFin']); ?>">
            </div>

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-success" type="submit">Guardar</button>
                <a class="btn btn-outline-secondary" href="index.php">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>

