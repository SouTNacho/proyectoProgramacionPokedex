<?php
include '../conexion.php';
$mysqli = conectar_bd();

$id_user = $_GET['id_user'];
$fecha = date('Y-m-d');

$res = $mysqli->query("SELECT * FROM gacha_uso WHERE id_user = $id_user");
if ($res->num_rows == 0) {
  $mysqli->query("INSERT INTO gacha_uso (id_user, fecha, tiros_restantes) VALUES ($id_user, '$fecha', 5)");
}

$row = $mysqli->query("SELECT * FROM gacha_uso WHERE id_user = $id_user")->fetch_assoc();

if ($row['fecha'] != $fecha) {
  $mysqli->query("UPDATE gacha_uso SET fecha = '$fecha', tiros_restantes = 5 WHERE id_user = $id_user");
  $row['tiros_restantes'] = 5;
}

echo json_encode($row);
?>
