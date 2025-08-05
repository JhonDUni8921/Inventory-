<?php
require "./Data/DataDB.php";

class Autenticacion {
    public static function validarAdministrador() {
        if (!isset($_COOKIE["NameUserM"])) {
            header("Location: ./index.php");
            exit();
        }
    }
}

class Equipo {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerResumen($id) {
        $sql = "SELECT id, serial, placa_proveedor, asignado_a FROM Equipos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerDetalles($id) {
        $sql = "SELECT * FROM Equipos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}

Autenticacion::validarAdministrador();

$id_equipo = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_equipo <= 0) {
    echo "ID inválido.";
    exit();
}

$conexion = ConexionBD::getInstancia()->getConexion();

$equipoRepo = new Equipo($conexion);
$equipo = $equipoRepo->obtenerResumen($id_equipo);
$detalles = $equipoRepo->obtenerDetalles($id_equipo);

if (!$equipo) {
    echo "Equipo no encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detalle del Equipo - Inventory+</title>
    <link rel="stylesheet" href="./CSS/StyleIndex.css" />
</head>
<body>
<header>
    <nav>
        <div class="DivNavLogo">
            <img src="./IMG/Logo.png" alt="" class="LogoNav" />
        </div>
        <div class="DivNav">
            <div class="DivButtonsNav1">
                <a href="./Index.php"><button class="ButtonNav">INICIO</button></a>
                <?php if (isset($_COOKIE["NameUserM"])): ?>
                    <?php if (isset($_COOKIE["RoleDB"]) && $_COOKIE["RoleDB"] == "Administrador"): ?>
                        <a href="./Usuarios.php"><button class="ButtonNav">USUARIOS</button></a>
                    <?php endif; ?>
                    <a href="./Equipos.php"><button class="ButtonNav">EQUIPOS</button></a>
                    <a href="./Inventario.php"><button class="ButtonNav">INVENTARIO</button></a>
                <?php endif; ?>
            </div>
            <div class="DivButtonsNav2">
                <?php if (isset($_COOKIE["NameUserM"])): ?>
                    <a href="./Data/LogOut.php"><button class="ButtonNav">CERRAR SESIÓN</button></a>
                <?php else: ?>
                    <a href="./LoginPage.php"><button class="ButtonNav">INICIAR SESIÓN</button></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<section>
    <article>
        <div class="DivPrincipal">
            <h1 class="MainTittle">Detalle del Equipo</h1>
        </div>

        <div class="table-container">
            <h3>Información general</h3>
            
            <p><strong>ID:</strong> <?php echo htmlspecialchars($equipo['id']); ?></p>
            <p><strong>Serial:</strong> <?php echo htmlspecialchars($equipo['serial']); ?></p>
            <p><strong>Placa Proveedor:</strong> <?php echo htmlspecialchars($equipo['placa_proveedor']); ?></p>
            <p><strong>Asignado a:</strong> <?php echo htmlspecialchars($equipo['asignado_a']); ?></p>

            <h3>Detalles del Equipo</h3>

            <?php if ($detalles): ?>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($detalles['estado']); ?></p>
                <p><strong>Empresa:</strong> <?php echo htmlspecialchars($detalles['empresa']); ?></p>
                <p><strong>CO:</strong> <?php echo htmlspecialchars($detalles['co']); ?></p>
                <p><strong>Ciudad:</strong> <?php echo htmlspecialchars($detalles['ciudad']); ?></p>
                <p><strong>Área:</strong> <?php echo htmlspecialchars($detalles['area']); ?></p>
                <p><strong>Nombre Equipo:</strong> <?php echo htmlspecialchars($detalles['nombre_equipo']); ?></p>
                <p><strong>Usuario:</strong> <?php echo htmlspecialchars($detalles['usuario']); ?></p>
                <p><strong>Asignado a:</strong> <?php echo htmlspecialchars($detalles['asignado_a']); ?></p>
                <p><strong>Fabricante:</strong> <?php echo htmlspecialchars($detalles['fabricante']); ?></p>
                <p><strong>Tipo de Computador:</strong> <?php echo htmlspecialchars($detalles['tipo_computador']); ?></p>
                <p><strong>Marca Procesador:</strong> <?php echo htmlspecialchars($detalles['marca_procesador']); ?></p>
                <p><strong>Generación Procesador:</strong> <?php echo htmlspecialchars($detalles['generacion_procesador']); ?></p>
                <p><strong>Velocidad Procesador:</strong> <?php echo htmlspecialchars($detalles['velocidad_procesador']); ?></p>
                <p><strong>RAM:</strong> <?php echo htmlspecialchars($detalles['ram']); ?></p>
                <p><strong>Tipo de Disco:</strong> <?php echo htmlspecialchars($detalles['tipo_disco']); ?></p>
                <p><strong>Windows:</strong> <?php echo htmlspecialchars($detalles['windows']); ?></p>
                <p><strong>Dispositivos Wi-Fi:</strong> <?php echo htmlspecialchars($detalles['dispositivos_wifi']); ?></p>
                <p><strong>Propietario:</strong> <?php echo htmlspecialchars($detalles['propietario']); ?></p>
            <?php else: ?>
                <p>No hay detalles registrados para este equipo.</p>
            <?php endif; ?>

            <div style="margin-top: 20px;">
                <a href="./Equipos.php"><button class="addButton">Volver</button></a>
            </div>
        </div>
    </article>
</section>

</body>
<footer>
    <p>By Jhojan Danilo</p>
</footer>
</html>
