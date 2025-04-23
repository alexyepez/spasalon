<?php


try {
    $db = new PDO('mysql:host=localhost;dbname=spasalon', 'root', 'root');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('SET NAMES utf8mb4');
} catch (PDOException $e) {
    echo "Error: No se pudo conectar a MySQL.";
    echo "Error de depuración: " . $e->getMessage();
    exit;
}

/*
$db = mysqli_connect('localhost', 'root', 'root', 'spasalon');


if (!$db) {
    echo "Error: No se pudo conectar a MySQL.";
    echo "errno de depuración: " . mysqli_connect_errno();
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}

/*
if ($db) {
    echo "Conexión exitosa a la base de datos";
} else {
    echo "Error en la conexión: " . mysqli_connect_error();
}
*/