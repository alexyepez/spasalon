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
}
