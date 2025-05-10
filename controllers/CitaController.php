<?php

namespace Controllers;

use MVC\Router;
use Model\Cliente;
use Model\Familiar;
use Model\Servicio;

class CitaController {

    public static function index( Router $router ) {
        session_start();

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