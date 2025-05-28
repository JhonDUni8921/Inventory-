<?php
include '../DataDB.php';

class Producto {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function eliminar($id) {
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $producto = new Producto($conexion);

    if ($producto->eliminar($id)) {
        header('Location: ../../Inventario.php');
        exit();
    } else {
        echo "Error al eliminar el producto.";
    }
} else {
    header('Location: ../../Inventario.php');
    exit();
}
?>
