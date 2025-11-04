<?php
include '../conexion.php';
$mysqli = conectar_bd();

$id_user = $_GET['id_user'];

$res = $mysqli->query("SELECT id_pokemon FROM pokedex WHERE id_user = $id_user AND obtenido = 1");
$pokemonIds = [];
while ($row = $res->fetch_assoc()) {
  $pokemonIds[] = $row['id_pokemon'];
}
echo json_encode($pokemonIds);
?>
