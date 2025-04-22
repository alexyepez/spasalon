<?php

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