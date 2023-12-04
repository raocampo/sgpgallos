// Array para almacenar los registros encontrados que cumplen con la condición
$registros_encontrados = array();

// Iteramos por todos los registros de la tablaX
foreach ($registros as $registro) {

    // Verificamos si hay otro registro que cumple la condición (misma talla y diferente propietario)
    foreach ($registros as $otro_registro) {
        if ($registro['talla'] == $otro_registro['talla'] && $registro['propietario'] != $otro_registro['propietario']) {
            // Agregamos los dos registros al array de registros encontrados
            $registros_encontrados[] = $registro;
            $registros_encontrados[] = $otro_registro;
            break; // Terminamos de buscar otros registros que cumplan la condición para este registro
        }
    }
}

// Agregamos los registros encontrados a las tablas correspondientes
echo "<table border='1'><tr><th>ID</th><th>Nombre</th><th>Color</th><th>Talla</th><th>Propietario</th></tr>";
for ($i = 0; $i < count($registros_encontrados); $i += 2) {
    echo "<tr><td>".$registros_encontrados[$i]['ID']."</td><td>".$registros_encontrados[$i]['nombre']."</td><td>".$registros_encontrados[$i]['color']."</td><td>".$registros_encontrados[$i]['talla']."</td><td>".$registros_encontrados[$i]['propietario']."</td></tr>";
}
echo "</table>";

echo "<table border='1'><tr><th>ID</th><th>Nombre</th><th>Color</th><th>Talla</th><th>Propietario</th></tr>";
for ($i = 1; $i < count($registros_encontrados); $i += 2) {
    echo "<tr><td>".$registros_encontrados[$i]['ID']."</td><td>".$registros_encontrados[$i]['nombre']."</td><td>".$registros_encontrados[$i]['color']."</td><td>".$registros_encontrados[$i]['talla']."</td><td>".$registros_encontrados[$i]['propietario']."</td></tr>";
}
echo "</table>";
