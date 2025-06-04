<?php

namespace Controllers;

use Model\Usuario;
use Model\Cliente;
use MVC\Router;

class ClienteController {

    // Método para mostrar todos los clientes
    public static function index(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        // Obtener todos los clientes (usuarios con rol_id=3)
        // Obtener todos los clientes (usuarios con rol_id=3)
        $usuariosClientes = Usuario::whereAll('rol_id', '3');

        // Agregar el ID real del cliente a cada objeto de usuario
        $clientes = [];
        foreach ($usuariosClientes as $usuario) {
            $clienteReal = Cliente::where('usuario_id', $usuario->id);
            if ($clienteReal) {
                // Agregar el ID del cliente al objeto usuario
                $usuario->cliente_id = $clienteReal->id;
                $clientes[] = $usuario;
            } else {
                // Si prefieres incluir a todos los usuarios aunque no tengan cliente
                $usuario->cliente_id = null;
                $clientes[] = $usuario;
            }
        }


        $router->render('admin/gestionar-clientes', [
            'nombre' => $_SESSION['nombre'] ?? '',
            'clientes' => $clientes,
            'alertas' => Usuario::getAlertas() // Aseguramos que alertas esté definido
        ]);
    }

    // Método para mostrar formulario de nuevo cliente y procesarlo
    public static function crear(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        $cliente = new Usuario();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cliente->sincronizar($_POST);

            // Validaciones
            $alertas = $cliente->validarNuevoCliente();

            if (empty($alertas)) {
                // Verificar que el email no exista
                $existeUsuario = Usuario::where('email', $cliente->email);

                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'El usuario ya está registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear password
                    $cliente->hashPassword();

                    // Generar token único
                    //$cliente->crearToken();

                    // Establecer como cliente (rol_id = 3)
                    $cliente->rol_id = 3;

                    // Establecer valor predeterminado para confirmado (activado)
                    $cliente->confirmado = 1;

                    // Guardar usuario
                    $resultado = $cliente->guardar();

                    if ($resultado) {
                        // Obtener el ID del usuario recién creado
                        $usuario_id = is_array($resultado) && isset($resultado['id']) ? $resultado['id'] : $cliente->id;

                        // Crear registro en la tabla Cliente
                        $datosCliente = [
                            'usuario_id' => $usuario_id,
                            'telefono' => $cliente->telefono,
                            'direccion' => '' // Campo vacío por defecto
                        ];

                        $nuevoCliente = new Cliente($datosCliente);
                        $resultadoCliente = $nuevoCliente->guardar();

                        if ($resultadoCliente) {
                            header('Location: /admin/gestionar-clientes');
                            exit;
                        } else {
                            // Si falla la creación del cliente, alertamos pero el usuario ya está creado
                            Usuario::setAlerta('error', 'Usuario creado, pero hubo un error al registrar datos de cliente');
                        }
                    } else {
                        Usuario::setAlerta('error', 'Error al registrar el cliente');
                    }
                }
            }

            $alertas = Usuario::getAlertas();
        }

        $router->render('admin/crear-cliente', [
            'nombre' => $_SESSION['nombre'] ?? '',
            'cliente' => $cliente,
            'alertas' => $alertas
        ]);
    }

    // Método para actualizar información de un cliente
    public static function actualizar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Establecer el tipo de contenido a JSON
            header('Content-Type: application/json');

            // Registrar datos recibidos
            error_log("POST datos recibidos: " . print_r($_POST, true));

            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

            if (!$id) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'ID de cliente inválido'
                ]);
                return;
            }

            $usuario = Usuario::find($id);

            if (!$usuario) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Cliente no encontrado'
                ]);
                return;
            }

            // Sincronizar datos del usuario
            $datosUsuario = [
                'nombre' => $_POST['nombre'] ?? $usuario->nombre,
                'apellido' => $_POST['apellido'] ?? $usuario->apellido,
                'email' => $_POST['email'] ?? $usuario->email
            ];

            $usuario->sincronizar($datosUsuario);

            // Guardar cambios en la tabla Usuario
            $resultadoUsuario = $usuario->guardar();

            // Actualizar también el registro en la tabla Cliente
            $cliente = Cliente::where('usuario_id', $id);

            if ($cliente) {
                $cliente->telefono = $_POST['telefono'] ?? $cliente->telefono;
                $resultadoCliente = $cliente->guardar();
            } else {
                // Si no existe el registro de cliente, lo creamos
                $nuevoCliente = new Cliente([
                    'usuario_id' => $id,
                    'telefono' => $_POST['telefono'] ?? '',
                    'direccion' => ''
                ]);
                $resultadoCliente = $nuevoCliente->guardar();
            }

            // Verificar resultados
            $exito = $resultadoUsuario ? true : false;

            echo json_encode([
                'resultado' => $exito,
                'mensaje' => $exito ? 'Cliente actualizado correctamente' : 'Error al actualizar cliente',
                'debug' => [
                    'usuario' => $resultadoUsuario,
                    'cliente' => $resultadoCliente ?? null
                ]
            ]);
            return;
        }
    }

    // Método para eliminar un cliente
    public static function eliminar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Establecer el tipo de contenido a JSON
            header('Content-Type: application/json');

            // Registrar datos recibidos
            error_log("POST datos recibidos: " . print_r($_POST, true));

            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

            if (!$id) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'ID de cliente inválido'
                ]);
                return;
            }

            $usuario = Usuario::find($id);

            if (!$usuario) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Cliente no encontrado'
                ]);
                return;
            }

            // Verificar que no sea el admin que está intentando eliminarse a sí mismo
            if ($usuario->rol_id == 1 && $usuario->id === $_SESSION['id']) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'No puedes eliminar tu propio usuario de administrador'
                ]);
                return;
            }

            // Primero eliminar el registro de la tabla Cliente
            $cliente = Cliente::where('usuario_id', $id);
            if ($cliente) {
                $cliente->eliminar();
            }

            // Eliminar el usuario
            $resultado = $usuario->eliminar();

            echo json_encode([
                'resultado' => $resultado ? true : false,
                'mensaje' => $resultado ? 'Cliente eliminado correctamente' : 'Error al eliminar cliente'
            ]);
            return;
        }
    }
}