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
        $this->rol_id = $args['rol_id'] ?? 3; // Rol cliente por defecto
        $this->confirmado = $args['confirmado'] ?? 0; // 0 no confirmado, 1 confirmado
        $this->token = $args['token'] ?? '';
    }

    // Validar el login de un usuario
    public function validarNuevaCuenta() {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if (!$this->apellido) {
            self::$alertas['error'][] = 'El Apellido es Obligatorio';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email  es Obligatorio';
        }
        if (!$this->telefono) {
            self::$alertas['error'][] = 'El Teléfono  es Obligatorio';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El Password  es Obligatorio';
        } elseif (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe tener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    // Validar el login de un usuario
    public function validarLogin() {
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email  es Obligatorio';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El Password  es Obligatorio';
        }
        return self::$alertas;
    }

    // Validar el email
    public function validarEmail() {
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email  es Obligatorio';
        }
        return self::$alertas;
    }

    // Encontrar un Usuario
    public static function find($id) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE id = {$id} LIMIT 1";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    // Validar el password
    public function validarPassword() {
        if (!$this->password) {
            self::$alertas['error'][] = 'La contraseña es Obligatoria';
        } 
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'La contraseña debe tener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    // Revisa si el usuario existe
    public function existeUsuario() {
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";
        //$email = self::$db->escape_string($this->email);
        //$query = "SELECT * FROM " . self::$tabla . " WHERE email = '$email' LIMIT 1";
        $resultado = self::$db->query($query);
        if ($resultado->num_rows) {
            self::$alertas['error'][] = 'El email ya está registrado';
        }
        return $resultado;
    }

    // Hashear la contraseña
    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    // Crear Token
    public function crearToken() {
        $this->token = trim(uniqid());
    }

    // Comprobar el password
    public function comprobarPasswordAndVerificado($password) {
        //return password_verify($password, $this->password) && $this->confirmado == 1;
        $resultado = password_verify($password, $this->password);

        if (!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = 'El Password es Incorrecto o la cuenta no ha sido confirmada';
        } else {
            return true;
        }
    }
}