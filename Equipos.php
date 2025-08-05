<?php
require "./Data/DataDB.php";

class EquipoRepository {
    private mysqli $conexion;

    public function __construct(mysqli $conexion) {
        $this->conexion = $conexion;
    }

    public function contarEquipos(string $search = ''): int {
        $where = '';
        $params = [];
        $types = '';

        if ($search !== '') {
            $searchParam = "%$search%";
            if (is_numeric($search)) {
                $where = " WHERE id = ? OR serial LIKE ? OR placa_proveedor LIKE ? OR asignado_a LIKE ? ";
                $params = [$search, $searchParam, $searchParam, $searchParam];
                $types = "ssss";
            } else {
                $where = " WHERE serial LIKE ? OR placa_proveedor LIKE ? OR asignado_a LIKE ? ";
                $params = [$searchParam, $searchParam, $searchParam];
                $types = "sss";
            }
        }

        $sql = "SELECT COUNT(*) as total FROM Equipos $where";
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

    public function obtenerEquipos(string $search = '', int $offset = 0, int $limit = 10): array {
        $where = '';
        $params = [];
        $types = '';

        if ($search !== '') {
            $searchParam = "%$search%";
            if (is_numeric($search)) {
                $where = " WHERE id = ? OR serial LIKE ? OR placa_proveedor LIKE ? OR asignado_a LIKE ? ";
                $params = [$search, $searchParam, $searchParam, $searchParam];
                $types = "ssss";
            } else {
                $where = " WHERE serial LIKE ? OR placa_proveedor LIKE ? OR asignado_a LIKE ? ";
                $params = [$searchParam, $searchParam, $searchParam];
                $types = "sss";
            }
        }

        $sql = "SELECT id, serial, placa_proveedor, asignado_a FROM Equipos $where ORDER BY id LIMIT ?, ?";
        $params = array_merge($params, [$offset, $limit]);
        $types .= "ii";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $result = $stmt->get_result();
        $equipos = [];

        while ($row = $result->fetch_assoc()) {
            $equipos[] = $row;
        }

        $stmt->close();
        return $equipos;
    }
}

$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10;
$offset = ($page - 1) * $itemsPerPage;

$conexion = ConexionBD::getInstancia()->getConexion();
$repo = new EquipoRepository($conexion);

$totalItems = $repo->contarEquipos($search);
$totalPages = ceil($totalItems / $itemsPerPage);
$equipos = $repo->obtenerEquipos($search, $offset, $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="./CSS/StyleIndex.css" />
    <title>Equipos - Inventory+</title>
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
<body>
<section>
  <article>
    <div class="DivPrincipal">
      <h1 class="MainTittle">Equipos de la Empresa</h1>
    </div>

    <div class="table-container">
      <h1 class="ColorTitle">Listado de Equipos</h1>

      <form method="GET" action="" class="search-form">
        <input type="text" name="search" placeholder="Buscar Equipo..." value="<?php echo htmlspecialchars($search); ?>" class="search-input" />
        <button type="submit" class="search-button">Buscar</button>
      </form>

      <?php if (count($equipos) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Serial</th>
              <th>Placa Proveedor</th>
              <th>Asignado a</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($equipos as $eq): ?>
              <tr>
                <td><?php echo $eq["id"]; ?></td>
                <td><?php echo htmlspecialchars($eq["serial"]); ?></td>
                <td><?php echo htmlspecialchars($eq["placa_proveedor"]); ?></td>
                <td><?php echo htmlspecialchars($eq["asignado_a"]); ?></td>
                <td class="action-buttons">
                    <a href="./EquiposDetalles.php?id=<?php echo $eq["id"]; ?>" class="editButton" title="Ver Detalles">
                        <img src="./IMG/Editar.png" alt="Detalles equipo" width="20px" />
                    </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No hay equipos que coincidan con la búsqueda.</p>
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
    </div>
  </article>
</section>
</body>
<footer>
    <p>By Jhojan Danilo</p>
</footer>
</html>
