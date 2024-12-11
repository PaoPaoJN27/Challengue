<?php
require 'conexion.php';

$errors = []; // Inicializar los errores fuera del bloque POST

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $fullName = trim($_POST['full_name']);

    // Validaciones para el usuario
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El correo electrónico no es válido.";
    }

    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres, incluyendo un número y una letra.";
    }

    if (strlen($fullName) < 5) {
        $errors[] = "El nombre completo debe tener al menos 5 caracteres.";
    }

    // Verifica si el correo electrónico ya está registrado
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "El correo electrónico ya está registrado.";
    }
    $stmt->close();

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (email, password, full_name) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashedPassword, $fullName);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Registro exitoso. <a href='login.php'>Inicia sesión aquí</a></p>";
        } else {
            $errors[] = "Hubo un error al registrarse.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="assets/css/estilosLoging.css">
</head>
<body>
    <div class="form-container">
        <h1>Registro de Usuario</h1>

        <!-- Mostrar errores -->
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form action="register.php" method="POST">
            <input type="email" name="email" placeholder="Correo Electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <input type="text" name="full_name" placeholder="Nombre Completo" required>
            <button type="submit">Registrar</button>
            <a href="login.php" class="logout-button">SI ya cuentas con sesión, ingresa aquí</a>
        </form>
    </div>
</body>
</html>