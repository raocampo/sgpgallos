<?php $url_base = "http://localhost/SG/admin/"; ?>

<!doctype html>
<html lang="es">

<head>
  <title>Torneos</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
  <!-- bootstrap-css -->
  <link rel="stylesheet" href="../../css/bootstrap.min.css" >
  <!-- //bootstrap-css -->
  <!-- Custom CSS -->
  <link href="../../css/style.css" rel='stylesheet' type='text/css' />
  <link href="../../css/style-responsive.css" rel="stylesheet"/>
  <!-- font CSS -->
  <link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
  <!-- font-awesome icons -->
  <link rel="stylesheet" href="../../css/font.css" type="text/css"/>
  <link href="../../css/font-awesome.css" rel="stylesheet"> 
  <link rel="stylesheet" href="../../css/morris.css" type="text/css"/>
  <!-- calendar -->
  <link rel="stylesheet" href="../../css/monthly.css">
  <!-- //calendar -->
  <!-- //font-awesome icons -->
  <script src="../../js/jquery2.0.3.min.js"></script>
  <script src="../../js/raphael-min.js"></script>
  <script src="../../js/morris.js"></script>
  <!-- Bootstrap CSS v5.2.1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
</head>

<body>
  <header>
  <div class="top-nav clearfix">
      <!--search & user info start-->
      <ul class="nav pull-right top-menu">
          <li>
              <input type="text" class="form-control search" placeholder=" Search">
          </li>
          <!-- user login dropdown start-->
          <li class="dropdown">
              <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                  <img alt="" src="images/2.png">
                  <span class="username">John Doe</span>
                  <b class="caret"></b>
              </a>
              <ul class="dropdown-menu extended logout">
                  <li><a href="#"><i class=" fa fa-suitcase"></i>Profile</a></li>
                  <li><a href="#"><i class="fa fa-cog"></i> Settings</a></li>
                  <li><a href="login.html"><i class="fa fa-key"></i> Log Out</a></li>
              </ul>
          </li>
          <!-- user login dropdown end -->
        
      </ul>
      <!--search & user info end-->
    </div>
    <!-- place navbar here -->
    <nav class="navbar navbar-expand navbar-light bg-light">
        <div class="nav navbar-nav">
            <a class="nav-item nav-link <?php echo ($_SERVER['REQUEST_URI'] == $url_base) ? 'active' : ''; ?> " id="tab-inicio" href="<?php echo $url_base;?>" aria-current="page">Inicio</a>

            <a class="nav-item nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'secciones/gallos') !== false) ? 'active' : ''; ?>" id="tab-gallos" href="<?php echo $url_base;?>secciones/gallos/">Gallos</a>

            <a class="nav-item nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'secciones/peleas/cotejamiento.php') !== false) ? 'active' : ''; ?>" id="tab-cotejamiento" href="<?php #echo $url_base;?>secciones/peleas/cotejamiento.php">Coteja</a>

            <a class="nav-item nav-link" href="<?php echo $url_base;?>cerrar.php">Cerrar Sesion</a>

        </div>
    </nav>
    
  </header>
  <main class="container">
    <br/>