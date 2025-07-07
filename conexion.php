<?php
$conn = mysqli_connect('localhost', 'root', '', 'colmado');

if (!$conn) {
  die('Error de conexión: ' . mysqli_connect_error());
}
?>