<?php
include("../../bd.php");

$cotejamientoManual = $_POST['cotejamiento'];

foreach ($parejasSeleccionadas as $parejaId) {
    $sentencia = $conexion->prepare("INSERT INTO coteja (ID_Coteja, galloL, galloV) VALUES (?, ?, ?)");
    $sentencia->bindParam(1, $parejaId);
    $sentencia->bindParam(2, $galloL);
    $sentencia->bindParam(3, $galloV);
    
    // Aquí debes asignar los valores de $galloL y $galloV según tus necesidades
    // Puedes obtener estos valores a partir de la tabla de gallos o utilizando otras fuentes de datos
    
    $sentencia->execute();
}


/* Contar el número de filas en la tabla "gallos"
$item = 0;
$item1 = 0;
$sentencia = $conexion->query("SELECT COUNT(*) FROM gallos");
$total_gallos = $sentencia->fetchColumn();

$toleranciaAltura = isset($_POST['altura']) ? $_POST['altura'] : null;
$toleranciaPeso = isset($_POST['peso']) ? $_POST['peso'] : null;
#$tolerancia = isset($_POST['altura']) ? $_POST['altura'] : null;

$cantidad_parejas = intval($total_gallos / 2);
if ($total_gallos % 2 != 0) {
    $total_gallos--;
}

//Con esta sentencias seleccionamos los datos de la tabla de familias
$sentencia = $conexion->prepare("SELECT * FROM familias WHERE codigo");
$sentencia->execute();

$familias = $sentencia->fetchAll(PDO::FETCH_ASSOC);*/

include("../../templates/header.sub.php");
?>

<div class="card container-fluid bg-transparent ">
    <div class="card-header ">
        <h3>Generar Cotejas</h3>
    </div>
    <!--INICIA SECCIÓN TOLERANCIA-->
    <section class=" container-fluid d-flex">
        <div class="card-body">
            <!--INICIA FORMULARIO TOLERANCIA-->
            <form class="contenido_tolerancia" action="" enctype="multipart/form-data" method="post">
                <div class=" card container container-fluid mt d-flex">
                    <div class=" mt-5 ">
                        <span>Tolerancia</span>
                    </div>

                    <div class="d-flex">

                        <div class=" mt-2 p-3 d-flex flex-column">

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

                        <div class=" mt-2 p-3 d-flex flex-column">

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
                <div class="card container-fluid ">
                    <div class="container mt-5">
                        <label for="">Gallos a cotejar</label>
                        <input class=" w-25 text-center" name="rescotejar" id="rescotejar" value="<?php echo $total_gallos; ?>" type="text">
                    </div>
                    <div class=" mt-5 ">
                        <button type="submit" class="btn btn-success">Cotejar</button>
                    </div>
                </div>
            </form><!--FIN FORMULARIO TOLERANCIA-->

            <!--INICIA DIV Y FORMULARIO DE COTEJA MANUAL-->
            <div class="card-body">
                <form id="formCotejoManual" action="cotejamiento.php" method="POST">
                    <div class=" card my-3 d-flex">
                        <?php
                        $sentencia = $conexion->prepare("SELECT gallos.ID, gallos.anillo, gallos.pesoReal, gallos.tamañoReal, gallos.placa, gallos.nacimiento, gallos.frente, familias.nombre AS nombre_familia, representante.nombreCompleto AS nombre_representante 
                    FROM gallos 
                    INNER JOIN familias ON gallos.familiasId = familias.codigo 
                    INNER JOIN representante ON gallos.representanteId = representante.ID
                     ORDER BY pesoReal");
                        $sentencia->execute();

                        $lista_gallos = $sentencia->fetchAll(PDO::FETCH_ASSOC)
                        ?>
                        <div class=" d-flex justify-content-center">
                            <table class="table table-responsive-md  table-sm text-center">
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
                                        <tr>
                                            <td><?php echo $item += 1; ?></td>
                                            <td><?php echo $gallo['anillo']; ?></td>
                                            <td><?php echo $gallo['nombre_familia']; ?></td>
                                            <td><?php echo $gallo['pesoReal']; ?></td>
                                            <td><?php echo $gallo['tamañoReal'];                                        ?>
                                            <td><input type="checkbox" class="checkbox-pareja" name="cotejamiento[]" value="<?php echo $pareja['ID_Coteja']; ?>" datoGallo="<?php echo $gallo['ID']; ?>"></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <div class="d-flex flex-column py-5">
                                <button type="submit" class="btn btn-success m-2">Cotejar Manualmente</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div><!--FIN DE COTEJAMIENTO MANUAL-->
        </div><!--FIN SECCIÓN TOLERANCIA MNAULA Y AUTOMATICA-->

        <!--SECCION DE ALGORITMO COTEJAMIENTO-->
        <div class="card-body">
            <div class=" card ">
                <!--INCIA FORMULARIO PARA SELECCIONAR COTEJAMIENTO-->
                <form class="contenido_tolerancia" action="peleaGenerada.php" enctype="multipart/form-data" method="post">
                    <div class=" card d-flex ">
                        <div class="card-header">
                            <span>Cotejas</span>
                        </div>
                        <div class="d-flex flex-row">
                            <?php
                            $ids_cotejados = [];
                            $parejas = [];
                            $gallosLibres = [];

                            if ((!empty($toleranciaPeso) && $toleranciaPeso != 0) && (!empty($toleranciaAltura) && $toleranciaAltura != 0)) :
                                $gallos = $conexion->query("SELECT * FROM gallos WHERE familiasId <> 0 ORDER BY tamañoReal")->fetchAll(PDO::FETCH_ASSOC);

                                $cantidadGallos = count($gallos);

                                for ($index1 = 0; $index1 < $cantidadGallos; $index1++) {
                                    $gallo1 = $gallos[$index1];
                                    $galloPeso1 = round($gallo1['pesoReal'], 2); // Redondea a 2 decimales
                                    $galloAltura1 = round($gallo1['tamañoReal'], 2); // Redondea a 2 decimales

                                    $parejaEncontrada = false;

                                    for ($index2 = $index1 + 1; $index2 < $cantidadGallos; $index2++) {
                                        $gallo2 = $gallos[$index2];

                                        if ($gallo1['familiasId'] == $gallo2['familiasId']) {
                                            continue;
                                        }

                                        $galloPeso2 = round($gallo2['pesoReal'], 2); // Redondea a 2 decimales
                                        $galloAltura2 = round($gallo2['tamañoReal'], 2); // Redondea a 2 decimales

                                        if (abs($galloPeso1 - $galloPeso2) <= $toleranciaPeso && abs($galloAltura1 - $galloAltura2) <= $toleranciaAltura) {
                                            $id1 = $gallo1['ID'];
                                            $id2 = $gallo2['ID'];

                                            if (in_array($id1, $ids_cotejados) || in_array($id2, $ids_cotejados)) {
                                                continue;
                                            }

                                            $parejas[] = [$gallo1, $gallo2];
                                            $ids_cotejados[] = $id1;
                                            $ids_cotejados[] = $id2;

                                            $parejaEncontrada = true;
                                        }
                                    }

                                    // Si no se encontró pareja para el gallo, se agrega a la lista de gallos libres
                                    if (!in_array($gallo1['ID'], $ids_cotejados)) {
                                        $gallosLibres[] = $gallo1;
                                    }
                                }


                                // Ordenar las parejas por pesoReal
                                usort($parejas, function ($a, $b) {
                                    $peso1 = $a[0]['pesoReal'];
                                    $peso2 = $b[0]['pesoReal'];

                                    if ($peso1 == $peso2) {
                                        return 0;
                                    }

                                    return ($peso1 < $peso2) ? -1 : 1;
                                });
                                /*}*/
                            ?>


                                <table class="table table-responsive-md table-sm text-center">
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
                                                <td><?php echo $item += 1; ?></td>
                                                <td><?php echo $gallo1['anillo']; ?></td>
                                                <td><?php echo $familia1['nombre']; ?></td>
                                                <td><?php echo $gallo1['tamañoReal']; ?></td>
                                                <td><?php echo $gallo1['pesoReal']; ?></td>
                                                <td><?php echo $gallo2['anillo']; ?></td>
                                                <td><?php echo $familia2['nombre']; ?></td>
                                                <td><?php echo $gallo2['tamañoReal']; ?></td>
                                                <td><?php echo $gallo2['pesoReal']; ?></td>
                                                <td><input type="checkbox" name="peleas[]" value="<?php echo $pareja['ID_Coteja']; ?>"></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else : $parejas = $conexion->query("SELECT g1.*, g2.*, familias.nombre AS nombre_familia FROM gallos g1
                            INNER JOIN gallos g2 ON g1.id <> g2.id AND (g1.pesoReal = g2.pesoReal AND g1.tamañoReal = g2.tamañoReal)
                            INNER JOIN familias ON g1.familiasId <> g2.familiasId AND g1.familiasId = familias.codigo
                            ORDER BY g1.pesoReal ASC LIMIT $total_gallos")->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                                <table class="table table-responsive-md table-sm text-center text-bg-dark ">
                                    <thead class="table-primary">
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
                                    </thead>
                                    <tbody>
                                        <?php
                                        for ($i = 0; $i < count($parejas); $i += 2) : ?>
                                            <tr>
                                                <td><?php echo $item += 1; ?></td>
                                                <td><?php echo $parejas[$i]['anillo']; ?></td>
                                                <td><?php echo $parejas[$i]['nombre_familia']; ?></td>
                                                <td><?php echo $parejas[$i]['tamañoReal']; ?></td>
                                                <td><?php echo $parejas[$i]['pesoReal']; ?></td>
                                                <td><?php echo $parejas[$i + 1]['anillo']; ?></td>
                                                <td><?php echo $parejas[$i + 1]['nombre_familia']; ?></td>
                                                <td><?php echo $parejas[$i + 1]['tamañoReal']; ?></td>
                                                <td><?php echo $parejas[$i + 1]['pesoReal']; ?></td>
                                                <td><input type="checkbox" name="peleas[]" value="<?php echo $parejas['ID_Coteja']; ?>"></td>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                            <div class="d-flex flex-column">
                                <button type="submit" class="btn btn-success m-2">Pactar Peleas</button>
                                <button type="submit" class="btn btn-success m-2">Liberar Peleas</button>
                                <button type="submit" class="btn btn-success m-2">Imprimir</button>
                            </div>
                        </div>
                    </div>

                    <div class="card d-flex">

                        <div class="card-header">
                            <span>Gallos Libres</span>
                        </div>
                        <div class="d-flex flex-row">
                            <div class="d-flex">
                                <table class="table table-responsive-md table-sm">
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
                                                <td><?php echo $item1 += 1; ?></td>
                                                <td><?php echo $galloLibre['anillo']; ?></td>
                                                <td><?php echo $familia1['nombre']; ?></td>
                                                <td><?php echo $galloLibre['tamañoReal']; ?></td>
                                                <td><?php echo $galloLibre['pesoReal']; ?></td>
                                                <td><input type="checkbox" name="peleas[]" value="<?php echo $parejas['ID_Coteja']; ?>"></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <div class="d-flex flex-column ">
                                    <button type="submit" class="btn btn-success m-2">Cotejar Manualmente</button>
                                    <button type="submit" class="btn btn-success m-2">Imprimir</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

    </section>



    <?php include("../../templates/footer.php"); ?>