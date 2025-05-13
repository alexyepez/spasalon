<?php

namespace Controllers;

use Model\Familiar;
use Model\Servicio;
use Model\Cita;
use Model\Colaborador;
use Model\Cliente;
use \Model\CitaServicio;

class APIController {
    public static function index() {
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    /*
    public static function guardar() {
    $respuesta = [
        'datos' => $_POST
    ];
    echo json_encode($respuesta);
    }
    */

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
}

