<?php
session_start();

$url_base = "http://localhost/SG/admin/";

if (!isset($_SESSION['apodo'])) {
  echo print_r($_SESSION);
  header("Location:" . $url_base . "login.php");
}

// Verificar si los parámetros se pasaron por la URL o si están almacenados en las variables de sesión
if (isset($_GET['nombreTorneo']) && isset($_GET['torneoId'])) {
  $nombreTorneo = $_GET['nombreTorneo'];
  $torneoId = $_GET['torneoId'];

  // Guardar los datos del torneo en variables de sesión
  $_SESSION['nombreTorneo'] = $nombreTorneo;
  $_SESSION['torneoId'] = $torneoId;
} elseif (isset($_SESSION['nombreTorneo']) && isset($_SESSION['torneoId'])) {
  // Obtener los datos del torneo de las variables de sesión
  $nombreTorneo = $_SESSION['nombreTorneo'];
  $torneoId = $_SESSION['torneoId'];
} else {
  // Redireccionar si los parámetros no están presentes en la URL y no están almacenados en las variables de sesión
  header("Location: " . $url_base);
  exit();
}

?>


<!doctype html>
<html lang="es">

<head>
  <title>Página de Administración</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="<?php echo $url_base; ?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $url_base; ?>bootstrap/css/all.min.css" rel="stylesheet">

  <link href="../../../css/estilo.css" rel='stylesheet' type='text/css' />
  <link href="../../../css/style.css" rel='stylesheet' type='text/css' />
  <link href="../../../css/style-responsive.css" rel="stylesheet" />
  <!--<link href="../../../css/estilo.css" rel='stylesheet' type='text/css' />-->
  <!-- font CSS -->
  <link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
  <!-- font-awesome icons -->
  <link rel="stylesheet" href="../../../css/all.min.css" />
  <link rel="stylesheet" href="../../../css/font.css" type="text/css" />
  <link href="../../../css/font-awesome.css" rel="stylesheet">
  <link rel="stylesheet" href="../../../css/morris.css" type="text/css" />
  <!-- calendar -->
  <link rel="stylesheet" href="../../../css/monthly.css">
  <link href="../../../DataTables/datatables.min.css" rel="stylesheet"/>
  <!-- //calendar -->
  <!--Estilos propios-->
  
  <!-- //font-awesome icons -->
  <script src="../../../js/jquery.js"></script>
  <script src="../../../DataTables/datatables.min.js"></script> 
  <!--<script src="../../../DataTables/jQuery-3.6.0/jquery-3.6.0.min.js"></script>-->
  <script src="../../../js/all.min.js"></script>
  <!--<script src="../../../js/jquery2.0.3.min.js"></script>  -->
  <script src="../../../js/raphael-min.js"></script>
  <script src="../../../js/morris.js"></script>
  <!--<script src="../../../js/cotejaManual.js"></script>-->
  <!-- Bootstrap CSS v5.2.1 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">-->


</head>

<body class="bg-transparent">
  <header>
    <!-- Agregar el logo 
    <img class="logo" src="../../../images/Logo.png" alt="">-->

    <div class=" text-center">
      <h1>Torneo: <?php echo $nombreTorneo; ?></h1>
    </div>

  </header>
  <main class="container-fluid bg-transparent">
    <br />
    