<?php
class ConexionBD {
    private static $instancia = null;
    private $conexion;

    private $server = "localhost";
    private $user = "root";
    private $pass = "";
    private $db = "Inventario";

    // Constructor privado para evitar nuevas instancias externas
    private function __construct() {
        $this->conexion = new mysqli($this->server, $this->user, $this->pass, $this->db);

        if ($this->conexion->connect_error) {
            die("Conexión fallida: " . $this->conexion->connect_error);
        }
    }

    // Método para obtener la instancia única
    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new ConexionBD();
        }
        return self::$instancia;
    }

    // Método para obtener el objeto mysqli
    public function getConexion() {
        return $this->conexion;
    }
}
?>
