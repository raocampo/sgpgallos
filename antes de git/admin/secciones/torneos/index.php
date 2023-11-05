<?php 

include("../../bd.php");

if(isset($_GET['txtID'])){
    //borra el registro llamado con el ID correspondiente
    //echo $_GET['txtID'];
    
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("DELETE FROM torneos WHERE ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
}

//Con esta sentencias seleccionamos los datos de la tabla de torneos
$sentencia=$conexion->prepare("SELECT * FROM `torneos`");
$sentencia->execute();

$lista_torn=$sentencia->fetchAll(PDO::FETCH_ASSOC);


include ("../../templates/header.php");
?>


<div class="card">
    <div class="card-header">
        <a name="" id="" class="btn btn-primary" href="crear.php" role="button"><i class="fa-solid fa-plus"></i> Agregar</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Fecha Inicio</th>
                        <th scope="col">Fecha Termina</th>
                        <th scope="col">Tipo Torneo</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody class="">
                    <?php foreach($lista_torn as $registro){?>
                    <tr class="">
                        <td><?php echo $registro['ID'];?></td>
                        <td><?php echo $registro['nombre'];?></td>
                        <td><?php echo $registro['fecha_inicio'];?></td>
                        <td><?php echo $registro['fecha_fin'];?></td>
                        <td><?php echo $registro['tipoTorneo'];?></td>
                        <td>
                            <a name="" id="" class="btn btn-info" href="editar.php?txtID=<?php
                            echo $registro['ID'];?>" role="button">Editar</a>

                            <a name="" id="" class="btn btn-info" href="../../templates/header.sub.php?nombreTorneo=<?php echo $registro['nombre'];?>&torneoId=<?php
                            echo $registro['ID'];?>" role="button">Seleccionar</a>
                            
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


