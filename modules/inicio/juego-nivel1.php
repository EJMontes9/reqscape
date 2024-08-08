<?php
session_start(); // Iniciar la sesión al principio del archivo

include "../../connection/connection.php";

// Verificar si la sesión está iniciada y obtener los datos del usuario
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Utilizar una declaración preparada para mayor seguridad
    $stmt = $con->prepare("SELECT usuario, correo, imagen_perfil FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se obtuvieron resultados
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        // Manejar el caso en el que no se obtuvieron resultados
        $row = array('usuario' => 'Usuario', 'imagen_perfil' => '../../modules/inicio/uploads/perfil.jpg'); // Valores predeterminados
    }

    $stmt->close();
} else {
    echo "No se ha iniciado sesión.";
    exit();
}

// Verificar si se ha enviado el room_code o si se desea jugar solo
if (isset($_GET['room_code']) && !empty($_GET['room_code'])) {
    $room_code = mysqli_real_escape_string($con, $_GET['room_code']);
    $_SESSION['room_code'] = $room_code; // Guardar el room_code en la sesión

    // Obtener los detalles de la sala y los requisitos asociados
    $sql_requirements = "SELECT r.id, r.name, r.is_ambiguous, r.retro
                         FROM room_requirements1 rr
                         JOIN requirements r ON rr.requirement_id = r.id
                         WHERE rr.room_code = '$room_code'";
    $result_requirements = mysqli_query($con, $sql_requirements);
    
    // Verificar si se obtuvieron resultados
    if ($result_requirements && mysqli_num_rows($result_requirements) > 0) {
        $requirements = mysqli_fetch_all($result_requirements, MYSQLI_ASSOC);
    } else {
        echo "No se encontraron requerimientos para el código de sala proporcionado.";
        exit();
    }
} else {
    // Limpiar cualquier código de sala existente en la sesión
    unset($_SESSION['room_code']);

    // Seleccionar 10 requerimientos al azar para el juego en solitario
    $sql_requirements = "SELECT id, name, is_ambiguous, retro
                         FROM requirements
                         ORDER BY RAND()
                         LIMIT 10";
    $result_requirements = mysqli_query($con, $sql_requirements);

    // Verificar si se obtuvieron resultados
    if ($result_requirements && mysqli_num_rows($result_requirements) > 0) {
        $requirements = mysqli_fetch_all($result_requirements, MYSQLI_ASSOC);
    } else {
        echo "No se encontraron requerimientos disponibles.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Juego - Niveles</title>
    <link rel="stylesheet" type="text/css" href="../../styles/inicio.css">
    <link rel="stylesheet" type="text/css" href="../../styles/niveles-juego.css">
    <link rel="stylesheet" type="text/css" href="../../styles/juego-nivel1.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
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
</style>
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
                    <span class="tooltiptext" id="tooltipHome">Home</span>
                </a>
                <a href="niveles-juego.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/niveles.png" alt="">
                    <span class="tooltiptext" id="tooltipLevels">Levels</span>
                </a>
                <a href="score-page.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/scoreglobal.png" alt="">
                    <span class="tooltiptext" id="tooltipScore">Score</span>
                </a>
                <a href="perfil.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/perfil.png" alt="">
                    <span class="tooltiptext" id="tooltipProfile">Profile</span>
                </a>
                <a href="<?php echo ($_SESSION['perfil'] == 'profesor') ? 'reporte-niveles.php' : 'informacion.php'; ?>" class="lg-cl1">
                    <img src="../../assets/img/inicio/<?php echo ($_SESSION['perfil'] == 'profesor') ? 'report.png' : 'info.png'; ?>" alt="">
                    <span class="tooltiptext" id="info">Información</span>
                </a>
                <a href="inicio-sesion.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/log-out.png" alt="">
                    <span class="tooltiptext" id="tooltipLogout">Logout</span>
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
                        <span id="username" class="username-span"><?php echo htmlspecialchars($row['usuario'], ENT_QUOTES, 'UTF-8'); ?></span>
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
                    <div class="titulo-coin">
                        <div class="fila1">
                            <div class="cash" id="score"></div>
                            <div class="coin">
                                <img src="../../assets/img/juego-lvl1/coin.png" alt="Coin">
                            </div>
                        </div>
                        <h1 class="fila1-titulo"><span id="levelTitle">Nivel 01</span></h1>
                    </div>
                    <div class="container">
                        <div class="box" id="box1" ondrop="drop(event)" ondragover="allowDrop(event)">
                            <h2 id="ambiguousTitle">Ambiguo</h2>
                            <ul></ul>
                        </div>
                        <div class="box" id="box2" ondrop="drop(event)" ondragover="allowDrop(event)">
                            <h2 id="requirementsTitle">Requerimientos</h2>
                            <ul>
                                <?php
                                // Mostrar todos los requerimientos seleccionados
                                foreach ($requirements as $requirement) {
                                    $isAmbiguous = $requirement["is_ambiguous"] ? 'true' : 'false';
                                    echo "<li draggable='true' ondragstart='drag(event)' id='req_" . $requirement["id"] . "' data-ambiguous='" . $isAmbiguous . "' data-retro='" . htmlspecialchars($requirement["retro"], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($requirement["name"], ENT_QUOTES, 'UTF-8') . "</li>";
                                }
                                ?>
                            </ul>
                        </div>
                        <div class="box" id="box3" ondrop="drop(event)" ondragover="allowDrop(event)">
                            <h2 id="nonAmbiguousTitle">No Ambiguo</h2>
                            <ul></ul>
                        </div>
                    </div>
                </div>

                <!-- Ventana modal para retroalimentación -->
                <div id="myModal" class="modal">
                    <div class="modal-content">
                        <div class="mensaje">Mensaje</div>
                        <span class="close">&times;</span>
                        <h2 id="modalTitle">Para Recordar:</h2>
                        <p id="modal-text"></p>
                    </div>
                </div>
                
                <script src="../../js/juego-nivel1.js"></script>
                <script src="../../js/cambio-idioma-nivel1.js"></script>
            </div>
        </div>
    </div>
</body>
</html>
