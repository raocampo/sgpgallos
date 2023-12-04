<?php

include("../../bd.php");

if ($_POST) {

    //Se realiza la recepción de los datos
    $frente = (isset($_POST['frente'])) ? $_POST['frente'] : "";
    $familiaId = (isset($_POST['familiaId'])) ? $_POST['familiaId'] : "";
    $representanteId = (isset($_POST['representanteId'])) ? $_POST['representanteId'] : "";


    $sentencia = $conexion->prepare("INSERT INTO `frente` (`codigo`, `frente`, `familiaId`, `representanteId`) VALUES (NULL, :frente, :familiaId, :representanteId)");

    $sentencia->bindParam(":frente", $frente);
    $sentencia->bindParam(":familiaId", $familiaId);
    $sentencia->bindParam(":representanteId", $representanteId);


    if ($sentencia->execute()) {
        $mensaje = "Se creo el registro...!";
        header("Location:index.php?mensaje= " . $mensaje);
    } else {
        echo "El Registro no se agrego";
    }
}

//Con esta sentencias seleccionamos los datos de la tabla de familias
$sentencia = $conexion->prepare("SELECT * FROM familias WHERE codigo");
$sentencia->execute();

$familia = $sentencia->fetchAll(PDO::FETCH_ASSOC);

//Con esta sentencias seleccionamos los datos de la tabla de representantes
$sentencia = $conexion->prepare("SELECT * FROM representante WHERE ID");
$sentencia->execute();

$representante = $sentencia->fetchAll(PDO::FETCH_ASSOC);

include("../../templates/header.php");

?>

<div class="card">
    <div class="card-header">
        Crear Frente
    </div>

    <div class="card-body">

        <form action="" enctype="multipart/form-data" method="post">

            <div class="mb-3">
                <label for="nombre" class="form-label">Frente</label>
                <input type="text" step="any" class="form-control" name="frente" id="frente" aria-describedby="helpId" placeholder=" ">
            </div>

            <div class="mb-3">
                <label for="familiaId" class="form-label">Familia-Organización-Club-Cuerda: </label>
                <select name="familiaId" id="familiaId" class="form-select">
                    <option value="">Seleccione la Cuerda</option>
                    <?php foreach ($familia as $datofam) :
                        echo '<option value="' . $datofam["codigo"] . '">' . $datofam["nombre"] . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="representanteId" class="form-label">Representante: </label>
                <select name="representanteId" id="representanteId" class="form-select">
                    <option value="">Seleccione el Representante</option>
                    <?php foreach ($representante as $datorep) :
                        echo '<option value="' . $datorep["ID"] . '">' . $datorep["nombreCompleto"] . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Guardar</button>
            <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

        </form>

    </div>
</div>

<?php include("../../templates/footer.php"); ?>