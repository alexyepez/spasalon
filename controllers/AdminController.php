<?php

namespace Controllers;

use Model\AdminCita;
use Model\Colaborador;
use Model\Cita;
use Model\Usuario;
use MVC\Router;

class AdminController {
    public static function index( Router $router ) {
        // Alternativa: Verificar si ya hay una sesión activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $fechas = explode('-', $fecha);
        if ( !checkdate( $fechas[1], $fechas[2], $fechas[0] ) ) {
            header('Location: /404');
        }

        // Consultar base de datos
        $consulta = "SELECT citas.id, citas.hora, CONCAT(usuarios.nombre, ' ', usuarios.apellido) AS cliente, ";
        $consulta .= " usuarios.email, usuarios.telefono, servicios.nombre AS nombre_servicio, servicios.precio ";
        $consulta .= " FROM citas ";
        $consulta .= " LEFT JOIN clientes ON citas.cliente_id = clientes.id ";
        $consulta .= " LEFT JOIN usuarios ON clientes.usuario_id = usuarios.id ";
        $consulta .= " LEFT JOIN citas_servicios ON citas_servicios.cita_id = citas.id ";
        $consulta .= " LEFT JOIN servicios ON citas_servicios.servicio_id = servicios.id ";
        $consulta .= " WHERE fecha = '${fecha}' ";

        $citas = AdminCita::SQL($consulta);

        $router->render('admin/index', [
            'nombre' => $_SESSION['nombre'],
            'citas' => $citas,
            'fecha' => $fecha
        ]);
    }

    // Método para mostrar la vista de gestión de terapeutas
    public static function gestionarTerapeutas(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        $terapeutas = Colaborador::all();

        // Consulta SQL correcta para valores NULL
        $query = "SELECT * FROM citas WHERE colaborador_id IS NULL";
        $citasSinTerapeuta = Cita::consultarSQL($query);

        $alertas = [];

        $router->render('admin/gestionar-terapeutas', [
            'nombre' => $_SESSION['nombre'],
            'terapeutas' => $terapeutas,
            'citasSinTerapeuta' => $citasSinTerapeuta,
            'alertas' => $alertas,
            'exito' => isset($_GET['exito']) ? $_GET['exito'] : null
        ]);
    }

    // Método para crear un nuevo terapeuta
    public static function crearTerapeuta(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $usuario->password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $usuario->rol_id = 2; // Rol de terapeuta
            $usuario->confirmado = 1; // Confirmado por el administrador

            $alertas = $usuario->validarNuevaCuenta();

            if (empty($alertas)) {
                $resultado = $usuario->guardar();

                if ($resultado['resultado']) {
                    $colaborador = new Colaborador([
                        'usuario_id' => $resultado['id'],
                        'especialidad' => $_POST['especialidad'] ?? null
                    ]);

                    $colaborador->guardar();
                    header('Location: /admin/gestionar-terapeutas?exito=terapeuta_creado');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al crear el terapeuta';
                }
            }
        }

        $router->render('admin/crear-terapeuta', [
            'nombre' => $_SESSION['nombre'],
            'alertas' => $alertas
        ]);
    }

    // Método para actualizar información de un terapeuta
    public static function actualizarTerapeuta() {
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
                    'mensaje' => 'ID de terapeuta inválido'
                ]);
                return;
            }

            $colaborador = Colaborador::find($id);

            if (!$colaborador) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Terapeuta no encontrado'
                ]);
                return;
            }

            $usuario = Usuario::find($colaborador->usuario_id);

            if (!$usuario) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Usuario del terapeuta no encontrado'
                ]);
                return;
            }

            // Sincronizar datos del usuario
            $datosUsuario = [
                'nombre' => $_POST['nombre'] ?? $usuario->nombre,
                'apellido' => $_POST['apellido'] ?? $usuario->apellido,
                'email' => $_POST['email'] ?? $usuario->email,
                'telefono' => $_POST['telefono'] ?? $usuario->telefono
            ];

            $usuario->sincronizar($datosUsuario);

            // Actualizar especialidad del terapeuta
            $colaborador->especialidad = $_POST['especialidad'] ?? $colaborador->especialidad;

            // Guardar cambios
            $resultadoUsuario = $usuario->guardar();
            $resultadoColaborador = $colaborador->guardar();

            error_log("Resultado guardar usuario: " . print_r($resultadoUsuario, true));
            error_log("Resultado guardar colaborador: " . print_r($resultadoColaborador, true));

            // Verificar resultados
            $exito = false;

            if (is_array($resultadoUsuario) && isset($resultadoUsuario['resultado'])) {
                $exitoUsuario = $resultadoUsuario['resultado'];
            } else {
                $exitoUsuario = $resultadoUsuario ? true : false;
            }

            if (is_array($resultadoColaborador) && isset($resultadoColaborador['resultado'])) {
                $exitoColaborador = $resultadoColaborador['resultado'];
            } else {
                $exitoColaborador = $resultadoColaborador ? true : false;
            }

            $exito = $exitoUsuario && $exitoColaborador;

            echo json_encode([
                'resultado' => $exito,
                'mensaje' => $exito ? 'Terapeuta actualizado correctamente' : 'Error al actualizar terapeuta',
                'debug' => [
                    'usuario' => $resultadoUsuario,
                    'colaborador' => $resultadoColaborador
                ]
            ]);
            return;
        }
    }



    // Método para eliminar un terapeuta
    public static function eliminarTerapeuta() {
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
                    'mensaje' => 'ID de terapeuta inválido'
                ]);
                return;
            }

            $colaborador = Colaborador::find($id);

            if (!$colaborador) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Terapeuta no encontrado'
                ]);
                return;
            }

            $usuario = Usuario::find($colaborador->usuario_id);

            // Primero eliminamos el colaborador
            $resultadoColaborador = $colaborador->eliminar();

            // Si hay usuario, también lo eliminamos
            $resultadoUsuario = true;
            if ($usuario) {
                $resultadoUsuario = $usuario->eliminar();
            }

            $exito = $resultadoColaborador && $resultadoUsuario;

            echo json_encode([
                'resultado' => $exito,
                'mensaje' => $exito ? 'Terapeuta eliminado correctamente' : 'Error al eliminar terapeuta',
                'debug' => [
                    'colaborador' => $resultadoColaborador,
                    'usuario' => $resultadoUsuario
                ]
            ]);
            return;
        }
    }


    // Método para asignar una cita a un terapeuta
    public static function asignarCita() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Agregar encabezados para debug
            header('Content-Type: application/json');

            $citaId = $_POST['cita_id'];
            $terapeutaId = $_POST['terapeuta_id'];

            // Verificar que tengamos los datos necesarios
            if (!$citaId || !$terapeutaId) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'ID de cita o terapeuta no proporcionado'
                ]);
                exit;
            }

            $cita = Cita::find($citaId);

            // Debug: verificar si la cita existe
            if (!$cita) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Cita no encontrada'
                ]);
                exit;
            }

            $terapeuta = Colaborador::find($terapeutaId);

            // Debug: verificar si el terapeuta existe
            if (!$terapeuta) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Terapeuta no encontrado'
                ]);
                exit;
            }

            // Asignar terapeuta a la cita
            $cita->colaborador_id = $terapeutaId;

            // Guardar cambios
            $resultado = $cita->guardar();

            if (!is_array($resultado)) {
                $resultado = ['resultado' => $resultado];
            }

            // Debug: mostrar resultados
            echo json_encode([
                'resultado' => $resultado['resultado'],
                'debug' => $resultado
            ]);
            exit;
        }
    }


}