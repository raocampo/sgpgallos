<?php
// procExclusiones.php

error_reporting(E_ERROR | E_WARNING | E_PARSE);

include("../../bd.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$torneoId = $_SESSION['torneoId'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $familia1 = $_POST['familia1'];
    $familia2 = $_POST['familia2'];

    // Verificar si las familias seleccionadas no están vacías
    if (!empty($familia1) && !empty($familia2)) {
        // Verificar si las familias ya están excluidas
        $sentenciaVerificacion = $conexion->prepare("SELECT COUNT(*) FROM exclusiones WHERE (nombreFamiliaUno = ? AND nombreFamiliaDos = ?) OR (nombreFamiliaUno = ? AND nombreFamiliaDos = ?) AND torneoId = ?");
        $sentenciaVerificacion->execute([$familia1, $familia2, $familia2, $familia1, $torneoId]);
        $exclusionesExistente = $sentenciaVerificacion->fetchColumn();

        if ($exclusionesExistente) {
            echo "Estas familias ya están excluidas en este torneo.";
        } else {
            // Insertar las exclusiones en la tabla
            $sentencia = $conexion->prepare("INSERT INTO exclusiones (nombreFamiliaUno, nombreFamiliaDos, torneoId) VALUES (?, ?, ?)");
            $sentencia->execute([$familia1, $familia2, $torneoId]);

            echo "Las familias se han excluido correctamente.";
        }
    } else {
        echo "Por favor, seleccione dos familias para excluir.";
    }
} else {
    echo "Acceso inválido a la página.";
}

include("../../templates/header.sub.php");
?>


<?php include("../../templates/footer.php"); ?>
