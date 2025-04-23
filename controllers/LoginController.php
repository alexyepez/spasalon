<?php

namespace Controllers;

use Model\Usuario;
use MVC\Router;


class LoginController {
    public static function login(Router $router) {
        $router->render('auth/login');
    }

    public static function logout() {
        echo "Desde Logout";
    }

    public static function olvide( Router $router) {
        $router->render('auth/olvide-password', [

        ]);

    }

    public static function recuperar() {
        echo "Desde recuperar";
    }

    public static function crear(Router $router) {
    $usuario = new Usuario;
    $alertas = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usuario->sincronizar($_POST);
        $alertas = $usuario->validarNuevaCuenta();

        if (empty($alertas)) {
            $resultado = $usuario->existeUsuario();
            if ($resultado->num_rows) {
                $alertas = Usuario::getAlertas();
            } else {
                // Hashear la contraseña
                debuguear('Usuario no existe, se puede registrar');
                
                /*
                $usuario->password = password_hash($usuario->password, PASSWORD_BCRYPT);
                $usuario->rol_id = 1; // Cliente
                $usuario->confirmado = 0;

                $resultado = $usuario->guardar();
                if ($resultado['resultado']) {
                    $query = "INSERT INTO clientes (usuario_id, telefono, direccion) VALUES ('" . self::$db->escape_string($resultado['id']) . "', '" . self::$db->escape_string($usuario->telefono) . "', NULL)";
                    $insert_cliente = self::$db->query($query);
                    if ($insert_cliente) {
                        header("Location: /login?registro=exitoso");
                        exit;
                    } else {
                        $alertas['error'][] = 'Error al registrar el cliente';
                    }
                } else {
                    $alertas['error'][] = 'Error al registrar el usuario';
                }
                */
            }
        }
    }
    $router->render('auth/crear-cuenta', [
        'usuario' => $usuario,
        'alertas' => $alertas
    ]);
}
}