<?php

include("../../bd.php");

if($_POST){

//Se realiza la recepción de los datos
  $nombre=(isset($_POST['nombre']))?$_POST['nombre']:"";
  $correo=(isset($_POST['correo']))?$_POST['correo']:"";
  $usuario=(isset($_POST['usuario']))?$_POST['usuario']:"";
  $clave=(isset($_POST['clave']))?$_POST['clave']:"";
  $empresa=(isset($_POST['dependencia']))?$_POST['dependencia']:"";

  $sentencia=$conexion->prepare("INSERT INTO `usuarios` (`ID`, `nombre`, `correo`, `apodo`, `clave`, `empresa`) VALUES (NULL, :nombre, :correo, :usuario, :clave, :dependencia)");

  $sentencia->bindParam(":nombre",$nombre);
  $sentencia->bindParam(":correo",$correo);
  $sentencia->bindParam(":usuario",$usuario);
  $sentencia->bindParam(":clave",$clave);
  $sentencia->bindParam(":dependencia",$empresa);
    
  if($sentencia->execute()){
    $mensaje="Se creo el registro...!";
    header("Location:index.php?mensaje= ".$mensaje);
  }
  else{
    echo "El Registro no se agrego";
  }
}
include ("../../templates/header.php");

?>

<div class="card w3layouts-main">
    <div class="card-header">
        Crear Usuarios
    </div>
    
    <div class="card-body">
        
    <form action="" enctype="multipart/form-data" method="post">

        <div class="mb-3">
          <label for="nombre" class="form-label">Nombres y Apellidos</label>
          <input type="text"
            class="form-control" name="nombre" id="nombre" aria-describedby="helpId" placeholder="Ingrese su Nombre y Apellido">
        </div>

        <div class="mb-3">
          <label for="correo" class="form-label">Correo-e</label>
          <input type="email"
            class="form-control" name="correo" id="correo" aria-describedby="helpId" placeholder="Ingrese su Correo">
        </div>

        <div class="mb-3">
          <label for="usuario" class="form-label">Usuario</label>
          <input type="text"
            class="form-control" name="usuario" id="usuario" aria-describedby="helpId" placeholder="Ingrese su Usuario">
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input type="password"
            class="form-control" name="clave" id="clave" aria-describedby="helpId" placeholder="Ingrese su Contraseña">
        </div>

        <div class="mb-3">
          <label for="dependencia" class="form-label">Dependencia</label>
          <input type="text"
            class="form-control" name="dependencia" id="dependencia" aria-describedby="helpId" placeholder="Ingrese nombre de su Empresa">
        </div>

       <button type="submit" class="btn btn-success"> Guardar</button>
       <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

    </form>

    </div>
    <div class="card-footer text-muted">
    

    </div>
</div>

<?php include ("../../templates/footer.php");?>