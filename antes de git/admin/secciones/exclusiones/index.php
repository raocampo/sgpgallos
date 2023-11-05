<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include("../../bd.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$nombreTorneo = $_SESSION['nombreTorneo'];
$torneoId = $_SESSION['torneoId'];

// Obtener las familias disponibles desde la base de datos
$sentencia = $conexion->prepare("SELECT * FROM familias");
$sentencia->execute();
$familias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

include("../../templates/header.sub.php");
?>

<div class="container text-center">
    <div class="row">
        <div class="col-4">

        </div>
        <div class="card col-4 ">
            <div class="card-header">
                <h2>Exclusiones</h2>
            </div>
            <div class="card-body d-flex justify-content-center ">
                <form action="procExclusiones.php" method="POST">

                    <p>Seleccione las familias a excluir:</p>

                    <div>
                        <label class="m-3" for="familia1">Familia 1:</label>
                        <select name="familia1" id="familia1">
                            <option value="">Seleccione una familia</option>
                            <?php foreach ($familias as $familia) : ?>
                                <option value="<?php echo $familia['codigo']; ?>"><?php echo $familia['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="m-3" for="familia2">Familia 2:</label>
                        <select name="familia2" id="familia2">
                            <option value="">Seleccione una familia</option>
                            <?php foreach ($familias as $familia) : ?>
                                <option value="<?php echo $familia['codigo']; ?>"><?php echo $familia['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="btn btn-success mt-3" type="submit">Agregar exclusión</button>
                </form>
            </div>
        </div>

    </div>


</div>

<?php include("../../templates/footer.php"); ?>