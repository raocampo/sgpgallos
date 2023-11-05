<?php

include("../../bd.php");

if (isset($_GET['txtID'])) {
  //En esta sentencias se recuperan los datos a editar con el ID que se escoja
  $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";

  $sentencia = $conexion->prepare("SELECT * FROM familias WHERE codigo=:codigo");
  $sentencia->bindParam(":codigo", $txtID);
  $sentencia->execute();

  $registro = $sentencia->fetch(PDO::FETCH_LAZY);

  $nombre = $registro['nombre'];
  $lugar = $registro['localidad'];
  $representante = $registro['representanteId'];
  //$fecha = $registro['creacion'];
}

if ($_POST) {

  $txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
  $nombre = (isset($_POST['nombre'])) ? $_POST['nombre'] : "";
  $lugar = (isset($_POST['localidad'])) ? $_POST['localidad'] : "";
  $representante = (isset($_POST['representanteId'])) ? $_POST['representanteId'] : "";
  //$fecha = (isset($_POST['creacion'])) ? $_POST['creacion'] : "";

  $sentencia = $conexion->prepare("UPDATE `familias` INNER JOIN representante ON familias.representanteId = representante.ID SET familias.nombre=:nombre, familias.localidad=:localidad, familias.representanteId=:representanteId WHERE familias.codigo=:codigo");

  $sentencia->bindParam(":nombre", $nombre);
  $sentencia->bindParam(":localidad", $lugar);
  $sentencia->bindParam(":representanteId", $representante);
  //$sentencia->bindParam(":fecha_creada", $fecha);
  $sentencia->bindParam(":codigo", $txtID);

  $sentencia->execute();

  
  if ($sentencia === TRUE) {
    echo "Cambios guardados";
  } else {
    echo "No se pudo actualizar. ";
    print_r($sentencia->errorInfo());
  }

  $mensaje = "Se edito el registro...!";
  header("Location:index.php?mensaje= " . $mensaje);
}

//Con esta sentencias seleccionamos los datos de la tabla de representantes
$sentencia = $conexion->prepare("SELECT * FROM representante WHERE ID");
$sentencia->execute();

$representantes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

include("../../templates/header.php"); ?>


<div class="card">
  <div class="card-header">
    Editar Cuerda
  </div>

  <div class="card-body">

    <form action="" enctype="multipart/form-data" method="post">

      <div class="mb-3">
        <label for="txtID" class="form-label">Codigo:</label>
        <input readonly value="<?php echo $txtID; ?>" type="text" class="form-control" name="txtID" id="txtID" aria-describedby="helpId" placeholder="ID">
      </div>

      <div class="mb-3">
        <label for="nombre" class="form-label">Cuerda:</label>
        <input value="<?php echo $nombre; ?>" type="text" class="form-control" name="nombre" id="nombre" aria-describedby="helpId" placeholder="Nombre del Criadero">
      </div>

      <div class="mb-3">
        <label for="representanteId" class="form-label">Representante: </label>
        <select name="representanteId" id="representanteId" class="form-select">
          <option value="">Seleccione el Representante</option>
          <?php foreach ($representantes as $datorep) :
            $selected = ($datorep["ID"] == $representante) ? 'selected' : ''; 
            echo '<option value="' . $datorep["ID"] . '" ' .$selected . '>' . $datorep["nombreCompleto"] . '</option>';
          endforeach;
          ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="localidad" class="form-label">Lugar de Organización</label>
        <input value="<?php echo $lugar; ?>" type="text" class="form-control" name="localidad" id="localidad" aria-describedby="helpId" placeholder="">
      </div>

      <!--<div class="mb-3">
        <label for="creacion" class="form-label">Fecha de Creación</label>
        <input value="<?php //echo $fecha; ?>" type="date" class="form-control" name="creacion" id="creacion" aria-describedby="helpId" placeholder="Fecha desde que inicio">
      </div>-->

      <button type="submit" class="btn btn-success">Actualizar</button>
      <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

    </form>

    <?php include("../../templates/footer.php"); ?>