<?php
namespace Model;

class Membresia extends ActiveRecord {
    protected static $tabla = 'membresias';
    protected static $columnasDB = ['id', 'nombre', 'precio', 'descripcion', 'beneficios', 'descuento'];

    public $id;
    public $nombre;
    public $precio;
    public $descripcion;
    public $beneficios;
    public $descuento;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->precio = $args['precio'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->beneficios = $args['beneficios'] ?? '';
        $this->descuento = $args['descuento'] ?? 0;
    }

    public function validar() {
        self::$alertas = [];

        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre de la membresía es obligatorio';
        }
        if (!$this->precio) {
            self::$alertas['error'][] = 'El precio de la membresía es obligatorio';
        }
        if (!is_numeric($this->precio)) {
            self::$alertas['error'][] = 'El precio debe ser un número válido';
        }
        if (!is_numeric($this->descuento) || $this->descuento < 0 || $this->descuento > 100) {
            self::$alertas['error'][] = 'El descuento debe ser un porcentaje válido entre 0 y 100';
        }

        return self::$alertas;
    }

    // Obtener clientes con esta membresía
    public function getClientes() {
        $clientesMembresias = ClienteMembresia::whereAll('membresia_id', $this->id);
        $clientes = [];

        foreach ($clientesMembresias as $cm) {
            $cliente = Cliente::find($cm->cliente_id);
            if ($cliente) {
                $clientes[] = $cliente;
            }
        }

        return $clientes;
    }

    // Método para calcular el precio con descuento
    public function aplicarDescuento($precioOriginal) {
        if (empty($this->descuento) || $this->descuento <= 0) {
            return $precioOriginal;
        }

        $descuento = ($precioOriginal * $this->descuento) / 100;
        return $precioOriginal - $descuento;
    }

    // Contar clientes activos con esta membresía
    public function contarClientesActivos() {
        $hoy = date('Y-m-d');

        // Usaremos una consulta directa sin depender del método SQL
        $query = "SELECT COUNT(DISTINCT cliente_id) as total FROM clientes_membresias 
             WHERE membresia_id = " . self::$db->escape_string($this->id) . " 
             AND fecha_inicio <= '" . self::$db->escape_string($hoy) . "' 
             AND fecha_fin >= '" . self::$db->escape_string($hoy) . "'";

        // Ejecutar consulta directamente
        $result = self::$db->query($query);

        if ($result && $result->num_rows > 0) {
            $fila = $result->fetch_assoc();
            $this->clientesActivos = (int)$fila['total'];
        } else {
            $this->clientesActivos = 0;
        }

        return $this->clientesActivos;
    }

    // Sobrescribir el método all para incluir el conteo de clientes
    public static function all() {
        $membresias = parent::all();

        // Para cada membresía, contar sus clientes activos
        foreach ($membresias as $membresia) {
            $membresia->contarClientesActivos();
        }

        return $membresias;
    }

    // También sobrescribir find para incluir el conteo en consultas individuales
    public static function find($id) {
        $membresia = parent::find($id);

        if ($membresia) {
            $membresia->contarClientesActivos();
        }

        return $membresia;
    }

}