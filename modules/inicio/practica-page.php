<?php
session_start();

include "../../connection/connection.php";

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
        'practice' => 'Practice',
        'ambiguous' => 'AMBIGUOUS',
        'not_ambiguous' => 'NOT AMBIGUOUS',
        'feedback' => 'FEEDBACK',
        'correct' => 'You selected correctly',
        'incorrect' => 'You selected incorrectly',
        'score_label' => 'Score:'
    ],
    'es' => [
        'home' => 'Inicio',
        'levels' => 'Niveles',
        'score' => 'Puntuación',
        'profile' => 'Perfil',
        'info' => 'Información',
        'logout' => 'Cerrar sesión',
        'practice' => 'Práctica',
        'ambiguous' => 'AMBIGUO',
        'not_ambiguous' => 'NO AMBIGUO',
        'feedback' => 'RETROALIMENTACIÓN',
        'correct' => 'Seleccionaste correctamente',
        'incorrect' => 'Seleccionaste incorrectamente',
        'score_label' => 'Puntuación:'
    ]
];

$translations = $translations[$lang];

// Inicializar puntaje si no existe
if (!isset($_SESSION['score'])) {
    $_SESSION['score'] = 0;
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
        echo "No se encontraron resultados.";
        exit();
    }
} else {
    echo "No se ha iniciado sesión.";
    exit();
}

// Consulta para obtener el requerimiento
$sql_requerimiento = "SELECT name, is_ambiguous FROM requirements ORDER BY RAND() LIMIT 1";
$result_requerimiento = mysqli_query($con, $sql_requerimiento);

if ($result_requerimiento && mysqli_num_rows($result_requerimiento) > 0) {
    $requerimiento = mysqli_fetch_assoc($result_requerimiento);
} else {
    echo "No se encontraron requerimientos.";
    exit();
}

// Actualizar el puntaje y el idioma en la sesión si se envían desde la solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['score'])) {
        $_SESSION['score'] = intval($_POST['score']);
    }
    if (isset($_POST['language'])) {
        $_SESSION['language'] = $_POST['language'];
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <title><?php echo $translations['practice']; ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../styles/inicio.css">
    <link rel="stylesheet" type="text/css" href="../../styles/niveles-juego.css">
    <style>
        .fila1 {
            display: grid;
            grid-template-columns: auto auto 1fr;
            align-items: center;
            margin: 2% 2% 0% 2%;
            color: #fff;
            margin: 5%;
            font-weight: lighter;
        }
        .cash {
            font-size: 30px;
            font-family: 'Digitalt';
        }

        .fila1-titulo {
            background-color: transparent;
            margin: 2% 0;
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            font-size: 40px;
            color: #eee;
            font-family: 'Digitalt';
        }
        .fila1-titulo span {
            margin-top: 2%;
        }
        .titulo-nivel-txt {
            border: 1px solid #eee;
            border-radius: 45px;
            padding: 1%;
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            font-size: 40px;
            font-family: 'Digitalt';
        }
        .titulo-coin {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            font-weight: 100px;
        }
        .contenido-juego {
            background-image: linear-gradient(to bottom, #2C9AFF, #53F3FD);
            margin: 2%;
            display: grid;
            grid-template-rows: auto 1fr auto;
            border-radius: 10px;
            height: 93%;
        }
        #score {
            font-size: 24px;
            font-weight: lighter;
        }
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
        .contenido-juego {
            text-align: center;
            margin: 20px;
        }
        .texto-req {
            background-color: #f0f0f0;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            font-size: 20px;
        }
        .btn-1, .btn-2 {
            margin: 10px;
        }
        .btn-1 button, .btn-2 button {
            background: linear-gradient(45deg, #A6F208 50%, #67EB00 50%);
            box-shadow: inset 0 0 10px #4EC307;
            color: whitesmoke;
            border-radius: 20px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-size: 42px;
            font-family: 'Digitalt';
            border: 1px solid #FFFFFF;
        }
        .btn-2 button:hover, .btn-1 button:hover {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.8);
            transition: box-shadow 0.3s ease;
        }
        #myModal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow: auto;
            font-family: 'Digitalt';
            font-weight: 100;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 20px;
            width: 40%;
            position: relative;
        }
        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
            background-color: #67EB00;
            border: 2px solid white;
            border-radius: 50%; 
            color: white;
            padding: 3% 4%;
        }
        .close:hover {
            background-color: #FF1717;
        }
        .mensaje {
            background-color: #2C9AFF;
            padding: 2%;
            color: #fefefe;
            font-size: 30px;
        }
        #modal-text {
            font-size: 25px;
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
        .texto-req{
            border-radius: 30px;
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
                <a href="<?php echo ($_SESSION['perfil'] == 'profesor') ? 'reporte-niveles.php' : 'informacion.php'; ?>" class="lg-cl1">
                    <img src="../../assets/img/inicio/<?php echo ($_SESSION['perfil'] == 'profesor') ? 'report.png' : 'info.png'; ?>" alt="">
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
                    <div class="titulo-coin">
                        <div class="fila1">
                            <div class="cash" id="score" data-translate="score_label"><?php echo $translations['score_label']; ?> <?php echo $_SESSION['score']; ?></div>
                            <div class="coin">
                                <img src="../../assets/img/juego-lvl1/coin.png" alt="Coin">
                            </div>
                        </div>
                        <h1 class="fila1-titulo"><span data-translate="practice"><?php echo $translations['practice']; ?></span></h1>
                    </div>
                    <div class="container-practica">
                        <div class="texto-req" id="texto-req">
                            <span class="txt"><?php echo $requerimiento['name']; ?></span>
                        </div>
                        <div class="btn-1">
                            <button data-translate="ambiguous" onclick="checkAnswer(true)"><?php echo $translations['ambiguous']; ?></button>
                        </div>
                        <div class="btn-2">
                            <button data-translate="not_ambiguous" onclick="checkAnswer(false)"><?php echo $translations['not_ambiguous']; ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- The Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <div class="mensaje" data-translate="feedback"><?php echo $translations['feedback']; ?></div>
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="modal-text"></p>
        </div>
    </div>
    
    <script>
        let isAmbiguous = <?php echo $requerimiento['is_ambiguous'] ? 'true' : 'false'; ?>;
        let score = <?php echo $_SESSION['score']; ?>;

        function checkAnswer(answer) {
            let message = answer === isAmbiguous ? "<?php echo $translations['correct']; ?>" : "<?php echo $translations['incorrect']; ?>";
            if (answer === isAmbiguous) {
                score += 10;
                updateScore(score);
            }
            showModal(message);
        }

        function updateScore(newScore) {
            document.getElementById("score").textContent = "<?php echo $translations['score_label']; ?> " + newScore;
            // Actualizar la sesión en el servidor
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("score=" + newScore);
        }

        function showModal(message) {
            document.getElementById("modal-text").textContent = message;
            document.getElementById("myModal").style.display = "block";
            setTimeout(loadNewRequirement, 2000); // Carga un nuevo requerimiento después de 2 segundos
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        window.onclick = function(event) {
            let modal = document.getElementById("myModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function loadNewRequirement() {
            let xhr = new XMLHttpRequest();
            xhr.open("GET", window.location.href, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    let response = xhr.responseText;
                    let tempDiv = document.createElement('div');
                    tempDiv.innerHTML = response;
                    let newRequirement = tempDiv.querySelector("#texto-req .txt").textContent;
                    let newIsAmbiguous = tempDiv.querySelector("script").innerText.includes("true");

                    document.getElementById("texto-req").innerHTML = `<span class="txt">${newRequirement}</span>`;
                    isAmbiguous = newIsAmbiguous;
                    closeModal();
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>
