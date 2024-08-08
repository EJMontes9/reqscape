<?php
session_start(); // Iniciar la sesión al principio del archivo

include "../../connection/connection.php";

// Consulta para obtener los datos del usuario
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT usuario, correo, imagen_perfil FROM usuarios WHERE id = $user_id";
    $result = mysqli_query($con, $sql);

    // Verificar si se obtuvieron resultados
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        // Manejar el caso en el que no se obtuvieron resultados
        echo "No se encontraron resultados.";
        exit();
    }
} else {
    echo "No se ha iniciado sesión.";
    exit();
}

// Verificar si se ha enviado el PIN de la sala
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['gamePin'])) {
    $gamePin = mysqli_real_escape_string($con, $_POST['gamePin']);

    // Verificar si el PIN de la sala existe y obtener el nivel correspondiente
    $sql_room = "SELECT nivel FROM room_requirements WHERE room_code = '$gamePin'";
    $result_room = mysqli_query($con, $sql_room);

    if (mysqli_num_rows($result_room) > 0) {
        $row_room = mysqli_fetch_assoc($result_room);
        $nivel = $row_room['nivel'];

        // Depuración: Ver qué valor se obtiene para nivel
        echo "Nivel obtenido: " . htmlspecialchars($nivel, ENT_QUOTES, 'UTF-8') . "<br>";
        echo "Tipo de nivel: " . gettype($nivel) . "<br>";

        // Verificar si el valor de nivel es numérico y válido
        if (is_numeric($nivel)) {
            $nivel = intval($nivel); // Convertir a entero por seguridad

            // Redirigir al juego con el código de la sala según el nivel
            if ($nivel == 1) {
                header("Location: juego-nivel1.php?room_code=$gamePin");
            } elseif ($nivel == 2) {
                header("Location: juego-nivel2.php?room_code=$gamePin");
            } else {
                echo "Nivel de juego no válido.";
            }
        } else {
            echo "Nivel no es numérico.";
        }
        exit();
    } else {
        echo "Código de sala no válido.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReqScape</title>
    <link rel="stylesheet" type="text/css" href="../../styles/inicio.css">
    <link rel="stylesheet" type="text/css" href="../../styles/sala_partida.css">
    <link rel="stylesheet" type="text/css" href="../../styles/sala-multijugador.css">
</head>
<body>
    <div class="fondo">
        <div class="columna-1">
            <div class="fila1-cl1"></div>
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
                <a href="<?php echo ($_SESSION['perfil'] == 'profesor') ? 'reporte-niveles.php' : 'informacion.php'; ?>" class="lg-cl1">
                    <img src="../../assets/img/inicio/<?php echo ($_SESSION['perfil'] == 'profesor') ? 'report.png' : 'info.png'; ?>" alt="">
                    <span class="tooltiptext" id="info">Información</span>
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
                <div class="usuario-logueado">
                    <div class="profile-data-item">
                        <span id="username" class="username-span"><?php echo htmlspecialchars($row["usuario"], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <?php
                    // Mostrar la imagen de perfil si está disponible
                    if (!empty($row["imagen_perfil"])) {
                        echo '<img class="profile-pic" src="' . htmlspecialchars($row["imagen_perfil"], ENT_QUOTES, 'UTF-8') . '" alt="Imagen de perfil">';
                    } else {
                        // Si no hay imagen de perfil, se mostrará el avatar predeterminado
                        echo '<img class="profile-pic" src="../../modules/inicio/uploads/perfil.jpg" alt="Avatar predeterminado">';
                    }
                    ?>
                </div>
            </div>
            <div class="fila2-cl2">
                <div class="contenido-juego">
                    <div class="input-container">
                        <form action="" method="post">
                            <input type="text" name="gamePin" id="gamePin" placeholder="PIN de juego" required>
                            <button type="submit">Ingresar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
