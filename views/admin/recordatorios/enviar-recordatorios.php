<?php
// En /scripts/enviar-recordatorios.php
require_once __DIR__ . '/../vendor/autoload.php';

use Controllers\RecordatorioController;

// Iniciar la aplicación
$app = new MVC\Router();

// Enviar los recordatorios
$enviados = RecordatorioController::enviarRecordatorios();

echo "Se han enviado $enviados recordatorios.\n";