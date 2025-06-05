<?php
namespace Controllers;

use Model\Membresia;
use Model\ClienteMembresia;
use Model\Cliente;
use MVC\Router;

class MembresiaController {
    public static function index(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        $membresias = Membresia::all();
        $alertas = [];
        $hoy = date('Y-m-d');

        // Contar clientes activos para cada membresía
        foreach ($membresias as &$membresia) {
            // Consulta directamente a la tabla clientes_membresias usando PHP y el método whereAll
            $membresiaAsignaciones = ClienteMembresia::whereAll('membresia_id', $membresia->id);

            // Inicializar contador
            $clientesActivos = 0;

            // Para cada asignación, verificar si está activa hoy
            foreach ($membresiaAsignaciones as $asignacion) {
                if (
                    $asignacion->fecha_inicio <= $hoy &&
                    $asignacion->fecha_fin >= $hoy
                ) {
                    $clientesActivos++;
                }
            }

            // Asignar el conteo calculado
            $membresia->clientesActivos = $clientesActivos;

            //error_log("Membresía {$membresia->id} tiene {$membresia->clientesActivos} clientes activos (conteo manual)");
        }

        $router->render('membresias/index', [
            'membresias' => $membresias,
            'alertas' => $alertas,
            'nombre' => $_SESSION['nombre'] ?? ''
        ]);
    }

    public static function crear(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        $membresia = new Membresia;
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $membresia->sincronizar($_POST);
            $alertas = $membresia->validar();

            if (empty($alertas)) {
                $resultado = $membresia->guardar();

                if ($resultado) {
                    header('Location: /membresias?exito=creado');
                    exit;
                }
            }
        }

        $router->render('membresias/crear', [
            'membresia' => $membresia,
            'alertas' => $alertas,
            'nombre' => $_SESSION['nombre'] ?? ''
        ]);
    }

    public static function actualizar(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        $id = $_GET['id'];
        if (!is_numeric($id)) return;

        $membresia = Membresia::find($id);
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $membresia->sincronizar($_POST);
            $alertas = $membresia->validar();

            if (empty($alertas)) {
                $resultado = $membresia->guardar();

                if ($resultado) {
                    header('Location: /membresias?exito=actualizado');
                    exit;
                }
            }
        }

        $router->render('membresias/actualizar', [
            'membresia' => $membresia,
            'alertas' => $alertas,
            'nombre' => $_SESSION['nombre'] ?? ''
        ]);
    }

    public static function eliminar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];

            // Verificar si hay clientes con esta membresía
            $clientesMembresias = ClienteMembresia::whereAll('membresia_id', $id);

            if (!empty($clientesMembresias)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'No se puede eliminar esta membresía porque hay clientes que la tienen asignada'
                ]);
                return;
            }

            $membresia = Membresia::find($id);
            $resultado = $membresia->eliminar();

            if ($resultado) {
                header('Location: /membresias?exito=eliminado');
                exit;
            }
        }
    }
}