<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include("../../bd.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$nombreTorneo = $_SESSION['nombreTorneo'];
$torneoId = $_SESSION['torneoId'];

$item = 0;
$parejas = [];

// Verificar si se ha enviado el formulario
if (isset($_POST['peleaGenerada'])) {

    // Verificar si se seleccionaron peleas
    if (!empty($_POST['peleas'])) {

        // Se han seleccionado peleas, procesar los datos.
        $peleasSeleccionadas = $_POST['peleas'];

        foreach ($peleasSeleccionadas as $pelea) {
            // Verificar si es un checkbox de ID_Coteja o de gallo1-gallo2
            if (strpos($pelea, '-') !== false) {
                // Es un checkbox de gallo1-gallo2
                $idsGallos = explode('-', $pelea);
                $galloL = $idsGallos[0];
                $galloV = $idsGallos[1];
            } else {
                // Es un checkbox de ID_Coteja
                $idCoteja = $pelea;

                // Obtener los IDs de los gallos correspondientes al ID_Coteja
                $sentenciaCoteja = $conexion->prepare("SELECT galloL, galloV FROM coteja WHERE ID_Coteja = :idCoteja");
                $sentenciaCoteja->bindParam(":idCoteja", $idCoteja);
                $sentenciaCoteja->execute();
                $coteja = $sentenciaCoteja->fetch(PDO::FETCH_ASSOC);

                if ($coteja) {
                    $galloL = $coteja['galloL'];
                    $galloV = $coteja['galloV'];
                } else {
                    // Manejar el caso de error o no se encontró el ID_Coteja
                    continue; // Saltar al siguiente bucle
                }
            }

            // Verificar si la pelea ya existe en la base de datos
            $sentenciaExistePelea = $conexion->prepare("SELECT COUNT(*) FROM peleas WHERE galloL = :galloL AND galloV = :galloV AND torneoId = :torneoId");
            $sentenciaExistePelea->bindParam(":galloL", $galloL);
            $sentenciaExistePelea->bindParam(":galloV", $galloV);
            $sentenciaExistePelea->bindParam(":torneoId", $torneoId);
            $sentenciaExistePelea->execute();
            $cantidadPeleas = $sentenciaExistePelea->fetchColumn();

            if ($cantidadPeleas == 0) {
                // La pelea no existe en la base de datos, proceder a insertarla

                // Realizar la inserción en la tabla "peleas"
                $sentencia = $conexion->prepare("INSERT INTO peleas (galloL, galloV, torneoId) VALUES (:galloL, :galloV, :torneoId)");
                $sentencia->bindParam(':galloL', $galloL);
                $sentencia->bindParam(':galloV', $galloV);
                $sentencia->bindParam(':torneoId', $torneoId);
                $sentencia->execute();
            }
        }

        // Redireccionar o mostrar un mensaje de éxito, según lo necesites
        // header('Location: peleas_guardadas.php');
        // echo "Las peleas se han guardado exitosamente.";
    } else {
        // No se han seleccionados peleas.
        echo "No se han seleccionado peleas";
    }
}

if (isset($_GET['txtID'])) {

    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";

    $sentencia = $conexion->prepare("DELETE FROM peleas WHERE ID_Pelea=:id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
}

include("../../templates/header.sub.php");
?>

<script>
// Función para imprimir el contenido de la tabla
function imprimirTabla() {
    window.print();
}
</script>

<div class="card container-fluid bg-transparent ">
    <div class="card-header">
        <h3>PELEAS PACTADAS <?php echo $nombreTorneo; ?> </h3>
    </div>
    <section class="card">
        <form class="contenido_tolerancia" id="formularioPeleas" action="" enctype="multipart/form-data" method="post">

            <div class="card-body d-flex">
                <table class=" table table-bordered table-responsive-md  table-sm flex-fill text-center" >
                    <thead class="table-primary ">
                        <tr>
                            <th>Codigo</th>
                            <th>Anillo</th>
                            <th>Criadero</th>
                            <th>Tamaño Real</th>
                            <th>Peso Real</th>
                            <th></th>
                            <th>Anillo</th>
                            <th>Criadero</th>
                            <th>Tamaño Real</th>
                            <th>Peso Real</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sentencia = $conexion->prepare("SELECT p.ID_Pelea, p.galloL, p.galloV,                                         gl.anillo AS anilloL, gl.pesoReal AS pesoRealL, gl.tamañoReal AS tamañoRealL, fl.nombre AS nombre_familiaL,
                                        gv.anillo AS anilloV, gv.pesoReal AS pesoRealV, gv.tamañoReal AS tamañoRealV, fv.nombre AS nombre_familiaV
                                        FROM peleas p
                                        INNER JOIN gallos gl ON p.galloL = gl.ID
                                        INNER JOIN gallos gv ON p.galloV = gv.ID
                                        INNER JOIN familias fl ON gl.familiasId = fl.codigo
                                        INNER JOIN familias fv ON gv.familiasId = fv.codigo
                                        WHERE p.torneoId = :torneoId");
                        $sentencia->bindParam(":torneoId", $torneoId);
                        $sentencia->execute();
                        $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

                        ?>
                        <?php foreach ($resultado as $pelea) { ?>
                            <tr>
                                <td><?php echo $item += 1; ?></td>
                                <td><?php echo $pelea['anilloL']; ?></td>
                                <td><?php echo $pelea['nombre_familiaL']; ?></td>
                                <td><?php echo $pelea['tamañoRealL']; ?></td>
                                <td><?php echo $pelea['pesoRealL']; ?></td>
                                <td><span>VS</span></td>
                                <td><?php echo $pelea['anilloV']; ?></td>
                                <td><?php echo $pelea['nombre_familiaV']; ?></td>
                                <td><?php echo $pelea['tamañoRealV']; ?></td>
                                <td><?php echo $pelea['pesoRealV']; ?></td>
                                <td>
                                  <a name="" id="" class="btn btn-success" href="peleaGenerada.php?txtID=<?php echo $pelea['ID_Pelea']; ?>" role="button">Liberar</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex flex-row ">
                <a name="" id="" class="btn btn-success m-2" href="resultados.php" role="button">Resultados</a>
                <button id="btnImprimir" type="button" class="btn btn-success m-2" onclick="imprimirTabla()">Imprimir</button>
            </div>
        </form>
    </section>
</div>

<?php include("../../templates/footer.php"); ?>