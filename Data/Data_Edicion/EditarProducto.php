<?php
if (isset($_COOKIE["RoleDB"]) && ($_COOKIE["RoleDB"] == "Auditor")) {
    header("Location: ../../index.php");
    exit();
}

include '../DataDB.php';

$conexion = ConexionBD::getInstancia()->getConexion();

class Producto {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM productos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function actualizar($id, $nombre, $descripcion, $precio, $cantidad) {
        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, cantidad = ?, fecha_actualizacion = NOW() WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $cantidad, $id);
        $stmt->execute();
    }
}

class Movimiento {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function registrar($usuario, $accion, $id_producto, $cantidad) {
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');

        $sql_mov = "INSERT INTO movimientos (fecha, hora, usuario, accion) VALUES (?, ?, ?, ?)";
        $stmt_mov = $this->conexion->prepare($sql_mov);
        $stmt_mov->bind_param("ssss", $fecha, $hora, $usuario, $accion);
        $stmt_mov->execute();
        $id_movimiento = $this->conexion->insert_id;

        $sql_det = "INSERT INTO detalles_movimiento (id_movimiento, id_articulo, cantidad, tipo_movimiento) VALUES (?, ?, ?, ?)";
        $stmt_det = $this->conexion->prepare($sql_det);
        $stmt_det->bind_param("iiis", $id_movimiento, $id_producto, $cantidad, $accion);
        $stmt_det->execute();
    }
}

if (!isset($_GET['id'])) {
    header('Location: ../../Inventario.php');
    exit();
}

$id = intval($_GET['id']);
$productoObj = new Producto($conexion);
$producto = $productoObj->obtenerPorId($id);

if (!$producto) {
    header('Location: ../../Inventario.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = floatval($_POST['precio']);
    $cantidad_nueva = intval($_POST['cantidad']);
    $cantidad_actual = intval($producto['cantidad']);

    $productoObj->actualizar($id, $nombre, $descripcion, $precio, $cantidad_nueva);

    $diferencia = $cantidad_nueva - $cantidad_actual;

    if ($diferencia != 0) {
        $usuario = $_COOKIE['NameUserM'] ?? 'Desconocido';
        $accion = ($diferencia > 0) ? 'Ingreso' : 'Salida';
        $cantidad_mov = abs($diferencia);

        $movimiento = new Movimiento($conexion);
        $movimiento->registrar($usuario, $accion, $id, $cantidad_mov);
    }

    header('Location: ../../Inventario.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../../CSS/StyleEditors.css" />
    <title>Editar Producto - Inventory+</title>
    <link rel="icon" href="../../IMG/Logo.png" type="image/x-icon" />
</head>

<header>
    <nav>
        <div class="DivNavLogo">
            <img src="../../IMG/Logo.png" alt="" class="LogoNav" />
        </div>
        <div class="DivNav">
            <div class="DivButtonsNav1">
                <a href="../../Index.php"><button class="ButtonNav">INICIO</button></a>

                <?php if (isset($_COOKIE["NameUserM"])): ?>
                    <?php if (isset($_COOKIE["RoleDB"]) && $_COOKIE["RoleDB"] == "Administrador"): ?>
                        <a href="../../Usuarios.php"><button class="ButtonNav">USUARIOS</button></a>
                    <?php endif; ?>

                    <a href="../../Equipos.php"><button class="ButtonNav">EQUIPOS</button></a>
                    <a href="../../Inventario.php"><button class="ButtonNav">INVENTARIO</button></a>
                <?php endif; ?>
            </div>
            <div class="DivButtonsNav2">
                <?php if (isset($_COOKIE["NameUserM"])): ?>
                    <a href="../../Data/LogOut.php"><button class="ButtonNav">CERRAR SESIÓN</button></a>
                <?php else: ?>
                    <a href="../../LoginPage.php"><button class="ButtonNav">INICIAR SESIÓN</button></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<body>
<section>
    <article>
        <div class="DivPrincipal">
            <h1 class="MainTittle">Editar Producto</h1>
            <form method="POST" class="FormStyle">

                <label>ID del Producto:</label>
                <input type="text" value="<?php echo htmlspecialchars($producto['id']); ?>" disabled />
                <br />

                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" maxlength="30" required />
                <br />

                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" maxlength="100" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                <br />

                <label for="precio">Precio:</label>
                <input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>" maxlength="30" required />
                <br />

                <label for="cantidad">Cantidad:</label>
                <input type="number" name="cantidad" value="<?php echo htmlspecialchars($producto['cantidad']); ?>" min="0" step="1" maxlength="10" required />
                <br />

                <button class="SubmitButton" type="submit">Guardar Cambios</button>
            </form>
        </div>
    </article>
</section>
</body>
<script src="../../JS/En_Editores/ScriptCarrito.js"></script>
<footer>
    <p>By Jhojan Danilo</p>
</footer>
</html>
