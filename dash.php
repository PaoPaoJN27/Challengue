<?php
session_start();

// Tiempo límite de inactividad (15 minutos)
$timeout = 15 * 60; 

// Verificar la última actividad
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    // Cerrar sesión por inactividad
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=true");
    exit;
} else {
    $_SESSION['last_activity'] = time(); // Actualizar la última actividad
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido</title>
    <link rel="stylesheet" href="assets/css/estilosLoging.css">
</head>
<body class="welcome-page">
    <div class="welcome-container">
        <h1 class="welcome-title">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </div>
</body>
</html>
