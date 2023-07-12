<?php

// Variables necesarias para la consulta
$tolerancia = $_POST['tolerancia']; // Tolerancia permitida en el peso
$familia = $_POST['familia']; // Familia del gallo a excluir

// Consulta para obtener los gallos que cumplan con las condiciones
if (empty($tolerancia) || $tolerancia == 0) {
    $consulta = $conexion->prepare("SELECT * FROM gallos WHERE peso = :peso AND familia != :familia");
    $consulta->bindParam(":peso", $_POST['peso'], PDO::PARAM_INT);
} else {
    $consulta = $conexion->prepare("SELECT * FROM gallos WHERE peso BETWEEN :minPeso AND :maxPeso AND familia != :familia");
    $minPeso = $_POST['peso'] - $tolerancia;
    $maxPeso = $_POST['peso'] + $tolerancia;
    $consulta->bindParam(":minPeso", $minPeso, PDO::PARAM_INT);
    $consulta->bindParam(":maxPeso", $maxPeso, PDO::PARAM_INT);
}

$consulta->bindParam(":familia", $familia, PDO::PARAM_INT);
$consulta->execute();
$gallos = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Array para guardar los resultados del cotejamiento
$coteja = array();

// Realizamos el cotejamiento
foreach ($gallos as $gallo1) {
    foreach ($gallos as $gallo2) {
        if ($gallo1['ID'] != $gallo2['ID']) {
            $diferencia = abs($gallo1['peso'] - $gallo2['peso']);
            if (empty($tolerancia) || $diferencia <= $tolerancia) {
                $coteja[] = array(
                    'galloL' => $gallo1['nombre'],
                    'galloV' => $gallo2['nombre']
                );
            }
        }
    }
}

// Guardamos los resultados del cotejamiento en la tabla coteja
$insert = $conexion->prepare("INSERT INTO coteja (galloL, galloV) VALUES (:galloL, :galloV)");
foreach ($coteja as $fila) {
    $insert->execute(array(
        ':galloL' => $fila['galloL'],
        ':galloV' => $fila['galloV']
    ));
}









/*//Con esta sentencia llamamos a los datos para cotejar
$sentencia = $conexion->prepare("SELECT gallos.ID, gallos.nombre, gallos.pesoReal, gallos.tamañoReal, familias.nombre AS nombre_familia FROM gallos 
INNER JOIN familias ON gallos.familiasId = familias.codigo
WHERE gallos.disponible = 1 ORDER BY gallos.pesoReal ASC ");
$sentencia->execute();
$gallosDisponibles = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Convertimos la tolerancia
$tolerancia;

//$eliminar = $conexion->prepare("DELETE FROM coteja");
//$eliminar->execute();

if(empty($_POST['peso']) || $_POST['peso'] == 0){
    $tolerancia = $_POST['peso'];
    // print_r($gallosDisponibles);
     //echo '<br>';
    $gallosIguales = array();

    for($i=0; $i<count($gallosDisponibles); $i+=1){
        $gallo1=$gallosDisponibles[$i];
        //print_r($gallo1);
        //echo '<br>';
        if($gallo1['pesoReal'] === $gallosDisponibles[0]['pesoReal'] ){

            array_push($gallosIguales, $gallo1);
            //print_r($gallosIguales);
        }
    }
    /*for($j=1; $j<count($gallosDisponibles); $j+=2){
        $gallo2=$gallosDisponibles[$j];
        print_r($gallo2);
        echo '<br>';
    }*/
    
    

    
    
    //$tolerancia <= 0 && 
    /*foreach ($gallosDisponibles as $key => $gallo) {
        if ($gallo['pesoReal'] == $gallosDisponibles[0]['pesoReal']) {
            // Si la tolerancia es cero y el peso es igual, agregamos el gallo al array de gallos iguales para la tabla 1
            array_push($gallosIguales, $gallo);
        } /*else if ($tolerancia > 0 && abs($gallo['pesoReal'] - $gallosDisponibles[0]['pesoReal']) <= $tolerancia) {
            // Si la tolerancia es mayor a cero y el peso está dentro de la tolerancia, agregamos el gallo al array de gallos iguales para la tabla 1
            array_push($gallosIguales, $gallo);
        }
    }

    // Ahora debemos buscar los gallos de pesos iguales que no sean de la misma familia para agregarlos a la tabla 2
    /*foreach ($gallosIguales as $gallo1) {
        foreach ($gallosDisponibles as $gallo) {
            if ($gallo['pesoReal'] == $gallo1['pesoReal'] && $gallo['ID'] != $gallo1['ID'] && $gallo['nombre_familia'] != $gallo1['nombre_familia']) {
                array_push($gallosIguales1, $gallo);
                break;
            }
        }
    }
}else {
    $tolerancia = isset($_POST['peso']) ? floatval($_POST['peso']) : 0.0;
    //echo "<br>". $tolerancia . "<br>";
    $gallosTolerancia = array();
    $gallosLibres = array();

    foreach ($gallosDisponibles as $key => $gallo) {
         if (abs($gallo['pesoReal'] - $gallosDisponibles[0]['pesoReal']) <= $tolerancia) {
            // Si la diferencia es menor o igual a la tolerancia, agregamos el gallo al array de gallos con tolerancia para la tabla 1
            array_push($gallosTolerancia, $gallo);
        } else if (abs($gallo['pesoReal'] - $gallosDisponibles[0]['pesoReal']) > $tolerancia) {
            // Si la diferencia es mayor a la tolerancia, agregamos el gallo al array de gallos libres
            array_push($gallosLibres, $gallo);
        }
    }

        $toleranciaKg = ($tolerancia / 1000);
        $tolerancialb = ($tolerancia / 453.60);
        $toleranciaoz = ($tolerancia / 28.35);
        
        echo "La tolerancia en KG es: ". $toleranciaKg . "<br>"; 
        echo "La tolerancia en lb es: ". $tolerancialb . "<br>"; 
        echo "La tolerancia en oz es: ". $toleranciaoz . "<br>";
}

if(empty($_POST['peso']) || $_POST['peso'] == 0) {
    $coteja=$conexion->prepare("INSERT INTO `coteja` (galloL, galloV) VALUES (?, ?)");

    $gallo1 = $gallosIguales[0];
    for($i=1; $i <count($gallosIguales); $i+=1){
        $gallo2 = $gallosIguales[$i];
        if($gallo1 != $gallo2){
            $coteja->execute([$gallo1['nombre'], $gallo2['nombre']]);
        }
    }
} else {
    $coteja=$conexion->prepare("INSERT INTO `coteja` (galloL, galloV) VALUES (?, ?)");

    $gallo1 = $gallosTolerancia[0];
    for($i=1; $i <count($gallosTolerancia); $i+=1){
        $gallo2 = $gallosTolerancia[$i];
        if($gallo1 != $gallo2){
            $coteja->execute([$gallo1['nombre'], $gallo2['nombre']]);
        }
    }
}


    
    
    /*foreach ($gallosIguales as $gallo1){
        foreach($gallosIguales as $gallo2){
            if($gallo1['ID'] != $gallo2['ID']){
                $coteja->execute([$gallo1['nombre'], $gallo2['nombre']]);
            }
        }
    } 

    $coteja=$conexion->prepare("SELECT * FROM `coteja`");
    $coteja->execute();

    $lista_coteja=$coteja->fetchAll(PDO::FETCH_ASSOC);
//Se realiza la consulta de los datos a cotejar




//$cotejas = emparejarGallos($gallos, $tolerancia);











/**
 * FUNCIÓN PRIMERA
 * Obtiene los gallos disponibles para el torneo, ordenados por peso real.
 * @param array $gallos - Lista de gallos.
 * @return array - Lista de gallos disponibles ordenados por peso real.
 * 
 * Esta función toma como parámetro una lista de gallos y devuelve una lista de los gallos disponibles para el torneo, ordenados por peso real. Para esto, primero se filtran los gallos que no están disponibles para el torneo y luego se ordenan los gallos restantes por peso real utilizando la función usort, que permite definir una función de comparación para ordenar el arreglo.
 */
function obtenerGallosDisponiblesPorPesoReal($gallos) {
    // Filtramos los gallos que no estén disponibles para el torneo.
    $gallosDisponibles = array_filter($gallos, function ($gallo) {
        return $gallo['disponible'] === true;
    });

    // Ordenamos los gallos por peso real.
    usort($gallosDisponibles, function ($gallo1, $gallo2) {
        return $gallo1['pesoReal'] <=> $gallo2['pesoReal'];
    });

    return $gallosDisponibles;
}
 /* 
    FUNCIÓN SEGUNDA

    La función gruposConGallosDisponibles recibe un arreglo de gallos y devuelve un arreglo de grupos de gallos disponibles para pelear.

    Primero, se crea un arreglo $grupos donde se agrupan los gallos por su familia. Luego, se recorre cada grupo y se verifica si hay al menos dos gallos disponibles en el grupo. Si es así, se ordena el grupo por peso real y se agrega al arreglo $gruposConGallosDisponibles.

    Finalmente, se devuelve el arreglo $gruposConGallosDisponibles con los grupos de gallos disponibles para pelear.
*/

function gruposConGallosDisponibles($gallos) {
    $grupos = array();
    foreach ($gallos as $gallo) {
        if ($gallo['disponible']) {
            $grupos[$gallo['familiasId']][] = $gallo;
        }
    }
    
    $gruposConGallosDisponibles = array();
    foreach ($grupos as $grupo) {
        if (count($grupo) >= 2) {
            usort($grupo, function($a, $b) {
                return $a['pesoReal'] - $b['pesoReal'];
            });
            $gruposConGallosDisponibles[] = $grupo;
        }
    }
    
    return $gruposConGallosDisponibles;
}

/*
    FUNCIÓN TERCERA

    La función recibe como parámetros el grupo de gallos y la tolerancia de peso permitida.
    Devuelve un array de arrays, donde cada subarray agrupa a los gallos que tienen un peso real similar dentro de la tolerancia dada.
*/
function agruparPorPeso($gruposConGallosDisponibles, $tolerancia) {
    $agrupados = array();
    foreach ($gruposConGallosDisponibles as $gallo) {
        $agrupado = false;
        foreach ($agrupados as &$grupoDePeso) {
            if (abs($grupoDePeso[0]['pesoReal'] - $gallo['pesoReal']) <= $tolerancia) {
                $grupoDePeso[] = $gallo;
                $agrupado = true;
                break;
            }
        }
        if (!$agrupado) {
            $agrupados[] = array($gallo);
        }
    }
    return $agrupados;
}

/*
    FUNCIÓN CUARTA

    Esta función utiliza la función hacerParejas que definimos anteriormente para emparejar los gallos con el mismo peso. Primero, se separan los gallos por peso, teniendo en cuenta la tolerancia dada, y se emparejan los gallos que tienen el mismo peso con hacerParejas. Luego, se buscan los gallos disponibles que no se emparejaron en el paso anterior y se emparejan los que tengan pesos diferentes, teniendo en cuenta las restricciones de cuerda y organización y la tolerancia dada. Si aún quedan


*/

function hacerParejas($gallos) {
    $parejas = array();
    for ($i = 0; $i < count($gallos) - 1; $i += 2) {
        $parejas[] = array($gallos[$i], $gallos[$i+1]);
    }
    return $parejas;
}


function emparejarGallos($grupo, $tolerancia = 0) {
    // Separamos los gallos por peso
    $gruposPorPeso = array();
    foreach ($grupo as $gallo) {
        $peso = round($gallo['pesoReal'] / $tolerancia) * $tolerancia;
        if (!isset($gruposPorPeso[$peso])) {
            $gruposPorPeso[$peso] = array();
        }
        $gruposPorPeso[$peso][] = $gallo;
    }
    
    $parejas = array();
    foreach ($gruposPorPeso as $gallosConEstePeso) {
        if (count($gallosConEstePeso) >= 2) {
            $parejas = array_merge($parejas, hacerParejas($gallosConEstePeso));
        }
    }
    
    // Emparejamos gallos con pesos diferentes
    $gallosDisponibles = array_diff($grupo, array_column($parejas, 0), array_column($parejas, 1));
    $gallosDisponibles = array_filter($gallosDisponibles, function($gallo) use ($tolerancia) {
        return $tolerancia <= 0 || count(array_filter($gallo, function($otroGallo) use ($gallo, $tolerancia) {
            return abs($gallo['pesoReal'] - $otroGallo['pesoReal']) <= $tolerancia && $gallo['cuerda'] != $otroGallo['cuerda'] && $gallo['organizacion'] != $otroGallo['organizacion'];
        })) > 0;
    });
    
    // Emparejamos gallos disponibles
    while (count($gallosDisponibles) > 1) {
        $gallo1 = array_shift($gallosDisponibles);
        $parejaEncontrada = false;
        foreach ($gallosDisponibles as $key => $gallo2) {
            if ($gallo1['cuerda'] != $gallo2['cuerda'] && $gallo1['organizacion'] != $gallo2['organizacion'] && abs($gallo1['pesoReal'] - $gallo2['pesoReal']) <= $tolerancia) {
                $parejas[] = array($gallo1, $gallo2);
                unset($gallosDisponibles[$key]);
                $parejaEncontrada = true;
                break;
            }
        }
        if (!$parejaEncontrada) {
            break;
        }
    }
    
    // Devolvemos las parejas encontradas
    return $parejas;
}

/* 4.1 */

function emparejarGallosUno($grupos, $tolerancia) {
    $parejas = array();
    foreach ($grupos as $grupo) {
        // Agrupar gallos por peso
        $gruposConGallosDisponibles = agruparPorPeso($grupo, $tolerancia);

        // Emparejar gallos dentro de cada grupo
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
                    }
                }
            }
            // Añadir parejas al grupo
            $grupo['parejas'] = $parejas;
        }
    }
    return $grupos;
}

/*
    FUNCIÓN CINCO

    Esta función se conecta a una base de datos MySQL y obtiene el último código de pelea registrado en la tabla "peleas".
    Luego, genera un nuevo código de pelea a partir del último código registrado, agregando 1 al número y rellenando los ceros a la izquierda si es necesario. Si no hay ningún código de pelea registrado, se genera el primer código de pelea "PEL000001".
    Finalmente, la función devuelve el nuevo código de pelea generado.
*/

function generarCodigoPelea() {
    // Establecemos la conexión a la base de datos
    $servidor = "localhost";
    $usuario = "nombre_de_usuario";
    $contrasena = "contraseña";
    $basedatos = "nombre_de_la_base_de_datos";
    $conexion = mysqli_connect($servidor, $usuario, $contrasena, $basedatos);
    
    // Obtenemos el último código de pelea registrado en la base de datos
    $sql = "SELECT codigo_pelea FROM peleas ORDER BY codigo_pelea DESC LIMIT 1";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_array($resultado);
    
    // Generamos el nuevo código de pelea
    if ($fila) {
        $ultimoCodigo = $fila["codigo_pelea"];
        $nuevoCodigo = "PEL" . str_pad(substr($ultimoCodigo, 3) + 1, 6, "0", STR_PAD_LEFT);
    } else {
        $nuevoCodigo = "PEL000001";
    }
    
    // Cerramos la conexión a la base de datos
    mysqli_close($conexion);
    
    return $nuevoCodigo;
}

/*
    FUNCIÓN SEIS

    Conectarte a la base de datos: primero, tendrás que establecer una conexión con la base de datos donde quieras almacenar las peleas generadas. Esto se puede hacer usando las funciones de conexión de la biblioteca de bases de datos que estés utilizando. Por ejemplo, si estás usando MySQL, puedes usar la función mysqli_connect() para conectarte a la base de datos.

    Definir la tabla de peleas: antes de guardar las peleas, tendrás que crear una tabla de base de datos para almacenarlas. La tabla debería tener columnas para el código de pelea, los dos gallos que participan en la pelea, la fecha y hora de la pelea, y cualquier otra información que desees almacenar.

    Escribir la función de guardar: la función de guardar deberá aceptar los detalles de la pelea (código de pelea, gallos que participan, etc.) y usar las funciones de base de datos para insertar los detalles en la tabla de peleas.
*/

function guardarPelea($codigoPelea, $gallo1, $gallo2, $fechaHora) {
    // Conectar a la base de datos
    $conn = mysqli_connect("localhost", "usuario", "contraseña", "basededatos");

    // Verificar si la conexión fue exitosa
    if (!$conn) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    // Insertar los detalles de la pelea en la tabla de peleas
    $sql = "INSERT INTO peleas (codigo_pelea, gallo1, gallo2, fecha_hora) VALUES ('$codigoPelea', '$gallo1', '$gallo2', '$fechaHora')";
    
    if (mysqli_query($conn, $sql)) {
        echo "Pelea guardada exitosamente";
    } else {
        echo "Error al guardar la pelea: " . mysqli_error($conn);
    }

    // Cerrar la conexión a la base de datos
    mysqli_close($conn);
}

$parejas = emparejarGallos($grupo, $tolerancia);

foreach ($parejas as $pareja) {
    $gallo1 = $pareja[0];
    $gallo2 = $pareja[1];
    // aquí puedes utilizar $gallo1 y $gallo2 para generar el código de pelea único
    generarCodigoPelea();
    // y guardar la pelea en la base de datos
    guardarPelea($codigoPelea, $gallo1, $gallo2, $fechaHora);
}


/*
Este es otros codigos de la primer iteración

//recibir valor de tolerancia de peso
if(isset($_GET["peso"])){

    $tolerancia_peso = ["peso"];
    
    function convertGramsToPoundsAndOunces($tolerancia_peso) {
        // Convertimos los gramos a onzas
        $ounces = $tolerancia_peso / 28.35;
        // Convertimos las onzas a libras y onzas
        $pounds = floor($ounces / 16);
        $ounces = $ounces % 16;
        // Retornamos el resultado como un array asociativo
        return array(
          "pounds" => $pounds,
          "ounces" => $ounces
        );
      }
    function convertirPeso($tolerancia_peso) {
        $pesoLb = $tolerancia_peso * 2.20462; // Convertir kilogramos a libras
        $pesoOz = round(($pesoLb - floor($pesoLb)) * 16, 1); // Calcular las onzas
        $pesoLb = floor($pesoLb); // Redondear las libras hacia abajo
        return $pesoLb." lb ".$pesoOz." oz"; // Retornar la cadena formateada
      }*
      
    }

    function clasificarPorPeso($gallos)
    {
        $categorias = array(
            "menos de 1 kg" => array(),
            "entre 1 y 2 kg" => array(),
            "entre 2 y 3 kg" => array(),
            "más de 3 kg" => array()
        );
    
        foreach ($gallos as $gallo) {
            if ($gallo["pesoReal"] < 1) {
                array_push($categorias["menos de 1 kg"], $gallo);
            } else if ($gallo["pesoReal"] >= 1 && $gallo["pesoReal"] < 2) {
                array_push($categorias["entre 1 y 2 kg"], $gallo);
            } else if ($gallo["pesoReal"] >= 2 && $gallo["pesoReal"] < 3) {
                array_push($categorias["entre 2 y 3 kg"], $gallo);
            } else {
                array_push($categorias["más de 3 kg"], $gallo);
            }
        }
    
        return $categorias;
    }
    
    function asignarPorClub($gallos)
    {
        $clubes = array();
    
        foreach ($gallos as $gallo) {
            $club = $gallo["familiasId"];
    
            if (!isset($clubes[$club])) {
                $clubes[$club] = array();
            }
    
            array_push($clubes[$club], $gallo);
        }
    
        return $clubes;
    }
    
    function generar_peleas($categorias, $clubes, $tolerancia_peso, $total_gallos)
    {
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
    
                // Si la tolerancia de peso es mayor a 0, se tiene en cuenta para la coteja
                if ($tolerancia_peso > 0 && abs($gallo1["pesoReal"] - $gallo2["pesoReal"]) > $tolerancia_peso) {
                    // Se indica que estos gallos no pudieron ser cotejados por la tolerancia de peso
                    $gallo1["cotejado"] = false;
                    $gallo2["cotejado"] = false;
                } else {
                    $gallo1["cotejado"] = true;
                    $gallo2["cotejado"] = true;
    
                    array_push($peleas, array(
                        "gallo1" => $gallo1["ID"],
                        "gallo2" => $gallo2["ID"]
                    ));
                }
    
                // Se quitan los gallos elegidos de los clubes correspondientes
                $clubes[$club1] = array_filter($clubes[$club1], function ($gallo) use ($gallo1) {
                    return $gallo["ID"] != $gallo1["ID"];
                });
    
                $clubes[$club2] = array_filter($clubes[$club2], function ($gallo) use ($gallo2) {
                    return $gallo["ID"] != $gallo2["ID"];
                });
            }
        }
    
        // Se recorren los gallos que no pudieron ser cotejados por la tolerancia de peso
        foreach ($clubes as $club => $gallos_club) {
            foreach ($gallos_club as $gallo) {
                if (!array_key_exists("cotejado", $gallo) || !$gallo["cotejado"]) {
                    // Se indica que este gallo no pudo ser cotejado por la tolerancia de peso
                    $gallo["cotejado"] = false;
                    // Se agrega a un arreglo para indicar que quedó libre para cotejar de forma manual
                    $gallos_sin_cotejo[] = $gallo["ID"];
                }
            }
        }
    
        return $peleas;
    }
    
    
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


    /*

    Este es otro código segunda iteración.

//Función obtener gallos por el peso real y disponibles
function obtenerGallosDisponiblesPorPesoReal($gallos)
{
    // Filtramos los gallos que no estén disponibles para el torneo.
    $gallosDisponibles = array_filter($gallos, function ($gallo) {
        return $gallo['disponible'] === true;
    });

    // Ordenamos los gallos por peso real.
    usort($gallosDisponibles, function ($gallo1, $gallo2) {
        return $gallo1['pesoReal'] <=> $gallo2['pesoReal'];
    });

    return $gallosDisponibles;
}

//Función de gallos disponibles por grupo
function gruposConGallosDisponibles($gallos) {
    $grupos = array();
    foreach ($gallos as $gallo) {
        if ($gallo['disponible']) {
            $grupos[$gallo['familiasId']][] = $gallo;
        }
    }
    
    $gruposConGallosDisponibles = array();
    foreach ($grupos as $grupo) {
        if (count($grupo) >= 2) {
            usort($grupo, function($a, $b) {
                return $a['pesoReal'] - $b['pesoReal'];
            });
            $gruposConGallosDisponibles[] = $grupo;
        }
    }
    
    return $gruposConGallosDisponibles;
}

//Función que recibe el grupo de gallos disponibles y les aplica la tolerancia
function agruparPorPeso($gruposConGallosDisponibles, $tolerancia) {
    $agrupados = array();
    foreach ($gruposConGallosDisponibles as $gallo) {
        $agrupado = false;
        foreach ($agrupados as &$grupoDePeso) {
            if (abs($grupoDePeso[0]['pesoReal'] - $gallo['pesoReal']) <= $tolerancia) {
                $grupoDePeso[] = $gallo;
                $agrupado = true;
                break;
            }
        }
        if (!$agrupado) {
            $agrupados[] = array($gallo);
        }
    }
    return $agrupados;
}


function hacerParejas($gallos) {
    $parejas = array();
    for ($i = 0; $i < count($gallos) - 1; $i += 2) {
        $parejas[] = array($gallos[$i], $gallos[$i+1]);
    }
    return $parejas;
}


function emparejarGallos($grupo, $tolerancia) {
    // Separamos los gallos por peso
    $gruposPorPeso = array();
    foreach ($grupo as $gallo) {
        $peso = round($gallo['pesoReal'] / $tolerancia) * $tolerancia;
        if (!isset($gruposPorPeso[$peso])) {
            $gruposPorPeso[$peso] = array();
        }
        $gruposPorPeso[$peso][] = $gallo;
    }
    
    $parejas = array();
    foreach ($gruposPorPeso as $gallosConEstePeso) {
        if (count($gallosConEstePeso) >= 2) {
            $parejas = array_merge($parejas, hacerParejas($gallosConEstePeso));
        }
    }
    
    // Emparejamos gallos con pesos diferentes
    $gallosDisponibles = array_diff($grupo, array_column($parejas, 0), array_column($parejas, 1));
    $gallosDisponibles = array_filter($gallosDisponibles, function($gallo) use ($tolerancia) {
        return $tolerancia <= 0 || count(array_filter($gallo, function($otroGallo) use ($gallo, $tolerancia) {
            return abs($gallo['pesoReal'] - $otroGallo['pesoReal']) <= $tolerancia && $gallo['familiasId'] != $otroGallo['familiasId'];
        })) > 0;
    });
    
    // Emparejamos gallos disponibles
    while (count($gallosDisponibles) > 1) {
        $gallo1 = array_shift($gallosDisponibles);
        $parejaEncontrada = false;
        foreach ($gallosDisponibles as $key => $gallo2) {
            if ($gallo1['familiasId'] != $gallo2['familiasId'] && abs($gallo1['pesoReal'] - $gallo2['pesoReal']) <= $tolerancia) {
                $parejas[] = array($gallo1, $gallo2);
                unset($gallosDisponibles[$key]);
                $parejaEncontrada = true;
                break;
            }
        }
        if (!$parejaEncontrada) {
            break;
        }
    }
    
    // Devolvemos las parejas encontradas
    return $parejas;
}*/


?>