<?php

include("../../bd.php");

if(isset($_GET['txtID'])){
    //En esta sentencias se recuperan los datos a editar con el ID que se escoja
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("SELECT * FROM usuarios WHERE ID=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $registro=$sentencia->fetch(PDO::FETCH_LAZY);

    $nombre=$registro['nombre'];
    $correo=$registro['correo'];
    $usuario=$registro['apodo'];
    $clave=$registro['clave'];
    $empresa=$registro['empresa'];
}

if($_POST){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
  $nombre=(isset($_POST['nombre']))?$_POST['nombre']:"";
  $correo=(isset($_POST['correo']))?$_POST['correo']:"";
  $usuario=(isset($_POST['usuario']))?$_POST['usuario']:"";
  $clave=(isset($_POST['clave']))?$_POST['clave']:"";
  $empresa=(isset($_POST['dependencia']))?$_POST['dependencia']:"";

  $sentencia=$conexion->prepare("UPDATE `usuarios` SET nombre=:nombre, correo=:correo, apodo=:usuario, clave=:clave, empresa=:dependencia WHERE ID=:id");

  $sentencia->bindParam(":nombre",$nombre);
  $sentencia->bindParam(":correo",$correo);
  $sentencia->bindParam(":usuario",$usuario);
  $sentencia->bindParam(":clave",$clave);
  $sentencia->bindParam(":dependencia",$empresa);

  $sentencia->bindParam(":id",$txtID);
  
  $sentencia->execute();

  if($sentencia === TRUE){
    echo "Cambios guardados";
  }else{
    echo "No se pudo actualizar. ";
    print_r($sentencia->errorInfo());
  } 

  $mensaje="Se edito el registro...!";
  header("Location:index.php?mensaje= ".$mensaje);

}

include ("../../templates/header.php");?>

<div class="card">
    <div class="card-header">
        Editar Usuario
    </div>
    
    <div class="card-body">
        
    <form action="" enctype="multipart/form-data" method="post">

        <div class="mb-3">
          <label for="txtID" class="form-label">ID:</label>
          <input readonly value="<?php echo $txtID;?>" type="text"
              class="form-control" name="txtID" id="txtID" aria-describedby="helpId" placeholder="ID">
        </div>

        <div class="mb-3">
          <label for="nombre" class="form-label">Nombres y Apellidos</label>
          <input value="<?php echo $nombre;?>" type="text"
            class="form-control" name="nombre" id="nombre" aria-describedby="helpId" placeholder="Edite su Nombre y Apellido">
        </div>

        <div class="mb-3">
          <label for="correo" class="form-label">Correo-e</label>
          <input value="<?php echo $correo;?>" type="email"
            class="form-control" name="correo" id="correo" aria-describedby="helpId" placeholder="Edite su Correo">
        </div>

        <div class="mb-3">
          <label for="usuario" class="form-label">Usuario</label>
          <input value="<?php echo $usuario;?>" type="text"
            class="form-control" name="usuario" id="usuario" aria-describedby="helpId" placeholder="Edite su Usuario">
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input value="<?php echo $clave;?> "type="password"
            class="form-control" name="clave" id="clave" aria-describedby="helpId" placeholder="Edite su Contraseña">
        </div>

        <div class="mb-3">
          <label for="dependencia" class="form-label">Dependencia</label>
          <input value="<?php echo $empresa;?> "type="text"
            class="form-control" name="dependencia" id="dependencia" aria-describedby="helpId" placeholder="Edite su Empresa">
        </div>

       <button type="submit" class="btn btn-success">Actualizar</button>
       <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

    </form>



<?php include ("../../templates/footer.php");?>