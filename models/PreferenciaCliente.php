<?php

namespace Model;

class PreferenciaCliente extends ActiveRecord {
    protected static $tabla = 'preferencias_cliente';
    protected static $columnasDB = ['id', 'cliente_id', 'categoria', 'valor', 'fecha_creacion'];

    public $id;
    public $cliente_id;
    public $categoria;
    public $valor;
    public $fecha_creacion;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->cliente_id = $args['cliente_id'] ?? '';
        $this->categoria = $args['categoria'] ?? '';
        $this->valor = $args['valor'] ?? '';
        $this->fecha_creacion = $args['fecha_creacion'] ?? '';
    }

    // Obtener todas las preferencias de un cliente
    public static function getPreferenciasCliente($clienteId) {
        return static::whereAll('cliente_id', $clienteId);
    }

    // Obtener todas las preferencias de un cliente por categoría
    public static function getPreferenciasPorCategoria($clienteId, $categoria) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE cliente_id = ${clienteId} AND categoria = '${categoria}'";
        return static::consultarSQL($query);
    }
}
