<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();

$txtID = isset($_GET['txtID']) ? (int) $_GET['txtID'] : 0;
$tipos = ['Nacional', 'Provincial', 'Local', 'Abierto', 'Prueba'];

if ($txtID <= 0) {
    set_flash('warning', 'Torneo no valido.');
    redirect_to('secciones/torneos/');
}

$consulta = $conexion->prepare('SELECT * FROM torneos WHERE ID = :id');
$consulta->bindValue(':id', $txtID, PDO::PARAM_INT);
$consulta->execute();
$registro = $consulta->fetch();

if (!$registro) {
    set_flash('warning', 'Torneo no encontrado.');
    redirect_to('secciones/torneos/');
}

$valores = [
    'nombre' => $registro['nombre'],
    'fechaInicio' => $registro['fecha_inicio'],
    'fechaFin' => $registro['fecha_fin'],
    'tipoTorneo' => $registro['tipoTorneo'],
];

$error = '';

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
        $actualiza = $conexion->prepare('UPDATE torneos SET nombre = :nombre, fecha_inicio = :inicio, fecha_fin = :fin, tipoTorneo = :tipo WHERE ID = :id');
        $actualiza->bindValue(':nombre', $valores['nombre']);
        $actualiza->bindValue(':inicio', $valores['fechaInicio']);
        $actualiza->bindValue(':fin', $valores['fechaFin'] !== '' ? $valores['fechaFin'] : $valores['fechaInicio']);
        $actualiza->bindValue(':tipo', $valores['tipoTorneo']);
        $actualiza->bindValue(':id', $txtID, PDO::PARAM_INT);
        $actualiza->execute();

        set_flash('success', 'Torneo actualizado correctamente.');
        redirect_to('secciones/torneos/');
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header">Editar torneo</div>
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

            <div class="col-md-6">
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
                <button class="btn btn-success" type="submit">Actualizar</button>
                <a class="btn btn-outline-secondary" href="index.php">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>

