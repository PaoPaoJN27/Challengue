<?php
session_start();
require 'conexion.php';

$errors = []; // Inicializamos el array de errores

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Obtener información del usuario
    $stmt = $conn->prepare("SELECT id, password, full_name, failed_attempts, blocked_until FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashedPassword, $fullName, $failedAttempts, $blockedUntil);
        $stmt->fetch();

        // Verificar si el usuario está bloqueado
        if ($blockedUntil && strtotime($blockedUntil) > time()) {
            $remainingTime = ceil((strtotime($blockedUntil) - time()) / 60);
            $errors[] = "Usuario bloqueado. Intenta de nuevo en $remainingTime minutos.";
        } else {
            // Verificar contraseña
            if (password_verify($password, $hashedPassword)) {
                // Restablecer intentos fallidos y desbloquear usuario
                $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, blocked_until = NULL WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();

                // Iniciar sesión
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $fullName;
                $_SESSION['last_activity'] = time(); // Para el control de inactividad
                header("Location: dash.php");
                exit;
            } else {
                // Incrementar intentos fallidos
                $failedAttempts++;
                if ($failedAttempts >= 3) {
                    $blockedUntil = date("Y-m-d H:i:s", strtotime("+2 hours"));
                    $stmt = $conn->prepare("UPDATE users SET failed_attempts = ?, blocked_until = ? WHERE id = ?");
                    $stmt->bind_param("isi", $failedAttempts, $blockedUntil, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE users SET failed_attempts = ? WHERE id = ?");
                    $stmt->bind_param("ii", $failedAttempts, $id);
                }
                $stmt->execute();

                $errors[] = "Contraseña incorrecta. Intentos restantes: " . (3 - $failedAttempts) . ".";
            }
        }
    } else {
        $errors[] = "Usuario no encontrado.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/css/estilosLoging.css">
</head>
<body>
    <div class="form-container">
        <h1>Iniciar Sesión</h1>

        <!-- Mostrar errores -->
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Correo Electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>

