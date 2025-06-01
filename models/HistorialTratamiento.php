<?php
namespace Model;

class HistorialTratamiento extends ActiveRecord {
    protected static $tabla = 'historiales_tratamientos';
    protected static $columnasDB = ['id', 'cliente_id', 'colaborador_id', 'servicio_id', 'fecha', 'notas'];

    public $id;
    public $cliente_id;
    public $colaborador_id;
    public $servicio_id;
    public $fecha;
    public $notas;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->cliente_id = $args['cliente_id'] ?? '';
        $this->colaborador_id = $args['colaborador_id'] ?? '';
        $this->servicio_id = $args['servicio_id'] ?? '';
        $this->fecha = $args['fecha'] ?? '';
        $this->notas = $args['notas'] ?? '';
    }

    public function validar() {
        if (!$this->cliente_id) {
            self::$alertas['error'][] = 'El cliente es obligatorio';
        }
        if (!$this->colaborador_id) {
            self::$alertas['error'][] = 'El terapeuta es obligatorio';
        }
        if (!$this->servicio_id) {
            self::$alertas['error'][] = 'El servicio es obligatorio';
        }
        if (!$this->fecha) {
            self::$alertas['error'][] = 'La fecha es obligatoria';
        }
        return self::$alertas;
    }

    // Obtener la cita asociada
    public function getCita() {
        return Cita::find($this->cliente_id);
    }

    // Obtener el colaborador/terapeuta asociado
    public function getColaborador() {
        return Colaborador::find($this->colaborador_id);
    }

}