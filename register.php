<?php
session_start();
include 'conexion.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['pass'] ?? '';
    $pass2 = $_POST['pass2'] ?? '';

    if ($nombre === '') $errors[] = "El nombre es obligatorio.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";
    if (strlen($pass) < 6) $errors[] = "La contraseña debe tener al menos 6 caracteres.";
    if ($pass !== $pass2) $errors[] = "Las contraseñas no coinciden.";

    if (empty($errors)) {
        $mysqli = conectar_bd();

        // Verificar si existe email
        $stmt = $mysqli->prepare("SELECT id_user FROM usuario WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "El email ya está registrado.";
            $stmt->close();
        } else {
            $stmt->close();
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO usuario (nombre, email, pass) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $nombre, $email, $hash);
            if ($stmt->execute()) {
                $id_user = $stmt->insert_id;
                // Iniciar sesión automáticamente
                $_SESSION['id_user'] = $id_user;
                $_SESSION['nombre'] = $nombre;
                header("Location: index.php");
                exit;
            } else {
                $errors[] = "Error al registrar. Intenta de nuevo.";
            }
            $stmt->close();
        }
        $mysqli->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - PokéGacha</title>
    <link rel="stylesheet" href="Styles.css">
    <link rel="shortcut icon" href="/src/Poke_Ball.webp" type="image/x-icon">
</head>
<body>
    <div class="pokedex-container">
        <div class="pokedex-header">
            <div class="pokedex-title-section">
                <h1 class="pokedex-title">PokéGacha</h1>
                <div class="pokedex-lights">
                    <div class="light large red"></div>
                    <div class="light medium yellow"></div>
                    <div class="light small green"></div>
                </div>
            </div>
        </div>
        
        <div class="pokedex-body-register">
            <div class="pokedex-screen-large">
                <div class="screen-content-register">
                    <div class="register-title">
                        <div class="register-icon">✨</div>
                        <h2>Crear Cuenta</h2>
                        <p>Únete a la aventura PokéGacha</p>
                    </div>

                    <!-- Mensajes de error -->
                    <?php if (!empty($errors)): ?>
                        <div class="register-errors">
                            <div class="error-icon">⚠️</div>
                            <div class="error-messages">
                                <?php foreach($errors as $e): ?>
                                    <div class="error-message"><?php echo htmlspecialchars($e); ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" class="register-form">
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">👤</span>
                                <span class="label-text">Nombre de Entrenador</span>
                            </label>
                            <input type="text" name="nombre" required 
                                   value="<?php echo htmlspecialchars($nombre ?? ''); ?>"
                                   class="form-input"
                                   placeholder="Tu nombre de entrenador Pokémon">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">📧</span>
                                <span class="label-text">Email</span>
                            </label>
                            <input type="email" name="email" required 
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                   class="form-input"
                                   placeholder="tupokemon@entrenador.com">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">🔒</span>
                                <span class="label-text">Contraseña</span>
                            </label>
                            <input type="password" name="pass" required 
                                   class="form-input"
                                   placeholder="Mínimo 6 caracteres">
                            <div class="form-hint">La contraseña debe tener al menos 6 caracteres</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">🔁</span>
                                <span class="label-text">Repetir Contraseña</span>
                            </label>
                            <input type="password" name="pass2" required 
                                   class="form-input"
                                   placeholder="Confirma tu contraseña">
                        </div>

                        <button type="submit" class="register-button">
                            <span class="button-icon">🎮</span>
                            <span class="button-text">CREAR CUENTA</span>
                        </button>
                    </form>
                    <div class="register-login">
                        <div class="login-text">¿Ya tenés cuenta?</div>
                        <a href="login.php" class="login-link">
                            <span class="link-icon">🔐</span>
                            <span class="link-text">Iniciar Sesión</span>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>