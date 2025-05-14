<?php

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log de errores en un archivo dentro de tu proyecto
$logPath = __DIR__ . '/../logs/debug.log';
ini_set('error_log', $logPath);

// Asegúrate de que el directorio de logs exista
if (!file_exists(dirname($logPath))) {
    mkdir(dirname($logPath), 0777, true);
}