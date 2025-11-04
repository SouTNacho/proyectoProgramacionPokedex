const TOTAL_POKEMONES = 151;

// Mostrar Pokédex
async function cargarPokedex() {
  const contenedor = document.getElementById("pokedex");
  if (!contenedor) return;

  const res = await fetch(`backend/obtener_pokedex.php?id_user=${id_user}`);
  const obtenidos = await res.json();

  for (let i = 1; i <= TOTAL_POKEMONES; i++) {
    const r = await fetch(`https://pokeapi.co/api/v2/pokemon/${i}`);
    const data = await r.json();

    const div = document.createElement("div");
    div.className = "pokemon";
    const tiene = obtenidos.includes(i.toString());
    div.innerHTML = `
      <img src="${data.sprites.front_default}" class="${tiene ? "" : "locked"}">
      <p>#${i} ${data.name}</p>
    `;
    contenedor.appendChild(div);
  }
}

// Tirar Gacha
async function tirarGacha() {
  const res = await fetch("backend/tirar_gacha.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id_user=${id_user}`
  });

  const data = await res.json();
  if (data.error) {
    alert(data.error);
    return;
  }

  const pokeRes = await fetch(`https://pokeapi.co/api/v2/pokemon/${data.id_pokemon}`);
  const pokeData = await pokeRes.json();

  document.getElementById("resultado").innerHTML = `
    <h2>¡Obtuviste a ${pokeData.name}!</h2>
    <img src="${pokeData.sprites.front_default}">
  `;
  actualizarTiros();
}

// Mostrar tiros restantes
async function actualizarTiros() {
  const res = await fetch(`backend/reset_diario.php?id_user=${id_user}`);
  const data = await res.json();
  const tiros = document.getElementById("tiros");
  if (tiros) tiros.textContent = `Tiros restantes hoy: ${data.tiros_restantes}`;
}

document.addEventListener("DOMContentLoaded", () => {
  if (document.getElementById("pokedex")) cargarPokedex();
  if (document.getElementById("tirarBtn")) {
    actualizarTiros();
    document.getElementById("tirarBtn").addEventListener("click", tirarGacha);
  }
});
