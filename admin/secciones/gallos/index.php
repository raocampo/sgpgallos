<?php 
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include("../../bd.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
$nombreTorneo = $_SESSION['nombreTorneo'];
$torneoId = $_SESSION['torneoId'];

if(isset($_GET['txtID'])){
    //borra el registro llamado con el ID correspondiente
    //echo $_GET['txtID'];
    
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    /*$sentencia=$conexion->prepare("SELECT imagen FROM gallos WHERE ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
    $registro_imagen=$sentencia->fetch(PDO::FETCH_LAZY);

    if(isset($registro_imagen["imagen"])){

        if(file_exists("../../../assets/img/gallos/".$registro_imagen["imagen"])){
            
            unlink("../../../assets/img/gallos/".$registro_imagen["imagen"]);
        
        }
    }*/

    $sentencia=$conexion->prepare("DELETE FROM gallos WHERE ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
}

//Con esta sentencia seleccionamos los datos de la tabla gallos,  nombre de familias y el nombre del representante correspondiente

$sentencia=$conexion->prepare("SELECT gallos.ID, gallos.anillo, gallos.pesoReal, gallos.tamañoReal, gallos.placa, gallos.nacimiento, gallos.frente, familias.nombre AS nombre_familia, representante.nombreCompleto AS nombre_representante 
FROM gallos 
INNER JOIN familias ON gallos.familiasId = familias.codigo 
INNER JOIN representante ON gallos.representanteId = representante.ID
WHERE gallos.torneoId = $torneoId");
$sentencia->execute();

$lista_gallos=$sentencia->fetchAll(PDO::FETCH_ASSOC);


include ("../../templates/header.sub.php");


?>

<div class="card">
    
    <div class="card-header">
        <a name="" id="" class="btn btn-primary" href="crear.php" role="button"><i class="fa-solid fa-plus"></i> Agregar</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" id="tabla_id">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Anillo</th>
                        <th scope="col">Peso</th>
                        <th scope="col">Tamaño</th>
                        <th scope="col">Placa</th>
                        <th scope="col">Mes Nacimiento</th>
                        <th scope="col">frente</th>
                        <th scope="col">Criadero</th>
                        <th scope="col">Representante</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lista_gallos as $registro){ ?>
                    <tr class="">
                        <td><?php echo $registro['ID'];?></td>
                        <td><?php echo $registro['anillo'];?></td>
                        <td><?php echo $registro['pesoReal'];?></td>
                        <td><?php echo $registro['tamañoReal'];?></td>
                        <td><?php echo $registro['placa'];?></td>
                        <td><?php echo $registro['nacimiento'];?></td>
                        <td><?php echo $registro['frente'];?></td>
                        <td><?php echo $registro['nombre_familia'];?></td>
                        <td><?php echo $registro['nombre_representante'];?></td>
                        <td>
                            <a name="" id="" class="btn btn-info" href="editar.php?txtID=<?php
                            echo $registro['ID'];?>" role="button">Editar</a>
                            <a name="" id="" class="btn btn-danger" href="index.php?txtID=<?php
                            echo $registro['ID'];?>" role="button">Eliminar</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
    </div>
</div>


<?php include ("../../templates/footer.php");?>

