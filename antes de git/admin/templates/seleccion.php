<?php 

session_start();

include("../bd.php");

if (isset($_GET['torneoId'])) {
    // Obtener el ID del torneo seleccionado
    $torneoId = $_GET['torneoId'];

    // Consultar la base de datos para obtener los detalles del torneo
    $sentencia = $conexion->prepare("SELECT nombre FROM torneos WHERE ID = :torneoId");
    $sentencia->bindParam(':torneoId', $torneoId, PDO::PARAM_INT);
    $sentencia->execute();

    // Obtener los detalles del torneo
    $torneo = $sentencia->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontraron los detalles del torneo
    if ($torneo) {
        $nombreTorneo = $torneo['nombre'];
        $torneoId = $torneo['ID'];

        $_SESSION['nombreTorneo'] = $nombreTorneo;
        $_SESSION['torneoId'] = $torneoId;

        header("Location: ../secciones/gallos/index.php");
        exit();
        // Ahora puedes utilizar $nombreTorneo para presentarlo en el menú
        // ...
    } else {
        // No se encontraron los detalles del torneo
        echo "No se encontraron detalles del torneo.";
        exit; // O realizar alguna otra acción en caso de error
    }
}



include ("../templates/header.sub.php");

?>



<?php include ("../templates/footer.php");?>


