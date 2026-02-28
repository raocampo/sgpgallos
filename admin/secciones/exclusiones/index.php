<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();
start_secure_session();
$context = require_tournament_context('Seleccione un torneo antes de gestionar exclusiones.');
$torneoId = $context['torneoId'];

$familias = $conexion->query('SELECT codigo, nombre FROM familias ORDER BY nombre ASC')->fetchAll();

include __DIR__ . '/../../templates/header.sub.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header">Nueva exclusion</div>
            <div class="card-body">
                <form action="procExclusiones.php" method="post" class="row g-3">
                    <?php echo csrf_input(); ?>

                    <div class="col-md-6">
                        <label class="form-label" for="familia1">Criadero 1</label>
                        <select class="form-select" name="familia1" id="familia1" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($familias as $familia): ?>
                                <option value="<?php echo e((string) $familia['codigo']); ?>"><?php echo e($familia['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="familia2">Criadero 2</label>
                        <select class="form-select" name="familia2" id="familia2" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($familias as $familia): ?>
                                <option value="<?php echo e((string) $familia['codigo']); ?>"><?php echo e($familia['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-success" type="submit">Guardar exclusion</button>
                        <a class="btn btn-outline-primary" href="procExclusiones.php">Ver exclusiones</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
