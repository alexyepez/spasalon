<?php

namespace Controllers;

use Model\Usuario;
use Model\Cliente;
use Classes\Email;
use Model\Colaborador;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];
        $auth = new Usuario;

        // Verificar si hay un parámetro de éxito en la URL
        $exito = false;
        $mensaje_exito = '';

        if (isset($_GET['exito'])) {
            $exito = true;
            if ($_GET['exito'] === 'password') {
                $mensaje_exito = 'Contraseña actualizada correctamente';
            } else if ($_GET['exito'] === 'registro') {
                $mensaje_exito = '¡Registro exitoso! Por favor, inicia sesión.';
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin(); // Se inicializa $auth por defecto
            $exito = false; // Se inicializa $exito para evitar errores

            if (empty($alertas)) {
                // Comprobar si el usuario existe
                $usuario = Usuario::where('email', $auth->email);
                if($usuario) {
                    // Verificar el password
                    if( $usuario->comprobarPasswordAndVerificado($auth->password) ) {
                        // Iniciar sesión
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['rol_id'] = $usuario->rol_id;
                        $_SESSION['login'] = true;

                        // Redireccionar al panel del administrador o terapeuta
                        if ($usuario->rol_id === '1') {
                            // Administrador
                            $_SESSION['admin'] = $usuario->rol_id ?? null;
                            header('Location: /admin');
                        } elseif ($usuario->rol_id == 2) { // Terapeuta
                            // Verificar si existe como colaborador
                            $colaborador = Colaborador::where('usuario_id', $usuario->id);
                            if ($colaborador) {
                                $_SESSION['colaborador_id'] = $colaborador->id;
                                header('Location: /terapeuta/index');
                            } else {
                                // Si es terapeuta pero no está registrado como colaborador
                                Usuario::setAlerta('error', 'No estás registrado como terapeuta');
                                header('Location: /');
                            }
                        } else {
                            // Cliente
                            //error_log("Redirigiendo a panel de cliente");
                            header('Location: /cita');
                        }
                        exit;
                    }

                } else {
                    Usuario::setAlerta('error', 'El usuario no existe');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/login', [
            'alertas' => $alertas,
            'auth' => $auth,
            'exito' => $exito,
            'mensaje_exito' => $mensaje_exito
        ]);
    }

    public static function logout() {
        session_start();

        $_SESSION=[];

        header('Location: /login');
    }

    public static function olvide( Router $router) {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();
            
            if (empty($alertas)) {
                // Buscar el usuario por email
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario && $usuario->confirmado === "1") {
                    // Generar un nuevo token
                    $usuario->crearToken();
                    $usuario->guardar(); // Guardar el nuevo token

                    // Enviar el email de recuperación
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Mensaje de éxito
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                } else {
                    // Mensaje de error
                    Usuario::setAlerta('error', 'El usuario no existe o no está confirmado');
                }
            }

        $alertas = Usuario::getAlertas();

        }
        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router) {
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);
        
        // Se busca el usuario por el token
        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido o no existe');
            $error = true;
        }

        // Si el usuario existe, se valida la nueva contraseña
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if (empty($alertas)) {
                $usuario->password = null;

                // Hashear la nueva contraseña
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null; // Limpiar el token
                $resultado = $usuario->guardar(); // Guardar el nuevo password

                if ($resultado) {
                    // Redirigir al login con un parámetro de éxito
                    header('Location: /login?exito=password');

                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router) {
        $usuario = new Usuario;
        $alertas = [];

        // Aquí puedes validar y registrar
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if (empty($alertas)) {
                $resultado = $usuario->existeUsuario();
                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear la contraseña
                    $usuario->hashPassword();

                    // Generar un token único para la verificación de cuenta
                    $usuario->crearToken(); // Generar un token único

                    // Enviar el email de confirmación
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    // Crear el usuario en la base de datos
                    $resultado = $usuario->guardar();

                    if ($resultado) {
                        // Obtener el id del usuario recién creado
                        $usuario_id = is_array($resultado) && isset($resultado['id']) ? $resultado['id'] : $usuario->id;
                        // Crear el cliente usando el modelo Cliente
                        $cliente = new Cliente([
                            'usuario_id' => $usuario_id,
                            'telefono' => $usuario->telefono,
                            'direccion' => null // Se puede modificar el campo en el formulario
                        ]);
                        $cliente->guardar();
                        header('Location: /mensaje');
                    }
                }
            }
        }
        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router) {
        $alertas = [];
        $token = trim(s($_GET['token']));
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido o no existe');
        } else {
            $usuario->confirmado = 1;
            $usuario->token = null;
            $usuario->guardar(); // Guardar el usuario confirmado
            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');
        }

        // Obtener las alertas
        $alertas = Usuario::getAlertas();

        // Renderizar la vista de confirmación
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}