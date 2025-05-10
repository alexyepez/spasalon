<?php

namespace Controllers;

use Model\Familiar;
use Model\Servicio;

class APIController {
    public static function index() {
        $servicios = Servicio::all();
        //debuguear($servicios);
        echo json_encode($servicios);
    }

    public static function guardar() {
    $respuesta = [
        'datos' => $_POST
    ];
    echo json_encode($respuesta);
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
}

