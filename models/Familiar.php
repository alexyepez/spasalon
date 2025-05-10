<?php
// filepath: c:\SpaSalon\models\Familiar.php
namespace Model;

class Familiar extends ActiveRecord {
    protected static $tabla = 'familiares';
    protected static $columnasDB = ['id', 'cliente_id', 'nombre', 'apellido', 'parentesco', 'fecha_nacimiento', 'telefono'];

    public $id;
    public $cliente_id;
    public $nombre;
    public $apellido;
    public $parentesco;
    public $fecha_nacimiento;
    public $telefono;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->cliente_id = $args['cliente_id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->parentesco = $args['parentesco'] ?? '';
        $this->fecha_nacimiento = $args['fecha_nacimiento'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
    }
}