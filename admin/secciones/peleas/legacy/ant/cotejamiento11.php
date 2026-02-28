<?php
include("../../bd.php");

// Contar el número de filas en la tabla "gallos"
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

$familias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia = $conexion->prepare("SELECT * FROM `torneos`");
$sentencia->execute();

$lista_torn = $sentencia->fetchAll(PDO::FETCH_ASSOC);

include("../../templates/header.sub.php");
?>
<div class=" text-center">
    <?php foreach ($lista_torn as $registro) { ?>
        <h1>Torneo: <?php echo $registro['nombre']; ?></h1>
    <?php } ?>
</div>
<div class="card container-fluid bg-transparent ">
    <div class="card-header ">
        <h3>Generar Cotejas</h3>
    </div>
    <section class=" container-fluid d-flex ">
        <div class="card-body">
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
                            <input class=" w-50" type="number" step="any" name="peso" id="peso" disabled>
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
                            <input class=" w-50" type="number" step="any" name="altura" id="altura" disabled>
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

                <div class=" card my-3">
                    <table class="table table-responsive-md  table-sm ">
                        <thead class="table-primary">
                            <tr>
                                <th>Nombre</th>
                                <th>Peso</th>
                                <th>Talla</th>
                                <th>Cuerda/Familia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php //echo $gallosIguales[$i]['nombre']; 
                                    ?></td>
                                <td><?php //echo $gallosIguales[$i]['pesoReal']; 
                                    ?></td>
                                <td><?php //echo $gallosIguales[$i]['tamañoReal']; 
                                    ?></td>
                                <td><?php //echo $gallosIguales[$i]['nombre_familia']; 
                                    ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-center mb-2"><span>VS</span></div>
                    <table class="table table-responsive-md  table-sm ">
                        <thead class="table-primary">
                            <tr>
                                <th>Nombre</th>
                                <th>Peso</th>
                                <th>Talla</th>
                                <th>Cuerda/Familia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php //for($i = 1; $i < count($gallosIguales); $i += 2): 
                            ?>
                            <tr>
                                <td><?php // echo $gallosIguales[$i]['nombre']; 
                                    ?></td>
                                <td><?php // echo $gallosIguales[$i]['pesoReal']; 
                                    ?></td>
                                <td><?php // echo $gallosIguales[$i]['tamañoReal']; 
                                    ?></td>
                                <td><?php // echo $gallosIguales[$i]['nombre_familia']
                                    ?></td>
                            </tr>
                            <?php // endfor; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class=" card ">
                <form class="contenido_tolerancia" action="" enctype="multipart/form-data" method="post">
                    <div class=" card d-flex ">
                        <div class="card-header">
                            <span>Cotejas</span>
                        </div>
                        <div class="d-flex flex-row">
                            <?php
                                $ids_cotejados = [];
                                $parejas = [];
                                $gallosLibres = [];
                                
                                if ((!empty($toleranciaPeso) && $toleranciaPeso != 0) || (!empty($toleranciaAltura) && $toleranciaAltura != 0)) :
                                    $gallos = $conexion->query("SELECT * FROM gallos WHERE familiasId <> 0 ORDER BY pesoReal")->fetchAll(PDO::FETCH_ASSOC);
                                
                                    while (count($parejas) < $cantidad_parejas) {
                                        $gallo1 = array_shift($gallos);
                                
                                        foreach ($gallos as $key => $gallo2) {
                                            if ($gallo1['ID'] == $gallo2['ID'] || $gallo1['familiasId'] == $gallo2['familiasId']) {
                                                continue;
                                            }
                                
                                            $galloAltura1 = $gallo1['tamañoReal'];
                                            $galloAltura2 = $gallo2['tamañoReal'];
                                
                                            // Evaluación de tamañoReal para Gallo 1
                                            if (($galloAltura1 >= 60 && $galloAltura1 <= 70) && ($galloAltura2 >= 60 && $galloAltura2 <= 70)) {
                                                // Evaluación de peso y tamañoReal para la pareja
                                                if (abs($gallo1['pesoReal'] - $gallo2['pesoReal']) <= $toleranciaPeso || abs($galloAltura1 - $galloAltura2) <= $toleranciaAltura) {
                                                    $parejas[] = [$gallo1, $gallo2];
                                                    $ids_cotejados[] = $gallo1['ID'];
                                                    $ids_cotejados[] = $gallo2['ID'];
                                                    unset($gallos[$key]);
                                                    break;
                                                }

                                                if ($gallo1['pesoReal'] == $gallo2['pesoReal'] && abs($gallo1['tamañoReal'] - $gallo2['tamañoReal']) <= $toleranciaAltura) {
                                                    // Resto del código
                                                    $id1 = $gallo1['ID'];
                                                    $id2 = $gallo2['ID'];
                                                    if (in_array($id1, $ids_cotejados) || in_array($id2, $ids_cotejados)) {
                                                        continue;
                                                    }
                                                    $parejas[] = [$gallo1, $gallo2];
                                                    $ids_cotejados[] = $id1;
                                                    $ids_cotejados[] = $id2;
                                                    unset($gallos[$key]);
                                                    break;
                                                }
    
                                                if ($gallo1['tamañoReal'] == $gallo2['tamañoReal'] && abs($gallo1['pesoReal'] - $gallo2['pesoReal']) <= $toleranciaPeso) {
                                                    // Resto del código
                                                    $id1 = $gallo1['ID'];
                                                    $id2 = $gallo2['ID'];
                                                    if (in_array($id1, $ids_cotejados) || in_array($id2, $ids_cotejados)) {
                                                        continue;
                                                    }
                                                    $parejas[] = [$gallo1, $gallo2];
                                                    $ids_cotejados[] = $id1;
                                                    $ids_cotejados[] = $id2;
                                                    unset($gallos[$key]);
                                                    break;
                                                }
                                            }
                                
                                            // Evaluación de tamañoReal para Gallo 2
                                            if (($galloAltura1 >= 71 && $galloAltura1 <= 80) && ($galloAltura2 >= 71 && $galloAltura2 <= 80)) {
                                                // Evaluación de peso y tamañoReal para la pareja
                                                if (abs($gallo1['pesoReal'] - $gallo2['pesoReal']) <= $toleranciaPeso || abs($galloAltura1 - $galloAltura2) <= $toleranciaAltura) {
                                                    $parejas[] = [$gallo1, $gallo2];
                                                    $ids_cotejados[] = $gallo1['ID'];
                                                    $ids_cotejados[] = $gallo2['ID'];
                                                    unset($gallos[$key]);
                                                    break;
                                                }

                                                if ($gallo1['pesoReal'] == $gallo2['pesoReal'] && abs($galloAltura1 - $galloAltura2) <= $toleranciaAltura) {
                                                    // Resto del código
                                                    $id1 = $gallo1['ID'];
                                                    $id2 = $gallo2['ID'];
                                                    if (in_array($id1, $ids_cotejados) || in_array($id2, $ids_cotejados)) {
                                                        continue;
                                                    }
                                                    $parejas[] = [$gallo1, $gallo2];
                                                    $ids_cotejados[] = $id1;
                                                    $ids_cotejados[] = $id2;
                                                    unset($gallos[$key]);
                                                    break;
                                                }
    
                                                if ($galloAltura1 == $galloAltura2 && abs($gallo1['pesoReal'] - $gallo2['pesoReal']) <= $toleranciaPeso) {
                                                    // Resto del código
                                                    $id1 = $gallo1['ID'];
                                                    $id2 = $gallo2['ID'];
                                                    if (in_array($id1, $ids_cotejados) || in_array($id2, $ids_cotejados)) {
                                                        continue;
                                                    }
                                                    $parejas[] = [$gallo1, $gallo2];
                                                    $ids_cotejados[] = $id1;
                                                    $ids_cotejados[] = $id2;
                                                    unset($gallos[$key]);
                                                    break;
                                                }
                                            }
                                
                                            // Evaluación de tamañoReal para ambos gallos
                                            if (($galloAltura1 >= 81 && $galloAltura1 <= 90) && ($galloAltura2 >= 81 && $galloAltura2 <= 90)) {
                                                // Evaluación de peso y tamañoReal para la pareja
                                                if (abs($gallo1['pesoReal'] - $gallo2['pesoReal']) <= $toleranciaPeso || abs($galloAltura1 - $galloAltura2) <= $toleranciaAltura) {
                                                    $parejas[] = [$gallo1, $gallo2];
                                                    $ids_cotejados[] = $gallo1['ID'];
                                                    $ids_cotejados[] = $gallo2['ID'];
                                                    unset($gallos[$key]);
                                                    break;
                                                }

                                                if ($gallo1['pesoReal'] == $gallo2['pesoReal'] && abs($galloAltura1 - $galloAltura2) <= $toleranciaAltura) {
                                                    // Resto del código
                                                    $id1 = $gallo1['ID'];
                                                    $id2 = $gallo2['ID'];
                                                    if (in_array($id1, $ids_cotejados) || in_array($id2, $ids_cotejados)) {
                                                        continue;
                                                    }
                                                    $parejas[] = [$gallo1, $gallo2];
                                                    $ids_cotejados[] = $id1;
                                                    $ids_cotejados[] = $id2;
                                                    unset($gallos[$key]);
                                                    break;
                                                }
    
                                                if ($galloAltura1 == $galloAltura2 && abs($gallo1['pesoReal'] - $gallo2['pesoReal']) <= $toleranciaPeso) {
                                                    // Resto del código
                                                    $id1 = $gallo1['ID'];
                                                    $id2 = $gallo2['ID'];
                                                    if (in_array($id1, $ids_cotejados) || in_array($id2, $ids_cotejados)) {
                                                        continue;
                                                    }
                                                    $parejas[] = [$gallo1, $gallo2];
                                                    $ids_cotejados[] = $id1;
                                                    $ids_cotejados[] = $id2;
                                                    unset($gallos[$key]);
                                                    break;
                                                }
                                            }

                                            if (($galloAltura1 >= 91 && $galloAltura1 <= 100) && ($galloAltura2 >= 91 && $galloAltura2 <= 100)) {
                                                // Evaluación de peso y tamañoReal para la pareja
                                                if (abs($gallo1['pesoReal'] - $gallo2['pesoReal']) <= $toleranciaPeso || abs($galloAltura1 - $galloAltura2) <= $toleranciaAltura) {
                                                    $parejas[] = [$gallo1, $gallo2];
                                                    $ids_cotejados[] = $gallo1['ID'];
                                                    $ids_cotejados[] = $gallo2['ID'];
                                                    unset($gallos[$key]);
                                                    break;
                                                }

                                                if ($gallo1['pesoReal'] == $gallo2['pesoReal'] && abs($galloAltura1 - $galloAltura2) <= $toleranciaAltura) {
                                                    // Resto del código
                                                    $id1 = $gallo1['ID'];
                                                    $id2 = $gallo2['ID'];
                                                    if (in_array($id1, $ids_cotejados) || in_array($id2, $ids_cotejados)) {
                                                        continue;
                                                    }
                                                    $parejas[] = [$gallo1, $gallo2];
                                                    $ids_cotejados[] = $id1;
                                                    $ids_cotejados[] = $id2;
                                                    unset($gallos[$key]);
                                                    break;
                                                }
    
                                                if ($galloAltura1 == $galloAltura2 && abs($gallo1['pesoReal'] - $gallo2['pesoReal']) <= $toleranciaPeso) {
                                                    // Resto del código
                                                    $id1 = $gallo1['ID'];
                                                    $id2 = $gallo2['ID'];
                                                    if (in_array($id1, $ids_cotejados) || in_array($id2, $ids_cotejados)) {
                                                        continue;
                                                    }
                                                    $parejas[] = [$gallo1, $gallo2];
                                                    $ids_cotejados[] = $id1;
                                                    $ids_cotejados[] = $id2;
                                                    unset($gallos[$key]);
                                                    break;
                                                }
                                            }
                                        }

                                        foreach ($gallos as $gallo) {
                                            $id = $gallo['ID'];
                                            $familiasId = $gallo['familiasId'];
                                        
                                            if ($id != $gallo1['ID'] && $familiasId != $gallo1['familiasId'] && !in_array($id, $ids_cotejados)) {
                                                if (!(abs($gallo1['pesoReal'] - $gallo['pesoReal']) <= $toleranciaPeso || abs($galloAltura1 - $gallo['tamañoReal']) <= $toleranciaAltura) || $galloAltura1 != $gallo['tamañoReal']) {
                                                    $gallosLibres[] = $gallo;
                                                }
                                            }
                                        }
                                        
                                        
                                    }                                
                                ?>
                                
                                <table class="table table-responsive-md table-sm ">
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
                                                <td><input type="checkbox" name="peleas[]" value="<?php echo $parejas['ID_Coteja']; ?>"></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else : $parejas = $conexion->query("SELECT g1.*, g2.*, familias.nombre AS nombre_familia FROM gallos g1
                            INNER JOIN gallos g2 ON g1.id <> g2.id AND (g1.pesoReal = g2.pesoReal OR g1.tamañoReal = g2.tamañoReal)
                            INNER JOIN familias ON g1.familiasId <> g2.familiasId AND g1.familiasId = familias.codigo
                            ORDER BY g1.pesoReal ASC LIMIT $total_gallos")->fetchAll(PDO::FETCH_ASSOC); 
                            ?>
                                <table class="table table-responsive-md table-sm ">
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
                            <?php 
                                // Agregar gallos que no formaron pareja a $gallosLibres
                                /*foreach ($gallos as $gallo) {
                                    if ($gallo['ID'] != $gallo1['ID'] && $gallo['familiasId'] != $gallo1['familiasId']) {
                                        if (!(abs($gallo1['pesoReal'] - $gallo['pesoReal']) <= $toleranciaPeso || abs($galloAltura1 - $gallo['tamañoReal']) <= $toleranciaAltura) || $galloAltura1 != $gallo['tamañoReal']) {
                                            $gallosLibres[] = $gallo;
                                        }
                                    }
                                }*/
                            ?>
                            <table class="table table-responsive-md table-sm">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ITEM</th>
                                        <th>Anillo</th>
                                        <th>Tamaño</th>
                                        <th>Peso</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><?php foreach ($gallosLibres as $galloLibre) : ?>
                                            <td><?php echo $item1 += 1; ?></td>
                                            <td><?php echo $galloLibre['anillo']; ?></td>
                                            <td><?php echo $galloLibre['tamañoReal']; ?></td>
                                            <td><?php echo $galloLibre['pesoReal']; ?></td>
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
                </form>
            </div>
        </div>

    </section>

    <section class="card">
        <form class="contenido_tolerancia" action="" enctype="multipart/form-data" method="post">
            <div class="card-header">
                <span>Peleas Pactadas para la contienda</span>
            </div>
            <div class="card-body d-flex">
                <table class=" table table-bordered table-responsive-md  table-sm flex-fill">
                    <thead class="table-primary">
                        <tr>
                            <th>Codigo</th>
                            <th>Torneo</th>
                            <th>Cuerda</th>
                            <th>Nombre</th>
                            <th>IdGallo</th>
                            <th>Gr</th>
                            <th>Lb</th>
                            <th>Oz</th>
                            <th>Talla</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Local</td>
                            <td>Suquinda</td>
                            <td>Paquito</td>
                            <td>2</td>
                            <td>4</td>
                            <td>8</td>
                            <td>2</td>
                            <td>4</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered table-responsive-md  table-sm flex-fill">
                    <thead class="table-primary">
                        <tr>
                            <th>Codigo</th>
                            <th>Torneo</th>
                            <th>Cuerda</th>
                            <th>Nombre</th>
                            <th>IdGallo</th>
                            <th>Gr</th>
                            <th>Lb</th>
                            <th>Oz</th>
                            <th>Talla</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Local</td>
                            <td>Vilc-Espinoza</td>
                            <td>Paquita</td>
                            <td>2</td>
                            <td>4</td>
                            <td>8</td>
                            <td>2</td>
                            <td>4</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex flex-row ">
                <a name="" id="" class="btn btn-success m-2" href="resultados.php" role="button">Resultados</a>
                <button type="submit" class="btn btn-success m-2">Imprimir</button>
            </div>
        </form>
    </section>

</div>

<?php include("../../templates/footer.php"); ?>