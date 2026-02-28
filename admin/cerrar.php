<?php

require_once __DIR__ . '/includes/app.php';

logout_user();
set_flash('success', 'Sesion cerrada correctamente.');
redirect_to('login.php');

