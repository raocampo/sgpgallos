<?php 

include("../../bd.php");

if(isset($_GET['txtID'])){
    //borra el registro llamado con el ID correspondiente
    //echo $_GET['txtID'];
    
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("DELETE FROM representante WHERE ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
}

//Con esta sentencias seleccionamos los datos de la tabla de servicios
$sentencia=$conexion->prepare("SELECT * FROM `representante`");
$sentencia->execute();

$lista_rep=$sentencia->fetchAll(PDO::FETCH_ASSOC);


include ("../../templates/header.php");?>

<div class="card">
    <div class="card-header">
       <a name="" id="" class="btn btn-primary" href="crear.php" role="button"><i class="fa-solid fa-plus"></i> Agregar</a>
    </div>
    <div class="card-body">
        <div class="table-responsive-sm">
            <table class="table ">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombres y Apellidos</th>
                        <!--<th scope="col">Fecha Nacimiento</th>
                        <th scope="col">Lugar</th>
                        <th scope="col">correo</th>-->
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lista_rep as $registro){?>
                    <tr class="">
                        <td><?php echo $registro['ID'];?></td>
                        <td><?php echo $registro['nombreCompleto'];?></td>
                        <!--<td><?php //echo $registro['localidad'];?></td>
                        <td><?php //echo $registro['fechaNac'];?></td>
                        <td><?php //echo $registro['correo'];?></td>-->
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


<?php include ("../../templates/footer.php");
 ?>

