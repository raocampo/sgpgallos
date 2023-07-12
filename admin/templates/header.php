<?php 
session_start();
$url_base = "http://localhost/SG/admin/"; 

if(!isset($_SESSION['apodo'])){
  echo print_r($_SESSION);
  header("Location:".$url_base."login.php");
}

?>

<!doctype html>
<html lang="es">

<head>
  <title>Página de Administración</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="<?php echo $url_base;?>bootstrap/css/bootstrap.min.css" rel="stylesheet"> 
  <link href="<?php echo $url_base;?>bootstrap/css/all.min.css" rel="stylesheet">  
  
  <link href="../../../css/estilo.css" rel='stylesheet' type='text/css' />
  <link href="../../../css/style.css" rel='stylesheet' type='text/css' />
  <link href="../../../css/style-responsive.css" rel="stylesheet" />
 
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
    <!-- //font-awesome icons -->
  <script src="../../../js/jquery.js"></script>
  <script src="../../../DataTables/datatables.min.js"></script>
  <script src="../../../js/all.min.js"></script>
  <!--<script src="../../../js/jquery2.0.3.min.js"></script>-->
  <script src="../../../js/jquery.min.js"></script>
  <script src="../../../js/raphael-min.js"></script>
  <script src="../../../js/morris.js"></script>
  <!--<script src="../../../js/cotejaManual.js"></script>-->
 
  <!-- Bootstrap CSS v5.2.1 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">-->
    

</head>

<body class="bg-transparent">
  <header>
    <!-- place navbar here -->

      <nav class="navbar navbar-expand navbar-light bg-light justify-content-center  py-5 bg-transparent">

        <div class="nav navbar-nav nav-tabs">

            <a class="nav-item nav-link <?php echo ($_SERVER['REQUEST_URI'] == $url_base) ? 'active' : ''; ?> " id="tab-inicio" href="<?php echo $url_base;?>" aria-current="page">Inicio</a>
            <a class="nav-item nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'secciones/torneos') !== false) ? 'active' : ''; ?>" id="tab-torneos" href="<?php echo $url_base;?>secciones/torneos/">Torneos</a>
            <a class="nav-item nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'secciones/representantes') !== false) ? 'active' : ''; ?> " id="tab-representantes" href="<?php echo $url_base;?>secciones/representantes/">Representantes</a>
            <a class="nav-item nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'secciones/familias') !== false) ? 'active' : ''; ?>" id="tab-familias" href="<?php echo $url_base;?>secciones/familias/">Criaderos</a>
            <!--<a class="nav-item nav-link <?php #echo (strpos($_SERVER['REQUEST_URI'], 'secciones/frente') !== false) ? 'active' : ''; ?>" id="tab-frente" href="<?php #echo $url_base;?>secciones/frente/">Frente</a>
            <a class="nav-item nav-link <?php #echo (strpos($_SERVER['REQUEST_URI'], 'secciones/gallos') !== false) ? 'active' : ''; ?>" id="tab-gallos" href="<?php #echo $url_base;?>secciones/gallos/">Gallos</a>
            
            <a class="nav-item nav-link <?php #echo (strpos($_SERVER['REQUEST_URI'], 'secciones/peleas/cotejamiento.php') !== false) ? 'active' : ''; ?>" id="tab-cotejamiento" href="<?php #echo $url_base;?>secciones/peleas/cotejamiento.php">Coteja</a>-->
            <a class="nav-item nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'secciones/usuarios') !== false) ? 'active' : ''; ?>" id="tab-usuarios" href="<?php echo $url_base;?>secciones/usuarios/">Usuarios</a>
            <a class="nav-item nav-link" href="<?php echo $url_base;?>cerrar.php">Cerrar Sesion</a>

        </div>
          
      </nav>
    
  </header>
  <main class="container-fluid bg-transparent">
    <br/>