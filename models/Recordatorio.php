<?php

namespace Model;

class Recordatorio extends ActiveRecord {
    protected static $tabla = 'recordatorios';
    protected static $columnasDB = ['id', 'cliente_id', 'cita_id', 'fecha', 'enviado', 'medio'];

    public $id;
    public $cliente_id;
    public $cita_id;
    public $fecha;
    public $enviado;
    public $medio;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->cliente_id = $args['cliente_id'] ?? '';
        $this->cita_id = $args['cita_id'] ?? '';
        $this->fecha = $args['fecha'] ?? '';
        $this->enviado = $args['enviado'] ?? 0;
        $this->medio = $args['medio'] ?? 'email';
    }

    public function validar() {
        if (!$this->cliente_id) {
            self::$alertas['error'][] = 'El cliente es obligatorio';
        }
        if (!$this->cita_id) {
            self::$alertas['error'][] = 'La cita es obligatoria';
        }
        if (!$this->fecha) {
            self::$alertas['error'][] = 'La fecha de envío es obligatoria';
        }
        return self::$alertas;
    }

    // Relaciones
    public function getCita() {
        return Cita::find($this->cita_id);
    }

    public function getCliente() {
        $cliente = Cliente::find($this->cliente_id);
        if ($cliente) {
            $usuario = Usuario::find($cliente->usuario_id);
            if ($usuario) {
                // Añadir información del usuario al cliente
                $cliente->nombre = $usuario->nombre;
                $cliente->apellido = $usuario->apellido;
                $cliente->email = $usuario->email;
            }
        }
        return $cliente;
    }
}