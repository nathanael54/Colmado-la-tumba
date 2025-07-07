<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header('Location: index.php');
  exit;
}

require_once 'conexion.php';

if (isset($_POST['crear'])) {
  $nombre = trim($_POST['nombre']);
  $precio = floatval($_POST['precio']);
  $cantidad = intval($_POST['cantidad']);
  $errores = [];
  if ($nombre === '') $errores[] = 'El nombre es obligatorio.';
  if ($precio < 0) $errores[] = 'El precio no puede ser negativo.';
  if ($cantidad < 0) $errores[] = 'La cantidad no puede ser negativa.';
  if (empty($errores)) {
    $query = "INSERT INTO productos (nombre, precio, cantidad) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sdi', $nombre, $precio, $cantidad);
    mysqli_stmt_execute($stmt);
    header('Location: productos.php?msg=creado');
    exit;
  }
}

if (isset($_GET['eliminar'])) {
  $id = intval($_GET['eliminar']);
  $query = "DELETE FROM productos WHERE id = ?";
  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmt, 'i', $id);
  mysqli_stmt_execute($stmt);
  header('Location: productos.php?msg=eliminado');
  exit;
}

if (isset($_POST['actualizar'])) {
  $id = intval($_POST['id']);
  $nombre = trim($_POST['nombre']);
  $precio = floatval($_POST['precio']);
  $cantidad = intval($_POST['cantidad']);
  $errores = [];
  if ($nombre === '') $errores[] = 'El nombre es obligatorio.';
  if ($precio < 0) $errores[] = 'El precio no puede ser negativo.';
  if ($cantidad < 0) $errores[] = 'La cantidad no puede ser negativa.';
  if (empty($errores)) {
    $query = "UPDATE productos SET nombre = ?, precio = ?, cantidad = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sdii', $nombre, $precio, $cantidad, $id);
    mysqli_stmt_execute($stmt);
    header('Location: productos.php?msg=actualizado');
    exit;
  }
}

$query = "SELECT * FROM productos";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Colmado Rojo - Productos</title>
  <link rel="stylesheet" href="estilos.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="productos">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
      <a href="dashboard.php" style="background:#ff4e50;color:#fff;padding:8px 18px;border-radius:8px;text-decoration:none;font-weight:bold;box-shadow:0 2px 8px rgba(255,78,80,0.08);transition:background 0.2s;">← Volver al Dashboard</a>
      <a href="cerrar-sesion.php" style="background:#b30000;color:#fff;padding:8px 18px;border-radius:8px;text-decoration:none;font-weight:bold;box-shadow:0 2px 8px rgba(255,78,80,0.08);transition:background 0.2s;">Cerrar sesión</a>
    </div>
    <h1><em>Colmado La Tumba</em> - Productos</h1>
    <?php if (!empty($errores)) { ?>
      <div style="background:#ffe0e0;color:#b30000;padding:12px 18px;border-radius:8px;margin-bottom:18px;font-weight:bold;">
        <?php foreach($errores as $err) echo $err.'<br>'; ?>
      </div>
    <?php } ?>
    <?php if (isset($_GET['msg'])) {
      $msg = $_GET['msg'];
      $txt = $msg === 'creado' ? '¡Producto creado exitosamente!' : ($msg === 'eliminado' ? 'Producto eliminado.' : 'Producto actualizado.');
      echo '<div style="background:#e0ffe0;color:#008000;padding:12px 18px;border-radius:8px;margin-bottom:18px;font-weight:bold;">'.$txt.'</div>';
    } ?>
    <div class="card-grid">
      <?php
      // Reiniciar el puntero del resultado para reutilizarlo
      mysqli_data_seek($result, 0);
      $cardIndex = 0;
      while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="product-card" style="--card-delay: <?php echo ($cardIndex * 0.08); ?>s;">
          <h3><?php echo htmlspecialchars($row['nombre']); ?></h3>
          <div class="price">RD$ <?php echo number_format($row['precio'], 2); ?></div>
          <div class="qty">Cantidad: <?php echo $row['cantidad']; ?></div>
          <div class="actions">
            <a href="productos.php?editar=<?php echo $row['id']; ?>">Editar</a>
            <a href="productos.php?eliminar=<?php echo $row['id']; ?>">Eliminar</a>
          </div>
        </div>
      <?php $cardIndex++; } ?>
    </div>
    <h2 style="margin-top:40px; color:#ff4e50;">Agregar nuevo producto</h2>
    <form method="post">
      <input type="text" name="nombre" placeholder="Nombre" required>
      <input type="number" name="precio" placeholder="Precio" step="0.01" min="0" required>
      <input type="number" name="cantidad" placeholder="Cantidad" min="0" required>
      <button type="submit" name="crear">Crear producto</button>
    </form>
    <?php if (isset($_GET['editar'])) { ?>
      <h2 style="color:#f9d423;">Editar producto</h2>
      <form method="post">
        <input type="hidden" name="id" value="<?php echo $_GET['editar']; ?>">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="number" name="precio" placeholder="Precio" step="0.01" min="0" required>
        <input type="number" name="cantidad" placeholder="Cantidad" min="0" required>
        <button type="submit" name="actualizar">Actualizar producto</button>
      </form>
    <?php } ?>
    <h2 style="margin-top:40px; color:#ff4e50;">Gráfico de Stock de Productos</h2>
    <div style="max-width:700px;margin:0 auto 32px auto;background:#fff;padding:24px 18px 12px 18px;border-radius:16px;box-shadow:0 2px 12px rgba(255,78,80,0.08);">
      <canvas id="stockChart"></canvas>
    </div>
  </div>
  <script>
window.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('stockChart').getContext('2d');
  const data = {
    labels: [
      <?php
      mysqli_data_seek($result, 0);
      $labels = [];
      $cantidades = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = json_encode($row['nombre']);
        $cantidades[] = $row['cantidad'];
      }
      echo implode(',', $labels);
      ?>
    ],
    datasets: [{
      label: 'Cantidad en stock',
      data: [<?php echo implode(',', $cantidades); ?>],
      backgroundColor: 'rgba(255,78,80,0.7)',
      borderColor: 'rgba(255,78,80,1)',
      borderWidth: 2,
      borderRadius: 8,
      hoverBackgroundColor: 'rgba(249,212,35,0.8)'
    }]
  };
  new Chart(ctx, {
    type: 'bar',
    data: data,
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        title: { display: false }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
});
</script>
</body>
</html>