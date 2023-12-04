<?php

include("../../bd.php");

if($_POST){

//Se realiza la recepción de los datos
  $nombre=(isset($_POST['nombre']))?$_POST['nombre']:"";
  $lugar=(isset($_POST['localidad']))?$_POST['localidad']:"";
 // $fecha=(isset($_POST['creacion']))?$_POST['creacion']:"";
  $rep=(isset($_POST['representanteId']))?$_POST['representanteId']:"";

  $sentencia=$conexion->prepare("INSERT INTO `familias` (`codigo`, `nombre`, `localidad`, `representanteId`) VALUES (NULL, :nombre, :localidad, :representanteId)");

  $sentencia->bindParam(":nombre",$nombre);
  $sentencia->bindParam(":localidad",$lugar);
  //$sentencia->bindParam(":fecha_creada",$fecha);
  $sentencia->bindParam(":representanteId",$rep);
    
  if($sentencia->execute()){
    $mensaje="Se creo el registro...!";
    header("Location:index.php?mensaje= ".$mensaje);
  }
  else{
    echo "El Registro no se agrego";
  }
}

//Con esta sentencias seleccionamos los datos de la tabla de representantes
$sentencia=$conexion->prepare("SELECT * FROM representante WHERE ID");
$sentencia->execute();

$representante=$sentencia->fetchAll(PDO::FETCH_ASSOC);



include ("../../templates/header.php");

?>

<div class="card">
    <div class="card-header">
        Crear Club u Organización
    </div>
    
    <div class="card-body">
        
    <form action="" enctype="multipart/form-data" method="post">

        <div class="mb-3">
          <label for="nombre" class="form-label">Cuerda</label>
          <input type="text"
            class="form-control" name="nombre" id="nombre" aria-describedby="helpId" placeholder="Nombre del Criadero">
        </div>

        <div class="mb-3">
          <label for="representante" class="form-label">Representante: </label>
          <select name="representanteId" id="representanteId" class="form-select">
            <option value="">Seleccione el Representante</option>
            <?php foreach ($representante as $datorep):
              echo '<option value="'.$datorep["ID"].'">'.$datorep["nombreCompleto"].'</option>';
            endforeach;
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label for="localidad" class="form-label">Lugar de Organización</label>
          <input type="text"
            class="form-control" name="localidad" id="localidad" aria-describedby="helpId" placeholder="Ingrese el lugar al que pertenece">
        </div>

        <!--<div class="mb-3">
          <label for="creacion" class="form-label">Fecha de Creación</label>
          <input type="date"
            class="form-control" name="creacion" id="creacion" aria-describedby="helpId" placeholder="Fecha desde que inicio">
        </div>-->

       <button type="submit" class="btn btn-success">Guardar</button>
       <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

    </form>

    </div>
    <div class="card-footer text-muted">
    

    </div>
</div>

<?php include ("../../templates/footer.php");?>