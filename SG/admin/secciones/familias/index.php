<?php 

include("../../bd.php");

if(isset($_GET['txtID'])){
    //borra el registro llamado con el ID correspondiente
    //echo $_GET['txtID'];
    
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("DELETE FROM familias WHERE codigo=:codigo");
    $sentencia->bindParam(":codigo",$txtID);
    $sentencia->execute();
}

//Con esta sentencia seleccionamos los datos de la tabla de familias y el nombre del representante correspondiente
$sentencia=$conexion->prepare("SELECT familias.codigo, familias.nombre, familias.localidad, representante.nombreCompleto FROM familias INNER JOIN representante ON familias.representanteId = representante.ID");
$sentencia->execute();

$lista_fam=$sentencia->fetchAll(PDO::FETCH_ASSOC);



include ("../../templates/header.php");?>

<div class="card">
    <div class="card-header">
        <a name="" id="" class="btn btn-primary" href="crear.php" role="button"><i class="fa-solid fa-plus"></i> Agregar</a>
    </div>
    <div class="card-body">
        <div class="table-responsive-sm">
            <table class="table">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">Codigo</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Representante</th>
                        <th scope="col">Localidad</th>
                        <!--<th scope="col">Fecha de Creación</th>-->
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lista_fam as $registro){ ?>
                    <tr class="">
                        <td><?php echo $registro['codigo'];?></td>
                        <td><?php echo $registro['nombre'];?></td>
                        <td><?php echo $registro['nombreCompleto'];?></td>
                        <td><?php echo $registro['localidad'];?></td>
                        <!--<td><?php //echo $registro['fecha_creada'];?></td>-->
                        <td>
                            <a name="" id="" class="btn btn-info" href="modificar.php?txtID=<?php
                            echo $registro['codigo'];?>" role="button">Editar</a>
                            <a name="" id="" class="btn btn-danger" href="index.php?txtID=<?php
                            echo $registro['codigo'];?>" role="button">Eliminar</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
    </div>
</div>


<?php include ("../../templates/footer.php");?>

