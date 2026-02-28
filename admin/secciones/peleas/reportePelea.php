<?php

require_once __DIR__ . '/../../includes/app.php';
require_once __DIR__ . '/../../bd.php';

require_auth();
start_secure_session();

ob_start();
$context = require_tournament_context('Seleccione un torneo antes de imprimir el reporte.');
$torneoId = $context['torneoId'];
$nombreTorneo = $context['nombreTorneo'];

$sentencia = $conexion->prepare('
    SELECT p.ID_Pelea, p.estado, p.ganador,
           gl.ID AS idL, gl.anillo AS anilloL, gl.pesoReal AS pesoRealL, gl.frente AS frenteL, fl.nombre AS nombre_familiaL,
           gv.ID AS idV, gv.anillo AS anilloV, gv.pesoReal AS pesoRealV, gv.frente AS frenteV, fv.nombre AS nombre_familiaV
    FROM peleas p
    INNER JOIN gallos gl ON p.galloL = gl.ID
    INNER JOIN gallos gv ON p.galloV = gv.ID
    INNER JOIN familias fl ON gl.familiasId = fl.codigo
    INNER JOIN familias fv ON gv.familiasId = fv.codigo
    WHERE p.torneoId = :torneoId
    ORDER BY p.ID_Pelea ASC
');
$sentencia->bindValue(':torneoId', $torneoId, PDO::PARAM_INT);
$sentencia->execute();
$listaPeleas = $sentencia->fetchAll();

include __DIR__ . '/../../templates/header.subRep.php';
?>
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
    }
    h1, h2, p {
        text-align: center;
        margin: 0 0 10px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }
    th, td {
        border: 1px solid #bbb;
        padding: 6px;
        font-size: 10px;
        text-align: center;
    }
</style>

<h1><?php echo e($nombreTorneo); ?></h1>
<h2>Listado de peleas</h2>
<p>Fecha de impresion: <?php echo e(date('Y-m-d')); ?></p>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Anillo A</th>
            <th>Criadero A</th>
            <th>Peso A</th>
            <th>Anillo B</th>
            <th>Criadero B</th>
            <th>Peso B</th>
            <th>Estado</th>
            <th>Ganador</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($listaPeleas as $index => $pelea): ?>
            <tr>
                <td><?php echo e((string) ($index + 1)); ?></td>
                <td><?php echo e($pelea['anilloL']); ?></td>
                <td><?php echo e($pelea['nombre_familiaL']); ?></td>
                <td><?php echo e((string) $pelea['pesoRealL']); ?></td>
                <td><?php echo e($pelea['anilloV']); ?></td>
                <td><?php echo e($pelea['nombre_familiaV']); ?></td>
                <td><?php echo e((string) $pelea['pesoRealV']); ?></td>
                <td><?php echo e($pelea['estado']); ?></td>
                <td>
                    <?php
                        $ganador = '';
                        if ((string) $pelea['ganador'] === (string) $pelea['idL']) {
                            $ganador = $pelea['anilloL'];
                        } elseif ((string) $pelea['ganador'] === (string) $pelea['idV']) {
                            $ganador = $pelea['anilloV'];
                        }
                        echo e($ganador);
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
<?php
$html = ob_get_clean();

require_once __DIR__ . '/../../Libreria/dompdf/autoload.inc.php';

$dompdf = new Dompdf\Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('listaPeleas.pdf', ['Attachment' => false]);
