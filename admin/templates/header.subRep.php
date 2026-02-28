<?php

require_once __DIR__ . '/../includes/app.php';

require_auth();
start_secure_session();
$context = require_tournament_context('No hay torneo seleccionado.');
$nombreTorneo = $context['nombreTorneo'];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php echo e($nombreTorneo); ?></title>
</head>
<body>
