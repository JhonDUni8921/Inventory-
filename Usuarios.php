<?php
include './Data/DataDB.php';

class UsuarioModel {
    private $conexion;
    private $itemsPerPage;

    public function __construct($conexion, $itemsPerPage = 10) {
        $this->conexion = $conexion;
        $this->itemsPerPage = $itemsPerPage;
    }

    public function contarUsuarios($search = '') {
        $where = '';
        $params = [];
        $types = '';

        if ($search !== '') {
            $where = "WHERE CC LIKE ? OR UserDB LIKE ? OR NameDB LIKE ? OR RoleDB LIKE ?";
            $likeSearch = "%$search%";
            $params = [$likeSearch, $likeSearch, $likeSearch, $likeSearch];
            $types = str_repeat('s', count($params));
        }

        $sql = "SELECT COUNT(*) as total FROM usuarios $where";
        $stmt = $this->conexion->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();

        return $result;
    }

    public function obtenerUsuarios($search = '', $offset = 0) {
        $params = [];
        $types = '';
        $where = '';

        if ($search !== '') {
            $where = "WHERE CC LIKE ? OR UserDB LIKE ? OR NameDB LIKE ? OR RoleDB LIKE ?";
            $likeSearch = "%$search%";
            $params = [$likeSearch, $likeSearch, $likeSearch, $likeSearch];
            $types = str_repeat('s', count($params));
        }

        $sql = "
            SELECT CC, UserDB, RoleDB, NameDB 
            FROM usuarios 
            $where
            ORDER BY 
                CASE RoleDB 
                    WHEN 'Administrador' THEN 1
                    WHEN 'Coordinador' THEN 2
                    WHEN 'Auditor' THEN 3
                    ELSE 4
                END
            LIMIT ?, ?
        ";

        $stmt = $this->conexion->prepare($sql);
        $params[] = $offset;
        $params[] = $this->itemsPerPage;
        $types .= "ii";

        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $resultado = [];
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $resultado[] = $row;
        }
        $stmt->close();

        return $resultado;
    }
}

// ✅ Validación de sesión
if (!isset($_COOKIE["NameUserM"]) || !isset($_COOKIE["RoleDB"]) || $_COOKIE["RoleDB"] != "Administrador") {
    header("Location: ./index.php");
    exit();
}

// ✅ Obtener conexión usando Singleton
$conexion = ConexionBD::getInstancia()->getConexion();

// ✅ Parámetros de búsqueda y paginación
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$offset = ($page - 1) * 10;

// ✅ Crear el modelo y procesar datos
$usuarioModel = new UsuarioModel($conexion, 10);
$totalItems = $usuarioModel->contarUsuarios($search);
$totalPaginas = ceil($totalItems / 10);
$users = $usuarioModel->obtenerUsuarios($search, $offset);

// ✅ Cerrar conexión
$conexion->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="./CSS/StyleIndex.css" />
    <title>Usuarios - Inventory+</title>
    <link rel="icon" href="./IMG/Logo.png" type="image/x-icon" />
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
        <h1 class="MainTittle">Usuarios Registrados</h1>
    </div>

    <div class="table-container">
        <h1 class="ColorTitle">Lista de Usuarios</h1>

        <form method="GET" action="" class="search-form">
            <input type="text" name="search" placeholder="Buscar usuario..." value="<?php echo htmlspecialchars($search); ?>" class="search-input" />
            <button type="submit" class="search-button">Buscar</button>
        </form>

        <?php if (!empty($users)) : ?>
            <table>
            <thead>
                <tr>
                    <th>Cédula de Ciudadanía</th>
                    <th>Nombre de Usuario</th>
                    <th>Nombre Completo</th>
                    <th>Rol Asignado</th>
                    <?php if (isset($_COOKIE["RoleDB"]) && ($_COOKIE["RoleDB"] == "Administrador" || $_COOKIE["RoleDB"] == "Coordinador")) : ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['CC']); ?></td>
                    <td><?php echo htmlspecialchars($user['UserDB']); ?></td>
                    <td><?php echo htmlspecialchars($user['NameDB']); ?></td>
                    <td><?php echo htmlspecialchars($user['RoleDB']); ?></td>
                    <?php if (isset($_COOKIE["RoleDB"]) && ($_COOKIE["RoleDB"] == "Administrador" || $_COOKIE["RoleDB"] == "Coordinador")) : ?>
                    <td class="action-buttons">
                        <a href="./Data/Data Users/EditarUser.php?id=<?php echo urlencode($user['CC']); ?>" class="editButton" title="Editar">
                            <img src="./IMG/Editar.png" alt="Editar" width="20px" />
                        </a>
                        <a href="./Data/Data Users/EliminarUser.php?id=<?php echo urlencode($user['CC']); ?>" class="deleteButton" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                            <img src="./IMG/Borrar.png" alt="Eliminar" width="20px" />
                        </a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
        <?php else : ?>
            <p>No hay usuarios disponibles.</p>
        <?php endif; ?>

        <div class="PaginationDiv">
            <?php if ($totalPaginas > 1) : ?>
                <nav class="pagination">
                <?php if ($page > 1) : ?>
                    <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>" class="page-link">&laquo; Anterior</a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPaginas; $p++) : ?>
                    <?php if ($p == $page) : ?>
                        <span class="page-link active"><?php echo $p; ?></span>
                    <?php else : ?>
                        <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $p; ?>" class="page-link"><?php echo $p; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPaginas) : ?>
                    <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>" class="page-link">Siguiente &raquo;</a>
                <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>

        <?php if (isset($_COOKIE["RoleDB"]) && ($_COOKIE["RoleDB"] == "Administrador" || $_COOKIE["RoleDB"] == "Coordinador")) : ?>
            <a href="./Data/Data Users/AñadirUser.php" class="addButton">Agregar Usuario</a>
        <?php endif; ?>
    </div>
</article>
</section>

</body>
<footer>
    <p>By Jhojan Danilo</p>
</footer>
</html>
