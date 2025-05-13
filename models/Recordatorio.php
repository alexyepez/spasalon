<?php

// Modelo Recordatorio.php
namespace Model;

class Recordatorio extends ActiveRecord {
    protected static $tabla = 'recordatorios';
    protected static $columnasDB = ['id', 'cliente_id', 'cita_id', 'fecha', 'enviado', 'medio'];
    
    public $id;
    public $cliente_id;
    public $cita_id;
    public $fecha;
    public $enviado;
    public $medio;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->cliente_id = $args['cliente_id'] ?? '';
        $this->cita_id = $args['cita_id'] ?? '';
        $this->fecha = $args['fecha'] ?? '';
        $this->enviado = $args['enviado'] ?? 0;
        $this->medio = $args['medio'] ?? 'email';
    }
}