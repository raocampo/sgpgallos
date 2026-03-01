<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();
start_secure_session();
$context = require_tournament_context('Seleccione un torneo antes de gestionar gallos.');
$torneoId = $context['torneoId'];
$nombreTorneo = $context['nombreTorneo'];
$redirectGallos = 'secciones/gallos/?nombreTorneo=' . urlencode($nombreTorneo) . '&torneoId=' . $torneoId;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_gallo'])) {
    require_csrf();
    ensure_open_tournament_or_redirect($conexion, $torneoId, $redirectGallos);

    $id = (int) $_POST['eliminar_gallo'];
    $sentencia = $conexion->prepare('DELETE FROM gallos WHERE ID = :id AND torneoId = :torneoId');
    $sentencia->bindValue(':id', $id, PDO::PARAM_INT);
    $sentencia->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
    $sentencia->execute();

    set_flash('success', 'Gallo eliminado correctamente.');
    redirect_to($redirectGallos);
}

$sentencia = $conexion->prepare('
    SELECT gallos.ID, gallos.anillo, gallos.pesoReal, gallos.tamañoReal, gallos.placa, gallos.nacimiento, gallos.frente,
           familias.nombre AS nombre_familia, representante.nombreCompleto AS nombre_representante
    FROM gallos
    INNER JOIN familias ON gallos.familiasId = familias.codigo
    INNER JOIN representante ON gallos.representanteId = representante.ID
    WHERE gallos.torneoId = :torneoId
    ORDER BY gallos.pesoReal ASC, gallos.tamañoReal ASC, gallos.anillo ASC
');
$sentencia->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
$sentencia->execute();
$lista_gallos = $sentencia->fetchAll();

$criaderos = array_unique(array_column($lista_gallos, 'nombre_familia'));
$representantes = array_unique(array_column($lista_gallos, 'nombre_representante'));

include __DIR__ . '/../../templates/header.sub.php';
?>

<div class="page-intro">
    <div>
        <span class="app-kicker">Registro deportivo</span>
        <h2 class="page-title mb-2">Gallos del torneo</h2>
        <p>Administre el padrón del torneo, revise pesos y mantenga consistente la base para el cotejamiento.</p>
    </div>
    <a class="btn btn-primary btn-sm" href="crear.php"><i class="fa-solid fa-plus"></i> Agregar gallo</a>
</div>

<div class="stats-grid mb-4">
    <div class="card stat-card">
        <div class="stat-label">Gallos</div>
        <div class="stat-value"><?php echo e((string) count($lista_gallos)); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Criaderos</div>
        <div class="stat-value"><?php echo e((string) count($criaderos)); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Representantes</div>
        <div class="stat-value"><?php echo e((string) count($representantes)); ?></div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header panel-toolbar">
        <div>
            <div class="panel-title">Listado general</div>
            <div class="panel-note">Ordenado por peso, altura y anillo para facilitar la revision previa al cotejamiento.</div>
        </div>
        <a class="btn btn-primary btn-sm" href="crear.php"><i class="fa-solid fa-plus"></i> Agregar</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle" data-datatable="true">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Anillo</th>
                        <th>Peso</th>
                        <th>Tamano</th>
                        <th>Placa</th>
                        <th>Mes nacimiento</th>
                        <th>Frente</th>
                        <th>Criadero</th>
                        <th>Representante</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_gallos as $index => $registro): ?>
                        <tr>
                            <td><?php echo e((string) ($index + 1)); ?></td>
                            <td><?php echo e($registro['anillo']); ?></td>
                            <td><?php echo e((string) $registro['pesoReal']); ?></td>
                            <td><?php echo e((string) $registro['tamañoReal']); ?></td>
                            <td><?php echo e($registro['placa']); ?></td>
                            <td><?php echo e($registro['nacimiento']); ?></td>
                            <td><?php echo e($registro['frente']); ?></td>
                            <td><?php echo e($registro['nombre_familia']); ?></td>
                            <td><?php echo e($registro['nombre_representante']); ?></td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-outline-primary btn-sm" href="editar.php?txtID=<?php echo urlencode((string) $registro['ID']); ?>">Editar</a>
                                <?php echo render_delete_button('eliminar_gallo', (int) $registro['ID']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
