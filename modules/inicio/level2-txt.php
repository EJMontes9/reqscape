<?php
session_start(); // Iniciar la sesión al principio del archivo

include "../../connection/connection.php";

// Verificar si se ha enviado la solicitud POST para cambiar el idioma
if (isset($_POST['change_language'])) {
    // Cambia el idioma en la sesión
    $_SESSION['lang'] = $_POST['language'];
}

// Establecer el idioma predeterminado si no está configurado
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'es'; // Cambia a 'en' si prefieres el inglés por defecto
}

// Cargar el archivo de idioma correspondiente
$lang = [];
if ($_SESSION['lang'] == 'es') {
    $lang['home'] = 'Inicio';
    $lang['levels'] = 'Niveles';
    $lang['score'] = 'Puntuación';
    $lang['profile'] = 'Perfil';
    $lang['info'] = 'Información';
    $lang['logout'] = 'Cerrar sesión';
    $lang['level_2'] = 'Nivel 02';
    $lang['task_2'] = 'TU SEGUNDA TAREA ES ORDENAR EL REQUERIMIENTO NO AMBIGUO';
    $lang['skip'] = 'SALTAR';
    $lang['change_language'] = 'Cambiar idioma';
} else {
    $lang['home'] = 'Home';
    $lang['levels'] = 'Levels';
    $lang['score'] = 'Score';
    $lang['profile'] = 'Profile';
    $lang['info'] = 'Info';
    $lang['logout'] = 'Logout';
    $lang['level_2'] = 'Level 02';
    $lang['task_2'] = 'YOUR SECOND TASK IS TO SORT THE UNAMBIGUOUS REQUIREMENT';
    $lang['skip'] = 'SKIP';
    $lang['change_language'] = 'Change Language';
}

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
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReqScape</title>
    <link rel="stylesheet" type="text/css" href="../../styles/inicio.css">
    <link rel="stylesheet" type="text/css" href="../../styles/level-txt.css">
    <style>
        .mensaje {
            background-color: #1783ff;
            padding: 2%;
            color: #fefefe;
            border-radius: 15px;
        }
        .tooltiptext {
            display: none;
        }
        /* Estilos para el contenedor de la imagen de perfil */
        .profile-pic-container {
            width: 120px;
            height: 120px;
            position: relative;
        }

        .contenedor-img.circular {
            width: 50%;
            height: 0;
            padding-bottom: 100%;
            overflow: hidden;
            border-radius: 10%;
        }

        .usuario-logueado {
            display: flex;
            justify-content: end;
            border-radius: 10%;
            margin: 2%;
            margin-bottom: 0%;
        }

        .profile-data-item {
            font-family: 'Digitalt';
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 3%;
            margin-top: 0%;
            margin-bottom: 1%;
            color: #1E1C69;
        }

        .usuario-logueado img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin: 2% 2%;
            margin-top: 0%;
            margin-left: 0%;
        }
        .fila1-cl1 {
            padding: 7%;
            border-bottom: 1px solid rgba(19, 67, 145, 0.4);
        }
        #languageSelector {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 100%;
            padding: 8%;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            background-color: #fff;
            color: #333;
            cursor: pointer;
            outline: none;
            transition: border-color 0.3s ease;
            text-align: center;
            font-family: 'Digitalt';
            font-weight: lighter;
        }
        #languageSelector:focus {
            border-color: #007BFF;
        }
        #languageSelector option {
            color: #333;
            background-color: #fff;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="fondo">
        <div class="columna-1">
            <div class="fila1-cl1">
                <select id="languageSelector">
                    <option value="en" <?php echo $_SESSION['lang'] == 'en' ? 'selected' : ''; ?>>English</option>
                    <option value="es" <?php echo $_SESSION['lang'] == 'es' ? 'selected' : ''; ?>>Español</option>
                </select>
            </div>
            <div class="fila2-cl1">
                <a href="inicio.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/inicio.png" alt="">
                    <span class="tooltiptext"><?php echo $lang['home']; ?></span>
                </a>
                <a href="niveles-juego.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/niveles.png" alt="">
                    <span class="tooltiptext"><?php echo $lang['levels']; ?></span>
                </a>
                <a href="score-page.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/scoreglobal.png" alt="">
                    <span class="tooltiptext"><?php echo $lang['score']; ?></span>
                </a>
                <a href="perfil.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/perfil.png" alt="">
                    <span class="tooltiptext"><?php echo $lang['profile']; ?></span>
                </a>
                <a href="<?php echo ($_SESSION['perfil'] == 'profesor') ? 'reporte-niveles.php' : 'informacion.php'; ?>" class="lg-cl1">
                    <img src="../../assets/img/inicio/<?php echo ($_SESSION['perfil'] == 'profesor') ? 'report.png' : 'info.png'; ?>" alt="">
                    <span class="tooltiptext" id="info">Información</span>
                </a>
                <a href="inicio-sesion.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/log-out.png" alt="">
                    <span class="tooltiptext"><?php echo $lang['logout']; ?></span>
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
                        <span id="username" class="username-span"><?php echo $row["usuario"]; ?></span>
                    </div>
                    <?php
                    // Mostrar la imagen de perfil si está disponible
                    if (!empty($row["imagen_perfil"])) {
                        echo '<img class="profile-pic" src="' . $row["imagen_perfil"] . '" alt="Imagen de perfil">';
                    } else {
                        echo '<img class="profile-pic" src="../../modules/inicio/uploads/perfil.jpg" alt="Avatar predeterminado">';
                    }
                    ?>
                </div>
            </div>
            <div class="fila2-cl2">
                <div class="contenido-juego">
                    <div class="fila1">
                        <span class="titulo-nivel-txt">
                            <?php echo $lang['level_2']; ?>
                        </span>
                    </div>
                    <div class="fila2">
                        <div class="fila2-1">
                            <span class="texto1-txt">
                                <?php echo $lang['task_2']; ?>
                            </span>
                        </div>
                        <div class="fila2-2">
                            <a href="juego-nivel2.php" class="btn-icono">
                                <?php echo $lang['skip']; ?>
                                <img src="../../assets/img/lever1-txt/saltar_btn.png" alt="Icono" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('languageSelector').addEventListener('change', function() {
            const selectedLanguage = this.value;
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'change_language';
            input.value = '1';

            const langInput = document.createElement('input');
            langInput.type = 'hidden';
            langInput.name = 'language';
            langInput.value = selectedLanguage;

            form.appendChild(input);
            form.appendChild(langInput);
            document.body.appendChild(form);
            form.submit();
        });
    </script>
</body>
</html>
