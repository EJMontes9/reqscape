<?php
session_start(); // Iniciar la sesión al principio del archivo

include "../../connection/connection.php";

// Consulta para obtener los datos del usuario
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT usuario, correo, imagen_perfil, perfil FROM usuarios WHERE id = $user_id";
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

// Establecer el idioma predeterminado
$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';

// Definir las traducciones
$translations = [
    'en' => [
        'home' => 'Home',
        'levels' => 'Levels',
        'score' => 'Score',
        'profile' => 'Profile',
        'info' => 'Info',
        'logout' => 'Logout',
        'create_room' => 'Create Room',
        'practice' => 'Practice',
        'play' => 'Play',
        'select_game_mode' => 'Select Game Mode',
        'solo_mode' => 'Solo Mode',
        'room_mode' => 'Room Mode',
        'select_level' => 'Select Level',
        'level_1' => 'Level 1',
        'level_2' => 'Level 2',
        'change_language' => 'Change Language'
    ],
    'es' => [
        'home' => 'Inicio',
        'levels' => 'Niveles',
        'score' => 'Puntuación',
        'profile' => 'Perfil',
        'info' => 'Información',
        'logout' => 'Cerrar sesión',
        'create_room' => 'Crear Sala',
        'practice' => 'Práctica',
        'play' => 'Jugar',
        'select_game_mode' => 'Selecciona el modo de juego',
        'solo_mode' => 'Modo Solitario',
        'room_mode' => 'Sala',
        'select_level' => 'Selecciona el nivel',
        'level_1' => 'Nivel 1',
        'level_2' => 'Nivel 2',
        'change_language' => 'Cambiar idioma'
    ]
];

$translations = $translations[$lang];
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReqScape</title>
    <link rel="stylesheet" type="text/css" href="../../styles/inicio.css">
    <style>
    /* Estilos para el contenedor de la imagen de perfil */
    .profile-pic-container {
        width: 120px; /* Tamaño fijo del contenedor */
        height: 120px; /* Tamaño fijo del contenedor */
        position: relative;
    }

    /* Estilos para hacer el contenedor circular */
    .contenedor-img.circular {
        width: 50%; /* Hacer el contenedor cuadrado */
        height: 0;
        padding-bottom: 100%; /* Mantener la relación de aspecto 1:1 */
        overflow: hidden;
        border-radius: 10%; /* Hacer el contenedor circular */
    }

    /* Estilos para la imagen de perfil */
    .usuario-logueado {
        display: flex;
        justify-content: end;
        border-radius: 10%; /* Hacer la imagen circular */
        margin: 2%;
        margin-bottom:0%;
        
    }
    .profile-data-item {
        font-family: 'Digitalt';
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right:3%;
        margin-top: 0%;
        margin-bottom:1%;
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
    .contenido-juego {
        height: 93%; /* Ajusta el porcentaje según el cálculo */
    }
    .btn-juego-principal {
        gap: 2%;
    }
    .btn-jugar{
        margin-right: 0%;
    }
    .fila1-cl1 {
        padding: 7%;
        /* Espacio a los lados */
        border-bottom: 1px solid rgba(19, 67, 145, 0.4);
        /* Borde en la parte inferior */
 
    }
    #languageSelector {
        appearance: none; /* Remueve la apariencia predeterminada del navegador */
        -webkit-appearance: none; /* Para Safari y Chrome */
        -moz-appearance: none; /* Para Firefox */
        width: 100%; /* Ancho del select */
        padding: 8%; /* Espaciado interior */
        border: 1px solid #ddd; /* Borde */
        border-radius: 5px; /* Bordes redondeados */
        font-size: 16px; /* Tamaño de la fuente */
        background-color: #fff; /* Color de fondo */
        color: #333; /* Color del texto */
        cursor: pointer; /* Cursor */
        outline: none; /* Remueve el borde de enfoque */
        transition: border-color 0.3s ease; /* Transición para el borde */
        text-align: center; /* Centra el texto dentro del select */
        font-family: 'Digitalt';
        font-weight: lighter;
    }

    /* Estilo cuando el select está enfocado */
    #languageSelector:focus {
        border-color: #007BFF; /* Cambia el color del borde cuando está enfocado */
    }

    /* Estilos para las opciones del select */
    #languageSelector option {
        color: #333; /* Color del texto de las opciones */
        background-color: #fff; /* Color de fondo de las opciones */
        text-align: center; /* Centra el texto dentro de las opciones */
    }
/* Estilos para el modal */

 /* Estilos para la ventana modal */
 .modal {
        display: none; /* Ocultar la modal por defecto */
        position: fixed; /* Mantener la modal en una posición fija */
        z-index: 1; /* Asegurar que la modal esté por encima de otros elementos */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto; /* Permitir el desplazamiento si el contenido es demasiado grande */
        background-color: rgba(0, 0, 0, 0.5); /* Fondo semitransparente */
        font-family: 'Digitalt';
      
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto; /* 15% desde la parte superior y centrado horizontalmente */
        padding: 20px;
        border: 1px solid #888;
        width: 80%; /* Ancho de la modal */
        max-width: 300px; /* Máximo ancho de la modal */
        text-align: center; /* Centrar el contenido */
        border-radius: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }

    .modal-content button {
        margin: 10px;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
    }
    /*modal para opciones de modo de juego*/
    #modoSolitario, #modoSala{
        background: linear-gradient(45deg, #552B9A 50%, #552B9A 50%);
        box-shadow: inset 0 0 10px #401486;
        color: whitesmoke;
        border-radius: 20px;
        /*width: 50%;*/
        margin-right: 5%;
        border: 1px solid #FFFFFF;
        padding: 15px 30px;
        font-size: 20px;
        font-family: 'Digitalt';
    }
    .close {
    color: #09B9FF;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    }
    .close:hover,
    .close:focus {
        color: #FF1717;
        text-decoration: none;
        cursor: pointer;
    }
    #nivel1,#nivel2 {
        background: linear-gradient(45deg, #552B9A 50%, #552B9A 50%);
        box-shadow: inset 0 0 10px #401486;
        color: whitesmoke;
        border-radius: 20px;
        /*width: 50%;*/
        margin-right: 5%;
        border: 1px solid #FFFFFF;
        padding: 15px 30px;
        font-size: 20px;
        font-family: 'Digitalt';
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
                    <span class="tooltiptext" data-translate="home"><?php echo $translations['home']; ?></span>
                </a>
                <a href="niveles-juego.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/niveles.png" alt="">
                    <span class="tooltiptext" data-translate="levels"><?php echo $translations['levels']; ?></span>
                </a>
                <a href="score-page.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/scoreglobal.png" alt="">
                    <span class="tooltiptext" data-translate="score"><?php echo $translations['score']; ?></span>
                </a>
                <a href="perfil.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/perfil.png" alt="">
                    <span class="tooltiptext" data-translate="profile"><?php echo $translations['profile']; ?></span>
                </a>
                <a href="<?php echo ($row['perfil'] == 'profesor') ? 'reporte-niveles.php' : 'informacion.php'; ?>" class="lg-cl1">
                <img src="../../assets/img/inicio/<?php echo ($row['perfil'] == 'profesor') ? 'report.png' : 'info.png'; ?>" alt="">
                <span class="tooltiptext" data-translate="info"><?php echo $translations['info']; ?></span>
                </a>
                <a href="inicio-sesion.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/log-out.png" alt="">
                    <span class="tooltiptext" data-translate="logout"><?php echo $translations['logout']; ?></span>
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
                        // Si no hay imagen de perfil, se mostrará el avatar predeterminado
                        echo '<img class="profile-pic" src="../../modules/inicio/uploads/perfil.jpg" alt="Avatar predeterminado">';
                    }
                    ?>
                </div>
            </div>
            <div class="fila2-cl2">
                <div class="contenido-juego">
                    <div class="logo-inicial">
                        <img src="../../assets/img/inicio/logoinicio.png" alt="">
                    </div>
                    <div class="btn-juego-principal">
                            <button class="btn-jugar" id="btn-sala" <?php echo $row["perfil"] != 'profesor' ? "style='display:none'":'';?> data-translate="create_room"><?php echo $translations['create_room']; ?></button>
                            <button class="btn-jugar" id="btn-practica" <?php echo $row["perfil"] == 'profesor' ? "style='display:none'":'';?> data-translate="practice"><?php echo $translations['practice']; ?></button>
                            <button class="btn-jugar" id="btn-jugar" <?php echo $row["perfil"] == 'profesor' ? "style='display:none'":'';?> data-translate="play"><?php echo $translations['play']; ?></button>
                    </div>
                    <!-- Modal para seleccionar el modo de juego -->
                    <div id="modalJugar" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2 data-translate="select_game_mode"><?php echo $translations['select_game_mode']; ?></h2>
                            <button id="modoSolitario" data-translate="solo_mode"><?php echo $translations['solo_mode']; ?></button>
                            <button id="modoSala" data-translate="room_mode"><?php echo $translations['room_mode']; ?></button>
                        </div>
                    </div>
                    <!-- Modal para seleccionar el nivel de juego -->
                    <div id="modalSala" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2 data-translate="select_level"><?php echo $translations['select_level']; ?></h2>
                            <button id="nivel1" data-translate="level_1"><?php echo $translations['level_1']; ?></button>
                            <button id="nivel2" data-translate="level_2"><?php echo $translations['level_2']; ?></button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        var modalJugar = document.getElementById("modalJugar");
        var modalSala = document.getElementById("modalSala");
        var btnJugar = document.getElementById("btn-jugar");
        var closeModalJugar = document.getElementsByClassName("close")[0];
        var closeModalSala = document.getElementsByClassName("close")[1];
        var modoSolitario = document.getElementById("modoSolitario");
        var modoSala = document.getElementById("modoSala");
        var nivel1 = document.getElementById("nivel1");
        var nivel2 = document.getElementById("nivel2");

        btnJugar.onclick = function() {
            modalJugar.style.display = "block";
        }

        closeModalJugar.onclick = function() {
            modalJugar.style.display = "none";
        }

        closeModalSala.onclick = function() {
            modalSala.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modalJugar) {
                modalJugar.style.display = "none";
            } else if (event.target == modalSala) {
                modalSala.style.display = "none";
            }
        }

        modoSolitario.onclick = function() {
            window.location.href = "level1-txt.php"; // Cambia a la ruta de tu página de modo solitario
        }

        modoSala.onclick = function() {
            modalJugar.style.display = "none";
            modalSala.style.display = "block";
        }

        nivel1.onclick = function() {
            window.location.href = "sala_partida.php";
        }

        nivel2.onclick = function() {
            window.location.href = "sala_partida_2.php";
        }

        var btnSala = document.getElementById("btn-sala");
        if (btnSala) {
            btnSala.onclick = function() {
                window.location.href = "niveles-juego.php";
            }
        }
    });

    // Obtener los botones
    var btnPractica = document.getElementById("btn-practica");

    // Redirigir a la página de práctica
    if (btnPractica) {
        btnPractica.onclick = function() {
            window.location.href = "practica-page.php";
        }
    }
    </script>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['language'])) {
    $language = $_POST['language'];
    $_SESSION['language'] = $language;
    // Recargar la página para aplicar el cambio de idioma
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
