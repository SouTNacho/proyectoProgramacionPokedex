<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$fechaHoy = date('Y-m-d');

$mysqli = conectar_bd();

// Revisar tiros del usuario de hoy
$stmt = $mysqli->prepare("SELECT tiros_restantes FROM gacha_uso WHERE id_user = ? AND fecha = ?");
$stmt->bind_param("is", $id_user, $fechaHoy);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $tiros_restantes = $row['tiros_restantes'];
} else {
    // Si no hay registro de hoy, iniciar con 5 tiros
    $stmt_insert = $mysqli->prepare("INSERT INTO gacha_uso (id_user, fecha, tiros_restantes) VALUES (?, ?, 5)");
    $stmt_insert->bind_param("is", $id_user, $fechaHoy);
    $stmt_insert->execute();
    $tiros_restantes = 5;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gacha Pokémon</title>
    <link rel="stylesheet" href="Styles.css">
    <link rel="shortcut icon" href="/src/Poke_Ball.webp" type="image/x-icon">
</head>
<body>
    <div class="pokedex-container">
        <div class="pokedex-header">
            <div class="pokedex-title-section">
                <h1 class="pokedex-title">Gacha Pokémon</h1>
                <div class="pokedex-lights">
                    <div class="light large red"></div>
                    <div class="light medium yellow"></div>
                    <div class="light small green"></div>
                </div>
            </div>
            <button class="pokedex-logout" onclick="window.location.href='logout.php'">Cerrar sesión</button>
        </div>
        
        <div class="pokedex-body-gacha">
            <div class="pokedex-screen-large">
                <div class="screen-header">
                </div>
                <div class="screen-content-gacha">
                    
                    <div class="gacha-stats">
                        <div class="tiros-counter">
                            <div class="counter-label">Tiros Restantes Hoy</div>
                            <div class="counter-number" id="tiros-actuales"><?php echo $tiros_restantes; ?>/5</div>
                        </div>
                    </div>

                    <div class="gacha-machine">
                        <div class="gacha-display" id="gacha-display">
                            <div class="gacha-ready">
                                <div class="gacha-icon">🎡</div>
                                <div class="gacha-ready-text">Presiona el botón para girar la ruleta</div>
                                <div class="gacha-message error" id="mensaje-error" style="display: none;"></div>
                            </div>
                        </div>
                        
                        <div class="gacha-form">
                            <?php if ($tiros_restantes > 0): ?>
                                <button type="button" class="gacha-button" id="tirar-gacha">
                                    <span class="button-spin">🎲</span>
                                    <span class="button-text">TIRAR GACHA</span>
                                    <span class="button-cost">(1 tiro)</span>
                                </button>
                            <?php else: ?>
                                <div class="gacha-button disabled">
                                    <span class="button-spin">🔒</span>
                                    <span class="button-text">SIN TIROS</span>
                                    <span class="button-cost">(Vuelve mañana)</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="gacha-navigation">
                        <button class="pokedex-button secondary" onclick="window.location.href='pokedex.php'">
                            <span class="button-icon">📘</span>
                            <span class="button-text">Ver Pokédex</span>
                        </button>
                        <button class="pokedex-button secondary" onclick="window.location.href='index.php'">
                            <span class="button-icon">🏠</span>
                            <span class="button-text">Volver al Inicio</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('tirar-gacha').addEventListener('click', function() {
            tirarGacha();
        });

        function tirarGacha() {
            const boton = document.getElementById('tirar-gacha');
            const display = document.getElementById('gacha-display');
            const tirosActuales = document.getElementById('tiros-actuales');
            const mensajeError = document.getElementById('mensaje-error');
            
            // Deshabilitar botón durante la petición
            boton.disabled = true;
            boton.innerHTML = '<span class="button-spin">⏳</span><span class="button-text">GIRANDO...</span>';
            
            // Mostrar animación de carga
            display.innerHTML = `
                <div class="gacha-loading">
                    <div class="loading-spin">🎡</div>
                    <div class="loading-text">Buscando Pokémon...</div>
                </div>
            `;

            // Hacer petición
            fetch('procesar_gacha.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar resultado
                    mostrarResultado(data.pokemon, data.nombre, data.nuevo, data.tiros_restantes);
                    tirosActuales.textContent = data.tiros_restantes + '/5';
                    
                    // Si no quedan tiros, deshabilitar botón
                    if (data.tiros_restantes <= 0) {
                        boton.className = 'gacha-button disabled';
                        boton.innerHTML = '<span class="button-spin">🔒</span><span class="button-text">SIN TIROS</span><span class="button-cost">(Vuelve mañana)</span>';
                    } else {
                        // Rehabilitar botón
                        boton.disabled = false;
                        boton.innerHTML = '<span class="button-spin">🎲</span><span class="button-text">TIRAR GACHA</span><span class="button-cost">(1 tiro)</span>';
                    }
                } else {
                    // Mostrar error 
                    mensajeError.textContent = data.message;
                    mensajeError.style.display = 'block';
                    display.innerHTML = `
                        <div class="gacha-ready">
                            <div class="gacha-icon">❌</div>
                            <div class="gacha-ready-text">Error al girar</div>
                        </div>
                    `;
                    
                    // Rehabilitar botón
                    boton.disabled = false;
                    boton.innerHTML = '<span class="button-spin">🎲</span><span class="button-text">TIRAR GACHA</span><span class="button-cost">(1 tiro)</span>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mensajeError.textContent = 'Error de conexión';
                mensajeError.style.display = 'block';
                
                // Rehabilitar botón
                boton.disabled = false;
                boton.innerHTML = '<span class="button-spin">🎲</span><span class="button-text">TIRAR GACHA</span><span class="button-cost">(1 tiro)</span>';
            });
        }

        function mostrarResultado(pokemonId, nombre, nuevo, tirosRestantes) {
            const display = document.getElementById('gacha-display');
            const tipo = nuevo ? 'nuevo' : 'repetido';
            const mensaje = nuevo ? '🎉 ¡NUEVO POKÉMON!' : '🔁 Pokémon Repetido';
            
            display.innerHTML = `
                <div class="pokemon-result">
                    <div class="result-header ${tipo}">${mensaje}</div>
                    <div class="pokemon-image-container">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemonId}.png" 
                             alt="${nombre}" 
                             class="pokemon-gacha">
                        <div class="pokemon-glow"></div>
                    </div>
                    <div class="pokemon-info">
                        <div class="pokemon-number-gacha">#${String(pokemonId).padStart(3, '0')}</div>
                        <div class="pokemon-name-gacha">${nombre}</div>
                    </div>
                </div>
            `;
            
            // Animación
            setTimeout(() => {
                const pokemonImage = document.querySelector('.pokemon-gacha');
                const pokemonGlow = document.querySelector('.pokemon-glow');
                if (pokemonImage && pokemonGlow) {
                    pokemonImage.style.transform = 'scale(1.1)';
                    pokemonGlow.style.opacity = '1';
                    
                    setTimeout(() => {
                        pokemonImage.style.transform = 'scale(1)';
                    }, 500);
                }
            }, 100);
        }
    </script>
</body>
</html>