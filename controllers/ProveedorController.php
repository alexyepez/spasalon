<?php

namespace Controllers;

use Model\Proveedor;
use Model\Inventario;
use MVC\Router;

class ProveedorController {

    // Método para mostrar todos los proveedores
    public static function index(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        // Obtener todos los proveedores
        $proveedores = Proveedor::all();
        $alertas = [];

        $router->render('admin/gestionar-proveedores', [
            'nombre' => $_SESSION['nombre'] ?? '',
            'proveedores' => $proveedores,
            'alertas' => $alertas
        ]);
    }

    // Método para mostrar el inventario
    public static function inventario(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        // Obtener todos los productos del inventario
        $inventario = Inventario::all();
        // Obtener todos los proveedores para el formulario
        $proveedores = Proveedor::all();
        $alertas = [];

        $router->render('admin/gestionar-inventario', [
            'nombre' => $_SESSION['nombre'] ?? '',
            'inventario' => $inventario,
            'proveedores' => $proveedores,
            'alertas' => $alertas
        ]);
    }

    // Método para crear un nuevo proveedor
    public static function crear(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        $proveedor = new Proveedor();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proveedor->sincronizar($_POST);
            $alertas = $proveedor->validar();

            if (empty($alertas)) {
                $resultado = $proveedor->guardar();
                if ($resultado) {
                    header('Location: /admin/gestionar-proveedores?exito=creado');
                    exit;
                } else {
                    Proveedor::setAlerta('error', 'Error al crear el proveedor');
                }
            }
        }

        $alertas = Proveedor::getAlertas();
        $router->render('admin/crear-proveedor', [
            'nombre' => $_SESSION['nombre'] ?? '',
            'proveedor' => $proveedor,
            'alertas' => $alertas
        ]);
    }

    // Método para crear un nuevo producto en inventario
    public static function crearInventario(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        $inventario = new Inventario();
        $proveedores = Proveedor::all();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Manejar fecha
            if (isset($_POST['fecha_ingreso']) && !empty($_POST['fecha_ingreso'])) {
                $_POST['fecha_ingreso'] = date('Y-m-d', strtotime($_POST['fecha_ingreso']));
            } else {
                $_POST['fecha_ingreso'] = date('Y-m-d'); // Fecha actual
            }

            $inventario->sincronizar($_POST);
            $alertas = $inventario->validar();

            if (empty($alertas)) {
                $resultado = $inventario->guardar();
                if ($resultado) {
                    header('Location: /admin/gestionar-inventario?exito=creado');
                    exit;
                } else {
                    Inventario::setAlerta('error', 'Error al crear el producto');
                }
            }
        }

        $alertas = Inventario::getAlertas();
        $router->render('admin/crear-inventario', [
            'nombre' => $_SESSION['nombre'] ?? '',
            'inventario' => $inventario,
            'proveedores' => $proveedores,
            'alertas' => $alertas
        ]);
    }

    // Método para actualizar un proveedor
    public static function actualizar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

            if (!$id) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'ID de proveedor inválido'
                ]);
                return;
            }

            $proveedor = Proveedor::find($id);

            if (!$proveedor) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Proveedor no encontrado'
                ]);
                return;
            }

            // Sincronizar datos - Incluir email y dirección
            $datosProveedor = [
                'nombre' => $_POST['nombre'] ?? $proveedor->nombre,
                'contacto' => $_POST['contacto'] ?? $proveedor->contacto,
                'telefono' => $_POST['telefono'] ?? $proveedor->telefono,
                'email' => $_POST['email'] ?? $proveedor->email,
                'direccion' => $_POST['direccion'] ?? $proveedor->direccion
            ];

            $proveedor->sincronizar($datosProveedor);
            $alertas = $proveedor->validar();

            if (empty($alertas)) {
                $resultado = $proveedor->guardar();
                echo json_encode([
                    'resultado' => $resultado ? true : false,
                    'mensaje' => $resultado ? 'Proveedor actualizado correctamente' : 'Error al actualizar proveedor'
                ]);
            } else {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => $alertas['error'][0] ?? 'Error de validación'
                ]);
            }

            return;
        }
    }

    // Método para actualizar un producto del inventario
    public static function actualizarInventario() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

            if (!$id) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'ID de producto inválido'
                ]);
                return;
            }

            $inventario = Inventario::find($id);

            if (!$inventario) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Producto no encontrado'
                ]);
                return;
            }

            // Manejar fecha
            if (isset($_POST['fecha_ingreso']) && !empty($_POST['fecha_ingreso'])) {
                $_POST['fecha_ingreso'] = date('Y-m-d', strtotime($_POST['fecha_ingreso']));
            }

            // Sincronizar datos
            $datosInventario = [
                'producto' => $_POST['producto'] ?? $inventario->producto,
                'descripcion' => $_POST['descripcion'] ?? $inventario->descripcion,
                'precio' => $_POST['precio'] ?? $inventario->precio,
                'cantidad' => $_POST['cantidad'] ?? $inventario->cantidad,
                'proveedor_id' => $_POST['proveedor_id'] ?? $inventario->proveedor_id,
                'fecha_ingreso' => $_POST['fecha_ingreso'] ?? $inventario->fecha_ingreso
            ];

            $inventario->sincronizar($datosInventario);
            $alertas = $inventario->validar();

            if (empty($alertas)) {
                $resultado = $inventario->guardar();
                echo json_encode([
                    'resultado' => $resultado ? true : false,
                    'mensaje' => $resultado ? 'Producto actualizado correctamente' : 'Error al actualizar producto'
                ]);
            } else {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => $alertas['error'][0] ?? 'Error de validación'
                ]);
            }

            return;
        }
    }

    // Método para eliminar un proveedor
    public static function eliminar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            // Obtener datos del JSON en el cuerpo de la solicitud
            $datos = json_decode(file_get_contents("php://input"), true);
            $id = filter_var($datos['id'] ?? '', FILTER_VALIDATE_INT);

            if (!$id) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'ID de proveedor inválido'
                ]);
                return;
            }

            // Verificar si hay productos asociados a este proveedor
            $productosAsociados = Inventario::whereAll('proveedor_id', $id);

            if (!empty($productosAsociados)) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'No se puede eliminar el proveedor porque tiene productos asociados en el inventario'
                ]);
                return;
            }

            $proveedor = Proveedor::find($id);

            if (!$proveedor) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Proveedor no encontrado'
                ]);
                return;
            }

            $resultado = $proveedor->eliminar();

            echo json_encode([
                'resultado' => $resultado ? true : false,
                'mensaje' => $resultado ? 'Proveedor eliminado correctamente' : 'Error al eliminar proveedor'
            ]);

            return;
        }
    }

    // Método para eliminar un producto del inventario
    public static function eliminarInventario() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            // Obtener datos del JSON en el cuerpo de la solicitud
            $datos = json_decode(file_get_contents("php://input"), true);
            $id = filter_var($datos['id'] ?? '', FILTER_VALIDATE_INT);

            if (!$id) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'ID de producto inválido'
                ]);
                return;
            }

            $inventario = Inventario::find($id);

            if (!$inventario) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Producto no encontrado'
                ]);
                return;
            }

            $resultado = $inventario->eliminar();

            echo json_encode([
                'resultado' => $resultado ? true : false,
                'mensaje' => $resultado ? 'Producto eliminado correctamente' : 'Error al eliminar producto'
            ]);

            return;
        }
    }
}