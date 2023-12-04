<!--A Design by W3layouts
Author: W3layout
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<?php 
$url_base = "http://localhost/SG/admin/"; 


session_start();
if($_POST){

	include("bd.php");
	print_r($_POST);

	$usuario=(isset($_POST['usuario']))?$_POST['usuario']:"";
	$clave=(isset($_POST['clave']))?$_POST['clave']:"";

	//Con esta sentencias verificamos el usuario y contraseña para mysql
	//$sentencia = $conexion->prepare("SELECT MAX(ID) as ID, apodo, clave, count(*) as n_usuario FROM usuarios WHERE apodo=:usuario AND clave=:clave");

	//Con esta sentencias verificamos el usuario y contraseña para mariadb
	$sentencia=$conexion->prepare("SELECT *, count(*) as n_usuario FROM usuarios WHERE apodo=:usuario AND clave=:clave");

	$sentencia->bindParam(":usuario",$usuario);
  	$sentencia->bindParam(":clave",$clave);

	$sentencia->execute();

	$lista_usuarios=$sentencia->fetch(PDO::FETCH_LAZY);

	if($lista_usuarios['n_usuario']>0){
		
		$_SESSION['apodo']=$lista_usuarios['apodo'];
		$_SESSION['logueado']=true;
		header("Location: index.php");
	
	}else{
		echo "El usuario o la contraseña no existe";
	}

	print_r($lista_usuarios);

}

?>
<!DOCTYPE html>
<head>
<title>SGP-LOGIN</title>
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
<link href="css/style-responsive.css" rel="stylesheet"/>
<!-- font CSS -->
<link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
<!-- font-awesome icons -->
<link rel="stylesheet" href="../css/font.css" type="text/css"/>
<link rel="stylesheet" href="../css/all.min.css"/>
<link href="../css/font-awesome.css" rel="stylesheet"> 
<!-- //font-awesome icons -->
<script src="../js/all.min.js"></script>
<script src="../js/jquery2.0.3.min.js"></script>
</head>
<body>
<div class="log-w3">
<div class="w3layouts-main">
	<h2>Ingresar</h2>
		<form action="#" method="post">
			<input type="text" class="ggg" name="usuario" placeholder="USUARIO" required="">
			<input type="password" class="ggg" name="clave" placeholder="CLAVE" required="">
			<span><input type="checkbox" />Recuerdame</span>
			<h6><a href="#">Olvidaste la Contraseña?</a></h6>
				<div class="clearfix"></div>
				<input type="submit" value="ENTRAR" name="login">
		</form>
		<!--<p>No tienes una Cuenta ?<a href="<?php #echo $url_base;?>secciones/usuarios/">Crear Usuario</a></p>-->
</div>
</div>
<script src="../js/bootstrap.js"></script>
<script src="../js/jquery.dcjqaccordion.2.7.js"></script>
<script src="../js/scripts.js"></script>
<script src="../js/jquery.slimscroll.js"></script>
<script src="../js/jquery.nicescroll.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot-chart/excanvas.min.js"></script><![endif]-->
<script src="../js/jquery.scrollTo.js"></script>
</body>
</html>
