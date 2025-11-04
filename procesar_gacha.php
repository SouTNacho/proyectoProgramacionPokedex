<?php
session_start();
include 'conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'No hay sesión activa']);
    exit();
}

$id_user = $_SESSION['id_user'];
$fechaHoy = date('Y-m-d');

$mysqli = conectar_bd();

// Revisar tiros del usuario para hoy
$stmt = $mysqli->prepare("SELECT tiros_restantes FROM gacha_uso WHERE id_user = ? AND fecha = ?");
$stmt->bind_param("is", $id_user, $fechaHoy);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $tiros_restantes = $row['tiros_restantes'];
} else {
    $stmt_insert = $mysqli->prepare("INSERT INTO gacha_uso (id_user, fecha, tiros_restantes) VALUES (?, ?, 5)");
    $stmt_insert->bind_param("is", $id_user, $fechaHoy);
    $stmt_insert->execute();
    $tiros_restantes = 5;
}

// Verificar si hay tiros disponibles
if ($tiros_restantes <= 0) {
    echo json_encode(['success' => false, 'message' => 'Ya usaste tus 5 tiros de hoy. Vuelve mañana!']);
    exit();
}

// Generar Pokémon aleatorio entre 1 y 151
$id_pokemon = rand(1, 151);

// Guardar en Pokédex si no existe
$check = $mysqli->prepare("SELECT * FROM pokedex WHERE id_user = ? AND id_pokemon = ?");
$check->bind_param("ii", $id_user, $id_pokemon);
$check->execute();
$check_result = $check->get_result();

$nuevo_pokemon = false;
if ($check_result->num_rows == 0) {
    $insert_poke = $mysqli->prepare("INSERT INTO pokedex (id_user, id_pokemon, obtenido) VALUES (?, ?, 1)");
    $insert_poke->bind_param("ii", $id_user, $id_pokemon);
    $insert_poke->execute();
    $nuevo_pokemon = true;
} else {
    $update_poke = $mysqli->prepare("UPDATE pokedex SET obtenido = 1 WHERE id_user = ? AND id_pokemon = ?");
    $update_poke->bind_param("ii", $id_user, $id_pokemon);
    $update_poke->execute();
    $nuevo_pokemon = false;
}

// Restar un tiro
$stmt_update = $mysqli->prepare("UPDATE gacha_uso SET tiros_restantes = tiros_restantes - 1 WHERE id_user = ? AND fecha = ?");
$stmt_update->bind_param("is", $id_user, $fechaHoy);
$stmt_update->execute();

$tiros_restantes -= 1;

// Devolver resultado
echo json_encode([
    'success' => true,
    'pokemon' => $id_pokemon,
    'nombre' => $nombres_pokemon[$id_pokemon] ?? "Pokémon #$id_pokemon",
    'nuevo' => $nuevo_pokemon,
    'tiros_restantes' => $tiros_restantes
]);
?>