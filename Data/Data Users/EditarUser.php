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

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE CC = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function actualizar($nuevoCC, $nombre, $usuario, $rol, $clave, $idActual) {
        $sql = "UPDATE usuarios SET CC = ?, NameDB = ?, UserDB = ?, RoleDB = ?";
        $tipos = "isssi";
        $parametros = [$nuevoCC, $nombre, $usuario, $rol, $idActual];

        if (!empty($clave)) {
            $sql .= ", PassDB = ?";
            $tipos = "issssi";
            array_splice($parametros, 4, 0, $clave);
        }

        $sql .= " WHERE CC = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param($tipos, ...$parametros);
        $stmt->execute();
    }
}

Autenticacion::validarAdministrador();

$conexion = ConexionBD::getInstancia()->getConexion();

$user = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $usuario = new Usuario($conexion);
    $user = $usuario->obtenerPorId($id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nuevoCC = $_POST['CC'];
        $nuevoNombre = $_POST['NameDB'];
        $nuevoUserDB = $_POST['UserDB'];
        $nuevoRol = $_POST['RoleDB'];
        $nuevaClave = !empty($_POST['PassDB']) ? $_POST['PassDB'] : null;

        $usuario->actualizar($nuevoCC, $nuevoNombre, $nuevoUserDB, $nuevoRol, $nuevaClave, $id);
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
    <link rel="stylesheet" href="../../CSS/StyleEditors.css">
    <title>Editar Usuario - Inventory+</title>
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
                <h1 class="MainTittle">EDITAR USUARIO</h1>
                <form method="POST" class="FormStyle">
                    <label for="CC">ID:</label>
                    <input type="text" name="CC" id="CC" value="<?php echo htmlspecialchars($user['CC']); ?>" maxlength="14" pattern="\d{1,14}" required>
                    <br>
                    <label for="NameDB">Nombre:</label>
                    <input type="text" name="NameDB" value="<?php echo htmlspecialchars($user['NameDB']); ?>" maxlength="20" required>
                    <br>
                    <label for="UserDB">Nombre de Usuario:</label>
                    <input type="text" name="UserDB" value="<?php echo htmlspecialchars($user['UserDB']); ?>" maxlength="16" required>
                    <br>
                    <label for="PassDB">Contraseña (Dejar en blanco si no deseas cambiarla):</label>
                    <input type="password" name="PassDB" placeholder="Nueva Contraseña (opcional)" maxlength="20">
                    <br>
                    <label for="RoleDB">Rol:</label>
                    <select name="RoleDB" required>
                        <option value="Administrador" <?php echo ($user['RoleDB'] == 'Administrador') ? 'selected' : ''; ?>>Administrador</option>
                        <option value="Coordinador" <?php echo ($user['RoleDB'] == 'Coordinador') ? 'selected' : ''; ?>>Coordinador</option>
                        <option value="Auditor" <?php echo ($user['RoleDB'] == 'Auditor') ? 'selected' : ''; ?>>Auditor</option>
                    </select>
                    <br>
                    <button class="SubmitButton" type="submit">Guardar Cambios</button>
                </form>
            </div>
        </article>
    </section>
</body>
<footer>
    <p>By Jhojan Danilo</p>
</footer>
</html>
