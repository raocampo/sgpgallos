<?php

include("../../bd.php");

if (isset($_GET['txtID'])) {
    //En esta sentencias se recuperan los datos a editar con el ID que se escoja
    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";

    $sentencia = $conexion->prepare("SELECT * FROM frente WHERE codigo=:id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();

    $registro = $sentencia->fetch(PDO::FETCH_LAZY);

    $nombre = $registro['frente'];
    $familia = $registro['familiaId'];
    $representante = $registro['representanteId'];
}

if ($_POST) {

    $txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
    $nombre = (isset($_POST['frente'])) ? $_POST['frente'] : "";
    $familia = (isset($_POST['familiaId'])) ? $_POST['familiaId'] : "";
    $representante = (isset($_POST['representanteId'])) ? $_POST['representanteId'] : "";
    
    $sentencia = $conexion->prepare("UPDATE `frente` INNER JOIN familias ON frente.familiaId = familias.codigo
    INNER JOIN representante ON frente.representanteId = representante.ID SET frente=:frente WHERE frente.codigo=:id");

    $sentencia->bindParam(":frente", $nombre);
    //$sentencia->bindParam(":familiaId", $familia);
    //$sentencia->bindParam(":representanteId", $representante);
    
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
}

//Con esta sentencias seleccionamos los datos de la tabla de familias
$sentencia = $conexion->prepare("SELECT * FROM familias WHERE codigo");
$sentencia->execute();

$familias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

//Con esta sentencias seleccionamos los datos de la tabla de representantes
$sentencia = $conexion->prepare("SELECT * FROM representante WHERE ID");
$sentencia->execute();

$representantes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

include("../../templates/header.php"); ?>


<div class="card">
    <div class="card-header">
        Editar Frente
    </div>

    <div class="card-body">

        <form action="" enctype="multipart/form-data" method="post">

            <div class="mb-3">
                <label for="txtID" class="form-label">ID:</label>
                <input readonly value="<?php echo $txtID; ?>" type="text" class="form-control" name="txtID" id="txtID" aria-describedby="helpId" placeholder="ID">
            </div>

            <div class="mb-3">
                <label for="nombre" class="form-label">Frente</label>
                <input type="text" value="<?php echo $nombre; ?>" class="form-control" name="frente" id="frente" aria-describedby="helpId" placeholder=" ">
            </div>

            <div class="mb-3">
                <label for="familiaId" class="form-label">Familia-Organización-Club-Cuerda: </label>
                <select name="familiaId" id="familiaId" class="form-select">
                    <option value=""></option>
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
                    <option value=""></option>
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