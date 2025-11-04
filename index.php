<?php
session_start();
include 'conexion.php'; 

// Si no hay usuario logueado, redirigir al login
if (!isset($_SESSION['id_user'])) {
  header("Location: login.php");
  exit();
}

$id_user = $_SESSION['id_user'];

// Conexión a la base de datos
$mysqli = conectar_bd();

// Fecha actual
$fechaHoy = date('Y-m-d');

// Verificar registros del gacha_uso
$query = "SELECT tiros_restantes FROM gacha_uso WHERE id_user = ? AND fecha = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("is", $id_user, $fechaHoy);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $tiros_restantes = $row['tiros_restantes'];
} else {
  // Si no tiene registro hoy, simplemente mostrar 5
  $tiros_restantes = 5;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pokémon Gacha</title>
  <link rel="stylesheet" href="Styles.css">
  <link rel="shortcut icon" href="/src/Poke_Ball.webp" type="image/x-icon">
</head>
<body>
  <div class="pokedex-container">
    <div class="pokedex-header">
      <div class="pokedex-title-section">
        <h1 class="pokedex-title">PokéIndex</h1>
        <div class="pokedex-lights">
          <div class="light large red"></div>
          <div class="light medium yellow"></div>
          <div class="light small green"></div>
        </div>
      </div>
      <button class="pokedex-logout" onclick="window.location.href='logout.php'">Cerrar sesión</button>
    </div>
    
    <div class="pokedex-body">
      <div class="pokedex-screen">
        <div class="screen-header">
        </div>
        <div class="screen-content">
          <div class="contador-pokedex">
            <div class="screen-text">Tiros restantes de Hoy:</div>
            <div class="screen-number"><?php echo $tiros_restantes; ?></div>
          </div>
          <div class="botones-pokedex">
            <button class="pokedex-button" onclick="window.location.href='gacha.php'">
              <span class="button-icon">🎮</span>
              <span class="button-text">Tirar Gacha</span>
            </button>
            <button class="pokedex-button" onclick="window.location.href='pokedex.php'">
              <span class="button-icon">🏆</span>
              <span class="button-text">Ver Pokédex</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>