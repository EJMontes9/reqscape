<?php
session_start();
include "../../connection/connection.php";
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function loadTranslations($lang) {
    $translations = [
        'en' => [
            'table_title' => 'Requirements Table',
            'name_label' => 'Name',
            'ambiguous_label' => 'Is it ambiguous?',
            'retro_label' => 'Feedback',
            'yes' => 'Yes',
            'no' => 'No',
            'create_button' => 'Create',
            'import_button' => 'Import CSV/Excel',
            'search_placeholder' => 'Search...',
            'edit_button' => 'Edit',
            'delete_button' => 'Delete',
            'confirm_delete' => 'Are you sure you want to delete this requirement?',
            'insert_success' => 'New record created successfully',
            'insert_error' => 'Error creating record: ',
            'update_success' => 'Record updated successfully',
            'update_error' => 'Error updating record: ',
            'delete_success' => 'Record deleted successfully',
            'delete_error' => 'Error deleting record: ',
            'import_success' => 'File imported successfully',
            'import_error' => 'Please upload a valid CSV or Excel file.',
            'previous' => 'Previous',
            'next' => 'Next',
            'generate_code' => 'Generate Code'
        ],
        'es' => [
            'table_title' => 'Tabla de requerimientos',
            'name_label' => 'Nombre',
            'ambiguous_label' => '¿Es ambiguo?',
            'retro_label' => 'Retroalimentación',
            'yes' => 'Sí',
            'no' => 'No',
            'create_button' => 'Crear',
            'import_button' => 'Importar CSV/Excel',
            'search_placeholder' => 'Buscar...',
            'edit_button' => 'Editar',
            'delete_button' => 'Eliminar',
            'confirm_delete' => '¿Estás seguro de que deseas eliminar este requerimiento?',
            'insert_success' => 'Nuevo registro creado con éxito',
            'insert_error' => 'Error al crear el registro: ',
            'update_success' => 'Registro actualizado con éxito',
            'update_error' => 'Error al actualizar el registro: ',
            'delete_success' => 'Registro eliminado con éxito',
            'delete_error' => 'Error al eliminar el registro: ',
            'import_success' => 'Archivo importado con éxito',
            'import_error' => 'Por favor, suba un archivo CSV o Excel válido.',
            'previous' => 'Anterior',
            'next' => 'Siguiente',
            'generate_code' => 'Generar Código'
        ]
    ];
    return $translations[$lang];
}

$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'es';
$translations = loadTranslations($lang);

if ($con->connect_error) {
    die("Conexión fallida: " . $con->connect_error);
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT usuario, correo, imagen_perfil FROM usuarios WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verificar si la columna 'usuario' existe en el array $row
        if (array_key_exists('usuario', $row)) {
            $usuario = htmlspecialchars($row['usuario'], ENT_QUOTES, 'UTF-8');
        } else {
            $usuario = 'Usuario desconocido'; // Valor por defecto o mensaje de error
        }
    } else {
        echo "No se encontraron resultados para el usuario con ID: $user_id";
        exit();
    }
} else {
    echo "No se ha iniciado sesión.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['insert'])) {
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $is_ambiguous = mysqli_real_escape_string($con, $_POST['is_ambiguous']);
        $retro = isset($_POST['retro']) ? mysqli_real_escape_string($con, $_POST['retro']) : '';

        $sql = "INSERT INTO requirements (name, is_ambiguous, retro) VALUES ('$name', $is_ambiguous, '$retro')";

        if ($con->query($sql) === TRUE) {
            echo "<script>alert('".$translations['insert_success']."');</script>";
        } else {
            echo "<script>alert('".$translations['insert_error'].$sql."<br>".$con->error."');</script>";
        }
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $sql = "DELETE FROM room_requirements1 WHERE requirement_id = $id";
        if ($con->query($sql) === TRUE) {
            $sql = "DELETE FROM requirements WHERE id = $id";
            if ($con->query($sql) === TRUE) {
                echo "<script>alert('".$translations['delete_success']."');</script>";
            } else {
                echo "<script>alert('".$translations['delete_error'].$con->error."');</script>";
            }
        } else {
            echo "<script>alert('".$translations['delete_error'].$con->error."');</script>";
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $is_ambiguous = mysqli_real_escape_string($con, $_POST['is_ambiguous']);
        $retro = isset($_POST['retro']) ? mysqli_real_escape_string($con, $_POST['retro']) : '';

        $sql = "UPDATE requirements SET name = '$name', is_ambiguous = $is_ambiguous, retro = '$retro' WHERE id = $id";

        if ($con->query($sql) === TRUE) {
            echo "<script>alert('".$translations['update_success']."');</script>";
        } else {
            echo "<script>alert('".$translations['update_error'].$con->error."');</script>";
        }
    } elseif (isset($_POST['import'])) {
        $file_mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
            $arr_file = explode('.', $_FILES['file']['name']);
            $extension = end($arr_file);

            if ('csv' == $extension) {
                $reader = IOFactory::createReader('Csv');
            } else {
                $reader = IOFactory::createReader('Xlsx');
            }

            $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            foreach ($sheetData as $index => $row) {
                if ($index === 0) continue;

                $name = mysqli_real_escape_string($con, $row[0]);
                $is_ambiguous = strtolower($row[1]) === 'sí' ? 1 : (strtolower($row[1]) === 'no' ? 0 : (int)$row[1]);
                $retro = mysqli_real_escape_string($con, $row[2]);

                $sql = "INSERT INTO requirements (name, is_ambiguous, retro) VALUES ('$name', $is_ambiguous, '$retro')";

                if ($con->query($sql) !== TRUE) {
                    echo "<script>alert('".$translations['insert_error'].$sql."<br>".$con->error."');</script>";
                }
            }

            echo "<script>alert('".$translations['import_success']."');</script>";
        } else {
            echo "<script>alert('".$translations['import_error']."');</script>";
        }
    } elseif (isset($_POST['generate_code'])) {
        if (isset($_POST['requirements']) && !empty($_POST['requirements'])) {
            $selectedRequirements = $_POST['requirements'];
            $room_code = strtoupper(substr(md5(time()), 0, 6));
    
            $validRequirements = array();
    
            // Check for valid requirements
            $stmt_check = $con->prepare("SELECT id FROM requirements WHERE id = ?");
            foreach ($selectedRequirements as $requirement_id) {
                $stmt_check->bind_param("i", $requirement_id);
                $stmt_check->execute();
                $result = $stmt_check->get_result();
    
                if ($result->num_rows > 0) {
                    $validRequirements[] = $requirement_id;
                } else {
                    echo "<script>alert('Error: El requerimiento con ID $requirement_id no existe.');</script>";
                }
            }
    
            if (!empty($validRequirements)) {
                // Begin transaction
                $con->begin_transaction();
    
                try {
                    $stmt_insert = $con->prepare("INSERT IGNORE INTO room_requirements1 (room_code, requirement_id, nivel) VALUES (?, ?, 1)");
    
                    foreach ($validRequirements as $requirement_id) {
                        $stmt_insert->bind_param("si", $room_code, $requirement_id);
                        if (!$stmt_insert->execute()) {
                            throw new Exception("Error al guardar el requerimiento con ID $requirement_id: " . $stmt_insert->error);
                        }
                    }
    
                    // Commit transaction
                    $con->commit();
    
                    header("Location: ver_codigo_y_puntajes.php?room_code=$room_code");
                    exit();
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $con->rollback();
                    echo "<script>alert('".$e->getMessage()."');</script>";
                }
            } else {
                echo "<script>alert('No se pudo generar el código porque no hay requerimientos válidos.');</script>";
            }
        } else {
            echo "<script>alert('Por favor seleccione al menos un requerimiento para generar el código.');</script>";
        }
    }
    
    
}

$limit = 9;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$search_query = "";
if (isset($_GET['query'])) {
    $search_query = mysqli_real_escape_string($con, $_GET['query']);
    $requirements = $con->query("SELECT * FROM requirements WHERE name LIKE '%$search_query%' LIMIT $start, $limit");
    $total_results = $con->query("SELECT COUNT(*) AS count FROM requirements WHERE name LIKE '%$search_query%'")->fetch_assoc()['count'];
} else {
    $requirements = $con->query("SELECT * FROM requirements LIMIT $start, $limit");
    $total_results = $con->query("SELECT COUNT(*) AS count FROM requirements")->fetch_assoc()['count'];
}

$total_pages = ceil($total_results / $limit);

$con->close();
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
        .search-container {
            margin: 2% 0%;
        }
        .offset-md-3  {
            margin-left: 0%;
        }
        .profile-data{
            background-color: #ffff;
        }
        .titulo-tabla{
            background-color: #ffff;
            display:flex;
            justify-content: center;
            font-family: "Digitalt";
            text-shadow: 2px 2px 4px rgba(107, 15, 150, 0.5);
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
            margin-bottom:0%;
        }
        .profile-data-item{
            font-family: 'Digitalt';
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right:3%;
            margin-top: 0%;
            margin-bottom:1%;
            color: #1E1C69;
        }
        .usuario-logueado img{
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin: 2% 2%;
            margin-top: 0%;
            margin-left: 0%;
        }
        .modal-backdrop {
            display: none;
        }
        .cont-prin{
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
    .btn-generar-codigo{
        display: flex;
        justify-content: flex-end;
    }
    .cl-retro, .cl-name{
        width: 25%;
    }
    
    .fondo{
        height: auto;
    }
    .columna-1{
        height: 98%;
    }
    .columna-2{
        height: 98%;
    }
    body{
        background-color: #53F3FD;
    }
    #botongene{
        margin-bottom: 1%;
    }
    .btn-select-all {
            margin-bottom: 1rem;
        }
    .lg-cl1 {
        display: flex;
        justify-content: center;
        padding: 10px 20px;
        width: 100%;
        height: 50%;
    }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="requirements[]"]');
            const generateButton = document.querySelector('button[name="generate_code"]');
            
            // Cargar los requerimientos seleccionados desde localStorage
            const selectedRequirements = new Set(JSON.parse(localStorage.getItem('selectedRequirements') || '[]'));
            
            checkboxes.forEach(checkbox => {
                if (selectedRequirements.has(checkbox.value)) {
                    checkbox.checked = true;
                }
                
                checkbox.addEventListener('change', function() {
                    if (checkbox.checked) {
                        selectedRequirements.add(checkbox.value);
                    } else {
                        selectedRequirements.delete(checkbox.value);
                    }
                    localStorage.setItem('selectedRequirements', JSON.stringify(Array.from(selectedRequirements)));
                });
            });

            generateButton.addEventListener('click', function(event) {
                const form = document.getElementById('requirementsForm');
                selectedRequirements.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'requirements[]';
                    input.value = id;
                    form.appendChild(input);
                });
            });
        });

        function validateForm() {
            var name = document.forms["requirementForm"]["name"].value;
            var is_ambiguous = document.forms["requirementForm"]["is_ambiguous"].value;
            if (name == "" || is_ambiguous == "") {
                alert("<?php echo $translations['insert_error']; ?>");
                return false;
            }
            return true;
        }

        function editRequirement(id, name, is_ambiguous, retro) {
            document.getElementById('requirementId').value = id;
            document.getElementById('name').value = name;
            document.getElementById('is_ambiguous').value = is_ambiguous;
            document.getElementById('retro').value = retro;
            document.getElementById('insertButton').style.display = 'none';
            document.getElementById('updateButton').style.display = 'inline';
            var modal = new bootstrap.Modal(document.getElementById('registroModal'));
            modal.show();
        }

        function searchRequirements() {
            var query = document.getElementById('searchInput').value;
            window.location.href = '?query=' + query + '&page=1';
        }

        document.getElementById('languageSelector').addEventListener('change', function() {
            var lang = this.value;
            // Guardar el idioma en la sesión
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "<?php echo $_SERVER['PHP_SELF']; ?>", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("language=" + lang);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    window.location.reload();
                }
            }
        });
        
   
    </script>
</head>
<body>
<div class="fondo">
<div class="columna-1">
    <div class="fila1-cl1">
        <select id="languageSelector">
            <option value="en" <?php echo $lang == 'en' ? 'selected' : ''; ?>>English</option>
            <option value="es" <?php echo $lang == 'es' ? 'selected' : ''; ?>>Español</option>
        </select>
    </div>
    <div class="fila2-cl1">
        <a href="inicio.php" class="lg-cl1">
            <img src="../../assets/img/inicio/inicio.png" alt="">
            <span class="tooltiptext">Inicio</span>
        </a>
        <a href="niveles-juego.php" class="lg-cl1">
            <img src="../../assets/img/inicio/niveles.png" alt="">
            <span class="tooltiptext">Niveles</span>
        </a>
        <a href="score-page.php" class="lg-cl1">
            <img src="../../assets/img/inicio/scoreglobal.png" alt="">
            <span class="tooltiptext">Puntuación</span>
        </a>
        <a href="perfil.php" class="lg-cl1">
            <img src="../../assets/img/inicio/perfil.png" alt="">
            <span class="tooltiptext">Perfil</span>
        </a>
        <a href="<?php echo ($_SESSION['perfil'] == 'profesor') ? 'reporte-niveles.php' : 'informacion.php'; ?>" class="lg-cl1">
            <img src="../../assets/img/inicio/<?php echo ($_SESSION['perfil'] == 'profesor') ? 'report.png' : 'info.png'; ?>" alt="">
            <span class="tooltiptext" id="info">Información</span>
        </a>
        <a href="inicio-sesion.php" class="lg-cl1">
            <img src="../../assets/img/inicio/log-out.png" alt="">
            <span class="tooltiptext">Cerrar Sesión</span>
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
            <span id="username" class="username-span"><?php echo $usuario; ?></span>
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
                    <div class="row">
                        <div class="cont-prin">
                            <div class="col-md-6 offset-md-3 search-container">
                                <input type="text" id="searchInput" class="form-control" placeholder="<?php echo $translations['search_placeholder']; ?>" value="<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>" onkeypress="if(event.keyCode == 13) searchRequirements()">
                                <button type="button" class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#registroModal"><?php echo $translations['create_button']; ?></button>
                            </div>
                            <form action="" method="post" enctype="multipart/form-data" class="mt-2">
                                <input type="file" name="file" class="form-control" required>
                                <button type="submit" name="import" class="btn btn-success mt-2"><?php echo $translations['import_button']; ?></button>
                            </form>
                        </div>
                        <!-- Buttons for selecting and deselecting all checkboxes -->
                        <div class="btn-select-all">
                            <button id="selectAll" type="button" class="btn btn-primary">Seleccionar Todos</button>
                            <button id="deselectAll" type="button" class="btn btn-secondary">Deseleccionar Todos</button>
                        </div>
                    </div>
                    <div class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="registroModalLabel"><?php echo $translations['create_button']; ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="requirementForm" name="requirementForm" onsubmit="return validateForm()" method="post">
                                        <input type="hidden" id="requirementId" name="id">
                                        <div class="mb-3">
                                            <label for="name" class="form-label"><?php echo $translations['name_label']; ?></label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="<?php echo $translations['name_label']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="is_ambiguous" class="form-label"><?php echo $translations['ambiguous_label']; ?></label>
                                            <select class="form-select" id="is_ambiguous" name="is_ambiguous" required>
                                                <option value=""><?php echo $translations['ambiguous_label']; ?></option>
                                                <option value="1"><?php echo $translations['yes']; ?></option>
                                                <option value="0"><?php echo $translations['no']; ?></option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="retro" class="form-label"><?php echo $translations['retro_label']; ?></label>
                                            <textarea class="form-control" id="retro" name="retro" rows="3" placeholder="<?php echo $translations['retro_label']; ?>"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="insert" id="insertButton"><?php echo $translations['create_button']; ?></button>
                                        <button type="submit" class="btn btn-primary" name="update" id="updateButton" style="display: none;"><?php echo $translations['edit_button']; ?></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <form id="requirementsForm" method="post" action="">
                                <table class="table table-striped" id="requirementTable">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th class="cl-name"><?php echo $translations['name_label']; ?></th>
                                            <th><?php echo $translations['ambiguous_label']; ?></th>
                                            <th class="cl-retro"><?php echo $translations['retro_label']; ?></th>
                                            <th>Acciones</th>
                                            <th>Seleccionar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($requirement = $requirements->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $requirement['id']; ?></td>
                                                <td><?php echo htmlspecialchars($requirement['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo $requirement['is_ambiguous'] ? $translations['yes'] : $translations['no']; ?></td>
                                                <td><?php echo htmlspecialchars($requirement['retro'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="editRequirement('<?php echo $requirement['id']; ?>', '<?php echo htmlspecialchars($requirement['name'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo $requirement['is_ambiguous']; ?>', '<?php echo htmlspecialchars($requirement['retro'], ENT_QUOTES, 'UTF-8'); ?>')"><i class="fas fa-edit"></i> <?php echo $translations['edit_button']; ?></button>
                                                    <form method="post" onsubmit="return confirm('<?php echo $translations['confirm_delete']; ?>');" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $requirement['id']; ?>">
                                                        <button type="submit" name="delete" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> <?php echo $translations['delete_button']; ?></button>
                                                    </form>
                                                </td>
                                                <td><input class="requirement-checkbox" type="checkbox" name="requirements[]" value="<?php echo $requirement['id']; ?>"></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-md-12">
                                        <nav aria-label="Page navigation example">
                                            <ul class="pagination justify-content-center">
                                                <li class="page-item <?php if($page <= 1) { echo 'disabled'; } ?>">
                                                    <a class="page-link" href="<?php if($page > 1) { echo "?query=$search_query&page=" . ($page - 1); } else { echo '#'; } ?>" aria-label="Previous">
                                                        <span aria-hidden="true">&laquo;</span>
                                                        <span class="sr-only"><?php echo $translations['previous']; ?></span>
                                                    </a>
                                                </li>
                                                <?php
                                                $start = max(1, $page - 2);
                                                $end = min($total_pages, $page + 2);

                                                if ($start > 1) {
                                                    echo '<li class="page-item"><a class="page-link" href="?query=' . $search_query . '&page=1">1</a></li>';
                                                    if ($start > 2) {
                                                        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                                    }
                                                }

                                                for ($i = $start; $i <= $end; $i++): ?>
                                                    <li class="page-item <?php if($page == $i) { echo 'active'; } ?>">
                                                        <a class="page-link" href="?query=<?php echo $search_query; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                                    </li>
                                                <?php endfor; ?>

                                                <?php
                                                if ($end < $total_pages) {
                                                    if ($end < $total_pages - 1) {
                                                        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                                    }
                                                    echo '<li class="page-item"><a class="page-link" href="?query=' . $search_query . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
                                                }
                                                ?>
                                                <li class="page-item <?php if($page >= $total_pages) { echo 'disabled'; } ?>">
                                                    <a class="page-link" href="<?php if($page < $total_pages) { echo "?query=$search_query&page=" . ($page + 1); } else { echo '#'; } ?>" aria-label="Next">
                                                        <span aria-hidden="true">&raquo;</span>
                                                        <span class="sr-only"><?php echo $translations['next']; ?></span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>

                                <div class="btn-generar-codigo">
                                    <button id="botongene" type="submit" class="btn btn-success mt-2" name="generate_code"><?php echo $translations['generate_code']; ?></button>
                                </div>
                                <input type="hidden" name="generate_code" value="1">
                            </form>
                        </div>
                    </div>

                </div>
            </div>            
        </div>      
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All Button
    document.getElementById('selectAll').addEventListener('click', function() {
        // Select all checkboxes in the table
        document.querySelectorAll('#requirementTable .requirement-checkbox').forEach(function(checkbox) {
            checkbox.checked = true;
        });
    });

    // Deselect All Button
    document.getElementById('deselectAll').addEventListener('click', function() {
        // Deselect all checkboxes in the table
        document.querySelectorAll('#requirementTable .requirement-checkbox').forEach(function(checkbox) {
            checkbox.checked = false;
        });
    });
});
</script>
</body>
</html>

<?php
// Cambiar el idioma si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['language'])) {
    $language = $_POST['language'];
    $_SESSION['language'] = $language;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
