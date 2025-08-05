<?php
include './Data/DataDB.php';

if (!isset($_COOKIE["NameUserM"])) {
    header("Location: ./index.php");
    exit();
}

$conexion = ConexionBD::getInstancia()->getConexion();

class Movimiento {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function contarMovimientos($search) {
        $searchParam = "%$search%";
        $sqlCount = "SELECT COUNT(DISTINCT m.id) as total
                     FROM movimientos m
                     LEFT JOIN detalles_movimiento d ON m.id = d.id_movimiento
                     WHERE m.usuario LIKE ? OR m.accion LIKE ? OR m.fecha LIKE ?";
        $stmtCount = $this->conexion->prepare($sqlCount);
        $stmtCount->bind_param("sss", $searchParam, $searchParam, $searchParam);
        $stmtCount->execute();
        $resCount = $stmtCount->get_result();
        $totalRows = $resCount->fetch_assoc()['total'];
        $stmtCount->close();
        return $totalRows;
    }

    public function obtenerMovimientos($search, $limit, $offset) {
        $searchParam = "%$search%";
        $sql = "SELECT m.id, m.fecha, m.hora, m.usuario, m.accion,
                GROUP_CONCAT(CONCAT(d.id_articulo, ' (', d.cantidad, ')') SEPARATOR ', ') AS articulos
                FROM movimientos m
                LEFT JOIN detalles_movimiento d ON m.id = d.id_movimiento
                WHERE m.usuario LIKE ? OR m.accion LIKE ? OR m.fecha LIKE ?
                GROUP BY m.id
                ORDER BY m.fecha DESC, m.hora DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sssii", $searchParam, $searchParam, $searchParam, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $movimientos = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $movimientos;
    }
}

$movimiento = new Movimiento($conexion);

$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$totalRows = $movimiento->contarMovimientos($search);
$totalPages = ceil($totalRows / $limit);

$resultados = $movimiento->obtenerMovimientos($search, $limit, $offset);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="./CSS/StyleIndex.css" />
    <title>Historial de Movimientos - Inventory+</title>
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
      <h1 class="MainTittle">Historial de Movimientos</h1>
    </div>

    <div class="table-container">

      <form method="GET" action="" class="search-form">
        <input type="text" name="search" placeholder="Buscar por usuario, acción o fecha..." value="<?php echo htmlspecialchars($search); ?>" class="search-input" />
        <button type="submit" class="search-button">Buscar</button>
      </form>

      <?php if (count($resultados) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>ID Movimiento</th>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Usuario</th>
              <th>Acción</th>
              <th>Artículos (ID, Cantidad, Tipo)</th>
              <th>Detalle</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($resultados as $row): ?>
              <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo $row["fecha"]; ?></td>
                <td><?php echo $row["hora"]; ?></td>
                <td><?php echo htmlspecialchars($row["usuario"]); ?></td>
                <td><?php echo htmlspecialchars($row["accion"]); ?></td>
                <td><?php echo htmlspecialchars($row["articulos"]); ?></td>
                <td class="action-buttons">
                  <a href="./MovimientosDetalle.php?id=<?php echo $row["id"]; ?>" class="editButton" title="Ver Detalle">
                    <img src="./IMG/Editar.png" alt="Ver detalle" width="20px" />
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No hay movimientos que coincidan con la búsqueda.</p>
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
        <a href="./Inventario.php" class="addButton">Volver</a>
      <?php endif; ?>
    </div>
  </article>
</section>
</body>
<footer>
    <p>By Jhojan Danilo</p>
</footer>
</html>
