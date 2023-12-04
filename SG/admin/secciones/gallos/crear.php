<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include("../../bd.php");

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$nombreTorneo = $_SESSION['nombreTorneo'];
$torneoId = $_SESSION['torneoId'];

if($_POST){
  
  //Se realiza la recepción de los datos
  $anillo=(isset($_POST['anillo']))?$_POST['anillo']:"";
  $pesoR=(isset($_POST['pesoReal']))?$_POST['pesoReal']:"";
  $alturaR=(isset($_POST['alturaReal']))?$_POST['alturaReal']:"";
  $placa=(isset($_POST['placa']))?$_POST['placa']:"";
  $nacimiento=(isset($_POST['nacimiento']))?$_POST['nacimiento']:"";
  $frente=(isset($_POST['frente']))?$_POST['frente']:"";
  $fam=(isset($_POST['familiasId']))?$_POST['familiasId']:"";
  $rep=(isset($_POST['representanteId']))?$_POST['representanteId']:"";
  //print_r($_POST);

  // Verificar si ya existe un gallo con el mismo anillo en el torneo actual
    $sentenciaVerificacion = $conexion->prepare("SELECT COUNT(*) FROM gallos WHERE anillo = ? AND torneoId = ?");
    $sentenciaVerificacion->execute([$anillo, $torneoId]);
    $galloExistente = $sentenciaVerificacion->fetchColumn();

    if ($galloExistente) {
        echo "<script>alert('Ya existe un gallo con el mismo anillo en este torneo. Por favor, ingrese un anillo diferente.');</script>";
    }else{
      $sentencia=$conexion->prepare("INSERT INTO `gallos` (`ID`, `anillo`, `pesoReal`, `tamañoReal`, `placa`, `nacimiento`, `frente`, `familiasId`, `representanteId`, `torneoId`) VALUES (NULL, :anillo, :pesoReal, :alturaReal, :placa, :nacimiento, :frente, :familiasId, :representanteId, :torneoId)");

  $sentencia->bindParam(":anillo",$anillo);
  $sentencia->bindParam(":pesoReal",$pesoR);
  $sentencia->bindParam(":alturaReal",$alturaR);
  $sentencia->bindParam(":placa",$placa);
  $sentencia->bindParam(":nacimiento",$nacimiento);
  $sentencia->bindParam(":frente",$frente);
  $sentencia->bindParam(":familiasId",$fam);
  $sentencia->bindParam(":representanteId",$rep);
  $sentencia->bindParam(":torneoId",$torneoId); 
  
  
  if($sentencia->execute()){
    $mensaje="Se creo el registro...!";
    header("Location:index.php?mensaje= ".$mensaje);
  }
  else{
    echo "El Registro no se agrego";
  }
    }
  
}

//Con esta sentencias seleccionamos los datos de la tabla de familias
$sentencia=$conexion->prepare("SELECT * FROM familias WHERE codigo ORDER BY nombre ASC");
$sentencia->execute();

$familias=$sentencia->fetchAll(PDO::FETCH_ASSOC);

//Con esta sentencias seleccionamos los datos de la tabla de representantes
$sentencia=$conexion->prepare("SELECT * FROM representante WHERE ID ORDER BY nombreCompleto ASC ");
$sentencia->execute();

$representantes=$sentencia->fetchAll(PDO::FETCH_ASSOC);


include("../../templates/header.sub.php"); ?>


<div class="card">
    <div class="card-header">
        Crear Gallos
    </div>
    
    <div class="card-body">
        
    <form action="" enctype="multipart/form-data" method="post">

        <input type="hidden" name="torneoId" id="torneoId" value="<?php echo $torneoId; ?>">

        <div class="mb-3">
          <label for="anillo" class="form-label">Anillo</label>
          <input type="text"
            class="form-control" name="anillo" id="anillo" aria-describedby="helpId" placeholder="Ingrese el anillo del gallo">
        </div>

        <div class="mb-3">
          <label for="pesoReal" class="form-label">Peso</label>
          <input type="number" step="any"
            class="form-control" name="pesoReal" id="pesoReal" aria-describedby="helpId" placeholder="Ingrese el peso">
        </div>

        <div class="mb-3">
          <label for="alturaReal" class="form-label">Tamaño</label>
          <input type="number" step="any"
            class="form-control" name="alturaReal" id="alturaReal" aria-describedby="helpId" placeholder="Ingrese la altura">
        </div>

        <div class="mb-3">
          <label for="placa" class="form-label">Placa</label>
          <input type="text"
            class="form-control" name="placa" id="placa" aria-describedby="helpId" placeholder="Ingrese placa del gallo">
        </div>

        <div class="mb-3">
          <label for="nacimiento" class="form-label">Mes de Nacimiento</label>
          <input type="text"
            class="form-control" name="nacimiento" id="nacimiento" aria-describedby="helpId" placeholder="Ingrese raza del gallo">
        </div>

        <div class="mb-3">
          <label for="familiasId" class="form-label">Frente</label>
          <input type="text"
            class="form-control" name="frente" id="frente" aria-describedby="helpId" placeholder="Ingresa frente">
        </div>

        <div class="mb-3">
          <label for="familiasId" class="form-label">Familia-Organización-Club: </label>
          <select name="familiasId" id="familiasId" class="form-select">
          <option value="">Seleccione la Cuerda</option>
            <?php foreach ($familias as $datofam):
              echo '<option value="'.$datofam["codigo"].'">'.$datofam["nombre"].'</option>';
            endforeach;
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label for="representanteId" class="form-label">Representante: </label>
          <select name="representanteId" id="representanteId" class="form-select">
            <option value="">Seleccione el Representante</option>
            <?php foreach ($representantes as $datorep):
              echo '<option value="'.$datorep["ID"].'">'.$datorep["nombreCompleto"].'</option>';
            endforeach;
            ?>
          </select>
        </div>

       <button type="submit" class="btn btn-success">Guardar</button>
       <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>      

    </form>

    </div>
</div>

<?php include ("../../templates/footer.php");?>