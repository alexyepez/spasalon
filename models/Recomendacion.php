<?php

namespace Model;

class Recomendacion extends ActiveRecord {
    protected static $tabla = 'recomendaciones';
    protected static $columnasDB = ['id', 'cliente_id', 'colaborador_id', 'servicio_id', 'descripcion', 'justificacion',
        'prioridad', 'generado_por_ia', 'estado', 'fecha_creacion', 'fecha_actualizacion'];

    public $id;
    public $cliente_id;
    public $colaborador_id;
    public $servicio_id;
    public $descripcion;
    public $justificacion;
    public $prioridad;
    public $generado_por_ia;
    public $estado;
    public $fecha_creacion;
    public $fecha_actualizacion;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->cliente_id = $args['cliente_id'] ?? '';
        $this->colaborador_id = $args['colaborador_id'] ?? '';
        $this->servicio_id = $args['servicio_id'] ?? null;
        $this->descripcion = $args['descripcion'] ?? '';
        $this->justificacion = $args['justificacion'] ?? '';
        $this->prioridad = $args['prioridad'] ?? 0;
        $this->generado_por_ia = $args['generado_por_ia'] ?? true;
        $this->estado = $args['estado'] ?? 0;
        $this->fecha_creacion = $args['fecha_creacion'] ?? '';
        $this->fecha_actualizacion = $args['fecha_actualizacion'] ?? '';
    }

    // Obtener recomendaciones activas para un cliente
    // Obtener recomendaciones activas para un cliente
    public static function getRecomendacionesCliente($clienteId) {
        $query = "SELECT r.*, s.nombre as servicio_nombre, s.precio,
             c.especialidad as colaborador_especialidad,
             u.nombre as colaborador_nombre, u.apellido as colaborador_apellido
             FROM " . static::$tabla . " r
             LEFT JOIN servicios s ON r.servicio_id = s.id
             LEFT JOIN colaboradores c ON r.colaborador_id = c.id
             LEFT JOIN usuarios u ON c.usuario_id = u.id
             WHERE r.cliente_id = ${clienteId}
             ORDER BY r.prioridad DESC, r.fecha_creacion DESC";

        return static::consultarSQL($query);
    }

    // Validar recomendación
    public function validar() {
        if (!$this->cliente_id) {
            self::$alertas['error'][] = 'El cliente es obligatorio';
        }
        if (!$this->colaborador_id) {
            self::$alertas['error'][] = 'El colaborador es obligatorio';
        }
        if (!$this->descripcion) {
            self::$alertas['error'][] = 'La descripción es obligatoria';
        }
        if (!$this->justificacion) {
            self::$alertas['error'][] = 'La justificación es obligatoria';
        }
        return self::$alertas;
    }

    // Obtener el colaborador asociado
    public function getColaborador() {
        return Colaborador::find($this->colaborador_id);
    }
}