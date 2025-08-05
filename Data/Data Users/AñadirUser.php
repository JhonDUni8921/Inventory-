<?php
include '../DataDB.php';

class Autenticacion {
    public static function validarAdministrador() {
        if (!isset($_COOKIE["NameUserM"]) || !isset($_COOKIE["RoleDB"]) || $_COOKIE["RoleDB"] !== "Administrador") {
            header("Location: ../../index.php");
            exit();
        }
    }
}

class Usuario {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function agregar($cc, $nombre, $usuario, $clave, $rol) {
        $sql = "INSERT INTO usuarios (CC, NameDB, UserDB, PassDB, RoleDB) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("issss", $cc, $nombre, $usuario, $clave, $rol);
        $stmt->execute();
    }
}

Autenticacion::validarAdministrador();

$conexion = ConexionBD::getInstancia()->getConexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cc = $_POST['CC'];
    $nombre = $_POST['NameDB'];
    $usuario = $_POST['UserDB'];
    $clave = $_POST['PassDB'];
    $rol = $_POST['RoleDB'];

    if (!empty($cc) && !empty($nombre) && !empty($usuario) && !empty($clave) && !empty($rol)) {
        $usuarioObj = new Usuario($conexion);
        $usuarioObj->agregar($cc, $nombre, $usuario, $clave, $rol);
        header('Location: ../../Usuarios.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Usuario - Inventory+</title>
    <link rel="stylesheet" href="../../CSS/StyleEditors.css">
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
                <h1 class="MainTittle">AÑADIR USUARIO</h1>
                <form method="POST" class="FormStyle">
                    <label for="CC">ID:</label>
                    <input type="text" name="CC" id="CC" maxlength="14" pattern="\d{1,14}" required>
                    <br>
                    <label for="NameDB">Nombre:</label>
                    <input type="text" name="NameDB" maxlength="20" required>
                    <br>
                    <label for="UserDB">Nombre de Usuario:</label>
                    <input type="text" name="UserDB" maxlength="16" required>
                    <br>
                    <label for="PassDB">Contraseña:</label>
                    <input type="password" name="PassDB" maxlength="20" required>
                    <br>
                    <label for="RoleDB">Rol:</label>
                    <select name="RoleDB" required>
                        <option value="Administrador">Administrador</option>
                        <option value="Coordinador">Coordinador</option>
                        <option value="Auditor" selected>Auditor</option>
                    </select>
                    <br>
                    <button class="SubmitButton" type="submit">Añadir Usuario</button>
                </form>
            </div>
        </article>
    </section>
</body>
<footer>
    <p>By Jhojan Danilo</p>
</footer>
</html>
