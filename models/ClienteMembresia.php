<?php
namespace Model;

class ClienteMembresia extends ActiveRecord {
    protected static $tabla = 'clientes_membresias';
    protected static $columnasDB = ['id', 'cliente_id', 'membresia_id', 'fecha_inicio', 'fecha_fin'];

    public $id;
    public $cliente_id;
    public $membresia_id;
    public $fecha_inicio;
    public $fecha_fin;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->cliente_id = $args['cliente_id'] ?? '';
        $this->membresia_id = $args['membresia_id'] ?? '';
        $this->fecha_inicio = $args['fecha_inicio'] ?? date('Y-m-d');
        $this->fecha_fin = $args['fecha_fin'] ?? '';
    }

    public function validar() {
        self::$alertas = [];

        if (!$this->cliente_id) {
            self::$alertas['error'][] = 'El cliente es obligatorio';
        }
        if (!$this->membresia_id) {
            self::$alertas['error'][] = 'La membresía es obligatoria';
        }
        if (!$this->fecha_inicio) {
            self::$alertas['error'][] = 'La fecha de inicio es obligatoria';
        }
        if (!$this->fecha_fin) {
            self::$alertas['error'][] = 'La fecha de fin es obligatoria';
        }

        // Validar que la fecha de fin sea posterior a la de inicio
        if ($this->fecha_inicio && $this->fecha_fin) {
            $inicio = new \DateTime($this->fecha_inicio);
            $fin = new \DateTime($this->fecha_fin);

            if ($inicio > $fin) {
                self::$alertas['error'][] = 'La fecha de fin debe ser posterior a la fecha de inicio';
            }
        }

        return self::$alertas;
    }

    // Verificar si la membresía está activa actualmente
    public function estaActiva() {
        if (!property_exists($this, 'fecha_inicio') || !property_exists($this, 'fecha_fin')) {
            return false;
        }

        try {
            $hoy = new \DateTime();
            $inicio = new \DateTime($this->fecha_inicio);
            $fin = new \DateTime($this->fecha_fin);

            return ($hoy >= $inicio && $hoy <= $fin);
        } catch (\Exception $e) {
            // Si hay algún error con las fechas, devolver false
            return false;
        }
    }

    // Obtener cliente
    public function getCliente() {
        return Cliente::find($this->cliente_id);
    }

    // Obtener membresía
    public function getMembresia() {
        return Membresia::find($this->membresia_id);
    }
}