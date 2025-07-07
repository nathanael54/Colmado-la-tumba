<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header('Location: index.php');
  exit;
}

require_once 'conexion.php';

// Obtener productos para mostrar en cards
$productos_cards = [];
$productos_query = "SELECT * FROM productos";
$productos_result = mysqli_query($conn, $productos_query);
if ($productos_result && mysqli_num_rows($productos_result) > 0) {
  while ($prod = mysqli_fetch_assoc($productos_result)) {
    $productos_cards[] = $prod;
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title><em>Colmado La Tumba</em> - Dashboard</title>
  <link rel="stylesheet" href="estilos.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
  <div class="dashboard">
    <header>
      <h1><i class="fas fa-store"></i> <em>Colmado La Tumba</em></h1>
      <nav>
        <ul>
          <li><a href="productos.php"><i class="fas fa-box"></i> Productos</a></li>
          <li><a href="estadisticas.php"><i class="fas fa-chart-bar"></i> Estadísticas</a></li>
          <li><a href="cerrar-sesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
      </nav>
    </header>
    <main>
      <h2>Bienvenido, <?php echo $_SESSION['usuario']; ?></h2>
      <p>Este es el panel de control de <em>Colmado La Tumba</em>. Aquí puedes gestionar tus productos y realizar otras tareas administrativas.</p>
      <?php if (count($productos_cards) > 0) { ?>
        <div class="card-grid">
          <?php foreach ($productos_cards as $prod) { ?>
            <div class="product-card">
              <h3><?php echo htmlspecialchars($prod['nombre']); ?></h3>
              <div class="price">RD$ <?php echo number_format($prod['precio'], 2); ?></div>
              <div class="qty">Cantidad: <?php echo $prod['cantidad']; ?></div>
              <div class="actions">
                <a href="productos.php?editar=<?php echo $prod['id']; ?>">Editar</a>
                <a href="productos.php?eliminar=<?php echo $prod['id']; ?>">Eliminar</a>
              </div>
            </div>
          <?php } ?>
        </div>
      <?php } else { ?>
        <p style="margin-top:32px;">No hay productos registrados aún.</p>
      <?php } ?>
    </main>
  </div>
</body>
</html>