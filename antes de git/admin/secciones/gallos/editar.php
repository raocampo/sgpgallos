<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include("../../bd.php");

if (isset($_GET['txtID'])) {
  //En esta sentencias se recuperan los datos a editar con el ID que se escoja
  $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";

  $sentencia = $conexion->prepare("SELECT * FROM gallos WHERE ID=:id");
  $sentencia->bindParam(":id", $txtID);
  $sentencia->execute();

  $registro = $sentencia->fetch(PDO::FETCH_LAZY);

  $anillo = $registro['anillo'];
  $pesoR = $registro['pesoReal'];
  $alturaR = $registro['tamañoReal'];
  $placa = $registro['placa'];
  $nacimiento = $registro['nacimiento'];
  $frente = $registro['frente'];
  $familia = $registro['familiasId'];
  $representante = $registro['representanteId'];
}

if ($_POST) {

  $txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
  $anillo = (isset($_POST['anillo'])) ? $_POST['anillo'] : "";
  $pesoR = (isset($_POST['pesoReal'])) ? $_POST['pesoReal'] : "";
  $alturaR = (isset($_POST['alturaReal'])) ? $_POST['alturaReal'] : "";
  $placa = (isset($_POST['placa'])) ? $_POST['placa'] : "";
  $nacimiento = (isset($_POST['nacimiento'])) ? $_POST['nacimiento'] : "";
  $frente = (isset($_POST['frente'])) ? $_POST['frente'] : "";
  $familia = (isset($_POST['familiasId'])) ? $_POST['familiasId'] : "";
  $representante = (isset($_POST['representanteId'])) ? $_POST['representanteId'] : "";

  /*$color=(isset($_POST['color']))?$_POST['color']:"";
  $imagen=(isset($_FILES['imagen']['name']))?$_FILES['imagen']['name']:"";
  $raza=(isset($_POST['raza']))?$_POST['raza']:"";*/

  $sentencia = $conexion->prepare("UPDATE `gallos` INNER JOIN familias ON gallos.familiasId = familias.codigo
  INNER JOIN representante ON gallos.representanteId = representante.ID SET gallos.anillo=:anillo, gallos.pesoReal=:pesoReal, gallos.tamañoReal=:alturaReal, gallos.placa=:placa, gallos.nacimiento=:nacimiento, gallos.frente=:frente, gallos.familiasId=:familiasId, gallos.representanteId=:representanteId WHERE gallos.ID=:id");

  $sentencia->bindParam(":anillo", $anillo);
  $sentencia->bindParam(":pesoReal", $pesoR);
  $sentencia->bindParam(":alturaReal", $alturaR);
  $sentencia->bindParam(":placa", $placa);
  $sentencia->bindParam(":nacimiento", $nacimiento);
  $sentencia->bindParam(":frente", $frente);
  $sentencia->bindParam(":familiasId", $familia);
  $sentencia->bindParam(":representanteId", $representante);

  $sentencia->bindParam(":id", $txtID);

  $sentencia->execute();

  if ($sentencia === TRUE) {
    echo "Cambios guardados";
  } else {
    echo "No se pudo actualizar. ";
    print_r($sentencia->errorInfo());
  }

  $mensaje = "Se edito el registro...!";
  header("Location:index.php?mensaje= " . $mensaje);

  /*$sentencia->bindParam(":color",$color);
  $sentencia->bindParam(":raza",$raza);
  if($_FILES["imagen"]["tmp_name"]!=""){

    $imagen=(isset($_FILES["imagen"]["name"]))?$_FILES["imagen"]["name"]:"";

    $fecha_image=new DateTime();
    $nombre_archivo_imagen=($imagen!="")? $fecha_imagen->getTimestamp()."_".$imagen:"";

    $tmp_imagen=$_FILES["imagen"]["tmp_name"];
    if($tmp_imagen!=""){
      move_uploaded_file($tmp_imagen,"../../../assets/img/gallos/".$nombre_archivo_imagen);
    }
    
    
    /*$txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("SELECT imagen FROM gallos WHERE ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
    $registro_imagen=$sentencia->fetch(PDO::FETCH_LAZY);

    if(isset($registro_imagen["imagen"])){

        if(file_exists("../../../assets/img/gallos/".$registro_imagen["imagen"])){
            
            unlink("../../../assets/img/gallos/".$registro_imagen["imagen"]);
        
        }
    }

    $sentencia=$conexion->prepare("UPDATE `gallos` SET imagen=:imagen, WHERE id=:ID");
    $sentencia->bindParam(":imagen",$nombre_archivo_imagen);
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

  }*/
}

//Con esta sentencias seleccionamos los datos de la tabla de familias
$sentencia = $conexion->prepare("SELECT * FROM familias WHERE codigo");
$sentencia->execute();

$familias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

//Con esta sentencias seleccionamos los datos de la tabla de representantes
$sentencia = $conexion->prepare("SELECT * FROM representante WHERE ID");
$sentencia->execute();

$representantes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

//Con esta sentencias seleccionamos los datos de la tabla de torneos
$sentencia=$conexion->prepare("SELECT * FROM `torneos`");
$sentencia->execute();

$lista_torn=$sentencia->fetchAll(PDO::FETCH_ASSOC);

include("../../templates/header.sub.php"); ?>

<div class="card-header">
  Modificar Gallos
</div>

<div class="card-body">

  <form action="" enctype="multipart/form-data" method="post">

    <div class="mb-3">
      <label for="txtID" class="form-label">ID:</label>
      <input readonly value="<?php echo $txtID; ?>" type="text" class="form-control" name="txtID" id="txtID" aria-describedby="helpId" placeholder="ID">
    </div>

    <div class="mb-3">
      <label for="anillo" class="form-label">Anillo</label>
      <input value="<?php echo $anillo; ?>" type="text" class="form-control" name="anillo" id="anillo" aria-describedby="helpId" placeholder="">
    </div>

    <div class="mb-3">
      <label for="pesoReal" class="form-label">Peso</label>
      <input value="<?php echo $pesoR; ?>" type="number" step="any" class="form-control" name="pesoReal" id="pesoReal" aria-describedby="helpId" placeholder="">
    </div>

    <div class="mb-3">
      <label for="alturaReal" class="form-label">Tamaño</label>
      <input value="<?php echo $alturaR; ?>" type="number" step="any" class="form-control" name="alturaReal" id="alturaReal" aria-describedby="helpId" placeholder="">
    </div>

    <div class="mb-3">
      <label for="placa" class="form-label">Placa</label>
      <input value="<?php echo $placa; ?>" type="text" class="form-control" name="placa" id="placa" aria-describedby="helpId" placeholder="">
    </div>

    <div class="mb-3">
      <label for="nacimiento" class="form-label">Mes de Nacimiento</label>
      <input value="<?php echo $nacimiento; ?>" type="text" class="form-control" name="nacimiento" id="nacimiento" aria-describedby="helpId" placeholder="">
    </div>

    <div class="mb-3">
      <label for="frente" class="form-label">Frente</label>
      <input value="<?php echo $frente; ?>" type="text" class="form-control" name="frente" id="frente" aria-describedby="helpId" placeholder="">
    </div>

    <div class="mb-3">
      <label for="familiasId" class="form-label">Familia-Organización-Club-Cuerda: </label>
      <select name="familiasId" id="familiasId" class="form-select">
        <option value="">Seleccione la Cuerda</option>
        <?php foreach ($familias as $datofam) :
          $selectedFam = ($datofam['codigo'] == $familia) ? 'selected' : '';
          echo '<option value="' . $datofam["codigo"] . '" ' . $selectedFam . '>' . $datofam["nombre"] . '</option>';
        endforeach;
        ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="representanteId" class="form-label">Representante: </label>
      <select name="representanteId" id="representanteId" class="form-select">
        <option value="">Seleccione el Representante</option>
        <?php foreach ($representantes as $datorep) :
          $selectedRep = ($datorep['ID'] == $representante) ? 'selected' : '';
          echo '<option value="' . $datorep["ID"] . '" ' . $selectedRep . '>' . $datorep["nombreCompleto"] . '</option>';
        endforeach;
        ?>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Actualizar</button>
    <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

  </form>

  <?php include("../../templates/footer.php"); ?>