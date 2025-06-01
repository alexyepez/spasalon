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

function esUltimo(string $actual, string $proximo) : bool {
    if ($actual !== $proximo) {
        return true;
    }
    return false;
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

// Función que revisa que el usuario está autenticado
function isAuth() : void {
    if (!isset($_SESSION['login'])) {
        header('Location: /login');
    }
}

// Función que valida si alguien es administrador
function isAdmin() : void {
    if(!isset($_SESSION['admin'])) {
        header('Location: /login');
    }
}