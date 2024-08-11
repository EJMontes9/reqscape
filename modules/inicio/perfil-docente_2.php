<?php
session_start();
include "../../connection/connection.php";

// Incluir el autoloader de Composer
require_once '../../vendor/autoload.php';

// Función para cargar las traducciones
function loadTranslations($lang)
{
    $translations = array();
    if ($lang == 'en') {
        $translations = array(
            'table_title' => 'Requirements Table',
            'name_label' => 'Name',
            'words_label' => 'Words',
            'actions_label' => 'Actions',
            'add_word' => 'Add Word',
            'correct' => 'Correct',
            'incorrect' => 'Incorrect',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'confirm_delete' => 'Are you sure you want to delete this requirement?',
            'insert_success' => 'New record created successfully',
            'insert_error' => 'Error creating record: ',
            'update_success' => 'Record updated successfully',
            'update_error' => 'Error updating record: ',
            'delete_success' => 'Record deleted successfully',
            'delete_error' => 'Error deleting record: ',
            'import_success' => 'File imported successfully',
            'import_error' => 'Please upload a valid CSV or Excel file.',
            'search_placeholder' => 'Search...',
            'previous' => 'Previous',
            'next' => 'Next',
            'create_button' => 'Create',
            'update_button' => 'Update',
            'generate_code' => 'Generate Code'
        );
    } else {
        $translations = array(
            'table_title' => 'Tabla de requerimientos',
            'name_label' => 'Nombre',
            'words_label' => 'Palabras',
            'actions_label' => 'Acciones',
            'add_word' => 'Agregar Palabra',
            'correct' => 'Correcta',
            'incorrect' => 'Incorrecta',
            'edit' => 'Editar',
            'delete' => 'Eliminar',
            'confirm_delete' => '¿Estás seguro de que deseas eliminar este requerimiento?',
            'insert_success' => 'Nuevo registro creado con éxito',
            'insert_error' => 'Error al crear el registro: ',
            'update_success' => 'Registro actualizado con éxito',
            'update_error' => 'Error al actualizar el registro: ',
            'delete_success' => 'Registro eliminado con éxito',
            'delete_error' => 'Error al eliminar el registro: ',
            'import_success' => 'Archivo importado con éxito',
            'import_error' => 'Por favor, suba un archivo CSV o Excel válido.',
            'search_placeholder' => 'Buscar...',
            'previous' => 'Anterior',
            'next' => 'Siguiente',
            'create_button' => 'Crear',
            'update_button' => 'Actualizar',
            'generate_code' => 'Generar Código'
        );
    }
    return $translations;
}

// Verificar si se ha seleccionado un idioma
if (isset($_POST['language'])) {
    $_SESSION['lang'] = $_POST['language'];
}

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es';
$translations = loadTranslations($lang);

// Consulta para obtener los datos del usuario
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT usuario, correo, imagen_perfil FROM usuarios WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se obtuvieron resultados
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        // Manejar el caso en el que no se obtuvieron resultados
        echo "No se encontraron resultados.";
        exit();
    }
} else {
    echo "No se ha iniciado sesión.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['insert'])) {
        // Código para insertar un nuevo requerimiento
        $name = $_POST['name'];
        $palabras = isset($_POST['palabras']) ? $_POST['palabras'] : [];
        $correctas = isset($_POST['correctas']) ? $_POST['correctas'] : [];

        $sql = "INSERT INTO requirements_2 (name) VALUES (?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $name);

        if ($stmt->execute()) {
            $requirement_id = $stmt->insert_id; // Obtener el ID del requerimiento insertado
            foreach ($palabras as $index => $palabra) {
                $palabra_correct = isset($correctas[$index]) ? 1 : 0;
                $sql_palabra = "INSERT INTO palabras (requirements_id, palabra, requirements_correct) VALUES (?, ?, ?)";
                $stmt_palabra = $con->prepare($sql_palabra);
                $stmt_palabra->bind_param("isi", $requirement_id, $palabra, $palabra_correct);
                $stmt_palabra->execute();
            }
            echo "<script>alert('" . $translations['insert_success'] . "');</script>";
        } else {
            echo "<script>alert('" . $translations['insert_error'] . $stmt->error . "');</script>";
        }
        $stmt->close();
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
        $id = $_POST['id'];

        // First, delete dependent rows in the room_requirements table
        $sql = "DELETE FROM room_requirements WHERE requirement_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Then delete dependent rows in the palabras table
            $sql = "DELETE FROM palabras WHERE requirements_id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                // Now delete the row in requirements_2
                $sql = "DELETE FROM requirements_2 WHERE id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    echo "<div class='alert alert-success' role='alert'><span class='font-medium'>Success:</span> " . $translations['delete_success'] . "</div>";
                } else {
                    echo "<div class='alert alert-danger' role='alert'><span class='font-medium'>Error:</span> " . $translations['delete_error'] . $stmt->error . "</div>";
                }
            } else {
                echo "<div class='alert alert-danger' role='alert'><span class='font-medium'>Error:</span> " . $translations['delete_error'] . $stmt->error . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger' role='alert'><span class='font-medium'>Error:</span> " . $translations['delete_error'] . $stmt->error . "</div>";
        }
        $stmt->close();
        exit();
    } elseif (isset($_POST['update'])) {
        // Código para actualizar un requerimiento
        $id = $_POST['id'];
        $name = $_POST['name'];
        $palabras = isset($_POST['palabras']) ? $_POST['palabras'] : [];
        $correctas = isset($_POST['correctas']) ? $_POST['correctas'] : [];

        $sql = "UPDATE requirements_2 SET name = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("si", $name, $id);

        if ($stmt->execute()) {
            // Actualizar las palabras
            $con->query("DELETE FROM palabras WHERE requirements_id = $id");
            foreach ($palabras as $index => $palabra) {
                $palabra_correct = isset($correctas[$index]) ? 1 : 0;
                $sql_palabra = "INSERT INTO palabras (requirements_id, palabra, requirements_correct) VALUES (?, ?, ?)";
                $stmt_palabra = $con->prepare($sql_palabra);
                $stmt_palabra->bind_param("isi", $id, $palabra, $palabra_correct);
                $stmt_palabra->execute();
            }
            echo "<script>alert('" . $translations['update_success'] . "');</script>";
        } else {
            echo "<script>alert('" . $translations['update_error'] . $stmt->error . "');</script>";
        }
        $stmt->close();
    } elseif (isset($_POST['import'])) {
        // Código para importar archivo CSV o Excel
        $fileName = $_FILES['file']['name'];
        $fileTmpName = $_FILES['file']['tmp_name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        if ($fileExtension == 'csv') {
            importCSV($fileTmpName, $con);
        } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
            importExcel($fileTmpName, $con);
        } else {
            echo "<div class='alert alert-danger' role='alert'><span class='font-medium'>Danger alert!</span> Formato de archivo no soportado.</div>";
        }
    } elseif (isset($_POST['generate_code'])) {
        // Código para generar un código de sala
        $selected_requirements = isset($_POST['selected_requirements']) ? $_POST['selected_requirements'] : [];
        $room_code = strtoupper(substr(md5(time()), 0, 6)); // Generar un código de sala aleatorio
        $nivel = 2; // Asegurar que el nivel se proporciona

        foreach ($selected_requirements as $requirement_id) {
            $sql = "INSERT INTO room_requirements (room_code, requirement_id, nivel) VALUES (?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("sii", $room_code, $requirement_id, $nivel);
            $stmt->execute();
        }

        // Redirigir a la nueva página con el código de sala
        header("Location: ver_codigo_y_puntaje_2.php?room_code=$room_code");
        exit();
    }
}

function importCSV($fileTmpName, $con)
{
    $file = fopen($fileTmpName, 'r');
    fgetcsv($file); // Omite la primera fila (cabecera)
    $errors = [];

    while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
        if (count($row) < 4) {
            $errors[] = "Error: Una fila no tiene suficientes datos.";
            continue;
        }

        $requerimiento = explode(';', $row[0]);
        $orden = explode(';', $row[2]);
        $es_correcta = explode(';', $row[3]);

        $sql = "INSERT INTO requirements_2 (name) VALUES ('" . mysqli_real_escape_string($con, implode(';', $requerimiento)) . "')";
        if (mysqli_query($con, $sql)) {
            $requirement_id = mysqli_insert_id($con); // Obtener el ID del requerimiento insertado
            foreach ($requerimiento as $index => $palabra) {
                $palabra_correct = $es_correcta[$index] === 'T' ? 1 : 0;
                $orden_palabra = $orden[$index];
                $sql_palabra = "INSERT INTO palabras (requirements_id, palabra, orden, requirements_correct) VALUES ($requirement_id, '$palabra', '$orden_palabra', $palabra_correct)";
                if (!mysqli_query($con, $sql_palabra)) {
                    $errors[] = "Error al insertar palabra: " . mysqli_error($con);
                }
            }
        } else {
            $errors[] = "Error al insertar datos: " . mysqli_error($con);
        }
    }
    fclose($file);

    if (!empty($errors)) {
        $uniqueErrors = array_unique($errors);
        echo "<div class='alert alert-danger' role='alert'><span class='font-medium'>Danger alert!</span> " . implode("<br>", $uniqueErrors) . "</div>";
    } else {
        echo "<div class='alert alert-success' role='alert'><span class='font-medium'>Info alert!</span> Importación completada.</div>";
    }
}

function importExcel($fileTmpName, $con)
{
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileTmpName);
    $worksheet = $spreadsheet->getActiveSheet();
    $isFirstRow = true;
    $errors = [];

    foreach ($worksheet->getRowIterator() as $row) {
        if ($isFirstRow) {
            $isFirstRow = false;
            continue; // Omite la primera fila (cabecera)
        }

        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        $data = [];
        foreach ($cellIterator as $cell) {
            $data[] = $cell->getValue();
        }

        if (count($data) < 4) {
            $errors[] = "Error: Una fila no tiene suficientes datos.";
            continue;
        }

        $requerimiento = $data[0];
        $palabras = explode(';', $data[1]);
        $orden = explode(';', $data[2]);
        $es_correcta = explode(';', $data[3]);

        $sql = "INSERT INTO requirements_2 (name) VALUES ('" . mysqli_real_escape_string($con, $requerimiento) . "')";
        if (mysqli_query($con, $sql)) {
            $requirement_id = mysqli_insert_id($con); // Obtener el ID del requerimiento insertado
            foreach ($palabras as $index => $palabra) {
                $palabra_correct = $es_correcta[$index] === 'T' ? 1 : 0;
                $orden_palabra = $orden[$index];
                $sql_palabra = "INSERT INTO palabras (requirements_id, palabra, orden, requirements_correct) VALUES ($requirement_id, '$palabra', '$orden_palabra', $palabra_correct)";
                if (!mysqli_query($con, $sql_palabra)) {
                    $errors[] = "Error al insertar palabra: " . mysqli_error($con);
                }
            }
        } else {
            $errors[] = "Error al insertar datos: " . mysqli_error($con);
        }
    }

    if (!empty($errors)) {
        $uniqueErrors = array_unique($errors);
        echo "<div class='alert alert-danger' role='alert'><span class='font-medium'>Danger alert!</span> " . implode("<br>", $uniqueErrors) . "</div>";
    } else {
        echo "<div class='alert alert-success' role='alert'><span class='font-medium'>Info alert!</span> Importación completada.</div>";
    }
}

// Paginación
$limit = 9; // Número de registros por página
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Consulta para obtener los requerimientos con paginación
$search_query = "";
if (isset($_GET['query'])) {
    $search_query = mysqli_real_escape_string($con, $_GET['query']);
    $requirements = $con->query("SELECT * FROM requirements_2 WHERE name LIKE '%$search_query%' LIMIT $start, $limit");
    $total_results = $con->query("SELECT COUNT(*) AS count FROM requirements_2 WHERE name LIKE '%$search_query%'")->fetch_assoc()['count'];
} else {
    $requirements = $con->query("SELECT * FROM requirements_2 LIMIT $start, $limit");
    $total_results = $con->query("SELECT COUNT(*) AS count FROM requirements_2")->fetch_assoc()['count'];
}

$total_pages = ceil($total_results / $limit);

// Mantener la conexión abierta
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations['table_title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../styles/inicio.css">
    <style>
        /* Estilo para el buscador */
        .search-container {
            margin: 2% 0%;
        }

        .offset-md-3 {
            margin-left: 0%;
        }

        .profile-data {
            background-color: #ffff;
        }

        .titulo-tabla {
            background-color: #ffff;
            display: flex;
            justify-content: center;
            font-family: "Digitalt";
            text-shadow: 2px 2px 4px rgba(107, 15, 150, 0.5);
        }

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

        .cont-prin {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 2%;
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

        .btn-generar-codigo {

            display: flex;
            justify-content: flex-end;
        }

        .fondo {
            height: auto;
        }

        .columna-1 {
            height: 98%;
        }

        .columna-2 {
            height: 98%;
        }

        body {
            background-color: #53F3FD;
        }

        #botongene {
            margin-bottom: 1%;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
            display: none;
        }

        .alert-danger {
            color: #b91c1c;
            background-color: #fef2f2;
        }

        .alert-success {
            color: #065f46;
            background-color: #d1fae5;
        }

        .alert .font-medium {
            font-weight: 500;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const alertBox = document.querySelector('.alert');
            if (alertBox) {
                alertBox.style.display = 'block';
                setTimeout(function () {
                    alertBox.style.display = 'none';
                }, 3000);
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function (event) {
                    event.preventDefault(); // Prevent the default form submission
                    const id = this.dataset.id;
                    if (confirm('<?php echo $translations['confirm_delete']; ?>')) {
                        fetch('perfil-docente_2.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `delete=1&id=${id}`
                        })
                            .then(response => response.text())
                            .then(data => {
                                const alertContainer = document.getElementById('alertContainer');
                                if (alertContainer) {
                                    alertContainer.innerHTML = data;
                                } else {
                                    console.error('Error: Element with id "alertContainer" not found.');
                                }
                                console.log(data); // Show the result in the console
                                this.closest('tr').remove(); // Remove the row from the DOM
                            })
                            .catch(error => console.error('Error:', error));
                    }
                });
            });
        });

        function validateForm() {
            var name = document.forms["requirementForm"]["name"].value;
            var palabras = document.forms["requirementForm"]["palabras"].value;
            if (name == "" || palabras == "") {
                alert("<?php echo $translations['insert_error']; ?>");
                return false;
            }
            return true;
        }

        function showModal(message) {
            var modal = document.getElementById("myModal");
            var modalMessage = document.getElementById("modalMessage");
            modalMessage.textContent = message;
            modal.style.display = "block";
        }

        function closeModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        }

        window.onclick = function (event) {
            var modal = document.getElementById("myModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function toggleTable() {
            var table = document.getElementById('requirementTable');
            if (table.style.display === 'none' || table.style.display === '') {
                table.style.display = 'block';
            } else {
                table.style.display = 'none';
            }
        }

        function addPalabraField() {
            var container = document.getElementById('palabrasContainer');
            var div = document.createElement('div');
            div.className = 'mb-3';
            div.innerHTML = `
                <input type="text" class="form-control" name="palabras[]" placeholder="<?php echo $translations['name_label']; ?>" required>
                <select class="form-select mt-1" name="correctas[]">
                    <option value="0"><?php echo $translations['incorrect']; ?></option>
                    <option value="1"><?php echo $translations['correct']; ?></option>
                </select>
            `;
            container.appendChild(div);
        }

        function editRequirement(id, name, palabras) {
            document.getElementById('requirementId').value = id;
            document.getElementById('name').value = name;
            var container = document.getElementById('palabrasContainer');
            container.innerHTML = '';
            palabras.forEach(palabra => {
                var div = document.createElement('div');
                div.className = 'mb-3';
                div.innerHTML = `
                    <input type="text" class="form-control" name="palabras[]" value="${palabra.palabra}" required>
                    <select class="form-select mt-1" name="correctas[]">
                        <option value="0" ${palabra.correct == 0 ? 'selected' : ''}><?php echo $translations['incorrect']; ?></option>
                        <option value="1" ${palabra.correct == 1 ? 'selected' : ''}><?php echo $translations['correct']; ?></option>
                    </select>
                `;
                container.appendChild(div);
            });
            document.getElementById('insertButton').style.display = 'none';
            document.getElementById('updateButton').style.display = 'inline';
            var modal = new bootstrap.Modal(document.getElementById('registroModal'), {
                backdrop: false // Desactiva el fondo del modal
            });
            modal.show();
        }

        function searchRequirements() {
            var query = document.getElementById('searchInput').value;
            window.location.href = '?query=' + query + '&page=1';
        }

        function openCreateModal() {
            document.getElementById('requirementForm').reset();
            document.getElementById('palabrasContainer').innerHTML = '';
            addPalabraField(); // Agrega un campo por defecto
            document.getElementById('insertButton').style.display = 'inline';
            document.getElementById('updateButton').style.display = 'none';
            var modal = new bootstrap.Modal(document.getElementById('registroModal'), {
                backdrop: false // Desactiva el fondo del modal
            });
            modal.show();
        }

        function generateCode() {
            var selectedRequirements = [];
            var checkboxes = document.querySelectorAll('input[name="selected_requirements[]"]:checked');
            checkboxes.forEach((checkbox) => {
                selectedRequirements.push(checkbox.value);
            });

            if (selectedRequirements.length === 0) {
                alert('Por favor, selecciona al menos un requerimiento.');
                return;
            }

            // Crear un formulario oculto y enviarlo al servidor
            var form = document.createElement('form');
            form.method = 'post';
            form.action = ''; // Mantener la acción actual para manejar en PHP
            form.style.display = 'none';

            selectedRequirements.forEach((requirement) => {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_requirements[]';
                input.value = requirement;
                form.appendChild(input);
            });

            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'generate_code';
            input.value = '1';
            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</head>
<body>
<div id="alertContainer"></div>
<div class="fondo">
    <div class="columna-1">
        <div class="fila1-cl1">
            <select id="languageSelector">
                <option value="en">English</option>
                <option value="es" selected>Español</option>
            </select>
        </div>
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
            <a href="<?php echo ($_SESSION['perfil'] == 'profesor') ? 'reporte-niveles.php' : 'informacion.php'; ?>"
               class="lg-cl1">
                <img src="../../assets/img/inicio/<?php echo ($_SESSION['perfil'] == 'profesor') ? 'report.png' : 'info.png'; ?>"
                     alt="">
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
                    <span id="username"
                          class="username-span"><?php echo htmlspecialchars($row["usuario"], ENT_QUOTES, 'UTF-8'); ?></span>
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
                <h1 class="titulo-tabla"><?php echo $translations['table_title']; ?></h1>
                <div class="profile-data">
                    <div class="container">

                        <!-- Buscador -->
                        <div class="row">
                            <div class="cont-prin">
                                <div class="col-md-6 offset-md-3 search-container">
                                    <input type="text" id="searchInput" class="form-control"
                                           placeholder="<?php echo $translations['search_placeholder']; ?>"
                                           value="<?php echo $search_query; ?>"
                                           onkeypress="if(event.keyCode == 13) searchRequirements()">
                                    <button type="button" class="btn btn-success mt-2"
                                            onclick="openCreateModal()"><?php echo $translations['create_button']; ?></button>
                                </div>
                                <!-- Formulario de importación -->
                                <form action="" method="post" enctype="multipart/form-data" class="mt-2">
                                    <input type="file" name="file" class="form-control" accept=".csv, .xls, .xlsx"
                                           required>
                                    <button type="submit" name="import" class="btn btn-success mt-2">Importar</button>
                                </form>
                            </div>
                            <!-- Buttons for selecting and deselecting all checkboxes -->
                            <div class="btn-select-all">
                                <button id="selectAll" type="button" class="btn btn-primary">Seleccionar Todos</button>
                                <button id="deselectAll" type="button" class="btn btn-secondary">Deseleccionar Todos
                                </button>
                            </div>
                        </div>
                        <!-- Modal de registro -->
                        <div class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="registroModalLabel"><?php echo $translations['create_button']; ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="requirementForm" name="requirementForm"
                                              onsubmit="return validateForm()" method="post">
                                            <input type="hidden" id="requirementId" name="id">
                                            <div class="mb-3">
                                                <label for="name"
                                                       class="form-label"><?php echo $translations['name_label']; ?></label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                       placeholder="<?php echo $translations['name_label']; ?>"
                                                       required>
                                            </div>
                                            <div id="palabrasContainer"></div>
                                            <button type="button" class="btn btn-secondary mb-3"
                                                    onclick="addPalabraField()"><?php echo $translations['add_word']; ?></button>
                                            <button type="submit" class="btn btn-primary" name="insert"
                                                    id="insertButton"><?php echo $translations['create_button']; ?></button>
                                            <button type="submit" class="btn btn-primary" name="update"
                                                    id="updateButton"
                                                    style="display: none;"><?php echo $translations['update_button']; ?></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tabla -->
                        <div class="row">
                            <div class="col-md-12">
                                <form id="requirementsForm">
                                    <table class="table table-striped" id="requirementTable">
                                        <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th><?php echo $translations['name_label']; ?></th>
                                            <th><?php echo $translations['words_label']; ?></th>
                                            <th><?php echo $translations['actions_label']; ?></th>
                                            <th>Seleccionar</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php while ($requirement = $requirements->fetch_assoc()):
                                            $palabras_result = $con->query("SELECT palabra, orden, requirements_correct FROM palabras WHERE requirements_id = " . $requirement['id']);
                                            $palabras = [];
                                            while ($palabra = $palabras_result->fetch_assoc()) {
                                                $palabras[] = [
                                                    'palabra' => $palabra['palabra'],
                                                    'orden' => $palabra['orden'],
                                                    'correct' => $palabra['requirements_correct']
                                                ];
                                            }
                                            ?>
                                            <tr>
                                                <td><?php echo $requirement['id']; ?></td>
                                                <td><?php echo htmlspecialchars($requirement['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td>
                                                    <?php foreach ($palabras as $p): ?>
                                                        <?php echo htmlspecialchars($p['palabra'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo $p['correct'] ? $translations['correct'] : $translations['incorrect']; ?>)
                                                        <br>
                                                    <?php endforeach; ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                            onclick="editRequirement('<?php echo $requirement['id']; ?>', '<?php echo htmlspecialchars($requirement['name'], ENT_QUOTES, 'UTF-8'); ?>', <?php echo htmlspecialchars(json_encode($palabras), ENT_QUOTES, 'UTF-8'); ?>)">
                                                        <i class="fas fa-edit"></i> <?php echo $translations['edit']; ?>
                                                    </button>
                                                    <form method="post"
                                                          onsubmit="return confirm('<?php echo $translations['confirm_delete']; ?>');"
                                                          style="display:inline;">
                                                        <input type="hidden" name="id"
                                                               value="<?php echo $requirement['id']; ?>"/>
                                                        <button type="submit" name="delete"
                                                                class="btn btn-danger btn-sm delete-button"
                                                                data-id="<?php echo $requirement['id']; ?>">
                                                            <i class="fas fa-trash"></i> <?php echo $translations['delete']; ?>
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <input class="requirement-checkbox2" type="checkbox"
                                                           name="selected_requirements[]"
                                                           value="<?php echo $requirement['id']; ?>">
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                    <!-- Paginador -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <nav aria-label="Page navigation example">
                                                <ul class="pagination justify-content-center">
                                                    <li class="page-item <?php if ($page <= 1) {
                                                        echo 'disabled';
                                                    } ?>">
                                                        <a class="page-link" href="<?php if ($page > 1) {
                                                            echo "?query=$search_query&page=" . ($page - 1);
                                                        } else {
                                                            echo '#';
                                                        } ?>" tabindex="-1"><?php echo $translations['previous']; ?></a>
                                                    </li>
                                                    <?php
                                                    $start = max(1, $page - 2);
                                                    $end = min($total_pages, $page + 2);

                                                    if ($start > 1) {
                                                        echo '<li class="page-item"><a class="page-link" href="?query=' . htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8') . '&page=1">1</a></li>';
                                                        if ($start > 2) {
                                                            echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                                        }
                                                    }

                                                    for ($i = $start; $i <= $end; $i++): ?>
                                                        <li class="page-item <?php if ($page == $i) {
                                                            echo 'active';
                                                        } ?>">
                                                            <a class="page-link"
                                                               href="?query=<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                                        </li>
                                                    <?php endfor; ?>

                                                    <?php
                                                    if ($end < $total_pages) {
                                                        if ($end < $total_pages - 1) {
                                                            echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                                        }
                                                        echo '<li class="page-item"><a class="page-link" href="?query=' . htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8') . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
                                                    }
                                                    ?>
                                                    <li class="page-item <?php if ($page >= $total_pages) {
                                                        echo 'disabled';
                                                    } ?>">
                                                        <a class="page-link" href="<?php if ($page < $total_pages) {
                                                            echo "?query=$search_query&page=" . ($page + 1);
                                                        } else {
                                                            echo '#';
                                                        } ?>"><?php echo $translations['next']; ?></a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>

                                    <div class="btn-generar-codigo">
                                        <button id="botongene" type="button" class="btn btn-success mt-2"
                                                onclick="generateCode()">Generar Código
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Incluye jQuery antes de Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Incluye Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select All Button
            document.getElementById('selectAll').addEventListener('click', function () {
                // Select all checkboxes in the table
                document.querySelectorAll('#requirementTable .requirement-checkbox2').forEach(function (checkbox) {
                    checkbox.checked = true;
                });
            });

            // Deselect All Button
            document.getElementById('deselectAll').addEventListener('click', function () {
                // Deselect all checkboxes in the table
                document.querySelectorAll('#requirementTable .requirement-checkbox2').forEach(function (checkbox) {
                    checkbox.checked = false;
                });
            });
        });
    </script>
</body>
</html>

<?php
// Cerrar la conexión al final del script
$con->close();
?>
