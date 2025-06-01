<?php

namespace Model;

class Servicio extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'servicios';
    protected static $columnasDB = ['id', 'nombre', 'precio', 'descripcion'];

    public $id;
    public $nombre;
    public $precio;
    public $descripcion;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->precio = $args['precio'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
    }

    public function validar() {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre del servicio es obligatorio';
        }

        if (!$this->precio) {
            self::$alertas['error'][] = 'El precio es obligatorio';
        }

        if ($this->precio < 0) {
            self::$alertas['error'][] = 'El precio debe ser mayor a 0';
        }

        if (!is_numeric($this->precio)) {
            self::$alertas['error'][] = 'El precio no es válido';
        }

        if (!$this->descripcion) {
            self::$alertas['error'][] = 'La descripción del servicio es obligatoria';
        }

        return self::$alertas;
    }
}