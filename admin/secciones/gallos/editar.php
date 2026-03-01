<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();
start_secure_session();
$context = require_tournament_context('Seleccione un torneo antes de editar gallos.');
$torneoId = $context['torneoId'];
$nombreTorneo = $context['nombreTorneo'];
$redirectGallos = 'secciones/gallos/?nombreTorneo=' . urlencode($nombreTorneo) . '&torneoId=' . $torneoId;
$txtID = isset($_GET['txtID']) ? (int) $_GET['txtID'] : 0;

if ($torneoId <= 0 || $txtID <= 0) {
    set_flash('warning', 'Gallo no valido.');
    redirect_to('secciones/torneos/');
}

$consulta = $conexion->prepare('SELECT * FROM gallos WHERE ID = :id AND torneoId = :torneoId');
$consulta->bindValue(':id', $txtID, PDO::PARAM_INT);
$consulta->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
$consulta->execute();
$registro = $consulta->fetch();

if (!$registro) {
    set_flash('warning', 'Gallo no encontrado.');
    redirect_to('secciones/gallos/?nombreTorneo=' . urlencode($nombreTorneo) . '&torneoId=' . $torneoId);
}

$familias = $conexion->query('SELECT codigo, nombre FROM familias ORDER BY nombre ASC')->fetchAll();
$representantes = $conexion->query('SELECT ID, nombreCompleto FROM representante ORDER BY nombreCompleto ASC')->fetchAll();

$valores = [
    'anillo' => $registro['anillo'],
    'pesoReal' => (string) $registro['pesoReal'],
    'alturaReal' => (string) $registro['tamañoReal'],
    'placa' => $registro['placa'],
    'nacimiento' => $registro['nacimiento'],
    'frente' => $registro['frente'],
    'familiasId' => (string) $registro['familiasId'],
    'representanteId' => (string) $registro['representanteId'],
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    ensure_open_tournament_or_redirect($conexion, $torneoId, $redirectGallos);

    foreach ($valores as $campo => $valor) {
        $valores[$campo] = post($campo);
    }

    if ($valores['anillo'] === '' || $valores['pesoReal'] === '' || $valores['alturaReal'] === '' || $valores['familiasId'] === '' || $valores['representanteId'] === '') {
        $error = 'Complete los campos obligatorios.';
    } else {
        $verifica = $conexion->prepare('SELECT COUNT(*) FROM gallos WHERE anillo = :anillo AND torneoId = :torneoId AND ID <> :id');
        $verifica->bindValue(':anillo', $valores['anillo']);
        $verifica->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
        $verifica->bindValue(':id', $txtID, PDO::PARAM_INT);
        $verifica->execute();

        if ((int) $verifica->fetchColumn() > 0) {
            $error = 'Ya existe otro gallo con ese anillo en el torneo actual.';
        } else {
            try {
                $actualiza = $conexion->prepare('
                    UPDATE gallos
                    SET anillo = :anillo,
                        pesoReal = :pesoReal,
                        tamañoReal = :tamanoReal,
                        placa = :placa,
                        nacimiento = :nacimiento,
                        frente = :frente,
                        familiasId = :familiasId,
                        representanteId = :representanteId
                    WHERE ID = :id AND torneoId = :torneoId
                ');
                $actualiza->bindValue(':anillo', $valores['anillo']);
                $actualiza->bindValue(':pesoReal', $valores['pesoReal']);
                $actualiza->bindValue(':tamanoReal', $valores['alturaReal']);
                $actualiza->bindValue(':placa', $valores['placa']);
                $actualiza->bindValue(':nacimiento', $valores['nacimiento']);
                $actualiza->bindValue(':frente', $valores['frente']);
                $actualiza->bindValue(':familiasId', (int) $valores['familiasId'], PDO::PARAM_INT);
                $actualiza->bindValue(':representanteId', (int) $valores['representanteId'], PDO::PARAM_INT);
                $actualiza->bindValue(':id', $txtID, PDO::PARAM_INT);
                $actualiza->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
                $actualiza->execute();

                set_flash('success', 'Gallo actualizado correctamente.');
                redirect_to($redirectGallos);
            } catch (Throwable $errorDb) {
                $error = 'No fue posible actualizar el gallo.';
            }
        }
    }
}

include __DIR__ . '/../../templates/header.sub.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header">Editar gallo</div>
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

            <div class="col-md-4">
                <label class="form-label" for="anillo">Anillo</label>
                <input class="form-control" type="text" name="anillo" id="anillo" value="<?php echo e($valores['anillo']); ?>" required>
            </div>

            <div class="col-md-3">
                <label class="form-label" for="pesoReal">Peso</label>
                <input class="form-control" type="number" step="0.01" name="pesoReal" id="pesoReal" value="<?php echo e($valores['pesoReal']); ?>" required>
            </div>

            <div class="col-md-3">
                <label class="form-label" for="alturaReal">Tamano</label>
                <input class="form-control" type="number" step="0.01" name="alturaReal" id="alturaReal" value="<?php echo e($valores['alturaReal']); ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label" for="placa">Placa</label>
                <input class="form-control" type="text" name="placa" id="placa" value="<?php echo e($valores['placa']); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label" for="nacimiento">Mes de nacimiento</label>
                <input class="form-control" type="text" name="nacimiento" id="nacimiento" value="<?php echo e($valores['nacimiento']); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label" for="frente">Frente</label>
                <input class="form-control" type="text" name="frente" id="frente" value="<?php echo e($valores['frente']); ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label" for="familiasId">Criadero</label>
                <select class="form-select" name="familiasId" id="familiasId" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($familias as $familia): ?>
                        <option value="<?php echo e((string) $familia['codigo']); ?>" <?php echo $valores['familiasId'] === (string) $familia['codigo'] ? 'selected' : ''; ?>>
                            <?php echo e($familia['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
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

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-success" type="submit">Actualizar</button>
                <a class="btn btn-outline-secondary" href="index.php">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
