<?php 

include("../../bd.php");

if(isset($_GET['txtID'])){
    //borra el registro llamado con el ID correspondiente
    //echo $_GET['txtID'];
    
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("DELETE FROM frente WHERE codigo=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
}

//Con esta sentencia seleccionamos los datos de la tabla gallos,  nombre de familias y el nombre del representante correspondiente

$sentencia=$conexion->prepare("SELECT frente.codigo, frente.frente,
familias.nombre AS nombre_familia, representante.nombreCompleto AS nombre_representante 
FROM frente 
INNER JOIN familias ON frente.familiaId = familias.codigo 
INNER JOIN representante ON frente.representanteId = representante.ID
");
$sentencia->execute();

$lista_frente=$sentencia->fetchAll(PDO::FETCH_ASSOC);


include ("../../templates/header.php");?>

<div class="card">
    <div class="card-header">
        <a name="" id="" class="btn btn-primary" href="crear.php" role="button"><i class="fa-solid fa-plus"></i> Agregar</a>
    </div>
    <div class="card-body">
        <div class="table-responsive-sm">
            <table class="table " id="table_id">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Frente</th>
                        <th scope="col">Cuerda</th>
                        <th scope="col">Representante</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lista_frente as $registro){?>
                    <tr class="">
                        <td><?php echo $registro['codigo'];?></td>
                        <td><?php echo $registro['frente'];?></td>
                        <td><?php echo $registro['nombre_familia'];?></td>
                        <td><?php echo $registro['nombre_representante'];?></td>
                        <td>
                            <a name="" id="" class="btn btn-info" href="editar.php?txtID=<?php
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

