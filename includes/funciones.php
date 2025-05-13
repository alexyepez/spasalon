<?php

// DEBUGGING
function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

// Verificar si usuario es terapeuta
function esTerapeuta() : bool {
    if (!isset($_SESSION['rol_id'])) {
        return false;
    }
    return $_SESSION['rol_id'] == 2;
}

// Verificar si usuario es administrador
function esAdmin() : bool {
    if (!isset($_SESSION['rol_id'])) {
        return false;
    }
    return $_SESSION['rol_id'] == 1;
}

// Verificar si usuario es cliente
function esCliente() : bool {
    if (!isset($_SESSION['rol_id'])) {
        return false;
    }
    return $_SESSION['rol_id'] == 3 || !esAdmin() && !esTerapeuta();
}

// OTROS HELPERS
function redireccionar(string $url) : void {
    header("Location: $url");
    exit;
}