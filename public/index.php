<?php

// Desactivar la visualización de errores para las peticiones AJAX/API
// Configuración de registro de errores para APIs
if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
    ini_set('display_errors', 0); // No mostrar errores al navegador
    //ini_set('log_errors', 1); // Habilitar registro de errores
    //ini_set('error_log', __DIR__ . '/../logs/php_errors.log'); // Establecer archivo de registro
}


require_once __DIR__ . '/../includes/app.php';
require_once __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../helpers.php';

use Controllers\APIController;
use Controllers\CitaController;
use Controllers\LoginController;
use Controllers\TerapeutaController;
use Controllers\AdminController;
use Controllers\ServicioController;
use Controllers\ClienteController;
use Controllers\ProveedorController;
use Controllers\RecordatorioController;
use Controllers\MembresiaController;
use Controllers\ClienteMembresiaController;
use Controllers\RecomendacionController;
use MVC\Router;
$router = new Router();

// Iniciar Sesión
$router->get('/', function($router) {
    $router->render('landing', [
        'claseImagen' => 'imagen-landing', // Específico para el landing
        'landing' => true // Indicador de que esta es la página de landing
    ]);
});


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
$router->post('/terapeuta/index', [TerapeutaController::class, 'index']);

// API de Tratamientos
$router->get('/api/tratamientos', [APIController::class, 'tratamientos']);

// Confirmar cuenta
$router->get('/confirmar-cuenta', [LoginController::class, 'confirmar']);
$router->get('/mensaje', [LoginController::class, 'mensaje']);

// Área de familiares
$router->get('/api/familiares', [APIController::class, 'familiares']);
$router->post('/api/familiares/crear', [APIController::class, 'crearFamiliar']);
$router->post('/api/familiares/eliminar', [APIController::class, 'eliminarFamiliar']);
$router->post('/api/familiares/actualizar', [APIController::class, 'actualizarFamiliar']);

// AREA PRIVADA
$router->post('/api/citas/estado', [APIController::class, 'cambiarEstadoCita']);
$router->get('/cita', [CitaController::class, 'index']);

// API de Citas
$router->get('/api/servicios', [APIController::class, 'index']);
$router->post('/api/citas', [APIController::class, 'guardar']);
$router->post('/api/eliminar', [APIController::class, 'eliminar']);

// CRUD de Servicios
$router->get('/servicios', [ServicioController::class, 'index']);
$router->get('/servicios/crear', [ServicioController::class, 'crear']);
$router->post('/servicios/crear', [ServicioController::class, 'crear']);
$router->get('/servicios/actualizar', [ServicioController::class, 'actualizar']);
$router->post('/servicios/actualizar', [ServicioController::class, 'actualizar']);
$router->post('/servicios/eliminar', [ServicioController::class, 'eliminar']);

// Rutas para gestión de terapeutas
$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin/gestionar-terapeutas', [AdminController::class, 'gestionarTerapeutas']);
$router->get('/admin/crear-terapeuta', [AdminController::class, 'crearTerapeuta']);
$router->post('/admin/crear-terapeuta', [AdminController::class, 'crearTerapeuta']);
$router->post('/admin/actualizar-terapeuta', [AdminController::class, 'actualizarTerapeuta']);
$router->post('/admin/eliminar-terapeuta', [AdminController::class, 'eliminarTerapeuta']);
$router->post('/admin/asignar-cita', [AdminController::class, 'asignarCita']);
$router->get('/admin/historial-citas', [Controllers\AdminController::class, 'historialCitas']);

// Rutas para gestión de clientes
$router->get('/admin/gestionar-clientes', [ClienteController::class, 'index']);
$router->get('/admin/crear-cliente', [ClienteController::class, 'crear']);
$router->post('/admin/crear-cliente', [ClienteController::class, 'crear']);
$router->post('/admin/actualizar-cliente', [ClienteController::class, 'actualizar']);
$router->post('/admin/eliminar-cliente', [ClienteController::class, 'eliminar']);

// Rutas para Proveedores e Inventario
$router->get('/admin/gestionar-proveedores', [ProveedorController::class, 'index']);
$router->get('/admin/crear-proveedor', [ProveedorController::class, 'crear']);
$router->post('/admin/crear-proveedor', [ProveedorController::class, 'crear']);
$router->post('/admin/actualizar-proveedor', [ProveedorController::class, 'actualizar']);
$router->post('/admin/eliminar-proveedor', [ProveedorController::class, 'eliminar']);

$router->get('/admin/gestionar-inventario', [ProveedorController::class, 'inventario']);
$router->get('/admin/crear-inventario', [ProveedorController::class, 'crearInventario']);
$router->post('/admin/crear-inventario', [ProveedorController::class, 'crearInventario']);
$router->post('/admin/actualizar-inventario', [ProveedorController::class, 'actualizarInventario']);
$router->post('/admin/eliminar-inventario', [ProveedorController::class, 'eliminarInventario']);

// Rutas de Recordatorios
$router->get('/admin/recordatorios', [RecordatorioController::class, 'index']);
$router->get('/admin/recordatorios/crear', [RecordatorioController::class, 'crear']);
$router->post('/admin/recordatorios/crear', [RecordatorioController::class, 'crear']);
$router->post('/admin/eliminar-recordatorio', [RecordatorioController::class, 'eliminar']);
$router->get('/admin/recordatorios/enviar', [RecordatorioController::class, 'ejecutarEnvio']);
$router->post('/admin/recordatorios/ejecutar-envio', [RecordatorioController::class, 'ejecutarEnvio']);
$router->post('/admin/enviar-recordatorio', [RecordatorioController::class, 'enviarRecordatorioIndividual']);

// Ruta para confirmar citas desde recordatorios
$router->get('/confirmar-cita', [CitaController::class, 'confirmarCita']);

// Rutas para membresías
$router->get('/membresias', [MembresiaController::class, 'index']);
$router->get('/membresias/crear', [MembresiaController::class, 'crear']);
$router->post('/membresias/crear', [MembresiaController::class, 'crear']);
$router->get('/membresias/actualizar', [MembresiaController::class, 'actualizar']);
$router->post('/membresias/actualizar', [MembresiaController::class, 'actualizar']);
$router->post('/membresias/eliminar', [MembresiaController::class, 'eliminar']);

// Rutas para membresías de clientes
$router->get('/admin/clientes/membresias', [ClienteMembresiaController::class, 'index']);
$router->get('/admin/clientes/membresia/crear', [ClienteMembresiaController::class, 'crear']);
$router->post('/admin/clientes/membresia/crear', [ClienteMembresiaController::class, 'crear']);
$router->post('/admin/clientes/membresias/eliminar', [ClienteMembresiaController::class, 'eliminar']);

// Rutas para recomendaciones IA
$router->post('/api/recomendaciones/generar', [RecomendacionController::class, 'generarRecomendaciones']);
$router->post('/api/recomendaciones/obtener', [RecomendacionController::class, 'obtenerRecomendaciones']);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();