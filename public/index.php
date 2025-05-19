<?php 

require_once __DIR__ . '/../includes/app.php';
require_once __DIR__ . '/../vendor/autoload.php';
//require_once __DIR__ . '/../config/error_log.php';

use Controllers\APIController;
use Controllers\CitaController;
use Controllers\LoginController;
use Controllers\TerapeutaController;
use MVC\Router;
$router = new Router();

// Iniciar Sesión
$router->get('/', function($router) {
    $router->render('landing', [
        'claseImagen' => 'imagen-landing', // Específico para el landing
        'landing' => true // Indicador de que esta es la página de landing
    ]);
});


// $router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);
$router->post('/login', [LoginController::class, 'login']);
$router->get('/login', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

// Recuperar password
$router->get('/olvide', [LoginController::class, 'olvide']);
$router->post('/olvide', [LoginController::class, 'olvide']);
$router->get('/recuperar', [LoginController::class, 'recuperar']);
$router->post('/recuperar', [LoginController::class, 'recuperar']);

// Crear Cuenta
$router->get('/crear-cuenta', [LoginController::class, 'crear']);
$router->post('/crear-cuenta', [LoginController::class, 'crear']);

// Panel del Terapeuta
$router->get('/terapeuta/index', [TerapeutaController::class, 'index']);

// Confirmar cuenta
$router->get('/confirmar-cuenta', [LoginController::class, 'confirmar']);
$router->get('/mensaje', [LoginController::class, 'mensaje']);

// Área de familiares
$router->get('/api/familiares', [APIController::class, 'familiares']);
$router->post('/api/familiares/crear', [APIController::class, 'crearFamiliar']);
$router->post('/api/familiares/eliminar', [APIController::class, 'eliminarFamiliar']);
$router->post('/api/familiares/actualizar', [APIController::class, 'actualizarFamiliar']);

// AREA PRIVADA
$router->get('/cita', [CitaController::class, 'index']);

// API de Citas
$router->get('/api/servicios', [APIController::class, 'index']);
$router->post('/api/citas', [APIController::class, 'guardar']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();