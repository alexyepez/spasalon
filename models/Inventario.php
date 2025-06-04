<?php

namespace Model;

class Inventario extends ActiveRecord {
    protected static $tabla = 'inventario';
    protected static $columnasDB = ['id', 'producto', 'descripcion', 'precio','cantidad', 'proveedor_id', 'fecha_ingreso'];

    public $id;
    public $producto;
    public $descripcion;
    public $precio;
    public $cantidad;
    public $proveedor_id;
    public $fecha_ingreso;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->producto = $args['producto'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->precio = $args['precio'] ?? '';
        $this->cantidad = $args['cantidad'] ?? 0;
        $this->proveedor_id = $args['proveedor_id'] ?? '';
        $this->fecha_ingreso = $args['fecha_ingreso'] ?? '';
    }

    public function validar() {
        if (!$this->producto) {
            self::$alertas['error'][] = 'El nombre del producto es obligatorio';
        }
        if (!$this->descripcion) {
            self::$alertas['error'][] = 'La descripción del producto es obligatoria';
        }
        if (!$this->precio) {
            self::$alertas['error'][] = 'El precio es obligatorio';
        }
        if ($this->cantidad <= 0) {
            self::$alertas['error'][] = 'La cantidad debe ser mayor a 0';
        }
        if (!$this->proveedor_id) {
            self::$alertas['error'][] = 'El proveedor es obligatorio';
        }
        if (!$this->fecha_ingreso) {
            self::$alertas['error'][] = 'La fecha de ingreso es obligatoria';
        }
        return self::$alertas;
    }

    public function getProveedor() {
        return Proveedor::find($this->proveedor_id);
    }
}
