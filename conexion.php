<?php
function conectar_bd() {
    $mysqli = new mysqli("localhost", "root", "", "pokegacha");
    if ($mysqli->connect_error) {
        die("Error al conectar: " . $mysqli->connect_error);
    }
    return $mysqli;
}
?>
