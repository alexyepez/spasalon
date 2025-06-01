<?php

namespace Controllers;

use Model\Familiar;
use Model\Servicio;
use Model\Cita;
use Model\Colaborador;
use Model\Cliente;
use \Model\CitaServicio;
use \Model\CitaCancelacion;
use \Model\HistorialTratamiento;

class APIController {
    public static function index() {
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    // Obtener familiares de un cliente
    public static function familiares() {
        $cliente_id = $_GET['cliente_id'] ?? null;
        if (!$cliente_id) {
            echo json_encode(['error' => 'Cliente ID no proporcionado']);
            return;
        }
        $familiares = Familiar::whereAll('cliente_id', $cliente_id);
        echo json_encode($familiares);
    }

    // Crear un nuevo familiar
    public static function crearFamiliar() {

        $cliente_id = $_POST['cliente_id'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $parentesco = $_POST['parentesco'];

        // Verifica si ya existe
        $existe = Familiar::whereAllMultiple([
            'cliente_id' => $cliente_id,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'parentesco' => $parentesco
        ]);

        if ($existe) {
            echo json_encode(['resultado' => false, 'mensaje' => 'El familiar ya existe']);
            return;
        }

        $familiar = new Familiar($_POST);
        $resultado = $familiar->guardar();
        echo json_encode($resultado);
        //echo json_encode(['resultado' => $resultado]);
        
    }

    // Eliminar un familiar
    public static function eliminarFamiliar() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $familiar = Familiar::find($id);
            if ($familiar) {
                $resultado = $familiar->eliminar();
                echo json_encode(['resultado' => $resultado]);
                return;
            }
        }
        echo json_encode(['resultado' => false]);
    }

    // Actualizar un familiar
    public static function actualizarFamiliar() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $familiar = Familiar::find($id);
            if ($familiar) {
                $familiar->sincronizar($_POST);
                $resultado = $familiar->guardar();
                echo json_encode(['resultado' => $resultado]);
            } else {
                echo json_encode(['resultado' => false]);
            }
        } else {
            echo json_encode(['resultado' => false, 'error' => 'ID no proporcionado']);
        }
    }

    // Función para guardar una cita
    public static function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['resultado' => false, 'mensaje' => 'Método no permitido']);
            return;
        }

        // Obtener y validar datos básicos
        $datos = json_decode(file_get_contents('php://input'), true);
        $servicios = $datos['servicios'] ?? [];
        $cliente_id = $datos['clienteId'] ?? null;
        $familiar_id = isset($datos['familiarId']) && $datos['familiarId'] !== '' ? (int)$datos['familiarId'] : null;
        $fecha = $datos['fecha'] ?? '';
        $hora = $datos['hora'] ?? '';

        // Validaciones básicas
        if (empty($servicios)) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Debes seleccionar al menos un servicio']);
            return;
        }

        if (!$fecha || !$hora) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Fecha y hora son requeridas']);
            return;
        }

        // Verificar cliente
        $cliente = Cliente::find($cliente_id);
        if (!$cliente) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Cliente no encontrado']);
            return;
        }

        // Verificar familiar si aplica
        if ($familiar_id) {
            $familiar = Familiar::find($familiar_id);
            if (!$familiar || $familiar->cliente_id != $cliente_id) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Familiar no válido o no pertenece al cliente']);
                return;
            }
        }

        // Asignar colaborador basado en el primer servicio
        $servicioPrincipal = Servicio::find($servicios[0]);
        $colaborador_id = null;
        
        if ($servicioPrincipal) {
            $nombreServicio = strtolower($servicioPrincipal->nombre);
            
            // Definir mapeo de servicios a especialidades
            $servicioEspecialidad = [
                'masaje' => 'Masajista',
                'facial' => 'Esteticista',
                'antiedad' => 'Esteticista',
                'manicura' => 'Manicurista',
                'pedicura' => 'Pedicurista',
                'depilación' => 'Esteticista'
            ];
            
            // Servicios que pueden usar terapeuta genérico
            $serviciosGenericos = ['exfoliación', 'aromaterapia', 'piedras'];

            // Buscar especialista específico
            foreach ($servicioEspecialidad as $palabraClave => $especialidad) {
                if (strpos($nombreServicio, $palabraClave) !== false) {
                    $colaboradores = Colaborador::whereAll('especialidad', $especialidad);
                    if (!empty($colaboradores)) {
                        $colaborador_id = $colaboradores[0]->id;
                        break;
                    }
                }
            }
            
            // Si no se encontró, verificar servicios genéricos
            if (!$colaborador_id) {
                foreach ($serviciosGenericos as $palabraClave) {
                    if (strpos($nombreServicio, $palabraClave) !== false) {
                        // Buscar terapeutas genéricos
                        $colaboradores = Colaborador::whereAll('especialidad', 'Genérico');
                        if (empty($colaboradores)) {
                            $colaboradores = Colaborador::all();
                        }
                        
                        if (!empty($colaboradores)) {
                            $colaborador_id = $colaboradores[0]->id;
                        }
                        break;
                    }
                }
            }
        }

        // Si aún no hay terapeuta, asignar cualquiera disponible
        if (!$colaborador_id) {
            $colaboradores = Colaborador::all();
            if (!empty($colaboradores)) {
                $colaborador_id = $colaboradores[0]->id;
            } else {
                echo json_encode(['resultado' => false, 'mensaje' => 'No hay terapeutas disponibles']);
                return;
            }
        }

        // Crear la cita
        $cita = new Cita([
            'cliente_id' => $cliente_id,
            'colaborador_id' => $colaborador_id,
            'familiar_id' => $familiar_id,
            'fecha' => $fecha,
            'hora' => $hora,
            'estado' => 0 // Pendiente
        ]);

        $resultado = $cita->guardar();

        if (!$resultado['resultado']) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Error al guardar la cita']);
            return;
        }

        // Asociar servicios a la cita
        $cita_id = $resultado['id'];
        foreach ($servicios as $servicio_id) {
            $citaServicio = new CitaServicio([
                'cita_id' => $cita_id,
                'servicio_id' => $servicio_id
            ]);
            $citaServicio->guardar();
        }

        echo json_encode(['resultado' => true]);
    }

    public static function cambiarEstadoCita() {
        // Establecer el tipo de contenido como JSON
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['resultado' => false, 'mensaje' => 'Método no permitido']);
                return;
            }

            $cita_id = $_POST['cita_id'] ?? null;
            $accion = $_POST['accion'] ?? '';

            if (!$cita_id) {
                echo json_encode(['resultado' => false, 'mensaje' => 'ID de cita no proporcionado']);
                return;
            }

            // Buscar la cita
            $cita = Cita::find($cita_id);
            if (!$cita) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Cita no encontrada']);
                return;
            }

            //error_log("Cita encontrada: " . print_r($cita, true));


            // Realizar la acción según lo solicitado
            switch ($accion) {
                case 'confirmar':
                    $cita->estado = 1; // Confirmada
                    break;
                case 'cancelar':
                    $cita->estado = 2; // Cancelada
                    $motivo = $_POST['motivo'] ?? 'No especificado';

                    // Guardar el motivo de cancelación
                    $cancelacion = new CitaCancelacion([
                        'cita_id' => $cita_id,
                        'motivo' => $motivo,
                        'fecha' => date('Y-m-d')
                    ]);

                    // No necesitamos validar aquí si los datos ya están presentes
                    $resultadoCancelacion = $cancelacion->guardar();
                    //error_log("Resultado de guardar cancelación: " . print_r($resultadoCancelacion, true));

                    break;
                default:
                    echo json_encode(['resultado' => false, 'mensaje' => 'Acción no válida']);
                    return;
            }

            // Guardar los cambios en la cita
            $resultado = $cita->guardar();
            //error_log("Resultado de guardar cita: " . print_r($resultado, true));


            if ($resultado) {
                echo json_encode(['resultado' => true, 'mensaje' => 'Estado de cita actualizado correctamente']);
            } else {
                echo json_encode(['resultado' => false, 'mensaje' => 'Error al actualizar el estado de la cita']);
            }

        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver un mensaje de error JSON
            //error_log("Excepción en cambiarEstadoCita: " . $e->getMessage());
            echo json_encode(['resultado' => false, 'mensaje' => 'Error en el servidor: ' . $e->getMessage()]);
        }
        exit; // Asegurarse de que no se envíe ningún otro contenido
    }

    // Obtener tratamientos de un terapeuta
    public static function tratamientos() {
        // Establecer el tipo de contenido como JSON
        header('Content-Type: application/json');

        try {
            $terapeutaId = $_GET['terapeutaId'] ?? null;

            if (!$terapeutaId) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Terapeuta ID no proporcionado']);
                return;
            }

            // Buscar tratamientos del terapeuta
            $tratamientos = HistorialTratamiento::whereAll('colaborador_id', $terapeutaId);

            // Buscar las citas relacionadas con estos tratamientos
            foreach ($tratamientos as $tratamiento) {
                // Buscar citas que coincidan con este tratamiento (por cliente_id, servicio_id y fecha)
                $citas = Cita::whereAllMultiple([
                    'cliente_id' => $tratamiento->cliente_id,
                    'colaborador_id' => $tratamiento->colaborador_id
                ]);

                // Si encontramos citas relacionadas, usar la primera
                if (!empty($citas)) {
                    // Buscar la cita más cercana a la fecha del tratamiento
                    $citaMasCercana = null;
                    $diferenciaMinima = PHP_INT_MAX;

                    foreach ($citas as $cita) {
                        $fechaTratamiento = strtotime($tratamiento->fecha);
                        $fechaCita = strtotime($cita->fecha);
                        $diferencia = abs($fechaTratamiento - $fechaCita);

                        if ($diferencia < $diferenciaMinima) {
                            $diferenciaMinima = $diferencia;
                            $citaMasCercana = $cita;
                        }
                    }

                    if ($citaMasCercana) {
                        $tratamiento->cita_id = $citaMasCercana->id;
                    }
                } else {
                    // Si no hay cita relacionada, usar un valor genérico
                    $tratamiento->cita_id = null;
                }
            }

            // Ordenar por fecha descendente (más recientes primero)
            usort($tratamientos, function($a, $b) {
                return strtotime($b->fecha) - strtotime($a->fecha);
            });

            echo json_encode($tratamientos);
        } catch (\Exception $e) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Error en el servidor: ' . $e->getMessage()]);
        }
        exit;
    }

    public static function eliminar() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $cita = Cita::find($id);
            $cita->eliminar();
            header('Location:' . $_SERVER['HTTP_REFERER']);
        }
    }
}

