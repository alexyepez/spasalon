<?php

namespace Model;

class AdminCita extends ActiveRecord {
    protected static $tabla = 'citas_servicios';
    protected static $columnasDB = ['id', 'hora', 'cliente', 'email', 'telefono', 'nombre_servicio', 'precio'];
    public $id;
    public $hora;
    public $cliente;
    public $email;
    public $telefono;
    public $nombre_servicio;
    public $precio;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->hora = $args['hora'] ?? '';
        $this->cliente = $args['cliente'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->nombre_servicio = $args['nombre_servicio'] ?? '';
        $this->precio = $args['precio'] ?? '';
    }
}