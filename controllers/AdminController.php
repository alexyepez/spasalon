<?php

namespace Controllers;

use Model\AdminCita;
use Model\Colaborador;
use Model\Cita;
use Model\Usuario;
use MVC\Router;

class AdminController {
    public static function index(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdmin();

        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $fechas = explode('-', $fecha);
        if (!checkdate($fechas[1], $fechas[2], $fechas[0])) {
            header('Location: /404');
        }

        // Paso 1: Obtener todas las citas con sus servicios y clientes
        $consulta = "SELECT citas.id, citas.hora, citas.cliente_id, 
                    CONCAT(usuarios.nombre, ' ', usuarios.apellido) AS cliente, 
                    usuarios.email, usuarios.telefono, 
                    servicios.nombre AS nombre_servicio, servicios.precio 
                FROM citas  
                LEFT JOIN clientes ON citas.cliente_id = clientes.id 
                LEFT JOIN usuarios ON clientes.usuario_id = usuarios.id 
                LEFT JOIN citas_servicios ON citas_servicios.cita_id = citas.id 
                LEFT JOIN servicios ON servicios.id = citas_servicios.servicio_id 
                WHERE citas.fecha = '{$fecha}'";

        $citas = AdminCita::SQL($consulta);

        // Verificar si hay citas
        if (empty($citas)) {
            $router->render('admin/index', [
                'nombre' => $_SESSION['nombre'],
                'citas' => [],
                'fecha' => $fecha,
                'membresiasActivas' => []
            ]);
            return;
        }

        // Paso 2: Obtener todos los IDs de clientes únicos
        $clientesIds = [];
        foreach ($citas as $cita) {
            if (!empty($cita->cliente_id) && !in_array($cita->cliente_id, $clientesIds)) {
                $clientesIds[] = $cita->cliente_id;
            }
        }

        // Mostrar en log los cliente_id encontrados
        //error_log("Clientes IDs encontrados: " . print_r($clientesIds, true));

        // Paso 3: Obtener membresías activas usando la tabla clientes_membresias
        $membresiasActivas = [];
        $hoy = date('Y-m-d');

        if (!empty($clientesIds)) {
            // Crear consulta para obtener membresías activas
            $placeholders = implode(',', array_fill(0, count($clientesIds), '?'));

            $consulta = "SELECT cm.cliente_id, m.nombre, m.descuento 
                    FROM clientes_membresias cm 
                    JOIN membresias m ON cm.membresia_id = m.id 
                    WHERE cm.cliente_id IN ($placeholders) 
                    AND cm.fecha_inicio <= ? 
                    AND cm.fecha_fin >= ?";

            // Preparar parámetros para la consulta
            $params = $clientesIds;
            $params[] = $hoy;
            $params[] = $hoy;

            // Preparar y ejecutar la consulta SQL manualmente usando ActiveRecord
            $db = \Model\ActiveRecord::getDB();
            $stmt = $db->prepare($consulta);

            if ($stmt) {
                // Crear tipos para bind_param
                $types = str_repeat('i', count($clientesIds)) . 'ss';

                // Vincular parámetros
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $resultado = $stmt->get_result();

                while ($fila = $resultado->fetch_assoc()) {
                    $membresiasActivas[$fila['cliente_id']] = (object)[
                        'nombre' => $fila['nombre'],
                        'descuento' => (float)$fila['descuento']
                    ];
                    //error_log("Membresía encontrada para cliente ID {$fila['cliente_id']}: {$fila['nombre']} con descuento {$fila['descuento']}%");
                }

                $stmt->close();
            } else {
                //error_log("Error preparando consulta: " . $db->error);
            }
        }

        // Paso 4: Aplicar descuentos a las citas
        foreach ($citas as $cita) {
            if (!empty($cita->cliente_id) && isset($membresiasActivas[$cita->cliente_id])) {
                $membresiaActiva = $membresiasActivas[$cita->cliente_id];

                // Añadir información de membresía a la cita
                $cita->nombreMembresia = $membresiaActiva->nombre;
                $cita->descuentoPorcentaje = $membresiaActiva->descuento;

                // Calcular precio con descuento
                $precioOriginal = (float)$cita->precio;
                $descuentoAplicado = ($precioOriginal * $cita->descuentoPorcentaje) / 100;
                $cita->precioConDescuento = $precioOriginal - $descuentoAplicado;

                //error_log("Aplicado descuento de {$cita->descuentoPorcentaje}% a cita ID {$cita->id} para cliente ID {$cita->cliente_id}");
            } else {
                // Sin membresía
                $cita->nombreMembresia = '';
                $cita->descuentoPorcentaje = 0;
                $cita->precioConDescuento = (float)$cita->precio;
            }
        }

        $router->render('admin/index', [
            'nombre' => $_SESSION['nombre'],
            'citas' => $citas,
            'fecha' => $fecha,
            'membresiasActivas' => $membresiasActivas
        ]);
    }

    public static function historialCitas(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        isAdmin();

        $consulta = "SELECT citas.id, citas.fecha, citas.hora, ";
        $consulta .= " CONCAT(usuarios.nombre, ' ', usuarios.apellido) as cliente ";
        $consulta .= " FROM citas ";
        $consulta .= " LEFT JOIN clientes ON citas.cliente_id = clientes.id ";
        $consulta .= " LEFT JOIN usuarios ON clientes.usuario_id = usuarios.id ";
        $consulta .= " ORDER BY citas.id ASC "; // MODIFICADO AQUÍ

        $citas = AdminCita::SQL($consulta);

        $router->render('admin/historial-citas', [
            'nombre' => $_SESSION['nombre'],
            'citas' => $citas
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
            //error_log("POST datos recibidos: " . print_r($_POST, true));

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

            //error_log("Resultado guardar usuario: " . print_r($resultadoUsuario, true));
            //error_log("Resultado guardar colaborador: " . print_r($resultadoColaborador, true));

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
            //error_log("POST datos recibidos: " . print_r($_POST, true));

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