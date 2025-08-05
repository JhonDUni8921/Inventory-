<?php
include './DataDB.php';

class AuthManager {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function verificarUsuario($usuario, $contrasena) {
        $sql = "SELECT CC, UserDB, PassDB, NameDB, RoleDB FROM usuarios WHERE UserDB = ?";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
        }

        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows !== 1) {
            return "usuario";
        }

        $user = $result->fetch_assoc();

        if ($contrasena !== $user["PassDB"]) {
            return "clave";
        }

        setcookie("NameUserM", $user["NameDB"], 0, "/");
        setcookie("UserNameP", $user["UserDB"], 0, "/");
        setcookie("RoleDB", $user["RoleDB"], 0, "/");
        setcookie("UserCC", $user["CC"], 0, "/");

        return "ok";
    }
}

if (!isset($_POST["UserName"]) || !isset($_POST["Password"])) {
    header("Location: ../LoginPage.php?error=datos");
    exit();
}

$usuario = $_POST["UserName"];
$contrasena = $_POST["Password"];

$conexion = ConexionBD::getInstancia()->getConexion();

try {
    $auth = new AuthManager($conexion);
    $resultado = $auth->verificarUsuario($usuario, $contrasena);

    if ($resultado === "ok") {
        header("Location: ../Index.php");
    } else {
        header("Location: ../LoginPage.php?error=" . $resultado);
    }
    exit();
} catch (Exception $e) {
    die("Error en autenticación: " . $e->getMessage());
}
?>
