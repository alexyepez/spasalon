<?php
namespace Model;
class ActiveRecord {

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // Alertas y Mensajes
    protected static $alertas = [];
    
    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }

    // Validación
    public static function getAlertas() {
        return static::$alertas;
    }

    public function validar() {
        static::$alertas = [];
        return static::$alertas;
    }

    // Método para obtener la base de datos
    public static function getDB() {
        return self::$db;
    }


    // Consulta SQL para crear un objeto en Memoria
    public static function consultarSQL($query) {
        //var_dump('Consulta SQL:', $query);
        // Consultar la base de datos
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        // liberar la memoria
        $resultado->free();

        // retornar los resultados
        return $array;
    }

    // Crea el objeto en memoria que es igual al de la BD
    protected static function crearObjeto($registro) {
        $objeto = new static;

        foreach($registro as $key => $value ) {
            if(property_exists( $objeto, $key  )) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    // Identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];
        foreach(static::$columnasDB as $columna) {
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    // Sanitizar los datos antes de guardarlos en la BD
    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach($atributos as $key => $value ) {
            // Si el campo es token, eliminar espacios
            if($key === 'token' && is_string($value)) {
                $value = trim($value);
            }
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }

    // Sincroniza BD con Objetos en memoria
    public function sincronizar($args=[]) { 
        foreach($args as $key => $value) {
          if(property_exists($this, $key) && !is_null($value)) {
            $this->$key = $value;
          }
        }
    }

    /* FUNCIÓN ORIGINAL GUARDAR
    // Registros - CRUD
    public function guardar() {
        $resultado = '';
        if(!empty($this->id)) {
            // actualizar
            $resultado = $this->actualizar();
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();
        }
        return $resultado;
    }*/

    public function guardar() {
        try {
            if (!$this->id) {
                // Es un nuevo registro (insertar)
                return $this->crear();
            } else {
                // Es un registro existente (actualizar)
                return $this->actualizar();
            }
        } catch (\Exception $e) {
            error_log("Error en método guardar(): " . $e->getMessage());
            return false;
        }
    }

    // Todos los registros
    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca un registro por su id
    public static function find($id) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE id = {$id}";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    // Obtener Registros con cierta cantidad
    public static function get($limite) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT {$limite}";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    // Obtener Registros por columna Devuelve solo el primer objeto
    public static function where($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE ${columna} = '{$valor}'";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado );
    }

    // Obtener todos los registros por columna Devuelve un array con todos los objetos que cumplen la condición
    public static function whereAll($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE ${columna} = '{$valor}'";
        $resultado = self::consultarSQL($query);
        return $resultado; // Retorna el array completo de objetos
    }

    // Consulta plana de SQL (Se utiliza cuando los métodos de ActiveRecord no son suficientes)
    public static function SQL($query) {
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Obtener todos los registros por varias columnas
    public static function whereAllMultiple($condiciones) {
        $condicionesSQL = [];
        foreach ($condiciones as $columna => $valor) {
            $condicionesSQL[] = "${columna} = '" . self::$db->escape_string($valor) . "'";
        }
        $query = "SELECT * FROM " . static::$tabla . " WHERE " . implode(' AND ', $condicionesSQL);
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // crea un nuevo registro
    /*
    public function crear() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();
        // Insertar en la base de datos
        $query = "INSERT INTO " . static::$tabla . " (";
        $query .= join(', ', array_keys($atributos));
        $query .= ") VALUES ('";
        $query .= join("', '", array_values($atributos));
        $query .= "')";

        // Ejecutar la consulta
        $resultado = self::$db->query($query);

        // Resultado de la consulta
        return [
            'resultado' =>  $resultado ? true : false,
            'id' => self::$db->insert_id,
            'query' => $query
        ];

    }

    */
    public function crear() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Construir columnas y valores por separado
        $columnas = join(', ', array_keys($atributos));

        // Preparar valores, tratando NULL de forma especial
        $valoresArray = [];
        foreach ($atributos as $valor) {
            // Si el valor es una cadena vacía o null, lo tratamos como NULL en SQL
            if ($valor === '' || $valor === null) {
                $valoresArray[] = "NULL";
            } else {
                $valoresArray[] = "'{$valor}'";
            }
        }

        $valores = join(', ', $valoresArray);

        // Crear consulta con manejo adecuado de NULL
        $query = "INSERT INTO " . static::$tabla . " ($columnas) VALUES ($valores)";

        //var_dump("Query crear: " . $query);

        // Ejecutar la consulta
        $resultado = self::$db->query($query);

        // Resultado de la consulta
        return [
            'resultado' => $resultado ? true : false,
            'id' => self::$db->insert_id,
            'query' => $query
        ];
    }


    // Actualizar el registro
    /*FUNCIÓN ORIGINAL ACTUALIZAR
    public function actualizar() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key}='{$value}'";
        }

        // Consulta SQL
        $query = "UPDATE " . static::$tabla ." SET ";
        $query .=  join(', ', $valores );
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 "; 

        // Actualizar BD
        $resultado = self::$db->query($query);
        return $resultado;
    } */

    public function actualizar() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach($atributos as $key => $value) {
            // Si el valor es una cadena vacía o null, lo tratamos como NULL en SQL
            if ($value === '' || $value === null) {
                $valores[] = "{$key}=NULL";
            } else {
                $valores[] = "{$key}='{$value}'";
            }
        }

        // Consulta SQL
        $query = "UPDATE " . static::$tabla ." SET ";
        $query .=  join(', ', $valores );
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 ";

        // Para depuración
        error_log("Query actualizar: " . $query);

        // Actualizar BD
        $resultado = self::$db->query($query);
        return $resultado;
    }

    // Eliminar un Registro por su ID
    public function eliminar() {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }
}