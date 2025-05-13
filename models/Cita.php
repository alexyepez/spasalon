<?php

namespace Model;

class Cita extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'citas';
    protected static $columnasDB = ['id', 'cliente_id', 'colaborador_id', 'familiar_id','fecha', 'hora', 'estado'];
    public $id;
    public $cliente_id;
    public $colaborador_id;
    public $familiar_id;
    public $fecha;
    public $hora;
    public $estado;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->cliente_id = $args['cliente_id'] ?? '';
        $this->colaborador_id = $args['colaborador_id'] ?? '';
        $this->familiar_id = $args['familiar_id'] ?? '';
        $this->fecha = $args['fecha'] ?? '';
        $this->hora = $args['hora'] ?? '';
        $this->estado = $args['estado'] ?? 0; // Estado por defecto
    }
}
