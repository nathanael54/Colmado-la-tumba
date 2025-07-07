<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header('Location: index.php');
  exit;
}
require_once 'conexion.php';

$total = 0;
$max = 0;
$min = null;
$prom = 0;
$cant = 0;
$productos = [];

$query = "SELECT * FROM productos";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $productos[] = $row;
    $total += $row['cantidad'];
    $cant++;
    if ($min === null || $row['cantidad'] < $min) $min = $row['cantidad'];
    if ($row['cantidad'] > $max) $max = $row['cantidad'];
  }
  $prom = $cant > 0 ? $total / $cant : 0;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Estadísticas de <em>Colmado La Tumba</em></title>
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <div class="productos">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
      <a href="dashboard.php" style="background:#ff4e50;color:#fff;padding:8px 18px;border-radius:8px;text-decoration:none;font-weight:bold;">← Volver al Dashboard</a>
      <a href="productos.php" style="background:#f9d423;color:#222;padding:8px 18px;border-radius:8px;text-decoration:none;font-weight:bold;">Ir a Productos</a>
      <a href="cerrar-sesion.php" style="background:#b30000;color:#fff;padding:8px 18px;border-radius:8px;text-decoration:none;font-weight:bold;">Cerrar sesión</a>
    </div>
    <h1>Estadísticas de <em>Colmado La Tumba</em></h1>
    <div style="display:flex;gap:32px;justify-content:center;margin:32px 0;flex-wrap:wrap;">
      <div class="product-card" style="min-width:180px;">
        <h3>Total de productos</h3>
        <div class="qty" style="font-size:2rem;"><?php echo $total; ?></div>
      </div>
      <div class="product-card" style="min-width:180px;">
        <h3>Promedio en stock</h3>
        <div class="qty" style="font-size:2rem;"><?php echo number_format($prom, 2); ?></div>
      </div>
      <div class="product-card" style="min-width:180px;">
        <h3>Mayor stock</h3>
        <div class="qty" style="font-size:2rem;"><?php echo $max; ?></div>
      </div>
      <div class="product-card" style="min-width:180px;">
        <h3>Menor stock</h3>
        <div class="qty" style="font-size:2rem;"><?php echo $min === null ? 0 : $min; ?></div>
      </div>
    </div>
    <h2 style="color:#ff4e50;">Listado de productos</h2>
    <div class="card-grid">
      <?php foreach($productos as $prod) { ?>
        <div class="product-card">
          <h3><?php echo htmlspecialchars($prod['nombre']); ?></h3>
          <div class="price">RD$ <?php echo number_format($prod['precio'], 2); ?></div>
          <div class="qty">Cantidad: <?php echo $prod['cantidad']; ?></div>
        </div>
      <?php } ?>
    </div>
  </div>
</body>
</html>
