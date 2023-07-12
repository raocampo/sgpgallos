<!--A Design by W3layouts
Author: W3layout
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<?php $url_base = "http://localhost/SG/admin/"; ?>
<!DOCTYPE html>
<head>
<title>MENU INICIO SPG</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Visitors Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- bootstrap-css -->
<link rel="stylesheet" href="../css/bootstrap.min.css" >
<!-- //bootstrap-css -->
<!-- Custom CSS -->
<link href="../css/style.css" rel='stylesheet' type='text/css' />
<link href="../css/style-responsive.css" rel="stylesheet"/>
<!-- font CSS -->
<link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
<!-- font-awesome icons -->
<link rel="stylesheet" href="../css/font.css" type="text/css"/>
<link rel="stylesheet" href="../css/all.min.css"/>
<link href="../css/font-awesome.css" rel="stylesheet"> 
<link rel="stylesheet" href="../css/morris.css" type="text/css"/>
<!-- calendar -->
<link rel="stylesheet" href="../css/monthly.css">
<!-- //calendar -->
<!-- //font-awesome icons -->
<script src="../js/all.min.js"></script>
<!--<script src="../js/jquery2.0.3.min.js"></script>-->
<script src="../js/raphael-min.js"></script>
<script src="../js/morris.js"></script>
</head>
<body>
<section id="container">
<!--header start-->
<header class="header fixed-top clearfix">
<!--logo start-->
<div class="brand">
    <img style="margin-top: 0; width:240px; height:80px;" src="../images/Logo.png" alt="ASOGAL">
    <!--<a href="index.html" class="logo"></a>-->
    <div class="sidebar-toggle-box">
        <div class="fa fa-bars"></div>
    </div>

</div>

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
                <span class="username">ADMINISTRADOR</span>
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
</header>
<!--header end-->
<!--sidebar start-->
<aside>
    <div id="sidebar" class="nav-collapse">
        <!-- sidebar menu start-->
        <div class="leftside-navigation">
            <ul class="sidebar-menu" id="nav-accordion">
                <li>
                    <a class="active" href="index.html">
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sub-menu">
                <a href="<?php echo $url_base;?>secciones/torneos/"> <i class="fa-solid fa-trophy"></i> Torneos </a>
                
                    <!--<a href="javascript:;">
                        <i class="fa-solid fa-trophy"></i>
                        <span>Torneo</span>
                    </a>
                    <ul class="sub">
                        <li><a href="<?php # echo $url_base;?>secciones/torneos/">Crear Torneo</a></li>
                        <li><a href="<?php #echo $url_base;?>secciones/torneos/cotejamiento.php">Cotejamiento</a></li>
						<!--<li><a href="dropzone.html">Dropzone</a></li>
                    </ul>-->
                </li>
                
                <li class="sub-menu">
                    
                    <a href="<?php echo $url_base;?>secciones/representantes/"><i class="fa-solid fa-person"></i> Representante-Org</a>
                    <!--<a href="javascript:;">
                        <!--<i class="fa fa-book"></i>
                        <i class="fa-solid fa-person"></i>
                        <span>Representante-Org</span>
                    </a>
                    <ul class="sub">
						<li><a href="<?php #echo $url_base;?>secciones/representantes/">Inicio</a></li>
						<!--<li><a href="glyphicon.html">Editar</a></li>
                        <li><a href="grids.html">Grids</a></li>
                    </ul>-->
                </li>
                <li class="sub-menu">
                    <a href="<?php echo $url_base;?>secciones/familias/"><i class="fa-solid fa-building-ngo"></i> Criadero </a>
                   <!-- <a href="fontawesome.html">
                        <!--<i class="fa fa-bullhorn"></i>
                        <i class="fa-solid fa-building-ngo"></i>
                        <span>Familias/Organización</span>
                    </a>
					<ul class="sub">
						<li><a href="<?php #echo $url_base;?>secciones/familias/">Ingresar</a></li>
					</ul>-->
                </li>
                <!--<li class="sub-menu">
                <a href="<?php #echo $url_base;?>secciones/gallos/"><i class="fa-solid fa-hand-lizard"></i> Gallos </a>
                    <!--<a href="javascript:;">
                        <i class="fa-solid fa-hand-lizard"></i>
                        <span>Gallos</span>
                    </a>
                    <ul class="sub">
                        <li><a href="<?php #echo $url_base;?>secciones/gallos/">Ingresar Gallos</a></li>
                        <!--<li><a href="responsive_table.html">Responsive Table</a></li>
                    </ul>
                </li>-->
                
                <!--<li class="sub-menu">
                    <a href="<?php #echo $url_base;?>secciones/peleas/cotejamiento.php"> <i class="fa-solid fa-trophy"></i> Cotejas </a>
                </li>
                <li class="sub-menu">
                     <a href="<?php #echo $url_base;?>secciones/configuraciones"> <i class="fa-solid fa-gear"></i> Configuraciones </a>
                    <!--<a href="javascript:;">
                        <i class="fa-solid fa-gear"></i>
                        <span> Configuraciones </span>
                    </a>
                    <ul class="sub">
                        <li><a href="<?php #echo $url_base;?>secciones/configuraciones/">Ingresar</a></li>
                        <!--<li><a href="mail_compose.html">Compose Mail</a></li>
                    </ul>
                </li>-->
                <li class="sub-menu">
                <a href="<?php echo $url_base;?>secciones/usuarios"> <i class="fa-solid fa-user"></i> Usuarios </a>
                    <!--<a href="javascript:;">
                        <i class="fa-solid fa-user"></i>
                        <span>Usuarios</span>
                    </a>
                    <ul class="sub">
                        <li><a href="<?php # echo $url_base;?>secciones/usuarios/">Registrar Usuario</a></li>
                        <!--<li><a href="flot_chart.html">Flot Charts</a></li>
                    </ul>-->
                </li>
                <!--<li class="sub-menu">
                    <a href="javascript:;">
                        <i class=" fa fa-bar-chart-o"></i>
                        <span>Maps</span>
                    </a>
                    <ul class="sub">
                        <li><a href="google_map.html">Google Map</a></li>
                        <li><a href="vector_map.html">Vector Map</a></li>
                    </ul>
                </li>
                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-glass"></i>
                        <span>Extra</span>
                    </a>
                    <ul class="sub">
                        <li><a href="gallery.html">Gallery</a></li>
						<li><a href="404.html">404 Error</a></li>
                        <li><a href="registration.html">Registration</a></li>
                    </ul>
                </li>-->
                <li>
                    <a href="<?php echo $url_base;?>cerrar.php">
                        <!--<i class="fa fa-user"></i>-->
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Cerrar Sesion</span>
                    </a>
                </li>
            </ul>            
        </div>
        <!-- sidebar menu end-->
    </div>
</aside>
<!--sidebar end-->

 <!-- footer -->
 	<div class="footer">
		<div class="wthree-copyright">
		  <p>© 2023 All rights reserved | Design by <a href="#">CorpSimtelec</a></p>
		</div>
    </div>
  <!-- / footer -->
</section>
<!--main content end-->
</section>
<script src="../js/bootstrap.js"></script>
<script src="../js/jquery.dcjqaccordion.2.7.js"></script>
<script src="../js/scripts.js"></script>
<script src="../js/jquery.slimscroll.js"></script>
<script src="../js/jquery.nicescroll.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot-chart/excanvas.min.js"></script><![endif]-->
<script src="../js/jquery.scrollTo.js"></script>
<!-- morris JavaScript -->	
<script>
	$(document).ready(function() {
		//BOX BUTTON SHOW AND CLOSE
	   jQuery('.small-graph-box').hover(function() {
		  jQuery(this).find('.box-button').fadeIn('fast');
	   }, function() {
		  jQuery(this).find('.box-button').fadeOut('fast');
	   });
	   jQuery('.small-graph-box .box-close').click(function() {
		  jQuery(this).closest('.small-graph-box').fadeOut(200);
		  return false;
	   });
	   
	    //CHARTS
	    function gd(year, day, month) {
			return new Date(year, month - 1, day).getTime();
		}
		
		graphArea2 = Morris.Area({
			element: 'hero-area',
			padding: 10,
        behaveLikeLine: true,
        gridEnabled: false,
        gridLineColor: '#dddddd',
        axes: true,
        resize: true,
        smooth:true,
        pointSize: 0,
        lineWidth: 0,
        fillOpacity:0.85,
			data: [
				{period: '2015 Q1', iphone: 2668, ipad: null, itouch: 2649},
				{period: '2015 Q2', iphone: 15780, ipad: 13799, itouch: 12051},
				{period: '2015 Q3', iphone: 12920, ipad: 10975, itouch: 9910},
				{period: '2015 Q4', iphone: 8770, ipad: 6600, itouch: 6695},
				{period: '2016 Q1', iphone: 10820, ipad: 10924, itouch: 12300},
				{period: '2016 Q2', iphone: 9680, ipad: 9010, itouch: 7891},
				{period: '2016 Q3', iphone: 4830, ipad: 3805, itouch: 1598},
				{period: '2016 Q4', iphone: 15083, ipad: 8977, itouch: 5185},
				{period: '2017 Q1', iphone: 10697, ipad: 4470, itouch: 2038},
			
			],
			lineColors:['#eb6f6f','#926383','#eb6f6f'],
			xkey: 'period',
            redraw: true,
            ykeys: ['iphone', 'ipad', 'itouch'],
            labels: ['All Visitors', 'Returning Visitors', 'Unique Visitors'],
			pointSize: 2,
			hideHover: 'auto',
			resize: true
		});
		
	   
	});
	</script>
<!-- calendar -->
	<script type="text/javascript" src="../js/monthly.js"></script>
	<script type="text/javascript">
		$(window).load( function() {

			$('#mycalendar').monthly({
				mode: 'event',
				
			});

			$('#mycalendar2').monthly({
				mode: 'picker',
				target: '#mytarget',
				setWidth: '250px',
				startHidden: true,
				showTrigger: '#mytarget',
				stylePast: true,
				disablePast: true
			});

		switch(window.location.protocol) {
		case 'http:':
		case 'https:':
		// running on a server, should be good.
		break;
		case 'file:':
		alert('Just a heads-up, events will not work when run locally.');
		}

		});
	</script>
	<!-- //calendar -->
</body>
</html>
