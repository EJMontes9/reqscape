<?php
session_start();

include "../../connection/connection.php";

// Función para cargar las traducciones según el idioma seleccionado
function loadTranslations($lang) {
    $translations = [
        'en' => [
            'home' => 'Home',
            'levels' => 'Levels',
            'score' => 'Score',
            'profile' => 'Profile',
            'info' => 'Info',
            'logout' => 'Logout',
            'information' => 'INFORMATION',
            'ambiguous' => 'AMBIGUOUS',
            'not_ambiguous' => 'NOT AMBIGUOUS',
            'ambiguous_text' => 'Refers to requirements that can be interpreted in different ways or are unclear in their meaning.',
            'not_ambiguous_text' => 'Must be clear, precise, and detailed, avoiding the use of terms subject to interpretation.',
            'practice' => 'Practice',
            'play' => 'Play'
        ],
        'es' => [
            'home' => 'Inicio',
            'levels' => 'Niveles',
            'score' => 'Puntuación',
            'profile' => 'Perfil',
            'info' => 'Información',
            'logout' => 'Cerrar sesión',
            'information' => 'INFORMACIÓN',
            'ambiguous' => 'AMBIGUO',
            'not_ambiguous' => 'NO AMBIGUO',
            'ambiguous_text' => 'Se refiere a aquellos requerimientos que pueden ser interpretados de diferentes maneras o que no son claros en su significado.',
            'not_ambiguous_text' => 'Debe ser claro, preciso y detallado, evitando el uso de términos sujetos a interpretación.',
            'practice' => 'Práctica',
            'play' => 'Jugar'
        ],
    ];
    return $translations[$lang];
}

$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'es';
$translations = loadTranslations($lang);

// Verificar si el formulario de inicio de sesión ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['change_language'])) {
    $correo = $_POST["usuario"];
    $contraseña = $_POST["password"];
    $selected_language = $_POST['language'];

    try {
        // Consultar la base de datos para verificar las credenciales del usuario
        $stmt = $con->prepare("SELECT id, correo, contrasena, usuario, perfil FROM usuarios WHERE correo = ?");
        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            if (password_verify($contraseña, $row['contrasena'])) {
                $_SESSION["user_id"] = $row['id'];
                $_SESSION["user_name"] = $row['usuario'];
                $_SESSION["usuario"] = $correo;
                $_SESSION["perfil"] = $row['perfil'];
                $_SESSION['language'] = $selected_language;
                header("Location: inicio.php");
                exit();
            } else {
                $error = "Usuario o contraseña incorrectos";
            }
        } else {
            $error = "Usuario o contraseña incorrectos";
        }
        $stmt->close();
    } catch (Exception $e) {
        die("Error al autenticar usuario: " . $e->getMessage());
    }
}

// Cambiar el idioma si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['language'])) {
    $_SESSION['language'] = $_POST['language'];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>


<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReqScape</title>
    <link rel="stylesheet" type="text/css" href="../../styles/inicio.css">
    <link rel="stylesheet" type="text/css" href="../../styles/level-txt.css">
    <link rel="stylesheet" type="text/css" href="../../styles/informacion.css">
    <style>
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
                <form method="post" id="language-form">
                    <select id="languageSelector" name="language" onchange="document.getElementById('language-form').submit()">
                        <option value="en" <?php echo $lang == 'en' ? 'selected' : ''; ?>>English</option>
                        <option value="es" <?php echo $lang == 'es' ? 'selected' : ''; ?>>Español</option>
                    </select>
                </form>
            </div>
            <div class="fila2-cl1">
                <a href="inicio.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/inicio.png" alt="">
                    <span class="tooltiptext"><?php echo $translations['home']; ?></span>
                </a>
                <a href="niveles-juego.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/niveles.png" alt="">
                    <span class="tooltiptext"><?php echo $translations['levels']; ?></span>
                </a>
                <a href="score-page.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/scoreglobal.png" alt="">
                    <span class="tooltiptext"><?php echo $translations['score']; ?></span>
                </a>
                <a href="perfil.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/perfil.png" alt="">
                    <span class="tooltiptext"><?php echo $translations['profile']; ?></span>
                </a>
                <a href="<?php echo ($_SESSION['perfil'] == 'profesor') ? 'reporte-niveles.php' : 'informacion.php'; ?>" class="lg-cl1">
                    <img src="../../assets/img/inicio/<?php echo ($_SESSION['perfil'] == 'profesor') ? 'report.png' : 'info.png'; ?>" alt="">
                    <span class="tooltiptext"><?php echo $translations['info']; ?></span>
                </a>
                <a href="inicio-sesion.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/log-out.png" alt="">
                    <span class="tooltiptext"><?php echo $translations['logout']; ?></span>
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
                    <div class="fila1">
                        <span class="titulo-nivel-txt" id="informacion"><?php echo $translations['information']; ?></span>
                    </div>
                    <div class="fila2">
                        <div class="fila2-1">
                            <table>
                                <tr>
                                    <th id="ambiguo-header"><?php echo $translations['ambiguous']; ?></th>
                                    <th id="no-ambiguo-header"><?php echo $translations['not_ambiguous']; ?></th>
                                </tr>
                                <tr>
                                    <td id="ambiguo-text"><?php echo $translations['ambiguous_text']; ?></td>
                                    <td id="no-ambiguo-text"><?php echo $translations['not_ambiguous_text']; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php if ($_SESSION['perfil'] != 'profesor') { ?>
                    <div class="fila2-2">
                        <div class="btn-juego-principal">
                            <form action="practica-page.php" method="GET">
                                <button type="submit" class="btn-practica" name="practica" id="practica"><?php echo $translations['practice']; ?></button>
                            </form>
                            <form action="level1-txt.php" method="GET">
                                <button type="submit" class="btn-jugar" name="jugar" id="jugar"><?php echo $translations['play']; ?></button>
                            </form>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
