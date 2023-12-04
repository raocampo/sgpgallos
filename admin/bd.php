<?php

$servidor="localhost";
$baseDatos="sgpgallos";
$usuario="root";
$clave= "";

try{

    $conexion= new PDO("mysql:host=$servidor;dbname=$baseDatos", $usuario, $clave);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexión realizada..!!";

}catch(Exception $error){
    echo $error->getMessage();
}

?>