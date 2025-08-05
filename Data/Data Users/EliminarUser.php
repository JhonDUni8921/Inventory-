<?php
include '../DataDB.php';

class Usuario {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function eliminar($id) {
        $sql = "DELETE FROM usuarios WHERE CC = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->affected_rows > 0;
        $stmt->close();
        return $resultado;
    }
}

$conexion = ConexionBD::getInstancia()->getConexion();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $usuario = new Usuario($conexion);

    if ($usuario->eliminar($id)) {
        header('Location: ../../Usuarios.php');
        exit();
    } else {
        echo "Error al eliminar el usuario.";
    }

    $conexion->close();
} else {
    header('Location: ../../Usuarios.php');
    exit();
}
?>
