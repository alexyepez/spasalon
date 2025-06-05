<?php
namespace Controllers;

use Model\Cliente;
use Model\ClienteMembresia;
use Model\Membresia;
use Model\Usuario;
use MVC\Router;

class ClienteMembresiaController {

    public static function index(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        $cliente_id = $_GET['cliente_id'] ?? null;

        if (!$cliente_id) {
            header('Location: /admin/clientes');
            exit;
        }

        $cliente = Cliente::find($cliente_id);

        if (!$cliente) {
            header('Location: /admin/clientes');
            exit;
        }

        $usuario = Usuario::find($cliente->usuario_id);
        $membresiaActiva = $cliente->getMembresiaActiva();

        // Si membresiaActiva no es null, verificar sus propiedades
        if ($membresiaActiva) {
            //error_log('Membresía activa encontrada: ' . print_r($membresiaActiva, true));
            //error_log('Tipo de membresiaActiva: ' . gettype($membresiaActiva));
            //error_log('Es objeto: ' . (is_object($membresiaActiva) ? 'Sí' : 'No'));

            if (is_object($membresiaActiva)) {
                $props = get_object_vars($membresiaActiva);
                //error_log('Propiedades: ' . print_r($props, true));
            }
        } else {
            //error_log('No se encontró membresía activa para el cliente ID: ' . $cliente_id);
        }

        $membresias = ClienteMembresia::whereAll('cliente_id', $cliente_id);

        $router->render('admin/clientes/membresias', [
            'cliente' => $cliente,
            'usuario' => $usuario,
            'membresiaActiva' => $membresiaActiva,
            'membresias' => $membresias,
            'nombre' => $_SESSION['nombre']
        ]);
    }

    public static function crear(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        $cliente_id = $_GET['cliente_id'] ?? null;

        if (!$cliente_id) {
            header('Location: /admin/clientes');
            exit;
        }

        $cliente = Cliente::find($cliente_id);

        if (!$cliente) {
            header('Location: /admin/clientes');
            exit;
        }

        $usuario = Usuario::find($cliente->usuario_id);
        $membresias = Membresia::all();
        $clienteMembresia = new ClienteMembresia();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clienteMembresia->sincronizar($_POST);
            $clienteMembresia->cliente_id = $cliente_id;

            $alertas = $clienteMembresia->validar();

            if (empty($alertas)) {
                $resultado = $clienteMembresia->guardar();

                if ($resultado) {
                    header('Location: /admin/clientes/membresias?cliente_id=' . $cliente_id);
                    exit;
                }
            }
        }

        $router->render('admin/clientes/membresia', [
            'cliente' => $cliente,
            'usuario' => $usuario,
            'membresias' => $membresias,
            'clienteMembresia' => $clienteMembresia,
            'alertas' => $alertas,
            'nombre' => $_SESSION['nombre']
        ]);
    }

    public static function eliminar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $clienteMembresia = ClienteMembresia::find($id);

            if (!$clienteMembresia) {
                header('Location: /admin/clientes');
                exit;
            }

            $cliente_id = $clienteMembresia->cliente_id;
            $resultado = $clienteMembresia->eliminar();

            if ($resultado) {
                header('Location: /admin/clientes/membresias?cliente_id=' . $cliente_id);
                exit;
            }
        }
    }
}