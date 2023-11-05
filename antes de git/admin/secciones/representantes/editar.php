<?php 

include("../../bd.php");

if(isset($_GET['txtID'])){
    //En esta sentencias se recuperan los datos a editar con el ID que se escoja
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("SELECT * FROM representante WHERE ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $registro=$sentencia->fetch(PDO::FETCH_LAZY);

    $datos=$registro['nombreCompleto'];
    //$localidad=$registro['localidad'];
    //$nacimiento=$registro['fechaNac'];
    //$correo=$registro['correo'];
}

if($_POST){

  $txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";

  $datos=(isset($_POST['nombreCompleto']))?$_POST['nombreCompleto']:"";
  //$localidad=(isset($_POST['localidad']))?$_POST['localidad']:"";
  //$nacimiento=(isset($_POST['fechaNac']))?$_POST['fechaNac']:"";
  //$correo=(isset($_POST['correo']))?$_POST['correo']:"";

  $sentencia=$conexion->prepare("UPDATE `representante` SET nombreCompleto=:nombreCompleto WHERE ID=:id");

  $sentencia->bindParam(":nombreCompleto",$datos);
  //$sentencia->bindParam(":localidad",$localidad);
  //$sentencia->bindParam(":fechaNac",$nacimiento);
  //$sentencia->bindParam(":correo",$correo);

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
        Editar Representante
    </div>
    
    <div class="card-body">
        
      <form action="" enctype="multipart/form-data" method="post">

          <div class="mb-3">
            <label for="txtID" class="form-label">ID:</label>
            <input readonly value="<?php echo $txtID;?>" type="text"
              class="form-control" name="txtID" id="txtID" aria-describedby="helpId" placeholder="ID">
          </div>

          <div class="mb-3">
            <label for="nombreCompleto" class="form-label">Nombres y Apellidos</label>
            <input value="<?php echo $datos;?>" type="text"
              class="form-control" name="nombreCompleto" id="nombreCompleto" aria-describedby="helpId" placeholder="Nombres y Apellidos">
          </div>

          <!--<div class="mb-3">
            <label for="localidad" class="form-label">Lugar</label>
            <input value="<?php //echo $localidad;?>" type="text"
              class="form-control" name="localidad" id="localidad" aria-describedby="helpId" placeholder="Lugar del Criadero">
          </div>

          <div class="mb-3">
            <label for="fechaNac" class="form-label">Fecha de Nacimiento</label>
            <input value="<?php //echo $nacimiento;?>" type="date"
              class="form-control" name="fechaNac" id="fechaNac" aria-describedby="helpId" placeholder="Fecha de Nacimiento">
          </div>

          <div class="mb-3">
            <label for="correo" class="form-label">Correo Electronico</label>
            <input value="<?php //echo $correo;?>" type="text"
              class="form-control" name="correo" id="correo" aria-describedby="helpId" placeholder="correo">
          </div>-->

        <button type="submit" class="btn btn-success">Actualizar</button>
        <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

      </form>

<?php include ("../../templates/footer.php");?>