<?php

include("../../bd.php");

if ($_POST) {

  //Se realiza la recepción de los datos
  $datos = (isset($_POST['datos'])) ? $_POST['datos'] : "";
  //$localidad = (isset($_POST['localidad'])) ? $_POST['localidad'] : "";
  //$nacimiento = (isset($_POST['fechaNac'])) ? $_POST['fechaNac'] : "";
  //$correo = (isset($_POST['correo'])) ? $_POST['correo'] : "";

  $sentencia = $conexion->prepare("INSERT INTO `representante` (`ID`, `nombreCompleto`) VALUES (NULL, :nombreCompleto)");

  $sentencia->bindParam(":nombreCompleto", $datos);
  /*$sentencia->bindParam(":localidad", $localidad);
  $sentencia->bindParam(":fechaNac", $nacimiento);
  $sentencia->bindParam(":correo", $correo);*/

  if ($sentencia->execute()) {
    $mensaje = "Se creo el registro...!";
    header("Location:index.php?mensaje= " . $mensaje);
  } else {
    echo "El Registro no se agrego";
  }
}
include("../../templates/header.php");

?>

<div class="card">
  <div class="card-header">
    Crear Representante
  </div>

  <div class="card-body">

      <form action="" enctype="multipart/form-data" method="post">

            <div class="mb-3">
              <label for="datos" class="form-label">Nombres y Apellidos</label>
              <input type="text" class="form-control" name="datos" id="datos" aria-describedby="helpId" placeholder="Nombres y Apellidos">
            </div>

            <!--<div class="mb-3">
              <label for="localidad" class="form-label">Lugar</label>
              <input type="text" class="form-control" name="localidad" id="localidad" aria-describedby="helpId" placeholder="Lugar del Criadero">
            </div>-->

            <button type="submit" class="btn btn-success">Guardar</button>
            <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

            <!--<div class="mb-3">
              <label for="fechaNac" class="form-label">Fecha de Nacimiento</label>
              <input type="date" class="form-control" name="fechaNac" id="fechaNac" aria-describedby="helpId" placeholder="Fecha de Nacimiento">
            </div>

            <div class="mb-3">
              <label for="correo" class="form-label">Correo Electronico</label>
              <input type="text" class="form-control" name="correo" id="correo" aria-describedby="helpId" placeholder="correo">
            </div>-->

          </form>

  </div>
  <div class="card-footer text-muted">

  </div>
</div>

<?php include("../../templates/footer.php"); ?>