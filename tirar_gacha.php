<?php
include '../conexion.php';
$mysqli = conectar_bd();

$id_user = $_POST['id_user'];
$fecha = date('Y-m-d');

// Comprobar si tiene registro de hoy
$res = $mysqli->query("SELECT * FROM gacha_uso WHERE id_user = $id_user");
if ($res->num_rows == 0) {
  $mysqli->query("INSERT INTO gacha_uso (id_user, fecha, tiros_restantes) VALUES ($id_user, '$fecha', 5)");
}
$row = $res->fetch_assoc();

if ($row && $row['fecha'] != $fecha) {
  $mysqli->query("UPDATE gacha_uso SET fecha = '$fecha', tiros_restantes = 5 WHERE id_user = $id_user");
  $row['tiros_restantes'] = 5;
}

// Verificar si le quedan tiros al user
if ($row && $row['tiros_restantes'] <= 0) {
  echo json_encode(["error" => "No tienes más tiros hoy"]);
  exit;
}

// Generar Pokémon aleatorio
$total = 151;
$id_pokemon = rand(1, $total);

// Guardar en pokedex
$check = $mysqli->query("SELECT * FROM pokedex WHERE id_user = $id_user AND id_pokemon = $id_pokemon");
if ($check->num_rows == 0) {
  $mysqli->query("INSERT INTO pokedex (id_user, id_pokemon, obtenido) VALUES ($id_user, $id_pokemon, 1)");
} else {
  $mysqli->query("UPDATE pokedex SET obtenido = 1 WHERE id_user = $id_user AND id_pokemon = $id_pokemon");
}

// Restar tiro
$mysqli->query("UPDATE gacha_uso SET tiros_restantes = tiros_restantes - 1 WHERE id_user = $id_user");

echo json_encode(["id_pokemon" => $id_pokemon]);
?>
