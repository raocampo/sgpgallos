<?php

require_once __DIR__ . '/includes/app.php';
require_once __DIR__ . '/bd.php';

start_secure_session();

if (!empty($_SESSION['user_id'])) {
    redirect_to();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $usuarioIngresado = post('usuario');
    $claveIngresada = post('clave');

    if ($usuarioIngresado === '' || $claveIngresada === '') {
        $error = 'Ingrese usuario y contrasena.';
    } else {
        $sentencia = $conexion->prepare('SELECT * FROM usuarios WHERE apodo = :usuario LIMIT 1');
        $sentencia->bindValue(':usuario', $usuarioIngresado);
        $sentencia->execute();
        $usuario = $sentencia->fetch();

        $autenticado = false;

        if ($usuario) {
            $claveGuardada = (string) ($usuario['clave'] ?? '');
            $autenticado = verify_stored_password($claveIngresada, $claveGuardada);

            if ($autenticado && !stored_password_is_hash($claveGuardada)) {
                $nuevoHash = hash_user_password($claveIngresada);
                $actualizaClave = $conexion->prepare('UPDATE usuarios SET clave = :clave WHERE ID = :id');
                $actualizaClave->bindValue(':clave', $nuevoHash);
                $actualizaClave->bindValue(':id', (int) $usuario['ID'], PDO::PARAM_INT);
                $actualizaClave->execute();
                $usuario['clave'] = $nuevoHash;
            }
        }

        if ($autenticado) {
            login_user($usuario);
            set_flash('success', 'Sesion iniciada correctamente.');
            redirect_to();
        }

        $error = 'Usuario o contrasena incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingreso al Sistema</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset_url('css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset_url('css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset_url('css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset_url('css/admin-panel.css')); ?>">
</head>
<body class="app-login">
    <div class="container login-shell d-flex align-items-center py-5">
        <div class="row justify-content-center w-100">
            <div class="col-lg-5 col-xl-4">
                <div class="text-center mb-4">
                    <span class="app-kicker">Acceso seguro</span>
                    <h1 class="app-title mb-3">Panel de competiciones</h1>
                    <p class="login-copy">Administra torneos, cotejas, peleas y resultados desde una sola consola.</p>
                </div>
                <div class="card login-panel">
                    <div class="card-header text-center">
                        <img src="<?php echo e(asset_url('images/Logo.png')); ?>" alt="Logo" style="max-width: 210px; width: 100%; margin-bottom: 1rem;">
                        <h2 class="panel-title h4 mb-0">Ingresar</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($error !== ''): ?>
                            <div class="alert alert-danger" role="alert"><?php echo e($error); ?></div>
                        <?php endif; ?>

                        <?php render_flash(); ?>

                        <form method="post">
                            <?php echo csrf_input(); ?>

                            <div class="mb-3">
                                <label class="form-label" for="usuario">Usuario</label>
                                <input class="form-control" type="text" name="usuario" id="usuario" required autocomplete="username">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="clave">Contrasena</label>
                                <input class="form-control" type="password" name="clave" id="clave" required autocomplete="current-password">
                            </div>

                            <button class="btn btn-primary w-100" type="submit">Entrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
