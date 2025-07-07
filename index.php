<?php
session_start();
if (isset($_SESSION['usuario'])) {
  header('Location: dashboard.php');
  exit;
}

if (isset($_POST['login'])) {
  $usuario = $_POST['usuario'];
  $contraseña = $_POST['contraseña'];

  require_once 'conexion.php';
  $query = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND contraseña = '$contraseña'";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) > 0) {
    $_SESSION['usuario'] = $usuario;
    header('Location: dashboard.php');
    exit;
  } else {
    echo '<script>alert("Usuario o contraseña incorrecta");</script>';
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title><em>Colmado La Tumba</em></title>
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <div class="login" style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:80vh;">
    <h1><em>Colmado La Tumba</em></h1>
    <form method="post" style="width:100%;display:flex;flex-direction:column;align-items:center;">
      <input type="text" name="usuario" placeholder="Usuario" style="margin-bottom:16px;max-width:260px;">
      <input type="password" name="contraseña" placeholder="Contraseña" style="margin-bottom:16px;max-width:260px;">
      <button type="submit" name="login" style="max-width:260px;">Iniciar sesión</button>
    </form>
  </div>
</body>
</html>