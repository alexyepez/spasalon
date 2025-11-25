// test-api.php
<?php
// Activar visualización de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Cargar dependencias básicas
require_once __DIR__ . '/../includes/app.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Iniciar la sesión si es necesario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Intentar obtener una cita directamente
use Controllers\APIController;
use Model\Cita;

// ID de cita para probar
$citaId = 42; // Reemplaza con un ID válido de tu base de datos

echo "<h1>Prueba de API obtenerCita</h1>";

// Simular parámetros GET
$_GET['id'] = $citaId;

// Intentar obtener la cita directamente
echo "<h2>Llamada directa al controlador:</h2>";
APIController::obtenerCita();

// También probar obtener la cita desde el modelo
echo "<h2>Consulta directa al modelo:</h2>";
$cita = Cita::find($citaId);
echo "<pre>";
var_dump($cita);
echo "</pre>";