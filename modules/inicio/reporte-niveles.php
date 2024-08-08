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
            'report_students' => 'Report Students',
            'search' => 'Search',
            'user' => 'User',
            'score' => 'Score',
            'level' => 'Level',
            'registration_date' => 'Registration Date',
            'room_code' => 'Room Code',
            'previous' => 'Previous',
            'next' => 'Next',
            'export_pdf' => 'Export to PDF'
        ],
        'es' => [
            'home' => 'Inicio',
            'levels' => 'Niveles',
            'score' => 'Puntuación',
            'profile' => 'Perfil',
            'info' => 'Información',
            'logout' => 'Cerrar sesión',
            'report_students' => 'Reporte Estudiantes',
            'search' => 'Buscar',
            'user' => 'Usuario',
            'score' => 'Puntaje',
            'level' => 'Nivel',
            'registration_date' => 'Fecha de Registro',
            'room_code' => 'Código de Sala',
            'previous' => 'Anterior',
            'next' => 'Siguiente',
            'export_pdf' => 'Exportar a PDF'
        ],
    ];
    return $translations[$lang];
}

// Manejar la selección del idioma
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['language'])) {
    $_SESSION['language'] = $_POST['language'];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'es';
$translations = loadTranslations($lang);

// Verificar si room_code está definido en GET
$room_code = isset($_GET['room_code']) ? $con->real_escape_string($_GET['room_code']) : '';

// Obtener el término de búsqueda
$search = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';

// Construir la consulta de conteo con condiciones opcionales
$sql_count = "SELECT COUNT(*) as total 
              FROM puntajes p
              JOIN usuarios u ON p.user_id = u.id
              WHERE u.perfil = 'estudiante' AND (p.nivel = 1 OR p.nivel = 2)";

if ($search !== '') {
    $sql_count .= " AND (u.usuario LIKE '%$search%' OR p.puntaje LIKE '%$search%' OR p.room_code LIKE '%$search%')";
}

$result_count = $con->query($sql_count);
$total_records = $result_count->fetch_assoc()['total'];

// Paginación
$limit = 3; // Número de registros por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$total_pages = ceil($total_records / $limit);

// Construir la consulta principal con condiciones opcionales y paginación
$sql = "SELECT u.usuario, p.puntaje, p.nivel, p.fecha_registro, p.room_code, u.imagen_perfil
        FROM puntajes p
        JOIN usuarios u ON p.user_id = u.id
        WHERE u.perfil = 'estudiante' AND (p.nivel = 1 OR p.nivel = 2)";
if ($search !== '') {
    $sql .= " AND (u.usuario LIKE '%$search%' OR p.puntaje LIKE '%$search%' OR p.room_code LIKE '%$search%')";
}

$sql .= " ORDER BY p.fecha_registro DESC LIMIT $start, $limit";

$result = $con->query($sql);

if (!$result) {
    die("Error al obtener puntajes: " . $con->error);
}

// Obtener el usuario logueado
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT usuario, imagen_perfil FROM usuarios WHERE id = $user_id";
$user_result = $con->query($sql_user);

if (!$user_result) {
    die("Error al obtener datos del usuario: " . $con->error);
}

$user_row = $user_result->fetch_assoc();

// Función para generar PDF
if (isset($_POST['export_pdf'])) {
    require_once('../../TCPDF-main/tcpdf.php');

    // Obtener todos los datos sin paginación para el PDF
    $sql_pdf = "SELECT u.usuario, p.puntaje, p.nivel, p.fecha_registro, p.room_code, u.imagen_perfil
                FROM puntajes p
                JOIN usuarios u ON p.user_id = u.id
                WHERE u.perfil = 'estudiante' AND (p.nivel = 1 OR p.nivel = 2)";

    if ($search !== '') {
        $sql_pdf .= " AND (u.usuario LIKE '%$search%' OR p.puntaje LIKE '%$search%' OR p.room_code LIKE '%$search%')";
    }

    $sql_pdf .= " ORDER BY p.fecha_registro DESC";

    $result_pdf = $con->query($sql_pdf);

    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);
    $html = '<h1>Código Generado: ' . htmlspecialchars($room_code, ENT_QUOTES, 'UTF-8') . '</h1>';
    $html .= '<h2>Puntajes de los Estudiantes - Nivel 1</h2>';
    $html .= '<table border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th>' . $translations['user'] . '</th>
                        <th>' . $translations['score'] . '</th>
                        <th>' . $translations['level'] . '</th>
                        <th>' . $translations['registration_date'] . '</th>
                        <th>' . $translations['room_code'] . '</th>
                    </tr>
                </thead>
                <tbody>';

    while ($row = $result_pdf->fetch_assoc()) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($row['usuario'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . htmlspecialchars($row['puntaje'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . htmlspecialchars($row['nivel'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . htmlspecialchars($row['fecha_registro'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . htmlspecialchars($row['room_code'], ENT_QUOTES, 'UTF-8') . '</td>
                  </tr>';
    }
    $html .= '</tbody></table>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('puntajes_estudiantes.pdf', 'D');
    exit;
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations['report_students']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../styles/inicio.css">
    <link rel="stylesheet" type="text/css" href="../../styles/ver_codigo_y_puntajes1.css">
    <style>
    .cont-pagination {
        display: flex;
        justify-content: center;
    }
    .text {
        margin: 0%;
        font-family: 'Digitalt';
    }
    .exportar {
        display: flex;
        justify-content: flex-end;
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
                <div class="usuario-logueado">
                    <div class="profile-data-item">
                        <span id="username" class="username-span"><?php echo htmlspecialchars($user_row['usuario'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <?php
                    // Mostrar la imagen de perfil si está disponible
                    if (!empty($user_row['imagen_perfil'])) {
                        echo '<img class="profile-pic" src="' . htmlspecialchars($user_row['imagen_perfil'], ENT_QUOTES, 'UTF-8') . '" alt="Imagen de perfil">';
                    } else {
                        // Si no hay imagen de perfil, se mostrará el avatar predeterminado
                        echo '<img class="profile-pic" src="../../modules/inicio/uploads/perfil.jpg" alt="Avatar predeterminado">';
                    }
                    ?>
                </div>
            </div>
            <div class="fila2-cl2">
                <div class="contenido-juego">
                <div class="contenedor-ab">
                    <div class="text">
                        <h2 class="text"><?php echo $translations['report_students']; ?></h2>
                    </div>
                    <div class="cont-form">
                        <form method="GET" action="" class="form1">
                            <input type="hidden" name="room_code" value="<?php echo htmlspecialchars($room_code, ENT_QUOTES, 'UTF-8'); ?>">
                            <label for="search"><?php echo $translations['search']; ?>:</label>
                            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" class="btn btn-primary"><?php echo $translations['search']; ?></button>
                        </form>
                    </div>
                    <div>
                        <table class="table table-striped mt-3">
                            <thead>
                                <tr>
                                    <th><?php echo $translations['user']; ?></th>
                                    <th><?php echo $translations['score']; ?></th>
                                    <th><?php echo $translations['level']; ?></th>
                                    <th><?php echo $translations['registration_date']; ?></th>
                                    <th><?php echo $translations['room_code']; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['usuario'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row['puntaje'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row['nivel'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha_registro'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row['room_code'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <nav class="cont-pagination">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?room_code=<?php echo htmlspecialchars($room_code, ENT_QUOTES, 'UTF-8'); ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&page=<?php echo $page-1; ?>"><?php echo $translations['previous']; ?></a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);

                            if ($start > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?room_code=' . htmlspecialchars($room_code, ENT_QUOTES, 'UTF-8') . '&search=' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . '&page=1">1</a></li>';
                                if ($start > 2) {
                                    echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                }
                            }

                            for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                                    <a class="page-link" href="?room_code=<?php echo htmlspecialchars($room_code, ENT_QUOTES, 'UTF-8'); ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php
                            if ($end < $total_pages) {
                                if ($end < $total_pages - 1) {
                                    echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="?room_code=' . htmlspecialchars($room_code, ENT_QUOTES, 'UTF-8') . '&search=' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
                            }
                            ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?room_code=<?php echo htmlspecialchars($room_code, ENT_QUOTES, 'UTF-8'); ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&page=<?php echo $page+1; ?>"><?php echo $translations['next']; ?></a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <form method="POST">
                        <div class="exportar">
                            <button type="submit" name="export_pdf" class="btn btn-primary" id="btn-export"><?php echo $translations['export_pdf']; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php
$con->close();
?>
