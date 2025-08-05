<?php
include '../DataDB.php';

class Producto {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function eliminar($id) {

        $sql1 = "DELETE FROM detalles_movimiento WHERE id_articulo = ?";
        $stmt1 = $this->conexion->prepare($sql1);
        $stmt1->bind_param("i", $id);
        $stmt1->execute();

        $sql2 = "DELETE FROM productos WHERE id = ?";
        $stmt2 = $this->conexion->prepare($sql2);
        $stmt2->bind_param("i", $id);
        $stmt2->execute();

        return $stmt2->affected_rows > 0;
    }
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $conexion = ConexionBD::getInstancia()->getConexion();
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
