<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();

$txtID = isset($_GET['txtID']) ? (int) $_GET['txtID'] : 0;

if ($txtID <= 0) {
    set_flash('warning', 'Representante no valido.');
    redirect_to('secciones/representantes/');
}

$consulta = $conexion->prepare('SELECT * FROM representante WHERE ID = :id');
$consulta->bindValue(':id', $txtID, PDO::PARAM_INT);
$consulta->execute();
$registro = $consulta->fetch();

if (!$registro) {
    set_flash('warning', 'Representante no encontrado.');
    redirect_to('secciones/representantes/');
}

$valores = [
    'nombreCompleto' => $registro['nombreCompleto'],
    'localidad' => $registro['localidad'],
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $valores['nombreCompleto'] = post('nombreCompleto');
    $valores['localidad'] = post('localidad');

    if ($valores['nombreCompleto'] === '') {
        $error = 'Ingrese el nombre del representante.';
    } else {
        $actualiza = $conexion->prepare('UPDATE representante SET nombreCompleto = :nombre, localidad = :localidad WHERE ID = :id');
        $actualiza->bindValue(':nombre', $valores['nombreCompleto']);
        $actualiza->bindValue(':localidad', $valores['localidad']);
        $actualiza->bindValue(':id', $txtID, PDO::PARAM_INT);
        $actualiza->execute();

        set_flash('success', 'Representante actualizado correctamente.');
        redirect_to('secciones/representantes/');
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header">Editar representante</div>
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
                <label class="form-label" for="nombreCompleto">Nombres y apellidos</label>
                <input class="form-control" type="text" name="nombreCompleto" id="nombreCompleto" value="<?php echo e($valores['nombreCompleto']); ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label" for="localidad">Localidad</label>
                <input class="form-control" type="text" name="localidad" id="localidad" value="<?php echo e($valores['localidad']); ?>">
            </div>

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-success" type="submit">Actualizar</button>
                <a class="btn btn-outline-secondary" href="index.php">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>

