<?php
namespace Controllers;

use MVC\Router;

class TerapeutaController {
    public static function dashboard(Router $router) {
        // Aquí puedes obtener datos dinámicos, por ejemplo:
        // $terapeuta = ...;
        // $citas = ...;
        // Por ahora, solo pasamos datos vacíos para la vista de ejemplo
        $router->render('terapeutas/dashboard', [
            'terapeuta' => null,
            'citas' => []
        ]);
    }
}
