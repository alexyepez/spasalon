<?php

namespace Controllers;

use MVC\Router;
use Model\Cliente;
use Model\Familiar;

class CitaController {

    public static function index( Router $router ) {
        //session_start();

        // Alternativa: Verificar si ya hay una sesión activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        // Llama a la función isAuth() para verificar si el usuario está autenticado.
        isAuth();

        $usuario_id = $_SESSION['id'];
        $cliente = Cliente::where('usuario_id', $usuario_id);
        $familiares = [];

        if ($cliente) {
            $familiares = Familiar::whereAll('cliente_id', $cliente->id);
        }

        $router->render('cita/index', [
            'nombre' => $_SESSION['nombre'],
            'apellido' => $_SESSION['apellido'],
            'cliente' => $cliente,
            'familiares' => $familiares
        ]);
    }
}