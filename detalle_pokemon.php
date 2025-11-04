<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$pokemon_id = $_GET['id'] ?? 0;

if ($pokemon_id < 1 || $pokemon_id > 151) {
    header("Location: pokedex.php");
    exit();
}

$mysqli = conectar_bd();
$stmt = $mysqli->prepare("SELECT obtenido FROM pokedex WHERE id_user = ? AND id_pokemon = ?");
$stmt->bind_param("ii", $id_user, $pokemon_id);
$stmt->execute();
$result = $stmt->get_result();

$obtenido = false;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $obtenido = (bool)$row['obtenido'];
}

$nombres_pokemon = [
    1 => "Bulbasaur", 2 => "Ivysaur", 3 => "Venusaur", 4 => "Charmander", 5 => "Charmeleon", 6 => "Charizard", 7 => "Squirtle", 8 => "Wartortle", 9 => "Blastoise", 10 => "Caterpie",
    11 => "Metapod", 12 => "Butterfree", 13 => "Weedle", 14 => "Kakuna", 15 => "Beedrill", 16 => "Pidgey", 17 => "Pidgeotto", 18 => "Pidgeot", 19 => "Rattata", 20 => "Raticate",
    21 => "Spearow", 22 => "Fearow", 23 => "Ekans", 24 => "Arbok", 25 => "Pikachu", 26 => "Raichu", 27 => "Sandshrew", 28 => "Sandslash", 29 => "Nidoran♀", 30 => "Nidorina",
    31 => "Nidoqueen", 32 => "Nidoran♂", 33 => "Nidorino", 34 => "Nidoking", 35 => "Clefairy", 36 => "Clefable", 37 => "Vulpix", 38 => "Ninetales", 39 => "Jigglypuff", 40 => "Wigglytuff",
    41 => "Zubat", 42 => "Golbat", 43 => "Oddish", 44 => "Gloom", 45 => "Vileplume", 46 => "Paras", 47 => "Parasect", 48 => "Venonat", 49 => "Venomoth", 50 => "Diglett",
    51 => "Dugtrio", 52 => "Meowth", 53 => "Persian", 54 => "Psyduck", 55 => "Golduck", 56 => "Mankey", 57 => "Primeape", 58 => "Growlithe", 59 => "Arcanine", 60 => "Poliwag",
    61 => "Poliwhirl", 62 => "Poliwrath", 63 => "Abra", 64 => "Kadabra", 65 => "Alakazam", 66 => "Machop", 67 => "Machoke", 68 => "Machamp", 69 => "Bellsprout", 70 => "Weepinbell",
    71 => "Victreebel", 72 => "Tentacool", 73 => "Tentacruel", 74 => "Geodude", 75 => "Graveler", 76 => "Golem", 77 => "Ponyta", 78 => "Rapidash", 79 => "Slowpoke", 80 => "Slowbro",
    81 => "Magnemite", 82 => "Magneton", 83 => "Farfetch'd", 84 => "Doduo", 85 => "Dodrio", 86 => "Seel", 87 => "Dewgong", 88 => "Grimer", 89 => "Muk", 90 => "Shellder",
    91 => "Cloyster", 92 => "Gastly", 93 => "Haunter", 94 => "Gengar", 95 => "Onix", 96 => "Drowzee", 97 => "Hypno", 98 => "Krabby", 99 => "Kingler", 100 => "Voltorb",
    101 => "Electrode", 102 => "Exeggcute", 103 => "Exeggutor", 104 => "Cubone", 105 => "Marowak", 106 => "Hitmonlee", 107 => "Hitmonchan", 108 => "Lickitung", 109 => "Koffing", 110 => "Weezing",
    111 => "Rhyhorn", 112 => "Rhydon", 113 => "Chansey", 114 => "Tangela", 115 => "Kangaskhan", 116 => "Horsea", 117 => "Seadra", 118 => "Goldeen", 119 => "Seaking", 120 => "Staryu",
    121 => "Starmie", 122 => "Mr. Mime", 123 => "Scyther", 124 => "Jynx", 125 => "Electabuzz", 126 => "Magmar", 127 => "Pinsir", 128 => "Tauros", 129 => "Magikarp", 130 => "Gyarados",
    131 => "Lapras", 132 => "Ditto", 133 => "Eevee", 134 => "Vaporeon", 135 => "Jolteon", 136 => "Flareon", 137 => "Porygon", 138 => "Omanyte", 139 => "Omastar", 140 => "Kabuto",
    141 => "Kabutops", 142 => "Aerodactyl", 143 => "Snorlax", 144 => "Articuno", 145 => "Zapdos", 146 => "Moltres", 147 => "Dratini", 148 => "Dragonair", 149 => "Dragonite", 150 => "Mewtwo",
    151 => "Mew"
];
$nombre_pokemon = $nombres_pokemon[$pokemon_id] ?? "Pokémon #$pokemon_id";

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>#<?php echo $pokemon_id; ?> <?php echo $nombre_pokemon; ?></title>
    <link rel="stylesheet" href="detalleStyles.css">
    <link rel="shortcut icon" href="/src/Poke_Ball.webp" type="image/x-icon">
</head>
<body>
    <div class="pokedex-container">
        <div class="pokedex-header">
            <div class="pokedex-title-section">
                <h1 class="pokedex-title">Detalle Pokémon</h1>
                <div class="pokedex-lights">
                    <div class="light large red"></div>
                    <div class="light medium yellow"></div>
                    <div class="light small green"></div>
                </div>
            </div>
            <button class="pokedex-logout" onclick="window.location.href='logout.php'">Cerrar sesión</button>
        </div>
    <div class="fondo">
        <div class="pokedex-body">
            <div class="pokedex-screen-large">
                <div class="screen-content-large">
                    <div class="pokemon-detail-container">
                        <div class="pokemon-detail-info">
                            <div class="pokemon-detail-number">#<?php echo str_pad($pokemon_id, 3, '0', STR_PAD_LEFT); ?></div>
                            <div class="pokemon-detail-name"><?php echo $nombre_pokemon; ?></div>
                            
                            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/<?php echo $pokemon_id; ?>.png" 
                                 alt="<?php echo $nombre_pokemon; ?>" 
                                 class="pokemon-detail-image <?php echo $obtenido ? 'obtained' : 'locked'; ?>">
                            
                            <div class="pokemon-detail-status <?php echo $obtenido ? 'status-obtenido' : 'status-faltante'; ?>">
                                <?php echo $obtenido ? '✔️ Obtenido' : '✖️ Faltante'; ?>
                            </div>
                            
                            <?php if ($obtenido): ?>
                                <div class="detail-message">
                                    <p class="info-txt">¡Felicidades! Has obtenido este Pokémon.</p>
                                    <div id="pokemon-stats-container" class="pokemon-stats-container">
                                        <div class="loading-details"></div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="detail-message">
                                    <p>Aún no has obtenido este Pokémon.</p>
                                    <p>Sigue jugando al Gacha para conseguirlo.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="detail-actions">
                            <button class="pokedex-button secondary" onclick="window.location.href='pokedex.php'">
                                <span class="button-icon">📘</span>
                                <span class="button-text">Volver a Pokédex</span>
                            </button>
                            <button class="pokedex-button secondary" onclick="window.location.href='gacha.php'">
                                <span class="button-icon">🎲</span>
                                <span class="button-text">Jugar Gacha</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    
    <?php if ($obtenido): ?>
    <script>

        document.addEventListener('DOMContentLoaded', function() {

            const pokemonId = <?php echo $pokemon_id; ?>;
            loadPokemonStats(pokemonId);

        });

        async function loadPokemonStats(pokemonId) {

            try {

                const response = await fetch(`https://pokeapi.co/api/v2/pokemon/${pokemonId}`);
                const pokemon = await response.json();
                displayPokemonStats(pokemon);

            } catch (error) {

                document.getElementById('pokemon-stats-container').innerHTML = 
                    '<p>Error al cargar las estadísticas.</p>';

            }

        }

        function displayPokemonStats(pokemon) {

            const container = document.getElementById('pokemon-stats-container');
            const stats = pokemon.stats;
            const statNames = {
                'hp': 'HP',
                'attack': 'Ataque',
                'defense': 'Defensa', 
                'special-attack': 'Ataque Especial',
                'special-defense': 'Defensa Especial',
                'speed': 'Velocidad'
            };

            let statsHTML = '<h3>Estadísticas</h3><div class="stats-grid">';
            stats.forEach(statInfo => {
                const statName = statNames[statInfo.stat.name];
                const baseStat = statInfo.base_stat;
                
                if (statName) {
                    statsHTML += `
                        <div class="stat-item">
                            <div class="stat-label">${statName}</div>
                            <div class="stat-bar-container">
                                <div class="stat-bar" style="width: 0%" data-value="${baseStat}"></div>
                            </div>
                            <div class="stat-value">${baseStat}</div>
                        </div>
                    `;
                }
            });
            
            statsHTML += '</div>';
            container.innerHTML = statsHTML;
            setTimeout(animateStatBars, 300);

        }

        function animateStatBars() {
            const statBars = document.querySelectorAll('.stat-bar');
            
            statBars.forEach(bar => {
                const value = parseInt(bar.getAttribute('data-value'));
                const percentage = Math.min((value / 150) * 100, 100);
                bar.style.width = percentage + '%';
            });
        }
    </script>
    <?php endif; ?>
</body>
</html>