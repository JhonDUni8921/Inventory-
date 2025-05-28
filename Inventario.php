<?php
require "./Data/DataDB.php";

class ProductoRepository {
    private mysqli $conexion;

    public function __construct(mysqli $conexion) {
        $this->conexion = $conexion;
    }

    public function contarProductos(string $search = ''): int {
        $where = '';
        $params = [];
        $types = '';

        if ($search !== '') {
            $searchParam = "%$search%";
            if (is_numeric($search)) {
                $where = " WHERE id = ? OR nombre LIKE ? OR descripcion LIKE ? ";
                $params = [$search, $searchParam, $searchParam];
                $types = "sss";
            } else {
                $where = " WHERE nombre LIKE ? OR descripcion LIKE ? ";
                $params = [$searchParam, $searchParam];
                $types = "ss";
            }
        }

        $sql = "SELECT COUNT(*) as total FROM productos $where";
        $stmt = $this->conexion->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $total = $result->fetch_assoc()['total'] ?? 0;
        $stmt->close();

        return (int)$total;
    }

    public function obtenerProductos(string $search = '', int $offset = 0, int $limit = 10): array {
        $where = '';
        $params = [];
        $types = '';

        if ($search !== '') {
            $searchParam = "%$search%";
            if (is_numeric($search)) {
                $where = " WHERE id = ? OR nombre LIKE ? OR descripcion LIKE ? ";
                $params = [$search, $searchParam, $searchParam];
                $types = "sss";
            } else {
                $where = " WHERE nombre LIKE ? OR descripcion LIKE ? ";
                $params = [$searchParam, $searchParam];
                $types = "ss";
            }
        }

        $sql = "SELECT id, nombre, descripcion, precio, cantidad FROM productos $where ORDER BY nombre LIMIT ?, ?";
        $stmt = $this->conexion->prepare($sql);
        $params = array_merge($params, [$offset, $limit]);
        $types .= "ii";

        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $result = $stmt->get_result();
        $productos = [];

        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }

        $stmt->close();
        return $productos;
    }
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10;
$offset = ($page - 1) * $itemsPerPage;

$repo = new ProductoRepository($conexion);

$totalItems = $repo->contarProductos($search);
$totalPages = ceil($totalItems / $itemsPerPage);
$productos = $repo->obtenerProductos($search, $offset, $itemsPerPage);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="./CSS/StyleIndex.css" />
    <title>Inventario - Inventory+</title>
    <link rel="icon" href="./IMG/Logo.png" type="image/x-icon" />
</head>
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

                    <a href="./Inventario.php"><button class="ButtonNav">INVENTARIO</button></a>
                    <a href="./Movimientos.php"><button class="ButtonNav">MOVIMIENTOS</button></a>
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
<body>
<section>
  <article>
    <div class="DivPrincipal">
      <h1 class="MainTittle">Inventario Disponible</h1>
    </div>

    <div class="table-container">
      <h1 class="ColorTitle">Inventario de Productos</h1>

      <form method="GET" action="" class="search-form">
        <input type="text" name="search" placeholder="Buscar Producto..." value="<?php echo htmlspecialchars($search); ?>" class="search-input" />
        <button type="submit" class="search-button">Buscar</button>
      </form>

      <?php if (count($productos) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <?php if (isset($_COOKIE["RoleDB"]) && ($_COOKIE["RoleDB"] == "Administrador" || $_COOKIE["RoleDB"] == "Coordinador")): ?>
                <th>Acciones</th>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($productos as $prod): ?>
              <tr>
                <td><?php echo htmlspecialchars($prod["id"]); ?></td>
                <td><?php echo htmlspecialchars($prod["nombre"]); ?></td>
                <td><?php echo htmlspecialchars($prod["descripcion"]); ?></td>
                <td><?php echo intval($prod["cantidad"]); ?></td>
                <td class="price-cell">$ <?php echo number_format($prod["precio"], 0, ',', '.'); ?></td>
                <?php if (isset($_COOKIE["RoleDB"]) && ($_COOKIE["RoleDB"] == "Administrador" || $_COOKIE["RoleDB"] == "Coordinador")): ?>
                  <td class="action-buttons">
                    <a href="./Data/Data_Edicion/EditarProducto.php?id=<?php echo $prod["id"]; ?>" class="editButton" title="Editar">
                      <img src="./IMG/Editar.png" alt="Editar producto" width="20px" />
                    </a>
                    <!--<a href="./Data/Data_Edicion/EliminarProducto.php?id=<?php echo $prod["id"]; ?>" class="deleteButton" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este producto?');">
                      <img src="./IMG/Borrar.png" alt="Eliminar producto" width="20px" />-->
                    </a>
                  </td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No hay productos que coincidan con la búsqueda.</p>
      <?php endif; ?>

      <div class="PaginationDiv">
        <?php if ($totalPages > 1): ?>
          <nav class="pagination">
            <?php if ($page > 1): ?>
              <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>" class="page-link">&laquo; Anterior</a>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
              <?php if ($p == $page): ?>
                <span class="page-link active"><?php echo $p; ?></span>
              <?php else: ?>
                <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $p; ?>" class="page-link"><?php echo $p; ?></a>
              <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
              <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>" class="page-link">Siguiente &raquo;</a>
            <?php endif; ?>
          </nav>
        <?php endif; ?>
      </div>

      <?php if (isset($_COOKIE["RoleDB"]) && ($_COOKIE["RoleDB"] == "Administrador" || $_COOKIE["RoleDB"] == "Coordinador")): ?>
        <a href="./Data/Data_Edicion/AñadirProducto.php" class="addButton">Agregar Producto</a>
      <?php endif; ?>
    </div>
  </article>
</section>
</body>
<footer>
    <p>By Jhojan Danilo</p>
</footer>
</html>