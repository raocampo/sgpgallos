<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include("../../bd.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$nombreTorneo = $_SESSION['nombreTorneo'];
$torneoId = $_SESSION['torneoId'];

// variables para presentar los registros
$item = 0;
$item1 = 0;
$item2 = 0;
$item3 = 0;
$gallosSeleccionados = array();

$sentencia = $conexion->prepare("SELECT COUNT(*) FROM gallos WHERE torneoId = :torneoId ");
$sentencia->bindParam(":torneoId", $torneoId);
$sentencia->execute();
$total_gallos = $sentencia->fetchColumn();

$toleranciaAltura = isset($_POST['altura']) ? $_POST['altura'] : null;
$toleranciaPeso = isset($_POST['peso']) ? $_POST['peso'] : null;

$cantidad_parejas = intval($total_gallos / 2);
if ($total_gallos % 2 != 0) {
    $total_gallos--;
}

if (isset($_GET['txtID'])) {

    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";

    $sentencia = $conexion->prepare("DELETE FROM coteja WHERE ID_Coteja=:id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
}

//Con esta sentencias seleccionamos los datos de la tabla de familias
$sentencia = $conexion->prepare("SELECT * FROM familias WHERE codigo");
$sentencia->execute();

$familias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

include("../../templates/header.sub.php");
?>

<div class="card container-fluid bg-transparent col-12 ">
    <div class="card-header ">
        <h3>Generar Cotejas</h3>
    </div>
    <!--INICIA SECCIÓN TOLERANCIA-->
    <section class=" container-fluid d-flex">
        <div class="d-flex flex-column col-6">
            <div class="card-body col-6">
                <!--INICIA FORMULARIO TOLERANCIA-->
                <form class="contenido_tolerancia" action="" enctype="multipart/form-data" method="post">
                    <div class=" card container container-fluid d-flex ">
                        <div class=" mt-5 ">
                            <span>Tolerancia</span>
                        </div>

                        <div class="d-flex">

                            <div class=" d-flex flex-column">

                                <div class="d-flex ">
                                    <input type="checkbox" name="medida" value="onzas" id="onzas">
                                    <label class=" m-1" for="">Peso</label>
                                </div>

                            </div>
                            <div class="container m-4">
                                <input class=" w-50" type="number" step="0.01" name="peso" id="peso" disabled>
                            </div>

                        </div>
                        <div class="d-flex">

                            <div class=" d-flex flex-column">

                                <div class="d-flex ">
                                    <input type="checkbox" name="medidaAltura" value="centimetros" id="centimetros">
                                    <label class=" m-1" for="">altura</label>
                                </div>

                            </div>
                            <div class="container m-4">
                                <input class=" w-50" type="number" step="0.01" name="altura" id="altura" disabled>
                            </div>

                        </div>

                    </div>
                    <div class="card container-fluid d-flex align-items-center justify-content-center col-12">
                        <div class="container mt-2">
                            <label for="">Gallos a cotejar</label>
                            <input class=" w-25 text-center" name="rescotejar" id="rescotejar" value="<?php echo $total_gallos; ?>" type="text">
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">Cotejar</button>
                        </div>
                    </div>
                </form><!--FIN FORMULARIO TOLERANCIA-->
            </div><!--FIN SECCIÓN TOLERANCIA MANUAL Y AUTOMATICA-->
            <!--INICIA DIV Y FORMULARIO DE COTEJA MANUAL-->
            <div class="card-body">
                <form id="formCotejoManual" action="" method="POST">
                    <div class=" card d-flex">
                        <?php
                        $sentencia = $conexion->prepare("SELECT gallos.ID, gallos.anillo, gallos.pesoReal, gallos.tamañoReal, gallos.placa, gallos.nacimiento, gallos.frente, familias.nombre AS nombre_familia, representante.nombreCompleto AS nombre_representante 
                        FROM gallos 
                        INNER JOIN familias ON gallos.familiasId = familias.codigo 
                        INNER JOIN representante ON gallos.representanteId = representante.ID
                        WHERE gallos.torneoId = :torneoId
                        ORDER BY pesoReal, tamañoReal ");
                        $sentencia->bindParam(":torneoId", $torneoId);
                        $sentencia->execute();

                        $lista_gallos = $sentencia->fetchAll(PDO::FETCH_ASSOC)
                        ?>
                        <?php
                        if (isset($_POST['cotejamiento']) && !empty($_POST['cotejamiento'])) {

                            $cotejamientoManual = $_POST['cotejamiento'];

                            // print_r($cotejamientoManual);

                            $sentencia = $conexion->prepare("INSERT INTO `coteja` (`galloL`, `galloV`, `estado`, `torneoId`) VALUES (:galloL, :galloV, :estado, :torneoId)");
                            $sentencia->bindParam(":torneoId", $torneoId);

                            $galloL = '';
                            $galloV = '';

                            foreach ($cotejamientoManual as $coteja) {
                                if ($galloL === '') {
                                    $galloL = $coteja;
                                } else {
                                    $galloV = $coteja;

                                    //Verficar si la pareja ya ha sido seleccionada
                                    if (parejaSeleccionada($galloL, $galloV, $conexion)) {
                                        echo "La pareja ya ha sido seleccionada previamente!!!";
                                        continue; //Saltar a la siguiente iteración del bucle
                                    }

                                    //$parejasSeleccionadas[] = [$galloL, $galloV];

                                    $sentencia->bindParam(":galloL", $galloL);
                                    $sentencia->bindParam(":galloV", $galloV);
                                    $sentencia->bindValue(":estado", 'Cotejado');
                                    $sentencia->execute();

                                    $galloL = '';
                                    $galloV = '';
                                }
                            }
                            //$gallosSeleccionados = $_POST['cotejamiento'];
                            //print_r($gallosSeleccionados);
                        }
                        //$gallosSeleccionados = $_POST['cotejamiento'];
                        function parejaSeleccionada($galloL, $galloV, $conexion)
                        {
                            $sentencia = $conexion->prepare("SELECT COUNT(*) FROM `coteja` WHERE (`galloL` = :galloL AND `galloV` = :galloV) OR (`galloL` = :galloV AND `galloV` = :galloL)");
                            $sentencia->bindParam(":galloL", $galloL);
                            $sentencia->bindParam(":galloV", $galloV);
                            $sentencia->execute();
                            $resultado = $sentencia->fetchColumn();
                            return ($resultado > 0); // Retorna true si la pareja ya ha sido seleccionada previamente
                        }
                        ?>
                        <div class=" d-flex justify-content-center">
                            <table class="table table-responsive-md  table-sm flex-fill text-center" id="tabla_id">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ITEM</th>
                                        <th>Anillo</th>
                                        <th>Gallo-Criadero</th>
                                        <th>AlturaL</th>
                                        <th>PesoL</th>
                                        <th>Seleccionar - Pareja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($lista_gallos as $gallo) {
                                    ?>
                                        <tr data-id="<?php echo $gallo['ID']; ?>">
                                            <td><?php echo $item += 1; ?></td>
                                            <td><?php echo $gallo['anillo']; ?></td>
                                            <td><?php echo $gallo['nombre_familia']; ?></td>
                                            <td><?php echo $gallo['pesoReal']; ?></td>
                                            <td><?php echo $gallo['tamañoReal'];                                        ?>
                                            <td><input type="checkbox" class="checkbox-pareja" name="cotejamiento[]" value="<?php echo $gallo['ID']; ?>"></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <div class="d-flex flex-column py-5">
                                <button type="submit" id="btnCotejaManual" class="btn btn-success m-2">Cotejar Manualmente</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div><!--FIN DE COTEJAMIENTO MANUAL-->
        </div>

        <!--SECCION DE ALGORITMO COTEJAMIENTO-->
        <div class="card-body container-fluid col-6">
            <!--INICIA FORMULARIO PARA SELECCIONAR COTEJAMIENTO-->
            <form class="contenido_tolerancia" action="peleaGenerada.php" enctype="multipart/form-data" method="post">
                <div class=" card d-flex row ">
                    <div class="card-header">
                        <span>Cotejas</span>
                    </div>

                    <div class=" d-flex flex-row">
                        <!--PRESENTAR COTEJA MANUAL-->
                        <table class="table table-responsive flex-fill text-center text-bg-light" id="tabla_id">
                            <thead class="table-primary">
                                <tr>
                                    <th>ITEM</th>
                                    <th>Anillo</th>
                                    <th>GalloL</th>
                                    <th>AlturaL</th>
                                    <th>PesoL</th>
                                    <th>Anillo</th>
                                    <th>GalloV</th>
                                    <th>AlturaV</th>
                                    <th>PesoV</th>
                                    <th>Seleccionar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sentencia = $conexion->prepare("SELECT c.ID_Coteja, c.galloL, c.galloV, c.estado,
                                        gl.anillo AS anilloL, gl.pesoReal AS pesoRealL, gl.tamañoReal AS tamañoRealL, fl.nombre AS nombre_familiaL,
                                        gv.anillo AS anilloV, gv.pesoReal AS pesoRealV, gv.tamañoReal AS tamañoRealV, fv.nombre AS nombre_familiaV
                                        FROM coteja c
                                        INNER JOIN gallos gl ON c.galloL = gl.ID
                                        INNER JOIN gallos gv ON c.galloV = gv.ID
                                        INNER JOIN familias fl ON gl.familiasId = fl.codigo
                                        INNER JOIN familias fv ON gv.familiasId = fv.codigo
                                        WHERE c.torneoId = :torneoId");
                                $sentencia->bindParam(":torneoId", $torneoId);
                                $sentencia->execute();
                                $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

                                ?>
                                <?php foreach ($resultado as $cotManual) { ?>
                                    <tr>
                                        <td><?php echo $item1 += 1; ?></td>
                                        <td><?php echo $cotManual['anilloL']; ?></td>
                                        <td><?php echo $cotManual['nombre_familiaL']; ?></td>
                                        <td><?php echo $cotManual['tamañoRealL']; ?></td>
                                        <td><?php echo $cotManual['pesoRealL']; ?></td>
                                        <td><?php echo $cotManual['anilloV']; ?></td>
                                        <td><?php echo $cotManual['nombre_familiaV']; ?></td>
                                        <td><?php echo $cotManual['tamañoRealV']; ?></td>
                                        <td><?php echo $cotManual['pesoRealV']; ?></td>
                                        <td><input type="checkbox" name="peleas[]" value="<?php echo $cotManual['ID_Coteja']; ?>"></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="d-flex flex-column">
                            <a name="" id="" class="btn btn-success" href="cotejamiento.php?txtID=<?php echo $cotManual['ID_Coteja']; ?>" role="button">Liberar</a>
                        </div>

                    </div>
                    <div class="d-flex flex-row">
                    <?php
                        $ids_cotejados = [];
                        $parejas = [];
                        $gallosLibres = [];

                        if ((!empty($toleranciaPeso) && $toleranciaPeso != 0) && (!empty($toleranciaAltura) && $toleranciaAltura != 0)) :
                            $gallos = $conexion->prepare("SELECT * FROM gallos WHERE familiasId <> 0 AND ID NOT IN (SELECT DISTINCT galloL FROM coteja WHERE torneoId =:torneoId) AND ID NOT IN(SELECT DISTINCT gallov FROM coteja WHERE torneoId =:torneoId) AND torneoId =:torneoId ORDER BY pesoReal");

                            $gallos->bindParam(":torneoId", $torneoId);
                            $gallos->execute();
                            $gallos = $gallos->fetchAll(PDO::FETCH_ASSOC);

                            $cantidadGallos = count($gallos);

                            // Matriz para almacenar las diferencias totales de peso y altura
                            $diferencias = array_fill(0, $cantidadGallos, array_fill(0, $cantidadGallos, null));

                            // Función auxiliar para calcular la diferencia total entre dos gallos
                            function calcularDiferencia($gallo1, $gallo2)
                            {
                                $difPeso = abs($gallo1['pesoReal'] - $gallo2['pesoReal']);
                                $difAltura = abs($gallo1['tamañoReal'] - $gallo2['tamañoReal']);
                                return $difPeso + $difAltura;
                            }

                            // Función auxiliar para encontrar la mejor pareja entre dos gallos
                            function encontrarMejorPareja($gallos, $indice1, $indice2, &$diferencias, &$ids_cotejados, $toleranciaPeso, $toleranciaAltura)
                            {
                                if ($indice1 >= $indice2) {
                                    return null;
                                }

                                if ($diferencias[$indice1][$indice2] !== null) {
                                    return $diferencias[$indice1][$indice2];
                                }

                                $mejorPareja = null;
                                $mejorParejaDiferencia = PHP_FLOAT_MAX;

                                for ($i = $indice1; $i < $indice2; $i++) {
                                    $gallo1 = $gallos[$i];

                                    if (in_array($gallo1['ID'], $ids_cotejados)) {
                                        continue;
                                    }

                                    for ($j = $i + 1; $j <= $indice2; $j++) {
                                        $gallo2 = $gallos[$j];

                                        if ($gallo1['familiasId'] == $gallo2['familiasId']) {
                                            continue;
                                        }

                                        if (in_array($gallo2['ID'], $ids_cotejados)) {
                                            continue;
                                        }

                                        $diferencia = calcularDiferencia($gallo1, $gallo2);

                                        if ($diferencia <= $toleranciaPeso + $toleranciaAltura) {
                                            if ($diferencia < $mejorParejaDiferencia) {
                                                $mejorPareja = [$gallo1, $gallo2];
                                                $mejorParejaDiferencia = $diferencia;
                                            }
                                        }
                                    }
                                }

                                $diferencias[$indice1][$indice2] = $mejorPareja;
                                return $mejorPareja;
                            }

                            // Función principal para encontrar las parejas utilizando programación dinámica
                            function encontrarParejas($gallos, $inicio, $fin, &$diferencias, &$ids_cotejados, $toleranciaPeso, $toleranciaAltura, &$parejas)
                            {
                                if ($inicio >= $fin) {
                                    return;
                                }

                                $mejorPareja = encontrarMejorPareja($gallos, $inicio, $fin, $diferencias, $ids_cotejados, $toleranciaPeso, $toleranciaAltura);

                                if ($mejorPareja !== null) {
                                    $parejas[] = $mejorPareja;
                                    $ids_cotejados[] = $mejorPareja[0]['ID'];
                                    $ids_cotejados[] = $mejorPareja[1]['ID'];

                                    $indice1 = array_search($mejorPareja[0], $gallos);
                                    $indice2 = array_search($mejorPareja[1], $gallos);

                                    encontrarParejas($gallos, $inicio, $indice1 - 1, $diferencias, $ids_cotejados, $toleranciaPeso, $toleranciaAltura, $parejas);
                                    encontrarParejas($gallos, $indice1 + 1, $indice2 - 1, $diferencias, $ids_cotejados, $toleranciaPeso, $toleranciaAltura, $parejas);
                                    encontrarParejas($gallos, $indice2 + 1, $fin, $diferencias, $ids_cotejados, $toleranciaPeso, $toleranciaAltura, $parejas);
                                } else {
                                    // Si no se encontró pareja para el rango, se agregan los gallos al listado de gallos libres
                                    for ($i = $inicio; $i <= $fin; $i++) {
                                        $gallosLibres[] = $gallos[$i];
                                    }
                                }
                            }

                            encontrarParejas($gallos, 0, $cantidadGallos - 1, $diferencias, $ids_cotejados, $toleranciaPeso, $toleranciaAltura, $parejas);

                            // Verificar si hay gallos libres y agregarlos al listado de parejas
                            if (!empty($gallosLibres)) {
                                foreach ($gallosLibres as $galloLibre) {
                                    $parejas[] = [$galloLibre, null];
                                }
                            }

                            /* Si no se encontró pareja para el gallo, se agrega a la lista de gallos libres
                            if (!$parejaEncontrada && !in_array($gallo1['ID'], $ids_cotejados)) {
                                $gallosLibres[] = $gallo1;
                            }*/

                            // Ordenar las parejas por pesoReal
                            usort($parejas, function ($a, $b) {
                                $peso1 = isset($a[0]) ? $a[0]['pesoReal'] : PHP_FLOAT_MAX;
                                $peso2 = isset($b[0]) ? $b[0]['pesoReal'] : PHP_FLOAT_MAX;

                                if ($peso1 == $peso2) {
                                    return 0;
                                }

                                return ($peso1 < $peso2) ? -1 : 1;
                            });
                        ?>


                            <table class="table table-responsive table-sm flex-fill text-center" id="tabla_id">
                                <!--<thead class="table-primary">
                                        <tr>
                                            <th>ITEM</th>
                                            <th>Anillo</th>
                                            <th>GalloL</th>
                                            <th>AlturaL</th>
                                            <th>PesoL</th>
                                            <th>Anillo</th>
                                            <th>GalloV</th>
                                            <th>AlturaV</th>
                                            <th>PesoV</th>
                                            <th>Seleccionar</th>
                                        </tr>
                                </thead>-->
                                <tbody>
                                    <?php foreach ($parejas as $pareja) :
                                        $gallo1 = $pareja[0];
                                        $gallo2 = $pareja[1];

                                        $familiasId1 = $gallo1['familiasId'];
                                        $familiasId2 = $gallo2['familiasId'];

                                        // Consulta para obtener los datos de la tabla 'familias'
                                        $query1 = $conexion->prepare("SELECT nombre FROM familias WHERE codigo = :familiasId");
                                        $query1->bindParam(':familiasId', $familiasId1);
                                        $query1->execute();
                                        $familia1 = $query1->fetch(PDO::FETCH_ASSOC);

                                        $query2 = $conexion->prepare("SELECT nombre FROM familias WHERE codigo = :familiasId");
                                        $query2->bindParam(':familiasId', $familiasId2);
                                        $query2->execute();
                                        $familia2 = $query2->fetch(PDO::FETCH_ASSOC);


                                    ?>
                                        <tr>
                                            <td><?php echo $item1 += 1; ?></td>
                                            <td><?php echo $gallo1['anillo']; ?></td>
                                            <td><?php echo $familia1['nombre']; ?></td>
                                            <td><?php echo $gallo1['tamañoReal']; ?></td>
                                            <td><?php echo $gallo1['pesoReal']; ?></td>
                                            <td><?php echo $gallo2['anillo']; ?></td>
                                            <td><?php echo $familia2['nombre']; ?></td>
                                            <td><?php echo $gallo2['tamañoReal']; ?></td>
                                            <td><?php echo $gallo2['pesoReal']; ?></td>
                                            <td><input type="checkbox" name="peleas[]" value="<?php echo $gallo1['ID'] . '-' . $gallo2['ID']; ?>"></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : $parejas = $conexion->prepare("SELECT g1.*, g2.*, familias.nombre AS nombre_familia FROM gallos g1
                            INNER JOIN gallos g2 ON g1.id <> g2.id AND (g1.pesoReal = g2.pesoReal AND g1.tamañoReal = g2.tamañoReal)
                            INNER JOIN familias ON g1.familiasId <> g2.familiasId AND g1.familiasId = familias.codigo WHERE g1.torneoId = :torneoId AND g2.torneoId = :torneoId 
                            ORDER BY g1.pesoReal ASC LIMIT $total_gallos");

                            $parejas->bindParam(":torneoId", $torneoId);
                            $parejas->execute();
                            $parejas = $parejas->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                            <table class="table table-responsive table-sm flex-fill text-center text-bg-dark " id="tabla_id">
                                <!--<thead class="table-primary">
                                        <tr>
                                            <th>ITEM</th>
                                            <th>Anillo</th>
                                            <th>GalloL</th>
                                            <th>TamañoL</th>
                                            <th>PesoL</th>
                                            <th>Anillo</th>
                                            <th>GalloV</th>
                                            <th>TamañoV</th>
                                            <th>PesoV</th>
                                            <th>Seleccionar</th>
                                        </tr>
                                    </thead>-->
                                <tbody>
                                    <?php
                                    for ($i = 0; $i < count($parejas); $i += 2) :
                                        $gallo1 = $parejas[$i][0];
                                        $gallo2 = $parejas[$i + 1][0];
                                    ?>
                                        <tr>
                                            <td><?php echo $item1 += 1; ?></td>
                                            <td><?php echo $gallo1['anillo']; ?></td>
                                            <td><?php echo $gallo1['nombre_familia']; ?></td>
                                            <td><?php echo $gallo1['tamañoReal']; ?></td>
                                            <td><?php echo $gallo1['pesoReal']; ?></td>
                                            <td><?php echo $gallo2['anillo']; ?></td>
                                            <td><?php echo $gallo2['nombre_familia']; ?></td>
                                            <td><?php echo $gallo2['tamañoReal']; ?></td>
                                            <td><?php echo $gallo2['pesoReal']; ?></td>
                                            <td><input type="checkbox" name="peleas[]" value="<?php echo $gallo1['ID'] . '-' . $gallo2['ID']; ?>"></td>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                        <div class="d-flex flex-column">
                            <input type="hidden" name="peleaGenerada" value="1">
                            <button name="peleasGenerada" type="submit" class="btn btn-success m-2">Pactar Peleas</button>
                            <!--<button type="submit" class="btn btn-success m-2">Liberar Peleas</button>-->
                            <!--<button type="submit" class="btn btn-success m-2">Imprimir</button>-->
                        </div>

                    </div>
                </div>
            </form>
            <!--SECCIÓN DE GALLOS LIBRES-->
            <div class="card d-flex">

                <div class="card-header">
                    <span>Gallos Libres</span>
                </div>

                <div class="d-flex flex-row">
                    <form action="">
                        <div class="d-flex">
                            <table class="table table-responsive-md table-sm" id="tabla_id">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ITEM</th>
                                        <th>Anillo</th>
                                        <th>Criadero</th>
                                        <th>Tamaño</th>
                                        <th>Peso</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($gallosLibres as $galloLibre) :

                                        $familiasId1 = $galloLibre['familiasId'];
                                        // Consulta para obtener los datos de la tabla 'familias'
                                        $query1 = $conexion->prepare("SELECT nombre FROM familias WHERE codigo = :familiasId");
                                        $query1->bindParam(':familiasId', $familiasId1);
                                        $query1->execute();
                                        $familia1 = $query1->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                        <tr>
                                            <td><?php echo $item2 += 1;
                                                ?></td>
                                            <td><?php echo $galloLibre['anillo'];
                                                ?></td>
                                            <td><?php echo $familia1['nombre'];
                                                ?></td>
                                            <td><?php echo $galloLibre['tamañoReal'];
                                                ?></td>
                                            <td><?php echo $galloLibre['pesoReal'];
                                                ?></td>
                                            <td><input type="checkbox" name="peleas[]" value="<?php echo $parejas['ID_Coteja'];
                                                                                                                          ?>"></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="d-flex flex-column ">
                                <button type="submit" class="btn btn-success m-2">Cotejar Manualmente</button>
                                <button type="submit" class="btn btn-success m-2">Imprimir</button>
                            </div>
                        </div>
                    </form>

                </div>

            </div>





        </div>

    </section>

</div>

<?php include("../../templates/footer.php"); ?>