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

        // Cambiado para usar all() ya que orderBy() no está disponible
        $recordatorios = Recordatorio::all();

        // Inicializar alertas
        $alertas = [];

        // Capturar mensaje de éxito si viene de una redirección (ej. después de crear)
        if(isset($_GET['exito'])) {
            if($_GET['exito'] === 'creado') {
                Recordatorio::setAlerta('exito', 'Recordatorio creado correctamente.');
            }
        }

        // Capturar mensaje después de enviar recordatorios
        if(isset($_GET['enviados'])) {
            $enviados = (int)$_GET['enviados'];
            $hayPendientes = isset($_GET['hayPendientes']) ? $_GET['hayPendientes'] === 'true' : false;
            $hayError = isset($_GET['error']) ? $_GET['error'] === 'true' : false;

            if(!$hayPendientes) {
                Recordatorio::setAlerta('info', 'No hay recordatorios pendientes para enviar hoy.');
            } else if($enviados > 0) {
                Recordatorio::setAlerta('exito', "Se han enviado $enviados recordatorios pendientes exitosamente.");
            } else if($hayError) {
                Recordatorio::setAlerta('error', 'No se pudieron enviar los recordatorios pendientes. Revisa los logs para más detalles.');
            } else {
                Recordatorio::setAlerta('info', 'No se encontraron recordatorios para enviar hoy.');
            }
        }

        $alertas = Recordatorio::getAlertas();

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
        $todosClientes = Cliente::all(); // Obtener todos los registros de la tabla clientes

        foreach ($todosClientes as $cliente) {
            $usuario = Usuario::find($cliente->usuario_id);
            // Asegurarse que el usuario exista y sea un cliente (rol_id 3 y no terapeuta)
            if ($usuario && isset($usuario->rol_id) && $usuario->rol_id == 3 && !isset($usuario->terapeuta_id)) {
                $cliente->nombre = $usuario->nombre; // Asignar nombre del usuario al objeto cliente
                $cliente->apellido = $usuario->apellido; // Asignar apellido del usuario al objeto cliente
                $cliente->email = $usuario->email; // Asignar email del usuario al objeto cliente
                $clientesConUsuarios[] = $cliente; // Añadir a la lista
            }
        }


        // Obtener citas y enriquecerlas con información del cliente
        $citasConDetalles = [];
        // También modificado para usar all() en lugar de orderBy()
        $citas = Cita::all();

        foreach ($citas as $cita) {
            $clienteDeCita = Cliente::find($cita->cliente_id);
            if ($clienteDeCita) {
                $usuarioDeCita = Usuario::find($clienteDeCita->usuario_id);
                if ($usuarioDeCita) {
                    $cita->nombreCliente = $usuarioDeCita->nombre . ' ' . $usuarioDeCita->apellido;
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
                    Recordatorio::setAlerta('error', 'Error al crear el recordatorio. Inténtalo de nuevo.');
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

            $datos = json_decode(file_get_contents("php://input"), true);
            $id = filter_var($datos['id'] ?? '', FILTER_VALIDATE_INT);

            if (!$id) {
                echo json_encode(['resultado' => false, 'mensaje' => 'ID de recordatorio inválido']);
                return;
            }

            $recordatorio = Recordatorio::find($id);

            if (!$recordatorio) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Recordatorio no encontrado']);
                return;
            }

            $resultado = $recordatorio->eliminar();

            echo json_encode([
                'resultado' => $resultado ? true : false,
                'mensaje' => $resultado ? 'Recordatorio eliminado correctamente' : 'Error al eliminar recordatorio'
            ]);
            return;
        }
        // Si no es POST, podría retornar un error o redirigir, pero para API es mejor solo responder a POST.
        header('HTTP/1.1 405 Method Not Allowed');
        echo json_encode(['resultado' => false, 'mensaje' => 'Método no permitido']);
    }

    public static function enviarRecordatorios() {
        $hoy = date('Y-m-d');

        // Primero obtener todos los recordatorios para hoy
        $recordatoriosHoy = Recordatorio::whereAll('fecha', $hoy);

        // Luego filtrar manualmente para obtener solo los pendientes (enviado = 0)
        $recordatorios = [];
        foreach ($recordatoriosHoy as $recordatorio) {
            if ($recordatorio->enviado == 0) {
                $recordatorios[] = $recordatorio;
            }
        }

        $contador = 0;
        $erroresAlEnviar = [];

        foreach ($recordatorios as $recordatorio) {
            $cita = $recordatorio->getCita();
            $cliente = $recordatorio->getCliente(); // getCliente ya carga el usuario

            if ($cita && $cliente && isset($cliente->email)) { // Asegurarse que el cliente y su email existen
                $email = new Email(
                    $cliente->email, // Usar el email del cliente obtenido
                    $cliente->nombre, // Usar el nombre del cliente obtenido
                    $cita->id,
                    $cita->fecha,
                    $cita->hora
                );

                $enviado = false;
                if ($recordatorio->medio === 'email') {
                    try {
                        $enviado = $email->enviarRecordatorio();
                    } catch (\Exception $e) {
                        // Registrar el error específico para este recordatorio
                        //error_log("Error al enviar email para recordatorio ID {$recordatorio->id}: " . $e->getMessage());
                        $erroresAlEnviar[] = "ID {$recordatorio->id}: " . $e->getMessage();
                    }
                } else {
                    // Lógica para otros medios si se implementa
                    $erroresAlEnviar[] = "ID {$recordatorio->id}: Medio de envío '{$recordatorio->medio}' no soportado.";
                }

                if ($enviado) {
                    $recordatorio->enviado = 1;
                    if ($recordatorio->guardar()) {
                        $contador++;
                    } else {
                        //error_log("Error al actualizar estado del recordatorio ID {$recordatorio->id} después de enviar.");
                        $erroresAlEnviar[] = "ID {$recordatorio->id}: No se pudo actualizar el estado a enviado.";
                    }
                }
            } else {
                $msgError = "Recordatorio ID {$recordatorio->id} no se pudo procesar: ";
                if(!$cita) $msgError .= "Cita no encontrada. ";
                if(!$cliente) $msgError .= "Cliente no encontrado. ";
                if($cliente && !isset($cliente->email)) $msgError .= "Email del cliente no disponible.";
                //error_log($msgError);
                $erroresAlEnviar[] = $msgError;
            }
        }

        // Devolver un array con el contador y los errores
        return ['enviados' => $contador, 'errores' => $erroresAlEnviar];
    }

    // Modificado para ser un endpoint API que responde JSON
    // Modificado para gestionar tanto peticiones GET como POST correctamente

    public static function ejecutarEnvio() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        isAdmin();

        // Obtener la fecha actual
        $hoy = date('Y-m-d');

        // Primero obtener todos los recordatorios para hoy
        $recordatoriosHoy = Recordatorio::whereAll('fecha', $hoy);

        // Luego filtrar manualmente para obtener solo los pendientes (enviado = 0)
        $pendientes = [];
        foreach ($recordatoriosHoy as $recordatorio) {
            if ($recordatorio->enviado == 0) {
                $pendientes[] = $recordatorio;
            }
        }

        $hayPendientes = count($pendientes) > 0;

        // Si no hay pendientes, no es necesario ejecutar el proceso de envío
        if (!$hayPendientes) {
            // Para peticiones AJAX (POST) responder con JSON
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode([
                    'resultado' => true,
                    'mensaje' => 'No hay recordatorios pendientes para enviar hoy.',
                    'hayPendientes' => false
                ]);
                return;
            }
            // Para accesos directos por URL (GET) redirigir a la página principal
            else {
                header('Location: /admin/recordatorios?enviados=0&hayPendientes=false');
                exit;
            }
        }

        // Si hay pendientes, entonces sí procesar los recordatorios
        $resultadoEnvio = self::enviarRecordatorios();
        $enviados = $resultadoEnvio['enviados'];
        $errores = $resultadoEnvio['errores'];

        // Para peticiones AJAX (POST) responder con JSON
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            if ($enviados > 0 && empty($errores)) {
                echo json_encode([
                    'resultado' => true,
                    'mensaje' => "Se han enviado $enviados recordatorios pendientes exitosamente.",
                    'hayPendientes' => true
                ]);
            } elseif ($enviados > 0 && !empty($errores)) {
                echo json_encode([
                    'resultado' => true,
                    'mensaje' => "Se enviaron $enviados recordatorios. Ocurrieron errores con otros: " . implode("; ", $errores),
                    'hayPendientes' => true
                ]);
            } else { // $enviados es 0 y hay errores
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'No se pudieron enviar recordatorios. Errores: ' . implode("; ", $errores),
                    'hayPendientes' => true
                ]);
            }
            return;
        }
        // Para accesos directos por URL (GET) redirigir a la página principal
        else {
            // Establecer mensaje según el resultado
            if ($enviados > 0) {
                header('Location: /admin/recordatorios?enviados=' . $enviados . '&hayPendientes=true');
            } else {
                header('Location: /admin/recordatorios?enviados=0&hayPendientes=true&error=true');
            }
            exit;
        }
    }


    public static function enviarRecordatorioIndividual() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $datos = json_decode(file_get_contents("php://input"), true);
            $id = filter_var($datos['id'] ?? '', FILTER_VALIDATE_INT);

            if (!$id) {
                echo json_encode(['resultado' => false, 'mensaje' => 'ID de recordatorio inválido']);
                return;
            }

            $recordatorio = Recordatorio::find($id);

            if (!$recordatorio) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Recordatorio no encontrado']);
                return;
            }

            // Verificar si ya fue enviado
            if ($recordatorio->enviado == 1) {
                echo json_encode(['resultado' => true, 'mensaje' => 'Este recordatorio ya fue enviado anteriormente.']);
                return;
            }

            $cita = $recordatorio->getCita();
            if (!$cita) {
                echo json_encode(['resultado' => false, 'mensaje' => 'La cita asociada no existe o fue eliminada.']);
                return;
            }

            $cliente = $recordatorio->getCliente(); // getCliente ya debería cargar datos del usuario si está bien implementado
            if (!$cliente || !isset($cliente->email) || !isset($cliente->nombre)) {
                echo json_encode(['resultado' => false, 'mensaje' => 'El cliente asociado o sus datos (email, nombre) no existen.']);
                return;
            }

            $enviado = false;
            $email = new Email(
                $cliente->email,
                $cliente->nombre,
                $cita->id,
                $cita->fecha,
                $cita->hora
            );

            if ($recordatorio->medio === 'email') {
                try {
                    $enviado = $email->enviarRecordatorio();
                } catch (\Exception $e) {
                    //error_log("Error al enviar email individual para ID {$recordatorio->id}: " . $e->getMessage());
                    echo json_encode(['resultado' => false, 'mensaje' => 'Error al enviar el email: ' . $e->getMessage()]);
                    return;
                }
            } else {
                echo json_encode(['resultado' => false, 'mensaje' => "Medio de envío '{$recordatorio->medio}' no soportado para envío individual."]);
                return;
            }

            if ($enviado) {
                $recordatorio->enviado = 1;
                if($recordatorio->guardar()){
                    echo json_encode(['resultado' => true, 'mensaje' => 'Recordatorio enviado correctamente']);
                } else {
                    //error_log("Error al actualizar estado del recordatorio ID {$recordatorio->id} después de envío individual.");
                    echo json_encode(['resultado' => false, 'mensaje' => 'Recordatorio enviado, pero error al actualizar su estado.']);
                }
            } else {
                echo json_encode(['resultado' => false, 'mensaje' => 'Error al enviar el recordatorio. Verifica la configuración de correo o los logs.']);
            }
            return;
        }
        http_response_code(405);
        echo json_encode(['resultado' => false, 'mensaje' => 'Método no permitido.']);
    }
}