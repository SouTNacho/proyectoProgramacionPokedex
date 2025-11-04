<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$mysqli = conectar_bd();

// Obtener los Pokémon que ya tiene el usuario
$stmt = $mysqli->prepare("SELECT id_pokemon FROM pokedex WHERE id_user = ? AND obtenido = 1");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

$obtenidos = [];
while ($row = $result->fetch_assoc()) {
    $obtenidos[] = (int)$row['id_pokemon'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pokédex Personal</title>
    <link rel="stylesheet" href="Styles.css">
    <link rel="shortcut icon" href="/src/Poke_Ball.webp" type="image/x-icon">
</head>
<body>
    <div class="pokedex-container">
        <div class="pokedex-header">
            <div class="pokedex-title-section">
                <h1 class="pokedex-title">Pokédex</h1>
                <div class="pokedex-lights">
                    <div class="light large red"></div>
                    <div class="light medium yellow"></div>
                    <div class="light small green"></div>
                </div>
            </div>
            <button class="pokedex-logout" onclick="window.location.href='logout.php'">Cerrar sesión</button>
        </div>
        
        <div class="pokedex-body-pokedex">
            <div class="pokedex-screen-large">
                <div class="screen-content-large">
                    <div class="pokedex-stats-container">
                        <div class="stat-card">
                            <div class="stat-icon">📊</div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo count($obtenidos); ?> / 151</div>
                                <div class="stat-label">Pokémon Obtenidos</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">🎯</div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo round((count($obtenidos) / 151) * 100, 1); ?>%</div>
                                <div class="stat-label">Porcentaje</div>
                            </div>
                        </div>
                    </div>
                    <div class="pokedex-grid-wrapper">
                        <div class="grid" id="pokedex"></div>
                    </div>
                    <div class="pokedex-navigation">
                        <button class="pokedex-button secondary" onclick="window.location.href='gacha.php'">
                            <span class="button-icon">🎲</span>
                            <span class="button-text">Volver al Gacha</span>
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
        // IDs de los Pokémon que el usuario ya tiene
        const obtenidos = <?php echo json_encode($obtenidos); ?>;
        const total_pokemon = 151;
        const contenedor = document.getElementById('pokedex');

        // Nombres de los pokemones para precargar
        const nombres = [
            "Bulbasaur","Ivysaur","Venusaur","Charmander","Charmeleon","Charizard","Squirtle","Wartortle","Blastoise","Caterpie",
            "Metapod","Butterfree","Weedle","Kakuna","Beedrill","Pidgey","Pidgeotto","Pidgeot","Rattata","Raticate",
            "Spearow","Fearow","Ekans","Arbok","Pikachu","Raichu","Sandshrew","Sandslash","Nidoran♀","Nidorina",
            "Nidoqueen","Nidoran♂","Nidorino","Nidoking","Clefairy","Clefable","Vulpix","Ninetales","Jigglypuff","Wigglytuff",
            "Zubat","Golbat","Oddish","Gloom","Vileplume","Paras","Parasect","Venonat","Venomoth","Diglett",
            "Dugtrio","Meowth","Persian","Psyduck","Golduck","Mankey","Primeape","Growlithe","Arcanine","Poliwag",
            "Poliwhirl","Poliwrath","Abra","Kadabra","Alakazam","Machop","Machoke","Machamp","Bellsprout","Weepinbell",
            "Victreebel","Tentacool","Tentacruel","Geodude","Graveler","Golem","Ponyta","Rapidash","Slowpoke","Slowbro",
            "Magnemite","Magneton","Farfetch'd","Doduo","Dodrio","Seel","Dewgong","Grimer","Muk","Shellder",
            "Cloyster","Gastly","Haunter","Gengar","Onix","Drowzee","Hypno","Krabby","Kingler","Voltorb",
            "Electrode","Exeggcute","Exeggutor","Cubone","Marowak","Hitmonlee","Hitmonchan","Lickitung","Koffing","Weezing",
            "Rhyhorn","Rhydon","Chansey","Tangela","Kangaskhan","Horsea","Seadra","Goldeen","Seaking","Staryu",
            "Starmie","Mr. Mime","Scyther","Jynx","Electabuzz","Magmar","Pinsir","Tauros","Magikarp","Gyarados",
            "Lapras","Ditto","Eevee","Vaporeon","Jolteon","Flareon","Porygon","Omanyte","Omastar","Kabuto",
            "Kabutops","Aerodactyl","Snorlax","Articuno","Zapdos","Moltres","Dratini","Dragonair","Dragonite","Mewtwo",
            "Mew"
        ];

        // Cargar Pokédex
        for (let i = 1; i <= total_pokemon; i++) {
            const div = document.createElement('div');
            div.className = 'pokemon-card';
            const obtenido = obtenidos.includes(i);
            const clase = obtenido ? 'obtained' : 'locked';
            const nombre = nombres[i-1] || `Pokémon #${i}`;

            div.innerHTML = `
                <div class="pokemon-number">#${String(i).padStart(3, '0')}</div>
                <a href="detalle_pokemon.php?id=${i}" class="pokemon-image-link">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${i}.png" class="pokemon-image ${clase}">
                </a>
                <div class="pokemon-name">${nombre}</div>
                <div class="pokemon-status ${clase}">${obtenido ? '✔ Obtenido' : '❌ Faltante'}</div>
            `;
            contenedor.appendChild(div);
        }
    </script>
</body>
</html>