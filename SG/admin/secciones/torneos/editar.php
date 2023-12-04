<?php 

include("../../bd.php");

if(isset($_GET['txtID'])){
    //En esta sentencias se recuperan los datos a editar con el ID que se escoja
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("SELECT * FROM `torneos` WHERE ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $registro=$sentencia->fetch(PDO::FETCH_LAZY);

    $nombre=$registro['nombre'];
    $fechaInicio=$registro['fecha_inicio'];
    $fechaFin=$registro['fecha_fin'];
    $tipoTorneo=$registro['tipoTorneo'];
}

if($_POST){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
  $nombre=(isset($_POST['nombre']))?$_POST['nombre']:"";
  $fechaInicio=(isset($_POST['fechaInicio']))?$_POST['fechaInicio']:"";
  $fechaFin=(isset($_POST['fechaFin']))?$_POST['fechaFin']:"";
  $tipoTorneo=(isset($_POST['tipoTorneo']))?$_POST['tipoTorneo']:"";

  $sentencia=$conexion->prepare("UPDATE `torneos` SET nombre=:nombre, fecha_inicio=:fechaInicio, fecha_fin=:fechaFin, tipoTorneo=:tipoTorneo WHERE ID=:id");

  $sentencia->bindParam(":nombre",$nombre);
  $sentencia->bindParam(":fechaInicio",$fechaInicio);
  $sentencia->bindParam(":fechaFin",$fechaFin);
  $sentencia->bindParam(":tipoTorneo",$tipoTorneo);
  $sentencia->bindParam(":id",$txtID);

  $sentencia->execute();
  
  if($sentencia === TRUE){
    echo "Cambios guardados";
  }else{
    echo "No se pudo actualizar. ";
    print_r($sentencia->errorInfo());
  } 

  $mensaje="Se edito el registro...!";
  header("Location:index.php?mensaje= ".$mensaje);

}

include ("../../templates/header.php");?>


<div class="card">
    <div class="card-header">
        Editar Torneos
    </div>
    
    <div class="card-body">
        
    <form action="" enctype="multipart/form-data" method="post">

        <div class="mb-3">
          <label for="txtID" class="form-label">ID:</label>
          <input readonly value="<?php echo $txtID;?>" type="text"
              class="form-control" name="txtID" id="txtID" aria-describedby="helpId" placeholder="ID">
        </div>

        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre</label>
          <input value="<?php echo $nombre;?>" type="text"
            class="form-control" name="nombre" id="nombre" aria-describedby="helpId" placeholder="Nombre de Torneo">
        </div>

        <div class="mb-3">
          <label for="fechaInicio" class="form-label">Fecha Inicio</label>
          <input value="<?php echo $fechaInicio;?>" type="date"
            class="form-control" name="fechaInicio" id="fechaInicio" aria-describedby="helpId" placeholder="Inicia">
        </div>

        <div class="mb-3">
          <label for="fechaFin" class="form-label">Fecha Termina</label>
          <input value="<?php echo $fechaFin;?>" type="date"
            class="form-control" name="fechaFin" id="fechaFin" aria-describedby="helpId" placeholder="Finaliza">
        </div>

        <div class="mb-3">

          <label for="tipoTorneo" class="form-label">Tipo de Torneo</label>
          <select name="tipoTorneo" id="tipoTorneo" value="<?php echo $tipoTorneo;?>"   class="form-select">
            <option value="">Escoja</option>
            <option value="Nacional">Nacional</option>
            <option value="Provincial">Provincial</option>
            <option value="Local">local</option>
            <option value="Abierto">Abierto</option>
            <option value="Prueba">Prueba</option>
          </select>
          <!--<input value="<?php #echo $tipoTorneo;?> "type="text"
            class="form-control" name="tipoTorneo" id="tipoTorneo" aria-describedby="helpId" placeholder="Escoja el tipo">-->
        </div>

       <button type="submit" class="btn btn-success">Actualizar</button>
       <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

    </form>

<?php include ("../../templates/footer.php");?>