<?php

namespace Controllers;

use MVC\Router;
use Model\Colaborador;
use Model\Cita;
use Model\HistorialTratamiento;
use Model\Usuario;


class TerapeutaController {
    public static function index(Router $router) {
        session_start();

        // Verificar autenticación
        if (!esTerapeuta()) {
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

        // Procesar formulario para registrar tratamiento
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_tratamiento'])) {
            $historial = new HistorialTratamiento($_POST);
            $historial->colaborador_id = $colaborador->id;
            $alertas = $historial->validar();

            if (empty($alertas)) {
                $resultado = $historial->guardar();
                if ($resultado['resultado']) {
                    // Actualizar estado de la cita
                    if (isset($_POST['cita_id'])) {
                        $cita = Cita::find($_POST['cita_id']);
                        if ($cita) {
                            $cita->estado = 1; // Cambiar a confirmada
                            $cita->guardar();
                        }
                    }
                    header('Location: /terapeuta/index?exito=tratamiento_registrado');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al registrar el tratamiento';
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