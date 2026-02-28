<?php

require_once __DIR__ . '/includes/app.php';

$servidor = $_ENV['SG_DB_HOST'] ?? 'localhost';
$baseDatos = $_ENV['SG_DB_NAME'] ?? 'sgpgallos';
$usuario = $_ENV['SG_DB_USER'] ?? 'root';
$clave = $_ENV['SG_DB_PASS'] ?? '';

try {
    $dsn = "mysql:host={$servidor};dbname={$baseDatos};charset=utf8mb4";
    $conexion = new PDO($dsn, $usuario, $clave, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (Throwable $error) {
    http_response_code(500);
    exit('No fue posible conectar con la base de datos.');
}

try {
    $columnas = ['estado', 'ganador', 'observaciones', 'fecha_resultado'];

    foreach ($columnas as $columna) {
        $consultaColumna = $conexion->prepare("SHOW COLUMNS FROM peleas LIKE :columna");
        $consultaColumna->bindValue(':columna', $columna);
        $consultaColumna->execute();

        if ($consultaColumna->fetch()) {
            continue;
        }

        if ($columna === 'estado') {
            $conexion->exec("ALTER TABLE peleas ADD COLUMN estado VARCHAR(20) NOT NULL DEFAULT 'pendiente' AFTER torneoId");
        } elseif ($columna === 'ganador') {
            $conexion->exec("ALTER TABLE peleas ADD COLUMN ganador VARCHAR(255) DEFAULT NULL AFTER estado");
        } elseif ($columna === 'observaciones') {
            $conexion->exec("ALTER TABLE peleas ADD COLUMN observaciones TEXT DEFAULT NULL AFTER ganador");
        } elseif ($columna === 'fecha_resultado') {
            $conexion->exec("ALTER TABLE peleas ADD COLUMN fecha_resultado DATETIME DEFAULT NULL AFTER observaciones");
        }
    }
} catch (Throwable $error) {
    // Las mejoras de esquema no deben impedir la carga del sistema.
}
