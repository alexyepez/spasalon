<?php
namespace Model;

class CitaCancelacion extends ActiveRecord {
    protected static $tabla = 'citas_cancelaciones';
    protected static $columnasDB = ['id', 'cita_id', 'motivo', 'fecha'];

    public $id;
    public $cita_id;
    public $motivo;
    public $fecha;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->cita_id = $args['cita_id'] ?? null;
        $this->motivo = $args['motivo'] ?? '';
        $this->fecha = $args['fecha'] ?? date('Y-m-d');
    }

    public function getCita() {
        return Cita::find($this->cita_id);
    }

}