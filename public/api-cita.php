<?php
// api-cita.php
require_once __DIR__ . '/../includes/app.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Habilitar visualización de errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;
use Model\Cliente;
use Model\Familiar;

// Establecer cabecera JSON
header('Content-Type: application/json');

// Obtener ID
$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['error' => 'ID de cita no proporcionado']);
    exit;
}

// Buscar cita
$cita = Cita::find($id);
if (!$cita) {
    echo json_encode(['error' => 'Cita no encontrada']);
    exit;
}

// Obtener servicios
$citaServicios = CitaServicio::whereAll('cita_id', $id);
$servicios = [];

foreach ($citaServicios as $citaServicio) {
    $servicio = Servicio::find($citaServicio->servicio_id);
    if ($servicio) {
        $servicios[] = [
            'id' => $servicio->id,
            'nombre' => $servicio->nombre,
            'precio' => $servicio->precio
        ];
    }
}

// Obtener cliente
$cliente = Cliente::find($cita->cliente_id);
$clienteNombre = '';
$clienteApellido = '';

if ($cliente && method_exists($cliente, 'getUsuario') && $cliente->getUsuario()) {
    $clienteNombre = $cliente->getUsuario()->nombre;
    $clienteApellido = $cliente->getUsuario()->apellido;
}

// Obtener familiar
$familiar = null;
if ($cita->familiar_id) {
    $familiar = Familiar::find($cita->familiar_id);
}

// Preparar respuesta
$respuesta = [
    'id' => $cita->id,
    'fecha' => $cita->fecha,
    'hora' => $cita->hora,
    'estado' => $cita->estado,
    'cliente_nombre' => $clienteNombre,
    'cliente_apellido' => $clienteApellido,
    'servicios' => $servicios
];

// Añadir familiar
if ($familiar) {
    $respuesta['familiar_id'] = $familiar->id;
    $respuesta['familiar_nombre'] = $familiar->nombre;
    $respuesta['familiar_apellido'] = $familiar->apellido;
}

echo json_encode($respuesta);