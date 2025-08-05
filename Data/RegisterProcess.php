<?php
include './DataDB.php';

class UsuarioManager {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function registrarUsuario($cc, $nombre, $usuario, $contrasena, $rol = 'Auditor') {
        $sql = "INSERT INTO usuarios (CC, NameDB, UserDB, PassDB, RoleDB) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            throw new Exception('Error en la preparación de la consulta: ' . $this->conexion->error);
        }

        $stmt->bind_param("sssss", $cc, $nombre, $usuario, $contrasena, $rol);

        if (!$stmt->execute()) {
            throw new Exception('Error al registrar el usuario: ' . $stmt->error);
        }

        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cc = $_POST['UserCC'];
    $userName = $_POST['UserName'];
    $userDB = $_POST['UserDB'];
    $password = $_POST['Password'];
    $confirmPassword = $_POST['ConfirmPassword'];

    if ($password !== $confirmPassword) {
        header('Location: ../RegisterPage.php');
        exit();
    }

    try {

        $conexion = ConexionBD::getInstancia()->getConexion();
        $usuarioManager = new UsuarioManager($conexion);
        $usuarioManager->registrarUsuario($cc, $userName, $userDB, $password);
        header('Location: ../LoginPage.php');
        exit();
    } catch (Exception $e) {
        die($e->getMessage());
    }
}
?>
