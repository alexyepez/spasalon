<?php

namespace Model;

class Usuario extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'rol_id', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $rol_id;
    public $confirmado;
    public $token;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->rol_id = $args['rol_id'] ?? 1; // Rol cliente por defecto
        $this->confirmado = $args['confirmado'] ?? 0; // 0 no confirmado, 1 confirmado
        $this->token = $args['token'] ?? '';
    }

    // Validar el login de un usuario
    public function validarNuevaCuenta() {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre del Cliente es Obligatorio';
        }
        if (!$this->apellido) {
            self::$alertas['error'][] = 'El Apellido del Cliente es Obligatorio';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email del Cliente es Obligatorio';
        }
        if (!$this->telefono) {
            self::$alertas['error'][] = 'El Teléfono del Cliente es Obligatorio';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El Password del Cliente es Obligatorio';
        } elseif (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe tener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    
    public function existeUsuario() {
        $query = "SELECT * FROM " . static::$tabla . " WHERE email = '" . self::$db->escape_string($this->email) . "' LIMIT 1";
        $resultado = self::$db->query($query);
        if ($resultado->num_rows) {
            static::$alertas['error'][] = 'El email ya está registrado';
        }
        return $resultado;
    }

}