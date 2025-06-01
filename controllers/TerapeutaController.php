<?php
namespace Controllers;

use MVC\Router;
use Model\Colaborador;
use Model\Cita;
use Model\Cliente;
use Model\Familiar;
use Model\HistorialTratamiento;
use Model\Usuario;

class TerapeutaController {
    public static function index(Router $router) {
        //session_start();

        // Alternativa: Verificar si ya hay una sesión activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        // Verificar autenticación
        if (!esTerapeuta() || isAuth() === false) {
            header('Location: /cita');
            return;
        }

        // Obtener el terapeuta autenticado usando la sesión
        $colaborador = Colaborador::where('usuario_id', $_SESSION['id']);
        if (!$colaborador) {
            Usuario::setAlerta('error', 'No estás registrado como terapeuta');
            $router->render('auth/login', [
                'alertas' => Usuario::getAlertas()
            ]);
            return;
        }

        // Obtener citas asignadas al terapeuta
        $citas = Cita::whereAll('colaborador_id', $colaborador->id);

        // Procesar citas para incluir información completa
        foreach ($citas as &$cita) {
            $cita->cliente = Cliente::find($cita->cliente_id);

            // Si es para un familiar, obtener información del familiar
            if ($cita->familiar_id) {
                $cita->familiar = Familiar::find($cita->familiar_id);
            }
        }
        unset($cita); // Importante para liberar la referencia

        // Inicializar variables
        $alertas = [];
        $historial = new HistorialTratamiento();

        // Procesar formulario para registrar tratamiento
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_tratamiento'])) {
            // Configurar manejo de errores para respuestas JSON
            error_reporting(E_ERROR | E_PARSE); // Solo reportar errores fatales
            ini_set('display_errors', 0); // No mostrar errores

            // Corregir el desfase de fecha (si recibimos 31 pero debería ser 30)
            if (isset($_POST['fecha'])) {
                $fechaOriginal = $_POST['fecha'];

                // Obtener la fecha actual según la zona horaria configurada
                $fechaActual = date('Y-m-d');

                // Reemplazar la fecha recibida con la fecha actual
                $_POST['fecha'] = $fechaActual;

                error_log('Fecha ajustada: Original = ' . $fechaOriginal . ', Ajustada = ' . $_POST['fecha']);
            }

            $historial = new HistorialTratamiento($_POST);
            $historial->colaborador_id = $colaborador->id;

            // Obtener la cita para extraer cliente_id y servicio_id
            $cita_id = $_POST['cita_id'] ?? null;
            if ($cita_id) {
                $cita = Cita::find($cita_id);
                if ($cita) {
                    // Asignar cliente_id desde la cita
                    $historial->cliente_id = $cita->cliente_id;

                    // Obtener el primer servicio de la cita
                    $citaServicios = \Model\CitaServicio::whereAll('cita_id', $cita_id);
                    if (!empty($citaServicios)) {
                        $historial->servicio_id = $citaServicios[0]->servicio_id;
                    } else {
                        // Si no hay servicios, agrega un error
                        $alertas['error'][] = 'No se encontraron servicios asociados a la cita';
                    }
                } else {
                    // Si no se encuentra la cita, agrega un error
                    $alertas['error'][] = 'No se encontró la cita especificada';
                }
            }

            // Asegurarnos de que la fecha es la correcta en el objeto historial
            $historial->fecha = date('Y-m-d');

            // Depurar objeto historial después de completar campos
            error_log('Objeto historial completado: ' . print_r($historial, true));

            // Validar el historial
            if (empty($alertas)) {
                $alertas = $historial->validar();

                // Si no hay errores, guardar el historial
                if (empty($alertas)) {
                    // Guardar el tratamiento y obtener el resultado
                    $resultado = $historial->guardar();

                    error_log('Resultado de guardar: ' . print_r($resultado, true));

                    if ($resultado['resultado']) {
                        // Actualizar el estado de la cita a "Confirmada" (estado = 1)
                        $cita = Cita::find($cita_id);
                        if ($cita) {
                            $cita->estado = 1; // Confirmada
                            $cita->guardar();
                        }

                        // Devolver respuesta JSON en lugar de HTML
                        header('Content-Type: application/json');
                        echo json_encode([
                            'resultado' => true,
                            'mensaje' => 'Tratamiento registrado correctamente'
                        ]);
                        return;
                    } else {
                        // Devolver error en formato JSON
                        header('Content-Type: application/json');
                        echo json_encode([
                            'resultado' => false,
                            'mensaje' => 'Error al registrar el tratamiento'
                        ]);
                        return;
                    }
                }
            }
        }

        // Renderizar la vista
        $router->render('terapeutas/index', [
            'colaborador' => $colaborador,
            'citas' => $citas,
            'historial' => $historial,
            'alertas' => $alertas,
            'exito' => isset($_GET['exito']) && $_GET['exito'] === 'tratamiento_registrado'
        ]);
    }
}
