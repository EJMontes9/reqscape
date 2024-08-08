<?php
session_start();
include "../../connection/connection.php";

function loadTranslations($lang) {
    $translations = [
        'en' => [
            'home' => 'Home',
            'levels' => 'Levels',
            'score' => 'Score',
            'profile' => 'Profile',
            'info' => 'Info',
            'logout' => 'Logout',
            'player_profile' => 'Player Profile',
            'username_label' => 'Username:',
            'email_label' => 'Email:',
            'profile_label' => 'Profile:',
            'upload_error' => 'Error uploading profile picture.',
            'upload_success' => 'Profile picture uploaded successfully.',
            'session_error' => 'No session started.',
            'no_results' => 'No results found.',
            'back_button' => 'Back',
            'upload_button' => 'Upload',
        ],
        'es' => [
            'home' => 'Inicio',
            'levels' => 'Niveles',
            'score' => 'Puntuación',
            'profile' => 'Perfil',
            'info' => 'Información',
            'logout' => 'Cerrar sesión',
            'player_profile' => 'Perfil del Jugador',
            'username_label' => 'Nombre de usuario:',
            'email_label' => 'Correo electrónico:',
            'profile_label' => 'Perfil:',
            'upload_error' => 'Error al subir la imagen de perfil.',
            'upload_success' => 'Imagen de perfil subida con éxito.',
            'session_error' => 'No se ha iniciado sesión.',
            'no_results' => 'No se encontraron resultados.',
            'back_button' => 'Volver',
            'upload_button' => 'Subir',
        ],
    ];
    return $translations[$lang];
}

$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'es';
$translations = loadTranslations($lang);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if (isset($_FILES['profile-pic'])) {
    $file_name = $_FILES['profile-pic']['name'];
    $file_tmp = $_FILES['profile-pic']['tmp_name'];
    $upload_dir = "uploads/";
    $target_file = $upload_dir . basename($file_name);

    if (move_uploaded_file($file_tmp, $target_file)) {
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $sql = "UPDATE usuarios SET imagen_perfil = '$target_file' WHERE id = $user_id";
            if (mysqli_query($con, $sql)) {
                header("Location: perfil.php");
                exit();
            } else {
                echo $translations['upload_error'] . mysqli_error($con);
            }
        } else {
            echo $translations['session_error'];
            exit();
        }
    } else {
        echo $translations['upload_error'];
    }
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT usuario, correo, imagen_perfil, perfil FROM usuarios WHERE id = $user_id";
    $result = mysqli_query($con, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo $translations['no_results'];
        exit();
    }
} else {
    echo $translations['session_error'];
    exit();
}

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
    <title><?php echo $translations['player_profile']; ?></title>
    <link rel="stylesheet" type="text/css" href="../../styles/inicio.css">
    <link rel="stylesheet" type="text/css" href="../../styles/perfil.css">
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
                <h1><?php echo $translations['player_profile']; ?></h1>
                <div class="profile-data">
                    <div class="profile-pic-container">
                        <div class="contenedor-img">
                            <label for="profile-pic" class="btn-upload">+</label>
                       
                        <?php
                        if (!empty($row["imagen_perfil"])) {
                            echo '<img class="profile-pic" src="' . $row["imagen_perfil"] . '" alt="Imagen de perfil">';
                        } else {
                            echo '<img class="profile-pic" src="../../modules/inicio/uploads/perfil.jpg" alt="Avatar predeterminado">';
                        }
                        ?>
                        </div>
                        <button class="btn" onclick="window.location.href = 'inicio.php'"><?php echo $translations['back_button']; ?></button>
                    </div>
                    <div class="profile-data-items">
                        <div class="profile-data-item">
                            <label for="username"><?php echo $translations['username_label']; ?></label>
                            <span id="username" class="username-span"><?php echo $row["usuario"]; ?></span>
                        </div>
                        <div class="profile-data-item">
                            <label for="email"><?php echo $translations['email_label']; ?></label>
                            <span id="email"><?php echo $row["correo"]; ?></span>
                        </div>
                        <div class="profile-data-item">
                            <label for="perfil"><?php echo $translations['profile_label']; ?></label>
                            <span id="perfil"><?php echo $row["perfil"]; ?></span>
                        </div>
                    </div>
                </div>
                <form action="" method="post" enctype="multipart/form-data" id="upload-form">
                    <input type="file" name="profile-pic" id="profile-pic">
                </form>              
            </div>      
    </div>
</div>
 
<script>
    document.getElementById('profile-pic').addEventListener('change', function() {
        document.getElementById('upload-form').submit();
    });
</script>
 
</body>
</html>
