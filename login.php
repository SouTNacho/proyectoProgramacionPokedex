<?php
session_start();
include 'conexion.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['pass'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";

    if (empty($errors)) {
        $mysqli = conectar_bd();
        $stmt = $mysqli->prepare("SELECT id_user, nombre, pass FROM usuario WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) {
            $errors[] = "Usuario o contraseña incorrectos.";
        } else {
            $stmt->bind_result($id_user, $nombre, $hash);
            $stmt->fetch();
            if (password_verify($pass, $hash)) {
                // Credenciales correctas
                $_SESSION['id_user'] = $id_user;
                $_SESSION['nombre'] = $nombre;
                header("Location: index.php");
                exit;
            } else {
                $errors[] = "Usuario o contraseña incorrectos.";
            }
        }
        $stmt->close();
        $mysqli->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - PokéGacha</title>
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
        
        <div class="pokedex-body-login">
            <div class="pokedex-screen-large">
                <div class="screen-content-login">
                    <div class="login-title">
                        <div class="login-icon">🔐</div>
                        <h2>Iniciar Sesión</h2>
                        <p>Ingresa a tu cuenta PokéGacha</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="login-errors">
                            <div class="error-icon">⚠️</div>
                            <div class="error-messages">
                                <?php foreach($errors as $e): ?>
                                    <div class="error-message"><?php echo htmlspecialchars($e); ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="login-form">
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
                                   placeholder="Ingresa tu contraseña">
                        </div>
                        <button type="submit" class="login-button">
                            <span class="button-icon">🎮</span>
                            <span class="button-text">ENTRAR AL SISTEMA</span>
                        </button>
                    </form>
                    <div class="login-register">
                        <div class="register-text">¿No tenés cuenta?</div>
                        <a href="register.php" class="register-link">
                            <span class="link-icon">✨</span>
                            <span class="link-text">Regístrate aquí</span>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>