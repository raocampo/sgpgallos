<?php
// procExclusiones.php

error_reporting(E_ERROR | E_WARNING | E_PARSE);

include("../../bd.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$item = 0;

$torneoId = $_SESSION['torneoId'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $familia1 = $_POST['familia1'];
    $familia2 = $_POST['familia2'];

    // Verificar si las familias seleccionadas no están vacías
    if (!empty($familia1) && !empty($familia2)) {
        // Verificar si las familias ya están excluidas
        $sentenciaVerificacion = $conexion->prepare("SELECT COUNT(*) FROM exclusiones WHERE (nombreFamiliaUno = ? AND nombreFamiliaDos = ?) OR (nombreFamiliaUno = ? AND nombreFamiliaDos = ?) AND torneoId = ?");
        $sentenciaVerificacion->execute([$familia1, $familia2, $familia2, $familia1, $torneoId]);
        $exclusionesExistente = $sentenciaVerificacion->fetchColumn();

        if ($exclusionesExistente) {
            echo "Estas familias ya están excluidas en este torneo.";
        } else {
            // Insertar las exclusiones en la tabla
            $sentencia = $conexion->prepare("INSERT INTO exclusiones (nombreFamiliaUno, nombreFamiliaDos, torneoId) VALUES (?, ?, ?)");
            $sentencia->execute([$familia1, $familia2, $torneoId]);

            //echo "Las familias se han excluido correctamente.";
        }
    } else {
        echo "Por favor, seleccione dos familias para excluir.";
    }
} /*else {
    echo "Acceso inválido a la página.";
}*/

if(isset($_GET['txtID'])){
    //borra el registro llamado con el ID correspondiente
    //echo $_GET['txtID'];
    
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("DELETE FROM exclusiones WHERE IdExclusion=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
}

//Con esta sentencia seleccionamos los datos de la tabla exclusiones

$sentenciaExclusiones = $conexion->prepare("SELECT exclusiones.IdExclusion, familia1.nombre AS nombreFamiliaUno, familia2.nombre AS nombreFamiliaDos
FROM exclusiones
INNER JOIN familias AS familia1 ON exclusiones.nombreFamiliaUno = familia1.codigo
INNER JOIN familias AS familia2 ON exclusiones.nombreFamiliaDos = familia2.codigo
WHERE exclusiones.torneoId = ?");
$sentenciaExclusiones->execute([$torneoId]);

$listaExclusiones = $sentenciaExclusiones->fetchAll(PDO::FETCH_ASSOC);
print_r($listaExclusiones);


include("../../templates/header.sub.php");
?>

<div class="container text-center">

    <div class="card">
        <div class="table-responsive">
            <table class="table text-center">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">ITEM</th></th>
                        <th scope="col">Criadero</th>
                        <th scope="col"></th>
                        <th scope="col">Criadero</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listaExclusiones as $exclusion){ ?>
                    <tr class="">
                        <td><?php echo $item += 1; ?></td>
                        <td><?php echo $exclusion['nombreFamiliaUno'];?></td>
                        <td><span>Excluido con</span></td>
                        <td><?php echo $exclusion['nombreFamiliaDos'];?></td>
                        <td>
                            <a name="" id="" class="btn btn-danger" href="procExclusiones.php?txtID=<?php
                            echo $exclusion['IdExclusion'];?>" role="button">Eliminar</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
    </div>
</div>


<?php include("../../templates/footer.php"); ?>
