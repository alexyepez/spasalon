<?php
namespace Model;

class Colaborador extends ActiveRecord {
    protected static $tabla = 'colaboradores';
    protected static $columnasDB = ['id', 'usuario_id', 'especialidad'];

    public $id;
    public $usuario_id;
    public $especialidad;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? '';
        $this->especialidad = $args['especialidad'] ?? '';
    }

    public static function findByUsuarioId($usuario_id) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE usuario_id = '" . self::$db->escape_string($usuario_id) . "' LIMIT 1";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    public function getUsuario() {
        return Usuario::find($this->usuario_id);
    }
}