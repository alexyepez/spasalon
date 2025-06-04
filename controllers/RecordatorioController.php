<?php
namespace Controllers;

use Model\Recordatorio;
use Model\Cita;
use Model\Cliente;
use Model\Usuario;
use Classes\Email;
use MVC\Router;

class RecordatorioController {

    public static function index(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        $recordatorios = Recordatorio::all();
        $alertas = [];

        $router->render('admin/recordatorios/index', [
            'nombre' => $_SESSION['nombre'] ?? '',
            'recordatorios' => $recordatorios,
            'alertas' => $alertas
        ]);
    }

    public static function crear(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        $recordatorio = new Recordatorio();

        // Obtener SOLO clientes con sus datos de usuario
        $clientesConUsuarios = [];
        $clientes = Cliente::all();

        foreach ($clientes as $cliente) {
            $usuario = Usuario::find($cliente->usuario_id);
            if ($usuario && $usuario->rol_id === "3" && !isset($usuario->terapeuta_id)) {
                // Añadir propiedades de usuario al cliente para uso en la vista
                $cliente->nombre = $usuario->nombre;
                $cliente->apellido = $usuario->apellido;
                $cliente->email = $usuario->email;
                $clientesConUsuarios[] = $cliente;
            }
        }

        // Obtener citas y enriquecerlas con información del cliente
        $citasConDetalles = [];
        $citas = Cita::all();

        foreach ($citas as $cita) {
            $cliente = Cliente::find($cita->cliente_id);
            if ($cliente) {
                $usuario = Usuario::find($cliente->usuario_id);
                if ($usuario) {
                    $cita->nombreCliente = $usuario->nombre . ' ' . $usuario->apellido;
                    $citasConDetalles[] = $cita;
                }
            }
        }

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $recordatorio->sincronizar($_POST);
            $alertas = $recordatorio->validar();

            if (empty($alertas)) {
                $resultado = $recordatorio->guardar();
                if ($resultado) {
                    header('Location: /admin/recordatorios?exito=creado');
                    exit;
                } else {
                    Recordatorio::setAlerta('error', 'Error al crear el recordatorio');
                }
            }
        }

        $alertas = Recordatorio::getAlertas();
        $router->render('admin/recordatorios/crear', [
            'nombre' => $_SESSION['nombre'] ?? '',
            'recordatorio' => $recordatorio,
            'clientes' => $clientesConUsuarios,
            'citas' => $citasConDetalles,
            'alertas' => $alertas
        ]);
    }

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
                    'mensaje' => 'ID de recordatorio inválido'
                ]);
                return;
            }

            $recordatorio = Recordatorio::find($id);

            if (!$recordatorio) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Recordatorio no encontrado'
                ]);
                return;
            }

            $resultado = $recordatorio->eliminar();

            echo json_encode([
                'resultado' => $resultado ? true : false,
                'mensaje' => $resultado ? 'Recordatorio eliminado correctamente' : 'Error al eliminar recordatorio'
            ]);

            return;
        }
    }

    public static function enviarRecordatorios() {
        // Obtener recordatorios para enviar hoy
        $hoy = date('Y-m-d');
        $recordatorios = Recordatorio::whereAll('fecha', $hoy, 'enviado', 0);
        $contador = 0;

        foreach ($recordatorios as $recordatorio) {
            $cita = $recordatorio->getCita();
            $cliente = $recordatorio->getCliente();

            if ($cita && $cliente) {
                $usuario = Usuario::find($cliente->usuario_id);

                if ($usuario) {$email = new Email(
                    $usuario->email,
                    $usuario->nombre,
                    $cita->id,
                    $cita->fecha, // Fecha de la cita
                    $cita->hora   // Hora de la cita
                    );


                    if ($recordatorio->medio === 'email') {
                        $enviado = $email->enviarRecordatorio();
                    } else {
                        // Implementar otros medios como SMS en el futuro
                        $enviado = false;
                    }

                    if ($enviado) {
                        $recordatorio->enviado = 1;
                        $recordatorio->guardar();
                        $contador++;
                    }
                }
            }
        }

        return $contador; // Devuelve el número de recordatorios enviados
    }

    public static function ejecutarEnvio(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin(); // Solo administradores pueden ejecutar esto manualmente

        $enviados = self::enviarRecordatorios();

        $alertas = [];
        $alertas['exito'][] = "Se han enviado $enviados recordatorios.";

        $recordatorios = Recordatorio::all();

        $router->render('admin/recordatorios/index', [
            'nombre' => $_SESSION['nombre'] ?? '',
            'recordatorios' => $recordatorios,
            'alertas' => $alertas
        ]);
    }

    public static function enviarRecordatorioIndividual() {
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
                    'mensaje' => 'ID de recordatorio inválido'
                ]);
                return;
            }

            $recordatorio = Recordatorio::find($id);

            if (!$recordatorio) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Recordatorio no encontrado'
                ]);
                return;
            }

            $cita = $recordatorio->getCita();
            if (!$cita) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'La cita asociada no existe'
                ]);
                return;
            }

            $cliente = $recordatorio->getCliente();
            if (!$cliente) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'El cliente asociado no existe'
                ]);
                return;
            }

            $usuario = Usuario::find($cliente->usuario_id);
            if (!$usuario) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'El usuario asociado al cliente no existe'
                ]);
                return;
            }

            // Verificar si el usuario es realmente un cliente
            if ($usuario->rol_id === "1" || isset($usuario->terapeuta_id)) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'El usuario no es un cliente válido para enviar recordatorios'
                ]);
                return;
            }

            $enviado = false;
            // Pasar fecha y hora de la cita
            $email = new Email(
                $usuario->email,
                $usuario->nombre,
                $cita->id,
                $cita->fecha, // Pasar la fecha
                $cita->hora   // Pasar la hora
            );


            if ($recordatorio->medio === 'email') {
                try {
                    $enviado = $email->enviarRecordatorio();
                } catch (\Exception $e) {
                    echo json_encode([
                        'resultado' => false,
                        'mensaje' => 'Error al enviar el email: ' . $e->getMessage()
                    ]);
                    return;
                }
            }

            if ($enviado) {
                $recordatorio->enviado = 1;
                $recordatorio->guardar();

                echo json_encode([
                    'resultado' => true,
                    'mensaje' => 'Recordatorio enviado correctamente'
                ]);
            } else {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Error al enviar el recordatorio. Verifica la configuración de correo.'
                ]);
            }

            return;
        }
    }
}