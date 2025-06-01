<?php

namespace Controllers;

use Model\AdminCita;
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
}