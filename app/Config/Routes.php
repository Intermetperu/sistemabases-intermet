<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- Autenticación (igual que en el proyecto original) ---
$routes->get('/', 'AuthController::index');
$routes->post('/auth/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

$routes->get('forgot-password', 'AuthController::forgotPassword');
$routes->post('forgot-password', 'AuthController::sendResetLink');
$routes->get('reset-password/(:segment)', 'AuthController::resetPassword/$1');
$routes->post('reset-password/(:segment)', 'AuthController::updatePassword/$1');

// --- Zona protegida: plantilla admin + sidebar ---
$routes->group('', ['filter' => 'AuthCheck'], function ($routes) {
    $routes->get('/dashboard', 'Admin\DashboardController::index');

    $routes->get('profile', 'Admin\ProfileController::edit');
    $routes->post('profile/update', 'Admin\ProfileController::update');
    $routes->post('profile/change-password', 'Admin\ProfileController::changePassword');

    // --- Módulo de Contactos (migrado del sistema en Node/Express) ---
    $routes->get('contactos', 'Admin\ContactoController::index');
    $routes->get('contactos/buscar', 'Admin\ContactoController::buscar');
    $routes->get('contactos/paises', 'Admin\ContactoController::paises');
    $routes->get('contactos/empresas-buscar', 'Admin\ContactoController::empresasBuscar');
    $routes->get('contactos/duplicados', 'Admin\ContactoController::duplicados');
    $routes->post('contactos/csv', 'Admin\ContactoController::subirCsv');
    $routes->post('contactos/actualizar/(:num)', 'Admin\ContactoController::actualizar/$1');
    $routes->post('contactos/eliminar', 'Admin\ContactoController::eliminar');
    $routes->get('contactos/total', 'Admin\ContactoController::total');
    $routes->get('contactos/listar', 'Admin\ContactoController::listar');
    $routes->get('contactos/columnas', 'Admin\ContactoController::columnas');
    $routes->get('contactos/contar-filtro', 'Admin\ContactoController::contarFiltro');
    $routes->post('contactos/crear', 'Admin\ContactoController::crear');
    $routes->post('contactos/eliminar-por-filtro', 'Admin\ContactoController::eliminarPorFiltro');
    $routes->get('contactos/exportar-csv', 'Admin\ContactoController::exportarCsv');
    $routes->get('contactos/eventos', 'Admin\ContactoController::eventos');
    $routes->post('contactos/eventos/crear', 'Admin\ContactoController::crearEvento');
    $routes->get('contactos/historial', 'Admin\ContactoController::historial');
    $routes->get('contactos/historial/(:num)/contactos', 'Admin\ContactoController::historialContactos/$1');
    $routes->get('contactos/correos/cuota', 'Admin\EnvioCorreoController::cuota');
    $routes->post('contactos/correos/subir-archivo', 'Admin\EnvioCorreoController::subirArchivo');
    $routes->post('contactos/correos/enviar', 'Admin\EnvioCorreoController::enviar');
});