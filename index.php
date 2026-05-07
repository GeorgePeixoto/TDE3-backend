<?php
ob_start();
session_start();
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';

// Base URL para assets e links — funciona independente de subpasta
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
define('BASE_URL', $scriptDir);

$page = $_GET['page'] ?? 'dashboard';

$routes = [
    'dashboard'         => __DIR__ . '/pages/dashboard.php',
    // Destinos
    'destinos'          => __DIR__ . '/pages/destinos/index.php',
    'destinos_criar'    => __DIR__ . '/pages/destinos/criar.php',
    'destinos_editar'   => __DIR__ . '/pages/destinos/editar.php',
    'destinos_deletar'  => __DIR__ . '/pages/destinos/deletar.php',
    // Pacotes
    'pacotes'           => __DIR__ . '/pages/pacotes/index.php',
    'pacotes_criar'     => __DIR__ . '/pages/pacotes/criar.php',
    'pacotes_editar'    => __DIR__ . '/pages/pacotes/editar.php',
    'pacotes_deletar'   => __DIR__ . '/pages/pacotes/deletar.php',
    // Clientes
    'clientes'          => __DIR__ . '/pages/clientes/index.php',
    'clientes_criar'    => __DIR__ . '/pages/clientes/criar.php',
    'clientes_editar'   => __DIR__ . '/pages/clientes/editar.php',
    'clientes_deletar'  => __DIR__ . '/pages/clientes/deletar.php',
    // Reservas
    'reservas'          => __DIR__ . '/pages/reservas/index.php',
    'reservas_criar'    => __DIR__ . '/pages/reservas/criar.php',
    'reservas_editar'   => __DIR__ . '/pages/reservas/editar.php',
    'reservas_deletar'  => __DIR__ . '/pages/reservas/deletar.php',
];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';

if (isset($routes[$page])) {
    require_once $routes[$page];
} else {
    echo '<div class="container"><h2>Pagina nao encontrada</h2></div>';
}

require_once __DIR__ . '/includes/footer.php';
