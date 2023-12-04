<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include("../../bd.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si la variable de sesión 'parejasCotejadas' no está definida
if (!isset($_SESSION['parejasCotejadas'])) {
    $_SESSION['parejasCotejadas'] = array(); // Inicializar como un arreglo vacío
}

$nombreTorneo = $_SESSION['nombreTorneo'];
$torneoId = $_SESSION['torneoId'];

// variables para presentar los registros
$item = 0;
$item1 = 0;
$item2 = 0;
$gallosSeleccionados = array();
$parejaEncontradaTolerancia;

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

//Se elimina la pareja cotejada manualmente de la tabla coteja
//Se activa el checkbox para volver a seleccionar
if (isset($_GET['txtID'])) {
    // Obtener los IDs de los gallos involucrados en la pareja cotejada
    $sentenciaID = $conexion->prepare("SELECT galloL, gallov FROM coteja WHERE ID_Coteja=:id");
    $sentenciaID->bindParam(":id", $_GET['txtID']);
    $sentenciaID->execute();
    $parejaIDs = $sentenciaID->fetch(PDO::FETCH_ASSOC);

    // Realizar la eliminación de la pareja cotejada según el ID recibido
    $txtID = $_GET['txtID'];

    $sentencia = $conexion->prepare("DELETE FROM coteja WHERE ID_Coteja=:id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();

    // Verificar y eliminar los IDs de los gallos de $_SESSION['parejasCotejadas'] si están presentes
    $galloL_ID = $parejaIDs['galloL'];
    $gallov_ID = $parejaIDs['gallov'];

    $index_galloL = array_search($galloL_ID, $_SESSION['parejasCotejadas']);
    if ($index_galloL !== false) {
        unset($_SESSION['parejasCotejadas'][$index_galloL]);
    }

    $index_gallov = array_search($gallov_ID, $_SESSION['parejasCotejadas']);
    if ($index_gallov !== false) {
        unset($_SESSION['parejasCotejadas'][$index_gallov]);
    }
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
            <div class="card-body col-8 m-auto">
                <!--INICIA FORMULARIO TOLERANCIA-->
                <form class="contenido_tolerancia" action="" enctype="multipart/form-data" method="post">
                    <div class=" card container container-fluid d-flex text-center">
                        <div class=" mt-2 ">
                            <h5>Tolerancia</h5>
                        </div>

                        <div class="d-flex">

                            <div class=" d-flex flex-column text-center justify-content-center mx-3">

                                <div class="d-flex justify-content-center mt-4 ">
                                    <input type="checkbox" name="medida" value="onzas" id="onzas">
                                    <label class="mx-1" for="">Peso</label>
                                    <input class=" w-25 text-center" type="number" step="0.01" name="peso" id="peso" disabled>
                                </div>

                            </div>

                        </div>
                        <div class="d-flex ">

                            <div class=" d-flex flex-column text-center justify-content-center mx-3">

                                <div class="d-flex justify-content-center mt-4 ">
                                    <input type="checkbox" name="medidaAltura" value="centimetros" id="centimetros">
                                    <label class=" mx-1" for="">altura</label>
                                    <input class=" w-25 text-center" type="number" step="0.01" name="altura" id="altura" disabled>
                                </div>

                            </div>

                        </div>

                        <div class="dflex flex-row align-items-center">
                            <div class="d-flex justify-content-center">
                                <div class="mx-3">
                                    <label class="m-1" for="nacimiento">Mes de nacimiento</label>
                                    <input type="checkbox" name="nacimiento" id="nacimiento">
                                </div>

                                <div><label class="m-1" for="exclusion">Exclusiones</label>
                                    <input type="checkbox" name="exclusion" id="exclusion">
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="card container-fluid d-flex align-items-center justify-content-center col-12">
                        <div class="container mt-3 text-center">
                            <label for="">Gallos a cotejar</label>
                            <input class=" w-25 text-center" name="rescotejar" id="rescotejar" value="<?php echo $total_gallos; ?>" type="text">
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-success mt-2 mb-3">Cotejar</button>
                        </div>
                    </div>
                </form><!--FIN FORMULARIO TOLERANCIA-->
            </div><!--FIN SECCIÓN TOLERANCIA MANUAL Y AUTOMATICA-->
            <!--INICIA DIV Y FORMULARIO DE COTEJA MANUAL-->
            <div class="card mx-0 container-fluid">
                <div class="card-header">
                    <span>Coteja Manual</span>
                </div>
                <form id="formCotejoManual" action="" method="POST">
                    <div class="d-flex">
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
                                        echo "<script>alert ('La pareja ya ha sido seleccionada previamente!!!');</script>";
                                        continue; //Saltar a la siguiente iteración del bucle
                                    }

                                    $sentencia->bindParam(":galloL", $galloL);
                                    $sentencia->bindParam(":galloV", $galloV);
                                    $sentencia->bindValue(":estado", 'Cotejado');
                                    $sentencia->execute();

                                    // Agregar las parejas cotejadas a la variable de sesión
                                    $_SESSION['parejasCotejadas'][] = $galloL;
                                    $_SESSION['parejasCotejadas'][] = $galloV;

                                    $galloL = '';
                                    $galloV = '';
                                }
                            }
                        }

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
                        <div class=" d-flex flex-column justify-content-center text-center table-responsive overflow-auto">
                            <div class="text-center">
                                <button type="submit" id="btnCotejaManual" class="btn btn-success m-2">Cotejar Manualmente</button>
                            </div>
                            <table class="table flex-fill text-center" id="tabla_id">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ITEM</th>
                                        <th>Anillo</th>
                                        <th>Criadero</th>
                                        <th>Peso</th>
                                        <th>Altura</th>
                                        <th>Frente</th>
                                        <th>Mes Nac</th>
                                        <th>Seleccionar Pareja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($lista_gallos as $gallo) {
                                        $galloID = $gallo['ID'];
                                        $checkboxDisabled = in_array($galloID, $_SESSION['parejasCotejadas']);
                                    ?>
                                        <tr data-id="<?php echo $gallo['ID']; ?>">
                                            <td><?php echo $item += 1; ?></td>
                                            <td><?php echo $gallo['anillo']; ?></td>
                                            <td><?php echo $gallo['nombre_familia']; ?></td>
                                            <td><?php echo $gallo['pesoReal']; ?></td>
                                            <td><?php echo $gallo['tamañoReal']; ?></td>
                                            <td><?php echo $gallo['frente']; ?></td>
                                            <td><?php echo $gallo['nacimiento']; ?></td>
                                            <td><input type="checkbox" class="checkbox-pareja" name="cotejamiento[]" value="<?php echo $gallo['ID']; ?>" <?php if ($checkboxDisabled) {
                                                                                                                                                                echo 'disabled';
                                                                                                                                                            } ?>></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div><!--FIN DE COTEJAMIENTO MANUAL-->
        </div>

        <!--SECCION DE ALGORITMO COTEJAMIENTO-->
        <div class="card-body container-fluid col-6">
            <!--INICIA FORMULARIO PARA SELECCIONAR COTEJAMIENTO-->
            <form class="contenido_tolerancia" action="peleaGenerada.php" enctype="multipart/form-data" method="post">
                <div class=" card d-flex flex-column">
                    <div class="card-header">
                        <span>Cotejas</span>
                    </div>

                    <div class="card d-flex table-responsive col-auto overflow-auto">

                        <div class="d-flex flex-row justify-content-center">
                            <div class="text-center">
                                <input type="hidden" name="peleaGenerada" value="1">
                                <button name="peleasGenerada" type="submit" class="btn btn-success m-2">Pactar Peleas</button>
                            </div>
                        </div>

                        <!--PRESENTAR COTEJA MANUAL-->
                        <table class="table flex-fill text-center text-bg-light">
                            <thead class="table-primary">
                                <tr>
                                    <th>ITEM</th>
                                    <th>AnilloL</th>
                                    <th>GalloL</th>
                                    <th>AlturaL</th>
                                    <th>PesoL</th>
                                    <th>FrenteL</th>
                                    <th>MesNacL</th>
                                    <th>AnilloV</th>
                                    <th>GalloV</th>
                                    <th>AlturaV</th>
                                    <th>PesoV</th>
                                    <th>FrenteV</th>
                                    <th>MesNacV</th>
                                    <th>Seleccionar</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sentencia = $conexion->prepare("SELECT c.ID_Coteja, c.galloL, c.galloV, c.estado,
                                        gl.anillo AS anilloL, gl.pesoReal AS pesoRealL, gl.tamañoReal AS tamañoRealL, gl.frente AS frenteL, gl.nacimiento AS nacimientoL, fl.nombre AS nombre_familiaL,
                                        gv.anillo AS anilloV, gv.pesoReal AS pesoRealV, gv.tamañoReal AS tamañoRealV, gv.frente AS frenteV, gv.nacimiento AS nacimientoV, fv.nombre AS nombre_familiaV
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
                                        <td><?php echo $cotManual['frenteL']; ?></td>
                                        <td><?php echo $cotManual['nacimientoL']; ?></td>
                                        <td><?php echo $cotManual['anilloV']; ?></td>
                                        <td><?php echo $cotManual['nombre_familiaV']; ?></td>
                                        <td><?php echo $cotManual['tamañoRealV']; ?></td>
                                        <td><?php echo $cotManual['pesoRealV']; ?></td>
                                        <td><?php echo $cotManual['frenteV']; ?></td>
                                        <td><?php echo $cotManual['nacimientoV']; ?></td>
                                        <td><input type="checkbox" name="peleas[]" value="<?php echo $cotManual['ID_Coteja']; ?>"></td>
                                        <td><a name="coteja" id="" href="cotejamiento.php?txtID=<?php echo  $cotManual['ID_Coteja']; ?>"><i class="fa-solid fa-trash-can"></i></a></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="card d-flex flex-row table-responsive overflow-auto">
                        <?php
                        $ids_cotejados = [];
                        $parejas = [];
                        $gallosLibres = [];
                        $familiaID1 = [];
                        $familiaID2 = [];

                        if ((!empty($toleranciaPeso) && $toleranciaPeso != 0) && (!empty($toleranciaAltura) && $toleranciaAltura != 0)) :
                            $gallos = $conexion->prepare("SELECT * FROM gallos
                                WHERE familiasId <> 0
                                    AND ID NOT IN (
                                        SELECT DISTINCT galloL FROM coteja WHERE torneoId = :torneoId
                                        UNION
                                        SELECT DISTINCT gallov FROM coteja WHERE torneoId = :torneoId
                                    )
                                    AND torneoId = :torneoId
                                ORDER BY pesoReal, tamañoReal
                            ");
                            $gallos->bindParam(":torneoId", $torneoId);
                            $gallos->execute();
                            $gallos = $gallos->fetchAll(PDO::FETCH_ASSOC);

                            $cantidadGallos = count($gallos);

                            $nacimiento = isset($_POST['nacimiento']) ? true : false;
                            $exclusion = isset($_POST['exclusion']) ? true : false;

                            $excluidas = [];

                            if ($exclusion) {
                                $sentencia = $conexion->prepare("SELECT nombreFamiliaUno, nombreFamiliaDos FROM exclusiones WHERE torneoId = :torneoId");
                                $sentencia->bindParam(":torneoId", $torneoId);
                                $sentencia->execute();
                                $exclusiones = $sentencia->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($exclusiones as $exclusion) {
                                    $nombreFamiliaUno = $exclusion['nombreFamiliaUno'];
                                    $nombreFamiliaDos = $exclusion['nombreFamiliaDos'];

                                    $excluidas[$nombreFamiliaUno] = true;
                                    $excluidas[$nombreFamiliaDos] = true;
                                }
                            }

                            for ($index1 = 0; $index1 < $cantidadGallos; $index1++) {
                                $gallo1 = $gallos[$index1];
                                $familiaID1 = $gallo1['familiasId'];
                                $galloPeso1 = round($gallo1['pesoReal'], 2); // Redondea a 2 decimales
                                $galloAltura1 = round($gallo1['tamañoReal'], 2); // Redondea a 2 decimales
                                $galloNacimiento1 = intval($gallo1['nacimiento']);

                                // Verificar si el gallo ya fue cotejado manualmente
                                if (in_array($gallo1['ID'], $gallosSeleccionados)) {
                                    continue;
                                }

                                if ($exclusion && isset($excluidas[$familiaID1])) {
                                    //$gallosLibres[] = $gallo1;
                                    continue;
                                }
                                $parejaEncontrada = false;
                                $mejorDiferenciaPeso = $toleranciaPeso; // Inicializar con la máxima tolerancia de peso
                                $mejorPareja = null;

                                for ($index2 = $index1 + 1; $index2 < $cantidadGallos; $index2++) {
                                    $gallo2 = $gallos[$index2];

                                    if ($gallo1['familiasId'] == $gallo2['familiasId']) {
                                        continue;
                                    }

                                    $galloPeso2 = round($gallo2['pesoReal'], 2); // Redondea a 2 decimales
                                    $galloAltura2 = round($gallo2['tamañoReal'], 2); // Redondea a 2 decimales
                                    $galloNacimiento2 = intval($gallo2['nacimiento']);

                                    if ($exclusion && (in_array($familiaID2, $excluidas) || in_array($familiaID1, $excluidas))) {
                                        continue;
                                    }

                                    $diferenciaPeso = abs($galloPeso1 - $galloPeso2);
                                    $diferenciaAltura = abs($galloAltura1 - $galloAltura2);

                                    if (
                                        $diferenciaPeso <= $toleranciaPeso
                                        && $diferenciaAltura <= $toleranciaAltura
                                        && $diferenciaPeso < $mejorDiferenciaPeso
                                        && !in_array($gallo1['ID'], $ids_cotejados)
                                        && !in_array($gallo2['ID'], $ids_cotejados)
                                        && (!$nacimiento || ($galloNacimiento1 == $galloNacimiento2
                                            || (abs(intval($galloNacimiento1) - intval($galloNacimiento2)) == 1 && intval($galloNacimiento1) < intval($galloNacimiento2))
                                        ))
                                    ) {
                                        $mejorDiferenciaPeso = $diferenciaPeso;
                                        $mejorPareja = [$gallo1, $gallo2];
                                        $parejaEncontrada = true;
                                    }
                                }

                                if ($parejaEncontrada) {
                                    $parejas[] = $mejorPareja;
                                    $ids_cotejados[] = $mejorPareja[0]['ID'];
                                    $ids_cotejados[] = $mejorPareja[1]['ID'];

                                    //$index1 = 0;
                                    //$parejaEncontradaTolerancia = true;
                                    //break; //Salir del bucle al encontrar pareja
                                } elseif (!in_array($gallo1['ID'], $ids_cotejados)) {
                                    $gallosLibres[] = $gallo1;
                                }/*elseif (!in_array($gallo1['ID'], $ids_cotejados)) {
                                    if(!$parejaEncontradaTolerancia){
                                        $gallosLibres[] = $gallo1;
                                    }
                                    
                                }*/
                            }

                            /* Asignar los gallos no cotejados a gallosLibres
                            foreach ($gallos as $gallo) {
                                if (!in_array($gallo['ID'], $ids_cotejados)) {
                                    $gallosLibres[] = $gallo;
                                }
                            }*/

                            // Ordenar las parejas por pesoReal
                            usort($parejas, function ($a, $b) {
                                $peso1 = $a[0]['pesoReal'];
                                $peso2 = $b[0]['pesoReal'];

                                if ($peso1 == $peso2) {
                                    return 0;
                                }

                                return ($peso1 < $peso2) ? -1 : 1;
                            });
                        ?>


                            <table class="table table-sm flex-fill text-center" id="tabla_id">
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
                                            <td><?php echo $gallo1['frente']; ?></td>
                                            <td><?php echo $gallo1['nacimiento']; ?></td>
                                            <td><?php echo $gallo2['anillo']; ?></td>
                                            <td><?php echo $familia2['nombre']; ?></td>
                                            <td><?php echo $gallo2['tamañoReal']; ?></td>
                                            <td><?php echo $gallo2['pesoReal']; ?></td>
                                            <td><?php echo $gallo2['frente']; ?></td>
                                            <td><?php echo $gallo2['nacimiento']; ?></td>
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
                            <table class="table table-sm flex-fill text-center text-bg-dark ">
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
                                        $gallo1 = $parejas[$i];
                                        $gallo2 = $parejas[$i + 1];
                                    ?>
                                        <tr>
                                            <td><?php echo $item1 += 1; ?></td>
                                            <td><?php echo $gallo1['anillo']; ?></td>
                                            <td><?php echo $gallo1['nombre_familia']; ?></td>
                                            <td><?php echo $gallo1['tamañoReal']; ?></td>
                                            <td><?php echo $gallo1['pesoReal']; ?></td>
                                            <td><?php echo $gallo1['frente']; ?></td>
                                            <td><?php echo $gallo1['nacimiento']; ?></td>
                                            <td><?php echo $gallo2['anillo']; ?></td>
                                            <td><?php echo $gallo2['nombre_familia']; ?></td>
                                            <td><?php echo $gallo2['tamañoReal']; ?></td>
                                            <td><?php echo $gallo2['pesoReal']; ?></td>
                                            <td><?php echo $gallo2['frente']; ?></td>
                                            <td><?php echo $gallo2['nacimiento']; ?></td>
                                            <td><input type="checkbox" name="peleas[]" value="<?php echo $gallo1['ID'] . '-' . $gallo2['ID']; ?>"></td>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                        <!--<div class="d-flex flex-column">
                            <input type="hidden" name="peleaGenerada" value="1">
                            <button name="peleasGenerada" type="submit" class="btn btn-success m-2">Pactar Peleas</button>
                            <button type="submit" class="btn btn-success m-2">Liberar Peleas</button>
                            <button type="submit" class="btn btn-success m-2">Imprimir</button>
                    </div>-->

                    </div>
                </div>
            </form>
            <!--SECCIÓN DE GALLOS LIBRES-->
            <div class="card container-fluid d-flex justify-content-center">

                <div class="card-header">
                    <span>Gallos Libres</span>
                </div>

                <div class="d-flex container-fluid ">
                    <form class="contenidoGallosLibres" action="" enctype="multipart/form-data" method="post">

                        <?php
                        /* Obtener los gallos seleccionados
                            if (isset($_POST['gallos']) && !empty($_POST['gallos'])) {
                                print_r($_POST);
                            $sentencia = $conexion->prepare("INSERT INTO `coteja` (`galloL`, `galloV`, `estado`, `torneoId`) VALUES (:galloL, :galloV, :estado, :torneoId)");
                            $sentencia->bindParam(":torneoId", $torneoId);

                            $galloL = '';
                            $galloV = '';
                           }*/

                        // Verificar si se han enviado los IDs de los gallos seleccionados
                        if (isset($_POST['gallos']) && !empty($_POST['gallos'])) {
                            // Obtener los IDs de los gallos seleccionados
                            $galloIds = $_POST['gallos'];

                            // Verificar que se hayan seleccionado dos gallos
                            if (count($galloIds) !== 2) {
                                echo "Debe seleccionar exactamente 2 gallos para poder realizar la coteja manual.";
                                exit();
                            }

                            // Obtener los IDs de los gallos seleccionados
                            $galloL = $galloIds[0];
                            $galloV = $galloIds[1];

                            // Insertar los gallos en la tabla coteja
                            $sentencia = $conexion->prepare("INSERT INTO `coteja` (`galloL`, `galloV`, `estado`, `torneoId`) VALUES (:galloL, :galloV, :estado, :torneoId)");
                            $estado = 0; // Opcional: Define el valor del campo "estado" según tus requerimientos
                            $sentencia->bindParam(":galloL", $galloL);
                            $sentencia->bindParam(":galloV", $galloV);
                            $sentencia->bindParam(":estado", $estado);
                            $sentencia->bindParam(":torneoId", $torneoId); // Asegúrate de tener $torneoId definido previamente

                            // Ejecutar la consulta
                            if ($sentencia->execute()) {
                                echo "Los gallos se cotejaron exitosamente.";
                            } /*else {
                                //echo "Error al cotejar los gallos. Por favor, inténtalo de nuevo.";
                            }*/
                        } /*else {
                            echo "Debe seleccionar exactamente 2 gallos para poder realizar la coteja manual.";
                        }*/
                        ?>
                        <div class="d-flex text-center justify-content-center">
                            <button name="cotManual" type="button" class="btn btn-success btn-cotejar-manual m-2">Coteja Manual</button>
                        </div>
                        <div class="d-flex col-auto table-responsive">

                            <table class="table flex-fill text-center">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ITEM</th>
                                        <th>Anillo</th>
                                        <th>Criadero</th>
                                        <th>Tamaño</th>
                                        <th>Peso</th>
                                        <th>Frente</th>
                                        <th>Mes Nac</th>
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
                                            <td><?php echo $galloLibre['frente'];
                                                ?></td>
                                            <td><?php echo $galloLibre['nacimiento'];
                                                ?></td>
                                            <td>
                                                <input class="checkbox-gallo" type="checkbox" name="gallos[]" value="<?php echo $galloLibre['ID']; ?>">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>

                </div>

            </div>

        </div>

    </section>

</div>
<script>
    $(document).ready(function() {
        $(".btn-cotejar-manual").click(function() {
            var checkboxes = $(".checkbox-gallo:checked");
            if (checkboxes.length === 2) {
                var galloIds = checkboxes.map(function() {
                    return this.value;
                }).get();

                // Enviar los IDs de los gallos seleccionados al servidor mediante AJAX
                $.ajax({
                    url: "", // Deja esto en blanco para enviar la solicitud al mismo archivo actual
                    type: "POST",
                    data: {
                        gallos: galloIds
                    },
                    success: function(response) {
                        // Aquí puedes manejar la respuesta del servidor si es necesario
                        alert("Los gallos se cotejaron exitosamente.");
                        // Actualizar la página para reflejar los cambios en la tabla de "Coteja Manual"
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        // Manejar errores si es necesario
                        console.error(error);
                        alert("Error al cotejar los gallos. Por favor, inténtalo de nuevo.");
                    }
                });
            } else {
                alert("Selecciona exactamente 2 gallos para cotejar.");
            }
        });
    });
</script>

<?php include("../../templates/footer.php"); ?>