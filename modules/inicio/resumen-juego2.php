<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$translations = [
    'info' => 'Información',
];

$infoText = isset($translations['info']) ? $translations['info'] : 'Info';

// Your existing HTML and PHP code
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReqScape</title>
    <link rel="stylesheet" type="text/css" href="../../styles/inicio.css">
    <link rel="stylesheet" type="text/css" href="../../styles/resumen-juego.css">
    <link rel="stylesheet" type="text/css" href="../../styles/lenguaje.css">
    <style>
        .btn-juego-principal2 {
            display: grid;
            align-self: stretch;
            align-items: center;
            flex-wrap: nowrap;
            justify-content: center;
            margin-bottom: 6%;
            margin: 2%;
        }
    </style>
</head>
<body>
<div class="fondo">
    <div class="columna-1">
        <div class="fila1-cl1">
            <select id="languageSelector">
                <option value="en">English</option>
                <option value="es">Español</option>
            </select>
        </div>
        <div class="fila2-cl1">
            <a href="inicio.php" class="lg-cl1">
                <img src="../../assets/img/inicio/inicio.png" alt="">
                <span class="tooltiptext">Home</span>
            </a>
            <a href="niveles-juego.php" class="lg-cl1">
                <img src="../../assets/img/inicio/niveles.png" alt="">
                <span class="tooltiptext">Levels</span>
            </a>
            <a href="score-page.php" class="lg-cl1">
                <img src="../../assets/img/inicio/scoreglobal.png" alt="">
                <span class="tooltiptext">Score</span>
            </a>
            <a href="perfil.php" class="lg-cl1">
                <img src="../../assets/img/inicio/perfil.png" alt="">
                <span class="tooltiptext">Profile</span>
            </a>
            <a href="<?php echo (isset($_SESSION['perfil']) && $_SESSION['perfil'] == 'profesor') ? 'reporte-niveles.php' : 'informacion.php'; ?>"
               class="lg-cl1">
                <img src="../../assets/img/inicio/<?php echo (isset($_SESSION['perfil']) && $_SESSION['perfil'] == 'profesor') ? 'report.png' : 'info.png'; ?>"
                     alt="">
                <span class="tooltiptext" data-translate="info"><?php echo $infoText; ?></span>
            </a>
            <a href="inicio-sesion.php" class="lg-cl1">
                <img src="../../assets/img/inicio/log-out.png" alt="">
                <span class="tooltiptext">Logout</span>
            </a>
        </div>
    </div>
    <div class="columna-2">
        <div class="fila1-cl2">
            <div class="logo">
                <img src="../../assets/img/logo.png" alt="">
            </div>
        </div>
        <div class="fila2-cl2">
            <div class="contenido-juego">
                <div class="btn-juego-principal2">
                    <div class="container">
                        <?php
                        // Verificar si se ha enviado el score desde el formulario
                        if (isset($_GET['score'])) {
                        // Obtener el puntaje numérico eliminando el texto "Score: "
                        $score = (int)$_GET['score'];

                        // Validar el puntaje como un número antes de procesarlo
                        if ($score > 0) {
                            include "../../connection/connection.php"; // Incluir el archivo de conexión a la base de datos

                            // Verificar si el usuario está logueado
                            if (isset($_SESSION['usuario'])) {
                                // Obtener el correo electrónico del usuario logueado
                                $correo_usuario = $_SESSION['usuario'];

                                // Obtener el ID del usuario utilizando el correo electrónico
                                $sql_user = "SELECT id FROM usuarios WHERE correo = '$correo_usuario'";
                                $result_user = mysqli_query($con, $sql_user);
                                if (mysqli_num_rows($result_user) > 0) {
                                    $row_user = mysqli_fetch_assoc($result_user);
                                    $user_id = $row_user['id'];

                                    // Escapar las variables para evitar inyección de SQL
                                    $user_id = mysqli_real_escape_string($con, $user_id);
                                    $score = mysqli_real_escape_string($con, $score);

                                    // Obtener el room_code de la sesión, si existe
                                    $room_code = isset($_SESSION['room_code']) ? mysqli_real_escape_string($con, $_SESSION['room_code']) : '';

                                    // Insertar el puntaje en la tabla puntajes con el nivel correspondiente
                                    $nivel = 1; // Nivel 1
                                    $sql = "INSERT INTO puntajes (user_id, puntaje, nivel, room_code) VALUES ('$user_id', '$score', '$nivel', '$room_code')";
                                    if (mysqli_query($con, $sql)) {
                                        echo "<p class='score'>¡Tu score actual es: " . htmlspecialchars($score) . "!</p>";

                                        // Mostrar el código de sala solo si no está vacío
                                        if (!empty($room_code)) {
                                            echo "<p class='room_code'>Código de Sala: " . htmlspecialchars($room_code) . "</p>";
                                        }

                                        // Determinar si el jugador puede avanzar al siguiente nivel
                                        if ($score >= 1) { // Cambiar aquí el puntaje necesario para avanzar al siguiente nivel
                                            echo "<p class='message'>¡Felicidades! ¡Puedes avanzar al siguiente nivel!</p>";
                                            // Mostrar el botón solo si el puntaje es suficiente
                                            echo '<div class="score-container">';
                                            echo '<a class="button_siguiente" href="level2-txt.php">Ir al siguiente nivel</a>';
                                            echo '</div>';
                                        } else {
                                            echo "<p class='message'>Tu score no es suficiente para avanzar al siguiente nivel.</p>";
                                        }
                                    } else {
                                        echo "<p class='message'>Error al guardar el puntaje en la base de datos.</p>";
                                    }
                                } else {
                                    echo "<p class='message'>El usuario no fue encontrado.</p>";
                                }
                            } else {
                                echo "<p class='message'>Debe iniciar sesión para acceder a esta página.</p>";
                            }

                            // Cerrar conexión
                            mysqli_close($con);
                        } else {
                            echo "<p class='message'>El puntaje enviado no es válido.</p>";
                        }
                    } else {
                        echo "<p class='message'>No se ha enviado el score del juego.</p>";
                    }
                    ?>

                    <div class="score-container">
                        <div class="score-content">
                            <a class="btn_practica" href="juego-nivel2.php">Volver al juego</a>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>