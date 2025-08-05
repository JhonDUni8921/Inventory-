<?php

include '../DataDB.php';

if (isset($_COOKIE["RoleDB"]) && ($_COOKIE["RoleDB"] == "Auditor")) {
    header("Location: ../../index.php");
    exit();
}

class Producto {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function agregar($nombre, $descripcion, $precio, $cantidad) {
        $sql = "INSERT INTO productos (nombre, descripcion, precio, cantidad) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $cantidad);
        $stmt->execute();
        $stmt->close();
    }
}

$conexion = ConexionBD::getInstancia()->getConexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];

    $producto = new Producto($conexion);
    $producto->agregar($nombre, $descripcion, $precio, $cantidad);

    header('Location: ../../Inventario.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../CSS/StyleEditors.css">
    <title>Agregar Producto - Inventory+</title>
    <link rel="icon" href="../../IMG/Logo.png" type="image/x-icon">
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
            <h1 class="MainTittle">AGREGAR OBJETO</h1>
            <form method="POST" class="FormStyle">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" required maxlength="30">
                <br>

                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" required maxlength="100"></textarea>
                <br>

                <label for="precio">Precio:</label>
                <input type="number" step="0.01" name="precio" required maxlength="30">
                <br>

                <label for="cantidad">Cantidad:</label>
                <input type="number" name="cantidad" required min="0" step="1" maxlength="10">
                <br>

                <button class="SubmitButton" type="submit">Agregar Producto</button>
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
