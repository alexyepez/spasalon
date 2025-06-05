<?php

namespace Model;

class AdminCita extends ActiveRecord {
    protected static $tabla = 'citas_servicios'; // Esta tabla parece más para la relación, pero la consulta es más amplia.
    // La clave es que las propiedades del objeto coincidan con las columnas seleccionadas en la consulta SQL.
    protected static $columnasDB = ['id', 'hora', 'cliente', 'email', 'telefono', 'nombre_servicio', 'precio', 'cliente_id']; // Añadido cliente_id

    public $id;
    public $hora;
    public $cliente; // Este es el nombre concatenado del cliente
    public $email;
    public $telefono;
    public $nombre_servicio;
    public $precio;
    public $cliente_id;
    public $fecha;
    public $nombreMembresia; // Para almacenar el nombre de la membresía
    public $descuentoPorcentaje; // Para almacenar el porcentaje de descuento
    public $precioConDescuento; // Para almacenar el precio final con descuento


    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->hora = $args['hora'] ?? '';
        $this->cliente = $args['cliente'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->nombre_servicio = $args['nombre_servicio'] ?? '';
        $this->precio = $args['precio'] ?? '';
        $this->cliente_id = $args['cliente_id'] ?? null;
        $this->fecha = $args['fecha'] ?? '';
        $this->nombreMembresia = $args['nombreMembresia'] ?? '';
        $this->descuentoPorcentaje = $args['descuentoPorcentaje'] ?? 0;
        $this->precioConDescuento = $args['precioConDescuento'] ?? ($args['precio'] ?? 0);
    }
}