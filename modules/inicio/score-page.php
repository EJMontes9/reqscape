<?php
session_start(); // Iniciar la sesión al principio del archivo

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
        'download_scores' => 'Download Scores',
        'player_scores' => 'Player Scores',
        'user' => 'User',
        'points' => 'Points',
        'level' => 'Level',
        'registration_date' => 'Registration Date',
        'previous' => 'Previous',
        'next' => 'Next'
    ],
    'es' => [
        'home' => 'Inicio',
        'levels' => 'Niveles',
        'score' => 'Puntuación',
        'profile' => 'Perfil',
        'info' => 'Información',
        'logout' => 'Cerrar sesión',
        'download_scores' => 'Descargar Puntajes',
        'player_scores' => 'Puntajes de Jugadores',
        'user' => 'Usuario',
        'points' => 'Puntos',
        'level' => 'Nivel',
        'registration_date' => 'Fecha de Registro',
        'previous' => 'Anterior',
        'next' => 'Siguiente'
    ]
];

$translations = $translations[$lang];

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

// Actualizar el idioma en la sesión si se envían desde la solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['language'])) {
    $_SESSION['language'] = $_POST['language'];
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Definir el número de registros por página
$records_per_page = 4;

// Determinar la página actual
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Inicializar variables para evitar errores de variable indefinida
$room_code = isset($_GET['room_code']) ? $_GET['room_code'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Consulta para obtener los puntajes con paginación
$sql = "SELECT usuarios.usuario, puntajes.puntaje, puntajes.nivel, puntajes.fecha_registro 
        FROM puntajes 
        INNER JOIN usuarios ON puntajes.user_id = usuarios.id 
        ORDER BY puntajes.puntaje DESC
        LIMIT $start_from, $records_per_page";

$result = mysqli_query($con, $sql);

// Obtener el total de registros para paginación
$sql_pagination = "SELECT COUNT(*) AS total_records FROM puntajes";
$result_pagination = mysqli_query($con, $sql_pagination);
$row_pagination = mysqli_fetch_assoc($result_pagination);
$total_records = $row_pagination['total_records'];
$total_pages = ceil($total_records / $records_per_page);

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReqScape</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../styles/inicio.css">
    <link rel="stylesheet" type="text/css" href="../../styles/score-page.css">
    <style>
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .page-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            margin-right: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .page-number a {
            display: block;
            color: #333;
            text-decoration: none;
        }

        .page-number a.active {
            font-weight: bold;
            background-color: #007bff;
            color: #fff;
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

        .container {
            width: 50%;
        }
        .cont-pagination{
            display: flex;
            justify-content: center;
        }

        .contenido-juego{
            height: 90%;
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
                    <span class="tooltiptext" id="info"><?php echo $translations['info']; ?></span>
                </a>
                <a href="inicio-sesion.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/log-out.png" alt="">
                    <span class="tooltiptext" data-translate="logout"><?php echo $translations['logout']; ?></span>
                </a>
                <a href="descargar-puntajes.php" class="lg-cl1">
                    <img src="../../assets/img/inicio/download.png" alt="">
                    <span class="tooltiptext"><?php echo $translations['download_scores']; ?></span>
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
                    <div class="container">
                        <h1><?php echo $translations['player_scores']; ?></h1>
                        <table>
                            <tr>
                                <th><?php echo $translations['user']; ?></th>
                                <th><?php echo $translations['points']; ?></th>
                                <th><?php echo $translations['level']; ?></th>
                                <th><?php echo $translations['registration_date']; ?></th>
                            </tr>
                            <?php
                            if (mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row["usuario"] . "</td>";
                                    echo "<td>" . $row["puntaje"] . "</td>";
                                    echo "<td>" . $row["nivel"] . "</td>";
                                    echo "<td>" . $row["fecha_registro"] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No hay puntajes registrados.</td></tr>";
                            }
                            ?>
                        </table>
                        <nav class="cont-pagination">
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?room_code=<?php echo $room_code; ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&page=<?php echo $page-1; ?>"><?php echo $translations['previous']; ?></a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php
                                $start = max(1, $page - 2);
                                $end = min($total_pages, $page + 2);

                                if ($start > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?room_code=' . $room_code . '&search=' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . '&page=1">1</a></li>';
                                    if ($start > 2) {
                                        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                    }
                                }

                                for ($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                                        <a class="page-link" href="?room_code=<?php echo $room_code; ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php
                                if ($end < $total_pages) {
                                    if ($end < $total_pages - 1) {
                                        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="?room_code=' . $room_code . '&search=' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
                                }
                                ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?room_code=<?php echo $room_code; ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&page=<?php echo $page+1; ?>"><?php echo $translations['next']; ?></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
