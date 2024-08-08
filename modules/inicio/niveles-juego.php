<?php
session_start();

// Verificar si ya hay un nivel desbloqueado almacenado en la sesión
if (!isset($_SESSION['nivelDesbloqueado'])) {
    // Si no hay, establecer el nivel inicial desbloqueado como 1
    $_SESSION['nivelDesbloqueado'] = 1;
}

// Simulación de niveles y sus URLs correspondientes
$levels = array(
    "Nivel 1" => "level1-txt.php",
    "Nivel 2" => "level2-txt.php"
);

// Verificar si el usuario es un profesor
$isProfesor = isset($_SESSION['perfil']) && $_SESSION['perfil'] === 'profesor';

// Modificar las URLs de los niveles si el usuario es profesor
if ($isProfesor) {
    $levels = array(
        "Nivel 1" => "perfil-docente_1.php",
        "Nivel 2" => "perfil-docente_2.php"
    );
}

// Establecer el idioma predeterminado
$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'es';

// Definir las traducciones
$translations = [
    'en' => [
        'home' => 'Home',
        'levels' => 'Levels',
        'score' => 'Score',
        'profile' => 'Profile',
        'info' => 'Info',
        'logout' => 'Logout',
        'level_heading' => 'Levels',
        'back_button' => 'Back'
    ],
    'es' => [
        'home' => 'Inicio',
        'levels' => 'Niveles',
        'score' => 'Puntuación',
        'profile' => 'Perfil',
        'info' => 'Información',
        'logout' => 'Cerrar sesión',
        'level_heading' => 'Niveles',
        'back_button' => 'Volver'
    ]
];

$translations = $translations[$lang];

// Cambiar el idioma si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['language'])) {
    $language = $_POST['language'];
    $_SESSION['language'] = $language;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <title>Juego - Niveles</title>
    <link rel="stylesheet" type="text/css" href="../../styles/inicio.css">
    <link rel="stylesheet" type="text/css" href="../../styles/niveles-juego.css">
    <style>
        .contenido-juego {
            height: 83%;
        }
        .level.locked img {
            width: 5%;
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
                <form method="post">
                    <select id="languageSelector" name="language" onchange="this.form.submit()">
                        <option value="en" <?php echo $lang == 'en' ? 'selected' : ''; ?>>English</option>
                        <option value="es" <?php echo $lang == 'es' ? 'selected' : ''; ?>>Español</option>
                    </select>
                </form>
            </div>
            <div class="fila2-cl1">
                <a href="inicio.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/inicio.png" alt="">
                    <span class="tooltiptext" id="home"><?php echo $translations['home']; ?></span>
                </a>
                <a href="niveles-juego.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/niveles.png" alt="">
                    <span class="tooltiptext" id="levels"><?php echo $translations['levels']; ?></span>
                </a>
                <a href="score-page.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/scoreglobal.png" alt="">
                    <span class="tooltiptext" id="score"><?php echo $translations['score']; ?></span>
                </a>
                <a href="perfil.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/perfil.png" alt="">
                    <span class="tooltiptext" id="profile"><?php echo $translations['profile']; ?></span>
                </a>
                <a href="<?php echo ($_SESSION['perfil'] == 'profesor') ? 'reporte-niveles.php' : 'informacion.php'; ?>" class="lg-cl1">
                    <img src="../../assets/img/inicio/<?php echo ($_SESSION['perfil'] == 'profesor') ? 'report.png' : 'info.png'; ?>" alt="">
                    <span class="tooltiptext" id="info"><?php echo $translations['info']; ?></span>
                </a>
                <a href="inicio-sesion.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/log-out.png" alt="">
                    <span class="tooltiptext" id="logout"><?php echo $translations['logout']; ?></span>
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
                    <h1 id="level_heading"><?php echo $translations['level_heading']; ?></h1>
                    <div class="level-container">
                        <?php
                            $count = 0; // Contador para controlar el número de elementos por fila
                            foreach ($levels as $nivel => $url) {
                                // Desbloquear los niveles para profesores o según el progreso del jugador
                                if ($isProfesor || substr($nivel, -1) <= $_SESSION['nivelDesbloqueado']) {
                                    echo "<a href='$url' class='level'>$nivel</a>";
                                } else {
                                    // Si el nivel no está desbloqueado, muestra un icono de candado
                                    echo "<span class='level locked'>$nivel <img src='../../assets/img/candado.png' alt='Candado'></span>";
                                }
                                
                                // Incrementar el contador
                                $count++;
                                // Si ya se han mostrado tres elementos, cerrar la fila y comenzar otra
                                if ($count % 3 == 0) {
                                    echo "</div><div class='level-container'>";
                                }
                            }
                        ?>
                    </div>
                    <div class="back-button">
                        <a href="inicio.php" id="back_button"><?php echo $translations['back_button']; ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
