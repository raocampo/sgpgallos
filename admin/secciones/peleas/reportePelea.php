<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include("../../bd.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ob_start();

$item = 0;
$nombreTorneo = $_SESSION['nombreTorneo'];
$torneoId = $_SESSION['torneoId'];

$sentencia = $conexion->prepare("SELECT p.ID_Pelea, p.galloL, p.galloV, 
                gl.anillo AS anilloL, gl.pesoReal AS pesoRealL, gl.frente AS frenteL, fl.nombre AS nombre_familiaL, gv.anillo AS anilloV, gv.pesoReal AS pesoRealV, gv.frente AS frenteV, fv.nombre AS nombre_familiaV FROM peleas p
                INNER JOIN gallos gl ON p.galloL = gl.ID
                INNER JOIN gallos gv ON p.galloV = gv.ID
                INNER JOIN familias fl ON gl.familiasId = fl.codigo
                INNER JOIN familias fv ON gv.familiasId = fv.codigo
                WHERE p.torneoId = :torneoId ORDER BY RAND()");
$sentencia->bindParam(":torneoId", $torneoId);
$sentencia->execute();
$listaPeleas = $sentencia->fetchAll(PDO::FETCH_ASSOC);


include("../../templates/header.subRep.php");

?>

<!-- Agregar estilos CSS para el logo y la tabla -->
<style>
    h3 p{
        
        font-family: 'Courier New', Courier, monospace;
        font-size: 14px;
    }
    .logo {
        margin-top: 0;
        width: 240px;
        height: 80px;
    }

    table {
        margin-top: 20px;
        width: 100%;
        border-collapse: collapse;
        font-family: 'Courier New', Courier, monospace;
        
    }

    th, td {
        padding: 5px;
        border: 1px solid #ccc;
        font-size: 10px;
    }
</style>

<h3 class="text-center">LISTADO DE PELEAS</h3>
<p><?php setlocale(LC_TIME, 'es_ES.UTF-8');
     echo date("d-m-Y");?></p>

<table class="text-center">
    <thead>
        <tr>
            <th>Pelea #</th>
            <th>Anillo</th>
            <th>Criadero</th>
            <th>Frente</th>
            <th>Peso</th>
            <th></th>
            <th>Anillo</th>
            <th>Criadero</th>
            <th>Frente</th>
            <th>Peso</th>
            <th>RESULTADO</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($listaPeleas as $pelea) { ?>
            <tr>
                <td><?php echo $item += 1; ?></td>
                <td><?php echo $pelea['anilloL']; ?></td>
                <td><?php echo $pelea['nombre_familiaL']; ?></td>
                <td><?php echo $pelea['frenteL']; ?></td>
                <td><?php echo $pelea['pesoRealL']; ?></td>
                <td><span>VS</span></td>
                <td><?php echo $pelea['anilloV']; ?></td>
                <td><?php echo $pelea['nombre_familiaV']; ?></td>
                <td><?php echo $pelea['frenteV']; ?></td>
                <td><?php echo $pelea['pesoRealV']; ?></td>
                <td></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php 
$html = ob_get_clean();

require_once '../../Libreria/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
$dompdf = new Dompdf();

$options = $dompdf->getOptions();
$options->set(array('isRemoteEnabled' => true));
$dompdf->setOptions($options);

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream("listaPelea.pdf", array("Attachment" => false));

?>
