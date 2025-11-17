<?php

namespace Controllers;

use Model\Recomendacion;
use Model\Cliente;
use Model\HistorialTratamiento; // Cambiado de Tratamiento a HistorialTratamiento
use Model\Cita;
use Model\PreferenciaCliente;

class RecomendacionController {

    public static function obtenerRecomendaciones() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clienteId = filter_var($_POST['cliente_id'], FILTER_VALIDATE_INT);

            if (!$clienteId) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'ID de cliente no válido'
                ]);
                return;
            }

            // Obtener recomendaciones existentes
            $recomendaciones = Recomendacion::getRecomendacionesCliente($clienteId);

            echo json_encode([
                'resultado' => true,
                'recomendaciones' => $recomendaciones
            ]);
        }
    }

    public static function generarRecomendaciones() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clienteId = filter_var($_POST['cliente_id'], FILTER_VALIDATE_INT);

            if (!$clienteId) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'ID de cliente no válido'
                ]);
                return;
            }

            try {
                // 1. Obtener información del cliente
                $cliente = Cliente::find($clienteId);
                if (!$cliente) {
                    echo json_encode([
                        'resultado' => false,
                        'mensaje' => 'Cliente no encontrado'
                    ]);
                    return;
                }

                // 2. Obtener historial de tratamientos
                $tratamientos = HistorialTratamiento::whereAll('cliente_id', $clienteId); // Cambiado a HistorialTratamiento

                // 3. Obtener historial de citas
                $citas = Cita::whereAll('cliente_id', $clienteId);

                // 4. Generar prompt para Claude
                $recomendacionesIA = self::solicitarRecomendacionesIA($cliente, $tratamientos, $citas);

                if (empty($recomendacionesIA)) {
                    echo json_encode([
                        'resultado' => false,
                        'mensaje' => 'No se pudieron generar recomendaciones'
                    ]);
                    return;
                }

                // 5. Guardar recomendaciones en la BD
                $resultado = self::guardarRecomendaciones($recomendacionesIA, $clienteId);

                echo json_encode($resultado);

            } catch (\Exception $e) {
                error_log("Error en generarRecomendaciones: " . $e->getMessage());
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
    }

    private static function solicitarRecomendacionesIA($cliente, $tratamientos, $citas) {
        // Configurar Claude API key
        $apiKey = $_ENV['CLAUDE_API_KEY'] ?? '';

        if (empty($apiKey)) {
            throw new \Exception('API Key de Claude no configurada');
        }

        // Preparar datos para el prompt
        $nombreCliente = $cliente->getUsuario()->nombre . ' ' . $cliente->getUsuario()->apellido;

        // Extraer información de tratamientos
        $historicoTratamientos = [];
        foreach ($tratamientos as $tratamiento) {
            $historicoTratamientos[] = [
                'fecha' => $tratamiento->fecha,
                'notas' => $tratamiento->notas
            ];
        }

        // Extraer información de citas y servicios
        $historicoCitas = [];
        foreach ($citas as $cita) {
            $servicios = [];
            $citaServicios = \Model\CitaServicio::whereAll('cita_id', $cita->id);

            foreach ($citaServicios as $cs) {
                $servicio = \Model\Servicio::find($cs->servicio_id);
                if ($servicio) {
                    $servicios[] = $servicio->nombre;
                }
            }

            $historicoCitas[] = [
                'fecha' => $cita->fecha,
                'hora' => $cita->hora,
                'servicios' => $servicios
            ];
        }

        // Obtener preferencias del cliente
        $preferencias = PreferenciaCliente::getPreferenciasCliente($cliente->id);
        $preferenciasDatos = [];

        foreach ($preferencias as $preferencia) {
            $preferenciasDatos[] = [
                'categoria' => $preferencia->categoria,
                'valor' => $preferencia->valor
            ];
        }

        // Construir el prompt
        $prompt = "Eres un especialista en bienestar que genera recomendaciones personalizadas para clientes de un spa. 
        Basado en el historial del cliente, genera 3-5 recomendaciones específicas.
        
        DATOS DEL CLIENTE:
        Nombre: {$nombreCliente}
        
        HISTORIAL DE TRATAMIENTOS:
        " . json_encode($historicoTratamientos, JSON_PRETTY_PRINT) . "
        
        HISTORIAL DE CITAS Y SERVICIOS:
        " . json_encode($historicoCitas, JSON_PRETTY_PRINT) . "
        
        PREFERENCIAS DEL CLIENTE:
        " . json_encode($preferenciasDatos, JSON_PRETTY_PRINT) . "
        
        Genera un JSON con las recomendaciones, cada una debe incluir:
        - descripcion: Breve descripción de la recomendación
        - justificacion: Por qué es beneficiosa esta recomendación
        - servicio_id: ID del servicio relacionado (null si no aplica)
        - prioridad: Valor de 1 a 5 (5 es la más alta)
        
        FORMATO DE RESPUESTA (exactamente así):
        [
            {
                \"descripcion\": \"...\",
                \"justificacion\": \"...\",
                \"servicio_id\": null,
                \"prioridad\": 4
            }
        ]";

        // Agregar registro para depuración
        error_log("Enviando prompt a Claude API para cliente ID: {$cliente->id}");

        // Llamar a Claude API
        $data = [
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => 1000,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ];

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $apiKey,
            'anthropic-version: 2023-06-01',
            'Content-Type: application/json'
        ]);

        // Agregar esta línea para resolver el problema de certificado
        curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/../../vendor/cacert.pem');

        // Intentar usar el certificado de Composer
        $composerCaPath = __DIR__ . '/../../vendor/composer/cacert/res/cacert.pem';
        if (file_exists($composerCaPath)) {
            curl_setopt($ch, CURLOPT_CAINFO, $composerCaPath);
        } else {
            // Intentar con otra ubicación o usar el predeterminado
            $defaultCaPath = __DIR__ . '/../../cacert.pem';
            if (file_exists($defaultCaPath)) {
                curl_setopt($ch, CURLOPT_CAINFO, $defaultCaPath);
            } else {
                // En desarrollo, puedes optar por deshabilitar la verificación
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                // En producción, es mejor lanzar un error
                throw new \Exception('No se encontró un archivo de certificados CA válido');
            }
        }


        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Guardar errores de cURL
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            error_log("Error de cURL: " . $error);
            curl_close($ch);
            throw new \Exception("Error de conexión con Claude API: " . $error);
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Error de API Claude: $httpCode - $response");
            throw new \Exception("Error al comunicarse con Claude API: $httpCode");
        }

        $responseData = json_decode($response, true);

        // Guardar la respuesta completa para depuración
        error_log("Respuesta de Claude API: " . json_encode($responseData));

        if (!isset($responseData['content'][0]['text'])) {
            throw new \Exception('Formato de respuesta de Claude inesperado');
        }

        // Extraer el JSON de la respuesta
        $responseText = $responseData['content'][0]['text'];

        // Intentar encontrar y extraer el JSON
        if (preg_match('/\[\s*\{.*\}\s*\]/s', $responseText, $matches)) {
            $jsonStr = $matches[0];
            $recomendacionesIA = json_decode($jsonStr, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Error al decodificar JSON de recomendaciones: " . json_last_error_msg() . " - JSON: " . $jsonStr);
                throw new \Exception('Error al decodificar JSON de recomendaciones: ' . json_last_error_msg());
            }

            return $recomendacionesIA;
        } else {
            error_log("No se encontró un formato JSON válido en la respuesta: " . $responseText);
            throw new \Exception('No se pudo extraer el JSON de recomendaciones de la respuesta');
        }
    }

    private static function guardarRecomendaciones($recomendacionesIA, $clienteId) {
        $recomendacionesGuardadas = [];
        $errores = [];

        // Obtener el ID del colaborador actual desde la sesión
        $colaboradorId = $_SESSION['colaborador_id'] ?? null;

        // Si no hay colaborador_id en la sesión, intentar obtenerlo
        if (!$colaboradorId && isset($_SESSION['id'])) {
            $colaborador = \Model\Colaborador::where('usuario_id', $_SESSION['id']);
            if ($colaborador) {
                $colaboradorId = $colaborador->id;
            }
        }

        // Si aún no hay colaborador_id, no podemos continuar
        if (!$colaboradorId) {
            error_log("No se pudo identificar al colaborador en la sesión: " . print_r($_SESSION, true));
            return [
                'resultado' => false,
                'mensaje' => 'No se pudo identificar al colaborador actual'
            ];
        }

        foreach ($recomendacionesIA as $recIA) {
            $recomendacion = new Recomendacion([
                'cliente_id' => $clienteId,
                'colaborador_id' => $colaboradorId,
                'servicio_id' => $recIA['servicio_id'] ?? null,
                'descripcion' => $recIA['descripcion'],
                'justificacion' => $recIA['justificacion'],
                'prioridad' => $recIA['prioridad'],
                'generado_por_ia' => true,
                'estado' => 0, // Pendiente
                'fecha_creacion' => date('Y-m-d H:i:s'),  // Agregar esta línea
                'fecha_actualizacion' => date('Y-m-d H:i:s')  // Y esta también
            ]);

            $resultado = $recomendacion->guardar();

            if ($resultado) {
                $recomendacionesGuardadas[] = $recomendacion;
            } else {
                $errores[] = 'Error al guardar recomendación: ' . $recIA['descripcion'];
            }
        }

        return [
            'resultado' => !empty($recomendacionesGuardadas),
            'recomendaciones' => $recomendacionesGuardadas,
            'errores' => $errores
        ];
    }
}