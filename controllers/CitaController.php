<?php

namespace Controllers;

use MVC\Router;
use Model\Cliente;
use Model\Familiar;
use Model\Cita;

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

    public static function confirmarCita(Router $router) {
        // Iniciar sesión para asegurar que $_SESSION esté disponible
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Obtener el token de la URL
        $token = $_GET['token'] ?? null;

        if (!$token) {
            header('Location: /');
            exit;
        }

        // Buscar la cita por su ID (que es el token)
        $cita = Cita::find($token);

        if (!$cita) {
            // La cita no existe
            $router->render('cita/error', [
                'titulo' => 'Error al confirmar',
                'mensaje' => 'La cita que intentas confirmar no existe o ya ha sido procesada.'
            ]);
            return;
        }

        // Si la cita ya está confirmada, considerarlo un éxito
        if ($cita->estado === 'confirmada') {
            $router->render('cita/confirmar', [
                'titulo' => 'Cita Ya Confirmada',
                'cita' => $cita,
                'resultado' => true
            ]);
            return;
        }

        // Actualizar el estado de la cita a "confirmada"
        $cita->estado = '2';
        $resultado = $cita->guardar();

        // Render de la vista con el resultado
        $router->render('cita/confirmar', [
            'titulo' => $resultado ? 'Cita Confirmada' : 'Error al Confirmar',
            'cita' => $cita,
            'resultado' => $resultado
        ]);
    }
}