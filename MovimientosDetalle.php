<?php

class ConexionBD {
    private static $instancia = null;
    private $conexion;

    private $server = "localhost";
    private $user = "root";
    private $pass = "";
    private $db = "Inventario";

    private function __construct() {
        $this->conexion = new mysqli($this->server, $this->user, $this->pass, $this->db);
        if ($this->conexion->connect_error) {
            die("Conexión fallida: " . $this->conexion->connect_error);
        }
    }

    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new ConexionBD();
        }
        return self::$instancia;
    }

    public function getConexion() {
        return $this->conexion;
    }
}

class Autenticacion {
    public static function validarAdministrador() {
        if (!isset($_COOKIE["NameUserM"])) {
            header("Location: ./index.php");
            exit();
        }
    }
}

class Movimiento {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerMovimiento($id) {
        $sql = "SELECT * FROM movimientos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function obtenerDetalles($id) {
        $sql = "SELECT d.id_articulo, p.nombre, d.cantidad
                FROM detalles_movimiento d
                INNER JOIN productos p ON d.id_articulo = p.id
                WHERE d.id_movimiento = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }
}

$conexion = ConexionBD::getInstancia()->getConexion();
Autenticacion::validarAdministrador();

if (!isset($_GET['id'])) {
    echo "ID de movimiento no especificado.";
    exit();
}

$id_movimiento = (int)$_GET['id'];

$movimientoObj = new Movimiento($conexion);
$movimiento = $movimientoObj->obtenerMovimiento($id_movimiento);

if (!$movimiento) {
    echo "Movimiento no encontrado.";
    exit();
}

$detalles = $movimientoObj->obtenerDetalles($id_movimiento);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detalle del Movimiento - Inventory+</title>
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
                <h1 class="MainTittle">Detalle del Movimiento</h1>
            </div>

            <div class="table-container">
                <h3>Información general</h3>
                
                <?php if ($detalles->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID Artículo</th>
                                <th>Nombre</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($detalle = $detalles->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $detalle['id_articulo']; ?></td>
                                    <td><?php echo htmlspecialchars($detalle['nombre']); ?></td>
                                    <td><?php echo $detalle['cantidad']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay artículos asociados a este movimiento.</p>
                <?php endif; ?>

                <p><strong>ID Movimiento:</strong> <?php echo $movimiento['id']; ?></p>
                <p><strong>Fecha:</strong> <?php echo $movimiento['fecha']; ?></p>
                <p><strong>Hora:</strong> <?php echo $movimiento['hora']; ?></p>
                <p><strong>Usuario:</strong> <?php echo htmlspecialchars($movimiento['usuario']); ?></p>
                <p><strong>Acción:</strong> <?php echo htmlspecialchars($movimiento['accion']); ?></p>

                <div style="margin-top: 20px;">
                    <a href="./Movimientos.php"><button class="addButton">Volver</button></a>
                </div>
            </div>
        </article>
    </section>
</body>
<footer>
    <p>By Jhojan Danilo</p>
</footer>
</html>