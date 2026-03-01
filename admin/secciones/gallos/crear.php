<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();
start_secure_session();
$context = require_tournament_context('Seleccione un torneo antes de crear gallos.');
$torneoId = $context['torneoId'];
$nombreTorneo = $context['nombreTorneo'];
$redirectGallos = 'secciones/gallos/?nombreTorneo=' . urlencode($nombreTorneo) . '&torneoId=' . $torneoId;

$familias = $conexion->query('SELECT codigo, nombre FROM familias ORDER BY nombre ASC')->fetchAll();
$representantes = $conexion->query('SELECT ID, nombreCompleto FROM representante ORDER BY nombreCompleto ASC')->fetchAll();

$valores = [
    'anillo' => '',
    'pesoReal' => '',
    'alturaReal' => '',
    'placa' => '',
    'nacimiento' => '',
    'frente' => '',
    'familiasId' => '',
    'representanteId' => '',
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
        $verifica = $conexion->prepare('SELECT COUNT(*) FROM gallos WHERE anillo = :anillo AND torneoId = :torneoId');
        $verifica->bindValue(':anillo', $valores['anillo']);
        $verifica->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
        $verifica->execute();

        if ((int) $verifica->fetchColumn() > 0) {
            $error = 'Ya existe un gallo con ese anillo en el torneo actual.';
        } else {
            try {
                $sentencia = $conexion->prepare('
                    INSERT INTO gallos (anillo, pesoReal, tamañoReal, placa, nacimiento, frente, familiasId, representanteId, torneoId)
                    VALUES (:anillo, :pesoReal, :tamanoReal, :placa, :nacimiento, :frente, :familiasId, :representanteId, :torneoId)
                ');
                $sentencia->bindValue(':anillo', $valores['anillo']);
                $sentencia->bindValue(':pesoReal', $valores['pesoReal']);
                $sentencia->bindValue(':tamanoReal', $valores['alturaReal']);
                $sentencia->bindValue(':placa', $valores['placa']);
                $sentencia->bindValue(':nacimiento', $valores['nacimiento']);
                $sentencia->bindValue(':frente', $valores['frente']);
                $sentencia->bindValue(':familiasId', (int) $valores['familiasId'], PDO::PARAM_INT);
                $sentencia->bindValue(':representanteId', (int) $valores['representanteId'], PDO::PARAM_INT);
                $sentencia->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
                $sentencia->execute();

                set_flash('success', 'Gallo creado correctamente.');
                redirect_to($redirectGallos);
            } catch (Throwable $errorDb) {
                $error = 'No fue posible guardar el gallo. Verifique anillo y datos relacionados.';
            }
        }
    }
}

include __DIR__ . '/../../templates/header.sub.php';
?>

<div class="card shadow-sm border-0">
    <div class="card-header">Crear gallo</div>
    <div class="card-body">
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3">
            <?php echo csrf_input(); ?>

            <div class="col-md-4">
                <label class="form-label" for="anillo">Anillo</label>
                <input class="form-control" type="text" name="anillo" id="anillo" value="<?php echo e($valores['anillo']); ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label" for="pesoReal">Peso</label>
                <input class="form-control" type="number" step="0.01" name="pesoReal" id="pesoReal" value="<?php echo e($valores['pesoReal']); ?>" required>
            </div>

            <div class="col-md-4">
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
                <button class="btn btn-success" type="submit">Guardar</button>
                <a class="btn btn-outline-secondary" href="index.php">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
