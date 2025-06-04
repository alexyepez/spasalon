<?php

namespace Model;

class Cliente extends ActiveRecord {
    protected static $tabla = 'clientes';
    protected static $columnasDB = ['id', 'usuario_id', 'telefono', 'direccion'];

    public $id;
    public $usuario_id;
    public $telefono;
    public $direccion;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? null;
        $this->telefono = $args['telefono'] ?? '';
        $this->direccion = $args['direccion'] ?? null;
    }

    // Obtener un usuario
    public function getUsuario() {
        return Usuario::find($this->usuario_id);
    }

    // Validar
    public function validar() {
        if(!$this->usuario_id) {
            self::$alertas['error'][] = 'El ID de Usuario es Obligatorio';
        }
        return self::$alertas;
    }

    // Obtener membresía activa del cliente
    public function getMembresiaActiva() {
        // En lugar de hacer una consulta directa, vamos a aprovechar el método whereAll de ClienteMembresia
        // y luego filtrar para encontrar la membresía activa
        $membresias = ClienteMembresia::whereAll('cliente_id', $this->id);

        if (empty($membresias)) {
            return null;
        }

        $hoy = date('Y-m-d');
        $membresiaActiva = null;

        foreach ($membresias as $membresia) {
            if (
                $membresia->fecha_inicio <= $hoy &&
                $membresia->fecha_fin >= $hoy
            ) {
                // Si encontramos una membresía activa, la guardamos y seguimos buscando
                // para quedarnos con la que tenga la fecha de vencimiento más lejana
                if (
                    !$membresiaActiva ||
                    $membresia->fecha_fin > $membresiaActiva->fecha_fin
                ) {
                    $membresiaActiva = $membresia;
                }
            }
        }

        return $membresiaActiva;
    }
}
