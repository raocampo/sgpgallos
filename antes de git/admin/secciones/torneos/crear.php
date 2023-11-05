<?php

include("../../bd.php");

if($_POST){

//Se realiza la recepción de los datos
  $nombre=(isset($_POST['nombre']))?$_POST['nombre']:"";
  $fechaInicio=(isset($_POST['fechaInicio']))?$_POST['fechaInicio']:"";
  $fechaFin=(isset($_POST['fechaFin']))?$_POST['fechaFin']:"";
  $tipoTorneo=(isset($_POST['tipoTorneo']))?$_POST['tipoTorneo']:"";

  $sentencia=$conexion->prepare("INSERT INTO `torneos` (`ID`, `nombre`, `fecha_inicio`, `fecha_fin`, `tipoTorneo`) VALUES (NULL, :nombre, :fecha_Inicio, :fecha_Fin, :tipoTorneo)");

  $sentencia->bindParam(":nombre",$nombre);
  $sentencia->bindParam(":fecha_Inicio",$fechaInicio);
  $sentencia->bindParam(":fecha_Fin",$fechaFin);
  $sentencia->bindParam(":tipoTorneo",$tipoTorneo);
    
  if($sentencia->execute()){
    $mensaje="Se creo el registro...!";
    header("Location:index.php?mensaje= ".$mensaje);
  }
  else{
    echo "El Registro no se agrego";
  }
}
include ("../../templates/header.php");

?>

<div class="card">
    <div class="card-header">
        Crear Torneos
    </div>
    
    <div class="card-body">
        
    <form action="" enctype="multipart/form-data" method="post">

        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre</label>
          <input type="text"
            class="form-control" name="nombre" id="nombre" aria-describedby="helpId" placeholder="Nombre de Torneo">
        </div>

        <div class="mb-3">
          <label for="fechaInicio" class="form-label">Fecha Inicio</label>
          <input type="date"
            class="form-control" name="fechaInicio" id="fechaInicio" aria-describedby="helpId" placeholder="Inicia">
        </div>

        <div class="mb-3">
          <label for="fechaFin" class="form-label">Fecha Termina</label>
          <input type="date"
            class="form-control" name="fechaFin" id="fechaFin" aria-describedby="helpId" placeholder="Finaliza">
        </div>

        <div class="mb-3">
          <label for="tipoTorneo" class="form-label">Tipo de Torneo</label>
          <!--<input type="text"
            class="form-control" name="tipoTorneo" id="tipoTorneo" aria-describedby="helpId" placeholder="Escoja el tipo">-->
          <select name="tipoTorneo" id="tipoTorneo" class="form-select">
            <option value="0">Escoja</option>
            <option value="Nacional">Nacional</option>
            <option value="Provincial">Provincial</option>
            <option value="Local">local</option>
            <option value="Abierto">Abierto</option>
            <option value="Prueba">Prueba</option>
          </select>
        </div>

       <button type="submit" class="btn btn-success">Guardar</button>
       <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

    </form>

    </div>
    <div class="card-footer text-muted">
    

    </div>
</div>

<?php include ("../../templates/footer.php");?>