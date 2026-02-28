<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();
start_secure_session();

$context = require_tournament_context('Seleccione un torneo antes de generar cotejas.');
$torneoId = $context['torneoId'];
$nombreTorneo = $context['nombreTorneo'];

$redirectUrl = 'secciones/peleas/cotejamiento.php?nombreTorneo=' . urlencode($nombreTorneo) . '&torneoId=' . $torneoId;
$filtrosSessionKey = 'cotejamiento_filtros';

function valor_decimal(?string $valor): ?float
{
    if ($valor === null) {
        return null;
    }

    $valor = trim(str_replace(',', '.', $valor));

    return $valor === '' ? null : (float) $valor;
}

function valor_mes(?string $valor): ?int
{
    if ($valor === null) {
        return null;
    }

    $limpio = preg_replace('/\D+/', '', trim($valor));
    if ($limpio === '') {
        return null;
    }

    $mes = (int) $limpio;

    return $mes >= 1 && $mes <= 12 ? $mes : null;
}

function coteja_ya_guardada(PDO $conexion, int $torneoId, int $galloL, int $galloV): bool
{
    $sentencia = $conexion->prepare('
        SELECT COUNT(*)
        FROM coteja
        WHERE torneoId = :torneoId
          AND (
                (galloL = :galloL_a AND galloV = :galloV_a)
             OR (galloL = :galloV_b AND galloV = :galloL_b)
          )
    ');
    $sentencia->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
    $sentencia->bindValue(':galloL_a', $galloL, PDO::PARAM_INT);
    $sentencia->bindValue(':galloV_a', $galloV, PDO::PARAM_INT);
    $sentencia->bindValue(':galloV_b', $galloV, PDO::PARAM_INT);
    $sentencia->bindValue(':galloL_b', $galloL, PDO::PARAM_INT);
    $sentencia->execute();

    return (int) $sentencia->fetchColumn() > 0;
}

function gallos_disponibles_para_coteja(PDO $conexion, int $torneoId, int $galloL, int $galloV): bool
{
    $sentenciaGallos = $conexion->prepare('
        SELECT COUNT(*)
        FROM gallos
        WHERE torneoId = :torneoId
          AND (ID = :galloL OR ID = :galloV)
    ');
    $sentenciaGallos->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
    $sentenciaGallos->bindValue(':galloL', $galloL, PDO::PARAM_INT);
    $sentenciaGallos->bindValue(':galloV', $galloV, PDO::PARAM_INT);
    $sentenciaGallos->execute();

    if ((int) $sentenciaGallos->fetchColumn() !== 2) {
        return false;
    }

    $sentenciaCotejas = $conexion->prepare('
        SELECT COUNT(*)
        FROM coteja
        WHERE torneoId = :torneoId
          AND (
                galloL = :galloL_a
             OR galloV = :galloL_b
             OR galloL = :galloV_a
             OR galloV = :galloV_b
          )
    ');
    $sentenciaCotejas->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
    $sentenciaCotejas->bindValue(':galloL_a', $galloL, PDO::PARAM_INT);
    $sentenciaCotejas->bindValue(':galloL_b', $galloL, PDO::PARAM_INT);
    $sentenciaCotejas->bindValue(':galloV_a', $galloV, PDO::PARAM_INT);
    $sentenciaCotejas->bindValue(':galloV_b', $galloV, PDO::PARAM_INT);
    $sentenciaCotejas->execute();

    return (int) $sentenciaCotejas->fetchColumn() === 0;
}

function registrar_coteja(PDO $conexion, int $torneoId, int $galloL, int $galloV): bool
{
    if ($galloL <= 0 || $galloV <= 0 || $galloL === $galloV) {
        return false;
    }

    if (!gallos_disponibles_para_coteja($conexion, $torneoId, $galloL, $galloV)) {
        return false;
    }

    if (coteja_ya_guardada($conexion, $torneoId, $galloL, $galloV)) {
        return false;
    }

    $sentencia = $conexion->prepare('
        INSERT INTO coteja (galloL, galloV, estado, torneoId)
        VALUES (:galloL, :galloV, :estado, :torneoId)
    ');
    $sentencia->bindValue(':galloL', $galloL, PDO::PARAM_INT);
    $sentencia->bindValue(':galloV', $galloV, PDO::PARAM_INT);
    $sentencia->bindValue(':estado', 'Cotejado');
    $sentencia->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);

    return $sentencia->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    if (isset($_POST['guardar_filtros'])) {
        $usaPeso = isset($_POST['medida']);
        $usaAltura = isset($_POST['medidaAltura']);
        $peso = $usaPeso ? post('peso') : '';
        $altura = $usaAltura ? post('altura') : '';
        $lote = max(0, (int) post('rescotejar'));

        $_SESSION[$filtrosSessionKey][$torneoId] = [
            'usaPeso' => $usaPeso,
            'usaAltura' => $usaAltura,
            'peso' => $peso,
            'altura' => $altura,
            'nacimiento' => isset($_POST['nacimiento']),
            'exclusion' => isset($_POST['exclusion']),
            'rescotejar' => $lote > 0 ? (string) $lote : '',
        ];

        if (($usaPeso && $peso === '') || ($usaAltura && $altura === '')) {
            set_flash('warning', 'Indique un valor para cada filtro activado.');
        }

        redirect_to($redirectUrl);
    }

    if (isset($_POST['eliminar_coteja'])) {
        $id = (int) $_POST['eliminar_coteja'];
        $sentencia = $conexion->prepare('DELETE FROM coteja WHERE ID_Coteja = :id AND torneoId = :torneoId');
        $sentencia->bindValue(':id', $id, PDO::PARAM_INT);
        $sentencia->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
        $sentencia->execute();

        set_flash('success', 'La coteja fue retirada correctamente.');
        redirect_to($redirectUrl);
    }

    if (isset($_POST['limpiar_cotejas'])) {
        $sentencia = $conexion->prepare('DELETE FROM coteja WHERE torneoId = :torneoId');
        $sentencia->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
        $sentencia->execute();

        set_flash('success', 'Se limpiaron todas las cotejas del torneo.');
        redirect_to($redirectUrl);
    }

    if (isset($_POST['guardar_coteja_manual'])) {
        $seleccionados = array_values(array_unique(array_map('intval', $_POST['cotejamiento'] ?? [])));

        if (count($seleccionados) !== 2) {
            set_flash('warning', 'Seleccione exactamente 2 gallos para registrar la coteja manual.');
        } elseif (registrar_coteja($conexion, $torneoId, $seleccionados[0], $seleccionados[1])) {
            set_flash('success', 'La coteja manual se registró correctamente.');
        } else {
            set_flash('danger', 'No fue posible registrar la coteja manual.');
        }

        redirect_to($redirectUrl);
    }

    if (isset($_POST['guardar_coteja_libre'])) {
        $seleccionados = array_values(array_unique(array_map('intval', $_POST['gallos'] ?? [])));

        if (count($seleccionados) !== 2) {
            set_flash('warning', 'Seleccione exactamente 2 gallos libres.');
        } elseif (registrar_coteja($conexion, $torneoId, $seleccionados[0], $seleccionados[1])) {
            set_flash('success', 'La coteja desde gallos libres se registró correctamente.');
        } else {
            set_flash('danger', 'No fue posible cotejar los gallos libres seleccionados.');
        }

        redirect_to($redirectUrl);
    }
}

$filtros = $_SESSION[$filtrosSessionKey][$torneoId] ?? [
    'usaPeso' => false,
    'usaAltura' => false,
    'peso' => '',
    'altura' => '',
    'nacimiento' => false,
    'exclusion' => false,
    'rescotejar' => '',
];

$toleranciaPeso = valor_decimal($filtros['peso'] ?? null);
$toleranciaAltura = valor_decimal($filtros['altura'] ?? null);
$aplicarFiltroPeso = !empty($filtros['usaPeso']) && $toleranciaPeso !== null;
$aplicarFiltroAltura = !empty($filtros['usaAltura']) && $toleranciaAltura !== null;
$filtrarNacimiento = !empty($filtros['nacimiento']);
$filtrarExclusion = !empty($filtros['exclusion']);

$sentencia = $conexion->prepare('SELECT COUNT(*) FROM gallos WHERE torneoId = :torneoId');
$sentencia->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
$sentencia->execute();
$totalGallos = (int) $sentencia->fetchColumn();
$maximoCotejable = $totalGallos - ($totalGallos % 2);

$sentencia = $conexion->prepare('
    SELECT c.ID_Coteja, c.galloL, c.galloV,
           gl.anillo AS anilloL, gl.pesoReal AS pesoRealL, fl.nombre AS nombre_familiaL,
           gv.anillo AS anilloV, gv.pesoReal AS pesoRealV, fv.nombre AS nombre_familiaV
    FROM coteja c
    INNER JOIN gallos gl ON c.galloL = gl.ID
    INNER JOIN gallos gv ON c.galloV = gv.ID
    INNER JOIN familias fl ON gl.familiasId = fl.codigo
    INNER JOIN familias fv ON gv.familiasId = fv.codigo
    WHERE c.torneoId = :torneoId
    ORDER BY c.ID_Coteja ASC
');
$sentencia->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
$sentencia->execute();
$cotejasGuardadas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$gallosComprometidos = [];
foreach ($cotejasGuardadas as $coteja) {
    $gallosComprometidos[] = (int) $coteja['galloL'];
    $gallosComprometidos[] = (int) $coteja['galloV'];
}
$gallosComprometidos = array_values(array_unique($gallosComprometidos));

$sentencia = $conexion->prepare('
    SELECT g.ID, g.anillo, g.pesoReal, g.tamañoReal, g.nacimiento, g.frente, g.familiasId,
           f.nombre AS nombre_familia,
           r.nombreCompleto AS nombre_representante
    FROM gallos g
    INNER JOIN familias f ON g.familiasId = f.codigo
    INNER JOIN representante r ON g.representanteId = r.ID
    WHERE g.torneoId = :torneoId
    ORDER BY g.pesoReal ASC, g.tamañoReal ASC, g.anillo ASC
');
$sentencia->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
$sentencia->execute();
$listaGallos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$gallosDisponibles = array_values(array_filter($listaGallos, static function (array $gallo) use ($gallosComprometidos): bool {
    return !in_array((int) $gallo['ID'], $gallosComprometidos, true);
}));

$loteSolicitado = (int) ($filtros['rescotejar'] ?? 0);
if ($loteSolicitado <= 0) {
    $loteSolicitado = count($gallosDisponibles);
}
$loteSolicitado = min($loteSolicitado, count($gallosDisponibles));
if ($loteSolicitado > 0 && $loteSolicitado % 2 !== 0) {
    $loteSolicitado--;
}

$gallosEnAnalisis = $loteSolicitado > 0 ? array_slice($gallosDisponibles, 0, $loteSolicitado) : [];
$gallosFueraDeAnalisis = $loteSolicitado > 0 ? array_slice($gallosDisponibles, $loteSolicitado) : $gallosDisponibles;

$exclusiones = [];
if ($filtrarExclusion) {
    $sentencia = $conexion->prepare('SELECT nombreFamiliaUno, nombreFamiliaDos FROM exclusiones WHERE torneoId = :torneoId');
    $sentencia->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
    $sentencia->execute();

    foreach ($sentencia->fetchAll(PDO::FETCH_ASSOC) as $registro) {
        $claveA = $registro['nombreFamiliaUno'] . '-' . $registro['nombreFamiliaDos'];
        $claveB = $registro['nombreFamiliaDos'] . '-' . $registro['nombreFamiliaUno'];
        $exclusiones[$claveA] = true;
        $exclusiones[$claveB] = true;
    }
}

$parejasPropuestas = [];
$gallosLibres = [];
$idsUsados = [];

for ($i = 0, $totalAnalisis = count($gallosEnAnalisis); $i < $totalAnalisis; $i++) {
    $galloBase = $gallosEnAnalisis[$i];
    $idBase = (int) $galloBase['ID'];

    if (isset($idsUsados[$idBase])) {
        continue;
    }

    $mejorIndice = null;
    $mejorPuntaje = null;

    for ($j = $i + 1; $j < $totalAnalisis; $j++) {
        $galloComparado = $gallosEnAnalisis[$j];
        $idComparado = (int) $galloComparado['ID'];

        if (isset($idsUsados[$idComparado])) {
            continue;
        }

        if ((string) $galloBase['familiasId'] === (string) $galloComparado['familiasId']) {
            continue;
        }

        if ($filtrarExclusion && isset($exclusiones[$galloBase['familiasId'] . '-' . $galloComparado['familiasId']])) {
            continue;
        }

        $difPeso = abs((float) $galloBase['pesoReal'] - (float) $galloComparado['pesoReal']);
        $difAltura = abs((float) $galloBase['tamañoReal'] - (float) $galloComparado['tamañoReal']);

        if ($aplicarFiltroPeso && $difPeso > (float) $toleranciaPeso) {
            continue;
        }

        if ($aplicarFiltroAltura && $difAltura > (float) $toleranciaAltura) {
            continue;
        }

        if ($filtrarNacimiento) {
            $mesBase = valor_mes((string) $galloBase['nacimiento']);
            $mesComparado = valor_mes((string) $galloComparado['nacimiento']);

            if ($mesBase === null || $mesComparado === null || abs($mesBase - $mesComparado) > 1) {
                continue;
            }
        }

        if (!$aplicarFiltroPeso && !$aplicarFiltroAltura && ($difPeso > 0 || $difAltura > 0)) {
            continue;
        }

        $puntaje = 0;
        $puntaje += $aplicarFiltroPeso ? ($difPeso * 1000) : 0;
        $puntaje += $aplicarFiltroAltura ? ($difAltura * 100) : 0;
        $puntaje += (!$aplicarFiltroPeso && !$aplicarFiltroAltura) ? (($difPeso + $difAltura) * 1000) : 0;

        if ($mejorPuntaje === null || $puntaje < $mejorPuntaje) {
            $mejorPuntaje = $puntaje;
            $mejorIndice = $j;
        }
    }

    if ($mejorIndice !== null) {
        $parejasPropuestas[] = [$galloBase, $gallosEnAnalisis[$mejorIndice]];
        $idsUsados[$idBase] = true;
        $idsUsados[(int) $gallosEnAnalisis[$mejorIndice]['ID']] = true;
    } else {
        $gallosLibres[] = $galloBase;
    }
}

$resumenFiltros = [];
if ($aplicarFiltroPeso) {
    $resumenFiltros[] = 'Peso +/- ' . rtrim(rtrim((string) $toleranciaPeso, '0'), '.');
}
if ($aplicarFiltroAltura) {
    $resumenFiltros[] = 'Altura +/- ' . rtrim(rtrim((string) $toleranciaAltura, '0'), '.');
}
if ($filtrarNacimiento) {
    $resumenFiltros[] = 'Nacimiento compatible';
}
if ($filtrarExclusion) {
    $resumenFiltros[] = 'Respeta exclusiones';
}
if (!$aplicarFiltroPeso && !$aplicarFiltroAltura) {
    $resumenFiltros[] = 'Sin peso/altura: coincidencia exacta';
}

include __DIR__ . '/../../templates/header.sub.php';
?>

<div class="page-intro">
    <div>
        <span class="app-kicker">Motor de cruces</span>
        <h2 class="page-title mb-2">Cotejamiento del torneo</h2>
        <p>Seleccione por peso, altura, nacimiento y exclusiones para generar propuestas automaticas del torneo activo.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-outline-primary btn-sm" href="peleaGenerada.php">Ver peleas</a>
        <?php if (!empty($cotejasGuardadas)): ?>
            <form method="post" class="d-inline">
                <?php echo csrf_input(); ?>
                <input type="hidden" name="limpiar_cotejas" value="1">
                <button type="submit" class="btn btn-outline-secondary btn-sm" data-confirm="Esto eliminara todas las cotejas guardadas del torneo. Desea continuar?">Limpiar cotejas</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="stats-grid mb-4">
    <div class="card stat-card">
        <div class="stat-label">Gallos cargados</div>
        <div class="stat-value"><?php echo e((string) $totalGallos); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Disponibles</div>
        <div class="stat-value"><?php echo e((string) count($gallosDisponibles)); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Cotejas guardadas</div>
        <div class="stat-value"><?php echo e((string) count($cotejasGuardadas)); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Propuestas</div>
        <div class="stat-value"><?php echo e((string) count($parejasPropuestas)); ?></div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Gallos libres</div>
        <div class="stat-value"><?php echo e((string) count($gallosLibres)); ?></div>
    </div>
</div>

<form id="form-pactar-peleas" action="peleaGenerada.php" method="post" class="d-none">
    <?php echo csrf_input(); ?>
    <input type="hidden" name="peleaGenerada" value="1">
</form>

<section class="board-grid pairing-layout">
    <div class="stack-grid">
        <div class="card table-card">
            <div class="card-header panel-toolbar">
                <div>
                    <div class="panel-title">Parametros de emparejamiento</div>
                    <div class="panel-note">Puede usar peso, altura o ambos. Nacimiento y exclusiones se aplican como filtros adicionales.</div>
                </div>
                <span class="badge-soft"><?php echo e((string) min($loteSolicitado, $maximoCotejable)); ?> gallos en analisis</span>
            </div>
            <div class="card-body">
                <form method="post" class="row g-3">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="guardar_filtros" value="1">

                    <div class="col-md-6">
                        <label class="form-label d-block" for="onzas">Filtrar por peso</label>
                        <div class="filter-input-group">
                            <input type="checkbox" name="medida" value="onzas" id="onzas" <?php echo $aplicarFiltroPeso ? 'checked' : ''; ?>>
                            <input class="form-control" type="number" step="0.01" min="0" name="peso" id="peso" value="<?php echo e((string) ($filtros['peso'] ?? '')); ?>" <?php echo $aplicarFiltroPeso ? '' : 'disabled'; ?>>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label d-block" for="centimetros">Filtrar por altura</label>
                        <div class="filter-input-group">
                            <input type="checkbox" name="medidaAltura" value="centimetros" id="centimetros" <?php echo $aplicarFiltroAltura ? 'checked' : ''; ?>>
                            <input class="form-control" type="number" step="0.01" min="0" name="altura" id="altura" value="<?php echo e((string) ($filtros['altura'] ?? '')); ?>" <?php echo $aplicarFiltroAltura ? '' : 'disabled'; ?>>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="rescotejar">Gallos a evaluar</label>
                        <input class="form-control" type="number" min="0" max="<?php echo e((string) $maximoCotejable); ?>" name="rescotejar" id="rescotejar" value="<?php echo e((string) ($filtros['rescotejar'] !== '' ? $filtros['rescotejar'] : $maximoCotejable)); ?>">
                    </div>

                    <div class="col-md-6">
                        <div class="filter-switches">
                            <label class="toggle-chip">
                                <input type="checkbox" name="nacimiento" <?php echo $filtrarNacimiento ? 'checked' : ''; ?>>
                                <span>Mes de nacimiento</span>
                            </label>
                            <label class="toggle-chip">
                                <input type="checkbox" name="exclusion" <?php echo $filtrarExclusion ? 'checked' : ''; ?>>
                                <span>Aplicar exclusiones</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-success">Generar cotejamiento</button>
                        <span class="badge-soft accent"><?php echo e(implode(' | ', $resumenFiltros)); ?></span>
                    </div>
                </form>
            </div>
        </div>

        <div class="card table-card">
            <div class="card-header panel-toolbar">
                <div>
                    <div class="panel-title">Coteja manual</div>
                    <div class="panel-note">Registro directo para casos donde quiera elegir manualmente dos gallos disponibles.</div>
                </div>
                <span class="badge-soft"><?php echo e((string) count($gallosDisponibles)); ?> disponibles</span>
            </div>
            <div class="card-body">
                <?php if (!empty($gallosDisponibles)): ?>
                    <form method="post">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="guardar_coteja_manual" value="1">
                        <div class="mini-toolbar">
                            <div class="panel-note">Solo se listan gallos que no estan ya comprometidos en otras cotejas.</div>
                            <button type="submit" class="btn btn-success btn-sm">Registrar coteja manual</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle" data-datatable="true">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Anillo</th>
                                        <th>Criadero</th>
                                        <th>Peso</th>
                                        <th>Altura</th>
                                        <th>Frente</th>
                                        <th>Nacimiento</th>
                                        <th>Representante</th>
                                        <th>Seleccionar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($gallosDisponibles as $index => $gallo): ?>
                                        <tr>
                                            <td><?php echo e((string) ($index + 1)); ?></td>
                                            <td><?php echo e($gallo['anillo']); ?></td>
                                            <td><?php echo e($gallo['nombre_familia']); ?></td>
                                            <td><?php echo e((string) $gallo['pesoReal']); ?></td>
                                            <td><?php echo e((string) $gallo['tamañoReal']); ?></td>
                                            <td><?php echo e($gallo['frente']); ?></td>
                                            <td><?php echo e((string) $gallo['nacimiento']); ?></td>
                                            <td><?php echo e($gallo['nombre_representante']); ?></td>
                                            <td><input type="checkbox" class="form-check-input js-limit-checkbox" data-limit="2" data-group="manual-coteja" name="cotejamiento[]" value="<?php echo e((string) $gallo['ID']); ?>"></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="empty-panel">
                        <h3>No hay gallos disponibles</h3>
                        <p>Todos los gallos del torneo ya tienen una coteja registrada.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="stack-grid">
        <div class="card table-card">
            <div class="card-header panel-toolbar">
                <div>
                    <div class="panel-title">Cotejas guardadas</div>
                    <div class="panel-note">Seleccione aqui las cotejas que desea convertir en peleas pactadas.</div>
                </div>
                <button type="submit" form="form-pactar-peleas" class="btn btn-success btn-sm">Pactar seleccionadas</button>
            </div>
            <div class="card-body">
                <?php if (!empty($cotejasGuardadas)): ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Anillo L</th>
                                    <th>Criadero L</th>
                                    <th>Peso L</th>
                                    <th>Anillo V</th>
                                    <th>Criadero V</th>
                                    <th>Peso V</th>
                                    <th>Seleccionar</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cotejasGuardadas as $index => $coteja): ?>
                                    <tr>
                                        <td><?php echo e((string) ($index + 1)); ?></td>
                                        <td><?php echo e($coteja['anilloL']); ?></td>
                                        <td><?php echo e($coteja['nombre_familiaL']); ?></td>
                                        <td><?php echo e((string) $coteja['pesoRealL']); ?></td>
                                        <td><?php echo e($coteja['anilloV']); ?></td>
                                        <td><?php echo e($coteja['nombre_familiaV']); ?></td>
                                        <td><?php echo e((string) $coteja['pesoRealV']); ?></td>
                                        <td><input type="checkbox" class="form-check-input" form="form-pactar-peleas" name="peleas[]" value="<?php echo e((string) $coteja['ID_Coteja']); ?>"></td>
                                        <td>
                                            <form method="post" class="d-inline">
                                                <?php echo csrf_input(); ?>
                                                <button type="submit" class="btn btn-outline-danger btn-sm" name="eliminar_coteja" value="<?php echo e((string) $coteja['ID_Coteja']); ?>" data-confirm="Confirma quitar esta coteja?">Quitar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-panel">
                        <h3>No hay cotejas guardadas</h3>
                        <p>Primero genere propuestas o registre cotejas manuales.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card table-card">
            <div class="card-header panel-toolbar">
                <div>
                    <div class="panel-title">Propuestas automaticas</div>
                    <div class="panel-note">El sistema aplica el filtro seleccionado y deja listas las mejores parejas encontradas.</div>
                </div>
                <span class="badge-soft"><?php echo e((string) count($parejasPropuestas)); ?> sugeridas</span>
            </div>
            <div class="card-body">
                <?php if (!empty($parejasPropuestas)): ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Anillo L</th>
                                    <th>Criadero L</th>
                                    <th>Peso L</th>
                                    <th>Altura L</th>
                                    <th>Anillo V</th>
                                    <th>Criadero V</th>
                                    <th>Peso V</th>
                                    <th>Altura V</th>
                                    <th>Seleccionar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($parejasPropuestas as $index => $pareja): ?>
                                    <?php [$galloL, $galloV] = $pareja; ?>
                                    <tr>
                                        <td><?php echo e((string) ($index + 1)); ?></td>
                                        <td><?php echo e($galloL['anillo']); ?></td>
                                        <td><?php echo e($galloL['nombre_familia']); ?></td>
                                        <td><?php echo e((string) $galloL['pesoReal']); ?></td>
                                        <td><?php echo e((string) $galloL['tamañoReal']); ?></td>
                                        <td><?php echo e($galloV['anillo']); ?></td>
                                        <td><?php echo e($galloV['nombre_familia']); ?></td>
                                        <td><?php echo e((string) $galloV['pesoReal']); ?></td>
                                        <td><?php echo e((string) $galloV['tamañoReal']); ?></td>
                                        <td><input type="checkbox" class="form-check-input" form="form-pactar-peleas" name="peleas[]" value="<?php echo e($galloL['ID'] . '-' . $galloV['ID']); ?>"></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-panel">
                        <h3>Sin propuestas para el filtro actual</h3>
                        <p>Pruebe cambiando peso, altura, lote de analisis o revise las exclusiones activas del torneo.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card table-card">
            <div class="card-header panel-toolbar">
                <div>
                    <div class="panel-title">Gallos libres</div>
                    <div class="panel-note">Gallos del lote analizado que no encontraron pareja con los criterios seleccionados.</div>
                </div>
                <span class="badge-soft accent"><?php echo e((string) count($gallosFueraDeAnalisis)); ?> fuera del lote</span>
            </div>
            <div class="card-body">
                <?php if (!empty($gallosLibres)): ?>
                    <form method="post">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="guardar_coteja_libre" value="1">
                        <div class="mini-toolbar">
                            <div class="panel-note">Puede elegir dos gallos libres y convertirlos en una coteja manual.</div>
                            <button type="submit" class="btn btn-outline-primary btn-sm">Cotejar libres</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Anillo</th>
                                        <th>Criadero</th>
                                        <th>Peso</th>
                                        <th>Altura</th>
                                        <th>Frente</th>
                                        <th>Nacimiento</th>
                                        <th>Seleccionar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($gallosLibres as $index => $gallo): ?>
                                        <tr>
                                            <td><?php echo e((string) ($index + 1)); ?></td>
                                            <td><?php echo e($gallo['anillo']); ?></td>
                                            <td><?php echo e($gallo['nombre_familia']); ?></td>
                                            <td><?php echo e((string) $gallo['pesoReal']); ?></td>
                                            <td><?php echo e((string) $gallo['tamañoReal']); ?></td>
                                            <td><?php echo e($gallo['frente']); ?></td>
                                            <td><?php echo e((string) $gallo['nacimiento']); ?></td>
                                            <td><input type="checkbox" class="form-check-input js-limit-checkbox" data-limit="2" data-group="gallos-libres" name="gallos[]" value="<?php echo e((string) $gallo['ID']); ?>"></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="empty-panel">
                        <h3>No quedaron gallos libres</h3>
                        <p>El filtro resolvio el lote completo o no quedaron gallos disponibles para emparejar.</p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($gallosFueraDeAnalisis)): ?>
                    <div class="soft-panel mt-3">
                        <strong>Disponibles fuera del lote:</strong>
                        <?php echo e((string) count($gallosFueraDeAnalisis)); ?> gallos aun no fueron evaluados. Aumente el lote si desea incluirlos.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.js-limit-checkbox').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                var group = this.getAttribute('data-group');
                var limit = parseInt(this.getAttribute('data-limit') || '0', 10);
                var selected = document.querySelectorAll('.js-limit-checkbox[data-group="' + group + '"]:checked');

                if (limit > 0 && selected.length > limit) {
                    this.checked = false;
                    window.alert('Solo puede seleccionar ' + limit + ' registros en esta accion.');
                }
            });
        });
    });
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
