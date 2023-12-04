<?php
// Conexión a la base de datos
include ("../../bd.php");

/* Consulta SQL para seleccionar los gallos disponibles y ordenarlos por peso real de menor a mayor
$sql = "SELECT * FROM gallos WHERE ID > 0 ORDER BY pesoReal ASC";*/

// Preparar la consulta
$sentencia = $conexion->prepare("SELECT * FROM gallos WHERE ID > 0 ORDER BY pesoReal ASC");

// Ejecutar la consulta
$sentencia->execute();

// Obtener los resultados
$resultados = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Imprimir los resultados
foreach ($resultados as $gallo) {
    echo "ID: " . $gallo['ID'] . ", Nombre: " . $gallo['nombre'] . ", Peso Real: " . $gallo['pesoReal'] . "<br>";
}

// Para agrupar los gallos por su familia (cuerda) y seleccionar los grupos que tienen al menos dos gallos disponibles para pelear, se puede usar la cláusula GROUP BY en conjunto con la función COUNT para contar el número de gallos disponibles en cada grupo. La consulta quedaría de la siguiente manera:

$grupo = $conexion->prepare("SELECT gallos.familiasId, COUNT(*) as num_gallos
FROM gallos g
WHERE gallos.disponible = 1
GROUP BY gallos.familiasId
HAVING num_gallos >= 1");

//Para comparar el peso real de cada gallo dentro del mismo grupo y tomar en cuenta la tolerancia de peso, se puede utilizar la siguiente consulta SQL:
$grupoPeso = $conexion->prepare("SELECT gallos.*, COUNT(g1.ID) as num_gallos_grupo, 
       g1.pesoReal - tolerancia_peso AS peso_min, 
       g1.pesoReal + tolerancia_peso AS peso_max 
FROM gallos g1 
INNER JOIN familias f ON g1.familiasId = f.codigo 
LEFT JOIN gallos g2 ON g1.familiasId = g2.familiasId AND g1.ID != g2.ID AND g2.disponible = 1 
WHERE g1.disponible = 1 
GROUP BY g1.ID 
HAVING num_gallos_grupo >= 2 
ORDER BY f.nombre, pesoReal ASC;");

/* 1. Primero, agrupamos los gallos por su familia: */
$gallosPorFamilia = array();

foreach ($gallos as $gallo) {
    if (!isset($gallosPorFamilia[$gallo['familiasId']])) {
        $gallosPorFamilia[$gallo['familiasId']] = array();
    }
    $gallosPorFamilia[$gallo['familiasId']][] = $gallo;
}

/** 2. Luego, filtramos los grupos que tienen al menos dos gallos disponibles:*/

$gruposConGallosDisponibles = array_filter($gallosPorFamilia, function ($gallos) {
    $disponibles = array_filter($gallos, function ($gallo) {
        return $gallo['disponible'] == 1;
    });
    return count($disponibles) >= 1;
});

/** 3. Después, ordenamos los gallos de cada grupo por su peso real: */

foreach ($gruposConGallosDisponibles as &$grupo) {
    usort($grupo, function ($a, $b) {
        return $a['pesoReal'] - $b['pesoReal'];
    });
}
 /** 4. Luego, emparejamos los gallos del grupo teniendo en cuenta la tolerancia de peso: */

 foreach ($gruposConGallosDisponibles as &$grupo) {
    $parejas = array();

    // Primero emparejamos los gallos con el mismo peso
    $pesos = array_unique(array_column($grupo, 'pesoReal'));
    foreach ($pesos as $peso) {
        $gallosConEstePeso = array_filter($grupo, function ($gallo) use ($peso, $tolerancia) {
            return abs($gallo['pesoReal'] - $peso) <= $tolerancia;
        });
        if (count($gallosConEstePeso) >= 2) {
            $parejas = array_merge($parejas, hacerParejas($gallosConEstePeso));
        }
    }

    // Luego emparejamos los gallos con pesos diferentes
    for ($i = 0; $i < count($grupo) - 1; $i++) {
        if (!isset($parejas[$i])) {
            $gallo1 = $grupo[$i];
            $mejorDiferencia = null;
            $mejorPareja = null;
            for ($j = $i + 1; $j < count($grupo); $j++) {
                $gallo2 = $grupo[$j];
                if ($gallo1['familiasId'] != $gallo2['familiasId']) {
                    $diferencia = abs($gallo1['pesoReal'] - $gallo2['pesoReal']);
                    if ($diferencia <= $tolerancia && ($mejorDiferencia === null || $diferencia < $mejorDiferencia)) {
                        $mejorDiferencia = $diferencia;
                        $mejorPareja = array($gallo1, $gallo2);
                    }
                }
            }
            if ($mejorPareja !== null) {
                $parejas[] = $mejorPareja;
            } else {
                // Si no encontramos una pareja para este gallo, lo dejamos libre
                $parejas[] = array($gallo1);
            }
        }
    }
    // Si hay un número impar de gallos, agregamos el sobrante a la lista de gallos disponibles para emparejamiento manual
    if (count($grupo) % 2 == 1) {
        $sobrante = array_pop($grupo);
        $gallosDisponiblesParaManual[] = $sobrante;
    }

    $grupo['parejas'] = $parejas;

    // Agregamos las parejas al grupo
    $grupo['parejas'] = $parejas;
}


/**4.1 Este código primero agrupa los gallos disponibles por su familia (cuerda) y selecciona aquellos grupos que tienen al menos dos gallos. Luego, para cada grupo de gallos, ordena los gallos por su peso real de menor a mayor. A continuación, empareja los gallos del grupo teniendo en cuenta la tolerancia de peso. Si la tolerancia no tiene valor o es cero, solo empareja gallos con pesos iguales. Si hay un número impar de gallos en el grupo, el gallo sobrante queda libre para pelear con cualquier otro gallo sobrante de otro grupo o para ser emparejado manualmente. Finalmente, muestra las parejas y gallos libres para cada grupo. */

// Obtener gallos agrupados por su familia (cuerda) y que tienen al menos dos gallos disponibles
$gallos_grupo = $db->query("SELECT * FROM gallos WHERE ID > 0 GROUP BY familiasId HAVING COUNT(*) >= 1")->fetchAll(PDO::FETCH_GROUP);

// Recorrer cada grupo de gallos
foreach ($gallos_grupo as $familiasId => $gallos) {
    // Ordenar gallos por su peso real de menor a mayor
    usort($gallos, function($a, $b) {
        return $a['pesoReal'] - $b['pesoReal'];
    });

    // Emparejar gallos
    $parejas = [];
    $libres = null;
    foreach ($gallos as $i => $gallo) {
        // Si es el último gallo y queda libre, añadirlo a los libres
        if ($i == count($gallos) - 1 && !isset($parejas[$i])) {
            $libres = $gallo;
            continue;
        }

        // Emparejar gallo con el siguiente que tenga un peso dentro de la tolerancia
        for ($j = $i + 1; $j < count($gallos); $j++) {
            $peso_diferencia = abs($gallo['pesoReal'] - $gallos[$j]['pesoReal']);
            $tolerancia = isset($tolerancia_peso) ? $tolerancia_peso : 0;
            if ($peso_diferencia <= $tolerancia && $gallo['familiasId'] != $gallos[$j]['familiasId']) {
                $parejas[$i] = $gallo;
                $parejas[$j] = $gallos[$j];
                break;
            }
        }
    }

    // Si queda un gallo libre, añadirlo a los libres
    if (count($gallos) % 2 != 0 && !isset($parejas[count($gallos) - 1])) {
        $libres = end($gallos);
    }

    // Mostrar parejas y gallos libres
    echo "Grupo de gallos de la cuerda $familiasId: <br>";
    foreach ($parejas as $i => $gallo) {
        echo "Pareja $i: " . $gallo['nombre'] . "<br>";
    }
    if ($libres) {
        echo "Gallos libres: " . $libres['nombre'] . "<br>";
    }
}


/* 5. Continuando con el código para generar las peleas, una vez que se han emparejado los gallos según las reglas mencionadas anteriormente, se pueden guardar las peleas en una tabla de peleas con la siguiente estructura:

| ID (autoincremental) | Gallo1_ID | Gallo2_ID | Torneo_ID | Codigo_Pelea |

Donde Gallo1_ID y Gallo2_ID son los ID de los gallos emparejados, Torneo_ID es el ID del torneo al que pertenecen las peleas, y Codigo_Pelea es un código generado automáticamente para identificar la pelea.

Para guardar las peleas en la tabla, se puede utilizar un bucle que itere sobre los grupos de gallos emparejados, y para cada grupo, genere las peleas correspondientes. El código puede ser algo así:*/

// Obtener los grupos de gallos emparejados
$grupos = obtener_grupos_emparejados();

// Iterar sobre los grupos y generar las peleas
foreach ($grupos as $grupo) {
  $gallos = $grupo['gallos'];
  $num_gallos = count($gallos);
  
  // Si hay un número impar de gallos, el sobrante queda libre
  $libre = null;
  if ($num_gallos % 2 == 1) {
    $libre = array_pop($gallos);
  }
  
  // Generar las peleas entre los gallos del grupo
  while (!empty($gallos)) {
    $gallo1 = array_shift($gallos);
    $gallo2 = null;
    $diferencia_peso = null;
    
    // Buscar el gallo más cercano en peso
    foreach ($gallos as $gallo) {
      if ($gallo['peso_real'] >= $gallo1['peso_real'] &&
          ($diferencia_peso === null || $gallo['peso_real'] - $gallo1['peso_real'] < $diferencia_peso)) {
        $gallo2 = $gallo;
        $diferencia_peso = $gallo['peso_real'] - $gallo1['peso_real'];
      }
    }
    
    // Si no se encontró un gallo cercano, emparejar con el sobrante o dejar libre
    if ($gallo2 === null) {
      if ($libre !== null) {
        $gallo2 = $libre;
        $libre = null;
      } else {
        $gallo2 = array_pop($gallos);
      }
    } else {
      // Eliminar el gallo emparejado del grupo
      $key = array_search($gallo2, $gallos);
      array_splice($gallos, $key, 1);
    }
    
    // Guardar la pelea en la tabla
    $codigo_pelea = generar_codigo_pelea();
    guardar_pelea($gallo1['id'], $gallo2['id'], $torneo_id, $codigo_pelea);
  }
}

/**En este código se utilizan varias funciones que deberán ser implementadas según las necesidades del proyecto:

obtener_grupos_emparejados(): esta función deberá obtener los grupos de gallos emparejados según las reglas mencionadas anteriormente. Puede ser una combinación de las consultas SQL vistas anteriormente y del código para agrupar y emparejar */




/*function generarPeleas($categorias, $clubes){
    $peleas = array();
    
    // Se recorren las categorías de peso
    foreach ($categorias as $categoria => $gallos_categoria) {
        $clubes_categoria = array();
        
        // Se recorren los clubes dentro de cada categoría de peso
        foreach ($clubes as $club => $gallos_club) {
            $gallos_categoria_club = array_intersect($gallos_categoria, $gallos_club);
            
            if (count($gallos_categoria_club) > 1) {
                $clubes_categoria[$club] = $gallos_categoria_club;
            }
        }
        
        // Si hay gallos disponibles para la categoría de peso
        if (count($clubes_categoria) > 1) {
            // Se eligen dos clubes de forma aleatoria
            $keys = array_keys($clubes_categoria);
            $club1 = $keys[array_rand($keys)];
            unset($keys[array_search($club1, $keys)]);
            $club2 = $keys[array_rand($keys)];
            
            $gallos1 = $clubes_categoria[$club1];
            $gallos2 = $clubes_categoria[$club2];
            
            // Se eligen dos gallos de cada club de forma aleatoria y se agregan a la pelea
            $gallo1 = $gallos1[array_rand($gallos1)];
            $gallo2 = $gallos2[array_rand($gallos2)];
            
            array_push($peleas, array(
                "gallo1" => $gallo1["id"],
                "gallo2" => $gallo2["id"]
            ));
        }
    }
    
    return $peleas;
}*/